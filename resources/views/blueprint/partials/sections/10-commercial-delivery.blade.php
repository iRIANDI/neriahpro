            <section id="section-10" class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white border-2 border-emerald-600 dark:border-emerald-500 p-6 sm:p-8 mb-8 rounded-none prd-scroll-target shadow-sm">
                <!-- Section 12 Header Accordion Bar -->
                <div class="flex items-center justify-between gap-2 mb-6 border-b border-zinc-200 dark:border-zinc-800 pb-3 cursor-pointer select-none" @click="toggleSection('section-10')">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-emerald-600 text-white font-mono font-bold text-xs flex items-center justify-center rounded-none">12</span>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Kunci Scope, Digital Contract &amp; Pembayaran DP / Lisensi</h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">SHA-256 Scope Seal, Midtrans Instant Snap &amp; Dedicated Engineer Slot</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 border border-emerald-300 dark:border-emerald-800 hidden sm:inline">
                            EKSEKUSI PROYEK
                        </span>
                        <button 
                            type="button" 
                            @click.stop="toggleSection('section-10')" 
                            class="px-2 py-1 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none flex items-center gap-1 font-mono text-xs border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700 ml-1 cursor-pointer"
                            :title="sectionCollapses['section-10'] ? 'Buka seksi ini' : 'Ciutkan seksi ini'"
                        >
                            <span class="text-[10px] uppercase font-bold text-zinc-400 dark:text-zinc-500 hidden sm:inline" x-text="sectionCollapses['section-10'] ? 'BUKA' : 'CIUTKAN'"></span>
                            <svg class="w-4 h-4 transform transition-transform duration-200" :class="sectionCollapses['section-10'] ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Collapsed Preview -->
                <div x-show="sectionCollapses['section-10']" @click="toggleSection('section-10')" class="py-2.5 px-3 bg-zinc-50 dark:bg-zinc-950/60 border border-dashed border-emerald-500/40 text-xs font-mono text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between cursor-pointer transition select-none mb-6">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Seksi 12 diciutkan (Kunci Scope &amp; Pembayaran DP/Lisensi). Klik untuk membuka formulir pembayaran...</span>
                    </span>
                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 uppercase font-bold">[ + EXPAND &amp; CHECKOUT ]</span>
                </div>

                <div x-show="!sectionCollapses['section-10']" x-transition class="accordion-body">

                <!-- Staging Sandbox Environment Banner (If Provisioned) -->
                @if($blueprint->staging_url)
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
                                            PELAYANAN GRATIS (RP 0)
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
                                        Aktif: {{ $blueprint->staging_provisioned_at->format('d M Y H:i') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

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

                @if($isStudioExplicit)
                    <!-- MITRA TURNKEY STUDIO TRACK HEADER BANNER -->
                    <div class="mb-6 p-4 sm:p-5 bg-zinc-900 border-2 border-amber-500 text-white rounded-none">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-0.5 bg-amber-500 text-black text-[10px] font-mono font-black uppercase">
                                        🤝 JALUR AKTIF: DEDICATED STUDIO TURNKEY (TIM NERIAH PRO)
                                    </span>
                                    <span class="text-xs text-amber-400 font-mono font-bold">Pengerjaan Penuh 100% oleh Tim Senior Architect</span>
                                </div>
                                <p class="text-xs text-zinc-300 font-sans mt-0.5">
                                    Proyek dikerjakan secara turnkey oleh tim engineer Neriah Pro dengan kontrak SPK SHA-256, WBS 7 Milestone, dan garansi bug pasca-peluncuran.
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <button
                                    type="button"
                                    @click="executionTrack = (executionTrack === 'studio' ? 'retail' : 'studio')"
                                    class="text-[11px] font-mono text-zinc-400 hover:text-white underline cursor-pointer"
                                >
                                    <span x-show="executionTrack === 'studio'">Perlu koding mandiri? Beralih ke Jalur Lisensi Retail &rarr;</span>
                                    <span x-show="executionTrack === 'retail'">&larr; Kembali ke Mode Dedicated Studio Turnkey</span>
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
                                        📦 JALUR AKTIF: LISENSI DIGITAL RETAIL (KODING MANDIRI)
                                    </span>
                                    <span class="text-xs text-emerald-400 font-mono font-bold">Cetak Biru PRD 26 Parameter &amp; Starter Kit AI</span>
                                </div>
                                <p class="text-xs text-zinc-300 font-sans mt-0.5">
                                    Lisensi software architecture mandiri: PRD 26 parameter, DDL SQL Strict ULID, Docker container, dan instruksi AI agent (.cursorrules).
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <button
                                    type="button"
                                    @click="executionTrack = (executionTrack === 'retail' ? 'studio' : 'retail')"
                                    class="text-[11px] font-mono text-emerald-400 font-bold hover:underline cursor-pointer"
                                >
                                    <span x-show="executionTrack === 'retail'">🚀 Upgrade ke Dedicated Studio Turnkey &rarr;</span>
                                    <span x-show="executionTrack === 'studio'">&larr; Kembali ke Lisensi Retail</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- DUAL-TRACK EXECUTION SWITCHER (RETAIL VS STUDIO - ONLY FOR UNSELECTED DRAFT) -->
                    <div class="mb-8 border-2 border-zinc-900 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950 p-4 sm:p-6 rounded-none">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                            <span class="px-2.5 py-0.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold uppercase tracking-wider">
                                DUAL-TRACK EXECUTION BRIDGE // PILIH METODE PENGERJAAN
                            </span>
                            <span class="text-[11px] font-mono text-zinc-500">
                                Transparan &bull; Bebas Lock-in &bull; Garansi Resmi
                            </span>
                        </div>

                        <h3 class="text-lg sm:text-xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mb-2">
                            Bagaimana Anda Ingin Mengeksekusi Cetak Biru Ini?
                        </h3>
                        <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed mb-6 max-w-3xl">
                            Pilih jalur yang paling sesuai dengan kapasitas tim Anda: Beli lisensi arsitektur untuk koding mandiri dengan tim internal (Jalur Mandiri Retail), atau serahkan pengerjaan penuh dari nol sampai live di server VPS kepada tim engineer Neriah Pro (Jalur Turnkey Studio).
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
                                            JALUR 01 // LISENSI DIGITAL RETAIL
                                        </span>
                                        <span x-show="executionTrack === 'retail'" class="px-2 py-0.5 bg-emerald-500 text-black text-[9px] font-mono font-bold uppercase">
                                            TERPILIH
                                        </span>
                                    </div>
                                    <div class="text-base font-black uppercase text-zinc-900 dark:text-white mb-1">
                                        Koding Mandiri (Tim Internal)
                                    </div>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                        Untuk Anda yang punya tim programmer sendiri &amp; hanya butuh PRD 26 parameter, DDL SQL Strict ULID, Docker container, dan aturan AI coding agent.
                                    </p>
                                </div>
                                <div class="mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-800 font-mono text-xs text-emerald-600 dark:text-emerald-400 font-bold flex items-center justify-between">
                                    <span>Mulai Rp {{ $litePrice }} (Sekali Bayar)</span>
                                    <span>Pilih Jalur Mandiri &darr;</span>
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
                                            JALUR 02 // DEDICATED STUDIO TURNKEY
                                        </span>
                                        <span x-show="executionTrack === 'studio'" class="px-2 py-0.5 bg-emerald-500 text-black text-[9px] font-mono font-bold uppercase">
                                            TERPILIH
                                        </span>
                                    </div>
                                    <div class="text-base font-black uppercase text-zinc-900 dark:text-white mb-1">
                                        Turnkey Penuh (Tim Neriah Pro)
                                    </div>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                        Untuk Anda yang butuh tim Neriah Pro mengoding 100%, membangun, menguji (Pest ApiContractTest), hingga live deploy di VPS bergaransi SLA.
                                    </p>
                                </div>
                                <div class="mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-800 font-mono text-xs text-blue-600 dark:text-blue-400 font-bold flex items-center justify-between">
                                    <span>DP 50% Kickoff Milestone</span>
                                    <span>Pilih Jalur Studio &darr;</span>
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
                                                <span>STATUS: LISENSI RETAIL AKTIF</span>
                                                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                            </div>
                                            <h4 class="text-sm font-bold text-zinc-900 dark:text-white font-sans mt-0.5">
                                                {{ $retailTierName }} &bull; Lisensi Resmi Terverifikasi
                                            </h4>
                                            <p class="text-xs text-zinc-600 dark:text-zinc-300 font-sans mt-0.5">
                                                Pembayaran lisensi mandiri Anda telah sukses dan tercatat seumur hidup. Seluruh berkas arsitektur, DDL SQL Strict ULID, Docker container, dan starter kit repository siap diunduh langsung di bawah ini.
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
                                        title="Unduh File Asli Markdown (.MD) untuk AI Coding Agent"
                                    >
                                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        <span>Unduh PRD (.MD)</span>
                                    </button>

                                    <a 
                                        href="{{ route('blueprint.download-cursorrules', $blueprint->slug) }}"
                                        class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-emerald-700 dark:text-emerald-400 border border-emerald-500/50 font-mono font-bold text-xs uppercase tracking-wider py-3 px-3 text-center rounded-none transition flex items-center justify-center gap-2 shadow-sm"
                                        title="Unduh .cursorrules / CLAUDE.md untuk AI Coding Agent"
                                    >
                                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                                        <span>Unduh .cursorrules</span>
                                    </a>

                                    <a 
                                        href="{{ route('blueprint.export-scaffold', $blueprint->slug) }}"
                                        class="bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase tracking-wider py-3 px-3 text-center rounded-none transition flex items-center justify-center gap-2 shadow-sm"
                                        title="Unduh Master Starter Kit ZIP Lengkap (Laravel 13, Filament v5, Docker)"
                                    >
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        <span>Unduh Starter Kit (.ZIP)</span>
                                    </a>

                                    <a 
                                        href="/customer/dashboard#licenses"
                                        class="bg-zinc-900 hover:bg-black dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white font-mono font-bold text-xs uppercase tracking-wider py-3 px-3 text-center rounded-none transition flex items-center justify-center gap-2 shadow-sm"
                                    >
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        <span>Portal Klien</span>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold uppercase block mb-1">
                                    ✓ LISENSI SOFTWARE FACTORY OS LANGSUNG AKTIF DI CUSTOMER PORTAL
                                </span>
                                <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                    Pilih paket lisensi di bawah. Begitu pembayaran Midtrans Snap (QRIS/VA) diverifikasi, file cetak biru, skema SQL DDL, dan starter-kit ZIP langsung dapat diunduh permanen di <a href="/customer/dashboard" target="_blank" class="underline text-emerald-500 font-bold">Portal Klien Anda</a>.
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
                                        Saya telah membaca dan menyetujui 
                                        <a href="/terms" target="_blank" class="font-bold underline text-zinc-900 dark:text-white hover:text-emerald-500">Syarat &amp; Ketentuan Layanan</a>, 
                                        <a href="/privacy-policy" target="_blank" class="font-bold underline text-zinc-900 dark:text-white hover:text-emerald-500">Kebijakan Privasi</a>, serta 
                                        <a href="/refund-policy" target="_blank" class="font-bold underline text-zinc-900 dark:text-white hover:text-emerald-500">Garansi &amp; Kebijakan Pengembalian Dana (Refund Policy)</a> yang berlaku di Neriah Pro.
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
                                        <li class="flex items-start gap-1.5">&bull; Dokumen PRD 26 Parameter Lengkap</li>
                                        <li class="flex items-start gap-1.5">&bull; Skema DDL PostgreSQL Strict ULID</li>
                                        <li class="flex items-start gap-1.5">&bull; Lisensi Akses Seumur Hidup</li>
                                    </ul>
                                </div>
                                <button
                                    type="button"
                                    @click="checkoutRetail('retail_lite')"
                                    :disabled="retailCheckingOut || !agreeRetailTerms"
                                    class="w-full bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black font-mono font-bold text-xs uppercase py-3 px-3 transition cursor-pointer text-center"
                                    :class="(!agreeRetailTerms ? 'opacity-50 cursor-not-allowed' : '')"
                                >
                                    <span x-show="!retailCheckingOut">Beli Lisensi Lite &rarr;</span>
                                    <span x-show="retailCheckingOut">Memproses...</span>
                                </button>
                            </div>

                            <!-- TIER 03: PRO (RECOMMENDED) -->
                            <div class="p-4 sm:p-5 bg-white dark:bg-zinc-900 border-2 border-emerald-500 flex flex-col justify-between rounded-none relative">
                                <div class="absolute -top-3 right-4 bg-emerald-500 text-black font-mono text-[9px] font-black uppercase px-2.5 py-0.5 tracking-wider">
                                    POPULER // DEVELOPER CHOICE
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
                                        <li class="flex items-start gap-1.5">&bull; Seluruh Fitur Paket Lite</li>
                                        <li class="flex items-start gap-1.5">&bull; Aturan AI Coding Agent (.cursorrules &amp; AGENTS.md)</li>
                                        <li class="flex items-start gap-1.5">&bull; Seeder Sintetis Realistis 100+ Baris</li>
                                        <li class="flex items-start gap-1.5">&bull; Docker Container Config Siap Pakai</li>
                                    </ul>
                                </div>
                                <button
                                    type="button"
                                    @click="checkoutRetail('retail_pro')"
                                    :disabled="retailCheckingOut || !agreeRetailTerms"
                                    class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase py-3 px-3 transition cursor-pointer text-center shadow-xs"
                                    :class="(!agreeRetailTerms ? 'opacity-50 cursor-not-allowed' : '')"
                                >
                                    <span x-show="!retailCheckingOut">Beli Lisensi Pro &rarr;</span>
                                    <span x-show="retailCheckingOut">Memproses...</span>
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
                                        <li class="flex items-start gap-1.5">&bull; Seluruh Fitur Paket Lite &amp; Pro</li>
                                        <li class="flex items-start gap-1.5">&bull; Master Starter Repository ZIP (Laravel 13, Filament v5, Livewire 4)</li>
                                        <li class="flex items-start gap-1.5">&bull; 1-on-1 Architecture Call 60 Menit dengan Lead Architect</li>
                                        <li class="flex items-start gap-1.5">&bull; 100% Biaya Lisensi Memotong DP jika Upgrade ke Studio</li>
                                    </ul>
                                </div>
                                <button
                                    type="button"
                                    @click="checkoutRetail('retail_ultimate')"
                                    :disabled="retailCheckingOut || !agreeRetailTerms"
                                    class="w-full bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-black font-mono font-bold text-xs uppercase py-3 px-3 transition cursor-pointer text-center"
                                    :class="(!agreeRetailTerms ? 'opacity-50 cursor-not-allowed' : '')"
                                >
                                    <span x-show="!retailCheckingOut">Beli Lisensi Ultimate &rarr;</span>
                                    <span x-show="retailCheckingOut">Memproses...</span>
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
                                            🚀 UPGRADE PATH // ZERO-LOSS GUARANTEE
                                        </span>
                                        <span class="text-xs text-emerald-400 font-mono font-bold">100% Biaya Lisensi Retail Dipotong dari DP Turnkey</span>
                                    </div>
                                    <h4 class="text-base font-black uppercase tracking-tight text-white">
                                        Ingin Tim Senior Architect Neriah Pro yang Mengoding &amp; Meluncurkan 100%?
                                    </h4>
                                    <p class="text-xs text-zinc-300 font-sans mt-1 max-w-2xl leading-relaxed">
                                        Jika tim internal Anda membutuhkan tim profesional untuk membangun dari nol hingga live di server VPS bergaransi SLA: <strong>100% biaya lisensi retail yang sudah Anda bayarkan akan diperhitungkan sebagai potongan langsung terhadap tagihan DP 50% Kickoff Proyek Turnkey</strong>.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="executionTrack = 'studio'"
                                    class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-black font-black font-mono text-xs uppercase tracking-wider rounded-none shrink-0 cursor-pointer shadow-lg transition"
                                >
                                    Lihat Jalur Turnkey Studio &rarr;
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
                        <span class="text-zinc-500 dark:text-zinc-400 text-xs block">
                            @if($blueprint->isDpConfirmed())
                                TERMIN TERVERIFIKASI:
                            @elseif(($contractDoc = $contractDocument ?? $blueprint->getContractDocument()) && $contractDoc->status === 'signed' && (float)$contractDoc->contract_amount > 0)
                                TERMIN KONTRAK SAH:
                            @else
                                TERMIN TERPILIH:
                            @endif
                            <span class="text-zinc-900 dark:text-white font-bold" x-text="tierAmounts[selectedTier]?.name || 'Standard Velocity'"></span>
                        </span>
                        @if($blueprint->is_free_grant)
                            <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-500/40 text-[10px] font-bold uppercase inline-block mb-1">
                                ✓ SUBSIDI KASIH (RP 0)
                            </span>
                            <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 block">RP 0 (PELAYANAN KASIH)</span>
                            <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block">VOUCHER: {{ $blueprint->voucher_code }}</span>
                        @elseif($blueprint->isDpConfirmed())
                            <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-500/40 text-[10px] font-bold uppercase inline-flex items-center gap-1 mb-1">
                                <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span>DP TERKONFIRMASI // SPRINT AKTIF</span>
                            </span>
                            @php
                                $contractDoc = $contractDocument ?? $blueprint->getContractDocument();
                                $paidDp = $contractDoc?->dp_amount;
                            @endphp
                            <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 block">
                                {{ $paidDp ? 'Rp ' . number_format($paidDp, 0, ',', '.') : 'LUNAS (DP 50%)' }}
                            </span>
                            <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block">
                                {{ $contractDoc?->contract_amount ? 'Total Kontrak: Rp ' . number_format($contractDoc->contract_amount, 0, ',', '.') : 'Kontrak Terkunci' }} &bull; SPRINT 1 IN PROGRESS
                            </span>
                        @elseif(($contractDoc = $contractDocument ?? $blueprint->getContractDocument()) && $contractDoc->status === 'signed' && (float)$contractDoc->contract_amount > 0)
                            <span class="px-2 py-0.5 bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 border border-cyan-500/40 text-[10px] font-bold uppercase inline-flex items-center gap-1 mb-1">
                                <svg class="w-3 h-3 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>KONTRAK TERTANDATANGANI // MENUNGGU DP</span>
                            </span>
                            <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 block">
                                DP (50%): Rp {{ number_format($contractDoc->dp_amount ?: ($contractDoc->contract_amount * 0.50), 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block">
                                Total Kontrak: Rp {{ number_format($contractDoc->contract_amount, 0, ',', '.') }} &bull; KONTRAK SAH MENGIKAT
                            </span>
                        @else
                            <span class="text-xl font-black text-emerald-600 dark:text-emerald-400" x-text="'DP (50%): Rp ' + (tierAmounts[selectedTier]?.dp || 0).toLocaleString('id-ID')"></span>
                            <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block" x-text="'Total Kontrak: Rp ' + (tierAmounts[selectedTier]?.contract || 0).toLocaleString('id-ID')"></span>
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
                            @elseif($blueprint->isContractSigned())
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
                                @click="navigator.clipboard.writeText('{{ $blueprint->document_sha256 ?: $blueprint->calculatePrdHash() }}'); if(window.showToast) window.showToast({ type: 'success', title: 'SHA-256 DISALIN', message: 'Cryptographic hash berhasil disalin ke clipboard.' })"
                                class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300 text-[10px] font-bold uppercase underline"
                            >
                                Salin Hash
                            </button>
                        </div>
                        <code class="text-emerald-600 dark:text-emerald-400 break-all select-all font-mono text-[11px] block">
                            {{ $blueprint->document_sha256 ?: $blueprint->calculatePrdHash() }}
                        </code>
                        @php
                            $contractDoc = $contractDocument ?? $blueprint->getContractDocument();
                            $hasAudit = ($blueprint->signed_agreement && $blueprint->signed_at) || ($contractDoc && $contractDoc->signed_at);
                        @endphp
                        @if($hasAudit)
                            <div class="mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-800/80 flex flex-wrap gap-x-4 gap-y-1 text-[10px] text-zinc-600 dark:text-zinc-400">
                                <span>Penandatangan: <strong class="text-zinc-900 dark:text-zinc-200">{{ $blueprint->client_name ?: ($contractDoc?->signer_name ?: $blueprint->nama_bisnis) }}</strong></span>
                                <span>Waktu: <strong class="text-zinc-900 dark:text-zinc-200">{{ ($blueprint->signed_at ?: $contractDoc?->signed_at)?->format('d M Y H:i:s T') }}</strong></span>
                                <span>IP Audit: <strong class="text-zinc-900 dark:text-zinc-200">{{ $blueprint->signer_ip ?: ($contractDoc?->signer_ip_address ?: 'Recorded') }}</strong></span>
                                @if(!empty($blueprint->staging_url))
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">Sandbox: <strong class="text-emerald-700 dark:text-emerald-300 font-mono">{{ $blueprint->staging_url }}</strong></span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <p class="text-zinc-600 dark:text-zinc-400 text-xs font-sans leading-relaxed">
                        Seluruh fitur dan arsitektur dalam PRD ini terikat secara kriptografis. Setiap perubahan di kemudian hari wajib melalui kesepakatan Change Request (CR) / Addendum tanpa mengubah basis dokumen utama.
                    </p>
                </div>

                <!-- Action State Engine: Active Sprint Cockpit vs Sign & Pay Actions -->
                @if($blueprint->isDpConfirmed())
                    <!-- PROYEK SUDAH BAYAR DP / AKTIF: Tampilkan Cockpit Kontrak & Status Sprint (Bukan Tombol Bayar / Cart) -->
                    <div class="p-5 bg-emerald-500/10 dark:bg-emerald-950/20 border-2 border-emerald-500/50 rounded-none space-y-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 bg-emerald-500 text-black flex items-center justify-center font-bold text-lg flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </span>
                                <div>
                                    <div class="text-xs uppercase font-mono tracking-wider text-emerald-700 dark:text-emerald-400 font-bold flex items-center gap-2">
                                        <span>STATUS: PEMBAYARAN DP TERVERIFIKASI &bull; SPRINT AKTIF</span>
                                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                    </div>
                                    <h4 class="text-sm font-bold text-zinc-900 dark:text-white font-sans mt-0.5">
                                        Ruang Lingkup (Scope) Resmi Dikunci &amp; Proyek Sedang Dikerjakan
                                    </h4>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-300 font-sans mt-0.5">
                                        Kontrak digital dan spesifikasi teknis PRD telah mengikat secara hukum. Anda tidak perlu membayar DP lagi atau menambahkan ke keranjang belanja.
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
                                    <span>Lihat Kontrak Digital</span>
                                </a>
                            @endif

                            @if(!empty($blueprint->staging_url))
                                <a 
                                    href="{{ $blueprint->staging_url }}"
                                    target="_blank"
                                    class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-emerald-700 dark:text-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-300 border border-emerald-500/50 font-mono font-bold text-xs uppercase tracking-wider py-3.5 px-4 text-center rounded-none transition flex items-center justify-center gap-2 shadow-sm"
                                >
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    <span>Buka Sandbox Staging</span>
                                </a>
                            @endif

                            <button 
                                type="button"
                                @click.prevent="$dispatch('open-raw-md-warning', { action: 'download' })"
                                class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-300 dark:border-zinc-700 font-mono font-bold text-xs uppercase tracking-wider py-3.5 px-4 text-center rounded-none transition flex items-center justify-center gap-2 shadow-sm cursor-pointer"
                                title="Unduh File Asli Markdown (.MD) untuk AI Coding Agent"
                            >
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                <span>Unduh PRD (.MD)</span>
                            </button>

                            <a 
                                href="{{ route('blueprint.download-cursorrules', $blueprint->slug) }}"
                                class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-emerald-700 dark:text-emerald-400 border border-emerald-500/50 font-mono font-bold text-xs uppercase tracking-wider py-3.5 px-4 text-center rounded-none transition flex items-center justify-center gap-2 shadow-sm"
                                title="Unduh .cursorrules / CLAUDE.md untuk AI Coding Agent"
                            >
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                                <span>Unduh .cursorrules</span>
                            </a>

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
                @elseif($blueprint->isContractSigned())
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
                                <span>Lihat Kontrak Tertandatangani</span>
                            </a>
                        @endif

                        <button 
                            type="button" 
                            @click="paymentModalOpen = true"
                            class="w-full h-full bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none transition flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <span>Bayar DP Sekarang &rarr;</span>
                            <svg class="w-4 h-4 text-black flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </button>

                        <form method="POST" action="{{ route('cart.add', $blueprint->slug) }}" class="m-0">
                            @csrf
                            <input type="hidden" name="tier" :value="selectedTier">
                            <button 
                                type="submit" 
                                class="w-full h-full bg-zinc-100 dark:bg-zinc-900 hover:bg-zinc-200 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-mono font-bold text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none border border-zinc-300 dark:border-zinc-700 transition flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <span>Tambahkan ke Cart</span>
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
                                class="w-full h-full bg-emerald-500 hover:bg-emerald-400 text-black font-mono font-black text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none transition flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <span>Tanda Tangani Kontrak &amp; Kunci Scope</span>
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                        </form>

                        <button 
                            type="button" 
                            @click="paymentModalOpen = true"
                            class="w-full h-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-900 dark:text-white font-mono font-bold text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none border border-zinc-300 dark:border-zinc-700 transition flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <span>Bayar DP / Klaim Voucher</span>
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </button>

                        <form method="POST" action="{{ route('cart.add', $blueprint->slug) }}" class="m-0">
                            @csrf
                            <input type="hidden" name="tier" :value="selectedTier">
                            <button 
                                type="submit" 
                                class="w-full h-full bg-zinc-100 dark:bg-zinc-900 hover:bg-zinc-200 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-mono font-bold text-xs uppercase tracking-wider py-4 px-4 text-center rounded-none border border-zinc-300 dark:border-zinc-700 transition flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <span>Tambahkan ke Cart</span>
                                <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </button>
                        </form>
                    </div>
                @endif
                </div> <!-- END TRACK CONTENT B (STUDIO) -->
                </div> <!-- END ACCORDION BODY SECTION-10 -->
            </section>
