@php
    $currentLang = $locale ?? (request()->cookie('neriah_locale', app()->getLocale()) === 'en' ? 'en' : 'id');
    $isEn = $currentLang === 'en';
    $title = is_array($policy->title) ? ($policy->title[$currentLang] ?? $policy->title['id'] ?? 'Kebijakan Layanan') : $policy->title;
    $content = is_array($policy->content) ? ($policy->content[$currentLang] ?? $policy->content['id'] ?? '') : $policy->content;
    $companyName = \App\Models\CmsGlobalSetting::getVal('company_name', 'Neriah Pro Digital Services');
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLang }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Neriah Pro</title>
    <meta name="description" content="{{ strip_tags(substr($content, 0, 160)) }}">

    <!-- Schema.org JSON-LD Structured Data -->
    {!! \App\Services\Seo\SchemaOrgService::render([
        \App\Services\Seo\SchemaOrgService::organization(),
        \App\Services\Seo\SchemaOrgService::breadcrumbs([
            ($isEn ? 'Home' : 'Beranda') => url('/'),
            $title => request()->url(),
        ]),
    ]) !!}

    <link rel="stylesheet" href="{{ asset('fonts/instrument-sans/instrument-sans.css') }}">
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.getItem('neriah_theme') === 'dark' || (!localStorage.getItem('neriah_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('neriah_theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('neriah_theme', 'dark');
            }
        }
    </script>
