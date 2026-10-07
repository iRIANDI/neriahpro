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
    @php
        $schemaList = [
            \App\Services\Seo\SchemaOrgService::organization(),
            \App\Services\Seo\SchemaOrgService::webSite(),
            \App\Services\Seo\SchemaOrgService::breadcrumbs([
                'Home' => url('/'),
                $pageTitle => $currentUrl,
            ])
        ];
        if (($page->slug ?? '') === 'pricing') {
            $schemaList[] = \App\Services\Seo\SchemaOrgService::pricingServices();
            $schemaList[] = \App\Services\Seo\SchemaOrgService::pricingFaq();
        }
    @endphp
    {!! \App\Services\Seo\SchemaOrgService::render($schemaList) !!}

    <!-- Local Fonts (Zero External Latency) -->
    <link rel="stylesheet" href="{{ asset('fonts/instrument-sans/instrument-sans.css') }}">

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

        @php
            $hasRenderedPricing = false;
        @endphp
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
                    if($type == 'architecture_pricing' || $type == 'pricing_section') {
                        if ($hasRenderedPricing) continue;
                        $pluginName = 'ArchitecturePricingIsland';
                    }
                    if($type == 'cv_pricing_table') {
                        if ($hasRenderedPricing) continue;
                        if (!$featureFlags['midtrans_mode'] && $featureFlags['enable_cv_pro'] && $featureFlags['enable_pricing']) {
                            $pluginName = 'CvPricingIsland';
                        } else {
                            continue; // Skip CV pricing when Midtrans strict review is active
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
                    @php
                        if ($pluginName === 'ArchitecturePricingIsland' || $pluginName === 'CvPricingIsland') {
                            $hasRenderedPricing = true;
                        }
                    @endphp
                    @react($pluginName, array_merge($pluginData, [
                        'whatsappNumber' => $getSettingVal('company_whatsapp', '628123456789'),
                        'featureFlags' => $featureFlags,
                        'currentLocale' => $locale,
                        'pricingSettings' => [
                            'advisory_price' => $getSettingVal('pricing_advisory_price', '2.500.000'),
                            'mvp_price' => $getSettingVal('pricing_mvp_price', '50.000.000'),
                            'umkm_price' => $getSettingVal('pricing_umkm_price', '7.500.000'),
                            'retail_spark_price' => $getSettingVal('pricing_retail_spark_price', '0'),
                            'retail_lite_price' => $getSettingVal('pricing_retail_lite_price', '99.000'),
                            'retail_pro_price' => $getSettingVal('pricing_retail_pro_price', '399.000'),
                            'retail_ultimate_price' => $getSettingVal('pricing_retail_ultimate_price', '1.490.000'),
                            'retail_spark_limit' => $getSettingVal('pricing_retail_spark_limit', '2x Audit Ide / Bulan (Reset tiap tanggal 1)'),
                            'retail_lite_limit' => $getSettingVal('pricing_retail_lite_limit', '1 Proyek PRD (Revisi Form 30 Hari & Unduh Selamanya)'),
                            'retail_pro_limit' => $getSettingVal('pricing_retail_pro_limit', '1 Proyek PRD (Unlimited AI Regen & Revisi 6 Bulan)'),
                            'retail_ultimate_limit' => $getSettingVal('pricing_retail_ultimate_limit', '1 Proyek Enterprise (1 Tahun Prioritas & 1-on-1 Call 60 Menit)'),
                            'retail_login_policy' => $getSettingVal('pricing_retail_login_policy', 'Guest Mode untuk Spark (Free). Wajib Login / Daftar Akun untuk paket Lite, Pro, & Ultimate guna proteksi dokumen & lisensi.'),
                            'retail_disclaimer' => $getSettingVal('pricing_retail_disclaimer', 'Paket Instant Architectural Blueprint 100% Self-Service: Dihasilkan instan oleh AI Project OS untuk Anda atau tim developer Anda kerjakan sendiri. Tidak ada koding atau pembuatan aplikasi oleh Neriah Pro.'),
                            'active_promo_banner' => $getSettingVal('pricing_active_promo_banner', 'Gunakan Kode Voucher "UMKM-SUBSIDI-50" untuk subsidi 50% atau "CORP-INNOVATION-15M" untuk potongan Rp 15 Juta!'),
                        ]
                    ]))
                @endif
            @endif
        @endforeach

        @if(($page->slug ?? '') === 'pricing' && ! $hasRenderedPricing)
            @react('ArchitecturePricingIsland', [
                'whatsappNumber' => $getSettingVal('company_whatsapp', '628123456789'),
                'featureFlags' => $featureFlags,
                'currentLocale' => app()->getLocale(),
                'pricingSettings' => [
                    'advisory_price' => $getSettingVal('pricing_advisory_price', '2.500.000'),
                    'mvp_price' => $getSettingVal('pricing_mvp_price', '50.000.000'),
                    'umkm_price' => $getSettingVal('pricing_umkm_price', '7.500.000'),
                    'retail_spark_price' => $getSettingVal('pricing_retail_spark_price', '0'),
                    'retail_lite_price' => $getSettingVal('pricing_retail_lite_price', '99.000'),
                    'retail_pro_price' => $getSettingVal('pricing_retail_pro_price', '399.000'),
                    'retail_ultimate_price' => $getSettingVal('pricing_retail_ultimate_price', '1.490.000'),
                    'retail_spark_limit' => $getSettingVal('pricing_retail_spark_limit', '2x Audit Ide / Bulan (Reset tiap tanggal 1)'),
                    'retail_lite_limit' => $getSettingVal('pricing_retail_lite_limit', '1 Proyek PRD (Revisi Form 30 Hari & Unduh Selamanya)'),
                    'retail_pro_limit' => $getSettingVal('pricing_retail_pro_limit', '1 Proyek PRD (Unlimited AI Regen & Revisi 6 Bulan)'),
                    'retail_ultimate_limit' => $getSettingVal('pricing_retail_ultimate_limit', '1 Proyek Enterprise (1 Tahun Prioritas & 1-on-1 Call 60 Menit)'),
                    'retail_login_policy' => $getSettingVal('pricing_retail_login_policy', 'Guest Mode untuk Spark (Free). Wajib Login / Daftar Akun untuk paket Lite, Pro, & Ultimate guna proteksi dokumen & lisensi.'),
                    'retail_disclaimer' => $getSettingVal('pricing_retail_disclaimer', 'Paket Instant Architectural Blueprint 100% Self-Service: Dihasilkan instan oleh AI Project OS untuk Anda atau tim developer Anda kerjakan sendiri. Tidak ada koding atau pembuatan aplikasi oleh Neriah Pro.'),
                    'active_promo_banner' => $getSettingVal('pricing_active_promo_banner', 'Gunakan Kode Voucher "UMKM-SUBSIDI-50" untuk subsidi 50% atau "CORP-INNOVATION-15M" untuk potongan Rp 15 Juta!'),
                ]
            ])
        @endif
    </main>

    <!-- Global Footer -->
    @react('FooterIsland', [
        'settings' => $getSettingVal('footer_links', null),
        'featureFlags' => $featureFlags,
        'whatsappNumber' => $getSettingVal('company_whatsapp', '628123456789')
    ])

    @php
        $googleTranslateEnabled = (bool) $getSettingVal('google_translate_enabled', true);
        $rawAllowed = $getSettingVal('google_translate_allowed_languages', ['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es']);
        $allowedLangList = is_array($rawAllowed) ? $rawAllowed : (is_string($rawAllowed) ? json_decode($rawAllowed, true) : ['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es']);
        if (empty($allowedLangList)) $allowedLangList = ['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es'];
    @endphp

    @if($googleTranslateEnabled)
    <!-- Google Translate Container & Bridge (Tier 2) -->
    <div id="google_translate_element" class="hidden"></div>
    <script>
        function googleTranslateElementInit() {
            try {
                new google.translate.TranslateElement({
                    pageLanguage: '{{ app()->getLocale() ?: "id" }}',
                    includedLanguages: '{{ implode(",", $allowedLangList) }}',
                    autoDisplay: false
                }, 'google_translate_element');
            } catch(e) {}
        }

        window.translateLanguage = function(langCode) {
            const select = document.querySelector('.goog-te-combo');
            if (select) {
                select.value = langCode;
                select.dispatchEvent(new Event('change'));
            } else {
                document.cookie = 'googtrans=/id/' + langCode + '; path=/; domain=' + window.location.hostname;
                document.cookie = 'googtrans=/id/' + langCode + '; path=/;';
                location.reload();
            }
        };
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async defer></script>
    @endif

</body>
</html>
