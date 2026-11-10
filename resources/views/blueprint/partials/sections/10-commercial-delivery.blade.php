

@php
    $litePrice = \App\Models\CmsGlobalSetting::getVal('pricing_retail_lite_price', '190.000');
    $proPrice = \App\Models\CmsGlobalSetting::getVal('pricing_retail_pro_price', '490.000');
    $ultimatePrice = \App\Models\CmsGlobalSetting::getVal('pricing_retail_ultimate_price', '1.490.000');

    // Determine concrete retail status & explicit track context
    $hasPaidRetail = ($isPurchased ?? false)
        || $blueprint->project_status === 'Retail License'
        || (method_exists($blueprint, 'hasPaidRetailLicense') && $blueprint->hasPaidRetailLicense())
        || !empty($blueprint->user_metadata['paid'])
        || !empty($blueprint->user_metadata['retail_tier'])
        || !empty($blueprint->user_metadata['package_tier'])
        || str_contains(strtolower($blueprint->slug ?? ''), '-retail-');

    $isRetailExplicit = $hasPaidRetail || ($blueprint->user_metadata['track'] ?? '') === 'retail' || request('track') === 'retail';
    $isStudioExplicit = (! $isRetailExplicit) && (($blueprint->user_metadata['track'] ?? '') === 'studio' || request('track') === 'studio' || request('mode') === 'mitra' || in_array($blueprint->project_status, ['Active Sprint', 'In Development (DP Paid)', 'Awaiting DP Payment', 'Completed & Fully Settled'], true));
