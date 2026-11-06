    <!-- Scaffold & Boilerplate Code Preview Modal (One-Click Exporter) -->
    <div 
        x-show="scaffoldModalOpen" 
        x-cloak 
        @keydown.escape.window="scaffoldModalOpen = false"
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 print:hidden"
        style="display: none;"
    >
        <!-- Backdrop Overlay -->
        <div class="fixed inset-0 bg-black/85 backdrop-blur-md" @click="scaffoldModalOpen = false" x-transition.opacity></div>

        <!-- Modal Dialog Card (Click events stopped from propagating to backdrop) -->
        <div 
            @click.stop
            class="relative z-10 bg-zinc-950 border-2 border-emerald-500 w-full max-w-5xl rounded-none shadow-2xl p-4 sm:p-6 flex flex-col h-[90vh] max-h-[90vh] overflow-hidden"
            x-transition
        >
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-zinc-800 pb-3 mb-3 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-emerald-500 rounded-none inline-block"></span>
                    <h3 class="text-sm sm:text-base font-mono font-black uppercase text-white tracking-wider">
                        {{ $isEn ? 'SCAFFOLD & BOILERPLATE CODE EXPORTER // 10 PILLARS SOFTWARE FACTORY OS' : 'EKSPOR KODE SCAFFOLD & BOILERPLATE // 10 PILAR SOFTWARE FACTORY OS' }}
                    </h3>
                </div>
                <button @click="scaffoldModalOpen = false" class="text-zinc-400 hover:text-white text-xl font-bold p-1 cursor-pointer">
                    &times;
                </button>
            </div>

            <!-- Modal Info Banner -->
            <div class="mb-3 p-2.5 bg-zinc-900 border border-zinc-800 text-xs font-mono text-zinc-300 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
                <div>
                    <span class="text-emerald-400 font-bold">{{ $isEn ? 'Target Stack:' : 'Target Framework:' }}</span> Laravel 13 (PHP 8.5+) &bull; PostgreSQL 16 Strict ULID &bull; Redis 7 &bull; Next.js App Router
                </div>
                <a href="{{ route('blueprint.export-scaffold', $blueprint->slug) }}" class="px-3.5 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-black font-black uppercase text-[11px] flex items-center gap-1.5 transition cursor-pointer w-fit">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>{{ $isEn ? 'DOWNLOAD FULL ZIP (.ZIP)' : 'UNDUH ZIP LENGKAP (.ZIP)' }}</span>
                </a>
            </div>

            <!-- Tab Selector (All 10 Pillars of Software Factory OS) -->
            <div class="flex items-center gap-1.5 mb-3 font-mono text-xs border-b border-zinc-800 pb-2 shrink-0 overflow-x-auto custom-prd-scrollbar py-1 select-none">
                <template x-for="tab in [
                    { id: 'docs/product/PRODUCT-BRIEF.md', label: 'PRODUCT-BRIEF.md (Pillar 0)' },
                    { id: 'docs/architecture/ADR-001-postgresql-ulid.md', label: 'ADR-001.md (Architecture)' },
                    { id: 'docker-compose.yml', label: 'docker-compose.yml (Containers)' },
                    { id: 'openapi.json', label: 'openapi.json (OpenAPI 3.1)' },
                    { id: 'schema_complete.sql', label: 'schema_complete.sql (PostgreSQL)' },
                    { id: 'design/wireframes/screens.md', label: 'screens.md (UI/UX Wireframes)' },
                    { id: 'design/tokens.json', label: 'tokens.json (Design Tokens)' },
                    { id: 'routes/web.php', label: 'routes/web.php (Laravel 13)' },
                    { id: 'routes/api.php', label: 'routes/api.php (Sanctum)' },
                    { id: 'app/Http/Controllers/HealthProbeController.php', label: 'HealthProbeController.php (Pillar 8)' },
                    { id: 'app/api/route.ts', label: 'Next.js App Router (TypeScript)' },
                    { id: 'database/seeders/SyntheticDataSeeder.php', label: 'SyntheticDataSeeder.php (Pillar 4)' },
                    { id: '.cursorrules', label: '.cursorrules (AI Agent)' },
                    { id: 'AGENTS.md', label: 'AGENTS.md (AI & ECC Rules)' },
                    { id: 'tests/Feature/ApiContractTest.php', label: 'ApiContractTest.php (Pest Contract)' },
                    { id: 'tests/e2e/smoke.spec.ts', label: 'smoke.spec.ts (Playwright E2E)' },
                    { id: 'deploy.sh', label: 'deploy.sh (CI/CD 6 Skenario)' },
                    { id: '.github/workflows/deploy.yml', label: 'deploy.yml (GitHub Actions)' },
                    { id: 'scripts/backup.sh', label: 'backup.sh (Disaster Recovery)' },
                    { id: 'scripts/restore.sh', label: 'restore.sh (Restore Drill)' },
                    { id: 'docs/runbooks/disaster-recovery.md', label: 'disaster-recovery.md (Runbook)' },
                    { id: 'README.md', label: 'README.md' }
                ]" :key="tab.id">
                    <button 
                        type="button" 
                        @click="scaffoldActiveTab = tab.id" 
                        :class="scaffoldActiveTab === tab.id ? 'bg-emerald-500 text-black font-black' : 'bg-zinc-900 text-zinc-400 hover:text-white'" 
                        class="px-2.5 py-1.5 transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 rounded-none"
                    >
                        <span x-text="tab.label"></span>
                        <span 
                            x-show="scaffoldCustomizedFiles.includes(tab.id)" 
                            class="w-1.5 h-1.5 bg-amber-400 inline-block rounded-none shadow-sm"
                            title="File ini telah dielaborasi secara kustom"
                        ></span>
                    </button>
                </template>
            </div>

            <!-- Code Content Area (Strictly Scrollable Container) -->
            <div class="relative flex-1 min-h-0 flex flex-col bg-black border border-zinc-800 p-4 font-mono text-xs overflow-hidden">
                <div x-show="scaffoldLoading" class="absolute inset-0 bg-black/80 flex items-center justify-center text-emerald-400 text-sm font-mono font-bold animate-pulse z-10">
                    Memuat sintesis file scaffold...
                </div>
                <div class="shrink-0 flex flex-wrap items-center justify-between gap-2 pb-2 mb-2 border-b border-zinc-900 text-[10px]">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="w-2 h-2 rounded-none shrink-0" :class="scaffoldCustomizedFiles.includes(scaffoldActiveTab) ? 'bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.6)]' : 'bg-emerald-500'"></span>
                        <span class="text-zinc-200 font-bold" x-text="scaffoldActiveTab">docker-compose.yml</span>
                        <span class="text-zinc-500" x-text="'(' + ((getScaffoldCode(scaffoldActiveTab) || '').split('\n').length) + ' baris)'"></span>
                        <template x-if="scaffoldCustomizedFiles.includes(scaffoldActiveTab)">
                            <span class="px-1.5 py-0.5 bg-amber-500/10 border border-amber-500/30 text-amber-400 text-[9px] font-bold uppercase tracking-wider">
                                DIELABORASI (CUSTOM)
                            </span>
                        </template>
                        <template x-if="!scaffoldCustomizedFiles.includes(scaffoldActiveTab)">
                            <span class="px-1.5 py-0.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-[9px] font-bold uppercase tracking-wider">
                                DEFAULT AI SINTESIS
                            </span>
                        </template>
                    </div>

                    <!-- Auto-Save Status Indicator & Action Bar -->
                    <div class="flex items-center gap-2">
                        <!-- Auto-Save Status Indicator -->
                        <div class="flex items-center gap-1.5 px-2 py-0.5 bg-zinc-900 border border-zinc-800 text-[10px]">
                            <template x-if="scaffoldSaveStatus === 'typing'">
                                <span class="flex items-center gap-1 text-amber-400">
                                    <span class="w-1.5 h-1.5 bg-amber-400 animate-ping"></span>
                                    <span>Mengetik...</span>
                                </span>
                            </template>
                            <template x-if="scaffoldSaveStatus === 'saving'">
                                <span class="flex items-center gap-1 text-sky-400 animate-pulse">
                                    <svg class="w-2.5 h-2.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    <span>Menyimpan otomatis...</span>
                                </span>
                            </template>
                            <template x-if="scaffoldSaveStatus === 'saved'">
                                <span class="flex items-center gap-1 text-emerald-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    <span x-text="'Tersimpan ' + scaffoldLastSaved"></span>
                                </span>
                            </template>
                            <template x-if="scaffoldSaveStatus === 'error'">
                                <span class="flex items-center gap-1 text-rose-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    <span>Gagal simpan</span>
                                </span>
                            </template>
                            <template x-if="scaffoldSaveStatus === 'idle'">
                                <span class="text-zinc-500">Auto-Save Siap</span>
                            </template>
                        </div>

                        <!-- Mode Switcher: Preview vs Edit & Elaborasi -->
                        <div class="inline-flex border border-zinc-800 p-0.5 bg-zinc-950">
                            <button type="button" @click="scaffoldMode = 'preview'" :class="scaffoldMode === 'preview' ? 'bg-zinc-800 text-zinc-100 font-bold' : 'text-zinc-500 hover:text-zinc-300'" class="px-2 py-0.5 transition cursor-pointer">
                                PREVIEW
                            </button>
                            <button type="button" @click="scaffoldFiles[scaffoldActiveTab] = getScaffoldCode(scaffoldActiveTab); scaffoldMode = 'edit'" :class="scaffoldMode === 'edit' ? 'bg-emerald-500 text-black font-black' : 'text-zinc-500 hover:text-zinc-300'" class="px-2 py-0.5 transition cursor-pointer flex items-center gap-1">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                <span>EDIT &amp; ELABORASI</span>
                            </button>
                        </div>

                        <!-- AI Grounded Deepener Button for Active Scaffold File -->
                        <button 
                            type="button" 
                            @click="openTweakModal('scaffold_file', 'Scaffold // ' + scaffoldActiveTab, getScaffoldCode(scaffoldActiveTab))" 
                            class="px-2 py-0.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 transition cursor-pointer flex items-center gap-1 font-mono text-[10px] font-bold"
                            title="Perdalam file scaffold ini secara langsung menggunakan AI berbasis Dokumen Acuan"
                        >
                            <svg class="w-2.5 h-2.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>PERDALAM DARI ACUAN</span>
                        </button>

                        <!-- Reset to AI default button (if customized) -->
                        <template x-if="scaffoldCustomizedFiles.includes(scaffoldActiveTab)">
                            <button type="button" @click="resetActiveScaffoldFile()" class="px-2 py-0.5 border border-zinc-800 hover:border-amber-500/50 hover:bg-zinc-900 text-zinc-400 hover:text-amber-400 transition cursor-pointer flex items-center gap-1" title="Kembalikan file ini ke sintesis default AI">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>RESET AI</span>
                            </button>
                        </template>

                        <button type="button" @click="copyActiveScaffold()" class="text-emerald-400 hover:text-emerald-300 font-bold uppercase transition flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            <span>Salin</span>
                        </button>
                    </div>
                </div>

                <!-- Code Container: Preview Mode vs Edit/Elaborasi Mode -->
                <div class="flex-1 min-h-0 relative overflow-hidden flex flex-col">
                    <!-- PREVIEW MODE -->
                    <pre 
                        x-show="scaffoldMode === 'preview'"
                        class="flex-1 min-h-0 w-full overflow-y-scroll overflow-x-auto text-[11px] text-emerald-400 leading-relaxed select-all font-mono whitespace-pre p-3 bg-zinc-950/90 border border-zinc-900 custom-prd-scrollbar focus:outline-none" 
                        tabindex="0"
                        x-text="getScaffoldCode(scaffoldActiveTab)"
                    ></pre>

                    <!-- EDIT & ELABORASI MODE (<textarea> with Tab Support & Auto-Save) -->
                    <div x-show="scaffoldMode === 'edit'" class="flex-1 min-h-0 flex flex-col relative">
                        <textarea 
                            class="flex-1 min-h-0 w-full overflow-y-scroll overflow-x-auto text-[11px] text-emerald-300 leading-relaxed font-mono p-3 bg-zinc-950 border border-emerald-500/40 custom-prd-scrollbar focus:outline-none focus:border-emerald-400 resize-none selection:bg-emerald-500/30 selection:text-white"
                            spellcheck="false"
                            x-model="scaffoldFiles[scaffoldActiveTab]"
                            @input="onScaffoldCodeInput()"
                            @keydown.tab.prevent="insertTabInEditor($event)"
                            placeholder="Tulis atau elaborasi spesifikasi scaffold kode di sini... Perubahan akan otomatis tersimpan dalam 1 detik."
                        ></textarea>
                        <div class="absolute bottom-2 right-4 pointer-events-none text-[9px] text-zinc-500 bg-zinc-950/90 px-2 py-0.5 border border-zinc-800">
                            TAB = 4 SPASI &bull; AUTO-SAVE 1000ms
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between pt-3 mt-3 border-t border-zinc-800 shrink-0 font-mono text-xs">
                <button type="button" @click="scaffoldModalOpen = false" class="px-4 py-2 border border-zinc-700 hover:bg-zinc-800 text-zinc-300 uppercase font-bold transition cursor-pointer">
                    {{ $isEn ? 'CLOSE' : 'TUTUP' }}
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" @click="copyActiveScaffold()" class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-emerald-400 border border-emerald-500/50 uppercase font-bold transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                        <span>{{ $isEn ? 'COPY TAB CODE' : 'SALIN KODE TAB' }}</span>
                    </button>
                    <a href="{{ route('blueprint.export-scaffold', $blueprint->slug) }}" class="px-5 py-2 bg-emerald-500 hover:bg-emerald-400 text-black uppercase font-black transition flex items-center gap-1.5 cursor-pointer shadow-lg">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>{{ $isEn ? 'DOWNLOAD .ZIP' : 'UNDUH .ZIP' }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

