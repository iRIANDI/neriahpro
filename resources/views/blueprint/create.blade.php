<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ultimate Tech Proposal & PRD Blueprint - Neriah Pro Hub</title>
    <meta name="description" content="Kuesioner penyusunan spesifikasi teknis dan blueprint arsitektur aplikasi terpusat untuk bisnis Anda.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Vite React and CSS -->
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/islands.jsx'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col">

    <!-- Global Navigation Island -->
    @if(isset($globalSettings['main_navigation']))
        @react('GlobalNavigationIsland', ['settings' => $globalSettings['main_navigation']->value ?? null])
    @else
        <header class="bg-slate-900 text-white border-b border-slate-800 py-4 px-6 sticky top-0 z-50">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <a href="/" class="text-xl font-extrabold tracking-tight flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-sm font-black">N</span>
                    <span>NERIAH<span class="text-indigo-400">PRO</span></span>
                </a>
                <a href="/admin/login" class="text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-white transition">
                    Portal Admin &rarr;
                </a>
            </div>
        </header>
    @endif

    <!-- Header Section -->
    <header class="bg-slate-900 text-white py-14 px-4 text-center shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 pointer-events-none bg-[radial-gradient(#6366f1_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="relative max-w-4xl mx-auto">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 mb-4">
                Neriah Pro &bull; Project OS Engine
            </span>
            <h1 class="text-3xl md:text-5xl font-extrabold mb-4 tracking-tight">Website Architecture Proposal</h1>
            <p class="text-base md:text-xl text-slate-300 max-w-3xl mx-auto leading-relaxed">
                Disiapkan khusus untuk menunjang skalabilitas dan kecepatan operasional bisnis Anda dengan arsitektur teknologi terpusat Modern Monolith.
            </p>
        </div>
    </header>

    <!-- Main React Island Section -->
    <main class="flex-1">
        @react('ProjectBlueprintIsland', [
            'csrfToken' => csrf_token(),
            'submitUrl' => url('/api/vision-blueprint'),
            'initialData' => []
        ])
    </main>

    <!-- Global Footer -->
    @if(isset($globalSettings['footer_links']))
        @react('FooterIsland', ['settings' => $globalSettings['footer_links']->value ?? null])
    @else
        <footer class="bg-slate-900 text-slate-400 py-8 px-4 text-center text-sm border-t border-slate-800">
            <div class="max-w-4xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
                <p>&copy; {{ date('Y') }} Neriah Pro HUB. All rights reserved.</p>
                <div class="flex items-center gap-4 text-xs">
                    <span>Modern Monolith Architecture</span>
                    <span>&bull;</span>
                    <span>PostgreSQL ULID Standard</span>
                </div>
            </div>
        </footer>
    @endif

</body>
</html>
