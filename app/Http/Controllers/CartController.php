<?php

namespace App\Http\Controllers;

use App\Models\VisionBlueprint;
use App\Models\Document;
use App\Services\MidtransSnapService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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
            if ($blueprint) {
                $contractAmount = (float) ($item['contract_amount'] ?? 50000000);
                $dpAmount = (float) ($item['dp_amount'] ?? ($contractAmount * 0.50));
                $tier = $item['tier'] ?? 'standard';
                $tierName = $item['tier_name'] ?? 'Standard Velocity (30 Hari)';
                $targetWaktu = $item['target_waktu'] ?? ($blueprint->target_waktu ?: '30 Hari Kerja');

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
                    'added_at' => $item['added_at'] ?? now()->toIso8601String(),
                    'expires_at' => $expiresAt->toIso8601String(),
                    'remaining_seconds' => $remainingSeconds,
                ];

                $totalContract += $contractAmount;
                $totalDp += $dpAmount;
            }
        }

        if ($hasExpired) {
            session()->put('neriah_cart', $cart);
            session()->flash('warning', 'Satu atau lebih slot reservasi proyek telah kadaluwarsa dan dilepas secara otomatis untuk mencegah Ghost Hold.');
        }

        return view('cart.index', [
            'items' => $items,
            'totalContract' => $totalContract,
            'totalDp' => $totalDp,
            'minRemainingSeconds' => $minRemainingSeconds ?? (self::RESERVATION_HOURS * 3600),
        ]);
    }

    /**
     * Add a vision blueprint / project spec to cart with selected velocity tier.
     */
    public function add(Request $request, string $slug): RedirectResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();

        $tier = $request->input('tier', 'standard');
        
        // Define pricing and duration matrix based on accelerator tier
        if ($tier === 'hyper_sprint') {
            $contractAmount = 100000000.00;
            $dpAmount = 50000000.00;
            $targetWaktu = '7 Hari Kerja (Hyper-Sprint 24/7)';
            $tierName = 'Hyper-Sprint Emergency (7 Hari + 24/7 Gemini Ultra Squad)';
        } elseif ($tier === 'fast_track') {
            $contractAmount = 75000000.00;
            $dpAmount = 37500000.00;
            $targetWaktu = '14 Hari Kerja (Fast-Track 2x)';
            $tierName = 'Fast-Track Accelerator (14 Hari + Gemini Ultra Reasoning)';
        } else {
            $contractAmount = 50000000.00;
            $dpAmount = 25000000.00;
            $targetWaktu = '30 Hari Kerja (Standard)';
            $tierName = 'Standard Velocity (30 Hari Kerja)';
        }

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
            'added_at' => $addedAt->toIso8601String(),
            'expires_at' => $expiresAt->toIso8601String(),
        ];

        session()->put('neriah_cart', $cart);

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
        return redirect()->route('cart.index')->with('success', 'Cart berhasil dikosongkan.');
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

        $totalDp = 0;
        $itemDetails = [];
        $firstClientName = null;
        $firstEmail = null;
        $firstPhone = null;

        foreach ($cart as $slug => $item) {
            $contractAmount = (float) ($item['contract_amount'] ?? 50000000);
            $dpAmount = (int) ($item['dp_amount'] ?? ($contractAmount * 0.50));
            $totalDp += $dpAmount;

            $blueprint = VisionBlueprint::where('slug', $slug)->first();
            if ($blueprint && !$firstClientName) {
                $firstClientName = $blueprint->client_name ?: $blueprint->nama_bisnis;
                $firstEmail = $blueprint->email;
                $firstPhone = $blueprint->phone;
            }

            $itemDetails[] = [
                'id' => substr('CART-' . strtoupper(Str::slug($slug)), 0, 50),
                'price' => $dpAmount,
                'quantity' => 1,
                'name' => substr('DP: ' . ($item['nama_bisnis'] ?? $slug), 0, 50),
            ];
        }

        if ($totalDp <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Total tagihan DP tidak valid.',
            ], 400);
        }

        $orderId = 'NP-CART-' . strtoupper(Str::random(6)) . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $totalDp,
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

        return response()->json([
            'success' => true,
            'token' => $snapResponse['token'],
            'redirect_url' => $snapResponse['redirect_url'],
            'order_id' => $orderId,
            'gross_amount' => $totalDp,
            'client_key' => $snapResponse['client_key'] ?? config('midtrans.client_key'),
        ]);
    }
}
