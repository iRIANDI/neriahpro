@php
    $agentsList = \App\Services\Prd\AiAgentRulesExporterService::AGENTS;
    $activeLocale = $locale ?? app()->getLocale();
    $isEn = ($activeLocale === 'en');
@endphp

<!-- Dedicated AI Agent Rules Studio Modal (6 Popular Coding Engines) -->
<div 
    x-show="showAiAgentRulesModal" 
    x-cloak 
    @keydown.escape.window="showAiAgentRulesModal = false"
    class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-3 sm:p-5 print:hidden"
    style="display: none;"
>
    <!-- Backdrop Overlay -->
    <div class="fixed inset-0 bg-black/85 backdrop-blur-md transition-opacity" @click="showAiAgentRulesModal = false" x-transition.opacity></div>

    <!-- Modal Dialog Card -->
    <div 
        @click.stop
        class="relative z-10 bg-white dark:bg-zinc-950 border-2 border-emerald-500 w-full max-w-6xl rounded-none shadow-2xl p-4 sm:p-6 flex flex-col h-[92vh] max-h-[94vh] overflow-hidden text-zinc-900 dark:text-zinc-100"
        x-transition
    >
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-3 shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="w-3.5 h-3.5 bg-emerald-500 rounded-none inline-block animate-pulse"></span>
                <div>
                    <h3 class="text-sm sm:text-base font-mono font-black uppercase tracking-wider text-zinc-900 dark:text-white flex items-center gap-2">
                        <span x-show="locale === 'en'">AI AGENT RULES STUDIO // 6 CODING ENGINES</span>
                        <span x-show="locale !== 'en'">STUDIO ATURAN AI AGENT // 6 MESIN KODING POPULER</span>
                        <span class="text-[10px] px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950 text-emerald-950 dark:text-emerald-300 border border-emerald-500 font-bold rounded-none">
                            ZERO-ASSUMPTION CONTRACTS
                        </span>
                    </h3>
                    <p class="text-[11px] font-mono text-zinc-600 dark:text-zinc-400 mt-0.5">
                        <span x-show="locale === 'en'">Production-grade system instructions compiled natively for Google Antigravity, Claude Code, OpenAI Codex, Cursor, Windsurf, &amp; Copilot.</span>
                        <span x-show="locale !== 'en'">Kontrak instruksi sistem rekayasa perangkat lunak untuk Google Antigravity, Claude Code, OpenAI Codex, Cursor, Windsurf, dan Copilot.</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a 
                    href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'rules-bundle']) }}"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase tracking-wider transition rounded-none border border-emerald-600 shadow-sm"
                    :title="locale === 'en' ? 'Download All Rules as .ZIP Bundle' : 'Unduh Semua Berkas Rules dalam Arsip .ZIP'"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span x-show="locale === 'en'">DOWNLOAD ALL (.ZIP)</span>
                    <span x-show="locale !== 'en'">UNDUH SEMUA (.ZIP)</span>
                </a>

                <button 
                    type="button" 
                    @click="showAiAgentRulesModal = false" 
                    class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white text-2xl font-bold p-1 leading-none cursor-pointer"
                    aria-label="Close"
                >
                    &times;
                </button>
            </div>
        </div>

        <!-- Engine Selector Tabs (6 Engines) -->
        <div class="shrink-0 mb-3 overflow-x-auto no-scrollbar">
            <div class="flex items-center gap-1.5 border-b border-zinc-200 dark:border-zinc-800 pb-1.5 min-w-max">
                @foreach($agentsList as $agentKey => $agent)
                    <button 
                        type="button"
                        @click="aiAgentRulesTab = '{{ $agentKey }}'"
                        class="px-3 py-2 text-xs font-mono font-bold uppercase tracking-wider transition flex items-center gap-2 rounded-none border cursor-pointer"
                        :class="aiAgentRulesTab === '{{ $agentKey }}' 
                            ? 'bg-zinc-900 text-emerald-400 border-emerald-500 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-400 shadow-sm' 
                            : 'bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-900 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-800'"
                    >
                        @if($agentKey === 'antigravity')
                            <svg class="w-3.5 h-3.5 text-cyan-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif($agentKey === 'claude')
                            <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        @elseif($agentKey === 'codex')
                            <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        @elseif($agentKey === 'cursor')
                            <svg class="w-3.5 h-3.5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        @elseif($agentKey === 'windsurf')
                            <svg class="w-3.5 h-3.5 text-teal-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"/></svg>
                        @else
                            <svg class="w-3.5 h-3.5 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @endif
                        <span>{{ $agent['name'] }}</span>
                        <span class="text-[9px] px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-200 border border-zinc-400 dark:border-zinc-700 font-mono">
                            {{ $agent['filename'] }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Tab Body: Scrollable Content for Active Agent -->
        <div class="flex-1 overflow-y-auto pr-1 space-y-4">
            @foreach($agentsList as $agentKey => $agent)
                <div x-show="aiAgentRulesTab === '{{ $agentKey }}'" class="space-y-4">
                    <!-- Agent Info & Command Bar -->
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-mono font-black text-zinc-900 dark:text-white uppercase tracking-wider">
                                    {{ $agent['name'] }}
                                </span>
                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950 text-emerald-950 dark:text-emerald-300 border border-emerald-500">
                                    {{ $agent['badge'] }}
                                </span>
                                <span class="text-[10px] font-mono text-zinc-600 dark:text-zinc-400 px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700">
                                    <span x-show="locale === 'en'">Target Path:</span><span x-show="locale !== 'en'">Lokasi Berkas:</span> <strong class="text-emerald-600 dark:text-emerald-400">{{ $agent['workspace_path'] }}</strong>
                                </span>
                            </div>
                            <p class="text-xs font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">{{ $agent['desc_en'] }}</span>
                                <span x-show="locale !== 'en'">{{ $agent['desc_id'] }}</span>
                            </p>
                        </div>

                        <!-- Action Buttons for This Engine -->
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            <!-- Direct Download -->
                            <a 
                                href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => $agentKey]) }}"
                                class="px-3.5 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase tracking-wider transition rounded-none flex items-center gap-1.5 shadow-xs border border-emerald-600"
                                :title="locale === 'en' ? 'Download {{ $agent['filename'] }} file' : 'Unduh berkas {{ $agent['filename'] }}'"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span><span x-show="locale === 'en'">DOWNLOAD</span><span x-show="locale !== 'en'">UNDUH</span> {{ $agent['filename'] }}</span>
                            </a>

                            <!-- Copy Directives -->
                            <button 
                                type="button"
                                @click="copyAgentRulesDirectly('{{ $agentKey }}', '{{ $blueprint->slug }}')"
                                class="px-3.5 py-2 bg-white dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-600 font-mono font-bold text-xs uppercase tracking-wider transition rounded-none flex items-center gap-1.5 cursor-pointer shadow-xs"
                            >
                                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                <span x-show="locale === 'en'">COPY TEXT</span>
                                <span x-show="locale !== 'en'">SALIN TEKS</span>
                            </button>
                        </div>
                    </div>

                    <!-- Quick Terminal Command Bar -->
                    <div class="p-3 bg-zinc-900 dark:bg-black border border-zinc-800 text-xs font-mono flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-2 overflow-x-auto text-zinc-300 min-w-0">
                            <span class="text-emerald-400 select-none">$</span>
                            <span class="text-zinc-400 select-none">curl -fsSL</span>
                            <span class="text-zinc-200 truncate">"{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => $agentKey]) }}"</span>
                            <span class="text-emerald-400 select-none">-o</span>
                            <span class="text-emerald-300 font-bold select-none">{{ $agent['workspace_path'] }}</span>
                        </div>
                        <button 
                            type="button"
                            @click="copyTextToClipboard('curl -fsSL \'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => $agentKey]) }}\' -o {{ $agent['workspace_path'] }}', 'CURL COMMAND COPIED')"
                            class="px-2.5 py-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 text-[10px] font-bold uppercase tracking-wider shrink-0 transition cursor-pointer"
                        >
                            <span x-show="locale === 'en'">COPY CURL</span>
                            <span x-show="locale !== 'en'">SALIN CURL</span>
                        </button>
                    </div>

                    <!-- Setup & Configuration Guide Accordion/Box -->
                    <div class="p-3.5 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 text-xs space-y-2">
                        <div class="font-mono font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 bg-emerald-500 rounded-none inline-block"></span>
                            <span x-show="locale === 'en'">SETUP &amp; ACTIVATION GUIDE FOR {{ $agent['name'] }}</span>
                            <span x-show="locale !== 'en'">PANDUAN SETUP &amp; AKTIVASI UNTUK {{ $agent['name'] }}</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1 text-[11px] font-sans">
                            <div class="p-2.5 bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800">
                                <div class="font-mono font-bold text-emerald-600 dark:text-emerald-400 mb-1">
                                    1. <span x-show="locale === 'en'">PLACEMENT</span><span x-show="locale !== 'en'">PENEMPATAN</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400">
                                    <span x-show="locale === 'en'">Save or paste this file directly at <code class="px-1 py-0.5 bg-zinc-200 dark:bg-zinc-800 font-mono text-[10px]">{{ $agent['workspace_path'] }}</code> in your project workspace root.</span>
                                    <span x-show="locale !== 'en'">Simpan atau salin berkas ini tepat di path <code class="px-1 py-0.5 bg-zinc-200 dark:bg-zinc-800 font-mono text-[10px]">{{ $agent['workspace_path'] }}</code> pada direktori utama proyek.</span>
                                </p>
                            </div>

                            <div class="p-2.5 bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800">
                                <div class="font-mono font-bold text-emerald-600 dark:text-emerald-400 mb-1">
                                    2. <span x-show="locale === 'en'">INVARIANTS LOCKED</span><span x-show="locale !== 'en'">PAGAR PENGAMAN</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400">
                                    <span x-show="locale === 'en'">Locks Strict ULID (<code class="font-mono text-[10px]">VARCHAR(26)</code>), Keyset Cursor Pagination O(1), and zero tolerance for magic strings.</span>
                                    <span x-show="locale !== 'en'">Mengunci Strict ULID (<code class="font-mono text-[10px]">VARCHAR(26)</code>), Keyset Cursor Pagination O(1), dan larangan keras hardcode magic string.</span>
                                </p>
                            </div>

                            <div class="p-2.5 bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800">
                                <div class="font-mono font-bold text-emerald-600 dark:text-emerald-400 mb-1">
                                    3. <span x-show="locale === 'en'">AUTOMATIC READING</span><span x-show="locale !== 'en'">BACA OTOMATIS</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400">
                                    <span x-show="locale === 'en'">{{ $agent['name'] }} automatically detects and prioritizes this file whenever a prompt or refactoring task begins.</span>
                                    <span x-show="locale !== 'en'">{{ $agent['name'] }} secara otomatis membaca dan memprioritaskan berkas ini saat sesi koding atau prompt dimulai.</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Code View Box -->
                    <div class="border border-zinc-300 dark:border-zinc-800 bg-zinc-950 text-zinc-100 font-mono text-xs">
                        <div class="px-3 py-2 bg-zinc-900 border-b border-zinc-800 flex items-center justify-between text-[11px] text-zinc-400">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>{{ $agent['filename'] }} (PREVIEW // SYSTEM INSTRUCTIONS)</span>
                            </span>
                            <span class="text-[10px] text-zinc-500">UTF-8 • MARKDOWN FORMAT</span>
                        </div>
                        <div class="p-4 max-h-80 overflow-y-auto text-[11px] leading-relaxed whitespace-pre-wrap select-all font-mono text-zinc-300">
@php
    $previewRules = \App\Services\Prd\AiAgentRulesExporterService::renderAgentRules($agentKey, $blueprint, $prd ?? [], $universalSchema ?? [], $isEn);
    $previewContent = is_array($previewRules) ? ($previewRules[0] ?? '') : $previewRules;
    // Show first 4000 characters for snappy preview
    $previewSnippet = Str::limit($previewContent, 3500, "\n\n... [Click 'DOWNLOAD' or 'COPY TEXT' to get the full rules file with complete ERD and API specifications] ...");
@endphp
{{ $previewSnippet }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Modal Footer -->
        <div class="border-t border-zinc-200 dark:border-zinc-800 pt-3 mt-3 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 shrink-0">
            <div class="text-[11px] font-mono text-zinc-500 dark:text-zinc-400 flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-none inline-block"></span>
                <span x-show="locale === 'en'">All 6 rule files comply with Laravel 13, Filament v5, PostgreSQL Strict ULID, and 6-Stage ECC Harness.</span>
                <span x-show="locale !== 'en'">Seluruh 6 berkas rules comply dengan Laravel 13, Filament v5, PostgreSQL Strict ULID, dan 6-Stage ECC Harness.</span>
            </div>

            <div class="flex items-center justify-end gap-2">
                <a 
                    href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'rules-bundle']) }}"
                    class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase tracking-wider transition rounded-none border border-emerald-600 flex items-center gap-2 shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span x-show="locale === 'en'">DOWNLOAD ALL 6 RULES (.ZIP)</span>
                    <span x-show="locale !== 'en'">UNDUH 6 RULES LENGKAP (.ZIP)</span>
                </a>

                <button 
                    type="button" 
                    @click="showAiAgentRulesModal = false" 
                    class="px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-mono font-bold text-xs uppercase tracking-wider transition rounded-none cursor-pointer"
                >
                    <span x-show="locale === 'en'">CLOSE</span>
                    <span x-show="locale !== 'en'">TUTUP</span>
                </button>
            </div>
        </div>
    </div>
</div>

