<?php

namespace App\Http\Controllers;

use App\Models\VisionBlueprint;
use App\Models\BlueprintVoucher;
use App\Models\Document;
use App\Services\MidtransSnapService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class CartController extends Controller
{
    /**
     * Ghost Hold Expiration Threshold (in hours).
     * Cart slot reservations expire after 24 hours to prevent ghost holding capacity.
     */
    const RESERVATION_HOURS = 24;

    /**
     * Display the shopping cart / project checkout board.
     */
    public function index(Request $request): View
    {
        $cart = session()->get('neriah_cart', []);
        $items = [];
        $totalContract = 0;
        $totalDp = 0;
        $minRemainingSeconds = null;

        $hasExpired = false;

        foreach ($cart as $slug => $item) {
            $expiresAt = isset($item['expires_at']) ? Carbon::parse($item['expires_at']) : Carbon::parse($item['added_at'])->addHours(self::RESERVATION_HOURS);
            
            // Check Ghost Hold expiration
            if (Carbon::now()->greaterThanOrEqualTo($expiresAt)) {
                unset($cart[$slug]);
                $hasExpired = true;
                continue;
            }

            $remainingSeconds = max(0, Carbon::now()->diffInSeconds($expiresAt, false));
            if ($minRemainingSeconds === null || $remainingSeconds < $minRemainingSeconds) {
                $minRemainingSeconds = $remainingSeconds;
            }

            $blueprint = VisionBlueprint::where('slug', $slug)->first();
            if (!$blueprint || $blueprint->signed_agreement || $blueprint->project_status === 'In Development (DP Paid)') {
                unset($cart[$slug]);
                $hasExpired = true;
                continue;
            }

            if ($blueprint) {
                // Ensure PRD with itemized estimation is populated
                if (empty($blueprint->prd_content) || !isset($blueprint->prd_content['itemized_cost_breakdown'])) {
                    $blueprint->generateAndSavePrd();
                    $blueprint->refresh();
                }

                $tier = $item['tier'] ?? 'standard';
                $resolvedTier = \App\Services\PrdGeneratorService::resolveVelocityTier($blueprint, $tier);

                $contractAmount = (float) ($resolvedTier['contract_amount'] ?? ($item['contract_amount'] ?? 25000000));
                $dpAmount = (float) ($resolvedTier['dp_amount'] ?? ($item['dp_amount'] ?? ($contractAmount * 0.50)));
                $tierName = $resolvedTier['name'] ?? ($item['tier_name'] ?? 'Standard Velocity (30 Hari)');
                $targetWaktu = $resolvedTier['duration'] ?? ($item['target_waktu'] ?? ($blueprint->target_waktu ?: '30 Hari Kerja'));
                $itemizedItems = $item['itemized_items'] ?? ($blueprint->prd_content['itemized_cost_breakdown']['items'] ?? []);

                // Auto-sync / heal cart session if amounts were previously mismatched
                if ($contractAmount !== (float)($item['contract_amount'] ?? 0) || $dpAmount !== (float)($item['dp_amount'] ?? 0)) {
                    $cart[$slug]['contract_amount'] = $contractAmount;
                    $cart[$slug]['dp_amount'] = $dpAmount;
                    $cart[$slug]['tier_name'] = $tierName;
                    $cart[$slug]['target_waktu'] = $targetWaktu;
                    session()->put('neriah_cart', $cart);
                }

                $items[] = [
                    'blueprint' => $blueprint,
                    'slug' => $blueprint->slug,
                    'title' => $blueprint->nama_bisnis ?: $blueprint->client_name,
                    'client_name' => $blueprint->client_name,
                    'email' => $blueprint->email,
                    'tier' => $tier,
                    'tier_name' => $tierName,
                    'target_waktu' => $targetWaktu,
                    'contract_amount' => $contractAmount,
                    'dp_amount' => $dpAmount,
                    'itemized_items' => $itemizedItems,
                    'added_at' => $item['added_at'] ?? now()->toIso8601String(),
                    'expires_at' => $expiresAt->toIso8601String(),
                    'remaining_seconds' => $remainingSeconds,
                ];

                $totalContract += $contractAmount;
                $totalDp += $dpAmount;
            }
        }

        if (empty($cart)) {
            session()->forget(['neriah_cart', 'neriah_cart_voucher']);
        } elseif ($hasExpired) {
            session()->put('neriah_cart', $cart);
            session()->flash('warning', 'Satu atau lebih slot reservasi proyek telah diperbarui atau dilepas secara otomatis.');
        }

        // Voucher Calculation for Cart
        $cartVoucher = session()->get('neriah_cart_voucher');
        $voucherData = null;
        $discountAmount = 0;

        if ($cartVoucher && !empty($cartVoucher['code'])) {
            $voucherModel = BlueprintVoucher::where('code', $cartVoucher['code'])->first();
            if ($voucherModel && $voucherModel->isValid()) {
                if ($voucherModel->discount_type === 'free_bypass' || ($voucherModel->discount_type === 'percent' && (float)$voucherModel->discount_value >= 100)) {
                    $discountAmount = $totalContract;
                } elseif ($voucherModel->discount_type === 'percent') {
                    $discountAmount = round($totalContract * ((float)$voucherModel->discount_value / 100));
                } elseif ($voucherModel->discount_type === 'fixed') {
                    $discountAmount = min($totalContract, (float)$voucherModel->discount_value);
                }
                $voucherData = [
                    'code' => $voucherModel->code,
                    'discount_type' => $voucherModel->discount_type,
                    'discount_value' => (float) $voucherModel->discount_value,
                    'description' => $voucherModel->description,
                    'is_free_bypass' => $voucherModel->discount_type === 'free_bypass' || ($voucherModel->discount_type === 'percent' && (float)$voucherModel->discount_value >= 100),
                ];
            } else {
                session()->forget('neriah_cart_voucher');
            }
        }

        $finalTotalContract = max(0, $totalContract - $discountAmount);
        $finalTotalDp = (int) round($finalTotalContract * 0.50);

        return view('cart.index', [
            'items' => $items,
            'totalContract' => $totalContract,
            'totalDp' => $totalDp,
            'voucher' => $voucherData,
            'discountAmount' => $discountAmount,
            'finalTotalContract' => $finalTotalContract,
            'finalTotalDp' => $finalTotalDp,
            'minRemainingSeconds' => $minRemainingSeconds ?? (self::RESERVATION_HOURS * 3600),
        ]);
    }

    /**
     * Add a vision blueprint / project spec to cart with selected velocity tier.
     */
    public function add(Request $request, string $slug): RedirectResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();

        // Ensure PRD with itemized estimation is populated
        if (empty($blueprint->prd_content) || !isset($blueprint->prd_content['itemized_cost_breakdown'])) {
            $blueprint->generateAndSavePrd();
            $blueprint->refresh();
        }

        $tier = $request->input('tier', 'standard');
        $matchedTier = \App\Services\PrdGeneratorService::resolveVelocityTier($blueprint, $tier);
        $itemizedData = $blueprint->prd_content['itemized_cost_breakdown'] ?? \App\Services\PrdGeneratorService::calculateItemizedEstimation($blueprint);

        $contractAmount = (float) $matchedTier['contract_amount'];
        $dpAmount = (float) $matchedTier['dp_amount'];
        $targetWaktu = $matchedTier['duration'] ?? '30 Hari Kerja';
        $tierName = $matchedTier['name'] ?? 'Standard Velocity (30 Hari Kerja)';

        // Allow explicit amount overrides if passed safely
        if ($request->filled('contract_amount')) {
            $contractAmount = (float) $request->input('contract_amount');
            $dpAmount = $contractAmount * 0.50;
        }

        $cart = session()->get('neriah_cart', []);

        $addedAt = Carbon::now();
        $expiresAt = $addedAt->copy()->addHours(self::RESERVATION_HOURS);

        $cart[$slug] = [
            'slug' => $slug,
            'nama_bisnis' => $blueprint->nama_bisnis ?: $blueprint->client_name,
            'tier' => $tier,
            'tier_name' => $tierName,
            'target_waktu' => $targetWaktu,
            'contract_amount' => $contractAmount,
            'dp_amount' => $dpAmount,
            'itemized_items' => $itemizedData['items'] ?? [],
            'added_at' => $addedAt->toIso8601String(),
            'expires_at' => $expiresAt->toIso8601String(),
        ];

        session()->put('neriah_cart', $cart);

        // If voucher provided from blueprint, apply to cart
        if ($request->filled('voucher')) {
            $vCode = strtoupper(trim((string) $request->input('voucher')));
            if (!empty($vCode)) {
                $vModel = BlueprintVoucher::where('code', $vCode)->first();
                if ($vModel && $vModel->isValid()) {
                    session()->put('neriah_cart_voucher', [
                        'code' => $vModel->code,
                        'discount_type' => $vModel->discount_type,
                        'discount_value' => (float) $vModel->discount_value,
                        'description' => $vModel->description,
                    ]);
                }
            }
        }

        return redirect()->route('cart.index')->with('success', 'Paket "' . $tierName . '" untuk proyek "' . ($blueprint->nama_bisnis ?: $blueprint->client_name) . '" berhasil dimasukkan ke Cart! Slot pengerjaan diamankan selama 24 jam.');
    }

    /**
     * API to fetch current active cart state for Global Navigation & islands.
     */
    public function apiCart(Request $request): JsonResponse
    {
        $cart = session()->get('neriah_cart', []);
        $items = [];
        $minRemainingSeconds = null;

        foreach ($cart as $slug => $item) {
            $expiresAt = isset($item['expires_at']) ? Carbon::parse($item['expires_at']) : Carbon::parse($item['added_at'])->addHours(self::RESERVATION_HOURS);
            
            if (Carbon::now()->greaterThanOrEqualTo($expiresAt)) {
                unset($cart[$slug]);
                continue;
            }

            $remainingSeconds = max(0, Carbon::now()->diffInSeconds($expiresAt, false));
            if ($minRemainingSeconds === null || $remainingSeconds < $minRemainingSeconds) {
                $minRemainingSeconds = $remainingSeconds;
            }

            $items[] = [
                'slug' => $slug,
                'title' => $item['nama_bisnis'] ?? $slug,
                'tier' => $item['tier'] ?? 'standard',
                'tier_name' => $item['tier_name'] ?? 'Standard Velocity',
                'target_waktu' => $item['target_waktu'] ?? '30 Hari',
                'contract_amount' => (float) ($item['contract_amount'] ?? 50000000),
                'dp_amount' => (float) ($item['dp_amount'] ?? 25000000),
                'expires_at' => $expiresAt->toIso8601String(),
                'remaining_seconds' => $remainingSeconds,
            ];
        }

        session()->put('neriah_cart', $cart);

        return response()->json([
            'count' => count($items),
            'items' => $items,
            'min_remaining_seconds' => $minRemainingSeconds,
            'reservation_hours' => self::RESERVATION_HOURS,
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function remove(Request $request, string $slug): RedirectResponse
    {
        $cart = session()->get('neriah_cart', []);

        if (isset($cart[$slug])) {
            unset($cart[$slug]);
            session()->put('neriah_cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Item berhasil dihapus dari Cart.');
    }

    /**
     * Clear all items in cart.
     */
    public function clear(): RedirectResponse
    {
        session()->forget('neriah_cart');
        session()->forget('neriah_cart_voucher');
        return redirect()->route('cart.index')->with('success', 'Cart berhasil dikosongkan.');
    }

    /**
     * Apply promo/subsidy voucher to active cart.
     */
    public function applyVoucher(Request $request): RedirectResponse
    {
        $code = strtoupper(trim((string) $request->input('voucher_code', '')));
        if (empty($code)) {
            return redirect()->route('cart.index')->with('warning', 'Silakan masukkan kode voucher terlebih dahulu.');
        }

        $voucher = BlueprintVoucher::where('code', $code)->first();
        if (!$voucher || !$voucher->isValid()) {
            return redirect()->route('cart.index')->with('warning', 'Kode voucher "' . $code . '" tidak valid, kuota telah habis, atau sudah kedaluwarsa.');
        }

        session()->put('neriah_cart_voucher', [
            'code' => $voucher->code,
            'discount_type' => $voucher->discount_type,
            'discount_value' => (float) $voucher->discount_value,
            'description' => $voucher->description,
        ]);

        return redirect()->route('cart.index')->with('success', 'Voucher "' . $voucher->code . '" berhasil diterapkan! Total tagihan telah disesuaikan.');
    }

    /**
     * Remove voucher from active cart.
     */
    public function removeVoucher(): RedirectResponse
    {
        session()->forget('neriah_cart_voucher');
        return redirect()->route('cart.index')->with('success', 'Voucher berhasil dilepas dari Cart.');
    }

    /**
     * Get Midtrans Snap Token for all items in Cart (Termin DP 50%).
     */
    public function getSnapToken(Request $request): JsonResponse
    {
        $cart = session()->get('neriah_cart', []);
        
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart belanja kosong. Silakan tambahkan proyek terlebih dahulu.',
            ], 400);
        }

        $totalContract = 0;
        $totalDp = 0;
        $firstClientName = null;
        $firstEmail = null;
        $firstPhone = null;

        foreach ($cart as $slug => $item) {
            $blueprint = VisionBlueprint::where('slug', $slug)->first();
            $resolvedTier = $blueprint ? \App\Services\PrdGeneratorService::resolveVelocityTier($blueprint, $item['tier'] ?? 'standard') : null;

            $contractAmount = $resolvedTier ? (float) $resolvedTier['contract_amount'] : (float) ($item['contract_amount'] ?? 50000000);
            $dpAmount = $resolvedTier ? (int) $resolvedTier['dp_amount'] : (int) ($item['dp_amount'] ?? ($contractAmount * 0.50));
            $totalContract += $contractAmount;
            $totalDp += $dpAmount;

            if ($blueprint && !$firstClientName) {
                $firstClientName = $blueprint->client_name ?: $blueprint->nama_bisnis;
                $firstEmail = $blueprint->email;
                $firstPhone = $blueprint->phone;
            }
        }

        // Voucher Calculation for Cart Snap Token
        $cartVoucher = session()->get('neriah_cart_voucher');
        $appliedVoucher = null;
        $discountAmount = 0;

        if ($cartVoucher && !empty($cartVoucher['code'])) {
            $vModel = BlueprintVoucher::where('code', $cartVoucher['code'])->first();
            if ($vModel && $vModel->isValid()) {
                $appliedVoucher = $vModel;
                if ($vModel->discount_type === 'percent') {
                    $discountAmount = round($totalContract * ((float)$vModel->discount_value / 100));
                } elseif ($vModel->discount_type === 'fixed') {
                    $discountAmount = min($totalContract, (float)$vModel->discount_value);
                }
            }
        }

        $finalContract = max(0, $totalContract - $discountAmount);
        $finalDp = (int) round($finalContract * 0.50);

        if ($finalDp <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Total tagihan bernilai Rp 0 (Free Grant). Silakan hubungi admin atau gunakan aktivasi voucher langsung pada halaman proposal.',
            ], 400);
        }

        $orderId = 'NP-CART-' . strtoupper(Str::random(6)) . '-' . time();

        $itemDetails = [
            [
                'id' => 'CART-DP',
                'price' => (int) $finalDp,
                'quantity' => 1,
                'name' => substr('DP (' . count($cart) . ' Proyek)' . ($appliedVoucher ? ' (Disc ' . $appliedVoucher->code . ')' : ''), 0, 50),
            ]
        ];

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $finalDp,
            ],
            'customer_details' => [
                'first_name' => $firstClientName ?: 'Client Neriah Pro',
                'email' => $firstEmail ?: 'client@neriahpro.com',
                'phone' => $firstPhone ?: '08123456789',
            ],
            'item_details' => $itemDetails,
        ];

        $snapResponse = MidtransSnapService::createSnapToken($params);

        if (!$snapResponse['success']) {
            return response()->json([
                'success' => false,
                'message' => $snapResponse['error'] ?? 'Gagal membuat sesi transaksi Midtrans.',
            ], $snapResponse['status_code'] ?? 500);
        }

        // Persist Cart item linkages to Document model and Cache for Webhook settlement
        foreach ($cart as $cSlug => $item) {
            $bp = VisionBlueprint::where('slug', $cSlug)->first();
            if ($bp) {
                if ($appliedVoucher) {
                    $bp->update(['voucher_code' => $appliedVoucher->code]);
                }
                Document::updateOrCreate(
                    [
                        'related_type' => VisionBlueprint::class,
                        'related_id' => $bp->id,
                        'document_type' => 'contract',
                    ],
                    [
                        'title' => 'Perjanjian Kerja Sama - ' . ($bp->nama_bisnis ?: $bp->client_name),
                        'status' => 'pending_signature',
                        'scope_locked' => false,
                        'contract_amount' => (float) ($item['contract_amount'] ?? 50000000),
                        'dp_amount' => (float) ($item['dp_amount'] ?? 25000000),
                        'midtrans_order_id' => $orderId,
                        'signer_name' => $firstClientName ?: ($bp->nama_bisnis ?: $bp->client_name),
                        'signer_email' => $firstEmail ?: $bp->email,
                        'document_hash' => $bp->document_sha256 ?: $bp->calculatePrdHash(),
                    ]
                );
            }
        }

        // Cache cart order mapping for fail-safe webhook fulfillment
        Cache::put('cart_order_' . $orderId, [
            'slugs' => array_keys($cart),
            'voucher_code' => $appliedVoucher?->code,
        ], now()->addDays(7));

        return response()->json([
            'success' => true,
            'token' => $snapResponse['token'],
            'redirect_url' => $snapResponse['redirect_url'],
            'order_id' => $orderId,
            'gross_amount' => $finalDp,
            'client_key' => $snapResponse['client_key'] ?? config('midtrans.client_key'),
        ]);
    }

    /**
     * Claim 100% Free Bypass voucher for all blueprints in cart.
     */
    public function claimFreeGrant(Request $request): RedirectResponse
    {
        $cart = session()->get('neriah_cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('warning', 'Cart Anda kosong.');
        }

        $cartVoucher = session()->get('neriah_cart_voucher');
        if (!$cartVoucher || empty($cartVoucher['code'])) {
            return redirect()->route('cart.index')->with('warning', 'Tidak ada voucher yang diterapkan pada cart.');
        }

        $voucher = BlueprintVoucher::where('code', $cartVoucher['code'])->first();
        if (!$voucher || !$voucher->isValid()) {
            return redirect()->route('cart.index')->with('warning', 'Kode voucher tidak valid atau sudah kedaluwarsa.');
        }

        // Calculate total contract
        $totalContract = 0;
        foreach ($cart as $item) {
            $totalContract += (float) ($item['contract_amount'] ?? 25000000);
        }

        $discountAmount = 0;
        if ($voucher->discount_type === 'free_bypass' || ($voucher->discount_type === 'percent' && (float)$voucher->discount_value >= 100)) {
            $discountAmount = $totalContract;
        } elseif ($voucher->discount_type === 'percent') {
            $discountAmount = round($totalContract * ((float)$voucher->discount_value / 100));
        } elseif ($voucher->discount_type === 'fixed') {
            $discountAmount = min($totalContract, (float)$voucher->discount_value);
        }

        $finalContract = max(0, $totalContract - $discountAmount);
        if ($finalContract > 0) {
            return redirect()->route('cart.index')->with('warning', 'Voucher ini tidak mencakup 100% gratis. Silakan gunakan tombol pembayaran DP via Midtrans Snap.');
        }

        // Consume voucher quota once
        $voucher->incrementUsage();

        foreach ($cart as $slug => $item) {
            $bp = VisionBlueprint::where('slug', $slug)->first();
            if ($bp) {
                $bp->update([
                    'voucher_code' => $voucher->code,
                    'is_free_grant' => true,
                    'is_published' => true,
                    'project_status' => 'In Development (Free Grant)',
                ]);
                $bp->provisionStagingUrl();
                $bp->recordSignOff($request->ip(), $request->userAgent());

                Document::updateOrCreate(
                    [
                        'related_type' => VisionBlueprint::class,
                        'related_id' => $bp->id,
                        'document_type' => 'contract',
                    ],
                    [
                        'title' => 'Perjanjian Kerja Sama - ' . ($bp->nama_bisnis ?: $bp->client_name),
                        'status' => 'signed',
                        'scope_locked' => true,
                        'contract_amount' => 0.00,
                        'dp_amount' => 0.00,
                        'signed_at' => now(),
                        'signer_name' => $bp->client_name ?: $bp->nama_bisnis,
                        'signer_email' => $bp->email,
                        'signer_ip_address' => $request->ip(),
                        'document_hash' => $bp->document_sha256 ?: $bp->calculatePrdHash(),
                    ]
                );
            }
        }

        session()->forget(['neriah_cart', 'neriah_cart_voucher']);

        return redirect()->route('cart.index')->with('success', 'Seluruh proyek dalam Cart berhasil diklaim dengan Voucher Pelayanan 100% (Free Grant) dan resmi masuk ke tahap pengembangan!');
    }
}