@endphp

            <section id="section-10" class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white border-2 border-emerald-600 dark:border-emerald-500 p-6 sm:p-8 mb-8 rounded-none prd-scroll-target shadow-sm">
                <!-- Section 10/13 Header Accordion Bar -->
                <div class="flex items-center justify-between gap-2 mb-6 border-b border-zinc-200 dark:border-zinc-800 pb-3 cursor-pointer select-none" @click="toggleSection('section-10')">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-emerald-600 text-white font-mono font-bold text-xs flex items-center justify-center rounded-none" x-text="getSectionNum('section-10')">{{ $isRetailLicense ? '10' : '13' }}</span>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">
                                @if($hasPaidRetail)
                                    <span x-show="locale === 'en'">Active Digital Retail License &amp; Lifetime Starter Kit Access</span>
                                    <span x-show="locale !== 'en'">Lisensi Digital Retail Aktif &amp; Akses Starter Kit Seumur Hidup</span>
                                @else
                                    <span x-show="executionTrack === 'retail'">
                                        <span x-show="locale === 'en'">Lock Scope &amp; Digital Retail License</span>
                                        <span x-show="locale !== 'en'">Kunci Scope &amp; Lisensi Digital Retail</span>
                                    </span>
                                    <span x-show="executionTrack === 'studio'">
                                        <span x-show="locale === 'en'">Lock Scope, Digital Contract &amp; DP Payment</span>
                                        <span x-show="locale !== 'en'">Kunci Scope, Digital Contract &amp; Pembayaran DP</span>
                                    </span>
                                @endif
                            </h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">
                                @if($hasPaidRetail)
                                    <span x-show="locale === 'en'">Verified SHA-256 Integrity Seal &bull; Architecture Files &amp; Starter Kit ZIP Ready for Download</span>
                                    <span x-show="locale !== 'en'">Segel Integritas SHA-256 Terverifikasi &bull; Berkas Arsitektur &amp; Starter Kit ZIP Siap Diunduh</span>
                                @else
                                    <span x-show="executionTrack === 'retail'">
                                        <span x-show="locale === 'en'">SHA-256 Scope Seal, Software Factory OS License &amp; Starter Kit ZIP Download</span>
                                        <span x-show="locale !== 'en'">SHA-256 Scope Seal, Lisensi Software Factory OS &amp; Unduh Starter Kit ZIP</span>
                                    </span>
                                    <span x-show="executionTrack === 'studio'">
                                        <span x-show="locale === 'en'">SHA-256 Scope Seal, Midtrans Instant Snap &amp; Dedicated Engineer Slot</span>
                                        <span x-show="locale !== 'en'">SHA-256 Scope Seal, Midtrans Instant Snap &amp; Dedicated Engineer Slot</span>
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 border border-emerald-300 dark:border-emerald-800 hidden sm:inline">
                            <span x-show="locale === 'en'">PROJECT EXECUTION</span>
                            <span x-show="locale !== 'en'">EKSEKUSI PROYEK</span>
                        </span>
                        <button 
                            type="button" 
                            @click.stop="toggleSection('section-10')" 
                            class="px-2 py-1 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none flex items-center gap-1 font-mono text-xs border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700 ml-1 cursor-pointer"
                            :title="sectionCollapses['section-10'] ? (locale === 'en' ? 'Expand this section' : 'Buka seksi ini') : (locale === 'en' ? 'Collapse this section' : 'Ciutkan seksi ini')"
                        >
                            <span class="text-[10px] uppercase font-bold text-zinc-400 dark:text-zinc-500 hidden sm:inline" x-text="sectionCollapses['section-10'] ? (locale === 'en' ? 'EXPAND' : 'BUKA') : (locale === 'en' ? 'COLLAPSE' : 'CIUTKAN')"></span>
                            <svg class="w-4 h-4 transform transition-transform duration-200" :class="sectionCollapses['section-10'] ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Collapsed Preview -->
                <div x-show="sectionCollapses['section-10']" @click="toggleSection('section-10')" class="py-2.5 px-3 bg-zinc-50 dark:bg-zinc-950/60 border border-dashed border-emerald-500/40 text-xs font-mono text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between cursor-pointer transition select-none mb-6">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span x-show="locale === 'en'">Section <span x-text="getSectionNum('section-10')">{{ $isRetailLicense ? '10' : '13' }}</span> collapsed (Scope Lock &amp; DP / License Payment). Click to open checkout form...</span>
                        <span x-show="locale !== 'en'">Seksi <span x-text="getSectionNum('section-10')">{{ $isRetailLicense ? '10' : '13' }}</span> diciutkan (Kunci Scope &amp; Pembayaran DP/Lisensi). Klik untuk membuka formulir pembayaran...</span>
                    </span>
                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 uppercase font-bold" x-text="locale === 'en' ? '[ + EXPAND &amp; CHECKOUT ]' : '[ + BUKA &amp; CHECKOUT ]'">[ + EXPAND &amp; CHECKOUT ]</span>
                </div>

                <div x-show="!sectionCollapses['section-10']" x-transition class="accordion-body">

                <!-- Staging Sandbox Environment Banner (Strictly for Genuine Dedicated Studio Turnkey with Live Deployments, NEVER on Retail Licenses) -->
                @if($blueprint->staging_url && ! $hasPaidRetail && $isStudioExplicit)
                    <div class="mb-6 p-4 sm:p-5 bg-emerald-500/10 dark:bg-zinc-950 border border-emerald-500/40 text-zinc-800 dark:text-zinc-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-none bg-emerald-500 animate-pulse"></span>
                                    <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                                        <span x-show="locale === 'en'">ACTIVE STAGING DEMO SANDBOX PROVISIONED</span>
                                        <span x-show="locale !== 'en'">LINGKUNGAN DEMO STAGING TELAH DIAKTIFKAN</span>
                                    </span>
                                    @if($blueprint->is_free_grant)
                                        <span class="px-2 py-0.5 bg-emerald-500 text-black text-[10px] font-mono font-black uppercase">
                                            <span x-show="locale === 'en'">FREE SERVICE (RP 0)</span>
                                            <span x-show="locale !== 'en'">PELAYANAN GRATIS (RP 0)</span>
                                        </span>
                                    @endif
                                </div>
                                <h4 class="text-lg font-black tracking-tight text-zinc-900 dark:text-white font-mono">
                                    <a href="{{ $blueprint->staging_url }}" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-600 dark:hover:text-emerald-400 underline decoration-emerald-500/50 underline-offset-4 flex items-center gap-1.5 break-all">
                                        <span>{{ $blueprint->staging_url }}</span>
                                        <svg class="w-4 h-4 flex-shrink-0 text-emerald-600 dark:text-emerald-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </h4>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 font-sans">
                                    <span x-show="locale === 'en'">Isolated deployment container ready. Sprint milestones and live feature testing can be tracked directly here.</span>
                                    <span x-show="locale !== 'en'">Sandbox deployment terisolasi telah dialokasikan khusus. Progres sprint dan demo berkala dapat dipantau langsung di link ini.</span>
                                </p>
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end justify-between gap-2 flex-shrink-0 font-mono">
                                <a 
                                    href="{{ $blueprint->staging_url }}" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    class="bg-emerald-500 hover:bg-emerald-400 text-black font-bold text-xs uppercase px-4 py-2.5 transition flex items-center gap-2 rounded-none"
                                >
                                    <span x-show="locale === 'en'">Open Demo &rarr;</span>
                                    <span x-show="locale !== 'en'">Buka Staging &rarr;</span>
                                </a>
                                @if($blueprint->staging_provisioned_at)
                                    <span class="text-[10px] text-zinc-500 dark:text-zinc-400">
                                        <span x-show="locale === 'en'">Active: </span>
                                        <span x-show="locale !== 'en'">Aktif: </span>
                                        {{ $blueprint->staging_provisioned_at->format('d M Y H:i') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif



                @if($isStudioExplicit)
                    <!-- MITRA TURNKEY STUDIO TRACK HEADER BANNER -->
                    <div class="mb-6 p-4 sm:p-5 bg-zinc-900 border-2 border-amber-500 text-white rounded-none">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-0.5 bg-amber-500 text-black text-[10px] font-mono font-black uppercase">
                                        <span x-show="locale === 'en'">🤝 ACTIVE TRACK: DEDICATED STUDIO TURNKEY (NERIAH PRO TEAM)</span>
                                        <span x-show="locale !== 'en'">🤝 JALUR AKTIF: DEDICATED STUDIO TURNKEY (TIM NERIAH PRO)</span>
                                    </span>
                                    <span class="text-xs text-amber-400 font-mono font-bold">
                                        <span x-show="locale === 'en'">100% Full Implementation by Senior Architect Squad</span>
                                        <span x-show="locale !== 'en'">Pengerjaan Penuh 100% oleh Tim Senior Architect</span>
                                    </span>
                                </div>
                                <p class="text-xs text-zinc-300 font-sans mt-0.5">
                                    <span x-show="locale === 'en'">Project delivered turnkey by Neriah Pro engineering squads with cryptographic SHA-256 contracts, 7-Milestone WBS, and post-launch bug warranty.</span>
                                    <span x-show="locale !== 'en'">Proyek dikerjakan secara turnkey oleh tim engineer Neriah Pro dengan kontrak SPK SHA-256, WBS 7 Milestone, dan garansi bug pasca-peluncuran.</span>
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <button
                                    type="button"
                                    @click="executionTrack = (executionTrack === 'studio' ? 'retail' : 'studio')"
                                    class="text-[11px] font-mono text-zinc-400 hover:text-white underline cursor-pointer"
                                >
                                    <span x-show="executionTrack === 'studio'">
                                        <span x-show="locale === 'en'">Need self-coding? Switch to Retail License Track &rarr;</span>
                                        <span x-show="locale !== 'en'">Perlu koding mandiri? Beralih ke Jalur Lisensi Retail &rarr;</span>
                                    </span>
                                    <span x-show="executionTrack === 'retail'">
                                        <span x-show="locale === 'en'">&larr; Return to Dedicated Studio Turnkey Mode</span>
                                        <span x-show="locale !== 'en'">&larr; Kembali ke Mode Dedicated Studio Turnkey</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                @elseif($isRetailExplicit)
                    <!-- RETAIL LICENSE TRACK HEADER BANNER -->
                    <div class="mb-6 p-4 sm:p-5 bg-zinc-900 border-2 border-emerald-500 text-white rounded-none">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-0.5 bg-emerald-500 text-black text-[10px] font-mono font-black uppercase">
                                        <span x-show="locale === 'en'">📦 ACTIVE TRACK: DIGITAL RETAIL LICENSE (SELF-CODING)</span>
                                        <span x-show="locale !== 'en'">📦 JALUR AKTIF: LISENSI DIGITAL RETAIL (KODING MANDIRI)</span>
                                    </span>
                                    <span class="text-xs text-emerald-400 font-mono font-bold">
                                        <span x-show="locale === 'en'">26-Parameter PRD Blueprint &amp; AI Starter Kit</span>
                                        <span x-show="locale !== 'en'">Cetak Biru PRD 26 Parameter &amp; Starter Kit AI</span>
                                    </span>
                                </div>
                                <p class="text-xs text-zinc-300 font-sans mt-0.5">
                                    <span x-show="locale === 'en'">Autonomous software architecture license: 26-parameter PRD, Strict ULID PostgreSQL DDL, Docker containers, and AI agent instructions (.cursorrules).</span>
                                    <span x-show="locale !== 'en'">Lisensi software architecture mandiri: PRD 26 parameter, DDL SQL Strict ULID, Docker container, dan instruksi AI agent (.cursorrules).</span>
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <button
                                    type="button"
                                    @click="executionTrack = (executionTrack === 'retail' ? 'studio' : 'retail')"
                                    class="text-[11px] font-mono text-emerald-400 font-bold hover:underline cursor-pointer"
                                >
                                    <span x-show="executionTrack === 'retail'">
                                        <span x-show="locale === 'en'">🚀 Upgrade to Dedicated Studio Turnkey &rarr;</span>
                                        <span x-show="locale !== 'en'">🚀 Upgrade ke Dedicated Studio Turnkey &rarr;</span>
                                    </span>
                                    <span x-show="executionTrack === 'studio'">
                                        <span x-show="locale === 'en'">&larr; Return to Retail License</span>
                                        <span x-show="locale !== 'en'">&larr; Kembali ke Lisensi Retail</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- DUAL-TRACK EXECUTION SWITCHER (RETAIL VS STUDIO - ONLY FOR UNSELECTED DRAFT) -->
                    <div class="mb-8 border-2 border-zinc-900 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950 p-4 sm:p-6 rounded-none">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                            <span class="px-2.5 py-0.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold uppercase tracking-wider">
                                <span x-show="locale === 'en'">DUAL-TRACK EXECUTION BRIDGE // CHOOSE EXECUTION METHOD</span>
                                <span x-show="locale !== 'en'">DUAL-TRACK EXECUTION BRIDGE // PILIH METODE PENGERJAAN</span>
                            </span>
                            <span class="text-[11px] font-mono text-zinc-500">
                                <span x-show="locale === 'en'">Transparent &bull; Zero Lock-in &bull; Official Warranty</span>
                                <span x-show="locale !== 'en'">Transparan &bull; Bebas Lock-in &bull; Garansi Resmi</span>
                            </span>
                        </div>

                        <h3 class="text-lg sm:text-xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2">
                            <span x-show="locale === 'en'">How Would You Like to Execute This Blueprint?</span>
                            <span x-show="locale !== 'en'">Bagaimana Anda Ingin Mengeksekusi Cetak Biru Ini?</span>
                        </h3>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-6 max-w-3xl">
                            <span x-show="locale === 'en'">Choose the path best suited to your team capacity: Purchase architecture license for internal self-coding (Retail Track), or entrust full engineering from scratch to production VPS deployment to Neriah Pro squads (Studio Track).</span>
                            <span x-show="locale !== 'en'">Pilih jalur yang paling sesuai dengan kapasitas tim Anda: Beli lisensi arsitektur untuk koding mandiri dengan tim internal (Jalur Mandiri Retail), atau serahkan pengerjaan penuh dari nol sampai live di server VPS kepada tim engineer Neriah Pro (Jalur Turnkey Studio).</span>
                        </p>

                        <!-- Track Switch Buttons (Brutalist Tab Switcher) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                            <button
                                type="button"
                                @click="executionTrack = 'retail'"
                                :class="executionTrack === 'retail' ? 'border-emerald-500 bg-emerald-500/10 text-zinc-900 dark:text-white ring-1 ring-emerald-500' : 'border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:border-zinc-500'"
                                class="p-4 text-left border-2 rounded-none transition cursor-pointer flex flex-col justify-between"
                            >
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="font-mono text-[10px] font-bold uppercase text-emerald-600 dark:text-emerald-400">
                                            <span x-show="locale === 'en'">TRACK 01 // DIGITAL RETAIL LICENSE</span>
                                            <span x-show="locale !== 'en'">JALUR 01 // LISENSI DIGITAL RETAIL</span>
                                        </span>
                                        <span x-show="executionTrack === 'retail'" class="px-2 py-0.5 bg-emerald-500 text-black text-[9px] font-mono font-bold uppercase">
                                            <span x-show="locale === 'en'">SELECTED</span>
                                            <span x-show="locale !== 'en'">TERPILIH</span>
                                        </span>
                                    </div>
                                    <div class="text-base font-black uppercase text-zinc-900 dark:text-white mb-1">
                                        <span x-show="locale === 'en'">Self-Coding (Internal Team)</span>
                                        <span x-show="locale !== 'en'">Koding Mandiri (Tim Internal)</span>
                                    </div>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                        <span x-show="locale === 'en'">For teams with in-house developers needing 26-param PRD, Strict ULID PostgreSQL DDL, Docker containers, and AI coding agent rules.</span>
                                        <span x-show="locale !== 'en'">Untuk Anda yang punya tim programmer sendiri &amp; hanya butuh PRD 26 parameter, DDL SQL Strict ULID, Docker container, dan aturan AI coding agent.</span>
                                    </p>
                                </div>
                                <div class="mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-800 font-mono text-xs text-emerald-600 dark:text-emerald-400 font-bold flex items-center justify-between">
                                    <span>
                                        <span x-show="locale === 'en'">From Rp {{ $litePrice }} (One-time)</span>
                                        <span x-show="locale !== 'en'">Mulai Rp {{ $litePrice }} (Sekali Bayar)</span>
                                    </span>
                                    <span>
                                        <span x-show="locale === 'en'">Select Self-Coding &darr;</span>
                                        <span x-show="locale !== 'en'">Pilih Jalur Mandiri &darr;</span>
                                    </span>
                                </div>
                            </button>

                            <button
                                type="button"
                                @click="executionTrack = 'studio'"
                                :class="executionTrack === 'studio' ? 'border-emerald-500 bg-emerald-500/10 text-zinc-900 dark:text-white ring-1 ring-emerald-500' : 'border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:border-zinc-500'"
                                class="p-4 text-left border-2 rounded-none transition cursor-pointer flex flex-col justify-between"
                            >
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="font-mono text-[10px] font-bold uppercase text-zinc-500 dark:text-zinc-400">
                                            <span x-show="locale === 'en'">TRACK 02 // DEDICATED STUDIO TURNKEY</span>
                                            <span x-show="locale !== 'en'">JALUR 02 // DEDICATED STUDIO TURNKEY</span>
                                        </span>
                                        <span x-show="executionTrack === 'studio'" class="px-2 py-0.5 bg-emerald-500 text-black text-[9px] font-mono font-bold uppercase">
                                            <span x-show="locale === 'en'">SELECTED</span>
                                            <span x-show="locale !== 'en'">TERPILIH</span>
                                        </span>
                                    </div>
                                    <div class="text-base font-black uppercase text-zinc-900 dark:text-white mb-1">
                                        <span x-show="locale === 'en'">Full Turnkey (Neriah Pro Team)</span>
                                        <span x-show="locale !== 'en'">Turnkey Penuh (Tim Neriah Pro)</span>
                                    </div>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                        <span x-show="locale === 'en'">For teams needing Neriah Pro to build, code 100%, test (Pest ApiContractTest), and deploy live on VPS with SLA warranty.</span>
                                        <span x-show="locale !== 'en'">Untuk Anda yang butuh tim Neriah Pro mengoding 100%, membangun, menguji (Pest ApiContractTest), hingga live deploy di VPS bergaransi SLA.</span>
                                    </p>
                                </div>
                                <div class="mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-800 font-mono text-xs text-blue-600 dark:text-blue-400 font-bold flex items-center justify-between">
                                    <span>
                                        <span x-show="locale === 'en'">50% DP Kickoff Milestone</span>
                                        <span x-show="locale !== 'en'">DP 50% Kickoff Milestone</span>
                                    </span>
                                    <span>
                                        <span x-show="locale === 'en'">Select Studio Track &darr;</span>
                                        <span x-show="locale !== 'en'">Pilih Jalur Studio &darr;</span>
                                    </span>
                                </div>
                            </button>
                        </div>
                    </div>
                @endif

                    <!-- TRACK CONTENT A: JALUR MANDIRI RETAIL CARDS & INSTANT SNAP CHECKOUT -->
                    <div x-show="executionTrack === 'retail'" x-cloak class="space-y-6 pt-2">
                        @php
                            $activeRetailTierSlug = $blueprint->user_metadata['retail_tier']
                                ?? $blueprint->user_metadata['package_tier']
                                ?? ($blueprint->isUltimateRetail() ? 'retail_ultimate' : 'retail_pro');

                            $retailTierName = match(strtolower((string) $activeRetailTierSlug)) {
                                'retail_lite', 'lite' => 'Tier 02 // Lite Specification',
                                'retail_pro', 'pro' => 'Tier 03 // Pro Engineering Directives',
                                'retail_ultimate', 'ultimate', 'agency_partner' => 'Tier 04 // Ultimate Software Factory OS',
                                default => 'Lisensi Digital Retail (Self-Service)',
                            };
                        @endphp

                        @if($hasPaidRetail)
                            <!-- RETAIL LICENSE ACTIVE -->
                            <div class="p-5 bg-emerald-500/10 dark:bg-emerald-950/20 border-2 border-emerald-500 rounded-none space-y-4">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <span class="w-10 h-10 bg-emerald-500 text-black flex items-center justify-center font-bold text-lg flex-shrink-0">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        </span>
                                        <div>
                                            <div class="text-xs uppercase font-mono tracking-wider text-emerald-700 dark:text-emerald-400 font-bold flex items-center gap-2">
                                                <span>
                                                    <span x-show="locale === 'en'">STATUS: RETAIL LICENSE ACTIVE</span>
                                                    <span x-show="locale !== 'en'">STATUS: LISENSI RETAIL AKTIF</span>
                                                </span>
                                                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                            </div>
                                            <h4 class="text-sm font-bold text-zinc-900 dark:text-white font-sans mt-0.5">
                                                {{ $retailTierName }} &bull; 
                                                <span x-show="locale === 'en'">Official Verified License</span>
                                                <span x-show="locale !== 'en'">Lisensi Resmi Terverifikasi</span>
                                            </h4>
                                            <p class="text-xs text-zinc-600 dark:text-zinc-300 font-sans mt-0.5">
                                                <span x-show="locale === 'en'">Your autonomous license payment was successful with lifetime access recorded. All architecture specs, Strict ULID PostgreSQL DDL, Docker containers, and starter kit repo are ready to download below.</span>
                                                <span x-show="locale !== 'en'">Pembayaran lisensi mandiri Anda telah sukses dan tercatat seumur hidup. Seluruh berkas arsitektur, DDL SQL Strict ULID, Docker container, dan starter kit repository siap diunduh langsung di bawah ini.</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="shrink-0">
                                        <span class="px-2.5 py-1 bg-emerald-500 text-black font-mono font-black text-xs uppercase tracking-wider">
                                            LIFETIME ACCESS
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-3 border-t border-emerald-500/20 no-print">
                                    <button 
                                        type="button"
                                        @click.prevent="$dispatch('open-raw-md-warning', { action: 'download' })"
                                        class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-300 dark:border-zinc-700 font-mono font-bold text-xs uppercase tracking-wider py-3 px-3 text-center rounded-none transition flex items-center justify-center gap-2 shadow-sm cursor-pointer"
                                        :title="locale === 'en' ? 'Download Original Markdown (.MD) for AI Coding Agent' : 'Unduh File Asli Markdown (.MD) untuk AI Coding Agent'"
                                    >
                                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        <span x-show="locale === 'en'">Download PRD (.MD)</span>
                                        <span x-show="locale !== 'en'">Unduh PRD (.MD)</span>
                                    </button>

                                    <!-- Multi-Agent AI Rules Hub Button & Dropdown -->
                                    <div class="relative" x-data="{ openAgentMenu: false }" @click.outside="openAgentMenu = false">
                                        <div class="flex items-stretch w-full shadow-sm">
                                            <button 
                                                type="button"
                                                @click="openAiAgentRulesModal('antigravity')"
                                                class="flex-1 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-emerald-700 dark:text-emerald-400 border border-emerald-500/50 font-mono font-bold text-xs uppercase tracking-wider py-3 px-2 text-center rounded-none transition flex items-center justify-center gap-1.5 cursor-pointer"
                                                :title="locale === 'en' ? 'Open AI Rules Hub (Antigravity, Claude, OpenAI, Cursor, etc.)' : 'Buka Hub Aturan AI (Antigravity, Claude, OpenAI, Cursor, dll.)'"
                                            >
                                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                                                <span x-show="locale === 'en'">AI RULES (6 ENGINES)</span>
                                                <span x-show="locale !== 'en'">AI RULES (6 ENGINES)</span>
                                            </button>
                                            <button 
                                                type="button"
                                                @click="openAgentMenu = !openAgentMenu"
                                                class="px-2 bg-emerald-50 dark:bg-zinc-800 hover:bg-emerald-100 dark:hover:bg-zinc-700 text-emerald-700 dark:text-emerald-400 border-y border-r border-emerald-500/50 rounded-none transition flex items-center justify-center cursor-pointer"
                                                :title="locale === 'en' ? 'Quick select AI Agent format' : 'Pilih cepat format AI Agent'"
                                            >
                                                <svg class="w-3.5 h-3.5 transition-transform" :class="openAgentMenu ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </button>
                                        </div>

                                        <!-- Dropdown Menu -->
                                        <div 
                                            x-show="openAgentMenu" 
                                            x-cloak 
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            class="absolute left-0 right-0 sm:right-auto sm:w-72 mt-1 z-30 bg-white dark:bg-zinc-950 border-2 border-emerald-500 rounded-none shadow-2xl py-1 text-xs font-mono"
                                        >
                                            <div class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider flex items-center justify-between">
                                                <span>CHOOSE AI AGENT ENGINE</span>
                                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">6 ENGINES</span>
                                            </div>

                                            <a 
                                                href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'antigravity']) }}"
                                                class="px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-800 dark:text-zinc-200 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between transition border-b border-zinc-100 dark:border-zinc-900"
                                            >
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 bg-cyan-500 rounded-none"></span>
                                                    <span class="font-bold">Google Antigravity &amp; Gemini</span>
                                                </div>
                                                <span class="text-[9px] px-1 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">AGENTS.md</span>
                                            </a>

                                            <a 
                                                href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'claude']) }}"
                                                class="px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-800 dark:text-zinc-200 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between transition border-b border-zinc-100 dark:border-zinc-900"
                                            >
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 bg-amber-500 rounded-none"></span>
                                                    <span class="font-bold">Anthropic Claude Code</span>
                                                </div>
                                                <span class="text-[9px] px-1 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">CLAUDE.md</span>
                                            </a>

                                            <a 
                                                href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'codex']) }}"
                                                class="px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-800 dark:text-zinc-200 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between transition border-b border-zinc-100 dark:border-zinc-900"
                                            >
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 bg-emerald-500 rounded-none"></span>
                                                    <span class="font-bold">OpenAI Codex &amp; ChatGPT</span>
                                                </div>
                                                <span class="text-[9px] px-1 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">CODEX.md</span>
                                            </a>

                                            <a 
                                                href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'cursor']) }}"
                                                class="px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-800 dark:text-zinc-200 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between transition border-b border-zinc-100 dark:border-zinc-900"
                                            >
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 bg-indigo-500 rounded-none"></span>
                                                    <span class="font-bold">Cursor AI IDE</span>
                                                </div>
                                                <span class="text-[9px] px-1 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">.cursorrules</span>
                                            </a>

                                            <a 
                                                href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'windsurf']) }}"
                                                class="px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-800 dark:text-zinc-200 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between transition border-b border-zinc-100 dark:border-zinc-900"
                                            >
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 bg-teal-500 rounded-none"></span>
                                                    <span class="font-bold">Codeium Windsurf</span>
                                                </div>
                                                <span class="text-[9px] px-1 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">.windsurfrules</span>
                                            </a>

                                            <a 
                                                href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'copilot']) }}"
                                                class="px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-800 dark:text-zinc-200 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between transition border-b border-zinc-100 dark:border-zinc-900"
                                            >
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 bg-purple-500 rounded-none"></span>
                                                    <span class="font-bold">GitHub Copilot</span>
                                                </div>
                                                <span class="text-[9px] px-1 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">copilot-instructions.md</span>
                                            </a>

                                            <div class="p-2 bg-zinc-50 dark:bg-zinc-900/80 border-t border-zinc-200 dark:border-zinc-800 flex flex-col gap-1.5">
                                                <a 
                                                    href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'rules-bundle']) }}"
                                                    class="w-full py-1.5 px-2 bg-emerald-500 hover:bg-emerald-400 text-black font-black text-center text-[11px] uppercase tracking-wider transition rounded-none flex items-center justify-center gap-1.5 shadow-sm"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    <span>DOWNLOAD ALL (.ZIP)</span>
                                                </a>

                                                <button 
                                                    type="button"
                                                    @click="openAgentMenu = false; openAiAgentRulesModal('antigravity')"
                                                    class="w-full py-1.5 px-2 bg-zinc-900 dark:bg-zinc-800 hover:bg-zinc-800 dark:hover:bg-zinc-700 text-white font-bold text-center text-[10px] uppercase tracking-wider transition rounded-none flex items-center justify-center gap-1.5 cursor-pointer"
                                                >
                                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span x-show="locale === 'en'">OPEN FULL RULES STUDIO &rarr;</span>
                                                    <span x-show="locale !== 'en'">BUKA STUDIO RULES LENGKAP &rarr;</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <a 
                                        href="{{ route('blueprint.export-scaffold', $blueprint->slug) }}"
                                        class="bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase tracking-wider py-3 px-3 text-center rounded-none transition flex items-center justify-center gap-2 shadow-sm"
                                        :title="locale === 'en' ? 'Download Full Master Starter Kit ZIP (Laravel 13, Filament v5, Docker)' : 'Unduh Master Starter Kit ZIP Lengkap (Laravel 13, Filament v5, Docker)'"
                                    >
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        <span x-show="locale === 'en'">Download Starter Kit (.ZIP)</span>
                                        <span x-show="locale !== 'en'">Unduh Starter Kit (.ZIP)</span>
                                    </a>

                                    <a 
                                        href="/customer/dashboard#licenses"
                                        class="bg-zinc-900 hover:bg-black dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white font-mono font-bold text-xs uppercase tracking-wider py-3 px-3 text-center rounded-none transition flex items-center justify-center gap-2 shadow-sm"
                                    >
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        <span x-show="locale === 'en'">Client Portal</span>
                                        <span x-show="locale !== 'en'">Portal Klien</span>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold uppercase block mb-1">
                                    <span x-show="locale === 'en'">✓ SOFTWARE FACTORY OS LICENSE INSTANTLY ACTIVE IN CLIENT PORTAL</span>
                                    <span x-show="locale !== 'en'">✓ LISENSI SOFTWARE FACTORY OS LANGSUNG AKTIF DI CUSTOMER PORTAL</span>
                                </span>
                                <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                    <span x-show="locale === 'en'">Select a license tier below. Once Midtrans Snap payment (QRIS/VA) is verified, blueprint files, SQL DDL schema, and starter kit ZIP are permanently available in <a href="/customer/dashboard" target="_blank" class="underline text-emerald-500 font-bold">Your Client Portal</a>.</span>
                                    <span x-show="locale !== 'en'">Pilih paket lisensi di bawah. Begitu pembayaran Midtrans Snap (QRIS/VA) diverifikasi, file cetak biru, skema SQL DDL, dan starter-kit ZIP langsung dapat diunduh permanen di <a href="/customer/dashboard" target="_blank" class="underline text-emerald-500 font-bold">Portal Klien Anda</a>.</span>
                                </p>
                            </div>

                            <!-- PERSETUJUAN SYARAT & KETENTUAN SERTA KEBIJAKAN REFUND -->
                            <div class="mb-4 p-3 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700">
                                <label class="flex items-start gap-2.5 cursor-pointer select-none">
                                    <input
                                        type="checkbox"
                                        x-model="agreeRetailTerms"
                                        class="mt-0.5 w-4 h-4 rounded-none border-zinc-300 dark:border-zinc-700 text-emerald-600 focus:ring-emerald-500 cursor-pointer"
                                    >
                                    <span class="text-xs text-zinc-700 dark:text-zinc-300 font-sans leading-relaxed">
                                        <span x-show="locale === 'en'">
                                            I have read and agree to the 
                                            <a href="/terms" target="_blank" class="font-bold underline text-zinc-900 dark:text-white hover:text-emerald-500">Terms of Service</a>, 
                                            <a href="/privacy-policy" target="_blank" class="font-bold underline text-zinc-900 dark:text-white hover:text-emerald-500">Privacy Policy</a>, and 
                                            <a href="/refund-policy" target="_blank" class="font-bold underline text-zinc-900 dark:text-white hover:text-emerald-500">Refund Policy</a> of Neriah Pro.
                                        </span>
                                        <span x-show="locale !== 'en'">
                                            Saya telah membaca dan menyetujui 
                                            <a href="/terms" target="_blank" class="font-bold underline text-zinc-900 dark:text-white hover:text-emerald-500">Syarat &amp; Ketentuan Layanan</a>, 
                                            <a href="/privacy-policy" target="_blank" class="font-bold underline text-zinc-900 dark:text-white hover:text-emerald-500">Kebijakan Privasi</a>, serta 
                                            <a href="/refund-policy" target="_blank" class="font-bold underline text-zinc-900 dark:text-white hover:text-emerald-500">Garansi &amp; Kebijakan Pengembalian Dana (Refund Policy)</a> yang berlaku di Neriah Pro.
                                        </span>
                                    </span>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- TIER 02: LITE -->
                            <div class="p-4 sm:p-5 bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-700 flex flex-col justify-between rounded-none">
                                <div>
                                    <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-[10px] font-mono font-bold uppercase">
                                        TIER 02 // ARCHITECTURE ONLY
                                    </span>
                                    <h4 class="text-lg font-black uppercase text-zinc-900 dark:text-white mt-2 mb-1">
                                        Lite Specification
                                    </h4>
                                    <div class="text-xl font-black font-mono text-zinc-900 dark:text-white mb-3">
                                        Rp {{ $litePrice }}
                                    </div>
                                    <ul class="text-xs space-y-2 text-zinc-600 dark:text-zinc-300 mb-6">
                                        <li class="flex items-start gap-1.5">&bull; <span x-show="locale === 'en'">Full 26-Parameter PRD Document</span><span x-show="locale !== 'en'">Dokumen PRD 26 Parameter Lengkap</span></li>
                                        <li class="flex items-start gap-1.5">&bull; <span x-show="locale === 'en'">Strict ULID PostgreSQL DDL Schema</span><span x-show="locale !== 'en'">Skema DDL PostgreSQL Strict ULID</span></li>
                                        <li class="flex items-start gap-1.5">&bull; <span x-show="locale === 'en'">Lifetime Access License</span><span x-show="locale !== 'en'">Lisensi Akses Seumur Hidup</span></li>
                                    </ul>
                                </div>
                                <button
                                    type="button"
                                    @click="checkoutRetail('retail_lite')"
                                    :disabled="retailCheckingOut || !agreeRetailTerms"
                                    class="w-full bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black font-mono font-bold text-xs uppercase py-3 px-3 transition cursor-pointer text-center"
                                    :class="(!agreeRetailTerms ? 'opacity-50 cursor-not-allowed' : '')"
                                >
                                    <span x-show="!retailCheckingOut">
                                        <span x-show="locale === 'en'">Buy Lite License &rarr;</span>
                                        <span x-show="locale !== 'en'">Beli Lisensi Lite &rarr;</span>
                                    </span>
                                    <span x-show="retailCheckingOut" x-text="locale === 'en' ? 'Processing...' : 'Memproses...'">Memproses...</span>
                                </button>
                            </div>

                            <!-- TIER 03: PRO (RECOMMENDED) -->
                            <div class="p-4 sm:p-5 bg-white dark:bg-zinc-900 border-2 border-emerald-500 flex flex-col justify-between rounded-none relative">
                                <div class="absolute -top-3 right-4 bg-emerald-500 text-black font-mono text-[9px] font-black uppercase px-2.5 py-0.5 tracking-wider">
                                    <span x-show="locale === 'en'">POPULAR // DEVELOPER CHOICE</span>
                                    <span x-show="locale !== 'en'">POPULER // DEVELOPER CHOICE</span>
                                </div>
                                <div>
                                    <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-mono font-bold uppercase">
                                        TIER 03 // FULL AI DIRECTIVES
                                    </span>
                                    <h4 class="text-lg font-black uppercase text-zinc-900 dark:text-white mt-2 mb-1">
                                        Pro Engineering Directives
                                    </h4>
                                    <div class="text-xl font-black font-mono text-zinc-900 dark:text-white mb-3">
                                        Rp {{ $proPrice }}
                                    </div>
                                    <ul class="text-xs space-y-2 text-zinc-600 dark:text-zinc-300 mb-6">
                                        <li class="flex items-start gap-1.5">&bull; <span x-show="locale === 'en'">All Lite Tier Features</span><span x-show="locale !== 'en'">Seluruh Fitur Paket Lite</span></li>
                                        <li class="flex items-start gap-1.5">&bull; <span x-show="locale === 'en'">AI Coding Agent Rules (.cursorrules &amp; AGENTS.md)</span><span x-show="locale !== 'en'">Aturan AI Coding Agent (.cursorrules &amp; AGENTS.md)</span></li>
                                        <li class="flex items-start gap-1.5">&bull; <span x-show="locale === 'en'">100+ Rows Synthetic Realistic Seeders</span><span x-show="locale !== 'en'">Seeder Sintetis Realistis 100+ Baris</span></li>
                                        <li class="flex items-start gap-1.5">&bull; <span x-show="locale === 'en'">Ready-to-use Docker Container Config</span><span x-show="locale !== 'en'">Docker Container Config Siap Pakai</span></li>
                                    </ul>
                                </div>
                                <button
                                    type="button"
                                    @click="checkoutRetail('retail_pro')"
                                    :disabled="retailCheckingOut || !agreeRetailTerms"
                                    class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase py-3 px-3 transition cursor-pointer text-center shadow-xs"
                                    :class="(!agreeRetailTerms ? 'opacity-50 cursor-not-allowed' : '')"
                                >
                                    <span x-show="!retailCheckingOut">
                                        <span x-show="locale === 'en'">Buy Pro License &rarr;</span>
                                        <span x-show="locale !== 'en'">Beli Lisensi Pro &rarr;</span>
                                    </span>
                                    <span x-show="retailCheckingOut" x-text="locale === 'en' ? 'Processing...' : 'Memproses...'">Memproses...</span>
                                </button>
                            </div>

                            <!-- TIER 04: ULTIMATE -->
                            <div class="p-4 sm:p-5 bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-700 hover:border-zinc-500 flex flex-col justify-between rounded-none">
                                <div>
                                    <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-[10px] font-mono font-bold uppercase">
                                        TIER 04 // MASTER STARTER KIT
                                    </span>
                                    <h4 class="text-lg font-black uppercase text-zinc-900 dark:text-white mt-2 mb-1">
                                        Ultimate Software Factory OS
                                    </h4>
                                    <div class="text-xl font-black font-mono text-zinc-900 dark:text-white mb-3">
                                        Rp {{ $ultimatePrice }}
                                    </div>
                                    <ul class="text-xs space-y-2 text-zinc-600 dark:text-zinc-300 mb-6">
                                        <li class="flex items-start gap-1.5">&bull; <span x-show="locale === 'en'">All Lite &amp; Pro Features</span><span x-show="locale !== 'en'">Seluruh Fitur Paket Lite &amp; Pro</span></li>
                                        <li class="flex items-start gap-1.5">&bull; <span x-show="locale === 'en'">Master Starter Repository ZIP (Laravel 13, Filament v5, Livewire 4)</span><span x-show="locale !== 'en'">Master Starter Repository ZIP (Laravel 13, Filament v5, Livewire 4)</span></li>
                                        <li class="flex items-start gap-1.5">&bull; <span x-show="locale === 'en'">60-Min 1-on-1 Architecture Consultation with Lead Architect</span><span x-show="locale !== 'en'">1-on-1 Architecture Call 60 Menit dengan Lead Architect</span></li>
                                        <li class="flex items-start gap-1.5">&bull; <span x-show="locale === 'en'">100% License Fee Deducted from DP if Upgraded to Studio</span><span x-show="locale !== 'en'">100% Biaya Lisensi Memotong DP jika Upgrade ke Studio</span></li>
                                    </ul>
                                </div>
                                <button
                                    type="button"
                                    @click="checkoutRetail('retail_ultimate')"
                                    :disabled="retailCheckingOut || !agreeRetailTerms"
                                    class="w-full bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black font-mono font-bold text-xs uppercase py-3 px-3 transition cursor-pointer text-center"
                                    :class="(!agreeRetailTerms ? 'opacity-50 cursor-not-allowed' : '')"
                                >
                                    <span x-show="!retailCheckingOut">
                                        <span x-show="locale === 'en'">Buy Ultimate License &rarr;</span>
                                        <span x-show="locale !== 'en'">Beli Lisensi Ultimate &rarr;</span>
                                    </span>
                                    <span x-show="retailCheckingOut" x-text="locale === 'en' ? 'Processing...' : 'Memproses...'">Memproses...</span>
                                </button>
                            </div>
                        </div>
                        @endif

                        <!-- UPGRADE TO DEDICATED STUDIO TURNKEY (UPSELL BANNER WITH 100% RETAIL DEDUCTION) -->
                        <div class="p-4 sm:p-5 bg-gradient-to-r from-zinc-900 to-zinc-950 border-2 border-emerald-500 text-white rounded-none space-y-2 mt-6">
                            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-emerald-500 text-black text-[10px] font-mono font-black uppercase">
                                            <span x-show="locale === 'en'">🚀 UPGRADE PATH // ZERO-LOSS GUARANTEE</span>
                                            <span x-show="locale !== 'en'">🚀 UPGRADE PATH // ZERO-LOSS GUARANTEE</span>
                                        </span>
                                        <span class="text-xs text-emerald-400 font-mono font-bold">
                                            <span x-show="locale === 'en'">100% Retail License Fee Deducted from Turnkey DP</span>
                                            <span x-show="locale !== 'en'">100% Biaya Lisensi Retail Dipotong dari DP Turnkey</span>
                                        </span>
                                    </div>
                                    <h4 class="text-base font-black uppercase tracking-tight text-white">
                                        <span x-show="locale === 'en'">Want Neriah Pro Senior Architects to Code &amp; Launch 100%?</span>
                                        <span x-show="locale !== 'en'">Ingin Tim Senior Architect Neriah Pro yang Mengoding &amp; Meluncurkan 100%?</span>
                                    </h4>
                                    <p class="text-xs text-zinc-300 font-sans mt-1 max-w-2xl leading-relaxed">
                                        <span x-show="locale === 'en'">If your internal team needs dedicated professionals to build from scratch to live VPS with SLA warranty: <strong>100% of your paid retail license fee will be credited directly towards your 50% Turnkey Project Kickoff DP invoice</strong>.</span>
                                        <span x-show="locale !== 'en'">Jika tim internal Anda membutuhkan tim profesional untuk membangun dari nol hingga live di server VPS bergaransi SLA: <strong>100% biaya lisensi retail yang sudah Anda bayarkan akan diperhitungkan sebagai potongan langsung terhadap tagihan DP 50% Kickoff Proyek Turnkey</strong>.</span>
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="executionTrack = 'studio'"
                                    class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-black font-black font-mono text-xs uppercase tracking-wider rounded-none shrink-0 cursor-pointer shadow-lg transition"
                                >
                                    <span x-show="locale === 'en'">View Turnkey Studio Track &rarr;</span>
                                    <span x-show="locale !== 'en'">Lihat Jalur Turnkey Studio &rarr;</span>
                                </button>
                            </div>
                        </div>
                    </div>

                <!-- TRACK CONTENT B: JALUR TURNKEY STUDIO (CONTRACT SIGNING & DP 50%) -->
                <div x-show="executionTrack === 'studio'">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-6 mb-6">
                    <div>
                        <span class="px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-widest bg-emerald-500 text-black inline-block mb-2 rounded-none">
                            LEGAL &amp; PAYMENT PROTOCOL
                        </span>
                        <h3 class="text-xl sm:text-2xl font-black uppercase tracking-tight flex items-center gap-2">
                            <span class="w-6 h-6 bg-emerald-500 text-black font-mono font-bold text-xs inline-flex items-center justify-center rounded-none">12</span>
                            <span x-show="locale === 'en'">Scope Lock, Digital Sign-Off &amp; Milestone DP</span>
                            <span x-show="locale !== 'en'">Kunci Scope Proyek, Persetujuan Digital &amp; DP</span>
                        </h3>
                        <p class="text-zinc-600 dark:text-zinc-400 text-xs mt-1 font-sans">
                            <span x-show="locale === 'en'">Project officially kicks off upon digital contract sign-off, cryptographic hash lock, and DP confirmation or free voucher grant.</span>
                            <span x-show="locale !== 'en'">Pengerjaan proyek resmi dimulai setelah penandatanganan digital, penguncian hash SHA-256, dan konfirmasi DP via Midtrans atau voucher pelayanan.</span>
                        </p>
                    </div>
                    <div class="text-left sm:text-right font-mono">
                        @if($hasPaidRetail)
                            @php
                                $paidRetailAmount = $blueprint->getPaidRetailAmount();
                            @endphp
                            <div class="space-y-1">
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-500/40 text-[10px] font-bold uppercase inline-flex items-center gap-1">
                                    <span x-show="locale === 'en'">🚀 UPGRADE TO TURNKEY // RETAIL LICENSE CREDIT -Rp {{ number_format($paidRetailAmount, 0, ',', '.') }}</span>
                                    <span x-show="locale !== 'en'">🚀 UPGRADE KE TURNKEY // KREDIT LISENSI RETAIL -Rp {{ number_format($paidRetailAmount, 0, ',', '.') }}</span>
                                </span>
                                <div class="text-xl font-black text-emerald-700 dark:text-emerald-400">
                                    <span x-text="(locale === 'en' ? 'Remaining DP Balance: Rp ' : 'Sisa Tagihan DP: Rp ') + Math.max(0, (tierAmounts[selectedTier]?.dp || 0) - {{ (int)$paidRetailAmount }}).toLocaleString('id-ID')"></span>
                                </div>
                                <div class="text-[10px] text-zinc-500 dark:text-zinc-400 flex flex-wrap justify-end gap-x-2">
                                    <span x-text="'Base DP (50%): Rp ' + (tierAmounts[selectedTier]?.dp || 0).toLocaleString('id-ID')"></span>
                                    <span>&bull;</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">
                                        <span x-show="locale === 'en'">Retail Credit: -Rp {{ number_format($paidRetailAmount, 0, ',', '.') }}</span>
                                        <span x-show="locale !== 'en'">Kredit Retail: -Rp {{ number_format($paidRetailAmount, 0, ',', '.') }}</span>
                                    </span>
                                    <span>&bull;</span>
                                    <span x-text="(locale === 'en' ? 'Total Contract: Rp ' : 'Total Kontrak: Rp ') + (tierAmounts[selectedTier]?.contract || 0).toLocaleString('id-ID')"></span>
                                </div>
                            </div>
                        @else
                            <span class="text-zinc-500 dark:text-zinc-400 text-xs block">
                                @if($blueprint->isDpConfirmed())
                                    <span x-show="locale === 'en'">VERIFIED TIER: </span>
                                    <span x-show="locale !== 'en'">TERMIN TERVERIFIKASI: </span>
                                @elseif(($contractDoc = $contractDocument ?? $blueprint->getContractDocument()) && $contractDoc->status === 'signed' && (float)$contractDoc->contract_amount > 0)
                                    <span x-show="locale === 'en'">EXECUTED CONTRACT TIER: </span>
                                    <span x-show="locale !== 'en'">TERMIN KONTRAK SAH: </span>
                                @else
                                    <span x-show="locale === 'en'">SELECTED TIER: </span>
                                    <span x-show="locale !== 'en'">TERMIN TERPILIH: </span>
                                @endif
                                <span class="text-zinc-900 dark:text-white font-bold" x-text="(locale === 'en' ? tierAmounts[selectedTier]?.name_en : tierAmounts[selectedTier]?.name_id) || tierAmounts[selectedTier]?.name || 'Standard Velocity'"></span>
                            </span>
                            @if($blueprint->is_free_grant)
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-500/40 text-[10px] font-bold uppercase inline-block mb-1">
                                    <span x-show="locale === 'en'">✓ FREE SERVICE (RP 0)</span>
                                    <span x-show="locale !== 'en'">✓ SUBSIDI KASIH (RP 0)</span>
                                </span>
                                <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 block">
                                    <span x-show="locale === 'en'">RP 0 (FREE SERVICE)</span>
                                    <span x-show="locale !== 'en'">RP 0 (PELAYANAN KASIH)</span>
                                </span>
                                <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block">VOUCHER: {{ $blueprint->voucher_code }}</span>
                            @elseif($blueprint->isDpConfirmed())
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-500/40 text-[10px] font-bold uppercase inline-flex items-center gap-1 mb-1">
                                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    <span x-show="locale === 'en'">CONFIRMED DP // ACTIVE SPRINT</span>
                                    <span x-show="locale !== 'en'">DP TERKONFIRMASI // SPRINT AKTIF</span>
                                </span>
                                @php
                                    $contractDoc = $contractDocument ?? $blueprint->getContractDocument();
                                    $paidDp = $contractDoc?->dp_amount;
                                @endphp
                                <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 block">
                                    <span x-show="locale === 'en'">{{ $paidDp ? 'Rp ' . number_format($paidDp, 0, ',', '.') : 'PAID (50% DP)' }}</span>
                                    <span x-show="locale !== 'en'">{{ $paidDp ? 'Rp ' . number_format($paidDp, 0, ',', '.') : 'LUNAS (DP 50%)' }}</span>
                                </span>
                                <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block">
                                    <span x-show="locale === 'en'">{{ $contractDoc?->contract_amount ? 'Total Contract: Rp ' . number_format($contractDoc->contract_amount, 0, ',', '.') : 'Scope Locked' }} &bull; SPRINT 1 IN PROGRESS</span>
                                    <span x-show="locale !== 'en'">{{ $contractDoc?->contract_amount ? 'Total Kontrak: Rp ' . number_format($contractDoc->contract_amount, 0, ',', '.') : 'Kontrak Terkunci' }} &bull; SPRINT 1 IN PROGRESS</span>
                                </span>
                            @elseif(($contractDoc = $contractDocument ?? $blueprint->getContractDocument()) && $contractDoc->status === 'signed' && (float)$contractDoc->contract_amount > 0)
                                <span class="px-2 py-0.5 bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 border border-cyan-500/40 text-[10px] font-bold uppercase inline-flex items-center gap-1 mb-1">
                                    <svg class="w-3 h-3 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span x-show="locale === 'en'">SIGNED CONTRACT // AWAITING DP</span>
                                    <span x-show="locale !== 'en'">KONTRAK TERTANDATANGANI // MENUNGGU DP</span>
                                </span>
                                <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 block">
                                    DP (50%): Rp {{ number_format($contractDoc->dp_amount ?: ($contractDoc->contract_amount * 0.50), 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block">
                                    <span x-show="locale === 'en'">Total Contract: Rp {{ number_format($contractDoc->contract_amount, 0, ',', '.') }}</span>
                                    <span x-show="locale !== 'en'">Total Kontrak: Rp {{ number_format($contractDoc->contract_amount, 0, ',', '.') }}</span>
                                    &bull; <span x-show="locale === 'en'">LEGALLY BINDING CONTRACT</span><span x-show="locale !== 'en'">KONTRAK SAH MENGIKAT</span>
                                </span>
                            @else
                                <span class="text-xl font-black text-emerald-600 dark:text-emerald-400" x-text="'DP (50%): Rp ' + (tierAmounts[selectedTier]?.dp || 0).toLocaleString('id-ID')"></span>
                                <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block" x-text="(locale === 'en' ? 'Total Contract: Rp ' : 'Total Kontrak: Rp ') + (tierAmounts[selectedTier]?.contract || 0).toLocaleString('id-ID')"></span>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Digital Sign-Off Cryptographic Audit Trail Seal -->
                <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono mb-6 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800/80 pb-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            <span class="font-bold text-zinc-900 dark:text-white uppercase tracking-wider text-[11px]">
                                SHA-256 SPECIFICATION INTEGRITY SEAL
                            </span>
                            @if($blueprint->isDpConfirmed())
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-500/40 text-[10px] font-bold inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    SCOPE FROZEN &amp; DP CONFIRMED
                                </span>
                            @elseif($blueprint->isContractSigned() && ! $hasPaidRetail)
                                <span class="px-2 py-0.5 bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 border border-cyan-500/40 text-[10px] font-bold inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    CONTRACT SIGNED &amp; SCOPE LOCKED
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/40 text-[10px] font-bold">
                                    READY FOR SIGN-OFF
                                </span>
                            @endif
                        </div>
                        <div class="text-[10px] text-zinc-500 dark:text-zinc-400">
                            SINGLE SOURCE OF TRUTH (ANTI-DISPUTE)
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-900/90 p-3 border border-zinc-200 dark:border-zinc-800 text-[11px] leading-relaxed">
                        <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400 mb-1">
                            <span class="text-zinc-500 dark:text-zinc-400 uppercase">Cryptographic Document Checksum (SHA-256):</span>
                            <button 
                                type="button" 
                                @click="navigator.clipboard.writeText('{{ $blueprint->document_sha256 ?: $blueprint->calculatePrdHash() }}'); if(window.showToast) window.showToast({ type: 'success', title: (locale === 'en' ? 'SHA-256 COPIED' : 'SHA-256 DISALIN'), message: (locale === 'en' ? 'Cryptographic hash successfully copied to clipboard.' : 'Cryptographic hash berhasil disalin ke clipboard.') })"
                                class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300 text-[10px] font-bold uppercase underline"
                            >
                                <span x-show="locale === 'en'">Copy Hash</span>
                                <span x-show="locale !== 'en'">Salin Hash</span>
                            </button>
                        </div>
                        <code class="text-emerald-600 dark:text-emerald-400 break-all select-all font-mono text-[11px] block">
                            {{ $blueprint->document_sha256 ?: $blueprint->calculatePrdHash() }}
                        </code>
                        @php
                            $contractDoc = $contractDocument ?? $blueprint->getContractDocument();
                            $hasAudit = ! $hasPaidRetail && (($blueprint->signed_agreement && $blueprint->signed_at) || ($contractDoc && $contractDoc->signed_at && (float)$contractDoc->contract_amount > 0));
                        @endphp
                        @if($hasAudit)
                            <div class="mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-800/80 flex flex-wrap gap-x-4 gap-y-1 text-[10px] text-zinc-600 dark:text-zinc-400">
                                <span><span x-show="locale === 'en'">Signer: </span><span x-show="locale !== 'en'">Penandatangan: </span><strong class="text-zinc-900 dark:text-zinc-200">{{ $blueprint->client_name ?: ($contractDoc?->signer_name ?: $blueprint->nama_bisnis) }}</strong></span>
                                <span><span x-show="locale === 'en'">Timestamp: </span><span x-show="locale !== 'en'">Waktu: </span><strong class="text-zinc-900 dark:text-zinc-200">{{ ($blueprint->signed_at ?: $contractDoc?->signed_at)?->format('d M Y H:i:s T') }}</strong></span>
                                <span><span x-show="locale === 'en'">Audit IP: </span><span x-show="locale !== 'en'">IP Audit: </span><strong class="text-zinc-900 dark:text-zinc-200">{{ $blueprint->signer_ip ?: ($contractDoc?->signer_ip_address ?: 'Recorded') }}</strong></span>
                                @if(!empty($blueprint->staging_url))
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">Sandbox: <strong class="text-emerald-700 dark:text-emerald-300 font-mono">{{ $blueprint->staging_url }}</strong></span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <p class="text-zinc-600 dark:text-zinc-400 text-xs font-sans leading-relaxed">
                        <span x-show="locale === 'en'">All features and architecture specifications in this PRD are cryptographically bound. Any subsequent scope adjustments require mutual Change Request (CR) / Addendum without altering the baseline master document.</span>
                        <span x-show="locale !== 'en'">Seluruh fitur dan arsitektur dalam PRD ini terikat secara kriptografis. Setiap perubahan di kemudian hari wajib melalui kesepakatan Change Request (CR) / Addendum tanpa mengubah basis dokumen utama.</span>
                    </p>
                </div>

                <!-- Action State Engine: Active Sprint Cockpit vs Sign & Pay Actions -->
                @if($blueprint->isDpConfirmed() && ! $hasPaidRetail)
                    <!-- PROYEK SUDAH BAYAR DP / AKTIF: Tampilkan Cockpit Kontrak & Status Sprint (Bukan Tombol Bayar / Cart) -->
                    <div class="p-5 bg-emerald-500/10 dark:bg-emerald-950/20 border-2 border-emerald-500/50 rounded-none space-y-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 bg-emerald-500 text-black flex items-center justify-center font-bold text-lg flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </span>
                                <div>
                                    <div class="text-xs uppercase font-mono tracking-wider text-emerald-700 dark:text-emerald-400 font-bold flex items-center gap-2">
                                        <span>
                                            <span x-show="locale === 'en'">STATUS: VERIFIED DP PAYMENT &bull; ACTIVE SPRINT</span>
                                            <span x-show="locale !== 'en'">STATUS: PEMBAYARAN DP TERVERIFIKASI &bull; SPRINT AKTIF</span>
                                        </span>
                                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                    </div>
                                    <h4 class="text-sm font-bold text-zinc-900 dark:text-white font-sans mt-0.5">
                                        <span x-show="locale === 'en'">Official Scope Locked &amp; Project Currently in Development</span>
                                        <span x-show="locale !== 'en'">Ruang Lingkup (Scope) Resmi Dikunci &amp; Proyek Sedang Dikerjakan</span>
                                    </h4>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-300 font-sans mt-0.5">
                                        <span x-show="locale === 'en'">Digital contract and PRD technical specifications are legally binding. No further DP payment or cart checkout required.</span>
                                        <span x-show="locale !== 'en'">Kontrak digital dan spesifikasi teknis PRD telah mengikat secara hukum. Anda tidak perlu membayar DP lagi atau menambahkan ke keranjang belanja.</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-2 border-t border-emerald-500/20 no-print">
                            @php
                                $contractDoc = $contractDocument ?? $blueprint->getContractDocument();
                            @endphp
                            @if($contractDoc)
                                <a 
                                    href="{{ route('document.sign', $contractDoc->id) }}"
                                    target="_blank"
                                    class="bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-bold text-xs uppercase tracking-wider py-3.5 px-4 text-center rounded-none transition flex items-center justify-center gap-2 shadow-sm"
                                >
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span x-show="locale === 'en'">View Digital Contract</span>
                                    <span x-show="locale !== 'en'">Lihat Kontrak Digital</span>
                                </a>
                            @endif

                            @if(!empty($blueprint->staging_url))
                                <a 
                                    href="{{ $blueprint->staging_url }}"
                                    target="_blank"
                                    class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-emerald-700 dark:text-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-300 border border-emerald-500/50 font-mono font-bold text-xs uppercase tracking-wider py-3.5 px-4 text-center rounded-none transition flex items-center justify-center gap-2 shadow-sm"
                                >
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    <span x-show="locale === 'en'">Open Staging Sandbox</span>
                                    <span x-show="locale !== 'en'">Buka Sandbox Staging</span>
                                </a>
                            @endif

                            <button 
                                type="button"
                                @click.prevent="$dispatch('open-raw-md-warning', { action: 'download' })"
                                class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-300 dark:border-zinc-700 font-mono font-bold text-xs uppercase tracking-wider py-3.5 px-4 text-center rounded-none transition flex items-center justify-center gap-2 shadow-sm cursor-pointer"
                                :title="locale === 'en' ? 'Download Original Markdown (.MD) for AI Coding Agent' : 'Unduh File Asli Markdown (.MD) untuk AI Coding Agent'"
                            >
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                <span x-show="locale === 'en'">Download PRD (.MD)</span>
                                <span x-show="locale !== 'en'">Unduh PRD (.MD)</span>
                            </button>

                            <!-- Multi-Agent AI Rules Hub Button & Dropdown -->
                            <div class="relative" x-data="{ openAgentMenuBottom: false }" @click.outside="openAgentMenuBottom = false">
                                <div class="flex items-stretch w-full shadow-sm">
                                    <button 
                                        type="button"
                                        @click="openAiAgentRulesModal('antigravity')"
                                        class="flex-1 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-emerald-700 dark:text-emerald-400 border border-emerald-500/50 font-mono font-bold text-xs uppercase tracking-wider py-3.5 px-3 text-center rounded-none transition flex items-center justify-center gap-1.5 cursor-pointer"
                                        :title="locale === 'en' ? 'Open AI Rules Hub (Antigravity, Claude, OpenAI, Cursor, etc.)' : 'Buka Hub Aturan AI (Antigravity, Claude, OpenAI, Cursor, dll.)'"
                                    >
                                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                                        <span x-show="locale === 'en'">AI RULES (6 ENGINES)</span>
                                        <span x-show="locale !== 'en'">AI RULES (6 ENGINES)</span>
                                    </button>
                                    <button 
                                        type="button"
                                        @click="openAgentMenuBottom = !openAgentMenuBottom"
                                        class="px-2.5 bg-emerald-50 dark:bg-zinc-800 hover:bg-emerald-100 dark:hover:bg-zinc-700 text-emerald-700 dark:text-emerald-400 border-y border-r border-emerald-500/50 rounded-none transition flex items-center justify-center cursor-pointer"
                                        :title="locale === 'en' ? 'Quick select AI Agent format' : 'Pilih cepat format AI Agent'"
                                    >
                                        <svg class="w-3.5 h-3.5 transition-transform" :class="openAgentMenuBottom ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                </div>

                                <!-- Dropdown Menu -->
                                <div 
                                    x-show="openAgentMenuBottom" 
                                    x-cloak 
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute left-0 right-0 sm:right-auto sm:w-72 mt-1 z-30 bg-white dark:bg-zinc-950 border-2 border-emerald-500 rounded-none shadow-2xl py-1 text-xs font-mono"
                                >
                                    <div class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider flex items-center justify-between">
                                        <span>CHOOSE AI AGENT ENGINE</span>
                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">6 ENGINES</span>
                                    </div>

                                    <a 
                                        href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'antigravity']) }}"
                                        class="px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-800 dark:text-zinc-200 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between transition border-b border-zinc-100 dark:border-zinc-900"
                                    >
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 bg-cyan-500 rounded-none"></span>
                                            <span class="font-bold">Google Antigravity &amp; Gemini</span>
                                        </div>
                                        <span class="text-[9px] px-1 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">AGENTS.md</span>
                                    </a>

                                    <a 
                                        href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'claude']) }}"
                                        class="px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-800 dark:text-zinc-200 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between transition border-b border-zinc-100 dark:border-zinc-900"
                                    >
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 bg-amber-500 rounded-none"></span>
                                            <span class="font-bold">Anthropic Claude Code</span>
                                        </div>
                                        <span class="text-[9px] px-1 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">CLAUDE.md</span>
                                    </a>

                                    <a 
                                        href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'codex']) }}"
                                        class="px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-800 dark:text-zinc-200 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between transition border-b border-zinc-100 dark:border-zinc-900"
                                    >
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 bg-emerald-500 rounded-none"></span>
                                            <span class="font-bold">OpenAI Codex &amp; ChatGPT</span>
                                        </div>
                                        <span class="text-[9px] px-1 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">CODEX.md</span>
                                    </a>

                                    <a 
                                        href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'cursor']) }}"
                                        class="px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-800 dark:text-zinc-200 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between transition border-b border-zinc-100 dark:border-zinc-900"
                                    >
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 bg-indigo-500 rounded-none"></span>
                                            <span class="font-bold">Cursor AI IDE</span>
                                        </div>
                                        <span class="text-[9px] px-1 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">.cursorrules</span>
                                    </a>

                                    <a 
                                        href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'windsurf']) }}"
                                        class="px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-800 dark:text-zinc-200 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between transition border-b border-zinc-100 dark:border-zinc-900"
                                    >
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 bg-teal-500 rounded-none"></span>
                                            <span class="font-bold">Codeium Windsurf</span>
                                        </div>
                                        <span class="text-[9px] px-1 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">.windsurfrules</span>
                                    </a>

                                    <a 
                                        href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'copilot']) }}"
                                        class="px-3 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-900 text-zinc-800 dark:text-zinc-200 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between transition border-b border-zinc-100 dark:border-zinc-900"
                                    >
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 bg-purple-500 rounded-none"></span>
                                            <span class="font-bold">GitHub Copilot</span>
                                        </div>
                                        <span class="text-[9px] px-1 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">copilot-instructions.md</span>
                                    </a>

                                    <div class="p-2 bg-zinc-50 dark:bg-zinc-900/80 border-t border-zinc-200 dark:border-zinc-800 flex flex-col gap-1.5">
                                        <a 
                                            href="{{ route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'rules-bundle']) }}"
                                            class="w-full py-1.5 px-2 bg-emerald-500 hover:bg-emerald-400 text-black font-black text-center text-[11px] uppercase tracking-wider transition rounded-none flex items-center justify-center gap-1.5 shadow-sm"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            <span>DOWNLOAD ALL (.ZIP)</span>
                                        </a>

                                        <button 
                                            type="button"
                                            @click="openAgentMenuBottom = false; openAiAgentRulesModal('antigravity')"
                                            class="w-full py-1.5 px-2 bg-zinc-900 dark:bg-zinc-800 hover:bg-zinc-800 dark:hover:bg-zinc-700 text-white font-bold text-center text-[10px] uppercase tracking-wider transition rounded-none flex items-center justify-center gap-1.5 cursor-pointer"
                                        >
                                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span x-show="locale === 'en'">OPEN FULL RULES STUDIO &rarr;</span>
                                            <span x-show="locale !== 'en'">BUKA STUDIO RULES LENGKAP &rarr;</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button 
                                type="button" 
                                @click="showAiPromptModal = true"
                                class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 font-mono font-bold text-xs uppercase tracking-wider py-3.5 px-4 text-center rounded-none transition flex items-center justify-center gap-2 shadow-sm cursor-pointer"
                            >
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span>AI Agent Ingestion</span>
                            </button>
                        </div>
                    </div>
                @elseif($blueprint->isContractSigned() && ! $hasPaidRetail)
                    <!-- KONTRAK SUDAH DITANDATANGANI TAPI BELUM BAYAR DP -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 no-print">
                        @php
                            $contractDoc = $contractDocument ?? $blueprint->getContractDocument();
                        @endphp
                        @if($contractDoc)
                            <a 
                                href="{{ route('document.sign', $contractDoc->id) }}"
                                target="_blank"
                                class="w-full h-full bg-cyan-600 hover:bg-cyan-500 text-white font-mono font-bold text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none transition flex items-center justify-center gap-2"
                            >
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span x-show="locale === 'en'">View Signed Contract</span>
                                <span x-show="locale !== 'en'">Lihat Kontrak Tertandatangani</span>
                            </a>
                        @endif

                        <button 
                            type="button" 
                            @click="paymentModalOpen = true"
                            class="w-full h-full bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none transition flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <span>
                                <span x-show="locale === 'en'">Pay DP Now &rarr;</span>
                                <span x-show="locale !== 'en'">Bayar DP Sekarang &rarr;</span>
                            </span>
                            <svg class="w-4 h-4 text-black flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </button>

                        <form method="POST" action="{{ route('cart.add', $blueprint->slug) }}" class="m-0">
                            @csrf
                            <input type="hidden" name="tier" :value="selectedTier">
                            <button 
                                type="submit" 
                                class="w-full h-full bg-zinc-100 dark:bg-zinc-900 hover:bg-zinc-200 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-mono font-bold text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none border border-zinc-300 dark:border-zinc-700 transition flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <span>
                                    <span x-show="locale === 'en'">Add to Cart</span>
                                    <span x-show="locale !== 'en'">Tambahkan ke Cart</span>
                                </span>
                                <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </button>
                        </form>
                    </div>
                @else
                    <!-- DRAFT: BELUM TANDATANGAN & BELUM BAYAR DP -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 no-print">
                        <form method="POST" action="{{ route('blueprint.generate-contract', $blueprint->slug) }}" class="m-0">
                            @csrf
                            <input type="hidden" name="tier" :value="selectedTier">
                            <button 
                                type="submit"
                                class="w-full h-full bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none transition flex items-center justify-center gap-2 cursor-pointer shadow-md"
                            >
                                @if($hasPaidRetail)
                                    <span>
                                        <span x-show="locale === 'en'">Sign Turnkey Contract (Retail Credit Active) &rarr;</span>
                                        <span x-show="locale !== 'en'">Tanda Tangani Kontrak Turnkey (Potongan Kredit Retail Aktif) &rarr;</span>
                                    </span>
                                @else
                                    <span>
                                        <span x-show="locale === 'en'">Sign Contract &amp; Lock Scope</span>
                                        <span x-show="locale !== 'en'">Tanda Tangani Kontrak &amp; Kunci Scope</span>
                                    </span>
                                @endif
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                        </form>

                        <button 
                            type="button" 
                            @click="paymentModalOpen = true"
                            class="w-full h-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-900 dark:text-white font-mono font-bold text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none border border-zinc-300 dark:border-zinc-700 transition flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <span>
                                <span x-show="locale === 'en'">Pay DP / Claim Voucher</span>
                                <span x-show="locale !== 'en'">Bayar DP / Klaim Voucher</span>
                            </span>
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </button>

                        <form method="POST" action="{{ route('cart.add', $blueprint->slug) }}" class="m-0">
                            @csrf
                            <input type="hidden" name="tier" :value="selectedTier">
                            <button 
                                type="submit" 
                                class="w-full h-full bg-zinc-100 dark:bg-zinc-900 hover:bg-zinc-200 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-mono font-bold text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none border border-zinc-300 dark:border-zinc-700 transition flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <span>
                                    <span x-show="locale === 'en'">Add to Cart</span>
                                    <span x-show="locale !== 'en'">Tambahkan ke Cart</span>
                                </span>
                                <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </button>
                        </form>
                    </div>
                @endif
                </div> <!-- END TRACK CONTENT B (STUDIO) -->
                </div> <!-- END ACCORDION BODY SECTION-10 -->
            </section>
