@php
    $bizTitle = $blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Aplikasi Proyek');
    $slugVal = $blueprint->slug ?: 'app';
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
                        WORLD-CLASS PETA JALAN &amp; GUARDRAILS
                    </span>
                    <span class="px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 rounded-none">
                        DOCKER PHP 8.5+ &bull; PG 16 ULID &bull; ECC HARNESS
                    </span>
                </div>
                <h2 class="text-xl sm:text-2xl lg:text-3xl font-black uppercase text-zinc-950 dark:text-zinc-50 tracking-tight flex items-center gap-2">
                    <span>🚀 PANDUAN EKSEKUSI &amp; 3 CARA MULAI PROYEK</span>
                </h2>
                <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-mono mt-1">
                    Blueprint ini dirancang bukan sekadar dokumen bacaan, melainkan <strong class="text-zinc-900 dark:text-zinc-200">Executable Architecture Contract</strong> siap koding. Pilih salah satu dari 3 cara berikut untuk mulai mengeksekusi proyek Anda.
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
                    <span>BUKA POPUP PANDUAN PENUH</span>
                </button>
                <button 
                    type="button" 
                    @click="openScaffoldModal()" 
                    class="px-3 py-2 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-white font-black uppercase text-xs rounded-none border border-zinc-900 dark:border-zinc-100 transition cursor-pointer flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>SCAFFOLD .ZIP (22 FILE)</span>
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
                        <div class="uppercase tracking-wider font-black text-xs">PILIHAN A: Sesi Chat Antigravity</div>
                        <div class="text-[11px] opacity-80 mt-0.5 font-normal">Paling Cepat &bull; Langsung Koding di Sesi Ini</div>
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
                        <div class="uppercase tracking-wider font-black text-xs">PILIHAN B: Standalone Proyek Baru</div>
                        <div class="text-[11px] opacity-80 mt-0.5 font-normal">Unduh Starter Kit ZIP &bull; Docker Container</div>
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
                        <div class="uppercase tracking-wider font-black text-xs">PILIHAN C: AI Cockpit Bertahap</div>
                        <div class="text-[11px] opacity-80 mt-0.5 font-normal">Vertical Slice &bull; Anti Context-Rot (0/11)</div>
                    </div>
                </button>
            </div>

            <!-- CARD CONTENT A: EKSEKUSI LANGSUNG DI SESI CHAT ANTIGRAVITY -->
            <div x-show="activeGuideCard === 'choice-a'" x-cloak class="p-5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-emerald-500 text-black font-mono text-[10px] font-black uppercase">REKOMENDASI LOKAL</span>
                        <h3 class="font-mono font-black text-sm text-zinc-950 dark:text-zinc-100 uppercase">
                            🚀 Pilihan A: Eksekusi Langsung di Sesi Chat Antigravity Ini (Paling Cepat &amp; Praktis)
                        </h3>
                    </div>
                    <span class="text-xs font-mono text-zinc-500 dark:text-zinc-400">Zero Setup Required</span>
                </div>

                <p class="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed font-sans">
                    Karena Anda saat ini <strong>sudah berada di dalam Antigravity IDE</strong> dan agen ini memiliki akses langsung ke filesystem codebase, terminal PowerShell, serta database, Anda dapat langsung menginstruksikan agen ini di kotak chat.
                </p>

                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs font-mono">
                        <span class="font-bold text-zinc-900 dark:text-zinc-200 uppercase">Salin Perintah Sprint 1 (Fondasi Database &amp; Model):</span>
                        <button 
                            type="button" 
                            @click="copyExecutionPrompt(`Antigravity, tolong baca Blueprint '${bizTitle}'. Mulai eksekusi Sprint 1: Buat migration PostgreSQL Strict ULID dan Model Eloquent untuk modul ${firstModule} (${firstTables}) sesuai spesifikasi Section 05. Pastikan menggunakan ->ulid('id')->primary() dan trait HasUlids, lalu jalankan php artisan migrate.`, 'PROMPT SPRINT 1 DISALIN')"
                            class="px-2.5 py-1 bg-emerald-500 hover:bg-emerald-400 text-black font-black text-[11px] rounded-none cursor-pointer transition flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            <span>SALIN PROMPT SPRINT 1</span>
                        </button>
                    </div>

                    <div class="p-3 bg-zinc-950 border border-zinc-800 font-mono text-xs text-emerald-400 select-all leading-relaxed whitespace-pre-wrap rounded-none">Antigravity, tolong baca Blueprint '{{ $bizTitle }}'. Mulai eksekusi Sprint 1: Buat migration PostgreSQL Strict ULID dan Model Eloquent untuk modul {{ $firstModule }} ({{ $firstTables }}) sesuai spesifikasi Section 05. Pastikan menggunakan ->ulid('id')->primary() dan trait HasUlids, lalu jalankan php artisan migrate.</div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs font-mono pt-2">
                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="text-emerald-600 dark:text-emerald-400 font-bold mb-1">1. Otomasi DDL &amp; ERD</div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">Antigravity memeriksa spesifikasi Section 05 dan menulis migrasi tabel dengan standard Strict ULID 26-karakter.</p>
                    </div>
                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="text-emerald-600 dark:text-emerald-400 font-bold mb-1">2. Eloquent Model HasUlids</div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">Model dibuat dengan trait <code>HasUlids</code>, relasi <code>foreignUlid()</code>, dan perlindungan SQL injection.</p>
                    </div>
                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="text-emerald-600 dark:text-emerald-400 font-bold mb-1">3. Verifikasi &amp; Lanjut Sprint</div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">Setelah <code>php artisan migrate</code> sukses, langsung perintahkan Sprint 2 (API/Service) dan Sprint 3 (UI Islands).</p>
                    </div>
                </div>
            </div>

            <!-- CARD CONTENT B: BIKIN PROYEK BARU BERSIH (STANDALONE APP) -->
            <div x-show="activeGuideCard === 'choice-b'" x-cloak class="p-5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-blue-500 text-white font-mono text-[10px] font-black uppercase">STANDALONE REPO</span>
                        <h3 class="font-mono font-black text-sm text-zinc-950 dark:text-zinc-100 uppercase">
                            📦 Pilihan B: Bikin Proyek Baru Bersih dari Awal (Standalone App)
                        </h3>
                    </div>
                    <span class="text-xs font-mono text-zinc-500 dark:text-zinc-400">Arsitektur Bersih &amp; Terisolasi</span>
                </div>

                <p class="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed font-sans">
                    Jika aplikasi ini ingin dibuat di repositori/folder terpisah yang benar-benar baru, gunakan paket Starter Kit Scaffold 10 Pilar Software Factory OS.
                </p>

                <!-- Steps to execute Choice B -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 text-xs font-mono">
                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="font-black text-emerald-600 dark:text-emerald-400 mb-1">LANGKAH 1</div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 mb-1">Unduh Starter Kit ZIP</div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans mb-3">Klik tombol hijau di bawah atau di toolbar atas untuk mengunduh arsip 22 berkas arsitektur.</p>
                        <a href="{{ route('blueprint.export-scaffold', $blueprint->slug) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-500 hover:bg-emerald-400 text-black font-black text-[10px] uppercase rounded-none transition">
                            UNDUH .ZIP SEKARANG &darr;
                        </a>
                    </div>

                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="font-black text-emerald-600 dark:text-emerald-400 mb-1">LANGKAH 2</div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 mb-1">Buka di Antigravity IDE</div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">Ekstrak ZIP ke folder kerja baru (misal <code>c:\projects\{{ $slugVal }}</code>). Di Antigravity IDE, klik <strong>File &rarr; Open Folder</strong>.</p>
                    </div>

                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="font-black text-emerald-600 dark:text-emerald-400 mb-1">LANGKAH 3</div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 mb-1">Konteks Otomatis Aktif</div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">Di dalam ZIP sudah terpasang <code>.agents/AGENTS.md</code>, <code>docker-compose.yml</code>, <code>schema_complete.sql</code>, dan <code>openapi.json</code>.</p>
                    </div>

                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="font-black text-emerald-600 dark:text-emerald-400 mb-1">LANGKAH 4</div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 mb-1">Perintah Pertama AI</div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">Buka chat Antigravity di workspace baru, lalu tempel prompt siap pakai.</p>
                    </div>
                </div>

                <!-- First Prompt for Standalone Project -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs font-mono">
                        <span class="font-bold text-zinc-900 dark:text-zinc-200 uppercase">Prompt Perdana di Workspace Standalone Baru:</span>
                        <button 
                            type="button" 
                            @click="copyExecutionPrompt(`Antigravity, pelajari AGENTS.md, PRODUCT-BRIEF.md, dan schema_complete.sql di workspace baru ini. Nyalakan container dengan docker compose up -d, jalankan migrasi database PostgreSQL Strict ULID, dan mulai implementasi MVP Fase 1.`, 'PROMPT STANDALONE DISALIN')"
                            class="px-2.5 py-1 bg-emerald-500 hover:bg-emerald-400 text-black font-black text-[11px] rounded-none cursor-pointer transition flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            <span>SALIN PROMPT STANDALONE</span>
                        </button>
                    </div>

                    <div class="p-3 bg-zinc-950 border border-zinc-800 font-mono text-xs text-emerald-400 select-all leading-relaxed whitespace-pre-wrap rounded-none">Antigravity, pelajari AGENTS.md, PRODUCT-BRIEF.md, dan schema_complete.sql di workspace baru ini. Nyalakan container dengan docker compose up -d, jalankan migrasi database PostgreSQL Strict ULID, dan mulai implementasi MVP Fase 1.</div>
                </div>
            </div>

            <!-- CARD CONTENT C: EKSEKUSI BERTAHAP MENGGUNAKAN AI COCKPIT -->
            <div x-show="activeGuideCard === 'choice-c'" x-cloak class="p-5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-none space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-purple-500 text-white font-mono text-[10px] font-black uppercase">ANTI CONTEXT-ROT</span>
                        <h3 class="font-mono font-black text-sm text-zinc-950 dark:text-zinc-100 uppercase">
                            🎯 Pilihan C: Eksekusi Bertahap Menggunakan AI Cockpit (Vertical Slice Prompting)
                        </h3>
                    </div>
                    <span class="text-xs font-mono text-zinc-500 dark:text-zinc-400">Zero Halusinasi LLM</span>
                </div>

                <p class="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed font-sans">
                    Jika Anda ingin menyalin prompt yang sudah dipersiapkan secara presisi per tahapan agar AI tidak mengalami <em>context-rot</em> (lupa konteks atau menghasilkan kode terpotong), gunakan AI Ingestion Cockpit.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs font-mono">
                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 mb-1">1. Buka AI Cockpit</div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans mb-3">Di toolbar atas klik tombol <code>PROMPT AGENT</code> atau klik tombol di bawah ini:</p>
                        <button type="button" @click="showAiPromptModal = true" class="px-2.5 py-1 bg-purple-600 hover:bg-purple-500 text-white font-black text-[10px] uppercase rounded-none transition">
                            BUKA MODAL COCKPIT &rarr;
                        </button>
                    </div>

                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 mb-1">2. Pilih Target Agent</div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">Pilih agent Anda: <strong>Antigravity IDE (Google DeepMind)</strong>, Cursor Composer, Claude Code CLI, atau Windsurf Cascade.</p>
                    </div>

                    <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 mb-1">3. Salin Prompt per Sprint</div>
                        <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">Mulai dari Sprint 1 (DB &amp; ULID), tempel ke chat AI, dan eksekusi secara berurutan hingga Sprint 5.</p>
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
                        URUTAN EKSEKUSI STANDAR // BEST PRACTICE VERTICAL SLICE
                    </h3>
                </div>
                <span class="text-xs text-zinc-500 dark:text-zinc-400 font-bold uppercase">5 Sprint Turnkey Roadmap</span>
            </div>

            <p class="text-xs text-zinc-600 dark:text-zinc-400 font-sans">
                Agar pengerjaan proyek berjalan lancar tanpa error atau kode terpotong, ikuti urutan sprint berikut secara disiplin:
            </p>

            <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-800">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 border-b border-zinc-200 dark:border-zinc-800 font-black">
                        <tr>
                            <th class="p-2.5 w-24 border-r border-zinc-200 dark:border-zinc-800">TAHAPAN</th>
                            <th class="p-2.5 border-r border-zinc-200 dark:border-zinc-800">FOKUS PEKERJAAN &amp; ARSITEKTUR</th>
                            <th class="p-2.5 w-56 border-r border-zinc-200 dark:border-zinc-800">VERIFIKASI KUALITAS</th>
                            <th class="p-2.5 w-32">GUARDRAIL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 bg-white dark:bg-zinc-950 font-mono text-[11px]">
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="p-2.5 font-black text-emerald-600 dark:text-emerald-400 border-r border-zinc-200 dark:border-zinc-800">Sprint 1</td>
                            <td class="p-2.5 text-zinc-800 dark:text-zinc-200 border-r border-zinc-200 dark:border-zinc-800 font-sans">
                                <strong>Database &amp; Model:</strong> Migration tabel dengan <code>->ulid('id')->primary()</code>, foreign keys relational <code>foreignUlid()</code>, Model Eloquent dengan trait <code>HasUlids</code>.
                            </td>
                            <td class="p-2.5 border-r border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300">
                                <code>php artisan migrate</code>
                            </td>
                            <td class="p-2.5 text-emerald-600 dark:text-emerald-400 font-bold">Strict ULID</td>
                        </tr>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="p-2.5 font-black text-emerald-600 dark:text-emerald-400 border-r border-zinc-200 dark:border-zinc-800">Sprint 2</td>
                            <td class="p-2.5 text-zinc-800 dark:text-zinc-200 border-r border-zinc-200 dark:border-zinc-800 font-sans">
                                <strong>Backend Service &amp; API:</strong> Service class bisnis, FormRequest validasi ketat, endpoint REST API dengan Keyset Cursor Pagination O(1) (<code>cursorPaginate()</code>).
                            </td>
                            <td class="p-2.5 border-r border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300">
                                <code>php artisan test</code> (Pest Contract)
                            </td>
                            <td class="p-2.5 text-emerald-600 dark:text-emerald-400 font-bold">Keyset O(1)</td>
                        </tr>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="p-2.5 font-black text-emerald-600 dark:text-emerald-400 border-r border-zinc-200 dark:border-zinc-800">Sprint 3</td>
                            <td class="p-2.5 text-zinc-800 dark:text-zinc-200 border-r border-zinc-200 dark:border-zinc-800 font-sans">
                                <strong>Frontend UI / Islands:</strong> Tampilan Clean Solid Brutalist (subtle corner <code>rounded-none/rounded-sm</code>, tanpa capsule <code>rounded-full</code>), dark/light mode fidelity, centralized toast notification (tanpa native alert).
                            </td>
                            <td class="p-2.5 border-r border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300">
                                Uji interaksi visual browser
                            </td>
                            <td class="p-2.5 text-emerald-600 dark:text-emerald-400 font-bold">Anti-AI-Slop</td>
                        </tr>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="p-2.5 font-black text-emerald-600 dark:text-emerald-400 border-r border-zinc-200 dark:border-zinc-800">Sprint 4</td>
                            <td class="p-2.5 text-zinc-800 dark:text-zinc-200 border-r border-zinc-200 dark:border-zinc-800 font-sans">
                                <strong>Security &amp; Guardrails:</strong> Honeypot anti-bot, rate limiting, penanganan error standar RFC 9457 Problem Details, dan tamper-evident SHA-256 audit logging.
                            </td>
                            <td class="p-2.5 border-r border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300">
                                Security audit &amp; regression test
                            </td>
                            <td class="p-2.5 text-emerald-600 dark:text-emerald-400 font-bold">UU PDP / OWASP</td>
                        </tr>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50">
                            <td class="p-2.5 font-black text-emerald-600 dark:text-emerald-400 border-r border-zinc-200 dark:border-zinc-800">Sprint 5</td>
                            <td class="p-2.5 text-zinc-800 dark:text-zinc-200 border-r border-zinc-200 dark:border-zinc-800 font-sans">
                                <strong>Build &amp; Deploy:</strong> <code>npm run build</code>, verifikasi health check probes <code>/api/health</code>, dan jalankan skrip deployment <code>./deploy.sh 1</code> atau <code>./deploy.sh 2</code>.
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
            <!-- Left: Docker Stack Commands -->
            <div class="p-4 bg-zinc-900 dark:bg-zinc-950 border border-zinc-800 text-zinc-200 space-y-3">
                <div class="flex items-center justify-between border-b border-zinc-800 pb-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-sky-400 rounded-none"></span>
                        <span class="font-black text-white uppercase text-xs">🐳 DOCKER STACK QUICKSTART</span>
                    </div>
                    <button 
                        type="button" 
                        @click="copyExecutionPrompt(`docker compose up -d\ndocker compose exec app composer install\ndocker compose exec app php artisan migrate\ndocker compose exec app php artisan db:seed --class=SyntheticDataSeeder\ndocker compose exec app npm install\ndocker compose exec app npm run build`, 'PERINTAH DOCKER DISALIN')"
                        class="px-2 py-0.5 bg-sky-500 hover:bg-sky-400 text-black font-black text-[10px] rounded-none cursor-pointer"
                    >
                        SALIN SEMUA
                    </button>
                </div>
                <p class="text-[11px] text-zinc-400 font-sans">
                    Arsip scaffold sudah memuat <code>docker-compose.yml</code> terkonfigurasi (PHP 8.5+, PostgreSQL 16 Strict, Redis 7, Nginx):
                </p>
                <pre class="p-3 bg-black border border-zinc-800 text-[11px] text-sky-300 overflow-x-auto select-all leading-relaxed whitespace-pre"># 1. Jalankan container di background
