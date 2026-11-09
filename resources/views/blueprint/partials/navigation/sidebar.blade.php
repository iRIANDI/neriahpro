            <aside 
                x-show="navMode === 'sidebar'"
                x-cloak
                class="w-72 xl:w-80 shrink-0 hidden xl:flex flex-col sticky z-30 font-mono shadow-sm no-print bg-white dark:bg-zinc-950 border-2 border-zinc-200 dark:border-zinc-800 rounded-none overflow-hidden"
                style="top: calc(var(--global-nav-h, 88px) + var(--toolbar-h, 46px) + 12px); max-height: calc(100vh - var(--global-nav-h, 88px) - var(--toolbar-h, 46px) - 24px);"
                x-on:wheel.passive="(e) => {
                    const scrollEl = $el.querySelector('.custom-prd-scrollbar');
                    if (scrollEl && !scrollEl.contains(e.target)) {
                        scrollEl.scrollTop += e.deltaY;
                    }
                }"
            >
                <!-- Sticky Sidebar Header -->
                <div class="p-3.5 bg-zinc-900 text-white flex items-center justify-between border-b border-zinc-800 shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-emerald-400 rounded-none animate-pulse"></span>
                        <div>
                            <h3 class="font-bold text-xs uppercase tracking-wider text-emerald-400">
                                <span x-show="locale === 'en'">PRD Directory</span>
                                <span x-show="locale !== 'en'">Index Navigasi PRD</span>
                            </h3>
                            <p class="text-[10px] text-zinc-400">
                                <span x-show="locale === 'en'">Live Architecture Keystones</span>
                                <span x-show="locale !== 'en'">Navigasi Cepat Dokumen</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <div class="text-[10px] font-bold text-emerald-400 bg-zinc-800 px-2 py-0.5 border border-zinc-700 whitespace-nowrap">
                            <span x-show="locale === 'en'"><span x-text="executionTrack === 'studio' ? '13' : '10'">13</span> CH // <span x-text="executionTrack === 'studio' ? '{{ count($blueprintSections ?? []) }}' : '{{ count($blueprintSections ?? []) - 3 }}'">{{ count($blueprintSections ?? []) }}</span> BLOCKS</span>
                            <span x-show="locale !== 'en'"><span x-text="executionTrack === 'studio' ? '13' : '10'">13</span> BAB // <span x-text="executionTrack === 'studio' ? '{{ count($blueprintSections ?? []) }}' : '{{ count($blueprintSections ?? []) - 3 }}'">{{ count($blueprintSections ?? []) }}</span> BLOK</span>
                        </div>
                        <button 
                            type="button" 
                            @click="toggleNavMode()" 
                            class="text-[10px] font-bold text-zinc-300 hover:text-white bg-zinc-800 hover:bg-zinc-700 px-2 py-0.5 border border-zinc-700 transition flex items-center gap-1 cursor-pointer"
                            :title="locale === 'en' ? 'Switch to Mode 2: Full Width (Hide sidebar, dock navigation)' : 'Alihkan ke Mode 2: Layar Penuh (Sembunyikan sidebar, navigasi via dock kanan bawah)'"
                        >
                            <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                            <span class="hidden 2xl:inline">
                                <span x-show="locale === 'en'">WIDE</span>
                                <span x-show="locale !== 'en'">LEBAR</span>
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Sticky Sidebar Search & Controls -->
                <div class="p-2.5 bg-zinc-50 dark:bg-zinc-900/70 border-b border-zinc-200 dark:border-zinc-800 flex items-center gap-1.5 shrink-0">
                    <div class="relative flex-1">
                        <input 
                            type="text" 
                            x-model="indexSearchQuery" 
                            :placeholder="locale === 'en' ? 'Filter sections...' : 'Cari bagian PRD...'"
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

                <!-- Scrollable Section List (Independently scrollable with mouse wheel) -->
                <div 
                    class="p-2 overflow-y-auto flex-1 min-h-0 space-y-2 text-xs divide-y divide-zinc-100 dark:divide-zinc-900 custom-prd-scrollbar select-none focus:outline-none"
                    tabindex="0"
                >
                    @foreach($accordionGroupDefs as $groupKey => $groupDef)
                        <div class="pt-2 first:pt-0">
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

                <!-- Sticky Sidebar Footer -->
                <div class="p-2.5 bg-zinc-100 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between text-[11px] shrink-0">
                    <span class="text-zinc-500 dark:text-zinc-400 truncate max-w-[170px]">
                        <strong class="text-emerald-600 dark:text-emerald-400" x-text="getActiveSectionTitle()"></strong>
                    </span>
                    <button 
                        type="button" 
                        @click="window.scrollTo({ top: 0, behavior: 'smooth' })" 
                        class="px-2 py-1 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold uppercase transition flex items-center gap-1 hover:opacity-90"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                        <span x-show="locale === 'en'">Top</span>
                        <span x-show="locale !== 'en'">Atas</span>
                    </button>
                </div>
            </aside>

