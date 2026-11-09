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
<!-- MODAL: DEVELOPER EXECUTION GUIDE // 3 CARA MULAI & DOCKER DEV STACK (WORLD CLASS)         -->
<!-- ========================================================================================= -->
<div 
    x-show="showExecutionGuideModal" 
    x-cloak 
    @keydown.escape.window="showExecutionGuideModal = false"
    class="fixed inset-0 z-50 overflow-y-auto bg-black/85 backdrop-blur-md flex items-center justify-center p-3 sm:p-6 no-print font-sans"
    style="display: none;"
>
    <!-- Modal Card -->
    <div 
        @click.outside="showExecutionGuideModal = false" 
        class="bg-zinc-950 border-2 border-emerald-500 max-w-5xl w-full my-auto max-h-[92vh] flex flex-col p-4 sm:p-6 rounded-none text-zinc-100 shadow-2xl relative overflow-hidden"
    >
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-zinc-800 pb-3 mb-4 shrink-0">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 bg-emerald-500 text-black flex items-center justify-center font-black text-sm rounded-none">
                    🚀
                </span>
                <div>
                    <h3 class="text-base sm:text-lg font-mono font-black uppercase text-white tracking-wider flex items-center gap-2">
                        <span x-show="locale === 'en'">DEVELOPER ROADMAP &amp; EXECUTION PLAYBOOK</span>
                        <span x-show="locale !== 'en'">PETA JALAN &amp; PANDUAN EKSEKUSI DEVELOPER</span>
                        <span class="px-2 py-0.5 text-[10px] bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 font-bold">
                            <span x-show="locale === 'en'">WORLD-CLASS OS</span>
                            <span x-show="locale !== 'en'">OS KELAS DUNIA</span>
                        </span>
                    </h3>
                    <p class="text-xs text-zinc-400 font-mono mt-0.5">
                        <span x-show="locale === 'en'">Guardrails, 3 Ways to Start, Docker Setup, and Execution via ECC (.agents/)</span>
                        <span x-show="locale !== 'en'">Guardrails, 3 Pilihan Cara Mulai, Setup Docker, dan Eksekusi dengan ECC (.agents/)</span>
                    </p>
                </div>
            </div>
            <button @click="showExecutionGuideModal = false" class="text-zinc-400 hover:text-white text-2xl font-bold p-1 cursor-pointer leading-none">
                &times;
            </button>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-1.5 mb-4 font-mono text-xs border-b border-zinc-800 pb-2 shrink-0 overflow-x-auto custom-prd-scrollbar select-none">
            <button 
                type="button" 
                @click="executionGuideTab = 'choice-a'" 
                :class="executionGuideTab === 'choice-a' ? 'bg-emerald-500 text-black font-black' : 'bg-zinc-900 text-zinc-400 hover:text-white'" 
                class="px-3 py-1.5 transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 rounded-none"
            >
                <span x-show="locale === 'en'">🚀 OPTION A: Antigravity Chat</span>
                <span x-show="locale !== 'en'">🚀 PILIHAN A: Sesi Chat Antigravity</span>
            </button>
            <button 
                type="button" 
                @click="executionGuideTab = 'choice-b'" 
                :class="executionGuideTab === 'choice-b' ? 'bg-emerald-500 text-black font-black' : 'bg-zinc-900 text-zinc-400 hover:text-white'" 
                class="px-3 py-1.5 transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 rounded-none"
            >
                <span x-show="locale === 'en'">📦 OPTION B: Clean Standalone App</span>
                <span x-show="locale !== 'en'">📦 PILIHAN B: Standalone Proyek Baru</span>
            </button>
            <button 
                type="button" 
                @click="executionGuideTab = 'choice-c'" 
                :class="executionGuideTab === 'choice-c' ? 'bg-emerald-500 text-black font-black' : 'bg-zinc-900 text-zinc-400 hover:text-white'" 
                class="px-3 py-1.5 transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 rounded-none"
            >
                <span x-show="locale === 'en'">🎯 OPTION C: Phased AI Cockpit</span>
                <span x-show="locale !== 'en'">🎯 PILIHAN C: AI Cockpit Bertahap</span>
            </button>
            <button 
                type="button" 
                @click="executionGuideTab = 'docker'" 
                :class="executionGuideTab === 'docker' ? 'bg-sky-500 text-black font-black' : 'bg-zinc-900 text-zinc-400 hover:text-white'" 
                class="px-3 py-1.5 transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 rounded-none"
            >
                <span x-show="locale === 'en'">🐳 Docker Quickstart</span>
                <span x-show="locale !== 'en'">🐳 Panduan Cepat Docker</span>
            </button>
            <button 
                type="button" 
                @click="executionGuideTab = 'coolify'" 
                :class="executionGuideTab === 'coolify' ? 'bg-purple-600 text-white font-black' : 'bg-zinc-900 text-zinc-400 hover:text-white'" 
                class="px-3 py-1.5 transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 rounded-none"
            >
                <span x-show="locale === 'en'">🚀 Coolify PaaS Setup</span>
                <span x-show="locale !== 'en'">🚀 Setup Coolify PaaS</span>
            </button>
            <button 
                type="button" 
                @click="executionGuideTab = 'sprints'" 
                :class="executionGuideTab === 'sprints' ? 'bg-amber-500 text-black font-black' : 'bg-zinc-900 text-zinc-400 hover:text-white'" 
                class="px-3 py-1.5 transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 rounded-none"
            >
                <span x-show="locale === 'en'">📊 5 Sprint Sequence</span>
                <span x-show="locale !== 'en'">📊 Urutan 5 Sprint</span>
            </button>
            <button 
                type="button" 
                @click="executionGuideTab = 'job-status'" 
                :class="executionGuideTab === 'job-status' ? 'bg-emerald-500 text-black font-black' : 'bg-zinc-900 text-zinc-400 hover:text-white'" 
                class="px-3 py-1.5 transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 rounded-none"
            >
                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-none animate-pulse"></span>
                <span x-show="locale === 'en'">⚡ Scaffold Job Status</span>
                <span x-show="locale !== 'en'">⚡ Status Background Job Scaffold</span>
            </button>
        </div>

        <!-- Scrollable Modal Body -->
        <div class="overflow-y-auto flex-1 pr-1.5 space-y-4 font-mono text-xs">
            
            <!-- TAB 1: PILIHAN A (CHAT ANTIGRAVITY) -->
            <div x-show="executionGuideTab === 'choice-a'" class="space-y-4">
                <div class="p-3 bg-zinc-900 border border-zinc-800 text-zinc-300">
                    <div class="font-bold text-emerald-400 mb-1">
                        <span x-show="locale === 'en'">🚀 Fastest &amp; Most Practical Recommendation:</span>
                        <span x-show="locale !== 'en'">🚀 Rekomendasi Paling Cepat &amp; Praktis:</span>
                    </div>
                    <p class="font-sans leading-relaxed text-xs">
                        <span x-show="locale === 'en'">Since you are currently <strong>already inside Antigravity IDE</strong> and this agent has direct access to the project codebase, you can command the agent directly in this chat session with zero extra setup.</span>
                        <span x-show="locale !== 'en'">Karena Anda saat ini <strong>sudah berada di dalam Antigravity IDE</strong> dan agen ini memiliki akses langsung ke codebase proyek, Anda bisa langsung memerintahkan agen di sesi chat ini tanpa setup tambahan.</span>
                    </p>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-300 font-bold uppercase text-[11px]">
                            <span x-show="locale === 'en'">Sprint 1 Start Command (Database &amp; Model Foundation):</span>
                            <span x-show="locale !== 'en'">Prompt Perintah Mulai Sprint 1 (Fondasi Database &amp; Model):</span>
                        </span>
                        <button 
                            type="button" 
                            @click="copyExecutionPrompt(locale === 'en' ? `Antigravity, please read Blueprint '${bizTitle}'. Start executing Sprint 1: Create PostgreSQL Strict ULID migrations and Eloquent Models for ${firstModule} (${firstTables}) module according to Section 05 specifications. Ensure using ->ulid('id')->primary() and HasUlids trait, then run php artisan migrate.` : `Antigravity, tolong baca Blueprint '${bizTitle}'. Mulai eksekusi Sprint 1: Buat migration PostgreSQL Strict ULID dan Model Eloquent untuk modul ${firstModule} (${firstTables}) sesuai spesifikasi Section 05. Pastikan menggunakan ->ulid('id')->primary() dan trait HasUlids, lalu jalankan php artisan migrate.`, locale === 'en' ? 'SPRINT 1 PROMPT COPIED' : 'PROMPT SPRINT 1 DISALIN')"
                            class="px-2.5 py-1 bg-emerald-500 hover:bg-emerald-400 text-black font-black uppercase text-[10px] rounded-none cursor-pointer flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            <span x-show="locale === 'en'">COPY PROMPT</span>
                            <span x-show="locale !== 'en'">SALIN PROMPT</span>
                        </button>
                    </div>
                    <pre class="p-3.5 bg-black border border-zinc-800 text-emerald-400 overflow-x-auto select-all leading-relaxed whitespace-pre-wrap text-xs"><span x-show="locale === 'en'">Antigravity, please read Blueprint '{{ $bizTitle }}'. Start executing Sprint 1: Create PostgreSQL Strict ULID migrations and Eloquent Models for {{ $firstModule }} ({{ $firstTables }}) module according to Section 05 specifications. Ensure using ->ulid('id')->primary() and HasUlids trait, then run php artisan migrate.</span><span x-show="locale !== 'en'">Antigravity, tolong baca Blueprint '{{ $bizTitle }}'. Mulai eksekusi Sprint 1: Buat migration PostgreSQL Strict ULID dan Model Eloquent untuk modul {{ $firstModule }} ({{ $firstTables }}) sesuai spesifikasi Section 05. Pastikan menggunakan ->ulid('id')->primary() dan trait HasUlids, lalu jalankan php artisan migrate.</span></pre>
                </div>

                <div class="p-3 bg-zinc-900 border border-zinc-800 text-zinc-300 space-y-2">
                    <div class="font-bold text-white uppercase text-[11px]">
                        <span x-show="locale === 'en'">What does Antigravity execute automatically?</span>
                        <span x-show="locale !== 'en'">Apa yang otomatis dikerjakan Antigravity?</span>
                    </div>
                    <ol class="list-decimal list-inside space-y-1 text-zinc-400 font-sans text-xs">
                        <li>
                            <span x-show="locale === 'en'">Reads Section 05 (ERD Schema DDL) from this blueprint specification.</span>
                            <span x-show="locale !== 'en'">Membaca Section 05 (ERD Schema DDL) dari database blueprint ini.</span>
                        </li>
                        <li>
                            <span x-show="locale === 'en'">Writes Laravel migration files with <code>->ulid('id')->primary()</code> (Strict ULID 26-character).</span>
                            <span x-show="locale !== 'en'">Menulis file migrasi Laravel menggunakan <code>->ulid('id')->primary()</code> (Strict ULID 26-karakter).</span>
                        </li>
                        <li>
                            <span x-show="locale === 'en'">Creates Eloquent Model classes with <code>HasUlids</code> trait and <code>foreignUlid()</code> relations.</span>
                            <span x-show="locale !== 'en'">Membuat class Model Eloquent dengan trait <code>HasUlids</code> dan relasi <code>foreignUlid()</code>.</span>
                        </li>
                        <li>
                            <span x-show="locale === 'en'">Executes terminal command <code>php artisan migrate</code> and verifies exit code 0.</span>
                            <span x-show="locale !== 'en'">Mengeksekusi terminal <code>php artisan migrate</code> dan memverifikasi exit code 0.</span>
                        </li>
                        <li>
                            <span x-show="locale === 'en'">Once Sprint 1 passes, proceed to Sprint 2 (API/Service) and Sprint 3 (UI Islands).</span>
                            <span x-show="locale !== 'en'">Setelah Sprint 1 lulus uji, Anda tinggal memerintahkan Sprint 2 (API/Service) dan Sprint 3 (UI).</span>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- TAB 2: PILIHAN B (STANDALONE PROYEK BARU) -->
            <div x-show="executionGuideTab === 'choice-b'" class="space-y-4">
                <div class="p-3 bg-zinc-900 border border-zinc-800 text-zinc-300">
                    <div class="font-bold text-sky-400 mb-1">
                        <span x-show="locale === 'en'">📦 Standalone App in Separate Folder / Repository:</span>
                        <span x-show="locale !== 'en'">📦 Standalone App di Folder / Repository Terpisah:</span>
                    </div>
                    <p class="font-sans leading-relaxed text-xs">
                        <span x-show="locale === 'en'">Use this option if you want to isolate this application into a fresh, independent repository from scratch.</span>
                        <span x-show="locale !== 'en'">Gunakan pilihan ini jika Anda ingin mengisolasi aplikasi ini menjadi repositori mandiri yang bersih dari nol.</span>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-2">
                        <div class="text-white font-bold uppercase text-[11px]">
                            <span x-show="locale === 'en'">Step 1: Download Scaffold ZIP Package</span>
                            <span x-show="locale !== 'en'">Langkah 1: Unduh Paket Scaffold ZIP</span>
                        </div>
                        <p class="text-zinc-400 text-xs font-sans">
                            <span x-show="locale === 'en'">Click the button below to download the complete package (22+ architecture files, Docker, Seeder, and Rules).</span>
                            <span x-show="locale !== 'en'">Klik tombol di bawah untuk mengunduh paket lengkap (22+ berkas arsitektur, Docker, Seeder, dan Rules).</span>
                        </p>
                        <a href="{{ route('blueprint.export-scaffold', $blueprint->slug) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-black font-black uppercase text-[10px] rounded-none">
                            <span x-show="locale === 'en'">DOWNLOAD FULL ZIP (.ZIP) &darr;</span>
                            <span x-show="locale !== 'en'">UNDUH FULL ZIP (.ZIP) &darr;</span>
                        </a>
                    </div>

                    <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-2">
                        <div class="text-white font-bold uppercase text-[11px]">
                            <span x-show="locale === 'en'">Step 2: Open in Antigravity IDE</span>
                            <span x-show="locale !== 'en'">Langkah 2: Buka di Antigravity IDE</span>
                        </div>
                        <p class="text-zinc-400 text-xs font-sans">
                            <span x-show="locale === 'en'">Extract the ZIP file into your new workspace directory (e.g. <code class="text-sky-300" x-text="`c:\\projects\\${workspaceDir || '{{ $shortSlugVal }}'}`">c:\projects\{{ $shortSlugVal }}</code>). In Antigravity IDE, select <strong>File &rarr; Open Folder</strong> and pick that folder.</span>
                            <span x-show="locale !== 'en'">Ekstrak file ZIP ke direktori kerja baru Anda (misal <code class="text-sky-300" x-text="`c:\\projects\\${workspaceDir || '{{ $shortSlugVal }}'}`">c:\projects\{{ $shortSlugVal }}</code>). Di Antigravity IDE, pilih <strong>File &rarr; Open Folder</strong> lalu pilih folder tersebut.</span>
                        </p>
                    </div>
                </div>

                <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-2">
                    <div class="text-white font-bold uppercase text-[11px]">
                        <span x-show="locale === 'en'">Step 3: Files Pre-Wired in New Project</span>
                        <span x-show="locale !== 'en'">Langkah 3: File yang Otomatis Terpasang di Proyek Baru</span>
                    </div>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-zinc-400 font-sans text-xs">
                        <li>&bull; <code>.agents/AGENTS.md</code> &amp; <code>.cursorrules</code> (AI SOP)</li>
                        <li>&bull; <code>docker-compose.yml</code> (PHP 8.5+, PG 16, Redis, Nginx)</li>
                        <li>&bull; <code>schema_complete.sql</code> (DDL ERD ready for import)</li>
                        <li>&bull; <code>openapi.json</code> &amp; <code>PRODUCT-BRIEF.md</code></li>
                    </ul>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-300 font-bold uppercase text-[11px]">
                            <span x-show="locale === 'en'">First Prompt in New Standalone Workspace:</span>
                            <span x-show="locale !== 'en'">Prompt Perdana di Workspace Standalone Baru:</span>
                        </span>
                        <button 
                            type="button" 
                            @click="copyExecutionPrompt(locale === 'en' ? `Antigravity, inspect AGENTS.md, PRODUCT-BRIEF.md, and schema_complete.sql in this new workspace. Spin up containers with docker compose up -d, run PostgreSQL Strict ULID database migrations, and begin MVP Phase 1 implementation.` : `Antigravity, pelajari AGENTS.md, PRODUCT-BRIEF.md, dan schema_complete.sql di workspace baru ini. Setup database menggunakan Docker Compose dan mulai implementasi MVP Fase 1.`, locale === 'en' ? 'STANDALONE PROMPT COPIED' : 'PROMPT STANDALONE DISALIN')"
                            class="px-2.5 py-1 bg-emerald-500 hover:bg-emerald-400 text-black font-black uppercase text-[10px] rounded-none cursor-pointer flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            <span x-show="locale === 'en'">COPY PROMPT</span>
                            <span x-show="locale !== 'en'">SALIN PROMPT</span>
                        </button>
                    </div>
                    <pre class="p-3.5 bg-black border border-zinc-800 text-emerald-400 overflow-x-auto select-all leading-relaxed whitespace-pre-wrap text-xs"><span x-show="locale === 'en'">Antigravity, inspect AGENTS.md, PRODUCT-BRIEF.md, and schema_complete.sql in this new workspace. Spin up containers with docker compose up -d, run PostgreSQL Strict ULID database migrations, and begin MVP Phase 1 implementation.</span><span x-show="locale !== 'en'">Antigravity, pelajari AGENTS.md, PRODUCT-BRIEF.md, dan schema_complete.sql di workspace baru ini. Setup database menggunakan Docker Compose dan mulai implementasi MVP Fase 1.</span></pre>
                </div>
            </div>

            <!-- TAB 3: PILIHAN C (AI COCKPIT) -->
            <div x-show="executionGuideTab === 'choice-c'" class="space-y-4">
                <div class="p-3 bg-zinc-900 border border-zinc-800 text-zinc-300">
                    <div class="font-bold text-purple-400 mb-1">
                        <span x-show="locale === 'en'">🎯 Vertical Slice Prompting (Anti Context-Rot):</span>
                        <span x-show="locale !== 'en'">🎯 Vertical Slice Prompting (Anti Context-Rot):</span>
                    </div>
                    <p class="font-sans leading-relaxed text-xs">
                        <span x-show="locale === 'en'">If you want to copy prompts tailored per step so the AI doesn't experience <em>context-rot</em> (forgetting context as conversation grows long).</span>
                        <span x-show="locale !== 'en'">Jika Anda ingin menyalin prompt yang sudah dipersiapkan secara presisi per tahapan modul agar AI tidak mengalami <em>context-rot</em> (lupa konteks saat chat menjadi panjang).</span>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-2">
                        <div class="text-white font-bold uppercase text-[11px]">
                            <span x-show="locale === 'en'">1. Open AI Cockpit</span>
                            <span x-show="locale !== 'en'">1. Buka AI Cockpit</span>
                        </div>
                        <p class="text-zinc-400 text-xs font-sans">
                            <span x-show="locale === 'en'">Click <code>PROMPT AGENT</code> on the top toolbar or click below to launch the multi-stage orchestration cockpit.</span>
                            <span x-show="locale !== 'en'">Klik tombol <code>PROMPT AGENT</code> di toolbar atau klik tombol di bawah untuk membuka modal orkestrasi per-tahap.</span>
                        </p>
                        <button type="button" @click="showExecutionGuideModal = false; showAiPromptModal = true;" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-500 text-white font-black uppercase text-[10px] rounded-none cursor-pointer">
                            <span x-show="locale === 'en'">OPEN COCKPIT MODAL &rarr;</span>
                            <span x-show="locale !== 'en'">BUKA MODAL COCKPIT &rarr;</span>
                        </button>
                    </div>

                    <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-2">
                        <div class="text-white font-bold uppercase text-[11px]">
                            <span x-show="locale === 'en'">2. Select Target Agent</span>
                            <span x-show="locale !== 'en'">2. Pilih Target Agen</span>
                        </div>
                        <p class="text-zinc-400 text-xs font-sans">
                            <span x-show="locale === 'en'">Supports Antigravity IDE (Google DeepMind), Cursor Composer, Claude Code CLI, or Windsurf Cascade.</span>
                            <span x-show="locale !== 'en'">Mendukung Antigravity IDE (Google DeepMind), Cursor Composer, Claude Code CLI, atau Windsurf Cascade.</span>
                        </p>
                    </div>

                    <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-2">
                        <div class="text-white font-bold uppercase text-[11px]">
                            <span x-show="locale === 'en'">3. Copy Prompt per Step</span>
                            <span x-show="locale !== 'en'">3. Salin Prompt per Tahapan</span>
                        </div>
                        <p class="text-zinc-400 text-xs font-sans">
                            <span x-show="locale === 'en'">Copy Sprint 1 prompt (DB &amp; ULID), paste into AI, run verification, then proceed to subsequent sprints systematically.</span>
                            <span x-show="locale !== 'en'">Salin prompt tahap 1 (DB &amp; ULID), tempel ke AI, jalankan verifikasi, lalu lanjut ke tahap berikutnya secara teratur.</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- TAB 4: DOCKER QUICKSTART (WINDOWS DOCKER DESKTOP + LINUX/VPS) -->
            <div x-show="executionGuideTab === 'docker'" x-data="{ dockerOsTab: 'windows' }" class="space-y-4">
                <!-- OS Selector Switcher -->
                <div class="flex items-center gap-2 border-b border-zinc-800 pb-2 font-mono text-xs">
                    <span class="text-zinc-400 font-bold uppercase text-[10px]">
                        <span x-show="locale === 'en'">SELECT ENVIRONMENT:</span>
                        <span x-show="locale !== 'en'">PILIH LINGKUNGAN:</span>
                    </span>
                    <button 
                        type="button" 
                        @click="dockerOsTab = 'windows'"
                        :class="dockerOsTab === 'windows' ? 'bg-sky-500 text-black font-black' : 'bg-zinc-800 text-zinc-300 hover:text-white'"
                        class="px-2.5 py-1 text-xs uppercase font-bold rounded-none transition cursor-pointer flex items-center gap-1.5"
                    >
                        <span>🪟 Windows (Docker Desktop + WSL 2)</span>
                    </button>
                    <button 
                        type="button" 
                        @click="dockerOsTab = 'linux'"
                        :class="dockerOsTab === 'linux' ? 'bg-sky-500 text-black font-black' : 'bg-zinc-800 text-zinc-300 hover:text-white'"
                        class="px-2.5 py-1 text-xs uppercase font-bold rounded-none transition cursor-pointer flex items-center gap-1.5"
                    >
                        <span>🐧 Linux / macOS / Cloud VPS</span>
                    </button>
                </div>

                <!-- Interactive Workspace Folder Customizer Bar -->
                <div class="p-3 bg-zinc-900 border border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-emerald-400 rounded-none inline-block"></span>
                            <span class="text-xs font-mono font-bold uppercase text-zinc-100">
                                <span x-show="locale === 'en'">Local Workspace Directory / Folder:</span>
                                <span x-show="locale !== 'en'">Nama Folder / Direktori Kerja Lokal:</span>
                            </span>
                            <span class="text-[9px] font-mono px-1.5 py-0.5 bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 font-bold">
                                <span x-show="locale === 'en'">CUSTOMIZABLE</span>
                                <span x-show="locale !== 'en'">DAPAT DISESUAIKAN</span>
                            </span>
                        </div>
                        <p class="text-[11px] text-zinc-400 font-sans">
                            <span x-show="locale === 'en'">Customize this folder name to match your local extracted directory (e.g. <code>my-app-workspace</code>). All terminal commands below will auto-update!</span>
                            <span x-show="locale !== 'en'">Ubah nama folder ini sesuai folder hasil ekstrak di komputer Anda (misal <code>direktori-kerja-saya</code>). Seluruh perintah terminal di bawah akan otomatis menyesuaikan!</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="font-mono text-xs text-zinc-400 bg-zinc-950 px-2 py-1 border border-zinc-800">C:\projects\</span>
                        <div class="relative inline-flex items-center" style="position: relative; display: inline-flex; align-items: center;">
                            <input 
                                type="text" 
                                :value="workspaceDir"
                                @input="handleFolderInput($event)"
                                @keydown.space.prevent="insertHyphen($event)"
                                @blur="workspaceDir = (workspaceDir || '').replace(/^-+|-+$/g, '')"
                                :placeholder="locale === 'en' ? 'e.g. my-app-workspace' : 'misal: direktori-kerja-saya'" 
                                placeholder="{{ $isEn ? 'e.g. my-app-workspace' : 'misal: direktori-kerja-saya' }}" 
                                style="padding-right: 28px;"
                                class="pl-2.5 pr-7 py-1 bg-black border border-emerald-500 text-emerald-300 font-mono text-xs focus:ring-1 focus:ring-emerald-400 focus:outline-none w-40 sm:w-48 rounded-none font-bold placeholder:text-zinc-600 placeholder:font-normal"
                                maxlength="36"
                            />
                            <button 
                                type="button" 
                                x-show="workspaceDir" 
                                @click="setWorkspaceDir('')" 
                                :title="locale === 'en' ? 'Clear (return to default)' : 'Kosongkan (kembali ke default)'"
                                title="Kosongkan (kembali ke default)"
                                style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); z-index: 20; display: flex; align-items: center; justify-content: center; width: 16px; height: 16px; background: transparent; border: none; line-height: 1;"
                                class="text-zinc-400 hover:text-emerald-400 text-base cursor-pointer font-black transition-colors select-none"
                            >
                                &times;
                            </button>
                        </div>
                        <button 
                            type="button" 
                            @click="setWorkspaceDir('')"
                            :title="locale === 'en' ? 'Reset to short default' : 'Reset ke default pendek'"
                            title="Reset ke default pendek"
                            class="px-2 py-1 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 hover:text-white text-[10px] font-mono rounded-none cursor-pointer"
                        >
                            RESET
                        </button>
                    </div>
                </div>

                <!-- SUB-TAB A: WINDOWS (DOCKER DESKTOP + WSL 2) -->
                <div x-show="dockerOsTab === 'windows'" class="space-y-4 font-mono text-xs">
                    <div class="p-3 bg-zinc-900 border border-zinc-800 text-zinc-300">
                        <div class="font-bold text-sky-400 mb-1 flex items-center gap-2">
                            <span x-show="locale === 'en'">🪟 Complete Docker Desktop Guide on Windows (WSL 2 Backend)</span>
                            <span x-show="locale !== 'en'">🪟 Panduan Lengkap Docker Desktop di Windows (WSL 2 Backend)</span>
                            <span class="px-1.5 py-0.2 bg-sky-500/20 text-sky-300 text-[10px] uppercase font-bold">100% REPRODUCIBLE</span>
                        </div>
                        <p class="font-sans leading-relaxed text-xs text-zinc-400">
                            <span x-show="locale === 'en'">Step-by-step guide to setting up Docker Desktop, spinning up PHP 8.5+, PostgreSQL 16 Strict ULID, and Redis 7, extracting scaffold files, and executing the project in Antigravity / Cursor IDE.</span>
                            <span x-show="locale !== 'en'">Panduan langkah demi langkah menyiapkan Docker Desktop Windows, menyalakan tech stack enterprise (PHP 8.5+, PostgreSQL 16 Strict ULID, Redis 7, Nginx), memuat file scaffold, dan mengeksekusi proyek di Antigravity / Cursor IDE.</span>
                        </p>
                    </div>

                    <!-- Step 1: Install & Setup Docker Desktop Windows -->
                    <div class="p-3.5 bg-black border border-zinc-800 space-y-2">
                        <div class="flex items-center justify-between border-b border-zinc-800 pb-1.5">
                            <span class="text-sky-400 font-bold uppercase text-xs flex items-center gap-2">
                                <span class="w-5 h-5 bg-sky-500 text-black flex items-center justify-center font-black text-[10px]">1</span>
                                <span x-show="locale === 'en'">Setup Docker Desktop &amp; WSL 2 Backend</span>
                                <span x-show="locale !== 'en'">Langkah 1: Setup Docker Desktop &amp; WSL 2 di Windows</span>
                            </span>
                            <span class="text-[10px] text-zinc-500 font-mono">POWERSHELL ADMIN</span>
                        </div>
                        <ol class="space-y-1.5 text-zinc-300 font-sans text-xs leading-relaxed list-decimal list-inside">
                            <li>
                                <span x-show="locale === 'en'"><strong>BIOS Virtualization:</strong> Open Task Manager (<kbd class="px-1 bg-zinc-800 text-zinc-300">Ctrl+Shift+Esc</kbd>) &rarr; <em>Performance</em> tab &rarr; <em>CPU</em> &rarr; Ensure <code class="text-emerald-400">Virtualization: Enabled</code>.</span>
                                <span x-show="locale !== 'en'"><strong>Virtualisasi BIOS:</strong> Buka Task Manager (<kbd class="px-1 bg-zinc-800 text-zinc-300">Ctrl+Shift+Esc</kbd>) &rarr; Tab <em>Performance</em> &rarr; <em>CPU</em> &rarr; Pastikan <code class="text-emerald-400">Virtualization: Enabled</code>.</span>
                            </li>
                            <li>
                                <span x-show="locale === 'en'"><strong>Enable WSL 2:</strong> Open PowerShell as Administrator and run the command below:</span>
                                <span x-show="locale !== 'en'"><strong>Aktifkan WSL 2:</strong> Buka PowerShell sebagai Administrator, jalankan perintah di bawah:</span>
                            </li>
                        </ol>
                        <div class="relative">
                            <pre class="p-2.5 bg-zinc-950 border border-zinc-800 text-sky-300 select-all overflow-x-auto text-[11px] leading-relaxed">wsl --install