docker compose up -d

# 2. Install dependencies & jalankan migrasi
docker compose exec app composer install
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed --class=SyntheticDataSeeder

# 3. Build frontend assets
docker compose exec app npm install && docker compose exec app npm run build

# 4. Akses aplikasi: http://localhost:8000</pre>
            </div>

            <!-- Right: ECC Harness (.agents/) & Multi-AI IDE Guide -->
            <div class="p-4 bg-zinc-900 dark:bg-zinc-950 border border-zinc-800 text-zinc-200 space-y-3">
                <div class="flex items-center justify-between border-b border-zinc-800 pb-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-emerald-400 rounded-none"></span>
                        <span class="font-black text-white uppercase text-xs">🧠 ECC HARNESS (.agents/) &amp; AI IDE</span>
                    </div>
                    <span class="text-[10px] text-emerald-400 font-bold uppercase">ZERO DEVIASI</span>
                </div>
                <p class="text-[11px] text-zinc-400 font-sans">
                    Di dalam folder <code>.agents/</code> proyek ini sudah terpasang <strong>ECC (Everything Claude Code / Antigravity Agent Harness)</strong>:
                </p>
                <ul class="text-[11px] space-y-1.5 text-zinc-300 font-mono">
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-400 font-black">&bull;</span>
                        <span><strong>AGENTS.md &amp; .cursorrules:</strong> SOP ketat arsitektur, larangan dialog native, standard O(1) Keyset.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-400 font-black">&bull;</span>
                        <span><strong>PONYTAIL.md:</strong> 4 anak tangga keputusan (Framework Native &rarr; Reuse &rarr; Config &rarr; Surgical Diff).</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-400 font-black">&bull;</span>
                        <span><strong>GRAPH_REPORT.md:</strong> Knowledge graph arsitektur 19 model &amp; puluhan controller tanpa blind scan.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-400 font-black">&bull;</span>
                        <span><strong>Skills Library:</strong> <code>code-reviewer</code>, <code>security-reviewer</code>, <code>verification-loop</code>, <code>browser-qa</code>.</span>
                    </li>
                </ul>
                <div class="p-2 bg-zinc-900 border border-zinc-800 text-[10px] text-zinc-400">
                    💡 <em>AI Agent (Antigravity, Cursor, Claude Code, Windsurf) otomatis membaca guardrails ini saat mengeksekusi setiap prompt.</em>
                </div>
            </div>
        </div>
    </div>
</section>
