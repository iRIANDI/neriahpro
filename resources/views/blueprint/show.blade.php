<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $blueprint->nama_bisnis ?? $blueprint->client_name }} - Ultimate PRD & Architecture Blueprint | Neriah Pro</title>
    <meta name="description" content="Product Requirements Document (PRD) & skema arsitektur database untuk {{ $blueprint->nama_bisnis }}.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

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
        }
    </script>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .print-break-inside-avoid { break-inside: avoid; }
        }
    </style>
    <style>
        [x-cloak] { display: none !important; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .print-break-inside-avoid { break-inside: avoid; }
        }
    </style>
    <!-- Alpine.js & Mermaid UMD Bundle -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.mermaid) {
                try {
                    mermaid.initialize({
                        startOnLoad: true,
                        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'neutral',
                        securityLevel: 'loose'
                    });
                } catch(e) {
                    console.warn('Mermaid init warning:', e);
                }
            }
        });
    </script>
@php
    $pricingTiers = $prd['velocity_pricing_options'] ?? \App\Services\PrdGeneratorService::generateVelocityPricingOptions(
        $blueprint->target_waktu ?? '30 Hari Kerja',
        $blueprint->user_metadata['kisaran_budget'] ?? null
    );
    $alpineTiers = [];
    foreach ($pricingTiers as $t) {
        $alpineTiers[$t['id']] = [
            'contract' => (float)$t['contract_amount'],
            'dp' => (float)$t['dp_amount'],
            'days' => $t['duration'],
            'name' => $t['name'],
        ];
    }
    $defaultSelectedTier = array_key_exists('fast_track', $alpineTiers) 
        ? 'fast_track' 
        : (array_key_exists('community_starter', $alpineTiers) ? 'community_starter' : array_key_first($alpineTiers));
