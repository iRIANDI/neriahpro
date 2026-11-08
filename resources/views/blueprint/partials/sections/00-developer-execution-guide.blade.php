@php
    $bizTitle = $blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Aplikasi Proyek');
    $slugVal = $blueprint->slug ?: 'app';
    $shortSlugVal = \App\Services\ScaffoldGeneratorService::deriveShortSlug($blueprint->slug ?? 'app');
    $firstModule = 'Modul Utama';
    $firstTables = 'Tabel Database';
    if (!empty($prd['features']['mvp_phase1'][0]['title'])) {
        $firstModule = $prd['features']['mvp_phase1'][0]['title'];
    } elseif (!empty($prd['features']['mvp_phase1'][0]['feature_name'])) {
        $firstModule = $prd['features']['mvp_phase1'][0]['feature_name'];
    }
    $erdTablesList = $prd['erd_schema']['tables'] ?? ($prd['engineering_specs']['database_erd']['entities'] ?? []);
    if (!empty($erdTablesList)) {
        $tableNames = array_slice(array_map(fn($t) => is_array($t) ? ($t['name'] ?? 'tabel') : (string)$t, $erdTablesList), 0, 3);
        $firstTables = implode(' dan ', $tableNames);
    }
@endphp

<!-- ========================================================================================= -->
<!-- SECTION 00: PETA JALAN & PANDUAN EKSEKUSI DEVELOPER // 3 CARA MULAI & DOCKER DEV STACK    -->
<!-- ========================================================================================= -->
<section id="section-00-execution-guide" class="mb-8 font-sans scroll-mt-28">
    <div class="bg-white dark:bg-zinc-950 border-2 border-emerald-500 p-5 sm:p-7 rounded-none shadow-xl relative overflow-hidden">
        <!-- Accent Top Bar Indicator -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 via-emerald-400 to-teal-500"></div>

        <!-- Section Title & Meta Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-5 mb-6">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 text-xs font-mono font-black uppercase tracking-wider bg-emerald-500 text-black rounded-none">
                        <span x-show="locale === 'en'">WORLD-CLASS ROADMAP &amp; GUARDRAILS</span>
                        <span x-show="locale !== 'en'">WORLD-CLASS PETA JALAN &amp; GUARDRAILS</span>
                    </span>
                    <span class="px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 rounded-none">
                        DOCKER PHP 8.5+ &bull; PG 16 ULID &bull; ECC HARNESS
                    </span>
                </div>
                <h2 class="text-xl sm:text-2xl lg:text-3xl font-black uppercase text-zinc-950 dark:text-zinc-50 tracking-tight flex items-center gap-2">
                    <span x-show="locale === 'en'">🚀 DEVELOPER EXECUTION GUIDE &amp; 3 WAYS TO START</span>
                    <span x-show="locale !== 'en'">🚀 PANDUAN EKSEKUSI &amp; 3 CARA MULAI PROYEK</span>
                </h2>
                <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-mono mt-1">
                    <span x-show="locale === 'en'">This blueprint is designed not merely as a reading document, but as a production-grade <strong class="text-zinc-900 dark:text-zinc-200">Executable Architecture Contract</strong> ready for coding. Choose one of the 3 approaches below to start executing your project.</span>
                    <span x-show="locale !== 'en'">Blueprint ini dirancang bukan sekadar dokumen bacaan, melainkan <strong class="text-zinc-900 dark:text-zinc-200">Executable Architecture Contract</strong> siap koding. Pilih salah satu dari 3 cara berikut untuk mulai mengeksekusi proyek Anda.</span>
                </p>
            </div>

            <!-- Quick Action Toolbar -->
            <div class="flex flex-wrap items-center gap-2 shrink-0 font-mono text-xs">
                <button 
                    type="button" 
                    @click="showExecutionGuideModal = true"
                    class="px-3.5 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-black uppercase text-xs rounded-none border border-emerald-400 transition cursor-pointer flex items-center gap-1.5 shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span x-show="locale === 'en'">OPEN FULL GUIDE MODAL</span>
                    <span x-show="locale !== 'en'">BUKA POPUP PANDUAN PENUH</span>
                </button>
                <button 
                    type="button" 
                    @click="openScaffoldModal()" 
                    class="px-3 py-2 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-white font-black uppercase text-xs rounded-none border border-zinc-900 dark:border-zinc-100 transition cursor-pointer flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span x-show="locale === 'en'">SCAFFOLD .ZIP (22 FILES)</span>
                    <span x-show="locale !== 'en'">SCAFFOLD .ZIP (22 FILE)</span>
                </button>
            </div>
        </div>

        <!-- 3 CARA MULAI: INTERACTIVE TABS & CARDS -->
        <div x-data="{ activeGuideCard: 'choice-a' }" class="space-y-4">
            <!-- Selector Tabs -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 font-mono text-xs">
                <button 
                    type="button" 
                    @click="activeGuideCard = 'choice-a'" 
                    class="p-3 border text-left transition cursor-pointer flex items-start gap-3 rounded-none"
                    :class="activeGuideCard === 'choice-a' 
                        ? 'bg-emerald-500 text-black border-emerald-400 font-black shadow-md' 
                        : 'bg-zinc-50 dark:bg-zinc-900/80 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border-zinc-300 dark:border-zinc-800 font-bold'"
                >
                    <span class="w-6 h-6 rounded-none flex items-center justify-center font-black text-xs shrink-0" :class="activeGuideCard === 'choice-a' ? 'bg-black text-emerald-400' : 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400'">
                        A
                    </span>
                    <div>
                        <div class="uppercase tracking-wider font-black text-xs">
                            <span x-show="locale === 'en'">OPTION A: Antigravity Chat Session</span>
                            <span x-show="locale !== 'en'">PILIHAN A: Sesi Chat Antigravity</span>
                        </div>
                        <div class="text-[11px] opacity-80 mt-0.5 font-normal">
                            <span x-show="locale === 'en'">Fastest &bull; Code Directly in This Session</span>
                            <span x-show="locale !== 'en'">Paling Cepat &bull; Langsung Koding di Sesi Ini</span>
                        </div>
                    </div>
                </button>

                <button 
                    type="button" 
                    @click="activeGuideCard = 'choice-b'" 
                    class="p-3 border text-left transition cursor-pointer flex items-start gap-3 rounded-none"
                    :class="activeGuideCard === 'choice-b' 
                        ? 'bg-emerald-500 text-black border-emerald-400 font-black shadow-md' 
                        : 'bg-zinc-50 dark:bg-zinc-900/80 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border-zinc-300 dark:border-zinc-800 font-bold'"
                >
                    <span class="w-6 h-6 rounded-none flex items-center justify-center font-black text-xs shrink-0" :class="activeGuideCard === 'choice-b' ? 'bg-black text-emerald-400' : 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400'">
                        B
                    </span>
                    <div>
                        <div class="uppercase tracking-wider font-black text-xs">
                            <span x-show="locale === 'en'">OPTION B: Clean Standalone Project</span>
                            <span x-show="locale !== 'en'">PILIHAN B: Standalone Proyek Baru</span>
                        </div>
                        <div class="text-[11px] opacity-80 mt-0.5 font-normal">
                            <span x-show="locale === 'en'">Download Starter Kit ZIP &bull; Docker Container</span>
                            <span x-show="locale !== 'en'">Unduh Starter Kit ZIP &bull; Docker Container</span>
                        </div>
                    </div>
                </button>

                <button 
                    type="button" 
                    @click="activeGuideCard = 'choice-c'" 
                    class="p-3 border text-left transition cursor-pointer flex items-start gap-3 rounded-none"
                    :class="activeGuideCard === 'choice-c' 
                        ? 'bg-emerald-500 text-black border-emerald-400 font-black shadow-md' 
                        : 'bg-zinc-50 dark:bg-zinc-900/80 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border-zinc-300 dark:border-zinc-800 font-bold'"
                >
                    <span class="w-6 h-6 rounded-none flex items-center justify-center font-black text-xs shrink-0" :class="activeGuideCard === 'choice-c' ? 'bg-black text-emerald-400' : 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400'">
                        C
                    </span>
                    <div>
                        <div class="uppercase tracking-wider font-black text-xs">
                            <span x-show="locale === 'en'">OPTION C: Step-by-Step AI Cockpit</span>
                            <span x-show="locale !== 'en'">PILIHAN C: AI Cockpit Bertahap</span>
                        </div>
                        <div class="text-[11px] opacity-80 mt-0.5 font-normal">
                            <span x-show="locale === 'en'">Vertical Slice &bull; Anti Context-Rot (0/11)</span>
                            <span x-show="locale !== 'en'">Vertical Slice &bull; Anti Context-Rot (0/11)</span>
                        </div>
                    </div>
                </button>
            </div>

            <!-- CARD CONTENT A: EKSEKUSI LANGSUNG DI SESI CHAT ANTIGRAVITY -->
            <div x-show="activeGuideCard === 'choice-a'" x-cloak class="p-5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-emerald-500 text-black font-mono text-[10px] font-black uppercase">
                            <span x-show="locale === 'en'">LOCAL RECOMMENDATION</span>
                            <span x-show="locale !== 'en'">REKOMENDASI LOKAL</span>
                        </span>
                        <h3 class="font-mono font-black text-sm text-zinc-950 dark:text-zinc-100 uppercase">
                            <span x-show="locale === 'en'">🚀 Option A: Direct Execution in This Antigravity Chat Session (Fastest &amp; Practical)</span>
                            <span x-show="locale !== 'en'">🚀 Pilihan A: Eksekusi Langsung di Sesi Chat Antigravity Ini (Paling Cepat &amp; Praktis)</span>
                        </h3>
                    </div>
                    <span class="text-xs font-mono text-zinc-500 dark:text-zinc-400">Zero Setup Required</span>
                </div>

                <p class="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed font-sans">
                    <span x-show="locale === 'en'">Since you are currently <strong>already inside Antigravity IDE</strong> and this agent has direct access to the codebase filesystem, PowerShell terminal, and database, you can instruct this agent immediately in this chat box.</span>
                    <span x-show="locale !== 'en'">Karena Anda saat ini <strong>sudah berada di dalam Antigravity IDE</strong> dan agen ini memiliki akses langsung ke filesystem codebase, terminal PowerShell, serta database, Anda dapat langsung menginstruksikan agen ini di kotak chat.</span>
                </p>

                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs font-mono">
                        <span class="font-bold text-zinc-900 dark:text-zinc-200 uppercase">
                            <span x-show="locale === 'en'">Copy Sprint 1 Command (Database &amp; Model Foundation):</span>
                            <span x-show="locale !== 'en'">Salin Perintah Sprint 1 (Fondasi Database &amp; Model):</span>
                        </span>
                        <button 
                            type="button" 
                            @click="copyExecutionPrompt(locale === 'en' ? `Antigravity, please read Blueprint '${bizTitle}'. Start executing Sprint 1: Create PostgreSQL Strict ULID migrations and Eloquent Models for ${firstModule} (${firstTables}) module according to Section 05 specifications. Ensure using ->ulid('id')->primary() and HasUlids trait, then run php artisan migrate.` : `Antigravity, tolong baca Blueprint '${bizTitle}'. Mulai eksekusi Sprint 1: Buat migration PostgreSQL Strict ULID dan Model Eloquent untuk modul ${firstModule} (${firstTables}) sesuai spesifikasi Section 05. Pastikan menggunakan ->ulid('id')->primary() dan trait HasUlids, lalu jalankan php artisan migrate.`, locale === 'en' ? 'SPRINT 1 PROMPT COPIED' : 'PROMPT SPRINT 1 DISALIN')"
                            class="px-2.5 py-1 bg-emerald-500 hover:bg-emerald-400 text-black font-black text-[11px] rounded-none cursor-pointer transition flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            <span x-show="locale === 'en'">COPY SPRINT 1 PROMPT</span>
                            <span x-show="locale !== 'en'">SALIN PROMPT SPRINT 1</span>
                        </button>
                    </div>

                    <div class="p-3 bg-zinc-950 border border-zinc-800 font-mono text-xs text-emerald-400 select-all leading-relaxed whitespace-pre-wrap rounded-none">
                        <span x-show="locale === 'en'">Antigravity, please read Blueprint '{{ $bizTitle }}'. Start executing Sprint 1: Create PostgreSQL Strict ULID migrations and Eloquent Models for {{ $firstModule }} ({{ $firstTables }}) module according to Section 05 specifications. Ensure using ->ulid('id')->primary() and HasUlids trait, then run php artisan migrate.</span>
                        <span x-show="locale !== 'en'">Antigravity, tolong baca Blueprint '{{ $bizTitle }}'. Mulai eksekusi Sprint 1: Buat migration PostgreSQL Strict ULID dan Model Eloquent untuk modul {{ $firstModule }} ({{ $firstTables }}) sesuai spesifikasi Section 05. Pastikan menggunakan ->ulid('id')->primary() dan trait HasUlids, lalu jalankan php artisan migrate.</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs font-mono pt-2">
                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="text-emerald-600 dark:text-emerald-400 font-bold mb-1">
                            <span x-show="locale === 'en'">1. DDL &amp; ERD Automation</span>
                            <span x-show="locale !== 'en'">1. Otomasi DDL &amp; ERD</span>
                        </div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">
                            <span x-show="locale === 'en'">Antigravity inspects Section 05 specs and writes table migrations with the 26-character Strict ULID standard.</span>
                            <span x-show="locale !== 'en'">Antigravity memeriksa spesifikasi Section 05 dan menulis migrasi tabel dengan standard Strict ULID 26-karakter.</span>
                        </p>
                    </div>
                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="text-emerald-600 dark:text-emerald-400 font-bold mb-1">
                            <span x-show="locale === 'en'">2. Eloquent Model HasUlids</span>
                            <span x-show="locale !== 'en'">2. Eloquent Model HasUlids</span>
                        </div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">
                            <span x-show="locale === 'en'">Models are crafted with <code>HasUlids</code> trait, <code>foreignUlid()</code> relationships, and SQL injection defenses.</span>
                            <span x-show="locale !== 'en'">Model dibuat dengan trait <code>HasUlids</code>, relasi <code>foreignUlid()</code>, dan perlindungan SQL injection.</span>
                        </p>
                    </div>
                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="text-emerald-600 dark:text-emerald-400 font-bold mb-1">
                            <span x-show="locale === 'en'">3. Verification &amp; Next Sprint</span>
                            <span x-show="locale !== 'en'">3. Verifikasi &amp; Lanjut Sprint</span>
                        </div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">
                            <span x-show="locale === 'en'">Once <code>php artisan migrate</code> succeeds, proceed directly to Sprint 2 (API/Service) and Sprint 3 (UI Islands).</span>
                            <span x-show="locale !== 'en'">Setelah <code>php artisan migrate</code> sukses, langsung perintahkan Sprint 2 (API/Service) dan Sprint 3 (UI Islands).</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- CARD CONTENT B: BIKIN PROYEK BARU BERSIH (STANDALONE APP) -->
            <div x-show="activeGuideCard === 'choice-b'" x-cloak class="p-5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-blue-500 text-white font-mono text-[10px] font-black uppercase">STANDALONE REPO</span>
                        <h3 class="font-mono font-black text-sm text-zinc-950 dark:text-zinc-100 uppercase">
                            <span x-show="locale === 'en'">📦 Option B: Build Fresh Clean Project from Scratch (Standalone App)</span>
                            <span x-show="locale !== 'en'">📦 Pilihan B: Bikin Proyek Baru Bersih dari Awal (Standalone App)</span>
                        </h3>
                    </div>
                    <span class="text-xs font-mono text-zinc-500 dark:text-zinc-400">
                        <span x-show="locale === 'en'">Clean &amp; Isolated Architecture</span>
                        <span x-show="locale !== 'en'">Arsitektur Bersih &amp; Terisolasi</span>
                    </span>
                </div>

                <p class="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed font-sans">
                    <span x-show="locale === 'en'">If you want this application built in a completely separate repository or folder, use the Software Factory OS 10 Pillars Starter Kit Scaffold.</span>
                    <span x-show="locale !== 'en'">Jika aplikasi ini ingin dibuat di repositori/folder terpisah yang benar-benar baru, gunakan paket Starter Kit Scaffold 10 Pilar Software Factory OS.</span>
                </p>

                <!-- Steps to execute Choice B -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 text-xs font-mono">
                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="font-black text-emerald-600 dark:text-emerald-400 mb-1">
                            <span x-show="locale === 'en'">STEP 1</span>
                            <span x-show="locale !== 'en'">LANGKAH 1</span>
                        </div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 mb-1">
                            <span x-show="locale === 'en'">Download Starter Kit ZIP</span>
                            <span x-show="locale !== 'en'">Unduh Starter Kit ZIP</span>
                        </div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans mb-3">
                            <span x-show="locale === 'en'">Click the green button below or in the toolbar above to download the 22-file architecture archive.</span>
                            <span x-show="locale !== 'en'">Klik tombol hijau di bawah atau di toolbar atas untuk mengunduh arsip 22 berkas arsitektur.</span>
                        </p>
                        <a href="{{ route('blueprint.export-scaffold', $blueprint->slug) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-500 hover:bg-emerald-400 text-black font-black text-[10px] uppercase rounded-none transition">
                            <span x-show="locale === 'en'">DOWNLOAD .ZIP NOW &darr;</span>
                            <span x-show="locale !== 'en'">UNDUH .ZIP SEKARANG &darr;</span>
                        </a>
                    </div>

                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="font-black text-emerald-600 dark:text-emerald-400 mb-1">
                            <span x-show="locale === 'en'">STEP 2</span>
                            <span x-show="locale !== 'en'">LANGKAH 2</span>
                        </div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 mb-1">
                            <span x-show="locale === 'en'">Open in Antigravity IDE</span>
                            <span x-show="locale !== 'en'">Buka di Antigravity IDE</span>
                        </div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">
                            <span x-show="locale === 'en'">Extract the ZIP into a new workspace (e.g. <code x-text="`c:\\projects\\${workspaceDir || '{{ $shortSlugVal }}'}`">c:\projects\{{ $shortSlugVal }}</code>). In Antigravity IDE, click <strong>File &rarr; Open Folder</strong>.</span>
                            <span x-show="locale !== 'en'">Ekstrak ZIP ke folder kerja baru (misal <code x-text="`c:\\projects\\${workspaceDir || '{{ $shortSlugVal }}'}`">c:\projects\{{ $shortSlugVal }}</code>). Di Antigravity IDE, klik <strong>File &rarr; Open Folder</strong>.</span>
                        </p>
                    </div>

                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="font-black text-emerald-600 dark:text-emerald-400 mb-1">
                            <span x-show="locale === 'en'">STEP 3</span>
                            <span x-show="locale !== 'en'">LANGKAH 3</span>
                        </div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 mb-1">
                            <span x-show="locale === 'en'">Context Pre-Loaded</span>
                            <span x-show="locale !== 'en'">Konteks Otomatis Aktif</span>
                        </div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">
                            <span x-show="locale === 'en'">Inside the ZIP, <code>.agents/AGENTS.md</code>, <code>docker-compose.yml</code>, <code>schema_complete.sql</code>, and <code>openapi.json</code> are pre-wired.</span>
                            <span x-show="locale !== 'en'">Di dalam ZIP sudah terpasang <code>.agents/AGENTS.md</code>, <code>docker-compose.yml</code>, <code>schema_complete.sql</code>, dan <code>openapi.json</code>.</span>
                        </p>
                    </div>

                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="font-black text-emerald-600 dark:text-emerald-400 mb-1">
                            <span x-show="locale === 'en'">STEP 4</span>
                            <span x-show="locale !== 'en'">LANGKAH 4</span>
                        </div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 mb-1">
                            <span x-show="locale === 'en'">First AI Prompt</span>
                            <span x-show="locale !== 'en'">Perintah Pertama AI</span>
                        </div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">
                            <span x-show="locale === 'en'">Open Antigravity chat in the new workspace, then paste the pre-configured prompt.</span>
                            <span x-show="locale !== 'en'">Buka chat Antigravity di workspace baru, lalu tempel prompt siap pakai.</span>
                        </p>
                    </div>
                </div>

                <!-- First Prompt for Standalone Project -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs font-mono">
                        <span class="font-bold text-zinc-900 dark:text-zinc-200 uppercase">
                            <span x-show="locale === 'en'">First Prompt in New Standalone Workspace:</span>
                            <span x-show="locale !== 'en'">Prompt Perdana di Workspace Standalone Baru:</span>
                        </span>
                        <button 
                            type="button" 
                            @click="copyExecutionPrompt(locale === 'en' ? `Antigravity, inspect AGENTS.md, PRODUCT-BRIEF.md, and schema_complete.sql in this new workspace. Spin up containers with docker compose up -d, run PostgreSQL Strict ULID database migrations, and begin MVP Phase 1 implementation.` : `Antigravity, pelajari AGENTS.md, PRODUCT-BRIEF.md, dan schema_complete.sql di workspace baru ini. Nyalakan container dengan docker compose up -d, jalankan migrasi database PostgreSQL Strict ULID, dan mulai implementasi MVP Fase 1.`, locale === 'en' ? 'STANDALONE PROMPT COPIED' : 'PROMPT STANDALONE DISALIN')"
                            class="px-2.5 py-1 bg-emerald-500 hover:bg-emerald-400 text-black font-black text-[11px] rounded-none cursor-pointer transition flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            <span x-show="locale === 'en'">COPY STANDALONE PROMPT</span>
                            <span x-show="locale !== 'en'">SALIN PROMPT STANDALONE</span>
                        </button>
                    </div>

                    <div class="p-3 bg-zinc-950 border border-zinc-800 font-mono text-xs text-emerald-400 select-all leading-relaxed whitespace-pre-wrap rounded-none">
                        <span x-show="locale === 'en'">Antigravity, inspect AGENTS.md, PRODUCT-BRIEF.md, and schema_complete.sql in this new workspace. Spin up containers with docker compose up -d, run PostgreSQL Strict ULID database migrations, and begin MVP Phase 1 implementation.</span>
                        <span x-show="locale !== 'en'">Antigravity, pelajari AGENTS.md, PRODUCT-BRIEF.md, dan schema_complete.sql di workspace baru ini. Nyalakan container dengan docker compose up -d, jalankan migrasi database PostgreSQL Strict ULID, dan mulai implementasi MVP Fase 1.</span>
                    </div>
                </div>
            </div>

            <!-- CARD CONTENT C: EKSEKUSI BERTAHAP MENGGUNAKAN AI COCKPIT -->
            <div x-show="activeGuideCard === 'choice-c'" x-cloak class="p-5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-purple-500 text-white font-mono text-[10px] font-black uppercase">ANTI CONTEXT-ROT</span>
                        <h3 class="font-mono font-black text-sm text-zinc-950 dark:text-zinc-100 uppercase">
                            <span x-show="locale === 'en'">🎯 Option C: Step-by-Step Execution via AI Cockpit (Vertical Slice Prompting)</span>
                            <span x-show="locale !== 'en'">🎯 Pilihan C: Eksekusi Bertahap Menggunakan AI Cockpit (Vertical Slice Prompting)</span>
                        </h3>
                    </div>
                    <span class="text-xs font-mono text-zinc-500 dark:text-zinc-400">
                        <span x-show="locale === 'en'">Zero LLM Hallucinations</span>
                        <span x-show="locale !== 'en'">Zero Halusinasi LLM</span>
                    </span>
                </div>

                <p class="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed font-sans">
                    <span x-show="locale === 'en'">If you prefer copying precision prompts stage-by-stage so the AI doesn't experience <em>context-rot</em> (forgetting context or cutting code logic), use the AI Ingestion Cockpit.</span>
                    <span x-show="locale !== 'en'">Jika Anda ingin menyalin prompt yang sudah dipersiapkan secara presisi per tahapan agar AI tidak mengalami <em>context-rot</em> (lupa konteks atau menghasilkan kode terpotong), gunakan AI Ingestion Cockpit.</span>
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs font-mono">
                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 mb-1">
                            <span x-show="locale === 'en'">1. Open AI Cockpit</span>
                            <span x-show="locale !== 'en'">1. Buka AI Cockpit</span>
                        </div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans mb-3">
                            <span x-show="locale === 'en'">In the top toolbar click <code>PROMPT AGENT</code> or click the button below:</span>
                            <span x-show="locale !== 'en'">Di toolbar atas klik tombol <code>PROMPT AGENT</code> atau klik tombol di bawah ini:</span>
                        </p>
                        <button type="button" @click="showAiPromptModal = true" class="px-2.5 py-1 bg-purple-600 hover:bg-purple-500 text-white font-black text-[10px] uppercase rounded-none transition">
                            <span x-show="locale === 'en'">OPEN COCKPIT MODAL &rarr;</span>
                            <span x-show="locale !== 'en'">BUKA MODAL COCKPIT &rarr;</span>
                        </button>
                    </div>

                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 mb-1">
                            <span x-show="locale === 'en'">2. Select Target Agent</span>
                            <span x-show="locale !== 'en'">2. Pilih Target Agent</span>
                        </div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">
                            <span x-show="locale === 'en'">Select your agent: <strong>Antigravity IDE (Google DeepMind)</strong>, Cursor Composer, Claude Code CLI, or Windsurf Cascade.</span>
                            <span x-show="locale !== 'en'">Pilih agent Anda: <strong>Antigravity IDE (Google DeepMind)</strong>, Cursor Composer, Claude Code CLI, atau Windsurf Cascade.</span>
                        </p>
                    </div>

                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 mb-1">
                            <span x-show="locale === 'en'">3. Copy Prompt per Sprint</span>
                            <span x-show="locale !== 'en'">3. Salin Prompt per Sprint</span>
                        </div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">
                            <span x-show="locale === 'en'">Starting from Sprint 1 (DB &amp; ULID), paste into AI chat, and execute sequentially up to Sprint 5.</span>
                            <span x-show="locale !== 'en'">Mulai dari Sprint 1 (DB &amp; ULID), tempel ke chat AI, dan eksekusi secara berurutan hingga Sprint 5.</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================================= -->
        <!-- URUTAN EKSEKUSI STANDAR (BEST PRACTICE VERTICAL SLICE TABLE)                              -->
        <!-- ========================================================================================= -->
        <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-800 space-y-3 font-mono">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-none animate-pulse"></span>
                    <h3 class="text-sm font-black uppercase text-zinc-950 dark:text-zinc-100 tracking-wider">
                        <span x-show="locale === 'en'">STANDARD EXECUTION SEQUENCE // BEST PRACTICE VERTICAL SLICE</span>
                        <span x-show="locale !== 'en'">URUTAN EKSEKUSI STANDAR // BEST PRACTICE VERTICAL SLICE</span>
                    </h3>
                </div>
                <span class="text-xs text-zinc-500 dark:text-zinc-400 font-bold uppercase">5 Sprint Turnkey Roadmap</span>
            </div>

            <p class="text-xs text-zinc-600 dark:text-zinc-400 font-sans">
                <span x-show="locale === 'en'">To ensure smooth project delivery without errors or truncated code, follow this sprint sequence strictly:</span>
                <span x-show="locale !== 'en'">Agar pengerjaan proyek berjalan lancar tanpa error atau kode terpotong, ikuti urutan sprint berikut secara disiplin:</span>
            </p>

            <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-800">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 border-b border-zinc-200 dark:border-zinc-800 font-black">
                        <tr>
                            <th class="p-2.5 w-24 border-r border-zinc-200 dark:border-zinc-800">
                                <span x-show="locale === 'en'">PHASE</span>
                                <span x-show="locale !== 'en'">TAHAPAN</span>
                            </th>
                            <th class="p-2.5 border-r border-zinc-200 dark:border-zinc-800">
                                <span x-show="locale === 'en'">WORK FOCUS &amp; ARCHITECTURE</span>
                                <span x-show="locale !== 'en'">FOKUS PEKERJAAN &amp; ARSITEKTUR</span>
                            </th>
                            <th class="p-2.5 w-56 border-r border-zinc-200 dark:border-zinc-800">
                                <span x-show="locale === 'en'">QUALITY VERIFICATION</span>
                                <span x-show="locale !== 'en'">VERIFIKASI KUALITAS</span>
                            </th>
                            <th class="p-2.5 w-32">
                                <span x-show="locale === 'en'">GUARDRAIL</span>
                                <span x-show="locale !== 'en'">GUARDRAIL</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 bg-white dark:bg-zinc-950 font-mono text-[11px]">
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="p-2.5 font-black text-emerald-600 dark:text-emerald-400 border-r border-zinc-200 dark:border-zinc-800">Sprint 1</td>
                            <td class="p-2.5 text-zinc-800 dark:text-zinc-200 border-r border-zinc-200 dark:border-zinc-800 font-sans">
                                <span x-show="locale === 'en'"><strong>Database &amp; Model:</strong> Table migrations with <code>->ulid('id')->primary()</code>, relational foreign keys <code>foreignUlid()</code>, Eloquent Models with <code>HasUlids</code> trait.</span>
                                <span x-show="locale !== 'en'"><strong>Database &amp; Model:</strong> Migration tabel dengan <code>->ulid('id')->primary()</code>, foreign keys relational <code>foreignUlid()</code>, Model Eloquent dengan trait <code>HasUlids</code>.</span>
                            </td>
                            <td class="p-2.5 border-r border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300">
                                <code>php artisan migrate</code>
                            </td>
                            <td class="p-2.5 text-emerald-600 dark:text-emerald-400 font-bold">Strict ULID</td>
                        </tr>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="p-2.5 font-black text-emerald-600 dark:text-emerald-400 border-r border-zinc-200 dark:border-zinc-800">Sprint 2</td>
                            <td class="p-2.5 text-zinc-800 dark:text-zinc-200 border-r border-zinc-200 dark:border-zinc-800 font-sans">
                                <span x-show="locale === 'en'"><strong>Backend Service &amp; API:</strong> Business service classes, strict FormRequest validation, REST API endpoints with Keyset Cursor Pagination O(1) (<code>cursorPaginate()</code>).</span>
                                <span x-show="locale !== 'en'"><strong>Backend Service &amp; API:</strong> Service class bisnis, FormRequest validasi ketat, endpoint REST API dengan Keyset Cursor Pagination O(1) (<code>cursorPaginate()</code>).</span>
                            </td>
                            <td class="p-2.5 border-r border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300">
                                <code>php artisan test</code> (Pest Contract)
                            </td>
                            <td class="p-2.5 text-emerald-600 dark:text-emerald-400 font-bold">Keyset O(1)</td>
                        </tr>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="p-2.5 font-black text-emerald-600 dark:text-emerald-400 border-r border-zinc-200 dark:border-zinc-800">Sprint 3</td>
                            <td class="p-2.5 text-zinc-800 dark:text-zinc-200 border-r border-zinc-200 dark:border-zinc-800 font-sans">
                                <span x-show="locale === 'en'"><strong>Frontend UI / Islands:</strong> Clean Solid Brutalist interface (subtle corners <code>rounded-none/rounded-sm</code>, no capsule <code>rounded-full</code>), dark/light mode fidelity, centralized toast notifications (no native alerts).</span>
                                <span x-show="locale !== 'en'"><strong>Frontend UI / Islands:</strong> Tampilan Clean Solid Brutalist (subtle corner <code>rounded-none/rounded-sm</code>, tanpa capsule <code>rounded-full</code>), dark/light mode fidelity, centralized toast notification (tanpa native alert).</span>
                            </td>
                            <td class="p-2.5 border-r border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300">
                                <span x-show="locale === 'en'">Visual browser interaction testing</span>
                                <span x-show="locale !== 'en'">Uji interaksi visual browser</span>
                            </td>
                            <td class="p-2.5 text-emerald-600 dark:text-emerald-400 font-bold">Anti-AI-Slop</td>
                        </tr>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="p-2.5 font-black text-emerald-600 dark:text-emerald-400 border-r border-zinc-200 dark:border-zinc-800">Sprint 4</td>
                            <td class="p-2.5 text-zinc-800 dark:text-zinc-200 border-r border-zinc-200 dark:border-zinc-800 font-sans">
                                <span x-show="locale === 'en'"><strong>Security &amp; Guardrails:</strong> Anti-bot honeypots, rate limiting, standard RFC 9457 Problem Details error handling, and tamper-evident SHA-256 audit logging.</span>
                                <span x-show="locale !== 'en'"><strong>Security &amp; Guardrails:</strong> Honeypot anti-bot, rate limiting, penanganan error standar RFC 9457 Problem Details, dan tamper-evident SHA-256 audit logging.</span>
                            </td>
                            <td class="p-2.5 border-r border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300">
                                <span x-show="locale === 'en'">Security audit &amp; regression tests</span>
                                <span x-show="locale !== 'en'">Security audit &amp; regression test</span>
                            </td>
                            <td class="p-2.5 text-emerald-600 dark:text-emerald-400 font-bold">UU PDP / OWASP</td>
                        </tr>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="p-2.5 font-black text-emerald-600 dark:text-emerald-400 border-r border-zinc-200 dark:border-zinc-800">Sprint 5</td>
                            <td class="p-2.5 text-zinc-800 dark:text-zinc-200 border-r border-zinc-200 dark:border-zinc-800 font-sans">
                                <span x-show="locale === 'en'"><strong>Build &amp; Deploy:</strong> <code>npm run build</code>, verify health check probes <code>/api/health</code>, and run deployment script <code>./deploy.sh 1</code> or <code>./deploy.sh 2</code>.</span>
                                <span x-show="locale !== 'en'"><strong>Build &amp; Deploy:</strong> <code>npm run build</code>, verifikasi health check probes <code>/api/health</code>, dan jalankan skrip deployment <code>./deploy.sh 1</code> atau <code>./deploy.sh 2</code>.</span>
                            </td>
                            <td class="p-2.5 border-r border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300">
                                <code>./deploy.sh [1-6]</code>
                            </td>
                            <td class="p-2.5 text-emerald-600 dark:text-emerald-400 font-bold">Zero-Downtime</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================================================================= -->
        <!-- DOCKER QUICKSTART & MULTI-AGENT SETUP                                                     -->
        <!-- ========================================================================================= -->
        <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-800 grid grid-cols-1 lg:grid-cols-2 gap-4 font-mono text-xs">
            <!-- Left: Docker Stack Commands (with Windows & Linux switcher) -->
            <div x-data="{ secDockerOs: 'windows' }" class="p-4 bg-zinc-900 dark:bg-zinc-950 border border-zinc-800 text-zinc-200 space-y-3">
                <div class="flex flex-wrap items-center justify-between border-b border-zinc-800 pb-2 gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-sky-400 rounded-none"></span>
                        <span class="font-black text-white uppercase text-xs">🐳 DOCKER STACK QUICKSTART</span>
                    </div>
                    <div class="flex items-center gap-1 text-[10px]">
                        <button 
                            type="button" 
                            @click="secDockerOs = 'windows'"
                            :class="secDockerOs === 'windows' ? 'bg-sky-500 text-black font-black' : 'bg-zinc-800 text-zinc-300 hover:text-white'"
                            class="px-2 py-0.5 uppercase transition cursor-pointer"
                        >
                            🪟 Windows
                        </button>
                        <button 
                            type="button" 
                            @click="secDockerOs = 'linux'"
                            :class="secDockerOs === 'linux' ? 'bg-sky-500 text-black font-black' : 'bg-zinc-800 text-zinc-300 hover:text-white'"
                            class="px-2 py-0.5 uppercase transition cursor-pointer"
                        >
                            🐧 Linux/VPS
                        </button>
                    </div>
                </div>

                <!-- Windows View -->
                <div x-show="secDockerOs === 'windows'" class="space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] text-zinc-400 font-sans">
                            <span x-show="locale === 'en'">Docker Desktop Windows (WSL 2) commands for PowerShell:</span>
                            <span x-show="locale !== 'en'">Perintah PowerShell untuk Docker Desktop Windows (WSL 2):</span>
                        </p>
                        <button 
                            type="button" 
                            @click="copyExecutionPrompt(`docker compose up -d\ndocker compose exec app composer install\ndocker compose exec app php artisan key:generate\ndocker compose exec app php artisan migrate\ndocker compose exec app php artisan db:seed --class=SyntheticDataSeeder\ndocker compose exec app npm install && docker compose exec app npm run build\ncurl http://localhost:8000/api/health`, locale === 'en' ? 'POWERSHELL COMMANDS COPIED' : 'PERINTAH POWERSHELL DISALIN')"
                            class="px-2 py-0.5 bg-sky-500 hover:bg-sky-400 text-black font-black text-[10px] rounded-none cursor-pointer"
                        >
                            <span x-show="locale === 'en'">COPY POWERSHELL</span>
                            <span x-show="locale !== 'en'">SALIN POWERSHELL</span>
                        </button>
                    </div>
                    <pre class="p-3 bg-black border border-zinc-800 text-[11px] text-sky-300 overflow-x-auto select-all leading-relaxed whitespace-pre font-mono"># 1. Jalankan container di latar belakang
