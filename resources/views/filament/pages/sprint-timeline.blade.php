<x-filament-panels::page>
    <div x-data="sprintTimelineDashboard()" x-init="initDashboard()" class="space-y-6">

        {{-- TOP STATS: EXECUTIVE SPRINT VELOCITY & ANTI-COLLISION SHIELD --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Stat 1: Total Sprints Aktif --}}
            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs">
                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400 text-xs font-mono mb-1">
                    <span>SPRINT BERJALAN</span>
                    <span class="w-2 h-2 bg-emerald-500 rounded-none animate-pulse"></span>
                </div>
                <div class="text-2xl font-black text-zinc-900 dark:text-zinc-100 font-mono">
                    {{ $activeSprintCount }} <span class="text-xs font-normal text-zinc-500">Proyek Aktif</span>
                </div>
                <div class="text-[11px] font-mono text-emerald-600 dark:text-emerald-400 mt-1">
                    &check; On-Track Sprint Velocity
                </div>
            </div>

            {{-- Stat 2: Kapasitas Slot Terisi --}}
            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs">
                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400 text-xs font-mono mb-1">
                    <span>KAPASITAS TERKELOLA</span>
                    <span class="text-[10px] font-mono px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">MANAGED</span>
                </div>
                <div class="text-2xl font-black text-zinc-900 dark:text-zinc-100 font-mono">
                    {{ $totalAssignedSlots }} / {{ $totalMaxSlots }} <span class="text-xs font-normal text-zinc-500">Slot Terisi</span>
                </div>
                <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-1.5 mt-2 rounded-none overflow-hidden">
                    <div class="bg-emerald-500 h-full" style="width: {{ $totalMaxSlots > 0 ? round(($totalAssignedSlots / $totalMaxSlots) * 100) : 0 }}%"></div>
                </div>
            </div>

            {{-- Stat 3: Sisa Slot Siap Booking --}}
            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none shadow-xs">
                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400 text-xs font-mono mb-1">
                    <span>SISA SLOT TERSEDIA</span>
                    <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400">READY</span>
                </div>
                <div class="text-2xl font-black text-zinc-900 dark:text-zinc-100 font-mono">
                    {{ $remainingOverallSlots }} <span class="text-xs font-normal text-zinc-500">Slot Terbuka</span>
                </div>
                <div class="text-[11px] font-mono text-zinc-500 mt-1">
                    Lintas 3 Periode Batch
                </div>
            </div>

            {{-- Stat 4: Anti-Collision Shield --}}
            <div class="p-4 bg-zinc-900 text-white dark:bg-zinc-950 border border-zinc-800 rounded-none shadow-xs">
                <div class="flex items-center justify-between text-zinc-400 text-xs font-mono mb-1">
                    <span>ANTI-COLLISION ENGINE</span>
                    <span class="text-[10px] font-bold text-emerald-400">AKTIF</span>
                </div>
                <div class="text-base font-bold tracking-tight text-white font-mono flex items-center gap-1.5 mt-0.5">
                    <svg width="16" height="16" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 16px; max-height: 16px; display: inline-block; flex-shrink: 0;" class="text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>0 Tabrakan Jadwal</span>
                </div>
                <div class="text-[11px] font-mono text-zinc-400 mt-1">
                    ULID Idempotency & Keyset Lock
                </div>
            </div>
        </div>

        {{-- DASHBOARD TABS NAVIGATION --}}
        <div class="border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-2 overflow-x-auto">
            <div class="flex items-center gap-2">
                <button 
                    type="button" 
                    @click="activeTab = 'matrix'"
                    :class="activeTab === 'matrix' ? 'border-b-2 border-emerald-500 text-zinc-900 dark:text-zinc-100 font-black' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 font-medium'"
                    class="px-4 py-3 text-xs font-mono uppercase tracking-wider flex items-center gap-2 transition cursor-pointer"
                >
                    <svg width="16" height="16" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 16px; max-height: 16px; display: inline-block; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>1. Matriks Kapasitas Slot</span>
                </button>

                <button 
                    type="button" 
                    @click="activeTab = 'gantt'; $nextTick(() => renderGantt())"
                    :class="activeTab === 'gantt' ? 'border-b-2 border-emerald-500 text-zinc-900 dark:text-zinc-100 font-black' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 font-medium'"
                    class="px-4 py-3 text-xs font-mono uppercase tracking-wider flex items-center gap-2 transition cursor-pointer"
                >
                    <svg width="16" height="16" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 16px; max-height: 16px; display: inline-block; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>2. Master Gantt Multi-Proyek (Mermaid)</span>
                </button>

                <button 
                    type="button" 
                    @click="activeTab = 'calendar'"
                    :class="activeTab === 'calendar' ? 'border-b-2 border-emerald-500 text-zinc-900 dark:text-zinc-100 font-black' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 font-medium'"
                    class="px-4 py-3 text-xs font-mono uppercase tracking-wider flex items-center gap-2 transition cursor-pointer"
                >
                    <svg width="16" height="16" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 16px; max-height: 16px; display: inline-block; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>3. Timeline Kalender Bulanan</span>
                </button>
            </div>

            <div class="flex items-center gap-2 py-2">
                <button
                    type="button"
                    @click="openAssignModal = true"
                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-black font-mono text-xs font-bold rounded-none flex items-center gap-1.5 transition cursor-pointer"
                >
                    <svg width="14" height="14" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 14px; max-height: 14px; display: inline-block; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Kunci Slot Proyek</span>
                </button>
            </div>
        </div>

        {{-- TAB 1: SPRINT CAPACITY & ANTI-COLLISION MATRIX --}}
        <div x-show="activeTab === 'matrix'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @foreach($batches as $bKey => $batch)
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none p-5 flex flex-col justify-between shadow-xs">
                        {{-- Batch Header --}}
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                                <div>
                                    <span class="text-[10px] font-mono px-2 py-0.5 font-bold uppercase {{ $batch['is_full'] ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/30' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' }}">
                                        {{ $batch['urgency_badge'] }}
                                    </span>
                                    <h3 class="text-base font-black text-zinc-900 dark:text-zinc-100 font-mono mt-1.5">
                                        {{ $batch['label'] }}
                                    </h3>
                                    <p class="text-xs font-mono text-zinc-500 dark:text-zinc-400">
                                        {{ $batch['dates'] }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xl font-mono font-black text-zinc-900 dark:text-zinc-100">
                                        {{ $batch['used_slots'] }}<span class="text-zinc-400 text-sm">/{{ $batch['total_slots'] }}</span>
                                    </span>
                                    <span class="block text-[10px] font-mono text-zinc-500">SLOT TERISI</span>
                                </div>
                            </div>

                            <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-2.5 mb-4 line-clamp-2">
                                {{ $batch['description'] ?? 'Alokasi sprint reguler terencana.' }}
                            </p>

                            {{-- Projects in this batch --}}
                            <div class="space-y-3">
                                <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400 block">DAFTAR SLOT TERKUNCI:</span>

                                @forelse($batch['projects'] as $p)
                                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none relative group">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0">
                                                <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate font-mono">
                                                    {{ $p['name'] }}
                                                </h4>
                                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">
                                                    Klien: {{ $p['client'] ?: '-' }} ({{ $p['email'] ?: '-' }})
                                                </p>
                                                <div class="flex items-center gap-2 mt-2">
                                                    <span class="text-[10px] font-mono px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">
                                                        {{ $p['status'] }}
                                                    </span>
                                                    <span class="text-[10px] font-mono text-zinc-500">
                                                        Target: {{ $p['target_waktu'] }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="flex flex-col gap-1 items-end shrink-0">
                                                @if($p['public_url'])
                                                    <a href="{{ $p['public_url'] }}" target="_blank" class="text-[10px] font-mono text-emerald-600 hover:underline">
                                                        Buka PRD &nearr;
                                                    </a>
                                                @endif
                                                @if($p['edit_url'])
                                                    <a href="{{ $p['edit_url'] }}" class="text-[10px] font-mono text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200">
                                                        Edit &rarr;
                                                    </a>
                                                @endif
                                                @if($p['type'] === 'blueprint')
                                                    <button 
                                                        type="button" 
                                                        wire:click="unassignProject('{{ $p['id'] }}')" 
                                                        class="text-[9px] font-mono text-rose-500 hover:underline mt-1"
                                                    >
                                                        Lepas Slot
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-3 text-center border border-dashed border-zinc-200 dark:border-zinc-800 text-zinc-400 text-xs font-mono">
                                        Belum ada proyek yang dikunci pada batch ini.
                                    </div>
                                @endforelse

                                {{-- Remaining Empty Slots --}}
                                @for($i = 0; $i < $batch['remaining_slots']; $i++)
                                    <div class="p-3 border border-dashed border-emerald-500/40 bg-emerald-500/5 text-center flex items-center justify-between rounded-none">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 bg-emerald-500"></span>
                                            <span class="text-xs font-mono text-emerald-700 dark:text-emerald-400 font-bold">
                                                Slot {{ $batch['used_slots'] + $i + 1 }} Tersedia
                                            </span>
                                        </div>
                                        <button 
                                            type="button"
                                            @click="selectBatchForAssignment('{{ $bKey }}')"
                                            class="text-[10px] font-mono px-2 py-1 bg-emerald-500 text-black font-bold uppercase hover:bg-emerald-400 transition cursor-pointer"
                                        >
                                            + Isi Slot
                                        </button>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        {{-- Batch Footer Progress --}}
                        <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 mt-6">
                            <div class="flex items-center justify-between text-xs font-mono text-zinc-500 mb-1.5">
                                <span>Utilisasi Kapasitas</span>
                                <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ $batch['utilization_percent'] }}%</span>
                            </div>
                            <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-1.5 rounded-none overflow-hidden">
                                <div class="bg-emerald-500 h-full" style="width: {{ $batch['utilization_percent'] }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- UNASSIGNED BLUEPRINTS PIPELINE (WAITING ROOM) --}}
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100 font-mono">
                            Antrean Blueprint Tanpa Slot (Waiting Pipeline)
                        </h3>
                        <p class="text-xs text-zinc-500 font-mono mt-0.5">
                            Daftar proyek PRD yang belum dialokasikan ke salah satu batch waktu pengerjaan.
                        </p>
                    </div>
                    <span class="text-xs font-mono px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                        {{ count($unassignedBlueprints) }} Blueprint
                    </span>
                </div>

                @if($unassignedBlueprints->isEmpty())
                    <p class="text-xs text-zinc-500 font-mono text-center py-4">
                        Seluruh proyek blueprint telah dialokasikan ke slot batch dengan rapi!
                    </p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($unassignedBlueprints->take(9) as $ub)
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate font-mono">
                                        {{ $ub->nama_bisnis ?: ($ub->client_name . ' Project') }}
                                    </div>
                                    <div class="text-[10px] text-zinc-500 font-mono">
                                        Status: {{ $ub->project_status }}
                                    </div>
                                </div>
                                <div class="shrink-0 flex items-center gap-1.5">
                                    <button 
                                        type="button" 
                                        wire:click="assignProject('{{ $ub->id }}', 'batch_1')"
                                        title="Alokasikan ke Batch 1"
                                        class="px-2 py-1 bg-zinc-200 dark:bg-zinc-800 hover:bg-emerald-500 hover:text-black font-mono text-[10px] font-bold transition cursor-pointer"
                                    >
                                        B1
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:click="assignProject('{{ $ub->id }}', 'batch_2')"
                                        title="Alokasikan ke Batch 2"
                                        class="px-2 py-1 bg-zinc-200 dark:bg-zinc-800 hover:bg-emerald-500 hover:text-black font-mono text-[10px] font-bold transition cursor-pointer"
                                    >
                                        B2
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:click="assignProject('{{ $ub->id }}', 'batch_q1_2027')"
                                        title="Alokasikan ke Batch Q1"
                                        class="px-2 py-1 bg-zinc-200 dark:bg-zinc-800 hover:bg-emerald-500 hover:text-black font-mono text-[10px] font-bold transition cursor-pointer"
                                    >
                                        Q1
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- TAB 2: UNIFIED MERMAID GANTT MULTI-PROJECT ROADMAP --}}
        <div x-show="activeTab === 'gantt'" class="space-y-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none shadow-xs">
                <div class="flex items-center justify-between pb-4 border-b border-zinc-200 dark:border-zinc-800 mb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-emerald-500"></span>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100 font-mono">
                                Master Delivery Roadmap: Multi-Project Gantt Execution
                            </h3>
                        </div>
                        <p class="text-xs text-zinc-500 font-mono mt-1">
                            Visualisasi diagram Gantt interaktif 60fps yang memetakan seluruh proyek di setiap batch secara deterministik.
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="copyGanttCode()"
                            class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-mono text-xs flex items-center gap-1.5 transition cursor-pointer rounded-none border border-zinc-300 dark:border-zinc-700"
                        >
                            <svg width="14" height="14" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 14px; max-height: 14px; display: inline-block; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span x-text="copied ? 'Tersalin!' : 'Salin Kode Mermaid'">Salin Kode Mermaid</span>
                        </button>

                        <button
                            type="button"
                            @click="renderGantt(true)"
                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-black font-mono text-xs font-bold transition cursor-pointer rounded-none"
                        >
                            &circlearrowright; Refresh Render
                        </button>
                    </div>
                </div>

                {{-- Source Code Text Holder (Hidden) --}}
                <script type="text/plain" id="unified-gantt-mermaid-source">{!! $unifiedGanttMermaid !!}</script>

                {{-- Mermaid Target Render Container --}}
                <div 
                    id="unified-gantt-mermaid-target" 
                    class="overflow-x-auto min-h-[350px] p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-center flex items-center justify-center text-zinc-700 dark:text-zinc-300 font-mono text-xs"
                >
                    <span>Memuat visualisasi Master Gantt Chart via Mermaid.js lokal...</span>
                </div>

                <div class="mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between text-[11px] font-mono text-zinc-500">
                    <span>Library: Mermaid.js v11.4.1 (100% Bundled Lokal // Zero CDN Latency)</span>
                    <span class="text-emerald-600 dark:text-emerald-400">&check; Compliant dengan Standar Arsitektur Neriah Pro</span>
                </div>
            </div>
        </div>

        {{-- TAB 3: MONTHLY TIMELINE CALENDAR SCHEDULE --}}
        <div x-show="activeTab === 'calendar'" class="space-y-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-none shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800 mb-4">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100 font-mono">
                            Timeline Kalender Bulanan (Q4 2026 – Q1 2027)
                        </h3>
                        <p class="text-xs text-zinc-500 font-mono mt-0.5">
                            Distribusi alokasi sprint per bulan untuk mencegah penumpukan sumber daya rekayasa.
                        </p>
                    </div>
                </div>

                {{-- Month Matrix --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Okt - Nov 2026 --}}
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center justify-between pb-2 border-b border-zinc-200 dark:border-zinc-800">
                            <span class="text-xs font-mono font-bold text-zinc-900 dark:text-zinc-100">OKT – NOV 2026</span>
                            <span class="text-[10px] font-mono px-1.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold">AKTIF</span>
                        </div>
                        <div class="mt-3 space-y-2">
                            <div class="text-[11px] font-mono text-zinc-600 dark:text-zinc-400">
                                <strong>Fokus:</strong> Discovery PRD, Skema PostgreSQL ULID, Scaffold Generator, dan Peluncuran MVP Batch 1.
                            </div>
                            <div class="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">Batch 1 Target:</span> 25 Nov 2026 Handover
                            </div>
                        </div>
                    </div>

                    {{-- Des 2026 - Jan 2027 --}}
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center justify-between pb-2 border-b border-zinc-200 dark:border-zinc-800">
                            <span class="text-xs font-mono font-bold text-zinc-900 dark:text-zinc-100">DES 2026 – JAN 2027</span>
                            <span class="text-[10px] font-mono px-1.5 py-0.5 bg-sky-500/10 text-sky-600 dark:text-sky-400 font-bold">UPCOMING</span>
                        </div>
                        <div class="mt-3 space-y-2">
                            <div class="text-[11px] font-mono text-zinc-600 dark:text-zinc-400">
                                <strong>Fokus:</strong> Kickoff Batch 2, integrasi Payment Midtrans Snap & Filament v5 Enterprise.
                            </div>
                            <div class="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                                <span class="font-bold text-sky-600 dark:text-sky-400">Batch 2 Target:</span> 15 Jan 2027 Handover
                            </div>
                        </div>
                    </div>

                    {{-- Feb - Mar 2027 --}}
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center justify-between pb-2 border-b border-zinc-200 dark:border-zinc-800">
                            <span class="text-xs font-mono font-bold text-zinc-900 dark:text-zinc-100">FEB – MAR 2027</span>
                            <span class="text-[10px] font-mono px-1.5 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 font-bold">RESERVASI</span>
                        </div>
                        <div class="mt-3 space-y-2">
                            <div class="text-[11px] font-mono text-zinc-600 dark:text-zinc-400">
                                <strong>Fokus:</strong> Batch Q1 2027, Prospek Skala Enterprise & Mobile Cross-Platform Flutter.
                            </div>
                            <div class="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                                <span class="font-bold text-amber-600 dark:text-amber-400">Batch Q1 Target:</span> Early Bird Priority
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL ASSIGN PROYEK KE SLOT BATCH (ALPINE.JS BACKDROP) --}}
        <div 
            x-show="openAssignModal" 
            x-cloak 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
            @keydown.escape.window="openAssignModal = false"
        >
            <div 
                @click.away="openAssignModal = false"
                class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-lg p-6 rounded-none shadow-xl"
            >
                <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800 mb-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100 font-mono">
                        Kunci Slot Proyek ke Batch
                    </h3>
                    <button type="button" @click="openAssignModal = false" class="text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 text-lg font-bold">
                        &times;
                    </button>
                </div>

                <div class="space-y-4 font-mono text-xs">
                    <div>
                        <label class="block text-zinc-700 dark:text-zinc-300 font-bold mb-1.5 uppercase">
                            1. Pilih Proyek Blueprint *
                        </label>
                        <select 
                            x-model="selectedBlueprintId"
                            class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-2.5 rounded-none text-zinc-900 dark:text-zinc-100"
                        >
                            <option value="">-- Pilih Proyek Blueprint --</option>
                            @foreach($unassignedBlueprints as $ub)
                                <option value="{{ $ub->id }}">{{ $ub->nama_bisnis ?: $ub->client_name }} ({{ $ub->project_status }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-zinc-700 dark:text-zinc-300 font-bold mb-1.5 uppercase">
                            2. Pilih Periode Batch Waktu *
                        </label>
                        <select 
                            x-model="selectedBatchKey"
                            class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-2.5 rounded-none text-zinc-900 dark:text-zinc-100"
                        >
                            @foreach($batches as $bKey => $b)
                                <option value="{{ $bKey }}">{{ $b['label'] }} ({{ $b['dates'] }}) — Sisa {{ $b['remaining_slots'] }} Slot</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800 mt-6">
                    <button 
                        type="button" 
                        @click="openAssignModal = false"
                        class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-xs rounded-none"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        @click="confirmAssignment()"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-black font-mono text-xs font-bold rounded-none"
                    >
                        Kunci Alokasi Slot
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- LOCAL MERMAID JAVASCRIPT BUNDLE (ZERO CDN LATENCY) --}}
    <script src="{{ asset('js/vendor/mermaid.min.js') }}"></script>

    {{-- DASHBOARD ALPINE COMPONENT SCRIPT --}}
    <script>
        function sprintTimelineDashboard() {
            return {
                activeTab: 'matrix',
                openAssignModal: false,
                selectedBlueprintId: '',
                selectedBatchKey: 'batch_1',
                copied: false,

                initDashboard() {
                    this.$nextTick(() => {
                        this.initMermaid();
                    });
                },

                selectBatchForAssignment(batchKey) {
                    this.selectedBatchKey = batchKey;
                    this.openAssignModal = true;
                },

                confirmAssignment() {
                    if (!this.selectedBlueprintId) {
                        if (typeof window.showToast === 'function') {
                            window.showToast({ type: 'warning', title: 'Pilih Proyek', message: 'Silakan pilih proyek blueprint terlebih dahulu!' });
                        } else {
                            console.warn('Silakan pilih proyek blueprint terlebih dahulu!');
                        }
                        return;
                    }
                    @this.call('assignProject', this.selectedBlueprintId, this.selectedBatchKey);
                    this.openAssignModal = false;
                    this.selectedBlueprintId = '';
                },

                initMermaid() {
                    if (!window.mermaid) return;
                    const isDark = document.documentElement.classList.contains('dark');
                    try {
                        window.mermaid.initialize({
                            startOnLoad: false,
                            theme: isDark ? 'dark' : 'neutral',
                            securityLevel: 'loose',
                            gantt: {
                                titleTopMargin: 25,
                                barHeight: 20,
                                barGap: 6,
                                topPadding: 50,
                                sidePadding: 75,
                                fontSize: 11,
                                numberSectionStyles: 4,
                                axisFormat: '%d %b',
                            }
                        });
                    } catch (e) {
                        console.warn('Mermaid init notice:', e);
                    }
                },

                async renderGantt(force = false) {
                    if (!window.mermaid) return;
                    const sourceEl = document.getElementById('unified-gantt-mermaid-source');
                    const targetEl = document.getElementById('unified-gantt-mermaid-target');
                    if (!sourceEl || !targetEl) return;

                    const code = sourceEl.textContent.trim();
                    if (!code) return;

                    try {
                        this.initMermaid();
                        const renderId = 'mermaid-gantt-' + Date.now();
                        const { svg } = await window.mermaid.render(renderId, code);
                        targetEl.innerHTML = svg;
                    } catch (err) {
                        console.error('Gantt render error:', err);
                        targetEl.innerHTML = `
                            <div class="p-4 text-left font-mono text-xs text-rose-500 bg-rose-500/10 border border-rose-500/30">
                                <strong>Gagal merender diagram Gantt:</strong> ${err.message || 'Syntax error'}
                                <pre class="mt-2 text-[10px] text-zinc-600 dark:text-zinc-400 overflow-x-auto">${code}</pre>
                            </div>
                        `;
                    }
                },

                copyGanttCode() {
                    const sourceEl = document.getElementById('unified-gantt-mermaid-source');
                    if (!sourceEl) return;
                    navigator.clipboard.writeText(sourceEl.textContent.trim()).then(() => {
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2500);
                    });
                }
            };
        }
    </script>
</x-filament-panels::page>
