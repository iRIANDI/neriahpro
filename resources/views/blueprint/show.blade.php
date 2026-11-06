@php
    $googleTranslateEnabled = (bool) \App\Models\CmsGlobalSetting::getVal('google_translate_enabled', true);
    $rawAllowed = \App\Models\CmsGlobalSetting::getVal('google_translate_allowed_languages', ['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es']);
    if (is_string($rawAllowed)) {
        $decoded = json_decode($rawAllowed, true);
        $rawAllowed = is_array($decoded) ? $decoded : [$rawAllowed];
    }
    $allowedLangList = is_array($rawAllowed) && !empty($rawAllowed) ? $rawAllowed : ['en', 'id', 'ja', 'zh-CN', 'ar', 'de', 'fr', 'es'];
    $langLabels = [
        'en' => ['flag' => '🇬🇧', 'name' => 'English'],
        'id' => ['flag' => '🇮🇩', 'name' => 'Indonesia'],
        'ja' => ['flag' => '🇯🇵', 'name' => '日本語'],
        'zh-CN' => ['flag' => '🇨🇳', 'name' => '简体中文'],
        'ar' => ['flag' => '🇸🇦', 'name' => 'العربية'],
        'de' => ['flag' => '🇩🇪', 'name' => 'Deutsch'],
        'fr' => ['flag' => '🇫🇷', 'name' => 'Français'],
        'es' => ['flag' => '🇪🇸', 'name' => 'Español'],
        'ko' => ['flag' => '🇰🇷', 'name' => '한국어'],
        'ru' => ['flag' => 'Русский', 'name' => 'Русский'],
        'pt' => ['flag' => '🇵🇹', 'name' => 'Português'],
        'nl' => ['flag' => '🇳🇱', 'name' => 'Nederlands'],
        'vi' => ['flag' => '🇻🇳', 'name' => 'Tiếng Việt'],
        'th' => ['flag' => '🇹🇭', 'name' => 'ไทย'],
    ];
    $currentLang = request()->cookie('neriah_locale', app()->getLocale());
    $isEn = $currentLang === 'en' || app()->getLocale() === 'en';

    // Lock & Entitlement Status
    $isRetailLicense = ($blueprint->project_status === 'Retail License' || !empty($blueprint->user_metadata['retail_tier']) || !empty($blueprint->user_metadata['package_tier']) || str_contains(strtolower($blueprint->slug ?? ''), '-retail-'));
    $isDpPaid = $blueprint->isDpConfirmed();
    $isScopeFrozen = $blueprint->isScopeFrozen();
    $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin();
    $isLocked = $isScopeFrozen && ! $isRetailLicense;
    $canUnlock = $isSuperAdmin || (! $isDpPaid) || $isRetailLicense;

    // Commercial & Velocity Tiers
    $contractDoc = $contractDocument ?? $blueprint->getContractDocument();
    $hasSignedContract = $contractDoc && $contractDoc->status === 'signed' && (float)$contractDoc->contract_amount > 0;
    $itemizedScope = $prd['itemized_cost_breakdown'] ?? \App\Services\PrdGeneratorService::calculateItemizedEstimation($blueprint);
    $pricingTiers = \App\Services\PrdGeneratorService::getPrimaryVelocityTiers($blueprint);
    $alpineTiers = [];
    $matchedSignedTierKey = null;

    foreach ($pricingTiers as $t) {
        $alpineTiers[$t['id']] = [
            'contract' => (float)$t['contract_amount'],
            'dp' => (float)$t['dp_amount'],
            'days' => $t['duration'],
            'name' => $t['name'],
        ];
        if ($hasSignedContract && (float)$t['contract_amount'] === (float)$contractDoc->contract_amount) {
            $matchedSignedTierKey = $t['id'];
        }
    }
    $tierKeys = array_keys($alpineTiers);

    if ($hasSignedContract) {
        $defaultSelectedTier = $matchedSignedTierKey ?? ($blueprint->user_metadata['selected_velocity_tier'] ?? (count($tierKeys) >= 2 ? $tierKeys[1] : ($tierKeys[0] ?? '')));
    } else {
        $preferredTier = $blueprint->user_metadata['selected_velocity_tier'] ?? null;
        $defaultSelectedTier = ($preferredTier && isset($alpineTiers[$preferredTier])) 
            ? $preferredTier 
            : (count($tierKeys) >= 2 ? $tierKeys[1] : ($tierKeys[0] ?? ''));
    }

    // Engineering Specs & Dev Steps
    $mvpEngineeringSpecs = $prd['features']['mvp_phase1'] ?? ($prd['engineering_specs']['mvp_specs'] ?? []);
    $totalDevSteps = count($mvpEngineeringSpecs) + 2;

    // ERD Tables & Prompt
    $erdTablesList = $prd['erd_schema']['tables'] ?? [];
    $erdSummary = [];
    foreach ($erdTablesList as $t) {
        $tableName = $t['name'] ?? ($t['table_name'] ?? 'tabel');
        $pkName = $t['primary_key'] ?? ($t['pk'] ?? 'id (ULID)');
        $cols = array_map(fn($c) => ($c['name'] ?? 'col') . ' (' . ($c['type'] ?? 'varchar') . ')', $t['columns'] ?? []);
        $erdSummary[] = "- Tabel: " . $tableName . " [PK: " . $pkName . "]\n  Kolom: " . implode(', ', array_slice($cols, 0, 8));
    }
    $erdText = implode("\n", $erdSummary);

    $hasSync = !empty($prd['mobile_and_sync_architecture']);
    $syncText = $hasSync 
        ? "\n\nArsitektur Sinkronisasi Offline-First (Bab 5.6):\n- Endpoint Push: POST /api/v1/sync/push\n- Endpoint Pull: GET /api/v1/sync/pull\n- Resolusi Konflik: Last-Write-Wins (LWW) berdasarkan updated_at ULID\n- SQLite Local DDL & SQLite client support" 
        : "";

    $foundationPrompt = "[FONDASI GLOBAL & SETUP BASIS DATA: " . ($blueprint->nama_bisnis ?: $blueprint->client_name) . "]\n"
        . "Tugas Arsitektur: Inisialisasi struktur database PostgreSQL, model Eloquent ULID, dan base stack project.\n\n"
        . "Spesifikasi Bab 5 (ERD Schema):\n" . $erdText . $syncText . "\n\n"
        . "Aturan Wajib Kepatuhan (.agents/AGENTS.md):\n"
        . "1. Gunakan ULID (->ulid('id')->primary()) untuk semua primary key tabel bisnis. Dilarang AUTO_INCREMENT.\n"
        . "2. Model Eloquent wajib menyertakan trait HasUlids.\n"
        . "3. Keyset Cursor Pagination O(1) (cursorPaginate()). Dilarang offset pagination.\n"
        . "4. Kolom multi-bahasa bertipe JSON dengan cast 'array'.\n"
        . "5. Jalankan verifikasi terminal: php artisan migrate:status && php artisan test\n"
        . "6. Setelah exit code 0, lakukan commit git: git commit -m 'chore(db): setup ULID migrations and base foundation'";
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $blueprint->nama_bisnis ?? $blueprint->client_name }} - Ultimate PRD & Architecture Blueprint | Neriah Pro</title>
    <meta name="description" content="Product Requirements Document (PRD) & skema arsitektur database untuk {{ $blueprint->nama_bisnis }}.">

    <!-- Schema.org JSON-LD Structured Data (Mandatory & Cached Forever) -->
    {!! \App\Services\Seo\SchemaOrgService::render([
        \App\Services\Seo\SchemaOrgService::organization(),
        \App\Services\Seo\SchemaOrgService::projectOsApplication(),
        \App\Services\Seo\SchemaOrgService::breadcrumbs([
            'Beranda' => url('/'),
            'Project OS Blueprint' => url('/blueprint'),
            $blueprint->nama_bisnis ?: 'Spesifikasi Sistem' => request()->url(),
        ]),
    ]) !!}

    <!-- Local Fonts (Zero External Latency) -->
    <link rel="stylesheet" href="{{ asset('fonts/instrument-sans/instrument-sans.css') }}">

    <!-- Vite React and CSS -->
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.getItem('neriah_theme') === 'dark' || (!localStorage.getItem('neriah_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('neriah_theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('neriah_theme', 'dark');
            }
            if (window.reRenderActiveMermaid) {
                setTimeout(() => window.reRenderActiveMermaid(), 50);
            }
        }

        window.printPrdDocument = function() {
            const wasDark = document.documentElement.classList.contains('dark');
            if (wasDark) {
                document.documentElement.classList.remove('dark');
            }
            try {
                const root = document.querySelector('[x-data]');
                if (root && root._x_dataStack && root._x_dataStack[0]) {
                    const data = root._x_dataStack[0];
                    if (typeof data.expandAllSections === 'function') {
                        data.expandAllSections();
                    }
                }
            } catch(e) {}
            setTimeout(() => {
                window.print();
                setTimeout(() => {
                    if (wasDark && localStorage.getItem('neriah_theme') !== 'light') {
                        document.documentElement.classList.add('dark');
                    }
                }, 300);
            }, 120);
        };

        window.addEventListener('beforeprint', () => {
            document.documentElement.classList.remove('dark');
        });

        window.addEventListener('afterprint', () => {
            if (localStorage.getItem('neriah_theme') === 'dark' || (!localStorage.getItem('neriah_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        });
    </script>

    @include('blueprint.partials.styles')
    @include('blueprint.partials.scripts.head-scripts')
    @include('blueprint.partials.scripts.alpine-app')
</head>
<body x-data="blueprintApp()" @open-raw-md-warning.window="openRawMdWarning($event.detail?.action)" class="bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col transition-colors duration-200 overflow-x-clip max-w-full">

    <!-- 1. TOP UTILITY HEADER (FULL SITE NAVIGATION + DUAL TIER LANGUAGE + THEME TOGGLE) -->
    <x-global-navigation :global-settings="$globalSettings ?? []" />

    @include('blueprint.partials.navigation.toolbar')

    @if(!$blueprint->is_published)
        @include('blueprint.partials.navigation.draft-fallback')
    @else
        @php
            $blueprintSections = [
                [
                    'id' => 'section-1',
                    'num' => '01',
                    'group' => 'scope',
                    'title_id' => 'Executive Technical Discovery',
                    'title_en' => 'Executive Technical Discovery',
                    'subtitle_id' => '26 Parameter Arsitektur & Analisis Kebutuhan Bisnis',
                    'subtitle_en' => '26 Architecture Parameters & Business Needs Analysis',
                    'badge' => '26 PARAMS',
                ],
                [
                    'id' => 'section-1-5',
                    'num' => '01.5',
                    'group' => 'scope',
                    'title_id' => 'Analisis ROI & Garansi Bebas Penyesalan',
                    'title_en' => 'Business ROI & No-Regret Guarantees',
                    'subtitle_id' => 'Formula Balik Modal Cepat, Risiko Menunda & 5 Proteksi Klien',
                    'subtitle_en' => 'Deadly ROI Equation, Cost of Inaction & 5 Client Protections',
                    'badge' => 'KILLER ROI',
                ],
                [
                    'id' => 'section-2',
                    'num' => '02',
                    'group' => 'scope',
                    'title_id' => 'Pengguna & Hak Akses (RBAC)',
                    'title_en' => 'Users & Access Control (RBAC)',
                    'subtitle_id' => 'Aktor Sistem, Peran Pengguna & Batasan Otoritas',
                    'subtitle_en' => 'System Actors, User Roles & Authorization Boundaries',
                    'badge' => 'RBAC',
                ],
                [
                    'id' => 'section-3',
                    'num' => '03',
                    'group' => 'scope',
                    'title_id' => 'Spesifikasi Rekayasa Fitur',
                    'title_en' => 'Feature Engineering Specs',
                    'subtitle_id' => 'Vertical Slices, Gherkin Scenarios & Agent Matrix',
                    'subtitle_en' => 'Vertical Slices, Gherkin Scenarios & Agent Matrix',
                    'badge' => 'MVP FASE 1',
                ],
                [
                    'id' => 'section-3-5',
                    'num' => '04',
                    'group' => 'studio',
                    'title_id' => 'Pusat Edukasi Developer AI',
                    'title_en' => 'AI Developer Education Hub',
                    'subtitle_id' => 'Protokol Handoff AI Coding Agent (Cursor, Claude, Windsurf)',
                    'subtitle_en' => 'AI Coding Agent Handoff Protocols (Cursor, Claude, Windsurf)',
                    'badge' => 'DEV PLAYBOOK',
                ],
                [
                    'id' => 'section-3-8',
                    'num' => '05',
                    'group' => 'studio',
                    'title_id' => 'Virtual Architecture Studio',
                    'title_en' => 'Virtual Architecture Studio',
                    'subtitle_id' => 'Command Center 4 Diagram Visual (Workflow, ERD, Dep, Gantt)',
                    'subtitle_en' => 'Command Center for 4 Visual Diagrams (Workflow, ERD, Dep, Gantt)',
                    'badge' => '4 CHARTS',
                ],
                [
                    'id' => 'section-4',
                    'num' => '06',
                    'group' => 'studio',
                    'title_id' => 'Alur Kerja Utama (User Flow)',
                    'title_en' => 'Core User Workflow',
                    'subtitle_id' => 'Diagram Alur Transaksi, Interaksi Visual & Validasi Bisnis',
                    'subtitle_en' => 'Transaction Flowchart, Visual Interaction & Business Validation',
                    'badge' => 'FLOWCHART',
                ],
                [
                    'id' => 'section-5',
                    'num' => '07',
                    'group' => 'studio',
                    'title_id' => 'Database ERD & Skema Relasi',
                    'title_en' => 'Database ERD & Relational Schema',
                    'subtitle_id' => 'Topologi PostgreSQL Strict, Entitas Kunci & Tipe Data ULID',
                    'subtitle_en' => 'PostgreSQL Strict Topology, Key Entities & ULID Datatypes',
                    'badge' => 'POSTGRESQL',
                ],
                [
                    'id' => 'section-6',
                    'num' => '08',
                    'group' => 'infra',
                    'title_id' => 'Spesifikasi Arsitektur Sistem & Infrastruktur',
                    'title_en' => 'System Architecture & Infrastructure Specs',
                    'subtitle_id' => 'Evaluasi VPS, Database Strict ULID, Redis Cache & Backup',
                    'subtitle_en' => 'VPS Evaluation, Strict ULID Database, Redis Cache & Backup',
                    'badge' => 'HARDWARE SIZING',
                ],
                [
                    'id' => 'section-7',
                    'num' => '09',
                    'group' => 'legal',
                    'title_id' => 'Opsi Kecepatan Pengerjaan (Velocity)',
                    'title_en' => 'Delivery Velocity Options',
                    'subtitle_id' => 'Standard, Fast-Track Swarm AI Accelerated, atau Emergency Squad',
                    'subtitle_en' => 'Standard, Fast-Track Swarm AI Accelerated, or Emergency Squad',
                    'badge' => 'VELOCITY',
                ],
                [
                    'id' => 'section-8',
                    'num' => '10',
                    'group' => 'legal',
                    'title_id' => 'Estimasi Timeline & Rencana Sprint',
                    'title_en' => 'Estimated Timeline & Sprint Plan',
                    'subtitle_id' => 'Alokasi Hari Pengerjaan per Sprint dari Kickoff sampai Delivery',
                    'subtitle_en' => 'Daily Sprint Allocations from Kickoff to Production Delivery',
                    'badge' => 'SPRINTS',
                ],
                [
                    'id' => 'section-9',
                    'num' => '11',
                    'group' => 'legal',
                    'title_id' => 'Tata Kelola, Kualitas & SLA',
                    'title_en' => 'Governance, Quality & SLA',
                    'subtitle_id' => 'Definition of Done (DoD), Garansi Bug 30 Hari & Penyerahan Repo',
                    'subtitle_en' => 'Definition of Done (DoD), 30-Day Bug Warranty & Repo Handover',
                    'badge' => 'SLA 30 HARI',
                ],
                [
                    'id' => 'section-10',
                    'num' => '12',
                    'group' => 'legal',
                    'title_id' => $blueprint->isRetailLicense() ? 'Kunci Scope & Lisensi Digital' : 'Kunci Scope & Pembayaran DP',
                    'title_en' => $blueprint->isRetailLicense() ? 'Scope Lock & Digital License' : 'Scope Lock & DP Payment',
                    'subtitle_id' => $blueprint->isRetailLicense() ? 'Segel Integritas SHA-256, Lisensi Digital Retail & Unduh Kode' : 'Segel Integritas SHA-256, Kontrak Digital & Midtrans / Voucher',
                    'subtitle_en' => $blueprint->isRetailLicense() ? 'SHA-256 Integrity Seal, Retail Digital License & Code Download' : 'SHA-256 Integrity Seal, Digital Contract & Midtrans / Voucher',
                    'badge' => $blueprint->isRetailLicense() ? 'LISENSI' : 'LEGAL & DP',
                ],
                [
                    'id' => 'section-13',
                    'num' => '13',
                    'group' => 'infra',
                    'title_id' => 'Ekspor Repository Project OS (15 Pilar)',
                    'title_en' => 'Export Project OS Repository (15 Pillars)',
                    'subtitle_id' => 'Scaffold Turnkey, 22 Berkas Arsitektur, .cursorrules & .ZIP',
                    'subtitle_en' => 'Turnkey Scaffolding, 22 Architecture Files, .cursorrules & .ZIP',
                    'badge' => 'FULL REPO',
                ],
            ];

            $accordionGroupDefs = [
                'scope' => [
                    'title_id' => 'Ruang Lingkup & Kebutuhan Bisnis',
                    'title_en' => 'Scope & Business Requirements',
                    'desc_id' => 'Spesifikasi discovery, aktor pengguna, dan dekomposisi fitur MVP',
                    'desc_en' => 'Discovery specs, user actors, and MVP feature decomposition',
                    'count' => 4,
                ],
                'studio' => [
                    'title_id' => 'Studio Visual & Diagram Arsitektur',
                    'title_en' => 'Visual Studio & Architecture Diagrams',
                    'desc_id' => 'Handoff developer, 4 chart studio, user flowchart, dan skema database ERD',
                    'desc_en' => 'Developer handoff, 4 chart studio, user flow, and database ERD schema',
                    'count' => 4,
                ],
                'infra' => [
                    'title_id' => 'Infrastruktur Cloud & AI Database',
                    'title_en' => 'Cloud Infrastructure & AI Database',
                    'desc_id' => 'Evaluasi arsitektur VPS, PostgreSQL strict, Redis cache & backup',
                    'desc_en' => 'VPS architecture evaluation, PostgreSQL strict, Redis cache & backup',
                    'count' => 1,
                ],
                'legal' => [
                    'title_id' => 'Investasi, SLA, Kontrak & Pembayaran',
                    'title_en' => 'Investment, SLA, Contracts & Payment',
                    'desc_id' => 'Opsi velocity pricing, timeline sprint, SLA garansi, dan kunci scope DP',
                    'desc_en' => 'Velocity pricing tiers, sprint timeline, SLA warranty, and DP scope lock',
                    'count' => 4,
                ],
            ];
        @endphp

        <!-- PUBLISHED FULL ULTIMATE PRD (SHARP BRUTALIST TECHNICAL THEME) -->
        <div class="mx-auto w-full py-6 flex gap-6 lg:gap-8 items-start justify-center relative transition-all duration-200"
            :class="navMode === 'focus' ? 'max-w-[1850px] px-3 sm:px-8' : 'max-w-[1480px] px-3 sm:px-6'">
            
            <!-- DESKTOP DEDICATED STICKY NAVIGATION SIDEBAR (xl:flex) -->
            @include('blueprint.partials.navigation.sidebar')

            <!-- MAIN PRD CONTENT -->
            <main :class="navMode === 'focus' ? 'w-full max-w-none min-w-0' : 'flex-1 max-w-4xl lg:max-w-5xl w-full min-w-0'" class="transition-all duration-200 overflow-x-clip">
                
                @include('blueprint.partials.hero-header')

                <!-- SECTION 1: EXECUTIVE TECHNICAL DISCOVERY -->
                @include('blueprint.partials.sections.01-executive-summary')

                <!-- SECTION 2: SYSTEM ACTORS & RBAC MATRIX -->
                @include('blueprint.partials.sections.02-actors-rbac')

                <!-- SECTION 3: MVP PHASE 1 CORE SPECIFICATIONS -->
                @include('blueprint.partials.sections.03-mvp-features')

                <!-- SECTION 3.5: AI CODESPEC ENGINE & PUSAT EDUKASI DEVELOPER -->
                @include('blueprint.partials.sections.03-5-uiux-design')

                <!-- SECTION 3.8: VIRTUAL ARCHITECTURE STUDIO (4 MERMAID DIAGRAMS) -->
                @include('blueprint.partials.sections.03-8-mermaid-visualizer')

                <!-- SECTION 4: SYSTEM WORKFLOWS & STATE MACHINES -->
                @include('blueprint.partials.sections.04-workflows')

                <!-- SECTION 5: DATABASE ARCHITECTURE & DDL -->
                @include('blueprint.partials.sections.05-database-erd')

                <!-- SECTION 6 & 13: SYSTEM ARCHITECTURE & 15-PILLAR PROJECT OS EXPORTER -->
                @include('blueprint.partials.sections.06-system-architecture')

                <!-- SECTION 7: ANTI-MALWARE & THREAT DEFENSE -->
                @include('blueprint.partials.sections.07-security-malware')

                <!-- SECTION 8: AUDIT TRAILS & UU PDP COMPLIANCE -->
                @include('blueprint.partials.sections.08-audit-compliance')

                <!-- SECTION 9: DISASTER RECOVERY & RUNBOOKS -->
                @include('blueprint.partials.sections.09-disaster-recovery')

                <!-- SECTION 10: COMMERCIAL QUOTATION, SPRINT ROADMAP & SCOPE LOCK -->
                @include('blueprint.partials.sections.10-commercial-delivery')

            </main>

            <!-- FLOATING QUICK-ACCESS DOCK & SCROLL-SPY NAVIGATOR (MODE 2: FOCUS FULL WIDTH / MOBILE) -->
            @include('blueprint.partials.navigation.drawer')

        </div>
    @endif

    <!-- MODAL DIALOGS -->
    @include('blueprint.partials.modals.task-scope-editor-modal')
    @include('blueprint.partials.modals.payment-voucher-modal')
    @include('blueprint.partials.modals.ai-cockpit-modal')
    @include('blueprint.partials.modals.legal-compliance-modal')
    @include('blueprint.partials.modals.scaffold-preview-modal')
    @include('blueprint.partials.modals.context-rot-modal')
    @include('blueprint.partials.modals.change-request-modal')
    @include('blueprint.partials.modals.google-translate-modal')
    @include('blueprint.partials.modals.tweak-segment-modal')
    @include('blueprint.partials.modals.fullscreen-diagram-modal')

    <!-- FOOTER -->
    @include('blueprint.partials.footer')

    <!-- TRAILING SCRIPTS -->
    @include('blueprint.partials.scripts.trailing-scripts')

</body>
</html>