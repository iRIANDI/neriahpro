@php
    $isEn = app()->getLocale() === 'en';
    $whatsappNumber = \App\Models\CmsGlobalSetting::getVal('company_whatsapp', '628123456789');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>neriahpro.com - {{ $isEn ? 'Client Portal & Project Workspace' : 'Portal Pelanggan & Workspace Proyek' }}</title>
    <meta name="description" content="{{ $isEn ? 'Unified client workspace to track software sprints, access lifetime license downloads, and view tax invoices.' : 'Workspace terpadu pelanggan untuk memantau sprint software, mengunduh lisensi seumur hidup, dan mengakses faktur pajak.' }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Vite Styles & Scripts -->
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js & Tab Navigation Support -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
        }

        function customerDashboardApp() {
            return {
                currentTab: (window.location.hash ? window.location.hash.replace('#', '') : 'overview'),
                
                init() {
                    const allowed = ['overview', 'projects', 'licenses', 'billing', 'assets', 'account'];
                    if (!allowed.includes(this.currentTab)) {
                        this.currentTab = 'overview';
                    }
                    window.addEventListener('hashchange', () => {
                        const hash = window.location.hash.replace('#', '');
                        if (allowed.includes(hash)) {
                            this.currentTab = hash;
                        }
                    });
                },

                setTab(tab) {
                    this.currentTab = tab;
                    window.location.hash = tab;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                async logoutCustomer() {
                    try {
                        await fetch('/api/customer/logout', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            }
                        });
                        window.location.href = '/customer/login';
                    } catch(e) {
                        window.location.href = '/';
                    }
                }
            };
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('customerDashboardApp', customerDashboardApp);
        });
    </script>