wsl --update</pre>
                            <button 
                                type="button" 
                                @click="copyExecutionPrompt(`wsl --install\nwsl --update`, locale === 'en' ? 'WSL COMMANDS COPIED' : 'PERINTAH WSL DISALIN')"
                                class="absolute right-2 top-2 px-2 py-0.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-[10px] rounded-none cursor-pointer"
                            >
                                <span x-show="locale === 'en'">COPY</span>
                                <span x-show="locale !== 'en'">SALIN</span>
                            </button>
                        </div>
                        <p class="text-[11px] text-zinc-400 font-sans">
                            <span x-show="locale === 'en'">3. Download &amp; install Docker Desktop from <a href="https://www.docker.com/products/docker-desktop/" target="_blank" class="text-sky-400 underline font-bold">docker.com</a>. During the setup wizard, ensure <strong>"Use the WSL 2 based engine"</strong> is checked. After restarting, open Docker Desktop &rarr; <em>Settings</em> &rarr; <em>General</em> &rarr; verify the WSL 2 option is enabled.</span>
                            <span x-show="locale !== 'en'">3. Unduh &amp; install Docker Desktop dari <a href="https://www.docker.com/products/docker-desktop/" target="_blank" class="text-sky-400 underline font-bold">docker.com</a>. Saat wizard instalasi, pastikan centang <strong>"Use the WSL 2 based engine"</strong>. Setelah restart, buka Docker Desktop &rarr; <em>Settings</em> &rarr; <em>General</em> &rarr; pastikan opsi WSL 2 aktif.</span>
                        </p>
                    </div>

                    <!-- Step 2: Download & Extract Scaffold -->
                    <div class="p-3.5 bg-black border border-zinc-800 space-y-2">
                        <div class="flex items-center justify-between border-b border-zinc-800 pb-1.5">
                            <span class="text-sky-400 font-bold uppercase text-xs flex items-center gap-2">
                                <span class="w-5 h-5 bg-sky-500 text-black flex items-center justify-center font-black text-[10px]">2</span>
                                <span x-show="locale === 'en'">Download &amp; Extract Scaffold Package</span>
                                <span x-show="locale !== 'en'">Langkah 2: Unduh &amp; Ekstrak Paket Scaffold Proyek</span>
                            </span>
                            <span class="text-[10px] text-emerald-400 font-mono font-bold">STARTER KIT .ZIP</span>
                        </div>
                        <p class="text-zinc-300 font-sans text-xs leading-relaxed">
                            <span x-show="locale === 'en'">Download the scaffold archive via the <strong>DOWNLOAD FULL ZIP (.ZIP)</strong> button. Extract the ZIP contents to your local Windows folder (recommended path without spaces, e.g.: <code class="text-amber-400" x-text="`C:\\projects\\${workspaceDir || '{{ $shortSlugVal }}'}`">C:\projects\{{ $shortSlugVal }}</code> or <code class="text-amber-400" x-text="`C:\\xampp\\htdocs\\${workspaceDir || '{{ $shortSlugVal }}'}`">C:\xampp\htdocs\{{ $shortSlugVal }}</code>).</span>
                            <span x-show="locale !== 'en'">Unduh arsip scaffold melalui tombol <strong>DOWNLOAD FULL ZIP (.ZIP)</strong>. Ekstrak isi ZIP tersebut ke folder kerja di Windows (disarankan path tanpa spasi, misal: <code class="text-amber-400" x-text="`C:\\projects\\${workspaceDir || '{{ $shortSlugVal }}'}`">C:\projects\{{ $shortSlugVal }}</code> atau <code class="text-amber-400" x-text="`C:\\xampp\\htdocs\\${workspaceDir || '{{ $shortSlugVal }}'}`">C:\xampp\htdocs\{{ $shortSlugVal }}</code>).</span>
                        </p>
                        <div class="p-2 bg-zinc-950 border border-zinc-800 text-[11px] text-zinc-400 font-mono space-y-1">
                            <div class="text-zinc-200 font-bold">&bull; <span x-show="locale === 'en'">Key Pre-Wired Files Inside the Folder:</span><span x-show="locale !== 'en'">Berkas Kunci yang Siap Digunakan di Dalam Folder:</span></div>
                            <div class="pl-2">&rarr; <code>Dockerfile</code> (<span x-show="locale === 'en'">PHP 8.5 FPM Alpine — extensionless</span><span x-show="locale !== 'en'">PHP 8.5 FPM Alpine — tanpa ekstensi</span>)</div>
                            <div class="pl-2">&rarr; <code>docker-compose.yml</code> (PHP 8.5+, PostgreSQL 16 Strict, Redis 7, Nginx)</div>
                            <div class="pl-2">&rarr; <code>.env.blueprint</code> (<span x-show="locale === 'en'">Ready-to-use environment configuration</span><span x-show="locale !== 'en'">Konfigurasi environment siap pakai</span>)</div>
                            <div class="pl-2">&rarr; <code>.agents/AGENTS.md</code> &amp; <code>.cursorrules</code> (<span x-show="locale === 'en'">ECC SOP &amp; Architectural Guardrails</span><span x-show="locale !== 'en'">SOP &amp; Guardrails ECC</span>)</div>
                            <div class="pl-2">&rarr; <code>schema_complete.sql</code> (<span x-show="locale === 'en'">PostgreSQL Strict ULID DDL Schema</span><span x-show="locale !== 'en'">Skema DDL PostgreSQL Strict ULID</span>)</div>
                            <div class="pl-2">&rarr; <code>SyntheticDataSeeder.php</code> &amp; <code>ApiContractTest.php</code></div>
                        </div>
                    </div>

                    <!-- Step 3: PowerShell Navigation & Environment File -->
                    <div class="p-3.5 bg-black border border-zinc-800 space-y-2">
                        <div class="flex items-center justify-between border-b border-zinc-800 pb-1.5">
                            <span class="text-sky-400 font-bold uppercase text-xs flex items-center gap-2">
                                <span class="w-5 h-5 bg-sky-500 text-black flex items-center justify-center font-black text-[10px]">3</span>
                                <span x-show="locale === 'en'">Open PowerShell &amp; Copy Environment File</span>
                                <span x-show="locale !== 'en'">Langkah 3: Buka PowerShell &amp; Siapkan File .env</span>
                            </span>
                            <span class="text-[10px] text-zinc-500 font-mono">TERMINAL WINDOWS</span>
                        </div>
                        <p class="text-zinc-300 font-sans text-xs leading-relaxed">
                            <span x-show="locale === 'en'">Open Windows File Explorer inside the extracted folder. Hold <kbd class="px-1 bg-zinc-800 text-zinc-300">Shift</kbd> and <strong>Right-Click</strong> in an empty area &rarr; select <strong>"Open in Terminal"</strong> (or open PowerShell normally then <code class="text-sky-300" x-text="`cd C:\\projects\\${workspaceDir || '{{ $shortSlugVal }}'}`">cd C:\projects\{{ $shortSlugVal }}</code>). Copy the environment template:</span>
                            <span x-show="locale !== 'en'">Buka File Explorer Windows di folder hasil ekstrak. Tahan tombol <kbd class="px-1 bg-zinc-800 text-zinc-300">Shift</kbd> lalu <strong>Klik Kanan</strong> di area kosong &rarr; pilih <strong>"Open in Terminal"</strong> (atau buka PowerShell biasa lalu <code class="text-sky-300" x-text="`cd C:\\projects\\${workspaceDir || '{{ $shortSlugVal }}'}`">cd C:\projects\{{ $shortSlugVal }}</code>). Salin environment template:</span>
                        </p>
                        <div class="relative">
                            <pre class="p-2.5 bg-zinc-950 border border-zinc-800 text-sky-300 select-all overflow-x-auto text-[11px] leading-relaxed">Copy-Item .env.blueprint .env</pre>
                            <button 
                                type="button" 
                                @click="copyExecutionPrompt(`cd C:\\projects\\${workspaceDir || '{{ $shortSlugVal }}'}\nCopy-Item .env.blueprint .env`, locale === 'en' ? 'ENV COMMAND COPIED' : 'PERINTAH ENV DISALIN')"
                                class="absolute right-2 top-2 px-2 py-0.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-[10px] rounded-none cursor-pointer"
                            >
                                <span x-show="locale === 'en'">COPY</span>
                                <span x-show="locale !== 'en'">SALIN</span>
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Run Tech Stack via Docker Compose -->
                    <div class="p-3.5 bg-black border border-zinc-800 space-y-2">
                        <div class="flex items-center justify-between border-b border-zinc-800 pb-1.5">
                            <span class="text-sky-400 font-bold uppercase text-xs flex items-center gap-2">
                                <span class="w-5 h-5 bg-sky-500 text-black flex items-center justify-center font-black text-[10px]">4</span>
                                <span x-show="locale === 'en'">Spin Up Tech Stack &amp; Run Initial Setup</span>
                                <span x-show="locale !== 'en'">Langkah 4: Nyalakan Stack &amp; Eksekusi Perintah (1-Klik Salin)</span>
                            </span>
                            <button 
                                type="button" 
                                @click="copyExecutionPrompt(`cd C:\\projects\\${workspaceDir || '{{ $shortSlugVal }}'}\ndocker compose up --build -d\ndocker compose exec app composer install\ndocker compose exec app php artisan key:generate\ndocker compose exec app php artisan migrate\ndocker compose exec app php artisan db:seed --class=SyntheticDataSeeder\ndocker compose exec app npm install\ndocker compose exec app npm run build\ncurl http://localhost:8000/api/health`, locale === 'en' ? 'POWERSHELL DOCKER SCRIPT COPIED' : 'SKRIP DOCKER POWERSHELL DISALIN')"
                                class="px-2.5 py-1 bg-sky-500 hover:bg-sky-400 text-black font-black uppercase text-[10px] rounded-none cursor-pointer"
                            >
                                <span x-show="locale === 'en'">COPY ALL POWERSHELL COMMANDS</span>
                                <span x-show="locale !== 'en'">SALIN SEMUA PERINTAH POWERSHELL</span>
                            </button>
                        </div>
                        <p class="text-zinc-300 font-sans text-xs leading-relaxed">
                            <span x-show="locale === 'en'">Run the following commands in PowerShell to spin up containers, install backend dependencies, execute PostgreSQL 16 Strict ULID database migrations, and seed realistic data:</span>
                            <span x-show="locale !== 'en'">Jalankan perintah-perintah berikut di PowerShell untuk menyalakan container, menginstall dependencies, menjalankan migrasi database PostgreSQL Strict ULID, dan mengisi data sintetis:</span>
                        </p>
                        <pre class="p-3.5 bg-zinc-950 border border-zinc-800 text-sky-300 overflow-x-auto select-all leading-relaxed whitespace-pre text-xs"><span x-show="locale === 'en'"># 0. Change directory to local workspace</span><span x-show="locale !== 'en'"># 0. Pindah ke direktori kerja lokal</span>
