<?php

namespace App\Http\Controllers;

use App\Models\VisionBlueprint;
use App\Models\BlueprintVoucher;
use App\Models\Document;
use App\Models\CmsGlobalSetting;
use App\Services\MidtransSnapService;
use App\Services\BlueprintDiscoveryService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BlueprintController extends Controller
{
    /**
     * Show the public Project OS & Vision Blueprint Questionnaire form.
     */
    public function create(Request $request): View
    {
        $globalSettings = CmsGlobalSetting::getAllCached();
        $isBlueprintEnabled = (bool) ($globalSettings['feature_enable_vision_blueprint']->value ?? true);

        if (! $isBlueprintEnabled && ! auth()->user()?->isSuperAdmin()) {
            abort(404);
        }

        $draftId = $request->query('draft_id');
        $slug = $request->query('slug');
        $initialData = [];

        if ($slug) {
            $existingBp = VisionBlueprint::where('slug', $slug)->first();
            if ($existingBp) {
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
                    '_meta' => [
                        'is_editing_slug' => $slug,
                        'raw_idea_text' => $existingBp->masalah_utama,
                    ]
                ];
            }
        } elseif ($draftId && Cache::has('blueprint_draft_' . $draftId)) {
            $initialData = Cache::get('blueprint_draft_' . $draftId, []);
        } elseif (session()->has('blueprint_draft')) {
            $initialData = session('blueprint_draft', []);
        }

        return view('blueprint.create', [
            'globalSettings' => $globalSettings,
            'initialData' => $initialData,
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
            'idea_text' => 'nullable|string|max:30000',
            'files.*' => 'nullable|file|max:15360|mimes:pdf,doc,docx,txt,md,rtf,csv,tsv,xlsx,pptx,png,jpg,jpeg,webp',
            'locale' => 'nullable|string|in:id,en'
        ]);

        $rawIdeaText = $request->input('idea_text', '') ?: '';
        $files = $request->file('files', []) ?: [];
        if (!is_array($files)) {
            $files = [$files];
        }
        $locale = $request->input('locale', app()->getLocale() ?: 'id');
        $projectName = $request->input('nama_bisnis') ?: $request->input('namaBisnis') ?: $request->input('project_name');

        try {
            $synthesized = $discoveryService->synthesize($rawIdeaText, $files, $locale, $projectName);

            $draftId = (string) Str::ulid();
            Cache::put('blueprint_draft_' . $draftId, $synthesized, now()->addHours(24));
            session(['blueprint_draft' => $synthesized]);

            return response()->json([
                'success' => true,
                'message' => $locale === 'en' 
                    ? 'Project blueprint synthesized successfully via MarkItDown & AI Engine.'
                    : 'Ide proyek berhasil dianalisis via MarkItDown & AI Engine.',
                'draft_id' => $draftId,
                'redirect_url' => route('blueprint.create', ['draft_id' => $draftId]),
                'converted_markdown' => $synthesized['_meta']['converted_markdown'] ?? '',
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
            'locale' => 'nullable|string|in:id,en'
        ]);

        $supplementText = $request->input('supplement_text');
        $currentBlueprint = $request->input('blueprint');
        $locale = $request->input('locale', 'id');

        try {
            $result = $discoveryService->supplementIdea($currentBlueprint, $supplementText, $locale);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'affected_fields' => $result['affected_fields'],
                'data' => $result['data'],
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

        // Ensure PRD content is populated or regenerate if requested or missing new evaluation schema or engineering specs
        if (empty($blueprint->prd_content) || !isset($blueprint->prd_content['engineering_specs']) || request()->has('regenerate')) {
            $blueprint->generateAndSavePrd();
            $blueprint->refresh();
        }

        return view('blueprint.show', [
            'blueprint' => $blueprint,
            'prd' => $blueprint->prd_content,
            'globalSettings' => $globalSettings,
        ]);
    }

    /**
     * Generate contract and redirect to sign page
     */
    public function generateContract(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        
        // Ensure PRD with itemized estimation is populated
        if (empty($blueprint->prd_content) || !isset($blueprint->prd_content['itemized_cost_breakdown'])) {
            $blueprint->generateAndSavePrd();
            $blueprint->refresh();
        }

        $tier = request('tier', 'standard');
        $itemizedData = $blueprint->prd_content['itemized_cost_breakdown'] ?? \App\Services\PrdGeneratorService::calculateItemizedEstimation($blueprint);
        $velocityTiers = $blueprint->prd_content['velocity_pricing_options'] ?? ($itemizedData['velocity_tiers'] ?? []);

        $matchedTier = null;
        foreach ($velocityTiers as $vt) {
            if (($vt['id'] ?? '') === $tier) {
                $matchedTier = $vt;
                break;
            }
        }
        if (!$matchedTier && !empty($velocityTiers)) {
            $matchedTier = $velocityTiers[0];
        }

        $contractAmount = $matchedTier ? (float) $matchedTier['contract_amount'] : 25000000.00;
        $dpAmount = $matchedTier ? (float) $matchedTier['dp_amount'] : ($contractAmount * 0.50);

        $overrides = [
            'contract_amount' => $contractAmount,
            'dp_amount' => $dpAmount,
        ];

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
        
        if (empty($blueprint->prd_content) || !isset($blueprint->prd_content['itemized_cost_breakdown'])) {
            $blueprint->generateAndSavePrd();
            $blueprint->refresh();
        }

        $tier = $request->input('tier', 'standard');
        $itemizedData = $blueprint->prd_content['itemized_cost_breakdown'] ?? \App\Services\PrdGeneratorService::calculateItemizedEstimation($blueprint);
        $velocityTiers = $blueprint->prd_content['velocity_pricing_options'] ?? ($itemizedData['velocity_tiers'] ?? []);

        $matchedTier = null;
        foreach ($velocityTiers as $vt) {
            if (($vt['id'] ?? '') === $tier) {
                $matchedTier = $vt;
                break;
            }
        }
        if (!$matchedTier && !empty($velocityTiers)) {
            $matchedTier = $velocityTiers[0];
        }

        $contractAmount = $matchedTier ? (float) $matchedTier['contract_amount'] : 25000000.00;
        $dpAmount = (int) ($matchedTier ? (float) $matchedTier['dp_amount'] : ($contractAmount * 0.50));
        $tierLabel = $matchedTier ? ($matchedTier['name'] ?? 'Standard Velocity') : 'Standard Velocity';

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $dpAmount,
            ],
            'customer_details' => [
                'first_name' => $blueprint->client_name ?: ($blueprint->nama_bisnis ?: 'Client'),
                'email' => $blueprint->email ?: 'client@neriahpro.com',
                'phone' => $blueprint->phone ?: '08123456789',
            ],
            'item_details' => [
                [
                    'id' => 'DP-' . strtoupper($tier),
                    'price' => $dpAmount,
                    'quantity' => 1,
                    'name' => substr('DP (50%) - ' . ($blueprint->nama_bisnis ?: 'Proyek') . ' (' . $tierLabel . ')', 0, 50),
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

        // Record digital sign-off and SHA-256 integrity hash if agreed
        if ($request->boolean('agree_sign_off')) {
            $blueprint->recordSignOff($request->ip(), $request->userAgent());
        }

        return response()->json([
            'success' => true,
            'token' => $snapResponse['token'],
            'redirect_url' => $snapResponse['redirect_url'],
            'order_id' => $orderId,
            'gross_amount' => $dpAmount,
            'client_key' => $snapResponse['client_key'] ?? config('midtrans.client_key'),
            'document_sha256' => $blueprint->document_sha256 ?: $blueprint->calculatePrdHash(),
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
        $prd = $blueprint->prd_content;
        
        // Ensure DomPDF can render this (might need a simpler view, but we can reuse show for now or create a dedicated one later)
        // Here we just render a simple string if no specific PDF view exists, or use a basic template.
        // For production, create a resources/views/pdf/blueprint.blade.php
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('blueprint.show', [
            'blueprint' => $blueprint,
            'prd' => $prd,
        ]);
        
        return $pdf->download('PRD_' . $blueprint->slug . '.pdf');
    }

    /**
     * Download PRD as Markdown
     */
    public function downloadMd(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
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
        $prd = $blueprint->prd_content ?? [];
        
        $md = \App\Services\PrdGeneratorService::toMarkdown($blueprint, $prd);
        
        return response($md, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