docker compose up -d

# 2. Install dependencies & migrate PostgreSQL Strict ULID
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed --class=SyntheticDataSeeder

# 3. Build frontend assets & tes health probe
docker compose exec app npm install && docker compose exec app npm run build
curl http://localhost:8000/api/health</pre>
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-[10px] text-zinc-400">&bull; Local: <code class="text-sky-300">http://localhost:8000</code></span>
                        <button 
                            type="button" 
                            @click="executionGuideTab = 'docker'; showExecutionGuideModal = true"
                            class="text-[10px] text-sky-400 hover:underline font-bold uppercase cursor-pointer"
                        >
                            <span x-show="locale === 'en'">FULL WINDOWS SETUP GUIDE &rarr;</span>
                            <span x-show="locale !== 'en'">PANDUAN LENGKAP WINDOWS &rarr;</span>
                        </button>
                    </div>
                </div>

                <!-- Linux/VPS View -->
                <div x-show="secDockerOs === 'linux'" class="space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] text-zinc-400 font-sans">
                            <span x-show="locale === 'en'">Bash terminal commands for Linux, macOS &amp; Cloud VPS:</span>
                            <span x-show="locale !== 'en'">Perintah terminal Bash untuk Linux, macOS &amp; Cloud VPS:</span>
                        </p>
                        <button 
                            type="button" 
                            @click="copyExecutionPrompt(`cp .env.blueprint .env\ndocker compose up -d\ndocker compose exec app composer install\ndocker compose exec app php artisan key:generate\ndocker compose exec app php artisan migrate\ndocker compose exec app php artisan db:seed --class=SyntheticDataSeeder\ndocker compose exec app npm install && docker compose exec app npm run build\ncurl http://localhost:8000/api/health`, locale === 'en' ? 'BASH COMMANDS COPIED' : 'PERINTAH BASH DISALIN')"
                            class="px-2 py-0.5 bg-sky-500 hover:bg-sky-400 text-black font-black text-[10px] rounded-none cursor-pointer"
                        >
                            <span x-show="locale === 'en'">COPY BASH</span>
                            <span x-show="locale !== 'en'">SALIN BASH</span>
                        </button>
                    </div>
                    <pre class="p-3 bg-black border border-zinc-800 text-[11px] text-sky-300 overflow-x-auto select-all leading-relaxed whitespace-pre font-mono"># 1. Setup env & start containers
