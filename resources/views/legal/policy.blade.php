@php
    $currentLang = request()->cookie('neriah_locale', app()->getLocale());
    $isEn = $currentLang === 'en' || app()->getLocale() === 'en';
    $title = is_array($policy->title) ? ($policy->title[$currentLang] ?? $policy->title['id'] ?? 'Kebijakan Layanan') : $policy->title;
    $content = is_array($policy->content) ? ($policy->content[$currentLang] ?? $policy->content['id'] ?? '') : $policy->content;
    $companyName = \App\Models\CmsGlobalSetting::getVal('company_name', 'Neriah Pro Digital Services');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Neriah Pro</title>
    <meta name="description" content="{{ strip_tags(substr($content, 0, 160)) }}">

    <!-- Schema.org JSON-LD Structured Data -->
    {!! \App\Services\Seo\SchemaOrgService::render([
        \App\Services\Seo\SchemaOrgService::organization(),
        \App\Services\Seo\SchemaOrgService::breadcrumbs([
            'Beranda' => url('/'),
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
    </script>
</head>
<body class="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-emerald-500 selection:text-black">

    <!-- Global Navigation Header Island -->
    <header class="border-b border-zinc-200 dark:border-zinc-800 bg-white/90 dark:bg-zinc-950/90 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="/" class="inline-flex items-center gap-2 text-base font-black uppercase tracking-tight text-zinc-900 dark:text-white">
                <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black flex items-center justify-center text-xs font-mono font-bold rounded-none">
                    N
                </span>
                <span>NERIAH<span class="text-emerald-500">PRO</span> // LEGAL</span>
            </a>

            <div class="flex items-center gap-3 font-mono text-xs">
                <a href="/pricing" class="text-zinc-600 dark:text-zinc-400 hover:text-emerald-500 transition">
                    {{ $isEn ? 'Pricing & Packages' : 'Pilihan Paket' }}
                </a>
                <a href="/customer/dashboard" class="px-3 py-1.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 font-bold transition">
                    {{ $isEn ? 'Client Portal' : 'Portal Klien' }}
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-12 flex-1 w-full">
        
        <!-- Breadcrumb & Tag -->
        <div class="mb-6 flex items-center gap-2 font-mono text-xs text-zinc-500">
            <a href="/" class="hover:text-emerald-500">Beranda</a>
            <span>/</span>
            <span class="text-zinc-900 dark:text-zinc-100 font-bold">{{ $title }}</span>
        </div>

        <!-- Policy Navigation Tabs -->
        <div class="flex flex-wrap gap-2 mb-8 border-b border-zinc-200 dark:border-zinc-800 pb-4">
            <a 
                href="/terms" 
                class="px-3 py-1.5 font-mono text-xs font-bold border transition {{ $resolvedType === 'terms_and_conditions' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500' }}"
            >
                {{ $isEn ? 'Terms of Service' : 'Syarat & Ketentuan' }}
            </a>
            <a 
                href="/privacy-policy" 
                class="px-3 py-1.5 font-mono text-xs font-bold border transition {{ $resolvedType === 'privacy_policy' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500' }}"
            >
                {{ $isEn ? 'Privacy Policy' : 'Kebijakan Privasi' }}
            </a>
            <a 
                href="/refund-policy" 
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
                    NERIAH PRO // OFFICIAL COMPLIANCE
                </span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2">
                {{ $title }}
            </h1>
            <div class="text-xs font-mono text-zinc-500 mb-8 pb-4 border-b border-zinc-200 dark:border-zinc-800">
                Terakhir Diperbarui: <strong>{{ optional($policy->updated_at)->format('d F Y') ?: date('d F Y') }}</strong> &bull; Diterbitkan oleh {{ $companyName }}
            </div>

            <!-- Rich Body Content -->
            <div class="prose prose-zinc dark:prose-invert max-w-none text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed space-y-4">
                {!! $content !!}
            </div>

            <!-- Compliance Footer Notice -->
            <div class="mt-10 pt-6 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 p-4 font-mono text-xs text-zinc-500 space-y-1.5">
                <div class="font-bold text-zinc-800 dark:text-zinc-200 uppercase">INFORMASI KONTAK RESMI & DUKUNGAN:</div>
                <p>
                    Apabila Anda memiliki pertanyaan mengenai syarat layanan, kebijakan privasi, atau klaim garansi, hubungi tim legal kami melalui WhatsApp resmi atau email ke: <a href="mailto:admin@neriahpro.com" class="text-emerald-600 dark:text-emerald-400 font-bold underline">admin@neriahpro.com</a>.
                </p>
            </div>
        </article>

    </main>

    <!-- Footer Island Root -->
    <div id="footer-island-root"></div>

</body>
</html>
