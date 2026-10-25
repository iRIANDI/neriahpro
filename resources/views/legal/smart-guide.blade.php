@php
    $currentLang = $locale ?? (session('locale') ?: (request()->cookie('neriah_locale', app()->getLocale())));
    if (!in_array($currentLang, ['id', 'en'], true)) {
        $currentLang = 'id';
    }
    $isEn = ($currentLang === 'en');
    $companyName = \App\Models\CmsGlobalSetting::getVal('company_name', 'Neriah Pro Digital Services');
    $supportEmail = \App\Models\CmsGlobalSetting::getVal('developer_support_email', 'support@neriahpro.com');
    $title = $isEn ? 'Customer Smart Guide & Rights-Obligations Matrix' : 'Panduan Pintar Pelanggan & Matriks Hak-Kewajiban';
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLang }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Neriah Pro</title>
    <meta name="description" content="{{ $isEn ? 'Comprehensive transparency guide for Neriah Pro clients detailing customer rights, technical deliverables, SLA guarantees, and payment governance across Retail licenses and Turnkey Studio MVP development.' : 'Panduan transparansi komprehensif bagi pelanggan Neriah Pro mengenai hak kepemilikan intelektual, hasil berkas kerja, garansi SLA perbaikan bug, dan tata kelola pembayaran Lisensi Retail maupun proyek Turnkey Studio MVP.' }}">

    <!-- Schema.org JSON-LD Structured Data -->
    {!! \App\Services\Seo\SchemaOrgService::render([
        \App\Services\Seo\SchemaOrgService::organization(),
        \App\Services\Seo\SchemaOrgService::breadcrumbs([
            ($isEn ? 'Home' : 'Beranda') => url('/'),
            ($isEn ? 'Legal & Compliance' : 'Kepatuhan Hukum & Legalitas') => url('/terms'),
            $title => request()->url(),
        ]),
    ]) !!}

    <link rel="stylesheet" href="{{ asset('fonts/instrument-sans/instrument-sans.css') }}">
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Local Alpine.js for Interactive Tabs & States -->
    <script defer src="{{ asset('js/vendor/alpine.min.js') }}"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <script>
        if (localStorage.getItem('neriah_theme') === 'dark' || (!localStorage.getItem('neriah_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function smartGuideApp() {
            return {
                activeTab: 'overview',
                init() {
                    const hash = window.location.hash.replace('#', '');
                    const validTabs = ['overview', 'retail', 'studio', 'sla'];
                    if (validTabs.includes(hash)) {
                        this.activeTab = hash;
                    }
                },
                setTab(tab) {
                    this.activeTab = tab;
                    try {
                        window.history.replaceState(null, null, '#' + tab);
                    } catch (e) {}
                }
            };
        }
    </script>
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-emerald-500 selection:text-black">

    <!-- Global Navigation -->
    <x-global-navigation :global-settings="$globalSettings" />

    <!-- Main Content Container -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-10 sm:py-12 flex-1 w-full" x-data="smartGuideApp()" x-init="init()">
        
        <!-- Breadcrumb & Tag -->
        <div class="mb-6 flex items-center gap-2 font-mono text-xs text-zinc-500">
            <a href="/" class="hover:text-emerald-500">{{ $isEn ? 'Home' : 'Beranda' }}</a>
            <span>/</span>
            <a href="/terms?lang={{ $currentLang }}" class="hover:text-emerald-500">{{ $isEn ? 'Legal & Compliance' : 'Legalitas & Kebijakan' }}</a>
            <span>/</span>
            <span class="text-zinc-900 dark:text-zinc-100 font-bold">{{ $title }}</span>
        </div>

        <!-- Policy Navigation Tabs (Harmonized with Legal Policy Suite) -->
        <div class="flex flex-wrap gap-2 mb-8 border-b border-zinc-200 dark:border-zinc-800 pb-4">
            <a 
                href="/terms?lang={{ $currentLang }}" 
                class="px-3 py-1.5 font-mono text-xs font-bold border transition bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500 rounded-none"
            >
                {{ $isEn ? 'Terms of Service' : 'Syarat & Ketentuan' }}
            </a>
            <a 
                href="/privacy-policy?lang={{ $currentLang }}" 
                class="px-3 py-1.5 font-mono text-xs font-bold border transition bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500 rounded-none"
            >
                {{ $isEn ? 'Privacy Policy' : 'Kebijakan Privasi' }}
            </a>
            <a 
                href="/refund-policy?lang={{ $currentLang }}" 
                class="px-3 py-1.5 font-mono text-xs font-bold border transition bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500 rounded-none"
            >
                {{ $isEn ? 'Refund & Warranty Policy' : 'Garansi & Pengembalian Dana' }}
            </a>
            <a 
                href="/smart-guide?lang={{ $currentLang }}" 
                class="px-3 py-1.5 font-mono text-xs font-bold border border-emerald-500 text-emerald-950 dark:text-emerald-200 bg-emerald-100 dark:bg-emerald-950/80 shadow-xs rounded-none"
            >
                {{ $isEn ? 'Smart Client Guide' : 'Panduan Pintar Klien' }}
            </a>
        </div>

        <!-- Hero Title Box -->
        <div class="border-l-4 border-emerald-500 pl-4 sm:pl-6 py-2 mb-10">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950/80 text-emerald-950 dark:text-emerald-200 border border-emerald-400 dark:border-emerald-700 text-[10px] font-mono font-bold uppercase tracking-wider mb-2">
                <span>{{ $isEn ? 'CLIENT GOVERNANCE // RIGHTS & OBLIGATIONS MATRIX' : 'TATA KELOLA KLIEN // MATRIKS HAK & KEWAJIBAN' }}</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black uppercase tracking-tight text-zinc-900 dark:text-white">
                {{ $title }}
            </h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed max-w-3xl">
                {{ $isEn 
                    ? 'This interactive guide clearly outlines technical deliverables, intellectual property rights, maintenance SLAs, and operational obligations for both Digital Retail License buyers and Turnkey Custom Studio MVP partners.' 
                    : 'Panduan interaktif ini merinci secara transparan hak kepemilikan intelektual, berkas hasil kerja teknis, garansi pemeliharaan SLA perbaikan bug, dan tata kelola pembayaran bagi pembeli Lisensi Retail Digital maupun mitra pengerjaan penuh Studio MVP.' 
                }}
            </p>
        </div>

        <!-- Navigation Tabs: 4 Dedicated Core Tracks -->
        <div class="flex flex-wrap gap-2 mb-8 border-b border-zinc-200 dark:border-zinc-800 pb-4">
            <button 
                type="button"
                @click="setTab('overview')" 
                :class="activeTab === 'overview' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white shadow-xs' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'"
                class="px-4 py-2 font-mono text-xs font-bold border transition rounded-none cursor-pointer"
            >
                {{ $isEn ? '1. Comparison Matrix' : '1. Matriks Komparasi' }}
            </button>
            <button 
                type="button"
                @click="setTab('retail')" 
                :class="activeTab === 'retail' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white shadow-xs' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'"
                class="px-4 py-2 font-mono text-xs font-bold border transition rounded-none cursor-pointer"
            >
                {{ $isEn ? '2. Retail Digital Track' : '2. Jalur Retail Digital' }}
            </button>
            <button 
                type="button"
                @click="setTab('studio')" 
                :class="activeTab === 'studio' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white shadow-xs' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'"
                class="px-4 py-2 font-mono text-xs font-bold border transition rounded-none cursor-pointer"
            >
                {{ $isEn ? '3. Studio MVP Track' : '3. Jalur Studio MVP' }}
            </button>
            <button 
                type="button"
                @click="setTab('sla')" 
                :class="activeTab === 'sla' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white shadow-xs' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'"
                class="px-4 py-2 font-mono text-xs font-bold border transition rounded-none cursor-pointer"
            >
                {{ $isEn ? '4. SLA & Midtrans Safety' : '4. Keamanan SLA & Midtrans' }}
            </button>
        </div>

        <!-- ================================================================= -->
        <!-- TAB 1: QUICK OVERVIEW & COMPARISON MATRIX                         -->
        <!-- ================================================================= -->
        <div x-show="activeTab === 'overview'" x-cloak class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Retail Card -->
                <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 rounded-none shadow-xs">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        <div>
                            <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 uppercase">
                                {{ $isEn ? 'Self-Service Track // Instant License' : 'Jalur Mandiri // Lisensi Instan' }}
                            </span>
                            <h3 class="text-lg font-black uppercase text-zinc-900 dark:text-white">
                                {{ $isEn ? 'Digital Retail License' : 'Lisensi Retail Digital' }}
                            </h3>
                        </div>
                        <span class="font-mono text-xs px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 font-bold border border-zinc-200 dark:border-zinc-700">Spark • Lite • Pro • Ultimate</span>
                    </div>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-4 leading-relaxed font-sans">
                        {{ $isEn 
                            ? 'Instant downloadable blueprint specifications, PostgreSQL Strict ULID schemas, OpenAPI 3.1 DDL, and AI coding directives for developers and founders building with their internal team.' 
                            : 'Dokumen cetak biru spesifikasi PRD siap pakai, DDL PostgreSQL Strict ULID, OpenAPI 3.1, dan instruksi coding AI untuk developer atau founder yang membangun sistem secara mandiri bersama tim internal.' 
                        }}
                    </p>
                    <div class="space-y-2 font-mono text-xs">
                        <div class="flex items-start gap-2">
                            <span class="text-emerald-500 font-bold">✓</span>
                            <span><strong>{{ $isEn ? 'Rights:' : 'Hak:' }}</strong> {{ $isEn ? 'Lifetime commercial license ownership, royalty-free usage, and instant digital asset downloads.' : 'Kepemilikan lisensi komersial seumur hidup, bebas royalti, dan hak unduh langsung seluruh berkas digital.' }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-amber-500 font-bold">!</span>
                            <span><strong>{{ $isEn ? 'Obligations:' : 'Kewajiban:' }}</strong> {{ $isEn ? 'Self-managed code implementation, managing local file backups, and a strict ban on reselling raw template assets.' : 'Eksekusi coding mandiri, pengelolaan cadangan berkas lokal, dan larangan keras menjual kembali berkas template mentah.' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Studio Card -->
                <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 rounded-none shadow-xs">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        <div>
                            <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 uppercase">
                                {{ $isEn ? 'Turnkey Track // Full Custom Studio' : 'Jalur Pengerjaan Penuh // Studio Kustom' }}
                            </span>
                            <h3 class="text-lg font-black uppercase text-zinc-900 dark:text-white">
                                {{ $isEn ? 'Studio Project & Turnkey Engineering' : 'Proyek Studio & Rekayasa Turnkey' }}
                            </h3>
                        </div>
                        <span class="font-mono text-xs px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950 text-emerald-950 dark:text-emerald-200 font-bold border border-emerald-400 dark:border-emerald-700">
                            {{ $isEn ? '5-Sprint Turnkey' : '5 Sprint Pengerjaan Tuntas' }}
                        </span>
                    </div>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-4 leading-relaxed font-sans">
                        {{ $isEn 
                            ? '100% turnkey software development executed directly by Senior Lead Architects. Monolith Laravel 13, Filament v5 admin, hardened VPS deployment, and 30-day post-production warranty.' 
                            : 'Pengerjaan kode perangkat lunak 100% tuntas oleh tim Senior Lead Architect. Monolith modern Laravel 13, Filament v5, server dedicated VPS berkeamanan tinggi, dan garansi pemeliharaan 30 hari kalender penuh.' 
                        }}
                    </p>
                    <div class="space-y-2 font-mono text-xs">
                        <div class="flex items-start gap-2">
                            <span class="text-emerald-500 font-bold">✓</span>
                            <span><strong>{{ $isEn ? 'Rights:' : 'Hak:' }}</strong> {{ $isEn ? '100% private GitHub repository source code ownership transfer, formal delivery handover certificate, 30-day bug warranty SLA.' : 'Penyerahan 100% kepemilikan source code di repositori privat GitHub, sertifikat serah terima resmi, dan garansi perbaikan bug SLA selama 30 hari.' }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-amber-500 font-bold">!</span>
                            <span><strong>{{ $isEn ? 'Obligations:' : 'Kewajiban:' }}</strong> {{ $isEn ? 'Executing digital contract agreement, 50% Down Payment settlement, weekly UAT milestone sign-offs, formal Change Requests for scope changes.' : 'Penandatanganan kontrak digital, pelunasan Uang Muka (DP) 50%, partisipasi pengujian (UAT) mingguan, dan pengajuan resmi untuk penambahan ruang lingkup.' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparison Table -->
            <div class="border border-zinc-200 dark:border-zinc-800 overflow-x-auto bg-white dark:bg-zinc-900 shadow-xs">
                <table class="w-full text-left font-mono text-xs border-collapse">
                    <thead>
                        <tr class="bg-zinc-100 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100">
                            <th class="p-3 w-1/4 uppercase">{{ $isEn ? 'Governance Parameter' : 'Parameter Tata Kelola' }}</th>
                            <th class="p-3 w-3/8 uppercase">{{ $isEn ? 'Digital Retail License Track' : 'Jalur Lisensi Retail Digital' }}</th>
                            <th class="p-3 w-3/8 uppercase">{{ $isEn ? 'Turnkey Studio Contract Track' : 'Jalur Kontrak Studio Turnkey' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-zinc-700 dark:text-zinc-300">
                        <tr>
                            <td class="p-3 font-bold text-zinc-900 dark:text-white">{{ $isEn ? 'Access & Timeline' : 'Pemberian Akses & Waktu Pengerjaan' }}</td>
                            <td class="p-3">{{ $isEn ? 'Instant access immediately upon verified Midtrans payment.' : 'Akses unduhan instan seketika setelah pembayaran Midtrans terverifikasi.' }}</td>
                            <td class="p-3">{{ $isEn ? 'Structured per booked sprint batch (30 business days / 5 sprints).' : 'Terstruktur sesuai slot batch sprint terpesan (30 hari kerja / 5 sprint).' }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-zinc-900 dark:text-white">{{ $isEn ? 'Intellectual Property (IP)' : 'Hak Kekayaan Intelektual (HAKI)' }}</td>
                            <td class="p-3">{{ $isEn ? 'Lifetime commercial license on specification; free to build and commercialize derivative software.' : 'Lisensi komersial seumur hidup atas spesifikasi; bebas membangun dan mengkomersialkan software turunan.' }}</td>
                            <td class="p-3">{{ $isEn ? '100% GitHub repository source code IP transferred to Client upon final settlement.' : '100% Hak cipta kode repositori GitHub diserahkan penuh ke Klien pascapelunasan.' }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-zinc-900 dark:text-white">{{ $isEn ? 'Warranty & Defect Fix SLA' : 'Garansi & SLA Perbaikan Cacat' }}</td>
                            <td class="p-3">{{ $isEn ? '24-hour file replacement guarantee if downloaded archive is corrupted or inaccessible.' : 'Jaminan penggantian berkas 24 jam jika arsip unduhan terbukti rusak atau corrupt.' }}</td>
                            <td class="p-3">{{ $isEn ? '30-calendar-day warranty for defect fixes & gratis emergency SLA support post-production.' : '30 Hari Kalender garansi perbaikan bug & dukungan SLA darurat gratis pascarilis produksi.' }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-zinc-900 dark:text-white">{{ $isEn ? 'Payment Structure' : 'Skema Pembayaran' }}</td>
                            <td class="p-3">{{ $isEn ? '100% upfront settlement via Midtrans Snap (QRIS, Virtual Account, Credit Card).' : 'Bayar penuh 100% di awal via Midtrans Snap (QRIS, Virtual Account, Kartu Kredit).' }}</td>
                            <td class="p-3">{{ $isEn ? '50% Down Payment via Midtrans Snap, 50% final installment upon UAT completion and delivery handover.' : 'Termin Uang Muka (DP) 50% via Midtrans Snap, dan pelunasan 50% setelah lolos pengujian UAT dan serah terima sistem.' }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-zinc-900 dark:text-white">{{ $isEn ? 'Scope Alteration' : 'Perubahan Ruang Lingkup Fitur' }}</td>
                            <td class="p-3">{{ $isEn ? 'Self-service customization via interactive questionnaire dashboard.' : 'Kustomisasi mandiri melalui formulir interaktif di dasbor sistem.' }}</td>
                            <td class="p-3">{{ $isEn ? 'Scope Freeze protocol; additional features processed via formal Addendum / Change Request.' : 'Protokol Pembekuan Ruang Lingkup (Scope Freeze); penambahan fitur baru diajukan melalui Addendum / Change Request resmi.' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- TAB 2: RETAIL DIGITAL TRACK                                       -->
        <!-- ================================================================= -->
        <div x-show="activeTab === 'retail'" x-cloak class="space-y-6">
            <div class="border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 bg-white dark:bg-zinc-900 shadow-xs">
                <h2 class="text-xl font-black uppercase text-zinc-900 dark:text-white mb-4">
                    {{ $isEn ? 'Rights & Responsibilities of Retail License Buyers' : 'Hak & Tanggung Jawab Pembeli Lisensi Retail Digital' }}
                </h2>
                <div class="space-y-6 text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed font-sans">
                    <div class="border-l-2 border-emerald-500 pl-4 space-y-2">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase font-mono text-xs">
                            {{ $isEn ? 'YOUR RIGHTS AS A LICENSE OWNER:' : 'HAK-HAK ANDA SEBAGAI PEMILIK LISENSI:' }}
                        </h4>
                        <ul class="list-disc list-inside space-y-1 text-xs font-mono">
                            <li>{{ $isEn ? 'Right to download complete deliverables: PRD PDF, OpenAPI 3.1 YAML/JSON specifications, and PostgreSQL Strict ULID DDL SQL.' : 'Hak mengunduh berkas lengkap: Dokumen PRD PDF, spesifikasi OpenAPI 3.1 YAML/JSON, dan skema SQL PostgreSQL Strict ULID.' }}</li>
                            <li>{{ $isEn ? 'Right to build, host, and commercialize software applications without paying royalties to Neriah Pro.' : 'Hak membangun, meng-hosting, dan mengkomersialkan aplikasi perangkat lunak tanpa membayar royalti kepada Neriah Pro.' }}</li>
                            <li>{{ $isEn ? 'Right to utilize AI Agent directives (.cursorrules, AGENTS.md) in Claude, Cursor, ChatGPT, or Windsurf.' : 'Hak menggunakan instruksi terarah AI Agent (.cursorrules, AGENTS.md) pada Claude, Cursor, ChatGPT, atau Windsurf.' }}</li>
                            <li>{{ $isEn ? 'Right to obtain official commercial invoices and verifiable receipts backed by SHA-256 seal in the client portal.' : 'Hak memperoleh bukti pembayaran komersial resmi dan kwitansi berstempel kriptografis SHA-256 di portal pelanggan.' }}</li>
                            <li>{{ $isEn ? 'Ultimate Tier Exclusive: Right to a 1-on-1 architecture consultation call (60 minutes) via Google Meet with a Senior Architect.' : 'Khusus Lisensi Ultimate: Hak atas 1 sesi konsultasi arsitektur 1-on-1 (60 menit) via Google Meet bersama Senior Architect.' }}</li>
                        </ul>
                    </div>

                    <div class="border-l-2 border-amber-500 pl-4 space-y-2">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase font-mono text-xs">
                            {{ $isEn ? 'OBLIGATIONS & USAGE RESTRICTIONS OF LICENSE OWNERS:' : 'KEWAJIBAN & BATASAN PENGGUNAAN PEMILIK LISENSI:' }}
                        </h4>
                        <ul class="list-disc list-inside space-y-1 text-xs font-mono">
                            <li>{{ $isEn ? 'Obligation to maintain safe local backups of downloaded assets (Neriah Pro retains cloud backups for 365 days).' : 'Wajib menyimpan cadangan lokal berkas yang diunduh (Neriah Pro menyimpan cadangan cloud selama 365 hari kalender).' }}</li>
                            <li>{{ $isEn ? 'STRICT BAN on reselling, redistributing, or publishing raw PRD templates as competing digital products.' : 'DILARANG KERAS menjual kembali (resell), menyebarluaskan, atau mempublikasikan template mentah PRD sebagai produk tandingan.' }}</li>
                            <li>{{ $isEn ? 'Clients assume full technical responsibility for independent software coding and server infrastructure when choosing self-service retail without studio services.' : 'Klien bertanggung jawab penuh atas pelaksanaan coding mandiri dan pengelolaan server apabila memilih jalur retail tanpa jasa studio.' }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- TAB 3: STUDIO MVP TRACK                                           -->
        <!-- ================================================================= -->
        <div x-show="activeTab === 'studio'" x-cloak class="space-y-6">
            <div class="border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 bg-white dark:bg-zinc-900 shadow-xs">
                <h2 class="text-xl font-black uppercase text-zinc-900 dark:text-white mb-4">
                    {{ $isEn ? 'Rights & Governance for Studio MVP Turnkey Projects' : 'Hak & Tata Kelola Kontrak Proyek Studio MVP' }}
                </h2>
                <div class="space-y-6 text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed font-sans">
                    <div class="border-l-2 border-emerald-500 pl-4 space-y-2">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase font-mono text-xs">
                            {{ $isEn ? 'CLIENT RIGHTS IN STUDIO PARTNERSHIPS:' : 'HAK KLIEN PADA KEMITRAAN STUDIO:' }}
                        </h4>
                        <ul class="list-disc list-inside space-y-1 text-xs font-mono">
                            <li>{{ $isEn ? 'Right to full 5-sprint engineering execution adhering strictly to the scope-locked PRD specifications.' : 'Hak atas pengerjaan rekayasa penuh 5 sprint sesuai spesifikasi PRD yang telah disepakati dan dikunci.' }}</li>
                            <li>{{ $isEn ? 'Right to 100% transfer of source code ownership into Client-owned private GitHub organization repositories upon final settlement.' : 'Hak transfer 100% kepemilikan source code di repositori privat GitHub milik organisasi Klien setelah pelunasan akhir.' }}</li>
                            <li>{{ $isEn ? 'Right to monitor real-time daily progress via the Master Gantt Timeline dashboard in the customer portal.' : 'Hak memantau progres harian secara langsung melalui Master Gantt Timeline di portal pelanggan.' }}</li>
                            <li>{{ $isEn ? 'Right to provision of isolated server infrastructure (Dedicated VPS Hardening), automated database backups, and SSL configuration.' : 'Hak atas penyediaan server cloud terisolasi berkeamanan tinggi (Dedicated VPS Hardening), backup otomatis, dan sertifikat SSL.' }}</li>
                            <li>{{ $isEn ? 'Right to 30 calendar days of post-production warranty for defect fixes, logic revisions, and emergency SLA response free of charge.' : 'Hak atas 30 hari kalender garansi perbaikan bug dan cacat logika gratis pascarilis produksi.' }}</li>
                        </ul>
                    </div>

                    <div class="border-l-2 border-amber-500 pl-4 space-y-2">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase font-mono text-xs">
                            {{ $isEn ? 'CLIENT COMMITMENTS & CONTRACTUAL OBLIGATIONS:' : 'KEWAJIBAN & KOMITMEN KONTRAKTUAL KLIEN:' }}
                        </h4>
                        <ul class="list-disc list-inside space-y-1 text-xs font-mono">
                            <li>{{ $isEn ? 'Obligation to execute the digital partnership agreement with e-signature prior to Sprint 1 kickoff.' : 'Wajib membubuhkan tanda tangan digital pada Kontrak Kerja Sama sebelum Sprint 1 dimulai.' }}</li>
                            <li>{{ $isEn ? 'Obligation to complete 50% Down Payment (DP) via Midtrans Snap to lock engineering batch capacity.' : 'Wajib menyelesaikan pembayaran Uang Muka (DP 50%) via Midtrans Snap untuk mengunci slot batch engineer.' }}</li>
                            <li>{{ $isEn ? 'Obligation to submit brand assets, cloud access, or seed data within the first 5 calendar days.' : 'Wajib menyerahkan materi branding, akun cloud, atau data sampel awal dalam 5 hari kalender pertama.' }}</li>
                            <li>{{ $isEn ? 'Obligation to actively participate in weekly UAT milestone sign-offs within 48 hours.' : 'Wajib berpartisipasi aktif dalam sesi UAT mingguan dan memberikan persetujuan milestone dalam waktu 48 jam.' }}</li>
                            <li>{{ $isEn ? 'Strict adherence to Scope Freeze protocol: Scope modifications outside the signed PRD require formal Addendum processing.' : 'Kepatuhan pada protokol Scope Freeze: Penambahan fitur di luar dokumen PRD diproses via Addendum terpisah.' }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- TAB 4: SLA & MIDTRANS SAFETY                                      -->
        <!-- ================================================================= -->
        <div x-show="activeTab === 'sla'" x-cloak class="space-y-6">
            <div class="border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 bg-white dark:bg-zinc-900 shadow-xs">
                <h2 class="text-xl font-black uppercase text-zinc-900 dark:text-white mb-4">
                    {{ $isEn ? 'SLA Standards, 30-Day Defect Warranty, and Midtrans Payment Protection' : 'Standar SLA, Garansi Bug 30 Hari, dan Proteksi Pembayaran Midtrans' }}
                </h2>
                <div class="space-y-4 text-xs font-mono text-zinc-700 dark:text-zinc-300 leading-relaxed">
                    <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 space-y-2">
                        <span class="font-bold text-zinc-900 dark:text-white uppercase">
                            {{ $isEn ? '1. 30-DAY WARRANTY & DEFECT RESOLUTION SLA' : '1. GARANSI 30 HARI & SLA PENANGANAN GANGGUAN' }}
                        </span>
                        <p>
                            {{ $isEn 
                                ? 'Upon production deployment and handover, Clients receive a 30-calendar-day warranty period. Critical severity defects (system downtime or checkout halt) are responded to within < 4 business hours.' 
                                : 'Setelah sistem diserahterimakan di server produksi, Klien memperoleh masa garansi 30 hari kalender. Setiap laporan bug dengan tingkat keparahan kritis (server tidak dapat diakses atau transaksi terhenti) akan ditanggapi dan ditangani dalam waktu < 4 jam pada hari kerja.' 
                            }}
                        </p>
                    </div>

                    <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 space-y-2">
                        <span class="font-bold text-zinc-900 dark:text-white uppercase">
                            {{ $isEn ? '2. MIDTRANS TRANSACTION PROTECTION & ZERO DOUBLE BILLING' : '2. PROTEKSI TRANSAKSI MIDTRANS & PENCEGAHAN PEMBAYARAN GANDA' }}
                        </span>
                        <p>
                            {{ $isEn 
                                ? 'All transactions are processed through authorized Midtrans Snap gateways. Our system enforces SHA-256 idempotency webhooks to guarantee zero double billing and automated commercial invoice reconciliation.' 
                                : 'Seluruh pembayaran di Neriah Pro diproses melalui gerbang berizin Midtrans Snap. Sistem kami menerapkan webhook idempotency berbasis tanda tangan SHA-256 untuk memastikan Klien tidak pernah ditagih ganda dan seluruh mutasi tercatat otomatis di invoice resmi.' 
                            }}
                        </p>
                    </div>

                    <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 space-y-2">
                        <span class="font-bold text-zinc-900 dark:text-white uppercase">
                            {{ $isEn ? '3. REFUND & DISPUTE RESOLUTION PROCEDURE' : '3. ALUR PERMINTAAN REFUND & PENYELESAIAN SENGKETA' }}
                        </span>
                        <p>
                            @if($isEn)
                                If technical delivery failure occurs or critical defects cannot be resolved within 48 hours, Clients are entitled to submit refund claims via portal support tickets or by emailing directly to <a href="mailto:{{ $supportEmail }}" class="text-emerald-500 underline font-bold">{{ $supportEmail }}</a>.
                            @else
                                Apabila terdapat kegagalan penerbitan berkas digital atau ketidaksesuaian kritis yang tidak dapat diperbaiki dalam kurun waktu 48 jam, Klien berhak mengajukan pengembalian dana melalui tombol tiket bantuan di portal pelanggan atau email resmi ke <a href="mailto:{{ $supportEmail }}" class="text-emerald-500 underline font-bold">{{ $supportEmail }}</a>.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    @php
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

        $featureFlags = [
            'enable_cv_pro' => (bool) $getSettingVal('feature_enable_cv_pro', true),
            'pricing_show_cv_tab' => (bool) $getSettingVal('pricing_show_cv_tab', false),
            'enable_pricing' => (bool) $getSettingVal('feature_enable_cv_pricing', true),
            'enable_job_hub' => (bool) $getSettingVal('feature_enable_cv_job_hub', true),
            'enable_keuangan' => (bool) $getSettingVal('feature_enable_cv_keuangan', true),
            'enable_mock_interview' => (bool) $getSettingVal('feature_enable_cv_mock_interview', true),
            'enable_linkedin_suite' => (bool) $getSettingVal('feature_enable_cv_linkedin_suite', true),
            'enable_vision_blueprint' => (bool) $getSettingVal('feature_enable_vision_blueprint', true),
            'enable_client_onboarding' => (bool) $getSettingVal('feature_enable_client_onboarding', true),
            'enable_digital_contract' => (bool) $getSettingVal('feature_enable_digital_contract', true),
            'midtrans_mode' => (bool) $getSettingVal('midtrans_compliance_strict_mode', true),
        ];
    @endphp

    <!-- Global Footer Island -->
    @react('FooterIsland', [
        'settings' => $getSettingVal('footer_links', null),
        'featureFlags' => $featureFlags,
        'whatsappNumber' => $getSettingVal('company_whatsapp', '6281380448253'),
        'supportEmail' => $supportEmail,
        'companyAddress' => $getSettingVal('company_address', 'Gianyar, Bali, Indonesia'),
        'ownerName' => $getSettingVal('company_owner_name', 'Yoseph Iriandi'),
        'ownerTitle' => $getSettingVal('company_owner_title', 'Independent Full-Stack Developer & Software Architect'),
        'whatsappTemplateId' => $getSettingVal('company_whatsapp_template_id', 'Halo Yoseph Iriandi, saya ingin konsultasi terkait layanan pengembangan software dan arsitektur digital di Neriah Pro.'),
        'whatsappTemplateEn' => $getSettingVal('company_whatsapp_template_en', 'Hello Yoseph Iriandi, I would like to inquire about software development and digital architecture services at Neriah Pro.'),
        'currentLocale' => $currentLang,
    ])

</body>
</html>
