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
use App\Services\Currency\CurrencyExchangeService;
use App\Services\Legal\LegalDocumentVaultService;
use App\Services\MidtransSnapService;
use App\Services\Notification\FonnteWhatsAppService;
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

class BlueprintCommercialController extends Controller
{
    use AuthorizesBlueprintAccess;

    /**
     * Generate contract and redirect to sign page
     */
    public function generateContract(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();

        // Ensure PRD with itemized estimation is populated
        if (empty($blueprint->prd_content) || ! isset($blueprint->prd_content['itemized_cost_breakdown'])) {
            $blueprint->generateAndSavePrd();
            $blueprint->refresh();
        }

        $tier = request('tier', 'standard');
        $matchedTier = PrdGeneratorService::resolveVelocityTier($blueprint, $tier);
        $contractAmount = (float) $matchedTier['contract_amount'];
        $retailCredit = $blueprint->getPaidRetailAmount();
        $rawDp = (float) round($contractAmount * 0.50);
        $dpAmount = max(0, $rawDp - $retailCredit);

        $isUpgradeFromRetail = $blueprint->hasPaidRetailLicense();
        $existingContract = $blueprint->getContractDocument();

        // If existing contract is already signed for this EXACT amount and tier, redirect directly to signed view
        if ($existingContract && $existingContract->status === 'signed' && (float) $existingContract->contract_amount === $contractAmount && (float) $existingContract->dp_amount === $dpAmount) {
            return redirect()->route('document.sign', ['document' => $existingContract->id]);
        }

        // Check if voucher applied
        $voucherCode = strtoupper(trim((string) request('voucher', request('voucher_code', ''))));
        $appliedVoucher = null;
        if (! empty($voucherCode)) {
            $voucher = BlueprintVoucher::where('code', $voucherCode)->first();
            if ($voucher && $voucher->isValid()) {
                $appliedVoucher = $voucher;
                if ($voucher->discount_type === 'free_bypass' || ($voucher->discount_type === 'percent' && (float) $voucher->discount_value >= 100)) {
                    $contractAmount = 0.00;
                    $dpAmount = 0.00;
                } elseif ($voucher->discount_type === 'percent') {
                    $contractAmount = max(0, $contractAmount - round($contractAmount * ((float) $voucher->discount_value / 100)));
                    $dpAmount = max(0, round($contractAmount * 0.50) - $retailCredit);
                } elseif ($voucher->discount_type === 'fixed') {
                    $contractAmount = max(0, $contractAmount - (float) $voucher->discount_value);
                    $dpAmount = max(0, round($contractAmount * 0.50) - $retailCredit);
                }
            }
        }

        $overrides = [
            'contract_amount' => $contractAmount,
            'dp_amount' => $dpAmount,
            'status' => 'pending_signature',
            'signed_at' => null,
            'signer_ip_address' => null,
            'digital_signature_image' => null,
            'document_hash' => null,
            'title' => 'Perjanjian Kerja Sama Turnkey Studio - ' . ($blueprint->nama_bisnis ?: $blueprint->client_name) . ' (' . ($matchedTier['name'] ?? 'Velocity') . ')',
        ];
        if ($appliedVoucher) {
            $overrides['voucher_code'] = $appliedVoucher->code;
        }

        // Reset signed agreement on blueprint if upgrading to turnkey studio
        if ($isUpgradeFromRetail && ! $blueprint->isDpConfirmed()) {
            $blueprint->update([
                'signed_agreement' => false,
                'signed_at' => null,
                'document_sha256' => null,
            ]);
        }

        $document = $blueprint->documents()->where('document_type', 'contract')->first();
        if (! $document) {
            $document = $blueprint->convertToDigitalContract($overrides);
        } else {
            $document->update([
                'title' => $overrides['title'],
                'status' => 'pending_signature',
                'contract_amount' => $overrides['contract_amount'],
                'dp_amount' => $overrides['dp_amount'],
                'signed_at' => null,
                'signer_ip_address' => null,
                'digital_signature_image' => null,
                'document_hash' => null,
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

        if (empty($blueprint->prd_content) || ! isset($blueprint->prd_content['itemized_cost_breakdown'])) {
            $blueprint->generateAndSavePrd();
            $blueprint->refresh();
        }

        $existingContract = $blueprint->getContractDocument();
        $isSignedContract = $existingContract && $existingContract->status === 'signed' && (float) $existingContract->contract_amount > 0;

        if ($isSignedContract) {
            $contractAmount = (float) $existingContract->contract_amount;
            $tierLabel = $existingContract->title ?: 'Kontrak Tertandatangani';
            $matchedTier = [
                'id' => 'signed_contract',
                'name' => $tierLabel,
                'contract_amount' => $contractAmount,
                'dp_amount' => (float) ($existingContract->dp_amount ?: ($contractAmount * 0.50)),
            ];
        } else {
            $tier = $request->input('tier', 'standard');
            $matchedTier = PrdGeneratorService::resolveVelocityTier($blueprint, $tier);
            $contractAmount = (float) $matchedTier['contract_amount'];
            $tierLabel = $matchedTier['name'] ?? 'Standard Velocity';
        }

        // Check for voucher discount
        $voucherCode = strtoupper(trim((string) $request->input('voucher_code', '')));
        $voucher = null;
        $discountAmount = 0;
        if (! empty($voucherCode)) {
            $voucher = BlueprintVoucher::where('code', $voucherCode)->first();
            if ($voucher && $voucher->isValid()) {
                if ($voucher->discount_type === 'percent') {
                    $discountAmount = round($contractAmount * ((float) $voucher->discount_value / 100));
                } elseif ($voucher->discount_type === 'fixed') {
                    $discountAmount = min($contractAmount, (float) $voucher->discount_value);
                }
            }
        }

        $retailCredit = (int) $blueprint->getPaidRetailAmount();
        $finalContractAmount = max(0, $contractAmount - $discountAmount);
        $rawDpAmount = (int) round($finalContractAmount * 0.50);
        $finalDpAmount = max(0, $rawDpAmount - $retailCredit);

        if ($finalDpAmount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan DP telah 100% tertutupi oleh kredit lisensi retail atau voucher. Silakan hubungi admin atau tanda tangani kontrak untuk konfirmasi.',
            ], 400);
        }

        // Guaranteed Order ID generation with full 26-character ULID (45 chars <= 50 Midtrans limit)
        $orderId = 'NPRO-DP-'.$blueprint->id.'-'.time();

        $itemName = 'DP (50%) - '.($blueprint->nama_bisnis ?: 'Proyek');
        if ($retailCredit > 0) {
            $itemName .= ' (Kredit -Rp '.number_format($retailCredit, 0, ',', '.').')';
        } elseif ($voucher) {
            $itemName .= ' (Disc '.$voucher->code.')';
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
                    'id' => 'DP-'.strtoupper($matchedTier['id'] ?? $tier),
                    'price' => $finalDpAmount,
                    'quantity' => 1,
                    'name' => substr($itemName, 0, 50),
                ],
            ],
        ];

