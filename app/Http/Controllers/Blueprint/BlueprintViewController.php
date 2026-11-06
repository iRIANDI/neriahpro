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

class BlueprintViewController extends Controller
{
    use AuthorizesBlueprintAccess;

    /**
     * Resolve whether active session/request is English (Tier 1 Locale).
     */
    protected function resolveIsEn(VisionBlueprint $blueprint): bool
    {
        $requested = request()->query('locale') ?: request()->query('lang');
        if ($requested) {
            return $requested === 'en';
        }
        $cookie = request()->cookie('neriah_locale');
        if ($cookie) {
            return $cookie === 'en';
        }
        if (session()->has('locale')) {
            return session('locale') === 'en';
        }
        $blueprintLocale = $blueprint->user_metadata['output_locale'] ?? $blueprint->user_metadata['locale'] ?? null;
        if ($blueprintLocale) {
            return $blueprintLocale === 'en';
        }
        return app()->getLocale() === 'en';
    }

    /**
     * Show the generated Ultimate PRD & Blueprint for a specific project slug.
     */
    public function show(string $slug): View
    {
        $globalSettings = CmsGlobalSetting::getAllCached();
        $isBlueprintEnabled = (bool) ($globalSettings['feature_enable_vision_blueprint']->value ?? true);

        if (! $isBlueprintEnabled && ! auth()->user()?->isSuperAdmin()) {
            abort(404);
        }

        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $isEn = $this->resolveIsEn($blueprint);

        // Auto-heal: If this is a Retail License (or has a retail tier / paid order) but is_published is false, publish it immediately!
        if ($blueprint->isRetailLicense() && ! $blueprint->is_published) {
            $blueprint->update([
                'is_published' => true,
                'project_status' => 'Retail License',
            ]);
            $blueprint->refresh();
        }

        $isScopeLocked = $blueprint->isScopeFrozen();
        $isDpConfirmed = $blueprint->isDpConfirmed();
        $isContractSigned = $blueprint->isContractSigned();
        $contractDocument = $blueprint->getContractDocument();

        // Ensure PRD content is populated or regenerate if requested and scope is not locked
        if (empty($blueprint->prd_content) || ! isset($blueprint->prd_content['engineering_specs']) || (request()->has('regenerate') && ! $isScopeLocked)) {
            if (!empty($blueprint->fitur_wajib)) {
                $isEn = ($blueprint->user_metadata['output_locale'] ?? app()->getLocale()) === 'en';
                $dom = BlueprintDiscoveryService::detectDomain(
                    $blueprint->nama_bisnis . ' ' . $blueprint->masalah_utama . ' ' . $blueprint->fitur_wajib . ' ' . $blueprint->fitur_tambahan
                );
                $lines = preg_split('/[\r\n]+/', trim($blueprint->fitur_wajib));
                $craftedLines = [];
                foreach ($lines as $idx => $line) {
                    $trimmed = trim($line, " \t\n\r\0\x0B-•*1234567890.)");
                    if (empty($trimmed)) continue;
                    $crafted = BlueprintDiscoveryService::craftFeatureFromRawItem($trimmed, $dom, $isEn);
                    $craftedLines[] = ($idx + 1) . '. ' . $crafted;
                }
                if (!empty($craftedLines)) {
                    $blueprint->fitur_wajib = implode("\n", $craftedLines);
                    $blueprint->save();
                }
            }
            $blueprint->generateAndSavePrd();
            $blueprint->refresh();
        }

        // Auto-heal mismatched or stale Business ROI and Virtual Charts if scope is not frozen
        if (! $isScopeLocked && ! empty($blueprint->prd_content)) {
            $prd = $blueprint->prd_content;
            $needsPrdSave = false;
            $isEn = ($blueprint->user_metadata['output_locale'] ?? app()->getLocale()) === 'en';

            // 1. Check ROI Domain Mismatch (e.g. Tour & Travel showing Villa/Real Estate)
            $corpus = strtolower($blueprint->nama_bisnis . ' ' . ($blueprint->masalah_utama ?? '') . ' ' . ($blueprint->tujuan_utama ?? '') . ' ' . ($blueprint->fitur_wajib ?? ''));
            $isTravel = str_contains($corpus, 'tour') || str_contains($corpus, 'travel') || str_contains($corpus, 'wisata') ||
                        str_contains($corpus, 'itinerary') || str_contains($corpus, 'trip') || str_contains($corpus, 'overland') ||
                        str_contains($corpus, 'ngurah rai') || str_contains($corpus, 'gayabalical');
            
            $currentCat = $prd['business_roi_analysis']['domain_category'] ?? '';
            $isRoiMismatched = ($isTravel && (str_contains($currentCat, 'Villa') || str_contains($currentCat, 'Real Estate') || empty($currentCat)));

            if ($isRoiMismatched || request()->has('regenerate_roi') || request()->has('refresh_roi')) {
                $prd['business_roi_analysis'] = PrdGeneratorService::generateBusinessRoiAnalysis(
                    $blueprint,
                    $prd['itemized_cost_breakdown'] ?? PrdGeneratorService::calculateItemizedEstimation($blueprint),
                    $isEn
                );
                $needsPrdSave = true;
            }

            // 1.5 Auto-repair single-step workflow or workflow with truncated "..." words
            $hasWorkflowTruncation = false;
            foreach (($prd['workflow'] ?? []) as $wfStep) {
                $actText = $wfStep['action'] ?? '';
                if (str_contains($actText, '...') || str_contains($actText, 'Ban...') || preg_match('/\b\w+\.\.\./', $actText)) {
                    $hasWorkflowTruncation = true;
                    break;
                }
            }

            if ((count($prd['workflow'] ?? []) <= 1 || $hasWorkflowTruncation) && !empty($blueprint->alur_kerja)) {
                $reparsedWorkflow = PrdGeneratorService::parseWorkflow($blueprint->alur_kerja, $blueprint->nama_bisnis ?? '', $isEn);
                if (count($reparsedWorkflow) >= 1) {
                    $prd['workflow'] = $reparsedWorkflow;
                    $needsPrdSave = true;
                }
            }

            // 2. Check Virtual Charts Presence, Completeness, dynamic wrap compliance, and truncation repair
            $isChartsRefreshRequested = request()->has('regenerate_charts') || request()->has('refresh_charts');
            $existingWf = $prd['virtual_charts']['workflow_mermaid'] ?? '';
            $isWfUnwrapped = (bool) preg_match('/\["[^"]{60,}"\]/', $existingWf) || (!empty($existingWf) && !str_contains($existingWf, '<br/>'));
            $isWfTruncated = str_contains($existingWf, '...');

            if ($isChartsRefreshRequested || $isWfUnwrapped || $isWfTruncated || empty($prd['virtual_charts']) || empty($prd['virtual_charts']['erd_mermaid']) || empty($prd['virtual_charts']['workflow_mermaid']) || empty($prd['virtual_charts']['feature_dependency_mermaid'])) {
                $erdTables = $prd['erd_schema']['tables'] ?? [];
                $prd['virtual_charts'] = [
                    'workflow_mermaid' => PrdGeneratorService::generateWorkflowMermaid($prd['workflow'] ?? []),
                    'erd_mermaid' => PrdGeneratorService::generateErdMermaid($erdTables),
                    'feature_dep_mermaid' => PrdGeneratorService::generateFeatureDependencyMermaid($prd['target_actors']['items'] ?? [], $prd['engineering_specs']['mvp_phase_1']['items'] ?? [], $erdTables),
                    'feature_dependency_mermaid' => PrdGeneratorService::generateFeatureDependencyMermaid($prd['target_actors']['items'] ?? [], $prd['engineering_specs']['mvp_phase_1']['items'] ?? [], $erdTables),
                    'gantt_mermaid' => PrdGeneratorService::generateSprintGanttMermaid($blueprint, $blueprint->target_waktu ?? '30 Hari Kerja'),
                    'sprint_gantt_mermaid' => PrdGeneratorService::generateSprintGanttMermaid($blueprint, $blueprint->target_waktu ?? '30 Hari Kerja'),
                    'infra_mermaid' => PrdGeneratorService::generateInfrastructureMermaid($blueprint, $blueprint->user_metadata ?? []),
                    'infrastructure_mermaid' => PrdGeneratorService::generateInfrastructureMermaid($blueprint, $blueprint->user_metadata ?? []),
                    'mobile_sync_mermaid' => PrdGeneratorService::generateMobileSyncMermaid($blueprint->nama_bisnis ?: 'Proyek'),
                ];
                $needsPrdSave = true;
            }

            // 3. Auto-heal feature acceptance criteria if any contain raw strings or missing Gherkin
            $featureSpecs = $prd['engineering_specs']['mvp_specs'] ?? ($prd['features']['mvp_phase1'] ?? []);
            if (!empty($featureSpecs) && is_array($featureSpecs)) {
                $hasStaleAc = false;
                foreach ($featureSpecs as $fSpec) {
                    if (!empty($fSpec['acceptance_criteria']) && is_array($fSpec['acceptance_criteria'])) {
                        foreach ($fSpec['acceptance_criteria'] as $acItem) {
                            if (is_string($acItem) || (is_array($acItem) && (!isset($acItem['given']) || $acItem['given'] === '-'))) {
                                $hasStaleAc = true;
                                break 2;
                            }
                        }
                    }
                }

                if ($hasStaleAc) {
                    foreach ($featureSpecs as &$fSpec) {
                        $fSpec['acceptance_criteria'] = PrdGeneratorService::normalizeAcceptanceCriteria(
                            $fSpec['acceptance_criteria'] ?? [],
                            $fSpec['title'] ?? '',
                            $fSpec['user_story'] ?? ''
                        );
                    }
                    unset($fSpec);
                    $prd['engineering_specs']['mvp_specs'] = $featureSpecs;
                    $prd['features']['mvp_phase1'] = $featureSpecs;
                    $needsPrdSave = true;
                }
            }

            if ($needsPrdSave) {
                $blueprint->update(['prd_content' => $prd]);
                $blueprint->refresh();
            }
        }

        $viewPrd = $blueprint->prd_content ?? [];
        if (!empty($viewPrd['engineering_specs']['mvp_specs']) && is_array($viewPrd['engineering_specs']['mvp_specs'])) {
            foreach ($viewPrd['engineering_specs']['mvp_specs'] as &$vSpec) {
                if (isset($vSpec['acceptance_criteria'])) {
                    $vSpec['acceptance_criteria'] = PrdGeneratorService::normalizeAcceptanceCriteria(
                        $vSpec['acceptance_criteria'],
                        $vSpec['title'] ?? '',
                        $vSpec['user_story'] ?? ''
                    );
                }
            }
            unset($vSpec);
            $viewPrd['features']['mvp_phase1'] = $viewPrd['engineering_specs']['mvp_specs'];
        }

        return view('blueprint.show', [
            'blueprint' => $blueprint,
            'prd' => $viewPrd,
            'globalSettings' => $globalSettings,
            'isScopeLocked' => $isScopeLocked,
            'isDpConfirmed' => $isDpConfirmed,
            'isContractSigned' => $isContractSigned,
            'contractDocument' => $contractDocument,
            'locale' => $isEn ? 'en' : 'id',
            'isEn' => $isEn,
        ]);
    }

