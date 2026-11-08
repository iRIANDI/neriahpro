@php
    $activeLocale = $locale ?? app()->getLocale();
    $isEn = ($activeLocale === 'en');
    $user = auth()->user();
    $isSuperAdmin = $user && ($user->isSuperAdmin() || $user->hasRole('super_admin') || str_contains((string) $user->email, 'neriahpro'));
@endphp

<!-- Download Center & 24-Hour Lifecycle Modal -->
<div 
    x-show="downloadCenterModalOpen" 
    x-cloak 
    @keydown.escape.window="downloadCenterModalOpen = false"
    class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-3 sm:p-5 print:hidden"
    style="display: none;"
>
    <!-- Backdrop Overlay -->
    <div class="fixed inset-0 bg-black/85 backdrop-blur-md transition-opacity" @click="downloadCenterModalOpen = false" x-transition.opacity></div>

    <!-- Modal Dialog Window -->
    <div 
        @click.stop
        class="relative z-10 bg-white dark:bg-zinc-950 border-2 border-emerald-500 w-full max-w-5xl rounded-none shadow-2xl p-4 sm:p-6 flex flex-col h-[90vh] max-h-[92vh] overflow-hidden text-zinc-900 dark:text-zinc-100"
        x-transition
    >
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4 shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 bg-emerald-500 rounded-none inline-block animate-pulse"></span>
                <div>
                    <h3 class="text-sm sm:text-base font-mono font-black uppercase tracking-wider text-zinc-900 dark:text-white flex items-center gap-2">
                        <span x-show="locale === 'en'">DOWNLOAD CENTER &amp; ARTIFACT LIFECYCLE</span>
                        <span x-show="locale !== 'en'">PUSAT UNDUHAN &amp; SIKLUS RETENSI ARSIP</span>
                        <span class="text-[10px] px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950 text-emerald-900 dark:text-emerald-300 border border-emerald-400 font-bold rounded-none">
                            24H AUTO-PURGE
                        </span>
                    </h3>
                    <p class="text-[11px] font-mono text-zinc-500 dark:text-zinc-400 mt-0.5">
                        <span x-show="locale === 'en'">Asynchronous background rendering engine for PDF, Markdown, &amp; Boilerplate ZIP without HTTP timeouts.</span>
                        <span x-show="locale !== 'en'">Mesin render berkas di latar belakang untuk PDF, Markdown, &amp; ZIP Scaffold tanpa risiko timeout browser.</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button 
                    type="button" 
                    @click="fetchDownloadArtifacts()" 
                    class="p-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:text-emerald-500 transition cursor-pointer"
                    :title="locale === 'en' ? 'Refresh list' : 'Perbarui daftar'"
                >
                    <svg class="w-4 h-4" :class="artifactsLoading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
                <button 
                    type="button" 
                    @click="downloadCenterModalOpen = false" 
                    class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white text-2xl font-bold p-1 leading-none cursor-pointer"
                >
                    &times;
                </button>
            </div>
        </div>

        <!-- Security & Access Isolation Banner -->
        <div class="mb-4 p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-mono flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 shrink-0">
            <div class="flex items-center gap-2 flex-wrap">
                @if($isSuperAdmin)
                    <span class="px-2 py-0.5 bg-amber-500 text-black font-black text-[10px] rounded-none uppercase flex items-center gap-1">
                        🛡️ SUPERADMIN MONITORING
                    </span>
                    <span class="text-zinc-600 dark:text-zinc-400 text-[11px]">
                        <span x-show="locale === 'en'">Viewing all client download artifacts for this blueprint.</span>
                        <span x-show="locale !== 'en'">Memantau seluruh arsip unduhan semua klien pada cetak biru ini.</span>
                    </span>
                @else
                    <span class="px-2 py-0.5 bg-emerald-500 text-black font-black text-[10px] rounded-none uppercase flex items-center gap-1">
                        🔒 CLIENT ISOLATION
                    </span>
                    <span class="text-zinc-600 dark:text-zinc-400 text-[11px]">
                        <span x-show="locale === 'en'">Access restricted strictly to your verified client session &amp; token.</span>
                        <span x-show="locale !== 'en'">Akses dokumen terisolasi ketat sesuai sesi dan token klien Anda.</span>
                    </span>
                @endif
            </div>

            <div class="text-[11px] text-zinc-500 dark:text-zinc-400 flex items-center gap-2">
                <span>⏱️ <span x-show="locale === 'en'">Auto-Purge Policy:</span><span x-show="locale !== 'en'">Kebijakan Retensi:</span> <strong>24 Jam</strong></span>
            </div>
        </div>

        <!-- Quick Generation Action Cards (3 Pillars of Export) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4 shrink-0 font-mono">
            <!-- 1. PDF Card -->
            <div class="p-3.5 bg-zinc-100 dark:bg-zinc-900/90 border border-zinc-300 dark:border-zinc-800 rounded-none flex flex-col justify-between hover:border-emerald-500/50 transition">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                            📄 <span x-show="locale === 'en'">PRD SPECIFICATION PDF</span><span x-show="locale !== 'en'">SPESIFIKASI PRD (PDF)</span>
                        </span>
                        <span class="text-[10px] px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold">DomPDF</span>
                    </div>
                    <p class="text-[11px] text-zinc-600 dark:text-zinc-400 mb-3 leading-relaxed">
                        <span x-show="locale === 'en'">14 comprehensive architecture sections, WBS sprint timeline, and DDL ERD ready for official A4 printing.</span>
                        <span x-show="locale !== 'en'">14 seksi arsitektur lengkap, timeline sprint WBS, dan ERD DDL siap cetak resmi A4.</span>
                    </p>
                </div>
                <button 
                    type="button" 
                    @click="requestDownloadArtifact('pdf')" 
                    :disabled="requestingType === 'pdf'"
                    class="w-full py-1.5 px-3 bg-zinc-900 hover:bg-zinc-800 dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white dark:text-black font-black uppercase text-xs rounded-none border border-zinc-700 dark:border-emerald-400 flex items-center justify-center gap-1.5 transition cursor-pointer disabled:opacity-50"
                >
                    <span x-show="requestingType !== 'pdf'">⚡ <span x-show="locale === 'en'">GENERATE PDF</span><span x-show="locale !== 'en'">BUAT PDF</span></span>
                    <span x-show="requestingType === 'pdf'" class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <span x-show="locale === 'en'">SCHEDULING...</span><span x-show="locale !== 'en'">MENJADWALKAN...</span>
                    </span>
                </button>
            </div>

            <!-- 2. Markdown Card -->
            <div class="p-3.5 bg-zinc-100 dark:bg-zinc-900/90 border border-zinc-300 dark:border-zinc-800 rounded-none flex flex-col justify-between hover:border-emerald-500/50 transition">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                            📝 <span x-show="locale === 'en'">RAW MARKDOWN (.MD)</span><span x-show="locale !== 'en'">MARKDOWN ASLI (.MD)</span>
                        </span>
                        <span class="text-[10px] px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold">CANARY SEAL</span>
                    </div>
                    <p class="text-[11px] text-zinc-600 dark:text-zinc-400 mb-3 leading-relaxed">
                        <span x-show="locale === 'en'">Full 26-parameter PRD embedded with Invisible Zero-Width Canary Watermark for AI IDE ingestion.</span>
                        <span x-show="locale !== 'en'">PRD 26 parameter penuh dilengkapi stempel hak cipta Invisible Canary Watermark untuk AI IDE.</span>
                    </p>
                </div>
                <button 
                    type="button" 
                    @click="requestDownloadArtifact('markdown')" 
                    :disabled="requestingType === 'markdown'"
                    class="w-full py-1.5 px-3 bg-zinc-900 hover:bg-zinc-800 dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white dark:text-black font-black uppercase text-xs rounded-none border border-zinc-700 dark:border-emerald-400 flex items-center justify-center gap-1.5 transition cursor-pointer disabled:opacity-50"
                >
                    <span x-show="requestingType !== 'markdown'">⚡ <span x-show="locale === 'en'">GENERATE MARKDOWN</span><span x-show="locale !== 'en'">BUAT MARKDOWN</span></span>
                    <span x-show="requestingType === 'markdown'" class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <span x-show="locale === 'en'">SCHEDULING...</span><span x-show="locale !== 'en'">MENJADWALKAN...</span>
                    </span>
                </button>
            </div>

            <!-- 3. Scaffold ZIP Card -->
            <div class="p-3.5 bg-zinc-100 dark:bg-zinc-900/90 border border-zinc-300 dark:border-zinc-800 rounded-none flex flex-col justify-between hover:border-emerald-500/50 transition">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                            📦 <span x-show="locale === 'en'">STARTER KIT BOILERPLATE (.ZIP)</span><span x-show="locale !== 'en'">STARTER KIT BOILERPLATE (.ZIP)</span>
                        </span>
                        <span class="text-[10px] px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold">25+ FILES</span>
                    </div>
                    <p class="text-[11px] text-zinc-600 dark:text-zinc-400 mb-3 leading-relaxed">
                        <span x-show="locale === 'en'">Complete repository scaffold: PostgreSQL Strict ULID migrations, Docker Compose stack, Model classes &amp; OpenAPI.</span>
                        <span x-show="locale !== 'en'">Paket lengkap repository scaffold: migrasi PG ULID, Docker Compose, Model Eloquent &amp; OpenAPI.</span>
                    </p>
                </div>
                <button 
                    type="button" 
                    @click="requestDownloadArtifact('scaffold_zip')" 
                    :disabled="requestingType === 'scaffold_zip'"
                    class="w-full py-1.5 px-3 bg-zinc-900 hover:bg-zinc-800 dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white dark:text-black font-black uppercase text-xs rounded-none border border-zinc-700 dark:border-emerald-400 flex items-center justify-center gap-1.5 transition cursor-pointer disabled:opacity-50"
                >
                    <span x-show="requestingType !== 'scaffold_zip'">⚡ <span x-show="locale === 'en'">GENERATE STARTER KIT</span><span x-show="locale !== 'en'">BUAT STARTER KIT</span></span>
                    <span x-show="requestingType === 'scaffold_zip'" class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <span x-show="locale === 'en'">SCHEDULING...</span><span x-show="locale !== 'en'">MENJADWALKAN...</span>
                    </span>
                </button>
            </div>
        </div>

        <!-- Artifacts List Section (Table / Cards) -->
        <div class="flex-1 overflow-y-auto custom-prd-scrollbar border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 p-3 font-mono">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-zinc-200 dark:border-zinc-800">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                    📋 <span x-show="locale === 'en'">ACTIVE ARTIFACTS QUEUE &amp; DOWNLOAD STATUS</span>
                    <span x-show="locale !== 'en'">DAFTAR BERKAS UNDUHAN AKTIF &amp; STATUS RETENSI</span>
                    <span class="text-emerald-500 font-black" x-text="'(' + artifactsList.length + ')'"></span>
                </span>
                <span class="text-[10px] text-zinc-500">
                    <span x-show="locale === 'en'">Live countdown ticker active</span>
                    <span x-show="locale !== 'en'">Ticker countdown aktif</span>
                </span>
            </div>

            <!-- Loading State -->
            <div x-show="artifactsLoading && artifactsList.length === 0" class="py-12 text-center text-xs text-zinc-500">
                <svg class="w-6 h-6 animate-spin mx-auto text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span><span x-show="locale === 'en'">Loading download list...</span><span x-show="locale !== 'en'">Memuat daftar berkas unduhan...</span></span>
            </div>

            <!-- Empty State -->
            <div x-show="!artifactsLoading && artifactsList.length === 0" class="py-12 text-center text-xs text-zinc-500 space-y-2">
                <div class="text-3xl">📥</div>
                <div class="font-bold text-zinc-700 dark:text-zinc-300">
                    <span x-show="locale === 'en'">No download artifacts generated yet.</span>
                    <span x-show="locale !== 'en'">Belum ada berkas unduhan yang digenerate.</span>
                </div>
                <p class="max-w-md mx-auto text-[11px] text-zinc-400">
                    <span x-show="locale === 'en'">Click one of the buttons above (PDF, Markdown, or Starter Kit) to start fast, asynchronous background generation.</span>
                    <span x-show="locale !== 'en'">Klik salah satu tombol di atas (PDF, Markdown, atau Starter Kit) untuk memulai proses background job yang efisien tanpa timeout.</span>
                </p>
            </div>

            <!-- List of Artifacts -->
            <div class="space-y-3" x-show="artifactsList.length > 0">
                <template x-for="item in artifactsList" :key="item.id">
                    <div 
                        class="p-3.5 bg-white dark:bg-zinc-900 border transition relative"
                        :class="{
                            'border-emerald-500 dark:border-emerald-600': item.status === 'completed' && !item.is_expired,
                            'border-amber-400 dark:border-amber-600': item.status === 'processing' || item.status === 'queued',
                            'border-red-400 dark:border-red-600': item.status === 'failed',
                            'border-zinc-300 dark:border-zinc-800 opacity-65': item.is_expired || item.status === 'expired'
                        }"
                    >
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <!-- Left: File Metadata -->
                            <div class="space-y-1.5 flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <!-- Artifact Type Badge -->
                                    <span 
                                        class="px-2 py-0.5 text-[10px] font-black uppercase rounded-none"
                                        :class="{
                                            'bg-red-100 dark:bg-red-950 text-red-800 dark:text-red-300 border border-red-400': item.artifact_type === 'pdf',
                                            'bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 border border-blue-400': item.artifact_type === 'markdown',
                                            'bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-400': item.artifact_type === 'scaffold_zip'
                                        }"
                                        x-text="item.artifact_type === 'scaffold_zip' ? 'ZIP' : item.artifact_type.toUpperCase()"
                                    ></span>

                                    <!-- Filename -->
                                    <h4 class="text-xs font-bold text-zinc-900 dark:text-white truncate" x-text="item.filename"></h4>

                                    <!-- File Size -->
                                    <span class="text-[11px] text-zinc-500" x-text="item.file_size_formatted || ''"></span>

                                    <!-- Superadmin Client Tag (if viewing across clients) -->
                                    <template x-if="item.superadmin_meta">
                                        <span class="text-[10px] px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold" :title="'Client ID: ' + item.superadmin_meta.client_identifier">
                                            👤 <span x-text="item.superadmin_meta.user_email || item.superadmin_meta.ip_hash"></span>
                                        </span>
                                    </template>
                                </div>

                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400" x-text="item.title"></p>

                                <!-- Status & Live Progress Strip -->
                                <div x-show="item.status === 'processing' || item.status === 'queued'" class="pt-1 space-y-1">
                                    <div class="flex items-center justify-between text-[10px] text-amber-600 dark:text-amber-400 font-bold">
                                        <span x-text="item.status === 'queued' ? (locale === 'en' ? '⏳ Queued in background...' : '⏳ Mengantre di server...') : (locale === 'en' ? '⚙️ Rendering artifact in background...' : '⚙️ Memproses berkas di latar belakang...')"></span>
                                        <span x-text="(item.progress || 10) + '%'"></span>
                                    </div>
                                    <div class="w-full bg-zinc-200 dark:bg-zinc-800 h-1.5 overflow-hidden">
                                        <div class="bg-amber-400 h-full transition-all duration-300" :style="'width: ' + (item.progress || 10) + '%'"></div>
                                    </div>
                                </div>

                                <!-- Error Message if Failed -->
                                <div x-show="item.status === 'failed'" class="text-[11px] text-red-500 font-bold pt-1">
                                    ⚠️ <span x-text="item.error_message || (locale === 'en' ? 'Generation failed' : 'Gagal memproses berkas')"></span>
                                </div>
                            </div>

                            <!-- Middle: Real-Time Live Countdown Timer Bar & Badge -->
                            <div class="sm:w-64 shrink-0 p-2.5 bg-zinc-100 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 space-y-1.5">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-zinc-500 text-[10px] uppercase font-bold">
                                        <span x-show="locale === 'en'">RETENTION:</span><span x-show="locale !== 'en'">MASA AKTIF:</span>
                                    </span>

                                    <!-- Status indicator -->
                                    <template x-if="item.status === 'completed' && !item.is_expired">
                                        <span 
                                            class="font-black text-xs flex items-center gap-1"
                                            :class="{
                                                'text-emerald-600 dark:text-emerald-400': item.remaining_seconds > 21600,
                                                'text-amber-600 dark:text-amber-400': item.remaining_seconds <= 21600 && item.remaining_seconds > 3600,
                                                'text-rose-600 dark:text-rose-400 animate-pulse': item.remaining_seconds <= 3600
                                            }"
                                        >
                                            ⏳ <span x-text="formatSecondsCountdown(item.remaining_seconds)"></span>
                                        </span>
                                    </template>

                                    <template x-if="item.is_expired || item.status === 'expired'">
                                        <span class="px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-bold text-[10px]">
                                            <span x-show="locale === 'en'">EXPIRED</span><span x-show="locale !== 'en'">KADALUWARSA</span>
                                        </span>
                                    </template>

                                    <template x-if="item.status === 'processing' || item.status === 'queued'">
                                        <span class="text-amber-500 text-[10px] font-bold animate-pulse">
                                            <span x-show="locale === 'en'">GENERATING...</span><span x-show="locale !== 'en'">MEMPROSES...</span>
                                        </span>
                                    </template>
                                </div>

                                <!-- Countdown Progress Bar -->
                                <div class="w-full bg-zinc-200 dark:bg-zinc-800 h-1.5 overflow-hidden">
                                    <div 
                                        class="h-full transition-all duration-1000"
                                        :class="{
                                            'bg-emerald-500': item.remaining_seconds > 21600,
                                            'bg-amber-400': item.remaining_seconds <= 21600 && item.remaining_seconds > 3600,
                                            'bg-rose-500': item.remaining_seconds <= 3600
                                        }"
                                        :style="'width: ' + (item.percentage_remaining !== undefined ? item.percentage_remaining : 100) + '%'"
                                    ></div>
                                </div>

                                <div class="text-[10px] text-zinc-400 leading-tight">
                                    <template x-if="!item.is_expired && item.status === 'completed'">
                                        <span>
                                            <span x-show="locale === 'en'">Auto-purged from server when counter hits zero.</span>
                                            <span x-show="locale !== 'en'">Dihapus permanen dari server saat waktu habis.</span>
                                        </span>
                                    </template>
                                    <template x-if="item.is_expired || item.status === 'expired'">
                                        <span class="text-zinc-400">
                                            <span x-show="locale === 'en'">Purged from disk for privacy &amp; security.</span>
                                            <span x-show="locale !== 'en'">Telah dimusnahkan dari server demi keamanan.</span>
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <!-- Right: Download / Regenerate Actions -->
                            <div class="flex items-center gap-2 shrink-0">
                                <!-- Download Button (Active) -->
                                <template x-if="item.status === 'completed' && !item.is_expired && item.download_url">
                                    <a 
                                        :href="item.download_url" 
                                        class="px-3.5 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-black uppercase text-xs rounded-none border border-emerald-400 flex items-center gap-1.5 transition cursor-pointer shadow-xs whitespace-nowrap"
                                        :title="locale === 'en' ? 'Download file to your local computer' : 'Unduh berkas ke komputer lokal Anda'"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        <span><span x-show="locale === 'en'">DOWNLOAD</span><span x-show="locale !== 'en'">UNDUH</span></span>
                                    </a>
                                </template>

                                <!-- Re-generate Button (Expired, Failed, or Stalled) -->
                                <template x-if="item.is_expired || item.status === 'expired' || item.status === 'failed' || (item.status === 'processing' && item.progress >= 60)">
                                    <button 
                                        type="button" 
                                        @click="requestDownloadArtifact(item.artifact_type, true)" 
                                        class="px-3 py-1.5 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white font-bold uppercase text-xs rounded-none border border-zinc-700 flex items-center gap-1 transition cursor-pointer whitespace-nowrap"
                                        :title="locale === 'en' ? 'Force regenerate this file' : 'Paksa buat ulang berkas ini'"
                                    >
                                        <span>🔄 <span x-show="locale === 'en'">REGENERATE</span><span x-show="locale !== 'en'">BUAT ULANG</span></span>
                                    </button>
                                </template>

                                <!-- Delete Early Button -->
                                <button 
                                    type="button" 
                                    @click="deleteDownloadArtifact(item.id)" 
                                    class="p-2 hover:bg-red-100 dark:hover:bg-red-950/60 text-zinc-400 hover:text-red-600 transition cursor-pointer"
                                    :title="locale === 'en' ? 'Delete artifact immediately' : 'Hapus berkas ini sekarang'"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs font-mono shrink-0">
            <div class="text-[11px] text-zinc-500 dark:text-zinc-400 flex items-center gap-1.5">
                <span class="text-emerald-500">🛡️</span>
                <span>
                    <span x-show="locale === 'en'">Standard Software Factory OS &bull; 100% Client Data Isolation &bull; Compliant with GDPR / UU PDP 2024.</span>
                    <span x-show="locale !== 'en'">Standar Software Factory OS &bull; Isolasi Data Klien 100% &bull; Kepatuhan UU PDP No. 27/2022.</span>
                </span>
            </div>

            <button 
                type="button" 
                @click="downloadCenterModalOpen = false" 
                class="px-4 py-1.5 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white font-bold uppercase text-xs rounded-none border border-zinc-700 transition cursor-pointer self-end"
            >
                <span x-show="locale === 'en'">CLOSE (ESC)</span>
                <span x-show="locale !== 'en'">TUTUP (ESC)</span>
            </button>
        </div>
    </div>
</div>

