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
</head>
<body class="bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex flex-col transition-colors duration-200">

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
                    <span>Cetak / PDF</span>
                </button>
                <a href="/blueprint" class="px-3 py-1 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black text-xs font-mono uppercase font-bold rounded-none transition">
                    + Proposal Baru
                </a>
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
                                POSTGRESQL STRICT ULID
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
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex items-center gap-2 mb-4 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">04</span>
                    <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Alur Kerja Utama (User Flow)</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($prd['workflow'] ?? [] as $flow)
                        <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 rounded-none flex flex-col justify-between">
                            <div class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black text-xs font-mono font-bold flex items-center justify-center rounded-none mb-3">
                                {{ $flow['step'] ?? $loop->iteration }}
                            </div>
                            <div>
                                <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1 font-mono uppercase">{{ $flow['action'] ?? '-' }}</h4>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed font-sans">{{ $flow['description'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- SECTION 5: ENTERPRISE DATABASE ERD & SCHEMA BLUEPRINT -->
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">05</span>
                        <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Database ERD & Schema (PostgreSQL Strict)</h2>
                    </div>
                    <span class="px-2.5 py-0.5 text-xs font-mono font-bold uppercase bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800 rounded-none">
                        ULID Primary Key &bull; O(1) Complexity
                    </span>
                </div>

                <div class="space-y-6">
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
                                                <td class="py-2 px-3 text-emerald-600 dark:text-emerald-400">{{ $col['type'] }}</td>
                                                <td class="py-2 px-3">
                                                    @if($col['index'] === 'PRIMARY')
                                                        <span class="px-1.5 py-0.5 bg-emerald-500 text-black font-bold text-[10px]">PRIMARY</span>
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

            <!-- SECTION 6: TIMELINE & GANTT MILESTONE (ALIGNED TO WORKING DAYS) -->
            @php
                preg_match('/(\d+)/', $blueprint->target_waktu ?? '30', $matches);
                $totalDays = !empty($matches[1]) ? (int)$matches[1] : 30;
                if ($totalDays < 5) $totalDays = 5;

                $s1End = max(2, (int) round($totalDays * 0.16));
                $s2End = max($s1End + 1, (int) round($totalDays * 0.55));
                $s3End = max($s2End + 1, (int) round($totalDays * 0.78));
                $s4End = max($s3End + 1, (int) round($totalDays * 0.90));
                $s5Start = $s4End + 1;
                if ($s5Start > $totalDays) $s5Start = $totalDays;
            @endphp
            <section class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid">
                <div class="flex items-center justify-between gap-2 mb-4 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">06</span>
                        <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Timeline & Milestone Proyek (Aligned)</h2>
                    </div>
                    <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 border border-emerald-300 dark:border-emerald-800">
                        Total Alokasi: {{ $totalDays }} Hari Kerja
                    </span>
                </div>

                <div class="space-y-2.5 font-mono text-xs">
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-emerald-500"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 1: Architecture & Database ERD Setup</span>
                        </div>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">Hari 1 - {{ $s1End }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-zinc-400"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 2: Core MVP Logic & Filament Admin CRUD</span>
                        </div>
                        <span class="text-zinc-500">Hari {{ $s1End + 1 }} - {{ $s2End }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-zinc-400"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 3: Frontend User Flow & API Integrasi</span>
                        </div>
                        <span class="text-zinc-500">Hari {{ $s2End + 1 }} - {{ $s3End }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-zinc-400"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 4: Security Audit, Stress Test & UAT</span>
                        </div>
                        <span class="text-zinc-500">Hari {{ $s3End + 1 }} - {{ $s4End }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-zinc-400"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 5: Production Deployment & Serah Terima</span>
                        </div>
                        <span class="text-zinc-500">Hari {{ $s5Start }} - {{ $totalDays }}</span>
                    </div>
                </div>
            </section>

            <!-- SECTION 7: SCOPE FREEZE, DIGITAL CONTRACT & DP MIDTRANS (CRUCIAL) -->
            <section class="bg-zinc-900 text-white border-2 border-emerald-500 p-6 sm:p-8 mb-8 rounded-none no-print">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-800 pb-6 mb-6">
                    <div>
                        <span class="px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-widest bg-emerald-500 text-black inline-block mb-2 rounded-none">
                            LEGAL & PAYMENT PROTOCOL
                        </span>
                        <h3 class="text-xl sm:text-2xl font-black uppercase tracking-tight">
                            Kunci Scope Proyek & Pembayaran DP
                        </h3>
                        <p class="text-zinc-400 text-xs mt-1 font-sans">
                            Pengerjaan proyek resmi dimulai setelah penandatanganan kontrak digital dan konfirmasi DP via Midtrans.
                        </p>
                    </div>
                    <div class="text-left sm:text-right font-mono">
                        <span class="text-zinc-400 text-xs block">TERMIN PEMBAYARAN</span>
                        <span class="text-xl font-black text-emerald-400">DP 50% &bull; Pelunasan 50%</span>
                    </div>
                </div>

                <!-- Scope Lock Policy Notice -->
                <div class="p-4 bg-zinc-950 border border-zinc-800 text-zinc-400 text-xs font-mono leading-relaxed mb-6">
                    <strong class="text-amber-400 block mb-1 uppercase font-bold">&bull; Batasan Ruang Lingkup & Ketentuan Tambah Fitur (Change Request)</strong>
                    Seluruh fitur yang tertera di atas terkunci secara hukum dalam Kontrak Induk. Apabila di kemudian hari Klien menghendaki penambahan fitur baru di luar spesifikasi ini, penambahan tersebut akan diakomodasikan melalui <strong>Change Request (CR) / Addendum</strong> terpisah dengan perhitungan biaya dan tambahan hari kerja tersendiri tanpa mengganggu jadwal kontrak utama.
                </div>

                <!-- Action Buttons: Sign Contract & Pay DP -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a 
                        href="https://wa.me/628123456789?text={{ urlencode('Halo Neriah Pro, saya menyetujui Ultimate PRD untuk proyek: ' . $blueprint->nama_bisnis . '. Mohon panduan tanda tangan kontrak digital dan pembayaran DP via Midtrans.') }}" 
                        target="_blank" 
                        class="bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase tracking-widest py-4 px-6 text-center rounded-none transition flex items-center justify-center gap-2"
                    >
                        <span>Tanda Tangani Kontrak & Kunci Scope</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </a>

                    <a 
                        href="https://wa.me/628123456789?text={{ urlencode('Halo Neriah Pro, saya ingin meminta Invoice DP Midtrans untuk proyek: ' . $blueprint->nama_bisnis) }}" 
                        target="_blank" 
                        class="bg-zinc-800 hover:bg-zinc-700 text-white font-mono font-bold text-xs uppercase tracking-widest py-4 px-6 text-center rounded-none border border-zinc-700 transition flex items-center justify-center gap-2"
                    >
                        <span>Instruksi Pembayaran DP (Midtrans)</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </a>
                </div>
            </section>

        </main>
    @endif

    <!-- Global Footer -->
    <footer class="bg-white dark:bg-zinc-900 text-zinc-500 dark:text-zinc-400 py-6 px-4 text-center text-xs border-t border-zinc-200 dark:border-zinc-800 font-mono no-print">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} NERIAH PRO HUB &bull; ULTIMATE PRD & ARCHITECTURE BLUEPRINT</p>
            <p class="text-zinc-400 dark:text-zinc-500">POSTGRESQL STRICT ULID // MODERN MONOLITH</p>
        </div>
    </footer>

</body>
</html>
