            <section id="section-9" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid prd-scroll-target">
                <div class="flex items-center justify-between gap-2 mb-4 border-b border-zinc-200 dark:border-zinc-800 pb-3 cursor-pointer select-none" @click="toggleSection('section-9')">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none" x-text="getSectionNum('section-9')">12</span>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">
                                <span x-show="locale === 'en'">Governance, Quality Standards &amp; Handover SLA</span>
                                <span x-show="locale !== 'en'">Tata Kelola, Kualitas &amp; SLA Serah Terima</span>
                            </h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">
                                <span x-show="locale === 'en'">Enterprise Service Level Agreement, DoD &amp; Warranty Clauses</span>
                                <span x-show="locale !== 'en'">Enterprise Service Level Agreement, DoD, &amp; Klausul Garansi</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 border border-emerald-300 dark:border-emerald-800 hidden sm:inline">
                            <span x-show="locale === 'en'">ENTERPRISE SLA</span>
                            <span x-show="locale !== 'en'">SLA ENTERPRISE</span>
                        </span>
                        <button 
                            type="button" 
                            @click.stop="toggleSection('section-9')" 
                            class="px-2 py-1 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none flex items-center gap-1 font-mono text-xs border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700 ml-1 cursor-pointer"
                            :title="sectionCollapses['section-9'] ? (locale === 'en' ? 'Expand this section' : 'Buka seksi ini') : (locale === 'en' ? 'Collapse this section' : 'Ciutkan seksi ini')"
                        >
                            <span class="text-[10px] uppercase font-bold text-zinc-400 dark:text-zinc-500 hidden sm:inline" x-text="sectionCollapses['section-9'] ? (locale === 'en' ? 'EXPAND' : 'BUKA') : (locale === 'en' ? 'COLLAPSE' : 'CIUTKAN')"></span>
                            <svg class="w-4 h-4 transform transition-transform duration-200" :class="sectionCollapses['section-9'] ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Collapsed Preview -->
                <div x-show="sectionCollapses['section-9']" @click="toggleSection('section-9')" class="py-2.5 px-3 bg-zinc-50 dark:bg-zinc-950/60 border border-dashed border-zinc-300 dark:border-zinc-800 text-xs font-mono text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between cursor-pointer transition select-none mb-6">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span x-show="locale === 'en'">Section <span x-text="getSectionNum('section-9')">12</span> collapsed (Governance, Quality &amp; SLA). Click to expand...</span>
                        <span x-show="locale !== 'en'">Seksi <span x-text="getSectionNum('section-9')">12</span> diciutkan (Tata Kelola, Kualitas &amp; SLA). Klik untuk membuka...</span>
                    </span>
                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500 uppercase font-bold" x-text="locale === 'en' ? '[ + EXPAND ]' : '[ + BUKA ]'">[ + EXPAND ]</span>
                </div>

                <div x-show="!sectionCollapses['section-9']" x-transition class="accordion-body">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 font-mono text-xs mb-6">
                    <!-- 1. Definition of Done (DoD) -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2 h-2 bg-emerald-500"></span>
                            <h3 class="font-bold uppercase text-zinc-900 dark:text-zinc-100 text-xs">
                                <span x-show="locale === 'en'">1. Official Completion Criteria (Definition of Done)</span>
                                <span x-show="locale !== 'en'">1. Kriteria Penyelesaian Resmi (Definition of Done)</span>
                            </h3>
                        </div>
                        @php
                            $defaultDodId = [
                                'Seluruh fitur MVP Fase 1 berjalan sesuai spesifikasi di server Staging & Production.',
                                'Lolos audit keamanan dasar (CSRF token, sanitasi input XSS, proteksi SQL Injection, & HTTPS SSL).',
                                'Skema basis data relasional PostgreSQL dengan Primary Key ULID terverifikasi.',
                                'Dasbor admin Filament v5 dapat diakses oleh peran Superadmin / Staff yang ditunjuk.',
                                'Sesi pelatihan administrasi singkat dan serah terima kredensial resmi sistem.'
                            ];
                            $defaultDodEn = [
                                'All Phase 1 MVP features operate to specifications on Staging & Production servers.',
                                'Passes core security audit (CSRF token, XSS sanitization, SQL Injection protection, & HTTPS SSL).',
                                'PostgreSQL relational database schema with verified ULID Primary Keys.',
                                'Filament v5 admin dashboard accessible by assigned Superadmin / Staff roles.',
                                'Brief admin training walkthrough and official system credentials handover.'
                            ];
                            $dodItems = $prd['governance_and_sla']['definition_of_done'] ?? null;
                        @endphp
                        <ul class="space-y-2 text-zinc-600 dark:text-zinc-400 text-[11px]">
                            @if(empty($dodItems))
                                @foreach($defaultDodId as $dIdx => $dodId)
                                    <li class="flex items-start gap-2">
                                        <span class="text-emerald-500 font-bold">&check;</span>
                                        <span>
                                            <span x-show="locale === 'en'">{{ $defaultDodEn[$dIdx] }}</span>
                                            <span x-show="locale !== 'en'">{{ $dodId }}</span>
                                        </span>
                                    </li>
                                @endforeach
                            @else
                                @foreach($dodItems as $dod)
                                    <li class="flex items-start gap-2">
                                        <span class="text-emerald-500 font-bold">&check;</span>
                                        <span>{{ $dod }}</span>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>

                    <!-- 2. Browser & Device Support Matrix -->
                    <div class="p-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2 h-2 bg-emerald-500"></span>
                            <h3 class="font-bold uppercase text-zinc-900 dark:text-zinc-100 text-xs">
                                <span x-show="locale === 'en'">2. Browser &amp; Device Support Matrix</span>
                                <span x-show="locale !== 'en'">2. Matriks Dukungan Browser &amp; Perangkat</span>
                            </h3>
                        </div>
                        <div class="space-y-3 text-[11px]">
                            <div class="p-2.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 dark:text-emerald-300">
                                <strong class="block mb-1">
                                    <span x-show="locale === 'en'">&check; OFFICIALLY SUPPORTED DEVICES &amp; BROWSERS:</span>
                                    <span x-show="locale !== 'en'">&check; PERANGKAT &amp; BROWSER DIDUKUNG RESMI:</span>
                                </strong>
                                <span x-show="locale === 'en'">{{ $prd['governance_and_sla']['browser_device_matrix']['supported_en'] ?? ($prd['governance_and_sla']['browser_device_matrix']['supported'] ?? 'Google Chrome, Apple Safari, Mozilla Firefox, Microsoft Edge (last 2 years releases); iOS Safari 15+; Android Chrome 100+.') }}</span>
                                <span x-show="locale !== 'en'">{{ $prd['governance_and_sla']['browser_device_matrix']['supported'] ?? 'Google Chrome, Apple Safari, Mozilla Firefox, Microsoft Edge (rilis 2 tahun terakhir); iOS Safari 15+; Android Chrome 100+.' }}</span>
                            </div>
                            <div class="p-2.5 bg-rose-500/10 border border-rose-500/30 text-rose-800 dark:text-rose-300">
                                <strong class="block mb-1">
                                    <span x-show="locale === 'en'">&times; EXCLUDED UNSUPPORTED BROWSERS:</span>
                                    <span x-show="locale !== 'en'">&times; PERAMBAN DI LUAR RUANG LINGKUP (EXCLUDED):</span>
                                </strong>
                                <span x-show="locale === 'en'">{{ $prd['governance_and_sla']['browser_device_matrix']['unsupported_en'] ?? ($prd['governance_and_sla']['browser_device_matrix']['unsupported'] ?? 'Internet Explorer 11, Opera Mini data-saving mode, UC Browser legacy rendering engine, and non-standard mobile browsers.') }}</span>
                                <span x-show="locale !== 'en'">{{ $prd['governance_and_sla']['browser_device_matrix']['unsupported'] ?? 'Internet Explorer 11, Opera Mini data-saving mode, UC Browser legacy rendering engine, dan peramban ponsel non-standar.' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 font-mono text-xs">
                    <!-- 3. Warranty & Bug-Fix Period -->
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <span class="text-[9px] uppercase px-1.5 py-0.5 bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold block w-fit mb-2">
                            <span x-show="locale === 'en'">30-DAY FULL WARRANTY</span>
                            <span x-show="locale !== 'en'">GARANSI PENUH 30 HARI</span>
                        </span>
                        <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">
                            <span x-show="locale === 'en'">Bug-Fix Warranty</span>
                            <span x-show="locale !== 'en'">Garansi Perbaikan Bug</span>
                        </h4>
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            <span x-show="locale === 'en'">Free resolution for bugs, runtime errors, or Phase 1 MVP discrepancies within 30 calendar days post-Go-Live.</span>
                            <span x-show="locale !== 'en'">Gratis perbaikan terhadap bug, error, atau ketidaksesuaian fungsi MVP Fase 1 selama 30 hari kalender setelah Go-Live.</span>
                        </p>
                    </div>

                    <!-- 4. Content Bottleneck Protocol -->
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <span class="text-[9px] uppercase px-1.5 py-0.5 bg-amber-500/20 text-amber-600 dark:text-amber-400 font-bold block w-fit mb-2">
                            <span x-show="locale === 'en'">7-DAY CONTENT DEADLINE</span>
                            <span x-show="locale !== 'en'">BATAS MATERI 7 HARI</span>
                        </span>
                        <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">
                            <span x-show="locale === 'en'">Client Content Clause</span>
                            <span x-show="locale !== 'en'">Klausul Konten Klien</span>
                        </h4>
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            <span x-show="locale === 'en'">If copy/photo assets are not provided within 7 business days, developers may utilize dummy/placeholder assets to maintain release schedule.</span>
                            <span x-show="locale !== 'en'">Jika materi teks/foto belum diberikan dalam 7 hari kerja, developer berhak menggunakan dummy/placeholder demi menjaga ketepatan waktu rilis.</span>
                        </p>
                    </div>

                    <!-- 5. Third-Party Recurring Transparency -->
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                        <span class="text-[9px] uppercase px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold block w-fit mb-2">
                            <span x-show="locale === 'en'">TRANSPARENT RECURRING</span>
                            <span x-show="locale !== 'en'">BIAYA BERULANG JUJUR</span>
                        </span>
                        <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">
                            <span x-show="locale === 'en'">Third-Party Estimates</span>
                            <span x-show="locale !== 'en'">Estimasi Pihak Ketiga</span>
                        </h4>
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            <span x-show="locale === 'en'">Annual domain ($10-$20/yr) &amp; hosting ($5-$15/mo) billed directly to official cloud providers with zero hidden markup.</span>
                            <span x-show="locale !== 'en'">Domain tahunan (Rp 150rb-250rb/thn) &amp; hosting (Rp 50rb-150rb/bln) dibayarkan langsung ke penyedia cloud resmi tanpa markup tersembunyi.</span>
                        </p>
                    </div>
                </div>
                </div> <!-- end accordion-body section-9 -->
            </section>