<span x-text="`cd C:\\projects\\${workspaceDir || '{{ $shortSlugVal }}'}`">cd C:\projects\{{ $shortSlugVal }}</span>

<span x-show="locale === 'en'"># 1. Spin up all container services in background (local build PHP 8.5)</span><span x-show="locale !== 'en'"># 1. Nyalakan seluruh container service di latar belakang (build lokal PHP 8.5)</span>
docker compose up --build -d

<span x-show="locale === 'en'"># 2. Install backend PHP 8.5+ dependencies via Composer</span><span x-show="locale !== 'en'"># 2. Install dependency backend PHP 8.5+ via Composer</span>
docker compose exec app composer install

<span x-show="locale === 'en'"># 3. Generate Laravel Application Key</span><span x-show="locale !== 'en'"># 3. Generate Application Key Laravel</span>
docker compose exec app php artisan key:generate

<span x-show="locale === 'en'"># 4. Run PostgreSQL 16 Strict ULID database migrations</span><span x-show="locale !== 'en'"># 4. Jalankan migrasi database PostgreSQL 16 Strict ULID</span>
docker compose exec app php artisan migrate

<span x-show="locale === 'en'"># 5. Seed realistic synthetic data (100+ rows)</span><span x-show="locale !== 'en'"># 5. Isi data sintetis realistis 100+ baris</span>
docker compose exec app php artisan db:seed --class=SyntheticDataSeeder

