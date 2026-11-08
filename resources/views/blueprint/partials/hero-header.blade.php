                
                <!-- Document Hero Card -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none relative">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-6 mb-6">
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-wider bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black rounded-none">
                                    SPEC_ID: {{ strtoupper(substr($blueprint->id, 0, 10)) }}
                                </span>
                                <span class="px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-wider bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800 rounded-none">
                                    DISTRIBUTED ULID ARCHITECTURE
                                </span>
                            </div>
                            <h1 class="text-2xl sm:text-4xl font-black uppercase text-zinc-900 dark:text-zinc-100 tracking-tight">
                                {{ $blueprint->nama_bisnis ?: $blueprint->client_name }}
                            </h1>
                            <p class="text-zinc-500 dark:text-zinc-400 text-xs mt-1 font-mono">
                                PREPARED BY NERIAH PRO TECH HUB // SYNCHRONIZED: {{ $blueprint->updated_at?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i') }} UTC
                            </p>
                        </div>

                        <div class="flex flex-col items-start md:items-end gap-1 font-mono text-xs">
                            <span class="text-zinc-400 uppercase tracking-widest">
                                <span x-show="locale === 'en'">CONTRACT STATUS</span>
                                <span x-show="locale !== 'en'">STATUS KONTRAK</span>
                            </span>
                            <span class="px-3 py-1 font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-none">
                                {{ strtoupper($blueprint->project_status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Meta Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs font-mono">
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-3 border border-zinc-200 dark:border-zinc-800 rounded-none">
                            <span class="text-zinc-400 block mb-0.5">
                                <span x-show="locale === 'en'">CLIENT PIC</span>
                                <span x-show="locale !== 'en'">PIC KLIEN</span>
                            </span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100 truncate block">{{ $blueprint->client_name }}</span>
                        </div>
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-3 border border-zinc-200 dark:border-zinc-800 rounded-none">
                            <span class="text-zinc-400 block mb-0.5">
                                <span x-show="locale === 'en'">OFFICIAL EMAIL</span>
                                <span x-show="locale !== 'en'">EMAIL RESMI</span>
                            </span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100 truncate block">{{ $blueprint->email }}</span>
                        </div>
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-3 border border-zinc-200 dark:border-zinc-800 rounded-none">
                            <span class="text-zinc-400 block mb-0.5">
                                <span x-show="locale === 'en'">PROJECT DURATION</span>
                                <span x-show="locale !== 'en'">DURASI PROYEK</span>
                            </span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                <span x-show="locale === 'en'">{{ str_replace(['Hari Kerja', 'hari kerja', 'Hari', 'hari'], ['Business Days', 'business days', 'Days', 'days'], $blueprint->target_waktu ?? '30 Business Days') }}</span>
                                <span x-show="locale !== 'en'">{{ $blueprint->target_waktu ?? '30 Hari Kerja' }}</span>
                            </span>
                        </div>
                        <div class="bg-zinc-50 dark:bg-zinc-950 p-3 border border-zinc-200 dark:border-zinc-800 rounded-none">
                            <span class="text-zinc-400 block mb-0.5">
                                <span x-show="locale === 'en'">ASSET READINESS</span>
                                <span x-show="locale !== 'en'">KESIAPAN ASET</span>
                            </span>
                            @php
                                $rawAset = $blueprint->kesiapan_aset ?? 'Sedang Disiapkan';
                                $asetEn = match(strtolower(trim($rawAset))) {
                                    'lengkap', 'lengkap (logo, copy, brand)' => 'Complete (Logo, Copy, Brand)',
                                    'sebagian', 'sebagian (logo / wireframe dasar)' => 'Partial (Logo / Basic Wireframe)',
                                    'sedang disiapkan', 'sedang disiapkan tim internal' => 'In Preparation by Internal Team',
                                    'belum ada', 'belum ada (mulai dari nol)' => 'None Yet (Start from Scratch)',
                                    default => str_replace(['Sedang Disiapkan', 'Tim Internal', 'Lengkap', 'Sebagian', 'Belum Ada'], ['In Preparation', 'Internal Team', 'Complete', 'Partial', 'None Yet'], $rawAset)
                                };
                            @endphp
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">
                                <span x-show="locale === 'en'">{{ $asetEn }}</span>
                                <span x-show="locale !== 'en'">{{ $rawAset }}</span>
                            </span>
                        </div>
                    </div>

                    @php
                        $retailTierMeta = $blueprint->user_metadata['retail_tier'] ?? $blueprint->user_metadata['package_tier'] ?? null;
                        $selectedVelocityMeta = $blueprint->user_metadata['selected_velocity_tier'] ?? null;
                        
                        $pkgTitle = $isEn ? 'Studio MVP Turnkey Project (Standard Velocity)' : 'Proyek Studio MVP Turnkey (Standard Velocity)';
                        $pkgBadge = 'STUDIO CONTRACT';
                        
                        $sparkPrice = \App\Models\CmsGlobalSetting::getVal('pricing_retail_spark_price', '0');
                        $litePrice = \App\Models\CmsGlobalSetting::getVal('pricing_retail_lite_price', '99.000');
                        $proPrice = \App\Models\CmsGlobalSetting::getVal('pricing_retail_pro_price', '399.000');
                        $ultimatePrice = \App\Models\CmsGlobalSetting::getVal('pricing_retail_ultimate_price', '1.490.000');

                        if ($retailTierMeta) {
                            $cleanRt = strtolower(str_replace('retail_', '', $retailTierMeta));
                            if ($cleanRt === 'spark') {
                                $pkgTitle = $isEn ? "Tier 01: Spark Free Idea Audit (Rp {$sparkPrice} - Initial Validation)" : "Paket 1: Spark Free Idea Audit (Rp {$sparkPrice} - Validasi Ide Awal)";
                                $pkgBadge = 'SPARK FREE AUDIT';
                            } elseif ($cleanRt === 'lite') {
                                $pkgTitle = $isEn ? "Tier 02: Starter AI Architect (Rp {$litePrice} - System Blueprint)" : "Paket 2: Starter AI Architect (Rp {$litePrice} - Cetak Biru Sistem)";
                                $pkgBadge = 'STARTER AI ARCHITECT';
                            } elseif ($cleanRt === 'pro') {
                                $pkgTitle = $isEn ? "Tier 03: Pro Agentic Blueprint (Rp {$proPrice} - Ready to Code)" : "Paket 3: Pro Agentic Blueprint (Rp {$proPrice} - Siap Koding)";
                                $pkgBadge = 'PRO AGENTIC BLUEPRINT';
                            } elseif ($cleanRt === 'ultimate' || $cleanRt === 'enterprise') {
                                $pkgTitle = $isEn ? "Tier 04: Ultimate Architecture OS (Rp {$ultimatePrice} - Complete 10 Pillars)" : "Paket 4: Ultimate Architecture OS (Rp {$ultimatePrice} - Lengkap 10 Pilar)";
                                $pkgBadge = 'ULTIMATE ARCHITECTURE OS';
                            }
                        } elseif ($selectedVelocityMeta) {
                            if ($selectedVelocityMeta === 'fast_track' || str_contains($selectedVelocityMeta, 'fast') || str_contains($selectedVelocityMeta, 'swarm')) {
                                $pkgTitle = $isEn ? 'Studio MVP: Fast-Track Swarm AI Accelerated (14 Business Days)' : 'Studio MVP: Fast-Track Swarm AI Accelerated (14 Hari Kerja)';
                                $pkgBadge = 'SWARM AI ACCELERATED';
                            } elseif ($selectedVelocityMeta === 'emergency') {
                                $pkgTitle = $isEn ? 'Studio MVP: Emergency Concurrency Squad (7 Business Days)' : 'Studio MVP: Emergency Concurrency Squad (7 Hari Kerja)';
                                $pkgBadge = 'EMERGENCY TOP SPEED';
                            }
                        }
                    @endphp
                    <!-- Software Factory OS Package Deliverables Status -->
                    <div class="mt-4 p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-none animate-pulse"></span>
                                <span class="text-zinc-500 dark:text-zinc-400 font-bold uppercase text-[10px]">{{ $isEn ? 'SOFTWARE FACTORY OS PACKAGE & LICENSE:' : 'PAKET & LISENSI SOFTWARE FACTORY OS:' }}</span>
                                <span class="font-black text-sm text-zinc-900 dark:text-zinc-100">{{ $pkgTitle }}</span>
                            </div>
                            <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 text-[10px] font-black uppercase w-fit">
                                {{ $pkgBadge }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-5 lg:grid-cols-10 gap-2 text-[10px]">
                            <div class="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-emerald-500 font-bold block">&check; {{ $isEn ? 'PILLAR 1' : 'PILAR 1' }}</span>
                                <span class="text-zinc-800 dark:text-zinc-200 font-bold">{{ $isEn ? 'PRD & ERD' : 'PRD & ERD' }}</span>
                                <span class="text-[9px] text-zinc-400 block">{{ $isEn ? 'Strict ULID' : 'Skema ULID' }}</span>
                            </div>
                            <div class="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-emerald-500 font-bold block">&check; {{ $isEn ? 'PILLAR 2' : 'PILAR 2' }}</span>
                                <span class="text-zinc-800 dark:text-zinc-200 font-bold">{{ $isEn ? 'UI Tokens' : 'Token UI' }}</span>
                                <span class="text-[9px] text-zinc-400 block">{{ $isEn ? 'Design System' : 'Sistem Desain' }}</span>
                            </div>
                            <div class="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-emerald-500 font-bold block">&check; {{ $isEn ? 'PILLAR 3' : 'PILAR 3' }}</span>
                                <span class="text-zinc-800 dark:text-zinc-200 font-bold">{{ $isEn ? 'Scaffold Code' : 'Rangka Kode' }}</span>
                                <span class="text-[9px] text-zinc-400 block">{{ $isEn ? 'Docker & Routes' : 'Docker & Rute' }}</span>
                            </div>
                            <div class="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-emerald-500 font-bold block">&check; {{ $isEn ? 'PILLAR 4' : 'PILAR 4' }}</span>
                                <span class="text-zinc-800 dark:text-zinc-200 font-bold">{{ $isEn ? 'Mock Seeder' : 'Data Awal' }}</span>
                                <span class="text-[9px] text-zinc-400 block">{{ $isEn ? '100+ Records' : '100+ Data' }}</span>
                            </div>
                            <div class="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-emerald-500 font-bold block">&check; {{ $isEn ? 'PILLAR 5' : 'PILAR 5' }}</span>
                                <span class="text-zinc-800 dark:text-zinc-200 font-bold">{{ $isEn ? 'AI & ECC' : 'AI & ECC' }}</span>
                                <span class="text-[9px] text-zinc-400 block">.cursorrules</span>
                            </div>
                            <div class="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-emerald-500 font-bold block">&check; {{ $isEn ? 'PILLAR 6' : 'PILAR 6' }}</span>
                                <span class="text-zinc-800 dark:text-zinc-200 font-bold">{{ $isEn ? 'Contract & E2E' : 'Uji Kontrak' }}</span>
                                <span class="text-[9px] text-zinc-400 block">Pest/E2E</span>
                            </div>
                            <div class="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-emerald-500 font-bold block">&check; {{ $isEn ? 'PILLAR 7' : 'PILAR 7' }}</span>
                                <span class="text-zinc-800 dark:text-zinc-200 font-bold">{{ $isEn ? 'Cloud CI/CD' : 'CI/CD Cloud' }}</span>
                                <span class="text-[9px] text-zinc-400 block">{{ $isEn ? 'deploy.sh' : 'deploy.sh' }}</span>
                            </div>
                            <div class="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-emerald-500 font-bold block">&check; {{ $isEn ? 'PILLAR 8' : 'PILAR 8' }}</span>
                                <span class="text-zinc-800 dark:text-zinc-200 font-bold">{{ $isEn ? 'Telemetry' : 'Telemetri' }}</span>
                                <span class="text-[9px] text-zinc-400 block">/healthz</span>
                            </div>
                            <div class="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-emerald-500 font-bold block">&check; {{ $isEn ? 'PILLAR 9' : 'PILAR 9' }}</span>
                                <span class="text-zinc-800 dark:text-zinc-200 font-bold">{{ $isEn ? 'Recovery' : 'Pemulihan' }}</span>
                                <span class="text-[9px] text-zinc-400 block">backup.sh</span>
                            </div>
                            <div class="p-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                                <span class="text-emerald-500 font-bold block">&check; {{ $isEn ? 'PILLAR 10' : 'PILAR 10' }}</span>
                                <span class="text-zinc-800 dark:text-zinc-200 font-bold">{{ $isEn ? 'Living Docs' : 'Dokumen API' }}</span>
                                <span class="text-[9px] text-zinc-400 block">Scalar API</span>
                            </div>
                        </div>
                    </div>

                    @php
                        $aiTel = $blueprint->prd_content['meta']['ai_telemetry'] ?? null;
                    @endphp
                    @if($aiTel)
                        @php
                            $isDeterministic = ($aiTel['provider'] ?? '') === 'deterministic_heuristic' || str_contains(strtolower($aiTel['provider_name'] ?? ''), 'deterministic');
                            $hasRealFailover = !empty($aiTel['fallback_occurred']) && !$isDeterministic;
                        @endphp
                        <div class="mt-3 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs font-mono">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="w-2 h-2 bg-emerald-500 rounded-none animate-pulse"></span>
                                <span class="text-zinc-500 dark:text-zinc-400">
                                    <span x-show="locale === 'en'">ARCHITECTURE ENGINE:</span>
                                    <span x-show="locale !== 'en'">ENGINE ARSITEKTUR:</span>
                                </span>
                                <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $aiTel['provider_name'] ?? 'Multi-AI Orchestrator' }}</span>
                                <span class="px-1.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-[10px] text-zinc-700 dark:text-zinc-300 font-bold border border-zinc-300 dark:border-zinc-700">{{ $aiTel['model'] ?? 'Flagship Reasoning' }}</span>
                                @if($hasRealFailover)
                                    <span class="px-1.5 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-bold border border-amber-500/30">
                                        <span x-show="locale === 'en'">FAILOVER ACTIVE</span>
                                        <span x-show="locale !== 'en'">FAILOVER AKTIF</span>
                                    </span>
                                @elseif($isDeterministic)
                                    <span class="px-1.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold border border-emerald-500/30">
                                        <span x-show="locale === 'en'">DETERMINISTIC STANDARD ACTIVE</span>
                                        <span x-show="locale !== 'en'">STANDAR DETERMINISTIK AKTIF</span>
                                    </span>
                                @else
                                    <span class="px-1.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold border border-emerald-500/30">
                                        🟢 <span x-show="locale === 'en'">HEALTHY QUOTA</span><span x-show="locale !== 'en'">KUOTA SEHAT</span>
                                    </span>
                                @endif
                            </div>
                            @if($hasRealFailover && !empty($aiTel['notification']))
                                <span class="text-[11px] text-amber-600 dark:text-amber-400 font-sans italic">
                                    {{ $aiTel['notification'] }}
                                </span>
                            @elseif($isDeterministic)
                                <span class="text-[11px] text-zinc-500 dark:text-zinc-400 font-sans">
                                    <span x-show="locale === 'en'">System operates optimally using industry-standard Deterministic Architecture Engine.</span>
                                    <span x-show="locale !== 'en'">Sistem beroperasi optimal menggunakan Deterministic Architecture Engine berstandar industri.</span>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

            <!-- INTERACTIVE ARCHITECTURE INDEX & ACCORDION TABLE OF CONTENTS (QUICK JUMP) -->
            <div class="bg-white dark:bg-zinc-900 border-2 border-emerald-500/70 p-5 sm:p-7 mb-8 rounded-none shadow-sm relative no-print">
                <!-- Header with Title, Accordion Toggle & Search -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-5 mb-5">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-none animate-pulse"></span>
                            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 text-[10px] font-mono font-bold tracking-widest uppercase">
                                QUICK JUMP DIRECTORY // SCROLL-SPY ACTIVE
                            </span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black uppercase text-zinc-900 dark:text-zinc-100 tracking-tight flex items-center gap-2">
                            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            <span x-show="locale === 'en'">PRD Table of Contents &amp; Architecture Index</span>
                            <span x-show="locale !== 'en'">Daftar Isi PRD &amp; Index Navigasi Arsitektur</span>
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-1">
                            <span x-show="locale === 'en'">Click any section below to jump instantly without tedious scrolling. Live indicator tracks your viewport.</span>
                            <span x-show="locale !== 'en'">Klik blok di bawah untuk langsung berpindah ke spesifikasi yang ingin difokuskan tanpa lelah scrolling. Indikator aktif mengikuti posisi layar.</span>
                        </p>
                    </div>

                    <!-- Search & Accordion Controls -->
                    <div class="flex flex-wrap items-center gap-2 font-mono text-xs">
                        <div class="relative min-w-[200px] flex-1 sm:flex-initial">
                            <input 
                                type="text" 
                                x-model="indexSearchQuery" 
                                :placeholder="locale === 'en' ? 'Search blocks/features/ERD...' : 'Cari blok/fitur/ERD...'"
                                placeholder="{{ $isEn ? 'Search blocks/features/ERD...' : 'Cari blok/fitur/ERD...' }}"
                                class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100 rounded-none focus:outline-none focus:border-emerald-500"
                            />
                            <button 
                                type="button" 
                                x-show="indexSearchQuery" 
                                @click="indexSearchQuery = ''" 
                                class="absolute right-2.5 top-2 text-zinc-400 hover:text-zinc-600 text-xs"
                            >&times;</button>
                        </div>
                        <button 
                            type="button" 
                            @click="expandAllGroups()" 
                            class="px-2.5 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 transition"
                            title="Buka Semua Grup"
                        >
                            <span x-show="locale === 'en'">Expand All</span>
                            <span x-show="locale !== 'en'">Buka Semua</span>
                        </button>
                        <button 
                            type="button" 
                            @click="collapseAllGroups()" 
                            class="px-2.5 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 transition"
                            title="Tutup Semua Grup"
                        >
                            <span x-show="locale === 'en'">Collapse</span>
                            <span x-show="locale !== 'en'">Tutup Semua</span>
                        </button>
                        <button 
                            type="button" 
                            @click="toggleAutoSync()" 
                            :class="autoSyncAccordion ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border-emerald-500' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 border-zinc-300 dark:border-zinc-700'"
                            class="px-2.5 py-1.5 text-xs font-mono font-bold border transition flex items-center gap-1.5"
                            :title="autoSyncAccordion ? 'Auto-Collapse Aktif: Mengikuti scroll bagian PRD' : 'Auto-Collapse Nonaktif: Klik untuk aktifkan mode spy'"
                        >
                            <span class="w-1.5 h-1.5 rounded-none" :class="autoSyncAccordion ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-400'"></span>
                            <span x-show="autoSyncAccordion">SPY AUTO</span>
                            <span x-show="!autoSyncAccordion">STATIC</span>
                        </button>
                        <button 
                            type="button" 
                            @click="indexAccordionOpen = !indexAccordionOpen" 
                            class="px-3 py-1.5 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold border border-zinc-900 dark:border-emerald-500 transition flex items-center gap-1.5"
                        >
                            <span x-show="indexAccordionOpen">&blacktriangle; <span x-show="locale === 'en'">Hide Index</span><span x-show="locale !== 'en'">Tutup Panel</span></span>
                            <span x-show="!indexAccordionOpen">&blacktriangledown; <span x-show="locale === 'en'">Show Index</span><span x-show="locale !== 'en'">Buka Panel</span></span>
                        </button>
                    </div>
                </div>

                <!-- Accordion Body -->
                <div x-show="indexAccordionOpen" x-transition.opacity class="space-y-4">
                    @foreach($accordionGroupDefs as $groupKey => $groupDef)
                        <div class="border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/40 rounded-none overflow-hidden">
                            <!-- Accordion Group Header -->
                            <button 
                                type="button" 
                                @click="toggleAccordionGroup('{{ $groupKey }}')"
                                :class="sectionGroupMap[activeSectionId] === '{{ $groupKey }}' 
                                    ? 'bg-emerald-500/10 dark:bg-emerald-950/40 border-b border-emerald-500/60' 
                                    : 'bg-zinc-100/70 dark:bg-zinc-900/90 hover:bg-zinc-200/60 dark:hover:bg-zinc-800/80'"
                                class="w-full p-3.5 transition flex items-center justify-between text-left font-mono"
                            >
                                <div class="flex items-center gap-2.5">
                                    <span class="w-6 h-6 bg-zinc-800 dark:bg-zinc-800 text-zinc-200 font-bold text-xs flex items-center justify-center rounded-none border border-zinc-700">
                                        {{ $loop->iteration }}
                                    </span>
                                    <div>
                                        <div class="font-bold text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                            <span x-show="locale === 'en'">{{ $groupDef['title_en'] }}</span>
                                            <span x-show="locale !== 'en'">{{ $groupDef['title_id'] }}</span>
                                            <span class="px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-[10px] font-normal border border-zinc-300 dark:border-zinc-700">
                                                {{ $groupDef['count'] }} <span x-show="locale === 'en'">Sections</span><span x-show="locale !== 'en'">Bagian</span>
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-sans mt-0.5 hidden sm:block">
                                            <span x-show="locale === 'en'">{{ $groupDef['desc_en'] }}</span>
                                            <span x-show="locale !== 'en'">{{ $groupDef['desc_id'] }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span x-show="sectionGroupMap[activeSectionId] === '{{ $groupKey }}'" class="px-2 py-0.5 bg-emerald-500 text-black text-[9px] font-mono font-bold uppercase tracking-wider">
                                        ACTIVE
                                    </span>
                                    <svg 
                                        class="w-4 h-4 transition-transform duration-200" 
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

                            <!-- Accordion Items Grid -->
                            <div 
                                x-show="accordionGroups['{{ $groupKey }}'] || indexSearchQuery.trim() !== ''" 
                                x-transition 
                                class="p-3 sm:p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 font-mono text-xs"
                            >
                                @foreach($blueprintSections as $sec)
                                    @if($sec['group'] === $groupKey)
                                        <div 
                                            data-spy-sec="{{ $sec['id'] }}"
                                            x-show="!indexSearchQuery || '{{ strtolower($sec['title_id'] . ' ' . $sec['title_en'] . ' ' . $sec['subtitle_id'] . ' ' . $sec['subtitle_en'] . ' ' . $sec['badge'] . ' ' . $sec['num']) }}'.includes(indexSearchQuery.toLowerCase())"
                                            @click="jumpTo('{{ $sec['id'] }}')"
                                            :class="activeSectionId === '{{ $sec['id'] }}' 
                                                ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400 font-bold shadow-xs' 
                                                : 'border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:border-zinc-400 dark:hover:border-zinc-600'"
                                            class="p-3 border rounded-none cursor-pointer transition flex flex-col justify-between group relative select-none"
                                        >
                                            <div>
                                                <div class="flex items-center justify-between gap-1 mb-1.5">
                                                    <span class="flex items-center gap-1.5">
                                                        <span 
                                                            :class="activeSectionId === '{{ $sec['id'] }}' ? 'bg-emerald-500 text-black' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300'"
                                                            class="w-5 h-5 flex items-center justify-center font-bold text-[10px] rounded-none"
                                                        >
                                                            {{ $sec['num'] }}
                                                        </span>
                                                        <span x-show="activeSectionId === '{{ $sec['id'] }}'" class="flex items-center gap-1 text-[10px] text-emerald-500 font-bold uppercase tracking-wider">
                                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-none animate-ping"></span>
                                                            <span class="hidden sm:inline">ACTIVE</span>
                                                        </span>
                                                    </span>
                                                    <span class="px-1.5 py-0.2 text-[9px] bg-zinc-100 dark:bg-zinc-800/80 text-zinc-500 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700/60 font-bold">
                                                        {{ $sec['badge'] }}
                                                    </span>
                                                </div>
                                                <div class="font-bold text-zinc-900 dark:text-zinc-100 group-hover:text-emerald-500 transition-colors text-xs leading-snug">
                                                    <span x-show="locale === 'en'">{{ $sec['title_en'] }}</span>
                                                    <span x-show="locale !== 'en'">{{ $sec['title_id'] }}</span>
                                                </div>
                                                <p class="text-[10px] text-zinc-500 dark:text-zinc-400 font-sans mt-1 leading-snug line-clamp-2">
                                                    <span x-show="locale === 'en'">{{ $sec['subtitle_en'] }}</span>
                                                    <span x-show="locale !== 'en'">{{ $sec['subtitle_id'] }}</span>
                                                </p>
                                            </div>
                                            <div class="mt-2.5 pt-1.5 border-t border-zinc-100 dark:border-zinc-800/60 flex items-center justify-between text-[10px] text-zinc-400 group-hover:text-emerald-500">
                                                <span><span x-show="locale === 'en'">Jump to section</span><span x-show="locale !== 'en'">Fokuskan blok</span></span>
                                                <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @php
                $targetDays = 30;
                if (preg_match('/(\d+)/', $blueprint->target_waktu ?? '', $m)) {
                    $targetDays = max(5, (int)$m[1]);
                }
                $createdDate = $blueprint->created_at ?? now()->subDays(3);
                $elapsedDays = max(1, (int)$createdDate->diffInDays(now()));
                $remainingDays = max(0, $targetDays - $elapsedDays);
                $progressPercent = min(100, max(5, (int)round(($elapsedDays / $targetDays) * 100)));
                $estimatedFinishDate = $createdDate->copy()->addDays($targetDays);
                
                // 5 Milestone Windows
                $m1End = max(2, (int)round($targetDays * 0.10));
                $m2End = max($m1End + 3, (int)round($targetDays * 0.35));
                $m3End = max($m2End + 4, (int)round($targetDays * 0.70));
                $m4End = max($m3End + 3, (int)round($targetDays * 0.88));
                $m5End = $targetDays;
            @endphp

            <!-- VISUAL SPRINT TIMELINE & PROJECT STATUS TRACKER (CLIENT TRANSPARENCY) -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 mb-8 rounded-none shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-5">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 bg-emerald-500 text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        <div>
                            <h2 class="text-base sm:text-lg font-black uppercase text-zinc-900 dark:text-zinc-100 tracking-tight">
                                <span x-show="locale === 'en'">Project Sprint Timeline &amp; Status Tracker</span>
                                <span x-show="locale !== 'en'">Timeline Pengerjaan &amp; Status Sprint Proyek</span>
                            </h2>
                            <p class="text-zinc-500 dark:text-zinc-400 text-xs font-mono">
                                <span x-show="locale === 'en'">Real-Time Contract Sync: {{ $targetDays }} Business Days // Estimated Handover: {{ $estimatedFinishDate->format('d M Y') }}</span>
                                <span x-show="locale !== 'en'">Sinkronisasi Real-Time Kontrak: {{ $targetDays }} Hari Kerja // Estimasi Serah Terima: {{ $estimatedFinishDate->format('d M Y') }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 font-mono text-xs">
                        <span class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 font-bold">
                            <span x-show="locale === 'en'">Day {{ $elapsedDays }} Of {{ $targetDays }}</span>
                            <span x-show="locale !== 'en'">Hari Ke-{{ $elapsedDays }} Dari {{ $targetDays }}</span>
                        </span>
                        <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 font-bold">
                            {{ $progressPercent }}% PROGRESS
                        </span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-zinc-200 dark:bg-zinc-800 h-3 mb-6 relative overflow-hidden rounded-none">
                    <div class="bg-emerald-500 h-full transition-all duration-500 ease-out" style="width: {{ $progressPercent }}%;"></div>
                </div>

                <!-- 5 Milestone Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs font-mono">
                    <!-- Stage 1 -->
                    @php $s1Done = $elapsedDays >= $m1End; @endphp
                    <div class="p-3.5 border {{ $s1Done ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-500/40 text-emerald-900 dark:text-emerald-300' : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400' }} rounded-none">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] text-zinc-400 uppercase font-bold">
                                <span x-show="locale === 'en'">Stage 1 (Days 1-{{ $m1End }})</span>
                                <span x-show="locale !== 'en'">Tahap 1 (Hari 1-{{ $m1End }})</span>
                            </span>
                            <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase {{ $s1Done ? 'bg-emerald-500 text-black' : 'bg-zinc-300 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-300' }}">
                                <span x-show="locale === 'en'">{{ $s1Done ? 'COMPLETED' : 'ACTIVE' }}</span>
                                <span x-show="locale !== 'en'">{{ $s1Done ? 'SELESAI' : 'AKTIF' }}</span>
                            </span>
                        </div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">Discovery &amp; Scope Lock</div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-snug">
                            <span x-show="locale === 'en'">Business idea questionnaire, PRD spec synthesis, and digital contract sign-off.</span>
                            <span x-show="locale !== 'en'">Kuesioner ide bisnis, sintesis PRD spec, dan persetujuan kontrak digital.</span>
                        </p>
                    </div>

                    <!-- Stage 2 -->
                    @php 
                        $s2Done = $elapsedDays > $m2End; 
                        $s2Active = !$s2Done && $elapsedDays >= $m1End;
                    @endphp
                    <div class="p-3.5 border {{ $s2Done ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-500/40' : ($s2Active ? 'bg-amber-50/60 dark:bg-amber-950/30 border-amber-500/50 text-amber-900 dark:text-amber-200' : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 text-zinc-500') }} rounded-none">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] text-zinc-400 uppercase font-bold">
                                <span x-show="locale === 'en'">Stage 2 (Days {{ $m1End + 1 }}-{{ $m2End }})</span>
                                <span x-show="locale !== 'en'">Tahap 2 (Hari {{ $m1End + 1 }}-{{ $m2End }})</span>
                            </span>
                            <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase {{ $s2Done ? 'bg-emerald-500 text-black' : ($s2Active ? 'bg-amber-500 text-black animate-pulse' : 'bg-zinc-300 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400') }}">
                                <span x-show="locale === 'en'">{{ $s2Done ? 'COMPLETED' : ($s2Active ? 'IN PROGRESS' : 'SCHEDULED') }}</span>
                                <span x-show="locale !== 'en'">{{ $s2Done ? 'SELESAI' : ($s2Active ? 'SEDANG JALAN' : 'TERJADWAL') }}</span>
                            </span>
                        </div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">Database &amp; Core Engine</div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-snug">
                            <span x-show="locale === 'en'">PostgreSQL ULID migrations, Eloquent Models, RBAC Shield &amp; Action Handlers.</span>
                            <span x-show="locale !== 'en'">Migrasi PostgreSQL ULID, Eloquent Model, RBAC Shield &amp; Action Handlers.</span>
                        </p>
                    </div>

                    <!-- Stage 3 -->
                    @php 
                        $s3Done = $elapsedDays > $m3End; 
                        $s3Active = !$s3Done && $elapsedDays > $m2End;
                    @endphp
                    <div class="p-3.5 border {{ $s3Done ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-500/40' : ($s3Active ? 'bg-amber-50/60 dark:bg-amber-950/30 border-amber-500/50 text-amber-900 dark:text-amber-200' : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 text-zinc-500') }} rounded-none">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] text-zinc-400 uppercase font-bold">
                                <span x-show="locale === 'en'">Stage 3 (Days {{ $m2End + 1 }}-{{ $m3End }})</span>
                                <span x-show="locale !== 'en'">Tahap 3 (Hari {{ $m2End + 1 }}-{{ $m3End }})</span>
                            </span>
                            <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase {{ $s3Done ? 'bg-emerald-500 text-black' : ($s3Active ? 'bg-amber-500 text-black animate-pulse' : 'bg-zinc-300 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400') }}">
                                <span x-show="locale === 'en'">{{ $s3Done ? 'COMPLETED' : ($s3Active ? 'IN PROGRESS' : 'SCHEDULED') }}</span>
                                <span x-show="locale !== 'en'">{{ $s3Done ? 'SELESAI' : ($s3Active ? 'SEDANG JALAN' : 'TERJADWAL') }}</span>
                            </span>
                        </div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">Frontend React Islands</div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-snug">
                            <span x-show="locale === 'en'">60fps interactive design, 2-tier multi-language, thousands separator &amp; local fontawesome.</span>
                            <span x-show="locale !== 'en'">Desain interaktif 60fps, 2-tier multi bahasa, format ribuan &amp; fontawesome lokal.</span>
                        </p>
                    </div>

                    <!-- Stage 4 -->
                    @php 
                        $s4Done = $elapsedDays > $m4End; 
                        $s4Active = !$s4Done && $elapsedDays > $m3End;
                    @endphp
                    <div class="p-3.5 border {{ $s4Done ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-500/40' : ($s4Active ? 'bg-amber-50/60 dark:bg-amber-950/30 border-amber-500/50 text-amber-900 dark:text-amber-200' : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 text-zinc-500') }} rounded-none">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] text-zinc-400 uppercase font-bold">
                                <span x-show="locale === 'en'">Stage 4 (Days {{ $m3End + 1 }}-{{ $m4End }})</span>
                                <span x-show="locale !== 'en'">Tahap 4 (Hari {{ $m3End + 1 }}-{{ $m4End }})</span>
                            </span>
                            <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase {{ $s4Done ? 'bg-emerald-500 text-black' : ($s4Active ? 'bg-amber-500 text-black animate-pulse' : 'bg-zinc-300 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400') }}">
                                <span x-show="locale === 'en'">{{ $s4Done ? 'COMPLETED' : ($s4Active ? 'IN PROGRESS' : 'SCHEDULED') }}</span>
                                <span x-show="locale !== 'en'">{{ $s4Done ? 'SELESAI' : ($s4Active ? 'SEDANG JALAN' : 'TERJADWAL') }}</span>
                            </span>
                        </div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">Security &amp; QA Gate</div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-snug">
                            <span x-show="locale === 'en'">Anti-AI malware defense, honeypot traps, automated PHPUnit tests 100% passed.</span>
                            <span x-show="locale !== 'en'">Anti-AI malware defense, honeypot traps, automated PHPUnit tests 100% lulus.</span>
                        </p>
                    </div>

                    <!-- Stage 5 -->
                    @php 
                        $s5Done = $elapsedDays >= $m5End; 
                        $s5Active = !$s5Done && $elapsedDays > $m4End;
                    @endphp
                    <div class="p-3.5 border {{ $s5Done ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-500/40' : ($s5Active ? 'bg-amber-50/60 dark:bg-amber-950/30 border-amber-500/50 text-amber-900 dark:text-amber-200' : 'bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 text-zinc-500') }} rounded-none">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] text-zinc-400 uppercase font-bold">
                                <span x-show="locale === 'en'">Stage 5 (Days {{ $m4End + 1 }}-{{ $m5End }})</span>
                                <span x-show="locale !== 'en'">Tahap 5 (Hari {{ $m4End + 1 }}-{{ $m5End }})</span>
                            </span>
                            <span class="px-1.5 py-0.5 text-[9px] font-bold uppercase {{ $s5Done ? 'bg-emerald-500 text-black' : ($s5Active ? 'bg-amber-500 text-black animate-pulse' : 'bg-zinc-300 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400') }}">
                                <span x-show="locale === 'en'">{{ $s5Done ? 'COMPLETED' : ($s5Active ? 'IN PROGRESS' : 'SCHEDULED') }}</span>
                                <span x-show="locale !== 'en'">{{ $s5Done ? 'SELESAI' : ($s5Active ? 'SEDANG JALAN' : 'TERJADWAL') }}</span>
                            </span>
                        </div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100 text-xs mb-1">Staging &amp; Delivery</div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-snug">
                            <span x-show="locale === 'en'">Client UAT on Staging, production deploy via deploy.sh, credential handover.</span>
                            <span x-show="locale !== 'en'">UAT klien di Staging, deploy production via deploy.sh, serah terima kredensial.</span>
                        </p>
                    </div>
                </div>
                </div>
            </div>

            <!-- DEVELOPER AI EXECUTION COCKPIT & SPRINT PLAYBOOK (HIGH-RETENTION HELPER FOR YOSEPH) -->
            <div class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white border-2 border-emerald-500/60 p-6 sm:p-8 mb-8 rounded-none shadow-sm print-break-inside-avoid">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
                    <div class="flex items-start gap-3">
                        <span class="w-9 h-9 bg-emerald-500 text-black font-mono font-bold text-sm flex items-center justify-center shrink-0 rounded-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[10px] font-mono font-bold tracking-widest uppercase border border-emerald-500/40">
                                    <span x-show="locale === 'en'">DEV PLAYBOOK // DEVELOPER EXCLUSIVE</span>
                                    <span x-show="locale !== 'en'">DEV PLAYBOOK // KHUSUS DEVELOPER</span>
                                </span>
                                <span class="text-[10px] text-zinc-500 dark:text-zinc-400 font-mono hidden sm:inline">
                                    <span x-show="locale === 'en'">HIGH-RETENTION GUIDE</span>
                                    <span x-show="locale !== 'en'">PANDUAN RETENSI TINGGI</span>
                                </span>
                            </div>
                            <h2 class="text-lg sm:text-xl font-black uppercase tracking-tight text-zinc-900 dark:text-white mt-1">
                                <span x-show="locale === 'en'">AI Coding Agent In-IDE Execution Guide (Start to Finish)</span>
                                <span x-show="locale !== 'en'">Panduan Eksekusi AI Coding Agent Dalam IDE (Start to Finish)</span>
                            </h2>
                            <p class="text-zinc-600 dark:text-zinc-400 text-xs font-mono mt-0.5">
                                <span x-show="locale === 'en'">Standard operating procedures for feeding PRD into Cursor / Claude Code / Antigravity with zero hallucination.</span>
                                <span x-show="locale !== 'en'">Prosedur baku mengumpankan PRD ke Cursor / Claude Code / Antigravity agar tepat sasaran tanpa halusinasi.</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="devPlaybookOpen = !devPlaybookOpen" class="px-3 py-1.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-xs font-mono uppercase font-bold border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1.5">
                            <span x-show="locale === 'en'" x-text="devPlaybookOpen ? 'HIDE DETAILS' : 'SHOW GUIDE'"></span>
                            <span x-show="locale !== 'en'" x-text="devPlaybookOpen ? 'SEMBUNYIKAN DETAIL' : 'TAMPILKAN PANDUAN'"></span>
                            <svg class="w-3.5 h-3.5 transition-transform" :class="devPlaybookOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </div>
                </div>

                <div x-show="devPlaybookOpen" x-transition.opacity.duration.200ms class="space-y-6">
                    <!-- Phase Navigation Buttons -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 font-mono text-xs">
                        <button type="button" @click="activeDevPhase = 1" :class="activeDevPhase === 1 ? 'bg-emerald-500 text-black font-bold border-emerald-500' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-750 border-zinc-300 dark:border-zinc-700'" class="p-3 text-left border transition flex items-center justify-between">
                            <span><span x-show="locale === 'en'">1. APP PRODUCTION</span><span x-show="locale !== 'en'">1. PRODUKSI APLIKASI</span></span>
                            <span class="text-[10px] opacity-75">Vertical Slice</span>
                        </button>
                        <button type="button" @click="activeDevPhase = 2" :class="activeDevPhase === 2 ? 'bg-emerald-500 text-black font-bold border-emerald-500' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-750 border-zinc-300 dark:border-zinc-700'" class="p-3 text-left border transition flex items-center justify-between">
                            <span><span x-show="locale === 'en'">2. QUALITY TESTING GATE</span><span x-show="locale !== 'en'">2. QUALITY TESTING GATE</span></span>
                            <span class="text-[10px] opacity-75">Audit &amp; Test</span>
                        </button>
                        <button type="button" @click="activeDevPhase = 3" :class="activeDevPhase === 3 ? 'bg-emerald-500 text-black font-bold border-emerald-500' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-750 border-zinc-300 dark:border-zinc-700'" class="p-3 text-left border transition flex items-center justify-between">
                            <span><span x-show="locale === 'en'">3. DELIVERY &amp; HANDOFF</span><span x-show="locale !== 'en'">3. DELIVERY &amp; HANDOFF</span></span>
                            <span class="text-[10px] opacity-75">Deploy &amp; Scope Lock</span>
                        </button>
                    </div>

                    <!-- Phase 1 Content -->
                    <div x-show="activeDevPhase === 1" class="bg-zinc-50 dark:bg-black/40 border border-zinc-200 dark:border-zinc-800 p-5 space-y-4 font-mono text-xs leading-relaxed">
                        <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-wider text-xs border-b border-zinc-200 dark:border-zinc-800 pb-2">
                            <span>
                                <span x-show="locale === 'en'">Phase 1: Vertical Slice Engineering (Vertical Slice Prompting)</span>
                                <span x-show="locale !== 'en'">Langkah Fase 1: Rekayasa Vertikal (Vertical Slice Prompting)</span>
                            </span>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-2 text-zinc-700 dark:text-zinc-300">
                                <div class="text-zinc-900 dark:text-white font-bold flex items-center gap-1.5">
                                    <span class="text-emerald-600 dark:text-emerald-400">1.1</span>
                                    <span x-show="locale === 'en'">Feature Directive Extraction from PRD</span>
                                    <span x-show="locale !== 'en'">Ekstraksi Directive Fitur dari PRD</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400 text-[11px]">
                                    <span x-show="locale === 'en'">Scroll down to <strong>Section 03 (Detailed Feature Specs)</strong> below. Click <strong>"COPY AGENT PROMPT"</strong> on any feature card. Never feed the entire PRD document in a single giant prompt (prevent <em>context-rot</em>).</span>
                                    <span x-show="locale !== 'en'">Scroll ke <strong>Area 03 (Spesifikasi Rinci Fitur)</strong> di bawah. Klik tombol <strong>"SALIN PROMPT AGENT"</strong> pada salah satu kartu fitur. Jangan pernah memberikan seluruh dokumen PRD dalam satu prompt raksasa (cegah <em>context-rot</em>).</span>
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-700 dark:text-zinc-300">
                                <div class="text-zinc-900 dark:text-white font-bold flex items-center gap-1.5">
                                    <span class="text-emerald-600 dark:text-emerald-400">1.2</span>
                                    <span x-show="locale === 'en'">Strict ULID &amp; JSON Primary Key Standard</span>
                                    <span x-show="locale !== 'en'">Standar Primary Key ULID &amp; JSON</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400 text-[11px]">
                                    <span x-show="locale === 'en'">Instruct the AI to create tables with <code class="text-amber-600 dark:text-amber-300">->ulid('id')->primary()</code> (VARCHAR 26) and trait <code class="text-amber-600 dark:text-amber-300">HasUlids</code>. Use <code class="text-amber-600 dark:text-amber-300">'array'</code> casting for multi-language fields (<code class="text-zinc-700 dark:text-zinc-300">title->id</code>, <code class="text-zinc-700 dark:text-zinc-300">title->en</code>).</span>
                                    <span x-show="locale !== 'en'">Instruksikan AI membuat tabel dengan <code class="text-amber-600 dark:text-amber-300">->ulid('id')->primary()</code> (VARCHAR 26) dan trait <code class="text-amber-600 dark:text-amber-300">HasUlids</code>. Gunakan casting <code class="text-amber-600 dark:text-amber-300">'array'</code> untuk field multi-bahasa (<code class="text-zinc-700 dark:text-zinc-300">title->id</code>, <code class="text-zinc-700 dark:text-zinc-300">title->en</code>).</span>
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-700 dark:text-zinc-300">
                                <div class="text-zinc-900 dark:text-white font-bold flex items-center gap-1.5">
                                    <span class="text-emerald-600 dark:text-emerald-400">1.3</span>
                                    <span x-show="locale === 'en'">Country Zones &amp; Thousands Separator</span>
                                    <span x-show="locale !== 'en'">Country Zone &amp; Pemisah Ribuan</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400 text-[11px]">
                                    <span x-show="locale === 'en'">For phone intake forms, always use <code class="text-emerald-600 dark:text-emerald-300">config('country_zones')</code> selector. For number/currency display >= 1,000, enforce automatic thousands separator (period for ID / comma for EN).</span>
                                    <span x-show="locale !== 'en'">Untuk form intake nomor telepon, wajib gunakan selector <code class="text-emerald-600 dark:text-emerald-300">config('country_zones')</code>. Untuk display angka/uang di atas 1.000, wajib ada pemisah ribuan otomatis (titik format ID / koma format EN).</span>
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-700 dark:text-zinc-300">
                                <div class="text-zinc-900 dark:text-white font-bold flex items-center gap-1.5">
                                    <span class="text-emerald-600 dark:text-emerald-400">1.4</span>
                                    <span x-show="locale === 'en'">Anti-AI-Slop UI/UX &amp; Local Icons</span>
                                    <span x-show="locale !== 'en'">UI/UX Anti-AI-Slop &amp; Icon Lokal</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400 text-[11px]">
                                    <span x-show="locale === 'en'">Border radius must be subtle (<code class="text-emerald-600 dark:text-emerald-300">rounded-none/rounded-sm</code>, strictly no pill <code class="text-rose-600 dark:text-rose-400">rounded-full</code>). Use local FontAwesome SVG via <code class="text-emerald-600 dark:text-emerald-300">\App\Support\FontAwesome::svg('name')</code> with zero CDN latency.</span>
                                    <span x-show="locale !== 'en'">Sudut border wajib tipis (<code class="text-emerald-600 dark:text-emerald-300">rounded-sm/md</code>, dilarang pill <code class="text-rose-600 dark:text-rose-400">rounded-full</code>). Gunakan icon SVG FontAwesome lokal via <code class="text-emerald-600 dark:text-emerald-300">\App\Support\FontAwesome::svg('name')</code> tanpa CDN luar.</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Phase 2 Content -->
                    <div x-show="activeDevPhase === 2" class="bg-zinc-50 dark:bg-black/40 border border-zinc-200 dark:border-zinc-800 p-5 space-y-4 font-mono text-xs leading-relaxed">
                        <div class="flex items-center gap-2 text-amber-600 dark:text-amber-400 font-bold uppercase tracking-wider text-xs border-b border-zinc-200 dark:border-zinc-800 pb-2">
                            <span>
                                <span x-show="locale === 'en'">Phase 2: Quality Testing Gate &amp; Automated Verification</span>
                                <span x-show="locale !== 'en'">Langkah Fase 2: Quality Testing Gate &amp; Otomasi Verifikasi</span>
                            </span>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-2 text-zinc-700 dark:text-zinc-300">
                                <div class="text-zinc-900 dark:text-white font-bold flex items-center gap-1.5">
                                    <span class="text-amber-600 dark:text-amber-400">2.1</span>
                                    <span x-show="locale === 'en'">Unit &amp; Feature Test Execution</span>
                                    <span x-show="locale !== 'en'">Eksekusi Unit &amp; Feature Tests</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400 text-[11px]">
                                    <span x-show="locale === 'en'">Mandate the AI to execute local terminal tests:</span>
                                    <span x-show="locale !== 'en'">Wajibkan AI menjalankan pengujian terminal lokal:</span>
                                    <pre class="bg-zinc-900 dark:bg-black p-2 text-emerald-400 text-[10px] mt-1 select-all border border-zinc-800">php artisan test --filter=[Model]Test</pre>
                                    <span x-show="locale === 'en'">All assertions must pass 100% before moving to the next task.</span>
                                    <span x-show="locale !== 'en'">Seluruh assertion wajib passed 100% sebelum beralih ke tugas berikutnya.</span>
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-700 dark:text-zinc-300">
                                <div class="text-zinc-900 dark:text-white font-bold flex items-center gap-1.5">
                                    <span class="text-amber-600 dark:text-amber-400">2.2</span>
                                    <span x-show="locale === 'en'">Frontend Vite Compilation Gate</span>
                                    <span x-show="locale !== 'en'">Frontend Vite Compilation Gate</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400 text-[11px]">
                                    <span x-show="locale === 'en'">Validate React Islands &amp; Tailwind bundle compilation:</span>
                                    <span x-show="locale !== 'en'">Validasi kompilasi bundle frontend React Islands &amp; Tailwind:</span>
                                    <pre class="bg-zinc-900 dark:bg-black p-2 text-emerald-400 text-[10px] mt-1 select-all border border-zinc-800">npm run build</pre>
                                    <span x-show="locale === 'en'">Ensures zero TypeScript/JSX syntax errors and manifest.json synchronization.</span>
                                    <span x-show="locale !== 'en'">Memastikan tidak ada syntax error TypeScript/JSX dan manifest.json tersinkronisasi.</span>
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-700 dark:text-zinc-300">
                                <div class="text-zinc-900 dark:text-white font-bold flex items-center gap-1.5">
                                    <span class="text-amber-600 dark:text-amber-400">2.3</span>
                                    <span x-show="locale === 'en'">Anti-AI Malware &amp; CSP Audit</span>
                                    <span x-show="locale !== 'en'">Audit Anti-AI Malware &amp; CSP</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400 text-[11px]">
                                    <span x-show="locale === 'en'">Ensure security middleware inspects malicious bots, public form honeypots are active, and CSP allows required internal assets.</span>
                                    <span x-show="locale !== 'en'">Pastikan middleware security memeriksa bot malicious, honeypot fields di form publik aktif, dan Content-Security-Policy tidak memblokir script internal.</span>
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-700 dark:text-zinc-300">
                                <div class="text-zinc-900 dark:text-white font-bold flex items-center gap-1.5">
                                    <span class="text-amber-600 dark:text-amber-400">2.4</span>
                                    <span x-show="locale === 'en'">Strict Ban on Native JS Dialogs</span>
                                    <span x-show="locale !== 'en'">Larangan Dialog JS Native</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400 text-[11px]">
                                    <span x-show="locale === 'en'">Grep codebase to verify zero <code class="text-rose-600 dark:text-rose-400">window.alert</code> or <code class="text-rose-600 dark:text-rose-400">window.confirm</code>. All action feedback must use <code class="text-emerald-600 dark:text-emerald-300">window.showToast</code>.</span>
                                    <span x-show="locale !== 'en'">Grep codebase untuk memastikan <code class="text-rose-600 dark:text-rose-400">window.alert</code> atau <code class="text-rose-600 dark:text-rose-400">window.confirm</code> bernilai 0. Seluruh feedback aksi wajib menggunakan <code class="text-emerald-600 dark:text-emerald-300">window.showToast</code>.</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Phase 3 Content -->
                    <div x-show="activeDevPhase === 3" class="bg-zinc-50 dark:bg-black/40 border border-zinc-200 dark:border-zinc-800 p-5 space-y-4 font-mono text-xs leading-relaxed">
                        <div class="flex items-center gap-2 text-cyan-600 dark:text-cyan-400 font-bold uppercase tracking-wider text-xs border-b border-zinc-200 dark:border-zinc-800 pb-2">
                            <span>
                                <span x-show="locale === 'en'">Phase 3: Deployment, Scope Lock &amp; Client Handover</span>
                                <span x-show="locale !== 'en'">Langkah Fase 3: Deployment, Scope Lock &amp; Serah Terima Klien</span>
                            </span>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-2 text-zinc-700 dark:text-zinc-300">
                                <div class="text-zinc-900 dark:text-white font-bold flex items-center gap-1.5">
                                    <span class="text-cyan-600 dark:text-cyan-400">3.1</span>
                                    <span x-show="locale === 'en'">Git Sync to Official Repository</span>
                                    <span x-show="locale !== 'en'">Git Sync ke Repositori Resmi</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400 text-[11px]">
                                    <span x-show="locale === 'en'">Clean commit and push to origin main:</span>
                                    <span x-show="locale !== 'en'">Commit perubahan bersih dan push ke origin main:</span>
                                    <pre class="bg-zinc-900 dark:bg-black p-2 text-cyan-400 text-[10px] mt-1 select-all border border-zinc-800">git add . ; git commit -m "feat(modul): deskripsi" ; git push origin main</pre>
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-700 dark:text-zinc-300">
                                <div class="text-zinc-900 dark:text-white font-bold flex items-center gap-1.5">
                                    <span class="text-cyan-600 dark:text-cyan-400">3.2</span>
                                    <span x-show="locale === 'en'">Server Deployment Script Execution</span>
                                    <span x-show="locale !== 'en'">Eksekusi Deployment Script Server</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400 text-[11px]">
                                    <span x-show="locale === 'en'">In server SSH terminal (Coolify / VPS), execute the appropriate scenario:</span>
                                    <span x-show="locale !== 'en'">Di terminal SSH server (Coolify / VPS), jalankan nomor skenario yang sesuai:</span>
                                    <pre class="bg-zinc-900 dark:bg-black p-2 text-amber-400 text-[10px] mt-1 select-all border border-zinc-800">./deploy.sh 2   # Skenario 2: Migrasi Aman</pre>
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-700 dark:text-zinc-300">
                                <div class="text-zinc-900 dark:text-white font-bold flex items-center gap-1.5">
                                    <span class="text-cyan-600 dark:text-cyan-400">3.3</span>
                                    <span x-show="locale === 'en'">Digital Contract Scope Lock</span>
                                    <span x-show="locale !== 'en'">Kunci Scope Kontrak Digital</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400 text-[11px]">
                                    <span x-show="locale === 'en'">Switch contract status to <code class="text-emerald-600 dark:text-emerald-400">LOCKED_SIGNED</code> in admin to lock scope and prevent unpaid scope creep.</span>
                                    <span x-show="locale !== 'en'">Ubah status dokumen kontrak digital menjadi <code class="text-emerald-600 dark:text-emerald-400">LOCKED_SIGNED</code> di admin panel untuk mengunci scope fitur agar terhindar dari scope creep yang tidak terbayar.</span>
                                </p>
                            </div>
                            <div class="space-y-2 text-zinc-300">
                                <div class="text-white font-bold flex items-center gap-1.5">
                                    <span class="text-cyan-400">3.4</span>
                                    <span x-show="locale === 'en'">Update Project Sprint Timeline</span>
                                    <span x-show="locale !== 'en'">Update Timeline Sprint Proyek</span>
                                </div>
                                <p class="text-zinc-400 text-[11px]">
                                    <span x-show="locale === 'en'">Update milestone progress on client dashboard so client can transparently monitor daily deliverables.</span>
                                    <span x-show="locale !== 'en'">Perbarui progress milestone pada dashboard klien sehingga klien dapat memantau secara transparan pencapaian fitur harian secara mandiri.</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Master Orchestration Prompt Box -->
                    <div class="pt-2 border-t border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-zinc-400 text-[11px] font-mono">
                            <span x-show="locale === 'en'">⚡ <strong>Master Agentic Prompt</strong>: Want to start sprint from scratch? Copy the full architectural summary into your AI Coding Agent.</span>
                            <span x-show="locale !== 'en'">⚡ <strong>Master Agentic Prompt</strong>: Ingin memulai sprint dari awal? Salin seluruh ringkasan arsitektur sistem proyek ini ke AI Coding Agent Anda.</span>
                        </div>
                        <button type="button" onclick="copyMasterOrchestrationPrompt(this)" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-bold uppercase tracking-wider text-xs font-mono transition shrink-0 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            <span x-show="locale === 'en'">COPY MASTER AI IDE PROMPT</span>
                            <span x-show="locale !== 'en'">SALIN MASTER PROMPT AI IDE</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- EXECUTIVE AI AGENT EXECUTION WIZARD & TIMELINE SYNC BANNER -->
            <div class="mb-8 p-5 bg-gradient-to-r from-zinc-950 via-zinc-900 to-zinc-950 border-2 border-emerald-500 rounded-none shadow-xl text-white font-mono space-y-4 no-print">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-zinc-800 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-3 h-3 bg-emerald-500 animate-pulse"></span>
                        <div>
                            <span class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider block">
                                <span x-show="locale === 'en'">AI AGENT EXECUTION WIZARD // ROADMAP &amp; CHECKPOINT ENGINE</span>
                                <span x-show="locale !== 'en'">AI AGENT EXECUTION WIZARD // ROADMAP &amp; CHECKPOINT ENGINE</span>
                            </span>
                            <h3 class="text-sm sm:text-base font-black uppercase text-white">
                                <span x-show="locale === 'en'">Guided AI Coding Agent Execution Playbook (Cursor / Claude Code / Antigravity IDE)</span>
                                <span x-show="locale !== 'en'">Panduan Terpandu Eksekusi AI Coding Agent (Cursor / Claude Code / Antigravity IDE)</span>
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-zinc-400">
                            <span x-show="locale === 'en'">Sprint Progress:</span>
                            <span x-show="locale !== 'en'">Progres Sprint:</span>
                        </span>
                        <span class="px-2 py-0.5 bg-emerald-500 text-black font-black text-xs">
                            <span x-text="getDevProgressPercentage()"></span>% <span x-show="locale === 'en'">Completed</span><span x-show="locale !== 'en'">Selesai</span>
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                    <div class="p-3 bg-zinc-900/90 border border-zinc-800 space-y-1">
                        <span class="text-emerald-400 font-bold block text-[11px]">
                            <span x-show="locale === 'en'">1. NO PROMPT DUMPING:</span>
                            <span x-show="locale !== 'en'">1. JANGAN PROMPT DUMPING:</span>
                        </span>
                        <p class="text-[11px] text-zinc-400 font-sans leading-relaxed">
                            <span x-show="locale === 'en'">Never copy thousands of PRD lines at once into AI chat. Execute per vertical slice so AI doesn't forget context or cut business logic.</span>
                            <span x-show="locale !== 'en'">Jangan salin ribuan baris PRD sekaligus ke AI chat. Eksekusi per fitur vertikal (Vertical Slice) agar AI tidak amnesia kode atau memotong logika.</span>
                        </p>
                    </div>
                    <div class="p-3 bg-zinc-900/90 border border-zinc-800 space-y-1">
                        <span class="text-cyan-400 font-bold block text-[11px]">
                            <span x-show="locale === 'en'">2. DEFINITION OF DONE (DoD):</span>
                            <span x-show="locale !== 'en'">2. TOLAK UKUR MUTU (DoD):</span>
                        </span>
                        <p class="text-[11px] text-zinc-400 font-sans leading-relaxed">
                            <span x-show="locale === 'en'">Execute terminal verification commands (e.g. <code>php artisan migrate:status</code> &amp; <code>php artisan test</code>) before marking checkpoints complete.</span>
                            <span x-show="locale !== 'en'">Jalankan perintah verifikasi terminal (misal: <code>php artisan migrate:status</code> &amp; <code>php artisan test</code>) sebelum menandai checkpoint selesai.</span>
                        </p>
                    </div>
                    <div class="p-3 bg-zinc-900/90 border border-zinc-800 space-y-1">
                        <span class="text-amber-400 font-bold block text-[11px]">
                            <span x-show="locale === 'en'">3. DASHBOARD SYNC:</span>
                            <span x-show="locale !== 'en'">3. SINKRONISASI DASHBOARD:</span>
                        </span>
                        <p class="text-[11px] text-zinc-400 font-sans leading-relaxed">
                            <span x-show="locale === 'en'">Each checked step automatically persists to server database and updates sprint progress on customer dashboard live.</span>
                            <span x-show="locale !== 'en'">Tiap tahap yang dicentang otomatis tersimpan ke server database dan mengupdate timeline progres pengerjaan di dashboard pelanggan secara live.</span>
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-zinc-800">
                    <div class="text-[11px] text-zinc-400">
                        <span x-show="locale === 'en'">⚡ Battle-tested execution order: <strong>Global Foundation (DB ULID) &rarr; MVP Features 01..N &rarr; Quality Gate &amp; Deploy</strong></span>
                        <span x-show="locale !== 'en'">⚡ Urutan eksekusi teruji: <strong>Fondasi Global (DB ULID) &rarr; Fitur MVP 01..N &rarr; Quality Gate &amp; Deploy</strong></span>
                    </div>
                    <a href="#section-3-5" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-black font-bold uppercase text-xs flex items-center gap-1.5 transition">
                        <span x-show="locale === 'en'">OPEN WIZARD COCKPIT &amp; CHECKPOINT SPRINT &rarr;</span>
                        <span x-show="locale !== 'en'">BUKA WIZARD COCKPIT &amp; CHECKPOINT SPRINT &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- GLOBAL DOCUMENT SECTION ACCORDION CONTROLS (EXPAND ALL / COLLAPSE ALL / REGENERATE) -->
            <div class="mb-8 p-4 sm:p-5 bg-white dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-none no-print">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 bg-emerald-500 rounded-none animate-pulse"></span>
                    <div>
                        <div class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <span>
                                <span x-show="locale === 'en'">SYSTEM ARCHITECTURE &amp; PRD SPECIFICATION</span>
                                <span x-show="locale !== 'en'">DOKUMEN SPESIFIKASI ARSITEKTUR &amp; PRD SISTEM</span>
                            </span>
                            <span class="text-[10px] font-mono px-2 py-0.5 bg-emerald-100 text-emerald-950 dark:bg-emerald-950/80 dark:text-emerald-200 border border-emerald-400 dark:border-emerald-700 font-black">
                                <span x-show="locale === 'en'">13 SECTIONS</span>
                                <span x-show="locale !== 'en'">13 SEKSI</span>
                            </span>
                        </div>
                        <div class="text-xs text-zinc-600 dark:text-zinc-400 font-sans mt-0.5">
                            <span x-show="locale === 'en'">Each section below includes an independent <strong>Accordion</strong> to toggle open/closed for comfortable reading without being overwhelming.</span>
                            <span x-show="locale !== 'en'">Setiap seksi di bawah dilengkapi fitur <strong>Accordion</strong> untuk dibuka-tutup secara mandiri agar nyaman dibaca dan tidak overwhelming.</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-2 font-mono text-xs shrink-0">
                    <button 
                        type="button" 
                        @click="expandAllSections()" 
                        class="px-3 py-1.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 font-bold transition flex items-center gap-1.5 cursor-pointer"
                        :title="locale === 'en' ? 'Expand all 13 architecture sections' : 'Buka seluruh 13 seksi arsitektur'"
                    >
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        <span>
                            <span x-show="locale === 'en'">EXPAND ALL</span>
                            <span x-show="locale !== 'en'">BUKA SEMUA (EXPAND)</span>
                        </span>
                    </button>
                    <button 
                        type="button" 
                        @click="collapseAllSections()" 
                        class="px-3 py-1.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 font-bold transition flex items-center gap-1.5 cursor-pointer"
                        :title="locale === 'en' ? 'Collapse all sections into titles & badges' : 'Ciutkan seluruh seksi menjadi ringkasan judul & badge'"
                    >
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                        <span>
                            <span x-show="locale === 'en'">COLLAPSE ALL</span>
                            <span x-show="locale !== 'en'">TUTUP SEMUA (COLLAPSE)</span>
                        </span>
                    </button>
                    @if(!($isLocked ?? false))
                    <button 
                        type="button" 
                        @click="regenerateFullBlueprint()" 
                        :disabled="fullRegenerating"
                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-black transition flex items-center gap-1.5 cursor-pointer shadow-xs"
                        :title="locale === 'en' ? 'Re-synthesize casual business ideas into world-class architecture spec and diagrams' : 'Crafting ulang ide casual menjadi spesifikasi blueprint profesional dan generate ulang diagram visual'"
                    >
                        <svg class="w-3.5 h-3.5" :class="fullRegenerating ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span x-show="locale === 'en'" x-text="fullRegenerating ? 'SYNTHESIZING...' : '🔄 REGENERATE PRD &amp; ARCHITECTURE'">🔄 REGENERATE PRD &amp; ARCHITECTURE</span>
                        <span x-show="locale !== 'en'" x-text="fullRegenerating ? 'MENSINTESIS...' : '🔄 REGENERATE PRD &amp; ARSITEKTUR'">🔄 REGENERATE PRD &amp; ARSITEKTUR</span>
                    </button>
                    @endif
                </div>
            </div>

            <!-- SECTION 1: EXECUTIVE TECHNICAL DISCOVERY -->
            @php $isExecutiveImpacted = in_array('executive_summary', $prd['_impacted_segments'] ?? []); @endphp
