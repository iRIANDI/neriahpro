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
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 1024px) {
            .ai-metric-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
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
                    x-on:click="activeSubTab = 'guide'"
                    :class="activeSubTab === 'guide' ? 'is-active' : 'is-inactive'"
                    class="ai-subtab-btn">
                <svg width="16" height="16" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 16px; max-height: 16px; display: inline-block; flex-shrink: 0;" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Panduan &amp; Helper Fitur Klien</span>
                <span class="px-1.5 py-0.5 rounded-xs text-[10px] font-mono bg-blue-100 text-blue-950 dark:bg-blue-950 dark:text-blue-200 border border-blue-400 dark:border-blue-700 font-bold">Helper AI</span>
            </button>

            <button type="button"
                    x-on:click="activeSubTab = 'skills'"
                    :class="activeSubTab === 'skills' ? 'is-active' : 'is-inactive'"
                    class="ai-subtab-btn">
                <svg width="16" height="16" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 16px; max-height: 16px; display: inline-block; flex-shrink: 0;" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                <span>Agent Skills &amp; ECC Universal</span>
                <span class="px-1.5 py-0.5 rounded-xs text-[10px] font-mono bg-emerald-100 text-emerald-950 dark:bg-emerald-950 dark:text-emerald-200 border border-emerald-400 dark:border-emerald-700 font-bold">{{ $totalSkills ?? 50 }} Skills // {{ $eccStatus['total_subagents'] ?? 68 }} Subagents</span>
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
                    <div class="flex flex-wrap items-center gap-2 mb-2 font-mono text-xs">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-xs bg-emerald-950 border border-emerald-600 text-emerald-300 font-bold">
                            <span class="w-2 h-2 rounded-xs bg-emerald-400 animate-pulse"></span>
                            Agentic Engine Ready
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-xs bg-blue-950 border border-blue-600 text-blue-300 font-bold">
                            <span class="w-2 h-2 rounded-xs bg-blue-400"></span>
                            ECC Universal v2.2.3 (.agents OK)
                        </span>
                        <span class="text-zinc-400">Software Factory OS Lead Copilot</span>
                    </div>
                    <h2 class="text-xl md:text-2xl font-black font-mono tracking-tight text-white uppercase">
                        Autonomous AI Copilot Command Hub
                    </h2>
                    <p class="mt-2 text-xs md:text-sm text-zinc-300 max-w-2xl leading-relaxed">
                        Pusat orkestrasi kecerdasan buatan Neriah Pro dengan Tri-Hybrid AI (RAG Dense+Sparse RRF, CAG Live Telemetry O(1), KAG Deterministic Rule Graph), <strong>49 Native Tools</strong>, Vercel Skills Hub, dan <strong>ECC Universal v2.2.3</strong> Harness di folder <code class="text-blue-300 font-mono">.agents/</code>.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 shrink-0 font-mono text-xs">
                    <button type="button"
                            x-on:click="activeSubTab = 'guide'"
                            class="px-4 py-2.5 bg-blue-950 hover:bg-blue-900 text-blue-200 border border-blue-700 font-bold rounded-xs transition-all cursor-pointer flex items-center gap-1.5">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>📖 Helper Fitur Klien</span>
                    </button>
                    <button type="button"
                            wire:click="triggerSyncKnowledge"
                            class="px-3.5 py-2.5 bg-zinc-800 text-zinc-100 hover:bg-zinc-700 border border-zinc-700 font-bold rounded-xs transition-all cursor-pointer">
                        🔄 Sinkronkan RAG
                    </button>
                    <button type="button"
                            x-on:click="$dispatch('open-ai-copilot')"
                            class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-black font-black rounded-xs shadow-lg transition-all cursor-pointer flex items-center gap-1.5">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span>Buka Chat Copilot</span>
                    </button>
                </div>
            </div>

            {{-- Metric Cards Grid --}}
            <div class="ai-metric-grid">
                <div class="ai-metric-card">
                    <div class="text-[10px] font-mono font-bold uppercase text-zinc-700 dark:text-zinc-400">Total Vektor Chunk (RAG)</div>
                    <div class="text-2xl font-black font-mono tabular-nums text-zinc-950 dark:text-zinc-100 mt-1">{{ number_format($totalChunks, 0, ',', '.') }}</div>
                    <div class="text-[11px] text-zinc-700 dark:text-zinc-400 mt-1">pgvector 768-dim + RRF Hybrid</div>
                </div>

                <div class="ai-metric-card">
                    <div class="text-[10px] font-mono font-bold uppercase text-zinc-700 dark:text-zinc-400">Project OS Blueprints &amp; Tools</div>
                    <div class="text-2xl font-black font-mono tabular-nums text-zinc-950 dark:text-zinc-100 mt-1">{{ number_format($totalBlueprints, 0, ',', '.') }} <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400">// 49 Native Tools</span></div>
                    <div class="text-[11px] text-zinc-700 dark:text-zinc-400 mt-1">Strict ULID + Spatie RBAC</div>
                </div>

                <div class="ai-metric-card">
                    <div class="text-[10px] font-mono font-bold uppercase text-zinc-700 dark:text-zinc-400">ECC Universal v2.2.3 (.agents)</div>
                    <div class="text-2xl font-black font-mono tabular-nums text-zinc-950 dark:text-zinc-100 mt-1">{{ $eccStatus['total_subagents'] ?? 68 }} <span class="text-xs font-bold text-blue-700 dark:text-blue-400">// {{ $eccStatus['total_rules'] ?? 123 }} Rules</span></div>
                    <div class="text-[11px] text-zinc-700 dark:text-zinc-400 mt-1">Status: {{ $eccStatus['status_label'] ?? 'OK' }}</div>
                </div>

                <div class="ai-metric-card">
                    <div class="text-[10px] font-mono font-bold uppercase text-zinc-700 dark:text-zinc-400">Agent Skills (Vercel Hub)</div>
                    <div class="text-2xl font-black font-mono tabular-nums text-zinc-950 dark:text-zinc-100 mt-1">{{ number_format($totalSkills ?? 50, 0, ',', '.') }} <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400">// find-skills</span></div>
                    <div class="text-[11px] text-zinc-700 dark:text-zinc-400 mt-1">Open Agent Ecosystem &amp; skills.sh</div>
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

        {{-- TAB: PANDUAN & HELPER FITUR AI KLIEN --}}
        <div x-show="activeSubTab === 'guide'" class="space-y-6">
            {{-- Guide Hero Banner --}}
            <div class="ai-hero-banner">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2 font-mono text-xs">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-xs bg-blue-950 border border-blue-600 text-blue-300 font-bold">
                            <span class="w-2 h-2 rounded-xs bg-blue-400"></span>
                            Client &amp; Architect Interactive Guide
                        </span>
                        <span class="text-zinc-400">Neriah Pro Software Factory OS v12.0</span>
                    </div>
                    <h2 class="text-xl md:text-2xl font-black font-mono tracking-tight text-white uppercase">
                        Panduan Komprehensif &amp; Helper Fitur AI
                    </h2>
                    <p class="mt-2 text-xs md:text-sm text-zinc-300 max-w-3xl leading-relaxed">
                        Modul kecerdasan buatan Neriah Pro beroperasi secara mandiri (agentic) dengan prinsip <strong>Zero-Assumption</strong> dan <strong>Anti-Halusinasi</strong>. Seluruh kapabilitas terhubung ke basis data PostgreSQL Strict ULID, 49 Native Tools, Vercel Skills Hub (<code class="text-emerald-400 font-mono">find-skills</code>), dan <strong>ECC Universal v2.2.3</strong> di folder <code class="text-blue-300 font-mono">.agents/</code>.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0 font-mono text-xs">
                    <button type="button"
                            x-on:click="$dispatch('open-ai-copilot')"
                            class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-black font-mono text-xs font-black rounded-xs shadow-lg transition-all cursor-pointer flex items-center gap-2">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span>Coba di Copilot Chat</span>
                    </button>
                </div>
            </div>

            {{-- 6 Core Features Grid --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                    <div>
                        <span class="text-xs font-mono font-bold uppercase text-blue-600 dark:text-blue-400">6 Pilar Modul AI</span>
                        <h3 class="text-base font-black font-mono text-zinc-950 dark:text-white uppercase mt-0.5">
                            Katalog Fitur &amp; Kemampuan Sistem
                        </h3>
                    </div>
                    <span class="text-xs font-mono text-zinc-500">Zero Halusinasi // 100% Deterministik</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($featuresGuide['features'] ?? [] as $feat)
                        <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs hover:border-zinc-400 dark:hover:border-zinc-600 transition-all flex flex-col justify-between space-y-3">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="px-2 py-0.5 rounded-xs text-[10px] font-mono font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700">
                                        {{ $feat['tag'] }}
                                    </span>
                                    <span class="text-[10px] font-mono text-emerald-800 dark:text-emerald-400 font-bold truncate max-w-[150px]">
                                        {{ $feat['badge'] }}
                                    </span>
                                </div>
                                <h4 class="font-mono font-bold text-sm text-zinc-950 dark:text-white">
                                    {{ $feat['title'] }}
                                </h4>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                    {{ $feat['summary'] }}
                                </p>
                            </div>

                            <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800/80 space-y-1.5 font-mono text-[11px]">
                                @foreach($feat['details'] ?? [] as $label => $val)
                                    <div class="text-zinc-500">
                                        <strong class="text-zinc-900 dark:text-zinc-200 font-semibold">{{ $label }}:</strong>
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ $val }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Interactive Quick Prompt Helper Chips for Clients --}}
            <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div>
                        <div class="font-mono font-bold text-xs uppercase text-emerald-800 dark:text-emerald-400">💡 Quick Helper Chips untuk Klien</div>
                        <h3 class="font-mono font-black text-sm text-zinc-950 dark:text-white uppercase mt-0.5">
                            Contoh Pertanyaan &amp; Perintah Siap Uji
                        </h3>
                    </div>
                    <span class="text-xs font-mono text-zinc-500">Klik 'Salin' atau 'Uji Sekarang'</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($quickPrompts ?? [] as $qp)
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xs space-y-2 hover:border-zinc-400 dark:hover:border-zinc-600 transition-all">
                            <div class="flex items-center justify-between text-xs font-mono">
                                <span class="px-2 py-0.5 rounded-xs text-[10px] font-bold bg-zinc-200 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100">
                                    {{ $qp['category'] }}
                                </span>
                                <span class="font-bold text-zinc-950 dark:text-zinc-100">{{ $qp['title'] }}</span>
                            </div>
                            <p class="text-xs text-zinc-700 dark:text-zinc-300 italic font-mono bg-white dark:bg-zinc-900 p-2.5 rounded-xs border border-zinc-200 dark:border-zinc-800">
                                "{{ $qp['prompt'] }}"
                            </p>
                            <div class="flex items-center justify-end gap-2 pt-1 font-mono text-xs">
                                <button type="button"
                                        x-on:click="navigator.clipboard.writeText('{{ addslashes($qp['prompt']) }}'); $dispatch('show-toast', {type: 'info', title: 'Prompt Tersalin', message: 'Teks prompt siap di-paste ke chat.'})"
                                        class="px-2.5 py-1 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 rounded-xs font-semibold cursor-pointer">
                                    📋 Salin Prompt
                                </button>
                                <button type="button"
                                        x-on:click="$dispatch('open-ai-copilot', { message: '{{ addslashes($qp['prompt']) }}' })"
                                        class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xs font-bold cursor-pointer">
                                    💬 Uji Sekarang →
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ECC Universal Execution Terminal Recipes --}}
            <div class="p-5 bg-zinc-950 border border-zinc-800 text-zinc-100 rounded-xs space-y-4 font-mono">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-800 pb-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs px-2 py-0.5 rounded-xs bg-blue-900 text-blue-200 border border-blue-600 font-bold uppercase">ECC Universal v2.2.3</span>
                            <span class="text-xs text-emerald-400 font-bold">Target: .agents (OK)</span>
                        </div>
                        <h4 class="text-sm font-black uppercase text-white">Resep Eksekusi ECC di Terminal Proyek</h4>
                    </div>
                    <code class="px-2 py-1 bg-zinc-900 border border-zinc-700 rounded-xs text-xs text-zinc-300">
                        Status: {{ $eccStatus['status_label'] ?? 'OK' }} // {{ $eccStatus['total_subagents'] ?? 68 }} Subagents
                    </code>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                    <div class="p-3 bg-zinc-900/90 border border-zinc-800 rounded-xs space-y-2">
                        <div class="text-zinc-400 text-[11px]">1. Diagnosa &amp; Audit Integritas .agents:</div>
                        <div class="flex items-center justify-between bg-black p-2 rounded-xs border border-zinc-800">
                            <code class="text-emerald-400 text-[11px] truncate">npx ecc-universal@2.2.3 doctor --target antigravity</code>
                            <button type="button"
                                    x-on:click="navigator.clipboard.writeText('npx ecc-universal@2.2.3 doctor --target antigravity'); $dispatch('show-toast', {type: 'info', title: 'Tersalin', message: 'Perintah doctor tersalin.'})"
                                    class="text-zinc-400 hover:text-white ml-2 text-[10px] cursor-pointer">Salin</button>
                        </div>
                    </div>

                    <div class="p-3 bg-zinc-900/90 border border-zinc-800 rounded-xs space-y-2">
                        <div class="text-zinc-400 text-[11px]">2. Cek Modul Terpasang di .agents:</div>
                        <div class="flex items-center justify-between bg-black p-2 rounded-xs border border-zinc-800">
                            <code class="text-emerald-400 text-[11px] truncate">npx ecc-universal@2.2.3 list-installed</code>
                            <button type="button"
                                    x-on:click="navigator.clipboard.writeText('npx ecc-universal@2.2.3 list-installed'); $dispatch('show-toast', {type: 'info', title: 'Tersalin', message: 'Perintah list-installed tersalin.'})"
                                    class="text-zinc-400 hover:text-white ml-2 text-[10px] cursor-pointer">Salin</button>
                        </div>
                    </div>

                    <div class="p-3 bg-zinc-900/90 border border-zinc-800 rounded-xs space-y-2">
                        <div class="text-zinc-400 text-[11px]">3. Pasang Skill dari Vercel Skills Hub:</div>
                        <div class="flex items-center justify-between bg-black p-2 rounded-xs border border-zinc-800">
                            <code class="text-emerald-400 text-[11px] truncate">npx skills add https://github.com/vercel-labs/skills --skill find-skills</code>
                            <button type="button"
                                    x-on:click="navigator.clipboard.writeText('npx skills add https://github.com/vercel-labs/skills --skill find-skills'); $dispatch('show-toast', {type: 'info', title: 'Tersalin', message: 'Perintah find-skills tersalin.'})"
                                    class="text-zinc-400 hover:text-white ml-2 text-[10px] cursor-pointer">Salin</button>
                        </div>
                    </div>

                    <div class="p-3 bg-zinc-900/90 border border-zinc-800 rounded-xs space-y-2">
                        <div class="text-zinc-400 text-[11px]">4. Re-install / Perbarui Harness .agents:</div>
                        <div class="flex items-center justify-between bg-black p-2 rounded-xs border border-zinc-800">
                            <code class="text-emerald-400 text-[11px] truncate">npx ecc-universal@2.2.3 install --target antigravity php --no-hooks</code>
                            <button type="button"
                                    x-on:click="navigator.clipboard.writeText('npx ecc-universal@2.2.3 install --target antigravity php --no-hooks'); $dispatch('show-toast', {type: 'info', title: 'Tersalin', message: 'Perintah install tersalin.'})"
                                    class="text-zinc-400 hover:text-white ml-2 text-[10px] cursor-pointer">Salin</button>
                        </div>
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

        {{-- TAB 4: AGENT SKILLS ECOSYSTEM (VERCEL SKILLS HUB & FIND-SKILLS) --}}
        <div x-show="activeSubTab === 'skills'" class="space-y-6">
            {{-- Skills Hero Banner --}}
            <div class="ai-hero-banner">
                <div>
                    <div class="flex items-center gap-2 mb-2 font-mono text-xs">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-xs bg-emerald-950 border border-emerald-600 text-emerald-300 font-bold">
                            <span class="w-2 h-2 rounded-xs bg-emerald-400 animate-pulse"></span>
                            Vercel Skills Hub Synchronized
                        </span>
                        <span class="text-zinc-400">Open Agent Skills Ecosystem</span>
                    </div>
                    <h2 class="text-xl md:text-2xl font-black font-mono tracking-tight text-white uppercase">
                        Agent Skills Engine &amp; Vercel Discovery Hub
                    </h2>
                    <p class="mt-2 text-xs md:text-sm text-zinc-300 max-w-2xl leading-relaxed">
                        Modul AI Project OS kini diperkuat oleh <strong>Vercel Skills CLI</strong> (<code class="text-emerald-400 font-mono">npx skills</code>) dan modul <code class="text-emerald-400 font-mono">find-skills</code> dari Vercel Labs. Agen AI dapat mencari, menginstal, dan mengevaluasi modul keahlian (skills) tambahan dari repositori terbuka secara deterministik tanpa merusak integritas kode.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0 font-mono text-xs">
                    <a href="https://skills.sh/"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="px-4 py-2.5 bg-zinc-800 text-zinc-100 hover:bg-zinc-700 border border-zinc-700 font-bold rounded-xs transition-all flex items-center justify-center gap-2">
                        <span>Jelajahi skills.sh ↗</span>
                    </a>
                    <button type="button"
                            x-on:click="navigator.clipboard.writeText('npx skills add https://github.com/vercel-labs/skills --skill find-skills'); $dispatch('show-toast', {type: 'success', title: 'Perintah Tersalin', message: 'Perintah instalasi find-skills berhasil disalin ke clipboard.'})"
                            class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-black font-black rounded-xs shadow-lg transition-all cursor-pointer flex items-center justify-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Salin npx skills add</span>
                    </button>
                </div>
            </div>

            {{-- Dual Ecosystem Highlights: find-skills & ECC Universal v2.2.3 --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- Featured Card 1: find-skills Vercel Labs --}}
                <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="p-2 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-xs text-zinc-900 dark:text-zinc-100 font-mono font-bold text-xs">
                                ⚡ VERCEL HUB
                            </span>
                            <div>
                                <div class="font-mono font-black text-sm text-zinc-950 dark:text-white flex items-center gap-2">
                                    <span>find-skills</span>
                                    <span class="px-2 py-0.5 rounded-xs text-[10px] font-bold bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-black">
                                        ✓ AKTIF
                                    </span>
                                </div>
                                <div class="text-[11px] text-zinc-500 font-mono">Terkunci di skills-lock.json</div>
                            </div>
                        </div>
                        <code class="px-2 py-1 bg-zinc-100 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-xs text-[10px] font-mono">
                            npx skills add ... --skill find-skills
                        </code>
                    </div>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        Membantu pengembang dan agen AI menemukan keahlian baru secara otomatis dari papan peringkat <a href="https://skills.sh/" target="_blank" class="text-emerald-600 dark:text-emerald-400 underline font-semibold">skills.sh</a>, memverifikasi reputasi sumber (Vercel Labs, Anthropic, Microsoft), dan menghasilkan perintah instalasi aman.
                    </p>
                </div>

                {{-- Featured Card 2: ECC Universal v2.2.3 --}}
                <div class="p-5 bg-white dark:bg-zinc-900 border border-blue-500/40 ring-1 ring-blue-500/20 rounded-xs space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="p-2 bg-blue-100 dark:bg-blue-950 border border-blue-300 dark:border-blue-700 rounded-xs text-blue-900 dark:text-blue-100 font-mono font-bold text-xs">
                                🛡️ ECC HARNESS
                            </span>
                            <div>
                                <div class="font-mono font-black text-sm text-zinc-950 dark:text-white flex items-center gap-2">
                                    <span>ECC Universal v2.2.3</span>
                                    <span class="px-2 py-0.5 rounded-xs text-[10px] font-bold bg-blue-100 text-blue-950 border border-blue-400 dark:bg-blue-950/80 dark:text-blue-200 dark:border-blue-700 font-black">
                                        ✓ .AGENTS (OK)
                                    </span>
                                </div>
                                <div class="text-[11px] text-zinc-500 font-mono">{{ $eccStatus['total_subagents'] ?? 68 }} Subagents // {{ $eccStatus['total_rules'] ?? 123 }} Rules</div>
                            </div>
                        </div>
                        <code class="px-2 py-1 bg-zinc-100 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-blue-800 dark:text-blue-300 rounded-xs text-[10px] font-mono">
                            npx ecc-universal@2.2.3 doctor
                        </code>
                    </div>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        Harness agen cerdas terpasang pada folder <code class="text-blue-600 dark:text-blue-400 font-bold">.agents/</code> dengan status verifikasi <strong>OK</strong> (checked=1, warnings=0, errors=0). Mendukung eksekusi subagents spesifik, review kode otomatis, dan pemulihan aturan deterministik.
                    </p>
                </div>
            </div>

            {{-- Search & Filter Bar --}}
            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs flex flex-col sm:flex-row items-center justify-between gap-3 font-mono text-xs">
                <div class="relative w-full sm:w-96">
                    <input type="text"
                           wire:model.live.debounce.300ms="skillSearch"
                           placeholder="Cari skill (misal: find-skills, tdd, audit)..."
                           class="w-full px-3 py-2 pr-8 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 rounded-xs text-xs text-zinc-950 dark:text-zinc-100 focus:outline-none focus:ring-1 focus:ring-emerald-500 font-mono">
                    @if(!empty($skillSearch))
                        <button type="button"
                                wire:click="resetSkillSearch"
                                class="absolute right-2 top-2.5 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 text-xs font-mono cursor-pointer">
                            ✕
                        </button>
                    @endif
                </div>

                <div class="flex items-center gap-3 text-xs text-zinc-500">
                    <span>Menampilkan <strong>{{ count($installedSkills ?? []) }}</strong> dari <strong>{{ $totalSkills ?? 50 }}</strong> Modul Skill</span>
                </div>
            </div>

            {{-- Skills Grid / Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse(($installedSkills ?? []) as $sk)
                    <div class="p-4 bg-white dark:bg-zinc-900 border {{ $sk['slug'] === 'find-skills' ? 'border-emerald-500/80 ring-1 ring-emerald-500/40' : 'border-zinc-200 dark:border-zinc-800' }} rounded-xs hover:border-zinc-400 dark:hover:border-zinc-600 transition-all space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="space-y-0.5">
                                <div class="font-mono font-bold text-sm text-zinc-950 dark:text-white flex items-center gap-1.5">
                                    <span>{{ $sk['name'] }}</span>
                                    @if($sk['slug'] === 'find-skills')
                                        <span class="px-1.5 py-0.2 rounded-xs text-[9px] font-black bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 uppercase">
                                            Vercel Hub
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-zinc-400 font-mono">{{ $sk['file_path'] }}</div>
                            </div>
                            <span class="px-2 py-0.5 rounded-xs text-[10px] font-mono font-bold {{ $sk['is_vercel'] ? 'bg-zinc-900 text-zinc-100 dark:bg-zinc-100 dark:text-zinc-900' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200' }}">
                                {{ $sk['category'] }}
                            </span>
                        </div>

                        <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed line-clamp-3">
                            {{ $sk['description'] }}
                        </p>

                        <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-[11px] font-mono text-zinc-500">
                            <span class="truncate max-w-[200px]">Src: {{ $sk['source'] }}</span>
                            <button type="button"
                                    x-on:click="navigator.clipboard.writeText('{{ $sk['install_command'] }}'); $dispatch('show-toast', {type: 'info', title: 'Tersalin', message: 'Perintah instalasi tersalin.'})"
                                    class="text-emerald-700 dark:text-emerald-400 hover:underline font-bold cursor-pointer">
                                Salin CLI ↗
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 p-8 text-center text-zinc-500 italic bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xs font-mono text-xs">
                        Tidak ada skill yang cocok dengan pencarian "{{ $skillSearch }}".
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-filament-panels::page>

