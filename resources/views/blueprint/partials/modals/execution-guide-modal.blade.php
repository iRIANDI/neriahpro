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
                        <span>PETA JALAN &amp; PANDUAN EKSEKUSI DEVELOPER</span>
                        <span class="px-2 py-0.5 text-[10px] bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 font-bold">WORLD-CLASS OS</span>
                    </h3>
                    <p class="text-xs text-zinc-400 font-mono mt-0.5">
                        Guardrails, 3 Pilihan Cara Mulai, Setup Docker, dan Eksekusi dengan ECC (.agents/)
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
                <span>🚀 PILIHAN A: Sesi Chat Antigravity</span>
            </button>
            <button 
                type="button" 
                @click="executionGuideTab = 'choice-b'" 
                :class="executionGuideTab === 'choice-b' ? 'bg-emerald-500 text-black font-black' : 'bg-zinc-900 text-zinc-400 hover:text-white'" 
                class="px-3 py-1.5 transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 rounded-none"
            >
                <span>📦 PILIHAN B: Standalone Proyek Baru</span>
            </button>
            <button 
                type="button" 
                @click="executionGuideTab = 'choice-c'" 
                :class="executionGuideTab === 'choice-c' ? 'bg-emerald-500 text-black font-black' : 'bg-zinc-900 text-zinc-400 hover:text-white'" 
                class="px-3 py-1.5 transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 rounded-none"
            >
                <span>🎯 PILIHAN C: AI Cockpit Bertahap</span>
            </button>
            <button 
                type="button" 
                @click="executionGuideTab = 'docker'" 
                :class="executionGuideTab === 'docker' ? 'bg-sky-500 text-black font-black' : 'bg-zinc-900 text-zinc-400 hover:text-white'" 
                class="px-3 py-1.5 transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 rounded-none"
            >
                <span>🐳 Docker Quickstart</span>
            </button>
            <button 
                type="button" 
                @click="executionGuideTab = 'sprints'" 
                :class="executionGuideTab === 'sprints' ? 'bg-amber-500 text-black font-black' : 'bg-zinc-900 text-zinc-400 hover:text-white'" 
                class="px-3 py-1.5 transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 rounded-none"
            >
                <span>📊 Urutan 5 Sprint</span>
            </button>
            <button 
                type="button" 
                @click="executionGuideTab = 'job-status'" 
                :class="executionGuideTab === 'job-status' ? 'bg-emerald-500 text-black font-black' : 'bg-zinc-900 text-zinc-400 hover:text-white'" 
                class="px-3 py-1.5 transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 rounded-none"
            >
                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-none animate-pulse"></span>
                <span>⚡ Status Background Job Scaffold</span>
            </button>
        </div>

        <!-- Scrollable Modal Body -->
        <div class="overflow-y-auto flex-1 pr-1.5 space-y-4 font-mono text-xs">
            
            <!-- TAB 1: PILIHAN A (CHAT ANTIGRAVITY) -->
            <div x-show="executionGuideTab === 'choice-a'" class="space-y-4">
                <div class="p-3 bg-zinc-900 border border-zinc-800 text-zinc-300">
                    <div class="font-bold text-emerald-400 mb-1">🚀 Rekomendasi Paling Cepat &amp; Praktis:</div>
                    <p class="font-sans leading-relaxed text-xs">
                        Karena Anda saat ini <strong>sudah berada di dalam Antigravity IDE</strong> dan agen ini memiliki akses langsung ke codebase proyek, Anda bisa langsung memerintahkan agen di sesi chat ini tanpa setup tambahan.
                    </p>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-300 font-bold uppercase text-[11px]">Prompt Perintah Mulai Sprint 1 (Fondasi Database &amp; Model):</span>
                        <button 
                            type="button" 
                            @click="copyExecutionPrompt(`Antigravity, tolong baca Blueprint '${bizTitle}'. Mulai eksekusi Sprint 1: Buat migration PostgreSQL Strict ULID dan Model Eloquent untuk modul ${firstModule} (${firstTables}) sesuai spesifikasi Section 05. Pastikan menggunakan ->ulid('id')->primary() dan trait HasUlids, lalu jalankan php artisan migrate.`, 'PROMPT SPRINT 1 DISALIN')"
                            class="px-2.5 py-1 bg-emerald-500 hover:bg-emerald-400 text-black font-black uppercase text-[10px] rounded-none cursor-pointer flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            <span>SALIN PROMPT</span>
                        </button>
                    </div>
                    <pre class="p-3.5 bg-black border border-zinc-800 text-emerald-400 overflow-x-auto select-all leading-relaxed whitespace-pre-wrap text-xs">Antigravity, tolong baca Blueprint '{{ $bizTitle }}'. Mulai eksekusi Sprint 1: Buat migration PostgreSQL Strict ULID dan Model Eloquent untuk modul {{ $firstModule }} ({{ $firstTables }}) sesuai spesifikasi Section 05. Pastikan menggunakan ->ulid('id')->primary() dan trait HasUlids, lalu jalankan php artisan migrate.</pre>
                </div>

                <div class="p-3 bg-zinc-900 border border-zinc-800 text-zinc-300 space-y-2">
                    <div class="font-bold text-white uppercase text-[11px]">Apa yang otomatis dikerjakan Antigravity?</div>
                    <ol class="list-decimal list-inside space-y-1 text-zinc-400 font-sans text-xs">
                        <li>Membaca Section 05 (ERD Schema DDL) dari database blueprint ini.</li>
                        <li>Menulis file migrasi Laravel menggunakan <code>->ulid('id')->primary()</code> (Strict ULID 26-karakter).</li>
                        <li>Membuat class Model Eloquent dengan trait <code>HasUlids</code> dan relasi <code>foreignUlid()</code>.</li>
                        <li>Mengeksekusi terminal <code>php artisan migrate</code> dan memverifikasi exit code 0.</li>
                        <li>Setelah Sprint 1 lulus uji, Anda tinggal memerintahkan Sprint 2 (API/Service) dan Sprint 3 (UI).</li>
                    </ol>
                </div>
            </div>

            <!-- TAB 2: PILIHAN B (STANDALONE PROYEK BARU) -->
            <div x-show="executionGuideTab === 'choice-b'" class="space-y-4">
                <div class="p-3 bg-zinc-900 border border-zinc-800 text-zinc-300">
                    <div class="font-bold text-sky-400 mb-1">📦 Standalone App di Folder / Repository Terpisah:</div>
                    <p class="font-sans leading-relaxed text-xs">
                        Gunakan pilihan ini jika Anda ingin mengisolasi aplikasi ini menjadi repositori mandiri yang bersih dari nol.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-2">
                        <div class="text-white font-bold uppercase text-[11px]">Langkah 1: Unduh Paket Scaffold ZIP</div>
                        <p class="text-zinc-400 text-xs font-sans">
                            Klik tombol di bawah untuk mengunduh paket lengkap (22+ berkas arsitektur, Docker, Seeder, dan Rules).
                        </p>
                        <a href="{{ route('blueprint.export-scaffold', $blueprint->slug) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-black font-black uppercase text-[10px] rounded-none">
                            UNDUH FULL ZIP (.ZIP) &darr;
                        </a>
                    </div>

                    <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-2">
                        <div class="text-white font-bold uppercase text-[11px]">Langkah 2: Buka di Antigravity IDE</div>
                        <p class="text-zinc-400 text-xs font-sans">
                            Ekstrak file ZIP ke direktori kerja baru Anda (misal <code>c:\projects\{{ $slugVal }}</code>). Di Antigravity IDE, pilih <strong>File &rarr; Open Folder</strong> lalu pilih folder tersebut.
                        </p>
                    </div>
                </div>

                <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-2">
                    <div class="text-white font-bold uppercase text-[11px]">Langkah 3: File yang Otomatis Terpasang di Proyek Baru</div>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-zinc-400 font-sans text-xs">
                        <li>&bull; <code>.agents/AGENTS.md</code> &amp; <code>.cursorrules</code> (SOP AI)</li>
                        <li>&bull; <code>docker-compose.yml</code> (PHP 8.5+, PG 16, Redis, Nginx)</li>
                        <li>&bull; <code>schema_complete.sql</code> (DDL ERD siap import)</li>
                        <li>&bull; <code>openapi.json</code> &amp; <code>PRODUCT-BRIEF.md</code></li>
                    </ul>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-300 font-bold uppercase text-[11px]">Prompt Perdana di Workspace Standalone Baru:</span>
                        <button 
                            type="button" 
                            @click="copyExecutionPrompt(`Antigravity, pelajari AGENTS.md, PRODUCT-BRIEF.md, dan schema_complete.sql di workspace baru ini. Setup database menggunakan Docker Compose dan mulai implementasi MVP Fase 1.`, 'PROMPT STANDALONE DISALIN')"
                            class="px-2.5 py-1 bg-emerald-500 hover:bg-emerald-400 text-black font-black uppercase text-[10px] rounded-none cursor-pointer flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            <span>SALIN PROMPT</span>
                        </button>
                    </div>
                    <pre class="p-3.5 bg-black border border-zinc-800 text-emerald-400 overflow-x-auto select-all leading-relaxed whitespace-pre-wrap text-xs">Antigravity, pelajari AGENTS.md, PRODUCT-BRIEF.md, dan schema_complete.sql di workspace baru ini. Setup database menggunakan Docker Compose dan mulai implementasi MVP Fase 1.</pre>
                </div>
            </div>

            <!-- TAB 3: PILIHAN C (AI COCKPIT) -->
            <div x-show="executionGuideTab === 'choice-c'" class="space-y-4">
                <div class="p-3 bg-zinc-900 border border-zinc-800 text-zinc-300">
                    <div class="font-bold text-purple-400 mb-1">🎯 Vertical Slice Prompting (Anti Context-Rot):</div>
                    <p class="font-sans leading-relaxed text-xs">
                        Jika Anda ingin menyalin prompt yang sudah dipersiapkan secara presisi per tahapan modul agar AI tidak mengalami <em>context-rot</em> (lupa konteks saat chat menjadi panjang).
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-2">
                        <div class="text-white font-bold uppercase text-[11px]">1. Buka AI Cockpit</div>
                        <p class="text-zinc-400 text-xs font-sans">
                            Klik tombol <code>PROMPT AGENT</code> di toolbar atau klik tombol di bawah untuk membuka modal orkestrasi per-tahap.
                        </p>
                        <button type="button" @click="showExecutionGuideModal = false; showAiPromptModal = true;" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-500 text-white font-black uppercase text-[10px] rounded-none cursor-pointer">
                            BUKA MODAL COCKPIT &rarr;
                        </button>
                    </div>

                    <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-2">
                        <div class="text-white font-bold uppercase text-[11px]">2. Pilih Target Agen</div>
                        <p class="text-zinc-400 text-xs font-sans">
                            Mendukung Antigravity IDE (Google DeepMind), Cursor Composer, Claude Code CLI, atau Windsurf Cascade.
                        </p>
                    </div>

                    <div class="p-3 bg-zinc-900 border border-zinc-800 space-y-2">
                        <div class="text-white font-bold uppercase text-[11px]">3. Salin Prompt per Tahapan</div>
                        <p class="text-zinc-400 text-xs font-sans">
                            Salin prompt tahap 1 (DB &amp; ULID), tempel ke AI, jalankan verifikasi, lalu lanjut ke tahap berikutnya secara teratur.
                        </p>
                    </div>
                </div>
            </div>

            <!-- TAB 4: DOCKER QUICKSTART -->
            <div x-show="executionGuideTab === 'docker'" class="space-y-4">
                <div class="p-3 bg-zinc-900 border border-zinc-800 text-zinc-300">
                    <div class="font-bold text-sky-400 mb-1">🐳 Docker Compose Development Environment:</div>
                    <p class="font-sans leading-relaxed text-xs">
                        Arsip scaffold proyek menyertakan <code>docker-compose.yml</code> enterprise dengan PHP 8.5+, PostgreSQL 16 (Strict Mode), Redis 7, dan Nginx Web Server.
                    </p>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-zinc-300 font-bold uppercase text-[11px]">Perintah Setup Terminal:</span>
                        <button 
                            type="button" 
                            @click="copyExecutionPrompt(`docker compose up -d\ndocker compose exec app composer install\ndocker compose exec app php artisan migrate\ndocker compose exec app php artisan db:seed --class=SyntheticDataSeeder\ndocker compose exec app npm install\ndocker compose exec app npm run build`, 'PERINTAH DOCKER DISALIN')"
                            class="px-2.5 py-1 bg-sky-500 hover:bg-sky-400 text-black font-black uppercase text-[10px] rounded-none cursor-pointer"
                        >
                            SALIN PERINTAH DOCKER
                        </button>
                    </div>
                    <pre class="p-3.5 bg-black border border-zinc-800 text-sky-300 overflow-x-auto select-all leading-relaxed whitespace-pre text-xs"># 1. Jalankan container services di latar belakang
