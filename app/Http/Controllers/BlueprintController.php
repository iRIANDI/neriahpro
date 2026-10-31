<?php

namespace App\Http\Controllers;

use App\Models\VisionBlueprint;
use App\Models\BlueprintVoucher;
use App\Models\Document;
use App\Models\CmsGlobalSetting;
use App\Services\MidtransSnapService;
use App\Services\BlueprintDiscoveryService;
use App\Services\BlueprintRefinementService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BlueprintController extends Controller
{
    /**
     * Enforce strict security access to blueprint documents.
     * Prevents IDOR leaks on paid tiers (Lite, Pro, Ultimate, MVP).
     */
    private function authorizeBlueprintAccess(VisionBlueprint $blueprint): void
    {
        $user = Auth::user();
        
        // Lead Architects / Super Admins bypass
        if ($user && ($user->isSuperAdmin() || $user->hasRole('super_admin') || str_contains($user->email, 'neriahpro'))) {
            return;
        }

        // Guest access allowed for Free Grant / Spark Tier or public blueprints without paid retail tier
        $tier = $blueprint->user_metadata['retail_tier'] ?? $blueprint->user_metadata['package_tier'] ?? null;
        if ($tier === 'retail_spark' || $blueprint->is_free_grant || empty($blueprint->user_metadata) || ! $tier) {
            return;
        }

        // Paid tiers mandate login
        if (! $user) {
            abort(403, 'Akses Ditolak: Sesuai kebijakan, wajib login/daftar akun untuk mengakses & mengunduh dokumen lisensi berbayar Anda.');
        }

        // Check exact ownership
        $blueprintUserId = $blueprint->user_metadata['user_id'] ?? null;
        $blueprintEmail = $blueprint->email ?? '';

        if ($blueprintUserId !== $user->id && strtolower(trim($blueprintEmail)) !== strtolower(trim($user->email))) {
            abort(403, 'Akses Ditolak: Dokumen ini terproteksi hak cipta Neriah Pro dan hanya dapat diakses oleh pemilik lisensi terdaftar.');
        }
    }
    /**
     * Show the public Project OS & Vision Blueprint Questionnaire form.
     */
    public function create(Request $request): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $globalSettings = CmsGlobalSetting::getAllCached();
        $isBlueprintEnabled = (bool) ($globalSettings['feature_enable_vision_blueprint']->value ?? true);

        if (! $isBlueprintEnabled && ! auth()->user()?->isSuperAdmin()) {
            abort(404);
        }

        $tier = $request->query('tier') ?: $request->query('package_tier') ?: $request->query('package');
        $slug = $request->query('slug');
        $draftId = $request->query('draft_id');
        $track = $request->query('track');

        // Direct Access Guard:
        // Wajib memilih paket terlebih dahulu dari halaman pricing (/pricing#retail).
        // Jika pengguna langsung mengakses https://neriahpro.com/blueprint tanpa memilih paket
        // (tanpa query ?tier=..., ?package=..., ?slug=..., atau ?draft_id=...), arahkan ke halaman pricing.
        if (! $tier && ! $slug && ! $draftId && ! $track) {
            return redirect('/pricing#retail');
        }

        // Defensive cleanup if draft_id contains query separators (e.g. "?track=...")
        if ($draftId && str_contains($draftId, '?')) {
            $draftId = explode('?', $draftId)[0];
        }
        if ($draftId && str_contains($draftId, '&')) {
            $draftId = explode('&', $draftId)[0];
        }

        $initialData = [];

        if ($slug) {
            $existingBp = VisionBlueprint::where('slug', $slug)->first();
            if ($existingBp) {
                if ($existingBp->isScopeFrozen()) {
                    return redirect()->route('blueprint.show', $existingBp->slug);
                }
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
                    'kisaranBudget' => $existingBp->user_metadata['kisaran_budget'] ?? 'Rp 15.000.000 - Rp 35.000.000 (Growth Monolith)',
                    'targetPlatform' => $existingBp->user_metadata['target_platform'] ?? 'Modern Web Application Responsive & PWA (Desktop, Tablet & Mobile)',
                    'migrasiData' => $existingBp->user_metadata['migrasi_data'] ?? 'Database Baru Bersih (Input Mandiri & Dukungan Impor Template Excel/CSV)',
                    'preferensiHosting' => $existingBp->user_metadata['preferensi_hosting'] ?? 'Managed Dedicated Cloud VPS Neriah Pro (PostgreSQL 16, Redis, Backup)',
                    'garansiSla' => $existingBp->user_metadata['garansi_sla'] ?? '30 Hari Garansi Bug Pascameluncur + Penyerahan Akses Penuh Private Repo GitHub',
                    'terminPembayaran' => $existingBp->user_metadata['termin_pembayaran'] ?? 'Termin Standar 50/50: 50% DP Kickoff & 50% Pelunasan setelah lolos UAT & Serah Terima Kunci (via Midtrans Snap)',
                    'primarySector' => $existingBp->user_metadata['primary_sector'] ?? ($existingBp->prd_content['composable_matrix']['primary_sector'] ?? ''),
                    'selectedPrimitives' => $existingBp->user_metadata['selected_primitives'] ?? ($existingBp->prd_content['composable_matrix']['injected_primitives'] ?? []),
                    'stackPreset' => \App\Support\PosStackPresetCatalog::normalizePresetCode(
                        $existingBp->user_metadata['stack_preset']
                        ?? ($existingBp->prd_content['universal_blueprint_schema']['project_metadata']['stack_preset'] ?? null)
                    ),
                    '_meta' => [
                        'is_editing_slug' => $slug,
                        'raw_idea_text' => $existingBp->masalah_utama,
                    ]
                ];
            }
        } elseif ($request->boolean('reset') || $request->has('new') || $request->has('clean')) {
            session()->forget('blueprint_draft');
            if ($draftId) {
                Cache::forget('blueprint_draft_' . $draftId);
            }
            $initialData = [];
        } elseif ($draftId && Cache::has('blueprint_draft_' . $draftId)) {
            $initialData = Cache::get('blueprint_draft_' . $draftId, []);
        } elseif (session()->has('blueprint_draft')) {
            // Robust session fallback
            $initialData = session('blueprint_draft', []);
        }

        if (!is_array($initialData)) {
            $initialData = [];
        }

        // Forward selected track if present in query string
        if ($track) {
            $initialData['selected_track'] = $track;
            $initialData['track'] = $track;
        }

        if ($tier) {
            $initialData['selected_tier'] = $tier;
            $initialData['tier'] = $tier;
            $initialData['package_tier'] = $tier;
        }

        $isRetail = ($track === 'retail' || in_array(strtolower((string) $tier), ['spark', 'starter', 'pro', 'ultimate', 'lite', 'retail_spark', 'retail_starter', 'retail_pro', 'retail_ultimate']));
        if ($isRetail) {
            $initialData['selected_track'] = 'retail';
            $initialData['track'] = 'retail';
            if (empty($initialData['kisaranBudget']) || str_contains($initialData['kisaranBudget'], 'Growth') || str_contains($initialData['kisaranBudget'], '15.000.000')) {
                $tierLower = strtolower((string) $tier);
                $satuPintu = \App\Support\PricingRegistry::getSatuPintuCatalog();
                if ($tierLower === 'starter' || $tierLower === 'lite' || $tierLower === 'retail_starter' || $tierLower === 'retail_lite') {
                    $liteLabel = $satuPintu[\App\Support\PricingRegistry::TIER_RETAIL_LITE]['formatted_price'] ?? 'Rp 99.000';
                    $initialData['kisaranBudget'] = "Lite: {$liteLabel} (PRD 26 Parameter, Schema DDL & AI Directives)";
                    $initialData['terminPembayaran'] = 'Pembayaran Penuh 100% Instan: QRIS / Virtual Account Midtrans Snap (Langsung Unduh Berkas)';
                } elseif ($tierLower === 'pro' || $tierLower === 'retail_pro') {
                    $proLabel = $satuPintu[\App\Support\PricingRegistry::TIER_RETAIL_PRO]['formatted_price'] ?? 'Rp 399.000';
                    $initialData['kisaranBudget'] = "Pro: {$proLabel} (Bundel Lengkap DDL, Docker Compose & Seeder)";
                    $initialData['terminPembayaran'] = 'Pembayaran Penuh 100% Instan: QRIS / Virtual Account Midtrans Snap (Langsung Unduh Berkas)';
                } elseif ($tierLower === 'ultimate' || $tierLower === 'retail_ultimate') {
                    $ultLabel = $satuPintu[\App\Support\PricingRegistry::TIER_RETAIL_ULTIMATE]['formatted_price'] ?? 'Rp 1.490.000';
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

        if ($draftId = $request->input('draft_id')) {
            Cache::forget('blueprint_draft_' . $draftId);
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
                'data' => []
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
        if (!is_array($files)) {
            $files = [$files];
        }
        $locale = $request->input('locale', app()->getLocale() ?: 'id');
        $projectName = $request->input('nama_bisnis') ?: $request->input('namaBisnis') ?: $request->input('project_name');
        $aiModel = $request->input('ai_model') ?: $request->input('ai_provider');
        $explicitSector = $request->input('primary_sector') ?: $request->input('primarySector');
        $explicitStackPreset = $request->input('stack_preset') ?: $request->input('stackPreset');
        $rawPrimitives = $request->input('selected_primitives') ?? $request->input('selectedPrimitives') ?? [];
        if (is_string($rawPrimitives)) {
            $decodedPrims = json_decode($rawPrimitives, true);
            $explicitPrimitives = is_array($decodedPrims) ? $decodedPrims : array_filter(array_map('trim', explode(',', $rawPrimitives)));
        } else {
            $explicitPrimitives = is_array($rawPrimitives) ? $rawPrimitives : [];
        }

        // 3. Spark Free Tier Quota Enforcement (2x per month per IP/Guest, auto-reset 1st of month)
        $user = auth()->user();
        $isExempt = $user && ($user->isSuperAdmin() || $user->hasRole('super_admin'));
        $identifier = $user ? $user->email : ($request->ip() ?: '127.0.0.1');
        $monthKey = 'spark_quota_' . md5($identifier) . '_' . now()->format('Y_m');
        $currentUsage = (int) Cache::get($monthKey, 0);

        if (! $isExempt) {
            $sparkLimitRaw = \App\Models\CmsGlobalSetting::getVal('pricing_retail_spark_limit', '2x Audit/Bulan');
            $maxSparkLimit = 2;
            if (preg_match('/(\d+)/', (string) $sparkLimitRaw, $matches)) {
                $maxSparkLimit = (int) $matches[1];
            }

            if ($currentUsage >= $maxSparkLimit) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'SPARK_QUOTA_EXCEEDED',
                    'message' => 'Batas kuota gratis paket Spark (' . $maxSparkLimit . 'x analisis per bulan) telah tercapai untuk bulan ini. Kuota Anda akan di-reset otomatis pada awal bulan depan. Silakan login atau upgrade ke paket Lite / Pro untuk akses tak terbatas.',
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
                $explicitStackPreset ?: null
            );

            // Increment Spark quota usage upon successful synthesis
            if (! $isExempt) {
                Cache::put($monthKey, $currentUsage + 1, now()->endOfMonth()->addDay());
            }

            $draftId = (string) Str::ulid();
            Cache::put('blueprint_draft_' . $draftId, $synthesized, now()->addHours(24));
            session(['blueprint_draft' => $synthesized]);

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
                'data' => $synthesized
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses ide: ' . $e->getMessage()
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
                $isExempt = $user && ($user->isSuperAdmin() || $user->hasRole('super_admin'));

                // Studio MVP / Scope Lock Enforcement
                if (! $isExempt && $existingRecord->isScopeFrozen()) {
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
                            'message' => 'Jendela revisi ' . $allowedDays . ' hari untuk paket ' . ucfirst($tier) . ' telah berakhir pada ' . $expirationDate->format('d M Y') . '. Silakan upgrade paket atau hubungi Lead Architect Neriah Pro untuk perpanjangan.',
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
                'message' => $e->getMessage()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses ide tambahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auto-save blueprint draft in background (Dual-Tier Persistence: Cache & Session + Database when editing slug).
     */
    public function autoSave(Request $request): JsonResponse
    {
        $blueprint = $request->input('blueprint', []);
        $draftId = $request->input('draft_id') ?: (string) Str::ulid();
        $persistedToDb = false;

        if (!empty($blueprint) && is_array($blueprint)) {
            Cache::put('blueprint_draft_' . $draftId, $blueprint, now()->addDays(7));
            session(['blueprint_draft' => $blueprint]);

            // If editing an existing project slug, persist directly to PostgreSQL database
            $slug = $blueprint['_meta']['is_editing_slug'] ?? $request->input('slug');
            if ($slug) {
                $record = VisionBlueprint::where('slug', $slug)->first();
                if ($record) {
                    $user = auth()->user();
                    $isExempt = $user && ($user->isSuperAdmin() || $user->hasRole('super_admin'));

                    // Studio MVP / Scope Lock Enforcement
                    if (! $isExempt && $record->isScopeFrozen()) {
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
                                'message' => 'Jendela revisi ' . $allowedDays . ' hari untuk paket ' . ucfirst($tier) . ' telah berakhir pada ' . $expirationDate->format('d M Y') . '.',
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
                    if (! empty($blueprint['stackPreset'])) {
                        $meta['stack_preset'] = \App\Support\PosStackPresetCatalog::normalizePresetCode($blueprint['stackPreset']);
                    }
                    $meta['last_autosaved_at'] = now()->toIso8601String();

                    $updatePayload = [
                        'user_metadata' => $meta,
                    ];
                    if (!empty($blueprint['namaBisnis'])) $updatePayload['nama_bisnis'] = $blueprint['namaBisnis'];
                    if (!empty($blueprint['clientName'])) $updatePayload['client_name'] = $blueprint['clientName'];
                    if (!empty($blueprint['email'])) $updatePayload['email'] = $blueprint['email'];
                    if (!empty($blueprint['phone'])) $updatePayload['phone'] = $blueprint['phone'];
                    if (!empty($blueprint['masalahUtama'])) $updatePayload['masalah_utama'] = $blueprint['masalahUtama'];
                    if (!empty($blueprint['tujuanUtama'])) $updatePayload['tujuan_utama'] = $blueprint['tujuanUtama'];
                    if (!empty($blueprint['targetAudiens'])) $updatePayload['target_audiens'] = $blueprint['targetAudiens'];
                    if (!empty($blueprint['aktorSistem'])) $updatePayload['aktor_sistem'] = $blueprint['aktorSistem'];
                    if (!empty($blueprint['fiturWajib'])) $updatePayload['fitur_wajib'] = $blueprint['fiturWajib'];
                    if (isset($blueprint['fiturTambahan'])) $updatePayload['fitur_tambahan'] = $blueprint['fiturTambahan'];
                    if (!empty($blueprint['alurKerja'])) $updatePayload['alur_kerja'] = $blueprint['alurKerja'];
                    if (isset($blueprint['kebutuhanIntegrasi'])) $updatePayload['kebutuhan_integrasi'] = $blueprint['kebutuhanIntegrasi'];
                    if (isset($blueprint['referensiDesain'])) $updatePayload['referensi_desain'] = $blueprint['referensiDesain'];
                    if (isset($blueprint['kesiapanAset'])) $updatePayload['kesiapan_aset'] = $blueprint['kesiapanAset'];
                    if (isset($blueprint['targetWaktu'])) $updatePayload['target_waktu'] = $blueprint['targetWaktu'];

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
            'saved_at' => now()->timezone($tz)->format('H:i:s') . ' WIB',
        ]);
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
        if (empty($blueprint->prd_content) || !isset($blueprint->prd_content['engineering_specs']) || (request()->has('regenerate') && !$isScopeLocked)) {
            $blueprint->generateAndSavePrd();
            $blueprint->refresh();
        }

        return view('blueprint.show', [
            'blueprint' => $blueprint,
            'prd' => $blueprint->prd_content,
            'globalSettings' => $globalSettings,
            'isScopeLocked' => $isScopeLocked,
            'isDpConfirmed' => $isDpConfirmed,
            'isContractSigned' => $isContractSigned,
            'contractDocument' => $contractDocument,
        ]);
    }

    /**
     * Generate contract and redirect to sign page
     */
    public function generateContract(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        
        $existingContract = $blueprint->getContractDocument();
        if ($existingContract && ($blueprint->isContractSigned() || $blueprint->isDpConfirmed())) {
            return redirect()->route('document.sign', ['document' => $existingContract->id]);
        }

        // Ensure PRD with itemized estimation is populated
        if (empty($blueprint->prd_content) || !isset($blueprint->prd_content['itemized_cost_breakdown'])) {
            $blueprint->generateAndSavePrd();
            $blueprint->refresh();
        }

        $tier = request('tier', 'standard');
        $matchedTier = \App\Services\PrdGeneratorService::resolveVelocityTier($blueprint, $tier);
        $contractAmount = (float) $matchedTier['contract_amount'];

        // Check if voucher applied
        $voucherCode = strtoupper(trim((string) request('voucher', request('voucher_code', ''))));
        $appliedVoucher = null;
        if (!empty($voucherCode)) {
            $voucher = BlueprintVoucher::where('code', $voucherCode)->first();
            if ($voucher && $voucher->isValid()) {
                $appliedVoucher = $voucher;
                if ($voucher->discount_type === 'free_bypass' || ($voucher->discount_type === 'percent' && (float) $voucher->discount_value >= 100)) {
                    $contractAmount = 0.00;
                } elseif ($voucher->discount_type === 'percent') {
                    $contractAmount = max(0, $contractAmount - round($contractAmount * ((float) $voucher->discount_value / 100)));
                } elseif ($voucher->discount_type === 'fixed') {
                    $contractAmount = max(0, $contractAmount - (float) $voucher->discount_value);
                }
            }
        }

        $dpAmount = (float) round($contractAmount * 0.50);

        $overrides = [
            'contract_amount' => $contractAmount,
            'dp_amount' => $dpAmount,
        ];
        if ($appliedVoucher) {
            $overrides['voucher_code'] = $appliedVoucher->code;
        }

        $document = $blueprint->documents()->where('document_type', 'contract')->first();
        if (!$document) {
            $document = $blueprint->convertToDigitalContract($overrides);
        } else {
            $document->update([
                'contract_amount' => $overrides['contract_amount'],
                'dp_amount' => $overrides['dp_amount'],
            ]);
        }

        return redirect()->route('document.sign', ['document' => $document->id]);
    }

    /**
     * Get Midtrans Snap Token for this blueprint proposal (Termin DP 50%).
     */
    public function getSnapToken(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();

        if ($blueprint->isDpConfirmed()) {
            return response()->json([
                'success' => false,
                'message' => 'Uang muka (DP) untuk proyek ini sudah terkonfirmasi / lunas. Tidak memerlukan pembayaran ulang.',
            ], 400);
        }
        
        if (empty($blueprint->prd_content) || !isset($blueprint->prd_content['itemized_cost_breakdown'])) {
            $blueprint->generateAndSavePrd();
            $blueprint->refresh();
        }

        $existingContract = $blueprint->getContractDocument();
        $isSignedContract = $existingContract && $existingContract->status === 'signed' && (float)$existingContract->contract_amount > 0;

        if ($isSignedContract) {
            $contractAmount = (float) $existingContract->contract_amount;
            $tierLabel = $existingContract->title ?: 'Kontrak Tertandatangani';
            $matchedTier = [
                'id' => 'signed_contract',
                'name' => $tierLabel,
                'contract_amount' => $contractAmount,
                'dp_amount' => (float)($existingContract->dp_amount ?: ($contractAmount * 0.50)),
            ];
        } else {
            $tier = $request->input('tier', 'standard');
            $matchedTier = \App\Services\PrdGeneratorService::resolveVelocityTier($blueprint, $tier);
            $contractAmount = (float) $matchedTier['contract_amount'];
            $tierLabel = $matchedTier['name'] ?? 'Standard Velocity';
        }

        // Check for voucher discount
        $voucherCode = strtoupper(trim((string) $request->input('voucher_code', '')));
        $voucher = null;
        $discountAmount = 0;
        if (!empty($voucherCode)) {
            $voucher = BlueprintVoucher::where('code', $voucherCode)->first();
            if ($voucher && $voucher->isValid()) {
                if ($voucher->discount_type === 'percent') {
                    $discountAmount = round($contractAmount * ((float) $voucher->discount_value / 100));
                } elseif ($voucher->discount_type === 'fixed') {
                    $discountAmount = min($contractAmount, (float) $voucher->discount_value);
                }
            }
        }

        $finalContractAmount = max(0, $contractAmount - $discountAmount);
        $finalDpAmount = (int) round($finalContractAmount * 0.50);

        if ($finalDpAmount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher ini memberikan akses 100% Gratis (Rp 0). Silakan gunakan tombol Klaim Voucher Pelayanan untuk mengaktifkan proyek tanpa pembayaran.',
            ], 400);
        }

        // Guaranteed Order ID generation with full 26-character ULID (45 chars <= 50 Midtrans limit)
        $orderId = 'NPRO-DP-' . $blueprint->id . '-' . time();

        $itemName = 'DP (50%) - ' . ($blueprint->nama_bisnis ?: 'Proyek');
        if ($voucher) {
            $itemName .= ' (Disc ' . $voucher->code . ')';
        }

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $finalDpAmount,
            ],
            'customer_details' => [
                'first_name' => $blueprint->client_name ?: ($blueprint->nama_bisnis ?: 'Client'),
                'email' => $blueprint->email ?: 'client@neriahpro.com',
                'phone' => $blueprint->phone ?: '08123456789',
            ],
            'item_details' => [
                [
                    'id' => 'DP-' . strtoupper($matchedTier['id'] ?? $tier),
                    'price' => $finalDpAmount,
                    'quantity' => 1,
                    'name' => substr($itemName, 0, 50),
                ]
            ],
        ];

        $snapResponse = MidtransSnapService::createSnapToken($params);

        if (!$snapResponse['success']) {
            return response()->json([
                'success' => false,
                'message' => $snapResponse['error'] ?? 'Gagal membuat sesi transaksi Midtrans.',
            ], $snapResponse['status_code'] ?? 500);
        }

        // Record pending signer metadata without prematurely locking agreement
        if ($request->boolean('agree_sign_off')) {
            $blueprint->update([
                'signer_ip' => $request->ip(),
                'signer_user_agent' => substr((string) $request->userAgent(), 0, 500),
            ]);
        }

        if ($voucher) {
            $blueprint->update(['voucher_code' => $voucher->code]);
        }

        // Cancel any previous pending transactions for this blueprint's order IDs
        $existingDoc = $blueprint->getContractDocument();
        if ($existingDoc && $existingDoc->midtrans_order_id) {
            \App\Models\Transaction::where('midtrans_order_id', $existingDoc->midtrans_order_id)
                ->where('status', 'pending')
                ->update(['status' => 'cancel']);
        }

        // Attach/update Digital Contract Document with order_id for webhook fulfillment
        $isSigned = $existingDoc && $existingDoc->status === 'signed' && (float)$existingDoc->contract_amount > 0;

        Document::updateOrCreate(
            [
                'related_type' => VisionBlueprint::class,
                'related_id' => $blueprint->id,
                'document_type' => 'contract',
            ],
            [
                'title' => $existingDoc?->title ?: ('Perjanjian Kerja Sama - ' . ($blueprint->nama_bisnis ?: $blueprint->client_name)),
                'status' => $isSigned ? 'signed' : 'pending_signature',
                'scope_locked' => $isSigned ? true : false,
                'contract_amount' => $contractAmount,
                'dp_amount' => $finalDpAmount,
                'midtrans_order_id' => $orderId,
                'signer_name' => $isSigned ? $existingDoc->signer_name : ($blueprint->client_name ?: $blueprint->nama_bisnis),
                'signer_email' => $isSigned ? $existingDoc->signer_email : $blueprint->email,
                'document_hash' => $isSigned ? $existingDoc->document_hash : ($blueprint->document_sha256 ?: $blueprint->calculatePrdHash()),
            ]
        );

        // Instantly record pending transaction for Blueprint DP in database
        \App\Models\Transaction::create([
            'user_id' => $blueprint->user_id,
            'midtrans_order_id' => $orderId,
            'status' => 'pending',
            'total_idr' => $finalDpAmount,
            'original_currency' => 'IDR',
            'original_amount' => $finalDpAmount,
            'customer_details' => [
                'name' => $blueprint->client_name ?: $blueprint->nama_bisnis,
                'email' => $blueprint->email,
                'phone' => $blueprint->phone,
                'type' => 'blueprint_dp',
                'blueprint_slug' => $blueprint->slug,
                'tier' => $matchedTier['name'] ?? $tier,
                'snap_token' => $snapResponse['token'],
            ],
        ]);

        return response()->json([
            'success' => true,
            'token' => $snapResponse['token'],
            'redirect_url' => $snapResponse['redirect_url'],
            'order_id' => $orderId,
            'gross_amount' => $finalDpAmount,
            'dp_amount' => $finalDpAmount,
            'contract_amount' => $contractAmount,
            'client_key' => $snapResponse['client_key'] ?? config('midtrans.client_key'),
            'document_sha256' => $blueprint->document_sha256 ?: $blueprint->calculatePrdHash(),
        ]);
    }

    /**
     * Get Midtrans Snap Token for Final Project Settlement / Pelunasan 50% (Milestone 2).
     */
    public function getPelunasanSnapToken(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();

        if (!$blueprint->isDpConfirmed()) {
            return response()->json([
                'success' => false,
                'message' => 'Tahap Uang Muka (DP 50%) belum diselesaikan. Silakan selesaikan DP terlebih dahulu.',
            ], 400);
        }

        if ($blueprint->isPelunasanConfirmed()) {
            return response()->json([
                'success' => false,
                'message' => 'Pelunasan akhir untuk proyek ini sudah lunas terverifikasi. Seluruh akses sistem dan lisensi telah aktif.',
            ], 400);
        }

        // Resolve contract amount & pelunasan amount
        $existingContract = $blueprint->getContractDocument();
        if ($existingContract && (float)$existingContract->contract_amount > 0) {
            $contractAmount = (float) $existingContract->contract_amount;
            $dpAmount = (float) ($existingContract->dp_amount ?: ($contractAmount * 0.50));
            $pelunasanAmount = max(0, $contractAmount - $dpAmount);
        } else {
            $tier = $blueprint->user_metadata['package_tier'] ?? 'standard';
            $resolvedTier = \App\Services\PrdGeneratorService::resolveVelocityTier($blueprint, $tier);
            $contractAmount = (float) ($resolvedTier['contract_amount'] ?? 50000000);
            $dpAmount = (float) ($resolvedTier['dp_amount'] ?? ($contractAmount * 0.50));
            $pelunasanAmount = max(0, $contractAmount - $dpAmount);
        }

        if ($pelunasanAmount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Sisa tagihan pelunasan bernilai Rp 0.',
            ], 400);
        }

        // 48 characters (11 + 26 + 1 + 10): strictly under Midtrans 50-character limit
        $orderId = 'NPRO-FINAL-' . $blueprint->id . '-' . time();
        $itemName = 'Pelunasan (50%) - ' . ($blueprint->nama_bisnis ?: 'Proyek');

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $pelunasanAmount,
            ],
            'customer_details' => [
                'first_name' => $blueprint->client_name ?: ($blueprint->nama_bisnis ?: 'Client'),
                'email' => $blueprint->email ?: 'client@neriahpro.com',
                'phone' => $blueprint->phone ?: '08123456789',
            ],
            'item_details' => [
                [
                    'id' => 'PELUNASAN-50',
                    'price' => (int) $pelunasanAmount,
                    'quantity' => 1,
                    'name' => substr($itemName, 0, 50),
                ]
            ],
        ];

        // Cancel any previous pending pelunasan transaction for this blueprint
        if ($existingContract && !empty($existingContract->content_clauses['pelunasan_order_id'])) {
            \App\Models\Transaction::where('midtrans_order_id', $existingContract->content_clauses['pelunasan_order_id'])
                ->where('status', 'pending')
                ->update(['status' => 'cancel']);
        }

        $snapResponse = MidtransSnapService::createSnapToken($params);

        if (!$snapResponse['success']) {
            return response()->json([
                'success' => false,
                'message' => $snapResponse['error'] ?? 'Gagal membuat sesi transaksi Midtrans untuk pelunasan.',
            ], $snapResponse['status_code'] ?? 500);
        }

        if ($existingContract) {
            $clauses = $existingContract->content_clauses ?: [];
            $clauses['pelunasan_order_id'] = $orderId;
            $clauses['pelunasan_requested_at'] = now()->toIso8601String();
            $existingContract->update([
                'content_clauses' => $clauses,
            ]);
        }

        // Instantly record pending transaction for Pelunasan in database
        \App\Models\Transaction::create([
            'user_id' => $blueprint->user_id,
            'midtrans_order_id' => $orderId,
            'status' => 'pending',
            'total_idr' => (int) $pelunasanAmount,
            'original_currency' => 'IDR',
            'original_amount' => (int) $pelunasanAmount,
            'customer_details' => [
                'name' => $blueprint->client_name ?: $blueprint->nama_bisnis,
                'email' => $blueprint->email,
                'phone' => $blueprint->phone,
                'type' => 'pelunasan_50_percent',
                'blueprint_slug' => $blueprint->slug,
                'snap_token' => $snapResponse['token'],
            ],
        ]);

        return response()->json([
            'success' => true,
            'token' => $snapResponse['token'],
            'redirect_url' => $snapResponse['redirect_url'],
            'order_id' => $orderId,
            'gross_amount' => (int) $pelunasanAmount,
            'pelunasan_amount' => (int) $pelunasanAmount,
            'formatted_pelunasan_amount' => 'Rp ' . number_format($pelunasanAmount, 0, ',', '.'),
            'contract_amount' => (int) $contractAmount,
            'client_key' => $snapResponse['client_key'] ?? config('midtrans.client_key'),
            'snap_url' => $snapResponse['snap_url'] ?? config('midtrans.snap_url'),
        ]);
    }

    /**
     * Validate a promo voucher code for a blueprint proposal.
     */
    public function validateVoucher(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();

        $code = strtoupper(trim((string) $request->input('code')));
        if (empty($code)) {
            return response()->json([
                'valid' => false,
                'message' => 'Silakan masukkan kode voucher.',
            ], 422);
        }

        $voucher = BlueprintVoucher::where('code', $code)->first();

        if (!$voucher || !$voucher->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode voucher tidak valid, kuota telah habis, atau sudah kedaluwarsa.',
            ], 422);
        }

        $isFreeBypass = $voucher->discount_type === 'free_bypass' 
            || ($voucher->discount_type === 'percent' && (float) $voucher->discount_value >= 100);

        return response()->json([
            'valid' => true,
            'code' => $voucher->code,
            'discount_type' => $voucher->discount_type,
            'discount_value' => (float) $voucher->discount_value,
            'description' => $voucher->description,
            'is_free_bypass' => $isFreeBypass,
            'message' => 'Voucher valid! ' . ($voucher->description ?: 'Potongan biaya berhasil diterapkan.'),
        ]);
    }

    /**
     * Claim voucher to bypass payment (Rp 0 Free Grant) or apply grant.
     */
    public function claimVoucher(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();

        $request->validate([
            'code' => 'required|string|max:50',
            'agree_sign_off' => 'required|accepted',
        ], [
            'agree_sign_off.accepted' => 'Anda harus menyetujui spesifikasi scope dan integritas dokumen PRD sebelum mengklaim voucher.',
        ]);

        $code = strtoupper(trim((string) $request->input('code')));
        $voucher = BlueprintVoucher::where('code', $code)->first();

        if (!$voucher || !$voucher->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher tidak valid, kuota telah habis, atau sudah kedaluwarsa.',
            ], 422);
        }

        $isFreeBypass = $voucher->discount_type === 'free_bypass' 
            || ($voucher->discount_type === 'percent' && (float) $voucher->discount_value >= 100);

        if (!$isFreeBypass) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher ini adalah voucher potongan harga/subsidi (bukan gratis 100%). Silakan gunakan tombol "Bayar Sekarang via Midtrans Snap" untuk melakukan pembayaran DP dengan harga diskon.',
            ], 422);
        }

        // Increment voucher usage
        $voucher->incrementUsage();

        // Record digital sign-off and SHA-256 audit hash
        $blueprint->recordSignOff($request->ip(), $request->userAgent());

        // Provision staging demo sandbox URL
        $stagingUrl = $blueprint->provisionStagingUrl();

        // Update blueprint status to free grant
        $blueprint->update([
            'voucher_code' => $voucher->code,
            'is_free_grant' => true,
            'is_published' => true,
            'project_status' => 'In Development (Free Grant)',
        ]);

        // Interconnect with Document/Contract if exists
        $document = $blueprint->documents()->where('document_type', 'contract')->first();
        if (!$document) {
            $blueprint->convertToDigitalContract([
                'contract_amount' => 0.00,
                'dp_amount' => 0.00,
                'status' => 'signed',
                'signer_ip_address' => $request->ip(),
                'signed_at' => now(),
                'document_hash' => $blueprint->document_sha256,
                'project_status' => 'In Development (Free Grant)',
            ]);
        } else {
            $document->update([
                'contract_amount' => 0.00,
                'dp_amount' => 0.00,
                'status' => 'signed',
                'signer_ip_address' => $request->ip(),
                'signed_at' => now(),
                'document_hash' => $blueprint->document_sha256,
            ]);
        }

        // Send Email Notification to Customer
        if (!empty($blueprint->email) && filter_var($blueprint->email, FILTER_VALIDATE_EMAIL) && !str_contains($blueprint->email, '@example.com')) {
            try {
                \Illuminate\Support\Facades\Mail::to($blueprint->email)->send(
                    new \App\Mail\BlueprintReadyNotificationMail($blueprint, 'Free Grant // Voucher')
                );
            } catch (\Throwable $mailEx) {
                \Illuminate\Support\Facades\Log::warning('Gagal kirim notifikasi claim voucher: ' . $mailEx->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diklaim! Proyek telah dialokasikan dengan status Pelayanan Gratis (Rp 0). Lingkungan staging Anda telah siap.',
            'staging_url' => $stagingUrl,
            'document_sha256' => $blueprint->document_sha256,
            'redirect_url' => route('blueprint.show', $blueprint->slug),
        ]);
    }

    /**
     * Download PRD as PDF
     */
    public function downloadPdf(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);
        
        $prd = $blueprint->prd_content;
        if (empty($prd) || !is_array($prd) || empty($prd['features'])) {
            try {
                $prd = \App\Services\PrdGeneratorService::generate($blueprint);
                $blueprint->prd_content = $prd;
                $blueprint->saveQuietly();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal auto-generate prd_content untuk PDF: ' . $e->getMessage());
                $prd = $prd ?: [];
            }
        }
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.blueprint', [
            'blueprint' => $blueprint,
            'prd' => $prd,
        ])->setPaper('a4', 'portrait')
          ->setOptions([
              'isHtml5ParserEnabled' => true,
              'isRemoteEnabled' => true,
              'defaultFont' => 'Helvetica',
          ]);
        
        return $pdf->download('PRD_' . $blueprint->slug . '.pdf');
    }

    /**
     * Download PRD as Markdown
     */
    public function downloadMd(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);
        $prd = $blueprint->prd_content ?? [];
        
        $md = \App\Services\PrdGeneratorService::toMarkdown($blueprint, $prd);
        
        return response($md, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="PRD_' . $blueprint->slug . '.md"'
        ]);
    }

    /**
     * Get Raw PRD Markdown for AI Code Agent clipboard copy
     */
    public function rawMd(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);
        $prd = $blueprint->prd_content ?? [];
        
        $md = \App\Services\PrdGeneratorService::toMarkdown($blueprint, $prd);
        
        return response($md, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    /**
     * Return O(1) cached 12-Sector + 12-Primitive + Rule Pack Composable Matrix catalog.
     */
    public function matrixCatalog(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'schema_uri' => \App\Services\ProjectOsBlueprintCompilerService::SCHEMA_URI,
            'catalog' => \App\Services\ProjectOsBlueprintCompilerService::getCachedCatalog(),
            'satu_pintu_packages' => \App\Support\PricingRegistry::getSatuPintuCatalog(),
            'commercial_estimator_config' => \App\Support\PricingRegistry::getCommercialEstimatorConfig(),
            'commercial_bridge_features' => \App\Services\PosCommercialBridgeService::fiveCommercialBridgeFeatures(),
            'guided_client_journey' => \App\Services\PosCommercialBridgeService::guidedClientJourneySteps(),
        ]);
    }

    /**
     * Download compiled Universal Blueprint Schema v1.0 JSON (.agents/BLUEPRINT_SCHEMA.json).
     */
    public function downloadSchemaJson(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);
        $prd = $blueprint->prd_content ?? [];
        $schema = $prd['universal_blueprint_schema']
            ?? \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint);

        return response(
            json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            200,
            [
                'Content-Type' => 'application/json; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="BLUEPRINT_SCHEMA.json"',
            ]
        );
    }

    /**
     * Download AI IDE Rules (.cursorrules / CLAUDE.md / AGENTS.md / PROJECT_OS_EXECUTION_BLUEPRINT.md)
     */
    public function downloadCursorrules(Request $request, string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);
        $prd = $blueprint->prd_content ?? [];
        if (empty($prd)) {
            $prd = \App\Services\PrdGeneratorService::generate($blueprint);
        }

        $format = strtolower((string)$request->query('format', 'cursorrules'));
        $univSchema = $prd['universal_blueprint_schema']
            ?? \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint);

        $phasedPrompts = $univSchema['phased_prompt_sequencer']
            ?? \App\Services\ProjectOsBlueprintCompilerService::compilePhasedPrompts($univSchema);

        if (preg_match('/^phase-([1-7])$/', $format, $m)) {
            $phaseIdx = ((int) $m[1]) - 1;
            $phaseItem = $phasedPrompts[$phaseIdx] ?? null;
            if ($phaseItem) {
                return response((string) ($phaseItem['prompt'] ?? ''), 200, [
                    'Content-Type' => 'text/markdown; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="' . ($phaseItem['filename'] ?? ("0{$m[1]}-phase.md")) . '"',
                ]);
            }
        }

        [$content, $filename] = match ($format) {
            'execution-blueprint' => [
                \App\Services\ProjectOsBlueprintCompilerService::renderExecutionBlueprintMarkdown($univSchema),
                'PROJECT_OS_EXECUTION_BLUEPRINT.md',
            ],
            'stack-topology' => [
                \App\Services\ProjectOsBlueprintCompilerService::renderStackTopologyContractMarkdown($univSchema),
                'STACK_TOPOLOGY_CONTRACT.md',
            ],
            'ui-ux-contract' => [
                \App\Services\ProjectOsBlueprintCompilerService::renderUiUxContractMarkdown(),
                'UI_UX_CONTRACT.md',
            ],
            'engineering-rules' => [
                \App\Services\ProjectOsBlueprintCompilerService::renderEngineeringRulesMarkdown(),
                'ENGINEERING_RULES.md',
            ],
            'ecc-harness' => [
                \App\Services\ProjectOsBlueprintCompilerService::renderAgentHarnessEccMarkdown(),
                'AGENT_HARNESS_ECC.md',
            ],
            'verify-blueprint' => [
                \App\Services\BlueprintSlopScannerService::generateVerifyBlueprintBashScript($blueprint->nama_bisnis ?: 'Enterprise App'),
                'verify-blueprint.sh',
            ],
            'print-spec' => [
                \App\Services\ProjectOsBlueprintCompilerService::renderPrintSpecMarkdown($univSchema),
                'PRINT_THERMAL_AND_A4_CONTRACT.md',
            ],
            'day2-ops' => [
                \App\Services\ProjectOsBlueprintCompilerService::renderDayTwoOpsMarkdown($univSchema),
                'DAY2_OPERATIONS_AND_SECURITY.md',
            ],
            'decision-tables' => [
                \App\Services\ProjectOsBlueprintCompilerService::renderDecisionTablesMarkdown($univSchema),
                'DECISION_TABLES_CONTRACT.md',
            ],
            'env-blueprint' => [
                \App\Services\ScaffoldGeneratorService::generateEnvBlueprintFile($univSchema, $blueprint->nama_bisnis ?: 'Enterprise App'),
                '.env.blueprint',
            ],
            'mcp-json' => [
                \App\Services\ScaffoldGeneratorService::generateMcpJsonFile($blueprint),
                '.mcp.json',
            ],
            'claude' => [
                \App\Services\PrdGeneratorService::toCursorrules($blueprint, $prd),
                'CLAUDE.md',
            ],
            'agents' => [
                \App\Services\ProjectOsBlueprintCompilerService::renderUnifiedAgentsMarkdown($univSchema),
                'AGENTS.md',
            ],
            default => [
                \App\Services\PrdGeneratorService::toCursorrules($blueprint, $prd),
                '.cursorrules',
            ],
        };

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Update and elaborate technical tasks and features for a blueprint proposal.
     * Guarded against scope lock (only allowed before contract is signed).
     */
    public function updateTasks(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();

        // 1. Guard against Scope Freeze (locked document)
        $isScopeLocked = (bool) $blueprint->signed_agreement || $blueprint->documents()->where('scope_locked', true)->exists();
        if ($isScopeLocked) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen PRD telah ditandatangani dan terkunci secara digital (Scope Freeze). Setiap perubahan atau penambahan task baru harus diajukan melalui Addendum / Change Request (CR) resmi.',
            ], 403);
        }

        $request->validate([
            'mvp_tasks' => 'required|array|min:1',
            'mvp_tasks.*.title' => 'required|string|max:255',
            'mvp_tasks.*.desc' => 'nullable|string|max:1000',
            'mvp_tasks.*.category' => 'nullable|string|max:100',
            'mvp_tasks.*.sprint_phase' => 'nullable|string|max:50',
            'phase2_tasks' => 'nullable|array',
            'phase2_tasks.*.title' => 'required|string|max:255',
            'phase2_tasks.*.desc' => 'nullable|string|max:1000',
            'phase2_tasks.*.category' => 'nullable|string|max:100',
            'phase2_tasks.*.sprint_phase' => 'nullable|string|max:50',
        ]);

        $mvpTasks = $request->input('mvp_tasks', []);
        $phase2Tasks = $request->input('phase2_tasks', []);

        // Format into normalized text lines for database columns
        $fiturWajibLines = [];
        foreach ($mvpTasks as $idx => $t) {
            $title = trim($t['title'] ?? '');
            $desc = trim($t['desc'] ?? '');
            $fiturWajibLines[] = $title . (!empty($desc) ? ' - ' . $desc : '');
        }

        $fiturTambahanLines = [];
        foreach ($phase2Tasks as $idx => $t) {
            $title = trim($t['title'] ?? '');
            $desc = trim($t['desc'] ?? '');
            $fiturTambahanLines[] = $title . (!empty($desc) ? ' - ' . $desc : '');
        }

        $blueprint->fitur_wajib = implode("\n", $fiturWajibLines);
        $blueprint->fitur_tambahan = implode("\n", $fiturTambahanLines);
        $blueprint->save();

        // Regenerate complete Ultimate PRD with deep vertical slice engineering specs, BDD, diagrams, and costs
        $prd = $blueprint->generateAndSavePrd();
        $blueprint->refresh();
        $blueprint->document_sha256 = $blueprint->calculatePrdHash();
        $blueprint->save();

        $itemized = $prd['itemized_cost_breakdown'] ?? \App\Services\PrdGeneratorService::calculateItemizedEstimation($blueprint);

        return response()->json([
            'success' => true,
            'message' => 'Daftar task & spesifikasi fitur berhasil dielaborasi dan disimpan!',
            'document_sha256' => $blueprint->document_sha256,
            'mvp_count' => count($mvpTasks),
            'phase2_count' => count($phase2Tasks),
            'itemized_summary' => [
                'base_total' => $itemized['base_contract_amount'] ?? 0,
                'items_count' => count($itemized['items'] ?? []),
            ]
        ]);
    }

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
        $isRetail = $blueprint->project_status === 'Retail License';
        $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin();
        $isDraft = !$blueprint->signed_agreement && !$blueprint->isScopeFrozen();

        if (!($isRetail || $isSuperAdmin || $isDraft)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Dokumen berada dalam status Scope Lock (Kontrak Studio) dan hanya dapat diubah melalui Change Request resmi. Hubungi admin Neriah Pro.'
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
                    if (!in_array($iKey, $impacted)) $impacted[] = $iKey;
                }
                $prd['_impacted_segments'] = $impacted;
            }
            if (isset($prd['_impacted_segments'])) {
                $prd['_impacted_segments'] = array_values(array_filter($prd['_impacted_segments'], fn($item) => $item !== $baseKey && $item !== $key));
            }
            
            $blueprint->update(['prd_content' => $prd]);

            return response()->json([
                'success' => true,
                'message' => 'Segmen spesifikasi berhasil diperbarui secara manual.',
            ]);
        }

        if ($mode === 'ai') {
            try {
                $additionalContext = [
                    'feature_id' => $request->input('feature_id'),
                    'spec_index' => $request->filled('spec_index') ? (int) $request->input('spec_index') : null,
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
                \Illuminate\Support\Facades\Log::error('Refinement failed: ' . $e->getMessage(), [
                    'exception' => $e
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperdalam segmen: ' . $e->getMessage(),
                ], 422);
            }
        }

        return response()->json(['success' => false, 'message' => 'Mode tidak valid.'], 400);
    }

    /**
     * Update and persist developer/architect execution checkpoints for a blueprint.
     * Synchronizes live progress with the customer dashboard.
     */
    public function updateCheckpoint(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'step_key' => 'required|string|max:100',
            'is_completed' => 'required|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $stepKey = $validated['step_key'];
        $isCompleted = (bool) $validated['is_completed'];
        $notes = $validated['notes'] ?? null;

        $metadata = $blueprint->user_metadata ?? [];
        $checkpoints = $metadata['dev_checkpoints'] ?? [];

        $checkpoints[$stepKey] = [
            'completed' => $isCompleted,
            'completed_at' => $isCompleted ? now()->toIso8601String() : null,
            'notes' => $notes,
            'updated_by' => auth()->user()?->name ?? 'Lead Architect / AI Agent',
        ];

        $metadata['dev_checkpoints'] = $checkpoints;

        // Calculate progress percentage based on total steps
        $totalSteps = 6;
        $completedCount = count(array_filter($checkpoints, fn ($c) => !empty($c['completed'])));
        $progressPercent = min(100, (int) round(($completedCount / max(1, $totalSteps)) * 100));

        $metadata['dev_progress_percent'] = $progressPercent;
        $metadata['dev_last_checkpoint_at'] = now()->toIso8601String();

        $blueprint->user_metadata = $metadata;
        $blueprint->save();

        return response()->json([
            'success' => true,
            'message' => "Checkpoint {$stepKey} berhasil disinkronkan ke database dan timeline pelanggan.",
            'step_key' => $stepKey,
            'is_completed' => $isCompleted,
            'completed_count' => $completedCount,
            'progress_percent' => $progressPercent,
            'updated_at' => now()->format('d M Y, H:i') . ' WIB',
        ]);
    }

    /**
     * Download Project Scaffold & Boilerplate ZIP Archive.
     */
    public function exportScaffold(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $zipPath = \App\Services\ScaffoldGeneratorService::createZipArchive($blueprint);

        $downloadFilename = ($blueprint->slug ?: 'project') . '-starter-kit.zip';

        return response()->download($zipPath, $downloadFilename)->deleteFileAfterSend(true);
    }

    /**
     * Preview Project Scaffold Files in JSON format for in-browser viewer.
     */
    public function previewScaffold(string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $files = \App\Services\ScaffoldGeneratorService::generateFiles($blueprint);
        $customizations = $blueprint->user_metadata['scaffold_customizations'] ?? [];

        return response()->json([
            'success' => true,
            'slug' => $blueprint->slug,
            'project_name' => $blueprint->nama_bisnis ?: 'Proyek',
            'files' => $files,
            'customized_files' => array_keys($customizations),
        ]);
    }

    /**
     * Auto-save or update an elaborated scaffold file for this blueprint.
     */
    public function saveScaffoldFile(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $request->validate([
            'filename' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $filename = $request->input('filename');
        $content = $request->input('content');

        // Prevent path traversal
        if (str_contains($filename, '..') || str_starts_with($filename, '/')) {
            return response()->json([
                'success' => false,
                'message' => 'Nama file tidak valid.',
            ], 422);
        }

        $userMeta = $blueprint->user_metadata ?? [];
        $scaffoldCustom = $userMeta['scaffold_customizations'] ?? [];
        $scaffoldCustom[$filename] = $content;
        $userMeta['scaffold_customizations'] = $scaffoldCustom;

        $blueprint->update([
            'user_metadata' => $userMeta,
        ]);

        return response()->json([
            'success' => true,
            'filename' => $filename,
            'saved_at' => now()->format('H:i:s') . ' WIB',
            'message' => "Berkas {$filename} berhasil dielaborasi dan disimpan otomatis.",
        ]);
    }

    /**
     * Reset an elaborated scaffold file back to default AI synthesis.
     */
    public function resetScaffoldFile(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $request->validate([
            'filename' => 'required|string|max:255',
        ]);

        $filename = $request->input('filename');

        $userMeta = $blueprint->user_metadata ?? [];
        if (isset($userMeta['scaffold_customizations'][$filename])) {
            unset($userMeta['scaffold_customizations'][$filename]);
            $blueprint->update([
                'user_metadata' => $userMeta,
            ]);
        }

        // Generate clean default
        $allFiles = \App\Services\ScaffoldGeneratorService::generateFiles($blueprint);
        $defaultContent = $allFiles[$filename] ?? '';

        return response()->json([
            'success' => true,
            'filename' => $filename,
            'content' => $defaultContent,
            'saved_at' => now()->format('H:i:s') . ' WIB',
            'message' => "Berkas {$filename} berhasil dikembalikan ke sintesis AI default.",
        ]);
    }

    /**
     * Refine an active scaffold file using Grounded Multi-Model AI based on Source Corpus.
     */
    public function refineScaffoldFile(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        // Security check for scope locking unless Retail License or SuperAdmin
        if ($blueprint->scope_locked && !($blueprint->project_status === 'Retail License' || (auth()->user()?->isSuperAdmin() ?? false))) {
            return response()->json([
                'success' => false,
                'message' => 'Spesifikasi telah dikunci (Scope Frozen). Pengubahan memerlukan Change Request resmi.',
            ], 403);
        }

        $validated = $request->validate([
            'filename' => 'required|string|max:255',
            'prompt' => 'nullable|string|max:3000',
            'ai_provider' => 'nullable|string|in:auto,relayrouter,gemini,deepseek,anthropic,openai',
        ]);

        try {
            $refinementService = app(\App\Services\BlueprintRefinementService::class);
            $result = $refinementService->refineScaffoldFile(
                $blueprint,
                $validated['filename'],
                $validated['prompt'] ?? null,
                $validated['ai_provider'] ?? 'auto'
            );

            return response()->json($result);
        } catch (\Throwable $e) {
            Log::error("Failed to refine scaffold file {$validated['filename']} for blueprint {$slug}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Terjadi kesalahan saat memproses pendalaman file dengan AI.',
            ], 422);
        }
    }

    /**
     * Real-time Live Presence & Collaborative Cursor Heartbeat.
     * Records collaborator coordinates, active section, and role into cache.
     */
    public function updatePresence(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();

        $clientId = $request->input('client_id') ?: session()->getId();
        $isArchitect = auth()->user()?->isSuperAdmin() || (auth()->user() && str_contains(auth()->user()->email, 'neriahpro'));
        $defaultName = auth()->check() ? auth()->user()->name : 'Tamu / Reviewer';
        $name = $request->input('name') ?: $defaultName;
        $role = $request->input('role') ?: ($isArchitect ? 'Lead Architect (Neriah Pro)' : (auth()->check() ? 'Klien / Stakeholder' : 'Tamu'));
        $x = (float) $request->input('x', 0);
        $y = (float) $request->input('y', 0);
        $section = $request->input('section', 'Section 01');

        $cacheKey = "blueprint_presence_{$blueprint->slug}";
        $presenceList = Cache::get($cacheKey, []);

        // Update current collaborator's state
        $presenceList[$clientId] = [
            'id' => $clientId,
            'name' => $name,
            'role' => $role,
            'x' => $x,
            'y' => $y,
            'section' => $section,
            'is_architect' => auth()->user()?->isSuperAdmin() || str_contains($role, 'Architect'),
            'last_seen' => now()->timestamp,
        ];

        // Purge expired participants (inactive > 12 seconds)
        $now = now()->timestamp;
        foreach ($presenceList as $id => $p) {
            if (($now - ($p['last_seen'] ?? 0)) > 12) {
                unset($presenceList[$id]);
            }
        }

        Cache::put($cacheKey, $presenceList, now()->addMinutes(5));

        return response()->json([
            'success' => true,
            'slug' => $blueprint->slug,
            'collaborators' => array_values($presenceList),
            'timestamp' => now()->timestamp,
        ]);
    }

    /**
     * Get active collaborators on this blueprint.
     */
    public function getPresence(string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $cacheKey = "blueprint_presence_{$blueprint->slug}";
        $presenceList = Cache::get($cacheKey, []);

        $now = now()->timestamp;
        foreach ($presenceList as $id => $p) {
            if (($now - ($p['last_seen'] ?? 0)) > 12) {
                unset($presenceList[$id]);
            }
        }

        return response()->json([
            'success' => true,
            'slug' => $blueprint->slug,
            'collaborators' => array_values($presenceList),
            'timestamp' => now()->timestamp,
        ]);
    }

    /**
     * Handle public consultation and booking inquiry from the Pricing Page.
     */
    public function pricingInquiry(Request $request): JsonResponse
    {
        if ($request->filled('honeypot')) {
            return response()->json([
                'success' => false,
                'message' => 'Anti-bot security validation triggered.',
            ], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'company' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'package_tier' => 'required_without:package|nullable|string|max:255',
            'package' => 'required_without:package_tier|nullable|string|max:255',
            'country_code' => 'nullable|string|max:10',
            'budget_range' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:2000',
            'voucher_code' => 'nullable|string|max:50',
            'sprint_batch' => 'nullable|string|max:100',
            'kickoff_slot' => 'nullable|string|max:100',
            'has_blueprint' => 'nullable|string|max:50',
            'blueprint_slug' => 'nullable|string|max:100',
            'umkm_category' => 'nullable|string|max:100',
        ]);

        $packageName = $request->input('package_tier') ?: $request->input('package');
        $packageMeta = \App\Support\PricingRegistry::get((string)$packageName);
        $isRetail = \App\Support\PricingRegistry::isSelfService((string)$packageName);
        $company = $request->input('company') ?: $request->input('company_name') ?: ($isRetail ? ($validated['name'] . ' (Personal License)') : ($validated['name'] . ' Project'));
        $phoneInput = $validated['phone'];
        $countryCode = $request->input('country_code', '+62');
        $cleanPhone = str_starts_with($phoneInput, '+') ? $phoneInput : "{$countryCode}" . ltrim($phoneInput, '0');

        $sprintBatch = $request->input('sprint_batch');
        $kickoffSlot = $request->input('kickoff_slot');
        $hasBlueprint = $request->input('has_blueprint');
        $blueprintSlug = $request->input('blueprint_slug');
        $umkmCategory = $request->input('umkm_category');

        $lead = \App\Models\LeadContact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'company_name' => $company,
            'job_title' => $isRetail ? 'Digital License Buyer' : 'Project Initiator / Decision Maker',
            'phone' => $cleanPhone,
            'status' => 'lead',
            'metadata' => [
                'source' => $isRetail ? 'Self-Service Retail License Checkout' : 'Pricing & Promotion Page Consultation Form',
                'package_interest' => $packageName,
                'sprint_batch' => $sprintBatch,
                'kickoff_slot' => $kickoffSlot,
                'has_blueprint' => $hasBlueprint,
                'blueprint_slug' => $blueprintSlug,
                'umkm_category' => $umkmCategory,
                'budget_range' => $validated['budget_range'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'voucher_code' => $validated['voucher_code'] ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'submitted_at' => now()->toIso8601String(),
            ],
        ]);

        $displayPackage = $packageMeta['name'] ?? $packageName;

        // Send Filament notification to Admins
        try {
            $admins = \App\Models\User::where('email', 'yoseph.iriandi.tambunan@gmail.com')
                ->orWhereHas('roles', fn ($q) => $q->where('name', 'super_admin'))
                ->get();

            $batchInfo = $sprintBatch ? " [Batch: {$sprintBatch}]" : ($umkmCategory ? " [Kategori: {$umkmCategory}]" : "");
            $notifTitle = $isRetail ? '💳 Pesanan Lisensi Digital Masuk!' : '🎯 Prospek Proyek / Reservasi Sprint Masuk!';
            $notifBody = $isRetail
                ? "Pembeli {$validated['name']} ({$company}) memesan lisensi: {$displayPackage}. Email: {$validated['email']} / WA: {$cleanPhone}."
                : "Klien {$validated['name']} ({$company}) memilih: {$displayPackage}{$batchInfo}.";

            foreach ($admins as $admin) {
                \Filament\Notifications\Notification::make()
                    ->title($notifTitle)
                    ->body($notifBody)
                    ->icon($isRetail ? 'heroicon-o-credit-card' : 'heroicon-o-calendar-days')
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view_lead')
                            ->label('Buka Data Lead')
                            ->url('/' . config('app.admin_path', 'admin') . '/lead-contacts'),
                    ])
                    ->sendToDatabase($admin);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Notification to admin failed: " . $e->getMessage());
        }

        $basePrice = $packageMeta['default_price'] ?? 99000;
        $settingKey = $packageMeta['setting_key'] ?? null;
        if ($settingKey) {
            $rawVal = CmsGlobalSetting::getVal($settingKey, null);
            if (!empty($rawVal)) {
                $cleaned = (int) preg_replace('/[^\d]/', '', (string)$rawVal);
                if ($cleaned > 0) {
                    $basePrice = $cleaned;
                }
            }
        }

        // Voucher Calculation
        $voucherCode = strtoupper(trim((string) ($validated['voucher_code'] ?? '')));
        $voucher = null;
        $discountAmount = 0;
        if (!empty($voucherCode)) {
            $voucher = BlueprintVoucher::where('code', $voucherCode)->first();
            if ($voucher && $voucher->isValid()) {
                if ($voucher->discount_type === 'percent') {
                    $discountAmount = (int) round($basePrice * ((float) $voucher->discount_value / 100));
                } elseif ($voucher->discount_type === 'fixed') {
                    $discountAmount = min($basePrice, (int) $voucher->discount_value);
                } elseif ($voucher->discount_type === 'free_bypass') {
                    $discountAmount = $basePrice;
                }
            }
        }

        $finalAmount = max(0, $basePrice - $discountAmount);
        $isPaidPackage = ($finalAmount > 0 && $packageName !== 'retail_spark' && ($packageMeta['is_paid'] ?? true));

        $snapResponse = null;
        $orderId = null;

        if ($isPaidPackage) {
            // 46 characters (9 + 26 + 1 + 10): strictly conforms to Midtrans 50-character limit
            $orderPrefix = $isRetail ? 'NPRO-LIC-' : 'NPRO-PKG-';
            $orderId = $orderPrefix . $lead->id . '-' . time();

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $finalAmount,
                ],
                'customer_details' => [
                    'first_name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $cleanPhone,
                ],
                'item_details' => [
                    [
                        'id' => strtoupper($packageName),
                        'price' => $finalAmount,
                        'quantity' => 1,
                        'name' => substr($displayPackage, 0, 50),
                    ]
                ],
            ];

            $snapResponse = MidtransSnapService::createSnapToken($params);

            if (empty($snapResponse['token'])) {
                $errorMsg = $snapResponse['error'] ?? 'Sistem gagal menghubungi server Midtrans Snap untuk membuat token pembayaran.';
                \Illuminate\Support\Facades\Log::error("Pricing Inquiry Midtrans Snap Failed for order {$orderId}: {$errorMsg}");

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses sesi pembayaran Midtrans: ' . $errorMsg,
                    'is_paid_package' => true,
                    'order_id' => $orderId,
                ], 422);
            }

            // Store cached checkout details for webhook reconciliation
            Cache::put('pricing_order_' . $orderId, [
                'lead_id' => $lead->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $cleanPhone,
                'company' => $company,
                'package_tier' => $packageName,
                'is_retail' => $isRetail,
                'display_package' => $displayPackage,
                'gross_amount' => $finalAmount,
                'voucher_code' => $voucher?->code,
                'created_at' => now()->toIso8601String(),
            ], now()->addDays(3));

            // Instantly record pending transaction in database for admin visibility
            \App\Models\Transaction::create([
                'user_id' => null,
                'midtrans_order_id' => $orderId,
                'status' => 'pending',
                'total_idr' => $finalAmount,
                'original_currency' => 'IDR',
                'original_amount' => $finalAmount,
                'customer_details' => [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $cleanPhone,
                    'company' => $company,
                    'package_tier' => $packageName,
                    'display_package' => $displayPackage,
                    'is_retail' => $isRetail,
                    'lead_id' => $lead->id,
                    'snap_token' => $snapResponse['token'],
                ],
            ]);
        }

        // Generate pre-filled WhatsApp direct message as optional secondary support
        $settings = CmsGlobalSetting::getAllCached();
        $targetWa = $settings['company_whatsapp']->value ?? '6281260840428';

        $waLines = [];
        if ($isRetail) {
            $waLines[] = "Halo Tim Lisensi Neriah Pro, saya *{$validated['name']}*" . (!empty($company) ? " dari *{$company}*" : "") . ".";
            $waLines[] = "Saya memesan lisensi mandiri: *{$displayPackage}*" . ($orderId ? " (Order ID: {$orderId})" : "");
            $waLines[] = "Mohon panduan aktivasi lisensi resmi untuk akses unduh cetak biru proyek kami.";
            if (!empty($validated['voucher_code'])) {
                $waLines[] = "🏷️ *Kode Voucher/Promo:* " . strtoupper($validated['voucher_code']);
            }
            if (!empty($validated['notes'])) {
                $waLines[] = "📝 *Catatan Ide/Kebutuhan:* " . $validated['notes'];
            }
            $waLines[] = "📧 *Email Terdaftar:* " . $validated['email'];
            $waLines[] = "📱 *WhatsApp:* " . $cleanPhone;
        } else {
            $waLines[] = "Halo Mas Yoseph Iriandi (Lead Architect Neriah Pro), saya *{$validated['name']}*" . (!empty($company) ? " dari *{$company}*" : "") . ".";
            $waLines[] = "Saya mengonfirmasi reservasi paket: *{$displayPackage}*" . ($orderId ? " (Ref: {$orderId})" : "");

            if ($sprintBatch) {
                $waLines[] = "🗓️ *Pilihan Slot Batch:* " . $sprintBatch;
            }
            if ($kickoffSlot) {
                $waLines[] = "⏰ *Waktu Kickoff Sync:* " . $kickoffSlot;
            }
            if ($hasBlueprint) {
                $waLines[] = "📐 *Status PRD:* " . ($hasBlueprint === 'ready' ? "Sudah Ada (Ref: {$blueprintSlug})" : "Belum Ada (Perlu Penyusunan Blueprint)");
            }
            if ($umkmCategory) {
                $waLines[] = "🏢 *Kategori Usaha:* " . $umkmCategory;
            }
            if (!empty($validated['notes'])) {
                $waLines[] = "📝 *Ringkasan Kebutuhan:* " . $validated['notes'];
            }
            if (!empty($validated['voucher_code'])) {
                $waLines[] = "🏷️ *Kode Voucher:* " . strtoupper($validated['voucher_code']);
            }
        }

        $waMessage = implode("\n", $waLines);
        $waUrl = "https://wa.me/{$targetWa}?text=" . rawurlencode($waMessage);

        $successMsg = $isRetail
            ? 'Pesanan lisensi digital mandiri Anda telah diproses. Sesi pembayaran Midtrans resmi telah dibuka.'
            : ($isPaidPackage
                ? 'Reservasi jadwal & paket Anda telah diproses. Sesi pembayaran Midtrans resmi telah disiapkan.'
                : 'Reservasi jadwal sprint & sesi scoping Anda telah dicatat. Lead Architect Yoseph Iriandi akan segera menghubungi Anda untuk koordinasi teknis.');

        return response()->json([
            'success' => true,
            'message' => $successMsg,
            'lead_id' => $lead->id,
            'whatsapp_url' => $isRetail ? null : $waUrl,
            'is_paid_package' => $isPaidPackage,
            'snap_token' => $snapResponse['token'] ?? null,
            'redirect_url' => $snapResponse['redirect_url'] ?? null,
            'order_id' => $orderId,
            'gross_amount' => $finalAmount,
            'net_price' => $finalAmount,
            'formatted_net_price' => 'Rp ' . number_format($finalAmount, 0, ',', '.'),
            'client_key' => $snapResponse['client_key'] ?? config('midtrans.client_key'),
            'snap_url' => $snapResponse['snap_url'] ?? config('midtrans.snap_url'),
            'package_tier' => $packageName,
            'is_retail' => $isRetail,
        ]);
    }

    /**
     * Killer Feature #6: One-Command CLI Bootstrapper (`curl -sL https://neriahpro.com/init/{identifier} | bash`).
     * Resolves VisionBlueprint (by exact ULID `id` or exact `slug`) or PosProject (by exact ULID `id`).
     */
    public function cliInitScript(Request $request, string $identifier)
    {
        $blueprint = VisionBlueprint::query()->where('id', $identifier)->first()
            ?? VisionBlueprint::query()->where('slug', $identifier)->first();

        $posProject = null;
        if (! $blueprint && \Illuminate\Support\Facades\Schema::hasTable('pos_projects')) {
            $posProject = \App\Models\PosProject::query()->where('id', $identifier)->first();
            if ($posProject && $posProject->vision_blueprint_id) {
                $blueprint = VisionBlueprint::query()->find($posProject->vision_blueprint_id);
            }
        }

        if (! $blueprint && ! $posProject) {
            $notFoundScript = "#!/usr/bin/env bash\necho \"[Project OS] Error 404: Blueprint '{$identifier}' tidak ditemukan.\" >&2\nexit 1\n";
            return response($notFoundScript, 404, [
                'Content-Type' => 'text/x-shellscript; charset=UTF-8',
            ]);
        }

        if ($blueprint) {
            $prd = $blueprint->prd_content ?? [];
            if (empty($prd)) {
                $prd = \App\Services\PrdGeneratorService::generate($blueprint);
            }
            $schema = $prd['universal_blueprint_schema']
                ?? \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => true]);
            $appName = $blueprint->nama_bisnis ?: ($schema['project_metadata']['name'] ?? 'Enterprise App');
            $cursorRules = \App\Services\PrdGeneratorService::toCursorrules($blueprint, $prd);
        } else {
            $schema = json_decode((string) $posProject->compiled_schema_json, true) ?: [];
            $appName = $posProject->app_name ?: 'Enterprise App';
            $cursorRules = (string) $posProject->compiled_agents_md;
        }

        $phasedPrompts = $schema['phased_prompt_sequencer']
            ?? \App\Services\ProjectOsBlueprintCompilerService::compilePhasedPrompts($schema);

        $filesToMaterialize = [
            'AGENTS.md' => \App\Services\ProjectOsBlueprintCompilerService::renderUnifiedAgentsMarkdown($schema),
            'CLAUDE.md' => $cursorRules,
            '.cursorrules' => $cursorRules,
            'PROJECT_OS_EXECUTION_BLUEPRINT.md' => \App\Services\ProjectOsBlueprintCompilerService::renderExecutionBlueprintMarkdown($schema),
            '.agents/PONYTAIL.md' => \App\Services\ScaffoldGeneratorService::generatePonytailProtocolMarkdown(),
            '.agents/STACK_TOPOLOGY_CONTRACT.md' => \App\Services\ProjectOsBlueprintCompilerService::renderStackTopologyContractMarkdown($schema),
            '.agents/UI_UX_CONTRACT.md' => \App\Services\ProjectOsBlueprintCompilerService::renderUiUxContractMarkdown(),
            '.agents/ENGINEERING_RULES.md' => \App\Services\ProjectOsBlueprintCompilerService::renderEngineeringRulesMarkdown(),
            '.agents/AGENT_HARNESS_ECC.md' => \App\Services\ProjectOsBlueprintCompilerService::renderAgentHarnessEccMarkdown(),
            '.agents/PRINT_THERMAL_AND_A4_CONTRACT.md' => \App\Services\ProjectOsBlueprintCompilerService::renderPrintSpecMarkdown($schema),
            '.agents/DAY2_OPERATIONS_AND_SECURITY.md' => \App\Services\ProjectOsBlueprintCompilerService::renderDayTwoOpsMarkdown($schema),
            '.agents/DECISION_TABLES_CONTRACT.md' => \App\Services\ProjectOsBlueprintCompilerService::renderDecisionTablesMarkdown($schema),
            '.agents/BLUEPRINT_SCHEMA.json' => (string) json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            '.agents/graphify/graph.json' => \App\Services\ProjectOsBlueprintCompilerService::renderInitialGraphifyJson($schema),
            'app/Actions/Tenancy/InitializeNewTenantAction.php' => \App\Services\ScaffoldGeneratorService::generateInitializeNewTenantAction($schema),
            'app/Domains/Rules/DeterministicDecisionMatrix.php' => \App\Services\ScaffoldGeneratorService::generateDeterministicDecisionMatrixClass($schema, $appName),
            'tests/Feature/Security/TenantIsolationSecurityTest.php' => \App\Services\ScaffoldGeneratorService::generateTenantIsolationSecurityTest($schema, $appName),
            'resources/views/prints/thermal-and-official-a4.blade.php' => \App\Services\ScaffoldGeneratorService::generateThermalAndOfficialPrintView($schema),
            'config/project_os_day2_ops.php' => \App\Services\ScaffoldGeneratorService::generateDayTwoOpsConfig($schema),
            '.env.blueprint' => \App\Services\ScaffoldGeneratorService::generateEnvBlueprintFile($schema, $appName),
            '.mcp.json' => \App\Services\ScaffoldGeneratorService::generateMcpJsonFile($blueprint),
            'database/seeders/RealisticSectorSeeder.php' => \App\Services\ScaffoldGeneratorService::generateRealisticSectorSeeder($schema),
            'verify-blueprint.sh' => \App\Services\BlueprintSlopScannerService::generateVerifyBlueprintBashScript($appName),
        ];

        foreach ($phasedPrompts as $pItem) {
            $fname = $pItem['filename'] ?? ('0' . ($pItem['phase'] ?? 1) . '-phase.md');
            $filesToMaterialize['.agents/prompts/' . $fname] = (string) ($pItem['prompt'] ?? '');
        }

        $safeAppBanner = preg_replace('/[^a-zA-Z0-9 _\-\.\(\)]/', '', $appName);
        $sectorCode = $schema['project_metadata']['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL';
        $stackCode = $schema['project_metadata']['stack_preset'] ?? 'STACK_M1_LARAVEL_FILAMENT_LIVEWIRE';
        $primaryUi = $schema['sectoral_ui_ux_archetype']['primary_archetype'] ?? 'POS_SPLIT_TERMINAL';

        $lines = [
            '#!/usr/bin/env bash',
            '# ==============================================================================',
            "# Neriah Pro Project OS v3.0 — One-Command CLI Bootstrapper",
            "# Target Project : {$safeAppBanner}",
            "# Sector / UI    : {$sectorCode} ({$primaryUi})",
            "# Stack Adapter  : {$stackCode}",
            '# ==============================================================================',
            'set -euo pipefail',
            '',
            'decode_b64() {',
            '  if base64 --decode </dev/null >/dev/null 2>&1; then',
            '    base64 --decode',
            '  else',
            '    base64 -D',
            '  fi',
            '}',
            '',
            'echo "=================================================================="',
            "echo \" [Project OS v3.0] Bootstrapping Blueprint: {$safeAppBanner}\"",
            "echo \" [Sector UI] {$sectorCode} -> {$primaryUi}\"",
            "echo \" [Stack]     {$stackCode}\"",
            'echo "=================================================================="',
            'mkdir -p .agents/graphify .agents/prompts app/Actions/Tenancy app/Domains/Rules tests/Feature/Security resources/views/prints config database/seeders',
            '',
        ];

        foreach ($filesToMaterialize as $relPath => $rawContent) {
            $b64 = base64_encode($rawContent);
            $lines[] = "echo \"  -> Materializing {$relPath}\"";
            $lines[] = "cat <<'EOF_B64' | tr -d '\\n\\r' | decode_b64 > \"{$relPath}\"";
            $lines[] = chunk_split($b64, 76, "\n") . 'EOF_B64';
            if ($relPath === 'verify-blueprint.sh') {
                $lines[] = 'chmod +x verify-blueprint.sh';
            }
            $lines[] = '';
        }

        $lines[] = 'echo "=================================================================="';
        $lines[] = 'echo " [SUCCESS] Project OS v3.0 Workspace Initialized!"';
        $lines[] = 'echo " Next Step 1: Buka AI Agent (Claude Code / Cursor / Antigravity)"';
        $lines[] = 'echo " Next Step 2: Jalankan secara berurutan .agents/prompts/01-foundation.md s/d 06-deployment.md"';
        $lines[] = 'echo " Next Step 3: Verifikasi anti-slop kapan saja dengan ./verify-blueprint.sh"';
        $lines[] = 'echo "=================================================================="';

        \App\Models\AuditLog::record(
            action: 'cli_bootstrap_pulled',
            model: $blueprint ?? $posProject,
            newValues: [
                'identifier' => $identifier,
                'app_name' => $appName,
                'sector' => $sectorCode,
                'stack_preset' => $stackCode,
                'files_count' => count($filesToMaterialize),
            ],
            description: "One-Command CLI Bootstrapper ditarik untuk '{$appName}' ({$identifier}).",
            eventCategory: 'cli_bootstrap'
        );

        return response(implode("\n", $lines) . "\n", 200, [
            'Content-Type' => 'text/x-shellscript; charset=UTF-8',
            'X-Project-OS-Version' => '3.0.0',
        ]);
    }

    /**
     * Killer Feature #4: Reverse-Engineer / Brownfield SQL & Migration Importer API.
     */
    public function brownfieldAudit(Request $request, \App\Services\BrownfieldMigrationAuditorService $auditor): JsonResponse
    {
        $validated = $request->validate([
            'schema_text' => 'required|string|max:150000',
            'project_hint' => 'nullable|string|max:255',
        ]);

        $report = $auditor->auditSchema(
            $validated['schema_text'],
            $validated['project_hint'] ?? null,
            true
        );

        return response()->json($report);
    }

    /**
     * Killer Feature #5: Blueprint Drift Auditor & Slop Scanner API.
     */
    public function slopAudit(Request $request, string $slug, \App\Services\BlueprintSlopScannerService $scanner): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $report = $scanner->auditBlueprint($blueprint, true);

        return response()->json($report);
    }

    /**
     * Layer 8: Interactive Pre-Flight Blueprint Doctor API (before or during blueprint creation).
     */
    public function blueprintDoctor(Request $request, \App\Services\BlueprintDoctorService $doctor): JsonResponse
    {
        $validated = $request->validate([
            'sector_code' => 'nullable|string|max:120',
            'primitive_codes' => 'nullable|array',
            'primitive_codes.*' => 'string|max:120',
            'stack_preset_code' => 'nullable|string|max:120',
            'workflow_text' => 'nullable|string|max:20000',
            'timeline_days' => 'nullable|integer|min:1|max:730',
        ]);

        return response()->json([
            'success' => true,
            'doctor' => $doctor->diagnose($validated, true),
        ]);
    }

    /**
     * Layer 8: Blueprint Doctor Live Audit on a persisted VisionBlueprint.
     */
    public function blueprintSlugDoctor(Request $request, string $slug, \App\Services\BlueprintDoctorService $doctor): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        return response()->json([
            'success' => true,
            'doctor' => $doctor->diagnoseBlueprint($blueprint, true),
        ]);
    }

    /**
     * Lifecycle #1: Create a Surgical Delta Change Request (v1.0 -> v1.1) for Day-30+ Feature Evolution.
     */
    public function createDeltaChangeRequest(
        Request $request,
        string $slug,
        \App\Services\BlueprintDeltaEvolutionService $deltaService
    ): JsonResponse {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'change_summary' => 'nullable|string|max:10000',
            'added_primitives' => 'nullable|array',
            'added_primitives.*' => 'string|max:120',
            'added_tables' => 'nullable|array',
            'added_decision_rules' => 'nullable|array',
        ]);

        $posProject = \App\Models\PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
        $result = $deltaService->createChangeRequest($blueprint, $posProject, $validated, true);

        return response()->json($result, 201);
    }

    /**
     * Lifecycle #1: O(1) Keyset Cursor-Paginated Change Request History for a Blueprint.
     */
    public function listDeltaChangeRequests(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        if (! \Illuminate\Support\Facades\Schema::hasTable('pos_change_requests')) {
            return response()->json([
                'success' => true,
                'items' => [],
                'next_cursor' => null,
            ]);
        }

        $cursorPage = \App\Models\PosChangeRequest::query()
            ->where('vision_blueprint_id', $blueprint->id)
            ->orderBy('id', 'desc')
            ->cursorPaginate(15);

        return response()->json([
            'success' => true,
            'items' => $cursorPage->items(),
            'next_cursor' => $cursorPage->nextCursor()?->encode(),
            'prev_cursor' => $cursorPage->previousCursor()?->encode(),
        ]);
    }

    /**
     * Mitra Commercial #1: Satu Pintu Live Scope-to-Quotation & WBS Estimator API.
     */
    public function commercialEstimate(Request $request, \App\Services\PosCommercialBridgeService $bridge): JsonResponse
    {
        $validated = $request->validate([
            'sector_code' => 'nullable|string|max:120',
            'primitive_codes' => 'nullable|array',
            'primitive_codes.*' => 'string|max:120',
            'stack_preset' => 'nullable|string|max:120',
            'termin_scheme' => 'nullable|string|in:30_50_20,50_50',
        ]);

        $sectorCode = \App\Support\PosSectorUiArchetypeCatalog::normalizeSectorCode(
            (string) ($validated['sector_code'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL')
        );
        $stackCode = \App\Support\PosStackPresetCatalog::normalizePresetCode(
            (string) ($validated['stack_preset'] ?? 'STACK_M1_LARAVEL_MONOLITH')
        );

        $dummyBp = new VisionBlueprint([
            'nama_bisnis' => 'Live Commercial Simulation',
            'slug' => 'live-commercial-simulation',
            'user_metadata' => [
                'primary_sector' => $sectorCode,
                'selected_primitives' => $validated['primitive_codes'] ?? [],
                'stack_preset' => $stackCode,
            ],
        ]);

        $schema = \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($dummyBp, [
            'sector_code' => $sectorCode,
            'primitive_codes' => $validated['primitive_codes'] ?? [],
            'stack_preset' => $stackCode,
            'persist_project' => false,
        ]);

        $quotation = $bridge->calculateScopeQuotation($schema, [
            'stack_preset' => $validated['stack_preset'] ?? $stackCode,
            'termin_scheme' => $validated['termin_scheme'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'quotation' => $quotation,
        ]);
    }

    /**
     * Mitra Commercial #2: Cryptographic Scope Freeze (SHA-256) & Official SPK Generator.
     */
    public function lockScopeAndGenerateSpk(
        Request $request,
        string $slug,
        \App\Services\PosCommercialBridgeService $bridge
    ): JsonResponse {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $validated = $request->validate([
            'termin_scheme' => 'nullable|string|in:30_50_20,50_50',
            'contract_amount' => 'nullable|numeric|min:0',
        ]);

        $posProject = \App\Models\PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
        $result = $bridge->lockScopeAndGenerateSpk($blueprint, $posProject, array_filter($validated, fn ($v) => $v !== null));

        return response()->json($result);
    }

    /**
     * Mitra Commercial #3: Client Approves Paid Change-Order Addendum & Generates Delta Invoice.
     */
    public function approveDeltaAddendum(
        Request $request,
        string $slug,
        string $crId,
        \App\Services\PosCommercialBridgeService $bridge
    ): JsonResponse {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $cr = \App\Models\PosChangeRequest::query()
            ->where('id', $crId)
            ->where('vision_blueprint_id', $blueprint->id)
            ->firstOrFail();

        $validated = $request->validate([
            'signer_name' => 'nullable|string|max:160',
            'signer_email' => 'nullable|email|max:160',
        ]);

        $result = $bridge->approveAddendumAndCreateInvoice($cr, $validated);

        return response()->json($result);
    }

    /**
     * Mitra Commercial #5a: Toggle Interactive Given-When-Then UAT Scenario.
     */
    public function toggleUatScenario(
        Request $request,
        string $slug,
        \App\Services\PosCommercialBridgeService $bridge
    ): JsonResponse {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $validated = $request->validate([
            'scenario_id' => 'required|string|max:80',
            'passed' => 'nullable|boolean',
            'actor_name' => 'nullable|string|max:160',
        ]);

        $posProject = \App\Models\PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
        if (! $posProject) {
            $posProject = app(\App\Services\ProjectOsBlueprintCompilerService::class)->compileAndPersist($blueprint);
        }

        $schema = $posProject && ! empty($posProject->compiled_schema_json)
            ? (is_array($posProject->compiled_schema_json) ? $posProject->compiled_schema_json : (json_decode((string) $posProject->compiled_schema_json, true) ?: []))
            : \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => false]);

        $uat = $bridge->toggleUatScenario(
            $posProject,
            $schema,
            $validated['scenario_id'],
            array_key_exists('passed', $validated) ? (bool) $validated['passed'] : null,
            $validated['actor_name'] ?? null
        );

        return response()->json([
            'success' => true,
            'uat' => $uat,
        ]);
    }

    /**
     * Mitra Commercial #5b: Client Digital UAT Sign-Off & Official BAST Issuance.
     */
    public function signOffUatAndIssueBast(
        Request $request,
        string $slug,
        \App\Services\PosCommercialBridgeService $bridge
    ): JsonResponse {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $validated = $request->validate([
            'signer_name' => 'nullable|string|max:160',
            'signer_email' => 'nullable|email|max:160',
            'mark_all_passed' => 'nullable|boolean',
        ]);
        $validated['signer_ip'] = $request->ip();

        $posProject = \App\Models\PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
        $result = $bridge->signOffUatAndIssueBast($blueprint, $posProject, $validated);

        return response()->json($result, ($result['success'] ?? false) ? 200 : 422);
    }

    /**
     * Render or stream PDF for Official A4 Mitra Commercial Documents (`rab`, `spk-appendix`, `bast`).
     */
    public function mitraDocument(
        Request $request,
        string $slug,
        string $docType,
        \App\Services\PosCommercialBridgeService $bridge
    ) {
        $docType = in_array($docType, ['rab', 'spk-appendix', 'bast'], true) ? $docType : 'rab';
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $posProject = \App\Models\PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
        if (! $posProject) {
            $posProject = app(\App\Services\ProjectOsBlueprintCompilerService::class)->compileAndPersist($blueprint);
        }

        $schema = $posProject && ! empty($posProject->compiled_schema_json)
            ? (is_array($posProject->compiled_schema_json) ? $posProject->compiled_schema_json : (json_decode((string) $posProject->compiled_schema_json, true) ?: []))
            : \App\Services\ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => false]);

        $quotation = ! empty($posProject?->commercial_quotation_json) && is_array($posProject->commercial_quotation_json)
            ? $posProject->commercial_quotation_json
            : $bridge->calculateScopeQuotation($schema);

        $uat = $bridge->buildUatChecklist(
            $schema,
            is_array($posProject?->uat_checklist_json) ? $posProject->uat_checklist_json : []
        );

        $changeRequests = \Illuminate\Support\Facades\Schema::hasTable('pos_change_requests')
            ? \App\Models\PosChangeRequest::query()->where('vision_blueprint_id', $blueprint->id)->orderBy('id', 'asc')->limit(20)->get()
            : collect();

        $viewData = [
            'docType' => $docType,
            'blueprint' => $blueprint,
            'posProject' => $posProject,
            'projectName' => $blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Enterprise Project'),
            'schema' => $schema,
            'quotation' => $quotation,
            'uat' => $uat,
            'changeRequests' => $changeRequests,
            'devInfo' => \App\Support\ContractLegalHelper::getDeveloperInfo(),
            'isPdfStream' => $request->query('download') === 'pdf',
        ];

        if ($request->query('download') === 'pdf' && class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('prints.mitra-commercial-pack', $viewData);

            return $pdf->stream("{$docType}-{$blueprint->slug}.pdf");
        }

        return view('prints.mitra-commercial-pack', $viewData);
    }
}
