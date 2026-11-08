<!-- AI Architecture Advisory & Tech Stack Radar Modal -->
<div 
    x-show="advisoryModalOpen" 
    x-cloak 
    class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto bg-black/70 backdrop-blur-xs"
    @keydown.escape.window="if(!advisoryIntegrating && !advisoryScanning) advisoryModalOpen = false"
>
    <!-- Modal Dialog Window -->
    <div 
        class="bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 w-full max-w-4xl shadow-[6px_6px_0px_#000] dark:shadow-[6px_6px_0px_#27272a] rounded-none flex flex-col max-h-[90vh] my-auto overflow-hidden animate-in fade-in zoom-in-95 duration-150"
        @click.outside="if(!advisoryIntegrating && !advisoryScanning) advisoryModalOpen = false"
    >
        <!-- Modal Top Bar -->
        <div class="px-4 py-3 bg-zinc-950 dark:bg-black text-white flex items-center justify-between border-b border-zinc-800 shrink-0 select-none">
            <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 bg-emerald-500 text-black font-mono font-black text-xs inline-flex items-center justify-center rounded-none shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
                <div>
                    <h3 class="font-mono text-xs sm:text-sm font-black uppercase tracking-wider text-white flex items-center gap-2">
                        <span x-text="isEn ? 'AI ARCHITECTURE ADVISORY & TECH RADAR' : 'PENASIHAT ARSITEKTUR AI & TECH RADAR'"></span>
                        <span 
                            class="px-1.5 py-0.2 text-[9px] font-mono font-black uppercase"
                            :class="isPurchasedPackage ? 'bg-emerald-500 text-black' : 'bg-amber-400 text-black'"
                            x-text="isPurchasedPackage ? (isEn ? 'LICENSED' : 'BERLISENSI') : (isEn ? 'LOCKED PRO' : 'TERKUNCI PRO')"
                        ></span>
                    </h3>
                    <p class="text-[10px] font-mono text-zinc-400" x-text="isEn ? 'Domain Gap Discovery, Mission-Critical Features & 2026 Tech Modernization' : 'Analisis Celah Fitur Domain, Rekomendasi Esensial & Modernisasi Stack 2026'"></p>
                </div>
            </div>

            <button 
                type="button" 
                @click="advisoryModalOpen = false" 
                :disabled="advisoryScanning || advisoryIntegrating"
                class="text-zinc-400 hover:text-white p-1 hover:bg-zinc-800 rounded-none transition cursor-pointer disabled:opacity-30"
                title="Tutup (Esc)"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- 1. LOCKED CONTAINER: SHOWN ONLY IF UNPAID / FREE SANDBOX (ZERO-LEAK ACCESS GATE) -->
        <div x-show="!isPurchasedPackage" class="p-6 sm:p-8 space-y-6 overflow-y-auto">
            <div class="border-2 border-amber-500 bg-amber-500/10 dark:bg-amber-950/40 p-5 sm:p-6 rounded-none space-y-4">
                <div class="flex items-start gap-3">
                    <span class="w-8 h-8 bg-amber-500 text-black font-black font-mono text-sm inline-flex items-center justify-center shrink-0">
                        🔒
                    </span>
                    <div class="space-y-1">
                        <h4 class="font-mono text-sm font-black uppercase text-amber-950 dark:text-amber-200" x-text="isEn ? 'PREMIUM ARCHITECTURAL INTELLIGENCE // ZERO-LEAK ACCESS CONTROL' : 'FITUR EKSKLUSIF BERLISENSI // ZERO-LEAK ACCESS CONTROL'"></h4>
                        <p class="text-xs font-sans text-zinc-800 dark:text-zinc-200 leading-relaxed" x-text="isEn 
                            ? 'The AI Architecture Advisory & Tech Stack Evolution Radar is an exclusive premium feature for officially purchased projects (Retail Pro/Ultimate or Dedicated Mitra Turnkey Studio). On free demo sandboxes, proprietary architectural gap detection and stack modernization are locked to protect enterprise engineering standards.' 
                            : 'Fitur Penasihat Arsitektur AI & Tech Stack Evolution Radar adalah fasilitas premium eksklusif untuk proyek yang telah memiliki lisensi berbayar resmi (Paket Retail Pro/Ultimate atau Proyek Mitra Turnkey Studio). Pada versi sandbox demo gratis, fitur ini dikunci untuk melindungi proprietary intelligence dan standar rekayasa enterprise.'">
                        </p>
                    </div>
                </div>

                <div class="p-3.5 bg-white dark:bg-zinc-900 border border-amber-400 dark:border-amber-700/60 font-mono text-xs space-y-2">
                    <div class="font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span class="text-emerald-500">✔</span>
                        <span x-text="isEn ? 'What this premium feature unlocks upon purchase:' : 'Kemampuan yang terbuka setelah lisensi aktif:'"></span>
                    </div>
                    <ul class="list-disc pl-5 space-y-1 text-zinc-700 dark:text-zinc-300 text-[11px] font-sans">
                        <li x-text="isEn ? 'Automated inspection of unbuilt essential features based on real-world industry benchmarks and your project corpus.' : 'Pemeriksaan otomatis fitur-fitur esensial yang belum terpikirkan berbasis benchmark industri riil dan dokumen acuan Anda.'"></li>
                        <li x-text="isEn ? 'Detailed rationale on WHY each feature is critical, including the operational, legal, and financial risks if omitted.' : 'Uraian mendalam MENGAPA setiap fitur krusial dibuat, lengkap dengan mitigasi risiko operasional, legalitas, dan finansial jika terlewat.'"></li>
                        <li x-text="isEn ? '2026 Tech Stack Modernization upgrades: WebRTC low-latency, PostgreSQL 16 Strict ULID, Keyset Cursor O(1), Reverb WebSockets, and RFC 9457 error contracts.' : 'Pembaruan teknologi 2026 kelas dunia: WebRTC latensi rendah, PostgreSQL 16 Strict ULID, Keyset Cursor O(1), Reverb WebSockets, dan kontrak error RFC 9457.'"></li>
                        <li x-text="isEn ? '1-Click seamless integration directly synthesizing 6-dimensional feature specs into your Blueprint and recalculating SHA-256 integrity seal.' : 'Integrasi instan 1-klik yang langsung merumuskan spesifikasi 6 dimensi ke dalam Blueprint Anda dan memperbarui segel kriptografi SHA-256.'"></li>
                    </ul>
                </div>
            </div>

            <!-- Conversion Action Bridges -->
            <div class="space-y-3">
                <div class="text-xs font-mono font-bold uppercase text-zinc-700 dark:text-zinc-300" x-text="isEn ? 'ACTIVATE YOUR LICENSE TO UNLOCK THIS FEATURE:' : 'AKTIFKAN LISENSI PROYEK UNTUK MEMBUKA FITUR INI:'"></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a 
                        href="#section-10" 
                        @click="advisoryModalOpen = false" 
                        class="p-4 bg-zinc-900 dark:bg-zinc-800 hover:bg-zinc-800 dark:hover:bg-zinc-700 text-white border-2 border-zinc-900 dark:border-zinc-600 rounded-none transition flex flex-col justify-between"
                    >
                        <div>
                            <span class="px-1.5 py-0.5 bg-emerald-500 text-black text-[9px] font-mono font-black uppercase inline-block mb-2">JALUR 1 // RETAIL</span>
                            <div class="font-mono text-xs font-bold uppercase text-white" x-text="isEn ? 'SELF-CODING RETAIL LICENSE' : 'LISENSI MANDIRI RETAIL'"></div>
                            <p class="text-[11px] font-sans text-zinc-400 mt-1" x-text="isEn ? 'From Rp 99.000 (One-time). Instant access to PRD, DDL, and Advisory.' : 'Mulai Rp 99.000 (Sekali bayar). Akses instan PRD 26 parameter, DDL SQL, dan Penasihat AI.'"></p>
                        </div>
                        <div class="mt-3 text-xs font-mono font-black text-emerald-400 flex items-center gap-1">
                            <span x-text="isEn ? 'Choose Retail License →' : 'Pilih Lisensi Retail →'"></span>
                        </div>
                    </a>

                    <a 
                        href="#section-10" 
                        @click="advisoryModalOpen = false" 
                        class="p-4 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 border-2 border-emerald-500 rounded-none transition flex flex-col justify-between"
                    >
                        <div>
                            <span class="px-1.5 py-0.5 bg-emerald-600 text-white text-[9px] font-mono font-black uppercase inline-block mb-2">JALUR 2 // TURNKEY</span>
                            <div class="font-mono text-xs font-bold uppercase text-emerald-950 dark:text-emerald-200" x-text="isEn ? 'DEDICATED TURNKEY STUDIO' : 'TURNKEY PENUH TIM NERIAH PRO'"></div>
                            <p class="text-[11px] font-sans text-zinc-700 dark:text-zinc-300 mt-1" x-text="isEn ? '100% turnkey implementation by Senior Architects with SLA guarantees.' : 'Dikerjakan 100% tuntas oleh Senior Architect dengan garansi SLA.'"></p>
                        </div>
                        <div class="mt-3 text-xs font-mono font-black text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
                            <span x-text="isEn ? 'Choose Turnkey Studio (DP 50%) →' : 'Pilih Turnkey Studio (DP 50%) →'"></span>
                        </div>
                    </a>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button 
                    type="button" 
                    @click="advisoryModalOpen = false" 
                    class="px-4 py-2 bg-zinc-200 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-mono text-xs font-bold rounded-none hover:bg-zinc-300 dark:hover:bg-zinc-700 transition cursor-pointer"
                >
                    <span x-text="isEn ? 'Close Window' : 'Tutup Jendela'"></span>
                </button>
            </div>
        </div>

        <!-- 2. UNLOCKED WORKSPACE: FOR PURCHASED / PAID PACKAGES -->
        <div x-show="isPurchasedPackage" class="flex flex-col flex-1 overflow-hidden">
            <!-- Sub-Header Tabs & Quick Actions -->
            <div class="px-4 py-2.5 bg-zinc-100 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 flex flex-wrap items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        @click="advisoryTab = 'missing_features'" 
                        class="px-3 py-1.5 font-mono text-xs font-black uppercase rounded-none transition flex items-center gap-1.5 cursor-pointer"
                        :class="advisoryTab === 'missing_features' 
                            ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 border-b-2 border-emerald-500' 
                            : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-300 dark:hover:bg-zinc-700'"
                    >
                        <span>💡</span>
                        <span x-text="isEn ? '1. MISSING ARCHITECTURE FEATURES' : '1. SARAN FITUR ESENSIAL'"></span>
                        <span 
                            x-show="advisoryResult?.missing_features?.length" 
                            class="px-1.5 py-0.2 bg-emerald-500 text-black text-[9px] font-mono font-black"
                            x-text="advisoryResult?.missing_features?.length"
                        ></span>
                    </button>

                    <button 
                        type="button" 
                        @click="advisoryTab = 'tech_upgrades'" 
                        class="px-3 py-1.5 font-mono text-xs font-black uppercase rounded-none transition flex items-center gap-1.5 cursor-pointer"
                        :class="advisoryTab === 'tech_upgrades' 
                            ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 border-b-2 border-emerald-500' 
                            : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-300 dark:hover:bg-zinc-700'"
                    >
                        <span>⚡</span>
                        <span x-text="isEn ? '2. TECH STACK EVOLUTION (2026)' : '2. MODERNISASI TECH STACK 2026'"></span>
                        <span 
                            x-show="advisoryResult?.tech_upgrades?.length" 
                            class="px-1.5 py-0.2 bg-sky-500 text-white text-[9px] font-mono font-black"
                            x-text="advisoryResult?.tech_upgrades?.length"
                        ></span>
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        @click="scanArchitectureAdvisory()" 
                        :disabled="advisoryScanning || advisoryIntegrating"
                        class="px-3 py-1 bg-zinc-900 dark:bg-zinc-800 text-white hover:bg-zinc-800 dark:hover:bg-zinc-700 font-mono text-xs font-bold rounded-none border border-zinc-700 transition flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
                        :title="isEn ? 'Re-scan and refresh recommendations' : 'Pindai ulang dan segarkan rekomendasi'"
                    >
                        <svg class="w-3.5 h-3.5 text-emerald-400" :class="advisoryScanning ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span x-text="advisoryScanning ? (isEn ? 'SCANNING...' : 'MEMINDAI...') : (isEn ? 'RE-SCAN' : 'PINDAI ULANG')"></span>
                    </button>
                </div>
            </div>

            <!-- Tab Body Contents -->
            <div class="p-4 sm:p-6 overflow-y-auto flex-1 space-y-4">
                <!-- Loading State Banner -->
                <div x-show="advisoryScanning" class="py-12 flex flex-col items-center justify-center space-y-3 text-center">
                    <div class="w-10 h-10 border-4 border-emerald-500 border-t-transparent animate-spin rounded-none"></div>
                    <div class="font-mono text-xs font-black uppercase text-zinc-900 dark:text-zinc-100" x-text="isEn ? 'AI IS INSPECTING PROJECT DOCUMENTS & STANDARDS...' : 'AI SEDANG MENGANALISIS DOKUMEN PROYEK & STANDAR DUNIA...'"></div>
                    <p class="text-xs font-sans text-zinc-500 dark:text-zinc-400 max-w-md" x-text="isEn ? 'Cross-referencing your business problem statement, target sector, and current feature matrix to discover critical architecture gaps without assumptions.' : 'Menganalisis dokumen acuan klien, sektor sistem, dan matriks fitur yang ada untuk menemukan fitur-fitur krusial yang belum tercover secara objektif.'"></p>
                </div>

                <!-- Empty Initial State -->
                <div x-show="!advisoryScanning && !advisoryResult" class="py-10 text-center space-y-3">
                    <span class="w-12 h-12 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono font-black text-xl inline-flex items-center justify-center rounded-none border border-zinc-300 dark:border-zinc-700">
                        🔍
                    </span>
                    <div class="font-mono text-xs font-black uppercase text-zinc-900 dark:text-zinc-100" x-text="isEn ? 'READY TO DISCOVER ARCHITECTURE GAPS' : 'SIAP MEMINDAI SARAN FITUR & MODERNISASI'"></div>
                    <p class="text-xs font-sans text-zinc-600 dark:text-zinc-400 max-w-lg mx-auto" x-text="isEn ? 'Click the scan button to let AI inspect your specifications, identify missing essential features, and recommend 2026 enterprise tech upgrades.' : 'Klik tombol di bawah agar AI membedah spesifikasi sistem Anda, mengidentifikasi fitur penting yang belum ada, dan merekomendasikan pembaruan teknologi 2026.'"></p>
                    <button 
                        type="button" 
                        @click="scanArchitectureAdvisory()" 
                        class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase rounded-none transition cursor-pointer shadow-[3px_3px_0px_#065f46]"
                    >
                        <span x-text="isEn ? '✨ RUN ARCHITECTURE SCAN NOW' : '✨ JALANKAN ANALISIS CELAH SEKARANG'"></span>
                    </button>
                </div>

                <!-- TAB 1: MISSING ARCHITECTURE FEATURES -->
                <div x-show="!advisoryScanning && advisoryResult && advisoryTab === 'missing_features'" class="space-y-3.5">
                    <div class="p-3 bg-emerald-500/10 border border-emerald-500/40 rounded-none flex items-center justify-between gap-3 text-xs font-mono">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-emerald-500 inline-block"></span>
                            <span class="font-black text-emerald-950 dark:text-emerald-300" x-text="isEn ? 'DISCOVERED ESSENTIAL FEATURES NOT YET COVERED:' : 'SARAN FITUR ESENSIAL YANG BELUM TERCOVER:'"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button 
                                type="button" 
                                @click="selectAllAdvisoryFeatures(true)" 
                                class="text-[11px] underline text-emerald-700 dark:text-emerald-400 hover:text-emerald-950 dark:hover:text-emerald-200 font-bold cursor-pointer"
                                x-text="isEn ? 'Select All' : 'Pilih Semua'"
                            ></button>
                            <span class="text-zinc-400">|</span>
                            <button 
                                type="button" 
                                @click="selectAllAdvisoryFeatures(false)" 
                                class="text-[11px] underline text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 font-bold cursor-pointer"
                                x-text="isEn ? 'Deselect All' : 'Batal Semua'"
                            ></button>
                        </div>
                    </div>

                    <!-- Feature Cards Loop -->
                    <div class="space-y-3">
                        <template x-for="item in (advisoryResult?.missing_features || [])" :key="item.id">
                            <div 
                                class="border-2 rounded-none p-4 transition-all duration-150 cursor-pointer"
                                :class="advisorySelectedFeatures.includes(item.id) 
                                    ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/20 shadow-[2px_2px_0px_#059669]' 
                                    : 'border-zinc-300 dark:border-zinc-800 bg-white dark:bg-zinc-900 opacity-75 hover:opacity-100'"
                                @click="toggleAdvisoryFeature(item.id)"
                            >
                                <div class="flex items-start gap-3">
                                    <input 
                                        type="checkbox" 
                                        :checked="advisorySelectedFeatures.includes(item.id)" 
                                        @click.stop="toggleAdvisoryFeature(item.id)"
                                        class="mt-1 w-4 h-4 text-emerald-600 border-zinc-400 dark:border-zinc-600 rounded-none focus:ring-0 cursor-pointer"
                                    />
                                    <div class="flex-1 space-y-2">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-mono text-xs sm:text-sm font-black uppercase text-zinc-900 dark:text-zinc-100" x-text="item.title"></h4>
                                                <span 
                                                    class="px-1.5 py-0.2 text-[9px] font-mono font-black uppercase"
                                                    :class="item.proposed_phase === 'mvp' ? 'bg-emerald-500 text-black' : 'bg-sky-500 text-white'"
                                                    x-text="item.proposed_phase === 'mvp' ? (isEn ? 'PHASE 1 (MVP)' : 'FASE 1 (MVP)') : (isEn ? 'ROADMAP' : 'ROADMAP SUSULAN')"
                                                ></span>
                                            </div>
                                            <span 
                                                class="px-2 py-0.5 text-[9px] font-mono font-black uppercase bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900"
                                                x-text="item.impact_rating || 'HIGH VALUE'"
                                            ></span>
                                        </div>

                                        <p class="text-xs font-sans text-zinc-700 dark:text-zinc-300 leading-relaxed" x-text="item.desc"></p>

                                        <!-- Kenapa Krusial Callout -->
                                        <div class="p-2.5 bg-amber-500/10 border-l-4 border-amber-500 text-[11px] font-sans text-amber-950 dark:text-amber-200 space-y-0.5">
                                            <strong class="font-mono font-bold uppercase block text-amber-900 dark:text-amber-300" x-text="isEn ? 'WHY THIS IS CRITICAL // RISK IF OMITTED:' : 'MENGAPA FITUR INI PENTING // RISIKO JIKA TIDAK DIBUAT:'"></strong>
                                            <span x-text="item.why_critical"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- TAB 2: TECH STACK EVOLUTION (2026) -->
                <div x-show="!advisoryScanning && advisoryResult && advisoryTab === 'tech_upgrades'" class="space-y-3.5">
                    <div class="p-3 bg-sky-500/10 border border-sky-500/40 rounded-none flex items-center justify-between gap-3 text-xs font-mono">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-sky-500 inline-block"></span>
                            <span class="font-black text-sky-950 dark:text-sky-300" x-text="isEn ? 'MODERN 2026 ARCHITECTURAL UPGRADES FOR YOUR SYSTEM:' : 'PEMBARUAN STANDAR REKAYASA 2026 UNTUK SISTEM ANDA:'"></span>
                        </div>
                    </div>

                    <!-- Tech Upgrade Cards Loop -->
                    <div class="space-y-3">
                        <template x-for="tech in (advisoryResult?.tech_upgrades || [])" :key="tech.id">
                            <div 
                                class="border-2 rounded-none p-4 transition-all duration-150 cursor-pointer"
                                :class="advisorySelectedTech.includes(tech.id) 
                                    ? 'border-sky-500 bg-sky-50/40 dark:bg-sky-950/20 shadow-[2px_2px_0px_#0284c7]' 
                                    : 'border-zinc-300 dark:border-zinc-800 bg-white dark:bg-zinc-900 opacity-75 hover:opacity-100'"
                                @click="toggleAdvisoryTech(tech.id)"
                            >
                                <div class="flex items-start gap-3">
                                    <input 
                                        type="checkbox" 
                                        :checked="advisorySelectedTech.includes(tech.id)" 
                                        @click.stop="toggleAdvisoryTech(tech.id)"
                                        class="mt-1 w-4 h-4 text-sky-600 border-zinc-400 dark:border-zinc-600 rounded-none focus:ring-0 cursor-pointer"
                                    />
                                    <div class="flex-1 space-y-1.5">
                                        <div class="flex items-center justify-between gap-2">
                                            <h4 class="font-mono text-xs sm:text-sm font-black uppercase text-zinc-900 dark:text-zinc-100" x-text="tech.name"></h4>
                                            <div class="flex items-center gap-1.5">
                                                <template x-for="std in tech.standards" :key="std">
                                                    <span class="px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono text-[9px] font-bold" x-text="std"></span>
                                                </template>
                                            </div>
                                        </div>

                                        <p class="text-xs font-sans text-zinc-700 dark:text-zinc-300" x-text="tech.summary"></p>

                                        <div class="p-2 bg-zinc-100 dark:bg-zinc-950 border-l-2 border-sky-500 text-[11px] font-sans text-zinc-600 dark:text-zinc-400">
                                            <span class="font-bold text-zinc-900 dark:text-zinc-200" x-text="isEn ? 'Engineering Impact: ' : 'Dampak Rekayasa: '"></span>
                                            <span x-text="tech.why_critical"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Sticky Bottom Integration Bar -->
            <div class="px-4 py-3 bg-zinc-950 dark:bg-black border-t border-zinc-800 text-white flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-3 text-xs font-mono">
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-none animate-pulse"></span>
                    <span class="text-zinc-300">
                        <strong class="text-white font-black" x-text="advisorySelectedFeatures.length"></strong> <span x-text="isEn ? 'features' : 'fitur'"></span> & 
                        <strong class="text-white font-black" x-text="advisorySelectedTech.length"></strong> <span x-text="isEn ? 'tech upgrades selected' : 'upgrade dipilih'"></span>
                    </span>
                </div>

                <div class="flex items-center gap-2.5 w-full sm:w-auto">
                    <button 
                        type="button" 
                        @click="advisoryModalOpen = false" 
                        :disabled="advisoryIntegrating"
                        class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-mono text-xs font-bold rounded-none transition cursor-pointer disabled:opacity-50 w-full sm:w-auto text-center"
                    >
                        <span x-text="isEn ? 'CANCEL' : 'BATAL'"></span>
                    </button>

                    <button 
                        type="button" 
                        @click="submitAdvisoryIntegration()" 
                        :disabled="advisoryIntegrating || (advisorySelectedFeatures.length === 0 && advisorySelectedTech.length === 0)"
                        class="px-5 py-2 bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-black font-mono font-black text-xs uppercase tracking-tight rounded-none transition flex items-center justify-center gap-2 cursor-pointer shadow-[3px_3px_0px_#065f46] disabled:opacity-40 disabled:cursor-not-allowed w-full sm:w-auto"
                    >
                        <svg x-show="!advisoryIntegrating" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <div x-show="advisoryIntegrating" class="w-3.5 h-3.5 border-2 border-black border-t-transparent animate-spin rounded-none"></div>
                        <span x-text="advisoryIntegrating ? (isEn ? 'INTEGRATING & SEALING...' : 'MENGINTEGRASIKAN & MENYEGEL...') : (isEn ? '🚀 APPROVE & INTEGRATE INTO BLUEPRINT' : '🚀 SETUJUI & INTEGRASIKAN KE BLUEPRINT')"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