        $snapResponse = MidtransSnapService::createSnapToken($params);

        if (! $snapResponse['success']) {
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
            Transaction::where('midtrans_order_id', $existingDoc->midtrans_order_id)
                ->where('status', 'pending')
                ->update(['status' => 'cancel']);
        }

        // Attach/update Digital Contract Document with order_id for webhook fulfillment
        $isSigned = $existingDoc && $existingDoc->status === 'signed' && (float) $existingDoc->contract_amount > 0;

        Document::updateOrCreate(
            [
                'related_type' => VisionBlueprint::class,
                'related_id' => $blueprint->id,
                'document_type' => 'contract',
            ],
            [
                'title' => $existingDoc?->title ?: ('Perjanjian Kerja Sama - '.($blueprint->nama_bisnis ?: $blueprint->client_name)),
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
        Transaction::create([
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

        if (! $blueprint->isDpConfirmed()) {
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
        if ($existingContract && (float) $existingContract->contract_amount > 0) {
            $contractAmount = (float) $existingContract->contract_amount;
            $dpAmount = (float) ($existingContract->dp_amount ?: ($contractAmount * 0.50));
            $pelunasanAmount = max(0, $contractAmount - $dpAmount);
        } else {
            $tier = $blueprint->user_metadata['package_tier'] ?? 'standard';
            $resolvedTier = PrdGeneratorService::resolveVelocityTier($blueprint, $tier);
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
        $orderId = 'NPRO-FINAL-'.$blueprint->id.'-'.time();
        $itemName = 'Pelunasan (50%) - '.($blueprint->nama_bisnis ?: 'Proyek');

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
                ],
            ],
        ];

        // Cancel any previous pending pelunasan transaction for this blueprint
        if ($existingContract && ! empty($existingContract->content_clauses['pelunasan_order_id'])) {
            Transaction::where('midtrans_order_id', $existingContract->content_clauses['pelunasan_order_id'])
                ->where('status', 'pending')
                ->update(['status' => 'cancel']);
        }

        $snapResponse = MidtransSnapService::createSnapToken($params);

        if (! $snapResponse['success']) {
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
        Transaction::create([
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
            'formatted_pelunasan_amount' => 'Rp '.number_format($pelunasanAmount, 0, ',', '.'),
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

        if (! $voucher || ! $voucher->isValid()) {
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
            'message' => 'Voucher valid! '.($voucher->description ?: 'Potongan biaya berhasil diterapkan.'),
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

        if (! $voucher || ! $voucher->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher tidak valid, kuota telah habis, atau sudah kedaluwarsa.',
            ], 422);
        }

        $isFreeBypass = $voucher->discount_type === 'free_bypass'
            || ($voucher->discount_type === 'percent' && (float) $voucher->discount_value >= 100);

        if (! $isFreeBypass) {
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
        if (! $document) {
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
        if (! empty($blueprint->email) && filter_var($blueprint->email, FILTER_VALIDATE_EMAIL) && ! str_contains($blueprint->email, '@example.com')) {
            try {
                Mail::to($blueprint->email)->send(
                    new BlueprintReadyNotificationMail($blueprint, 'Free Grant // Voucher')
                );
            } catch (\Throwable $mailEx) {
                \Illuminate\Support\Facades\Log::warning('Gagal kirim notifikasi claim voucher: '.$mailEx->getMessage());
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
     * 1-Click VIP Owner Bypass for a single blueprint.
     * Instantly unlocks all 15-pillar Project OS features, signed contract, staging sandbox, and full active sprint status.
     * Strictly gated to Super Administrator (Solo Founder).
     */
    public function vipOwnerBypass(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses Ditolak: Hanya Super Administrator yang berhak menggunakan VIP Bypass.'], 403);
            }
            abort(403, 'Akses Ditolak: Fitur 1-Click VIP Owner Bypass hanya dapat diakses oleh Super Administrator (Solo Founder).');
        }

        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();

        // Record digital sign-off and SHA-256 audit hash
        $blueprint->recordSignOff($request->ip(), $request->userAgent());

        // Provision staging demo sandbox URL
        $stagingUrl = $blueprint->provisionStagingUrl();

        // Update blueprint status to Active Sprint with VIP bypass
        $blueprint->update([
            'voucher_code' => 'VIP-OWNER-BYPASS',
            'is_free_grant' => true,
            'is_published' => true,
            'project_status' => 'Active Sprint',
            'signed_agreement' => true,
        ]);

        // Interconnect with Document/Contract with explicit signer metadata
        Document::updateOrCreate(
            [
                'related_type' => VisionBlueprint::class,
                'related_id' => $blueprint->id,
                'document_type' => 'contract',
            ],
            [
                'title' => 'Kontrak VIP Founder OS - ' . ($blueprint->nama_bisnis ?: $blueprint->client_name),
                'status' => 'signed',
                'scope_locked' => true,
                'contract_amount' => 0.00,
                'dp_amount' => 0.00,
                'signed_at' => now(),
                'signer_name' => auth()->user()->name ?? ($blueprint->client_name ?: 'Solo Founder (Super Admin)'),
                'signer_email' => auth()->user()->email,
                'signer_ip_address' => $request->ip(),
                'document_hash' => $blueprint->document_sha256 ?: $blueprint->calculatePrdHash(),
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '👑 VIP Founder Bypass Berhasil: Seluruh 15 Pilar Project OS & Active Sprint telah diaktifkan!',
                'staging_url' => $stagingUrl,
                'document_sha256' => $blueprint->document_sha256,
                'redirect_url' => route('blueprint.show', $blueprint->slug),
            ]);
        }

        return redirect()->route('blueprint.show', $blueprint->slug)->with('success', '👑 [VIP OWNER BYPASS] Proyek resmi diaktifkan ke status Active Sprint! Seluruh 15 Pilar Project OS, staging sandbox, dan kontrak digital telah terbuka.');
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

        if ($request->filled('email')) {
            $request->merge([
                'email' => InputHygieneStandard::sanitizeStrictEmail((string) $request->input('email')),
            ]);
        }

        $packageName = $request->input('package_tier') ?: ($request->input('package') ?: $request->input('selected_package'));
        $isGrant = in_array((string) $packageName, ['impact_nonprofit_grant', 'impact_csr', 'grant'], true);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', 'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/'],
            'phone' => 'required|string|max:50',
            'company' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'package_tier' => 'required_without_all:package,selected_package|nullable|string|max:255',
            'package' => 'required_without_all:package_tier,selected_package|nullable|string|max:255',
            'selected_package' => 'required_without_all:package_tier,package|nullable|string|max:255',
            'country_code' => 'nullable|string|max:10',
            'budget_range' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:2000',
            'voucher_code' => 'nullable|string|max:50',
            'sprint_batch' => 'nullable|string|max:100',
            'kickoff_slot' => 'nullable|string|max:100',
            'has_blueprint' => 'nullable|string|max:50',
            'blueprint_slug' => 'nullable|string|max:100',
            'umkm_category' => 'nullable|string|max:100',
            'legal_decree_number' => $isGrant ? 'required|string|max:160' : 'nullable|string|max:160',
            'legal_document' => $isGrant ? 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240' : 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'organization_type' => 'nullable|string|max:50',
        ]);

        $packageMeta = PricingRegistry::get((string) $packageName);
        $isRetail = PricingRegistry::isSelfService((string) $packageName);
        $company = $request->input('company') ?: $request->input('company_name') ?: ($isRetail ? ($validated['name'].' (Personal License)') : ($validated['name'].' Project'));
        $phoneInput = (string) $validated['phone'];
        $countryCode = (string) $request->input('country_code', '+62');
        $cleanPhone = str_starts_with(trim($phoneInput), '+')
            ? ('+'.(preg_replace('/\D/', '', $phoneInput) ?: ''))
            : InputHygieneStandard::toE164($countryCode, $phoneInput);

        $sprintBatch = $request->input('sprint_batch');
        $kickoffSlot = $request->input('kickoff_slot');
        $hasBlueprint = $request->input('has_blueprint');
        $blueprintSlug = $request->input('blueprint_slug');
        $umkmCategory = $request->input('umkm_category');

        $lead = LeadContact::create([
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

        // Secure Legal Document Partitioning & 1-Click Verification for Non-Profit/CSR Grants
        $uploadedDocMeta = null;
        $grantApplicationId = null;

        if ($packageName === 'impact_nonprofit_grant') {
            $grantApplicationId = strtolower((string) Str::ulid());
            $decreeNumber = (string) ($request->input('legal_decree_number') ?: ('SK-SUBMITTED-'.strtoupper(substr($lead->id, -8))));
            $orgType = (string) ($request->input('organization_type') ?: 'NONPROFIT_YAYASAN');
            if (! in_array($orgType, ['NONPROFIT_YAYASAN', 'NGO_SOCIAL', 'RELIGIOUS_HOUSE', 'ACADEMIC_CAMPUS'], true)) {
                $orgType = 'NONPROFIT_YAYASAN';
            }

            if ($request->hasFile('legal_document')) {
                try {
                    $uploadedDocMeta = LegalDocumentVaultService::storeLegalDocument(
                        $request->file('legal_document'),
                        'impact_grants',
                        $grantApplicationId,
                        [
                            'lead_id' => $lead->id,
                            'name' => $validated['name'],
                            'company' => $company,
                            'decree_number' => $decreeNumber,
                        ]
                    );
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to store legal document in vault: '.$e->getMessage());
                }
            }

            $grantApplication = PosImpactGrantApplication::create([
                'id' => $grantApplicationId,
                'user_id' => Auth::id(),
                'applicant_name' => $validated['name'],
                'organization_name' => $company,
                'organization_type' => $orgType,
                'official_email' => $validated['email'],
                'phone_country_code' => $countryCode,
                'phone_number' => $cleanPhone,
                'legal_decree_number' => $decreeNumber,
                'private_document_path' => $uploadedDocMeta['storage_path'] ?? null,
                'requested_project_budget_idr' => 0,
                'mission_statement' => [
                    'id' => $validated['notes'] ?? 'Pengajuan Hibah CSR Yayasan/NGO/Rumah Ibadah 100% via Formulir Pricing.',
                    'en' => $validated['notes'] ?? '100% Subsidized CSR Non-Profit/Faith Grant Application via Pricing Form.',
                ],
                'status' => 'PENDING_REVIEW',
            ]);

            if ($uploadedDocMeta) {
                Document::create([
                    'title' => 'Dokumen Legalitas: '.$company,
                    'document_type' => 'legal_proof',
                    'related_type' => PosImpactGrantApplication::class,
                    'related_id' => $grantApplication->id,
                    'file_path' => $uploadedDocMeta['storage_path'],
                    'document_hash' => $uploadedDocMeta['sha256'],
                    'status' => 'draft',
                    'scope_locked' => true,
                ]);
            }
        } elseif ($request->hasFile('legal_document')) {
            try {
                $uploadedDocMeta = LegalDocumentVaultService::storeLegalDocument(
                    $request->file('legal_document'),
                    'leads',
                    (string) $lead->id,
                    [
                        'lead_id' => $lead->id,
                        'name' => $validated['name'],
                        'company' => $company,
                    ]
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to store legal document in vault: '.$e->getMessage());
            }
        }

        if ($uploadedDocMeta || $grantApplicationId) {
            $currentMeta = $lead->metadata ?? [];
            if ($uploadedDocMeta) {
                $currentMeta['legal_document'] = $uploadedDocMeta;
            }
            if ($grantApplicationId) {
                $currentMeta['grant_application_id'] = $grantApplicationId;
            }
            $lead->update(['metadata' => $currentMeta]);
        }

        $displayPackage = $packageMeta['name'] ?? $packageName;

        // Send Filament notification to Admins via Spatie RBAC roles (Zero Hardcoded Email)
        try {
            $admins = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'developer']))
                ->get();

            $batchInfo = $sprintBatch ? " [Batch: {$sprintBatch}]" : ($umkmCategory ? " [Kategori: {$umkmCategory}]" : '');
            $notifTitle = $isRetail ? '💳 Pesanan Lisensi Digital Masuk!' : '🎯 Prospek Proyek / Reservasi Sprint Masuk!';
            $notifBody = $isRetail
                ? "Pembeli {$validated['name']} ({$company}) memesan lisensi: {$displayPackage}. Email: {$validated['email']} / WA: {$cleanPhone}."
                : "Klien {$validated['name']} ({$company}) memilih: {$displayPackage}{$batchInfo}.";

            foreach ($admins as $admin) {
                Notification::make()
                    ->title($notifTitle)
                    ->body($notifBody)
                    ->icon($isRetail ? 'heroicon-o-credit-card' : 'heroicon-o-calendar-days')
                    ->actions([
                        Action::make('view_lead')
                            ->label('Buka Data Lead')
                            ->url('/'.config('app.admin_path', 'admin').'/lead-contacts'),
                    ])
                    ->sendToDatabase($admin);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Notification to admin failed: '.$e->getMessage());
        }

        // Automated Real-Time WhatsApp Alert to Admin via Fonnte API
        if ($packageName === 'impact_nonprofit_grant' && isset($grantApplication)) {
            try {
                FonnteWhatsAppService::notifyNewGrantApplication($grantApplication, $uploadedDocMeta);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Fonnte WhatsApp notification failed for grant application: '.$e->getMessage());
            }
        }

        $basePrice = $packageMeta['default_price'] ?? 99000;
        $settingKey = $packageMeta['setting_key'] ?? null;
        if ($settingKey) {
            $rawVal = CmsGlobalSetting::getVal($settingKey, null);
            if (! empty($rawVal)) {
                $cleaned = (int) preg_replace('/[^\d]/', '', (string) $rawVal);
                if ($cleaned > 0) {
                    $basePrice = $cleaned;
                }
            }
        }

        // Voucher Calculation
        $voucherCode = strtoupper(trim((string) ($validated['voucher_code'] ?? '')));
        $voucher = null;
        $discountAmount = 0;
        if (! empty($voucherCode)) {
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
            $orderId = $orderPrefix.$lead->id.'-'.time();

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
                    ],
                ],
            ];

            $snapResponse = MidtransSnapService::createSnapToken($params);

            if (empty($snapResponse['token'])) {
                $errorMsg = $snapResponse['error'] ?? 'Sistem gagal menghubungi server Midtrans Snap untuk membuat token pembayaran.';
                \Illuminate\Support\Facades\Log::error("Pricing Inquiry Midtrans Snap Failed for order {$orderId}: {$errorMsg}");

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses sesi pembayaran Midtrans: '.$errorMsg,
                    'is_paid_package' => true,
                    'order_id' => $orderId,
                ], 422);
            }

            // Store cached checkout details for webhook reconciliation
            Cache::put('pricing_order_'.$orderId, [
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
                'blueprint_slug' => $blueprintSlug,
                'created_at' => now()->toIso8601String(),
            ], now()->addDays(3));

            // Instantly record pending transaction in database for admin visibility
            $selectedCurrency = strtoupper(trim((string) $request->input('selected_currency', 'IDR')));
            if (! in_array($selectedCurrency, ['IDR', 'USD', 'EUR', 'SGD', 'GBP', 'AUD', 'JPY'], true)) {
                $selectedCurrency = 'IDR';
            }
            $exchangeRate = (float) $request->input('exchange_rate', 0);
            if ($exchangeRate <= 0) {
                $exchangeRate = CurrencyExchangeService::getRate($selectedCurrency);
            }
            $foreignAmount = (float) $request->input('foreign_amount', 0);
            if ($foreignAmount <= 0) {
                $converted = CurrencyExchangeService::convert((float) $finalAmount, $selectedCurrency);
                $foreignAmount = (float) $converted['converted_amount'];
            }

            Transaction::create([
                'user_id' => null,
                'midtrans_order_id' => $orderId,
                'status' => 'pending',
                'total_idr' => $finalAmount,
                'original_currency' => $selectedCurrency,
                'original_amount' => $foreignAmount,
                'exchange_rate' => $exchangeRate,
                'customer_details' => [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $cleanPhone,
                    'company' => $company,
                    'package_tier' => $packageName,
                    'display_package' => $displayPackage,
                    'is_retail' => $isRetail,
                    'lead_id' => $lead->id,
                    'blueprint_slug' => $blueprintSlug,
                    'snap_token' => $snapResponse['token'],
                    'currency' => $selectedCurrency,
                    'exchange_rate' => $exchangeRate,
                    'foreign_amount' => $foreignAmount,
                    'rate_timestamp' => now()->toIso8601String(),
                    'rate_source' => 'Bank Indonesia / Open FX Real-Time Feed',
                ],
            ]);

            // Link pending order to existing blueprint to guarantee zero orphan blueprints
            if ($blueprintSlug) {
                try {
                    $existingBp = VisionBlueprint::where('slug', $blueprintSlug)->first();
                    if ($existingBp) {
                        $meta = $existingBp->user_metadata ?? [];
                        $meta['pending_order_id'] = $orderId;
                        $meta['pending_package_tier'] = $packageName;
                        $meta['selected_track'] = $isRetail ? 'retail' : 'studio';
                        $existingBp->update(['user_metadata' => $meta]);
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Failed to link pending order {$orderId} to blueprint {$blueprintSlug}: " . $e->getMessage());
                }
            }
        }

        // Automated Real-Time WhatsApp Alert to Admin for new Leads & Orders
        if ($packageName !== 'impact_nonprofit_grant') {
            try {
                FonnteWhatsAppService::notifyNewLead($lead, $displayPackage, $isRetail, $orderId, (int) $finalAmount);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Fonnte WhatsApp notification failed for lead: '.$e->getMessage());
            }
        }

        // Generate pre-filled WhatsApp direct message as optional secondary support
        $settings = CmsGlobalSetting::getAllCached();
        $targetWa = $settings['company_whatsapp']->value ?? '6281260840428';

        $waLines = [];
        if ($isRetail) {
            $waLines[] = "Halo Tim Lisensi Neriah Pro, saya *{$validated['name']}*".(! empty($company) ? " dari *{$company}*" : '').'.';
            $waLines[] = "Saya memesan lisensi mandiri: *{$displayPackage}*".($orderId ? " (Order ID: {$orderId})" : '');
            $waLines[] = 'Mohon panduan aktivasi lisensi resmi untuk akses unduh cetak biru proyek kami.';
            if (! empty($validated['voucher_code'])) {
                $waLines[] = '🏷️ *Kode Voucher/Promo:* '.strtoupper($validated['voucher_code']);
            }
            if (! empty($validated['notes'])) {
                $waLines[] = '📝 *Catatan Ide/Kebutuhan:* '.$validated['notes'];
            }
            $waLines[] = '📧 *Email Terdaftar:* '.$validated['email'];
            $waLines[] = '📱 *WhatsApp:* '.$cleanPhone;
        } else {
            $waLines[] = "Halo Mas Yoseph Iriandi (Lead Architect Neriah Pro), saya *{$validated['name']}*".(! empty($company) ? " dari *{$company}*" : '').'.';
            $waLines[] = "Saya mengonfirmasi reservasi paket: *{$displayPackage}*".($orderId ? " (Ref: {$orderId})" : '');

            if ($sprintBatch) {
                $waLines[] = '🗓️ *Pilihan Slot Batch:* '.$sprintBatch;
            }
            if ($kickoffSlot) {
                $waLines[] = '⏰ *Waktu Kickoff Sync:* '.$kickoffSlot;
            }
            if ($hasBlueprint) {
                $waLines[] = '📐 *Status PRD:* '.($hasBlueprint === 'ready' ? "Sudah Ada (Ref: {$blueprintSlug})" : 'Belum Ada (Perlu Penyusunan Blueprint)');
            }
            if ($umkmCategory) {
                $waLines[] = '🏢 *Kategori Usaha:* '.$umkmCategory;
            }
            if (! empty($validated['notes'])) {
                $waLines[] = '📝 *Ringkasan Kebutuhan:* '.$validated['notes'];
            }
            if (! empty($validated['voucher_code'])) {
                $waLines[] = '🏷️ *Kode Voucher:* '.strtoupper($validated['voucher_code']);
            }
        }

        $waMessage = implode("\n", $waLines);
        $waUrl = "https://wa.me/{$targetWa}?text=".rawurlencode($waMessage);

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
            'formatted_net_price' => 'Rp '.number_format($finalAmount, 0, ',', '.'),
            'client_key' => $snapResponse['client_key'] ?? config('midtrans.client_key'),
            'snap_url' => $snapResponse['snap_url'] ?? config('midtrans.snap_url'),
            'package_tier' => $packageName,
            'is_retail' => $isRetail,
            'grant_application_id' => $grantApplicationId,
            'legal_document' => $uploadedDocMeta ? [
                'name' => $uploadedDocMeta['original_name'],
                'size_bytes' => $uploadedDocMeta['size_bytes'],
            ] : null,
        ]);
    }

    /**
     * Mitra Commercial #1: Satu Pintu Live Scope-to-Quotation & WBS Estimator API.
     */
    public function commercialEstimate(Request $request, PosCommercialBridgeService $bridge): JsonResponse
    {
        $validated = $request->validate([
            'sector_code' => 'nullable|string|max:120',
            'primitive_codes' => 'nullable|array',
            'primitive_codes.*' => 'string|max:120',
            'stack_preset' => 'nullable|string|max:120',
            'selected_menus' => 'nullable|array',
            'selected_menus.*' => 'string|max:40',
            'selected_ui_variant' => 'nullable|string|max:80',
            'navigation_shell' => 'nullable|string|max:48',
            'public_ux_preset' => 'nullable|string|max:80',
            'output_locale' => 'nullable|string|max:16',
            'termin_scheme' => 'nullable|string|in:30_50_20,50_50',
        ]);

        $sectorCode = PosSectorUiArchetypeCatalog::normalizeSectorCode(
            (string) ($validated['sector_code'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL')
        );
        $stackCode = PosStackPresetCatalog::normalizePresetCode(
            (string) ($validated['stack_preset'] ?? 'STACK_M1_LARAVEL_MONOLITH')
        );

        $dummyBp = new VisionBlueprint([
            'nama_bisnis' => 'Live Commercial Simulation',
            'slug' => 'live-commercial-simulation',
            'user_metadata' => [
                'primary_sector' => $sectorCode,
                'selected_primitives' => $validated['primitive_codes'] ?? [],
                'stack_preset' => $stackCode,
                'selected_menus' => $validated['selected_menus'] ?? null,
                'selected_ui_variant' => $validated['selected_ui_variant'] ?? null,
                'navigation_shell' => $validated['navigation_shell'] ?? null,
                'public_ux_preset' => $validated['public_ux_preset'] ?? null,
                'output_locale' => $validated['output_locale'] ?? 'id',
            ],
        ]);

        $schema = ProjectOsBlueprintCompilerService::compileUniversalSchema($dummyBp, [
            'sector_code' => $sectorCode,
            'primitive_codes' => $validated['primitive_codes'] ?? [],
            'stack_preset' => $stackCode,
            'selected_menus' => $validated['selected_menus'] ?? null,
            'selected_ui_variant' => $validated['selected_ui_variant'] ?? null,
            'navigation_shell' => $validated['navigation_shell'] ?? null,
            'public_ux_preset' => $validated['public_ux_preset'] ?? null,
            'output_locale' => $validated['output_locale'] ?? 'id',
            'persist_project' => false,
        ]);

        $quotation = $bridge->calculateScopeQuotation($schema, [
            'stack_preset' => $validated['stack_preset'] ?? $stackCode,
            'selected_ui_variant' => $validated['selected_ui_variant'] ?? null,
            'navigation_shell' => $validated['navigation_shell'] ?? null,
            'public_ux_preset' => $validated['public_ux_preset'] ?? null,
            'output_locale' => $validated['output_locale'] ?? 'id',
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
        PosCommercialBridgeService $bridge
    ): JsonResponse {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $validated = $request->validate([
            'termin_scheme' => 'nullable|string|in:30_50_20,50_50',
            'contract_amount' => 'nullable|numeric|min:0',
        ]);

        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
        $result = $bridge->lockScopeAndGenerateSpk($blueprint, $posProject, array_filter($validated, fn ($v) => $v !== null));

        return response()->json($result);
    }

    /**
     * Unlock Scope Helper: Membuka kunci scope agar pengguna/admin dapat melanjutkan elaborasi.
     * Dapat dipanggil melalui AJAX atau tombol web biasa.
     */
    public function unlockScope(
        Request $request,
        string $slug,
        PosCommercialBridgeService $bridge
    ): JsonResponse|RedirectResponse {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $actor = Auth::user();
        $reason = (string) $request->input('reason', '');

        $result = $bridge->unlockScope($blueprint, $actor, $reason);

        if (! $result['success']) {
            if ($request->wantsJson()) {
                return response()->json($result, 422);
            }

            return redirect()->route('blueprint.show', $blueprint->slug)
                ->with('error', $result['message']);
        }

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->route('blueprint.show', $blueprint->slug)
            ->with('status', $result['message']);
    }

    /**
     * Unlock Scope GET Helper (for quick 1-click links).
     */
    public function unlockScopeRedirect(
        Request $request,
        string $slug,
        PosCommercialBridgeService $bridge
    ): RedirectResponse {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $actor = Auth::user();
        $reason = (string) $request->input('reason', 'Quick 1-Click Unlock');

        $result = $bridge->unlockScope($blueprint, $actor, $reason);

        if (! $result['success']) {
            return redirect()->route('blueprint.show', $blueprint->slug)
                ->with('error', $result['message']);
        }

        if ($request->query('edit') || $request->input('edit')) {
            return redirect()->route('blueprint.create', ['slug' => $blueprint->slug])
                ->with('status', 'Scope berhasil dibuka. Silakan sesuaikan spesifikasi proyek Anda.');
        }

        return redirect()->route('blueprint.show', $blueprint->slug)
            ->with('status', $result['message']);
    }

    /**
     * Render or stream PDF for Official A4 Mitra Commercial Documents (`rab`, `spk-appendix`, `bast`, `sop-training`).
     */
    public function mitraDocument(
        Request $request,
        string $slug,
        string $docType,
        PosCommercialBridgeService $bridge
    ) {
        $docType = in_array($docType, ['rab', 'spk-appendix', 'bast', 'sop-training'], true) ? $docType : 'rab';
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
        if (! $posProject) {
            $posProject = app(ProjectOsBlueprintCompilerService::class)->compileAndPersist($blueprint);
        }

        $schema = $posProject && ! empty($posProject->compiled_schema_json)
            ? (is_array($posProject->compiled_schema_json) ? $posProject->compiled_schema_json : (json_decode((string) $posProject->compiled_schema_json, true) ?: []))
            : ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => false]);

        $quotation = ! empty($posProject?->commercial_quotation_json) && is_array($posProject->commercial_quotation_json)
            ? $posProject->commercial_quotation_json
            : $bridge->calculateScopeQuotation($schema);

        $uat = $bridge->buildUatChecklist(
            $schema,
            is_array($posProject?->uat_checklist_json) ? $posProject->uat_checklist_json : []
        );

        $changeRequests = Schema::hasTable('pos_change_requests')
            ? PosChangeRequest::query()->where('vision_blueprint_id', $blueprint->id)->orderBy('id', 'asc')->limit(20)->get()
            : collect();

        $whiteLabel = is_array($posProject?->white_label_branding_json) ? $posProject->white_label_branding_json : [];
        if ($request->filled('agency_name')) {
            $whiteLabel = [
                'enabled' => true,
                'agency_name' => trim((string) $request->query('agency_name')),
                'agency_email' => trim((string) $request->query('agency_email', '')),
                'pic_name' => trim((string) $request->query('pic_name', '')),
                'pic_title' => trim((string) $request->query('pic_title', '')),
            ];
            if ($posProject && Schema::hasColumn('pos_projects', 'white_label_branding_json')) {
                $posProject->forceFill(['white_label_branding_json' => $whiteLabel])->saveQuietly();
            }
        }

        $activeLocale = $request->query('locale')
            ?: ($request->cookie('neriah_locale')
            ?: (session('locale') ?: ($blueprint->user_metadata['output_locale'] ?? app()->getLocale())));
        $locale = in_array(strtolower((string) $activeLocale), ['en', 'id'], true) ? strtolower((string) $activeLocale) : 'id';
        $isEn = ($locale === 'en');

        $viewData = [
            'docType' => $docType,
            'blueprint' => $blueprint,
            'posProject' => $posProject,
            'projectName' => $blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Enterprise Project'),
            'schema' => $schema,
            'quotation' => $quotation,
            'uat' => $uat,
            'changeRequests' => $changeRequests,
            'devInfo' => ContractLegalHelper::getDeveloperInfo(),
            'whiteLabel' => $whiteLabel,
            'isPdfStream' => $request->query('download') === 'pdf',
            'locale' => $locale,
            'isEn' => $isEn,
        ];

        if ($request->query('download') === 'pdf' && class_exists(Pdf::class)) {
            $pdf = Pdf::loadView('prints.mitra-commercial-pack', $viewData);

            return $pdf->stream("{$docType}-{$blueprint->slug}.pdf");
        }

        return view('prints.mitra-commercial-pack', $viewData);
    }

    /**
     * Return live multi-currency exchange rates and supported currencies.
     */
    public function currencyRates(): JsonResponse
    {
        return response()->json(array_merge(['success' => true], CurrencyExchangeService::getAllRates()));
    }
}
