            <section id="section-3-5" class="bg-white dark:bg-zinc-900 border-2 border-emerald-500/50 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid prd-scroll-target">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 border-b border-zinc-200 dark:border-zinc-800 pb-4">
                    <div class="flex items-center gap-2 cursor-pointer select-none" @click="toggleSection('section-3-5')">
                        <span class="w-6 h-6 bg-emerald-500 text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">04</span>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">
                                <span x-show="locale === 'en'">AI Developer Education Hub &amp; Interactive Sprint Cockpit</span>
                                <span x-show="locale !== 'en'">Pusat Edukasi Developer AI &amp; Interactive Sprint Cockpit</span>
                            </h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">Panduan taktis membimbing AI Agent (Cursor Composer, Claude Code CLI, Windsurf Cascade, Devin, Antigravity IDE) membaca PRD per fitur vertikal secara terpandu sampai tuntas.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 border border-emerald-300 dark:border-emerald-800">
                            VERTICAL SLICE ENGINE // O(1) CONTEXT
                        </span>
                        <button 
                            type="button" 
                            @click.stop="toggleSection('section-3-5')" 
                            class="px-2 py-1 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none flex items-center gap-1 font-mono text-xs border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700"
                            :title="sectionCollapses['section-3-5'] ? 'Buka seksi ini' : 'Ciutkan seksi ini'"
                        >
                            <span class="text-[10px] uppercase font-bold text-zinc-400 dark:text-zinc-500 hidden sm:inline" x-text="sectionCollapses['section-3-5'] ? 'BUKA' : 'CIUTKAN'"></span>
                            <svg class="w-4 h-4 transform transition-transform duration-200" :class="sectionCollapses['section-3-5'] ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Collapsed Preview -->
                <div x-show="sectionCollapses['section-3-5']" @click="toggleSection('section-3-5')" class="py-2.5 px-3 bg-zinc-50 dark:bg-zinc-950/60 border border-dashed border-zinc-300 dark:border-zinc-800 text-xs font-mono text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between cursor-pointer transition select-none mb-6">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Seksi 04 diciutkan (Pusat Edukasi Developer AI &amp; Interactive Sprint Cockpit). Klik untuk membuka...</span>
                    </span>
                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500 uppercase font-bold">[ + EXPAND ]</span>
                </div>

                <div x-show="!sectionCollapses['section-3-5']" x-transition class="accordion-body">

                <!-- Executive Warning & Rationale Against Prompt Dumping -->
                <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 font-mono text-xs mb-6 text-zinc-800 dark:text-zinc-300 space-y-4">
                    <div class="border-b border-zinc-200 dark:border-zinc-800 pb-3">
                        <div class="text-amber-600 dark:text-amber-400 font-bold uppercase text-xs sm:text-sm tracking-wide mb-1">
                            Strategi Orkestrasi AI: Apakah PRD Diberikan Sekaligus atau Sedikit demi Sedikit?
                        </div>
                        <div class="p-2.5 bg-rose-50 dark:bg-rose-500/10 border-l-4 border-rose-500 text-rose-700 dark:text-rose-400 font-black text-xs sm:text-sm uppercase tracking-tight mt-2">
                            🚨 JAWABAN TEGAS: JANGAN PERNAH MEMBERIKAN SELURUH DOKUMEN PRD SEKALIGUS DALAM SATU PROMPT KODING!
                        </div>
                    </div>

                    <p class="font-sans text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed">
                        Memberikan seluruh dokumen PRD (ribuan baris) ke dalam jendela obrolan AI yang sedang mengedit kode aktif adalah <strong>kesalahan paling fatal</strong> yang sering dilakukan developer. Ini adalah penyebab nomor satu mengapa kode menjadi berantakan, amnesia migrasi, dan banyak file terhapus secara tidak sengaja.
                    </p>

                    <!-- 4 Technical Breakdown Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                        <div class="p-3 bg-white dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 shadow-sm">
                            <span class="text-rose-600 dark:text-rose-400 font-bold block mb-1">1. Attention Drift &amp; Context Rot:</span>
                            <p class="font-sans text-[11px] text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Walaupun model AI modern memiliki context window besar (200K hingga 2M token), kemampuan penalaran logika menurun seiring bertambahnya token. AI akan mengalami <em>instruction dilution</em> (mengabaikan aturan-aturan kecil di tengah dokumen).
                            </p>
                        </div>
                        <div class="p-3 bg-white dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 shadow-sm">
                            <span class="text-amber-600 dark:text-amber-400 font-bold block mb-1">2. Shallow Code &amp; Mock Implementation:</span>
                            <p class="font-sans text-[11px] text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Jika AI diminta mengimplementasikan 10 fitur sekaligus, AI akan kehabisan token output. Akibatnya, AI mulai memotong kode, meninggalkan komentar berbahaya seperti <code class="text-amber-600 dark:text-amber-300">// TODO: implement logic here</code>, atau membuat fungsi dummy/mock yang tidak bekerja.
                            </p>
                        </div>
                        <div class="p-3 bg-white dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 shadow-sm">
                            <span class="text-sky-600 dark:text-sky-400 font-bold block mb-1">3. Amnesia Migrasi &amp; Regresi:</span>
                            <p class="font-sans text-[11px] text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                AI akan lupa relasi foreign key dari modul yang dibuat 5 menit lalu dan membuat duplikasi fungsi yang memecah kode sebelumnya.
                            </p>
                        </div>
                        <div class="p-3 bg-white dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 shadow-sm">
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold block mb-1">4. Audit Diff yang Mustahil:</span>
                            <p class="font-sans text-[11px] text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                Jika 1 prompt menghasilkan perubahan pada 40 file sekaligus, Anda sebagai manusia tidak akan bisa mereview bug secara teliti sebelum menekan Accept All.
                            </p>
                        </div>
                    </div>

                    <!-- ASCII Flowchart Diagram -->
                    <div class="pt-2">
                        <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400 mb-1.5">
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold uppercase text-[11px]">Metodologi Terbaik: &quot;Vertical Slice Prompting&quot; (Per Fitur Vertikal)</span>
                            <span class="text-[10px] text-zinc-500 font-mono">STANDAR EMAS NERIAH PRO</span>
                        </div>
                        <pre class="bg-zinc-900 dark:bg-black p-4 text-[11px] text-emerald-400/95 overflow-x-auto select-all leading-relaxed whitespace-pre font-mono border border-zinc-800">+---------------------------------------------------------------------------------+