@endphp
</head>
<body x-data="{ 
    userMenuOpen: false, 
    paymentModalOpen: false, 
    flowTab: 'visual', 
    erdTab: 'visual', 
    erdLang: 'id',
    selectedTier: '{{ $defaultSelectedTier }}',
    tierAmounts: {{ json_encode($alpineTiers) }}
}" class="bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col transition-colors duration-200">

    <!-- Header Navigation Bar (Sharp Precision Theme) -->
    <header class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 py-3 px-6 sticky top-0 z-50 no-print transition-colors">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="/" class="text-sm font-black uppercase tracking-tight flex items-center gap-2 text-zinc-900 dark:text-white">
                <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black flex items-center justify-center text-xs font-mono font-bold rounded-none">N</span>
                <span>NERIAH<span class="text-emerald-500">PRO</span> // PRD SPEC</span>
            </a>

            <div class="flex items-center gap-3">
                <button onclick="toggleTheme()" class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono rounded-none border border-zinc-300 dark:border-zinc-700 transition">
                    THEME
                </button>
                <button onclick="window.print()" class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono uppercase font-bold rounded-none border border-zinc-300 dark:border-zinc-700 flex items-center gap-1.5 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span class="hidden sm:inline">Cetak</span>
                </button>
                <a href="{{ route('blueprint.download-pdf', $blueprint->slug) }}" class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono uppercase font-bold rounded-none border border-zinc-300 dark:border-zinc-700 flex items-center gap-1.5 transition">
                    <span>PDF</span>
                </a>
                <a href="{{ route('blueprint.download-md', $blueprint->slug) }}" class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono uppercase font-bold rounded-none border border-zinc-300 dark:border-zinc-700 flex items-center gap-1.5 transition">
                    <span>MD</span>
                </a>

                <a href="{{ route('blueprint.show', $blueprint->slug) }}?regenerate=1" onclick="return confirm('Sintesis ulang PRD & Diagram Arsitektur dari kuesioner awal?')" class="px-2.5 py-1 bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 text-xs font-mono uppercase font-bold border border-amber-500/30 transition flex items-center gap-1" title="Sintesis Ulang PRD">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span class="hidden md:inline">REGENERATE</span>
                </a>
                
                <div class="h-6 w-px bg-zinc-300 dark:bg-zinc-700 mx-1 hidden sm:block"></div>

                @php
                    $rawCart = session('neriah_cart', []);
                    $cartCount = count($rawCart);
                    $minRemaining = null;
                    $now = now()->timestamp;
                    foreach ($rawCart as $c) {
                        $exp = $c['expires_at'] ?? ($c['added_at'] + (24 * 3600));
                        $rem = max(0, $exp - $now);
                        if ($rem > 0 && ($minRemaining === null || $rem < $minRemaining)) {
                            $minRemaining = $rem;
                        }
                    }
                @endphp

                <!-- Cart Navigation Button with Anti-Ghost Hold Timer -->
                <a href="{{ route('cart.index') }}" class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-mono uppercase font-bold border border-zinc-300 dark:border-zinc-700 flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>CART</span>
                    @if($cartCount > 0)
                        <span class="px-1.5 py-0.2 bg-emerald-500 text-black text-[10px] font-bold">{{ $cartCount }}</span>
                        @if($minRemaining)
                            <span class="text-[10px] text-amber-500 font-bold hidden sm:inline" id="nav-cart-timer" data-rem="{{ $minRemaining }}">⏱️ {{ gmdate('H:i:s', $minRemaining) }}</span>
                        @endif
                    @endif
                </a>

                @auth
                    <!-- User Dropdown Navigation Board -->
                    <div class="relative" @click.outside="userMenuOpen = false">
                        <button @click="userMenuOpen = !userMenuOpen" class="px-3 py-1 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black hover:bg-black dark:hover:bg-emerald-400 text-xs font-mono uppercase font-bold border border-zinc-900 dark:border-emerald-500 flex items-center gap-1.5 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="max-w-[110px] truncate">{{ explode(' ', Auth::user()->name)[0] }}</span>
                            <svg class="w-3 h-3 transition-transform" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <!-- Navigation Board Dropdown Menu -->
                        <div x-show="userMenuOpen" x-cloak x-transition.opacity.duration.150ms class="absolute right-0 top-full mt-1.5 w-64 bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-emerald-500 shadow-2xl z-50 font-mono">
                            <div class="p-3.5 bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800">
                                <div class="text-[9px] text-zinc-400 uppercase tracking-widest font-bold">AKTOR TERAUTENTIKASI</div>
                                <div class="text-xs font-bold text-zinc-900 dark:text-white truncate mt-0.5">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] text-emerald-600 dark:text-emerald-400 truncate">{{ Auth::user()->email }}</div>
                                <span class="inline-block mt-2 px-2 py-0.5 text-[9px] bg-zinc-200 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 uppercase font-bold">
                                    ROLE: {{ Auth::user()->roles->first()?->name ?? (Auth::user()->role ?? 'AUTHENTICATED') }}
                                </span>
                            </div>
                            <div class="py-1 text-xs">
                                <a href="{{ url('/admin') }}" class="flex items-center gap-2.5 px-4 py-2.5 font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 border-b border-zinc-100 dark:border-zinc-800 transition">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span>ADMIN CONTROL PANEL</span>
                                </a>
                                <a href="{{ route('cart.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 font-bold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 border-b border-zinc-100 dark:border-zinc-800 transition">
                                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <span>CART BELANJA & ESCROW</span>
                                </a>
                            </div>
                            <form method="POST" action="{{ Route::has('logout') ? route('logout') : url('/logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-3 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    <span>LOG OUT</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="px-3 py-1 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black text-xs font-mono uppercase font-bold rounded-none transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        LOGIN
                    </a>
                @endauth
            </div>
        </div>
    </header>

    @if(!$blueprint->is_published)
        <!-- PRIVATE DRAFT SHIELD (SHARP BRUTALIST) -->
        <main class="flex-1 flex items-center justify-center p-6">
            <div class="max-w-xl w-full bg-white dark:bg-zinc-900 border-2 border-amber-500 rounded-none p-8 text-center shadow-none">
                <div class="w-12 h-12 bg-amber-500 text-black flex items-center justify-center mx-auto mb-4 font-mono font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <span class="inline-block px-3 py-1 bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 text-xs font-mono font-bold uppercase tracking-widest border border-amber-300 dark:border-amber-800 mb-3 rounded-none">
                    ACCESS RESTRICTED // PRIVATE DRAFT
                </span>
                <h1 class="text-xl sm:text-2xl font-black uppercase text-zinc-900 dark:text-zinc-100 mb-3">
                    Dokumen Masih Dalam Tinjauan Arsitektur
                </h1>
                <p class="text-zinc-600 dark:text-zinc-400 text-xs sm:text-sm leading-relaxed mb-6 font-sans">
                    Spesifikasi PRD & Blueprint untuk proyek <strong>{{ $blueprint->nama_bisnis ?: $blueprint->client_name }}</strong> saat ini masih dalam proses penyesuaian arsitektural internal oleh tim pengembang Neriah Pro.
                </p>
                <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 text-xs text-zinc-500 dark:text-zinc-400 mb-6 text-left space-y-1 font-mono rounded-none">
                    <div>PROJECT_NAME: <span class="text-zinc-900 dark:text-zinc-100 font-bold">{{ $blueprint->nama_bisnis }}</span></div>
                    <div>CLIENT_PIC: <span class="text-zinc-900 dark:text-zinc-100 font-bold">{{ $blueprint->client_name }}</span></div>
                    <div>ACCESS_MODE: <span class="text-rose-600 font-bold">LOCKED_PRIVATE</span></div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="/admin/login" class="px-5 py-2.5 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black text-xs font-mono uppercase font-bold rounded-none transition">
                        Login Administrator
                    </a>
                    <a href="/" class="px-5 py-2.5 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-mono uppercase font-bold rounded-none transition">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </main>
    @else
        <!-- PUBLISHED FULL ULTIMATE PRD (SHARP BRUTALIST TECHNICAL THEME) -->
        <main class="flex-1 py-10 px-4 sm:px-6 max-w-5xl mx-auto w-full">
            
            <!-- Document Hero Card -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none relative">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-6 mb-6">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-wider bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black rounded-none">
                                SPEC_ID: {{ strtoupper(substr($blueprint->id, 0, 10)) }}
                            </span>
                            <span class="px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-wider bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800 rounded-none">
                                DISTRIBUTED ULID ARCHITECTURE
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-4xl font-black uppercase text-zinc-900 dark:text-zinc-100 tracking-tight">
                            {{ $blueprint->nama_bisnis ?: $blueprint->client_name }}
                        </h1>
                        <p class="text-zinc-500 dark:text-zinc-400 text-xs mt-1 font-mono">
                            PREPARED BY NERIAH PRO TECH HUB // SYNCHRONIZED: {{ $blueprint->updated_at->format('Y-m-d H:i') }} UTC
                        </p>
                    </div>

                    <div class="flex flex-col items-start md:items-end gap-1 font-mono text-xs">
                        <span class="text-zinc-400 uppercase tracking-widest">STATUS KONTRAK</span>
                        <span class="px-3 py-1 font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-none">
                            {{ strtoupper($blueprint->project_status) }}
                        </span>
                    </div>
                </div>

                <!-- Meta Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs font-mono">
                    <div class="bg-zinc-50 dark:bg-zinc-950 p-3 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <span class="text-zinc-400 block mb-0.5">PIC KLIEN</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100 truncate block">{{ $blueprint->client_name }}</span>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-950 p-3 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <span class="text-zinc-400 block mb-0.5">EMAIL RESMI</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100 truncate block">{{ $blueprint->email }}</span>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-950 p-3 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <span class="text-zinc-400 block mb-0.5">DURASI PROYEK</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $blueprint->target_waktu ?? '30 Hari Kerja' }}</span>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-950 p-3 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <span class="text-zinc-400 block mb-0.5">KESIAPAN ASET</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $blueprint->kesiapan_aset ?? 'Sedang Disiapkan' }}</span>
                    </div>
                </div>
            </div>

            <!-- SECTION 1: EXECUTIVE TECHNICAL DISCOVERY -->
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex items-center gap-2 mb-4 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">01</span>
                    <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Executive Technical Discovery</h2>
                </div>

                <div class="grid md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-zinc-50 dark:bg-zinc-950 p-5 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400 mb-2">Masalah Utama yang Diselesaikan</h3>
                        <p class="text-zinc-700 dark:text-zinc-300 text-sm leading-relaxed font-sans">
                            {{ $blueprint->masalah_utama ?? ($prd['executive_summary']['problem_statement'] ?? '-') }}
                        </p>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-950 p-5 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-2">Tolak Ukur Kesuksesan (KPI)</h3>
                        <p class="text-zinc-700 dark:text-zinc-300 text-sm leading-relaxed font-sans">
                            {{ $blueprint->tujuan_utama ?? ($prd['executive_summary']['success_metrics'] ?? '-') }}
                        </p>
                    </div>
                </div>

                <div class="bg-zinc-50 dark:bg-zinc-950 border-l-4 border-emerald-500 p-4 font-sans text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed rounded-none">
                    <strong class="font-mono uppercase text-xs text-emerald-600 dark:text-emerald-400 block mb-1">Filosofi Arsitektur & Efisiensi Biaya</strong>
                    {{ $prd['executive_summary']['architecture_philosophy'] ?? 'Sistem menggunakan arsitektur Modern Monolith (Laravel 13 & Filament PHP) untuk memangkas biaya server, menjamin isolasi data, dan mempercepat peluncuran fitur hingga 3x lipat.' }}
                </div>
            </section>

            <!-- SECTION 2: PENGGUNA & HAK AKSES (RBAC) -->
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex items-center gap-2 mb-4 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">02</span>
                    <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Pengguna & Hak Akses (Aktor / RBAC)</h2>
                </div>
                <div class="mb-4 text-xs font-mono text-zinc-500 dark:text-zinc-400">
                    TARGET_AUDIENCE: <span class="text-zinc-900 dark:text-zinc-100 font-bold">{{ $blueprint->target_audiens ?? ($prd['executive_summary']['target_audience'] ?? '-') }}</span>
                </div>

                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($prd['system_actors'] ?? [] as $actor)
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-200 dark:border-zinc-800 rounded-none flex flex-col justify-between">
                            <div>
                                <span class="inline-block px-2 py-0.5 text-xs font-mono font-bold uppercase tracking-wider bg-zinc-200 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 mb-2 rounded-none">
                                    {{ $actor['name'] ?? 'Aktor Sistem' }}
                                </span>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed font-sans">
                                    {{ $actor['role'] ?? '-' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- SECTION 3: SPESIFIKASI FITUR (MVP VS ROADMAP) -->
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex items-center gap-2 mb-4 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">03</span>
                    <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Spesifikasi Fitur (MVP vs Fase 2)</h2>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <!-- MVP Phase 1 -->
                    <div class="border border-emerald-500/40 bg-emerald-500/5 p-5 rounded-none">
                        <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-mono text-xs font-bold uppercase tracking-wider mb-4">
                            <span class="w-2 h-2 bg-emerald-500 inline-block"></span>
                            <h3>Fitur Wajib (Fase 1 - MVP Peluncuran)</h3>
                        </div>
                        <ul class="space-y-2.5">
                            @foreach($prd['features']['mvp_phase1'] ?? [] as $fitur)
                                <li class="text-xs bg-white dark:bg-zinc-900 p-3 border border-emerald-500/20 rounded-none">
                                    <div class="font-bold text-zinc-900 dark:text-zinc-100">{{ $fitur['title'] ?? '-' }}</div>
                                    <div class="text-zinc-500 dark:text-zinc-400 mt-0.5 font-sans">{{ $fitur['desc'] ?? '' }}</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Phase 2 Roadmap -->
                    <div class="border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 p-5 rounded-none">
                        <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-400 font-mono text-xs font-bold uppercase tracking-wider mb-4">
                            <span class="w-2 h-2 bg-zinc-400 inline-block"></span>
                            <h3>Fitur Tambahan (Fase 2 - Roadmap)</h3>
                        </div>
                        @if(empty($prd['features']['phase2_roadmap']))
                            <p class="text-xs text-zinc-400 italic font-mono">Belum ada fitur susulan. Fokus 100% pada rilis Fase 1 MVP.</p>
                        @else
                            <ul class="space-y-2.5">
                                @foreach($prd['features']['phase2_roadmap'] as $fitur)
                                    <li class="text-xs bg-white dark:bg-zinc-900 p-3 border border-zinc-200 dark:border-zinc-800 rounded-none">
                                        <div class="font-bold text-zinc-900 dark:text-zinc-100">{{ $fitur['title'] ?? '-' }}</div>
                                        <div class="text-zinc-500 dark:text-zinc-400 mt-0.5 font-sans">{{ $fitur['desc'] ?? '' }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </section>

            <!-- SECTION 4: ALUR KERJA (USER FLOW) -->
            @php
                $rawWorkflows = $prd['workflow'] ?? [];
                // Dynamic fallback for single-string workflows with comma-separated steps
                if (count($rawWorkflows) === 1 && strlen($rawWorkflows[0]['action'] ?? '') > 35) {
                    $singleText = $rawWorkflows[0]['action'];
                    $parts = preg_split('/,\s*(?:lalu\s+|kemudian\s+|setelah itu\s+|selanjutnya\s+)?|\s+(?:lalu|kemudian|setelah itu|selanjutnya)\s+/i', $singleText);
                    $expanded = [];
                    $sIdx = 1;
                    foreach ($parts as $p) {
                        $p = trim($p, " \t\n\r\0\x0B-•*1234567890.),");
                        if (strlen($p) > 3) {
                            $lowerP = strtolower($p);
                            $actor = 'Pengguna / Pengunjung';
                            $badge = 'DISCOVERY';
                            if (str_contains($lowerP, 'filter') || str_contains($lowerP, 'cari') || str_contains($lowerP, 'search')) {
                                $actor = 'Sistem / Search Engine';
                                $badge = 'QUERY_FILTER';
                            } elseif (str_contains($lowerP, 'notifikasi') || str_contains($lowerP, 'langganan') || str_contains($lowerP, 'berlangganan')) {
                                $actor = 'Notification Engine';
                                $badge = 'NOTIFICATION';
                            } elseif (str_contains($lowerP, 'katalog') || str_contains($lowerP, 'daftar')) {
                                $actor = 'Katalog Properti';
                                $badge = 'CATALOG';
                            }
                            $expanded[] = [
                                'step' => $sIdx++,
                                'action' => $p,
                                'description' => 'Tahapan validasi, interaksi antarmuka, dan transmisi data alur kerja.',
                                'actor' => $actor,
                                'badge' => $badge,
                            ];
                        }
                    }
                    if (count($expanded) > 1) {
                        $rawWorkflows = $expanded;
                    }
                }
            @endphp
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-6">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">04</span>
                        <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Alur Kerja Utama (User Flow)</h2>
                    </div>

                    <!-- Flow View Tabs -->
                    <div class="flex items-center gap-1 font-mono text-xs no-print">
                        <button 
                            @click="flowTab = 'visual'" 
                            :class="flowTab === 'visual' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-3 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            <span>VISUAL PIPELINE</span>
                        </button>
                        <button 
                            @click="flowTab = 'mermaid'" 
                            :class="flowTab === 'mermaid' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-3 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                            <span>MERMAID CHART</span>
                        </button>
                    </div>
                </div>

                <!-- 1. Interactive Visual Flowchart (Pipeline Nodes & Connectors) -->
                <div x-show="flowTab === 'visual'" class="mb-6">
                    <div class="bg-zinc-950 border border-zinc-800 p-6 rounded-none relative overflow-hidden">
                        <div class="absolute top-0 right-0 px-3 py-1 bg-emerald-500/10 border-b border-l border-emerald-500/30 text-[10px] font-mono text-emerald-400 uppercase tracking-widest font-bold">
                            INTERACTIVE PROCESS PIPELINE
                        </div>

                        <!-- Horizontal Scroll Pipeline Container -->
                        <div class="overflow-x-auto pb-4 pt-2">
                            <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3 min-w-[700px]">
                                @foreach($rawWorkflows as $index => $flow)
                                    <!-- Node Card -->
                                    <div class="flex-1 bg-zinc-900 border border-zinc-700 p-4 relative group hover:border-emerald-500 transition shadow-lg">
                                        <div class="flex items-center justify-between gap-2 mb-2 font-mono">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span class="text-xs font-bold text-white">STEP 0{{ $flow['step'] ?? ($index + 1) }}</span>
                                            </div>
                                            <span class="text-[9px] uppercase px-1.5 py-0.5 bg-zinc-800 text-emerald-400 border border-emerald-500/30 font-bold">
                                                {{ $flow['badge'] ?? 'PROCESS' }}
                                            </span>
                                        </div>

                                        <h4 class="font-mono font-bold text-xs uppercase text-zinc-100 mb-2 leading-snug line-clamp-2">
                                            {{ $flow['action'] ?? '-' }}
                                        </h4>

                                        <p class="text-[11px] text-zinc-400 font-sans leading-relaxed mb-3">
                                            {{ $flow['description'] ?? 'Tahapan validasi dan transmisi alur kerja.' }}
                                        </p>

                                        <div class="pt-2 border-t border-zinc-800 flex items-center justify-between text-[10px] font-mono text-zinc-400">
                                            <span class="text-zinc-500">AKTOR:</span>
                                            <span class="text-emerald-400 font-bold truncate max-w-[120px]">{{ $flow['actor'] ?? 'Pengguna' }}</span>
                                        </div>
                                    </div>

                                    @if(!$loop->last)
                                        <!-- Flow Connector Arrow -->
                                        <div class="flex items-center justify-center text-emerald-500 px-1 py-1">
                                            <div class="hidden lg:flex items-center gap-0.5">
                                                <div class="w-4 h-0.5 bg-emerald-500/60"></div>
                                                <svg class="w-4 h-4 text-emerald-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                            </div>
                                            <div class="flex lg:hidden items-center justify-center my-1">
                                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Mermaid Flowchart View (Code & Native Mermaid SVG) -->
                <div x-show="flowTab === 'mermaid'" x-cloak class="mb-6">
                    <div class="overflow-x-auto bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-6 rounded-none">
                        <pre class="mermaid text-center">
graph LR
@foreach($rawWorkflows as $index => $flow)
    S{{ $index + 1 }}["{{ $index + 1 }}. {{ addslashes(str_replace(['"', "'", "\n", "\r"], ' ', Str::limit($flow['action'] ?? '-', 40))) }}"]
    @if(!$loop->last)
    S{{ $index + 1 }} --> S{{ $index + 2 }}
    @endif
@endforeach
                        </pre>
                    </div>
                </div>

                <!-- Detailed Steps Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($rawWorkflows as $flow)
                        <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 rounded-none flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black text-xs font-mono font-bold flex items-center justify-center rounded-none">
                                    {{ $flow['step'] ?? $loop->iteration }}
                                </div>
                                <span class="text-[10px] font-mono font-bold text-emerald-600 dark:text-emerald-400 uppercase">
                                    {{ $flow['badge'] ?? 'STEP' }}
                                </span>
                            </div>
                            <div>
                                <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1 font-mono uppercase">{{ $flow['action'] ?? '-' }}</h4>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed font-sans">{{ $flow['description'] ?? '' }}</p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-zinc-200 dark:border-zinc-800 text-[10px] font-mono text-zinc-400">
                                Aktor: <span class="text-zinc-700 dark:text-zinc-300 font-bold">{{ $flow['actor'] ?? 'Pengguna' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- SECTION 5: ENTERPRISE DATABASE ERD & SCHEMA BLUEPRINT -->
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">05</span>
                        <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Database ERD & Schema (PostgreSQL Strict)</h2>
                    </div>

                    <!-- ERD Tabs & Multi-Language Selector -->
                    <div class="flex flex-wrap items-center gap-2 font-mono text-xs no-print">
                        <!-- Multi-Language Tier 1 Toggle -->
                        <div class="flex items-center border border-zinc-300 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 p-0.5">
                            <span class="px-2 text-[10px] text-zinc-400 font-bold uppercase">LABEL TIER 1:</span>
                            <button 
                                @click="erdLang = 'id'" 
                                :class="erdLang === 'id' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'"
                                class="px-2 py-0.5 text-[10px] font-mono transition"
                            >
                                ID
                            </button>
                            <button 
                                @click="erdLang = 'en'" 
                                :class="erdLang === 'en' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'"
                                class="px-2 py-0.5 text-[10px] font-mono transition"
                            >
                                EN
                            </button>
                        </div>

                        <!-- ERD View Tabs -->
                        <button 
                            @click="erdTab = 'visual'" 
                            :class="erdTab === 'visual' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3z"></path></svg>
                            <span>TOPOLOGY</span>
                        </button>
                        <button 
                            @click="erdTab = 'mermaid'" 
                            :class="erdTab === 'mermaid' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                            <span>MERMAID ERD</span>
                        </button>
                        <button 
                            @click="erdTab = 'table'" 
                            :class="erdTab === 'table' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>DICTIONARY</span>
                        </button>
                    </div>
                </div>

                <!-- 1. Interactive Visual ERD Topology (Connected Entity Relationship Cards) -->
                <div x-show="erdTab === 'visual'" class="mb-6">
                    <div class="bg-zinc-950 border border-zinc-800 p-6 rounded-none relative">
                        <!-- Cardinality Banner -->
                        <div class="mb-6 p-3 bg-zinc-900 border border-zinc-800 flex flex-wrap items-center justify-between gap-3 text-xs font-mono">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-emerald-500 text-black font-bold text-[10px]">RELATIONAL GRAPH</span>
                                <span class="text-zinc-300 text-[11px]">Strict PostgreSQL Foreign Key Cardinalities</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-4 text-[11px] text-zinc-400">
                                <div><strong class="text-emerald-400">users (1)</strong> &bull;--&lt; <strong>sistem_katalog_rumah (N)</strong></div>
                                <div><strong class="text-emerald-400">users (1)</strong> &bull;--&lt; <strong>activity_logs (N)</strong></div>
                                <div><strong class="text-emerald-400">sistem_katalog_rumah (1)</strong> &bull;--&lt; <strong>system_notifications (N)</strong></div>
                            </div>
                        </div>

                        <!-- Entity Schema Cards (2x2 Grid) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($prd['erd_schema']['tables'] ?? [] as $table)
                                <div class="bg-zinc-900 border-2 border-zinc-800 hover:border-emerald-500/60 transition shadow-xl font-mono text-xs">
                                    <!-- Entity Card Header -->
                                    <div class="p-3.5 bg-zinc-950 border-b border-zinc-800 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3z"></path></svg>
                                            <span class="font-black text-white uppercase text-sm tracking-wide">{{ $table['name'] }}</span>
                                        </div>
                                        <span class="px-2 py-0.5 bg-zinc-800 text-emerald-400 border border-emerald-500/30 text-[10px] font-bold">
                                            PK: ULID
                                        </span>
                                    </div>

                                    <div class="px-3.5 py-2 bg-zinc-900/60 border-b border-zinc-800 text-[11px] text-zinc-400 font-sans">
                                        {{ $table['description'] }}
                                    </div>

                                    <!-- Columns Attributes List -->
                                    <div class="divide-y divide-zinc-800/80">
                                        @foreach($table['columns'] ?? [] as $col)
                                            <div class="px-3.5 py-2 flex items-center justify-between gap-2 hover:bg-zinc-800/40 transition">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    @if($col['index'] === 'PRIMARY')
                                                        <span class="px-1.5 py-0.2 bg-emerald-500 text-black font-black text-[9px]">PK</span>
                                                    @elseif(str_contains(strtolower($col['type']), 'foreign') || str_contains($col['name'], '_id'))
                                                        <span class="px-1.5 py-0.2 bg-sky-500 text-white font-black text-[9px]">FK</span>
                                                    @elseif($col['index'] === 'UNIQUE')
                                                        <span class="px-1.5 py-0.2 bg-purple-500 text-white font-black text-[9px]">UK</span>
                                                    @elseif($col['index'] === 'INDEX')
                                                        <span class="px-1.5 py-0.2 bg-zinc-700 text-zinc-300 font-bold text-[9px]">IDX</span>
                                                    @else
                                                        <span class="w-4 inline-block text-zinc-600 text-center">&bull;</span>
                                                    @endif

                                                    <span class="font-bold text-zinc-100 truncate">{{ $col['name'] }}</span>
                                                </div>

                                                <div class="flex items-center gap-2 text-right">
                                                    <span class="text-zinc-500 text-[11px] font-sans truncate max-w-[130px]" x-text="erdLang === 'id' ? '{{ $col['label']['id'] ?? ($col['notes'] ?? '-') }}' : '{{ $col['label']['en'] ?? ($col['name'] ?? '-') }}'"></span>
                                                    <span class="text-emerald-400 text-[10px] font-bold">{{ $col['type'] }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- 2. Mermaid ERD Diagram View -->
                <div x-show="erdTab === 'mermaid'" x-cloak class="mb-6">
                    <div class="overflow-x-auto bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-6 rounded-none">
                        <pre class="mermaid text-center">
erDiagram
    USERS ||--o{ SISTEM_KATALOG_RUMAH : "creates / owns"
    USERS ||--o{ ACTIVITY_LOGS : "logs actions"
    USERS ||--o{ SYSTEM_NOTIFICATIONS : "receives"
    SISTEM_KATALOG_RUMAH ||--o{ SYSTEM_NOTIFICATIONS : "triggers"

@foreach($prd['erd_schema']['tables'] ?? [] as $table)
    {{ strtoupper($table['name']) }} {
        @foreach($table['columns'] ?? [] as $col)
        string {{ str_replace(['-', ' '], '_', $col['name']) }}
        @endforeach
    }
@endforeach
                        </pre>
                    </div>
                </div>

                <!-- 3. PostgreSQL Strict Data Dictionary Table -->
                <div x-show="erdTab === 'table'" x-cloak class="space-y-6">
                    @foreach($prd['erd_schema']['tables'] ?? [] as $table)
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-none overflow-hidden">
                            <div class="bg-zinc-900 text-white px-4 py-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-1 font-mono text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="text-emerald-400 font-bold">TABLE: {{ $table['name'] }}</span>
                                    <span class="text-zinc-400 hidden sm:inline">&bull; {{ $table['description'] }}</span>
                                </div>
                                <span class="text-zinc-400">
                                    PK: {{ $table['primary_key'] ?? 'id (ULID)' }}
                                </span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-zinc-100 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 uppercase font-mono font-bold">
                                            <th class="py-2 px-3">Column Name</th>
                                            <th class="py-2 px-3">Tier 1 Label</th>
                                            <th class="py-2 px-3">Data Type</th>
                                            <th class="py-2 px-3">Index</th>
                                            <th class="py-2 px-3">Nullable</th>
                                            <th class="py-2 px-3">Description / Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 font-mono">
                                        @foreach($table['columns'] ?? [] as $col)
                                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50 transition">
                                                <td class="py-2 px-3 font-bold text-zinc-900 dark:text-zinc-100">{{ $col['name'] }}</td>
                                                <td class="py-2 px-3 text-zinc-700 dark:text-zinc-300 font-sans" x-text="erdLang === 'id' ? '{{ $col['label']['id'] ?? ($col['notes'] ?? '-') }}' : '{{ $col['label']['en'] ?? ($col['name'] ?? '-') }}'"></td>
                                                <td class="py-2 px-3 text-emerald-600 dark:text-emerald-400 font-bold">{{ $col['type'] }}</td>
                                                <td class="py-2 px-3">
                                                    @if($col['index'] === 'PRIMARY')
                                                        <span class="px-1.5 py-0.5 bg-emerald-500 text-black font-bold text-[10px]">PRIMARY</span>
                                                    @elseif(str_contains(strtolower($col['type']), 'foreign') || str_contains($col['name'], '_id'))
                                                        <span class="px-1.5 py-0.5 bg-sky-500 text-white font-bold text-[10px]">FOREIGN</span>
                                                    @elseif($col['index'] === 'UNIQUE')
                                                        <span class="px-1.5 py-0.5 bg-purple-500 text-white font-bold text-[10px]">UNIQUE</span>
                                                    @elseif($col['index'] === 'INDEX')
                                                        <span class="px-1.5 py-0.5 bg-blue-500 text-white font-bold text-[10px]">INDEX</span>
                                                    @else
                                                        <span class="text-zinc-400 text-[10px]">-</span>
                                                    @endif
                                                </td>
                                                <td class="py-2 px-3 text-zinc-500">{{ $col['nullable'] ? 'YES' : 'NO' }}</td>
                                                <td class="py-2 px-3 font-sans text-zinc-600 dark:text-zinc-400">{{ $col['notes'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- SECTION 06: EVALUASI ARSITEKTUR & REKOMENDASI INFRASTRUKTUR (HOSTING, POLA SISTEM & AI DATABASE) -->
            @php
                $archEval = $prd['architecture_evaluation'] ?? \App\Services\PrdGeneratorService::evaluateArchitecture(
                    $blueprint->nama_bisnis ?? $blueprint->client_name,
                    $blueprint->masalah_utama ?? '',
                    $prd['features']['mvp_phase1'] ?? [],
                    $blueprint->alur_kerja ?? '',
                    $blueprint->user_metadata ?? []
                );
            @endphp
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex items-center justify-between gap-2 mb-6 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">06</span>
                        <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Evaluasi Arsitektur & Infrastruktur (AI Database Ready)</h2>
                    </div>
                    <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 border border-emerald-300 dark:border-emerald-800">
                        HIGH INTEGRITY ARCHITECTURE
                    </span>
                </div>

                <!-- Client Architectural Parameters Summary -->
                <div class="mb-8 p-4 bg-zinc-950 border border-zinc-800 font-mono text-xs">
                    <div class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider mb-2">PARAMETER PENILAIAN DARI KUESIONER KLIEN:</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-[11px]">
                        <div class="p-2.5 bg-zinc-900 border border-zinc-800">
                            <span class="text-zinc-500 text-[10px] block">TARGET SKALA / TRAFIK</span>
                            <strong class="text-zinc-200">{{ $archEval['scope_boundaries']['client_scale'] ?? ($blueprint->user_metadata['skala_pengguna'] ?? '0 - 100k User / Bulan') }}</strong>
                        </div>
                        <div class="p-2.5 bg-zinc-900 border border-zinc-800">
                            <span class="text-zinc-500 text-[10px] block">JANGKAUAN PASAR</span>
                            <strong class="text-zinc-200">{{ $archEval['scope_boundaries']['client_market'] ?? ($blueprint->user_metadata['jangkauan_pasar'] ?? 'Domestik Indonesia (IDR)') }}</strong>
                        </div>
                        <div class="p-2.5 bg-zinc-900 border border-zinc-800">
                            <span class="text-zinc-500 text-[10px] block">STANDAR KEPATUHAN</span>
                            <strong class="text-zinc-200">{{ $archEval['scope_boundaries']['client_compliance'] ?? ($blueprint->user_metadata['kepatuhan_keamanan'] ?? 'OWASP Top 10 & Enkripsi') }}</strong>
                        </div>
                        <div class="p-2.5 bg-zinc-900 border border-zinc-800">
                            <span class="text-zinc-500 text-[10px] block">RENCANA ANGGARAN KLIEN</span>
                            <strong class="text-emerald-400">{{ $archEval['scope_boundaries']['client_budget'] ?? ($blueprint->user_metadata['kisaran_budget'] ?? 'Rp 50M - Rp 100M') }}</strong>
                        </div>
                    </div>
                </div>

                <!-- 1. Shared Hosting vs Dedicated VPS Assessment -->
                <div class="mb-8">
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500"></span>
                        1. Analisis Bobot & Kelayakan Lingkungan Hosting: Shared Host vs Dedicated VPS
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Shared Hosting (Rejected) -->
                        <div class="p-5 bg-rose-500/5 dark:bg-rose-950/20 border-2 border-rose-500/40 font-mono text-xs">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="font-black uppercase text-rose-600 dark:text-rose-400 text-sm">Shared Hosting</span>
                                <span class="px-2 py-0.5 bg-rose-500 text-white text-[9px] font-bold uppercase">DISQUALIFIED (DITOLAK)</span>
                            </div>
                            <p class="text-zinc-600 dark:text-zinc-400 font-sans text-xs mb-3">
                                Shared hosting tidak memenuhi standar integritas sistem AI dan basis data berskala tinggi karena limitasi mendasar:
                            </p>
                            <ul class="space-y-2 text-zinc-700 dark:text-zinc-300 text-[11px]">
                                <li class="flex items-start gap-2">
                                    <span class="text-rose-500 font-bold">&times;</span>
                                    <span><strong>Ketiadaan pgvector:</strong> Tidak mendukung ekstensi C-level <code class="bg-rose-100 dark:bg-rose-900/40 px-1">pgvector</code> untuk pencarian semantik vektor AI.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-rose-500 font-bold">&times;</span>
                                    <span><strong>PHP Execution Timeout:</strong> Dibatasi 30-60 detik yang akan membunuh koneksi saat LLM AI melakukan deep-reasoning streaming.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-rose-500 font-bold">&times;</span>
                                    <span><strong>Tidak Ada Queue Supervisor:</strong> Ketiadaan daemon Redis worker persistent untuk memproses job asynchronous di latar belakang.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-rose-500 font-bold">&times;</span>
                                    <span><strong>Noisy Neighbors Risk:</strong> Throttling CPU tak terprediksi akibat lonjakan trafik website lain dalam satu server bersama.</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Dedicated VPS (Recommended) -->
                        <div class="p-5 bg-emerald-500/5 dark:bg-emerald-950/20 border-2 border-emerald-500 font-mono text-xs">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="font-black uppercase text-emerald-600 dark:text-emerald-400 text-sm">Dedicated VPS (Docker / Nixpacks)</span>
                                <span class="px-2 py-0.5 bg-emerald-500 text-black text-[9px] font-bold uppercase">MANDATORY (WAJIB)</span>
                            </div>
                            <p class="text-zinc-600 dark:text-zinc-400 font-sans text-xs mb-3">
                                Menjamin stabilitas eksekusi AI dan isolasi komputasi penuh dengan rasio performa-harga optimal:
                            </p>
                            <ul class="space-y-2 text-zinc-700 dark:text-zinc-300 text-[11px]">
                                <li class="flex items-start gap-2">
                                    <span class="text-emerald-500 font-bold">&check;</span>
                                    <span><strong>PostgreSQL 16+ & pgvector Native:</strong> Vector embeddings tersimpan co-located langsung di dalam engine database relasional.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-emerald-500 font-bold">&check;</span>
                                    <span><strong>Dedicated Resource Isolation:</strong> 100% alokasi vCPU, RAM, dan NVMe storage bebas interferensi pihak luar.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-emerald-500 font-bold">&check;</span>
                                    <span><strong>Persistent Redis Supervisor:</strong> Antrean background task AI agent & notifikasi berjalan kontinyu 24/7.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-emerald-500 font-bold">&check;</span>
                                    <span><strong>Zero-Downtime Deployment:</strong> Pipeline Nixpacks & reverse-proxy Nginx HTTP/2 dengan auto-healing SSL.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 2. Monolith vs Decoupled Assessment -->
                <div class="mb-8">
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500"></span>
                        2. Penilaian Pola Arsitektur: Modern Monolith vs Decoupled (Microservices)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Modern Monolith -->
                        <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-emerald-500/60 font-mono text-xs">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="font-bold uppercase text-zinc-900 dark:text-zinc-100">Modern Monolith (Laravel 13 + Filament v5)</span>
                                <span class="px-2 py-0.5 bg-emerald-500 text-black text-[9px] font-bold">REKOMENDASI FASE 1</span>
                            </div>
                            <ul class="space-y-1.5 text-zinc-600 dark:text-zinc-400 text-[11px]">
                                <li>&bull; <strong>Zero Network Latency:</strong> Komunikasi antar modul berjalan intra-process O(1) tanpa overhead HTTP network antar-microservices.</li>
                                <li>&bull; <strong>Pangkas Biaya Infrastruktur 60-70%:</strong> Satu kesatuan container deployment menghemat anggaran server staging & produksi dibanding kluster microservices.</li>
                                <li>&bull; <strong>ACID Strict Integrity:</strong> Integritas transaksi finansial tanpa rumitnya distributed 2-phase commit.</li>
                                <li>&bull; <strong>Rapid Time-to-Market:</strong> Sinkronisasi instan antara model bisnis Eloquent dan dashboard Filament.</li>
                                <li>&bull; <strong>Island Architecture Frontend:</strong> Memberikan fluiditas interaksi 60fps setara SPA dengan stabilitas dan kecepatan SEO Server-Side Rendering.</li>
                            </ul>
                        </div>

                        <!-- Decoupled -->
                        <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 font-mono text-xs">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="font-bold uppercase text-zinc-700 dark:text-zinc-300">Decoupled / Microservices Cluster</span>
                                <span class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[9px] font-bold">FASE ROADMAP LANJUTAN</span>
                            </div>
                            <ul class="space-y-1.5 text-zinc-500 text-[11px]">
                                <li>&bull; Hanya dianjurkan jika tim rekayasa sudah berkembang menjadi lebih dari 15-20 developer di repositori terpisah.</li>
                                <li>&bull; Menambah biaya operasional server terpisah (Backend API server + Frontend Next.js node cluster terpisah).</li>
                                <li>&bull; Meningkatkan latensi round-trip HTTP dan beban autentikasi token JWT di setiap request interaksi.</li>
                                <li>&bull; Membutuhkan orkestrasi Kubernetes kompleks yang tidak efisien untuk peluncuran perdana (MVP).</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 3. Global Scale Analysis (Matrix) -->
                <div class="mb-8">
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500"></span>
                        3. Analisis Skala Jangkauan Pengguna Dunia (Global Reach Matrix)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 font-mono text-xs">
                        @foreach($archEval['global_scale_analysis']['tiers'] ?? [] as $scaleTier)
                            <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-[9px] uppercase px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold block w-fit mb-2">
                                    {{ $scaleTier['status'] }}
                                </span>
                                <h4 class="font-black text-sm text-zinc-900 dark:text-zinc-100 mb-1">{{ $scaleTier['scale'] }}</h4>
                                <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold mb-2">{{ $scaleTier['architecture'] }}</div>
                                <p class="text-[10px] text-zinc-500 dark:text-zinc-400 leading-normal">{{ $scaleTier['verdict'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 4. Budget & TCO Efficiency Analysis -->
                <div class="mb-8">
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500"></span>
                        4. Analisis Anggaran Klien & Efisiensi Modal (TCO Comparison)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 font-mono text-xs">
                        <div class="p-5 bg-emerald-500/5 dark:bg-emerald-950/20 border-2 border-emerald-500">
                            <span class="text-[9px] uppercase font-bold text-emerald-600 dark:text-emerald-400 block mb-1">EFISIENSI MODAL TINGGI (90%)</span>
                            <h4 class="font-black text-base text-zinc-900 dark:text-zinc-100 mb-2">Modern Monolith Standard</h4>
                            <div class="space-y-1.5 text-[11px] text-zinc-700 dark:text-zinc-300">
                                <div>&bull; Biaya Server Bulanan: <strong>Rp 350.000 - Rp 1.500.000 / bln</strong></div>
                                <div>&bull; DevOps Headcount: <strong>0 FTE (Automated Nixpacks CI/CD)</strong></div>
                                <div class="pt-2 border-t border-emerald-500/30 text-emerald-700 dark:text-emerald-300">
                                    90% anggaran klien dialokasikan murni untuk fitur bisnis & akuisisi pengguna, bukan untuk beban overhead infrastruktur.
                                </div>
                            </div>
                        </div>

                        <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                            <span class="text-[9px] uppercase font-bold text-zinc-400 block mb-1">OVERHEAD MODAL TINGGI (CAPITAL HEAVY)</span>
                            <h4 class="font-black text-base text-zinc-700 dark:text-zinc-300 mb-2">Decoupled Microservices</h4>
                            <div class="space-y-1.5 text-[11px] text-zinc-500">
                                <div>&bull; Biaya Kluster Cloud: <strong>Rp 8.000.000 - Rp 25.000.000+ / bln</strong></div>
                                <div>&bull; DevOps Headcount: <strong>1-2 Dedicated DevOps Engineers (Rp 20-40 jt/bln)</strong></div>
                                <div class="pt-2 border-t border-zinc-200 dark:border-zinc-800">
                                    60% anggaran tersedot hanya untuk memelihara kluster Kubernetes, API Gateway, Service Mesh, dan distributed tracing.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. The 4 Decoupling Triggers (Warning / Criteria checklist) -->
                <div class="mb-8 p-5 bg-zinc-950 border-2 border-amber-500/70 text-zinc-200 font-mono text-xs">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-3 h-3 bg-amber-500"></span>
                        <h4 class="font-black text-sm uppercase text-amber-400">4 Faktor Penentu Mutlak Kapan Sistem Wajib Decoupled</h4>
                    </div>
                    <p class="text-zinc-400 text-xs mb-4 font-sans">
                        Sistem tidak boleh dipecah menjadi microservices kecuali satu atau lebih pemicu mutlak berikut terpenuhi:
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-[11px]">
                        @foreach($archEval['decoupling_threshold_triggers']['triggers'] ?? [] as $trigger)
                            <div class="p-3 bg-zinc-900 border border-zinc-800">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-1.5 py-0.2 bg-amber-500 text-black font-black text-[9px]">{{ $trigger['number'] }}</span>
                                    <span class="font-bold text-white text-xs">{{ $trigger['title'] }}</span>
                                </div>
                                <p class="text-zinc-400 text-[10px] leading-relaxed">{{ $trigger['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 6. Strict Scope Lock Boundaries (In-Scope vs Strict Out-of-Scope) -->
                <div class="mb-8">
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500"></span>
                        6. Matriks Batasan Ruang Lingkup (Strict Scope Lock & Anti-Feature Creep)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 font-mono text-xs">
                        <div class="p-5 bg-emerald-500/5 dark:bg-emerald-950/20 border border-emerald-500/40">
                            <span class="text-[9px] uppercase font-bold text-emerald-600 dark:text-emerald-400 block mb-2">&check; IN-SCOPE (FASE 1 - MVP DIJAMIN KONTRAK)</span>
                            <ul class="space-y-1.5 text-zinc-700 dark:text-zinc-300 text-[11px]">
                                @foreach($archEval['scope_boundaries']['in_scope'] ?? [] as $inScope)
                                    <li class="flex items-start gap-2">
                                        <span class="text-emerald-500 font-bold">&bull;</span>
                                        <span>{{ $inScope }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="p-5 bg-rose-500/5 dark:bg-rose-950/20 border border-rose-500/40">
                            <span class="text-[9px] uppercase font-bold text-rose-600 dark:text-rose-400 block mb-2">&times; STRICT OUT-OF-SCOPE (DIKUNCI DARI KONTRAK UTAMA)</span>
                            <ul class="space-y-1.5 text-zinc-700 dark:text-zinc-300 text-[11px]">
                                @foreach($archEval['scope_boundaries']['out_of_scope'] ?? [] as $outScope)
                                    <li class="flex items-start gap-2">
                                        <span class="text-rose-500 font-bold">&times;</span>
                                        <span>{{ $outScope }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 7. AI-Ready PostgreSQL Database Blueprint -->
                <div class="mb-8 p-5 bg-zinc-950 border border-zinc-800 text-zinc-200 font-mono text-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-800 pb-3 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-emerald-500"></span>
                            <span class="font-bold uppercase text-sm text-white">7. Basis Data PostgreSQL 16+ (pgvector & Strict ULID)</span>
                        </div>
                        <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 text-[10px] font-bold">
                            AI-READY DATABASE ENGINE
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-[11px] leading-relaxed">
                        <div class="space-y-2">
                            <div>
                                <strong class="text-emerald-400 block mb-0.5">&bull; Ekstensi pgvector Co-Location:</strong>
                                Vector embeddings (1536-dim / 3072-dim) disimpan berdampingan langsung dengan data transaksi dan pengguna tanpa memerlukan SaaS database vektor terpisah seperti Pinecone atau Milvus.
                            </div>
                            <div>
                                <strong class="text-emerald-400 block mb-0.5">&bull; Indeks HNSW (Hierarchical Navigable Small World):</strong>
                                Pencarian kedekatan semantik vektor dengan kompleksitas sub-millisecond O(log N) untuk RAG (Retrieval-Augmented Generation) berkecepatan tinggi.
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <strong class="text-emerald-400 block mb-0.5">&bull; Strict ULID Primary Key Standard:</strong>
                                Format string 26-karakter bebas sequence lock yang menjamin pembagian partisi terdistribusi dan keystone cursor pagination O(1) tanpa degradasi performa.
                            </div>
                            <div>
                                <strong class="text-emerald-400 block mb-0.5">&bull; Dynamic JSONB Indexing:</strong>
                                Mendukung penyimpanan context window percakapan agen AI Gemini Ultra serta fleksibilitas metadata dokumen.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 8. Recommended Tools Grid -->
                <div>
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500"></span>
                        8. Rekomendasi Stack & Tools Rekayasa Perangkat Lunak
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 font-mono text-xs">
                        @foreach($archEval['recommended_tools'] ?? [] as $tool)
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-[9px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ $tool['category'] }}</span>
                                <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">{{ $tool['name'] }}</h4>
                                <p class="text-[10px] text-zinc-500 dark:text-zinc-400 leading-normal">{{ $tool['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- SECTION 07: OPSI VELOCITY PENGERJAAN & AKSESORIS AI GEMINI ULTRA (PRICING & SPRINT SELECTION) -->
            <section class="bg-white dark:bg-zinc-900 border-2 border-emerald-500 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-6 h-6 bg-emerald-500 text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">07</span>
                            <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Opsi Velocity & Akselerasi AI Gemini Ultra</h2>
                        </div>
                        <p class="text-zinc-500 dark:text-zinc-400 text-xs font-mono">
                            Pilih kecepatan penyelesaian sistem. Kecepatan akselerasi melibatkan alokasi komputasi cloud Swarm AI Gemini Ultra dan paralel engineering squad.
                        </p>
                    </div>
                    <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-3 py-1 border border-emerald-300 dark:border-emerald-800 self-start sm:self-auto">
                        PILIH PAKET UNTUK KONTRAK
                    </span>
                </div>

                <!-- Mathematical Cost Formula Breakdown Banner -->
                <div class="mb-6 p-4 bg-zinc-950 border border-zinc-800 text-zinc-300 font-mono text-xs">
                    <div class="flex items-center justify-between gap-2 border-b border-zinc-800 pb-2 mb-3">
                        <strong class="uppercase text-emerald-400 font-bold tracking-wider">FORMULA LEVEL BIAYA AKSELERASI SWARM AI:</strong>
                        <span class="text-[10px] text-zinc-400">TRANSPARENT PRICING MODEL</span>
                    </div>
                    <div class="p-3 bg-zinc-900 border border-zinc-800 font-mono text-center text-xs sm:text-sm text-emerald-400 font-bold mb-3 overflow-x-auto">
                        Total Investasi = Base Engineering Fee + (&Delta; Velocity Factor &times; Sewa Swarm AI Ultra Cloud) + Dedicated Concurrency Squad
                    </div>
                    <p class="text-zinc-400 text-[11px] leading-relaxed font-sans">
                        Pengerjaan kilat tidak sekadar menambah jam kerja manusia, melainkan mengalokasikan <strong>Swarm AI Agent Parallel Workers (Gemini Ultra)</strong> dengan kuota inferensi jutaan token per menit untuk auto-synthesize skema database, unit test otomatis, dan refactoring real-time tanpa antrean cloud.
                    </p>
                </div>

                <!-- Dynamic Comparative Velocity & Budget Pricing Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 font-mono">
                    @foreach($pricingTiers as $tierItem)
                        @php
                            $isFastTrackOrRecommended = str_contains($tierItem['id'], 'fast_track') || str_contains($tierItem['id'], 'community_plus') || str_contains($tierItem['id'], 'community_starter');
                            $isEmergencyOrEnterprise = str_contains($tierItem['id'], 'hyper_sprint') || str_contains($tierItem['id'], 'community_enterprise');
                        @endphp
                        <div 
                            @click="selectedTier = '{{ $tierItem['id'] }}'"
                            :class="selectedTier === '{{ $tierItem['id'] }}' 
                                ? '{{ $isEmergencyOrEnterprise ? 'border-2 border-amber-500 bg-amber-500/10 dark:bg-amber-950/30 shadow-lg' : 'border-2 border-emerald-500 bg-emerald-500/10 dark:bg-emerald-950/30 shadow-lg' }}' 
                                : 'border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 hover:border-zinc-400'"
                            class="p-5 cursor-pointer transition relative flex flex-col justify-between"
                        >
                            @if(str_contains($tierItem['id'], 'fast_track') || str_contains($tierItem['id'], 'community_plus'))
                                <div class="absolute -top-3 right-4 px-2 py-0.5 bg-emerald-500 text-black text-[9px] font-black uppercase tracking-wider">
                                    ⚡ RECOMMENDED
                                </div>
                            @elseif($isEmergencyOrEnterprise)
                                <div class="absolute -top-3 right-4 px-2 py-0.5 bg-amber-500 text-black text-[9px] font-black uppercase tracking-wider">
                                    🔥 TOP TIER
                                </div>
                            @endif

                            <div>
                                <div class="flex items-center justify-between gap-1 mb-2">
                                    <span class="text-[9px] uppercase px-1.5 py-0.5 {{ $isEmergencyOrEnterprise ? 'bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/40' : 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/40' }} font-bold">
                                        {{ $tierItem['badge'] }}
                                    </span>
                                    <span x-show="selectedTier === '{{ $tierItem['id'] }}'" class="w-2 h-2 rounded-full {{ $isEmergencyOrEnterprise ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                                </div>
                                <h3 class="text-base font-black uppercase text-zinc-900 dark:text-zinc-100">{{ $tierItem['name'] }}</h3>
                                <div class="text-xl font-black {{ $isEmergencyOrEnterprise ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }} my-2">
                                    Rp {{ number_format($tierItem['contract_amount'], 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-emerald-600 dark:text-emerald-400 font-bold mb-2">
                                    Termin DP 50%: Rp {{ number_format($tierItem['dp_amount'], 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-zinc-700 dark:text-zinc-300 mb-3 bg-zinc-100 dark:bg-zinc-900 p-2 border border-zinc-200 dark:border-zinc-800">
                                    <strong>Formula:</strong> {{ $tierItem['cost_formula'] }}
                                </div>
                                <ul class="space-y-1.5 text-[11px] text-zinc-600 dark:text-zinc-400 mb-4 border-t border-zinc-200 dark:border-zinc-800 pt-3">
                                    <li>&bull; Durasi: <strong>{{ $tierItem['duration'] }}</strong> ({{ $tierItem['speed_multiplier'] }})</li>
                                    <li>&bull; Alokasi: {{ $tierItem['squad_allocation'] }}</li>
                                    <li>&bull; Engine: {{ $tierItem['ai_quota_spec'] }}</li>
                                    @foreach($tierItem['ai_swarm_specs'] ?? [] as $spec)
                                        <li>&bull; {{ $spec }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button 
                                type="button" 
                                :class="selectedTier === '{{ $tierItem['id'] }}' 
                                    ? '{{ $isEmergencyOrEnterprise ? 'bg-amber-500 text-black font-bold' : 'bg-emerald-500 text-black font-bold' }}' 
                                    : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300'"
                                class="w-full py-2 text-xs uppercase tracking-wider transition"
                            >
                                <span x-text="selectedTier === '{{ $tierItem['id'] }}' ? '&check; PAKET TERPILIH' : 'PILIH PAKET'"></span>
                            </button>
                        </div>
                    @endforeach
                </div>

            <!-- SECTION 08: TIMELINE & GANTT MILESTONE (ALIGNED TO VELOCITY) -->
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex items-center justify-between gap-2 mb-4 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">08</span>
                        <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Timeline & Milestone Proyek (Velocity Aligned)</h2>
                    </div>
                    <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 border border-emerald-300 dark:border-emerald-800">
                        Target: <span x-text="tierAmounts[selectedTier].days"></span>
                    </span>
                </div>

                <div class="space-y-2.5 font-mono text-xs">
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-emerald-500"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 1: Architecture, pgvector Setup & ULID Database Migrations</span>
                        </div>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold" x-text="selectedTier === 'hyper_sprint' ? 'Hari 1 - 2' : (selectedTier === 'fast_track' ? 'Hari 1 - 3' : 'Hari 1 - 5')">Hari 1 - 3</span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-zinc-400"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 2: Core Business Logic & Filament Admin v5 Engine</span>
                        </div>
                        <span class="text-zinc-500" x-text="selectedTier === 'hyper_sprint' ? 'Hari 3 - 4' : (selectedTier === 'fast_track' ? 'Hari 4 - 7' : 'Hari 6 - 16')">Hari 4 - 7</span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-zinc-400"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 3: Frontend Island UI (React 19) & User Flow Pipeline</span>
                        </div>
                        <span class="text-zinc-500" x-text="selectedTier === 'hyper_sprint' ? 'Hari 5' : (selectedTier === 'fast_track' ? 'Hari 8 - 10' : 'Hari 17 - 23')">Hari 8 - 10</span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-zinc-400"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 4: AI Vector Integration, Security Audit & Stress Test</span>
                        </div>
                        <span class="text-zinc-500" x-text="selectedTier === 'hyper_sprint' ? 'Hari 6' : (selectedTier === 'fast_track' ? 'Hari 11 - 12' : 'Hari 24 - 27')">Hari 11 - 12</span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-zinc-400"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 5: Production Dedicated VPS Deployment & UAT Handover</span>
                        </div>
                        <span class="text-zinc-500" x-text="selectedTier === 'hyper_sprint' ? 'Hari 7' : (selectedTier === 'fast_track' ? 'Hari 13 - 14' : 'Hari 28 - 30')">Hari 13 - 14</span>
                    </div>
                </div>
            </section>

            <!-- SECTION 09: STANDAR TATA KELOLA, KUALITAS & SLA SERAH TERIMA (GOVERNANCE & SLA) -->
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex items-center justify-between gap-2 mb-6 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">09</span>
                        <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Tata Kelola, Kualitas & SLA Serah Terima</h2>
                    </div>
                    <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 border border-emerald-300 dark:border-emerald-800">
                        ENTERPRISE SERVICE LEVEL AGREEMENT
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 font-mono text-xs mb-6">
                    <!-- 1. Definition of Done (DoD) -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2 h-2 bg-emerald-500"></span>
                            <h3 class="font-bold uppercase text-zinc-900 dark:text-zinc-100 text-xs">1. Kriteria Penyelesaian Resmi (Definition of Done)</h3>
                        </div>
                        <ul class="space-y-2 text-zinc-600 dark:text-zinc-400 text-[11px]">
                            @foreach($prd['governance_and_sla']['definition_of_done'] ?? [
                                'Seluruh fitur MVP Fase 1 berjalan sesuai spesifikasi di server Staging & Production.',
                                'Lolos audit keamanan dasar (CSRF token, sanitasi input XSS, proteksi SQL Injection, & HTTPS SSL).',
                                'Skema basis data relasional PostgreSQL dengan Primary Key ULID terverifikasi.',
                                'Dasbor admin Filament v5 dapat diakses oleh peran Superadmin / Staff yang ditunjuk.',
                                'Sesi pelatihan administrasi singkat dan serah terima kredensial resmi sistem.'
                            ] as $dod)
                                <li class="flex items-start gap-2">
                                    <span class="text-emerald-500 font-bold">&check;</span>
                                    <span>{{ $dod }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- 2. Browser & Device Support Matrix -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2 h-2 bg-emerald-500"></span>
                            <h3 class="font-bold uppercase text-zinc-900 dark:text-zinc-100 text-xs">2. Matriks Dukungan Browser & Perangkat</h3>
                        </div>
                        <div class="space-y-3 text-[11px]">
                            <div class="p-2.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 dark:text-emerald-300">
                                <strong class="block mb-1">&check; PERANGKAT & BROWSER DIDUKUNG RESMI:</strong>
                                {{ $prd['governance_and_sla']['browser_device_matrix']['supported'] ?? 'Google Chrome, Apple Safari, Mozilla Firefox, Microsoft Edge (rilis 2 tahun terakhir); iOS Safari 15+; Android Chrome 100+.' }}
                            </div>
                            <div class="p-2.5 bg-rose-500/10 border border-rose-500/30 text-rose-800 dark:text-rose-300">
                                <strong class="block mb-1">&times; PERAMBAN DI LUAR RUANG LINGKUP (EXCLUDED):</strong>
                                {{ $prd['governance_and_sla']['browser_device_matrix']['unsupported'] ?? 'Internet Explorer 11, Opera Mini data-saving mode, UC Browser legacy rendering engine, dan peramban ponsel non-standar.' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 font-mono text-xs">
                    <!-- 3. Warranty & Bug-Fix Period -->
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <span class="text-[9px] uppercase px-1.5 py-0.5 bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold block w-fit mb-2">
                            GARANSI PENUH 30 HARI
                        </span>
                        <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">Garansi Perbaikan Bug</h4>
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Gratis perbaikan terhadap bug, error, atau ketidaksesuaian fungsi MVP Fase 1 selama 30 hari kalender setelah Go-Live.
                        </p>
                    </div>

                    <!-- 4. Content Bottleneck Protocol -->
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <span class="text-[9px] uppercase px-1.5 py-0.5 bg-amber-500/20 text-amber-600 dark:text-amber-400 font-bold block w-fit mb-2">
                            BATAS MATERI 7 HARI
                        </span>
                        <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">Klausul Konten Klien</h4>
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Jika materi teks/foto belum diberikan dalam 7 hari kerja, developer berhak menggunakan dummy/placeholder demi menjaga ketepatan waktu rilis.
                        </p>
                    </div>

                    <!-- 5. Third-Party Recurring Transparency -->
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <span class="text-[9px] uppercase px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold block w-fit mb-2">
                            BIAYA BERULANG JUJUR
                        </span>
                        <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">Estimasi Pihak Ketiga</h4>
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Domain tahunan (Rp 150rb-250rb/thn) & hosting (Rp 50rb-150rb/bln) dibayarkan langsung ke penyedia cloud resmi tanpa markup tersembunyi.
                        </p>
                    </div>
                </div>
            </section>

            <!-- SECTION 10: SCOPE FREEZE, DIGITAL CONTRACT & DP MIDTRANS (CRUCIAL) -->
            <section class="bg-zinc-900 text-white border-2 border-emerald-500 p-6 sm:p-8 mb-8 rounded-none no-print">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-800 pb-6 mb-6">
                    <div>
                        <span class="px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-widest bg-emerald-500 text-black inline-block mb-2 rounded-none">
                            LEGAL & PAYMENT PROTOCOL
                        </span>
                        <h3 class="text-xl sm:text-2xl font-black uppercase tracking-tight flex items-center gap-2">
                            <span class="w-6 h-6 bg-emerald-500 text-black font-mono font-bold text-xs inline-flex items-center justify-center rounded-none">10</span>
                            <span>Kunci Scope Proyek & Pembayaran DP</span>
                        </h3>
                        <p class="text-zinc-400 text-xs mt-1 font-sans">
                            Pengerjaan proyek resmi dimulai setelah penandatanganan kontrak digital dan konfirmasi DP via Midtrans Escrow.
                        </p>
                    </div>
                    <div class="text-left sm:text-right font-mono">
                        <span class="text-zinc-400 text-xs block">TERMIN TERPILIH: <span class="text-white font-bold" x-text="tierAmounts[selectedTier].name"></span></span>
                        <span class="text-xl font-black text-emerald-400" x-text="'DP (50%): Rp ' + tierAmounts[selectedTier].dp.toLocaleString('id-ID')"></span>
                        <span class="text-[10px] text-zinc-400 block" x-text="'Total Kontrak: Rp ' + tierAmounts[selectedTier].contract.toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <!-- Scope Lock Policy Notice -->
                <div class="p-4 bg-zinc-950 border border-zinc-800 text-zinc-400 text-xs font-mono leading-relaxed mb-6">
                    <strong class="text-amber-400 block mb-1 uppercase font-bold">&bull; Batasan Ruang Lingkup & Ketentuan Tambah Fitur (Change Request)</strong>
                    Seluruh fitur yang tertera di atas terkunci secara hukum dalam Kontrak Induk. Apabila di kemudian hari Klien menghendaki penambahan fitur baru di luar spesifikasi ini, penambahan tersebut akan diakomodasikan melalui <strong>Change Request (CR) / Addendum</strong> terpisah dengan perhitungan biaya dan tambahan hari kerja tersendiri tanpa mengganggu jadwal kontrak utama.
                </div>

                <!-- Action Buttons: Sign Contract & Pay DP -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <form method="POST" action="{{ route('blueprint.generate-contract', $blueprint->slug) }}" class="m-0">
                        @csrf
                        <input type="hidden" name="tier" :value="selectedTier">
                        <button 
                            type="submit"
                            class="w-full h-full bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none transition flex items-center justify-center gap-2"
                        >
                            <span>Tanda Tangani Kontrak & Kunci Scope</span>
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                    </form>

                    <button 
                        type="button" 
                        @click="paymentModalOpen = true"
                        class="w-full h-full bg-zinc-800 hover:bg-zinc-700 text-white font-mono font-bold text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none border border-zinc-700 transition flex items-center justify-center gap-2"
                    >
                        <span>Instruksi Bayar DP (Midtrans)</span>
                        <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </button>

                    <form method="POST" action="{{ route('cart.add', $blueprint->slug) }}" class="m-0">
                        @csrf
                        <input type="hidden" name="tier" :value="selectedTier">
                        <button 
                            type="submit"
                            class="w-full h-full bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-mono font-bold text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none border border-zinc-700 transition flex items-center justify-center gap-2"
                        >
                            <span>Tambahkan ke Cart</span>
                            <svg class="w-4 h-4 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </button>
                    </form>
                </div>
            </section>

        </main>
    @endif

    <!-- Interactive Midtrans Escrow Payment Modal -->
    <div 
        x-show="paymentModalOpen" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 font-mono text-xs"
        @keydown.escape.window="paymentModalOpen = false"
    >
        <div 
            class="bg-white dark:bg-zinc-900 border-2 border-emerald-500 max-w-lg w-full p-6 shadow-2xl relative"
            @click.outside="paymentModalOpen = false"
        >
            <!-- Close Button -->
            <button 
                @click="paymentModalOpen = false" 
                class="absolute top-4 right-4 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 text-lg font-bold"
            >
                &times;
            </button>

            <!-- Modal Header -->
            <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-4">
                <span class="px-2 py-0.5 bg-emerald-500 text-black text-[10px] font-bold uppercase tracking-wider">
                    MIDTRANS SECURE ESCROW PAYMENT
                </span>
                <h3 class="text-xl font-black uppercase text-zinc-900 dark:text-zinc-100 mt-2">
                    Instruksi Pembayaran DP (50%)
                </h3>
                <p class="text-zinc-500 dark:text-zinc-400 text-xs mt-1 font-sans">
                    Proyek: <strong>{{ $blueprint->nama_bisnis ?: $blueprint->client_name }}</strong>
                </p>
            </div>

            <!-- Invoice Summary Card -->
            <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 mb-4 space-y-2">
                <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                    <span>Opsi Velocity Terpilih</span>
                    <span class="font-bold text-zinc-900 dark:text-zinc-100" x-text="tierAmounts[selectedTier].name"></span>
                </div>
                <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                    <span>Nilai Total Kontrak</span>
                    <span class="font-bold text-zinc-900 dark:text-zinc-100" x-text="'Rp ' + tierAmounts[selectedTier].contract.toLocaleString('id-ID')"></span>
                </div>
                <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                    <span>Termin DP (Uang Muka)</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">50% di Muka</span>
                </div>
                <div class="border-t border-zinc-200 dark:border-zinc-800 pt-2 flex justify-between items-baseline">
                    <span class="font-bold uppercase text-zinc-900 dark:text-zinc-100">Total Tagihan DP</span>
                    <span class="text-lg font-black text-emerald-600 dark:text-emerald-400" x-text="'Rp ' + tierAmounts[selectedTier].dp.toLocaleString('id-ID')"></span>
                </div>
                <div class="text-[10px] text-zinc-400 pt-1">
                    ORDER ID: NPRO-DP-{{ strtoupper(substr($blueprint->id, 0, 8)) }}
                </div>
            </div>

            <!-- Payment Methods Info -->
            <div class="space-y-2 mb-6">
                <div class="text-[11px] font-bold uppercase text-zinc-700 dark:text-zinc-300">
                    Kanal Pembayaran Otomatis:
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-[10px]">
                    <div class="p-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 font-bold">QRIS (GoPay/OVO)</div>
                    <div class="p-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 font-bold">BCA VA</div>
                    <div class="p-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 font-bold">Mandiri Bill</div>
                    <div class="p-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 font-bold">Kartu Kredit</div>
                </div>
            </div>

            <!-- Actions -->
            <div class="space-y-2">
                <a 
                    href="https://app.sandbox.midtrans.com/snap/v2/vtweb/demo-neriahpro-dp" 
                    target="_blank" 
                    rel="noopener noreferrer"
                    class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs uppercase tracking-wider py-3.5 px-4 text-center block transition shadow-lg"
                >
                    Bayar Sekarang via Midtrans Snap &rarr;
                </a>
                
                <form method="POST" action="{{ route('cart.add', $blueprint->slug) }}" class="m-0">
                    @csrf
                    <input type="hidden" name="tier" :value="selectedTier">
                    <button 
                        type="submit" 
                        class="w-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold text-xs uppercase tracking-wider py-2.5 px-4 text-center block transition border border-zinc-300 dark:border-zinc-700"
                    >
                        Simpan ke Cart Belanja
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Global Footer -->
    <footer class="bg-white dark:bg-zinc-900 text-zinc-500 dark:text-zinc-400 py-6 px-4 text-center text-xs border-t border-zinc-200 dark:border-zinc-800 font-mono no-print">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} NERIAH PRO HUB &bull; ULTIMATE PRD & ARCHITECTURE BLUEPRINT</p>
            <p class="text-zinc-400 dark:text-zinc-500">ENTERPRISE ARCHITECTURE // HIGH-INTEGRITY DATA</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timerEl = document.getElementById('nav-cart-timer');
            if (timerEl) {
                let remaining = parseInt(timerEl.getAttribute('data-rem'), 10);
                if (!isNaN(remaining) && remaining > 0) {
                    setInterval(function() {
                        remaining--;
                        if (remaining <= 0) {
                            timerEl.textContent = '⏱️ EXPIRED';
                            timerEl.classList.remove('text-amber-500');
                            timerEl.classList.add('text-rose-500');
                        } else {
                            const h = Math.floor(remaining / 3600);
                            const m = Math.floor((remaining % 3600) / 60);
                            const s = Math.floor(remaining % 60);
                            timerEl.textContent = '⏱️ ' + [
                                h.toString().padStart(2, '0'),
                                m.toString().padStart(2, '0'),
                                s.toString().padStart(2, '0')
                            ].join(':');
                        }
                    }, 1000);
                }
            }
        });
    </script>
</body>
</html>
