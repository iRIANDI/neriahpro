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
                        'sectors' => ['label' => '12 Sektor Fungsional', 'count' => $stats['sectors'] ?? 12],
                        'primitives' => ['label' => '12 Core Primitives', 'count' => $stats['primitives'] ?? 12],
                        'stacks' => ['label' => '9 Stack Presets (M1–S2)', 'count' => $stats['stacks'] ?? 9],
                        'rules' => ['label' => 'Anti-Slop Rule Packs', 'count' => $stats['rules'] ?? 6],
                        'projects' => ['label' => 'Assembled Projects', 'count' => $stats['projects'] ?? 0],
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

        <!-- Content Area: Grid View vs Enterprise Sticky List View -->
        @if(empty($items))
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
                                    {{ $item['primitives_count'] }} Core Primitives Terpasang
                                </div>
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

