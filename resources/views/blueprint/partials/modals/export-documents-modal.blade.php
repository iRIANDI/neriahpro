@php
    $activeLocale = $locale ?? app()->getLocale();
    $isEn = ($activeLocale === 'en');
@endphp

<!-- UNIFIED EXPORT & DOCUMENTS HUB MODAL -->
<div 
    x-show="showExportHubModal" 
    x-cloak 
    @keydown.escape.window="showExportHubModal = false"
    class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-3 sm:p-5 print:hidden"
    style="display: none;"
>
    <!-- Backdrop Overlay -->
    <div 
        class="fixed inset-0 bg-black/85 backdrop-blur-md transition-opacity" 
        @click="showExportHubModal = false" 
        x-transition.opacity
    ></div>

    <!-- Modal Dialog Window -->
    <div 
        @click.stop
        class="relative z-10 bg-white dark:bg-zinc-950 border-2 border-emerald-500 w-full max-w-4xl rounded-none shadow-2xl flex flex-col max-h-[90vh] overflow-hidden text-zinc-900 dark:text-zinc-100"
        x-transition
    >
        <!-- Modal Header -->
        <div class="px-4 sm:px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-zinc-50 dark:bg-zinc-900/60">
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 bg-emerald-500 rounded-none inline-block animate-pulse"></span>
                <div>
                    <h3 class="text-sm sm:text-base font-mono font-black uppercase tracking-wider text-zinc-900 dark:text-white flex items-center gap-2 flex-wrap">
                        <span x-show="locale === 'en'">EXPORT &amp; DOCUMENTS ARTIFACT HUB</span>
                        <span x-show="locale !== 'en'">HUB DOKUMEN &amp; EKSPOR ARSITEKTUR</span>
                        <span class="text-[10px] px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950 text-emerald-950 dark:text-emerald-300 border border-emerald-400 font-bold rounded-none">
                            16 ARTIFACTS
                        </span>
                    </h3>
                    <p class="text-[11px] font-mono text-zinc-600 dark:text-zinc-400 mt-0.5">
                        <span x-show="locale === 'en'">Complete suite of printable documentation, AI IDE rules, DDL schemas, and commercial legal contracts.</span>
                        <span x-show="locale !== 'en'">Koleksi lengkap dokumen siap cetak, aturan AI agent IDE, skema database, dan berkas kontrak komersial.</span>
                    </p>
                </div>
            </div>

            <button 
                type="button" 
                @click="showExportHubModal = false" 
                class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white text-2xl font-bold p-1 leading-none cursor-pointer transition"
                aria-label="Tutup Modal"
            >
                &times;
            </button>
        </div>

        <!-- Search / Filter Input Bar (with Instant Clear 'X' Button) -->
        <div class="px-4 sm:px-6 py-2.5 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-100/70 dark:bg-zinc-900/40 shrink-0">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input 
                    type="text" 
                    x-model="exportHubFilter" 
                    :placeholder="locale === 'en' ? 'e.g. search PDF, Markdown, .cursorrules, Schema, RAB, SPK...' : 'misal: cari PDF, Markdown, .cursorrules, Schema, RAB, SPK...'"
                    class="w-full pl-9 pr-8 py-1.5 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 font-mono text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-emerald-500 rounded-none"
                />
                <!-- Instant Clear 'X' Button -->
                <button 
                    type="button" 
                    x-show="exportHubFilter.length > 0" 
                    @click="exportHubFilter = ''" 
                    class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 cursor-pointer font-bold text-sm"
                    title="Hapus pencarian"
                >
                    &times;
                </button>
            </div>
        </div>

        <!-- Scrollable Document List Body (Portrait & Desktop Optimized) -->
        <div class="p-4 sm:p-6 overflow-y-auto custom-prd-scrollbar flex-1 space-y-6">

            <!-- ========================================================= -->
            <!-- GROUP 1: OFFICIAL DOCUMENTS & PRINT                       -->
            <!-- ========================================================= -->
            <div>
                <div class="px-2 py-1 bg-zinc-100 dark:bg-zinc-900 border-l-4 border-emerald-500 text-xs font-mono font-black uppercase text-zinc-900 dark:text-zinc-100 flex items-center justify-between mb-2.5">
                    <span>
                        <span x-show="locale === 'en'">📄 OFFICIAL DOCUMENTS &amp; PRINT</span>
                        <span x-show="locale !== 'en'">📄 DOKUMEN RESMI &amp; CETAK</span>
                    </span>
                    <span class="text-[10px] text-zinc-500 font-normal">3 FORMATS</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5">
                    <!-- 1. Print Formal PRD -->
                    <button 
                        type="button" 
                        @click="showExportHubModal = false; window.printPrdDocument()"
                        class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 text-left transition flex flex-col justify-between group cursor-pointer"
                        x-show="!exportHubFilter || 'print cetak prd document resmi'.includes(exportHubFilter.toLowerCase())"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1 font-mono">
                                <span class="text-xs font-black text-zinc-900 dark:text-zinc-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">
                                    <span x-show="locale === 'en'">Print Formal PRD</span>
                                    <span x-show="locale !== 'en'">Cetak Dokumen PRD</span>
                                </span>
                                <span class="px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-[10px] font-bold text-zinc-600 dark:text-zinc-300">Ctrl+P</span>
                            </div>
                            <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">Print-ready high-contrast formal view for physical review or save as browser PDF.</span>
                                <span x-show="locale !== 'en'">Format standar siap cetak atau simpan sebagai PDF print browser tanpa distorsi warna.</span>
                            </p>
                        </div>
                        <div class="mt-3 text-[10px] font-mono font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                            <span>CETAK SEKARANG &rarr;</span>
                        </div>
                    </button>

                    <!-- 2. Official PDF Document -->
                    <button 
                        type="button" 
                        @click="showExportHubModal = false; triggerPdfDownload()"
                        class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 text-left transition flex flex-col justify-between group cursor-pointer"
                        x-show="!exportHubFilter || 'pdf document resmi antrean job queue'.includes(exportHubFilter.toLowerCase())"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1 font-mono">
                                <span class="text-xs font-black text-zinc-900 dark:text-zinc-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">
                                    <span x-show="locale === 'en'">Official PDF Document</span>
                                    <span x-show="locale !== 'en'">Dokumen PDF Resmi</span>
                                </span>
                                <span class="px-1.5 py-0.2 bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-300 font-bold text-[10px]">PDF</span>
                            </div>
                            <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">Asynchronous background rendering engine with SHA-256 seal &amp; 24h retention.</span>
                                <span x-show="locale !== 'en'">Mesin render latar belakang dengan segel integritas hash SHA-256 dan unduh aman.</span>
                            </p>
                        </div>
                        <div class="mt-3 text-[10px] font-mono font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                            <span>UNDUH PDF &rarr;</span>
                        </div>
                    </button>

                    <!-- 3. Raw Markdown Corpus (.MD) -->
                    <button 
                        type="button" 
                        @click="showExportHubModal = false; triggerMdDownload()"
                        class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 text-left transition flex flex-col justify-between group cursor-pointer"
                        x-show="!exportHubFilter || 'markdown md corpus raw text'.includes(exportHubFilter.toLowerCase())"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1 font-mono">
                                <span class="text-xs font-black text-zinc-900 dark:text-zinc-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">
                                    <span x-show="locale === 'en'">Raw Markdown Corpus</span>
                                    <span x-show="locale !== 'en'">Dokumen Markdown (.MD)</span>
                                </span>
                                <span class="px-1.5 py-0.2 bg-sky-100 dark:bg-sky-950 text-sky-800 dark:text-sky-300 font-bold text-[10px]">.MD</span>
                            </div>
                            <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">Pure GFM markdown corpus suitable for documentation repositories and LLM ingestion.</span>
                                <span x-show="locale !== 'en'">Teks murni Markdown GitHub untuk dokumentasi repository git dan LLM context.</span>
                            </p>
                        </div>
                        <div class="mt-3 text-[10px] font-mono font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                            <span>UNDUH MARKDOWN &rarr;</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- GROUP 2: AI IDE & AGENT DIRECTIVES                        -->
            <!-- ========================================================= -->
            <div>
                <div class="px-2 py-1 bg-zinc-100 dark:bg-zinc-900 border-l-4 border-emerald-500 text-xs font-mono font-black uppercase text-zinc-900 dark:text-zinc-100 flex items-center justify-between mb-2.5">
                    <span>
                        <span x-show="locale === 'en'">🤖 AI IDE &amp; AGENT DIRECTIVES</span>
                        <span x-show="locale !== 'en'">🤖 ATURAN AI IDE &amp; AGENT</span>
                    </span>
                    <span class="text-[10px] text-zinc-500 font-normal">5 FILES</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                    <!-- 1. AI Rules Hub (6 Popular Engines) -->
                    <div 
                        @click="showExportHubModal = false; openAiAgentRulesModal('antigravity')" 
                        class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 transition flex flex-col justify-between group cursor-pointer"
                        x-show="!exportHubFilter || 'cursorrules claude agent rules ide prompt antigravity gemini codex windsurf copilot'.includes(exportHubFilter.toLowerCase())"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1 font-mono">
                                <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 group-hover:underline">AI RULES (6 ENGINES)</span>
                                <span class="px-1.5 py-0.2 bg-emerald-100 dark:bg-emerald-950 text-emerald-950 dark:text-emerald-300 font-bold text-[9px]">STUDIO</span>
                            </div>
                            <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">Antigravity (AGENTS.md), Claude Code (CLAUDE.md), OpenAI (CODEX.md), Cursor, Windsurf, &amp; Copilot.</span>
                                <span x-show="locale !== 'en'">Aturan sistem untuk Antigravity (AGENTS.md), Claude (CLAUDE.md), OpenAI (CODEX.md), Cursor, Windsurf, &amp; Copilot.</span>
                            </p>
                        </div>
                        <div class="mt-2.5 text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold flex items-center justify-between">
                            <span x-show="locale === 'en'">OPEN RULES STUDIO &rarr;</span>
                            <span x-show="locale !== 'en'">BUKA RULES STUDIO &rarr;</span>
                            <span class="text-zinc-500 text-[9px]">ZIP / DIRECT</span>
                        </div>
                    </div>

                    <!-- 2. EXECUTION.MD -->
                    <a 
                        href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'execution-blueprint', 'locale' => $isEn ? 'en' : 'id']) }}" 
                        :href="'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'execution-blueprint']) }}&locale=' + (locale || 'id')" 
                        @click="showExportHubModal = false" 
                        class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 transition flex flex-col justify-between group"
                        x-show="!exportHubFilter || 'execution blueprint zero-assumption kontrak eksekusi'.includes(exportHubFilter.toLowerCase())"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1 font-mono">
                                <span class="text-xs font-black text-zinc-900 dark:text-zinc-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">EXECUTION.MD</span>
                                <span class="px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-[9px] font-bold text-zinc-600 dark:text-zinc-300">CONTRACT</span>
                            </div>
                            <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">Zero-Assumption Contract specifying execution order and strict boundaries.</span>
                                <span x-show="locale !== 'en'">Kontrak eksekusi rekayasa Zero-Assumption tanpa celah tebak-tebakan.</span>
                            </p>
                        </div>
                        <div class="mt-2.5 text-[10px] font-mono text-zinc-500 font-bold">UNDUH FILE &rarr;</div>
                    </a>

                    <!-- 3. DECISION.MD -->
                    <a 
                        href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'decision-tables', 'locale' => $isEn ? 'en' : 'id']) }}" 
                        :href="'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'decision-tables']) }}&locale=' + (locale || 'id')" 
                        @click="showExportHubModal = false" 
                        class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 transition flex flex-col justify-between group"
                        x-show="!exportHubFilter || 'decision tables matrix php match aturan keputusan'.includes(exportHubFilter.toLowerCase())"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1 font-mono">
                                <span class="text-xs font-black text-zinc-900 dark:text-zinc-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">DECISION.MD</span>
                                <span class="px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-[9px] font-bold text-zinc-600 dark:text-zinc-300">MATRIX</span>
                            </div>
                            <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">Deterministic decision tables compiled from business logic matrix.</span>
                                <span x-show="locale !== 'en'">Tabel aturan keputusan deterministik berbasis PHP match() &amp; state invariants.</span>
                            </p>
                        </div>
                        <div class="mt-2.5 text-[10px] font-mono text-zinc-500 font-bold">UNDUH FILE &rarr;</div>
                    </a>

                    <!-- 4. SYNTAX26.MD -->
                    <a 
                        href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'syntax-lock', 'locale' => $isEn ? 'en' : 'id']) }}" 
                        :href="'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'syntax-lock']) }}&locale=' + (locale || 'id')" 
                        @click="showExportHubModal = false" 
                        class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 transition flex flex-col justify-between group"
                        x-show="!exportHubFilter || 'syntax syntax26 kamus anti-halusinasi laravel filament'.includes(exportHubFilter.toLowerCase())"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1 font-mono">
                                <span class="text-xs font-black text-zinc-900 dark:text-zinc-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">SYNTAX26.MD</span>
                                <span class="px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-[9px] font-bold text-zinc-600 dark:text-zinc-300">SYNTAX</span>
                            </div>
                            <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">Syntax Anti-Hallucination dictionary for Laravel 13, Filament v5, &amp; Livewire 4.</span>
                                <span x-show="locale !== 'en'">Kamus anti-halusinasi fungsi framework Laravel 13, Filament v5, dan Livewire 4.</span>
                            </p>
                        </div>
                        <div class="mt-2.5 text-[10px] font-mono text-zinc-500 font-bold">UNDUH FILE &rarr;</div>
                    </a>

                    <!-- 5. SYMBOL.JSON -->
                    <a 
                        href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'symbol-manifest', 'locale' => $isEn ? 'en' : 'id']) }}" 
                        :href="'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'symbol-manifest']) }}&locale=' + (locale || 'id')" 
                        @click="showExportHubModal = false" 
                        class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 transition flex flex-col justify-between group"
                        x-show="!exportHubFilter || 'symbol manifest json presisi model route'.includes(exportHubFilter.toLowerCase())"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1 font-mono">
                                <span class="text-xs font-black text-zinc-900 dark:text-zinc-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">SYMBOL.JSON</span>
                                <span class="px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-[9px] font-bold text-zinc-600 dark:text-zinc-300">MANIFEST</span>
                            </div>
                            <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">Exact symbol manifest locking class names, method signatures, &amp; route keys.</span>
                                <span x-show="locale !== 'en'">Manifest simbol presisi nama class, model, method, dan route anti-duplikasi.</span>
                            </p>
                        </div>
                        <div class="mt-2.5 text-[10px] font-mono text-zinc-500 font-bold">UNDUH FILE &rarr;</div>
                    </a>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- GROUP 3: ARCHITECTURE & DEVOPS CONFIGS                    -->
            <!-- ========================================================= -->
            <div>
                <div class="px-2 py-1 bg-zinc-100 dark:bg-zinc-900 border-l-4 border-emerald-500 text-xs font-mono font-black uppercase text-zinc-900 dark:text-zinc-100 flex items-center justify-between mb-2.5">
                    <span>
                        <span x-show="locale === 'en'">⚙️ ARCHITECTURE &amp; DEVOPS</span>
                        <span x-show="locale !== 'en'">⚙️ ARSITEKTUR &amp; DEVOPS</span>
                    </span>
                    <span class="text-[10px] text-zinc-500 font-normal">4 CONFIGS</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2.5">
                    <!-- 1. BLUEPRINT_SCHEMA.json -->
                    <a 
                        href="{{ route('blueprint.download-schema-json', ['slug' => $blueprint->slug, 'locale' => $isEn ? 'en' : 'id']) }}" 
                        :href="'{{ route('blueprint.download-schema-json', $blueprint->slug) }}?locale=' + (locale || 'id')" 
                        @click="showExportHubModal = false" 
                        class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 transition flex flex-col justify-between group"
                        x-show="!exportHubFilter || 'schema json universal blueprint api'.includes(exportHubFilter.toLowerCase())"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1 font-mono">
                                <span class="text-xs font-black text-zinc-900 dark:text-zinc-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">SCHEMA.json</span>
                                <span class="px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-[9px] font-bold text-zinc-600 dark:text-zinc-300">v2.0</span>
                            </div>
                            <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">Universal Blueprint Schema JSON for external integrations.</span>
                                <span x-show="locale !== 'en'">Universal Blueprint Schema JSON untuk integrasi sistem eksternal.</span>
                            </p>
                        </div>
                        <div class="mt-2.5 text-[10px] font-mono text-zinc-500 font-bold">UNDUH JSON &rarr;</div>
                    </a>

                    <!-- 2. .env.blueprint -->
                    <a 
                        href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'env-blueprint', 'locale' => $isEn ? 'en' : 'id']) }}" 
                        :href="'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'env-blueprint']) }}&locale=' + (locale || 'id')" 
                        @click="showExportHubModal = false" 
                        class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 transition flex flex-col justify-between group"
                        x-show="!exportHubFilter || 'env environment secret vault blueprint devops'.includes(exportHubFilter.toLowerCase())"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1 font-mono">
                                <span class="text-xs font-black text-zinc-900 dark:text-zinc-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">.env.blueprint</span>
                                <span class="px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-[9px] font-bold text-zinc-600 dark:text-zinc-300">VAULT</span>
                            </div>
                            <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">Secret vault blueprint for PostgreSQL, Redis, &amp; security keys.</span>
                                <span x-show="locale !== 'en'">Cetak biru secret vault environment PostgreSQL, Redis, dan keys.</span>
                            </p>
                        </div>
                        <div class="mt-2.5 text-[10px] font-mono text-zinc-500 font-bold">UNDUH ENV &rarr;</div>
                    </a>

                    <!-- 3. verify-blueprint.sh -->
                    <a 
                        href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'verify-blueprint', 'locale' => $isEn ? 'en' : 'id']) }}" 
                        :href="'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'verify-blueprint']) }}&locale=' + (locale || 'id')" 
                        @click="showExportHubModal = false" 
                        class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 transition flex flex-col justify-between group"
                        x-show="!exportHubFilter || 'verify shell bash script ci cd scanner gatekeeper'.includes(exportHubFilter.toLowerCase())"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1 font-mono">
                                <span class="text-xs font-black text-zinc-900 dark:text-zinc-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">verify.sh</span>
                                <span class="px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-[9px] font-bold text-zinc-600 dark:text-zinc-300">CI/CD</span>
                            </div>
                            <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">Automated CI/CD anti-slop verification shell scanner script.</span>
                                <span x-show="locale !== 'en'">Skrip shell scanner anti-slop CI/CD untuk gerbang verifikasi.</span>
                            </p>
                        </div>
                        <div class="mt-2.5 text-[10px] font-mono text-zinc-500 font-bold">UNDUH SH &rarr;</div>
                    </a>

                    <!-- 4. MENU25.MD -->
                    <a 
                        href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'enterprise-menu', 'locale' => $isEn ? 'en' : 'id']) }}" 
                        :href="'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'enterprise-menu']) }}&locale=' + (locale || 'id')" 
                        @click="showExportHubModal = false" 
                        class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-emerald-500 transition flex flex-col justify-between group"
                        x-show="!exportHubFilter || 'menu menu25 matrix navigasi enterprise matriks'.includes(exportHubFilter.toLowerCase())"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1 font-mono">
                                <span class="text-xs font-black text-zinc-900 dark:text-zinc-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">MENU25.MD</span>
                                <span class="px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-[9px] font-bold text-zinc-600 dark:text-zinc-300">25 MENUS</span>
                            </div>
                            <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">Enterprise 25-Menu navigation matrix (L1–L4 structure).</span>
                                <span x-show="locale !== 'en'">Matriks navigasi 25 menu enterprise berjenjang L1 hingga L4.</span>
                            </p>
                        </div>
                        <div class="mt-2.5 text-[10px] font-mono text-zinc-500 font-bold">UNDUH MENU &rarr;</div>
                    </a>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- GROUP 4: MITRA COMMERCIAL PACK (A4)                       -->
            <!-- ========================================================= -->
            <div>
                <div class="px-2 py-1 bg-amber-50 dark:bg-amber-950/60 border-l-4 border-amber-500 text-xs font-mono font-black uppercase text-amber-950 dark:text-amber-200 flex items-center justify-between mb-2.5">
                    <span>
                        <span x-show="locale === 'en'">📋 MITRA COMMERCIAL PACK (A4)</span>
                        <span x-show="locale !== 'en'">📋 PAKET KOMERSIAL MITRA (A4)</span>
                    </span>
                    <span class="text-[10px] text-amber-700 dark:text-amber-400 font-normal">4 LEGAL CONTRACTS</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2.5">
                    <!-- 1. RAB A4 -->
                    <a 
                        href="{{ route('blueprint.mitra-document', ['slug' => $blueprint->slug, 'docType' => 'rab']) }}" 
                        :href="'{{ route('blueprint.mitra-document', ['slug' => $blueprint->slug, 'docType' => 'rab']) }}&locale=' + (locale || 'id')" 
                        target="_blank" 
                        @click="showExportHubModal = false" 
                        class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-amber-500 transition flex flex-col justify-between group"
                        x-show="!exportHubFilter || 'rab a4 boq wbs anggaran biaya rincian'.includes(exportHubFilter.toLowerCase())"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1 font-mono">
                                <span class="text-xs font-black text-amber-600 dark:text-amber-400 group-hover:underline">RAB A4</span>
                                <span class="px-1.5 py-0.2 bg-amber-100 dark:bg-amber-950 text-amber-950 dark:text-amber-300 font-bold text-[9px]">BoQ</span>
                            </div>
                            <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">Algorithmic BoQ &amp; 7-Phase WBS line-item investment sheet.</span>
                                <span x-show="locale !== 'en'">Rincian Anggaran Biaya algoritmik 7 fase &amp; lembar WBS.</span>
                            </p>
                        </div>
                        <div class="mt-2.5 text-[10px] font-mono text-amber-600 dark:text-amber-400 font-bold">BUKA DOKUMEN &rarr;</div>
                    </a>

                    <!-- 2. SPK SHA-256 -->
                    <a 
                        href="{{ route('blueprint.mitra-document', ['slug' => $blueprint->slug, 'docType' => 'spk-appendix']) }}" 
                        :href="'{{ route('blueprint.mitra-document', ['slug' => $blueprint->slug, 'docType' => 'spk-appendix']) }}&locale=' + (locale || 'id')" 
                        target="_blank" 
                        @click="showExportHubModal = false" 
                        class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-amber-500 transition flex flex-col justify-between group"
                        x-show="!exportHubFilter || 'spk sha-256 kontrak perjanjian kerja lampiran'.includes(exportHubFilter.toLowerCase())"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1 font-mono">
                                <span class="text-xs font-black text-amber-600 dark:text-amber-400 group-hover:underline">SPK SHA-256</span>
                                <span class="px-1.5 py-0.2 bg-amber-100 dark:bg-amber-950 text-amber-950 dark:text-amber-300 font-bold text-[9px]">CONTRACT</span>
                            </div>
                            <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">Contract Appendix with Cryptographic Hash Seal for legal sign-off.</span>
                                <span x-show="locale !== 'en'">Lampiran Surat Perjanjian Kerja dengan segel hash SHA-256.</span>
                            </p>
                        </div>
                        <div class="mt-2.5 text-[10px] font-mono text-amber-600 dark:text-amber-400 font-bold">BUKA DOKUMEN &rarr;</div>
                    </a>

                    <!-- 3. BAST A4 -->
                    <a 
                        href="{{ route('blueprint.mitra-document', ['slug' => $blueprint->slug, 'docType' => 'bast']) }}" 
                        :href="'{{ route('blueprint.mitra-document', ['slug' => $blueprint->slug, 'docType' => 'bast']) }}&locale=' + (locale || 'id')" 
                        target="_blank" 
                        @click="showExportHubModal = false" 
                        class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-amber-500 transition flex flex-col justify-between group"
                        x-show="!exportHubFilter || 'bast a4 berita acara serah terima uat digital sign'.includes(exportHubFilter.toLowerCase())"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1 font-mono">
                                <span class="text-xs font-black text-amber-600 dark:text-amber-400 group-hover:underline">BAST A4</span>
                                <span class="px-1.5 py-0.2 bg-amber-100 dark:bg-amber-950 text-amber-950 dark:text-amber-300 font-bold text-[9px]">UAT</span>
                            </div>
                            <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">Digital Handover &amp; User Acceptance Testing (UAT) sign-off document.</span>
                                <span x-show="locale !== 'en'">Berita Acara Serah Terima pekerjaan dan verifikasi UAT digital.</span>
                            </p>
                        </div>
                        <div class="mt-2.5 text-[10px] font-mono text-amber-600 dark:text-amber-400 font-bold">BUKA DOKUMEN &rarr;</div>
                    </a>

                    <!-- 4. SOP.MD / SOP-A4 -->
                    <a 
                        href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'client-sop', 'locale' => $isEn ? 'en' : 'id']) }}" 
                        :href="'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'client-sop']) }}&locale=' + (locale || 'id')" 
                        @click="showExportHubModal = false" 
                        class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:border-amber-500 transition flex flex-col justify-between group"
                        x-show="!exportHubFilter || 'sop sop.md manual operasional training staf panduan'.includes(exportHubFilter.toLowerCase())"
                    >
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-1 font-mono">
                                <span class="text-xs font-black text-amber-600 dark:text-amber-400 group-hover:underline">SOP.MD / A4</span>
                                <span class="px-1.5 py-0.2 bg-amber-100 dark:bg-amber-950 text-amber-950 dark:text-amber-300 font-bold text-[9px]">TRAINING</span>
                            </div>
                            <p class="text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                <span x-show="locale === 'en'">Client Staff Training Manual &amp; Standard Operating Procedures.</span>
                                <span x-show="locale !== 'en'">Manual Operasional dan panduan pelatihan staf pengguna sistem.</span>
                            </p>
                        </div>
                        <div class="mt-2.5 text-[10px] font-mono text-amber-600 dark:text-amber-400 font-bold">UNDUH SOP &rarr;</div>
                    </a>
                </div>
            </div>

        </div>

        <!-- Modal Footer Actions (Quick Links) -->
        <div class="px-4 sm:px-6 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/60 flex flex-col sm:flex-row items-center justify-between gap-2.5 shrink-0 font-mono text-xs">
            <button 
                type="button" 
                @click="showExportHubModal = false; openScaffoldModal()"
                class="w-full sm:w-auto px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-black uppercase flex items-center justify-center gap-2 cursor-pointer transition rounded-none shadow-xs"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span x-show="locale === 'en'">EXPORT FULL SCAFFOLD (DOCKER + DDL)</span>
                <span x-show="locale !== 'en'">EKSPOR LENGKAP SCAFFOLD BOILERPLATE</span>
            </button>

            <a 
                href="{{ url('/blueprint?tier=retail_spark&new=1') }}" 
                class="w-full sm:w-auto px-3.5 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 font-bold flex items-center justify-center gap-1.5 transition rounded-none text-center"
            >
                <span>+</span>
                <span x-show="locale === 'en'">START NEW PROJECT</span>
                <span x-show="locale !== 'en'">MULAI PROYEK BARU</span>
            </a>
        </div>
    </div>
</div>