|                        ALUR KERJA ORKESTRASI AI AGENT                           |
+---------------------------------------------------------------------------------+
|                                                                                 |
|  [ LANGKAH 1: FONDASI GLOBAL (1 Kali di Awal) ]                                 |
|  - Input ke AI: Bab 5 (ERD Schema), Bab 5.6 (Sync Spec), Bab 6 (Tech Stack)     |
|  - Instruksi AI: &quot;Buat migrasi database, model ULID, dan setup base project&quot;    |
|  - Verifikasi: Jalankan `php artisan migrate` -&gt; Commit Git                     |
|                                                                                 |
|  [ LANGKAH 2: EKSEKUSI PER FITUR (Iterasi Berulang) ]                           |
|  - Buka kartu fitur PRD (misal: FEAT-MVP-01)                                    |
|  - Klik tombol &quot;Salin Prompt Handoff AI Code Agent&quot; yang sudah tersedia         |
|  - Paste ke Cursor / Claude Code / Antigravity                                  |
|  - AI hanya bekerja di 3-4 file yang ditentukan (Model -&gt; Controller -&gt; UI)     |
|  - Verifikasi: Jalankan `php artisan test` -&gt; Commit Git                        |
|                                                                                 |
|  [ LANGKAH 3: FITUR SELANJUTNYA ]                                               |
|  - Ambil kartu fitur berikutnya (FEAT-MVP-02)                                   |
|  - Ulangi Langkah 2                                                             |
|                                                                                 |
+---------------------------------------------------------------------------------+</pre>
                    </div>

                    <!-- 3 Benefits Highlights -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-1 text-[11px]">
                        <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300">
                            <strong class="block text-emerald-700 dark:text-emerald-400 font-bold mb-0.5">&check; Zero Context-Rot:</strong>
                            <span class="text-zinc-700 dark:text-zinc-300">AI fokus 100% pada satu masalah spesifik dalam batasan file yang ketat.</span>
                        </div>
                        <div class="p-2.5 bg-sky-50 dark:bg-sky-950/40 border border-sky-300 dark:border-sky-500/30 text-sky-800 dark:text-sky-300">
                            <strong class="block text-sky-700 dark:text-sky-400 font-bold mb-0.5">&check; Kualitas Kode Penuh:</strong>
                            <span class="text-zinc-700 dark:text-zinc-300">Tidak ada pemotongan kode atau // TODO. AI menuliskan validasi lengkap.</span>
                        </div>
                        <div class="p-2.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-500/30 text-amber-800 dark:text-amber-300">
                            <strong class="block text-amber-700 dark:text-amber-400 font-bold mb-0.5">&check; Troubleshooting Mudah:</strong>
                            <span class="text-zinc-700 dark:text-zinc-300">Jika error, Anda tahu persis modul mana yang bermasalah. Riwayat Git rapi per fitur.</span>
                        </div>
                    </div>

                    <div class="text-[11px] text-zinc-600 dark:text-zinc-400 italic pt-1 border-t border-zinc-200 dark:border-zinc-800">
                        &quot;Di dalam halaman <code class="text-emerald-600 dark:text-emerald-400">show.blade.php</code> pada setiap kartu fitur (Bab 3), tim kami telah menyediakan tombol <strong>&apos;Salin Prompt Handoff AI Code Agent&apos;</strong> yang siap Anda gunakan untuk disalin ke AI Agent per fitur secara terpandu.&quot;
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- INTERACTIVE SPRINT EXECUTION COCKPIT (THE LIVE STEP-BY-STEP ORCHESTRATOR) -->
                <!-- ========================================================================= -->
                <div class="bg-white dark:bg-zinc-950 border-2 border-emerald-600 dark:border-emerald-500/60 p-5 sm:p-6 mb-8 rounded-none font-mono text-xs text-zinc-900 dark:text-zinc-100 shadow-sm">
                    <!-- Cockpit Progress Top Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-emerald-500 inline-block animate-pulse"></span>
                            <span class="font-bold text-zinc-900 dark:text-white uppercase text-xs tracking-wider">COCKPIT PELAKSANAAN SPRINT AI DEVELOPER</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold">
                                PROGRES SPRINT: <span x-text="getDevCompletedCount()"></span> / {{ $totalDevSteps }} TAHAP SELESAI (<span x-text="getDevProgressPercentage()"></span>%)
                            </span>
                            <button 
                                type="button" 
                                @click="resetDevProgress()" 
                                class="text-[10px] text-zinc-500 dark:text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 underline transition"
                                title="Reset data pengerjaan sprint lokal"
                            >
                                Reset Progres
                            </button>
                        </div>
                    </div>

                    <!-- Visual Progress Bar -->
                    <div class="w-full bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 h-2.5 overflow-hidden mb-5">
                        <div class="bg-gradient-to-r from-emerald-600 via-emerald-500 to-sky-400 h-full transition-all duration-300" :style="'width: ' + getDevProgressPercentage() + '%'"></div>
                    </div>

                    <!-- Step Selection Tabs (Horizontal Scrollable) -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-3 mb-5 border-b border-zinc-200 dark:border-zinc-800 scrollbar-thin">
                        <!-- Step 0: Fondasi Global -->
                        <button 
                            type="button" 
                            @click="devActiveStep = 0"
                            :class="devActiveStep === 0 ? 'bg-emerald-500 text-black font-bold border-emerald-400' : (isStepCompleted('step_foundation') ? 'bg-emerald-50 dark:bg-zinc-900 text-emerald-700 dark:text-emerald-400 border-emerald-500/40' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800 hover:text-zinc-900 dark:hover:text-zinc-200')"
                            class="px-3 py-1.5 border text-xs whitespace-nowrap flex items-center gap-1.5 transition shrink-0"
                        >
                            <span x-show="isStepCompleted('step_foundation')" class="text-xs">&check;</span>
                            <span>01. FONDASI GLOBAL</span>
                        </button>

                        <!-- Step 1..N: Each MVP Feature -->
                        @foreach($mvpEngineeringSpecs as $specIdx => $spec)
                            @php
                                $sKey = $spec['feature_id'] ?? ($spec['id'] ?? ('FEAT-SPEC-' . $specIdx));
                            @endphp
                            <button 
                                type="button" 
                                @click="devActiveStep = {{ $specIdx + 1 }}"
                                :class="devActiveStep === {{ $specIdx + 1 }} ? 'bg-emerald-500 text-black font-bold border-emerald-400' : (isStepCompleted('{{ $sKey }}') ? 'bg-emerald-50 dark:bg-zinc-900 text-emerald-700 dark:text-emerald-400 border-emerald-500/40' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800 hover:text-zinc-900 dark:hover:text-zinc-200')"
                                class="px-3 py-1.5 border text-xs whitespace-nowrap flex items-center gap-1.5 transition shrink-0"
                            >
                                <span x-show="isStepCompleted('{{ $sKey }}')" class="text-xs">&check;</span>
                                <span>{{ sprintf('%02d', $specIdx + 2) }}. {{ $spec['id'] ?? ('FITUR ' . ($specIdx + 1)) }}</span>
                            </button>
                        @endforeach

                        <!-- Step Final: Quality Gate & Deploy -->
                        <button 
                            type="button" 
                            @click="devActiveStep = {{ $totalDevSteps - 1 }}"
                            :class="devActiveStep === {{ $totalDevSteps - 1 }} ? 'bg-emerald-500 text-black font-bold border-emerald-400' : (isStepCompleted('step_deployment') ? 'bg-emerald-50 dark:bg-zinc-900 text-emerald-700 dark:text-emerald-400 border-emerald-500/40' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800 hover:text-zinc-900 dark:hover:text-zinc-200')"
                            class="px-3 py-1.5 border text-xs whitespace-nowrap flex items-center gap-1.5 transition shrink-0"
                        >
                            <span x-show="isStepCompleted('step_deployment')" class="text-xs">&check;</span>
                            <span>{{ sprintf('%02d', $totalDevSteps) }}. QUALITY GATE &amp; DEPLOY</span>
                        </button>
                    </div>

                    <!-- ============================================ -->
                    <!-- ACTIVE STEP DETAIL DISPLAY PANE -->
                    <!-- ============================================ -->
                    
                    <!-- PANE 0: LANGKAH 1 - FONDASI GLOBAL (1 Kali di Awal) -->
                    <div x-show="devActiveStep === 0" class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                            <div>
                                <span class="px-2 py-0.5 bg-emerald-500 text-black font-bold text-[10px] uppercase">TAHAP 01 // FONDASI ARSITEKTUR</span>
                                <h3 class="text-sm sm:text-base font-black text-zinc-900 dark:text-white mt-1 uppercase">LANGKAH 1: FONDASI GLOBAL &amp; SETUP BASIS DATA (1 KALI DI AWAL)</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    @click="toggleStepCompleted('step_foundation', 'Langkah 1: Fondasi Global')"
                                    :class="isStepCompleted('step_foundation') ? 'bg-emerald-500 text-black font-bold border-emerald-500' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700 hover:border-emerald-500'"
                                    class="px-3 py-1.5 border text-xs font-mono flex items-center gap-1.5 transition"
                                >
                                    <span class="w-1.5 h-1.5 rounded-none" :class="isStepCompleted('step_foundation') ? 'bg-black' : 'bg-zinc-400 dark:bg-zinc-500'"></span>
                                    <span x-text="isStepCompleted('step_foundation') ? '✓ TAHAP 1 TERVERIFIKASI &amp; COMMITTED' : 'TANDAI TAHAP 1 SELESAI'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- One-Click Scaffold Archive Download (Pilar 3 & 10 Pillars Turnkey Foundation) -->
                        <div class="p-4 bg-emerald-500/10 dark:bg-emerald-950/40 border-2 border-emerald-500 text-xs font-mono space-y-3">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 bg-emerald-500 animate-pulse"></span>
                                        <span class="font-black text-xs sm:text-sm uppercase text-zinc-900 dark:text-zinc-100 tracking-wide">1. UNDUH INITIAL REPO SCAFFOLD (.ZIP 10 PILAR)</span>
                                    </div>
                                    <p class="font-sans text-[11px] text-zinc-600 dark:text-zinc-400 mt-1 leading-relaxed">
                                        Unduh bundel repositori turnkey berisikan seluruh 10 Pilar Software Factory OS: <code>docker-compose.yml</code>, <code>schema_complete.sql</code>, <code>SyntheticDataSeeder.php</code>, <code>ApiContractTest.php</code>, <code>deploy.sh</code>, dan aturan AI agent (<code>.cursorrules</code>, <code>AGENTS.md</code>).
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button 
                                        type="button" 
                                        @click="openScaffoldModal()" 
                                        class="px-3 py-2 bg-white dark:bg-zinc-900 border border-emerald-500/50 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-emerald-700 dark:text-emerald-400 font-bold text-xs uppercase flex items-center justify-center gap-1.5 transition cursor-pointer"
                                        title="Buka Pratinjau File Scaffold Sebelum Unduh"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>PREVIEW SCAFFOLD</span>
                                    </button>
                                    <a 
                                        href="{{ route('blueprint.export-scaffold', $blueprint->slug) }}" 
                                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-black text-xs uppercase flex items-center justify-center gap-2 transition cursor-pointer shadow-none"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        <span>UNDUH REPO SCAFFOLD (.ZIP)</span>
                                    </a>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-2 border-t border-emerald-500/30 text-[11px] font-sans">
                                <div class="p-2.5 bg-white dark:bg-zinc-900 border border-emerald-500/30">
                                    <strong class="text-emerald-700 dark:text-emerald-400 block font-mono text-xs">LANGKAH A: EKSTRAK</strong>
                                    <span class="text-zinc-600 dark:text-zinc-400 text-[11px] leading-relaxed">Ekstrak berkas <code>.zip</code> ini ke folder proyek baru di komputer lokal atau VPS Anda.</span>
                                </div>
                                <div class="p-2.5 bg-white dark:bg-zinc-900 border border-emerald-500/30">
                                    <strong class="text-emerald-700 dark:text-emerald-400 block font-mono text-xs">LANGKAH B: RUN CONTAINER</strong>
                                    <span class="text-zinc-600 dark:text-zinc-400 text-[11px] leading-relaxed">Buka terminal, jalankan <code>docker compose up -d</code> untuk menyalakan engine PostgreSQL 16 &amp; Redis 7.</span>
                                </div>
                                <div class="p-2.5 bg-white dark:bg-zinc-900 border border-emerald-500/30">
                                    <strong class="text-emerald-700 dark:text-emerald-400 block font-mono text-xs">LANGKAH C: JALANKAN PROMPT</strong>
                                    <span class="text-zinc-600 dark:text-zinc-400 text-[11px] leading-relaxed">Salin prompt Fondasi Global di bawah ke IDE Agent (Antigravity/Cursor/Claude Code) untuk build migrasi database.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Target Context Inputs -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-zinc-500 dark:text-zinc-400 text-[10px] block font-bold mb-0.5">DOKUMEN INPUT KE AI:</span>
                                <span class="text-zinc-900 dark:text-zinc-200 text-xs font-bold block">Bab 5 (ERD PostgreSQL ULID)</span>
                                @if($hasSync)
                                    <span class="text-emerald-600 dark:text-emerald-400 text-[10px] block mt-1">+ Bab 5.6 (Sync Engine Spec)</span>
                                @endif
                                <span class="text-sky-600 dark:text-sky-400 text-[10px] block mt-0.5">+ Bab 6 (Tech Stack &amp; VPS)</span>
                            </div>
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-zinc-500 dark:text-zinc-400 text-[10px] block font-bold mb-0.5">TARGET BOUNDED FILES:</span>
                                <span class="text-zinc-700 dark:text-zinc-300 text-[11px] block font-mono">database/migrations/*_create_*.php</span>
                                <span class="text-zinc-700 dark:text-zinc-300 text-[11px] block font-mono">app/Models/*.php</span>
                            </div>
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-zinc-500 dark:text-zinc-400 text-[10px] block font-bold mb-0.5">TERMINAL VERIFICATION GATE:</span>
                                <code class="text-emerald-600 dark:text-emerald-400 text-[11px] block font-mono select-all">php artisan migrate:status</code>
                                <code class="text-amber-600 dark:text-amber-400 text-[11px] block font-mono select-all">git commit -m &quot;chore(db): setup ULID migrations&quot;</code>
                            </div>
                        </div>

                        <!-- Prompt Pre-Crafted Box -->
                        <div class="p-3.5 bg-zinc-50 dark:bg-zinc-900/90 border border-zinc-200 dark:border-zinc-800 space-y-2">
                            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold uppercase text-[11px]">Prompt Fondasi Global Siap Di-Paste ke AI Agent:</span>
                                <div class="flex items-center gap-2">
                                    <button 
                                        type="button" 
                                        onclick="copyCockpitStepPrompt(this, 'prompt-step-foundation')"
                                        class="px-2.5 py-1 bg-emerald-500 text-black font-bold text-xs hover:bg-emerald-400 transition"
                                    >
                                        SALIN PROMPT FONDASI (LANGKAH 1)
                                    </button>
                                </div>
                            </div>
                            <pre id="prompt-step-foundation" class="bg-zinc-950 p-3 text-[11px] text-emerald-300/90 overflow-x-auto select-all leading-relaxed whitespace-pre-wrap font-mono border border-zinc-800">{{ $foundationPrompt }}</pre>
                        </div>

                        <!-- Action Next Footer -->
                        <div class="flex items-center justify-between pt-2 border-t border-zinc-200 dark:border-zinc-800">
                            <button 
                                type="button" 
                                @click="jumpTo('section-5')" 
                                class="text-xs text-sky-600 dark:text-sky-400 hover:text-sky-500 dark:hover:text-sky-300 underline font-mono flex items-center gap-1"
                            >
                                <span>&rarr; Lihat Skema ERD di Bab 5</span>
                            </button>
                            <button 
                                type="button" 
                                @click="if (!isStepCompleted('step_foundation')) { toggleStepCompleted('step_foundation', 'Langkah 1: Fondasi Global'); } devActiveStep = 1;" 
                                class="px-4 py-2 bg-emerald-500 text-black font-bold text-xs hover:bg-emerald-400 transition flex items-center gap-2"
                            >
                                <span>Lanjut ke Langkah 2 (Fitur MVP 01)</span>
                                <span>&rarr;</span>
                            </button>
                        </div>
                    </div>

                    <!-- PANES 1..N: LANGKAH 2 - EKSEKUSI PER FITUR VERTICAL SLICE -->
                    @foreach($mvpEngineeringSpecs as $specIdx => $spec)
                        @php
                            $stepNum = $specIdx + 1;
                            $featKey = $spec['feature_id'] ?? ($spec['id'] ?? ('FEAT-SPEC-' . $specIdx));
                            $craftedSpec = \App\Services\BlueprintDiscoveryService::polishFeatureSpecification($spec, $blueprint->domain_category ?? 'custom_portal', $isEn ?? false);
                            $featTitle = $craftedSpec['title'] ?? ($spec['title'] ?? ('Fitur ' . $stepNum));
                            $cleanSlug = Str::studly(Str::slug($featTitle));
                            $testCmd = 'php artisan test --filter=' . $cleanSlug . 'Test';
                            $promptCodeId = 'prompt-step-feat-' . $specIdx;
                        @endphp
                        <div x-show="devActiveStep === {{ $stepNum }}" class="space-y-4">
                            <!-- Prerequisite Step Dependency Banner -->
                            <div x-show="!isStepCompleted('step_foundation')" class="p-3 bg-amber-500/10 border border-amber-500/40 text-amber-900 dark:text-amber-200 text-xs font-mono flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <div class="flex-1 space-y-1">
                                    <div class="font-bold uppercase tracking-wider text-[11px] text-amber-800 dark:text-amber-300">
                                        {{ $isEn ? '⚠️ PREREQUISITE WARNING: STEP 1 (GLOBAL FOUNDATION) NOT YET VERIFIED' : '⚠️ PERINGATAN DEPENDENSI: LANGKAH 1 (FONDASI GLOBAL) BELUM DIVERIFIKASI' }}
                                    </div>
                                    <p class="text-[11px] font-sans leading-relaxed text-zinc-700 dark:text-zinc-300">
                                        {{ $isEn ? 'This feature depends on baseline ULID database tables, base layouts, and authentication configured in Step 1. We strongly advise completing and verifying Step 1 first to prevent schema collisions.' : 'Fitur ini membutuhkan skema database ULID, layout dasar, dan autentikasi yang dibuat di Langkah 1 (Fondasi Global). Sangat disarankan menyelesaikan Langkah 1 terlebih dahulu untuk mencegah konflik kode.' }}
                                    </p>
                                    <button type="button" @click="devActiveStep = 0" class="text-amber-700 dark:text-amber-400 underline font-bold hover:text-amber-600 text-[11px]">
                                        {{ $isEn ? '← Return to Step 1 (Global Foundation)' : '← Kembali Selesaikan Langkah 1 (Fondasi Global)' }}
                                    </button>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-emerald-500 text-black font-bold text-[10px] uppercase">TAHAP 02.{{ sprintf('%02d', $stepNum) }} // VERTICAL SLICE</span>
                                        <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[10px]">{{ $spec['category'] ?? 'CORE DOMAIN' }}</span>
                                        <span class="px-2 py-0.5 bg-sky-500/10 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 text-[10px]">{{ $spec['complexity_label'] ?? 'Standard' }}</span>
                                    </div>
                                    <h3 class="text-sm sm:text-base font-black text-zinc-900 dark:text-white uppercase">{{ $featKey }}: {{ $featTitle }}</h3>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button 
                                        type="button" 
                                        @click="toggleStepCompleted('{{ $featKey }}', '{{ addslashes($featTitle) }}')"
                                        :class="isStepCompleted('{{ $featKey }}') ? 'bg-emerald-500 text-black font-bold border-emerald-500' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700 hover:border-emerald-500'"
                                        class="px-3 py-1.5 border text-xs font-mono flex items-center gap-1.5 transition"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-none" :class="isStepCompleted('{{ $featKey }}') ? 'bg-black' : 'bg-zinc-400 dark:bg-zinc-500'"></span>
                                        <span x-text="isStepCompleted('{{ $featKey }}') ? '✓ FITUR TERVERIFIKASI &amp; COMMITTED' : 'TANDAI FITUR SELESAI'"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Target Bounded Files & User Story -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-2">
                                    <span class="text-amber-600 dark:text-amber-400 text-[10px] block font-bold">BOUNDED TARGET FILES (ISOLASI FILE AGENT):</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($spec['target_files'] ?? [] as $tf)
                                            @php
                                                $displayTf = \App\Services\PrdGeneratorService::sanitizeFilePath($tf, $spec['title'] ?? '', $spec['related_table'] ?? null);
                                            @endphp
                                            <span class="px-2 py-0.5 bg-white dark:bg-black text-zinc-800 dark:text-zinc-300 text-[10px] font-mono border border-zinc-300 dark:border-zinc-800 select-all">
                                                {{ $displayTf }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-2">
                                    <span class="text-zinc-500 dark:text-zinc-400 text-[10px] block font-bold">USER STORY:</span>
                                    <p class="font-sans text-[11px] text-zinc-700 dark:text-zinc-300 italic leading-relaxed">
                                        &quot;{{ $spec['user_story'] ?? 'Pengguna dapat menjalankan alur kerja ini dengan aman dan tervalidasi.' }}&quot;
                                    </p>
                                    <div class="pt-1 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between text-[10px]">
                                        <span class="text-zinc-500 dark:text-zinc-400">VERIFIKASI TEST:</span>
                                        <code class="text-emerald-600 dark:text-emerald-400 font-mono select-all">{{ $testCmd }}</code>
                                    </div>
                                </div>
                            </div>

                            <!-- Related Table Schema & Acceptance Criteria Grid -->
                            @if(!empty($spec['related_table_data']) || !empty($spec['acceptance_criteria']))
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <!-- Related Table Schema Card -->
                                    @if(!empty($spec['related_table_data']))
                                        @php
                                            $relTbl = $spec['related_table_data'];
                                        @endphp
                                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-emerald-700 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-wider">
                                                    TARGET TABEL ERD: <span class="font-mono text-zinc-900 dark:text-white">{{ $relTbl['name'] ?? ($spec['related_table'] ?? '-') }}</span>
                                                </span>
                                                <span class="px-1.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[9px] font-mono">POSTGRESQL ULID</span>
                                            </div>
                                            <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans leading-tight">
                                                {{ $relTbl['description'] ?? 'Tabel operasional modul inti.' }}
                                            </p>
                                            <div class="space-y-1 max-h-36 overflow-y-auto custom-prd-scrollbar border-t border-zinc-200 dark:border-zinc-800 pt-1.5">
                                                @foreach(array_slice($relTbl['columns'] ?? [], 0, 8) as $col)
                                                    <div class="flex items-center justify-between text-[10px] font-mono py-0.5">
                                                        <span class="text-zinc-800 dark:text-zinc-200">{{ $col['name'] ?? '' }}</span>
                                                        <span class="text-zinc-500 dark:text-zinc-400">{{ $col['type'] ?? '' }}{{ !empty($col['is_index']) ? ' • idx' : '' }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Acceptance Criteria DoD Card -->
                                    @php
                                        $normalizedDod = \App\Services\PrdGeneratorService::normalizeAcceptanceCriteria(
                                            $spec['acceptance_criteria'] ?? [], 
                                            $spec['title'] ?? '', 
                                            $spec['user_story'] ?? ''
                                        );
                                    @endphp
                                    @if(!empty($normalizedDod))
                                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-2">
                                            <span class="text-sky-700 dark:text-sky-400 text-[10px] font-bold uppercase tracking-wider block">
                                                ACCEPTANCE CRITERIA (DEFINITION OF DONE):
                                            </span>
                                            <div class="space-y-2 max-h-40 overflow-y-auto custom-prd-scrollbar text-[11px]">
                                                @foreach($normalizedDod as $acIdx => $acItem)
                                                    <div class="p-2 bg-white dark:bg-black/60 border border-zinc-200 dark:border-zinc-800/80 space-y-1">
                                                        <div class="font-bold text-zinc-900 dark:text-zinc-100 flex items-center justify-between gap-1.5 border-b border-zinc-100 dark:border-zinc-800/60 pb-1">
                                                            <div class="flex items-center gap-1.5">
                                                                <span class="w-1.5 h-1.5 bg-sky-500 inline-block"></span>
                                                                <span class="uppercase text-[11px]">{{ $acItem['scenario'] ?? ('Skenario ' . ($acIdx + 1)) }}</span>
                                                            </div>
                                                            <span class="text-[8px] font-mono px-1.5 py-0.2 bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20 font-bold">VERIFIED</span>
                                                        </div>
                                                        <div class="text-[10px] text-zinc-700 dark:text-zinc-300 font-mono space-y-0.5 pl-2 border-l border-sky-500/40">
                                                            <div><strong class="text-emerald-600 dark:text-emerald-400">Given:</strong> {{ (!empty($acItem['given']) && $acItem['given'] !== '-') ? $acItem['given'] : \App\Services\PrdGeneratorService::deriveGivenCondition($acItem['scenario'] ?? '', $spec['title'] ?? 'Fitur', $acIdx) }}</div>
                                                            <div><strong class="text-sky-600 dark:text-sky-400">When:</strong> {{ (!empty($acItem['when']) && $acItem['when'] !== '-') ? $acItem['when'] : \App\Services\PrdGeneratorService::deriveWhenCondition($acItem['scenario'] ?? '', $spec['title'] ?? 'Fitur', $acIdx) }}</div>
                                                            <div><strong class="text-amber-600 dark:text-amber-400">Then:</strong> {{ (!empty($acItem['then']) && $acItem['then'] !== '-') ? $acItem['then'] : \App\Services\PrdGeneratorService::deriveThenCondition($acItem['scenario'] ?? '', $spec['title'] ?? 'Fitur', $acIdx) }}</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Pre-Crafted Vertical Slice Prompt -->
                            <div class="p-3.5 bg-zinc-50 dark:bg-zinc-900/90 border border-zinc-200 dark:border-zinc-800 space-y-2">
                                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold uppercase text-[11px]">Prompt Directive Siap Di-Paste ke Cursor / Claude Code / Antigravity:</span>
                                    <button 
                                        type="button" 
                                        onclick="copyCockpitStepPrompt(this, '{{ $promptCodeId }}')"
                                        class="px-2.5 py-1 bg-emerald-500 text-black font-bold text-xs hover:bg-emerald-400 transition"
                                    >
                                        SALIN PROMPT FITUR INI
                                    </button>
                                </div>
                                <pre id="{{ $promptCodeId }}" class="bg-zinc-950 p-3 text-[11px] text-emerald-300/90 overflow-x-auto select-all leading-relaxed whitespace-pre-wrap font-mono border border-zinc-800">{{ $spec['code_agent_directive'] ?? ($spec['agent_directive_prompt'] ?? '') }}</pre>
                            </div>

                            <!-- Footer Nav -->
                            <div class="flex items-center justify-between pt-2 border-t border-zinc-200 dark:border-zinc-800">
                                <div class="flex items-center gap-3">
                                    <button 
                                        type="button" 
                                        @click="devActiveStep = {{ $stepNum - 1 }}" 
                                        class="text-xs text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 underline font-mono"
                                    >
                                        &larr; Tahap Sebelumnya
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="jumpTo('card-feat-{{ $specIdx }}')" 
                                        class="text-xs text-sky-600 dark:text-sky-400 hover:text-sky-500 dark:hover:text-sky-300 underline font-mono"
                                    >
                                        &loz; Lihat Kartu Spesifikasi Lengkap (Bab 3)
                                    </button>
                                </div>
                                <button 
                                    type="button" 
                                    @click="if (!isStepCompleted('{{ $featKey }}')) { toggleStepCompleted('{{ $featKey }}', '{{ addslashes($featTitle) }}'); } devActiveStep = {{ $stepNum < count($mvpEngineeringSpecs) ? ($stepNum + 1) : ($totalDevSteps - 1) }};" 
                                    class="px-4 py-2 bg-emerald-500 text-black font-bold text-xs hover:bg-emerald-400 transition flex items-center gap-2"
                                >
                                    <span>Tandai Selesai &amp; Lanjut</span>
                                    <span>&rarr;</span>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    <!-- PANE LAST: LANGKAH AKHIR - AUTOMATED QUALITY GATE & DEPLOYMENT -->
                    <div x-show="devActiveStep === {{ $totalDevSteps - 1 }}" class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                            <div>
                                <span class="px-2 py-0.5 bg-emerald-500 text-black font-bold text-[10px] uppercase">TAHAP AKHIR // PRODUCTION VERIFICATION</span>
                                <h3 class="text-sm sm:text-base font-black text-zinc-900 dark:text-white mt-1 uppercase">LANGKAH 3: AUTOMATED QUALITY GATE &amp; PRODUCTION DEPLOYMENT</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    @click="toggleStepCompleted('step_deployment', 'Langkah Akhir: Quality Gate & Deployment')"
                                    :class="isStepCompleted('step_deployment') ? 'bg-emerald-500 text-black font-bold border-emerald-500' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700 hover:border-emerald-500'"
                                    class="px-3 py-1.5 border text-xs font-mono flex items-center gap-1.5 transition"
                                >
                                    <span class="w-1.5 h-1.5 rounded-none" :class="isStepCompleted('step_deployment') ? 'bg-black' : 'bg-zinc-400 dark:bg-zinc-500'"></span>
                                    <span x-text="isStepCompleted('step_deployment') ? '✓ SELURUH SPRINT PRODUCTION READY' : 'TANDAI QUALITY GATE SELESAI'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- 4 Quality Gate Verification Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-1">
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold block">1. AUTOMATED TESTS</span>
                                <code class="text-zinc-800 dark:text-zinc-300 block text-[11px] font-mono select-all">php artisan test</code>
                                <p class="text-[10px] text-zinc-600 dark:text-zinc-400">Pastikan 100% assertions lulus dengan exit code 0.</p>
                            </div>
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-1">
                                <span class="text-sky-600 dark:text-sky-400 font-bold block">2. ASSET BUNDLE</span>
                                <code class="text-zinc-800 dark:text-zinc-300 block text-[11px] font-mono select-all">npm run build</code>
                                <p class="text-[10px] text-zinc-600 dark:text-zinc-400">Sinkronisasi public/build/manifest.json untuk produksi.</p>
                            </div>
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-1">
                                <span class="text-amber-600 dark:text-amber-400 font-bold block">3. GIT SYNC</span>
                                <code class="text-zinc-800 dark:text-zinc-300 block text-[11px] font-mono select-all">git push origin main</code>
                                <p class="text-[10px] text-zinc-600 dark:text-zinc-400">Sinkronkan seluruh commit riwayat per fitur ke repository.</p>
                            </div>
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-1">
                                <span class="text-rose-600 dark:text-rose-400 font-bold block">4. DEPLOY SCRIPT</span>
                                <code class="text-zinc-800 dark:text-zinc-300 block text-[11px] font-mono select-all">./deploy.sh 6</code>
                                <p class="text-[10px] text-zinc-600 dark:text-zinc-400">Skenario 6 (Assets) atau Skenario 2 (Migrasi Aman).</p>
                            </div>
                        </div>

                        <!-- Quality Gate Completion Banner -->
                        <div class="p-4 bg-emerald-500/10 dark:bg-emerald-950/30 border border-emerald-500/40 dark:border-emerald-500/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <span class="text-emerald-700 dark:text-emerald-400 font-bold text-xs uppercase block">STATUS FINAL SPRINT:</span>
                                <p class="text-zinc-700 dark:text-zinc-300 text-xs font-sans mt-0.5">
                                    Ketika seluruh tahapan telah terverifikasi, aplikasi siap diserahterimakan kepada klien dengan garansi integritas 100% bebas amnesia arsitektur.
                                </p>
                            </div>
                            <button 
                                type="button" 
                                @click="jumpTo('section-10')" 
                                class="px-4 py-2 bg-emerald-500 text-black font-bold text-xs hover:bg-emerald-400 transition shrink-0 uppercase"
                            >
                                Kunci Scope &amp; Serah Terima &rarr;
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tool IDE Selector Tabs (Cursor, Claude Code, Windsurf, Devin, Antigravity) -->
                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                        <span>PLAYBOOK PER TOOL IDE / TERMINAL (PILIH TOOL ANDA):</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-1.5 font-mono text-xs mb-4">
                        <button 
                            type="button" 
                            @click="selectedIdeTool = 'antigravity_ide'" 
                            :class="selectedIdeTool === 'antigravity_ide' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'"
                            class="px-3 py-1.5 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5"
                        >
                            <span>Google Antigravity IDE</span>
                        </button>
                        <button 
                            type="button" 
                            @click="selectedIdeTool = 'cursor'" 
                            :class="selectedIdeTool === 'cursor' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'"
                            class="px-3 py-1.5 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5"
                        >
                            <span>Cursor Composer (Cmd+I)</span>
                        </button>
                        <button 
                            type="button" 
                            @click="selectedIdeTool = 'claude'" 
                            :class="selectedIdeTool === 'claude' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'"
                            class="px-3 py-1.5 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5"
                        >
                            <span>Claude Code CLI</span>
                        </button>
                        <button 
                            type="button" 
                            @click="selectedIdeTool = 'windsurf'" 
                            :class="selectedIdeTool === 'windsurf' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'"
                            class="px-3 py-1.5 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5"
                        >
                            <span>Windsurf Cascade</span>
                        </button>
                        <button 
                            type="button" 
                            @click="selectedIdeTool = 'devin'" 
                            :class="selectedIdeTool === 'devin' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'"
                            class="px-3 py-1.5 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5"
                        >
                            <span>Devin &amp; Copilot Workspace</span>
                        </button>
                    </div>

                    <!-- Antigravity IDE Guide -->
                    <div x-show="selectedIdeTool === 'antigravity_ide'" class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 font-mono text-xs space-y-3">
                        <div class="flex items-center justify-between text-zinc-700 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-800 pb-2">
                            <span class="font-bold text-emerald-700 dark:text-emerald-400 uppercase">1. CARA MENGGUNAKAN DI GOOGLE DEEPMIND ANTIGRAVITY IDE:</span>
                            <span class="text-[10px] text-zinc-500 dark:text-zinc-400">Autonomous Agentic Coding</span>
                        </div>
                        <ol class="list-decimal list-inside space-y-1.5 text-zinc-700 dark:text-zinc-400 text-xs font-sans">
                            <li>Buka workspace di Antigravity IDE. Pastikan file <code class="text-emerald-600 dark:text-emerald-400">.agents/AGENTS.md</code> dan Ponytail Decision Ladder aktif.</li>
                            <li>Buka tab <strong>Cockpit Pelaksanaan Sprint</strong> di atas, pilih tahap yang sedang berjalan (Langkah 1 Fondasi atau Fitur spesifik).</li>
                            <li>Klik tombol <strong>SALIN PROMPT</strong>, lalu tempelkan ke prompt bar Antigravity IDE.</li>
                            <li>AI akan memproses diff secara terisolasi pada target files bounded, menjalankan verifikasi pengujian terminal, dan melaporkan ringkasan perubahan.</li>
                        </ol>
                    </div>

                    <!-- Cursor Composer Tool Guide -->
                    <div x-show="selectedIdeTool === 'cursor'" x-cloak class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 font-mono text-xs space-y-3">
                        <div class="flex items-center justify-between text-zinc-700 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-800 pb-2">
                            <span class="font-bold text-emerald-700 dark:text-emerald-400 uppercase">2. CARA MENGGUNAKAN DI CURSOR COMPOSER (Cmd+I):</span>
                            <span class="text-[10px] text-zinc-500 dark:text-zinc-400">Shortcut: Cmd+I (Mac) / Ctrl+I (Win)</span>
                        </div>
                        <ol class="list-decimal list-inside space-y-1.5 text-zinc-700 dark:text-zinc-400 text-xs font-sans">
                            <li>Buka Cursor Composer dengan menekan <code class="text-emerald-600 dark:text-emerald-400">Cmd+I</code>.</li>
                            <li>Ketik simbol <code class="text-cyan-600 dark:text-cyan-400">@</code> untuk melampirkan file yang menjadi batas target modul (lihat <em>BOUNDED TARGET FILES</em> pada kartu fitur).</li>
                            <li>Salin <strong>PROMPT FITUR</strong> dari Cockpit Pelaksanaan Sprint di atas, lalu tempel ke Composer.</li>
                            <li>Tekan Enter, tinjau perubahan diff baris per baris, dan jalankan perintah verifikasi terminal <code class="text-amber-600 dark:text-amber-400">php artisan test --filter=...</code> sebelum menekan Accept All.</li>
                        </ol>
                    </div>

                    <!-- Claude Code CLI Tool Guide -->
                    <div x-show="selectedIdeTool === 'claude'" x-cloak class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 font-mono text-xs space-y-3">
                        <div class="flex items-center justify-between text-zinc-700 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-800 pb-2">
                            <span class="font-bold text-cyan-700 dark:text-cyan-400 uppercase">3. CARA MENGGUNAKAN DI CLAUDE CODE CLI (claude):</span>
                            <span class="text-[10px] text-zinc-500 dark:text-zinc-400">Terminal Command</span>
                        </div>
                        <ol class="list-decimal list-inside space-y-1.5 text-zinc-700 dark:text-zinc-400 text-xs font-sans">
                            <li>Jalankan perintah <code class="text-cyan-600 dark:text-cyan-400">claude</code> pada terminal root direktori proyek.</li>
                            <li>Beri perintah terpandu dengan prompt dari Cockpit: <code class="text-emerald-600 dark:text-emerald-400 select-all">claude &quot;[Tempel prompt fitur dari Cockpit di sini]&quot;</code></li>
                            <li>Biarkan Claude Code membaca file bounded, mengeksekusi diff, dan menjalankan loop pengujian terminal secara otonom.</li>
                        </ol>
                    </div>

                    <!-- Windsurf Cascade Tool Guide -->
                    <div x-show="selectedIdeTool === 'windsurf'" x-cloak class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 font-mono text-xs space-y-3">
                        <div class="flex items-center justify-between text-zinc-700 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-800 pb-2">
                            <span class="font-bold text-sky-700 dark:text-sky-400 uppercase">4. CARA MENGGUNAKAN DI WINDSURF CASCADE:</span>
                            <span class="text-[10px] text-zinc-500 dark:text-zinc-400">Flow-Based Agent</span>
                        </div>
                        <ol class="list-decimal list-inside space-y-1.5 text-zinc-700 dark:text-zinc-400 text-xs font-sans">
                            <li>Buka panel Cascade di Windsurf dan aktifkan mode <strong>Agentic Write</strong>.</li>
                            <li>Tempelkan prompt dari Cockpit Pelaksanaan Sprint. Pastikan batasan target file terkunci.</li>
                            <li>Pantau cascade flow hingga build sukses dan verifikasi tes lolos.</li>
                        </ol>
                    </div>

                    <!-- Devin & Copilot Tool Guide -->
                    <div x-show="selectedIdeTool === 'devin'" x-cloak class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 font-mono text-xs space-y-3">
                        <div class="flex items-center justify-between text-zinc-700 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-800 pb-2">
                            <span class="font-bold text-amber-700 dark:text-amber-400 uppercase">5. CARA MENGGUNAKAN DI DEVIN &amp; GITHUB COPILOT:</span>
                            <span class="text-[10px] text-zinc-500 dark:text-zinc-400">Autonomous Agent / Workspace</span>
                        </div>
                        <ol class="list-decimal list-inside space-y-1.5 text-zinc-700 dark:text-zinc-400 text-xs font-sans">
                            <li>Buat issue/task baru dengan judul ID Fitur (cth: FEAT-MVP-01).</li>
                            <li>Salin User Story dan seluruh tabel skenario Gherkin (Given-When-Then) dari Cockpit ke dalam task description.</li>
                            <li>Biarkan Devin / Copilot menyelesaikan issue dan membuka Pull Request terisolasi.</li>
                        </ol>
                    </div>
                </div>

                <!-- 3 Pillars of Engineering Manifesto -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-mono text-xs mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                    <!-- 1. Frontend Anti-AI-Slop -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold uppercase">
                                <span class="w-2.5 h-2.5 bg-emerald-500"></span>
                                <h3>{{ $isEn ? '1. FRONTEND: ANTI AI-SLOP UI/UX' : '1. FRONTEND: UI/UX ANTI AI-SLOP' }}</h3>
                            </div>
                            <ul class="space-y-2 text-[11px] text-zinc-700 dark:text-zinc-300">
                                <li class="flex items-start gap-1.5">
                                    <span class="text-emerald-500 font-bold">&check;</span>
                                    <span><strong>{{ $isEn ? 'Curated Palette:' : 'Palet Kurasi:' }}</strong> {{ $isEn ? 'Zinc monochrome base with sharp Emerald & Amber accents. Zero generic pastel.' : 'Base Zinc monokrom dengan aksen tajam Emerald & Amber. Zero generic pastel.' }}</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-emerald-500 font-bold">&check;</span>
                                    <span><strong>{{ $isEn ? 'Zero Native Popups:' : 'Zero Native Popups:' }}</strong> {{ $isEn ? 'Strictly ban alert() or confirm(). Use Floating Toast & Modals.' : 'Dilarang keras alert() atau confirm(). Gunakan Floating Toast & Modal.' }}</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-emerald-500 font-bold">&check;</span>
                                    <span><strong>{{ $isEn ? 'Subtle Corners:' : 'Sudut Tipis:' }}</strong> {{ $isEn ? 'Subtle border radius (rounded-none/sm), ban capsule rounded-full buttons.' : 'Border radius halus (rounded-none/sm), dilarang tombol kapsul rounded-full.' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- 2. Backend Scalability Manifesto -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-sky-600 dark:text-sky-400 font-bold uppercase">
                                <span class="w-2.5 h-2.5 bg-sky-500"></span>
                                <h3>{{ $isEn ? '2. BACKEND: ENTERPRISE SCALABILITY' : '2. BACKEND: SKALABILITAS ENTERPRISE' }}</h3>
                            </div>
                            <ul class="space-y-2 text-[11px] text-zinc-700 dark:text-zinc-300">
                                <li class="flex items-start gap-1.5">
                                    <span class="text-sky-500 font-bold">&check;</span>
                                    <span><strong>Strict ULID Primary Keys:</strong> {{ $isEn ? 'Use ulid(\'id\') (VARCHAR(26)). Avoid AUTO_INCREMENT.' : 'Gunakan ulid(\'id\') (VARCHAR(26)). Hindari AUTO_INCREMENT.' }}</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-sky-500 font-bold">&check;</span>
                                    <span><strong>{{ $isEn ? 'Keyset Cursor Pagination O(1):' : 'Keyset Cursor Pagination O(1):' }}</strong> {{ $isEn ? 'Avoid OFFSET paginate(). Always use cursorPaginate().' : 'Hindari paginate() OFFSET. Wajib gunakan cursorPaginate().' }}</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-sky-500 font-bold">&check;</span>
                                    <span><strong>{{ $isEn ? 'Atomic Transactions:' : 'Transaksi Atomik:' }}</strong> {{ $isEn ? 'Encapsulate mutations in Single Action Classes wrapped in DB::transaction().' : 'Enkapsulasi logika mutasi dalam Single Action Class di dalam DB::transaction().' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- 3. Agent Handoff & Anti Context-Rot Protocol -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-amber-600 dark:text-amber-400 font-bold uppercase">
                                <span class="w-2.5 h-2.5 bg-amber-500"></span>
                                <h3>{{ $isEn ? '3. INTEGRATION: ZERO CONTEXT-ROT' : '3. INTEGRASI: ZERO CONTEXT-ROT' }}</h3>
                            </div>
                            <ul class="space-y-2 text-[11px] text-zinc-700 dark:text-zinc-300">
                                <li class="flex items-start gap-1.5">
                                    <span class="text-amber-500 font-bold">&check;</span>
                                    <span><strong>One-Feature-At-A-Time:</strong> {{ $isEn ? 'Copy prompts per vertical feature slice, not the entire PRD.' : 'Salin prompt per fitur vertikal, bukan seluruh PRD.' }}</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-amber-500 font-bold">&check;</span>
                                    <span><strong>Bounded File Scoping:</strong> {{ $isEn ? 'Explicitly bound target files in prompts so AI doesn\'t alter unrelated code.' : 'Batasi target file pada prompt agar AI tidak merusak file lain.' }}</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-amber-500 font-bold">&check;</span>
                                    <span><strong>Terminal Verification:</strong> {{ $isEn ? 'Mandate AI to run test suites (php artisan test) before completing tasks.' : 'Wajibkan AI menjalankan tes (php artisan test) sebelum menandai task selesai.' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                </div>
            </section>

            <!-- SECTION 3.8: VIRTUAL ARCHITECTURE STUDIO (THE 4 VIRTUAL CHARTS COMMAND CENTER) -->
