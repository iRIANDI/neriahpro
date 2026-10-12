@props(['globalSettings'])

@php
    $getSettingVal = function($key, $default = null) use (&$globalSettings) {
        try {
            if (!isset($globalSettings[$key])) return $default;
            $item = $globalSettings[$key];
            if ($item instanceof \__PHP_Incomplete_Class) return $default;
            if (is_object($item)) return $item->value ?? $default;
            if (is_array($item)) return $item['value'] ?? $default;
            return $default;
        } catch (\Throwable) {
            return $default;
        }
    };

    $featureFlags = [
        'enable_cv_pro' => (bool) $getSettingVal('feature_enable_cv_pro', true),
        'pricing_show_cv_tab' => (bool) $getSettingVal('pricing_show_cv_tab', false),
        'enable_pricing' => (bool) $getSettingVal('feature_enable_cv_pricing', true),
        'enable_job_hub' => (bool) $getSettingVal('feature_enable_cv_job_hub', true),
        'enable_keuangan' => (bool) $getSettingVal('feature_enable_cv_keuangan', true),
        'enable_mock_interview' => (bool) $getSettingVal('feature_enable_cv_mock_interview', true),
        'enable_linkedin_suite' => (bool) $getSettingVal('feature_enable_cv_linkedin_suite', true),
        'enable_vision_blueprint' => (bool) $getSettingVal('feature_enable_vision_blueprint', true),
        'enable_client_onboarding' => (bool) $getSettingVal('feature_enable_client_onboarding', true),
        'enable_digital_contract' => (bool) $getSettingVal('feature_enable_digital_contract', true),
        'midtrans_mode' => (bool) $getSettingVal('midtrans_compliance_strict_mode', true),
    ];

    // Anti-Ghost Hold Cart Data with countdown
    $rawCart = session('neriah_cart', []);
    $cartItems = [];
    $minRemaining = null;
    $now = \Illuminate\Support\Carbon::now();
    foreach ($rawCart as $slug => $c) {
        try {
            $exp = isset($c['expires_at'])
                ? \Illuminate\Support\Carbon::parse($c['expires_at'])
                : (isset($c['added_at']) ? \Illuminate\Support\Carbon::parse($c['added_at'])->addHours(24) : $now->copy()->addHours(24));
            $rem = max(0, (int) $now->diffInSeconds($exp, false));
            if ($rem > 0) {
                $cartItems[] = [
                    'slug' => $slug,
                    'title' => $c['title'] ?? 'Blueprint Project',
                    'contract_amount' => $c['contract_amount'] ?? 50000000,
                    'dp_amount' => $c['dp_amount'] ?? 25000000,
                    'tier' => $c['tier'] ?? 'standard',
                    'expires_at' => $exp->toIso8601String(),
                    'remaining_seconds' => $rem,
                ];
                if ($minRemaining === null || $rem < $minRemaining) {
                    $minRemaining = $rem;
                }
            }
        } catch (\Throwable $e) {
            // ignore malformed entry
        }
    }
    $cartData = [
        'count' => count($cartItems),
        'items' => $cartItems,
        'min_remaining_seconds' => $minRemaining,
    ];

    $authUser = auth()->check() ? [
        'id' => auth()->id(),
        'name' => auth()->user()->name,
        'email' => auth()->user()->email,
        'is_admin' => auth()->user()->hasRole('super_admin') || auth()->user()->email === 'yoseph.iriandi.tambunan@gmail.com',
        'admin_path' => '/' . env('FILAMENT_ADMIN_PATH', 'admin'),
    ] : null;
@endphp

<!-- Global Navigation Island -->
@react('GlobalNavigationIsland', [
    'settings' => $getSettingVal('main_navigation', null),
    'featureFlags' => $featureFlags,
    'cartData' => $cartData,
    'authUser' => $authUser,
])
