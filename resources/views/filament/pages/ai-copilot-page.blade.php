<x-filament-panels::page>
    @vite(['resources/css/app.css'])

    <style>
        .ai-copilot-wrapper {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .ai-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        /* Sub-tab Navigation Bar */
        .ai-subtab-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            padding: 6px;
            background: #f4f4f5;
            border: 1px solid #e4e4e7;
            border-radius: 4px;
        }
        .dark .ai-subtab-bar {
            background: #18181b;
            border-color: #27272a;
        }

        .ai-subtab-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 2px;
            font-size: 12px;
            font-family: ui-monospace, monospace;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
        }
        .ai-subtab-btn.is-active {
            background: #09090b !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-color: #27272a !important;
        }
        .dark .ai-subtab-btn.is-active {
            background: #f4f4f5 !important;
            color: #09090b !important;
            font-weight: 700 !important;
            border-color: #e4e4e7 !important;
        }
        .ai-subtab-btn.is-inactive {
            color: #71717a;
            background: transparent;
        }
        .ai-subtab-btn.is-inactive:hover {
            color: #09090b;
            background: #e4e4e7;
        }
        .dark .ai-subtab-btn.is-inactive:hover {
            color: #ffffff;
            background: #27272a;
        }

        /* Hero Banner */
        .ai-hero-banner {
            padding: 24px;
            background: #09090b;
            border: 1px solid #27272a;
            color: #ffffff;
            border-radius: 2px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4);
        }
        @media (min-width: 768px) {
            .ai-hero-banner {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        /* Metric Cards Grid */
        .ai-metric-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 16px;
        }
        @media (min-width: 640px) {
            .ai-metric-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        .ai-metric-card {
            padding: 16px;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 2px;
        }
        .dark .ai-metric-card {
            background: #18181b;
            border-color: #27272a;
        }

        /* Tri-Hybrid Architecture Container */
        .ai-tri-container {
            padding: 24px;
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 2px;
        }
        .dark .ai-tri-container {
            background: #18181b;
            border-color: #27272a;
        }

        .ai-tri-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 16px;
            margin-top: 16px;
        }
        @media (min-width: 768px) {
            .ai-tri-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        .ai-tri-card {
            padding: 16px;
            background: #fafafa;
            border: 1px solid #e4e4e7;
            border-radius: 2px;
        }
        .dark .ai-tri-card {
            background: #09090b;
            border-color: #27272a;
        }

        /* Vector Table Container */
        .ai-table-box {
            border: 1px solid #e4e4e7;
            border-radius: 2px;
            overflow: hidden;
            background: #ffffff;
        }
        .dark .ai-table-box {
            border-color: #27272a;
            background: #09090b;
        }
    </style>

    <div x-data="{ activeSubTab: @entangle('activeSubTab') }" class="ai-copilot-wrapper space-y-6">

        {{-- SUB-TAB NAVIGATION BAR --}}
        <div class="ai-subtab-bar">
            <button type="button"
                    x-on:click="activeSubTab = 'command'"
                    :class="activeSubTab === 'command' ? 'is-active' : 'is-inactive'"
                    class="ai-subtab-btn">
                <svg width="16" height="16" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 16px; max-height: 16px; display: inline-block; flex-shrink: 0;" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Pusat Komando AI &amp; Tri-Hybrid</span>
            </button>

            <button type="button"
                    x-on:click="activeSubTab = 'knowledge'"
                    :class="activeSubTab === 'knowledge' ? 'is-active' : 'is-inactive'"
                    class="ai-subtab-btn">
                <svg width="16" height="16" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 16px; max-height: 16px; display: inline-block; flex-shrink: 0;" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Repositori Vektor (RAG pgvector)</span>
                <span class="px-1.5 py-0.5 rounded-xs text-[10px] font-mono bg-zinc-200 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700">768 Dimensi</span>
            </button>

            <button type="button"
                    x-on:click="activeSubTab = 'cctv'"
                    :class="activeSubTab === 'cctv' ? 'is-active' : 'is-inactive'"
                    class="ai-subtab-btn">
                <svg width="16" height="16" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 16px; max-height: 16px; display: inline-block; flex-shrink: 0;" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>Jejak CCTV &amp; Audit Trail</span>
            </button>
        </div>

        {{-- TAB 1: PUSAT KOMANDO --}}
        <div x-show="activeSubTab === 'command'" class="space-y-6">
            {{-- Hero Banner --}}
            <div class="ai-hero-banner">
                <div>
                    <div class="flex items-center gap-2 mb-2 font-mono text-xs">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-xs bg-emerald-950 border border-emerald-600 text-emerald-300 font-bold">
                            <span class="w-2 h-2 rounded-xs bg-emerald-400 animate-pulse"></span>
                            Agentic Engine Ready
                        </span>
                        <span class="text-zinc-400">Software Factory OS Lead Copilot</span>
                    </div>
                    <h2 class="text-xl md:text-2xl font-black font-mono tracking-tight text-white uppercase">
                        Autonomous AI Copilot Command Hub
                    </h2>
                    <p class="mt-2 text-xs md:text-sm text-zinc-300 max-w-2xl leading-relaxed">
                        Pusat orkestrasi kecerdasan buatan Neriah Pro dengan Tri-Hybrid AI (RAG Dense+Sparse RRF, CAG Live Telemetry O(1), KAG Deterministic Rule Graph) serta <strong>24 Native Tools</strong> tervalidasi RBAC &amp; SHA-256 Audit Log untuk 12 Sectoral UI/UX Archetypes, 8 Layer Day-2 Production, 5 Day-30+ Lifecycle Engines, serta 5 Mitra Commercial-to-Code Bridges Satu Pintu (RAB Algoritmik, SPK SHA-256, Paid Addendum, 7-Milestone Gantt &amp; Digital BAST).
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <button type="button"
                            wire:click="triggerSyncKnowledge"
                            class="px-4 py-2.5 bg-zinc-800 text-zinc-100 hover:bg-zinc-700 border border-zinc-700 font-mono text-xs font-bold rounded-xs transition-all cursor-pointer">
                        🔄 Sinkronkan Full RAG v5.0
                    </button>
                    <button type="button"
                            x-on:click="$dispatch('open-ai-copilot')"
                            class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-black font-mono text-xs font-black rounded-xs shadow-lg transition-all cursor-pointer flex items-center gap-2">
                        <svg width="16" height="16" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 16px; max-height: 16px; display: inline-block; flex-shrink: 0;" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span>Buka Chat Copilot</span>
                    </button>
                </div>
            </div>

            {{-- Metric Cards Grid --}}
            <div class="ai-metric-grid">
                <div class="ai-metric-card">
                    <div class="text-[10px] font-mono font-bold uppercase text-zinc-700 dark:text-zinc-400">Total Vektor Chunk (RAG)</div>
                    <div class="text-2xl font-black font-mono tabular-nums text-zinc-950 dark:text-zinc-100 mt-1">{{ number_format($totalChunks, 0, ',', '.') }}</div>
                    <div class="text-[11px] text-zinc-700 dark:text-zinc-400 mt-1">10 Pilar + 12 Sektor UI + 9 Stack + 6 Killer Moat</div>
                </div>

                <div class="ai-metric-card">
                    <div class="text-[10px] font-mono font-bold uppercase text-zinc-700 dark:text-zinc-400">Project OS Blueprints &amp; Tools</div>
                    <div class="text-2xl font-black font-mono tabular-nums text-zinc-950 dark:text-zinc-100 mt-1">{{ number_format($totalBlueprints, 0, ',', '.') }} <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400">// 24 Native Tools</span></div>
                    <div class="text-[11px] text-zinc-700 dark:text-zinc-400 mt-1">Strict ULID + Mitra RAB/SPK/BAST + Live MCP + SHA-256 Audit</div>
                </div>

                <div class="ai-metric-card">
                    <div class="text-[10px] font-mono font-bold uppercase text-zinc-700 dark:text-zinc-400">Active Sprint Parallel</div>
                    <div class="text-2xl font-black font-mono tabular-nums text-emerald-800 dark:text-emerald-400 mt-1">{{ $activeSprints }} / 3 Slot</div>
                    <div class="text-[11px] text-zinc-700 dark:text-zinc-400 mt-1">Managed Capacity Anti-Collision</div>
                </div>
            </div>

            {{-- Arsitektur Tri-Hybrid Intelligence --}}
            <div class="ai-tri-container space-y-4">
                <div class="border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <span class="text-xs font-mono font-bold uppercase text-emerald-600 dark:text-emerald-400">Tri-Hybrid AI Architecture</span>
                    <h3 class="text-base font-black font-mono text-zinc-950 dark:text-white uppercase mt-0.5">
                        Pilar Memori AI: RAG • CAG • KAG
                    </h3>
                </div>

                <div class="ai-tri-grid text-xs">
                    <div class="ai-tri-card space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-bold text-zinc-950 dark:text-white">1. RAG (Retrieval)</span>
                            <span class="px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-[10px] font-mono rounded-xs">pgvector</span>
                        </div>
                        <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Mengombinasikan <strong>Dense Vector 768-dim</strong> (Google Gemini / MRL) dan <strong>Sparse Lexical</strong> via Reciprocal Rank Fusion (RRF) untuk memastikan kutipan PRD dan pasal kepatuhan hukum 100% akurat.
                        </p>
                    </div>

                    <div class="ai-tri-card space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-bold text-zinc-950 dark:text-white">2. CAG (Context Augmented)</span>
                            <span class="px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-[10px] font-mono rounded-xs">Cache O(1)</span>
                        </div>
                        <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Menyuntikkan status proyek aktif, kapasitas sprint terisi, riwayat webhook Midtrans, dan unit economics secara live ke dalam konteks prompt tanpa latensi query berulang.
                        </p>
                    </div>

                    <div class="ai-tri-card space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-bold text-zinc-950 dark:text-white">3. KAG (Knowledge Augmented)</span>
                            <span class="px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-[10px] font-mono rounded-xs">Graph Rules</span>
                        </div>
                        <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Penegakan aturan rekayasa deterministik (Strict ULID, O(1) Keyset Cursor, Larangan Window.alert, dan Zero Tolerance Dosa Hardcode) yang secara mekanis memfilter respon AI dari halusinasi.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: REPOSITORI VEKTOR (RAG) DENGAN CURSOR PAGINATION O(1) --}}
        <div x-show="activeSubTab === 'knowledge'" class="space-y-4">
            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="relative w-full sm:w-80">
                    <input type="text"
                           wire:model.live.debounce.300ms="docSearch"
                           placeholder="Cari dokumen vektor..."
                           class="w-full px-3 py-2 pr-8 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 rounded-xs text-xs text-zinc-950 dark:text-zinc-100 focus:outline-none focus:ring-1 focus:ring-emerald-500 font-mono">
                    @if(!empty($docSearch))
                        <button type="button"
                                wire:click="resetDocSearch"
                                class="absolute right-2 top-2.5 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 text-xs font-mono cursor-pointer">
                            ✕
                        </button>
                    @endif
                </div>

                <div class="flex items-center gap-2 text-xs font-mono text-zinc-500">
                    <span>Keyset Cursor Pagination O(1)</span>
                </div>
            </div>

            <div class="ai-table-box">
                <table class="w-full text-left text-xs font-mono">
                    <thead class="bg-zinc-100 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 sticky top-0">
                        <tr>
                            <th class="py-2.5 px-4 font-bold text-zinc-700 dark:text-zinc-300">Dokumen Sumber</th>
                            <th class="py-2.5 px-4 font-bold text-zinc-700 dark:text-zinc-300">Source Type</th>
                            <th class="py-2.5 px-4 font-bold text-zinc-700 dark:text-zinc-300 text-center">Jumlah Chunks</th>
                            <th class="py-2.5 px-4 font-bold text-zinc-700 dark:text-zinc-300 text-right">Terakhir Diperbarui</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @if($indexedDocs && $indexedDocs->count() > 0)
                            @foreach($indexedDocs as $doc)
                                <tr class="odd:bg-white dark:odd:bg-zinc-950 even:bg-zinc-50/50 dark:even:bg-zinc-900/30 hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80 transition-colors">
                                    <td class="py-3 px-4 font-bold text-zinc-950 dark:text-zinc-100">
                                        {{ $doc->display_title ?: $doc->source_id }}
                                        <div class="text-[10px] text-zinc-400 font-normal">{{ $doc->source_id }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-zinc-600 dark:text-zinc-400">
                                        <span class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 rounded-xs text-[10px] font-bold">
                                            {{ $doc->source_type }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $doc->chunk_count }} Chunks
                                    </td>
                                    <td class="py-3 px-4 text-right text-zinc-500 text-[11px]">
                                        {{ \Carbon\Carbon::parse($doc->updated_at)->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="py-8 text-center text-zinc-500 italic">
                                    Tidak ada dokumen vektor yang ditemukan. Klik 'Sinkronkan 10 Pilar RAG' untuk mengindeks dokumen.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Cursor Pagination Nav --}}
            @if($indexedDocs && ($indexedDocs->hasMorePages() || $indexedDocs->previousCursor()))
                <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs text-xs font-mono">
                    <div>
                        @if($indexedDocs->previousCursor())
                            <button type="button"
                                    wire:click="prevDocPage('{{ $indexedDocs->previousCursor()->encode() }}')"
                                    class="px-3 py-1.5 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 font-bold cursor-pointer">
                                ← Halaman Sebelumnya
                            </button>
                        @endif
                    </div>
                    <div>
                        @if($indexedDocs->hasMorePages())
                            <button type="button"
                                    wire:click="nextDocPage('{{ $indexedDocs->nextCursor()->encode() }}')"
                                    class="px-3 py-1.5 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xs hover:bg-zinc-100 dark:hover:bg-zinc-800 font-bold cursor-pointer">
                                Halaman Berikutnya →
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- TAB 3: CCTV & AUDIT WORKFLOW TRACES --}}
        <div x-show="activeSubTab === 'cctv'" class="space-y-4">
            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs flex items-center justify-between">
                <div>
                    <h3 class="font-mono text-xs font-bold uppercase text-zinc-950 dark:text-white">Jejak CCTV Audit Workflow (AiWorkflowTrace)</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Merekam setiap pemanggilan LLM, fungsi tool calling, dan durasi eksekusi secara real-time.</p>
                </div>
            </div>

            <div class="space-y-2 font-mono text-xs">
                @forelse($recentTraces as $trace)
                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-zinc-950 dark:text-zinc-100">{{ $trace->title }}</span>
                                <span class="text-[10px] px-1.5 py-0.2 {{ $trace->status === 'success' ? 'bg-emerald-100 text-emerald-950 dark:bg-emerald-950 dark:text-emerald-200 border border-emerald-400 dark:border-emerald-700' : 'bg-rose-100 text-rose-950 dark:bg-rose-950 dark:text-rose-200 border border-rose-400 dark:border-rose-700' }} rounded-xs font-bold">
                                    {{ strtoupper($trace->status) }}
                                </span>
                            </div>
                            <div class="text-[11px] text-zinc-500">{{ $trace->summary ?? 'Eksekusi selesai tanpa pesan tambahan.' }}</div>
                        </div>

                        <div class="flex items-center gap-4 text-[10px] text-zinc-400 shrink-0">
                            <span>Stage: <strong>{{ $trace->stage }}</strong></span>
                            <span>{{ $trace->execution_time_ms ? $trace->execution_time_ms . 'ms' : '-' }}</span>
                            <span>{{ $trace->created_at?->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-zinc-500 italic bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xs">
                        Belum ada riwayat jejak CCTV terekam.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-filament-panels::page>

