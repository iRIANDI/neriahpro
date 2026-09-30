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

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Lato:ital,wght@0,300;0,400;0,700;1,400&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300&family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">

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

    <!-- Global Navigation -->
    @react('GlobalNavigationIsland', ['settings' => $globalSettings['main_navigation']->value ?? null])

    <main class="flex-1 pt-14">
        @react('CvProStudioIsland', ['initialData' => $initialData])
    </main>

    <!-- Global Footer -->
    @react('FooterIsland', ['settings' => $globalSettings['footer_navigation']->value ?? null])

</body>
</html>
