<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        // Self-healing guard against incomplete class deserialization
        if (!is_object($page) || $page instanceof \__PHP_Incomplete_Class) {
            $page = \App\Models\CmsPage::where('slug', 'home')->where('is_published', true)->first() ?: (object)[
                'title' => ['en' => 'Digital Services Hub', 'id' => 'Pusat Rekayasa Digital'],
                'meta_description' => ['en' => 'High-retention digital architecture platform.', 'id' => 'Pusat arsitektur dan rekayasa perangkat lunak.'],
                'slug' => 'home',
                'plugins' => []
            ];
        }

        // Self-healing guard for globalSettings against incomplete class deserialization
        if (!isset($globalSettings) || $globalSettings instanceof \__PHP_Incomplete_Class || (!is_array($globalSettings) && !($globalSettings instanceof \Illuminate\Support\Collection) && !($globalSettings instanceof \ArrayAccess))) {
            try {
                \Illuminate\Support\Facades\Cache::forget('cms_global_settings');
                \Illuminate\Support\Facades\Cache::forget('cms_global_settings_data');
                $globalSettings = \App\Models\CmsGlobalSetting::getAllCached();
            } catch (\Throwable $e) {
                $globalSettings = collect();
            }
        }

        $pageTitle = 'Digital Services Hub';
        if (is_array($page->title)) {
            $pageTitle = $page->title[app()->getLocale()] ?? $page->title['en'] ?? $page->title['id'] ?? 'Digital Services Hub';
        } elseif (is_string($page->title)) {
            $pageTitle = $page->title;
        }

        $siteDomain = 'neriahpro.com';
        $fullTabTitle = "{$siteDomain} - {$pageTitle}";

        $pageDesc = 'Pusat arsitektur dan rekayasa perangkat lunak berskala tinggi.';
        if (is_array($page->meta_description)) {
            $pageDesc = $page->meta_description[app()->getLocale()] ?? $page->meta_description['en'] ?? $page->meta_description['id'] ?? $pageDesc;
        } elseif (is_string($page->meta_description)) {
            $pageDesc = $page->meta_description;
        }

        $currentUrl = url()->current();
    @endphp

    <title>{{ $fullTabTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}">
    <link rel="canonical" href="{{ $currentUrl }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $currentUrl }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:image" content="{{ asset('favicon.ico') }}">
    <meta property="og:site_name" content="Neriah Pro">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    <meta name="twitter:image" content="{{ asset('favicon.ico') }}">

    <!-- Schema.org JSON-LD Structured Data -->
    {!! \App\Services\Seo\SchemaOrgService::render([
        \App\Services\Seo\SchemaOrgService::organization(),
        \App\Services\Seo\SchemaOrgService::webSite(),
        \App\Services\Seo\SchemaOrgService::breadcrumbs([
            'Home' => url('/'),
            $pageTitle => $currentUrl,
        ])
    ]) !!}

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Vite React and CSS -->
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/islands.jsx'])

    <script>
        // Init theme before DOM paint to prevent flash
        if (localStorage.getItem('neriah_theme') === 'dark' || (!localStorage.getItem('neriah_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased overflow-x-hidden min-h-screen transition-colors duration-200">
    
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
    @endphp

    <!-- Global Navigation -->
    @react('GlobalNavigationIsland', [
        'settings' => $getSettingVal('main_navigation', null),
        'featureFlags' => $featureFlags,
        'cartData' => $cartData,
    ])

    <!-- Breadcrumb (Dynamic, hidden on home) -->
    @php
        $breadcrumbPaths = [
            ['label' => 'Home', 'url' => '/'],
        ];
        if($page->slug !== 'home') {
            $title = $page->title[app()->getLocale()] ?? $page->title['en'] ?? (is_string($page->title) ? $page->title : 'Neriah Pro');
            $breadcrumbPaths[] = ['label' => $title, 'url' => '/' . $page->slug];
        }
    @endphp
    @react('BreadcrumbIsland', ['paths' => $breadcrumbPaths])

    <main>
        @foreach($page->plugins ?? [] as $plugin)
            @php
                // Cast array to object if necessary
                $plugin = is_array($plugin) ? (object) $plugin : $plugin;
            @endphp
            @if($plugin->is_active ?? true)
                @php
                    $type = $plugin->plugin_type ?? $plugin->type ?? '';
                @endphp
                @if(in_array($type, ['feature_grid', 'product_grid']))
                    @continue
                @endif
                @php
                    $pluginName = '';
                    if($type == 'hero_section') $pluginName = 'HeroIsland';
                    if($type == 'onboarding_form' && $featureFlags['enable_client_onboarding']) $pluginName = 'ClientOnboardingIsland';
                    if($type == 'cv_pricing_table' || $type == 'pricing_section') {
                        // Hide pricing table completely if CV Pro is disabled or in Midtrans strict mode
                        if (!$featureFlags['midtrans_mode'] && $featureFlags['enable_cv_pro'] && $featureFlags['enable_pricing']) {
                            $pluginName = 'CvPricingIsland';
                        }
                    }
                    // Resolve multilingual fields for current active locale
                    $locale = app()->getLocale();
                    $rawContent = (array) ($plugin->content_data ?? $plugin->data ?? []);
                    $pluginData = [];
                    foreach ($rawContent as $k => $v) {
                        if (is_array($v) && (isset($v['id']) || isset($v['en']))) {
                            $pluginData[$k] = $v[$locale] ?? $v['en'] ?? $v['id'] ?? '';
                        } else {
                            $pluginData[$k] = $v;
                        }
                    }
                @endphp
                
                @if($pluginName)
                    @react($pluginName, array_merge($pluginData, [
                        'whatsappNumber' => $getSettingVal('company_whatsapp', '628123456789'),
                        'featureFlags' => $featureFlags,
                        'currentLocale' => $locale,
                    ]))
                @endif
            @endif
        @endforeach
    </main>

    <!-- Global Footer -->
    @react('FooterIsland', [
        'settings' => $getSettingVal('footer_links', null),
        'featureFlags' => $featureFlags,
        'whatsappNumber' => $getSettingVal('company_whatsapp', '628123456789')
    ])

</body>
</html>
