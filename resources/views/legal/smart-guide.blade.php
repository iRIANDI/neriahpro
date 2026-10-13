@php
    $currentLang = $locale ?? (request()->cookie('neriah_locale', app()->getLocale()) === 'en' ? 'en' : 'id');
    $isEn = $currentLang === 'en';
    $companyName = \App\Models\CmsGlobalSetting::getVal('company_name', 'Neriah Pro Digital Services');
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
                <span>NERIAH<span class="text-emerald-500">PRO</span> // SMART GUIDE</span>
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
    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-10 sm:py-12 flex-1 w-full" x-data="{ activeTab: 'overview' }">
        
        <!-- Breadcrumb & Tag -->
        <div class="mb-6 flex items-center gap-2 font-mono text-xs text-zinc-500">
            <a href="/" class="hover:text-emerald-500">{{ $isEn ? 'Home' : 'Beranda' }}</a>
            <span>/</span>
            <span class="text-zinc-900 dark:text-zinc-100 font-bold">{{ $title }}</span>
        </div>

        <!-- Hero Title Box -->
        <div class="border-l-4 border-emerald-500 pl-4 sm:pl-6 py-2 mb-10">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950/80 text-emerald-950 dark:text-emerald-200 border border-emerald-400 dark:border-emerald-700 text-[10px] font-mono font-bold uppercase tracking-wider mb-2">
                <span>CLIENT GOVERNANCE // HAK &amp; KEWAJIBAN RETENSI TINGGI</span>
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
                @click="activeTab = 'overview'" 
                :class="activeTab === 'overview' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'"
                class="px-4 py-2 font-mono text-xs font-bold border transition rounded-none cursor-pointer"
            >
                {{ $isEn ? '1. Comparison Matrix' : '1. Matriks Hak vs Kewajiban' }}
            </button>
            <button 
                type="button"
                @click="activeTab = 'retail'" 
                :class="activeTab === 'retail' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'"
                class="px-4 py-2 font-mono text-xs font-bold border transition rounded-none cursor-pointer"
            >
                {{ $isEn ? '2. Retail Digital Track' : '2. Jalur Lisensi Retail' }}
            </button>
            <button 
                type="button"
                @click="activeTab = 'studio'" 
                :class="activeTab === 'studio' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'"
                class="px-4 py-2 font-mono text-xs font-bold border transition rounded-none cursor-pointer"
            >
                {{ $isEn ? '3. Studio MVP Track' : '3. Jalur Kontrak Studio' }}
            </button>
            <button 
                type="button"
                @click="activeTab = 'sla'" 
                :class="activeTab === 'sla' ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-zinc-900 dark:border-white' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500'"
                class="px-4 py-2 font-mono text-xs font-bold border transition rounded-none cursor-pointer"
            >
                {{ $isEn ? '4. SLA & Midtrans Safety' : '4. SLA, Garansi & Midtrans' }}
            </button>
        </div>

        <!-- ================================================================= -->
        <!-- TAB 1: QUICK OVERVIEW & COMPARISON MATRIX                         -->
        <!-- ================================================================= -->
        <div x-show="activeTab === 'overview'" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Retail Card -->
                <div class="border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 p-6 rounded-none">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        <div>
                            <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 uppercase">Jalur Mandiri // Self-Service</span>
                            <h3 class="text-lg font-black uppercase text-zinc-900 dark:text-white">Lisensi Retail Digital</h3>
                        </div>
                        <span class="font-mono text-xs px-2.5 py-1 bg-zinc-200 dark:bg-zinc-800 font-bold">Spark • Lite • Pro • Ultimate</span>
                    </div>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-4 leading-relaxed">
                        {{ $isEn 
                            ? 'Instant downloadable blueprint specifications, PostgreSQL ULID schemas, OpenAPI 3.1 DDL, and AI coding directives for developers and founders building with their own team.' 
                            : 'Dokumen cetak biru spesifikasi PRD siap pakai, DDL PostgreSQL Strict ULID, OpenAPI 3.1, dan aturan coding AI untuk developer atau founder yang membangun dengan tim internal sendiri.' 
                        }}
                    </p>
                    <div class="space-y-2 font-mono text-xs">
                        <div class="flex items-start gap-2">
                            <span class="text-emerald-500 font-bold">✓</span>
                            <span><strong>Hak:</strong> Kepemilikan lisensi komersial seumur hidup, bebas royalti, unduhan berkas instan.</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-amber-500 font-bold">!</span>
                            <span><strong>Kewajiban:</strong> Eksekusi coding mandiri, backup lokal berkas, dilarang menjual kembali file mentah.</span>
                        </div>
                    </div>
                </div>

                <!-- Studio Card -->
                <div class="border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 p-6 rounded-none">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        <div>
                            <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 uppercase">Jalur Turnkey // Full Custom</span>
                            <h3 class="text-lg font-black uppercase text-zinc-900 dark:text-white">Proyek Studio &amp; Kemitraan</h3>
                        </div>
                        <span class="font-mono text-xs px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950 text-emerald-900 dark:text-emerald-300 font-bold">5 Sprint Turnkey</span>
                    </div>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-4 leading-relaxed">
                        {{ $isEn 
                            ? '100% turnkey software development executed by Senior Lead Architect. Monolith Laravel 13, Filament v5, isolated VPS hardening, and 30-day bug warranty.' 
                            : 'Pengerjaan kode 100% tuntas oleh tim Senior Lead Architect. Monolith modern Laravel 13, Filament v5, dedicated VPS, dan garansi pemeliharaan 30 hari penuh.' 
                        }}
                    </p>
                    <div class="space-y-2 font-mono text-xs">
                        <div class="flex items-start gap-2">
                            <span class="text-emerald-500 font-bold">✓</span>
                            <span><strong>Hak:</strong> Penyerahan 100% source code GitHub Private, sertifikat serah terima, garansi 30 hari SLA.</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-amber-500 font-bold">!</span>
                            <span><strong>Kewajiban:</strong> TTD digital kontrak, pelunasan DP 50%, partisipasi UAT mingguan, Change Request resmi.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparison Table -->
            <div class="border border-zinc-200 dark:border-zinc-800 overflow-x-auto">
                <table class="w-full text-left font-mono text-xs border-collapse">
                    <thead>
                        <tr class="bg-zinc-100 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100">
                            <th class="p-3 w-1/4">Parameter Tata Kelola</th>
                            <th class="p-3 w-3/8">Jalur Lisensi Retail Digital</th>
                            <th class="p-3 w-3/8">Jalur Kontrak Studio Turnkey</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-zinc-700 dark:text-zinc-300">
                        <tr>
                            <td class="p-3 font-bold text-zinc-900 dark:text-white">Pemberian Akses &amp; Waktu</td>
                            <td class="p-3">Seketika (Instant) setelah pembayaran Midtrans terverifikasi.</td>
                            <td class="p-3">Sesuai slot sprint terpesan (30 hari kerja / 5 sprint).</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-zinc-900 dark:text-white">Kepemilikan Hak Cipta (IP)</td>
                            <td class="p-3">Lisensi komersial seumur hidup atas spesifikasi; bebas bangun software turunan.</td>
                            <td class="p-3">100% Hak cipta kode repositori GitHub diserahkan penuh ke Klien pascapelunasan.</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-zinc-900 dark:text-white">Garansi &amp; SLA Bug</td>
                            <td class="p-3">24 Jam penggantian file jika terbukti rusak atau corrupt saat diunduh.</td>
                            <td class="p-3">30 Hari Kalender garansi perbaikan bug &amp; SLA gratis pascaproduksi.</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-zinc-900 dark:text-white">Metode Pembayaran</td>
                            <td class="p-3">Bayar penuh 100% via Midtrans Snap (QRIS, VA, Kartu Kredit).</td>
                            <td class="p-3">Termin Uang Muka DP 50% via Midtrans Snap, pelunasan 50% saat serah terima.</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-zinc-900 dark:text-white">Perubahan Fitur (Scope)</td>
                            <td class="p-3">Tweak mandiri melalui formulir interaktif di dasbor.</td>
                            <td class="p-3">Scope Freeze; fitur baru diajukan melalui Addendum / Change Request resmi.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- TAB 2: RETAIL DIGITAL TRACK                                       -->
        <!-- ================================================================= -->
        <div x-show="activeTab === 'retail'" class="space-y-6">
            <div class="border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 bg-white dark:bg-zinc-900">
                <h2 class="text-xl font-black uppercase text-zinc-900 dark:text-white mb-4">
                    Hak &amp; Tanggung Jawab Pembeli Lisensi Retail
                </h2>
                <div class="space-y-6 text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed font-sans">
                    <div class="border-l-2 border-emerald-500 pl-4 space-y-2">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase font-mono text-xs">HAK-HAK ANDA SEBAGAI PEMILIK LISENSI:</h4>
                        <ul class="list-disc list-inside space-y-1 text-xs font-mono">
                            <li>Hak mengunduh berkas lengkap (PRD PDF, OpenAPI 3.1 YAML/JSON, DDL SQL PostgreSQL).</li>
                            <li>Hak membangun dan mengkomersialkan aplikasi perangkat lunak tanpa membayar royalti ke Neriah Pro.</li>
                            <li>Hak menggunakan prompt AI Agent (.cursorrules, AGENTS.md) pada Claude, Cursor, ChatGPT, atau Windsurf.</li>
                            <li>Hak memperoleh faktur pajak dan kwitansi resmi berstempel SHA-256 di portal pelanggan.</li>
                            <li>Khusus Paket Ultimate: Hak sesi konsultasi arsitektur 1-on-1 (60 menit) via Google Meet bersama Senior Architect.</li>
                        </ul>
                    </div>

                    <div class="border-l-2 border-amber-500 pl-4 space-y-2">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase font-mono text-xs">KEWAJIBAN &amp; BATASAN PEMILIK LISENSI:</h4>
                        <ul class="list-disc list-inside space-y-1 text-xs font-mono">
                            <li>Wajib menyimpan cadangan lokal berkas yang diunduh (Neriah Pro menyimpan data cadangan cloud selama 365 hari).</li>
                            <li>DILARANG KERAS menjual kembali (resell) file template mentah PRD sebagai produk kompetitor.</li>
                            <li>Klien bertanggung jawab penuh atas implementasi coding mandiri apabila memilih jalur retail tanpa jasa studio.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- TAB 3: STUDIO MVP TRACK                                           -->
        <!-- ================================================================= -->
        <div x-show="activeTab === 'studio'" class="space-y-6">
            <div class="border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 bg-white dark:bg-zinc-900">
                <h2 class="text-xl font-black uppercase text-zinc-900 dark:text-white mb-4">
                    Hak &amp; Tata Kelola Kontrak Proyek Studio MVP
                </h2>
                <div class="space-y-6 text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed font-sans">
                    <div class="border-l-2 border-emerald-500 pl-4 space-y-2">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase font-mono text-xs">HAK KLIEN PROYEK KEMITRAAN:</h4>
                        <ul class="list-disc list-inside space-y-1 text-xs font-mono">
                            <li>Hak atas pengerjaan rekayasa penuh 5 sprint sesuai spesifikasi PRD yang telah dikunci.</li>
                            <li>Hak atas transfer 100% kepemilikan source code di GitHub Private Repository milik organisasi Klien.</li>
                            <li>Hak memantau progress real-time setiap hari melalui Master Gantt Timeline di portal pelanggan.</li>
                            <li>Hak atas penyediaan server cloud terisolasi (Dedicated VPS Hardening) dan konfigurasi SSL.</li>
                            <li>Hak atas 30 hari kalender garansi perbaikan bug dan cacat logika gratis pascarilis produksi.</li>
                        </ul>
                    </div>

                    <div class="border-l-2 border-amber-500 pl-4 space-y-2">
                        <h4 class="font-bold text-zinc-900 dark:text-white uppercase font-mono text-xs">KEWAJIBAN &amp; KOMITMEN KLIEN:</h4>
                        <ul class="list-disc list-inside space-y-1 text-xs font-mono">
                            <li>Wajib membubuhkan tanda tangan digital pada Kontrak Kerja Sama sebelum sprint 1 dimulai.</li>
                            <li>Wajib menyelesaikan pembayaran Uang Muka (DP 50%) via Midtrans Snap untuk mengunci slot batch engineer.</li>
                            <li>Wajib menyerahkan materi branding, akun cloud, atau data sampel awal dalam 5 hari pertama.</li>
                            <li>Wajib berpartisipasi aktif dalam sesi UAT mingguan dan memberikan persetujuan milestone.</li>
                            <li>Memahami prinsip Scope Freeze: Penambahan fitur di luar dokumen PRD diproses via Addendum terpisah.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- TAB 4: SLA, GARANSI & MIDTRANS SAFETY                             -->
        <!-- ================================================================= -->
        <div x-show="activeTab === 'sla'" class="space-y-6">
            <div class="border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 bg-white dark:bg-zinc-900">
                <h2 class="text-xl font-black uppercase text-zinc-900 dark:text-white mb-4">
                    Standar SLA, Garansi Bug 30 Hari, dan Proteksi Pembayaran Midtrans
                </h2>
                <div class="space-y-4 text-xs font-mono text-zinc-700 dark:text-zinc-300 leading-relaxed">
                    <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 space-y-2">
                        <span class="font-bold text-zinc-900 dark:text-white uppercase">1. GARANSI 30 HARI &amp; SLA PENANGANAN MASALAH</span>
                        <p>Setelah sistem diserahterimakan di server produksi, Klien memperoleh masa garansi 30 hari kalender. Setiap laporan bug dengan tingkat keparahan tinggi (sistem down / transaksi terhenti) akan ditanggapi dalam waktu &lt; 4 jam pada hari kerja.</p>
                    </div>

                    <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 space-y-2">
                        <span class="font-bold text-zinc-900 dark:text-white uppercase">2. PROTEKSI TRANSAKSI MIDTRANS &amp; IDEMPOTENCY</span>
                        <p>Seluruh pembayaran di Neriah Pro diproses melalui gerbang berizin Midtrans Snap. Sistem kami menerapkan webhook idempotency berbasis hash SHA-256 untuk memastikan Klien tidak pernah ditagih dua kali dan seluruh mutasi tercatat otomatis di faktur pajak.</p>
                    </div>

                    <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 space-y-2">
                        <span class="font-bold text-zinc-900 dark:text-white uppercase">3. ALUR PERMINTAAN REFUND &amp; SENGKETA</span>
                        <p>Apabila terdapat kegagalan penerbitan berkas digital atau ketidaksesuaian kritis yang tidak dapat diperbaiki dalam 48 jam, Klien berhak mengajukan pengembalian dana melalui tombol tiket bantuan di portal pelanggan atau email resmi ke <a href="mailto:admin@neriahpro.com" class="text-emerald-500 underline font-bold">admin@neriahpro.com</a>.</p>
                    </div>
                </div>
            </div>
        </div>

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
                    {{ $isEn ? 'Refund Policy' : 'Kebijakan Refund' }}
                </a>
            </div>
        </div>
    </footer>

</body>
</html>
