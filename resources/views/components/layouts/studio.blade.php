@php
    $studioSchemaJsonLd = \Illuminate\Support\Facades\Cache::rememberForever('schema_org:architect_studio_v4', function () {
        return json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => 'NeriahPro Project OS v4.0 — The 3-Pane Architect Studio',
            'applicationCategory' => 'DeveloperApplication',
            'operatingSystem' => 'Web, Linux, macOS, Windows',
            'url' => url('/blueprint/studio'),
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'IDR',
            ],
            'description' => 'Zero-Assumption 3-Pane Software Architecture Workbench with 12 Sectoral UI Archetypes, 9 Stack Adapters, Decision Matrix Builder, Live MCP Bridge, & Day-30 Delta Blueprint Generator.',
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    });
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>neriahpro.com - {{ $title ?? 'Project OS Architect Studio v4.0' }}</title>
    <script type="application/ld+json">{!! $studioSchemaJsonLd !!}</script>
    <script>
        (function () {
            const saved = localStorage.getItem('theme');
            if (saved === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .studio-scroll { scrollbar-width: thin; scrollbar-color: rgba(113,113,122,0.45) transparent; }
        .studio-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
        .studio-scroll::-webkit-scrollbar-thumb { background: rgba(113,113,122,0.45); }
    </style>
</head>
<body class="bg-zinc-100 dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100 font-sans antialiased min-h-screen lg:h-screen lg:overflow-hidden flex flex-col">
    {{ $slot }}

    <!-- Centralized Zero-Slop Floating Toast Container -->
    <div id="studio-toast-stack" class="fixed bottom-10 right-4 z-50 flex flex-col gap-2 pointer-events-none max-w-sm"></div>

    <script>
        window.showToast = window.showToast || function (opts) {
            const stack = document.getElementById('studio-toast-stack');
            if (!stack) return;
            const type = (opts && opts.type) || 'success';
            const title = (opts && opts.title) || 'Project OS v4.0';
            const message = (opts && opts.message) || '';
            const tone = type === 'error'
                ? 'border-red-500 bg-red-50 text-red-950 dark:bg-red-950 dark:text-red-100'
                : (type === 'warning'
                    ? 'border-amber-500 bg-amber-50 text-amber-950 dark:bg-amber-950 dark:text-amber-100'
                    : 'border-emerald-500 bg-white text-zinc-950 dark:bg-zinc-900 dark:text-zinc-100');
            const el = document.createElement('div');
            el.className = 'pointer-events-auto border-l-4 border border-zinc-300 dark:border-zinc-700 px-3.5 py-2.5 shadow-xl rounded-xs text-xs font-mono transition-opacity duration-200 ' + tone;
            el.innerHTML = '<div class="font-black uppercase tracking-wider mb-0.5"></div><div class="font-sans text-[11px] leading-snug"></div>';
            el.children[0].textContent = title;
            el.children[1].textContent = message;
            stack.appendChild(el);
            setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 220); }, 3200);
        };

        window.toggleStudioTheme = function () {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        };
    </script>
    @livewireScripts
</body>
</html>

