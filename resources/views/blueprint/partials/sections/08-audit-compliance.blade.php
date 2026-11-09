            <section id="section-8" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid prd-scroll-target">
                <div class="flex items-center justify-between gap-2 mb-4 border-b border-zinc-200 dark:border-zinc-800 pb-3 cursor-pointer select-none" @click="toggleSection('section-8')">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">10</span>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">
                                <span x-show="locale === 'en'">Project Timeline &amp; Milestones (Velocity Aligned)</span>
                                <span x-show="locale !== 'en'">Timeline &amp; Milestone Proyek (Velocity Aligned)</span>
                            </h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">
                                <span x-show="locale === 'en'">Sprint Working Days Allocation from Kickoff to Final Delivery</span>
                                <span x-show="locale !== 'en'">Alokasi Hari Pengerjaan per Sprint dari Kickoff sampai Delivery</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 border border-emerald-300 dark:border-emerald-800 hidden sm:inline">
                            Target: <span x-text="tierAmounts[selectedTier]?.days || '-'"></span>
                        </span>
                        <button 
                            type="button" 
                            @click.stop="toggleSection('section-8')" 
                            class="px-2 py-1 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none flex items-center gap-1 font-mono text-xs border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700 ml-1 cursor-pointer"
                            :title="sectionCollapses['section-8'] ? (locale === 'en' ? 'Expand this section' : 'Buka seksi ini') : (locale === 'en' ? 'Collapse this section' : 'Ciutkan seksi ini')"
                        >
                            <span class="text-[10px] uppercase font-bold text-zinc-400 dark:text-zinc-500 hidden sm:inline" x-text="sectionCollapses['section-8'] ? (locale === 'en' ? 'EXPAND' : 'BUKA') : (locale === 'en' ? 'COLLAPSE' : 'CIUTKAN')"></span>
                            <svg class="w-4 h-4 transform transition-transform duration-200" :class="sectionCollapses['section-8'] ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Collapsed Preview -->
                <div x-show="sectionCollapses['section-8']" @click="toggleSection('section-8')" class="py-2.5 px-3 bg-zinc-50 dark:bg-zinc-950/60 border border-dashed border-zinc-300 dark:border-zinc-800 text-xs font-mono text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between cursor-pointer transition select-none mb-6">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span x-show="locale === 'en'">Section 10 collapsed (Sprint Timeline &amp; Milestones). Click to expand...</span>
                        <span x-show="locale !== 'en'">Seksi 10 diciutkan (Timeline Sprint &amp; Milestone). Klik untuk membuka...</span>
                    </span>
                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500 uppercase font-bold" x-text="locale === 'en' ? '[ + EXPAND ]' : '[ + BUKA ]'">[ + EXPAND ]</span>
                </div>

                <div x-show="!sectionCollapses['section-8']" x-transition class="accordion-body">

                <div class="space-y-2.5 font-mono text-xs">
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-emerald-500"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 1: Architecture Blueprint, PostgreSQL Schema &amp; ULID Migration</span>
                        </div>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold" x-text="(locale === 'en' ? 'Day 1 - ' : 'Hari 1 - ') + Math.max(2, Math.round(parseInt(tierAmounts[selectedTier]?.days || 14) * 0.25))"></span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-zinc-400"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 2: Core Business Logic &amp; Filament Admin v5 Engine</span>
                        </div>
                        <span class="text-zinc-500" x-text="(locale === 'en' ? 'Day ' : 'Hari ') + (Math.max(2, Math.round(parseInt(tierAmounts[selectedTier]?.days || 14) * 0.25)) + 1) + ' - ' + Math.round(parseInt(tierAmounts[selectedTier]?.days || 14) * 0.60)"></span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-zinc-400"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 3: Frontend Island UI (React 19) &amp; User Flow Pipeline</span>
                        </div>
                        <span class="text-zinc-500" x-text="(locale === 'en' ? 'Day ' : 'Hari ') + (Math.round(parseInt(tierAmounts[selectedTier]?.days || 14) * 0.60) + 1) + ' - ' + Math.round(parseInt(tierAmounts[selectedTier]?.days || 14) * 0.85)"></span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 bg-zinc-400"></span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Sprint 4: End-to-End Testing, Security Audit &amp; Production Handover</span>
                        </div>
                        <span class="text-emerald-500 font-bold" x-text="(locale === 'en' ? 'Day ' : 'Hari ') + (Math.round(parseInt(tierAmounts[selectedTier]?.days || 14) * 0.85) + 1) + ' - ' + (tierAmounts[selectedTier]?.days || (locale === 'en' ? 'Complete' : 'Selesai'))"></span>
                    </div>
                </div>
                </div> <!-- end accordion-body section-8 -->
            </section>

            <!-- SECTION 11: STANDAR TATA KELOLA, KUALITAS & SLA SERAH TERIMA (GOVERNANCE & SLA) -->
