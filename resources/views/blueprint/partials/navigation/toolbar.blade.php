    <header id="blueprint-toolbar" 
            class="bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800 py-1.5 px-2 sm:px-4 lg:px-6 sticky z-40 no-print transition-all shadow-xs mt-0 mb-0 w-full max-w-full overflow-x-clip"
            style="top: var(--global-nav-h, 88px);">
        <div class="w-full max-w-[1700px] mx-auto overflow-x-auto no-scrollbar overscroll-x-contain">
            <div class="flex items-center justify-between min-w-max gap-2 sm:gap-2.5 py-0.5 pr-6 sm:pr-8">
            
            <!-- LEFT WING: Navigation & Workspace Switcher -->
            <div class="flex items-center gap-2 shrink-0">
                <!-- Back to Client Portal Button -->
                <a href="/customer/dashboard" class="px-2.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 text-xs font-mono font-bold rounded-none border border-zinc-300 dark:border-zinc-700 flex items-center gap-1.5 transition cursor-pointer" :title="locale === 'en' ? 'Back to Client Portal' : 'Kembali ke Portal Klien'">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span class="hidden sm:inline" x-show="locale === 'en'">PORTAL</span>
                    <span class="hidden sm:inline" x-show="locale !== 'en'">PORTAL</span>
                </a>

                @if(!$blueprint->is_published)
                    <span class="px-2.5 py-1.5 bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 text-xs font-mono uppercase font-bold border border-amber-300 dark:border-amber-800 flex items-center gap-1.5 select-none shrink-0 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span class="hidden md:inline">ACCESS RESTRICTED // PRIVATE DRAFT</span>
                        <span class="md:hidden">DRAFT</span>
                    </span>
                @else
                    <!-- Workspace Switcher Segmented Control (3-Pane Studio vs Partner Mode) -->
                    <div class="inline-flex rounded-none border border-zinc-300 dark:border-zinc-700 divide-x divide-zinc-300 dark:divide-zinc-700 bg-zinc-100 dark:bg-zinc-800 text-xs font-mono font-bold">
                        <a href="{{ route('blueprint.studio', ['slug' => $blueprint->slug]) }}" class="px-2.5 sm:px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-emerald-400 dark:bg-emerald-600 dark:hover:bg-emerald-500 dark:text-zinc-950 flex items-center gap-1.5 transition whitespace-nowrap" :title="locale === 'en' ? 'Open Fullscreen 3-Pane Architect Studio v5.0' : 'Buka Studio Arsitek 3-Pane v5.0'">
                            <span>🖥️ <span class="hidden md:inline">3-PANE STUDIO</span><span class="md:hidden">STUDIO</span></span>
                        </a>
                        <a href="{{ route('blueprint.studio', ['slug' => $blueprint->slug, 'mode' => 'mitra']) }}" class="px-2.5 py-1.5 hover:bg-amber-200 text-amber-950 dark:hover:bg-amber-900 dark:text-amber-300 flex items-center gap-1 transition whitespace-nowrap" :title="locale === 'en' ? 'Open Partner Mode: Algorithmic BoQ, SHA-256 Scope Lock' : 'Buka Mode Mitra Proyek: RAB Algoritmik & Kunci SPK'">
                            <span>🤝 <span class="hidden lg:inline" x-show="locale === 'en'">PARTNER</span><span class="hidden lg:inline" x-show="locale !== 'en'">MITRA</span></span>
                        </a>
                    </div>

                    <!-- Developer Execution Guide Trigger (3 Cara Mulai & Docker Guide) -->
                    <button type="button" @click="showExecutionGuideModal = true" class="px-2.5 sm:px-3 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-black text-xs font-mono font-black rounded-none border border-emerald-400 flex items-center gap-1.5 transition shrink-0 whitespace-nowrap cursor-pointer shadow-xs" :title="locale === 'en' ? 'Developer Execution Guide: 3 Ways to Start + Docker Dev Stack' : 'Panduan Eksekusi Developer: 3 Pilihan Cara Mulai + Docker Stack'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span class="hidden sm:inline">
                            <span x-show="locale === 'en'">3 WAYS TO START</span>
                            <span x-show="locale !== 'en'">3 CARA MULAI</span>
                        </span>
                        <span class="sm:hidden">START</span>
                    </button>

                    <!-- Real-time Live Collaborative Presence Indicator -->
                    <div x-show="collaborators.length > 1" x-cloak class="hidden 2xl:flex items-center gap-1.5 px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-xs font-mono shrink-0 whitespace-nowrap">
                        <span class="w-2 h-2 bg-emerald-500 rounded-none animate-pulse"></span>
                        <span class="text-zinc-700 dark:text-zinc-300 font-bold" x-text="locale === 'en' ? (collaborators.length + ' Live') : (collaborators.length + ' Kolaborator')"></span>
                    </div>
                @endif
            </div>

            <!-- CENTER WING: Primary Actions & AI Suite Dropdown -->
            <div class="flex items-center gap-2 shrink-0">
                <!-- One-Click Scaffold & Boilerplate Exporter -->
                <button type="button" @click="openScaffoldModal()" class="px-3 sm:px-3.5 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-black text-xs font-mono uppercase font-black rounded-none flex items-center gap-1.5 transition cursor-pointer shadow-xs whitespace-nowrap" :title="locale === 'en' ? 'Export Docker Compose, SQL Migrations, &amp; Route Structure' : 'Ekspor Docker Compose, SQL Migrasi, &amp; Struktur Route'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span class="hidden md:inline">
                        <span x-show="locale === 'en'">EXPORT SCAFFOLD</span>
                        <span x-show="locale !== 'en'">EKSPOR SCAFFOLD</span>
                    </span>
                    <span class="md:hidden">SCAFFOLD</span>
                </button>

                <!-- Download Center & 24h Lifecycle Modal Button -->
                <button 
                    type="button" 
                    @click="openDownloadCenterModal()" 
                    class="px-2.5 sm:px-3 py-1.5 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-emerald-400 text-xs font-mono uppercase font-black rounded-none border border-emerald-500/60 flex items-center gap-1.5 transition cursor-pointer shadow-none shrink-0 whitespace-nowrap" 
                    :title="locale === 'en' ? 'Open Download Center: View generated files & background jobs' : 'Buka Pusat Unduhan: Lihat file unduhan & background job'"
                >
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span class="hidden lg:inline" x-text="locale === 'en' ? 'DOWNLOADS' : 'UNDUHAN'">UNDUHAN</span>
                    <span x-show="activeDownloadsCount > 0" x-cloak class="px-1.5 py-0.2 bg-emerald-500 text-black text-[10px] font-black rounded-none" x-text="activeDownloadsCount"></span>
                </button>

                <!-- AI Suite Modal Trigger -->
                <button 
                    type="button" 
                    @click="showAiSuiteModal = true" 
                    class="px-2.5 sm:px-3 py-1.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 text-xs font-mono uppercase font-bold rounded-none border border-zinc-300 dark:border-zinc-700 flex items-center gap-1.5 transition shrink-0 whitespace-nowrap cursor-pointer shadow-xs"
                    :title="locale === 'en' ? 'Open AI Tools Suite: Tech Radar, Prompt Helper, Sprint Cockpit' : 'Buka Menu AI: Tech Radar, Prompt Helper, Cockpit Eksekusi'"
                >
                    <span class="w-2 h-2 rounded-none bg-emerald-500 animate-pulse"></span>
                    <span class="hidden sm:inline">AI SUITE</span>
                    <span class="sm:hidden">AI</span>
                    <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>

            <!-- RIGHT WING: Unified Export Dropdown + View Mode + Scope Status -->
            <div class="flex items-center gap-2 shrink-0">
                <!-- UNIFIED EXPORT & DOCUMENTS MODAL TRIGGER -->
                <button 
                    type="button" 
                    @click="showExportHubModal = true" 
                    class="px-3 py-1.5 bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-white text-xs font-mono font-black rounded-none border border-zinc-800 dark:border-zinc-200 flex items-center gap-1.5 transition cursor-pointer shadow-xs whitespace-nowrap shrink-0"
                    :title="locale === 'en' ? 'Open Export & Documents Hub (PDF, Markdown, .cursorrules, Schema, Contracts)' : 'Buka Hub Dokumen & Ekspor (PDF, Markdown, .cursorrules, Skema, Dokumen Kontrak)'"
                >
                    <svg class="w-3.5 h-3.5 text-emerald-400 dark:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="hidden md:inline">
                        <span x-show="locale === 'en'">EXPORT &amp; DOCUMENTS</span>
                        <span x-show="locale !== 'en'">DOKUMEN &amp; EKSPOR</span>
                    </span>
                    <span class="md:hidden">EXPORT</span>
                    <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <!-- Navigation Layout Mode Switcher (Mode 1: Sidebar vs Mode 2: Full Width) -->
                <button 
                    type="button" 
                    @click="toggleNavMode()" 
                    class="px-2.5 py-1.5 font-mono text-xs uppercase font-black rounded-none border transition flex items-center gap-1.5 shrink-0 whitespace-nowrap cursor-pointer shadow-none"
                    :class="navMode === 'sidebar' 
                        ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 border-zinc-900 dark:border-zinc-100' 
                        : 'bg-emerald-100 text-emerald-950 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-400 dark:border-emerald-700 hover:bg-emerald-200 dark:hover:bg-emerald-900'"
                    :title="navMode === 'sidebar' 
                        ? (locale === 'en' ? 'Mode 1 Active: Sidebar index pinned. Click for Full Width.' : 'Mode 1 Aktif: Index disematkan di kiri. Klik untuk Layar Penuh.') 
                        : (locale === 'en' ? 'Mode 2 Active: Full Width. Click for Sidebar.' : 'Mode 2 Aktif: Layar Penuh. Klik untuk Sidebar Kiri.')"
                >
                    <svg class="w-3.5 h-3.5" :class="navMode === 'sidebar' ? 'text-emerald-400 dark:text-emerald-600' : 'text-emerald-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h6v12H4zM12 6h8v12h-8z"/></svg>
                    <span class="hidden xl:inline"><span x-show="navMode === 'sidebar'">SIDEBAR</span><span x-show="navMode !== 'sidebar'">FULL WIDTH</span></span>
                </button>

                <!-- Single Unified Section Expand / Collapse Toggle -->
                <button 
                    type="button" 
                    @click="toggleAllSections()" 
                    class="px-2.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 text-xs font-mono font-bold rounded-none transition flex items-center gap-1.5 cursor-pointer shrink-0" 
                    :title="allSectionsExpanded ? (locale === 'en' ? 'Collapse all PRD sections' : 'Ciutkan seluruh seksi PRD') : (locale === 'en' ? 'Expand all PRD sections' : 'Buka seluruh seksi PRD')"
                >
                    <svg x-show="allSectionsExpanded" class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                    <svg x-show="!allSectionsExpanded" class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    <span x-show="allSectionsExpanded">
                        <span x-show="locale === 'en'">COLLAPSE</span>
                        <span x-show="locale !== 'en'">CIUTKAN</span>
                    </span>
                    <span x-show="!allSectionsExpanded">
                        <span x-show="locale === 'en'">EXPAND</span>
                        <span x-show="locale !== 'en'">BUKA</span>
                    </span>
                </button>

                <!-- Smart Auto-Accordion Scroll Engine Toggle -->
                <button 
                    type="button" 
                    @click="toggleAutoAccordion()" 
                    class="px-2.5 py-1.5 border text-xs font-mono font-bold rounded-none transition flex items-center gap-1.5 cursor-pointer shrink-0" 
                    :class="autoAccordionEnabled 
                        ? 'bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border-emerald-500/30' 
                        : 'bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 border-zinc-300 dark:border-zinc-700'"
                    :title="autoAccordionEnabled 
                        ? (locale === 'en' ? 'Smart Auto-Accordion Active: Automatically collapses passed sections and expands visible sections on scroll. Click to disable.' : 'Smart Auto-Accordion Aktif: Otomatis ciutkan seksi yang sudah dilewati dan buka seksi yang tampil di layar. Klik untuk nonaktifkan.') 
                        : (locale === 'en' ? 'Smart Auto-Accordion Disabled. Click to enable automatic collapse/expand on scroll.' : 'Smart Auto-Accordion Nonaktif. Klik untuk aktifkan otomatisasi buka-tutup seksi saat scroll.')"
                >
                    <span class="w-1.5 h-1.5 rounded-none" :class="autoAccordionEnabled ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-400'"></span>
                    <span class="hidden md:inline">
                        <span x-show="autoAccordionEnabled">AUTO-ACCORDION</span>
                        <span x-show="!autoAccordionEnabled">MANUAL</span>
                    </span>
                </button>

                @php
                    $isRetailLicense = ($blueprint->project_status === 'Retail License'
                        || ($isPurchased ?? false)
                        || (method_exists($blueprint, 'hasPaidRetailLicense') && $blueprint->hasPaidRetailLicense())
                        || !empty($blueprint->user_metadata['paid'])
                        || !empty($blueprint->user_metadata['retail_tier'])
                        || !empty($blueprint->user_metadata['package_tier'])
                        || str_contains(strtolower($blueprint->slug ?? ''), '-retail-'));
                    $isDpPaid = $blueprint->isDpConfirmed();
                    $isScopeFrozen = $blueprint->isScopeFrozen();
                    $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin();
                    $isLocked = $isScopeFrozen && ! $isRetailLicense;
                    $canUnlock = $isSuperAdmin || (! $isDpPaid) || $isRetailLicense;
                @endphp

                <!-- Scope Status / Edit Trigger -->
                @if(!$isLocked)
                    <a href="{{ route('blueprint.create', ['slug' => $blueprint->slug]) }}" class="px-2.5 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-mono uppercase font-bold rounded-none border border-emerald-500/30 transition flex items-center gap-1 shrink-0 whitespace-nowrap" :title="locale === 'en' ? 'Complete / Add Requirements in Studio' : 'Lengkapi / Tambah Kebutuhan di Studio'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span class="hidden sm:inline" x-text="locale === 'en' ? 'EDIT SPEC' : 'LENGKAPI'">LENGKAPI</span>
                    </a>
                @elseif($canUnlock)
                    <form method="POST" action="{{ route('blueprint.unlock-scope', $blueprint->slug) }}" class="m-0 shrink-0">
                        @csrf
                        <input type="hidden" name="edit" value="1">
                        <button type="submit" class="px-2.5 py-1.5 {{ $isSuperAdmin ? 'bg-rose-600 hover:bg-rose-500 text-white border-rose-700' : 'bg-amber-500 hover:bg-amber-400 text-black border-amber-600' }} text-xs font-mono uppercase font-black rounded-none border transition flex items-center gap-1 shrink-0 whitespace-nowrap cursor-pointer shadow-none" :title="locale === 'en' ? ($isSuperAdmin ? 'Super Admin: Instantly unlock scope' : 'Unlock scope to continue specification elaboration') : ($isSuperAdmin ? 'Super Admin: Buka kunci scope secara instan' : 'Buka kunci scope untuk melanjutkan elaborasi spesifikasi')">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                            <span class="hidden md:inline">
                                <span x-show="locale === 'en'">{{ $isSuperAdmin ? 'UNLOCK (ADMIN)' : 'UNLOCK SCOPE' }}</span>
                                <span x-show="locale !== 'en'">{{ $isSuperAdmin ? 'UNLOCK (ADMIN)' : 'BUKA KUNCI' }}</span>
                            </span>
                            <span class="md:hidden">UNLOCK</span>
                        </button>
                    </form>
                @else
                    <span class="px-2.5 py-1.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 text-xs font-mono uppercase font-bold border border-zinc-200 dark:border-zinc-700 flex items-center gap-1 cursor-not-allowed select-none shrink-0 whitespace-nowrap" 
                          :title="locale === 'en' ? 'Scope Locked: Active Partner project with confirmed DP.' : 'Scope Terkunci: Proyek Mitra aktif dengan DP terverifikasi.'">
                        <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span class="hidden md:inline">
                            <span x-show="locale === 'en'">SCOPE LOCKED</span>
                            <span x-show="locale !== 'en'">SCOPE TERKUNCI</span>
                        </span>
                    </span>
                @endif

                @if(auth()->check() && auth()->user()->isSuperAdmin())
                    @if(!$blueprint->isDpConfirmed())
                        <form method="POST" action="{{ route('blueprint.vip-bypass', $blueprint->slug) }}" class="m-0 shrink-0">
                            @csrf
                            <button 
                                type="submit" 
                                class="px-2.5 py-1.5 bg-zinc-950 hover:bg-zinc-900 text-amber-300 border-2 border-amber-500 text-xs font-mono uppercase font-black rounded-none flex items-center gap-1.5 transition cursor-pointer shadow-xs shrink-0 whitespace-nowrap"
                                :title="locale === 'en' ? 'Instantly activate this Blueprint as VIP Founder without Midtrans payment' : 'Aktifkan Blueprint ini secara instan sebagai VIP Founder tanpa melalui pembayaran Midtrans'"
                            >
                                <span class="text-amber-400 text-sm drop-shadow-[0_0_8px_rgba(251,191,36,0.9)]">👑</span>
                                <span class="hidden lg:inline text-amber-300 font-bold">VIP OWNER BYPASS</span>
                                <span class="lg:hidden text-amber-300 font-bold">BYPASS</span>
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
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
