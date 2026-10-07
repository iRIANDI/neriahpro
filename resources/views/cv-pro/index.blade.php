<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CV Pro Enterprise Studio // AI Resume & Mock Interview Co-Pilot</title>
    <meta name="description" content="Platform pembuatan resume ATS-friendly berstandar enterprise dengan Microsoft MarkItDown document scan, audit skor ATS real-time, simulasi mock interview rekaman suara, dan outreach letter suite.">
    <link rel="canonical" href="{{ route('cv-pro.index') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('cv-pro.index') }}">
    <meta property="og:title" content="CV Pro Enterprise Studio // AI Resume & Mock Interview Co-Pilot">
    <meta property="og:description" content="Platform pembuatan resume ATS-friendly berstandar enterprise dengan Microsoft MarkItDown scan fisik, audit ATS real-time, dan mock interview co-pilot.">
    <meta property="og:image" content="{{ asset('favicon.ico') }}">
    <meta property="og:site_name" content="Neriah Pro">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="CV Pro Enterprise Studio // AI Resume & Mock Interview Co-Pilot">
    <meta name="twitter:description" content="Platform pembuatan resume ATS-friendly dengan Microsoft MarkItDown document scan, skor ATS real-time, dan simulasi mock interview suara.">
    <meta name="twitter:image" content="{{ asset('favicon.ico') }}">

    <!-- Local Fonts (Zero External Latency) -->
    <link rel="stylesheet" href="{{ asset('fonts/instrument-sans/instrument-sans.css') }}">

    <!-- Schema.org JSON-LD Structured Data -->
    {!! \App\Services\Seo\SchemaOrgService::render([
        \App\Services\Seo\SchemaOrgService::organization(),
        \App\Services\Seo\SchemaOrgService::webSite(),
        \App\Services\Seo\SchemaOrgService::cvProApplication(),
        \App\Services\Seo\SchemaOrgService::breadcrumbs([
            'Home' => url('/'),
            'CV Pro Studio' => route('cv-pro.index'),
        ])
    ]) !!}

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
<body class="bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col transition-colors duration-200">

    @php
        if (!isset($globalSettings) || $globalSettings instanceof \__PHP_Incomplete_Class || (!is_array($globalSettings) && !($globalSettings instanceof \Illuminate\Support\Collection) && !($globalSettings instanceof \ArrayAccess))) {
            try {
                \Illuminate\Support\Facades\Cache::forget('cms_global_settings');
                \Illuminate\Support\Facades\Cache::forget('cms_global_settings_data');
                $globalSettings = \App\Models\CmsGlobalSetting::getAllCached();
            } catch (\Throwable $e) {
                $globalSettings = collect();
            }
        }

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
    @endphp

    <!-- Global Navigation -->
    @react('GlobalNavigationIsland', [
        'settings' => $getSettingVal('main_navigation', null),
        'featureFlags' => $featureFlags,
    ])

    <main class="flex-1 pt-14">
        @php
            $currentUser = auth()->user() ? [
                'id' => auth()->user()->id,
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'is_super_admin' => auth()->user()->hasRole('super_admin') || auth()->user()->email === 'yoseph.iriandi.tambunan@gmail.com',
            ] : null;
        @endphp
        @react('CvProStudioIsland', ['initialData' => $initialData, 'featureFlags' => $featureFlags, 'currentUser' => $currentUser])
    </main>

    <!-- Global Footer -->
    @react('FooterIsland', [
        'settings' => $getSettingVal('footer_navigation', null),
        'featureFlags' => $featureFlags,
    ])

</body>
</html>