</head>
<body class="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-emerald-500 selection:text-black">

    <!-- Global Navigation Header Island -->
    <header class="border-b border-zinc-200 dark:border-zinc-800 bg-white/95 dark:bg-zinc-950/95 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="/" class="inline-flex items-center gap-2 text-base font-black uppercase tracking-tight text-zinc-900 dark:text-white">
                <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black flex items-center justify-center text-xs font-mono font-bold rounded-none">
                    N
                </span>
                <span>NERIAH<span class="text-emerald-500">PRO</span> // LEGAL</span>
            </a>

            <!-- Header Controls: Lang Switcher, Theme Toggle, Client Portal -->
            <div class="flex items-center gap-2 sm:gap-3 font-mono text-xs">
                <!-- Language Switcher -->
                <div class="inline-flex border border-zinc-300 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-900 p-0.5 rounded-none">
                    <a 
                        href="?lang=id" 
                        class="px-2 py-1 font-bold text-xs transition {{ $currentLang === 'id' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-emerald-500' }}"
                        title="Bahasa Indonesia"
                    >
                        ID
                    </a>
                    <a 
                        href="?lang=en" 
                        class="px-2 py-1 font-bold text-xs transition {{ $currentLang === 'en' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-emerald-500' }}"
                        title="English"
                    >
                        EN
                    </a>
                </div>

                <!-- Dark / Light Theme Toggle -->
                <button 
                    type="button" 
                    onclick="toggleTheme()" 
                    class="p-2 border border-zinc-300 dark:border-zinc-700 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-900 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-none cursor-pointer transition"
                    title="{{ $isEn ? 'Toggle Dark/Light Mode' : 'Ganti Tema Gelap/Terang' }}"
                    aria-label="Toggle Theme"
                >
                    <span class="dark:hidden text-xs">🌙</span>
                    <span class="hidden dark:inline text-xs">☀️</span>
                </button>

                <a href="/pricing" class="hidden sm:inline-block px-3 py-1.5 text-zinc-600 dark:text-zinc-400 hover:text-emerald-500 transition">
                    {{ $isEn ? 'Pricing' : 'Pilihan Paket' }}
                </a>
                <a href="/customer/dashboard" class="px-3 py-1.5 bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black font-bold transition rounded-none">
                    {{ $isEn ? 'Portal' : 'Portal Klien' }}
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10 sm:py-12 flex-1 w-full">
        
        <!-- Breadcrumb & Tag -->
        <div class="mb-6 flex items-center gap-2 font-mono text-xs text-zinc-500">
            <a href="/" class="hover:text-emerald-500">{{ $isEn ? 'Home' : 'Beranda' }}</a>
            <span>/</span>
            <span class="text-zinc-900 dark:text-zinc-100 font-bold">{{ $title }}</span>
        </div>

        <!-- Policy Navigation Tabs -->
        <div class="flex flex-wrap gap-2 mb-8 border-b border-zinc-200 dark:border-zinc-800 pb-4">
            <a 
                href="/terms?lang={{ $currentLang }}" 
                class="px-3 py-1.5 font-mono text-xs font-bold border transition {{ $resolvedType === 'terms_and_conditions' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500' }}"
            >
                {{ $isEn ? 'Terms of Service' : 'Syarat & Ketentuan' }}
            </a>
            <a 
                href="/privacy-policy?lang={{ $currentLang }}" 
                class="px-3 py-1.5 font-mono text-xs font-bold border transition {{ $resolvedType === 'privacy_policy' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500' }}"
            >
                {{ $isEn ? 'Privacy Policy' : 'Kebijakan Privasi' }}
            </a>
            <a 
                href="/refund-policy?lang={{ $currentLang }}" 
                class="px-3 py-1.5 font-mono text-xs font-bold border transition {{ $resolvedType === 'refund_policy' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500' }}"
            >
                {{ $isEn ? 'Refund & Warranty Policy' : 'Garansi & Pengembalian Dana' }}
            </a>
        </div>

        <!-- Article Card -->
        <article class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-10 shadow-xs rounded-none">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-2.5 h-2.5 bg-emerald-500"></span>
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                    NERIAH PRO // OFFICIAL COMPLIANCE &bull; {{ strtoupper($currentLang) }}
                </span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2">
                {{ $title }}
            </h1>
            <div class="text-xs font-mono text-zinc-500 mb-8 pb-4 border-b border-zinc-200 dark:border-zinc-800">
                {{ $isEn ? 'Last Updated:' : 'Terakhir Diperbarui:' }} <strong>{{ optional($policy->updated_at)->format('d F Y') ?: date('d F Y') }}</strong> &bull; {{ $isEn ? 'Published by' : 'Diterbitkan oleh' }} {{ $companyName }}
            </div>

            <!-- Rich Body Content -->
            <div class="prose prose-zinc dark:prose-invert max-w-none text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed space-y-4">
                {!! $content !!}
            </div>

            <!-- Compliance Footer Notice -->
            <div class="mt-10 pt-6 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 p-4 font-mono text-xs text-zinc-500 space-y-1.5">
                <div class="font-bold text-zinc-800 dark:text-zinc-200 uppercase">
                    {{ $isEn ? 'OFFICIAL CONTACT & COMPLIANCE SUPPORT:' : 'INFORMASI KONTAK RESMI & DUKUNGAN:' }}
                </div>
                <p>
                    {{ $isEn 
                        ? 'If you have any questions regarding our terms, privacy practices, or warranty claims, please contact our legal team via WhatsApp or email to: ' 
                        : 'Apabila Anda memiliki pertanyaan mengenai syarat layanan, kebijakan privasi, atau klaim garansi, hubungi tim legal kami melalui WhatsApp resmi atau email ke: ' 
                    }}
                    <a href="mailto:admin@neriahpro.com" class="text-emerald-600 dark:text-emerald-400 font-bold underline">admin@neriahpro.com</a>.
                </p>
            </div>
        </article>

    </main>

    <!-- Global Footer -->
    <footer class="mt-16 border-t border-zinc-200 dark:border-zinc-800 py-8 bg-zinc-50 dark:bg-zinc-900/60 font-mono text-xs text-zinc-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p>&copy; {{ date('Y') }} {{ $companyName }}. {{ $isEn ? 'All rights reserved.' : 'Hak cipta dilindungi undang-undang.' }}</p>
            <div class="flex flex-wrap items-center gap-4 text-[11px]">
                <a href="/terms?lang={{ $currentLang }}" class="hover:text-emerald-500 underline underline-offset-2">
                    {{ $isEn ? 'Terms of Service' : 'Syarat & Ketentuan' }}
                </a>
                <span class="text-zinc-300 dark:text-zinc-700">&bull;</span>
                <a href="/privacy-policy?lang={{ $currentLang }}" class="hover:text-emerald-500 underline underline-offset-2">
                    {{ $isEn ? 'Privacy Policy' : 'Kebijakan Privasi' }}
                </a>
                <span class="text-zinc-300 dark:text-zinc-700">&bull;</span>
                <a href="/refund-policy?lang={{ $currentLang }}" class="hover:text-emerald-500 underline underline-offset-2">
                    {{ $isEn ? 'Refund & Warranty Policy' : 'Garansi & Kebijakan Refund' }}
                </a>
            </div>
        </div>
    </footer>

</body>
</html>