    /**
     * Recalculate and regenerate Business Feasibility & ROI Analysis for this blueprint (JSON API).
     */
    public function recalculateRoi(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        if ($blueprint->isScopeFrozen()) {
            return response()->json([
                'success' => false,
                'message' => 'Spesifikasi telah dikunci (Scope Frozen). Analisis ROI tidak dapat diubah.',
            ], 422);
        }

        $prd = $blueprint->prd_content ?? [];
        $isEn = $this->resolveIsEn($blueprint);
        $itemized = $prd['itemized_cost_breakdown'] ?? PrdGeneratorService::calculateItemizedEstimation($blueprint, [], $isEn);
        $newRoi = PrdGeneratorService::generateBusinessRoiAnalysis($blueprint, $itemized, $isEn);

        $prd['business_roi_analysis'] = $newRoi;
        
        // Also regenerate ERD and Virtual Charts to ensure domain alignment
        $erdTables = $prd['erd_schema']['tables'] ?? [];
        $prd['virtual_charts']['erd_mermaid'] = PrdGeneratorService::generateErdMermaid($erdTables);
        $prd['virtual_charts']['workflow_mermaid'] = PrdGeneratorService::generateWorkflowMermaid($prd['workflow'] ?? []);
        $prd['virtual_charts']['feature_dep_mermaid'] = PrdGeneratorService::generateFeatureDependencyMermaid($prd['target_actors']['items'] ?? [], $prd['engineering_specs']['mvp_phase_1']['items'] ?? [], $erdTables);
        $prd['virtual_charts']['feature_dependency_mermaid'] = $prd['virtual_charts']['feature_dep_mermaid'];
        $prd['virtual_charts']['gantt_mermaid'] = PrdGeneratorService::generateSprintGanttMermaid($blueprint, $blueprint->target_waktu ?? '30 Hari Kerja');
        $prd['virtual_charts']['sprint_gantt_mermaid'] = $prd['virtual_charts']['gantt_mermaid'];
        $prd['virtual_charts']['infra_mermaid'] = PrdGeneratorService::generateInfrastructureMermaid($blueprint, $blueprint->user_metadata ?? []);
        $prd['virtual_charts']['infrastructure_mermaid'] = $prd['virtual_charts']['infra_mermaid'];
        $prd['virtual_charts']['mobile_sync_mermaid'] = PrdGeneratorService::generateMobileSyncMermaid($blueprint->nama_bisnis ?: 'Proyek');

        $blueprint->update(['prd_content' => $prd]);
        $blueprint->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Analisis kelayakan bisnis & proyeksi ROI berhasil dihitung ulang sesuai visi proyek!',
            'data' => $newRoi,
        ]);
    }

    /**
     * Recalculate ROI and redirect back to the blueprint view.
     */
    public function recalculateRoiRedirect(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        if (! $blueprint->isScopeFrozen()) {
            $prd = $blueprint->prd_content ?? [];
            $isEn = $this->resolveIsEn($blueprint);
            $itemized = $prd['itemized_cost_breakdown'] ?? PrdGeneratorService::calculateItemizedEstimation($blueprint, [], $isEn);
            $newRoi = PrdGeneratorService::generateBusinessRoiAnalysis($blueprint, $itemized, $isEn);
            $prd['business_roi_analysis'] = $newRoi;
            $erdTables = $prd['erd_schema']['tables'] ?? [];
            $prd['virtual_charts']['erd_mermaid'] = PrdGeneratorService::generateErdMermaid($erdTables);
            $prd['virtual_charts']['workflow_mermaid'] = PrdGeneratorService::generateWorkflowMermaid($prd['workflow'] ?? []);
            $prd['virtual_charts']['feature_dep_mermaid'] = PrdGeneratorService::generateFeatureDependencyMermaid($prd['target_actors']['items'] ?? [], $prd['engineering_specs']['mvp_phase_1']['items'] ?? [], $erdTables);
            $prd['virtual_charts']['feature_dependency_mermaid'] = $prd['virtual_charts']['feature_dep_mermaid'];
            $prd['virtual_charts']['gantt_mermaid'] = PrdGeneratorService::generateSprintGanttMermaid($blueprint, $blueprint->target_waktu ?? '30 Hari Kerja');
            $prd['virtual_charts']['sprint_gantt_mermaid'] = $prd['virtual_charts']['gantt_mermaid'];
            $prd['virtual_charts']['infra_mermaid'] = PrdGeneratorService::generateInfrastructureMermaid($blueprint, $blueprint->user_metadata ?? []);
            $prd['virtual_charts']['infrastructure_mermaid'] = $prd['virtual_charts']['infra_mermaid'];
            $prd['virtual_charts']['mobile_sync_mermaid'] = PrdGeneratorService::generateMobileSyncMermaid($blueprint->nama_bisnis ?: 'Proyek');
            $blueprint->update(['prd_content' => $prd]);
            $blueprint->refresh();
        }

        return redirect()->route('blueprint.show', ['slug' => $slug])
            ->with('status', 'Analisis kelayakan bisnis dan ROI telah diperbarui sesuai visi proyek.');
    }

    /**
     * Regenerate Full PRD, Architecture Specs, ERD & Diagrams (API Endpoint).
     */
    public function regenerateFullBlueprint(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        if ($blueprint->signed_agreement || $blueprint->isDpConfirmed() || $blueprint->isRetailLicense()) {
            return response()->json([
                'success' => false,
                'message' => 'Scope blueprint telah terkunci oleh kontrak atau lisensi. Perubahan spesifikasi harus melalui prosedur Request Addendum.',
            ], 422);
        }

        // 1. Detect Domain accurately
        $isEn = $this->resolveIsEn($blueprint);
        $domain = BlueprintDiscoveryService::detectDomain(
            $blueprint->nama_bisnis . ' ' . $blueprint->masalah_utama . ' ' . $blueprint->fitur_wajib . ' ' . $blueprint->fitur_tambahan
        );

        // 2. Re-craft raw user feature inputs into formal blueprint items
        if (!empty($blueprint->fitur_wajib)) {
            $lines = preg_split('/[\r\n]+/', trim($blueprint->fitur_wajib));
            $craftedLines = [];
            foreach ($lines as $idx => $line) {
                $trimmed = trim($line, " \t\n\r\0\x0B-•*1234567890.)");
                if (empty($trimmed)) continue;
                $crafted = BlueprintDiscoveryService::craftFeatureFromRawItem($trimmed, $domain, $isEn);
                $craftedLines[] = ($idx + 1) . '. ' . $crafted;
            }
            if (!empty($craftedLines)) {
                $blueprint->fitur_wajib = implode("\n", $craftedLines);
            }
        }

        if (!empty($blueprint->fitur_tambahan)) {
            $lines = preg_split('/[\r\n]+/', trim($blueprint->fitur_tambahan));
            $craftedLines = [];
            foreach ($lines as $idx => $line) {
                $trimmed = trim($line, " \t\n\r\0\x0B-•*1234567890.)");
                if (empty($trimmed)) continue;
                $crafted = BlueprintDiscoveryService::craftFeatureFromRawItem($trimmed, $domain, $isEn);
                $craftedLines[] = ($idx + 1) . '. ' . $crafted;
            }
            if (!empty($craftedLines)) {
                $blueprint->fitur_tambahan = implode("\n", $craftedLines);
            }
        }

        $blueprint->save();

        // 3. Generate and Save Complete PRD
        $prd = $blueprint->generateAndSavePrd($isEn ? 'en' : 'id');
        $blueprint->refresh();
        $blueprint->document_sha256 = $blueprint->calculatePrdHash();
        $blueprint->save();

        return response()->json([
            'success' => true,
            'message' => 'Spesifikasi PRD, diagram arsitektur, ERD, dan formula ROI berhasil disintesis ulang dengan standar enterprise!',
            'data' => [
                'sha256' => $blueprint->document_sha256,
                'domain' => $domain,
                'features_count' => count($prd['engineering_specs']['mvp_phase_1']['items'] ?? []),
            ],
        ]);
    }

    /**
     * Web redirect fallback for full regeneration.
     */
    public function regenerateFullBlueprintRedirect(Request $request, string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        if (!$blueprint->signed_agreement && !$blueprint->isDpConfirmed() && !$blueprint->isRetailLicense()) {
            $isEn = $this->resolveIsEn($blueprint);
            $domain = BlueprintDiscoveryService::detectDomain(
                $blueprint->nama_bisnis . ' ' . $blueprint->masalah_utama . ' ' . $blueprint->fitur_wajib . ' ' . $blueprint->fitur_tambahan
            );

            if (!empty($blueprint->fitur_wajib)) {
                $lines = preg_split('/[\r\n]+/', trim($blueprint->fitur_wajib));
                $craftedLines = [];
                foreach ($lines as $idx => $line) {
                    $trimmed = trim($line, " \t\n\r\0\x0B-•*1234567890.)");
                    if (empty($trimmed)) continue;
                    $crafted = BlueprintDiscoveryService::craftFeatureFromRawItem($trimmed, $domain, $isEn);
                    $craftedLines[] = ($idx + 1) . '. ' . $crafted;
                }
                if (!empty($craftedLines)) {
                    $blueprint->fitur_wajib = implode("\n", $craftedLines);
                }
            }

            if (!empty($blueprint->fitur_tambahan)) {
                $lines = preg_split('/[\r\n]+/', trim($blueprint->fitur_tambahan));
                $craftedLines = [];
                foreach ($lines as $idx => $line) {
                    $trimmed = trim($line, " \t\n\r\0\x0B-•*1234567890.)");
                    if (empty($trimmed)) continue;
                    $crafted = BlueprintDiscoveryService::craftFeatureFromRawItem($trimmed, $domain, $isEn);
                    $craftedLines[] = ($idx + 1) . '. ' . $crafted;
                }
                if (!empty($craftedLines)) {
                    $blueprint->fitur_tambahan = implode("\n", $craftedLines);
                }
            }

            $blueprint->save();
            $blueprint->generateAndSavePrd($isEn ? 'en' : 'id');
            $blueprint->refresh();
            $blueprint->document_sha256 = $blueprint->calculatePrdHash();
            $blueprint->save();
        }

        return redirect()->route('blueprint.show', ['slug' => $slug])
            ->with('status', 'Spesifikasi PRD dan seluruh diagram arsitektur telah disintesis ulang dengan standar enterprise.');
    }

}