docker compose up -d

# 2. Masuk ke container dan pasang dependensi backend
docker compose exec app composer install

# 3. Jalankan migrasi database PostgreSQL Strict ULID
docker compose exec app php artisan migrate

# 4. Bangkitkan data realistis sintetis
docker compose exec app php artisan db:seed --class=SyntheticDataSeeder

# 5. Pasang dependensi frontend dan build assets
docker compose exec app npm install && docker compose exec app npm run build

# 6. Uji kesehatan server (Health Check Probe)
curl http://localhost:8000/api/health</pre>
                </div>

                <div class="p-3 bg-zinc-900 border border-zinc-800 text-zinc-400 space-y-1 text-xs font-sans">
                    <div class="text-white font-bold font-mono uppercase text-[11px]">Port Konfigurasi Docker:</div>
                    <div>&bull; Nginx Web Server: <code>http://localhost:8000</code></div>
                    <div>&bull; PostgreSQL 16: <code>localhost:5432</code> (User: <code>neriah</code>, DB: <code>{{ $slugVal }}</code>)</div>
                    <div>&bull; Redis 7: <code>localhost:6379</code></div>
                </div>
            </div>

            <!-- TAB 5: URUTAN 5 SPRINT -->
            <div x-show="executionGuideTab === 'sprints'" class="space-y-3">
                <p class="text-xs text-zinc-400 font-sans leading-relaxed">
                    Urutan eksekusi berikut adalah standar industri untuk meminimalkan deviasi dan menjamin arsitektur bebas utang teknis:
                </p>

                <div class="overflow-x-auto border border-zinc-800">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-zinc-900 text-zinc-100 border-b border-zinc-800 font-black">
                            <tr>
                                <th class="p-2.5 w-24 border-r border-zinc-800">SPRINT</th>
                                <th class="p-2.5 border-r border-zinc-800">FOKUS PEKERJAAN</th>
                                <th class="p-2.5 w-56 border-r border-zinc-800">VERIFIKASI KUALITAS</th>
                                <th class="p-2.5 w-32">GUARDRAILS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800 bg-black font-mono text-[11px]">
                            <tr>
                                <td class="p-2.5 font-black text-emerald-400 border-r border-zinc-800">Sprint 1</td>
                                <td class="p-2.5 text-zinc-300 border-r border-zinc-800 font-sans">
                                    <strong>Database &amp; Model:</strong> Migration tabel dengan <code>->ulid('id')->primary()</code>, relasi <code>foreignUlid()</code>, Model Eloquent dengan trait <code>HasUlids</code>.
                                </td>
                                <td class="p-2.5 border-r border-zinc-800 text-zinc-400"><code>php artisan migrate</code></td>
                                <td class="p-2.5 text-emerald-400 font-bold">Strict ULID</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-black text-emerald-400 border-r border-zinc-800">Sprint 2</td>
                                <td class="p-2.5 text-zinc-300 border-r border-zinc-800 font-sans">
                                    <strong>Backend Service &amp; API:</strong> Service class bisnis, FormRequest validasi ketat, endpoint REST API dengan Keyset Cursor Pagination O(1).
                                </td>
                                <td class="p-2.5 border-r border-zinc-800 text-zinc-400"><code>php artisan test</code> (Pest)</td>
                                <td class="p-2.5 text-emerald-400 font-bold">Keyset O(1)</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-black text-emerald-400 border-r border-zinc-800">Sprint 3</td>
                                <td class="p-2.5 text-zinc-300 border-r border-zinc-800 font-sans">
                                    <strong>Frontend UI / Islands:</strong> Antarmuka Clean Solid Brutalist, subtle round corner (<code>rounded-none/rounded-sm</code>), dark/light mode, toast central (tanpa native alert).
                                </td>
                                <td class="p-2.5 border-r border-zinc-800 text-zinc-400">Uji interaksi browser</td>
                                <td class="p-2.5 text-emerald-400 font-bold">Anti-AI-Slop</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-black text-emerald-400 border-r border-zinc-800">Sprint 4</td>
                                <td class="p-2.5 text-zinc-300 border-r border-zinc-800 font-sans">
                                    <strong>Security &amp; Guardrails:</strong> Honeypot anti-bot, rate limiting, error RFC 9457 Problem Details, dan audit trail SHA-256.
                                </td>
                                <td class="p-2.5 border-r border-zinc-800 text-zinc-400">Security &amp; regression test</td>
                                <td class="p-2.5 text-emerald-400 font-bold">UU PDP / OWASP</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-black text-emerald-400 border-r border-zinc-800">Sprint 5</td>
                                <td class="p-2.5 text-zinc-300 border-r border-zinc-800 font-sans">
                                    <strong>Build &amp; Deploy:</strong> <code>npm run build</code>, verifikasi health check probes <code>/api/health</code>, dan jalankan skrip deployment <code>./deploy.sh 1|2</code>.
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
                    <div class="font-bold text-emerald-400 mb-1">⚡ Background Job Scaffold Generator:</div>
                    <p class="font-sans leading-relaxed text-xs">
                        Pembuatan 22+ file dan kompilasi arsip ZIP dapat diproses secara asinkron di antrean server (Background Queue Job) agar efisien tanpa membebani browser atau thread request HTTP.
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
                    <div class="text-xs text-zinc-300 font-mono" x-text="scaffoldJobMessage || 'Siap memproses background job kapan saja.'">
                        Siap memproses background job kapan saja.
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-zinc-900">
                        <button 
                            type="button" 
                            @click="startBackgroundScaffoldGeneration()"
                            :disabled="scaffoldJobStatus === 'processing' || scaffoldJobStatus === 'queued'"
                            class="px-3.5 py-1.5 bg-emerald-500 hover:bg-emerald-400 disabled:opacity-50 text-black font-black uppercase text-xs rounded-none cursor-pointer transition flex items-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span>JALANKAN BACKGROUND JOB GENERATOR</span>
                        </button>

                        <a 
                            x-show="scaffoldJobStatus === 'completed' || {{ !empty($blueprint->user_metadata['scaffold_ready']) ? 'true' : 'false' }}"
                            href="{{ route('blueprint.export-scaffold', $blueprint->slug) }}" 
                            class="px-3.5 py-1.5 bg-sky-500 hover:bg-sky-400 text-black font-black uppercase text-xs rounded-none transition flex items-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>UNDUH ARSIP ZIP SEKARANG (.ZIP)</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="border-t border-zinc-800 pt-3 mt-4 flex items-center justify-between text-xs font-mono text-zinc-500 shrink-0">
            <div>
                STANDAR SOFTWARE FACTORY OS &bull; 100% REPRODUCIBLE
            </div>
            <button 
                type="button" 
                @click="showExecutionGuideModal = false"
                class="px-3 py-1 bg-zinc-900 hover:bg-zinc-800 text-zinc-300 font-bold uppercase rounded-none border border-zinc-800 cursor-pointer"
            >
                TUTUP
            </button>
        </div>
    </div>
</div>