<span x-show="locale === 'en'"># 6. Install frontend packages & compile bundle</span><span x-show="locale !== 'en'"># 6. Install frontend packages & compile bundle</span>
docker compose exec app npm install
docker compose exec app npm run build

<span x-show="locale === 'en'"># 7. Verify health check probe</span><span x-show="locale !== 'en'"># 7. Verifikasi health check probe</span>
curl http://localhost:8000/api/health</pre>

                        <div class="p-2.5 bg-zinc-900 border border-zinc-800 text-zinc-300 grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
                            <div>&bull; <strong><span x-show="locale === 'en'">Web App:</span><span x-show="locale !== 'en'">Aplikasi Web:</span></strong> <a href="http://localhost:8000" target="_blank" class="text-sky-400 underline">http://localhost:8000</a></div>
                            <div>&bull; <strong>PostgreSQL 16:</strong> <code>localhost:5432</code> (DB: <code x-text="`${workspaceDir || '{{ $shortSlugVal }}'}_db`">{{ $shortSlugVal }}_db</code>)</div>
                            <div>&bull; <strong>Redis Cache:</strong> <code>localhost:6379</code></div>
                            <div>&bull; <strong><span x-show="locale === 'en'">Living API Docs:</span><span x-show="locale !== 'en'">Dokumentasi API:</span></strong> <a href="http://localhost:8000/api/docs" target="_blank" class="text-emerald-400 underline">http://localhost:8000/api/docs</a></div>
                        </div>
                    </div>

                    <!-- Step 5: Open in IDE with ECC -->
                    <div class="p-3.5 bg-black border border-emerald-500/60 space-y-2">
                        <div class="flex items-center justify-between border-b border-zinc-800 pb-1.5">
                            <span class="text-emerald-400 font-bold uppercase text-xs flex items-center gap-2">
                                <span class="w-5 h-5 bg-emerald-500 text-black flex items-center justify-center font-black text-[10px]">5</span>
                                <span x-show="locale === 'en'">Open in Antigravity / Cursor IDE with ECC Harness</span>
                                <span x-show="locale !== 'en'">Langkah 5: Buka di Antigravity / Cursor IDE dengan ECC (.agents/)</span>
                            </span>
                            <button 
                                type="button" 
                                @click="copyExecutionPrompt(locale === 'en' ? `Antigravity, inspect AGENTS.md, PRODUCT-BRIEF.md, and schema_complete.sql in this new workspace. Spin up containers with docker compose up -d, run PostgreSQL Strict ULID database migrations, and begin MVP Phase 1 implementation.` : `Antigravity, pelajari AGENTS.md, PRODUCT-BRIEF.md, dan schema_complete.sql di workspace baru ini. Setup database menggunakan Docker Compose dan mulai implementasi MVP Fase 1.`, locale === 'en' ? 'AI PROMPT COPIED' : 'PROMPT AI DISALIN')"
                                class="px-2.5 py-1 bg-emerald-500 hover:bg-emerald-400 text-black font-black uppercase text-[10px] rounded-none cursor-pointer"
                            >
                                <span x-show="locale === 'en'">COPY AI STARTER PROMPT</span>
                                <span x-show="locale !== 'en'">SALIN PROMPT PERDANA AI</span>
                            </button>
                        </div>
                        <p class="text-zinc-300 font-sans text-xs leading-relaxed">
                            <span x-show="locale === 'en'">Open Antigravity IDE or Cursor in Windows &rarr; <strong>File &rarr; Open Folder</strong> &rarr; select the extracted workspace folder. Open AI chat and paste the following prompt:</span>
                            <span x-show="locale !== 'en'">Buka Antigravity IDE atau Cursor di Windows &rarr; <strong>File &rarr; Open Folder</strong> &rarr; pilih folder hasil ekstrak. Buka jendela chat AI dan tempelkan prompt berikut:</span>
                        </p>
                        <pre class="p-3 bg-zinc-950 border border-zinc-800 text-emerald-400 overflow-x-auto select-all leading-relaxed whitespace-pre-wrap text-xs"><span x-show="locale === 'en'">Antigravity, inspect AGENTS.md, PRODUCT-BRIEF.md, and schema_complete.sql in this new workspace. Spin up containers with docker compose up -d, run PostgreSQL Strict ULID database migrations, and begin MVP Phase 1 implementation.</span><span x-show="locale !== 'en'">Antigravity, pelajari AGENTS.md, PRODUCT-BRIEF.md, dan schema_complete.sql di workspace baru ini. Setup database menggunakan Docker Compose dan mulai implementasi MVP Fase 1.</span></pre>
                    </div>
                </div>

                <!-- SUB-TAB B: LINUX / MACOS / CLOUD VPS -->
                <div x-show="dockerOsTab === 'linux'" class="space-y-4 font-mono text-xs">
                    <div class="p-3 bg-zinc-900 border border-zinc-800 text-zinc-300">
                        <div class="font-bold text-sky-400 mb-1">
                            <span x-show="locale === 'en'">🐳 Linux / macOS / Cloud VPS Terminal Quickstart:</span>
                            <span x-show="locale !== 'en'">🐳 Terminal Quickstart Linux / macOS / Cloud VPS:</span>
                        </div>
                        <p class="font-sans leading-relaxed text-xs text-zinc-400">
                            <span x-show="locale === 'en'">The project scaffold archive includes an enterprise <code>docker-compose.yml</code> with PHP 8.5+, PostgreSQL 16 (Strict Mode), Redis 7, and Nginx Web Server.</span>
                            <span x-show="locale !== 'en'">Arsip scaffold proyek menyertakan <code>docker-compose.yml</code> enterprise dengan PHP 8.5+, PostgreSQL 16 (Strict Mode), Redis 7, dan Nginx Web Server.</span>
                        </p>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-300 font-bold uppercase text-[11px]">
                                <span x-show="locale === 'en'">Bash Terminal Commands:</span>
                                <span x-show="locale !== 'en'">Perintah Terminal Bash:</span>
                            </span>
                            <button 
                                type="button" 
                                @click="copyExecutionPrompt(`cp .env.blueprint .env\ndocker compose up --build -d\ndocker compose exec app composer install\ndocker compose exec app php artisan key:generate\ndocker compose exec app php artisan migrate\ndocker compose exec app php artisan db:seed --class=SyntheticDataSeeder\ndocker compose exec app npm install && docker compose exec app npm run build\ncurl http://localhost:8000/api/health`, locale === 'en' ? 'BASH COMMANDS COPIED' : 'PERINTAH BASH DISALIN')"
                                class="px-2.5 py-1 bg-sky-500 hover:bg-sky-400 text-black font-black uppercase text-[10px] rounded-none cursor-pointer"
                            >
                                <span x-show="locale === 'en'">COPY BASH COMMANDS</span>
                                <span x-show="locale !== 'en'">SALIN PERINTAH BASH</span>
                            </button>
                        </div>
                        <pre class="p-3.5 bg-black border border-zinc-800 text-sky-300 overflow-x-auto select-all leading-relaxed whitespace-pre text-xs"># 1. Setup environment file
