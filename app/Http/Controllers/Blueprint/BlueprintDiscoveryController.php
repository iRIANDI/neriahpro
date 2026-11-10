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

class BlueprintDiscoveryController extends Controller
{
    use AuthorizesBlueprintAccess;

    /**
     * Show the public Project OS & Vision Blueprint Questionnaire form.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $globalSettings = CmsGlobalSetting::getAllCached();
        $isBlueprintEnabled = (bool) ($globalSettings['feature_enable_vision_blueprint']->value ?? true);

        if (! $isBlueprintEnabled && ! auth()->user()?->isSuperAdmin()) {
            abort(404);
        }

        $tier = $request->query('tier') ?: $request->query('package_tier') ?: $request->query('package');
        $slug = $request->query('slug');
        $draftId = $request->query('draft_id');
        $track = $request->query('track') ?: $request->query('mode');
        $isNew = $request->boolean('new') || $request->boolean('clean') || $request->has('new') || $request->has('clean');

        // If explicitly starting a new project (e.g. from customer portal launcher or reset action),
        // gracefully set default starter tier if not provided, avoiding redirect loops to /pricing.
        if ($isNew && ! $tier && ! $slug && ! $draftId) {
            $tier = ($track === 'mitra' || $track === 'studio')
                ? \App\Support\PricingRegistry::TIER_FULL_MVP
                : \App\Support\PricingRegistry::TIER_RETAIL_LITE;
        }

        // Direct Access & Satu Pintu Commercial Gate (v11.0):
        // Wajib memilih paket SaaS terlebih dahulu dari halaman pricing (/pricing#retail atau /pricing#project).
        // Hanya mengizinkan akses apabila memilih tier eksplisit (?tier=...), membuka blueprint sah (?slug=...), atau melanjutkan draft (?draft_id=...).
        // Query ?track=retail atau ?track=mitra TANPA tier dilarang menerobos gerbang harga!
        if (! $tier && ! $slug && ! $draftId) {
            return redirect($track === 'mitra' ? '/pricing#project' : '/pricing#retail');
        }

        // Defensive cleanup if draft_id contains query separators (e.g. "?track=...")
        if ($draftId && str_contains($draftId, '?')) {
            $draftId = explode('?', $draftId)[0];
        }
        if ($draftId && str_contains($draftId, '&')) {
            $draftId = explode('&', $draftId)[0];
        }

        // Fallback to active session draft if user refreshed without query parameters (Zero-Loss refresh)
        if (! $draftId && session()->has('blueprint_draft_id') && ! $request->boolean('reset') && ! $request->has('new') && ! $request->has('clean')) {
            $draftId = (string) session('blueprint_draft_id');
        }

        $user = auth()->user();
        $userHasRetailLicense = false;
        if ($user) {
            $userEmail = strtolower(trim((string) $user->email));
            $userHasRetailLicense = VisionBlueprint::where(function ($q) use ($user, $userEmail) {
                if ($userEmail) {
                    $q->whereRaw('LOWER(email) = ?', [$userEmail]);
                }
                $q->orWhere('user_metadata->user_id', $user->id);
            })
            ->where(function ($q) {
                $q->where('project_status', 'Retail License')
                    ->orWhereNotNull('user_metadata->retail_tier')
                    ->orWhereNotNull('user_metadata->package_tier')
                    ->orWhere('user_metadata->paid', true);
            })
            ->exists();
        }

        $initialData = [];

        $existingBp = null;
        $isExistingRetail = false;
        if ($slug) {
            $existingBp = VisionBlueprint::where('slug', $slug)->first();
            if ($existingBp) {
                $isExistingRetail = ($userHasRetailLicense
                    || $existingBp->isRetailLicense()
                    || $existingBp->project_status === 'Retail License'
                    || ($existingBp->user_metadata['track'] ?? '') === 'retail'
                    || ($existingBp->user_metadata['selected_track'] ?? '') === 'retail'
                    || ! empty($existingBp->user_metadata['retail_tier'])
                    || ! empty($existingBp->user_metadata['package_tier'])
                    || ! empty($existingBp->user_metadata['paid'])
                    || str_contains(strtolower($existingBp->slug ?? ''), '-retail-'));

                // Auto-unlock draft blueprint for editing if DP has not been paid and agreement is not signed
                if (! $existingBp->isDpConfirmed() && ! $existingBp->signed_agreement && $existingBp->isScopeFrozen()) {
                    try {
                        app(\App\Services\PosCommercialBridgeService::class)->unlockScope($existingBp, $user, 'Auto-unlock for draft editing');
                        $existingBp->refresh();
                    } catch (\Throwable $e) {
                        // Safe fallback
                    }
                }

                if ($existingBp->isDpConfirmed() && ! $isExistingRetail && ! auth()->user()?->isSuperAdmin()) {
                    return redirect()->route('blueprint.show', $existingBp->slug)
                        ->with('info', 'Proyek ini telah memiliki pembayaran DP terkonfirmasi.');
                }

                $existingTier = $existingBp->user_metadata['package_tier'] ?? $existingBp->user_metadata['retail_tier'] ?? ($isExistingRetail ? 'lite' : null);

                $initialData = [
                    'namaBisnis' => $existingBp->nama_bisnis,
                    'clientName' => $existingBp->client_name,
                    'email' => $existingBp->email,
                    'phone' => $existingBp->phone,
                    'masalahUtama' => $existingBp->masalah_utama,
                    'tujuanUtama' => $existingBp->tujuan_utama,
                    'targetAudiens' => $existingBp->target_audiens,
                    'aktorSistem' => $existingBp->aktor_sistem,
                    'fiturWajib' => $existingBp->fitur_wajib,
                    'fiturTambahan' => $existingBp->fitur_tambahan,
                    'alurKerja' => $existingBp->alur_kerja,
                    'kebutuhanIntegrasi' => $existingBp->kebutuhan_integrasi,
                    'referensiDesain' => $existingBp->referensi_desain,
                    'kesiapanAset' => $existingBp->kesiapan_aset,
                    'durasiHari' => $existingBp->durasi_hari ?? '30',
                    'targetWaktu' => $existingBp->target_waktu,
                    'skalaPengguna' => $existingBp->user_metadata['skala_pengguna'] ?? '0 - 100.000 Pengguna / Bulan (Dedicated VPS Monolith)',
                    'jangkauanPasar' => $existingBp->user_metadata['jangkauan_pasar'] ?? 'Domestik Indonesia (IDR, Zona WIB/WITA/WIT)',
                    'outOfScope' => $existingBp->user_metadata['out_of_scope'] ?? '',
                    'kepatuhanKeamanan' => $existingBp->user_metadata['kepatuhan_keamanan'] ?? 'Standar Web Application & OWASP Top 10 (CSRF, XSS, HTTPS)',
                    'kisaranBudget' => ($isExistingRetail && (! empty($existingBp->user_metadata['kisaran_budget']) && ! str_contains($existingBp->user_metadata['kisaran_budget'], '15.000.000') && ! str_contains($existingBp->user_metadata['kisaran_budget'], 'Growth') && ! str_contains($existingBp->user_metadata['kisaran_budget'], '50.000.000') && ! str_contains($existingBp->user_metadata['kisaran_budget'], 'Advisory') && ! str_contains($existingBp->user_metadata['kisaran_budget'], 'UMKM')))
                        ? $existingBp->user_metadata['kisaran_budget']
                        : ($isExistingRetail ? 'Pro: Rp 399.000 (Bundel Lengkap DDL, 7-Fase Prompt AI, Docker & Seeder)' : ($existingBp->user_metadata['kisaran_budget'] ?? 'Rp 15.000.000 - Rp 35.000.000 (Growth Monolith)')),
                    'targetPlatform' => $existingBp->user_metadata['target_platform'] ?? 'Modern Web Application Responsive & PWA (Desktop, Tablet & Mobile)',
                    'migrasiData' => $existingBp->user_metadata['migrasi_data'] ?? 'Database Baru Bersih (Input Mandiri & Dukungan Impor Template Excel/CSV)',
                    'preferensiHosting' => $existingBp->user_metadata['preferensi_hosting'] ?? 'Managed Dedicated Cloud VPS Neriah Pro (PostgreSQL 16, Redis, Backup)',
                    'garansiSla' => ($isExistingRetail && (! empty($existingBp->user_metadata['garansi_sla']) && ! str_contains($existingBp->user_metadata['garansi_sla'], 'Garansi Bug') && ! str_contains($existingBp->user_metadata['garansi_sla'], 'Pascameluncur') && ! str_contains($existingBp->user_metadata['garansi_sla'], 'Private Repo')))
                        ? $existingBp->user_metadata['garansi_sla']
                        : ($isExistingRetail ? 'Lisensi Mandiri: Penyerahan Langsung Dokumen PRD Fortune 500, Skema DDL Strict ULID & Docker Starter Kit' : ($existingBp->user_metadata['garansi_sla'] ?? '30 Hari Garansi Bug Pascameluncur + Penyerahan Akses Penuh Private Repo GitHub')),
                    'terminPembayaran' => ($isExistingRetail && (! empty($existingBp->user_metadata['termin_pembayaran']) && ! str_contains($existingBp->user_metadata['termin_pembayaran'], '50/50') && ! str_contains($existingBp->user_metadata['termin_pembayaran'], 'Milestone') && ! str_contains($existingBp->user_metadata['termin_pembayaran'], 'DP Kickoff')))
                        ? $existingBp->user_metadata['termin_pembayaran']
                        : ($isExistingRetail ? 'Pembayaran Penuh 100% Instan: QRIS / Virtual Account Midtrans Snap (Langsung Unduh Berkas)' : ($existingBp->user_metadata['termin_pembayaran'] ?? 'Termin Standar 50/50: 50% DP Kickoff & 50% Pelunasan setelah lolos UAT & Serah Terima Kunci (via Midtrans Snap)')),
                    'primarySector' => $existingBp->user_metadata['primary_sector'] ?? ($existingBp->prd_content['composable_matrix']['primary_sector'] ?? ''),
                    'selectedPrimitives' => $existingBp->user_metadata['selected_primitives'] ?? ($existingBp->prd_content['composable_matrix']['injected_primitives'] ?? []),
                    'selectedMenus' => $existingBp->user_metadata['selected_menus'] ?? [],
                    'selectedUiVariant' => $existingBp->user_metadata['selected_ui_variant'] ?? null,
                    'navigationShell' => $existingBp->user_metadata['navigation_shell'] ?? null,
                    'publicUxPreset' => $existingBp->user_metadata['public_ux_preset'] ?? null,
                    'outputLocale' => $existingBp->user_metadata['output_locale'] ?? 'id',
                    'personaMode' => $existingBp->user_metadata['persona_mode'] ?? 'BUSINESS_WIZARD',
                    'stackPreset' => PosStackPresetCatalog::normalizePresetCode(
                        $existingBp->user_metadata['stack_preset']
                        ?? ($existingBp->prd_content['universal_blueprint_schema']['project_metadata']['stack_preset'] ?? null)
                    ),
                    '_meta' => [
                        'is_editing_slug' => $slug,
                        'draft_token' => 'edit_'.$slug,
                        'is_fresh_workspace' => false,
                        'raw_idea_text' => $existingBp->masalah_utama,
                    ],
                ];

                if ($isExistingRetail) {
                    $initialData['selected_track'] = 'retail';
                    $initialData['track'] = 'retail';
                    if ($existingTier) {
                        $initialData['selected_tier'] = $existingTier;
                        $initialData['tier'] = $existingTier;
                        $initialData['package_tier'] = $existingTier;
                    }
                }
            }
        } elseif ($draftId && ! $request->boolean('reset') && ! $request->has('new') && ! $request->has('clean')) {
            if (Cache::has('blueprint_draft_'.$draftId)) {
                $initialData = Cache::get('blueprint_draft_'.$draftId, []);
                if (is_array($initialData) && ! empty($initialData)) {
                    $initialData['_meta'] = array_merge($initialData['_meta'] ?? [], [
                        'draft_token' => $draftId,
                        'is_fresh_workspace' => false,
                    ]);
                }
            } else {
                $initialData = [];
            }
        } else {
            // HERMETIC ISOLATION (v6.0): Every fresh visit without an explicit ?slug=... or ?draft_id=...
            // MUST start 100% clean with a brand-new draft_token — ZERO bleed from previous projects!
            session()->forget('blueprint_draft');
            if ($draftId) {
                Cache::forget('blueprint_draft_'.$draftId);
            }
            $freshToken = 'draft_'.strtolower((string) Str::ulid());
            $initialData = [
                '_meta' => [
                    'draft_token' => $freshToken,
                    'is_fresh_workspace' => true,
                ],
            ];
        }

        if (! is_array($initialData)) {
            $initialData = [];
        }

        // Forward selected track if present in query string
        if ($track) {
            $initialData['selected_track'] = $track;
            $initialData['track'] = $track;
        }

        $tier = $tier ?: ($initialData['selected_tier'] ?? null);
        $track = $track ?: ($initialData['selected_track'] ?? null);

        if ($tier) {
            $initialData['selected_tier'] = $tier;
            $initialData['tier'] = $tier;
            $initialData['package_tier'] = $tier;
        }

        $user = auth()->user();
        $userHasRetailLicense = false;
        if ($user) {
            $userEmail = strtolower(trim((string) $user->email));
            $userHasRetailLicense = VisionBlueprint::where(function ($q) use ($user, $userEmail) {
                if ($userEmail) {
                    $q->whereRaw('LOWER(email) = ?', [$userEmail]);
                }
                $q->orWhere('user_metadata->user_id', $user->id);
            })
            ->where(function ($q) {
                $q->where('project_status', 'Retail License')
                    ->orWhereNotNull('user_metadata->retail_tier')
                    ->orWhereNotNull('user_metadata->package_tier')
                    ->orWhere('user_metadata->paid', true);
            })
            ->exists();
        }

        $normalizedTrack = ($track === 'mitra' ? 'studio' : $track);
        $isStudioTrack = ($normalizedTrack === 'studio' || in_array(strtolower((string) $tier), ['full_mvp', 'umkm_starter', 'blueprint_advisory']));

        $isRetail = ! $isStudioTrack && (
            $isExistingRetail
            || ($userHasRetailLicense && ! $isNew && ! $normalizedTrack)
            || $normalizedTrack === 'retail'
            || in_array(strtolower((string) $tier), ['spark', 'starter', 'pro', 'ultimate', 'lite', 'retail_spark', 'retail_starter', 'retail_pro', 'retail_ultimate'])
        );

        if ($isStudioTrack) {
            $initialData['selected_track'] = 'studio';
            $initialData['track'] = 'studio';
            $initialData['is_retail_customer'] = false;
            $initialData['has_paid_retail_license'] = false;
        } elseif ($isRetail) {
            $initialData['is_retail_customer'] = true;
            $initialData['is_existing_retail'] = $isExistingRetail;
            $initialData['has_paid_retail_license'] = $isExistingRetail ? $userHasRetailLicense : false;
            $initialData['selected_track'] = 'retail';
            $initialData['track'] = 'retail';
            if ($tier) {
                $initialData['selected_tier'] = $tier;
                $initialData['tier'] = $tier;
                $initialData['package_tier'] = $tier;
            }
            if (empty($initialData['kisaranBudget']) || str_contains($initialData['kisaranBudget'], 'Growth') || str_contains($initialData['kisaranBudget'], '15.000.000')) {
                $tierLower = strtolower((string) ($tier ?: 'lite'));
                $satuPintu = PricingRegistry::getSatuPintuCatalog();
                if ($tierLower === 'starter' || $tierLower === 'lite' || $tierLower === 'retail_starter' || $tierLower === 'retail_lite') {
                    $liteLabel = $satuPintu[PricingRegistry::TIER_RETAIL_LITE]['formatted_price'] ?? 'Rp 99.000';
                    $initialData['kisaranBudget'] = "Lite: {$liteLabel} (PRD 26 Parameter, Schema DDL & AI Directives)";
                    $initialData['terminPembayaran'] = 'Pembayaran Penuh 100% Instan: QRIS / Virtual Account Midtrans Snap (Langsung Unduh Berkas)';
                } elseif ($tierLower === 'pro' || $tierLower === 'retail_pro') {
                    $proLabel = $satuPintu[PricingRegistry::TIER_RETAIL_PRO]['formatted_price'] ?? 'Rp 399.000';
                    $initialData['kisaranBudget'] = "Pro: {$proLabel} (Bundel Lengkap DDL, Docker Compose & Seeder)";
                    $initialData['terminPembayaran'] = 'Pembayaran Penuh 100% Instan: QRIS / Virtual Account Midtrans Snap (Langsung Unduh Berkas)';
                } elseif ($tierLower === 'ultimate' || $tierLower === 'retail_ultimate') {
                    $ultLabel = $satuPintu[PricingRegistry::TIER_RETAIL_ULTIMATE]['formatted_price'] ?? 'Rp 1.490.000';
                    $initialData['kisaranBudget'] = "Ultimate: {$ultLabel} (Full 10 Pilar Software Factory OS Turnkey Starter Kit)";
                    $initialData['terminPembayaran'] = 'Pembayaran Penuh 100% Instan: QRIS / Virtual Account Midtrans Snap (Langsung Unduh Berkas)';
                } else {
                    $initialData['kisaranBudget'] = 'Spark: Rp 0 (Akses Audit Arsitektur & Cetak Biru Gratis)';
                    $initialData['terminPembayaran'] = 'Paket Spark (Gratis): Rp 0 / Akses Audit Arsitektur & Cetak Biru Gratis 100%';
                }
            }
            if (empty($initialData['garansiSla']) || str_contains($initialData['garansiSla'], 'Garansi Bug') || str_contains($initialData['garansiSla'], 'Pascameluncur')) {
                $initialData['garansiSla'] = 'Lisensi Mandiri: Penyerahan Langsung Dokumen PRD Fortune 500, Skema DDL Strict ULID & Docker Starter Kit';
            }
        }

        return view('blueprint.create', [
            'globalSettings' => $globalSettings,
            'initialData' => $initialData,
        ]);
    }

    /**
     * Explicitly reset and purge current blueprint draft from session and cache.
     */
    public function resetDraft(Request $request): JsonResponse
    {
        session()->forget('blueprint_draft');
        session()->forget('blueprint_draft_id');

        if ($draftId = $request->input('draft_id')) {
            Cache::forget('blueprint_draft_'.$draftId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Draf formulir blueprint berhasil dibersihkan.',
        ]);
    }

