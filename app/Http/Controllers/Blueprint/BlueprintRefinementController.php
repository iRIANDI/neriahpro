<?php

namespace App\Http\Controllers\Blueprint;

use App\Http\Controllers\Blueprint\Concerns\AuthorizesBlueprintAccess;
use App\Http\Controllers\Controller;
use App\Mail\BlueprintReadyNotificationMail;
use App\Models\AuditLog;
use App\Models\BlueprintVoucher;
use App\Models\CmsGlobalSetting;
use App\Models\Document;
use App\Models\LeadContact;
use App\Models\PosChangeRequest;
use App\Models\PosImpactGrantApplication;
use App\Models\PosProject;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\BlueprintDeltaEvolutionService;
use App\Services\BlueprintDiscoveryService;
use App\Services\BlueprintDoctorService;
use App\Services\BlueprintKpiCascadeService;
use App\Services\BlueprintRefinementService;
use App\Services\BlueprintSlopScannerService;
use App\Services\BrownfieldMigrationAuditorService;
use App\Services\Legal\LegalDocumentVaultService;
use App\Services\MidtransSnapService;
use App\Services\PosCommercialBridgeService;
use App\Services\PrdGeneratorService;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Services\ScaffoldGeneratorService;
use App\Services\Security\DocumentThreatSanitizerService;
use App\Services\TopologicalPromptChunker;
use App\Support\ContractLegalHelper;
use App\Support\InputHygieneStandard;
use App\Support\PosAgentShieldAndCoDesignCatalog;
use App\Support\PosDualZoneFrontendAndSaasCatalog;
use App\Support\PosEnterpriseGuaranteeCatalog;
use App\Support\PosInclusiveCommercialCatalog;
use App\Support\PosRealWorldRuntimeCatalog;
use App\Support\PosSectorUiArchetypeCatalog;
use App\Support\PosStackPresetCatalog;
use App\Support\PricingRegistry;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlueprintRefinementController extends Controller
{
    use AuthorizesBlueprintAccess;

    /**
     * Partial / Granular Segment Edit & AI Regeneration.
     * Allows Retail users or SuperAdmins to modify specific PRD segments without resetting the entire document.
     */
    /**
     * Retrieve the grounded source specification corpus (MarkItDown, uploaded files, original brief).
     */
    public function getSourceCorpus(Request $request, string $slug, BlueprintRefinementService $refinementService): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $corpusData = $refinementService->getSourceCorpus($blueprint);

        return response()->json([
            'success' => true,
            'data' => $corpusData,
        ]);
    }

    /**
     * Partial / Granular Segment Edit & Grounded AI Refinement.
     * Allows Retail users or SuperAdmins to modify or deepen specific PRD segments based on Source Corpus.
     */
    public function tweakSegment(Request $request, string $slug, BlueprintRefinementService $refinementService): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        // Access Logic: Retail, SuperAdmin, or Draft can edit. Studio contracts are locked.
        $isRetail = $blueprint->project_status === 'Retail License'
            || (method_exists($blueprint, 'isRetailTrack') && $blueprint->isRetailTrack())
            || (method_exists($blueprint, 'hasPaidRetailLicense') && $blueprint->hasPaidRetailLicense());
        $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin();
        $isDraft = ! $blueprint->signed_agreement && ! $blueprint->isScopeFrozen();

        if (! ($isRetail || $isSuperAdmin || $isDraft)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Dokumen berada dalam status Scope Lock (Kontrak Studio) dan hanya dapat diubah melalui Change Request resmi. Hubungi admin Neriah Pro.',
            ], 403);
        }

        $request->validate([
            'segment_key' => 'required|string',
            'mode' => 'required|in:manual,ai',
            'prompt' => 'nullable|string',
            'content' => 'nullable|string',
            'ai_provider' => 'nullable|string',
            'feature_id' => 'nullable|string',
            'spec_index' => 'nullable|integer',
        ]);

        $key = $request->input('segment_key');
        $mode = $request->input('mode');

        if ($mode === 'manual') {
            $newContent = $request->input('content');

            // Nested JSON update in prd_content
            $prd = $blueprint->prd_content ?? [];

            if (str_contains($key, '.')) {
                $parts = explode('.', $key);
                if (count($parts) === 2) {
                    $prd[$parts[0]][$parts[1]] = is_string($newContent) ? (json_decode($newContent, true) ?? $newContent) : $newContent;
                }
            } else {
                $prd[$key] = is_string($newContent) ? (json_decode($newContent, true) ?? $newContent) : $newContent;
            }

            // Impact Matrix (Ripple Effect - Upstream to Downstream)
            $impactMatrix = [
                'executive_summary' => ['system_actors', 'mvp_features', 'database_erd'],
                'masalah_utama' => ['executive_summary', 'system_actors', 'mvp_features'],
                'system_actors' => ['mvp_features', 'alurs_kerja', 'database_erd', 'custom_ai_agent_rules'],
                'mvp_features' => ['alurs_kerja', 'database_erd', 'custom_ai_agent_rules'],
                'alurs_kerja' => ['database_erd', 'custom_ai_agent_rules'],
                'database_erd' => ['custom_ai_agent_rules'],
            ];
            $baseKey = str_contains($key, '.') ? explode('.', $key)[0] : $key;
            if (isset($impactMatrix[$baseKey])) {
                $impacted = $prd['_impacted_segments'] ?? [];
                foreach ($impactMatrix[$baseKey] as $iKey) {
                    if (! in_array($iKey, $impacted)) {
                        $impacted[] = $iKey;
                    }
                }
                $prd['_impacted_segments'] = $impacted;
            }
            if (isset($prd['_impacted_segments'])) {
                $prd['_impacted_segments'] = array_values(array_filter($prd['_impacted_segments'], fn ($item) => $item !== $baseKey && $item !== $key));
            }

            $blueprint->update(['prd_content' => $prd]);

            return response()->json([
                'success' => true,
                'message' => 'Segmen spesifikasi berhasil diperbarui secara manual.',
            ]);
        }

        if ($mode === 'ai') {
            try {
                $locale = $request->input('locale')
                    ?: ($request->header('X-Locale')
                    ?: ($request->cookie('neriah_locale') ?: (session('locale') ?: ($blueprint->user_metadata['output_locale'] ?? app()->getLocale()))));
                $locale = in_array(strtolower((string)$locale), ['en', 'id'], true) ? strtolower((string)$locale) : 'id';

                $additionalContext = [
                    'feature_id' => $request->input('feature_id'),
                    'spec_index' => $request->filled('spec_index') ? (int) $request->input('spec_index') : null,
                    'locale' => $locale,
                ];

                $result = $refinementService->refineSegment(
                    $blueprint,
                    $key,
                    $request->input('prompt'),
                    $request->input('ai_provider'),
                    $additionalContext
                );

                return response()->json($result);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Refinement failed: '.$e->getMessage(), [
                    'exception' => $e,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperdalam segmen: '.$e->getMessage(),
                ], 422);
            }
        }

        return response()->json(['success' => false, 'message' => 'Mode tidak valid.'], 400);
    }

    /**
     * Generate an AI-elaborated directive instruction grounded in project source corpus & world-class standards.
     */
    public function elaborateDirective(Request $request, string $slug, BlueprintRefinementService $refinementService): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $request->validate([
            'segment_key' => 'required|string',
            'ai_provider' => 'nullable|string',
            'custom_title' => 'nullable|string',
            'feature_id' => 'nullable|string',
            'feature_content' => 'nullable',
            'locale' => 'nullable|string|in:id,en',
        ]);

        $segmentKey = $request->input('segment_key');
        $aiProvider = $request->input('ai_provider');
        $customTitle = $request->input('custom_title');
        $featureId = $request->input('feature_id');
        $featureContent = is_string($request->input('feature_content')) 
            ? $request->input('feature_content') 
            : (is_array($request->input('feature_content')) ? json_encode($request->input('feature_content')) : null);

        $locale = $request->input('locale') 
            ?: ($request->header('X-Locale') 
            ?: ($request->cookie('neriah_locale') ?: app()->getLocale()));
        $locale = $locale === 'en' ? 'en' : 'id';
        $isEn = $locale === 'en';

        try {
            $directive = $refinementService->elaborateDirective(
                $blueprint, 
                $segmentKey, 
                $aiProvider, 
                $customTitle, 
                $featureId, 
                $featureContent,
                $locale
            );

            return response()->json([
                'success' => true,
                'directive' => $directive,
                'message' => $isEn 
                    ? 'Refinement directive successfully compiled from Project Documents & Global Standards.'
                    : 'Instruksi penajaman berhasil dielaborasi dari Dokumen Acuan & Standar Industri.',
                'message_en' => 'Refinement directive successfully compiled from Project Documents & Global Standards.',
                'message_id' => 'Instruksi penajaman berhasil dielaborasi dari Dokumen Acuan & Standar Industri.',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Elaborate directive failed: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => $isEn 
                    ? 'Failed to elaborate directive: '.$e->getMessage()
                    : 'Gagal mengelaborasi instruksi: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Dedicated AI KPI Elaboration Engine with Cascading Architectural Impact.
     * Deep-dives into business metrics and cascades changes to Features, Actors, and UAT.
     */
    public function elaborateKpiCascade(Request $request, BlueprintKpiCascadeService $kpiCascadeService, ?string $slug = null): JsonResponse
    {
        $blueprint = null;
        if (!empty($slug)) {
            $blueprint = VisionBlueprint::where('slug', $slug)->first();
            if ($blueprint) {
                $this->authorizeBlueprintAccess($blueprint);
            }
        }

        $payload = $request->validate([
            'tujuanUtama' => 'nullable|string|max:50000',
            'tujuan_utama' => 'nullable|string|max:50000',
            'masalahUtama' => 'nullable|string|max:50000',
            'masalah_utama' => 'nullable|string|max:50000',
            'namaBisnis' => 'nullable|string|max:255',
            'nama_bisnis' => 'nullable|string|max:255',
            'deskripsiBisnis' => 'nullable|string|max:50000',
            'deskripsi_bisnis' => 'nullable|string|max:50000',
            'fiturUtama' => 'nullable|string|max:100000',
            'fitur_utama' => 'nullable|string|max:100000',
            'aktorSistem' => 'nullable|string|max:50000',
            'aktor_sistem' => 'nullable|string|max:50000',
            'skenarioGherkin' => 'nullable|string|max:50000',
            'skenario_gherkin' => 'nullable|string|max:50000',
            'primarySector' => 'nullable|string|max:120',
            'primary_sector' => 'nullable|string|max:120',
            'locale' => 'nullable|string|in:id,en',
        ]);

        try {
            $result = $kpiCascadeService->elaborateAndCascade($payload, $blueprint);

            return response()->json($result);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Elaborate KPI Cascade failed: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengelaborasi KPI: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Slug-specific wrapper for KPI Cascade elaboration.
     */
    public function elaborateKpiCascadeSlug(Request $request, string $slug, BlueprintKpiCascadeService $kpiCascadeService): JsonResponse
    {
        return $this->elaborateKpiCascade($request, $kpiCascadeService, $slug);
    }

}
