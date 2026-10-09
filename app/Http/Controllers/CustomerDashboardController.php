<?php

namespace App\Http\Controllers;

use App\Models\CmsGlobalSetting;
use App\Models\Transaction;
use App\Models\VisionBlueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    /**
     * Display the centralized Customer & Client Project Dashboard.
     */
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        if (! Auth::check()) {
            session(['url.intended' => route('customer.dashboard')]);
            return redirect()->route('customer.login');
        }

        $user = Auth::user();
        if (! $user) {
            session(['url.intended' => route('customer.dashboard')]);
            return redirect()->route('customer.login');
        }

        $email = strtolower(trim($user->email ?? ''));
        $userId = $user->id;
        $globalSettings = CmsGlobalSetting::getAllCached();
        $isPgsql = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql';

        // Retrieve blueprints owned by or registered to this customer
        $blueprints = VisionBlueprint::where(function ($q) use ($email, $userId) {
            if ($email) {
                $q->whereRaw('LOWER(email) = ?', [$email]);
            }
            if ($userId) {
                $q->orWhere('user_metadata->user_id', $userId);
            }
        })->orderBy('created_at', 'desc')->get();

        // Retrieve payment transactions (resilient across PostgreSQL & MySQL)
        $transactions = Transaction::where(function ($q) use ($email, $userId, $isPgsql) {
            if ($userId) {
                $q->where('user_id', $userId);
            }
            if ($email) {
                $q->orWhere(function ($sub) use ($email, $isPgsql) {
                    $sub->whereNotNull('customer_details');
                    if ($isPgsql) {
                        $sub->whereRaw('LOWER("customer_details"::text) LIKE ?', ['%' . $email . '%']);
                    } else {
                        $sub->whereRaw('LOWER(customer_details) LIKE ?', ['%' . $email . '%']);
                    }
                });
            }
        })->orderBy('created_at', 'desc')->get();

        // Auto-reconcile / self-heal pending transactions with Midtrans on page load
        $pendingToSync = $transactions->where('status', 'pending')->take(2);
        $reSyncNeeded = false;

        foreach ($pendingToSync as $pTx) {
            $orderId = $pTx->midtrans_order_id;
            if (empty($orderId)) continue;

            try {
                $statusCheck = \App\Services\MidtransSnapService::checkStatus($orderId);
                if ($statusCheck['success']) {
                    $data = $statusCheck['data'] ?? [];
                    $trxStatus = $data['transaction_status'] ?? null;
                    $fraudStatus = $data['fraud_status'] ?? null;

                    if (in_array($trxStatus, ['settlement', 'capture']) && ($fraudStatus !== 'challenge')) {
                        $pTx->update([
                            'status' => 'settlement',
                            'midtrans_transaction_id' => $data['transaction_id'] ?? $pTx->midtrans_transaction_id,
                            'user_id' => $user->id,
                        ]);
                        $dummyReq = Request::create('/customer/transaction/' . $pTx->id . '/sync', 'POST', [
                            'gross_amount' => $data['gross_amount'] ?? $pTx->total_idr,
                            'transaction_id' => $data['transaction_id'] ?? $pTx->midtrans_transaction_id,
                        ]);
                        app(\App\Http\Controllers\MidtransWebhookController::class)->fulfillOrder($orderId, $dummyReq);
                        $reSyncNeeded = true;
                    } elseif (in_array($trxStatus, ['expire', 'cancel', 'deny'])) {
                        $pTx->update([
                            'status' => $trxStatus,
                            'midtrans_transaction_id' => $data['transaction_id'] ?? $pTx->midtrans_transaction_id,
                        ]);
                        $reSyncNeeded = true;
                    }
                }
            } catch (\Throwable $e) {
                // Silently continue if gateway check fails
            }
        }

        // If any transaction status was reconciled during auto-check, refresh collections
        if ($reSyncNeeded) {
            $blueprints = VisionBlueprint::where(function ($q) use ($email, $userId) {
                if ($email) {
                    $q->whereRaw('LOWER(email) = ?', [$email]);
                }
                if ($userId) {
                    $q->orWhere('user_metadata->user_id', $userId);
                }
            })->orderBy('created_at', 'desc')->get();

            $transactions = Transaction::where(function ($q) use ($email, $userId, $isPgsql) {
                if ($userId) {
                    $q->where('user_id', $userId);
                }
                if ($email) {
                    $q->orWhere(function ($sub) use ($email, $isPgsql) {
                        $sub->whereNotNull('customer_details');
                        if ($isPgsql) {
                            $sub->whereRaw('LOWER("customer_details"::text) LIKE ?', ['%' . $email . '%']);
                        } else {
                            $sub->whereRaw('LOWER(customer_details) LIKE ?', ['%' . $email . '%']);
                        }
                    });
                }
            })->orderBy('created_at', 'desc')->get();
        }

        // Retrieve linked domain & hosting assets if any (PostgreSQL strictly uses 'expires_at')
        $blueprintIds = $blueprints->pluck('id')->filter()->toArray();
        $hostingAssets = !empty($blueprintIds) 
            ? \App\Models\DomainHostingAsset::whereIn('vision_blueprint_id', $blueprintIds)->orderBy('expires_at', 'asc')->get()
            : collect();

        // Categorize into Retail Self-Service Licenses vs Studio Custom Projects via Concrete Registry
        $selfServiceIds = \App\Support\PricingRegistry::getSelfServiceIds();

        $retailLicenses = $blueprints->filter(function ($bp) use ($selfServiceIds) {
            $tier = $bp->user_metadata['retail_tier'] ?? $bp->user_metadata['package_tier'] ?? null;
            return in_array($tier, $selfServiceIds, true)
                || in_array($bp->project_status, ['Retail License', 'Self-Service', 'Instant Blueprint'], true);
        });

        $studioProjects = $blueprints->reject(function ($bp) use ($selfServiceIds) {
            $tier = $bp->user_metadata['retail_tier'] ?? $bp->user_metadata['package_tier'] ?? null;
            return in_array($tier, $selfServiceIds, true)
                || in_array($bp->project_status, ['Retail License', 'Self-Service', 'Instant Blueprint'], true);
        });

        // Filter active pending orders so customer can resume or verify payment
        $pendingRetailOrders = $transactions->filter(function ($tx) {
            if ($tx->status !== 'pending') return false;
            $orderId = $tx->midtrans_order_id ?? '';
            return str_starts_with($orderId, 'NPRO-LC-')
                || str_starts_with($orderId, 'NPRO-LIC-')
                || ($tx->customer_details['is_retail'] ?? false)
                || in_array($tx->customer_details['package_tier'] ?? '', \App\Support\PricingRegistry::getSelfServiceIds(), true);
        });

        // Filter expired / cancelled / denied retail orders for transparent status explanation
        $expiredRetailOrders = $transactions->filter(function ($tx) {
            if (!in_array($tx->status, ['expire', 'cancel', 'deny'], true)) return false;
            $orderId = $tx->midtrans_order_id ?? '';
            return str_starts_with($orderId, 'NPRO-LC-')
                || str_starts_with($orderId, 'NPRO-LIC-')
                || ($tx->customer_details['is_retail'] ?? false)
                || in_array($tx->customer_details['package_tier'] ?? '', \App\Support\PricingRegistry::getSelfServiceIds(), true);
        });

        $pendingStudioOrders = $transactions->filter(function ($tx) {
            if ($tx->status !== 'pending') return false;
            $orderId = $tx->midtrans_order_id ?? '';
            return str_starts_with($orderId, 'NPRO-DP-')
                || str_starts_with($orderId, 'NP-BP-')
                || str_starts_with($orderId, 'NP-CART-')
                || str_starts_with($orderId, 'NPRO-PK-')
                || str_starts_with($orderId, 'NPRO-PKG-')
                || ($tx->customer_details['type'] ?? '') === 'blueprint_dp';
        });

        // Compute active metrics
        $totalBlueprints = $blueprints->count();
        $totalRetail = $retailLicenses->count();
        $totalStudio = $studioProjects->count();
        $activeStudio = $studioProjects->filter(fn ($p) => $p->isDpConfirmed() || $p->signed_agreement)->count();
        $latestTransaction = $transactions->first();

        // Resolve Google Translate settings
        $googleTranslateEnabled = (bool) CmsGlobalSetting::getVal('google_translate_enabled', true);
        $rawAllowed = CmsGlobalSetting::getVal('google_translate_allowed_languages', ['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es']);
        $allowedLangList = is_array($rawAllowed) ? $rawAllowed : (is_string($rawAllowed) ? json_decode($rawAllowed, true) : ['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es']);
        if (empty($allowedLangList)) {
            $allowedLangList = ['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es'];
        }

        // Resolve prefilled profile from blueprints or transactions if user profile columns are empty
        $prefilledCompany = $user->company_name 
            ?? ($blueprints->first()->nama_bisnis ?? ($transactions->first()?->customer_details['company_name'] ?? ''));
        $prefilledPhone = $user->phone 
            ?? ($blueprints->first()->phone ?? '');
        $prefilledCountryCode = $user->phone_country_code ?? '+62';

        return view('customer.dashboard', [
            'user' => $user,
            'blueprints' => $blueprints,
            'retailLicenses' => $retailLicenses,
            'studioProjects' => $studioProjects,
            'pendingRetailOrders' => $pendingRetailOrders,
            'expiredRetailOrders' => $expiredRetailOrders,
            'pendingStudioOrders' => $pendingStudioOrders,
            'transactions' => $transactions,
            'latestTransaction' => $latestTransaction,
            'hostingAssets' => $hostingAssets,
            'globalSettings' => $globalSettings,
            'countryZones' => config('country_zones.zones', []),
            'defaultCountryCode' => config('country_zones.default', 'ID'),
            'googleTranslateEnabled' => $googleTranslateEnabled,
            'allowedLangList' => $allowedLangList,
            'profileDefaults' => [
                'name' => $user->name,
                'email' => $user->email,
                'company_name' => $prefilledCompany,
                'phone_country_code' => $prefilledCountryCode,
                'phone' => $prefilledPhone,
                'npwp' => $user->npwp ?? '',
                'billing_address' => $user->billing_address ?? '',
                'billing_city' => $user->billing_city ?? '',
                'billing_province' => $user->billing_province ?? '',
                'billing_postal_code' => $user->billing_postal_code ?? '',
                'notification_preferences' => $user->notification_preferences ?? [
                    'email_sprints' => true,
                    'wa_billing' => true,
                ],
            ],
            'metrics' => [
                'total_blueprints' => $totalBlueprints,
                'total_retail' => $totalRetail,
                'total_studio' => $totalStudio,
                'active_studio' => $activeStudio,
                'total_spend_idr' => (float) $transactions->whereIn('status', ['settlement', 'capture', 'success'])->sum('total_idr'),
            ],
        ]);
    }

    /**
     * Check transaction status with Midtrans API and self-heal / fulfill if settled.
     */
    public function syncTransaction(Request $request, string $id): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('customer.login');
        }

        $email = strtolower(trim($user->email ?? ''));
        $tx = Transaction::where('id', $id)
            ->where(function ($q) use ($user, $email) {
                $q->where('user_id', $user->id);
                if ($email) {
                    $q->orWhere(function ($sub) use ($email) {
                        $sub->whereNotNull('customer_details')
                            ->where('customer_details', 'LIKE', '%"' . $email . '"%');
                    });
                }
            })->firstOrFail();

        // 1. Sandbox simulation bypass for developer / sandbox testing
        if (!config('midtrans.is_production', false) && ($request->boolean('simulate') || $request->input('action') === 'simulate')) {
            $tx->update([
                'status' => 'settlement',
                'user_id' => $user->id,
            ]);
            $request->merge([
                'gross_amount' => $tx->total_idr,
                'transaction_id' => 'SIM-' . time(),
            ]);
            app(\App\Http\Controllers\MidtransWebhookController::class)->fulfillOrder($tx->midtrans_order_id, $request);

            return redirect()->route('customer.dashboard')->with('success', 'Simulasi pembayaran Sandbox berhasil! Lisensi digital telah resmi diaktifkan.');
        }

        // 2. Query Midtrans API directly for live status
        $statusCheck = \App\Services\MidtransSnapService::checkStatus($tx->midtrans_order_id);

        if ($statusCheck['success']) {
            $data = $statusCheck['data'] ?? [];
            $trxStatus = $data['transaction_status'] ?? null;
            $fraudStatus = $data['fraud_status'] ?? null;

            if (in_array($trxStatus, ['settlement', 'capture']) && ($fraudStatus !== 'challenge')) {
                $tx->update([
                    'status' => 'settlement',
                    'midtrans_transaction_id' => $data['transaction_id'] ?? $tx->midtrans_transaction_id,
                    'user_id' => $user->id,
                ]);

                $request->merge([
                    'gross_amount' => $data['gross_amount'] ?? $tx->total_idr,
                    'transaction_id' => $data['transaction_id'] ?? $tx->midtrans_transaction_id,
                ]);

                // Fulfill order & provision blueprint / licenses
                app(\App\Http\Controllers\MidtransWebhookController::class)->fulfillOrder($tx->midtrans_order_id, $request);

                return redirect()->route('customer.dashboard')->with('success', 'Pembayaran berhasil diverifikasi oleh Midtrans Escrow! Lisensi digital Anda telah aktif.');
            } elseif (in_array($trxStatus, ['expire', 'cancel', 'deny'])) {
                $tx->update(['status' => $trxStatus]);

                return redirect()->route('customer.dashboard')->with('warning', 'Status pembayaran di Midtrans adalah: ' . strtoupper($trxStatus) . '. Silakan buat pesanan baru jika ingin melanjutkan.');
            } else {
                return redirect()->route('customer.dashboard')->with('info', 'Status tagihan saat ini masih PENDING (Menunggu Pembayaran). Silakan selesaikan pembayaran sesuai panduan Midtrans.');
            }
        }

        return redirect()->route('customer.dashboard')->with('warning', 'Tidak dapat memeriksa gateway Midtrans saat ini: ' . ($statusCheck['error'] ?? 'Koneksi gagal.'));
    }

    /**
     * Update customer profile and billing preferences.
     */
    public function updateProfile(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Sesi login tidak valid.'], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone_country_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:30',
            'npwp' => 'nullable|string|max:50',
            'billing_address' => 'nullable|string|max:500',
            'billing_city' => 'nullable|string|max:100',
            'billing_province' => 'nullable|string|max:100',
            'billing_postal_code' => 'nullable|string|max:20',
            'notify_email_sprints' => 'nullable|boolean',
            'notify_wa_billing' => 'nullable|boolean',
        ]);

        // Clean phone number (strip spaces/dashes and leading zeros if country code provided)
        $rawPhone = preg_replace('/[^0-9]/', '', $validated['phone'] ?? '');
        $countryCode = trim($validated['phone_country_code'] ?? '+62');
        if (!str_starts_with($countryCode, '+')) {
            $countryCode = '+' . $countryCode;
        }

        $notifPrefs = [
            'email_sprints' => (bool) ($request->input('notify_email_sprints', true)),
            'wa_billing' => (bool) ($request->input('notify_wa_billing', true)),
        ];

        $user->name = $validated['name'];

        $fields = [
            'company_name' => $validated['company_name'] ?? null,
            'phone_country_code' => $countryCode,
            'phone' => $rawPhone,
            'npwp' => $validated['npwp'] ?? null,
            'billing_address' => $validated['billing_address'] ?? null,
            'billing_city' => $validated['billing_city'] ?? null,
            'billing_province' => $validated['billing_province'] ?? null,
            'billing_postal_code' => $validated['billing_postal_code'] ?? null,
            'notification_preferences' => $notifPrefs,
        ];

        foreach ($fields as $col => $val) {
            if (Schema::hasColumn('users', $col)) {
                $user->{$col} = $val;
            }
        }

        if (Schema::hasColumn('users', 'profile_metadata')) {
            $existing = is_array($user->profile_metadata) ? $user->profile_metadata : [];
            $user->profile_metadata = array_merge($existing, $fields);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil dan data penagihan akun Anda berhasil diperbarui!',
            'data' => [
                'name' => $user->name,
                'company_name' => $user->company_name ?? ($fields['company_name'] ?? ''),
                'phone' => $user->phone ?? ($fields['phone'] ?? ''),
                'phone_country_code' => $user->phone_country_code ?? $countryCode,
                'npwp' => $user->npwp ?? ($fields['npwp'] ?? ''),
                'billing_address' => $user->billing_address ?? ($fields['billing_address'] ?? ''),
            ],
        ]);
    }
}
