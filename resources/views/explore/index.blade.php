<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        try {
            $globalSettings = \App\Models\CmsGlobalSetting::getAllCached();
        } catch (\Throwable) {
            $globalSettings = collect();
        }
        $getSettingVal = function($key, $default = null) use (&$globalSettings) {
            try {
                if (!isset($globalSettings[$key])) return $default;
                $item = $globalSettings[$key];
                if (is_object($item)) return $item->value ?? $default;
                if (is_array($item)) return $item['value'] ?? $default;
                return $default;
            } catch (\Throwable) {
                return $default;
            }
        };
        $featureFlags = [
            'enable_cv_pro' => (bool) $getSettingVal('feature_enable_cv_pro', true),
            'pricing_show_cv_tab' => (bool) $getSettingVal('pricing_show_cv_tab', false),
            'enable_pricing' => (bool) $getSettingVal('feature_enable_cv_pricing', true),
            'enable_vision_blueprint' => (bool) $getSettingVal('feature_enable_vision_blueprint', true),
            'enable_client_onboarding' => (bool) $getSettingVal('feature_enable_client_onboarding', true),
            'enable_digital_contract' => (bool) $getSettingVal('feature_enable_digital_contract', true),
            'midtrans_mode' => (bool) $getSettingVal('midtrans_compliance_strict_mode', true),
        ];
        $metaDesc = $locale === 'en'
            ? 'Explore cryptographically verified enterprise software blueprints across 12 functional sectors. Dual-Zone UI (Linear Back-Stage + 6 Front-Stage UX Presets), PostgreSQL 16 Strict ULID, and Zero AI Slop.'
            : 'Jelajahi pustaka arsitektur perangkat lunak enterprise terverifikasi kriptografi lintas 12 sektor fungsional. Dual-Zone UI (Linear Back-Stage + 6 Preset UX Publik), PostgreSQL 16 Strict ULID, tanpa AI Slop.';
        $currentUrl = url()->current();
        $firstSlug = $testimonials[0]['seo_slug'] ?? 'verified-architecture';
        $ogImage = route('explore.og.png', ['slug' => $firstSlug]);
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $metaDesc }}">
    <link rel="canonical" href="{{ $currentUrl }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $currentUrl }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:site_name" content="Neriah Pro">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <script type="application/ld+json">{!! $schemaOrgJsonLd !!}</script>

    <link rel="stylesheet" href="{{ asset('fonts/instrument-sans/instrument-sans.css') }}">
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/islands.jsx'])

    <script>
        if (localStorage.getItem('neriah_theme') === 'dark' || (!localStorage.getItem('neriah_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased overflow-x-hidden min-h-screen flex flex-col">

    <!-- Enterprise Fullscreen Loading Screen (Prevents Component Pop-In) -->
    <x-fullscreen-loader feature="explore" />

    <x-global-navigation :global-settings="$globalSettings" />

    @react('BreadcrumbIsland', [
        'paths' => [
            ['label' => $locale === 'en' ? 'Home' : 'Beranda', 'url' => '/'],
            ['label' => $locale === 'en' ? 'Explore Verified Architectures' : 'Eksplorasi Arsitektur Terverifikasi', 'url' => route('explore.index')],
        ]
    ])

    <main class="flex-grow">
        <!-- SSR Programmatic SEO Hero & 12-Sector Index Bar -->
        <section class="bg-white dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[11px] font-black uppercase tracking-widest rounded-none">
                        PROJECT OS v11.0 // SEO + GEO CITATION AUTHORITY LIBRARY
                    </span>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ url('/pricing#retail') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-mono text-xs font-black uppercase tracking-wider rounded-none transition-colors">
                            {{ $locale === 'en' ? 'Configure Satu Pintu & SaaS License' : 'Konfigurasi Satu Pintu & Paket SaaS' }}
                        </a>
                        <a href="{{ url('/llms.txt') }}" class="px-3.5 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-900 dark:hover:bg-zinc-800 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 font-mono text-xs font-bold uppercase tracking-wider rounded-none transition-colors">
                            /llms.txt
                        </a>
                    </div>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-zinc-950 dark:text-white mb-4">
                    {{ $locale === 'en'
                        ? '12 Functional Sectors. Dual-Zone Enterprise UI. Cryptographically Verified.'
                        : '12 Sektor Fungsional. Dual-Zone Enterprise UI. Terverifikasi Kriptografi.' }}
                </h1>
                <p class="max-w-4xl text-sm sm:text-base text-zinc-700 dark:text-zinc-300 leading-relaxed mb-8">
                    {{ $locale === 'en'
                        ? 'Every architecture receipt below is bound to a real PostgreSQL 16 Strict ULID schema, concurrency lock set, Zone A Back-Stage operational interface, and Zone B Public Front-Stage UX preset — with zero fake reviews and zero AI slop.'
                        : 'Setiap resi arsitektur di bawah terikat ke skema PostgreSQL 16 Strict ULID nyata, kunci konkurensi atomik, antarmuka operasional Zone A Back-Stage, serta preset UX publik Zone B Front-Stage — 0% testimoni karangan dan 0% AI slop.' }}
                </p>

                <!-- Crawlable SSR 12-Sector Links -->
                <nav aria-label="12 Functional Sectors" class="flex flex-wrap gap-2 mb-8">
                    <a href="{{ route('explore.index') }}"
                       class="px-3 py-1.5 font-mono text-xs font-bold uppercase border rounded-none transition-colors {{ $activeSector === 'ALL' ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-white dark:text-zinc-950 dark:border-white' : 'bg-zinc-50 text-zinc-900 border-zinc-300 hover:border-zinc-500 dark:bg-zinc-900 dark:text-zinc-200 dark:border-zinc-700 dark:hover:border-zinc-500' }}">
                        {{ $locale === 'en' ? 'ALL 12 SECTORS' : 'SEMUA 12 SEKTOR' }}
                    </a>
                    @foreach($sectors as $sec)
                        @php
                            $isCurr = $activeSector === $sec['code'];
                            $secSlug = strtolower($sec['code']);
                        @endphp
                        <a href="{{ route('explore.sector', ['sector' => $secSlug]) }}"
                           class="px-3 py-1.5 font-mono text-xs font-bold border rounded-none transition-colors {{ $isCurr ? 'bg-emerald-700 text-white border-emerald-700 dark:bg-emerald-500 dark:text-zinc-950 dark:border-emerald-400' : 'bg-white text-zinc-900 border-zinc-300 hover:bg-zinc-100 dark:bg-zinc-900 dark:text-zinc-200 dark:border-zinc-800 dark:hover:bg-zinc-800' }}">
                            {{ $sec['name'][$locale] ?? $sec['code'] }}
                        </a>
                    @endforeach
                </nav>

                <!-- GEO Mechanism #3: Semantic Answer Capsule (50-Word TL;DR + High-Signal Comparison Table) -->
                <article aria-label="GEO Semantic Answer Capsule" class="border-2 border-zinc-900 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 p-5 sm:p-6 rounded-none space-y-5">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                        <div class="font-mono text-xs font-black uppercase tracking-wider text-emerald-950 dark:text-emerald-300">
                            {{ $locale === 'en' ? 'TL;DR EXECUTIVE ANSWER CAPSULE (GEO CITATION STANDARD)' : 'TL;DR RINGKASAN EKSEKUTIF (STANDAR KUTIPAN AI & GEO)' }}
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 font-mono text-[10px]">
                            @foreach(($answerCapsule['schema_types'] ?? []) as $stType)
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-black">
                                    Schema.org/{{ $stType }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <p class="text-sm sm:text-base font-medium text-zinc-950 dark:text-zinc-100 leading-relaxed">
                        {{ $answerCapsule['tldr_text'] ?? '' }}
                    </p>

                    <div class="flex flex-wrap items-center gap-2 font-mono text-[11px]">
                        <span class="font-black text-zinc-800 dark:text-zinc-200 uppercase">
                            {{ $locale === 'en' ? 'Statutory & Audit Compliance:' : 'Kepatuhan Regulasi & Standar:' }}
                        </span>
                        @foreach(($answerCapsule['statutory_standards'] ?? []) as $stdBadge)
                            <span class="px-2 py-0.5 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 font-bold">
                                {{ $stdBadge }}
                            </span>
                        @endforeach
                    </div>

                    <div class="overflow-x-auto border border-zinc-300 dark:border-zinc-800 bg-white dark:bg-zinc-950">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead class="bg-zinc-100 dark:bg-zinc-900 border-b border-zinc-300 dark:border-zinc-800 font-mono uppercase">
                                <tr>
                                    <th class="py-2.5 px-3 font-black text-zinc-950 dark:text-zinc-100">{{ $locale === 'en' ? 'Engineering Dimension' : 'Dimensi Arsitektur' }}</th>
                                    <th class="py-2.5 px-3 font-black text-zinc-700 dark:text-zinc-300">{{ $locale === 'en' ? 'Generic AI / Legacy Dev' : 'Arsitektur Generik / AI Biasa' }}</th>
                                    <th class="py-2.5 px-3 font-black text-emerald-950 dark:text-emerald-300">{{ $locale === 'en' ? 'NeriahPro Project OS v11.0 Standard' : 'Standar NeriahPro Project OS v11.0' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                @foreach(($comparisonRows ?? []) as $cmpRow)
                                    <tr class="odd:bg-white dark:odd:bg-zinc-950 even:bg-zinc-50/70 dark:even:bg-zinc-900/40 hover:bg-zinc-100/90 dark:hover:bg-zinc-800/70">
                                        <td class="py-2.5 px-3 font-mono font-black text-zinc-950 dark:text-zinc-100">{{ $cmpRow['dimension'] }}</td>
                                        <td class="py-2.5 px-3 text-zinc-700 dark:text-zinc-300">{{ $cmpRow['generic'] }}</td>
                                        <td class="py-2.5 px-3 font-bold text-emerald-950 dark:text-emerald-300">{{ $cmpRow['neriahpro'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>
        </section>

        <!-- Interactive Kinetic Aniwall + Sticky Scrollytelling Island -->
        @react('VerifiedAniwallShowcaseIsland', [
            'currentLocale' => $locale,
            'testimonials' => $testimonials,
            'publicUxPresets' => $publicUxPresets,
            'initialSector' => $activeSector,
            'exploreUrl' => route('explore.index'),
        ])

        <!-- SSR Crawlable Blueprint Index Cards (20% Public Architecture Preview Links) -->
        <section class="bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800 py-14 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                    <div>
                        <div class="font-mono text-xs font-black uppercase tracking-widest text-emerald-900 dark:text-emerald-300 mb-1">
                            SSR INDEXABLE BLUEPRINT CATALOG ({{ count($testimonials) }})
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black text-zinc-950 dark:text-white">
                            {{ $locale === 'en' ? 'Inspect Public 20% Blueprint Teasers' : 'Inspeksi Teaser Publik 20% Arsitektur Sektoral' }}
                        </h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($testimonials as $card)
                        @php
                            $cardSectorSlug = strtolower($card['sector_code'] ?? 'sector_01_retail_fnb');
                            $cardSlug = $card['seo_slug'] ?? \Illuminate\Support\Str::slug($card['client_company'] ?? $card['id']);
                            $receipt = $card['receipt'] ?? [];
                        @endphp
                        <article class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 p-5 rounded-none flex flex-col justify-between transition-colors">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 font-mono text-[11px] font-black text-zinc-950 dark:text-zinc-100 rounded-none">
                                        {{ $card['sector_badge'] ?? $card['sector_code'] }}
                                    </span>
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black uppercase rounded-none">
                                        {{ ($card['verification_source'] ?? '') === 'MITRA_BAST_SIGNED' ? 'BAST VERIFIED' : 'PHASE-6 VERIFIED' }}
                                    </span>
                                </div>

                                <h3 class="text-base font-black text-zinc-950 dark:text-white mb-2">
                                    <a href="{{ route('explore.show', ['sector' => $cardSectorSlug, 'slug' => $cardSlug]) }}" class="hover:underline">
                                        {{ $card['client_company'] }} — {{ $card['public_ux_label'] ?? $card['public_ux_preset'] }}
                                    </a>
                                </h3>

                                <p class="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed mb-4">
                                    "{{ $card['quote'] }}"
                                </p>

                                <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 font-mono text-[11px] tabular-nums space-y-1 mb-4">
                                    <div class="text-zinc-900 dark:text-zinc-200 font-bold">
                                        {{ number_format((int) ($receipt['ulid_tables_count'] ?? 24), 0, $locale === 'id' ? ',' : '.', $locale === 'id' ? '.' : ',') }} ULID Tables |
                                        {{ (int) ($receipt['concurrency_locks_count'] ?? 6) }} Locks |
                                        -{{ (int) ($receipt['token_saved_percent'] ?? 65) }}% Tokens
                                    </div>
                                    <div class="text-zinc-700 dark:text-zinc-400">
                                        Zone A: {{ $card['operational_ui_variant'] }}
                                    </div>
                                    <div class="text-emerald-900 dark:text-emerald-300 font-bold">
                                        SHA-256: {{ $receipt['scope_hash_short'] ?? 'verified' }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-zinc-200 dark:border-zinc-800">
                                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">
                                    {{ $card['client_name'] }} · {{ $card['client_role'] }}
                                </span>
                                <a href="{{ route('explore.show', ['sector' => $cardSectorSlug, 'slug' => $cardSlug]) }}"
                                   class="font-mono text-xs font-black text-emerald-900 dark:text-emerald-300 hover:underline">
                                    {{ $locale === 'en' ? 'Inspect 20% Spec ->' : 'Inspeksi 20% Spek ->' }}
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- 5 Iron Animation Laws + 6 Critical SaaS Commercial Modules -->
        <section class="bg-white dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800 py-14 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="border border-zinc-200 dark:border-zinc-800 p-6 bg-zinc-50 dark:bg-zinc-900/60 rounded-none">
                    <div class="font-mono text-xs font-black uppercase tracking-widest text-emerald-900 dark:text-emerald-300 mb-2">
                        ZERO-JANK GUARANTEE
                    </div>
                    <h2 class="text-xl font-black text-zinc-950 dark:text-white mb-4">
                        {{ $locale === 'en' ? '5 Iron Laws of Anti-Slop Enterprise Animation' : '5 Hukum Besi Animasi Enterprise Anti-Slop' }}
                    </h2>
                    <div class="space-y-3">
                        @foreach($fiveIronLaws as $law)
                            <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                                <div class="font-mono text-xs font-black text-zinc-950 dark:text-white">
                                    [{{ $law['code'] }}] {{ $law['localized_title'] ?? ($law['title'][$locale] ?? $law['title']['id'] ?? '') }}
                                </div>
                                <p class="text-xs text-zinc-700 dark:text-zinc-300 mt-1">{{ $law['localized_rule'] ?? ($law['rule'][$locale] ?? $law['rule']['id'] ?? '') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border border-zinc-200 dark:border-zinc-800 p-6 bg-zinc-50 dark:bg-zinc-900/60 rounded-none">
                    <div class="font-mono text-xs font-black uppercase tracking-widest text-cyan-900 dark:text-cyan-300 mb-2">
                        COMMERCIAL ENGINE v10.0
                    </div>
                    <h2 class="text-xl font-black text-zinc-950 dark:text-white mb-4">
                        {{ $locale === 'en' ? '6 SaaS Growth & Governance Modules Active' : '6 Modul Pertumbuhan & Tata Kelola SaaS Aktif' }}
                    </h2>
                    <div class="space-y-3">
                        @foreach($sixSaasModules as $mod)
                            <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                                <div class="font-mono text-xs font-black text-zinc-950 dark:text-zinc-100">
                                    [{{ $mod['code'] }}] {{ $mod['localized_title'] ?? ($mod['title'][$locale] ?? $mod['title']['id'] ?? '') }}
                                </div>
                                <p class="text-xs text-zinc-700 dark:text-zinc-300 mt-1">{{ $mod['localized_summary'] ?? ($mod['summary'][$locale] ?? $mod['summary']['id'] ?? '') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>

    @react('FooterIsland', [
        'settings' => $getSettingVal('footer_format') ?: $getSettingVal('footer_links', null),
        'featureFlags' => $featureFlags,
        'whatsappNumber' => $getSettingVal('company_whatsapp', '6281380448253'),
        'supportEmail' => $getSettingVal('developer_support_email', 'support@neriahpro.com'),
        'companyAddress' => $getSettingVal('company_address', 'Gianyar, Bali, Indonesia'),
        'ownerName' => $getSettingVal('company_owner_name', 'Yoseph Iriandi'),
        'ownerTitle' => $getSettingVal('company_owner_title', 'Independent Full-Stack Developer & Software Architect'),
        'whatsappTemplateId' => $getSettingVal('company_whatsapp_template_id', 'Halo Yoseph Iriandi, saya ingin konsultasi terkait layanan pengembangan software dan arsitektur digital di Neriah Pro.'),
        'whatsappTemplateEn' => $getSettingVal('company_whatsapp_template_en', 'Hello Yoseph Iriandi, I would like to inquire about software development and digital architecture services at Neriah Pro.'),
        'currentLocale' => $locale,
    ])
</body>
</html>