</head>
<body class="bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen transition-colors duration-200" x-data="customerDashboardApp()">

    <!-- 1. TOP UTILITY HEADER -->
    <header class="border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <!-- Left: Brand & Portal Badge -->
            <div class="flex items-center gap-3">
                <a href="/" class="flex items-center gap-2 group">
                    <span class="w-8 h-8 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-black text-sm flex items-center justify-center rounded-none shadow-xs group-hover:scale-105 transition-transform">
                        N
                    </span>
                    <span class="font-mono text-sm font-black tracking-tight text-zinc-900 dark:text-white uppercase">
                        Neriah<span class="text-emerald-500">Pro</span>
                    </span>
                </a>
                <span class="text-zinc-300 dark:text-zinc-700">/</span>
                <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold uppercase tracking-wider rounded-none border border-emerald-500/20">
                    {{ $isEn ? 'CLIENT WORKSPACE' : 'WORKSPACE PELANGGAN' }}
                </span>
            </div>

            <!-- Right: Actions & User Details -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- User Email Badge -->
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-xs font-mono">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-zinc-700 dark:text-zinc-300 font-bold truncate max-w-[200px]">{{ $user->email }}</span>
                </div>

                <!-- Dark / Light Mode -->
                <button 
                    type="button" 
                    onclick="toggleTheme()" 
                    class="p-2 border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition rounded-none bg-zinc-50 dark:bg-zinc-800 cursor-pointer"
                    title="Toggle Theme"
                >
                    <span class="dark:hidden">🌙</span>
                    <span class="hidden dark:inline">☀️</span>
                </button>

                <!-- New Project / Blueprint CTA -->
                <a 
                    href="/blueprint" 
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-black font-mono text-xs font-bold uppercase tracking-wider transition rounded-none shadow-xs"
                >
                    <span>+ {{ $isEn ? 'NEW PROJECT' : 'PROYEK BARU' }}</span>
                </a>

                <!-- Logout -->
                <button 
                    type="button" 
                    @click="logoutCustomer()" 
                    class="px-3 py-1.5 border border-zinc-200 dark:border-zinc-700 hover:border-rose-500 text-zinc-600 dark:text-zinc-400 hover:text-rose-500 text-xs font-mono font-bold transition rounded-none bg-zinc-50 dark:bg-zinc-800 cursor-pointer"
                >
                    {{ $isEn ? 'LOGOUT' : 'KELUAR' }}
                </button>
            </div>
        </div>
    </header>

    <!-- 2. MAIN MODULAR MULTI-PANE CONTAINER (ANTI-FATIGUE SCROLL ARCHITECTURE) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- MODULAR HORIZONTAL / VERTICAL NAVIGATION PILLS -->
        <nav class="flex items-center gap-1.5 sm:gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-6 overflow-x-auto font-mono text-xs no-scrollbar">
            <!-- Tab 1: Overview -->
            <button 
                type="button"
                @click="setTab('overview')"
                :class="currentTab === 'overview' 
                    ? 'bg-zinc-900 text-white dark:bg-white dark:text-black font-bold shadow-xs' 
                    : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white border border-zinc-200 dark:border-zinc-800'"
                class="px-3.5 py-2 transition rounded-none uppercase flex items-center gap-2 whitespace-nowrap cursor-pointer"
            >
                <span>📊</span>
                <span>{{ $isEn ? 'Overview' : 'Ringkasan Akun' }}</span>
            </button>

            <!-- Tab 2: Studio Projects -->
            <button 
                type="button"
                @click="setTab('projects')"
                :class="currentTab === 'projects' 
                    ? 'bg-emerald-600 text-white dark:bg-emerald-500 dark:text-black font-bold shadow-xs' 
                    : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white border border-zinc-200 dark:border-zinc-800'"
                class="px-3.5 py-2 transition rounded-none uppercase flex items-center gap-2 whitespace-nowrap cursor-pointer"
            >
                <span>🚀</span>
                <span>{{ $isEn ? 'Studio Projects' : 'Proyek Studio' }}</span>
                <span class="px-1.5 py-0.2 bg-zinc-200/50 dark:bg-zinc-800 text-[10px] font-bold">
                    {{ $metrics['total_studio'] }}
                </span>
            </button>

            <!-- Tab 3: Retail Downloads -->
            <button 
                type="button"
                @click="setTab('licenses')"
                :class="currentTab === 'licenses' 
                    ? 'bg-cyan-600 text-white dark:bg-cyan-400 dark:text-black font-bold shadow-xs' 
                    : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white border border-zinc-200 dark:border-zinc-800'"
                class="px-3.5 py-2 transition rounded-none uppercase flex items-center gap-2 whitespace-nowrap cursor-pointer"
            >
                <span>📦</span>
                <span>{{ $isEn ? 'Retail Licenses & Downloads' : 'Lisensi Retail & Unduhan' }}</span>
                <span class="px-1.5 py-0.2 bg-zinc-200/50 dark:bg-zinc-800 text-[10px] font-bold">
                    {{ $metrics['total_retail'] }}
                </span>
            </button>

            <!-- Tab 4: Invoices & Receipts -->
            <button 
                type="button"
                @click="setTab('billing')"
                :class="currentTab === 'billing' 
                    ? 'bg-amber-600 text-white dark:bg-amber-500 dark:text-black font-bold shadow-xs' 
                    : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white border border-zinc-200 dark:border-zinc-800'"
                class="px-3.5 py-2 transition rounded-none uppercase flex items-center gap-2 whitespace-nowrap cursor-pointer"
            >
                <span>🧾</span>
                <span>{{ $isEn ? 'Tax Invoices & Receipts' : 'Faktur Pajak & Kwitansi' }}</span>
                <span class="px-1.5 py-0.2 bg-zinc-200/50 dark:bg-zinc-800 text-[10px] font-bold">
                    {{ $transactions->count() }}
                </span>
            </button>

            <!-- Tab 5: Infrastructure Assets (Ready for expansion) -->
            <button 
                type="button"
                @click="setTab('assets')"
                :class="currentTab === 'assets' 
                    ? 'bg-indigo-600 text-white dark:bg-indigo-400 dark:text-black font-bold shadow-xs' 
                    : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white border border-zinc-200 dark:border-zinc-800'"
                class="px-3.5 py-2 transition rounded-none uppercase flex items-center gap-2 whitespace-nowrap cursor-pointer"
            >
                <span>🌐</span>
                <span>{{ $isEn ? 'Domain & VPS Hosting' : 'Domain & Server Hosting' }}</span>
                @if($hostingAssets->isNotEmpty())
                    <span class="px-1.5 py-0.2 bg-zinc-200/50 dark:bg-zinc-800 text-[10px] font-bold">
                        {{ $hostingAssets->count() }}
                    </span>
                @endif
            </button>

            <!-- Tab 6: Account & Security -->
            <button 
                type="button"
                @click="setTab('account')"
                :class="currentTab === 'account' 
                    ? 'bg-zinc-700 text-white dark:bg-zinc-300 dark:text-black font-bold shadow-xs' 
                    : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white border border-zinc-200 dark:border-zinc-800'"
                class="px-3.5 py-2 transition rounded-none uppercase flex items-center gap-2 whitespace-nowrap cursor-pointer"
            >
                <span>⚙️</span>
                <span>{{ $isEn ? 'Account' : 'Pengaturan Akun' }}</span>
            </button>
        </nav>

        <!-- =================================================================== -->
        <!-- PANE 1: OVERVIEW & EXECUTIVE LAUNCHPAD                             -->
        <!-- =================================================================== -->
        <div x-show="currentTab === 'overview'" x-cloak class="space-y-6">
            
            <!-- Welcome Header Banner -->
            <div class="p-6 bg-white dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold uppercase tracking-wider mb-2">
                        <span>● {{ $isEn ? 'ACTIVE CLIENT WORKSPACE' : 'WORKSPACE KLIEN TERDAFTAR' }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-zinc-900 dark:text-white font-sans">
                        {{ $isEn ? 'Welcome back' : 'Selamat Datang' }}, {{ $user->name ?: explode('@', $user->email)[0] }}
                    </h1>
                    <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-sans mt-1 max-w-2xl leading-relaxed">
                        {{ $isEn 
                            ? 'Your modular engineering portal. Monitor active sprint timelines, access lifetime downloads, and print corporate reimbursement tax receipts without page bloat.'
                            : 'Portal rekayasa perangkat lunak terpadu Anda. Pantau timeline pengerjaan sprint, akses pusat unduhan seumur hidup, dan cetak faktur pajak resmi.' }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a 
                        href="/blueprint" 
                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-mono text-xs font-black uppercase tracking-wider transition rounded-none shadow-xs"
                    >
                        + {{ $isEn ? 'NEW BLUEPRINT' : 'BUAT PROYEK BARU' }}
                    </a>
                    <a 
                        href="https://wa.me/{{ $whatsappNumber }}?text={{ rawurlencode('Halo Lead Architect Neriah Pro, saya ingin mendiskusikan progres proyek di dashboard pelanggan kami.') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="px-4 py-2 bg-zinc-900 hover:bg-black dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white font-mono text-xs font-bold uppercase tracking-wider transition rounded-none border border-zinc-700"
                    >
                        💬 {{ $isEn ? 'WHATSAPP ARCHITECT' : 'CHAT ARCHITECT' }}
                    </a>
                </div>
            </div>

            <!-- 4 KPI Metrics Row -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs cursor-pointer hover:border-cyan-500 transition" @click="setTab('licenses')">
                    <span class="text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider block">
                        {{ $isEn ? 'DIGITAL LICENSES' : 'LISENSI DIGITAL RETAIL' }}
                    </span>
                    <span class="text-2xl sm:text-3xl font-black font-mono text-cyan-600 dark:text-cyan-400 mt-1 block">
                        {{ $metrics['total_retail'] }}
                    </span>
                    <span class="text-[10px] text-zinc-500 font-sans mt-0.5 block">
                        {{ $isEn ? 'Lifetime downloads ready →' : 'Akses unduh seumur hidup →' }}
                    </span>
                </div>

                <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs cursor-pointer hover:border-emerald-500 transition" @click="setTab('projects')">
                    <span class="text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider block">
                        {{ $isEn ? 'STUDIO PROJECTS' : 'PROYEK STUDIO AKTIF' }}
                    </span>
                    <span class="text-2xl sm:text-3xl font-black font-mono text-emerald-600 dark:text-emerald-400 mt-1 block">
                        {{ $metrics['total_studio'] }}
                    </span>
                    <span class="text-[10px] text-zinc-500 font-sans mt-0.5 block">
                        {{ $metrics['active_studio'] }} {{ $isEn ? 'in active sprint →' : 'sedang dikerjakan →' }}
                    </span>
                </div>

                <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs cursor-pointer hover:border-amber-500 transition" @click="setTab('licenses')">
                    <span class="text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider block">
                        {{ $isEn ? '30-DAY REVISION GUARANTEE' : 'GARANSI REVISI 30 HARI' }}
                    </span>
                    <span class="text-2xl sm:text-3xl font-black font-mono text-amber-500 mt-1 block">
                        ACTIVE
                    </span>
                    <span class="text-[10px] text-zinc-500 font-sans mt-0.5 block">
                        {{ $isEn ? 'Parameter refinements enabled' : 'Bebas penyesuaian parameter' }}
                    </span>
                </div>

                <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs cursor-pointer hover:border-zinc-900 dark:hover:border-zinc-500 transition" @click="setTab('billing')">
                    <span class="text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider block">
                        {{ $isEn ? 'TOTAL SETTLEMENT' : 'TOTAL TRANSAKSI LUNAS' }}
                    </span>
                    <span class="text-xl sm:text-2xl font-black font-mono text-zinc-900 dark:text-white mt-1 block truncate">
                        Rp {{ number_format($metrics['total_spend_idr'], 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] text-zinc-500 font-sans mt-0.5 block">
                        {{ $transactions->whereIn('status', ['settlement', 'capture', 'success'])->count() }} {{ $isEn ? 'official invoices →' : 'faktur resmi terbit →' }}
                    </span>
                </div>
            </div>

            <!-- Quick Launch Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- Quick Card: Latest Project Timeline Snippet -->
                <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono font-bold uppercase text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                            <span>🚀</span>
                            <span>{{ $isEn ? 'STUDIO SPRINT STATUS' : 'STATUS SPRINT STUDIO TERKINI' }}</span>
                        </span>
                        <button type="button" @click="setTab('projects')" class="text-[11px] font-mono font-bold text-zinc-500 hover:text-emerald-500 underline cursor-pointer">
                            {{ $isEn ? 'View All →' : 'Buka Semua →' }}
                        </button>
                    </div>

                    @if($studioProjects->isEmpty())
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-dashed border-zinc-200 dark:border-zinc-800 text-center text-xs text-zinc-500">
                            {{ $isEn ? 'No active studio custom engineering project yet.' : 'Belum ada proyek custom engineering aktif.' }}
                        </div>
                    @else
                        @php $firstStudio = $studioProjects->first(); @endphp
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                            <div class="flex items-center justify-between text-xs font-bold font-sans text-zinc-900 dark:text-white">
                                <span>{{ $firstStudio->nama_bisnis ?: $firstStudio->client_name }}</span>
                                <span class="text-[10px] font-mono px-1.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                    {{ $firstStudio->project_status ?: 'Active' }}
                                </span>
                            </div>
                            <div class="w-full bg-zinc-200 dark:bg-zinc-800 h-2 mt-2 rounded-none overflow-hidden">
                                <div class="bg-emerald-500 h-full" style="width: {{ $firstStudio->user_metadata['dev_progress_percent'] ?? 15 }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] font-mono text-zinc-500 mt-1.5">
                                <span>Progress: {{ $firstStudio->user_metadata['dev_progress_percent'] ?? 15 }}%</span>
                                <a href="/blueprint/{{ $firstStudio->slug }}" class="text-emerald-600 hover:underline">
                                    {{ $isEn ? 'Open PRD →' : 'Buka Dokumen PRD →' }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Quick Card: Latest Retail License Downloads -->
                <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono font-bold uppercase text-cyan-600 dark:text-cyan-400 flex items-center gap-1.5">
                            <span>📦</span>
                            <span>{{ $isEn ? 'LATEST DIGITAL LICENSE' : 'LISENSI DIGITAL TERBARU' }}</span>
                        </span>
                        <button type="button" @click="setTab('licenses')" class="text-[11px] font-mono font-bold text-zinc-500 hover:text-cyan-500 underline cursor-pointer">
                            {{ $isEn ? 'Download Center →' : 'Pusat Unduhan →' }}
                        </button>
                    </div>

                    @if($retailLicenses->isEmpty())
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-dashed border-zinc-200 dark:border-zinc-800 text-center text-xs text-zinc-500">
                            {{ $isEn ? 'No retail licenses registered to this account.' : 'Belum ada lisensi retail terdaftar di akun ini.' }}
                        </div>
                    @else
                        @php $firstRetail = $retailLicenses->first(); @endphp
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-zinc-900 dark:text-white truncate">
                                    {{ $firstRetail->nama_bisnis ?: $firstRetail->client_name }}
                                </h4>
                                <span class="text-[10px] font-mono text-zinc-500 block">
                                    Ref: {{ $firstRetail->slug }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1 text-[11px] font-mono font-bold shrink-0">
                                <a href="/blueprint/{{ $firstRetail->slug }}/download/pdf" class="px-2 py-1 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700">PDF</a>
                                <a href="/blueprint/{{ $firstRetail->slug }}/download/md" class="px-2 py-1 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700">MD</a>
                                <a href="/blueprint/{{ $firstRetail->slug }}/export/scaffold" class="px-2 py-1 bg-emerald-500 text-black">ZIP</a>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

        </div>

        <!-- =================================================================== -->
        <!-- PANE 2: STUDIO ENGINEERING PROJECTS & LIVE TIMELINES               -->
        <!-- =================================================================== -->
        <div x-show="currentTab === 'projects'" x-cloak class="space-y-6">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                <div>
                    <h2 class="text-lg font-black uppercase text-zinc-900 dark:text-white font-mono flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-none"></span>
                        <span>{{ $isEn ? 'Studio Engineering Projects & Live Sprints' : 'Proyek Rekayasa Studio & Timeline Sprint Live' }}</span>
                    </h2>
                    <p class="text-xs text-zinc-500 font-sans mt-0.5">
                        {{ $isEn ? 'Sprint milestones are automatically updated in real-time as developers mark checkpoints on the PRD.' : 'Tahapan pengerjaan sprint otomatis tersinkronisasi saat developer menyelesaikan checkpoint di PRD.' }}
                    </p>
                </div>
                <span class="text-xs font-mono font-bold text-zinc-500">
                    {{ $studioProjects->count() }} {{ $isEn ? 'Projects' : 'Proyek' }}
                </span>
            </div>

            @if($studioProjects->isEmpty())
                <div class="p-10 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-center space-y-3 rounded-none">
                    <span class="text-3xl block">📐</span>
                    <h3 class="font-bold text-sm font-mono uppercase text-zinc-900 dark:text-white">
                        {{ $isEn ? 'NO ACTIVE STUDIO ENGINEERING PROJECTS YET' : 'BELUM ADA PROYEK REKAYASA STUDIO AKTIF' }}
                    </h3>
                    <p class="text-xs text-zinc-500 max-w-md mx-auto">
                        {{ $isEn 
                            ? 'You have not reserved any Full MVP or UMKM Starter custom software engineering sprint yet.' 
                            : 'Anda belum memesan sprint pengerjaan Full MVP atau UMKM Starter. Dapatkan sistem monolit modern dengan garansi DP 50% dan kontrak hukum resmi.' }}
                    </p>
                    <a href="/pricing" class="inline-block mt-2 px-4 py-2 bg-emerald-500 text-black font-mono text-xs font-bold uppercase tracking-wider rounded-none">
                        {{ $isEn ? 'EXPLORE STUDIO PACKAGES →' : 'JELAJAHI PAKET STUDIO REKAYASA →' }}
                    </a>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($studioProjects as $project)
                        @php
                            $metadata = $project->user_metadata ?? [];
                            $checkpoints = $metadata['dev_checkpoints'] ?? [];
                            $progressPercent = $metadata['dev_progress_percent'] ?? 0;
                            if (empty($progressPercent)) {
                                $totalPhases = 6;
                                $completed = count(array_filter($checkpoints, fn ($c) => !empty($c['completed'])));
                                $progressPercent = min(100, (int) round(($completed / $totalPhases) * 100));
                            }
                            $isLocked = $project->isScopeFrozen();
                            $isDpPaid = $project->isDpConfirmed();
                            $contractDoc = $project->getContractDocument();
                        @endphp

                        <div class="p-6 bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-800 rounded-none shadow-xs space-y-5">
                            
                            <!-- Project Header Info -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold uppercase">
                                            {{ $project->project_status ?: 'Active Sprint' }}
                                        </span>
                                        @if($metadata['sprint_batch'] ?? false)
                                            <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-[10px]">
                                                🗓️ {{ $metadata['sprint_batch'] }}
                                            </span>
                                        @endif
                                        @if($isLocked)
                                            <span class="px-2 py-0.5 bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-mono text-[10px] font-bold">
                                                🔒 SCOPE LOCKED
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-xl font-black text-zinc-900 dark:text-white uppercase font-sans mt-1">
                                        {{ $project->nama_bisnis ?: $project->client_name }}
                                    </h3>
                                    <span class="text-xs text-zinc-500 font-mono block">
                                        Ref ID: {{ $project->slug }} // {{ $project->created_at ? $project->created_at->format('d M Y') : 'N/A' }}
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <a 
                                        href="/blueprint/{{ $project->slug }}" 
                                        class="px-3 py-1.5 bg-zinc-900 hover:bg-black dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black font-mono text-xs font-bold uppercase tracking-wider rounded-none transition"
                                    >
                                        {{ $isEn ? 'OPEN PRD SPEC →' : 'BUKA PRD LENGKAP →' }}
                                    </a>
                                    @if($contractDoc)
                                        <a 
                                            href="/document/{{ $contractDoc->id }}/preview" 
                                            class="px-3 py-1.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 font-mono text-xs font-bold uppercase tracking-wider rounded-none transition"
                                        >
                                            📜 {{ $isEn ? 'CONTRACT SPK' : 'KONTRAK SPK' }}
                                        </a>
                                    @endif
                                    @if($project->staging_url)
                                        <a 
                                            href="{{ $project->staging_url }}" 
                                            target="_blank" 
                                            class="px-3 py-1.5 bg-sky-500 hover:bg-sky-400 text-black font-mono text-xs font-bold uppercase tracking-wider rounded-none transition"
                                        >
                                            🚀 STAGING LIVE
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Live Sprint Progress Bar & Percent Indicator -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs font-mono">
                                    <span class="font-bold text-zinc-700 dark:text-zinc-300 uppercase">
                                        {{ $isEn ? 'LIVE SPRINT EXECUTION PROGRESS' : 'PROGRES PENGERJAAN SPRINT LIVE' }}
                                    </span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $progressPercent }}% {{ $isEn ? 'COMPLETED' : 'SELESAI' }}
                                    </span>
                                </div>
                                <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-3 border border-zinc-200 dark:border-zinc-700 rounded-none overflow-hidden">
                                    <div class="bg-gradient-to-r from-emerald-600 via-emerald-500 to-sky-400 h-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                                </div>
                            </div>

                            <!-- 6-Phase Concrete Timeline Checkpoints View -->
                            <div class="pt-2">
                                <span class="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block mb-3">
                                    {{ $isEn ? 'SPRINT CHECKPOINT ROADMAP (AUTO-SYNCED FROM PRD):' : 'TAHAPAN PENGERJAAN SPRINT (TERSINKRONISASI DARI PRD):' }}
                                </span>
                                
                                @php
                                    $phases = [
                                        'step_foundation' => [
                                            'num' => '01',
                                            'title' => $isEn ? 'Architecture Foundation & Strict ULID SQL' : 'Fondasi Arsitektur & Skema SQL Strict ULID',
                                            'desc' => $isEn ? 'PostgreSQL 16 tables, HasUlids, Docker Stack & Base Migrations.' : 'Setup database PostgreSQL 16, model HasUlids, Docker stack, dan migrasi awal.',
                                        ],
                                        'FEAT-SPEC-0' => [
                                            'num' => '02',
                                            'title' => $isEn ? 'Core Domain Models & Relations' : 'Model Domain Inti & Integritas Relasi',
                                            'desc' => $isEn ? 'Eloquent entities, polymorphic keys, casts, and data integrity.' : 'Penyusunan relasi antar entitas bisnis, casts array/json, dan validasi foreign key.',
                                        ],
                                        'FEAT-SPEC-1' => [
                                            'num' => '03',
                                            'title' => $isEn ? 'Business Logic & O(1) Pagination' : 'Business Logic & Standar Keyset O(1)',
                                            'desc' => $isEn ? 'Services layer, cursor pagination, and enterprise stability.' : 'Implementasi logika transaksi, service layer, dan pagination O(1) tanpa offset.',
                                        ],
                                        'FEAT-SPEC-2' => [
                                            'num' => '04',
                                            'title' => $isEn ? 'API Contracts & Security Shield' : 'Kontrak REST API & Pertahanan Keamanan',
                                            'desc' => $isEn ? 'Form requests, honeypots, rate limiting, and OpenAPI 3.1.' : 'Validasi input ketat, honeypot anti-bot, rate limiter, dan kontrak endpoint.',
                                        ],
                                        'FEAT-SPEC-3' => [
                                            'num' => '05',
                                            'title' => $isEn ? 'Frontend UI & Modern Reactive Islands' : 'Frontend UI & Komponen Interaktif Modern',
                                            'desc' => $isEn ? 'Responsive layouts, subtle corners, zero native alert slop.' : 'Tampilan responsive desktop/mobile, radius tipis, toast notifikasi elegan.',
                                        ],
                                        'step_deployment' => [
                                            'num' => '06',
                                            'title' => $isEn ? 'Quality Gate, 100% Tests & Staging Live' : 'Uji Mutu Otomatis, Test Suite & Staging Live',
                                            'desc' => $isEn ? 'Automated test suite passing, staging sandbox live & UAT.' : 'Seluruh automated test passing 100%, sandbox staging aktif untuk uji coba klien.',
                                        ],
                                    ];
                                @endphp

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                    @foreach($phases as $pKey => $phase)
                                        @php
                                            $cpData = $checkpoints[$pKey] ?? null;
                                            $isDone = !empty($cpData['completed']);
                                            $completedAt = !empty($cpData['completed_at']) ? \Carbon\Carbon::parse($cpData['completed_at'])->format('d M, H:i') : null;
                                        @endphp
                                        <div class="p-3 border rounded-none transition {{ $isDone ? 'bg-emerald-500/5 border-emerald-500/40 dark:bg-emerald-950/20' : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800' }}">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="font-mono text-[10px] font-bold {{ $isDone ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-500' }}">
                                                    PHASE {{ $phase['num'] }}
                                                </span>
                                                @if($isDone)
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                                        ✓ {{ $isEn ? 'DONE' : 'SELESAI' }} {{ $completedAt ? "({$completedAt})" : '' }}
                                                    </span>
                                                @else
                                                    <span class="text-[10px] font-mono text-zinc-400">
                                                        ⏳ {{ $isEn ? 'IN PROGRESS' : 'DALAM PROSES' }}
                                                    </span>
                                                @endif
                                            </div>
                                            <h4 class="text-xs font-bold text-zinc-900 dark:text-white font-mono leading-tight">
                                                {{ $phase['title'] }}
                                            </h4>
                                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-sans mt-1 leading-snug">
                                                {{ $phase['desc'] }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- =================================================================== -->
        <!-- PANE 3: RETAIL DIGITAL LICENSES & LIFETIME DOWNLOAD CENTER         -->
        <!-- =================================================================== -->
        <div x-show="currentTab === 'licenses'" x-cloak class="space-y-6">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                <div>
                    <h2 class="text-lg font-black uppercase text-zinc-900 dark:text-white font-mono flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-cyan-500 rounded-none"></span>
                        <span>{{ $isEn ? 'Retail Digital Licenses & Lifetime Download Center' : 'Lisensi Spesifikasi Digital Retail & Pusat Unduh Seumur Hidup' }}</span>
                    </h2>
                    <p class="text-xs text-zinc-500 font-sans mt-0.5">
                        {{ $isEn ? 'Files are stored permanently in your account for unlimited 1-click downloads.' : 'Berkas spesifikasi PRD, skema SQL DDL, dan ZIP scaffold tersimpan permanen untuk unduh tak terbatas.' }}
                    </p>
                </div>
                <span class="text-xs font-mono font-bold text-zinc-500">
                    {{ $retailLicenses->count() }} {{ $isEn ? 'Licenses' : 'Lisensi' }}
                </span>
            </div>

            @if($retailLicenses->isEmpty())
                <div class="p-10 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-center space-y-3 rounded-none">
                    <span class="text-3xl block">📦</span>
                    <h3 class="font-bold text-sm font-mono uppercase text-zinc-900 dark:text-white">
                        {{ $isEn ? 'NO RETAIL DIGITAL LICENSES YET' : 'BELUM ADA LISENSI DIGITAL RETAIL' }}
                    </h3>
                    <p class="text-xs text-zinc-500 max-w-md mx-auto">
                        {{ $isEn 
                            ? 'Get instant 26-parameter PRDs, PostgreSQL Strict ULID schemas, and complete codebase scaffolds for your engineering team to build independently.' 
                            : 'Dapatkan spesifikasi PRD 26 parameter instan, skema SQL PostgreSQL Strict ULID, dan scaffold codebase lengkap untuk dibangun mandiri oleh tim developer Anda.' }}
                    </p>
                    <a href="/pricing" class="inline-block mt-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-mono text-xs font-bold uppercase tracking-wider rounded-none">
                        {{ $isEn ? 'ORDER LITE OR PRO PRD (STARTING RP 99.000) →' : 'PESAN LISENSI LITE / PRO (MULAI RP 99RB) →' }}
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($retailLicenses as $license)
                        @php
                            $tier = $license->user_metadata['retail_tier'] ?? $license->user_metadata['package_tier'] ?? 'retail_lite';
                            $tierLabel = match($tier) {
                                'retail_spark', 'spark' => 'Spark Free Audit (Rp 0)',
                                'retail_pro', 'pro' => 'Pro Production PRD (Rp 399.000)',
                                'retail_ultimate', 'ultimate' => 'Ultimate Advisory PRD (Rp 1.490.000)',
                                default => 'Lite PRD Generator (Rp 99.000)',
                            };
                            $allowedDays = match($tier) {
                                'retail_pro', 'pro' => 180,
                                'retail_ultimate', 'ultimate' => 365,
                                default => 30,
                            };
                            $daysRemaining = max(0, $allowedDays - (int) ($license->created_at ? $license->created_at->diffInDays(now()) : 0));
                            $isRevisionActive = $daysRemaining > 0;
                        @endphp

                        <div class="p-5 bg-white dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs space-y-4 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="px-2 py-0.5 bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-mono text-[10px] font-bold uppercase">
                                        {{ $tierLabel }}
                                    </span>
                                    <span class="font-mono text-[10px] text-zinc-500">
                                        {{ $license->created_at ? $license->created_at->format('d M Y') : 'N/A' }}
                                    </span>
                                </div>

                                <h3 class="text-lg font-black uppercase text-zinc-900 dark:text-white font-sans">
                                    {{ $license->nama_bisnis ?: $license->client_name }}
                                </h3>
                                <p class="text-xs text-zinc-500 font-mono mt-0.5">
                                    Slug: {{ $license->slug }}
                                </p>

                                <!-- 30-Day Revision Guarantee Badge -->
                                <div class="mt-3 p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between text-xs font-mono">
                                    <span class="text-zinc-600 dark:text-zinc-400">
                                        {{ $isEn ? 'Revision Window:' : 'Garansi Revisi Parameter:' }}
                                    </span>
                                    @if($isRevisionActive)
                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
                                            🟢 {{ $daysRemaining }} {{ $isEn ? 'Days Left' : 'Hari Tersisa' }}
                                        </span>
                                    @else
                                        <span class="text-zinc-500 font-bold">
                                            ⚪ {{ $isEn ? 'Window Expired' : 'Masa Revisi Berakhir' }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Lifetime Download Buttons -->
                            <div class="space-y-2 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                                <span class="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">
                                    {{ $isEn ? '1-CLICK LIFETIME DOWNLOADS:' : 'PUSAT UNDUHAN SEUMUR HIDUP:' }}
                                </span>
                                
                                <div class="grid grid-cols-2 gap-2 text-xs font-mono font-bold">
                                    <a 
                                        href="/blueprint/{{ $license->slug }}/download/pdf" 
                                        class="p-2 text-center bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 transition rounded-none"
                                        title="Unduh Dokumen Eksekutif PRD PDF"
                                    >
                                        📄 {{ $isEn ? 'PDF Doc' : 'Dokumen PDF' }}
                                    </a>
                                    <a 
                                        href="/blueprint/{{ $license->slug }}/download/md" 
                                        class="p-2 text-center bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 transition rounded-none"
                                        title="Unduh PRD Markdown untuk AI Prompt"
                                    >
                                        📝 {{ $isEn ? 'Markdown .md' : 'Markdown PRD' }}
                                    </a>
                                    <a 
                                        href="/blueprint/{{ $license->slug }}/export/scaffold" 
                                        class="p-2 text-center bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 transition rounded-none"
                                        title="Unduh Kode Scaffold, Docker Compose & SQL ZIP"
                                    >
                                        📦 {{ $isEn ? 'Scaffold ZIP' : 'Scaffold ZIP' }}
                                    </a>
                                    <a 
                                        href="/blueprint/{{ $license->slug }}" 
                                        class="p-2 text-center bg-zinc-900 hover:bg-black dark:bg-white dark:hover:bg-zinc-200 text-white dark:text-black transition rounded-none"
                                    >
                                        📐 {{ $isEn ? 'Open PRD →' : 'Buka PRD →' }}
                                    </a>
                                </div>

                                @if($isRevisionActive)
                                    <a 
                                        href="/blueprint?slug={{ $license->slug }}" 
                                        class="w-full mt-2 py-2 px-3 bg-amber-500 hover:bg-amber-400 text-black font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition rounded-none"
                                    >
                                        <span>🔄 {{ $isEn ? 'REFINE PARAMETERS (ACTIVE REVISION)' : 'PERBARUI PARAMETER & REGENERASI PRD' }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- =================================================================== -->
        <!-- PANE 4: OFFICIAL TAX INVOICES & REIMBURSEMENT RECEIPTS             -->
        <!-- =================================================================== -->
        <div x-show="currentTab === 'billing'" x-cloak class="space-y-6">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                <div>
                    <h2 class="text-lg font-black uppercase text-zinc-900 dark:text-white font-mono flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-amber-500 rounded-none"></span>
                        <span>{{ $isEn ? 'Official Tax Invoices & Corporate Receipts' : 'Faktur Pajak Resmi & Kwitansi Reimbursement Kantor' }}</span>
                    </h2>
                    <p class="text-xs text-zinc-500 font-sans mt-0.5">
                        {{ $isEn ? 'Instantly issued for corporate finance reimbursement and tax compliance.' : 'Diterbitkan instan untuk keperluan pembukuan keuangan dan klaim reimbursement kantor.' }}
                    </p>
                </div>
                <span class="text-xs font-mono font-bold text-zinc-500">
                    {{ $transactions->count() }} {{ $isEn ? 'Records' : 'Transaksi' }}
                </span>
            </div>

            @if($transactions->isEmpty())
                <div class="p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-center rounded-none text-xs text-zinc-500 font-mono">
                    {{ $isEn ? 'No payment transaction records found for your account.' : 'Belum ada riwayat transaksi pembayaran tercatat untuk akun ini.' }}
                </div>
            @else
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none overflow-x-auto shadow-xs">
                    <table class="w-full text-left font-mono text-xs">
                        <thead class="bg-zinc-100 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 uppercase text-[10px]">
                            <tr>
                                <th class="p-3">{{ $isEn ? 'Order Reference' : 'Nomor Pesanan / Order ID' }}</th>
                                <th class="p-3">{{ $isEn ? 'Date' : 'Tanggal' }}</th>
                                <th class="p-3">{{ $isEn ? 'Total IDR' : 'Jumlah (IDR)' }}</th>
                                <th class="p-3">{{ $isEn ? 'Status' : 'Status Pembayaran' }}</th>
                                <th class="p-3 text-right">{{ $isEn ? 'Action' : 'Aksi Dokumen' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-zinc-800 dark:text-zinc-200">
                            @foreach($transactions as $tx)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-950/50 transition">
                                    <td class="p-3 font-bold text-zinc-900 dark:text-white">
                                        {{ $tx->midtrans_order_id ?: ('NPRO-' . substr($tx->id, 0, 8)) }}
                                    </td>
                                    <td class="p-3 text-zinc-500">
                                        {{ $tx->created_at ? $tx->created_at->format('d M Y, H:i') : 'N/A' }}
                                    </td>
                                    <td class="p-3 font-bold">
                                        Rp {{ number_format((float) $tx->total_idr, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3">
                                        @if(in_array($tx->status, ['settlement', 'capture', 'success']))
                                            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-500/20 text-[10px]">
                                                ✓ LUNAS (SETTLED)
                                            </span>
                                        @elseif($tx->status === 'pending')
                                            <span class="px-2 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 font-bold border border-amber-500/20 text-[10px]">
                                                ⏳ MENUNGGU BAYAR
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-[10px]">
                                                {{ strtoupper($tx->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-right">
                                        <button 
                                            type="button" 
                                            onclick="window.print()" 
                                            class="px-2.5 py-1 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 text-[10px] font-bold transition rounded-none cursor-pointer"
                                        >
                                            🖨️ {{ $isEn ? 'PRINT RECEIPT' : 'CETAK KWITANSI' }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- =================================================================== -->
        <!-- PANE 5: DOMAIN & VPS HOSTING ASSETS (EXPANDABLE INFRASTRUCTURE)    -->
        <!-- =================================================================== -->
        <div x-show="currentTab === 'assets'" x-cloak class="space-y-6">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                <div>
                    <h2 class="text-lg font-black uppercase text-zinc-900 dark:text-white font-mono flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-indigo-500 rounded-none"></span>
                        <span>{{ $isEn ? 'Domain & Dedicated Hosting Assets' : 'Aset Domain & Server VPS Terkelola' }}</span>
                    </h2>
                    <p class="text-xs text-zinc-500 font-sans mt-0.5">
                        {{ $isEn ? 'Track production domain renewals, dedicated VPS instances, and SSL security status.' : 'Pantau masa aktif domain, server VPS terisolasi, dan status sertifikat SSL aplikasi Anda.' }}
                    </p>
                </div>
                <span class="text-xs font-mono font-bold text-zinc-500">
                    {{ $hostingAssets->count() }} {{ $isEn ? 'Assets' : 'Aset' }}
                </span>
            </div>

            @if($hostingAssets->isEmpty())
                <div class="p-10 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-center space-y-3 rounded-none">
                    <span class="text-3xl block">🌐</span>
                    <h3 class="font-bold text-sm font-mono uppercase text-zinc-900 dark:text-white">
                        {{ $isEn ? 'NO DOMAIN OR HOSTING ASSETS REGISTERED YET' : 'BELUM ADA ASET DOMAIN / SERVER TERCATAT' }}
                    </h3>
                    <p class="text-xs text-zinc-500 max-w-md mx-auto">
                        {{ $isEn 
                            ? 'Domain and VPS infrastructure assets are automatically provisioned and tracked when your Studio project reaches Phase 5-6.' 
                            : 'Aset domain dan VPS akan otomatis tercatat dan dipantau di sini ketika pengerjaan proyek Studio Anda memasuki tahap staging/live.' }}
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($hostingAssets as $asset)
                        <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-0.5 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-mono text-[10px] font-bold uppercase">
                                    {{ $asset->asset_type ?: 'Domain / Hosting' }}
                                </span>
                                <span class="font-mono text-[10px] text-zinc-500">
                                    Exp: {{ $asset->expiration_date ? \Carbon\Carbon::parse($asset->expiration_date)->format('d M Y') : 'N/A' }}
                                </span>
                            </div>
                            <h4 class="text-base font-bold text-zinc-900 dark:text-white font-mono">
                                {{ $asset->name }}
                            </h4>
                            <p class="text-xs text-zinc-500">
                                {{ $asset->provider ?: 'Managed VPS Cloud' }} // {{ $asset->ip_address ?: 'Dedicated IP' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- =================================================================== -->
        <!-- PANE 6: ACCOUNT & PREFERENCES                                       -->
        <!-- =================================================================== -->
        <div x-show="currentTab === 'account'" x-cloak class="space-y-6">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                <div>
                    <h2 class="text-lg font-black uppercase text-zinc-900 dark:text-white font-mono flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-zinc-500 rounded-none"></span>
                        <span>{{ $isEn ? 'Account Profile & Security Preferences' : 'Profil Akun & Preferensi Keamanan' }}</span>
                    </h2>
                    <p class="text-xs text-zinc-500 font-sans mt-0.5">
                        {{ $isEn ? 'Manage authentication credentials and verified customer information.' : 'Kelola informasi kredensial login dan status akun terverifikasi Anda.' }}
                    </p>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-none space-y-4 max-w-2xl">
                <div class="space-y-1">
                    <span class="text-xs font-mono font-bold text-zinc-400 uppercase">E-mail Pelanggan Terdaftar</span>
                    <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 font-mono text-sm text-zinc-900 dark:text-white font-bold">
                        {{ $user->email }}
                    </div>
                </div>

                <div class="space-y-1">
                    <span class="text-xs font-mono font-bold text-zinc-400 uppercase">Metode Autentikasi</span>
                    <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs text-zinc-700 dark:text-zinc-300 flex items-center justify-between">
                        <span>🔐 Passwordless 6-Digit Email OTP (Zero Password Vulnerability)</span>
                        <span class="text-emerald-500 font-bold font-mono">AKTIF</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                    <span class="text-xs text-zinc-500">Keluar dari sesi portal di perangkat ini:</span>
                    <button 
                        type="button" 
                        @click="logoutCustomer()" 
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-mono text-xs font-bold uppercase transition rounded-none cursor-pointer"
                    >
                        {{ $isEn ? 'LOGOUT SESSION' : 'KELUAR DARI AKUN' }}
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- 3. FOOTER -->
    <footer class="mt-20 border-t border-zinc-200 dark:border-zinc-800 py-8 bg-white dark:bg-zinc-900 text-center font-mono text-xs text-zinc-500">
        <p>&copy; {{ date('Y') }} Neriah Pro. {{ $isEn ? 'All rights reserved. Modular Software Architecture OS.' : 'Hak cipta dilindungi. Sistem Operasi Arsitektur Perangkat Lunak Skala Enterprise.' }}</p>
    </footer>
</body>
</html>
