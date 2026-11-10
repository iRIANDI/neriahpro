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
                $row = $globalSettings[$key];
                if (is_object($row)) return $row->value ?? $default;
                if (is_array($row)) return $row['value'] ?? $default;
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
        $receipt = $item['receipt'] ?? [];
        $preview = $item['anonymized_preview'] ?? [];
        $currentUrl = url()->current();
        $metaDesc = ($item['sector_badge'] ?? $item['sector_code']) . ' — ' . ($item['quote'] ?? '');
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $metaDesc }}">
    <link rel="canonical" href="{{ $currentUrl }}">

    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ $currentUrl }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:image" content="{{ $ogSvgUrl }}">
    <meta property="og:site_name" content="Neriah Pro">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">
    <meta name="twitter:image" content="{{ $ogSvgUrl }}">

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
            ['label' => $locale === 'en' ? 'Explore Architectures' : 'Eksplorasi Arsitektur', 'url' => route('explore.index')],
            ['label' => $item['sector_badge'] ?? $item['sector_code'], 'url' => $currentUrl],
        ]
    ])

    <main class="flex-grow py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto space-y-8">
            <!-- Header Card -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 rounded-none">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-xs font-black uppercase rounded-none">
                            {{ ($item['verification_source'] ?? '') === 'MITRA_BAST_SIGNED' ? 'CRYPTOGRAPHIC BAST SIGNED' : 'RETAIL PHASE-6 VERIFIED' }}
                        </span>
                        <span class="px-2.5 py-1 bg-zinc-100 text-zinc-950 border border-zinc-300 dark:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700 font-mono text-xs font-bold rounded-none">
                            {{ $item['sector_badge'] ?? $item['sector_code'] }}
                        </span>
                    </div>
                    <span class="font-mono text-xs text-zinc-700 dark:text-zinc-300 tabular-nums">
                        SHA-256 Scope Seal: <strong class="text-emerald-900 dark:text-emerald-300">{{ $receipt['scope_hash_short'] ?? 'verified' }}</strong>
                    </span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-black text-zinc-950 dark:text-white mb-4">
                    {{ $item['client_company'] }} — {{ $item['public_ux_label'] ?? $item['public_ux_preset'] }}
                </h1>

                <blockquote class="p-4 bg-zinc-50 dark:bg-zinc-950 border-l-4 border-emerald-600 text-sm sm:text-base text-zinc-800 dark:text-zinc-200 italic mb-6">
                    "{{ $item['quote'] }}"
                    <footer class="mt-2 not-italic font-mono text-xs font-bold text-zinc-900 dark:text-zinc-100">
                        — {{ $item['client_name'] }}, {{ $item['client_role'] }} ({{ $item['client_company'] }})
                    </footer>
                </blockquote>

                <!-- Verified Architecture Receipt Telemetry -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 tabular-nums">
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="font-mono text-[11px] uppercase text-zinc-700 dark:text-zinc-400">PostgreSQL 16 ULID Tables</div>
                        <div class="text-2xl font-black text-zinc-950 dark:text-white mt-1">{{ (int) ($receipt['ulid_tables_count'] ?? 24) }}</div>
                    </div>
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="font-mono text-[11px] uppercase text-zinc-700 dark:text-zinc-400">Concurrency Locks</div>
                        <div class="text-2xl font-black text-zinc-950 dark:text-white mt-1">{{ (int) ($receipt['concurrency_locks_count'] ?? 6) }}</div>
                    </div>
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="font-mono text-[11px] uppercase text-zinc-700 dark:text-zinc-400">LLM Token Saved</div>
                        <div class="text-2xl font-black text-emerald-900 dark:text-emerald-300 mt-1">{{ (int) ($receipt['token_saved_percent'] ?? 65) }}%</div>
                    </div>
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="font-mono text-[11px] uppercase text-zinc-700 dark:text-zinc-400">Stack Topology</div>
                        <div class="text-sm font-mono font-black text-zinc-950 dark:text-white mt-2">{{ $receipt['stack_code'] ?? 'M1_LARAVEL_FILAMENT_MODULAR' }}</div>
                    </div>
                </div>

                <!-- GEO Mechanism #3: Sector-Specific Answer Capsule & Statutory Standards -->
                <div class="mt-6 p-4 bg-zinc-50 dark:bg-zinc-950 border-2 border-zinc-300 dark:border-zinc-800 rounded-none space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="font-mono text-xs font-black uppercase text-emerald-950 dark:text-emerald-300">
                            {{ $locale === 'en' ? 'TL;DR SECTOR EXECUTIVE DEFINITION (GEO CITATION CAPSULE)' : 'TL;DR DEFINISI EKSEKUTIF SEKTORAL (KAPSUL KUTIPAN AI & GEO)' }}
                        </span>
                        <div class="flex flex-wrap gap-1.5 font-mono text-[10px]">
                            @foreach(($answerCapsule['schema_types'] ?? []) as $stType)
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-black">
                                    Schema.org/{{ $stType }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 leading-relaxed font-medium">
                        {{ $answerCapsule['tldr_text'] ?? '' }}
                    </p>
                </div>
            </div>

            <!-- 20% Public Anonymized Blueprint Preview (Zero Proprietary Business Secrets Leaked) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-none">
                    <div class="font-mono text-xs font-black uppercase tracking-widest text-zinc-900 dark:text-zinc-200 mb-2">
                        ZONE A (OPERATIONAL BACK-STAGE) + ZONE B (PUBLIC FRONT-STAGE)
                    </div>
                    <h2 class="text-lg font-black text-zinc-950 dark:text-white mb-4">
                        {{ $locale === 'en' ? 'Dual-Zone Enterprise UI Contract' : 'Kontrak Antarmuka Dual-Zone Enterprise' }}
                    </h2>
                    <div class="space-y-3 text-xs">
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                            <div class="font-mono font-black text-zinc-950 dark:text-white">Zone A — Operational Back-Stage (Zero Heavy Animation)</div>
                            <div class="font-mono text-emerald-900 dark:text-emerald-300 mt-1">{{ $item['operational_ui_variant'] }}</div>
                            <p class="text-zinc-700 dark:text-zinc-300 mt-1">
                                {{ $locale === 'en'
                                    ? 'Strict Linear/Stripe keyboard-first operational precision, O(1) cursor pagination, sticky header, and tabular-nums.'
                                    : 'Presisi operasional ala Linear/Stripe berbasis keyboard-first, O(1) cursor pagination, sticky header, dan tabular-nums.' }}
                            </p>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                            <div class="font-mono font-black text-zinc-950 dark:text-white">Zone B — Public Front-Stage ({{ $uxDetail['code'] ?? $item['public_ux_preset'] }})</div>
                            <div class="font-mono text-cyan-900 dark:text-cyan-300 mt-1">{{ $uxDetail['localized_name'] ?? (is_array($uxDetail['name'] ?? null) ? ($uxDetail['name'][$locale] ?? $uxDetail['name']['id'] ?? '') : ($uxDetail['name'] ?? $item['public_ux_label'])) }}</div>
                            <p class="text-zinc-700 dark:text-zinc-300 mt-1">{{ $uxDetail['localized_summary'] ?? (is_array($uxDetail['summary'] ?? null) ? ($uxDetail['summary'][$locale] ?? $uxDetail['summary']['id'] ?? '') : ($uxDetail['summary'] ?? '')) }}</p>
                            <div class="mt-2 font-mono text-[11px] text-zinc-800 dark:text-zinc-300">
                                Engine: {{ $uxDetail['local_tech_stack'] ?? $uxDetail['animation_engine'] ?? 'Local CSS GPU translate3d + IntersectionObserver' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-none">
                    <div class="font-mono text-xs font-black uppercase tracking-widest text-emerald-900 dark:text-emerald-300 mb-2">
                        20% PUBLIC SCHEMA & CONCURRENCY TEASER
                    </div>
                    <h2 class="text-lg font-black text-zinc-950 dark:text-white mb-4">
                        {{ $locale === 'en' ? 'Anonymized Core ULID Tables & Locks' : 'Tabel Inti ULID & Kunci Konkurensi Teranonimisasi' }}
                    </h2>
                    <div class="space-y-3 text-xs font-mono">
                        <div>
                            <div class="text-zinc-700 dark:text-zinc-400 mb-1.5">Core 26-char ULID Tables (Public Teaser):</div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach(($preview['core_tables'] ?? ['users', 'audit_logs', 'pos_projects']) as $tbl)
                                    <span class="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 rounded-none">{{ $tbl }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <div class="text-zinc-700 dark:text-zinc-400 mb-1.5">Active Concurrency & Integrity Guards:</div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach(($preview['concurrency_guards'] ?? ['SELECT ... FOR UPDATE', 'Idempotency-Key ULID']) as $guard)
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 rounded-none">{{ $guard }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="p-3 bg-zinc-950 text-zinc-100 border border-zinc-800 rounded-none mt-3">
                            <div class="text-emerald-400 font-bold mb-1">// Remaining 80% Locked Production Artifacts:</div>
                            <div class="text-zinc-300 leading-relaxed">
                                Full PostgreSQL 16 Strict DDL, 15 Architectural Pillars, Topological Sprint DAG Tasks (Phase 1–7), AgentShield Security Rules, & MCP IDE Live Bridge.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conversion CTA Banner (Solid Brutalist, Zero Gradient) -->
            <div class="bg-zinc-950 text-white border-2 border-emerald-600 p-6 sm:p-8 rounded-none flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <div class="font-mono text-xs font-black uppercase tracking-widest text-emerald-400 mb-1">
                        BUILD THIS EXACT SECTOR ARCHITECTURE IN 60 SECONDS
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black">
                        {{ $locale === 'en'
                            ? 'Customize This Sector Blueprint with Your Own Features & Brand'
                            : 'Kustomisasi Blueprint Sektor Ini Sesuai Fitur & Brand Bisnis Anda' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-zinc-300 mt-1">
                        {{ $locale === 'en'
                            ? 'Choose Retail Self-Dev (AI Blueprint + MCP IDE Bridge) or Mitra Turnkey Engineering by Neriah Pro Senior Architects.'
                            : 'Pilih jalur Retail Self-Dev (Blueprint AI + Jembatan MCP IDE) atau Mitra Turnkey Engineering bersama Senior Architect Neriah Pro.' }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3 shrink-0">
                    <a href="{{ url('/pricing?sector=' . urlencode($item['sector_code'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL') . '#retail') }}"
                       class="px-5 py-3 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-mono text-xs font-black uppercase tracking-wider rounded-none transition-colors">
                        {{ $locale === 'en' ? 'Configure in Satu Pintu & Choose SaaS Tier ->' : 'Konfigurasi Satu Pintu & Pilih Paket SaaS ->' }}
                    </a>
                    <a href="{{ route('explore.index') }}"
                       class="px-4 py-3 bg-zinc-900 hover:bg-zinc-800 text-white border border-zinc-700 font-mono text-xs font-bold uppercase tracking-wider rounded-none transition-colors">
                        {{ $locale === 'en' ? '<- Back to All 12 Sectors' : '<- Kembali ke 12 Sektor' }}
                    </a>
                </div>
            </div>
        </div>
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

