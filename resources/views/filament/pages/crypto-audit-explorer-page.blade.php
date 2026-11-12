<x-filament-panels::page>
    @vite(['resources/css/app.css'])

    @php
        $loc = app()->getLocale() === 'en' ? 'en' : 'id';
        $data = $this->explorerData;
        /** @var array<int, \App\Models\AuditLog> $rows */
        $rows = $data['rows'] ?? [];
        $nextCursor = $data['next_cursor'] ?? null;
        $prevCursor = $data['prev_cursor'] ?? null;
        $presets = $data['presets'] ?? [];
        /** @var \App\Models\AuditLog|null $inspected */
        $inspected = $data['inspected'] ?? null;
        $pageRowIds = array_values(array_map(fn ($r) => (string) $r->id, $rows));
        $allPageSelected = count($pageRowIds) > 0 && count(array_intersect($pageRowIds, $this->selectedIds)) === count($pageRowIds);

        $padClass = match ($this->density) {
            'compact' => 'py-1.5 px-3 text-xs',
            'relaxed' => 'py-4 px-5 text-sm',
            default => 'py-3 px-4 text-xs',
        };

        $chainSummary = $this->chainVerificationSummary;
        $isChainIntact = ($chainSummary['status'] ?? 'VERIFIED_INTACT') === 'VERIFIED_INTACT';
    @endphp

    <div class="space-y-4 font-sans text-zinc-950 dark:text-zinc-100 relative">

        <!-- 1. LIVE CRYPTOGRAPHIC SHA-256 HASH CHAIN CERTIFICATE BANNER -->
        <div class="p-4 border-2 rounded-none flex flex-col lg:flex-row lg:items-center justify-between gap-3 {{ $isChainIntact ? 'bg-emerald-50/90 dark:bg-emerald-950/40 border-emerald-500 dark:border-emerald-700' : 'bg-red-50 dark:bg-red-950/60 border-red-600 dark:border-red-500' }}">
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2 font-mono text-xs">
                    <span class="px-2 py-0.5 font-black uppercase {{ $isChainIntact ? 'bg-zinc-950 text-emerald-400 dark:bg-emerald-400 dark:text-zinc-950' : 'bg-red-700 text-white' }}">
                        {{ $isChainIntact ? 'VERIFIED INTACT ✅' : '🚨 TAMPER ALERT MISMATCH' }}
                    </span>
                    <span class="font-black text-zinc-950 dark:text-zinc-100">
                        {{ $loc === 'en' ? 'SHA-256 Canonical JSONB Hash Chain (prev_hash → current_hash)' : 'Rantai Kriptografi SHA-256 Kanonik JSONB (prev_hash → current_hash)' }}
                    </span>
                </div>
                <p class="text-xs font-mono text-zinc-800 dark:text-zinc-300 tabular-nums">
                    Checked: <strong>{{ number_format((int) ($chainSummary['checked_rows'] ?? 0)) }}</strong> rows &bull;
                    Chained Intact: <strong>{{ number_format((int) ($chainSummary['verified_chained_rows'] ?? 0)) }}</strong> &bull;
                    Tampered: <strong class="{{ ($chainSummary['tampered_rows_count'] ?? 0) > 0 ? 'text-red-700 dark:text-red-300' : 'text-emerald-900 dark:text-emerald-300' }}">{{ number_format((int) ($chainSummary['tampered_rows_count'] ?? 0)) }}</strong> &bull;
                    Head Hash: <code>{{ substr((string) ($chainSummary['latest_head_hash'] ?? 'GENESIS'), 0, 20) }}…</code>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 font-mono text-xs shrink-0">
                <button
                    type="button"
                    wire:click="runLiveChainVerification"
                    class="px-3 py-2 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-black rounded-xs cursor-pointer transition"
                >
                    🔍 {{ $loc === 'en' ? 'Verify SHA-256 Chain' : 'Verifikasi Rantai SHA-256' }}
                </button>
                <button
                    type="button"
                    wire:click="exportThreeSheetExcel"
                    class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 border border-emerald-700 font-black rounded-xs cursor-pointer transition"
                >
                    📊 {{ $loc === 'en' ? 'Export 3-Sheet .XLSX' : 'Unduh 3-Sheet .XLSX (KAP/ISO)' }}
                </button>
                <button
                    type="button"
                    wire:click="exportStreamingCsv"
                    class="px-3 py-2 bg-white hover:bg-zinc-100 text-zinc-950 border border-zinc-400 dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700 font-black rounded-xs cursor-pointer transition"
                >
                    📥 {{ $loc === 'en' ? 'Stream .CSV O(1)' : 'Stream .CSV O(1)' }}
                </button>
            </div>
        </div>

        <!-- 2. 12 SECTOR-SPECIFIC STATUTORY AUDIT PRESETS -->
        <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-sm space-y-2">
            <div class="flex flex-wrap items-center justify-between gap-2 font-mono text-[11px]">
                <span class="font-black uppercase text-zinc-900 dark:text-zinc-200">
                    🏛️ {{ $loc === 'en' ? '12 SECTOR INDUSTRY AUDIT PRESETS (1-CLICK COMPLIANCE FILTER):' : '12 PRESET AUDIT REGULASI SEKTOR INDUSTRI (FILTER KEPATUHAN 1-KLIK):' }}
                </span>
                <button
                    type="button"
                    wire:click="clearAllFilters"
                    class="px-2 py-0.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-900 dark:hover:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-200 font-bold rounded-none cursor-pointer"
                >
                    ✕ {{ $loc === 'en' ? 'Reset All Filters' : 'Reset Semua Filter' }}
                </button>
            </div>
            <div class="flex flex-wrap gap-1.5 font-mono text-[10px]">
                <button
                    type="button"
                    wire:click="applySectorPreset('ALL_12_SECTORS')"
                    class="px-2.5 py-1 border rounded-none cursor-pointer transition {{ $this->sectorCode === 'ALL_12_SECTORS' ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-500 dark:text-zinc-950 dark:border-emerald-400 font-black' : 'bg-zinc-50 hover:bg-zinc-100 text-zinc-900 border-zinc-300 dark:bg-zinc-900 dark:text-zinc-200 dark:border-zinc-800 font-bold' }}"
                >
                    ALL SECTORS
                </button>
                @foreach($presets as $sCode => $pMeta)
                    <button
                        type="button"
                        wire:click="applySectorPreset('{{ $sCode }}')"
                        title="{{ $pMeta['localized_title'] ?? '' }}"
                        class="px-2 py-1 border rounded-none cursor-pointer transition {{ $this->sectorCode === $sCode ? 'bg-zinc-950 text-emerald-400 border-zinc-950 dark:bg-emerald-500 dark:text-zinc-950 dark:border-emerald-400 font-black' : 'bg-zinc-50 hover:bg-zinc-100 text-zinc-900 border-zinc-300 dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-800 font-bold' }}"
                    >
                        S{{ sprintf('%02d', $pMeta['sector_number'] ?? 1) }} • {{ $pMeta['badge'] ?? $sCode }}
                    </button>
                @endforeach
            </div>
            @if($this->sectorCode !== 'ALL_12_SECTORS' && isset($presets[$this->sectorCode]))
                <div class="p-2 bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-400 dark:border-emerald-800 text-xs font-mono text-emerald-950 dark:text-emerald-200">
                    <strong>{{ $presets[$this->sectorCode]['localized_title'] ?? '' }}</strong>
                    — Standards: {{ implode(' • ', $presets[$this->sectorCode]['compliance_standards'] ?? []) }}
                </div>
            @endif
        </div>

        <!-- 3. 8-DIMENSION FILTER BAR + SEARCH ("X") + DENSITY + COLUMN VISIBILITY + GRID/LIST SWITCHER -->
        <div class="p-3 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-sm space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2 font-mono text-xs">
                <div>
                    <label class="block text-[10px] font-black uppercase text-zinc-700 dark:text-zinc-400 mb-0.5">{{ $loc === 'en' ? '1. Date From' : '1. Dari Tanggal' }}</label>
                    <input type="date" wire:model.live="dateFrom" class="w-full px-2 py-1.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 rounded-xs" />
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-zinc-700 dark:text-zinc-400 mb-0.5">{{ $loc === 'en' ? '2. Date To' : '2. Sampai Tanggal' }}</label>
                    <input type="date" wire:model.live="dateTo" class="w-full px-2 py-1.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 rounded-xs" />
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-zinc-700 dark:text-zinc-400 mb-0.5">{{ $loc === 'en' ? '3. Channel' : '3. Kanal Eksekusi' }}</label>
                    <select wire:model.live="executionChannel" class="w-full px-2 py-1.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 rounded-xs">
                        <option value="">ALL CHANNELS</option>
                        <option value="WEB_UI">WEB_UI</option>
                        <option value="AI_COPILOT">AI_COPILOT</option>
                        <option value="API_WEBHOOK">API_WEBHOOK</option>
                        <option value="CLI">CLI</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-zinc-700 dark:text-zinc-400 mb-0.5">{{ $loc === 'en' ? '4. Actor Type' : '4. Tipe Pelaku' }}</label>
                    <select wire:model.live="actorType" class="w-full px-2 py-1.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 rounded-xs">
                        <option value="">ALL ACTORS</option>
                        <option value="admin">ADMIN</option>
                        <option value="client">CLIENT</option>
                        <option value="ai_agent">AI_AGENT</option>
                        <option value="system">SYSTEM</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-zinc-700 dark:text-zinc-400 mb-0.5">{{ $loc === 'en' ? '5. Domain Category' : '5. Kategori Event' }}</label>
                    <select wire:model.live="eventCategory" class="w-full px-2 py-1.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 rounded-xs">
                        <option value="">ALL CATEGORIES</option>
                        <option value="agentshield_security">agentshield_security</option>
                        <option value="codesign_alignment">codesign_alignment</option>
                        <option value="ai_agent">ai_agent</option>
                        <option value="commercial_contract">commercial_contract</option>
                        <option value="security">security</option>
                        <option value="billing">billing</option>
                        <option value="project_os">project_os</option>
                        <option value="cv_pro_studio">cv_pro_studio</option>
                        <option value="superadmin_bypass">superadmin_bypass</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-zinc-700 dark:text-zinc-400 mb-0.5">{{ $loc === 'en' ? '6. Entity / Model' : '6. Entitas / Model' }}</label>
                    <input type="text" wire:model.live.debounce.300ms="targetType" placeholder="PosProject / Document..." class="w-full px-2 py-1.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 rounded-xs" />
                </div>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                <!-- Search with Mandatory Instant Clear ('X') Button -->
                <div class="relative flex-1 max-w-lg">
                    <input
                        type="text"
                        wire:model.live.debounce.250ms="search"
                        placeholder="{{ $loc === 'en' ? 'Search ULID, action, actor, IP, or SHA-256 hash...' : 'Cari ULID, nama aksi, pelaku, alamat IP, atau deskripsi...' }}"
                        class="w-full pl-3 pr-9 py-1.5 text-xs font-mono bg-zinc-50 dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"
                    />
                    @if($this->search !== '')
                        <button
                            type="button"
                            wire:click="resetSearch"
                            title="{{ $loc === 'en' ? 'Instant Clear Search' : 'Hapus teks pencarian instan' }}"
                            class="absolute right-2 top-1/2 -translate-y-1/2 px-1.5 py-0.5 text-xs font-mono font-black bg-zinc-200 dark:bg-zinc-800 text-zinc-950 dark:text-zinc-100 hover:bg-zinc-300 dark:hover:bg-zinc-700 rounded-none cursor-pointer"
                        >
                            ✕
                        </button>
                    @endif
                </div>

                <!-- Column Visibility + Row Density + Grid/List Dual View Switcher -->
                <div class="flex flex-wrap items-center gap-2 font-mono text-[11px]">
                    <div x-data="{ openCols: false }" class="relative">
                        <button
                            type="button"
                            @click="openCols = !openCols"
                            class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 font-bold rounded-xs cursor-pointer"
                        >
                            👁️ {{ $loc === 'en' ? 'Columns' : 'Kolom' }}
                        </button>
                        <div
                            x-show="openCols"
                            @click.outside="openCols = false"
                            x-cloak
                            class="absolute right-0 mt-1 w-48 p-2 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 shadow-xl z-40 space-y-1"
                        >
                            @foreach([
                                'created_at' => 'Timestamp UTC',
                                'sector_channel' => 'Sector & Channel',
                                'action_category' => 'Action & Category',
                                'actor' => 'Actor & IP',
                                'entity' => 'Target Entity',
                                'hash_chain' => 'SHA-256 Chain',
                            ] as $colKey => $colLabel)
                                <label class="flex items-center gap-2 text-[11px] text-zinc-900 dark:text-zinc-100 cursor-pointer">
                                    <input type="checkbox" wire:click="toggleColumnVisibility('{{ $colKey }}')" @checked($this->visibleColumns[$colKey] ?? true) class="rounded-none" />
                                    <span>{{ $colLabel }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="inline-flex border border-zinc-300 dark:border-zinc-700 rounded-xs overflow-hidden">
                        @foreach(['compact' => 'Compact', 'normal' => 'Normal', 'relaxed' => 'Relaxed'] as $dKey => $dLbl)
                            <button
                                type="button"
                                wire:click="setDensity('{{ $dKey }}')"
                                class="px-2 py-1 font-bold cursor-pointer {{ $this->density === $dKey ? 'bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950' : 'bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-300' }}"
                            >
                                {{ $dLbl }}
                            </button>
                        @endforeach
                    </div>

                    <div class="inline-flex border border-zinc-300 dark:border-zinc-700 rounded-xs overflow-hidden">
                        <button
                            type="button"
                            wire:click="setViewMode('list')"
                            class="px-2.5 py-1 font-black cursor-pointer {{ $this->viewMode === 'list' ? 'bg-zinc-950 text-white dark:bg-emerald-500 dark:text-zinc-950' : 'bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-300' }}"
                        >
                            LIST
                        </button>
                        <button
                            type="button"
                            wire:click="setViewMode('grid')"
                            class="px-2.5 py-1 font-black cursor-pointer {{ $this->viewMode === 'grid' ? 'bg-zinc-950 text-white dark:bg-emerald-500 dark:text-zinc-950' : 'bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-300' }}"
                        >
                            GRID
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. DUAL VIEW: ULTIMATE ENTERPRISE LIST TABLE (STICKY HEADER + FREEZE L/R + ZEBRA) OR GRID CARDS -->
        @if($this->viewMode === 'list')
            <div class="border border-zinc-300 dark:border-zinc-800 bg-white dark:bg-zinc-950 rounded-xs overflow-x-auto max-h-[65vh] overflow-y-auto">
                <table class="w-full text-left border-collapse font-mono">
                    <thead class="sticky top-0 z-20 bg-zinc-100/95 dark:bg-zinc-900/95 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800 text-[11px] uppercase text-zinc-900 dark:text-zinc-200">
                        <tr>
                            <th class="sticky left-0 z-30 bg-zinc-100 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800 {{ $padClass }}">
                                <div class="flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        wire:click="selectCurrentPageRows({{ json_encode($pageRowIds) }})"
                                        @checked($allPageSelected)
                                        class="rounded-none"
                                    />
                                    <button type="button" wire:click="cycleSort('id')" class="font-black flex items-center gap-1 cursor-pointer">
                                        <span>ULID</span>
                                        <span>{{ $this->sortBy === 'id' ? ($this->sortDirection === 'asc' ? '↑' : '↓') : '↕' }}</span>
                                    </button>
                                </div>
                            </th>
                            @if($this->visibleColumns['created_at'] ?? true)
                                <th class="{{ $padClass }} font-black">UTC Timestamp</th>
                            @endif
                            @if($this->visibleColumns['sector_channel'] ?? true)
                                <th class="{{ $padClass }} font-black">Sector &amp; Channel</th>
                            @endif
                            @if($this->visibleColumns['action_category'] ?? true)
                                <th class="{{ $padClass }}">
                                    <button type="button" wire:click="cycleSort('action')" class="font-black flex items-center gap-1 cursor-pointer">
                                        <span>Action / Category</span>
                                        <span>{{ $this->sortBy === 'action' ? ($this->sortDirection === 'asc' ? '↑' : ($this->sortDirection === 'desc' ? '↓' : '↕')) : '↕' }}</span>
                                    </button>
                                </th>
                            @endif
                            @if($this->visibleColumns['actor'] ?? true)
                                <th class="{{ $padClass }}">
                                    <button type="button" wire:click="cycleSort('actor_name')" class="font-black flex items-center gap-1 cursor-pointer">
                                        <span>Actor &amp; IP</span>
                                        <span>{{ $this->sortBy === 'actor_name' ? ($this->sortDirection === 'asc' ? '↑' : ($this->sortDirection === 'desc' ? '↓' : '↕')) : '↕' }}</span>
                                    </button>
                                </th>
                            @endif
                            @if($this->visibleColumns['entity'] ?? true)
                                <th class="{{ $padClass }} font-black">Entity &amp; Summary</th>
                            @endif
                            @if($this->visibleColumns['hash_chain'] ?? true)
                                <th class="{{ $padClass }} font-black">SHA-256 Chain</th>
                            @endif
                            <th class="sticky right-0 z-30 bg-zinc-100 dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 {{ $padClass }} font-black text-right">
                                Inspect
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($rows as $row)
                            @php
                                $rowIntact = $row->verifyRowIntegrity();
                                $isRowSelected = in_array((string) $row->id, $this->selectedIds, true);
                            @endphp
                            <tr class="odd:bg-white dark:odd:bg-zinc-950 even:bg-zinc-50/60 dark:even:bg-zinc-900/40 hover:bg-zinc-100/90 dark:hover:bg-zinc-800/80 transition">
                                <td class="sticky left-0 z-10 bg-inherit border-r border-zinc-200 dark:border-zinc-800 {{ $padClass }} hover:ring-1 hover:ring-inset hover:ring-emerald-500/60 tabular-nums">
                                    <div class="flex items-center gap-2">
                                        <input
                                            type="checkbox"
                                            wire:click="toggleSelectRow('{{ $row->id }}')"
                                            @checked($isRowSelected)
                                            class="rounded-none"
                                        />
                                        <span class="font-bold text-zinc-950 dark:text-zinc-100" title="{{ $row->id }}">{{ substr((string) $row->id, 0, 10) }}…</span>
                                    </div>
                                </td>
                                @if($this->visibleColumns['created_at'] ?? true)
                                    <td class="{{ $padClass }} hover:ring-1 hover:ring-inset hover:ring-emerald-500/60 tabular-nums text-zinc-800 dark:text-zinc-300 whitespace-nowrap">
                                        {{ $row->created_at?->format('Y-m-d H:i:s') }}
                                    </td>
                                @endif
                                @if($this->visibleColumns['sector_channel'] ?? true)
                                    <td class="{{ $padClass }} hover:ring-1 hover:ring-inset hover:ring-emerald-500/60">
                                        <div class="font-black text-zinc-950 dark:text-zinc-100 text-[10px]">{{ $row->sector_code ?: 'CORE_OS' }}</div>
                                        <div class="text-[10px] text-zinc-600 dark:text-zinc-400">{{ $row->execution_channel ?: 'WEB_UI' }}</div>
                                    </td>
                                @endif
                                @if($this->visibleColumns['action_category'] ?? true)
                                    <td class="{{ $padClass }} hover:ring-1 hover:ring-inset hover:ring-emerald-500/60">
                                        <span class="px-1.5 py-0.5 bg-zinc-900 text-white dark:bg-zinc-800 dark:text-emerald-300 text-[10px] font-black rounded-none">{{ $row->action }}</span>
                                        <div class="text-[10px] text-zinc-600 dark:text-zinc-400 mt-0.5">{{ $row->event_category ?: 'system' }}</div>
                                    </td>
                                @endif
                                @if($this->visibleColumns['actor'] ?? true)
                                    <td class="{{ $padClass }} hover:ring-1 hover:ring-inset hover:ring-emerald-500/60">
                                        <div class="font-bold text-zinc-950 dark:text-zinc-100">{{ $row->actor_name ?: 'System' }}</div>
                                        <div class="text-[10px] text-zinc-600 dark:text-zinc-400 tabular-nums">{{ strtoupper((string) ($row->actor_type ?: 'system')) }} • {{ $row->ip_address ?: '-' }}</div>
                                    </td>
                                @endif
                                @if($this->visibleColumns['entity'] ?? true)
                                    <td class="{{ $padClass }} hover:ring-1 hover:ring-inset hover:ring-emerald-500/60 max-w-xs">
                                        <div class="font-bold text-zinc-900 dark:text-zinc-200 text-[11px] truncate">{{ $row->model_type ? class_basename($row->model_type) : '-' }} {{ $row->model_id ? '#' . substr((string) $row->model_id, 0, 8) : '' }}</div>
                                        <div class="text-[11px] font-sans text-zinc-700 dark:text-zinc-300 truncate" title="{{ $row->description }}">{{ $row->description }}</div>
                                    </td>
                                @endif
                                @if($this->visibleColumns['hash_chain'] ?? true)
                                    <td class="{{ $padClass }} hover:ring-1 hover:ring-inset hover:ring-emerald-500/60 tabular-nums whitespace-nowrap">
                                        <span class="px-1.5 py-0.5 border text-[10px] font-black rounded-none {{ $rowIntact ? 'bg-emerald-100 text-emerald-950 border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700' : 'bg-red-100 text-red-950 border-red-500 dark:bg-red-950 dark:text-red-200 dark:border-red-600' }}">
                                            {{ $rowIntact ? 'INTACT ✓' : 'TAMPERED 🚨' }}
                                        </span>
                                        <div class="text-[10px] text-zinc-600 dark:text-zinc-400 mt-0.5">{{ substr((string) ($row->current_hash ?: $row->integrity_hash ?: 'LEGACY'), 0, 12) }}…</div>
                                    </td>
                                @endif
                                <td class="sticky right-0 z-10 bg-inherit border-l border-zinc-200 dark:border-zinc-800 {{ $padClass }} text-right">
                                    <button
                                        type="button"
                                        wire:click="inspectRow('{{ $row->id }}')"
                                        class="px-2 py-1 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:text-zinc-950 text-[10px] font-black rounded-none cursor-pointer"
                                    >
                                        DIFF
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-xs text-zinc-600 dark:text-zinc-400">
                                    {{ $loc === 'en' ? 'No audit records matched the active cryptographic filter.' : 'Tidak ada rekaman jejak audit yang cocok dengan filter aktif.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 font-mono text-xs">
                @foreach($rows as $row)
                    @php $rowIntact = $row->verifyRowIntegrity(); @endphp
                    <div class="p-3.5 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 hover:border-emerald-500 rounded-xs space-y-2 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-1.5 py-0.5 bg-zinc-950 text-white dark:bg-zinc-800 dark:text-emerald-300 text-[10px] font-black">{{ $row->action }}</span>
                                <span class="px-1.5 py-0.5 border text-[10px] font-black {{ $rowIntact ? 'bg-emerald-100 text-emerald-950 border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700' : 'bg-red-100 text-red-950 border-red-500' }}">
                                    {{ $rowIntact ? 'SHA-256 INTACT ✓' : 'TAMPERED 🚨' }}
                                </span>
                            </div>
                            <div class="mt-2 font-sans text-xs font-bold text-zinc-950 dark:text-zinc-100">{{ $row->description }}</div>
                            <div class="mt-1 text-[10px] text-zinc-600 dark:text-zinc-400 tabular-nums">
                                {{ $row->created_at?->format('Y-m-d H:i:s') }} • {{ $row->actor_name ?: 'System' }} ({{ $row->execution_channel ?: 'WEB_UI' }})
                            </div>
                        </div>
                        <div class="pt-2 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between text-[10px]">
                            <span class="truncate text-zinc-600 dark:text-zinc-400">Hash: {{ substr((string) ($row->current_hash ?: $row->integrity_hash ?: '-'), 0, 16) }}…</span>
                            <button type="button" wire:click="inspectRow('{{ $row->id }}')" class="px-2 py-0.5 bg-zinc-950 text-white dark:bg-emerald-500 dark:text-zinc-950 font-black cursor-pointer">
                                INSPECT
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- O(1) KEYSET CURSOR PAGINATION CONTROLS -->
        <div class="flex items-center justify-between font-mono text-xs pt-1">
            <span class="text-zinc-600 dark:text-zinc-400 tabular-nums">
                Keyset Cursor O(1) Engine &bull; {{ count($rows) }} rows loaded
            </span>
            <div class="flex gap-2">
                <button
                    type="button"
                    wire:click="prevPage('{{ $prevCursor }}')"
                    @disabled(!$prevCursor)
                    class="px-3 py-1.5 border rounded-xs font-black {{ $prevCursor ? 'bg-white dark:bg-zinc-900 border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 cursor-pointer' : 'bg-zinc-100 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 text-zinc-400 cursor-not-allowed' }}"
                >
                    &larr; Prev Cursor
                </button>
                <button
                    type="button"
                    wire:click="nextPage('{{ $nextCursor }}')"
                    @disabled(!$nextCursor)
                    class="px-3 py-1.5 border rounded-xs font-black {{ $nextCursor ? 'bg-white dark:bg-zinc-900 border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 cursor-pointer' : 'bg-zinc-100 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 text-zinc-400 cursor-not-allowed' }}"
                >
                    Next Cursor &rarr;
                </button>
            </div>
        </div>

        <!-- FLOATING MULTI-SELECT BATCH ACTION BAR -->
        @if(count($this->selectedIds) > 0)
            <div class="sticky bottom-4 z-40 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 px-4 py-2.5 rounded-xs shadow-2xl flex flex-wrap items-center justify-between gap-3 font-mono text-xs">
                <span class="font-black tabular-nums">
                    ☑️ {{ number_format(count($this->selectedIds)) }} {{ $loc === 'en' ? 'audit records selected' : 'baris jejak audit dipilih' }}
                </span>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" wire:click="exportThreeSheetExcel" class="px-3 py-1 bg-emerald-500 text-zinc-950 font-black rounded-none cursor-pointer">
                        📊 Export Selected (.XLSX 3-Sheet)
                    </button>
                    <button type="button" wire:click="exportStreamingCsv" class="px-3 py-1 bg-amber-400 text-zinc-950 font-black rounded-none cursor-pointer">
                        📥 Export Selected (.CSV)
                    </button>
                    <button type="button" wire:click="clearSelection" class="px-2.5 py-1 bg-zinc-700 text-white dark:bg-zinc-300 dark:text-zinc-950 font-bold rounded-none cursor-pointer">
                        ✕ Clear
                    </button>
                </div>
            </div>
        @endif

        <!-- FORENSIC DIFF & HASH CHAIN MODAL INSPECTOR -->
        @if($inspected)
            <div class="fixed inset-0 z-50 bg-black/75 backdrop-blur-xs flex items-center justify-center p-4">
                <div class="w-full max-w-3xl bg-white dark:bg-zinc-950 border-2 border-zinc-400 dark:border-zinc-700 rounded-none shadow-2xl overflow-hidden flex flex-col max-h-[88vh]">
                    <div class="px-4 py-3 bg-zinc-950 text-white flex items-center justify-between font-mono text-xs">
                        <span class="font-black uppercase">FORENSIC AUDIT RECORD // ULID {{ $inspected->id }}</span>
                        <button type="button" wire:click="inspectRow(null)" class="px-2 py-0.5 bg-zinc-800 hover:bg-zinc-700 font-bold cursor-pointer">✕ CLOSE</button>
                    </div>
                    <div class="p-4 overflow-y-auto space-y-3 font-mono text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800">
                            <div><strong>Prev Hash:</strong> <span class="break-all text-[10px]">{{ $inspected->prev_hash ?: 'GENESIS_0000' }}</span></div>
                            <div><strong>Current Hash:</strong> <span class="break-all text-[10px] text-emerald-900 dark:text-emerald-300">{{ $inspected->current_hash ?: $inspected->integrity_hash }}</span></div>
                            <div><strong>Channel:</strong> {{ $inspected->execution_channel ?: 'WEB_UI' }}</div>
                            <div><strong>Idempotency Key:</strong> {{ $inspected->idempotency_key ?: '-' }}</div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div>
                                <div class="font-black uppercase text-red-900 dark:text-red-300 mb-1">Before (old_values JSONB)</div>
                                <pre class="p-2.5 bg-zinc-950 text-red-200 text-[10px] overflow-x-auto">{{ json_encode($inspected->old_values ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                            <div>
                                <div class="font-black uppercase text-emerald-900 dark:text-emerald-300 mb-1">After (new_values JSONB)</div>
                                <pre class="p-2.5 bg-zinc-950 text-emerald-200 text-[10px] overflow-x-auto">{{ json_encode($inspected->new_values ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>