    /**
     * Analyze user's raw idea text and uploaded documents/images via MarkItDown & AI Engine.
     */
    public function analyzeIdea(Request $request, BlueprintDiscoveryService $discoveryService): JsonResponse
    {
        // 1. Honeypot Anti-Spam Check
        if ($request->filled('_hp_check') || $request->filled('_website')) {
            return response()->json([
                'success' => true,
                'message' => 'Idea processed successfully.',
                'redirect_url' => route('blueprint.create'),
                'data' => [],
            ], 200);
        }

        // 2. Validate inputs
        $request->validate([
            'idea_text' => 'nullable|string|max:100000',
            'files.*' => 'nullable|file|max:15360|mimes:pdf,doc,docx,txt,md,rtf,csv,tsv,xlsx,pptx,png,jpg,jpeg,webp',
            'locale' => 'nullable|string|in:id,en',
            'ai_model' => 'nullable|string|max:50',
            'ai_provider' => 'nullable|string|max:50',
            'primary_sector' => 'nullable|string|max:80',
            'selected_primitives' => 'nullable',
            'stack_preset' => 'nullable|string|max:120',
            'stackPreset' => 'nullable|string|max:120',
        ]);

        $rawIdeaText = $request->input('idea_text', '') ?: '';
        $files = $request->file('files', []) ?: [];
        if (! is_array($files)) {
            $files = [$files];
        }
        $locale = $request->input('locale', app()->getLocale() ?: 'id');
        $projectName = $request->input('nama_bisnis') ?: $request->input('namaBisnis') ?: $request->input('project_name');
        $aiModel = $request->input('ai_model') ?: $request->input('ai_provider');
        $explicitSector = $request->input('primary_sector') ?: $request->input('primarySector');
        $explicitStackPreset = $request->input('stack_preset') ?: $request->input('stackPreset');
        $explicitTrack = $request->input('track') ?: $request->input('current_track') ?: $request->input('selected_track');
        $explicitTier = $request->input('tier') ?: $request->input('package_tier') ?: $request->input('selected_tier');
        $rawPrimitives = $request->input('selected_primitives') ?? $request->input('selectedPrimitives') ?? [];
        if (is_string($rawPrimitives)) {
            $decodedPrims = json_decode($rawPrimitives, true);
            $explicitPrimitives = is_array($decodedPrims) ? $decodedPrims : array_filter(array_map('trim', explode(',', $rawPrimitives)));
        } else {
            $explicitPrimitives = is_array($rawPrimitives) ? $rawPrimitives : [];
        }

        // 3. Spark Free Tier Quota Enforcement (2x per month per IP/Guest, auto-reset 1st of month)
        $user = auth()->user();
        if ($user) {
            $userEmail = strtolower(trim((string) $user->email));
            $userHasRetailLicense = VisionBlueprint::where(function ($q) use ($user, $userEmail) {
                if ($userEmail) {
                    $q->whereRaw('LOWER(email) = ?', [$userEmail]);
                }
                $q->orWhere('user_metadata->user_id', $user->id);
            })
            ->where(function ($q) {
                $q->where('project_status', 'Retail License')
                    ->orWhereNotNull('user_metadata->retail_tier')
                    ->orWhereNotNull('user_metadata->package_tier')
                    ->orWhere('user_metadata->paid', true);
            })
            ->exists();

            if ($userHasRetailLicense) {
                $explicitTrack = 'retail';
            }
        }
        $isExempt = $user && (
            $user->isSuperAdmin()
            || $user->hasRole('super_admin')
            || ($userHasRetailLicense ?? false)
            || (method_exists($user, 'hasRole') && ($user->hasRole('client_retail') || $user->hasRole('client_partner')))
        );
        $identifier = $user ? $user->email : ($request->ip() ?: '127.0.0.1');
        $monthKey = 'spark_quota_'.md5($identifier).'_'.now()->format('Y_m');
        $currentUsage = (int) Cache::get($monthKey, 0);

        if (! $isExempt) {
            $sparkLimitRaw = CmsGlobalSetting::getVal('pricing_retail_spark_limit', '2x Audit/Bulan');
            $maxSparkLimit = 2;
            if (preg_match('/(\d+)/', (string) $sparkLimitRaw, $matches)) {
                $maxSparkLimit = (int) $matches[1];
            }

            if ($currentUsage >= $maxSparkLimit) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'SPARK_QUOTA_EXCEEDED',
                    'message' => 'Batas kuota gratis paket Spark ('.$maxSparkLimit.'x analisis per bulan) telah tercapai untuk bulan ini. Kuota Anda akan di-reset otomatis pada awal bulan depan. Silakan login atau upgrade ke paket Lite / Pro untuk akses tak terbatas.',
                    'limit' => $maxSparkLimit,
                    'current_usage' => $currentUsage,
                    'reset_at' => now()->startOfMonth()->addMonth()->format('d M Y'),
                ], 429);
            }
        }

        try {
            $synthesized = $discoveryService->synthesize(
                $rawIdeaText,
                $files,
                $locale,
                $projectName,
                $aiModel,
                $explicitSector ?: null,
                $explicitPrimitives,
                $explicitStackPreset ?: null,
                $explicitTrack,
                $explicitTier
            );

            // Increment Spark quota usage upon successful synthesis
            if (! $isExempt) {
                Cache::put($monthKey, $currentUsage + 1, now()->endOfMonth()->addDay());
            }

            $draftId = (string) Str::ulid();
            Cache::put('blueprint_draft_'.$draftId, $synthesized, now()->addHours(24));
            session(['blueprint_draft' => $synthesized]);
            session(['blueprint_draft_id' => $draftId]);

            $aiTelemetry = $synthesized['_meta']['ai_telemetry'] ?? null;
            $failoverNotice = $aiTelemetry['notification'] ?? null;

            return response()->json([
                'success' => true,
                'message' => $locale === 'en'
                    ? 'Project blueprint synthesized successfully via MarkItDown & AI Engine.'
                    : 'Ide proyek berhasil dianalisis via MarkItDown & AI Engine.',
                'draft_id' => $draftId,
                'redirect_url' => route('blueprint.create', ['draft_id' => $draftId]),
                'project_name' => $synthesized['namaBisnis'] ?? 'Proyek Tanpa Nama',
                'domain' => $synthesized['domain'] ?? 'custom_portal',
                'track_recommendation' => $synthesized['track_recommendation'] ?? null,
                'converted_markdown' => $synthesized['_meta']['converted_markdown'] ?? '',
                'ai_telemetry' => $aiTelemetry,
                'notification' => $failoverNotice,
                'data' => $synthesized,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses ide: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Proactively integrate additional requirements or ideas into the active blueprint.
     */
    public function supplementIdea(Request $request, BlueprintDiscoveryService $discoveryService): JsonResponse
    {
        $request->validate([
            'supplement_text' => 'required|string|max:5000',
            'blueprint' => 'required|array',
            'locale' => 'nullable|string|in:id,en',
            'ai_model' => 'nullable|string|max:50',
            'ai_provider' => 'nullable|string|max:50',
        ]);

        $supplementText = $request->input('supplement_text');
        $currentBlueprint = $request->input('blueprint');
        $locale = $request->input('locale', 'id');
        $aiModel = $request->input('ai_model') ?: $request->input('ai_provider');

        // Revision Window & Scope Lock Enforcement for Existing Blueprints
        $editingSlug = $currentBlueprint['_meta']['is_editing_slug'] ?? $request->input('slug');
        if ($editingSlug) {
            $existingRecord = VisionBlueprint::where('slug', $editingSlug)->first();
            if ($existingRecord) {
                $user = auth()->user();
                $isExempt = $user && (
            $user->isSuperAdmin()
            || $user->hasRole('super_admin')
            || ($userHasRetailLicense ?? false)
            || (method_exists($user, 'hasRole') && ($user->hasRole('client_retail') || $user->hasRole('client_partner')))
        );

                // Studio MVP / Scope Lock Enforcement: Retail licenses are not locked by studio contracts
                if (! $isExempt && ! $existingRecord->isRetailLicense() && $existingRecord->isScopeFrozen()) {
                    return response()->json([
                        'success' => false,
                        'error_code' => 'SCOPE_LOCKED',
                        'message' => 'Ruang lingkup (scope) proyek ini telah dikunci (frozen) sesuai kontrak/DP aktif. Penambahan atau modifikasi ide memerlukan addendum kontrak resmi.',
                    ], 403);
                }

                if ($existingRecord->created_at && ! $isExempt) {
                    $tier = strtolower($existingRecord->user_metadata['package_tier'] ?? $existingRecord->user_metadata['retail_tier'] ?? 'lite');
                    $allowedDays = match ($tier) {
                        'retail_lite', 'lite' => 30,
                        'retail_pro', 'pro' => 180,
                        'retail_ultimate', 'ultimate' => 365,
                        default => 30,
                    };
                    $expirationDate = $existingRecord->created_at->copy()->addDays($allowedDays);
                    if (now()->greaterThan($expirationDate)) {
                        return response()->json([
                            'success' => false,
                            'error_code' => 'REVISION_WINDOW_EXPIRED',
                            'message' => 'Jendela revisi '.$allowedDays.' hari untuk paket '.ucfirst($tier).' telah berakhir pada '.$expirationDate->format('d M Y').'. Silakan upgrade paket atau hubungi Lead Architect Neriah Pro untuk perpanjangan.',
                            'expired_at' => $expirationDate->toIso8601String(),
                        ], 403);
                    }
                }
            }
        }

        try {
            $result = $discoveryService->supplementIdea($currentBlueprint, $supplementText, $locale, $aiModel);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'affected_fields' => $result['affected_fields'],
                'data' => $result['data'],
                'ai_telemetry' => $result['ai_telemetry'] ?? null,
                'notification' => $result['notification'] ?? null,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses ide tambahan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Auto-save blueprint draft in background (Dual-Tier Persistence: Cache & Session + Database when editing slug).
     */
    public function autoSave(Request $request): JsonResponse
    {
        $blueprint = $request->input('blueprint', []);
        $draftId = $request->input('draft_id') ?: ('draft_'.strtolower((string) Str::ulid()));
        $persistedToDb = false;

        if (! empty($blueprint) && is_array($blueprint)) {
            // HERMETIC ISOLATION (v6.0): Scope draft strictly to its unique draft_id cache key;
            // NEVER overwrite an existing DB VisionBlueprint unless explicitly editing a slug (`is_explicit_slug_edit === true`)!
            $isExplicitEdit = $request->has('is_explicit_slug_edit')
                ? $request->boolean('is_explicit_slug_edit')
                : (! empty($blueprint['_meta']['is_editing_slug']));

            $cleanDraft = $blueprint;
            if (! $isExplicitEdit) {
                unset($cleanDraft['_meta']['is_editing_slug']);
            }
            Cache::put('blueprint_draft_'.$draftId, $cleanDraft, now()->addDays(7));

            $slug = $isExplicitEdit
                ? (string) ($request->input('slug') ?: ($blueprint['_meta']['is_editing_slug'] ?? ''))
                : null;
            if ($slug === '') {
                $slug = null;
            }
            if ($slug) {
                $record = VisionBlueprint::where('slug', $slug)->first();
                if ($record) {
                    $user = auth()->user();
                    $isExempt = $user && (
            $user->isSuperAdmin()
            || $user->hasRole('super_admin')
            || ($userHasRetailLicense ?? false)
            || (method_exists($user, 'hasRole') && ($user->hasRole('client_retail') || $user->hasRole('client_partner')))
        );

                    // Studio MVP / Scope Lock Enforcement: Retail licenses are not locked by studio contracts
                    if (! $isExempt && ! $record->isRetailLicense() && $record->isScopeFrozen()) {
                        return response()->json([
                            'success' => false,
                            'error_code' => 'SCOPE_LOCKED',
                            'message' => 'Ruang lingkup (scope) proyek ini telah dikunci (frozen) sesuai kontrak/DP aktif. Penambahan atau modifikasi ide memerlukan addendum kontrak resmi.',
                        ], 403);
                    }

                    if (! $isExempt && $record->created_at) {
                        $tier = strtolower($record->user_metadata['package_tier'] ?? $record->user_metadata['retail_tier'] ?? 'lite');
                        $allowedDays = match ($tier) {
                            'retail_lite', 'lite' => 30,
                            'retail_pro', 'pro' => 180,
                            'retail_ultimate', 'ultimate' => 365,
                            default => 30,
                        };
                        $expirationDate = $record->created_at->copy()->addDays($allowedDays);
                        if (now()->greaterThan($expirationDate)) {
                            return response()->json([
                                'success' => false,
                                'error_code' => 'REVISION_WINDOW_EXPIRED',
                                'message' => 'Jendela revisi '.$allowedDays.' hari untuk paket '.ucfirst($tier).' telah berakhir pada '.$expirationDate->format('d M Y').'.',
                                'expired_at' => $expirationDate->toIso8601String(),
                            ], 403);
                        }
                    }

                    $meta = $record->user_metadata ?? [];
                    $meta['target_platform'] = $blueprint['targetPlatform'] ?? ($meta['target_platform'] ?? null);
                    $meta['migrasi_data'] = $blueprint['migrasiData'] ?? ($meta['migrasi_data'] ?? null);
                    $meta['preferensi_hosting'] = $blueprint['preferensiHosting'] ?? ($meta['preferensi_hosting'] ?? null);
                    $meta['garansi_sla'] = $blueprint['garansiSla'] ?? ($meta['garansi_sla'] ?? null);
                    $meta['termin_pembayaran'] = $blueprint['terminPembayaran'] ?? ($meta['termin_pembayaran'] ?? null);
                    $meta['skala_pengguna'] = $blueprint['skalaPengguna'] ?? ($meta['skala_pengguna'] ?? null);
                    $meta['jangkauan_pasar'] = $blueprint['jangkauanPasar'] ?? ($meta['jangkauan_pasar'] ?? null);
                    $meta['out_of_scope'] = $blueprint['outOfScope'] ?? ($meta['out_of_scope'] ?? null);
                    $meta['kepatuhan_keamanan'] = $blueprint['kepatuhanKeamanan'] ?? ($meta['kepatuhan_keamanan'] ?? null);
                    $meta['kisaran_budget'] = $blueprint['kisaranBudget'] ?? ($meta['kisaran_budget'] ?? null);
                    if (! empty($blueprint['primarySector'])) {
                        $meta['primary_sector'] = $blueprint['primarySector'];
                    }
                    if (isset($blueprint['selectedPrimitives']) && is_array($blueprint['selectedPrimitives'])) {
                        $meta['selected_primitives'] = $blueprint['selectedPrimitives'];
                    }
                    if (isset($blueprint['selectedMenus']) && is_array($blueprint['selectedMenus'])) {
                        $meta['selected_menus'] = $blueprint['selectedMenus'];
                    }
                    if (! empty($blueprint['stackPreset'])) {
                        $meta['stack_preset'] = PosStackPresetCatalog::normalizePresetCode($blueprint['stackPreset']);
                    }
                    if (! empty($blueprint['selectedUiVariant'])) {
                        $meta['selected_ui_variant'] = (string) $blueprint['selectedUiVariant'];
                    }
                    if (! empty($blueprint['navigationShell'])) {
                        $meta['navigation_shell'] = (string) $blueprint['navigationShell'];
                    }
                    if (! empty($blueprint['publicUxPreset'])) {
                        $meta['public_ux_preset'] = PosDualZoneFrontendAndSaasCatalog::normalizePublicUxPreset((string) $blueprint['publicUxPreset']);
                    }
                    if (! empty($blueprint['outputLocale'])) {
                        $meta['output_locale'] = PosInclusiveCommercialCatalog::normalizeOutputLocale((string) $blueprint['outputLocale']);
                    }
                    if (! empty($blueprint['personaMode'])) {
                        $meta['persona_mode'] = PosInclusiveCommercialCatalog::normalizePersonaMode((string) $blueprint['personaMode']);
                    }
                    if ($request->has('custom_folder') || isset($blueprint['customFolder']) || isset($blueprint['workspaceDir']) || isset($blueprint['short_slug'])) {
                        $rawFolder = (string) ($request->input('custom_folder') ?? ($blueprint['customFolder'] ?? ($blueprint['workspaceDir'] ?? ($blueprint['short_slug'] ?? ''))));
                        $cleanFolder = preg_replace('/[^a-z0-9_-]/', '', strtolower(str_replace(' ', '-', $rawFolder)));
                        $cleanFolder = trim($cleanFolder, '-_');
                        if (! empty($cleanFolder)) {
                            $meta['short_slug'] = $cleanFolder;
                        } else {
                            unset($meta['short_slug']);
                        }
                    }
                    $meta['last_autosaved_at'] = now()->toIso8601String();

                    $updatePayload = [
                        'user_metadata' => $meta,
                    ];
                    if (! empty($blueprint['namaBisnis'])) {
                        $updatePayload['nama_bisnis'] = $blueprint['namaBisnis'];
                    }
                    if (! empty($blueprint['clientName'])) {
                        $updatePayload['client_name'] = $blueprint['clientName'];
                    }
                    if (! empty($blueprint['email'])) {
                        $updatePayload['email'] = InputHygieneStandard::sanitizeStrictEmail((string) $blueprint['email']);
                    }
                    if (! empty($blueprint['phone'])) {
                        $updatePayload['phone'] = InputHygieneStandard::toE164((string) ($blueprint['countryCode'] ?? '+62'), (string) $blueprint['phone']);
                    }
                    if (! empty($blueprint['masalahUtama'])) {
                        $updatePayload['masalah_utama'] = $blueprint['masalahUtama'];
                    }
                    if (! empty($blueprint['tujuanUtama'])) {
                        $updatePayload['tujuan_utama'] = $blueprint['tujuanUtama'];
                    }
                    if (! empty($blueprint['targetAudiens'])) {
                        $updatePayload['target_audiens'] = $blueprint['targetAudiens'];
                    }
                    if (! empty($blueprint['aktorSistem'])) {
                        $updatePayload['aktor_sistem'] = $blueprint['aktorSistem'];
                    }
                    if (! empty($blueprint['fiturWajib'])) {
                        $updatePayload['fitur_wajib'] = $blueprint['fiturWajib'];
                    }
                    if (isset($blueprint['fiturTambahan'])) {
                        $updatePayload['fitur_tambahan'] = $blueprint['fiturTambahan'];
                    }
                    if (! empty($blueprint['alurKerja'])) {
                        $updatePayload['alur_kerja'] = $blueprint['alurKerja'];
                    }
                    if (isset($blueprint['kebutuhanIntegrasi'])) {
                        $updatePayload['kebutuhan_integrasi'] = $blueprint['kebutuhanIntegrasi'];
                    }
                    if (isset($blueprint['referensiDesain'])) {
                        $updatePayload['referensi_desain'] = $blueprint['referensiDesain'];
                    }
                    if (isset($blueprint['kesiapanAset'])) {
                        $updatePayload['kesiapan_aset'] = $blueprint['kesiapanAset'];
                    }
                    if (isset($blueprint['targetWaktu'])) {
                        $updatePayload['target_waktu'] = $blueprint['targetWaktu'];
                    }

                    $record->update($updatePayload);
                    $persistedToDb = true;
                }
            }
        }

        $tz = config('app.timezone', 'Asia/Jakarta');

        return response()->json([
            'success' => true,
            'draft_id' => $draftId,
            'persisted_to_db' => $persistedToDb,
            'saved_at' => now()->timezone($tz)->format('H:i:s').' WIB',
        ]);
    }

    /**
     * Return O(1) cached 12-Sector + 12-Primitive + Rule Pack Composable Matrix catalog + v8.0 Inclusive Catalog.
     */
    public function matrixCatalog(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'schema_uri' => ProjectOsBlueprintCompilerService::SCHEMA_URI,
            'catalog' => ProjectOsBlueprintCompilerService::getCachedCatalog(),
            'satu_pintu_packages' => PricingRegistry::getSatuPintuCatalog(),
            'inclusive_value_metric_packages' => PricingRegistry::getInclusiveValueMetricCatalog(),
            'storyboard_wizard' => PosInclusiveCommercialCatalog::storyboardWizardCatalog(),
            'sector_visual_ui_catalog' => PosInclusiveCommercialCatalog::allSectorVisualUiCatalog(),
            'navigation_shell_options' => PosInclusiveCommercialCatalog::navigationShellOptions(),
            'retention_and_security_matrix' => PosInclusiveCommercialCatalog::retentionAndSecurityMatrix(),
            'community_primitives_marketplace' => PosInclusiveCommercialCatalog::communityPrimitivesMarketplace(),
            'commercial_estimator_config' => PricingRegistry::getCommercialEstimatorConfig(),
            'commercial_bridge_features' => PosCommercialBridgeService::fiveCommercialBridgeFeatures(),
            'guided_client_journey' => PosCommercialBridgeService::guidedClientJourneySteps(),
            'agentshield_layers' => DocumentThreatSanitizerService::fiveLayersBlueprintContract('NeriahPro Project OS', 'STACK_M1_LARAVEL_MONOLITH', 'SECTOR_01_COMMERCE_OMNICHANNEL')['layers'],
            'sector_audit_presets' => PosAgentShieldAndCoDesignCatalog::twelveSectorAuditPresets(),
            'codesign_room_version' => PosAgentShieldAndCoDesignCatalog::VERSION,
            'public_ux_presets' => array_values(PosDualZoneFrontendAndSaasCatalog::sixPublicUxPresets()),
            'five_iron_animation_laws' => PosDualZoneFrontendAndSaasCatalog::fiveIronAnimationLaws(),
            'input_hygiene_contract' => PosDualZoneFrontendAndSaasCatalog::inputHygieneContract(),
            'six_critical_saas_modules' => PosDualZoneFrontendAndSaasCatalog::sixCriticalSaasModules(),
            'verified_testimonials_aniwall' => PosDualZoneFrontendAndSaasCatalog::getVerifiedTestimonialsCatalog(),
            'country_zones' => InputHygieneStandard::countryZonesForFrontend(),
            'v10_release' => PosDualZoneFrontendAndSaasCatalog::VERSION,
        ]);
    }

    /**
     * Project OS v7.0: Live Topological DAG, L1–L4 Menu Matrix, 5 Enterprise Glue, 5 Real-World Elements & 15 Pillars Preview API.
     */
    public function topologicalPreview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sector_code' => 'nullable|string|max:120',
            'primary_sector' => 'nullable|string|max:120',
            'primitive_codes' => 'nullable|array',
            'primitive_codes.*' => 'string|max:120',
            'selected_primitives' => 'nullable|array',
            'selected_primitives.*' => 'string|max:120',
            'stack_preset' => 'nullable|string|max:120',
            'selected_menus' => 'nullable|array',
            'selected_menus.*' => 'string|max:40',
            'selected_ui_variant' => 'nullable|string|max:80',
            'public_ux_preset' => 'nullable|string|max:80',
            'navigation_shell' => 'nullable|string|max:48',
            'output_locale' => 'nullable|string|max:16',
            'persona_mode' => 'nullable|string|max:32',
            'app_name' => 'nullable|string|max:160',
            'project_name' => 'nullable|string|max:160',
            'locale' => 'nullable|string|in:id,en',
        ]);

        $sectorCode = PosSectorUiArchetypeCatalog::normalizeSectorCode(
            (string) ($validated['sector_code'] ?? $validated['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL')
        );
        $stackCode = PosStackPresetCatalog::normalizePresetCode(
            (string) ($validated['stack_preset'] ?? 'STACK_M1_LARAVEL_MONOLITH')
        );
        $resolvedPrimitives = $validated['primitive_codes'] ?? $validated['selected_primitives'] ?? [];
        $resolvedPublicUx = ! empty($validated['public_ux_preset'])
            ? PosDualZoneFrontendAndSaasCatalog::normalizePublicUxPreset((string) $validated['public_ux_preset'], $sectorCode)
            : PosDualZoneFrontendAndSaasCatalog::defaultPublicUxForSector($sectorCode);

        $schema = ProjectOsBlueprintCompilerService::compileSchemaFromPayload([
            'app_name' => $validated['app_name'] ?? $validated['project_name'] ?? 'Enterprise Isolated Preview',
            'sector_code' => $sectorCode,
            'primitive_codes' => $resolvedPrimitives,
            'stack_preset_code' => $stackCode,
            'selected_menus' => $validated['selected_menus'] ?? null,
            'selected_ui_variant' => $validated['selected_ui_variant'] ?? null,
            'public_ux_preset' => $resolvedPublicUx,
            'navigation_shell' => $validated['navigation_shell'] ?? null,
            'output_locale' => $validated['output_locale'] ?? ($validated['locale'] ?? 'id'),
            'persona_mode' => $validated['persona_mode'] ?? null,
        ]);

        $quotation = app(PosCommercialBridgeService::class)->calculateScopeQuotation($schema, [
            'stack_preset' => $stackCode,
            'selected_ui_variant' => $validated['selected_ui_variant'] ?? null,
            'public_ux_preset' => $resolvedPublicUx,
            'navigation_shell' => $validated['navigation_shell'] ?? null,
            'output_locale' => $validated['output_locale'] ?? ($validated['locale'] ?? 'id'),
        ]);

        return response()->json([
            'success' => true,
            'guarantees_release' => $schema['guarantees_release'] ?? '10.0.0',
            'architectural_freeze' => $schema['architectural_freeze'] ?? [],
            'project_isolation_id' => $schema['project_metadata']['project_isolation_id'] ?? null,
            'visual_frontend_ui_contract' => $schema['visual_frontend_ui_contract'] ?? [],
            'dual_zone_ui_contract' => $schema['dual_zone_ui_contract'] ?? [],
            'input_hygiene_contract' => $schema['input_hygiene_contract'] ?? [],
            'jurisdiction_compliance_profile' => $schema['jurisdiction_compliance_profile'] ?? [],
            'living_cve_security_alerts' => $schema['living_cve_security_alerts'] ?? [],
            'five_deterministic_guarantees' => $schema['five_deterministic_guarantees'] ?? [],
            'five_enterprise_glue_primitives' => $schema['five_enterprise_glue_primitives'] ?? [],
            'five_real_world_elements' => PosRealWorldRuntimeCatalog::fiveRealWorldElementsOverview($validated['locale'] ?? app()->getLocale()),
            'fifteen_pillars_freeze_matrix' => $schema['fifteen_pillars_freeze_matrix'] ?? [],
            'hardware_iot_peripheral_contract' => $schema['hardware_iot_peripheral_contract'] ?? [],
            'realtime_websocket_contract' => $schema['realtime_websocket_contract'] ?? [],
            'strict_query_and_partition_contract' => $schema['strict_query_and_partition_contract'] ?? [],
            'syntax_lock_2026_contract' => $schema['syntax_lock_2026_contract'] ?? [],
            'client_sop_and_onboarding' => $schema['client_sop_and_onboarding'] ?? [],
            'agentshield_security_contract' => $schema['agentshield_security_contract'] ?? [],
            'approved_dependency_manifest' => $schema['approved_dependency_manifest'] ?? [],
            'cryptographic_audit_explorer_contract' => $schema['cryptographic_audit_explorer_contract'] ?? [],
            'codesign_alignment_contract' => $schema['codesign_alignment_contract'] ?? [],
            'world_class_task_matrix' => $schema['world_class_task_matrix'] ?? [],
            'cross_stage_causality_graph' => $schema['cross_stage_causality_graph'] ?? [],
            'live_commercial_sync' => [
                'quotation_idr' => $quotation['quotation_idr'] ?? 0,
                'formatted_quotation_idr' => $quotation['formatted_quotation_idr'] ?? 'Rp 0',
                'man_hours' => $quotation['man_hours'] ?? 0,
                'scope_hash_short' => $quotation['scope_hash_short'] ?? '',
                'scope_hash_full' => $quotation['scope_hash_full'] ?? '',
            ],
            'enterprise_menu_matrix' => $schema['enterprise_menu_matrix'] ?? [],
            'topological_dag_plan' => $schema['topological_dag_plan'] ?? [],
            'symbol_manifest' => app(\App\Services\Entitlement\BlueprintEntitlementGateService::class)->filterSymbolManifestForTier($schema['symbol_manifest'] ?? [], 'FREE_SANDBOX'),
            'sub_chunked_prompts' => array_values(array_map(fn (array $sp): array => [
                'part_code' => $sp['part_code'],
                'major_phase' => $sp['major_phase'],
                'filename' => $sp['filename'],
                'title' => $sp['title'],
                'dag_layer_badge' => $sp['dag_layer_badge'],
                'entities_count' => $sp['entities_count'],
                'estimated_loc' => $sp['estimated_loc'],
            ], $schema['sub_chunked_prompts'] ?? [])),
        ]);
    }

}
