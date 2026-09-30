<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $pageTitle = $page->title[app()->getLocale()] ?? $page->title['en'] ?? (is_string($page->title) ? $page->title : 'Neriah Pro // Digital Services Hub');
        $pageDesc = $page->meta_description[app()->getLocale()] ?? $page->meta_description['en'] ?? (is_string($page->meta_description) ? $page->meta_description : 'Pusat arsitektur dan rekayasa perangkat lunak berskala tinggi.');
        $currentUrl = url()->current();
    @endphp

    <title>{{ $pageTitle }}</title>
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
        $featureFlags = [
            'enable_cv_pro' => (bool) ($globalSettings['feature_enable_cv_pro']->value ?? true),
            'enable_pricing' => (bool) ($globalSettings['feature_enable_cv_pricing']->value ?? true),
            'enable_job_hub' => (bool) ($globalSettings['feature_enable_cv_job_hub']->value ?? true),
            'enable_keuangan' => (bool) ($globalSettings['feature_enable_cv_keuangan']->value ?? true),
            'enable_mock_interview' => (bool) ($globalSettings['feature_enable_cv_mock_interview']->value ?? true),
            'enable_linkedin_suite' => (bool) ($globalSettings['feature_enable_cv_linkedin_suite']->value ?? true),
            'enable_vision_blueprint' => (bool) ($globalSettings['feature_enable_vision_blueprint']->value ?? true),
            'enable_client_onboarding' => (bool) ($globalSettings['feature_enable_client_onboarding']->value ?? true),
            'midtrans_mode' => (bool) ($globalSettings['midtrans_compliance_strict_mode']->value ?? false),
        ];
    @endphp

    <!-- Global Navigation -->
    @react('GlobalNavigationIsland', [
        'settings' => $globalSettings['main_navigation']->value ?? null,
        'featureFlags' => $featureFlags,
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
                    $pluginName = '';
                    $type = $plugin->plugin_type ?? $plugin->type ?? '';
                    if($type == 'hero_section') $pluginName = 'HeroIsland';
                    if($type == 'feature_grid' || $type == 'product_grid') $pluginName = 'ProductGridIsland';
                    if($type == 'onboarding_form') $pluginName = 'ClientOnboardingIsland';
                    if($type == 'cv_pricing_table' || $type == 'pricing_section') {
                        // Hide pricing table completely if CV Pro is disabled or in Midtrans strict mode
                        if (!$featureFlags['midtrans_mode'] && $featureFlags['enable_cv_pro'] && $featureFlags['enable_pricing']) {
                            $pluginName = 'CvPricingIsland';
                        }
                    }
                @endphp
                
                @if($pluginName)
                    @react($pluginName, array_merge((array) ($plugin->content_data ?? $plugin->data ?? []), [
                        'whatsappNumber' => $globalSettings['company_whatsapp']->value ?? '628123456789',
                        'featureFlags' => $featureFlags,
                    ]))
                @endif
            @endif
        @endforeach
    </main>

    <!-- Global Footer -->
    @react('FooterIsland', [
        'settings' => $globalSettings['footer_links']->value ?? null,
        'featureFlags' => $featureFlags,
        'whatsappNumber' => $globalSettings['company_whatsapp']->value ?? '628123456789'
    ])

</body>
</html>