cp .env.blueprint .env
docker compose up -d

# 2. Dependencies, key & migrations
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed --class=SyntheticDataSeeder

# 3. Build frontend & health probe
docker compose exec app npm install && docker compose exec app npm run build
curl http://localhost:8000/api/health</pre>
                </div>
            </div>

            <!-- Right: ECC Harness (.agents/) & Multi-AI IDE Guide -->
            <div class="p-4 bg-zinc-900 dark:bg-zinc-950 border border-zinc-800 text-zinc-200 space-y-3">
                <div class="flex items-center justify-between border-b border-zinc-800 pb-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-emerald-400 rounded-none"></span>
                        <span class="font-black text-white uppercase text-xs">🧠 ECC HARNESS (.agents/) &amp; AI IDE</span>
                    </div>
                    <span class="text-[10px] text-emerald-400 font-bold uppercase">
                        <span x-show="locale === 'en'">ZERO DEVIATION</span>
                        <span x-show="locale !== 'en'">ZERO DEVIASI</span>
                    </span>
                </div>
                <p class="text-[11px] text-zinc-400 font-sans">
                    <span x-show="locale === 'en'">Inside the <code>.agents/</code> folder of this project, <strong>ECC (Everything Claude Code / Antigravity Agent Harness)</strong> is fully installed:</span>
                    <span x-show="locale !== 'en'">Di dalam folder <code>.agents/</code> proyek ini sudah terpasang <strong>ECC (Everything Claude Code / Antigravity Agent Harness)</strong>:</span>
                </p>
                <ul class="text-[11px] space-y-1.5 text-zinc-300 font-mono">
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-400 font-black">&bull;</span>
                        <span>
                            <strong>AGENTS.md &amp; .cursorrules:</strong> 
                            <span x-show="locale === 'en'">Strict architectural SOP, ban on native dialogs, O(1) Keyset standard.</span>
                            <span x-show="locale !== 'en'">SOP ketat arsitektur, larangan dialog native, standard O(1) Keyset.</span>
                        </span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-400 font-black">&bull;</span>
                        <span>
                            <strong>PONYTAIL.md:</strong> 
                            <span x-show="locale === 'en'">4-rung decision ladder (Framework Native &rarr; Reuse &rarr; Config &rarr; Surgical Diff).</span>
                            <span x-show="locale !== 'en'">4 anak tangga keputusan (Framework Native &rarr; Reuse &rarr; Config &rarr; Surgical Diff).</span>
                        </span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-400 font-black">&bull;</span>
                        <span>
                            <strong>GRAPH_REPORT.md:</strong> 
                            <span x-show="locale === 'en'">Architectural knowledge graph of 19 models &amp; dozens of controllers without blind scanning.</span>
                            <span x-show="locale !== 'en'">Knowledge graph arsitektur 19 model &amp; puluhan controller tanpa blind scan.</span>
                        </span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-400 font-black">&bull;</span>
                        <span>
                            <strong>Skills Library:</strong> <code>code-reviewer</code>, <code>security-reviewer</code>, <code>verification-loop</code>, <code>browser-qa</code>.
                        </span>
                    </li>
                </ul>
                <div class="p-2 bg-zinc-900 border border-zinc-800 text-[10px] text-zinc-400">
                    <span x-show="locale === 'en'">💡 <em>AI Agents (Antigravity, Cursor, Claude Code, Windsurf) automatically read these guardrails when executing every prompt.</em></span>
                    <span x-show="locale !== 'en'">💡 <em>AI Agent (Antigravity, Cursor, Claude Code, Windsurf) otomatis membaca guardrails ini saat mengeksekusi setiap prompt.</em></span>
                </div>
            </div>
        </div>
    </div>
</section>