cp .env.blueprint .env

# 2. Start container services in background (build local PHP 8.5)
docker compose up --build -d

# 3. Enter container and install backend dependencies
docker compose exec app composer install
docker compose exec app php artisan key:generate

# 4. Run PostgreSQL Strict ULID database migrations
docker compose exec app php artisan migrate

# 5. Generate synthetic realistic seed data
docker compose exec app php artisan db:seed --class=SyntheticDataSeeder

# 6. Install frontend dependencies and build assets
docker compose exec app npm install && docker compose exec app npm run build

# 7. Verify server health check probe
curl http://localhost:8000/api/health</pre>
                    </div>

                    <div class="p-3 bg-zinc-900 border border-zinc-800 text-zinc-400 space-y-1 text-xs font-sans">
                        <div class="text-white font-bold font-mono uppercase text-[11px]">
                            <span x-show="locale === 'en'">Docker Port Configuration:</span>
                            <span x-show="locale !== 'en'">Port Konfigurasi Docker:</span>
                        </div>
                        <div>&bull; Nginx Web Server: <code>http://localhost:8000</code></div>
                        <div>&bull; PostgreSQL 16: <code>localhost:5432</code> (User: <code>neriah</code>, DB: <code x-text="`${workspaceDir || '{{ $shortSlugVal }}'}_db`">{{ $shortSlugVal }}_db</code>)</div>
                        <div>&bull; Redis 7: <code>localhost:6379</code></div>
                        <div>&bull; Living API Docs: <code>http://localhost:8000/api/docs</code></div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: COOLIFY PAAS SETUP (ONE-STOP VPS PRODUCTION SETUP) -->
            <div x-show="executionGuideTab === 'coolify'" class="space-y-4">
                <!-- Coolify Header Banner -->
                <div class="p-3.5 bg-gradient-to-r from-purple-950/60 to-zinc-950 border border-purple-500/40 text-zinc-200">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 bg-purple-400 rounded-none animate-ping"></span>
                        <span class="font-mono font-bold text-xs uppercase text-purple-300">
                            <span x-show="locale === 'en'">🚀 ONE-STOP COOLIFY SELF-HOSTED PAAS PLAYBOOK</span>
                            <span x-show="locale !== 'en'">🚀 PANDUAN LENGKAP SETUP &amp; DEPLOYMENT COOLIFY PAAS</span>
                        </span>
                        <span class="px-2 py-0.5 bg-purple-500/20 text-purple-300 border border-purple-500/40 font-mono text-[9px] font-bold">ZERO-DEV-OPS</span>
                    </div>
                    <p class="font-sans leading-relaxed text-xs text-zinc-300">
                        <span x-show="locale === 'en'">Coolify is an open-source, self-hosted Heroku/Vercel/Render alternative. This starter kit includes a production-grade <code>docker-compose.coolify.yml</code> file pre-configured with Traefik auto-SSL, PostgreSQL 16 Strict ULID, and Redis 7.</span>
                        <span x-show="locale !== 'en'">Coolify adalah platform PaaS mandiri (alternatif Heroku/Vercel) berbasis Docker &amp; Traefik. Starter kit ini sudah menyertakan berkas <code>docker-compose.coolify.yml</code> siap pakai lengkap dengan otomatisasi SSL Let's Encrypt, PostgreSQL 16 Strict ULID, dan Redis 7.</span>
                    </p>
                </div>

                <!-- Complete Tech Stack on Coolify (Grid of Components) -->
                <div class="space-y-2">
                    <div class="font-mono font-bold text-xs uppercase text-zinc-300 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-purple-400"></span>
                        <span x-show="locale === 'en'">1. PRODUCTION TECH STACK ON COOLIFY (ISOLATED CONTAINERS):</span>
                        <span x-show="locale !== 'en'">1. ARSITEKTUR TECH STACK PRODUKSI DI COOLIFY (CONTAINER TERISOLASI):</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                        <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-1">
                            <div class="text-[11px] font-bold text-purple-300 font-mono flex items-center justify-between">
                                <span>🌐 <span x-show="locale === 'en'">WEB SERVER</span><span x-show="locale !== 'en'">WEB SERVER</span></span>
                                <span class="text-[9px] px-1 bg-zinc-800 text-zinc-400">HTTP/2</span>
                            </div>
                            <div class="text-xs font-bold text-white">Nginx Alpine</div>
                            <p class="text-[11px] text-zinc-400 font-sans leading-relaxed">
                                <span x-show="locale === 'en'">High-performance reverse proxy, gzip/brotli compression, asset caching, and edge DDoS mitigation.</span>
                                <span x-show="locale !== 'en'">Reverse proxy berkinerja tinggi, gzip/brotli compression, asset caching, dan mitigasi DDoS edge.</span>
                            </p>
                        </div>
                        <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-1">
                            <div class="text-[11px] font-bold text-emerald-400 font-mono flex items-center justify-between">
                                <span>⚡ <span x-show="locale === 'en'">APP RUNTIME</span><span x-show="locale !== 'en'">RUNTIME APLIKASI</span></span>
                                <span class="text-[9px] px-1 bg-zinc-800 text-zinc-400">PHP 8.4+</span>
                            </div>
                            <div class="text-xs font-bold text-white">Laravel 13 (PHP-FPM)</div>
                            <p class="text-[11px] text-zinc-400 font-sans leading-relaxed">
                                <span x-show="locale === 'en'">OPcache + JIT enabled, Livewire 4, Filament v5 Enterprise, <code>pdo_pgsql</code>, <code>redis</code>, <code>pcntl</code> extensions.</span>
                                <span x-show="locale !== 'en'">OPcache + JIT aktif, Livewire 4, Filament v5 Enterprise, ekstensi <code>pdo_pgsql</code>, <code>redis</code>, <code>pcntl</code>.</span>
                            </p>
                        </div>
                        <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-1">
                            <div class="text-[11px] font-bold text-sky-400 font-mono flex items-center justify-between">
                                <span>🗄️ <span x-show="locale === 'en'">DATABASE</span><span x-show="locale !== 'en'">DATABASE</span></span>
                                <span class="text-[9px] px-1 bg-zinc-800 text-zinc-400">PG 16</span>
                            </div>
                            <div class="text-xs font-bold text-white">PostgreSQL 16 ULID</div>
                            <p class="text-[11px] text-zinc-400 font-sans leading-relaxed">
                                <span x-show="locale === 'en'">26-character ULID Primary Keys, Keyset Cursor Pagination O(1), isolated volume anti-data loss.</span>
                                <span x-show="locale !== 'en'">Primary Key ULID 26 karakter, Keyset Cursor Pagination O(1), volume terisolasi anti-data loss.</span>
                            </p>
                        </div>
                        <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-1">
                            <div class="text-[11px] font-bold text-rose-400 font-mono flex items-center justify-between">
                                <span>🚀 <span x-show="locale === 'en'">CACHE &amp; QUEUE</span><span x-show="locale !== 'en'">CACHE &amp; ANTRIAN</span></span>
                                <span class="text-[9px] px-1 bg-zinc-800 text-zinc-400">IN-MEMORY</span>
                            </div>
                            <div class="text-xs font-bold text-white">Redis 7 Alpine</div>
                            <p class="text-[11px] text-zinc-400 font-sans leading-relaxed">
                                <span x-show="locale === 'en'">Sub-millisecond session store, dynamic tag caching, and asynchronous queue processing (background jobs).</span>
                                <span x-show="locale !== 'en'">Penyimpanan session kilat, cache tag dinamis, dan pemrosesan antrean asinkron (background jobs).</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step-by-Step Playbook -->
                <div class="space-y-3 pt-2">
                    <div class="font-mono font-bold text-xs uppercase text-zinc-300 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-purple-400"></span>
                        <span x-show="locale === 'en'">2. STEP-BY-STEP COOLIFY INSTALLATION &amp; DEPLOYMENT:</span>
                        <span x-show="locale !== 'en'">2. LANGKAH-LANGKAH INSTALASI VPS &amp; DEPLOYMENT DI COOLIFY:</span>
                    </div>

                    <!-- Step 1: Install Coolify on VPS -->
                    <div class="p-3.5 bg-zinc-900 border border-zinc-800 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 bg-purple-600 text-white font-mono font-bold text-xs flex items-center justify-center">1</span>
                                <span class="font-bold text-white uppercase text-xs">
                                    <span x-show="locale === 'en'">Install Coolify on VPS (Ubuntu 22.04 / 24.04 / Debian 12)</span>
                                    <span x-show="locale !== 'en'">Install Coolify di Server VPS (Ubuntu 22.04 / 24.04 / Debian 12)</span>
                                </span>
                            </div>
                            <button 
                                type="button" 
                                @click="navigator.clipboard.writeText('curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash'); if(window.showToast) window.showToast({ type: 'success', title: 'PERINTAH DISALIN', message: 'Perintah install Coolify telah disalin ke clipboard.' })"
                                class="px-2 py-0.5 bg-purple-600 hover:bg-purple-500 text-white font-mono font-bold text-[10px] uppercase rounded-none cursor-pointer flex items-center gap-1"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                <span>COPY</span>
                            </button>
                        </div>
                        <p class="text-[11px] text-zinc-400 font-sans">
                            <span x-show="locale === 'en'">Login to your VPS via SSH as root (<code>ssh root@ip-vps</code>), then run this official one-liner script:</span>
                            <span x-show="locale !== 'en'">Login ke terminal VPS Anda sebagai root (<code>ssh root@ip-vps</code>), lalu jalankan perintah instalasi resmi Coolify:</span>
                        </p>
                        <pre class="p-2.5 bg-black border border-zinc-800 text-purple-300 font-mono text-xs select-all overflow-x-auto">curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash</pre>
                        <p class="text-[10px] text-zinc-500 font-mono">
                            <span x-show="locale === 'en'">&bull; VPS ports required: <code>22</code> (SSH), <code>80</code> (HTTP), <code>443</code> (HTTPS), <code>8000</code> (initial Coolify Dashboard).</span>
                            <span x-show="locale !== 'en'">&bull; Port VPS wajib terbuka: <code>22</code> (SSH), <code>80</code> (HTTP), <code>443</code> (HTTPS), <code>8000</code> (Coolify Dashboard awal).</span>
                        </p>
                    </div>

                    <!-- Step 2: Access Dashboard & Add Git Source -->
                    <div class="p-3.5 bg-zinc-900 border border-zinc-800 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 bg-purple-600 text-white font-mono font-bold text-xs flex items-center justify-center">2</span>
                            <span class="font-bold text-white uppercase text-xs">
                                <span x-show="locale === 'en'">Access Coolify Dashboard &amp; Connect Git Repository</span>
                                <span x-show="locale !== 'en'">Buka Dashboard Coolify &amp; Hubungkan Repositori Git</span>
                            </span>
                        </div>
                        <div class="text-[11px] text-zinc-400 font-sans space-y-1 leading-relaxed">
                            <div>&bull; <span x-show="locale === 'en'">Open browser to <code>http://&lt;YOUR-VPS-IP&gt;:8000</code> and create your first administrator account.</span><span x-show="locale !== 'en'">Buka browser ke <code>http://&lt;IP-VPS-ANDA&gt;:8000</code> dan buat akun admin pertama.</span></div>
                            <div>&bull; <span x-show="locale === 'en'">Navigate to <strong>Sources</strong> &rarr; Connect your <strong>GitHub / GitLab</strong> account where you pushed this starter kit code.</span><span x-show="locale !== 'en'">Masuk ke menu <strong>Sources</strong> &rarr; Hubungkan akun <strong>GitHub / GitLab</strong> tempat Anda menyimpan source code starter kit ini.</span></div>
                        </div>
                    </div>

                    <!-- Step 3: Deploy via docker-compose.coolify.yml -->
                    <div class="p-3.5 bg-zinc-900 border border-zinc-800 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 bg-purple-600 text-white font-mono font-bold text-xs flex items-center justify-center">3</span>
                                <span class="font-bold text-white uppercase text-xs">
                                    <span x-show="locale === 'en'">Create New Resource &rarr; Docker Compose</span>
                                    <span x-show="locale !== 'en'">Buat Resource Baru &rarr; Pilih Docker Compose</span>
                                </span>
                            </div>
                            <span class="text-[10px] font-mono text-emerald-400 font-bold">docker-compose.coolify.yml</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 font-sans space-y-1 leading-relaxed">
                            <div>1. <span x-show="locale === 'en'">In Coolify, click <strong>Projects</strong> &rarr; <strong>Production</strong> &rarr; <strong>+ New Resource</strong> &rarr; <strong>Docker Compose</strong>.</span><span x-show="locale !== 'en'">Di Coolify, klik <strong>Projects</strong> &rarr; <strong>Production</strong> &rarr; <strong>+ New Resource</strong> &rarr; <strong>Docker Compose</strong>.</span></div>
                            <div>2. <span x-show="locale === 'en'">Select your Git repository and target branch (e.g. <code>main</code>).</span><span x-show="locale !== 'en'">Pilih Git repository proyek Anda dan branch target (misal: <code>main</code>).</span></div>
                            <div>3. <span x-show="locale === 'en'">In the <strong>Docker Compose File</strong> field, enter: <code>docker-compose.coolify.yml</code> (included in starter kit root).</span><span x-show="locale !== 'en'">Pada input <strong>Docker Compose File</strong>, ketik: <code>docker-compose.coolify.yml</code> (berkas ini sudah tersedia di root starter kit).</span></div>
                            <div>4. <span x-show="locale === 'en'">In the <strong>Domains</strong> field, enter your domain: <code>https://app.yourdomain.com</code>. Traefik automatically issues Let's Encrypt SSL!</span><span x-show="locale !== 'en'">Di kolom <strong>Domains</strong>, masukkan domain Anda: <code>https://app.domainanda.com</code>. Traefik otomatis menerbitkan SSL Let's Encrypt!</span></div>
                        </div>
                    </div>

                    <!-- Step 4: Environment Variables Template -->
                    <div class="p-3.5 bg-zinc-900 border border-zinc-800 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 bg-purple-600 text-white font-mono font-bold text-xs flex items-center justify-center">4</span>
                                <span class="font-bold text-white uppercase text-xs">
                                    <span x-show="locale === 'en'">Copy Environment Variables to Coolify Tab:</span>
                                    <span x-show="locale !== 'en'">Salin Variabel Environment ke Tab Coolify:</span>
                                </span>
                            </div>
                            <button 
                                type="button" 
                                @click="
                                    const envText = `APP_NAME=\"${bizTitle}\"\nAPP_ENV=production\nAPP_KEY=base64:GENERATE_KEY_VIA_ARTISAN\nAPP_DEBUG=false\nAPP_URL=https://app.domainanda.com\nCOOLIFY_URL=https://app.domainanda.com\n\nDB_CONNECTION=pgsql\nDB_HOST=postgres\nDB_PORT=5432\nDB_DATABASE=${(workspaceDir || '{{ $shortSlugVal }}')}_db\nDB_USERNAME=${(workspaceDir || '{{ $shortSlugVal }}')}_user\nDB_PASSWORD=GantiDenganPasswordSangatKuat123!\n\nCACHE_STORE=redis\nSESSION_DRIVER=redis\nQUEUE_CONNECTION=redis\nREDIS_HOST=redis\nREDIS_PORT=6379\n\nAI_THREAT_SHIELD_ENABLED=true`;
                                    navigator.clipboard.writeText(envText);
                                    if(window.showToast) window.showToast({ type: 'success', title: 'ENV COOLIFY DISALIN', message: 'Environment variables siap dipaste ke dashboard Coolify.' });
                                "
                                class="px-2 py-0.5 bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-bold text-[10px] uppercase rounded-none cursor-pointer flex items-center gap-1"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                <span>COPY ENV BLOCK</span>
                            </button>
                        </div>
                        <pre class="p-3 bg-black border border-zinc-800 text-emerald-300 font-mono text-xs select-all overflow-x-auto leading-relaxed whitespace-pre">APP_NAME="{{ $bizTitle }}"
