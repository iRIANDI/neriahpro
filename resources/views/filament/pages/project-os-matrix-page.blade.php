<x-filament-panels::page>
    @vite(['resources/css/app.css'])

    @php
        $data = $this->matrixData;
        $stats = $data['stats'] ?? [];
        $items = $data['items'] ?? [];
        $nextCursor = $data['next_cursor'] ?? null;
        $prevCursor = $data['prev_cursor'] ?? null;
        $padClass = match ($this->density) {
            'compact' => 'py-1.5 px-3 text-xs',
            'relaxed' => 'py-4 px-5 text-sm',
            default => 'py-3 px-4 text-xs',
        };
    @endphp

    <div class="space-y-5 font-sans text-zinc-950 dark:text-zinc-100">
        <!-- KPI Summary Strip (5-Col Grid, Tabular Nums, Crisp 1px Borders, Zero-Slop) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="p-4 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-sm">
                <div class="text-[11px] font-mono font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-400">Domain-Driven Taxonomies</div>
                <div class="mt-1 flex items-baseline justify-between">
                    <span class="text-2xl font-black font-mono tabular-nums text-zinc-950 dark:text-zinc-100">{{ number_format($stats['sectors'] ?? 12, 0, ',', '.') }}</span>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black rounded-xs">SEKTOR FUNGSIONAL</span>
                </div>
            </div>
            <div class="p-4 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-sm">
                <div class="text-[11px] font-mono font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-400">Composable Building Blocks</div>
                <div class="mt-1 flex items-baseline justify-between">
                    <span class="text-2xl font-black font-mono tabular-nums text-zinc-950 dark:text-zinc-100">{{ number_format($stats['primitives'] ?? 12, 0, ',', '.') }}</span>
                    <span class="px-2 py-0.5 bg-zinc-100 text-zinc-950 border border-zinc-400 dark:bg-zinc-900 dark:text-zinc-200 dark:border-zinc-700 font-mono text-[10px] font-black rounded-xs">7-DIM PRIMITIVES</span>
                </div>
            </div>
            <div class="p-4 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-sm">
                <div class="text-[11px] font-mono font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-400">Curated Stack Adapters</div>
                <div class="mt-1 flex items-baseline justify-between">
                    <span class="text-2xl font-black font-mono tabular-nums text-zinc-950 dark:text-zinc-100">{{ number_format($stats['stacks'] ?? 9, 0, ',', '.') }}</span>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black rounded-xs">PRESETS M1–S2</span>
                </div>
            </div>
            <div class="p-4 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-sm">
                <div class="text-[11px] font-mono font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-400">Zero-Assumption Guardrails</div>
                <div class="mt-1 flex items-baseline justify-between">
                    <span class="text-2xl font-black font-mono tabular-nums text-zinc-950 dark:text-zinc-100">{{ number_format($stats['rules'] ?? 6, 0, ',', '.') }}</span>
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700 font-mono text-[10px] font-black rounded-xs">ANTI-SLOP PACKS</span>
                </div>
            </div>
            <div class="p-4 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-sm">
                <div class="text-[11px] font-mono font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-400">Assembled Blueprints</div>
                <div class="mt-1 flex items-baseline justify-between">
                    <span class="text-2xl font-black font-mono tabular-nums text-zinc-950 dark:text-zinc-100">{{ number_format($stats['projects'] ?? 0, 0, ',', '.') }}</span>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black rounded-xs">STRICT ULID</span>
                </div>
            </div>
        </div>

        <!-- Tabs + Toolbar (Search with Instant Clear 'X', Grid/List Switcher, Row Density, Export CSV/JSON, Seeder Sync) -->
        <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-sm space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                <div class="flex flex-wrap items-center gap-1.5">
                    @foreach([
                        'sectors' => ['label' => '12 Sektor UI/UX', 'count' => $stats['sectors'] ?? 12],
                        'primitives' => ['label' => '12 Core Primitives', 'count' => $stats['primitives'] ?? 12],
                        'stacks' => ['label' => '9 Stack Presets (M1–S2)', 'count' => $stats['stacks'] ?? 9],
                        'rules' => ['label' => 'Anti-Slop Rule Packs', 'count' => $stats['rules'] ?? 6],
                        'projects' => ['label' => 'Assembled Projects', 'count' => $stats['projects'] ?? 0],
                        'day2' => ['label' => '8 Layer Day-2 & Doctor', 'count' => 'v3.0'],
                        'lifecycle' => ['label' => 'Day-30+ Lifecycle & MCP', 'count' => 'v4.0'],
                        'mitra' => ['label' => 'Mitra Bridge & Satu Pintu', 'count' => 'v5.0'],
                        'lab' => ['label' => 'Brownfield & Slop Lab', 'count' => 'LAB'],
                    ] as $tabKey => $tabMeta)
                        <button
                            type="button"
                            wire:click="setTab('{{ $tabKey }}')"
                            class="px-3 py-1.5 text-xs font-mono font-bold rounded-xs border transition flex items-center gap-2 cursor-pointer {{ $this->activeTab === $tabKey ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-500 dark:text-black dark:border-emerald-400' : 'bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-200 border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                        >
                            <span>{{ $tabMeta['label'] }}</span>
                            <span class="px-1.5 py-0.5 text-[10px] font-black tabular-nums rounded-xs {{ $this->activeTab === $tabKey ? 'bg-white text-black dark:bg-black dark:text-white' : 'bg-zinc-200 text-zinc-950 dark:bg-zinc-800 dark:text-zinc-200' }}">
                                {{ $tabMeta['count'] }}
                            </span>
                        </button>
                    @endforeach
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('blueprint.studio') }}" target="_blank" class="px-3 py-1.5 text-xs font-mono font-black bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 border border-zinc-800 dark:border-zinc-300 rounded-xs transition">
                        🖥️ Buka 3-Pane Studio
                    </a>
                    <button type="button" wire:click="syncMatrixSeeder" class="px-3 py-1.5 text-xs font-mono font-bold bg-emerald-600 hover:bg-emerald-500 text-black border border-emerald-700 rounded-xs transition cursor-pointer">
                        Sinkronkan Seeder O(1)
                    </button>
                    <button type="button" wire:click="exportCsv" class="px-2.5 py-1.5 text-xs font-mono font-bold bg-zinc-100 dark:bg-zinc-900 hover:bg-zinc-200 dark:hover:bg-zinc-800 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs transition cursor-pointer">
                        CSV
                    </button>
                    <button type="button" wire:click="exportJson" class="px-2.5 py-1.5 text-xs font-mono font-bold bg-zinc-100 dark:bg-zinc-900 hover:bg-zinc-200 dark:hover:bg-zinc-800 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs transition cursor-pointer">
                        JSON
                    </button>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <!-- Search Input with Mandatory Instant Clear ('X') Button -->
                <div class="relative flex-1 max-w-md">
                    <input
                        type="text"
                        wire:model.live.debounce.250ms="search"
                        placeholder="Cari sektor, kode primitive, tabel ULID, atau aturan..."
                        class="w-full pl-3 pr-9 py-1.5 text-xs font-mono bg-white dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"
                    />
                    @if($this->search !== '')
                        <button
                            type="button"
                            wire:click="resetSearch"
                            title="Hapus pencarian"
                            class="absolute right-2 top-1/2 -translate-y-1/2 px-1.5 py-0.5 text-xs font-mono font-black bg-zinc-200 dark:bg-zinc-800 text-zinc-950 dark:text-zinc-100 hover:bg-zinc-300 dark:hover:bg-zinc-700 rounded-xs cursor-pointer"
                        >
                            ✕
                        </button>
                    @endif
                </div>

                <!-- Dual View (Grid / List) + Row Density Switcher -->
                <div class="flex flex-wrap items-center gap-2">
                    @if($this->viewMode === 'list')
                        <div class="inline-flex border border-zinc-300 dark:border-zinc-700 rounded-xs overflow-hidden">
                            @foreach(['compact' => 'Compact', 'normal' => 'Normal', 'relaxed' => 'Relaxed'] as $dKey => $dLabel)
                                <button
                                    type="button"
                                    wire:click="setDensity('{{ $dKey }}')"
                                    class="px-2.5 py-1 text-[11px] font-mono font-bold cursor-pointer {{ $this->density === $dKey ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950' : 'bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                                >
                                    {{ $dLabel }}
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <div class="inline-flex border border-zinc-300 dark:border-zinc-700 rounded-xs overflow-hidden">
                        <button
                            type="button"
                            wire:click="setViewMode('grid')"
                            class="px-3 py-1 text-xs font-mono font-bold cursor-pointer {{ $this->viewMode === 'grid' ? 'bg-zinc-900 text-white dark:bg-emerald-500 dark:text-black' : 'bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                        >
                            GRID
                        </button>
                        <button
                            type="button"
                            wire:click="setViewMode('list')"
                            class="px-3 py-1 text-xs font-mono font-bold cursor-pointer {{ $this->viewMode === 'list' ? 'bg-zinc-900 text-white dark:bg-emerald-500 dark:text-black' : 'bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                        >
                            LIST TABLE
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area: Day-2 Ops & Blueprint Doctor vs Brownfield & Slop Lab vs Grid/List -->
        @if($this->activeTab === 'day2')
            @php
                $eightLayers = \App\Support\PosDayTwoProductionCatalog::finalEightLayersSummary();
                $tenPillars = \App\Support\PosDayTwoProductionCatalog::tenPerfectionPillars();
                $sectorOptions = \App\Support\ProjectOsMatrixCatalog::sectors();
                $stackOptions = \App\Support\PosStackPresetCatalog::stackPresets();
            @endphp
            <div class="space-y-4">
                <!-- Interactive Layer 8: Blueprint Doctor Pre-Flight Conflict Resolver -->
                <div class="p-4 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-sm space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-2.5">
                        <div>
                            <div class="text-[10px] font-mono font-black uppercase text-emerald-900 dark:text-emerald-300">LAYER 08 // INTERACTIVE PRE-FLIGHT CONFLICT RESOLVER</div>
                            <h3 class="text-sm font-black text-zinc-950 dark:text-zinc-100">Blueprint Doctor — Deteksi Celah Lintas Modul &amp; Auto-Fix 1-Klik</h3>
                        </div>
                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black rounded-xs">
                            PROJECT OS v3.0
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] font-mono font-bold uppercase text-zinc-700 dark:text-zinc-400 mb-1">Sektor Fungsional</label>
                            <select wire:model="doctorSectorCode" class="w-full px-3 py-1.5 text-xs font-mono bg-white dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs">
                                @foreach($sectorOptions as $sOpt)
                                    <option value="{{ $sOpt['code'] }}">{{ $sOpt['code'] }} — {{ $sOpt['name']['id'] ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-mono font-bold uppercase text-zinc-700 dark:text-zinc-400 mb-1">Preset Topologi &amp; Stack</label>
                            <select wire:model="doctorStackCode" class="w-full px-3 py-1.5 text-xs font-mono bg-white dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs">
                                @foreach($stackOptions as $stOpt)
                                    <option value="{{ $stOpt['code'] }}">[{{ $stOpt['short_code'] }}] {{ $stOpt['name']['id'] ?? $stOpt['code'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-mono font-bold uppercase text-zinc-700 dark:text-zinc-400 mb-1">Target Durasi (Hari)</label>
                            <input type="number" min="7" max="365" wire:model="doctorTimelineDays" class="w-full px-3 py-1.5 text-xs font-mono tabular-nums bg-white dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs" />
                        </div>
                    </div>

                    <textarea rows="2" wire:model="doctorWorkflow" placeholder="Ketik deskripsi alur kerja bisnis (mis. resep apotek, stok FEFO, jurnal kas, approval yayasan)..." class="w-full p-2.5 text-xs font-mono bg-zinc-50 dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs"></textarea>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" wire:click="runBlueprintDoctorLab" class="px-4 py-2 text-xs font-mono font-black bg-emerald-600 hover:bg-emerald-500 text-black border border-emerald-700 rounded-xs cursor-pointer">
                            🩺 JALANKAN BLUEPRINT DOCTOR PRE-FLIGHT
                        </button>
                        @if(!empty($this->doctorResult['one_click_auto_fix']['has_fixes']))
                            <button type="button" wire:click="applyBlueprintDoctorAutoFix" class="px-4 py-2 text-xs font-mono font-black bg-amber-400 hover:bg-amber-300 text-black border border-amber-600 rounded-xs cursor-pointer">
                                ⚡ TERAPKAN AUTO-FIX 1-KLIK ({{ count($this->doctorResult['one_click_auto_fix']['add_primitive_codes'] ?? []) }} MODUL)
                            </button>
                        @endif
                    </div>

                    @if(!empty($this->doctorResult))
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xs space-y-2">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="font-mono text-xs font-black text-zinc-950 dark:text-zinc-100 tabular-nums">
                                    {{ $this->doctorResult['summary_banner']['id'] ?? '' }}
                                </span>
                                <span class="px-2 py-0.5 font-mono text-[10px] font-black rounded-xs border {{ ($this->doctorResult['status'] ?? '') === 'OPTIMAL_READY' ? 'bg-emerald-100 text-emerald-950 border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700' : 'bg-amber-100 text-amber-950 border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700' }}">
                                    {{ $this->doctorResult['status'] ?? 'READY' }}
                                </span>
                            </div>
                            @foreach(($this->doctorResult['findings'] ?? []) as $f)
                                <div class="p-2.5 bg-white dark:bg-zinc-950 border border-amber-400 dark:border-amber-700 rounded-xs text-xs">
                                    <div class="font-mono font-black text-amber-950 dark:text-amber-200">[{{ $f['code'] }}] {{ $f['title']['id'] ?? '' }}</div>
                                    <div class="text-zinc-700 dark:text-zinc-300 mt-0.5">{{ $f['message']['id'] ?? '' }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Grid of 8 Day-2 Production Layers -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                    @foreach($eightLayers as $layer)
                        <div class="p-3.5 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 rounded-sm space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black rounded-xs">
                                    {{ $layer['badge'] }}
                                </span>
                                <span class="font-mono text-[10px] font-bold text-zinc-600 dark:text-zinc-400 tabular-nums">L-0{{ $layer['number'] }}</span>
                            </div>
                            <div class="text-xs font-black text-zinc-950 dark:text-zinc-100">{{ $layer['title']['id'] }}</div>
                            <p class="text-[11px] text-zinc-700 dark:text-zinc-300 leading-relaxed">{{ $layer['desc']['id'] }}</p>
                        </div>
                    @endforeach
                </div>

                <!-- 10 Ultimate Perfection Pillars Matrix -->
                <div class="p-4 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-sm">
                    <div class="text-xs font-mono font-black uppercase text-zinc-950 dark:text-zinc-100 mb-3">
                        Peta Kesempurnaan 10 Pilar Ultimate Blueprint neriahpro.com
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5">
                        @foreach($tenPillars as $tp)
                            <div class="p-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs">
                                <div class="font-mono text-[11px] font-black text-emerald-900 dark:text-emerald-300 tabular-nums">{{ $tp['num'] }} // {{ $tp['title'] }}</div>
                                <div class="text-[11px] text-zinc-700 dark:text-zinc-300 mt-1 leading-snug">{{ $tp['detail'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif($this->activeTab === 'lifecycle')
            @php
                $fiveLifecycle = \App\Support\PosLifecycleAndStudioCatalog::fiveLifecycleFeaturesSummary();
                $sectorList = \App\Support\ProjectOsMatrixCatalog::sectors();
                $activeDecisionMatrix = \App\Support\PosLifecycleAndStudioCatalog::decisionTablesForSector($this->decisionPreviewSector);
                $compiledMatchPhp = \App\Support\PosLifecycleAndStudioCatalog::compileDecisionTablesToPhpClass($activeDecisionMatrix);
                $activeEnvVault = \App\Support\PosLifecycleAndStudioCatalog::environmentVaultBlueprint($this->decisionPreviewSector);
                $renderedEnvFile = \App\Support\PosLifecycleAndStudioCatalog::renderEnvBlueprintFile($activeEnvVault);
            @endphp
            <div class="space-y-4">
                <!-- 5 Day-30 to Day-365 Lifecycle Retention Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
                    @foreach($fiveLifecycle as $lf)
                        <div class="p-4 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-emerald-600 rounded-sm space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black rounded-xs">
                                    {{ $lf['badge'] }}
                                </span>
                                <span class="font-mono text-[10px] font-bold text-zinc-600 dark:text-zinc-400 tabular-nums">F-0{{ $lf['number'] }}</span>
                            </div>
                            <div class="text-xs font-black text-zinc-950 dark:text-zinc-100">{{ $lf['title']['id'] ?? '' }}</div>
                            <p class="text-[11px] text-zinc-700 dark:text-zinc-300 leading-relaxed">{{ $lf['desc']['id'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>

                <!-- Interactive Sector Decision Matrix (match()) + .env.blueprint Inspector -->
                <div class="p-4 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-sm space-y-4">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                        <div>
                            <div class="text-[10px] font-mono font-black uppercase text-emerald-900 dark:text-emerald-300">
                                DETERMINISTIC DECISION MATRIX COMPILER &amp; .ENV.BLUEPRINT VAULT
                            </div>
                            <h3 class="text-sm font-black text-zinc-950 dark:text-zinc-100">
                                {{ $activeDecisionMatrix['matrix_name'] ?? 'Sectoral Truth-Table → PHP 8.4+ match()' }}
                            </h3>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <select wire:model.live="decisionPreviewSector" class="px-3 py-1.5 text-xs font-mono bg-zinc-50 dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs">
                                @foreach($sectorList as $sItem)
                                    <option value="{{ $sItem['code'] }}">{{ $sItem['code'] }} — {{ $sItem['name']['id'] ?? '' }}</option>
                                @endforeach
                            </select>
                            <a href="{{ route('blueprint.studio') }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-black font-mono text-xs font-black border border-emerald-700 rounded-xs">
                                🖥️ Edit di 3-Pane Studio
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-800">
                        <table class="w-full text-left border-collapse font-mono text-xs">
                            <thead class="bg-zinc-100 dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border-b border-zinc-300 dark:border-zinc-800 uppercase text-[10px]">
                                <tr>
                                    <th class="py-2 px-3">Rule ID</th>
                                    <th class="py-2 px-3">Condition A</th>
                                    <th class="py-2 px-3">Condition B</th>
                                    <th class="py-2 px-3">Condition C</th>
                                    <th class="py-2 px-3">Action (`match()`)</th>
                                    <th class="py-2 px-3">Exact Formula `DECIMAL(19,4)`</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                @foreach(($activeDecisionMatrix['rules'] ?? []) as $rRow)
                                    <tr class="odd:bg-white dark:odd:bg-zinc-950 even:bg-zinc-50/70 dark:even:bg-zinc-900/40">
                                        <td class="py-2 px-3 font-black text-emerald-900 dark:text-emerald-300">{{ $rRow['rule_id'] }}</td>
                                        <td class="py-2 px-3 text-zinc-900 dark:text-zinc-200">{{ $rRow['condition_a'] }}</td>
                                        <td class="py-2 px-3 text-zinc-900 dark:text-zinc-200">{{ $rRow['condition_b'] }}</td>
                                        <td class="py-2 px-3 text-zinc-900 dark:text-zinc-200">{{ $rRow['condition_c'] }}</td>
                                        <td class="py-2 px-3 font-bold text-zinc-950 dark:text-zinc-100">{{ $rRow['action_code'] }}</td>
                                        <td class="py-2 px-3 text-emerald-950 dark:text-emerald-200">{{ $rRow['exact_formula'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <div class="font-mono text-[10px] font-black uppercase text-zinc-800 dark:text-zinc-300">
                                Compiled PHP 8.4+ Class (`DeterministicDecisionMatrix.php` — Zero Nested If-Else):
                            </div>
                            <pre class="p-3 bg-zinc-950 text-emerald-300 border border-zinc-800 font-mono text-[11px] max-h-56 overflow-auto whitespace-pre-wrap">{{ $compiledMatchPhp }}</pre>
                        </div>
                        <div class="space-y-1.5">
                            <div class="font-mono text-[10px] font-black uppercase text-zinc-800 dark:text-zinc-300">
                                Sectoral `.env.blueprint` + `php artisan pos:check-env`:
                            </div>
                            <pre class="p-3 bg-zinc-950 text-amber-200 border border-zinc-800 font-mono text-[11px] max-h-56 overflow-auto whitespace-pre-wrap">{{ $renderedEnvFile }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($this->activeTab === 'mitra')
            @php
                $fiveCommercial = \App\Services\PosCommercialBridgeService::fiveCommercialBridgeFeatures();
                $satuPintuCatalog = \App\Support\PricingRegistry::getSatuPintuCatalog();
                $sectorListMitra = \App\Support\ProjectOsMatrixCatalog::sectors();
                $stackListMitra = \App\Support\PosStackPresetCatalog::stackPresets();
                $selectedSecMeta = collect($sectorListMitra)->firstWhere('code', $this->decisionPreviewSector) ?: ($sectorListMitra[0] ?? []);
                $previewSchema = \App\Services\ProjectOsBlueprintCompilerService::compileSchemaFromPayload([
                    'name' => 'Simulasi Mitra (' . ($selectedSecMeta['name']['id'] ?? $this->decisionPreviewSector) . ')',
                    'sector_code' => $this->decisionPreviewSector,
                    'primitive_codes' => $selectedSecMeta['mandatory_primitives'] ?? ['PRIM_IAM_MULTI_TENANT', 'PRIM_AUDIT_SECURITY_LEDGER'],
                    'stack_preset_code' => $this->mitraPreviewStack,
                ]);
                $liveQuotation = app(\App\Services\PosCommercialBridgeService::class)->calculateScopeQuotation($previewSchema);
            @endphp
            <div class="space-y-4">
                <!-- 5 Commercial-to-Code Bridge Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
                    @foreach($fiveCommercial as $cf)
                        <div class="p-4 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 hover:border-amber-500 rounded-sm space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700 font-mono text-[10px] font-black rounded-xs">
                                    {{ $cf['badge'] }}
                                </span>
                                <span class="font-mono text-[10px] font-bold text-zinc-600 dark:text-zinc-400 tabular-nums">M-0{{ $cf['number'] }}</span>
                            </div>
                            <div class="text-xs font-black text-zinc-950 dark:text-zinc-100">{{ $cf['title']['id'] ?? '' }}</div>
                            <p class="text-[11px] text-zinc-700 dark:text-zinc-300 leading-relaxed">{{ $cf['summary']['id'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>

                <!-- Live Algorithmic RAB & WBS Simulator (Satu Pintu Sync) -->
                <div class="p-4 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-sm space-y-4">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                        <div>
                            <div class="text-[10px] font-mono font-black uppercase text-amber-900 dark:text-amber-300">
                                ALGORITHMIC SCOPE-TO-QUOTATION SIMULATOR (TERSINKRONISASI KE MANAGESETTINGS)
                            </div>
                            <h3 class="text-sm font-black text-zinc-950 dark:text-zinc-100">
                                Kalkulasi RAB Deterministik per Sektor &amp; Stack Preset (Anti-Underpricing)
                            </h3>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <select wire:model.live="decisionPreviewSector" class="px-3 py-1.5 text-xs font-mono bg-zinc-50 dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs">
                                @foreach($sectorListMitra as $sItem)
                                    <option value="{{ $sItem['code'] }}">{{ $sItem['code'] }} — {{ $sItem['name']['id'] ?? '' }}</option>
                                @endforeach
                            </select>
                            <select wire:model.live="mitraPreviewStack" class="px-3 py-1.5 text-xs font-mono bg-zinc-50 dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs">
                                @foreach($stackListMitra as $stItem)
                                    <option value="{{ $stItem['code'] }}">[{{ $stItem['short_code'] }}] {{ $stItem['name']['id'] ?? $stItem['code'] }}</option>
                                @endforeach
                            </select>
                            <a href="{{ url('/blueprint/studio?mode=mitra&sector=' . $this->decisionPreviewSector . '&stack=' . $this->mitraPreviewStack) }}" target="_blank" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-black font-mono text-xs font-black border border-amber-700 rounded-xs">
                                🤝 Buka Mode Mitra di Studio
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 font-mono">
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs">
                            <div class="text-[10px] font-bold uppercase text-zinc-600 dark:text-zinc-400">Beban Kompleksitas</div>
                            <div class="text-sm font-black text-zinc-950 dark:text-zinc-100 tabular-nums mt-1">
                                {{ $liveQuotation['metrics']['tables_count'] ?? 0 }} Tabel • {{ $liveQuotation['metrics']['transitions_count'] ?? 0 }} Transisi • {{ $liveQuotation['metrics']['concurrency_locks_count'] ?? 0 }} Lock
                            </div>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs">
                            <div class="text-[10px] font-bold uppercase text-zinc-600 dark:text-zinc-400">Total Effort × Pengali Stack</div>
                            <div class="text-sm font-black text-zinc-950 dark:text-zinc-100 tabular-nums mt-1">
                                {{ number_format((int) ($liveQuotation['total_man_hours'] ?? 0), 0, ',', '.') }} Jam ({{ $liveQuotation['estimated_working_days'] ?? 0 }} Hari) × {{ $liveQuotation['stack_multiplier'] ?? 1 }}x
                            </div>
                        </div>
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-400 dark:border-emerald-700 rounded-xs">
                            <div class="text-[10px] font-bold uppercase text-emerald-900 dark:text-emerald-300">RAB Turnkey Wajar</div>
                            <div class="text-base font-black text-emerald-950 dark:text-emerald-200 tabular-nums mt-1">
                                {{ $liveQuotation['formatted_total_quotation_idr'] ?? 'Rp 0' }}
                            </div>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs">
                            <div class="text-[10px] font-bold uppercase text-zinc-600 dark:text-zinc-400">SHA-256 Scope Hash</div>
                            <div class="text-xs font-black text-zinc-950 dark:text-zinc-100 mt-1 truncate" title="{{ $liveQuotation['scope_hash_sha256'] ?? '' }}">
                                {{ substr((string) ($liveQuotation['scope_hash_sha256'] ?? ''), 0, 22) }}…
                            </div>
                        </div>
                    </div>

                    <!-- Satu Pintu Retail vs Mitra Table -->
                    <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-800">
                        <table class="w-full text-left border-collapse font-mono text-xs">
                            <thead class="bg-zinc-100 dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border-b border-zinc-300 dark:border-zinc-800 uppercase text-[10px]">
                                <tr>
                                    <th class="py-2 px-3">Paket Satu Pintu</th>
                                    <th class="py-2 px-3">Jalur Eksekusi</th>
                                    <th class="py-2 px-3">Harga Aktif</th>
                                    <th class="py-2 px-3">Cakupan 12 Sektor &amp; Stack</th>
                                    <th class="py-2 px-3">Mesin v5.0</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                @foreach($satuPintuCatalog as $pCode => $pRow)
                                    <tr class="odd:bg-white dark:odd:bg-zinc-950 even:bg-zinc-50/70 dark:even:bg-zinc-900/40">
                                        <td class="py-2 px-3 font-black text-zinc-950 dark:text-zinc-100">{{ $pRow['name_id'] ?? $pCode }}</td>
                                        <td class="py-2 px-3">
                                            <span class="px-2 py-0.5 text-[10px] font-black rounded-xs border {{ ($pRow['track_mode'] ?? '') === 'MITRA_TURNKEY_PROJECT' ? 'bg-amber-100 text-amber-950 border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700' : 'bg-emerald-100 text-emerald-950 border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700' }}">
                                                {{ $pRow['track_mode'] ?? 'RETAIL_SELF_DEV' }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-3 font-black tabular-nums text-emerald-900 dark:text-emerald-300">{{ $pRow['formatted_price'] ?? '-' }}</td>
                                        <td class="py-2 px-3 text-zinc-800 dark:text-zinc-300">{{ $pRow['sector_scope'] ?? '-' }} ({{ $pRow['stack_scope'] ?? '-' }})</td>
                                        <td class="py-2 px-3 text-zinc-700 dark:text-zinc-400">{{ implode(' • ', array_slice($pRow['included_v5_engines'] ?? [], 0, 2)) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @elseif($this->activeTab === 'lab')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Left Card: Killer #4 Brownfield SQL / Migration Importer -->
                <div class="p-4 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-sm space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-2.5">
                        <div>
                            <div class="text-[10px] font-mono font-black uppercase text-emerald-900 dark:text-emerald-300">KILLER ENGINE #4 // ZERO-DATA-LOSS REFACTOR</div>
                            <h3 class="text-sm font-black text-zinc-950 dark:text-zinc-100">Brownfield SQL / Migration / Prisma Importer</h3>
                        </div>
                        <button type="button" wire:click="loadSampleBrownfieldSql" class="px-2.5 py-1 text-[11px] font-mono font-bold bg-zinc-100 dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded-xs cursor-pointer">
                            Muat Contoh SQL Lama
                        </button>
                    </div>

                    <input
                        type="text"
                        wire:model="brownfieldHint"
                        placeholder="Konteks bisnis singkat (opsional, misal: Sistem Kasir & Apotek Klinik)"
                        class="w-full px-3 py-1.5 text-xs font-mono bg-white dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs"
                    />

                    <textarea
                        rows="8"
                        wire:model="brownfieldSql"
                        placeholder="Tempelkan CREATE TABLE SQL, Laravel Migration, atau schema.prisma di sini..."
                        class="w-full p-3 text-xs font-mono bg-zinc-50 dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"
                    ></textarea>

                    <button type="button" wire:click="runBrownfieldAudit" class="w-full py-2 text-xs font-mono font-black bg-emerald-600 hover:bg-emerald-500 text-black border border-emerald-700 rounded-xs cursor-pointer">
                        AUDIT SCHEMA &amp; SUSUN MIGRASI SHADOW ULID
                    </button>

                    @if(!empty($this->brownfieldResult['success']))
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xs space-y-2.5">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="font-mono text-xs font-black text-zinc-950 dark:text-zinc-100 tabular-nums">
                                    Readiness Score: {{ $this->brownfieldResult['readiness_score'] }}/100 ({{ $this->brownfieldResult['grade'] }})
                                </span>
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black rounded-xs">
                                    {{ $this->brownfieldResult['recommended_sector']['code'] ?? '' }} → {{ $this->brownfieldResult['recommended_sector']['primary_ui_archetype'] ?? '' }}
                                </span>
                            </div>
                            <div class="space-y-1">
                                @foreach(($this->brownfieldResult['violations'] ?? []) as $v)
                                    <div class="p-2 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xs text-[11px]">
                                        <span class="font-mono font-black text-amber-900 dark:text-amber-300">[{{ $v['code'] }}]</span>
                                        <strong class="text-zinc-950 dark:text-zinc-100">{{ $v['title'] }}</strong>
                                        <div class="text-zinc-700 dark:text-zinc-300 mt-0.5">{{ $v['remediation'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                            <div>
                                <div class="text-[10px] font-mono font-black uppercase text-zinc-800 dark:text-zinc-300 mb-1">AI Zero-Data-Loss Refactor Prompt:</div>
                                <pre class="p-2.5 bg-zinc-950 text-zinc-100 text-[11px] font-mono overflow-x-auto max-h-48 border border-zinc-800 rounded-xs">{{ $this->brownfieldResult['ai_refactor_prompt'] ?? '' }}</pre>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Card: Killer #5 Slop & Drift Code Scanner -->
                <div class="p-4 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-sm space-y-3">
                    <div class="border-b border-zinc-200 dark:border-zinc-800 pb-2.5">
                        <div class="text-[10px] font-mono font-black uppercase text-amber-900 dark:text-amber-300">KILLER ENGINE #5 // ZERO-SLOP GATEKEEPER</div>
                        <h3 class="text-sm font-black text-zinc-950 dark:text-zinc-100">Blueprint Drift &amp; Code Slop Scanner (CLI: php artisan pos:audit)</h3>
                    </div>

                    <textarea
                        rows="9"
                        wire:model="slopSnippet"
                        placeholder="Tempelkan kode Blade / React JSX / Migration untuk memindai pelanggaran rounded-full, bg-gradient-to-*, alert(), CDN eksternal, $table->id(), atau ->paginate()..."
                        class="w-full p-3 text-xs font-mono bg-zinc-50 dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"
                    ></textarea>

                    <button type="button" wire:click="runSlopCodeScan" class="w-full py-2 text-xs font-mono font-black bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 border border-zinc-800 dark:border-zinc-300 rounded-xs cursor-pointer">
                        PINDAI KEPATUHAN ANTI-SLOP &amp; STRICT ULID
                    </button>

                    @if(!empty($this->slopResult['success']))
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xs space-y-2">
                            <div class="font-mono text-xs font-black text-zinc-950 dark:text-zinc-100 tabular-nums">
                                Compliance Score: {{ $this->slopResult['compliance_score'] }}/100 ({{ $this->slopResult['violations_count'] }} Temuan)
                            </div>
                            <pre class="p-2.5 bg-zinc-950 text-zinc-100 text-[11px] font-mono overflow-x-auto max-h-48 border border-zinc-800 rounded-xs">{{ $this->slopResult['auto_fix_prompt'] ?? '' }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        @elseif(empty($items))
            <div class="p-8 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-sm text-center">
                <div class="font-mono text-sm font-bold text-zinc-900 dark:text-zinc-100">Tidak ada data yang cocok</div>
                <div class="text-xs text-zinc-600 dark:text-zinc-400 mt-1">Ubah kata kunci pencarian atau buat blueprint baru di halaman Studio `/blueprint`.</div>
            </div>
        @elseif($this->viewMode === 'grid')
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($items as $item)
                    <div class="p-4 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 hover:ring-1 hover:ring-emerald-500/60 rounded-sm transition flex flex-col justify-between gap-3">
                        @if($this->activeTab === 'sectors')
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black rounded-xs tabular-nums">
                                        SEKTOR #{{ str_pad((string) ($item['sector_number'] ?? 0), 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <label class="inline-flex items-center gap-1.5 text-[11px] font-mono cursor-pointer">
                                        <input type="checkbox" wire:model.live="selectedIds" value="{{ $item['id'] }}" class="rounded-none border-zinc-400" />
                                        <span class="text-zinc-700 dark:text-zinc-400">Pilih</span>
                                    </label>
                                </div>
                                <div class="text-sm font-black text-zinc-950 dark:text-zinc-100">{{ $item['name'] }}</div>
                                <div class="text-[11px] font-mono font-bold text-emerald-900 dark:text-emerald-300 mt-0.5">{{ $item['code'] }}</div>
                                <p class="text-xs text-zinc-700 dark:text-zinc-300 mt-2 leading-relaxed">{{ $item['description'] }}</p>

                                @if(!empty($item['primary_archetype']))
                                    <div class="mt-3 p-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs space-y-1.5">
                                        <div class="text-[10px] font-mono font-black uppercase text-emerald-900 dark:text-emerald-300">
                                            Sectoral Industrial UI/UX Archetype
                                        </div>
                                        <div class="flex flex-wrap gap-1">
                                            <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black rounded-xs">
                                                {{ $item['primary_archetype'] }}
                                            </span>
                                            @if(!empty($item['secondary_archetype']))
                                                <span class="px-1.5 py-0.5 bg-zinc-200 text-zinc-950 border border-zinc-400 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 font-mono text-[10px] font-bold rounded-xs">
                                                    {{ $item['secondary_archetype'] }}
                                                </span>
                                            @endif
                                        </div>
                                        @if(!empty($item['wireframe_panes']))
                                            <div class="grid grid-cols-1 gap-1 pt-1">
                                                @foreach(array_slice($item['wireframe_panes'], 0, 3) as $pane)
                                                    <div class="px-2 py-1 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 font-mono text-[10px] text-zinc-900 dark:text-zinc-200">
                                                        <strong>[{{ $pane['span'] ?? 'PANE' }}]</strong> {{ $pane['title'] ?? '' }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="text-[10px] font-mono text-zinc-700 dark:text-zinc-400">
                                            Seeder: <strong class="text-zinc-950 dark:text-zinc-200">{{ $item['seeder_class'] ?? 'RealisticSectorSeeder' }}</strong>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-3">
                                    <div class="text-[10px] font-mono font-black uppercase text-zinc-700 dark:text-zinc-400 mb-1">Mandatory Primitives</div>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($item['mandatory_primitives'] as $mp)
                                            <span class="px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-900 text-zinc-950 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 font-mono text-[10px] font-bold rounded-xs">{{ $mp }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-zinc-200 dark:border-zinc-800 flex flex-wrap gap-1">
                                @foreach($item['compliance'] as $std)
                                    <span class="px-1.5 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700 font-mono text-[10px] font-black rounded-xs">{{ $std }}</span>
                                @endforeach
                            </div>
                        @elseif($this->activeTab === 'primitives')
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="px-2 py-0.5 bg-zinc-100 text-zinc-950 border border-zinc-400 dark:bg-zinc-900 dark:text-zinc-200 dark:border-zinc-700 font-mono text-[10px] font-black rounded-xs">
                                        {{ $item['category'] }}
                                    </span>
                                    <input type="checkbox" wire:model.live="selectedIds" value="{{ $item['id'] }}" class="rounded-none border-zinc-400" />
                                </div>
                                <div class="text-sm font-black text-zinc-950 dark:text-zinc-100">{{ $item['name'] }}</div>
                                <div class="text-[11px] font-mono font-bold text-emerald-900 dark:text-emerald-300 mt-0.5">{{ $item['code'] }}</div>
                                <p class="text-xs text-zinc-700 dark:text-zinc-300 mt-2 leading-relaxed">{{ $item['description'] }}</p>

                                <div class="mt-3">
                                    <div class="text-[10px] font-mono font-black uppercase text-zinc-700 dark:text-zinc-400 mb-1">ULID Tables</div>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($item['tables'] as $tbl)
                                            <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black rounded-xs">{{ $tbl }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-zinc-200 dark:border-zinc-800 space-y-1 text-[11px] font-mono text-zinc-800 dark:text-zinc-300 tabular-nums">
                                <div>State Machines: {{ implode(', ', $item['state_machines']) ?: '-' }}</div>
                                <div class="text-[10px] text-emerald-900 dark:text-emerald-300 font-bold">
                                    7-Dim: {{ $item['formulas_count'] ?? 1 }} Formulas • {{ $item['sagas_count'] ?? 1 }} Sagas • {{ $item['archetypes_count'] ?? 1 }} UI Archetypes • {{ $item['acceptance_count'] ?? 2 }} Tests
                                </div>
                            </div>
                        @elseif($this->activeTab === 'stacks')
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 font-mono text-[10px] font-black rounded-xs">
                                            {{ $item['short_code'] }}
                                        </span>
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black rounded-xs">
                                            {{ $item['topology'] }}
                                        </span>
                                        @if(!empty($item['is_default']))
                                            <span class="px-1.5 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700 font-mono text-[9px] font-black rounded-xs">DEFAULT</span>
                                        @endif
                                    </div>
                                    <input type="checkbox" wire:model.live="selectedIds" value="{{ $item['id'] }}" class="rounded-none border-zinc-400" />
                                </div>
                                <div class="text-sm font-black text-zinc-950 dark:text-zinc-100">{{ $item['name'] }}</div>
                                <div class="text-[11px] font-mono font-bold text-emerald-900 dark:text-emerald-300 mt-0.5">{{ $item['code'] }}</div>
                                <p class="text-xs text-zinc-700 dark:text-zinc-300 mt-2 leading-relaxed">{{ $item['description'] }}</p>
                            </div>
                            <div class="pt-2 border-t border-zinc-200 dark:border-zinc-800 space-y-1 text-[11px] font-mono text-zinc-800 dark:text-zinc-300">
                                <div><strong class="text-zinc-950 dark:text-zinc-100">Type Contract:</strong> {{ $item['type_contract'] }}</div>
                                <div class="text-[10px] text-zinc-600 dark:text-zinc-400">Team Scale: {{ $item['team_scale'] }}</div>
                            </div>
                        @elseif($this->activeTab === 'rules')
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700 font-mono text-[10px] font-black rounded-xs">
                                        LAYER: {{ $item['layer'] }}
                                    </span>
                                    <input type="checkbox" wire:model.live="selectedIds" value="{{ $item['id'] }}" class="rounded-none border-zinc-400" />
                                </div>
                                <div class="text-sm font-black text-zinc-950 dark:text-zinc-100">{{ $item['name'] }}</div>
                                <div class="text-[11px] font-mono text-zinc-700 dark:text-zinc-400 mb-2">{{ $item['slug'] }}</div>
                                <ul class="space-y-1 text-xs text-zinc-800 dark:text-zinc-300 list-disc pl-4">
                                    @foreach(array_slice($item['directives'], 0, 5) as $dir)
                                        <li>{{ $dir }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black rounded-xs">
                                        {{ $item['archetype'] }}
                                    </span>
                                    <span class="text-[11px] font-mono text-zinc-700 dark:text-zinc-400 tabular-nums">{{ $item['updated_at'] }}</span>
                                </div>
                                <div class="text-sm font-black text-zinc-950 dark:text-zinc-100">{{ $item['app_name'] }}</div>
                                <div class="text-[11px] font-mono font-bold text-emerald-900 dark:text-emerald-300 mt-0.5">{{ $item['sector_code'] }}</div>
                                <div class="text-xs font-mono text-zinc-800 dark:text-zinc-300 mt-2 tabular-nums">
                                    {{ $item['primitives_count'] }} Core Primitives • {{ $item['phases_count'] ?? 6 }} Phased Prompts
                                </div>
                                @if(!empty($item['cli_init_command']))
                                    <div class="mt-2 p-2 bg-zinc-950 text-emerald-300 border border-zinc-800 font-mono text-[10px] overflow-x-auto rounded-xs">
                                        {{ $item['cli_init_command'] }}
                                    </div>
                                @endif
                            </div>
                            @if(!empty($item['blueprint_slug']))
                                <div class="pt-2 border-t border-zinc-200 dark:border-zinc-800 flex items-center gap-2">
                                    <a href="{{ route('blueprint.show', $item['blueprint_slug']) }}" target="_blank" class="px-2.5 py-1 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono text-[11px] font-black rounded-xs">
                                        BUKA PRD
                                    </a>
                                    <a href="{{ route('blueprint.download-schema-json', $item['blueprint_slug']) }}" class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 font-mono text-[11px] font-bold rounded-xs">
                                        SCHEMA.JSON
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <!-- Enterprise Sticky Header + Freeze Columns + Zebra + Tri-State Sort Table -->
            <div class="overflow-x-auto max-h-[620px] border border-zinc-200 dark:border-zinc-800 rounded-sm bg-white dark:bg-zinc-950">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 z-20 bg-zinc-100/95 dark:bg-zinc-900/95 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800 font-mono text-[11px] uppercase text-zinc-900 dark:text-zinc-100">
                        <tr>
                            <th class="sticky left-0 z-30 bg-zinc-100 dark:bg-zinc-900 {{ $padClass }} border-r border-zinc-200 dark:border-zinc-800 w-10">✓</th>
                            <th wire:click="cycleSort('code')" class="{{ $padClass }} cursor-pointer select-none hover:bg-zinc-200/70 dark:hover:bg-zinc-800">
                                Kode / Identifier
                                @if($this->sortBy === 'code')
                                    <span>{{ $this->sortDirection === 'asc' ? '↑' : ($this->sortDirection === 'desc' ? '↓' : '') }}</span>
                                @endif
                            </th>
                            <th class="{{ $padClass }}">Nama &amp; Deskripsi</th>
                            <th class="{{ $padClass }}">Kontrak Teknis / Primitives</th>
                            <th class="sticky right-0 z-30 bg-zinc-100 dark:bg-zinc-900 {{ $padClass }} border-l border-zinc-200 dark:border-zinc-800 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach($items as $item)
                            <tr class="odd:bg-white dark:odd:bg-zinc-950 even:bg-zinc-50/60 dark:even:bg-zinc-900/40 hover:bg-zinc-100/90 dark:hover:bg-zinc-800/80 transition">
                                <td class="sticky left-0 z-10 bg-inherit {{ $padClass }} border-r border-zinc-200 dark:border-zinc-800">
                                    <input type="checkbox" wire:model.live="selectedIds" value="{{ $item['id'] }}" class="rounded-none border-zinc-400" />
                                </td>
                                <td class="{{ $padClass }} font-mono font-black text-zinc-950 dark:text-zinc-100 hover:ring-1 hover:ring-inset hover:ring-emerald-500/60">
                                    {{ $item['code'] ?? $item['slug'] ?? $item['app_name'] ?? $item['id'] }}
                                </td>
                                <td class="{{ $padClass }} hover:ring-1 hover:ring-inset hover:ring-emerald-500/60">
                                    <div class="font-bold text-zinc-950 dark:text-zinc-100">{{ $item['name'] ?? $item['app_name'] ?? '-' }}</div>
                                    @if(!empty($item['description']))
                                        <div class="text-zinc-700 dark:text-zinc-400 mt-0.5 line-clamp-2">{{ $item['description'] }}</div>
                                    @endif
                                </td>
                                <td class="{{ $padClass }} font-mono hover:ring-1 hover:ring-inset hover:ring-emerald-500/60">
                                    @if($this->activeTab === 'sectors')
                                        {{ implode(', ', $item['mandatory_primitives'] ?? []) }}
                                    @elseif($this->activeTab === 'primitives')
                                        Tables: {{ implode(', ', $item['tables'] ?? []) }} ({{ $item['acceptance_count'] ?? 2 }} acceptance specs)
                                    @elseif($this->activeTab === 'stacks')
                                        [{{ $item['short_code'] ?? '' }}] {{ $item['topology'] ?? '' }} • {{ $item['type_contract'] ?? '-' }}
                                    @elseif($this->activeTab === 'rules')
                                        Layer: {{ $item['layer'] ?? '-' }} ({{ count($item['directives'] ?? []) }} directives)
                                    @else
                                        {{ $item['sector_code'] ?? '-' }} • {{ $item['primitives_count'] ?? 0 }} primitives
                                    @endif
                                </td>
                                <td class="sticky right-0 z-10 bg-inherit {{ $padClass }} border-l border-zinc-200 dark:border-zinc-800 text-right font-mono">
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 text-[10px] font-black rounded-xs">
                                        ULID READY
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- O(1) Keyset Cursor Pagination Footer -->
        @if($prevCursor || $nextCursor)
            <div class="flex items-center justify-between p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-sm">
                <div class="text-xs font-mono text-zinc-700 dark:text-zinc-400">Keyset Cursor Pagination O(1) — Zero Offset Scan</div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        wire:click="prevPage('{{ $prevCursor }}')"
                        @disabled(!$prevCursor)
                        class="px-3 py-1 text-xs font-mono font-bold border border-zinc-300 dark:border-zinc-700 rounded-xs disabled:opacity-40 cursor-pointer"
                    >
                        ← Sebelumnya
                    </button>
                    <button
                        type="button"
                        wire:click="nextPage('{{ $nextCursor }}')"
                        @disabled(!$nextCursor)
                        class="px-3 py-1 text-xs font-mono font-bold border border-zinc-300 dark:border-zinc-700 rounded-xs disabled:opacity-40 cursor-pointer"
                    >
                        Berikutnya →
                    </button>
                </div>
            </div>
        @endif

        <!-- Floating Batch Action Bar when 1+ rows selected -->
        @if(!empty($this->selectedIds))
            <div class="sticky bottom-4 z-40 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-950 px-4 py-2.5 rounded-xs border border-zinc-700 dark:border-zinc-300 flex flex-wrap items-center justify-between gap-3">
                <div class="text-xs font-mono font-black tabular-nums">
                    {{ count($this->selectedIds) }} ITEM TERPILIH
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="exportCsv" class="px-3 py-1 text-xs font-mono font-black bg-emerald-500 text-black rounded-xs cursor-pointer">
                        Ekspor CSV Terpilih
                    </button>
                    <button type="button" wire:click="exportJson" class="px-3 py-1 text-xs font-mono font-black bg-white text-black dark:bg-zinc-900 dark:text-white rounded-xs cursor-pointer">
                        Ekspor JSON Terpilih
                    </button>
                    <button type="button" wire:click="clearSelection" class="px-2.5 py-1 text-xs font-mono font-bold border border-zinc-600 dark:border-zinc-400 rounded-xs cursor-pointer">
                        Batal
                    </button>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>

