@php 
    $isExecutiveImpacted = in_array('executive_summary', $prd['_impacted_segments'] ?? []); 
    $isEn = ($isEn ?? false) || ($locale ?? 'id') === 'en';
@endphp
            <section id="section-1" class="bg-white dark:bg-zinc-900 border {{ $isExecutiveImpacted ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-zinc-200 dark:border-zinc-800' }} p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid prd-scroll-target transition-all">
                <div class="flex flex-col gap-2 mb-4 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                    <div class="flex items-center justify-between gap-2 cursor-pointer select-none" @click="toggleSection('section-1')">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">01</span>
                            <div>
                                <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">Executive Technical Discovery</h2>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">{{ $isEn ? '26 Architecture Parameters & Business Requirements Discovery' : '26 Parameter Arsitektur & Analisis Kebutuhan Bisnis' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click.stop="openTweakModal('executive_summary', 'Executive Technical Discovery')" class="px-3 py-1.5 border {{ $isExecutiveImpacted ? 'border-amber-500 text-amber-600 bg-amber-50 hover:bg-amber-100 dark:border-amber-600 dark:text-amber-400 dark:bg-amber-900/20 dark:hover:bg-amber-900/40' : 'border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400' }} font-bold uppercase text-[10px] flex items-center gap-1.5 transition shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                TWEAK
                            </button>
                            <button 
                                type="button" 
                                @click.stop="toggleSection('section-1')" 
                                class="px-2 py-1 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none flex items-center gap-1 font-mono text-xs border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700"
                                :title="sectionCollapses['section-1'] ? (isEn ? 'Expand this section' : 'Buka seksi ini') : (isEn ? 'Collapse this section' : 'Ciutkan seksi ini')"
                            >
                                <span class="text-[10px] uppercase font-bold text-zinc-400 dark:text-zinc-500 hidden sm:inline" x-text="sectionCollapses['section-1'] ? (isEn ? 'EXPAND' : 'BUKA') : (isEn ? 'COLLAPSE' : 'CIUTKAN')"></span>
                                <svg class="w-4 h-4 transform transition-transform duration-200" :class="sectionCollapses['section-1'] ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                    </div>
                    @if($isExecutiveImpacted)
                    <div class="flex items-start gap-2 p-2.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 text-xs font-mono text-amber-700 dark:text-amber-400 mt-1">
                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p x-html="locale === 'en' ? '<strong>NEEDS REVIEW:</strong> This segment is detected as <em>stale</em> due to manual tweaks in strongly dependent upstream segments. Please click TWEAK and review or AI regenerate to align it.' : '<strong>NEEDS REVIEW:</strong> Segmen ini terdeteksi <em>stale</em> karena perubahan manual/tweak di segmen lain yang memiliki ketergantungan erat. Harap klik TWEAK lalu tinjau atau regenerate AI agar selaras kembali.'"></p>
                    </div>
                    @endif
                </div>

                <!-- Collapsed Preview -->
                <div x-show="sectionCollapses['section-1']" @click="toggleSection('section-1')" class="py-2.5 px-3 bg-zinc-50 dark:bg-zinc-950/60 border border-dashed border-zinc-300 dark:border-zinc-800 text-xs font-mono text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between cursor-pointer transition select-none mt-2">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>{{ $isEn ? 'Section 01 collapsed (26 Parameter Discovery). Click to expand...' : 'Seksi 01 diciutkan (26 Parameter Discovery). Klik untuk membuka...' }}</span>
                    </span>
                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500 uppercase font-bold">[ + EXPAND ]</span>
                </div>

                <div x-show="!sectionCollapses['section-1']" x-transition class="accordion-body">

                <div class="grid md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-zinc-50 dark:bg-zinc-950 p-5 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400 mb-2">
                            <span x-show="locale === 'en'">Key Problems Solved</span>
                            <span x-show="locale !== 'en'">Masalah Utama yang Diselesaikan</span>
                        </h3>
                        <div class="text-zinc-700 dark:text-zinc-300 text-sm leading-relaxed font-sans">
                            {!! \App\Support\BlueprintPdfHelper::formatWebPoints($blueprint->masalah_utama ?? ($prd['executive_summary']['problem_statement'] ?? '-'), true) !!}
                        </div>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-950 p-5 border border-zinc-200 dark:border-zinc-800 rounded-none">
                        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-2">
                            <span x-show="locale === 'en'">Success Metrics (KPI)</span>
                            <span x-show="locale !== 'en'">Tolak Ukur Kesuksesan (KPI)</span>
                        </h3>
                        <div class="text-zinc-700 dark:text-zinc-300 text-sm leading-relaxed font-sans">
                            {!! \App\Support\BlueprintPdfHelper::formatWebPoints($blueprint->tujuan_utama ?? ($prd['executive_summary']['success_metrics'] ?? '-'), true) !!}
                        </div>
                    </div>
                </div>

                <div class="bg-zinc-50 dark:bg-zinc-950 border-l-4 border-emerald-500 p-4 font-sans text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed rounded-none mb-6">
                    <strong class="font-mono uppercase text-xs text-emerald-600 dark:text-emerald-400 block mb-1">
                        <span x-show="locale === 'en'">Architecture Philosophy &amp; Cost Efficiency</span>
                        <span x-show="locale !== 'en'">Filosofi Arsitektur &amp; Efisiensi Biaya</span>
                    </strong>
                    {{ $prd['executive_summary']['architecture_philosophy'] ?? 'Sistem menggunakan arsitektur Modern Monolith (Laravel 13 & Filament PHP) untuk memangkas biaya server, menjamin isolasi data, dan mempercepat peluncuran fitur hingga 3x lipat.' }}
                </div>

                <!-- 5 CRITICAL GOVERNANCE PARAMETERS GRID (ANTI-DISPUTE SHIELD) -->
                <div class="pt-5 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            <span x-text="locale === 'en' ? '5 Core Architectural Governance Parameters (Dispute Prevention)' : '5 Pilar Tata Kelola Arsitektur & Anti-Sengketa Klien'">5 Pilar Tata Kelola Arsitektur & Anti-Sengketa Klien</span>
                        </span>
                        <span class="text-[10px] font-mono px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 uppercase font-bold">
                            CONTRACTUAL LOCK
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs font-mono">
                        <!-- 1. Target Platform -->
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-200 dark:border-zinc-800 rounded-none hover:border-emerald-500/50 transition">
                            <span class="text-zinc-400 text-[10px] block mb-1 uppercase font-bold" x-text="locale === 'en' ? '1. Target Platform & Accessibility' : '1. Target Platform & Aksesibilitas'">1. Target Platform & Aksesibilitas</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100 block text-xs leading-snug">
                                {{ $blueprint->target_platform ?? ($prd['executive_summary']['target_platform'] ?? 'Responsive Modern Web & PWA') }}
                            </span>
                        </div>

                        <!-- 2. Legacy Data Migration -->
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-200 dark:border-zinc-800 rounded-none hover:border-emerald-500/50 transition">
                            <span class="text-zinc-400 text-[10px] block mb-1 uppercase font-bold" x-text="locale === 'en' ? '2. Legacy Data Migration Scope' : '2. Migrasi Data Warisan'">2. Migrasi Data Warisan</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100 block text-xs leading-snug">
                                {{ $blueprint->migrasi_data ?? ($prd['executive_summary']['legacy_data_migration'] ?? 'Database Baru Bersih (Clean Start)') }}
                            </span>
                        </div>

                        <!-- 3. Hosting Infrastructure -->
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-200 dark:border-zinc-800 rounded-none hover:border-emerald-500/50 transition">
                            <span class="text-zinc-400 text-[10px] block mb-1 uppercase font-bold" x-text="locale === 'en' ? '3. Server & Hosting Infrastructure' : '3. Infrastruktur Hosting & Server'">3. Infrastruktur Hosting & Server</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100 block text-xs leading-snug">
                                {{ $blueprint->preferensi_hosting ?? ($prd['executive_summary']['hosting_infrastructure'] ?? 'Managed Cloud VPS Neriah Pro') }}
                            </span>
                        </div>

                        <!-- 4. Warranty & SLA -->
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-200 dark:border-zinc-800 rounded-none hover:border-emerald-500/50 transition">
                            <span class="text-zinc-400 text-[10px] block mb-1 uppercase font-bold" x-text="locale === 'en' ? '4. Warranty, SLA & Git Handover' : '4. Garansi, SLA & Serah Terima Git'">4. Garansi, SLA & Serah Terima Git</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100 block text-xs leading-snug">
                                {{ $blueprint->garansi_sla ?? ($prd['executive_summary']['warranty_sla'] ?? '30 Hari Garansi Bug + Transfer Repo Git') }}
                            </span>
                        </div>

                        <!-- 5. Payment Milestones -->
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-200 dark:border-zinc-800 rounded-none hover:border-emerald-500/50 transition sm:col-span-2 lg:col-span-2">
                            <span class="text-zinc-400 text-[10px] block mb-1 uppercase font-bold" x-text="locale === 'en' ? '5. Payment Milestone Schedule' : '5. Skema Termin Pembayaran'">5. Skema Termin Pembayaran</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 block text-xs leading-snug">
                                {{ $blueprint->user_metadata['termin_pembayaran'] ?? ($prd['executive_summary']['payment_milestones'] ?? 'Termin 1 (50% DP Kickoff) + Termin 2 (50% Pelunasan setelah UAT Lolos)') }}
                            </span>
                        </div>
                    </div>
                </div>
                </div>
            </section>

            <!-- SECTION 1.5: ANALISIS KELAYAKAN BISNIS, PROYEKSI ROI & 5 GARANSI BEBAS PENYESALAN -->
            @php
                $corpusRoi = strtolower($blueprint->nama_bisnis . ' ' . ($blueprint->masalah_utama ?? '') . ' ' . ($blueprint->tujuan_utama ?? '') . ' ' . ($blueprint->fitur_wajib ?? ''));
                $isTravelDomain = str_contains($corpusRoi, 'tour') || str_contains($corpusRoi, 'travel') || str_contains($corpusRoi, 'wisata') ||
                            str_contains($corpusRoi, 'itinerary') || str_contains($corpusRoi, 'trip') || str_contains($corpusRoi, 'overland') ||
                            str_contains($corpusRoi, 'ngurah rai') || str_contains($corpusRoi, 'gayabalical');
                $savedCat = $prd['business_roi_analysis']['domain_category'] ?? '';
                $isMismatchedRoi = $isTravelDomain && (str_contains($savedCat, 'Villa') || str_contains($savedCat, 'Real Estate') || empty($savedCat));

                if ($isMismatchedRoi || empty($prd['business_roi_analysis']) || request()->has('regenerate_roi')) {
                    $businessRoi = \App\Services\PrdGeneratorService::generateBusinessRoiAnalysis(
                        $blueprint, 
                        $prd['itemized_cost_breakdown'] ?? \App\Services\PrdGeneratorService::calculateItemizedEstimation($blueprint)
                    );
                } else {
                    $businessRoi = $prd['business_roi_analysis'];
                }
            @endphp
            <section id="section-1-5" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid prd-scroll-target">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 border-b border-zinc-200 dark:border-zinc-800 pb-4">
                    <div class="flex items-center gap-2 cursor-pointer select-none" @click="toggleSection('section-1-5')">
                        <span class="w-8 h-6 bg-emerald-500 text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">01.5</span>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                <span x-text="locale === 'en' ? 'Business Feasibility, ROI & No-Regret Protections' : 'Analisis Kelayakan Bisnis, Proyeksi ROI & Garansi Bebas Penyesalan'">Analisis Kelayakan Bisnis, Proyeksi ROI &amp; Garansi Bebas Penyesalan</span>
                            </h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-sans mt-0.5" x-text="locale === 'en' ? 'Why this system is a high-yield revenue engine rather than a sunk operational cost.' : 'Alasan matematis mengapa investasi sistem ini menjadi mesin pencetak omzet, bukan biaya modal yang hilang.'">
                                Alasan matematis mengapa investasi sistem ini menjadi mesin pencetak omzet, bukan biaya modal yang hilang.
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto font-mono text-[11px]">
                        @if(!$isScopeLocked)
                        <button 
                            type="button" 
                            @click="recalculateRoi()" 
                            :disabled="roiRecalculating"
                            class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-black font-mono font-bold text-[11px] transition flex items-center gap-1.5 shadow-sm rounded-none cursor-pointer"
                            title="Hitung ulang formula ROI berdasarkan visi dan sektor bisnis terkini"
                        >
                            <svg class="w-3 h-3" :class="roiRecalculating ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span x-text="roiRecalculating ? 'Menghitung...' : 'HITUNG ULANG ROI'">HITUNG ULANG ROI</span>
                        </button>
                        @endif
                        <span class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 font-bold uppercase">
                            {{ $businessRoi['domain_category'] ?? 'Custom Commercial Enterprise' }}
                        </span>
                        <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 font-bold uppercase">
                            {{ $businessRoi['domain_badge'] ?? 'HIGH_TICKET' }}
                        </span>
                        <button 
                            type="button" 
                            @click.stop="toggleSection('section-1-5')" 
                            class="px-2 py-1 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none flex items-center gap-1 font-mono text-xs border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700"
                            :title="sectionCollapses['section-1-5'] ? 'Buka seksi ini' : 'Ciutkan seksi ini'"
                        >
                            <span class="text-[10px] uppercase font-bold text-zinc-400 dark:text-zinc-500 hidden sm:inline" x-text="sectionCollapses['section-1-5'] ? 'BUKA' : 'CIUTKAN'"></span>
                            <svg class="w-4 h-4 transform transition-transform duration-200" :class="sectionCollapses['section-1-5'] ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Collapsed Preview -->
                <div x-show="sectionCollapses['section-1-5']" @click="toggleSection('section-1-5')" class="py-2.5 px-3 bg-zinc-50 dark:bg-zinc-950/60 border border-dashed border-zinc-300 dark:border-zinc-800 text-xs font-mono text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between cursor-pointer transition select-none mb-6">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span x-text="locale === 'en' ? 'Section 01.5 collapsed (Business Viability, ROI Formula & 5 No-Regret Guarantees). Click to expand...' : 'Seksi 01.5 diciutkan (Analisis Kelayakan Bisnis, Formula Balik Modal & 5 Garansi Bebas Penyesalan). Klik untuk membuka...'"></span>
                    </span>
                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500 uppercase font-bold" x-text="locale === 'en' ? '[ + EXPAND ]' : '[ + BUKA ]'">[ + EXPAND ]</span>
                </div>

                <div x-show="!sectionCollapses['section-1-5']" x-transition class="accordion-body">

                <!-- TOP BENCHMARK SUMMARY STRIP -->
                <div class="mb-6 p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs font-mono">
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold" x-text="locale === 'en' ? '● MARKET BENCHMARK:' : '● BENCHMARK PASAR:'">● BENCHMARK PASAR:</span>
                        <span class="text-zinc-700 dark:text-zinc-300 font-semibold">{{ $businessRoi['ticket_benchmark'] ?? 'Transaksi Komersial' }}</span>
                    </div>
                    <div class="text-zinc-500 dark:text-zinc-400 text-[11px]">
                        <span x-text="locale === 'en' ? 'Realistic BEP Target: ' : 'Target BEP Realistis: '">Target BEP Realistis: </span><span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $businessRoi['deadly_roi_equation']['projected_annual_roi'] ?? '250% - 500%' }}</span>
                    </div>
                </div>

                <!-- 2-COLUMNS: KILLER ROI EQUATION vs COST OF INACTION -->
                <div class="grid lg:grid-cols-12 gap-6 mb-8">
                    <!-- LEFT COLUMN (7 COLS): KILLER ROI EQUATION -->
                    <div class="lg:col-span-7 bg-white dark:bg-zinc-950 p-6 border-2 border-emerald-500 rounded-none flex flex-col justify-between shadow-sm">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    <span x-text="locale === 'en' ? 'Deadly Sales ROI Equation' : 'Formula Balik Modal Cepat (Deadly ROI Angle)'">Formula Balik Modal Cepat (Deadly ROI Angle)</span>
                                </span>
                                <span class="text-[10px] font-mono px-2 py-0.5 bg-emerald-500 text-black font-extrabold uppercase">
                                    HIGH-YIELD ASSET
                                </span>
                            </div>

                            <h3 class="text-base sm:text-lg font-black text-zinc-900 dark:text-zinc-100 font-sans leading-snug mb-3">
                                {{ $businessRoi['deadly_roi_equation']['headline'] ?? 'Balik Modal Cepat dari Transaksi Awal' }}
                            </h3>

                            <!-- FORMULA DISPLAY BOX -->
                            <div class="p-4 bg-zinc-50 dark:bg-zinc-900 border border-emerald-500/40 rounded-none font-mono text-xs mb-4 leading-relaxed">
                                <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block mb-1 font-sans uppercase font-bold tracking-wider" x-text="locale === 'en' ? 'Mathematical Transaction Value Calculation:' : 'Perhitungan Matematis Nilai Transaksi:'">Perhitungan Matematis Nilai Transaksi:</span>
                                <div class="font-bold text-zinc-900 dark:text-emerald-300 text-xs sm:text-sm">
                                    {{ $businessRoi['deadly_roi_equation']['formula'] ?? '-' }}
                                </div>
                            </div>

                            <p class="text-zinc-700 dark:text-zinc-300 text-xs sm:text-sm font-sans leading-relaxed mb-4">
                                {{ $businessRoi['deadly_roi_equation']['narrative'] ?? '' }}
                            </p>
                        </div>

                        <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 text-xs font-mono flex items-center justify-between">
                            <span class="text-zinc-500 dark:text-zinc-400" x-text="locale === 'en' ? 'Estimated Year 1 Projected ROI:' : 'Estimasi Proyeksi ROI Tahun 1:'">Estimasi Proyeksi ROI Tahun 1:</span>
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold text-sm sm:text-base">{{ $businessRoi['deadly_roi_equation']['projected_annual_roi'] ?? '450%+' }}</span>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN (5 COLS): COST OF INACTION & WIN-WIN STRATEGY -->
                    <div class="lg:col-span-5 flex flex-col justify-between gap-4">
                        <!-- COST OF INACTION CARD -->
                        <div class="bg-rose-950/15 dark:bg-rose-950/25 p-5 border border-rose-500/40 rounded-none">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-xs font-mono font-bold text-rose-500 dark:text-rose-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <span x-text="locale === 'en' ? 'Cost of Inaction' : 'Biaya Fatal Jika Menunda'">Biaya Fatal Jika Menunda</span>
                                </span>
                                <span class="text-[9px] font-mono px-1.5 py-0.5 bg-rose-500/20 text-rose-400 border border-rose-500/40 font-bold uppercase">
                                    LOST REVENUE
                                </span>
                            </div>

                            <ul class="space-y-2.5 text-xs font-sans text-zinc-700 dark:text-zinc-300 leading-relaxed">
                                @foreach($businessRoi['cost_of_inaction']['risks'] ?? [] as $risk)
                                    <li class="flex items-start gap-2">
                                        <span class="text-rose-500 font-bold shrink-0 mt-0.5">✕</span>
                                        <span>{{ $risk }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- WIN-WIN STRATEGY CARD -->
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-5 border border-zinc-200 dark:border-zinc-800 rounded-none">
                            <span class="text-xs font-mono font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider block mb-2 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ $businessRoi['win_win_strategy']['title'] ?? 'Solusi Menang-Menang (Win-Win Phased Kickoff)' }}</span>
                            </span>
                            <p class="text-xs font-sans text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                {{ $businessRoi['win_win_strategy']['recommendation'] ?? 'Memulai dengan peluncuran modul lean fase 1 agar bisnis segera aktif dan menghasilkan omzet nyata.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 5 NO-REGRET GUARANTEES (NERIAH PRO COMMITMENT) -->
                <div class="pt-6 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                        <div>
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                <span x-text="locale === 'en' ? '5 No-Regret Client Protections (Why You Will Never Regret Choosing Us)' : '5 Garansi Bebas Penyesalan Neriah Pro (Alasan Klien Tidak Pernah Menyesal)'">
                                    5 Garansi Bebas Penyesalan Neriah Pro (Alasan Klien Tidak Pernah Menyesal)
                                </span>
                            </span>
                            <p class="text-[11px] text-zinc-500 font-sans mt-0.5">
                                @if($blueprint->isRetailLicense())
                                    <span x-text="locale === 'en' ? 'Official architecture & perpetual digital license protections applied to every enterprise blueprint from neriahpro.com.' : 'Standar perlindungan lisensi digital & cetak biru arsitektur mandiri resmi neriahpro.com.'">Standar perlindungan lisensi digital &amp; cetak biru arsitektur mandiri resmi neriahpro.com.</span>
                                @else
                                    <span x-text="locale === 'en' ? 'Standard contractual protections applied to every enterprise software delivered by neriahpro.com.' : 'Standar perlindungan kontraktual resmi yang berlaku di setiap sistem yang dibangun neriahpro.com.'">Standar perlindungan kontraktual resmi yang berlaku di setiap sistem yang dibangun neriahpro.com.</span>
                                @endif
                            </p>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 uppercase font-bold self-start sm:self-auto">
                            100% PEACE OF MIND
                        </span>
                    </div>

                    <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        @foreach($businessRoi['why_neriah_pro_guarantees'] ?? [] as $idx => $guarantee)
                            @php
                                $isRetail = $blueprint->isRetailLicense();
                                $gTitle = $guarantee['title'] ?? '';
                                $gDesc = $guarantee['desc'] ?? '';
                                $gBadge = $guarantee['badge'] ?? 'GUARANTEED';

                                if ($isRetail) {
                                    if ($idx === 4 || str_contains($gTitle, '50/50') || str_contains($gDesc, 'DP 50%') || str_contains($gTitle, 'Escrow') || str_contains($gTitle, 'Milestone') || str_contains($gTitle, 'Termin')) {
                                        $gTitle = 'Lisensi Digital 1x Bayar & Bebas Utang Termin (100% Lunas)';
                                        $gDesc = 'Pembelian lisensi kode arsitektur mandiri 1x bayar tanpa cicilan termin DP 50%, tanpa biaya tersembunyi, dan tanpa langganan bulanan. Anda langsung mengunduh seluruh spesifikasi PRD, DDL SQL Strict ULID, Docker scaffold, dan rules AI coding agent secara permanen.';
                                        $gBadge = 'ONE_TIME_LICENSE';
                                    } elseif ($idx === 1 || str_contains($gDesc, 'pengerjaan sprint')) {
                                        $gDesc = 'Seluruh rincian fitur pada PRD ini dikunci secara kriptografis. Spesifikasi arsitektur yang Anda beli bersifat final, terverifikasi integritasnya, dan bebas biaya lisensi siluman.';
                                    } elseif ($idx === 0) {
                                        $gDesc = 'Seluruh hak pakai blueprint arsitektur, DDL database, dan starter code diserahkan penuh kepada Anda. Anda bebas mengembangkannya bersama tim internal tanpa ketergantungan sewa platform tertutup.';
                                    }
                                }
                            @endphp
                            <div class="bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-200 dark:border-zinc-800 rounded-none hover:border-emerald-500/60 transition flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between gap-1 mb-2">
                                        <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                            0{{ $idx + 1 }}
                                        </span>
                                        <span class="text-[9px] font-mono px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-800 font-bold">
                                            {{ $gBadge }}
                                        </span>
                                    </div>
                                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 font-sans mb-1.5 leading-snug">
                                        {{ $gTitle }}
                                    </h4>
                                    <p class="text-[11px] text-zinc-600 dark:text-zinc-400 font-sans leading-relaxed">
                                        {{ $gDesc }}
                                    </p>
                                </div>
                                <div class="mt-3 pt-2 border-t border-zinc-200 dark:border-zinc-800/80 flex items-center gap-1 text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span x-text="locale === 'en' ? '{{ $isRetail ? 'DIGITAL LICENSE GUARANTEED' : 'CONTRACTUALLY GUARANTEED' }}' : '{{ $isRetail ? 'TERJAMIN LISENSI DIGITAL' : 'TERJAMIN KONTRAK' }}'">{{ $isRetail ? 'TERJAMIN LISENSI DIGITAL' : 'TERJAMIN KONTRAK' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                </div>
            </section>

            <!-- SECTION 2: PENGGUNA & HAK AKSES (RBAC) -->
            @php $isActorsImpacted = in_array('system_actors', $prd['_impacted_segments'] ?? []); @endphp
