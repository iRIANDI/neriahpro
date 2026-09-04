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

    <!-- Global Header (Precision Sharp Style) -->
    <header class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 py-3.5 px-6 sticky top-0 z-50 transition-colors">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="/" class="text-base font-black uppercase tracking-tight flex items-center gap-2 text-zinc-900 dark:text-white">
                <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black flex items-center justify-center text-xs font-mono font-bold rounded-none">N</span>
                <span>NERIAH<span class="text-emerald-500">PRO</span> // HUB</span>
            </a>
            <div class="flex items-center gap-4">
                <a href="/admin/login" class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition">
                    Portal Admin &rarr;
                </a>
            </div>
        </div>
    </header>

    <!-- Header Section (Technical Precision Theme) -->
    <section class="bg-zinc-900 text-white py-12 px-4 text-center border-b border-zinc-800 relative">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block px-3 py-1 text-[11px] font-mono font-bold uppercase tracking-widest bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 mb-3 rounded-none">
                SYSTEM SPECIFICATION & PRD PROTOCOL
            </span>
            <h1 class="text-2xl sm:text-4xl font-black uppercase tracking-tight mb-2">
                Website Architecture Proposal
            </h1>
            <p class="text-xs sm:text-sm text-zinc-400 max-w-2xl mx-auto font-sans leading-relaxed">
                Disiapkan khusus untuk menunjang skalabilitas dan kecepatan operasional bisnis Anda dengan arsitektur teknologi terpusat Modern Monolith.
            </p>
        </div>
    </section>

    <!-- Main React Island Section -->
    <main class="flex-1">
        @react('ProjectBlueprintIsland', [
            'csrfToken' => csrf_token(),
            'submitUrl' => url('/api/vision-blueprint'),
            'initialData' => []
        ])
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-zinc-900 text-zinc-500 dark:text-zinc-400 py-6 px-4 text-center text-xs border-t border-zinc-200 dark:border-zinc-800 font-mono">
        <div class="max-w-4xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} NERIAH PRO HUB. ALL RIGHTS RESERVED.</p>
            <p class="text-zinc-400 dark:text-zinc-500">POSTGRESQL STRICT ULID // MODERN MONOLITH</p>
        </div>
    </footer>

</body>
</html>
