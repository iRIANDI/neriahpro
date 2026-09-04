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

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .print-break-inside-avoid { break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col">

    <!-- Header Navigation Bar -->
    <header class="bg-slate-900 text-white border-b border-slate-800 py-4 px-6 sticky top-0 z-50 no-print">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="/" class="text-lg font-extrabold tracking-tight flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-xs font-black">N</span>
                <span>NERIAH<span class="text-indigo-400">PRO</span></span>
            </a>

            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg border border-slate-700 flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak / PDF</span>
                </button>
                <a href="/blueprint" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow transition">
                    + Buat Proposal Baru
                </a>
            </div>
        </div>
    </header>

    @if(!$blueprint->is_published)
        <!-- PRIVATE DRAFT SHIELD -->
        <main class="flex-1 flex items-center justify-center p-6">
            <div class="max-w-xl w-full bg-white rounded-2xl shadow-xl border border-amber-200 p-8 text-center">
                <div class="w-16 h-16 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <span class="inline-block px-3 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-full uppercase tracking-wider mb-2">
                    Dokumen Bersifat Privat
                </span>
                <h1 class="text-2xl font-bold text-slate-800 mb-3">Dokumen Masih Dalam Tahap Tinjauan</h1>
                <p class="text-slate-600 text-sm leading-relaxed mb-6">
                    Spesifikasi PRD & Blueprint untuk proyek <strong>{{ $blueprint->nama_bisnis ?: $blueprint->client_name }}</strong> saat ini masih dalam proses penyesuaian arsitektural internal oleh tim pengembang Neriah Pro.
                </p>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs text-slate-500 mb-6 text-left space-y-1 font-mono">
                    <div>Proyek: <span class="text-slate-800 font-semibold">{{ $blueprint->nama_bisnis }}</span></div>
                    <div>PIC Klien: <span class="text-slate-800 font-semibold">{{ $blueprint->client_name }}</span></div>
                    <div>Status Akses: <span class="text-rose-600 font-semibold">Private Mode (Akses Dibatasi)</span></div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="/admin/login" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-xl transition">
                        Login Administrator
                    </a>
                    <a href="/" class="px-5 py-2.5 border border-slate-300 hover:bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl transition">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </main>
    @else
        <!-- PUBLISHED FULL ULTIMATE PRD -->
        <main class="flex-1 py-10 px-4 sm:px-6 max-w-5xl mx-auto w-full">
            
            <!-- Document Hero Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-2 h-full bg-emerald-600"></div>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-6 mb-6">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                Product Requirements Document (PRD)
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-100 text-indigo-800">
                                v1.0.0 Architecture Blueprint
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">
                            {{ $blueprint->nama_bisnis ?: $blueprint->client_name }}
                        </h1>
                        <p class="text-slate-500 text-sm mt-1">
                            Disiapkan oleh <strong>Neriah Pro Tech Hub</strong> &bull; Terakhir disinkronisasi: {{ $blueprint->updated_at->format('d M Y, H:i') }} WIB
                        </p>
                    </div>
                    <div class="flex flex-col items-start md:items-end gap-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Status Proyek</span>
                        <span class="px-3 py-1 rounded-lg text-sm font-bold bg-slate-100 text-slate-800 border border-slate-200">
                            {{ $blueprint->project_status }}
                        </span>
                    </div>
                </div>

                <!-- Snapshot Meta Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="text-slate-400 block mb-0.5">Penanggung Jawab (PIC)</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $blueprint->client_name }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="text-slate-400 block mb-0.5">Email Klien</span>
                        <span class="font-bold text-slate-800 text-sm truncate block">{{ $blueprint->email }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="text-slate-400 block mb-0.5">Kesiapan Aset</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $blueprint->kesiapan_aset ?? 'Sedang Disiapkan' }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <span class="text-slate-400 block mb-0.5">Target Waktu Rilis</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $blueprint->target_waktu ?? 'Fase 1' }}</span>
                    </div>
                </div>
            </div>

            <!-- SECTION 1: EXECUTIVE TECHNICAL DISCOVERY -->
            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8 print-break-inside-avoid">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <span class="w-7 h-7 rounded-md bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">1</span>
                    <h2 class="text-xl font-bold text-slate-900">Executive Technical Discovery</h2>
                </div>

                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-rose-600 mb-2">Masalah Utama yang Diselesaikan</h3>
                        <p class="text-slate-700 text-sm leading-relaxed">
                            {{ $blueprint->masalah_utama ?? ($prd['executive_summary']['problem_statement'] ?? '-') }}
                        </p>
                    </div>
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-2">Tolak Ukur Kesuksesan (KPI)</h3>
                        <p class="text-slate-700 text-sm leading-relaxed">
                            {{ $blueprint->tujuan_utama ?? ($prd['executive_summary']['success_metrics'] ?? '-') }}
                        </p>
                    </div>
                </div>

                <div class="bg-indigo-50/60 border border-indigo-100 rounded-xl p-5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-indigo-800 mb-1">Filosofi Arsitektur & Efisiensi Biaya</h3>
                    <p class="text-slate-700 text-sm leading-relaxed">
                        {{ $prd['executive_summary']['architecture_philosophy'] ?? 'Sistem menggunakan infrastruktur Modern Monolith (Laravel 13 + Filament PHP + PostgreSQL) untuk memangkas kompleksitas multi-container dan mempercepat peluncuran fitur hingga 3x lipat.' }}
                    </p>
                </div>
            </section>

            <!-- SECTION 2: PENGGUNA & HAK AKSES (RBAC) -->
            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8 print-break-inside-avoid">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <span class="w-7 h-7 rounded-md bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">2</span>
                    <h2 class="text-xl font-bold text-slate-900">Pengguna & Hak Akses Sistem (Aktor / RBAC)</h2>
                </div>
                <div class="mb-4 text-sm text-slate-600">
                    <strong>Target Audiens / Pengunjung:</strong> {{ $blueprint->target_audiens ?? ($prd['executive_summary']['target_audience'] ?? '-') }}
                </div>

                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($prd['system_actors'] ?? [] as $actor)
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col justify-between">
                            <div>
                                <span class="inline-block px-2.5 py-0.5 rounded text-xs font-bold bg-indigo-100 text-indigo-800 mb-2">
                                    {{ $actor['name'] ?? 'Aktor Sistem' }}
                                </span>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    {{ $actor['role'] ?? '-' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- SECTION 3: FUNGSIONALITAS SISTEM (MVP vs FASE 2) -->
            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8 print-break-inside-avoid">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <span class="w-7 h-7 rounded-md bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">3</span>
                    <h2 class="text-xl font-bold text-slate-900">Spesifikasi Fitur (MVP vs Fase 2)</h2>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- MVP Phase 1 -->
                    <div class="border border-emerald-200 bg-emerald-50/20 rounded-xl p-5">
                        <div class="flex items-center gap-2 text-emerald-800 font-bold mb-4">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3>Fitur Wajib (Fase 1 - MVP Peluncuran)</h3>
                        </div>
                        <ul class="space-y-3">
                            @foreach($prd['features']['mvp_phase1'] ?? [] as $fitur)
                                <li class="text-sm bg-white p-3 rounded-lg border border-emerald-100 shadow-2xs">
                                    <div class="font-semibold text-slate-800">{{ $fitur['title'] ?? '-' }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $fitur['desc'] ?? '' }}</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Phase 2 Roadmap -->
                    <div class="border border-slate-200 bg-slate-50/50 rounded-xl p-5">
                        <div class="flex items-center gap-2 text-slate-700 font-bold mb-4">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3>Fitur Tambahan (Fase 2 - Roadmap)</h3>
                        </div>
                        @if(empty($prd['features']['phase2_roadmap']))
                            <p class="text-xs text-slate-400 italic">Belum ada fitur susulan yang didefinisikan. Seluruh fokus saat ini ditujukan ke Fase 1 MVP.</p>
                        @else
                            <ul class="space-y-3">
                                @foreach($prd['features']['phase2_roadmap'] as $fitur)
                                    <li class="text-sm bg-white p-3 rounded-lg border border-slate-200 shadow-2xs">
                                        <div class="font-semibold text-slate-800">{{ $fitur['title'] ?? '-' }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">{{ $fitur['desc'] ?? '' }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </section>

            <!-- SECTION 4: ALUR KERJA (USER FLOW) -->
            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8 print-break-inside-avoid">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <span class="w-7 h-7 rounded-md bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">4</span>
                    <h2 class="text-xl font-bold text-slate-900">Alur Kerja Utama Sistem (User Flow)</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($prd['workflow'] ?? [] as $flow)
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 relative flex flex-col justify-between">
                            <div class="absolute -top-3 left-4 w-6 h-6 bg-slate-900 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                {{ $flow['step'] ?? $loop->iteration }}
                            </div>
                            <div class="pt-2">
                                <h4 class="font-bold text-slate-800 text-sm mb-1">{{ $flow['action'] ?? '-' }}</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">{{ $flow['description'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- SECTION 5: ENTERPRISE DATABASE ERD & SCHEMA BLUEPRINT -->
            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8 print-break-inside-avoid">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-md bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">5</span>
                        <h2 class="text-xl font-bold text-slate-900">Database ERD & Schema Blueprint (PostgreSQL Strict)</h2>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 self-start sm:self-auto font-mono">
                        ULID Primary Keys &bull; Keyset O(1)
                    </span>
                </div>

                <p class="text-sm text-slate-600 mb-6">
                    {{ $prd['erd_schema']['description'] ?? 'Skema basis data dengan kompleksitas O(1) keystone pagination, UUID-agnostic ULID 26-char string primary keys, dan integritas relasi foreign key terisolasi.' }}
                </p>

                <div class="space-y-6">
                    @foreach($prd['erd_schema']['tables'] ?? [] as $table)
                        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
                            <div class="bg-slate-900 text-white px-5 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-emerald-400 font-bold text-sm">TABLE: {{ $table['name'] }}</span>
                                    <span class="text-xs text-slate-400 hidden sm:inline">&bull; {{ $table['description'] }}</span>
                                </div>
                                <span class="text-xs text-slate-300 font-mono bg-slate-800 px-2 py-0.5 rounded">
                                    PK: {{ $table['primary_key'] ?? 'id (ULID)' }}
                                </span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-slate-100 border-b border-slate-200 text-slate-600 uppercase font-semibold">
                                            <th class="py-2.5 px-4">Column Name</th>
                                            <th class="py-2.5 px-4">Data Type</th>
                                            <th class="py-2.5 px-4">Index</th>
                                            <th class="py-2.5 px-4">Nullable</th>
                                            <th class="py-2.5 px-4">Description / Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 font-mono">
                                        @foreach($table['columns'] ?? [] as $col)
                                            <tr class="hover:bg-slate-50 transition">
                                                <td class="py-2.5 px-4 font-bold text-slate-800">{{ $col['name'] }}</td>
                                                <td class="py-2.5 px-4 text-indigo-600">{{ $col['type'] }}</td>
                                                <td class="py-2.5 px-4">
                                                    @if($col['index'] === 'PRIMARY')
                                                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold text-[10px]">PRIMARY</span>
                                                    @elseif($col['index'] === 'UNIQUE')
                                                        <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-800 font-bold text-[10px]">UNIQUE</span>
                                                    @elseif($col['index'] === 'INDEX')
                                                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-bold text-[10px]">INDEX</span>
                                                    @else
                                                        <span class="text-slate-400 text-[10px]">-</span>
                                                    @endif
                                                </td>
                                                <td class="py-2.5 px-4 text-slate-600">{{ $col['nullable'] ? 'YES' : 'NO' }}</td>
                                                <td class="py-2.5 px-4 font-sans text-slate-600">{{ $col['notes'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- SECTION 6: REKOMENDASI TECH STACK JANGKA PANJANG -->
            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8 print-break-inside-avoid">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <span class="w-7 h-7 rounded-md bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">6</span>
                    <h2 class="text-xl font-bold text-slate-900">Rekomendasi Arsitektur & Platform Jangka Panjang</h2>
                </div>

                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($prd['tech_stack'] ?? [] as $key => $stack)
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block mb-1">
                                {{ strtoupper(str_replace('_', ' ', $key)) }}
                            </span>
                            <div class="font-bold text-slate-900 text-sm mb-1">{{ $stack['name'] ?? '-' }}</div>
                            <p class="text-xs text-slate-500 leading-relaxed">{{ $stack['role'] ?? '-' }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- SECTION 7: RENCANA KERJA & MILESTONE -->
            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8 print-break-inside-avoid">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <span class="w-7 h-7 rounded-md bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">7</span>
                    <h2 class="text-xl font-bold text-slate-900">Action Plan & Tahapan Pelaksanaan</h2>
                </div>

                <div class="space-y-3">
                    @foreach($prd['action_plan'] ?? [] as $plan)
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm">
                            <div class="flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full {{ $plan['status'] === 'Active' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                                <span class="font-bold text-slate-800">{{ $plan['phase'] ?? '-' }}</span>
                            </div>
                            <span class="text-slate-500 font-mono bg-white px-2.5 py-1 rounded border border-slate-200 text-xs">
                                {{ $plan['duration'] ?? '-' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- CALL TO ACTION / CONFIRMATION -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl p-8 text-center shadow-lg no-print">
                <h3 class="text-2xl font-black mb-2">Siap Merealisasikan Blueprint Proyek Ini?</h3>
                <p class="text-slate-300 text-sm max-w-xl mx-auto mb-6">
                    Dokumen ini telah terkunci dan tersinkronisasi. Tim developer kami siap memulai implementasi tahap Discovery & Database Setup.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @if(!empty($blueprint->phone))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $blueprint->phone) }}?text={{ urlencode('Halo, saya ingin mendiskusikan kelanjutan Ultimate PRD proyek: ' . $blueprint->nama_bisnis) }}" target="_blank" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-sm transition shadow-lg">
                            Hubungi via WhatsApp
                        </a>
                    @endif
                    <a href="mailto:{{ $blueprint->email }}?subject={{ urlencode('Konfirmasi Proyek: ' . $blueprint->nama_bisnis) }}" class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl text-sm border border-white/20 transition">
                        Kirim Email Konfirmasi
                    </a>
                </div>
            </div>

        </main>
    @endif

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-6 px-4 text-center text-xs border-t border-slate-800 no-print">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} Neriah Pro HUB &bull; Vision Blueprint & Ultimate PRD Engine</p>
            <p class="text-slate-500">Enterprise PostgreSQL &bull; Monolith Rapid Architecture</p>
        </div>
    </footer>

</body>
</html>