APP_ENV=production
APP_KEY=base64:GENERATE_KEY_VIA_ARTISAN
APP_DEBUG=false
APP_URL=https://app.domainanda.com

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=<span x-text="`${workspaceDir || '{{ $shortSlugVal }}'}_db`">{{ $shortSlugVal }}_db</span>
DB_USERNAME=<span x-text="`${workspaceDir || '{{ $shortSlugVal }}'}_user`">{{ $shortSlugVal }}_user</span>
DB_PASSWORD=GantiDenganPasswordSangatKuat123!

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PORT=6379

AI_THREAT_SHIELD_ENABLED=true</pre>
                    </div>

                    <!-- Step 5: Post-Deployment Verification -->
                    <div class="p-3.5 bg-zinc-900 border border-zinc-800 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 bg-purple-600 text-white font-mono font-bold text-xs flex items-center justify-center">5</span>
                            <span class="font-bold text-white uppercase text-xs">
                                <span x-show="locale === 'en'">Post-Deploy Migration &amp; Health Check</span>
                                <span x-show="locale !== 'en'">Verifikasi Migrasi Database &amp; Health Probe</span>
                            </span>
                        </div>
                        <div class="text-[11px] text-zinc-400 font-sans space-y-1.5 leading-relaxed">
                            <p>
                                <span x-show="locale === 'en'">Once the <strong>Deploy</strong> process in Coolify finishes, open the <strong>Terminal</strong> tab or execute inside the <code>app</code> container:</span>
                                <span x-show="locale !== 'en'">Setelah proses <strong>Deploy</strong> di Coolify selesai, buka tab <strong>Terminal</strong> atau jalankan di container <code>app</code>:</span>
                            </p>
                            <pre class="p-2 bg-black border border-zinc-800 text-purple-300 font-mono text-xs select-all overflow-x-auto">php artisan migrate --force && php artisan db:seed --class=SyntheticDataSeeder --force && php artisan storage:link</pre>
                            <p class="text-zinc-500 font-mono text-[10px]">
                                <span x-show="locale === 'en'">&bull; Health Check Endpoint: <code>https://app.yourdomain.com/api/health</code> (status: OK).</span>
                                <span x-show="locale !== 'en'">&bull; Endpoint Health Check: <code>https://app.domainanda.com/api/health</code> (status: OK).</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 6: URUTAN 5 SPRINT -->
            <div x-show="executionGuideTab === 'sprints'" class="space-y-3">
                <p class="text-xs text-zinc-400 font-sans leading-relaxed">
                    <span x-show="locale === 'en'">The following execution sequence is the industry standard to minimize deviation and guarantee zero technical debt:</span>
                    <span x-show="locale !== 'en'">Urutan eksekusi berikut adalah standar industri untuk meminimalkan deviasi dan menjamin arsitektur bebas utang teknis:</span>
                </p>

                <div class="overflow-x-auto border border-zinc-800">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-zinc-900 text-zinc-100 border-b border-zinc-800 font-black">
                            <tr>
                                <th class="p-2.5 w-24 border-r border-zinc-800">SPRINT</th>
                                <th class="p-2.5 border-r border-zinc-800">
                                    <span x-show="locale === 'en'">WORK FOCUS</span>
                                    <span x-show="locale !== 'en'">FOKUS PEKERJAAN</span>
                                </th>
                                <th class="p-2.5 w-56 border-r border-zinc-800">
                                    <span x-show="locale === 'en'">QUALITY VERIFICATION</span>
                                    <span x-show="locale !== 'en'">VERIFIKASI KUALITAS</span>
                                </th>
                                <th class="p-2.5 w-32">GUARDRAILS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800 bg-black font-mono text-[11px]">
                            <tr>
                                <td class="p-2.5 font-black text-emerald-400 border-r border-zinc-800">Sprint 1</td>
                                <td class="p-2.5 text-zinc-300 border-r border-zinc-800 font-sans">
                                    <span x-show="locale === 'en'"><strong>Database &amp; Model:</strong> Table migrations with <code>->ulid('id')->primary()</code>, relational <code>foreignUlid()</code>, Eloquent Model with <code>HasUlids</code> trait.</span>
                                    <span x-show="locale !== 'en'"><strong>Database &amp; Model:</strong> Migration tabel dengan <code>->ulid('id')->primary()</code>, relasi <code>foreignUlid()</code>, Model Eloquent dengan trait <code>HasUlids</code>.</span>
                                </td>
                                <td class="p-2.5 border-r border-zinc-800 text-zinc-400"><code>php artisan migrate</code></td>
                                <td class="p-2.5 text-emerald-400 font-bold">Strict ULID</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-black text-emerald-400 border-r border-zinc-800">Sprint 2</td>
                                <td class="p-2.5 text-zinc-300 border-r border-zinc-800 font-sans">
                                    <span x-show="locale === 'en'"><strong>Backend Service &amp; API:</strong> Business service classes, strict FormRequest validation, REST API endpoints with Keyset Cursor Pagination O(1).</span>
                                    <span x-show="locale !== 'en'"><strong>Backend Service &amp; API:</strong> Service class bisnis, FormRequest validasi ketat, endpoint REST API dengan Keyset Cursor Pagination O(1).</span>
                                </td>
                                <td class="p-2.5 border-r border-zinc-800 text-zinc-400"><code>php artisan test</code> (Pest)</td>
                                <td class="p-2.5 text-emerald-400 font-bold">Keyset O(1)</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-black text-emerald-400 border-r border-zinc-800">Sprint 3</td>
                                <td class="p-2.5 text-zinc-300 border-r border-zinc-800 font-sans">
                                    <span x-show="locale === 'en'"><strong>Frontend UI / Islands:</strong> Clean Solid Brutalist UI, subtle rounded corners (<code>rounded-none/rounded-sm</code>), dark/light mode fidelity, centralized toast (no native alerts).</span>
                                    <span x-show="locale !== 'en'"><strong>Frontend UI / Islands:</strong> Antarmuka Clean Solid Brutalist, subtle round corner (<code>rounded-none/rounded-sm</code>), dark/light mode, toast central (tanpa native alert).</span>
                                </td>
                                <td class="p-2.5 border-r border-zinc-800 text-zinc-400">
                                    <span x-show="locale === 'en'">Browser interaction tests</span>
                                    <span x-show="locale !== 'en'">Uji interaksi browser</span>
                                </td>
                                <td class="p-2.5 text-emerald-400 font-bold">Anti-AI-Slop</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-black text-emerald-400 border-r border-zinc-800">Sprint 4</td>
                                <td class="p-2.5 text-zinc-300 border-r border-zinc-800 font-sans">
                                    <span x-show="locale === 'en'"><strong>Security &amp; Guardrails:</strong> Honeypot anti-bot, rate limiting, RFC 9457 Problem Details error handling, and tamper-evident SHA-256 audit trail.</span>
                                    <span x-show="locale !== 'en'"><strong>Security &amp; Guardrails:</strong> Honeypot anti-bot, rate limiting, error RFC 9457 Problem Details, dan audit trail SHA-256.</span>
                                </td>
                                <td class="p-2.5 border-r border-zinc-800 text-zinc-400">
                                    <span x-show="locale === 'en'">Security &amp; regression tests</span>
                                    <span x-show="locale !== 'en'">Security &amp; regression test</span>
                                </td>
                                <td class="p-2.5 text-emerald-400 font-bold">UU PDP / OWASP</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-black text-emerald-400 border-r border-zinc-800">Sprint 5</td>
                                <td class="p-2.5 text-zinc-300 border-r border-zinc-800 font-sans">
                                    <span x-show="locale === 'en'"><strong>Build &amp; Deploy:</strong> <code>npm run build</code>, verify health check probes <code>/api/health</code>, and run deployment script <code>./deploy.sh 1|2</code>.</span>
                                    <span x-show="locale !== 'en'"><strong>Build &amp; Deploy:</strong> <code>npm run build</code>, verifikasi health check probes <code>/api/health</code>, dan jalankan skrip deployment <code>./deploy.sh 1|2</code>.</span>
                                </td>
                                <td class="p-2.5 border-r border-zinc-800 text-zinc-400"><code>./deploy.sh [1-6]</code></td>
                                <td class="p-2.5 text-emerald-400 font-bold">Zero-Downtime</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 6: STATUS BACKGROUND JOB SCAFFOLD -->
            <div x-show="executionGuideTab === 'job-status'" class="space-y-4">
                <div class="p-3 bg-zinc-900 border border-zinc-800 text-zinc-300">
                    <div class="font-bold text-emerald-400 mb-1">
                        <span x-show="locale === 'en'">⚡ Background Job Scaffold Generator:</span>
                        <span x-show="locale !== 'en'">⚡ Background Job Scaffold Generator:</span>
                    </div>
                    <p class="font-sans leading-relaxed text-xs">
                        <span x-show="locale === 'en'">Generation of 22+ files and compilation of ZIP archive can be processed asynchronously in the server queue (Background Queue Job) for maximum efficiency without blocking the browser or HTTP request threads.</span>
                        <span x-show="locale !== 'en'">Pembuatan 22+ file dan kompilasi arsip ZIP dapat diproses secara asinkron di antrean server (Background Queue Job) agar efisien tanpa membebani browser atau thread request HTTP.</span>
                    </p>
                </div>

                <!-- Status Card -->
                <div class="p-4 bg-black border border-zinc-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-none inline-block" :class="{
                                'bg-zinc-600': scaffoldJobStatus === 'idle',
                                'bg-amber-400 animate-pulse': scaffoldJobStatus === 'queued' || scaffoldJobStatus === 'processing',
                                'bg-emerald-500': scaffoldJobStatus === 'completed',
                                'bg-rose-500': scaffoldJobStatus === 'failed'
                            }"></span>
                            <span class="font-bold uppercase text-xs" x-text="'STATUS: ' + scaffoldJobStatus.toUpperCase()">STATUS: IDLE</span>
                        </div>
                        <span class="text-zinc-500 text-[11px]" x-text="scaffoldJobProgress + '%'">0%</span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-zinc-900 h-2 border border-zinc-800 overflow-hidden">
                        <div class="bg-emerald-500 h-full transition-all duration-300" :style="'width: ' + scaffoldJobProgress + '%'"></div>
                    </div>

                    <!-- Status Message -->
                    <div class="text-xs text-zinc-300 font-mono" x-text="scaffoldJobMessage || (locale === 'en' ? 'Ready to process background job anytime.' : 'Siap memproses background job kapan saja.')">
                        Siap memproses background job kapan saja.
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-zinc-900">
                        <button 
                            type="button" 
                            @click="startBackgroundScaffoldGeneration(false)"
                            :disabled="scaffoldJobStatus === 'processing'"
                            class="px-3.5 py-1.5 bg-emerald-500 hover:bg-emerald-400 disabled:opacity-50 text-black font-black uppercase text-xs rounded-none cursor-pointer transition flex items-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span x-show="locale === 'en'">RUN QUEUED JOB</span>
                            <span x-show="locale !== 'en'">JALANKAN QUEUE JOB</span>
                        </button>

                        <button 
                            type="button" 
                            @click="startBackgroundScaffoldGeneration(true)"
                            :disabled="scaffoldJobStatus === 'processing'"
                            class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-400 text-black font-black uppercase text-xs rounded-none cursor-pointer transition flex items-center gap-1.5 shadow-sm"
                            :title="locale === 'en' ? 'Execute synchronously inline without waiting for background worker daemon' : 'Eksekusi langsung secara instan tanpa menunggu worker antrean server'"
                        >
                            <span>⚡</span>
                            <span x-show="locale === 'en'">PROCEED INSTANT (SYNC RUN)</span>
                            <span x-show="locale !== 'en'">PROSES INSTAN SEKARANG</span>
                        </button>

                        <button 
                            type="button" 
                            @click="pollScaffoldJobStatus()"
                            class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold uppercase text-xs rounded-none border border-zinc-700 cursor-pointer transition flex items-center gap-1.5"
                            :title="locale === 'en' ? 'Check latest job status from server' : 'Periksa status terbaru dari server'"
                        >
                            <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            <span x-show="locale === 'en'">REFRESH STATUS</span>
                            <span x-show="locale !== 'en'">CEK STATUS</span>
                        </button>

                        <a 
                            x-show="scaffoldJobStatus === 'completed' || {{ !empty($blueprint->user_metadata['scaffold_ready']) ? 'true' : 'false' }}"
                            href="{{ route('blueprint.export-scaffold', $blueprint->slug) }}" 
                            class="px-3.5 py-1.5 bg-sky-500 hover:bg-sky-400 text-black font-black uppercase text-xs rounded-none transition flex items-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span x-show="locale === 'en'">DOWNLOAD ZIP ARCHIVE NOW (.ZIP)</span>
                            <span x-show="locale !== 'en'">UNDUH ARSIP ZIP SEKARANG (.ZIP)</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="border-t border-zinc-800 pt-3 mt-4 flex items-center justify-between text-xs font-mono text-zinc-500 shrink-0">
            <div>
                <span x-show="locale === 'en'">SOFTWARE FACTORY OS STANDARD &bull; 100% REPRODUCIBLE</span>
                <span x-show="locale !== 'en'">STANDAR SOFTWARE FACTORY OS &bull; 100% REPRODUCIBLE</span>
            </div>
            <button 
                type="button" 
                @click="showExecutionGuideModal = false"
                class="px-3 py-1 bg-zinc-900 hover:bg-zinc-800 text-zinc-300 font-bold uppercase rounded-none border border-zinc-800 cursor-pointer"
            >
                <span x-show="locale === 'en'">CLOSE</span>
                <span x-show="locale !== 'en'">TUTUP</span>
            </button>
        </div>
    </div>
</div>
