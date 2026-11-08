    <header id="blueprint-toolbar" 
            class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 py-2.5 px-4 sm:px-6 sticky z-40 no-print transition-all shadow-xs mt-0 mb-4"
            style="top: var(--global-nav-h, 88px);">
        <div class="w-full max-w-[1700px] mx-auto flex items-center justify-center flex-wrap gap-2 sm:gap-2.5">
            <!-- Back to Client Portal Button -->
            <a href="/customer/dashboard" class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 text-xs font-mono font-bold rounded-none border border-zinc-300 dark:border-zinc-700 flex items-center gap-1.5 transition shrink-0 whitespace-nowrap cursor-pointer" :title="locale === 'en' ? 'Back to Client Portal' : 'Kembali ke Portal Klien'">
                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span x-show="locale === 'en'">CLIENT PORTAL</span>
                <span x-show="locale !== 'en'">PORTAL KLIEN</span>
            </a>

            @if(!$blueprint->is_published)
                <span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 text-xs font-mono uppercase font-bold border border-amber-300 dark:border-amber-800 flex items-center gap-1.5 select-none shrink-0 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>ACCESS RESTRICTED // PRIVATE DRAFT</span>
                </span>
            @else
                <!-- Real-time Live Collaborative Presence Indicator -->
                <div x-show="collaborators.length > 1" x-cloak class="flex items-center gap-2 px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-xs font-mono shrink-0 whitespace-nowrap">
                    <span class="w-2 h-2 bg-emerald-500 rounded-none animate-pulse"></span>
                    <span class="text-zinc-700 dark:text-zinc-300 font-bold" x-text="locale === 'en' ? (collaborators.length + ' Live Collaborators') : (collaborators.length + ' Kolaborator Live')"></span>
                </div>

                <!-- Launch World-Class Developer Execution Guide (3 Cara Mulai & Docker Guide) -->
                <button type="button" @click="showExecutionGuideModal = true" class="px-2.5 sm:px-3 py-1 bg-emerald-500 hover:bg-emerald-400 text-black text-xs font-mono uppercase font-black rounded-none border border-emerald-400 flex items-center gap-1.5 transition shrink-0 whitespace-nowrap cursor-pointer shadow-xs" :title="locale === 'en' ? 'Developer Execution Guide: 3 Ways to Start + Docker Dev Stack' : 'Panduan Eksekusi Developer: 3 Pilihan Cara Mulai (Antigravity Chat, Standalone App, AI Cockpit) + Docker Stack'">
                    <span x-show="locale === 'en'">🚀 3 WAYS TO START</span>
                    <span x-show="locale !== 'en'">🚀 3 CARA MULAI</span>
                </button>

                <!-- Launch Full-Screen 100vh 3-Pane Architect Studio v5.0 (Dual-Mode Retail & Mitra) -->
                <a href="{{ route('blueprint.studio', ['slug' => $blueprint->slug]) }}" class="px-2.5 sm:px-3 py-1 bg-zinc-950 hover:bg-zinc-800 text-emerald-400 dark:bg-emerald-600 dark:hover:bg-emerald-500 dark:text-zinc-950 text-xs font-mono uppercase font-black rounded-none border border-emerald-500 flex items-center gap-1.5 transition shrink-0 whitespace-nowrap" :title="locale === 'en' ? 'Open Fullscreen 3-Pane Architect Studio v5.0' : 'Buka Meja Kerja 3-Pane Architect Studio v5.0 (Dual-Mode Retail & Mitra, Decision Matrix, Delta v1.1 & Live MCP)'">
                    <span>🖥️ 3-PANE STUDIO</span>
                </a>
                <a href="{{ route('blueprint.studio', ['slug' => $blueprint->slug, 'mode' => 'mitra']) }}" class="px-2.5 sm:px-3 py-1 bg-amber-100 hover:bg-amber-200 text-amber-950 dark:bg-amber-950/80 dark:hover:bg-amber-900 dark:text-amber-200 text-xs font-mono uppercase font-black rounded-none border border-amber-400 dark:border-amber-700 flex items-center gap-1.5 transition shrink-0 whitespace-nowrap" :title="locale === 'en' ? 'Open Partner Mode: Algorithmic BoQ, SHA-256 Scope Lock, UAT Checklist' : 'Buka Mode Mitra Proyek: RAB Algoritmik, Kunci SPK SHA-256, Checklist UAT & BAST Digital'">
                    <span x-show="locale === 'en'">🤝 PARTNER MODE</span>
                    <span x-show="locale !== 'en'">🤝 MODE MITRA</span>
                </a>

                <!-- Official A4 Mitra Commercial Pack (RAB, SPK Appendix SHA-256, Digital BAST) -->
                <div class="inline-flex rounded-none border border-amber-400 dark:border-amber-700 divide-x divide-amber-400 dark:divide-amber-700 bg-amber-50 dark:bg-amber-950/40 text-xs font-mono font-black shrink-0 whitespace-nowrap">
                    <a href="{{ route('blueprint.mitra-document', ['slug' => $blueprint->slug, 'docType' => 'rab']) }}" :href="'{{ route('blueprint.mitra-document', ['slug' => $blueprint->slug, 'docType' => 'rab']) }}&locale=' + (locale || 'id')" target="_blank" class="px-2 py-1 hover:bg-amber-200/70 dark:hover:bg-amber-900/60 text-amber-950 dark:text-amber-200 transition" title="Cetak / Unduh A4 Estimasi RAB Algoritmik & WBS 7 Fase">
                        <span>RAB A4</span>
                    </a>
                    <a href="{{ route('blueprint.mitra-document', ['slug' => $blueprint->slug, 'docType' => 'spk-appendix']) }}" :href="'{{ route('blueprint.mitra-document', ['slug' => $blueprint->slug, 'docType' => 'spk-appendix']) }}&locale=' + (locale || 'id')" target="_blank" class="px-2 py-1 hover:bg-amber-200/70 dark:hover:bg-amber-900/60 text-amber-950 dark:text-amber-200 transition" title="Cetak / Unduh A4 Lampiran Teknis Kontrak SPK/PKS bersegel SHA-256">
                        <span>SPK SHA-256</span>
                    </a>
                    <a href="{{ route('blueprint.mitra-document', ['slug' => $blueprint->slug, 'docType' => 'bast']) }}" :href="'{{ route('blueprint.mitra-document', ['slug' => $blueprint->slug, 'docType' => 'bast']) }}&locale=' + (locale || 'id')" target="_blank" class="px-2 py-1 hover:bg-amber-200/70 dark:hover:bg-amber-900/60 text-amber-950 dark:text-amber-200 transition" title="Cetak / Unduh A4 Berita Acara Serah Terima (BAST) Digital & Rekap UAT">
                        <span>BAST A4</span>
                    </a>
                </div>

                <!-- One-Click Scaffold & Boilerplate Exporter Modal Button -->
                <button type="button" @click="openScaffoldModal()" class="px-2.5 sm:px-3 py-1 bg-emerald-500 hover:bg-emerald-400 text-black text-xs font-mono uppercase font-black rounded-none flex items-center gap-1.5 transition cursor-pointer shadow-none shrink-0 whitespace-nowrap" title="Ekspor Docker Compose, SQL Migrasi, & Struktur Route">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span class="hidden sm:inline">EXPORT SCAFFOLD</span>
                    <span class="sm:hidden">SCAFFOLD</span>
                </button>

                <!-- AI Architecture Advisory & Tech Radar Trigger (Zero-Leak Gated for Paid Packages) -->
                @if($blueprint->isPurchasedPackage())
                    <button 
                        type="button" 
                        @click="openAdvisoryModal()" 
                        class="px-2.5 sm:px-3 py-1 bg-zinc-950 hover:bg-zinc-800 text-amber-400 dark:bg-amber-500/10 dark:hover:bg-amber-500/20 dark:text-amber-300 text-xs font-mono uppercase font-black rounded-none border border-amber-400 dark:border-amber-600 flex items-center gap-1.5 transition shrink-0 whitespace-nowrap cursor-pointer"
                        :title="locale === 'en' ? 'Open AI Architecture Advisory & Tech Stack Evolution Radar' : 'Buka Penasihat Arsitektur AI & Radar Modernisasi Teknologi 2026'"
                    >
                        <span class="w-2 h-2 bg-amber-400 rounded-none animate-pulse"></span>
                        <span class="hidden md:inline" x-text="locale === 'en' ? '✨ TECH RADAR & ADVISORY' : '✨ SARAN FITUR & TECH RADAR'">✨ SARAN FITUR & TECH RADAR</span>
                        <span class="md:hidden">✨ ADVISORY</span>
                    </button>
                @else
                    <button 
                        type="button" 
                        @click="openAdvisoryModal()" 
                        class="px-2.5 sm:px-3 py-1 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-400 text-xs font-mono uppercase font-bold rounded-none border border-zinc-300 dark:border-zinc-700 flex items-center gap-1.5 transition shrink-0 whitespace-nowrap cursor-pointer"
                        :title="locale === 'en' ? 'Locked: AI Architecture Advisory (Exclusive to Paid Packages)' : 'Terkunci: Penasihat Arsitektur AI & Tech Radar (Eksklusif Lisensi Berbayar)'"
                    >
                        <span>🔒</span>
                        <span class="hidden md:inline" x-text="locale === 'en' ? 'TECH RADAR [PRO]' : 'SARAN FITUR [PRO]'">SARAN FITUR [PRO]</span>
                        <span class="md:hidden">🔒 PRO</span>
                    </button>
                @endif

                <!-- Navigation Layout Mode Switcher (Mode 1: Sidebar Index vs Mode 2: Full Width & Floating Dock) -->
                <button 
                    type="button" 
                    @click="toggleNavMode()" 
                    class="px-2.5 sm:px-3 py-1 font-mono text-xs uppercase font-black rounded-none border transition flex items-center gap-1.5 shrink-0 whitespace-nowrap cursor-pointer shadow-none"
                    :class="navMode === 'sidebar' 
                        ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 border-zinc-900 dark:border-zinc-100' 
                        : 'bg-emerald-100 text-emerald-950 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-400 dark:border-emerald-700 hover:bg-emerald-200 dark:hover:bg-emerald-900'"
                    :title="navMode === 'sidebar' 
                        ? 'Mode 1 Aktif: Index disematkan di kiri layar. Klik untuk beralih ke Mode 2 (Layar Penuh).' 
                        : 'Mode 2 Aktif: Layar Penuh. Klik untuk beralih ke Mode 1 (Sidebar Kiri).'"
                >
                    <span x-show="navMode === 'sidebar'" class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-400 dark:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h6v12H4zM12 6h8v12h-8z"/></svg>
                        <span class="hidden md:inline"><span x-show="locale === 'en'">MODE 1: SIDEBAR</span><span x-show="locale !== 'en'">MODE 1: SIDEBAR</span></span>
                        <span class="md:hidden">SIDEBAR</span>
                    </span>
                    <span x-show="navMode !== 'sidebar'" x-cloak class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                        <span class="hidden md:inline"><span x-show="locale === 'en'">MODE 2: FULL WIDTH</span><span x-show="locale !== 'en'">MODE 2: LEBAR PENUH</span></span>
                        <span class="md:hidden"><span x-show="locale === 'en'">FULL</span><span x-show="locale !== 'en'">LEBAR</span></span>
                    </span>
                </button>

                <!-- Unified Document Actions: Print, PDF, MD, .cursorrules -->
                <div class="inline-flex rounded-none border border-zinc-300 dark:border-zinc-700 divide-x divide-zinc-300 dark:divide-zinc-700 bg-zinc-100 dark:bg-zinc-800 text-xs font-mono font-bold shrink-0 whitespace-nowrap">
                    <button type="button" onclick="window.printPrdDocument()" class="px-2.5 py-1 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 transition flex items-center gap-1 cursor-pointer" :title="locale === 'en' ? 'Print PRD Document' : 'Cetak PRD (Print)'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span class="hidden md:inline">PRINT</span>
                    </button>
                    <a href="{{ route('blueprint.download-pdf', ['slug' => $blueprint->slug, 'locale' => $isEn ? 'en' : 'id']) }}" :href="'{{ route('blueprint.download-pdf', $blueprint->slug) }}?locale=' + (locale || 'id')" class="px-2.5 py-1 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 transition flex items-center gap-1" :title="locale === 'en' ? 'Download Official PDF Document' : 'Unduh Dokumen PDF Resmi'">
                        <span>PDF</span>
                    </a>
                    <button type="button" @click.prevent="$dispatch('open-raw-md-warning', { action: 'download' })" class="px-2.5 py-1 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 transition flex items-center gap-1 cursor-pointer" :title="locale === 'en' ? 'Download Raw Markdown (.MD)' : 'Unduh Markdown Asli (.MD) - Disarankan Per-Fitur di Cockpit'">
                        <span class="hidden sm:inline">MD</span>
                    </button>
                    <a href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'locale' => $isEn ? 'en' : 'id']) }}" :href="'{{ route('blueprint.download-cursorrules', $blueprint->slug) }}?locale=' + (locale || 'id')" class="px-2.5 py-1 hover:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 transition flex items-center gap-1" :title="locale === 'en' ? 'Download .cursorrules / CLAUDE.md for AI IDE' : 'Unduh .cursorrules / CLAUDE.md untuk AI IDE'">
                        <span class="hidden xl:inline">.CURSORRULES</span>
                        <span class="xl:hidden">RULES</span>
                    </a>
                    <a href="{{ route('blueprint.download-schema-json', ['slug' => $blueprint->slug, 'locale' => $isEn ? 'en' : 'id']) }}" :href="'{{ route('blueprint.download-schema-json', $blueprint->slug) }}?locale=' + (locale || 'id')" class="px-2.5 py-1 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-200 transition flex items-center gap-1" :title="locale === 'en' ? 'Download Universal Blueprint Schema (BLUEPRINT_SCHEMA.json)' : 'Unduh Universal Blueprint Schema v2.0.0 (BLUEPRINT_SCHEMA.json)'">
                        <span class="hidden xl:inline">SCHEMA.JSON</span>
                        <span class="xl:hidden">JSON</span>
                    </a>
                    <a href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'execution-blueprint', 'locale' => $isEn ? 'en' : 'id']) }}" :href="'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'execution-blueprint']) }}&locale=' + (locale || 'id')" class="px-2.5 py-1 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-200 transition flex items-center gap-1" :title="locale === 'en' ? 'Download Zero-Assumption Execution Contract (PROJECT_OS_EXECUTION_BLUEPRINT.md)' : 'Unduh Kontrak Eksekusi Zero-Assumption (PROJECT_OS_EXECUTION_BLUEPRINT.md)'">
                        <span class="hidden xl:inline">EXECUTION.MD</span>
                        <span class="xl:hidden">EXEC</span>
                    </a>
                    <a href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'decision-tables', 'locale' => $isEn ? 'en' : 'id']) }}" :href="'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'decision-tables']) }}&locale=' + (locale || 'id')" class="px-2.5 py-1 hover:bg-emerald-500/20 text-emerald-900 dark:text-emerald-300 transition flex items-center gap-1" :title="locale === 'en' ? 'Download Deterministic Business Rule Decision Tables' : 'Unduh Deterministic Business Rule Decision Tables (.agents/DECISION_TABLES_CONTRACT.md)'">
                        <span class="hidden xl:inline">DECISION.MD</span>
                        <span class="xl:hidden">DEC</span>
                    </a>
                    <a href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'env-blueprint', 'locale' => $isEn ? 'en' : 'id']) }}" :href="'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'env-blueprint']) }}&locale=' + (locale || 'id')" class="px-2.5 py-1 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-200 transition flex items-center gap-1" :title="locale === 'en' ? 'Download Environment Blueprint (.env.blueprint)' : 'Unduh Environment & Secret Vault Blueprint (.env.blueprint)'">
                        <span class="hidden xl:inline">.ENV.BLUEPRINT</span>
                        <span class="xl:hidden">ENV</span>
                    </a>
                    <a href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'verify-blueprint', 'locale' => $isEn ? 'en' : 'id']) }}" :href="'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'verify-blueprint']) }}&locale=' + (locale || 'id')" class="px-2.5 py-1 hover:bg-emerald-500/20 text-emerald-950 dark:text-emerald-200 font-black transition flex items-center gap-1" :title="locale === 'en' ? 'Download CI Gatekeeper Scanner (verify-blueprint.sh)' : 'Unduh CI Gatekeeper Anti-Slop Scanner (verify-blueprint.sh)'">
                        <span class="hidden xl:inline">VERIFY.SH</span>
                        <span class="xl:hidden">SH</span>
                    </a>
                    <a href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'symbol-manifest', 'locale' => $isEn ? 'en' : 'id']) }}" :href="'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'symbol-manifest']) }}&locale=' + (locale || 'id')" class="px-2.5 py-1 hover:bg-emerald-500/20 text-emerald-950 dark:text-emerald-200 font-black transition flex items-center gap-1" :title="locale === 'en' ? 'Download Exact Symbol Manifest (SYMBOL_MANIFEST.json)' : 'Unduh Exact Symbol Lock Anti-Amnesia (.agents/SYMBOL_MANIFEST.json)'">
                        <span class="hidden xl:inline">SYMBOL.JSON</span>
                        <span class="xl:hidden">SYM</span>
                    </a>
                    <a href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'enterprise-menu', 'locale' => $isEn ? 'en' : 'id']) }}" :href="'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'enterprise-menu']) }}&locale=' + (locale || 'id')" class="px-2.5 py-1 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-200 font-bold transition flex items-center gap-1" :title="locale === 'en' ? 'Download Enterprise Menu Matrix' : 'Unduh 25 Sub-Menu Operasional L1–L4 (.agents/ENTERPRISE_MENU_MATRIX.md)'">
                        <span class="hidden xl:inline">MENU25.MD</span>
                        <span class="xl:hidden">M25</span>
                    </a>
                    <a href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'syntax-lock', 'locale' => $isEn ? 'en' : 'id']) }}" :href="'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'syntax-lock']) }}&locale=' + (locale || 'id')" class="px-2.5 py-1 hover:bg-emerald-500/20 text-emerald-950 dark:text-emerald-200 font-black transition flex items-center gap-1" :title="locale === 'en' ? 'Download Syntax Lock Dictionary' : 'Unduh Kamus Anti-Halusinasi Sintaks 2026 (.agents/SYNTAX_LOCK_2026.md)'">
                        <span class="hidden xl:inline">SYNTAX26.MD</span>
                        <span class="xl:hidden">S26</span>
                    </a>
                    <a href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'client-sop', 'locale' => $isEn ? 'en' : 'id']) }}" :href="'{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'client-sop']) }}&locale=' + (locale || 'id')" class="px-2.5 py-1 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-200 font-bold transition flex items-center gap-1" :title="locale === 'en' ? 'Download Client Operational SOP Manual' : 'Unduh Buku SOP Operasional & Training Klien (.agents/CLIENT_SOP_AND_TRAINING_MANUAL.md)'">
                        <span class="hidden xl:inline">SOP.MD</span>
                        <span class="xl:hidden">SOP</span>
                    </a>
                    <a href="{{ route('blueprint.mitra-document', ['slug' => $blueprint->slug, 'docType' => 'sop-training', 'locale' => $isEn ? 'en' : 'id']) }}" :href="'{{ route('blueprint.mitra-document', ['slug' => $blueprint->slug, 'docType' => 'sop-training']) }}&locale=' + (locale || 'id')" target="_blank" class="px-2.5 py-1 hover:bg-amber-500/20 text-amber-950 dark:text-amber-200 font-black transition flex items-center gap-1" :title="locale === 'en' ? 'Open Printable A4 Staff Training & SOP Manual' : 'Buka Buku SOP & Panduan Training Staf Siap Cetak A4 (Lampiran BAST)'">
                        <span class="hidden xl:inline">SOP-A4</span>
                        <span class="xl:hidden">A4</span>
                    </a>
                    <a href="{{ url('/blueprint?tier=retail_spark&new=1') }}" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-black transition flex items-center gap-1" :title="locale === 'en' ? 'Start Clean New Project' : 'Mulai Proyek Baru Bersih (100% Terisolasi Tanpa Sisa Proyek Ini)'">
                        <span class="hidden xl:inline"><span x-show="locale === 'en'">+ NEW PROJECT</span><span x-show="locale !== 'en'">+ PROYEK BARU</span></span>
                        <span class="xl:hidden"><span x-show="locale === 'en'">+NEW</span><span x-show="locale !== 'en'">+BARU</span></span>
                    </a>
                </div>

                <!-- Dedicated AI AGENT HELPER Trigger -->
                <button type="button" @click="showAiPromptModal = true" class="px-2.5 sm:px-3 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-mono uppercase font-bold rounded-none border border-emerald-500/30 flex items-center gap-1.5 transition shrink-0 whitespace-nowrap cursor-pointer" :title="locale === 'en' ? 'Open AI Agent Prompt Helper (Antigravity, Cursor, Claude Code, Windsurf)' : 'Buka Pusat Helper Prompt AI Agent (Antigravity, Cursor, Claude Code, Windsurf)'">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                    <span class="hidden md:inline">PROMPT AGENT</span>
                </button>

                <!-- AI Orchestration Sprint Cockpit Button in Header -->
                <button type="button" @click="jumpTo('section-3-5')" class="px-2.5 py-1 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black text-xs font-mono uppercase font-bold rounded-none border border-zinc-700 dark:border-emerald-400 flex items-center gap-1.5 transition shrink-0 whitespace-nowrap cursor-pointer" :title="locale === 'en' ? 'Open AI Agent Sprint Execution Cockpit' : 'Buka AI Agent Sprint Execution Cockpit'">
                    <span class="w-1.5 h-1.5 rounded-none" :class="getDevCompletedCount() > 0 ? 'bg-emerald-400 dark:bg-black animate-pulse' : 'bg-amber-400 dark:bg-black'"></span>
                    <span class="hidden lg:inline">COCKPIT:</span>
                    <span x-text="getDevCompletedCount() + '/' + {{ $totalDevSteps }}"></span>
                </button>

                @php
                    $isRetailLicense = ($blueprint->project_status === 'Retail License' || !empty($blueprint->user_metadata['retail_tier']) || !empty($blueprint->user_metadata['package_tier']) || str_contains(strtolower($blueprint->slug ?? ''), '-retail-'));
                    $isDpPaid = $blueprint->isDpConfirmed();
                    $isScopeFrozen = $blueprint->isScopeFrozen();
                    $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin();
                    // Scope is locked for editing only if frozen AND not retail
                    $isLocked = $isScopeFrozen && ! $isRetailLicense;
                    $canUnlock = $isSuperAdmin || (! $isDpPaid) || $isRetailLicense;
                @endphp

                @if(!$isLocked)
                    <a href="{{ route('blueprint.create', ['slug' => $blueprint->slug]) }}" class="px-2.5 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-mono uppercase font-bold rounded-none border border-emerald-500/30 transition flex items-center gap-1 shrink-0 whitespace-nowrap" :title="locale === 'en' ? 'Complete / Add Requirements in Studio' : 'Lengkapi / Tambah Kebutuhan di Studio'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span class="hidden xl:inline" x-text="locale === 'en' ? 'EDIT SPEC' : 'LENGKAPI'">LENGKAPI</span>
                    </a>
                @elseif($canUnlock)
                    <form method="POST" action="{{ route('blueprint.unlock-scope', $blueprint->slug) }}" class="m-0 shrink-0">
                        @csrf
                        <button type="submit" class="px-2.5 py-1 {{ $isSuperAdmin ? 'bg-rose-600 hover:bg-rose-500 text-white border-rose-700' : 'bg-amber-500 hover:bg-amber-400 text-black border-amber-600' }} text-xs font-mono uppercase font-black rounded-none border transition flex items-center gap-1 shrink-0 whitespace-nowrap cursor-pointer shadow-none" :title="locale === 'en' ? ($isSuperAdmin ? 'Super Admin: Instantly unlock scope' : 'Unlock scope to continue specification elaboration') : ($isSuperAdmin ? 'Super Admin: Buka kunci scope secara instan' : 'Buka kunci scope untuk melanjutkan elaborasi spesifikasi')">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                            <span class="hidden md:inline">
                                <span x-show="locale === 'en'">{{ $isSuperAdmin ? 'UNLOCK (ADMIN)' : 'UNLOCK SCOPE' }}</span>
                                <span x-show="locale !== 'en'">{{ $isSuperAdmin ? 'UNLOCK (ADMIN)' : 'BUKA KUNCI' }}</span>
                            </span>
                            <span class="md:hidden">UNLOCK</span>
                        </button>
                    </form>
                @else
                    <span class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 text-xs font-mono uppercase font-bold border border-zinc-200 dark:border-zinc-700 flex items-center gap-1 cursor-not-allowed select-none shrink-0 whitespace-nowrap" 
                          :title="locale === 'en' ? 'Scope Locked: Active Partner project with confirmed DP. For feature revisions, submit an Addendum.' : 'Scope Terkunci: Proyek Mitra aktif dengan DP terverifikasi. Untuk revisi fitur, silakan ajukan Addendum / Change Request.'">
                        <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span class="hidden md:inline">SCOPE LOCKED</span>
                    </span>
                @endif

                @if(auth()->check() && auth()->user()->isSuperAdmin())
                    @if(!$blueprint->isDpConfirmed())
                        <form method="POST" action="{{ route('blueprint.vip-bypass', $blueprint->slug) }}" class="m-0 shrink-0">
                            @csrf
                            <button 
                                type="submit" 
                                class="px-2.5 sm:px-3 py-1 bg-amber-500 hover:bg-amber-400 text-black text-xs font-mono uppercase font-black rounded-none flex items-center gap-1.5 transition cursor-pointer shadow-none shrink-0 whitespace-nowrap border border-amber-600"
                                :title="locale === 'en' ? 'Instantly activate this Blueprint as VIP Founder without Midtrans payment' : 'Aktifkan Blueprint ini secara instan sebagai VIP Founder tanpa melalui pembayaran Midtrans'"
                            >
                                <span>👑</span>
                                <span class="hidden md:inline">VIP OWNER BYPASS</span>
                                <span class="md:hidden">BYPASS</span>
                            </button>
                        </form>
                    @else
                        <span class="px-2.5 py-1 bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/40 text-xs font-mono font-black uppercase flex items-center gap-1 shrink-0 whitespace-nowrap select-none" :title="locale === 'en' ? 'Active Project under VIP Founder License' : 'Proyek Aktif di Bawah Lisensi VIP Founder'">
                            <span>👑</span>
                            <span class="hidden md:inline">VIP FOUNDER ACTIVE</span>
                            <span class="md:hidden">VIP</span>
                        </span>
                    @endif
                @endif

                <!-- Relocated Accordion View Controls (Beside Cockpit & Edit Spec) -->
                <div class="inline-flex rounded-none border border-zinc-300 dark:border-zinc-700 divide-x divide-zinc-300 dark:divide-zinc-700 bg-zinc-100 dark:bg-zinc-800 text-xs font-mono font-bold shrink-0 whitespace-nowrap">
                    <button type="button" @click="expandAllSections()" class="px-2.5 py-1 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 transition flex items-center gap-1 cursor-pointer" :title="locale === 'en' ? 'Expand all 13 PRD sections' : 'Buka Seluruh 13 Seksi PRD'">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        <span class="hidden xl:inline"><span x-show="locale === 'en'">EXPAND ALL</span><span x-show="locale !== 'en'">BUKA SEMUA</span></span>
                        <span class="xl:hidden"><span x-show="locale === 'en'">EXPAND</span><span x-show="locale !== 'en'">BUKA</span></span>
                    </button>
                    <button type="button" @click="collapseAllSections()" class="px-2.5 py-1 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 transition flex items-center gap-1 cursor-pointer" :title="locale === 'en' ? 'Collapse all PRD sections' : 'Ciutkan Seluruh Seksi PRD'">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                        <span class="hidden xl:inline"><span x-show="locale === 'en'">COLLAPSE ALL</span><span x-show="locale !== 'en'">TUTUP SEMUA</span></span>
                        <span class="xl:hidden"><span x-show="locale === 'en'">COLLAPSE</span><span x-show="locale !== 'en'">TUTUP</span></span>
                    </button>
                </div>
            @endif
        </div>
    </header>

    <!-- Real-time Collaborative Presence Cursor Pointer (Live WebSockets / Reverb / Redis Sync) -->
    <div x-show="collaboratorCursor.visible" 
         :style="`top: ${collaboratorCursor.y}%; left: ${collaboratorCursor.x}%; transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);`" 
         class="fixed z-50 pointer-events-none flex items-center gap-1.5 select-none transition-all duration-300 transform -translate-x-1 -translate-y-1"
         x-cloak>
        <svg class="w-4 h-4 text-emerald-500 fill-emerald-500 drop-shadow-md" viewBox="0 0 24 24">
            <path d="M3 3l7.07 16.97 2.51-7.39 7.39-2.51L3 3z"/>
        </svg>
        <div class="px-2 py-0.5 bg-zinc-900/95 text-white border border-emerald-500 text-[10px] font-mono font-bold tracking-tight shadow-xl flex items-center gap-1.5 rounded-none">
            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-none animate-ping"></span>
            <span x-text="collaboratorCursor.name">Lead Architect (Neriah Pro)</span>
            <span class="text-[8px] text-emerald-400 font-mono uppercase bg-emerald-950 px-1 py-0.2 border border-emerald-800">LIVE</span>
        </div>
    </div>

