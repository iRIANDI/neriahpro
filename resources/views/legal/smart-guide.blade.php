@php
    $currentLang = $locale ?? (request()->cookie('neriah_locale', app()->getLocale()) === 'en' ? 'en' : 'id');
    $isEn = $currentLang === 'en';
    $companyName = \App\Models\CmsGlobalSetting::getVal('company_name', 'Neriah Pro Digital Services');
    $supportEmail = \App\Models\CmsGlobalSetting::getVal('developer_support_email', 'support@neriahpro.com');
    $title = $isEn ? 'Customer Smart Retention Guide & Rights-Obligations Matrix' : 'Panduan Pintar & Matriks Hak-Kewajiban Pelanggan';
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLang }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Neriah Pro</title>
    <meta name="description" content="{{ $isEn ? 'Comprehensive guide for Neriah Pro clients detailing customer rights, deliverables, SLA guarantees, and obligations across Retail licenses and Studio development.' : 'Panduan komprehensif bagi pelanggan Neriah Pro mengenai hak, kewajiban, deliverable, garansi SLA, dan tata kelola lisensi Retail maupun proyek Studio MVP.' }}">

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
                setTab(tab) {
                    this.activeTab = tab;
                }
            };
        }
    </script>
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-emerald-500 selection:text-black">

    <!-- Global Navigation -->
    <x-global-navigation :global-settings="$globalSettings" />

    <!-- Main Content Container -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-10 sm:py-12 flex-1 w-full" x-data="smartGuideApp()">
        
        <!-- Breadcrumb & Tag -->
        <div class="mb-6 flex items-center gap-2 font-mono text-xs text-zinc-500">
            <a href="/" class="hover:text-emerald-500">{{ $isEn ? 'Home' : 'Beranda' }}</a>
            <span>/</span>
            <span class="text-zinc-900 dark:text-zinc-100 font-bold">{{ $title }}</span>
        </div>

        <!-- Hero Title Box -->
        <div class="border-l-4 border-emerald-500 pl-4 sm:pl-6 py-2 mb-10">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950/80 text-emerald-950 dark:text-emerald-200 border border-emerald-400 dark:border-emerald-700 text-[10px] font-mono font-bold uppercase tracking-wider mb-2">
                <span>{{ $isEn ? 'CLIENT GOVERNANCE // HIGH-RETENTION RIGHTS & OBLIGATIONS' : 'TATA KELOLA KLIEN // HAK & KEWAJIBAN RETENSI TINGGI' }}</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black uppercase tracking-tight text-zinc-900 dark:text-white">
                {{ $title }}
            </h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed max-w-3xl">
                {{ $isEn 
                    ? 'This interactive guide clearly outlines the exact deliverables, intellectual property rights, maintenance SLAs, and operational obligations for both Retail License buyers and Custom Studio MVP partners.' 
                    : 'Panduan interaktif ini merinci secara transparan hak kepemilikan intelektual, deliverable teknis, garansi SLA perbaikan bug, dan tata kelola pembayaran bagi pembeli Lisensi Retail maupun mitra pengerjaan Studio MVP.' 
                }}
            </p>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex flex-wrap gap-2 mb-8 border-b border-zinc-200 dark:border-zinc-800 pb-4">
            <button 
                type="button"
                @click="setTab('overview')" 
                :class="activeTab === 'overview' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white shadow-xs' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'"
                class="px-4 py-2 font-mono text-xs font-bold border transition rounded-none cursor-pointer"
            >
                {{ $isEn ? '1. Comparison Matrix' : '1. Matriks Hak vs Kewajiban' }}
            </button>
            <button 
                type="button"
                @click="setTab('retail')" 
                :class="activeTab === 'retail' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white shadow-xs' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'"
                class="px-4 py-2 font-mono text-xs font-bold border transition rounded-none cursor-pointer"
            >
                {{ $isEn ? '2. Retail Digital Track' : '2. Jalur Lisensi Retail' }}
            </button>
            <button 
                type="button"
                @click="setTab('studio')" 
                :class="activeTab === 'studio' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white shadow-xs' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'"
                class="px-4 py-2 font-mono text-xs font-bold border transition rounded-none cursor-pointer"
            >
                {{ $isEn ? '3. Studio MVP Track' : '3. Jalur Kontrak Studio' }}
            </button>
            <button 
                type="button"
                @click="setTab('sla')" 
                :class="activeTab === 'sla' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white shadow-xs' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'"
                class="px-4 py-2 font-mono text-xs font-bold border transition rounded-none cursor-pointer"
            >
                {{ $isEn ? '4. SLA & Midtrans Safety' : '4. SLA, Garansi & Midtrans' }}
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
                                {{ $isEn ? 'Self-Service Track // Instant License' : 'Jalur Mandiri // Self-Service' }}
                            </span>
                            <h3 class="text-lg font-black uppercase text-zinc-900 dark:text-white">
                                {{ $isEn ? 'Digital Retail License' : 'Lisensi Retail Digital' }}
                            </h3>
                        </div>
                        <span class="font-mono text-xs px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 font-bold border border-zinc-200 dark:border-zinc-700">Spark • Lite • Pro • Ultimate</span>
                    </div>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-4 leading-relaxed font-sans">
                        {{ $isEn 
                            ? 'Instant downloadable blueprint specifications, PostgreSQL ULID schemas, OpenAPI 3.1 DDL, and AI coding directives for developers and founders building with their own team.' 
                            : 'Dokumen cetak biru spesifikasi PRD siap pakai, DDL PostgreSQL Strict ULID, OpenAPI 3.1, dan aturan coding AI untuk developer atau founder yang membangun dengan tim internal sendiri.' 
                        }}
                    </p>
                    <div class="space-y-2 font-mono text-xs">
                        <div class="flex items-start gap-2">
                            <span class="text-emerald-500 font-bold">✓</span>
                            <span><strong>{{ $isEn ? 'Rights:' : 'Hak:' }}</strong> {{ $isEn ? 'Lifetime commercial license ownership, royalty-free usage, instant digital asset download.' : 'Kepemilikan lisensi komersial seumur hidup, bebas royalti, unduhan berkas instan.' }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-amber-500 font-bold">!</span>
                            <span><strong>{{ $isEn ? 'Obligations:' : 'Kewajiban:' }}</strong> {{ $isEn ? 'Self-managed coding execution, local file backups, strict ban on raw template resale.' : 'Eksekusi coding mandiri, backup lokal berkas, dilarang menjual kembali file mentah.' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Studio Card -->
                <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 rounded-none shadow-xs">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        <div>
                            <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 uppercase">
                                {{ $isEn ? 'Turnkey Track // Full Custom Studio' : 'Jalur Turnkey // Full Custom' }}
                            </span>
                            <h3 class="text-lg font-black uppercase text-zinc-900 dark:text-white">
                                {{ $isEn ? 'Studio Project & Partnership' : 'Proyek Studio & Kemitraan' }}
                            </h3>
                        </div>
                        <span class="font-mono text-xs px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950 text-emerald-900 dark:text-emerald-300 font-bold border border-emerald-300 dark:border-emerald-800">
                            {{ $isEn ? '5-Sprint Turnkey' : '5 Sprint Turnkey' }}
                        </span>
                    </div>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-4 leading-relaxed font-sans">
                        {{ $isEn 
                            ? '100% turnkey software development executed by Senior Lead Architect. Monolith Laravel 13, Filament v5, isolated VPS hardening, and 30-day bug warranty.' 
                            : 'Pengerjaan kode 100% tuntas oleh tim Senior Lead Architect. Monolith modern Laravel 13, Filament v5, dedicated VPS, dan garansi pemeliharaan 30 hari penuh.' 
                        }}
                    </p>
                    <div class="space-y-2 font-mono text-xs">
                        <div class="flex items-start gap-2">
                            <span class="text-emerald-500 font-bold">✓</span>
                            <span><strong>{{ $isEn ? 'Rights:' : 'Hak:' }}</strong> {{ $isEn ? '100% GitHub Private source code handover, delivery certificate, 30-day bug warranty SLA.' : 'Penyerahan 100% source code GitHub Private, sertifikat serah terima, garansi 30 hari SLA.' }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-amber-500 font-bold">!</span>
                            <span><strong>{{ $isEn ? 'Obligations:' : 'Kewajiban:' }}</strong> {{ $isEn ? 'Digital contract e-signature, 50% DP settlement, weekly UAT participation, formal Change Requests.' : 'TTD digital kontrak, pelunasan DP 50%, partisipasi UAT mingguan, Change Request resmi.' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparison Table -->
            <div class="border border-zinc-200 dark:border-zinc-800 overflow-x-auto bg-white dark:bg-zinc-900 shadow-xs">
                <table class="w-full text-left font-mono text-xs border-collapse">
                    <thead>
                        <tr class="bg-zinc-100 dark:bg-zinc-850 border-b border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100">
                            <th class="p-3 w-1/4 uppercase">{{ $isEn ? 'Governance Parameter' : 'Parameter Tata Kelola' }}</th>
                            <th class="p-3 w-3/8 uppercase">{{ $isEn ? 'Digital Retail License Track' : 'Jalur Lisensi Retail Digital' }}</th>
                            <th class="p-3 w-3/8 uppercase">{{ $isEn ? 'Turnkey Studio Contract Track' : 'Jalur Kontrak Studio Turnkey' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-zinc-700 dark:text-zinc-300">
                        <tr>
                            <td class="p-3 font-bold text-zinc-900 dark:text-white">{{ $isEn ? 'Access & Timeline' : 'Pemberian Akses & Waktu' }}</td>
                            <td class="p-3">{{ $isEn ? 'Instant access immediately upon verified Midtrans payment.' : 'Seketika (Instant) setelah pembayaran Midtrans terverifikasi.' }}</td>
                            <td class="p-3">{{ $isEn ? 'Structured per booked sprint batch (30 business days / 5 sprints).' : 'Sesuai slot sprint terpesan (30 hari kerja / 5 sprint).' }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-zinc-900 dark:text-white">{{ $isEn ? 'Intellectual Property (IP)' : 'Kepemilikan Hak Cipta (IP)' }}</td>
                            <td class="p-3">{{ $isEn ? 'Lifetime commercial license on specification; free to build derivative software.' : 'Lisensi komersial seumur hidup atas spesifikasi; bebas bangun software turunan.' }}</td>
                            <td class="p-3">{{ $isEn ? '100% GitHub repository source code IP transferred to Client upon final settlement.' : '100% Hak cipta kode repositori GitHub diserahkan penuh ke Klien pascapelunasan.' }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-zinc-900 dark:text-white">{{ $isEn ? 'Warranty & Bug SLA' : 'Garansi & SLA Bug' }}</td>
                            <td class="p-3">{{ $isEn ? '24-hour file replacement guarantee if downloaded archive is corrupted.' : '24 Jam penggantian file jika terbukti rusak atau corrupt saat diunduh.' }}</td>
                            <td class="p-3">{{ $isEn ? '30-calendar-day warranty for defect fixes & gratis emergency SLA post-production.' : '30 Hari Kalender garansi perbaikan bug & SLA gratis pascaproduksi.' }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-zinc-900 dark:text-white">{{ $isEn ? 'Payment Structure' : 'Metode Pembayaran' }}</td>
                            <td class="p-3">{{ $isEn ? '100% upfront settlement via Midtrans Snap (QRIS, VA, Card).' : 'Bayar penuh 100% via Midtrans Snap (QRIS, VA, Kartu Kredit).' }}</td>
                            <td class="p-3">{{ $isEn ? '50% Down Payment via Midtrans Snap, 50% final installment upon handover.' : 'Termin Uang Muka DP 50% via Midtrans Snap, pelunasan 50% saat serah terima.' }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-zinc-900 dark:text-white">{{ $isEn ? 'Scope Alteration' : 'Perubahan Fitur (Scope)' }}</td>
                            <td class="p-3">{{ $isEn ? 'Self-service customization via interactive questionnaire dashboard.' : 'Tweak mandiri melalui formulir interaktif di dasbor.' }}</td>
                            <td class="p-3">{{ $isEn ? 'Scope Freeze; new features submitted via formal Addendum / Change Request.' : 'Scope Freeze; fitur baru diajukan melalui Addendum / Change Request resmi.' }}</td>
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
                    {{ $isEn ? 'Rights & Responsibilities of Retail License Buyers' : 'Hak & Tanggung Jawab Pembeli Lisensi Retail' }}
                </h2>
                <div class="space-y-6 text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed font-sans">
                    <div class="border-l-2 border-emerald-500 pl-4 space-y-2">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase font-mono text-xs">
                            {{ $isEn ? 'YOUR RIGHTS AS A LICENSE OWNER:' : 'HAK-HAK ANDA SEBAGAI PEMILIK LISENSI:' }}
                        </h4>
                        <ul class="list-disc list-inside space-y-1 text-xs font-mono">
                            <li>{{ $isEn ? 'Right to download complete deliverables (PRD PDF, OpenAPI 3.1 YAML/JSON, PostgreSQL DDL SQL).' : 'Hak mengunduh berkas lengkap (PRD PDF, OpenAPI 3.1 YAML/JSON, DDL SQL PostgreSQL).' }}</li>
                            <li>{{ $isEn ? 'Right to build and commercialize software applications without paying royalties to Neriah Pro.' : 'Hak membangun dan mengkomersialkan aplikasi perangkat lunak tanpa membayar royalti ke Neriah Pro.' }}</li>
                            <li>{{ $isEn ? 'Right to utilize AI Agent directives (.cursorrules, AGENTS.md) in Claude, Cursor, ChatGPT, or Windsurf.' : 'Hak menggunakan prompt AI Agent (.cursorrules, AGENTS.md) pada Claude, Cursor, ChatGPT, atau Windsurf.' }}</li>
                            <li>{{ $isEn ? 'Right to obtain official tax invoices and receipts backed by SHA-256 seal in the client portal.' : 'Hak memperoleh faktur pajak dan kwitansi resmi berstempel SHA-256 di portal pelanggan.' }}</li>
                            <li>{{ $isEn ? 'Ultimate Tier Exclusive: Right to a 1-on-1 architecture consultation call (60 minutes) via Google Meet with Senior Architect.' : 'Khusus Paket Ultimate: Hak sesi konsultasi arsitektur 1-on-1 (60 menit) via Google Meet bersama Senior Architect.' }}</li>
                        </ul>
                    </div>

                    <div class="border-l-2 border-amber-500 pl-4 space-y-2">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase font-mono text-xs">
                            {{ $isEn ? 'OBLIGATIONS & RESTRICTIONS OF LICENSE OWNERS:' : 'KEWAJIBAN & BATASAN PEMILIK LISENSI:' }}
                        </h4>
                        <ul class="list-disc list-inside space-y-1 text-xs font-mono">
                            <li>{{ $isEn ? 'Obligation to maintain local backups of downloaded assets (Neriah Pro maintains 365-day cloud backups).' : 'Wajib menyimpan cadangan lokal berkas yang diunduh (Neriah Pro menyimpan data cadangan cloud selama 365 hari).' }}</li>
                            <li>{{ $isEn ? 'STRICT BAN on reselling raw PRD blueprint templates as competing products or public redistributions.' : 'DILARANG KERAS menjual kembali (resell) file template mentah PRD sebagai produk kompetitor.' }}</li>
                            <li>{{ $isEn ? 'Clients assume full responsibility for software implementation when choosing self-service retail without studio services.' : 'Klien bertanggung jawab penuh atas implementasi coding mandiri apabila memilih jalur retail tanpa jasa studio.' }}</li>
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
                            {{ $isEn ? 'CLIENT RIGHTS IN STUDIO PARTNERSHIPS:' : 'HAK KLIEN PROYEK KEMITRAAN:' }}
                        </h4>
                        <ul class="list-disc list-inside space-y-1 text-xs font-mono">
                            <li>{{ $isEn ? 'Right to full 5-sprint engineering execution adhering strictly to the locked PRD specifications.' : 'Hak atas pengerjaan rekayasa penuh 5 sprint sesuai spesifikasi PRD yang telah dikunci.' }}</li>
                            <li>{{ $isEn ? 'Right to 100% transfer of source code ownership into Client-owned private GitHub organization repositories.' : 'Hak atas transfer 100% kepemilikan source code di GitHub Private Repository milik organisasi Klien.' }}</li>
                            <li>{{ $isEn ? 'Right to monitor real-time daily progress via the Master Gantt Timeline in the customer portal.' : 'Hak memantau progress real-time setiap hari melalui Master Gantt Timeline di portal pelanggan.' }}</li>
                            <li>{{ $isEn ? 'Right to provision of isolated server infrastructure (Dedicated VPS Hardening) and SSL configuration.' : 'Hak atas penyediaan server cloud terisolasi (Dedicated VPS Hardening) dan konfigurasi SSL.' }}</li>
                            <li>{{ $isEn ? 'Right to 30 calendar days of post-production warranty for defect fixes and logic revisions free of charge.' : 'Hak atas 30 hari kalender garansi perbaikan bug dan cacat logika gratis pascarilis produksi.' }}</li>
                        </ul>
                    </div>

                    <div class="border-l-2 border-amber-500 pl-4 space-y-2">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase font-mono text-xs">
                            {{ $isEn ? 'CLIENT COMMITMENTS & OBLIGATIONS:' : 'KEWAJIBAN & KOMITMEN KLIEN:' }}
                        </h4>
                        <ul class="list-disc list-inside space-y-1 text-xs font-mono">
                            <li>{{ $isEn ? 'Obligation to execute the digital partnership agreement with e-signature prior to Sprint 1 kickoff.' : 'Wajib membubuhkan tanda tangan digital pada Kontrak Kerja Sama sebelum sprint 1 dimulai.' }}</li>
                            <li>{{ $isEn ? 'Obligation to complete 50% Down Payment (DP) via Midtrans Snap to lock engineering batch capacity.' : 'Wajib menyelesaikan pembayaran Uang Muka (DP 50%) via Midtrans Snap untuk mengunci slot batch engineer.' }}</li>
                            <li>{{ $isEn ? 'Obligation to submit brand assets, cloud access, or seed data within the first 5 calendar days.' : 'Wajib menyerahkan materi branding, akun cloud, atau data sampel awal dalam 5 hari pertama.' }}</li>
                            <li>{{ $isEn ? 'Obligation to actively participate in weekly UAT milestone sign-offs.' : 'Wajib berpartisipasi aktif dalam sesi UAT mingguan dan memberikan persetujuan milestone.' }}</li>
                            <li>{{ $isEn ? 'Adherence to Scope Freeze protocol: Scope modifications outside the signed PRD require formal Addendum processing.' : 'Memahami prinsip Scope Freeze: Penambahan fitur di luar dokumen PRD diproses via Addendum terpisah.' }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- TAB 4: SLA, GARANSI & MIDTRANS SAFETY                             -->
        <!-- ================================================================= -->
        <div x-show="activeTab === 'sla'" x-cloak class="space-y-6">
            <div class="border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 bg-white dark:bg-zinc-900 shadow-xs">
                <h2 class="text-xl font-black uppercase text-zinc-900 dark:text-white mb-4">
                    {{ $isEn ? 'SLA Standards, 30-Day Bug Warranty, and Midtrans Payment Protection' : 'Standar SLA, Garansi Bug 30 Hari, dan Proteksi Pembayaran Midtrans' }}
                </h2>
                <div class="space-y-4 text-xs font-mono text-zinc-700 dark:text-zinc-300 leading-relaxed">
                    <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 space-y-2">
                        <span class="font-bold text-zinc-900 dark:text-white uppercase">
                            {{ $isEn ? '1. 30-DAY WARRANTY & DEFECT RESOLUTION SLA' : '1. GARANSI 30 HARI & SLA PENANGANAN MASALAH' }}
                        </span>
                        <p>
                            {{ $isEn 
                                ? 'Upon production deployment and handover, Clients receive a 30-calendar-day warranty period. Critical severity bugs (system down / checkout halt) are responded to within < 4 business hours.' 
                                : 'Setelah sistem diserahterimakan di server produksi, Klien memperoleh masa garansi 30 hari kalender. Setiap laporan bug dengan tingkat keparahan tinggi (sistem down / transaksi terhenti) akan ditanggapi dalam waktu < 4 jam pada hari kerja.' 
                            }}
                        </p>
                    </div>

                    <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 space-y-2">
                        <span class="font-bold text-zinc-900 dark:text-white uppercase">
                            {{ $isEn ? '2. MIDTRANS TRANSACTION PROTECTION & IDEMPOTENCY' : '2. PROTEKSI TRANSAKSI MIDTRANS & IDEMPOTENCY' }}
                        </span>
                        <p>
                            {{ $isEn 
                                ? 'All transactions are processed through authorized Midtrans Snap gateways. Our system enforces SHA-256 idempotency webhooks to guarantee zero double billing and automated tax invoice reconciliation.' 
                                : 'Seluruh pembayaran di Neriah Pro diproses melalui gerbang berizin Midtrans Snap. Sistem kami menerapkan webhook idempotency berbasis hash SHA-256 untuk memastikan Klien tidak pernah ditagih dua kali dan seluruh mutasi tercatat otomatis di faktur pajak.' 
                            }}
                        </p>
                    </div>

                    <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 space-y-2">
                        <span class="font-bold text-zinc-900 dark:text-white uppercase">
                            {{ $isEn ? '3. REFUND & DISPUTE RESOLUTION PROCEDURE' : '3. ALUR PERMINTAAN REFUND & SENGKETA' }}
                        </span>
                        <p>
                            @if($isEn)
                                If technical delivery failure occurs or critical defects cannot be resolved within 48 hours, Clients are entitled to submit refund claims via portal support tickets or by emailing directly to <a href="mailto:{{ $supportEmail }}" class="text-emerald-500 underline font-bold">{{ $supportEmail }}</a>.
                            @else
                                Apabila terdapat kegagalan penerbitan berkas digital atau ketidaksesuaian kritis yang tidak dapat diperbaiki dalam 48 jam, Klien berhak mengajukan pengembalian dana melalui tombol tiket bantuan di portal pelanggan atau email resmi ke <a href="mailto:{{ $supportEmail }}" class="text-emerald-500 underline font-bold">{{ $supportEmail }}</a>.
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
        'whatsappNumber' => $getSettingVal('company_whatsapp', '628123456789'),
        'supportEmail' => $supportEmail,
        'currentLocale' => $currentLang,
    ])

</body>
</html>
