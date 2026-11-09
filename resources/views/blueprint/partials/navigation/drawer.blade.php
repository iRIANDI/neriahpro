
            <!-- FLOATING QUICK-ACCESS DOCK & SCROLL-SPY NAVIGATOR (MODE 2: FOCUS FULL WIDTH / MOBILE) -->
            <aside 
                :class="navMode === 'focus' ? 'flex' : 'flex xl:hidden'"
                x-cloak
                class="fixed bottom-5 right-4 sm:right-6 z-40 font-mono no-print flex-col items-end"
                @keydown.escape.window="floatingIndexOpen = false"
            >
                <!-- Floating Accordion Drawer Popover -->
                <div 
                    x-show="floatingIndexOpen" 
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                    @click.outside="floatingIndexOpen = false"
                    x-on:wheel.passive="(e) => {
                        const scrollEl = $el.querySelector('.custom-prd-scrollbar');
                        if (scrollEl && !scrollEl.contains(e.target)) {
                            scrollEl.scrollTop += e.deltaY;
                        }
                    }"
                    class="mb-3 w-[92vw] sm:w-[400px] h-[75vh] max-h-[720px] min-h-[350px] flex flex-col bg-white dark:bg-zinc-950 border-2 border-emerald-500 shadow-2xl rounded-none overflow-hidden"
                >
                    <!-- Drawer Header -->
                    <div class="p-3.5 bg-zinc-900 text-white flex items-center justify-between border-b border-zinc-800">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-emerald-400 rounded-none animate-pulse"></span>
                            <div>
                                <h3 class="font-bold text-xs uppercase tracking-wider text-emerald-400">
                                    <span x-show="locale === 'en'">Quick PRD Directory</span>
                                    <span x-show="locale !== 'en'">Index Navigasi Cepat PRD</span>
                                </h3>
                                <p class="text-[10px] text-zinc-400">
                                    <span x-show="locale === 'en'">Jump to section without scrolling</span>
                                    <span x-show="locale !== 'en'">Pindah cepat tanpa lelah scrolling</span>
                                </p>
                            </div>
                        </div>
                        <button 
                            type="button" 
                            @click="floatingIndexOpen = false" 
                            class="text-zinc-400 hover:text-white text-base px-2 py-0.5 leading-none transition"
                        >
                            &times;
                        </button>
                    </div>

                    <!-- Search & Quick Actions Bar -->
                    <div class="p-2.5 bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 flex items-center gap-2">
                        <div class="relative flex-1">
                            <input 
                                type="text" 
                                x-model="indexSearchQuery" 
                                :placeholder="locale === 'en' ? 'Filter sections...' : 'Cari blok/fitur/ERD...'"
                                class="w-full bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 px-2.5 py-1 text-xs text-zinc-900 dark:text-zinc-100 rounded-none focus:outline-none focus:border-emerald-500"
                            />
                            <button 
                                type="button" 
                                x-show="indexSearchQuery" 
                                @click="indexSearchQuery = ''" 
                                class="absolute right-2 top-1 text-zinc-400 hover:text-zinc-600 text-xs"
                            >&times;</button>
                        </div>
                        <button 
                            type="button" 
                            @click="expandAllGroups()" 
                            class="px-2 py-1 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-[10px] border border-zinc-300 dark:border-zinc-700 transition"
                            :title="locale === 'en' ? 'Expand All' : 'Buka Semua'"
                        >
                            &boxplus;
                        </button>
                        <button 
                            type="button" 
                            @click="collapseAllGroups()" 
                            class="px-2 py-1 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-[10px] border border-zinc-300 dark:border-zinc-700 transition"
                            :title="locale === 'en' ? 'Collapse All' : 'Tutup Semua'"
                        >
                            &boxminus;
                        </button>
                        <button 
                            type="button" 
                            @click="toggleAutoSync()" 
                            :class="autoSyncAccordion ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border-emerald-500' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500 border-zinc-300 dark:border-zinc-700'"
                            class="px-2 py-1 text-[10px] font-mono font-bold border transition flex items-center gap-1"
                            :title="autoSyncAccordion ? 'Auto-Collapse Aktif: Otomatis buka-tutup grup mengikuti scroll' : 'Auto-Collapse Nonaktif: Klik untuk aktifkan mode spy'"
                        >
                            <span class="w-1.5 h-1.5 rounded-none" :class="autoSyncAccordion ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-400'"></span>
                            <span x-show="autoSyncAccordion">SPY</span>
                            <span x-show="!autoSyncAccordion">OFF</span>
                        </button>
                    </div>

                    <!-- Drawer Accordion Body (Scrollable with mousewheel) -->
                    <div 
                        class="p-2 overflow-y-auto flex-1 min-h-0 space-y-2 text-xs divide-y divide-zinc-100 dark:divide-zinc-900 custom-prd-scrollbar select-none focus:outline-none"
                        tabindex="0"
                    >
                        @foreach($accordionGroupDefs as $groupKey => $groupDef)
                            <div class="pt-2 first:pt-0">
                                <!-- Group Header Toggle -->
                                <button 
                                    type="button" 
                                    @click="toggleAccordionGroup('{{ $groupKey }}')"
                                    :class="sectionGroupMap[activeSectionId] === '{{ $groupKey }}' 
                                        ? 'bg-emerald-500/10 dark:bg-emerald-950/40 border-emerald-500/60 text-emerald-600 dark:text-emerald-400 font-bold' 
                                        : 'bg-zinc-100 dark:bg-zinc-900/80 hover:bg-zinc-200 dark:hover:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border-zinc-200 dark:border-zinc-800'"
                                    class="w-full px-2 py-1.5 text-left flex items-center justify-between transition border"
                                >
                                    <span class="text-[11px] flex items-center gap-1.5">
                                        <span class="font-mono" :class="sectionGroupMap[activeSectionId] === '{{ $groupKey }}' ? 'text-emerald-500 font-black' : 'text-zinc-500'">#{{ $loop->iteration }}</span>
                                        <span x-show="locale === 'en'">{{ $groupDef['title_en'] }}</span>
                                        <span x-show="locale !== 'en'">{{ $groupDef['title_id'] }}</span>
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        <span x-show="sectionGroupMap[activeSectionId] === '{{ $groupKey }}'" class="px-1.5 py-0.2 bg-emerald-500 text-black text-[8px] font-mono font-bold uppercase tracking-wider">
                                            ACTIVE
                                        </span>
                                        <svg 
                                            class="w-3.5 h-3.5 transition-transform duration-200" 
                                            :class="[
                                                accordionGroups['{{ $groupKey }}'] ? 'rotate-180' : '',
                                                sectionGroupMap[activeSectionId] === '{{ $groupKey }}' ? 'text-emerald-500' : 'text-zinc-500'
                                            ]" 
                                            fill="none" 
                                            stroke="currentColor" 
                                            viewBox="0 0 24 24"
                                        ><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </button>

                                <!-- Group Items -->
                                <div 
                                    x-show="accordionGroups['{{ $groupKey }}'] || indexSearchQuery.trim() !== ''" 
                                    x-transition
                                    class="mt-1 space-y-1 pl-1"
                                >
                                    @foreach($blueprintSections as $sec)
                                        @if($sec['group'] === $groupKey)
                                            @php
                                                $isStudioExclusive = in_array($sec['id'], ['section-7', 'section-8', 'section-9'], true);
                                            @endphp
                                            <div 
                                                data-spy-sec="{{ $sec['id'] }}"
                                                @if($isStudioExclusive)
                                                    x-show="executionTrack === 'studio' && (!indexSearchQuery || '{{ strtolower($sec['title_id'] . ' ' . $sec['title_en'] . ' ' . $sec['subtitle_id'] . ' ' . $sec['subtitle_en'] . ' ' . $sec['badge'] . ' ' . $sec['num']) }}'.includes(indexSearchQuery.toLowerCase()))"
                                                @else
                                                    x-show="!indexSearchQuery || '{{ strtolower($sec['title_id'] . ' ' . $sec['title_en'] . ' ' . $sec['subtitle_id'] . ' ' . $sec['subtitle_en'] . ' ' . $sec['badge'] . ' ' . $sec['num']) }}'.includes(indexSearchQuery.toLowerCase())"
                                                @endif
                                                @click="jumpTo('{{ $sec['id'] }}')"
                                                :class="activeSectionId === '{{ $sec['id'] }}' 
                                                    ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400 font-bold' 
                                                    : 'border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 text-zinc-700 dark:text-zinc-300 hover:border-zinc-400 dark:hover:border-zinc-600'"
                                                class="px-2.5 py-1.5 border rounded-none cursor-pointer transition flex items-center justify-between text-xs group"
                                            >
                                                <div class="flex items-center gap-2 truncate pr-1">
                                                    <span 
                                                        :class="activeSectionId === '{{ $sec['id'] }}' ? 'bg-emerald-500 text-black' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'"
                                                        class="w-4 h-4 flex-shrink-0 flex items-center justify-center font-bold text-[9px] rounded-none"
                                                    >
                                                        {{ $sec['num'] }}
                                                    </span>
                                                    <span class="truncate text-[11px] group-hover:text-emerald-500">
                                                        <span x-show="locale === 'en'">{{ $sec['title_en'] }}</span>
                                                        <span x-show="locale !== 'en'">{{ $sec['title_id'] }}</span>
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-1 flex-shrink-0">
                                                    <span x-show="activeSectionId === '{{ $sec['id'] }}'" class="w-1.5 h-1.5 bg-emerald-500 rounded-none animate-ping"></span>
                                                    <span class="text-[9px] text-zinc-400 uppercase font-mono">{{ $sec['badge'] }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Drawer Footer with Back to Top -->
                    <div class="p-2.5 bg-zinc-100 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between text-[11px]">
                        <span class="text-zinc-500 dark:text-zinc-400">
                            <span x-show="locale === 'en'">Active:</span>
                            <span x-show="locale !== 'en'">Aktif:</span>
                            <strong class="text-emerald-600 dark:text-emerald-400 ml-1" x-text="getActiveSectionTitle()"></strong>
                        </span>
                        <button 
                            type="button" 
                            @click="window.scrollTo({ top: 0, behavior: 'smooth' }); floatingIndexOpen = false;" 
                            class="px-2 py-1 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold uppercase transition flex items-center gap-1 hover:opacity-90"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            <span x-show="locale === 'en'">Top</span>
                            <span x-show="locale !== 'en'">Atas</span>
                        </button>
                    </div>
                </div>

                <!-- Main Dock Pill-Free Bar -->
                <div class="flex items-center bg-zinc-950/95 text-white border-2 border-emerald-500 shadow-2xl backdrop-blur-md rounded-none overflow-hidden select-none">
                    <!-- Live Radar & Active Section Status -->
                    <button 
                        type="button" 
                        @click="floatingIndexOpen = !floatingIndexOpen"
                        class="px-3.5 py-2 hover:bg-zinc-800/80 transition flex items-center gap-2.5 text-left border-r border-zinc-800 group"
                        :title="locale === 'en' ? 'Click to toggle PRD section index' : 'Klik untuk buka/tutup index bagian PRD'"
                    >
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1.5 text-[9px] text-zinc-400 leading-none">
                                <span class="text-emerald-400 font-bold" x-text="'[' + getActiveSectionNumber() + ' / 12]'"></span>
                                <span class="hidden sm:inline uppercase">SPY ACTIVE</span>
                            </div>
                            <span class="text-xs font-bold text-zinc-100 group-hover:text-emerald-400 transition-colors max-w-[150px] sm:max-w-[210px] truncate leading-tight mt-0.5" x-text="getActiveSectionTitle()"></span>
                        </div>
                        <svg 
                            class="w-3.5 h-3.5 text-emerald-400 transition-transform duration-200" 
                            :class="floatingIndexOpen ? 'rotate-180' : ''" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        ><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                    </button>

                    <!-- Quick Jump Index Button -->
                    <button 
                        type="button" 
                        @click="floatingIndexOpen = !floatingIndexOpen"
                        :class="floatingIndexOpen ? 'bg-emerald-500 text-black font-black' : 'text-zinc-200 hover:bg-zinc-800/80 hover:text-emerald-400'"
                        class="px-3 py-2 text-xs font-bold uppercase tracking-wider transition border-r border-zinc-800 flex items-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        <span class="hidden sm:inline" x-show="locale === 'en'">INDEX</span>
                        <span class="hidden sm:inline" x-show="locale !== 'en'">DAFTAR ISI</span>
                    </button>

                    <!-- Switch Back to Mode 1 (Sidebar Mode) -->
                    <button 
                        type="button" 
                        x-show="navMode === 'focus'"
                        @click="setNavMode('sidebar')" 
                        class="px-3 py-2 text-xs font-bold uppercase tracking-wider transition border-r border-zinc-800 flex items-center gap-1.5 text-zinc-300 hover:text-emerald-400 hover:bg-zinc-800/80 cursor-pointer"
                        :title="locale === 'en' ? 'Switch back to Mode 1: Show sidebar index on the left' : 'Beralih kembali ke Mode 1: Tampilkan index di sebelah kiri layar'"
                    >
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h6v12H4zM12 6h8v12h-8z"/></svg>
                        <span class="hidden sm:inline">
                            <span x-show="locale === 'en'">SIDEBAR MODE</span>
                            <span x-show="locale !== 'en'">MODE SIDEBAR</span>
                        </span>
                    </button>

                    <!-- Scroll to Top Button -->
                    <button 
                        type="button" 
                        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                        class="px-2.5 py-2 text-zinc-400 hover:text-white hover:bg-zinc-800/80 transition"
                        :title="locale === 'en' ? 'Back to top' : 'Kembali ke atas'"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    </button>
                </div>
            </aside>

