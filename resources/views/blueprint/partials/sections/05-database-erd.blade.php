@php 
    $isErdImpacted = in_array('erd_schema', $prd['_impacted_segments'] ?? []); 
    $isEn = ($isEn ?? false) || ($locale ?? 'id') === 'en';
@endphp
            <section id="section-5" class="bg-white dark:bg-zinc-900 border {{ $isErdImpacted ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-zinc-200 dark:border-zinc-800' }} p-6 sm:p-8 mb-8 rounded-none print-break-inside-avoid prd-scroll-target transition-all">
                <div class="flex flex-col gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 cursor-pointer select-none" @click="toggleSection('section-5')">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-mono font-bold text-xs flex items-center justify-center rounded-none">07</span>
                            <div>
                                <h2 class="text-lg sm:text-xl font-black uppercase text-zinc-900 dark:text-zinc-100">{{ $isEn ? 'Database ERD & Relational Schema (PostgreSQL Strict ULID)' : 'Database ERD & Skema Relasi (PostgreSQL Strict ULID)' }}</h2>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">{{ $isEn ? 'Relational Database Topology, Key Entities & Foreign Key Constraints' : 'Topologi Basis Data Relasional, Entitas Kunci & Foreign Key Constraints' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if(!($isScopeLocked ?? false) || ($blueprint->project_status === 'Retail License') || (auth()->user()?->isSuperAdmin() ?? false))
                                <button 
                                    type="button" 
                                    @click.stop="openTweakModal('database_erd', '{{ $isEn ? 'Database ERD (PostgreSQL Strict ULID)' : 'Database ERD (PostgreSQL Strict ULID)' }}')" 
                                    class="px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-mono font-bold text-xs border border-emerald-500/30 flex items-center gap-1.5 transition shrink-0 cursor-pointer"
                                    title="{{ $isEn ? 'Synchronize and deepen table schemas and ERD relations from grounded project documents' : 'Sinkronkan dan perdalam skema tabel serta relasi ERD dari Dokumen Acuan Proyek' }}"
                                >
                                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span>{{ $isEn ? 'ELABORATE ERD FROM GROUNDED CORPUS' : 'PERDALAM ERD DARI DOKUMEN ACUAN' }}</span>
                                </button>
                            @endif
                            <button 
                                type="button" 
                                @click.stop="toggleSection('section-5')" 
                                class="px-2 py-1 text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition rounded-none flex items-center gap-1 font-mono text-xs border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700 ml-1 cursor-pointer"
                                :title="sectionCollapses['section-5'] ? (isEn ? 'Expand this section' : 'Buka seksi ini') : (isEn ? 'Collapse this section' : 'Ciutkan seksi ini')"
                            >
                                <span class="text-[10px] uppercase font-bold text-zinc-400 dark:text-zinc-500 hidden sm:inline" x-text="sectionCollapses['section-5'] ? (isEn ? 'EXPAND' : 'BUKA') : (isEn ? 'COLLAPSE' : 'CIUTKAN')"></span>
                                <svg class="w-4 h-4 transform transition-transform duration-200" :class="sectionCollapses['section-5'] ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </div>
                    </div>
                    @if($isErdImpacted)
                    <div class="flex items-start gap-2 p-2.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 text-xs font-mono text-amber-700 dark:text-amber-400 mt-1">
                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p x-html="locale === 'en' ? '<strong>NEEDS REVIEW:</strong> This ERD segment is detected as <em>stale</em>. You recently tweaked upstream components (like Features / Core Problems), please adjust the table schemas to align with the latest features.' : '<strong>NEEDS REVIEW:</strong> Segmen ERD ini terdeteksi <em>stale</em>. Anda baru saja melakukan tweak pada komponen hulu (seperti Fitur / Masalah Utama), harap sesuaikan skema tabel agar selaras dengan fitur terbaru tersebut.'"></p>
                    </div>
                    @endif
                </div>

                <!-- Collapsed Preview -->
                <div x-show="sectionCollapses['section-5']" @click="toggleSection('section-5')" class="py-2.5 px-3 bg-zinc-50 dark:bg-zinc-950/60 border border-dashed border-zinc-300 dark:border-zinc-800 text-xs font-mono text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center justify-between cursor-pointer transition select-none mb-6">
                    <span class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>{{ $isEn ? 'Section 07 collapsed (Database ERD & Relational Schema). Click to expand...' : 'Seksi 07 diciutkan (Database ERD & Skema Relasi). Klik untuk membuka...' }}</span>
                    </span>
                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500 uppercase font-bold">[ + EXPAND ]</span>
                </div>

                <div x-show="!sectionCollapses['section-5']" x-transition class="accordion-body">

                    <!-- ERD Tabs & Multi-Language Selector -->
                    <div class="flex flex-wrap items-center gap-2 font-mono text-xs no-print">
                        <!-- Multi-Language Tier 1 Toggle -->
                        <div class="flex items-center border border-zinc-300 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 p-0.5">
                            <span class="px-2 text-[10px] text-zinc-400 font-bold uppercase">LABEL TIER 1:</span>
                            <button 
                                @click="erdLang = 'id'" 
                                :class="erdLang === 'id' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'"
                                class="px-2 py-0.5 text-[10px] font-mono transition"
                            >
                                ID
                            </button>
                            <button 
                                @click="erdLang = 'en'" 
                                :class="erdLang === 'en' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'"
                                class="px-2 py-0.5 text-[10px] font-mono transition"
                            >
                                EN
                            </button>
                        </div>

                        <!-- ERD View Tabs -->
                        <button 
                            @click="erdTab = 'visual'" 
                            :class="erdTab === 'visual' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3z"></path></svg>
                            <span>TOPOLOGY</span>
                        </button>
                        <button 
                            @click="erdTab = 'mermaid'; $nextTick(() => window.renderMermaidDiagram('mermaid-erd-target', 'mermaid-erd-source'))" 
                            :class="erdTab === 'mermaid' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                            <span>MERMAID ERD</span>
                        </button>
                        <button 
                            @click="erdTab = 'table'" 
                            :class="erdTab === 'table' ? 'bg-zinc-900 dark:bg-emerald-500 text-white dark:text-black font-bold' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="px-2.5 py-1 border border-zinc-300 dark:border-zinc-700 transition flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>DICTIONARY</span>
                        </button>
                    </div>

                @php
                    $rawTables = $prd['erd_schema']['tables'] ?? [];
                    $sanitizedTables = [];
                    foreach ($rawTables as $t) {
                        $cleanTName = strtoupper(preg_replace('/[^a-zA-Z0-9_]/', '_', $t['name'] ?? 'ENTITY'));
                        $cleanTName = trim($cleanTName, '_');
                        if (empty($cleanTName)) $cleanTName = 'SYSTEM_RECORD';
                        
                        $cleanCols = [];
                        foreach ($t['columns'] ?? [] as $c) {
                            $colName = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $c['name'] ?? 'col'));
                            $colName = trim($colName, '_');
                            if (empty($colName)) continue;

                            $rawType = strtolower($c['type'] ?? 'string');
                            if (str_contains($rawType, 'ulid')) {
                                $colType = 'string';
                            } elseif (str_contains($rawType, 'int')) {
                                $colType = 'int';
                            } elseif (str_contains($rawType, 'bool')) {
                                $colType = 'boolean';
                            } elseif (str_contains($rawType, 'json')) {
                                $colType = 'jsonb';
                            } elseif (str_contains($rawType, 'time') || str_contains($rawType, 'date')) {
                                $colType = 'timestamp';
                            } else {
                                $colType = 'string';
                            }

                            $keyAttr = '';
                            if (($c['index'] ?? '') === 'PRIMARY' || $colName === 'id') {
                                $keyAttr = 'PK';
                            } elseif (str_contains(strtolower($c['type'] ?? ''), 'foreign') || str_ends_with($colName, '_id')) {
                                $keyAttr = 'FK';
                            } elseif (($c['index'] ?? '') === 'UNIQUE') {
                                $keyAttr = 'UK';
                            }
                            $cleanCols[] = [
                                'name' => $colName,
                                'type' => $colType,
                                'key' => $keyAttr,
                            ];
                        }
                        $sanitizedTables[] = [
                            'name' => $cleanTName,
                            'raw_name' => $t['name'],
                            'columns' => $cleanCols,
                        ];
                    }
                    $domainTableClean = $sanitizedTables[1]['name'] ?? 'PROJECT_RECORDS';
                    $domainTableRaw = $rawTables[1]['name'] ?? 'project_records';

                    // Compute dynamic relational connections from Foreign Keys
                    $resolvedRelations = [];
                    $tableLookupMap = [];
                    foreach ($sanitizedTables as $st) {
                        $rawLow = strtolower($st['raw_name']);
                        $tableLookupMap[$rawLow] = $st['name'];
                        $tableLookupMap[rtrim($rawLow, 's')] = $st['name'];
                    }

                    foreach ($sanitizedTables as $st) {
                        $childClean = $st['name'];
                        $childRaw = $st['raw_name'];
                        foreach ($st['columns'] as $col) {
                            $cname = strtolower($col['name']);
                            if ($col['key'] === 'FK' && $cname !== 'id') {
                                $targetClean = null;
                                $targetRaw = null;
                                if (in_array($cname, ['user_id', 'customer_id', 'driver_id', 'pic_id', 'client_id', 'admin_id'])) {
                                    $targetClean = $tableLookupMap['users'] ?? 'USERS';
                                    $targetRaw = 'users';
                                } else {
                                    $base = preg_replace('/_id$/', '', $cname);
                                    if (isset($tableLookupMap[$base])) {
                                        $targetClean = $tableLookupMap[$base];
                                        $targetRaw = $base;
                                    } elseif (isset($tableLookupMap[$base . 's'])) {
                                        $targetClean = $tableLookupMap[$base . 's'];
                                        $targetRaw = $base . 's';
                                    } else {
                                        foreach ($tableLookupMap as $k => $cln) {
                                            if (str_contains($cln, strtoupper($base)) || str_contains(strtoupper($base), $cln)) {
                                                $targetClean = $cln;
                                                $targetRaw = $k;
                                                break;
                                            }
                                        }
                                    }
                                }

                                if ($targetClean && $targetClean !== $childClean) {
                                    $relKey = "{$targetClean}__{$childClean}";
                                    if (!isset($resolvedRelations[$relKey])) {
                                        $resolvedRelations[$relKey] = [
                                            'parent' => $targetClean,
                                            'parent_raw' => $targetRaw ?: strtolower($targetClean),
                                            'child' => $childClean,
                                            'child_raw' => $childRaw,
                                            'fk_col' => $col['name'],
                                            'label' => '1:N',
                                        ];
                                    }
                                }
                            }
                        }
                    }

                    if (empty($resolvedRelations)) {
                        $resolvedRelations['USERS__' . $domainTableClean] = [
                            'parent' => 'USERS',
                            'parent_raw' => 'users',
                            'child' => $domainTableClean,
                            'child_raw' => $domainTableRaw,
                            'fk_col' => 'user_id',
                            'label' => '1:N',
                        ];
                    }
                @endphp

                <!-- 1. Interactive Visual ERD Topology (Connected Entity Relationship Cards) -->
                <div x-show="erdTab === 'visual'" class="mb-6">
                    <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-6 rounded-none relative">
                        <!-- Cardinality Banner -->
                        <div class="mb-6 p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                            <div class="flex items-center justify-between gap-3 mb-3 pb-2 border-b border-zinc-200 dark:border-zinc-800">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 bg-emerald-500 text-black font-black text-[10px]">RELATIONAL GRAPH</span>
                                    <span class="text-zinc-800 dark:text-zinc-200 text-xs font-bold">Relasi Antar Tabel (PostgreSQL Strict Foreign Key Cardinalities)</span>
                                </div>
                                <span class="text-[10px] text-zinc-500 font-mono hidden sm:inline">{{ count($resolvedRelations) }} Relasi Terdefinisi</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 text-[11px]">
                                @foreach($resolvedRelations as $rel)
                                    <div class="flex items-center gap-1.5 px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300">
                                        <strong class="text-emerald-600 dark:text-emerald-400 font-black">{{ $rel['parent_raw'] }} (1)</strong>
                                        <span class="text-zinc-400 font-bold">&bull;--&lt;</span>
                                        <strong class="text-zinc-900 dark:text-white font-black">{{ $rel['child_raw'] }} (N)</strong>
                                        <span class="text-[9px] px-1 py-0.2 bg-sky-500/20 text-sky-700 dark:text-sky-300 font-mono font-bold">via {{ $rel['fk_col'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Entity Schema Cards (2x2 Grid) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($prd['erd_schema']['tables'] ?? [] as $table)
                                <div class="bg-white dark:bg-zinc-900 border-2 border-zinc-200 dark:border-zinc-800 hover:border-emerald-500/60 transition shadow-sm dark:shadow-xl font-mono text-xs">
                                    <!-- Entity Card Header -->
                                    <div class="p-3.5 bg-zinc-100 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3z"></path></svg>
                                            <span class="font-black text-zinc-900 dark:text-white uppercase text-sm tracking-wide">{{ $table['name'] }}</span>
                                        </div>
                                        <span class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-emerald-700 dark:text-emerald-400 border border-emerald-500/30 text-[10px] font-bold">
                                            PK: ULID
                                        </span>
                                    </div>

                                    <div class="px-3.5 py-2 bg-zinc-50 dark:bg-zinc-900/60 border-b border-zinc-200 dark:border-zinc-800 text-[11px] text-zinc-600 dark:text-zinc-400 font-sans">
                                        {{ $table['description'] }}
                                    </div>

                                    <!-- Columns Attributes List -->
                                    <div class="divide-y divide-zinc-200 dark:divide-zinc-800/80">
                                        @foreach($table['columns'] ?? [] as $col)
                                             <div class="px-3.5 py-2 flex items-center justify-between gap-2 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    @if($col['index'] === 'PRIMARY')
                                                        <span class="px-1.5 py-0.2 bg-emerald-500 text-black font-black text-[9px]">PK</span>
                                                    @elseif(str_contains(strtolower($col['type']), 'foreign') || str_contains($col['name'], '_id'))
                                                        <span class="px-1.5 py-0.2 bg-sky-500 text-white font-black text-[9px]">FK</span>
                                                        <span class="text-[9px] text-sky-600 dark:text-sky-400 font-mono font-bold">&rarr; {{ preg_replace('/_id$/', '', $col['name']) }}</span>
                                                    @elseif($col['index'] === 'UNIQUE')
                                                        <span class="px-1.5 py-0.2 bg-purple-500 text-white font-black text-[9px]">UK</span>
                                                    @elseif($col['index'] === 'INDEX')
                                                        <span class="px-1.5 py-0.2 bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold text-[9px]">IDX</span>
                                                    @else
                                                        <span class="w-4 inline-block text-zinc-400 dark:text-zinc-600 text-center">&bull;</span>
                                                    @endif

                                                    <span class="font-bold text-zinc-900 dark:text-zinc-100 truncate">{{ $col['name'] }}</span>
                                                </div>

                                                <div class="flex items-center gap-2 text-right">
                                                    <span class="text-zinc-500 dark:text-zinc-400 text-[11px] font-sans truncate max-w-[130px]" x-text="erdLang === 'id' ? '{{ $col['label']['id'] ?? ($col['notes'] ?? '-') }}' : '{{ $col['label']['en'] ?? ($col['name'] ?? '-') }}'"></span>
                                                    <span class="text-emerald-600 dark:text-emerald-400 text-[10px] font-bold">{{ $col['type'] }}</span>
                                                </div>
                                             </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- 2. Mermaid ERD Diagram View (Safe Render with Dynamic Relations) -->
                <div x-show="erdTab === 'mermaid'" x-cloak class="mb-6">
                    <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6 rounded-none relative">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-4 pb-2 border-b border-zinc-200 dark:border-zinc-800 text-xs font-mono">
                            <div class="flex items-center gap-2">
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold uppercase">SKEMA BASIS DATA MERMAID (POSTGRESQL STRICT ERD)</span>
                                <span class="text-[10px] text-zinc-500 font-mono hidden md:inline">(Drag untuk Pan &bull; Scroll untuk Zoom)</span>
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <button type="button" @click="zoomDiagram('mermaid-erd-target', -0.25)" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold transition rounded-none cursor-pointer" title="Perkecil Zoom (-)">-</button>
                                <button type="button" @click="resetDiagram('mermaid-erd-target')" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-[10px] font-bold transition rounded-none cursor-pointer" title="Reset Zoom (1:1)">1:1</button>
                                <button type="button" @click="zoomDiagram('mermaid-erd-target', 0.25)" class="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold transition rounded-none cursor-pointer" title="Perbesar Zoom (+)">+</button>
                                <button type="button" @click="openDiagramFullscreen('Skema Basis Data Relasional (PostgreSQL Strict ERD)', 'mermaid-erd-source')" class="px-2.5 py-0.5 bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold uppercase transition rounded-none cursor-pointer flex items-center gap-1" title="Buka di Layar Penuh">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                    <span>LAYAR PENUH</span>
                                </button>
                                <button type="button" onclick="window.copyMermaidCode('mermaid-erd-source', this)" class="px-2.5 py-0.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-emerald-500 hover:text-black text-zinc-700 dark:text-zinc-300 text-[10px] font-mono font-bold transition flex items-center gap-1 border border-zinc-300 dark:border-zinc-700 cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span>SALIN KODE MERMAID ERD</span>
                                </button>
                            </div>
                        </div>

                        <script type="text/plain" id="mermaid-erd-source">erDiagram
@foreach($resolvedRelations as $rel)
    {{ $rel['parent'] }} ||--o{ {{ $rel['child'] }} : "references/has"
@endforeach

@foreach($sanitizedTables as $st)
    {{ $st['name'] }} {
@foreach($st['columns'] as $c)
        {{ $c['type'] }} {{ $c['name'] }} {{ $c['key'] }}
@endforeach
    }
@endforeach
                        </script>

                        <div 
                            class="overflow-hidden min-h-[260px] max-h-[600px] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 flex items-center justify-center p-3 relative cursor-grab active:cursor-grabbing select-none"
                            @mousedown="startDiagramDrag('mermaid-erd-target', $event)"
                            @mousemove="onDiagramDrag('mermaid-erd-target', $event)"
                            @mouseup="stopDiagramDrag('mermaid-erd-target')"
                            @mouseleave="stopDiagramDrag('mermaid-erd-target')"
                            @wheel.prevent="zoomDiagram('mermaid-erd-target', $event.deltaY < 0 ? 0.2 : -0.2)"
                        >
                            <div id="mermaid-erd-target" class="w-full flex items-center justify-center text-center text-zinc-800 dark:text-zinc-200" data-zoom="1" data-pan-x="0" data-pan-y="0">
                                <div class="text-zinc-400 text-xs font-mono animate-pulse flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-none bg-emerald-500 animate-ping"></span>
                                    <span>Memuat skema relasi database Mermaid...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. PostgreSQL Strict Data Dictionary Table -->
                <div x-show="erdTab === 'table'" x-cloak class="space-y-6">
                    @foreach($prd['erd_schema']['tables'] ?? [] as $table)
                        <div class="border border-zinc-200 dark:border-zinc-800 rounded-none overflow-hidden">
                            <div class="bg-zinc-900 text-white px-4 py-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-1 font-mono text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="text-emerald-400 font-bold">TABLE: {{ $table['name'] }}</span>
                                    <span class="text-zinc-400 hidden sm:inline">&bull; {{ $table['description'] }}</span>
                                </div>
                                <span class="text-zinc-400">
                                    PK: {{ $table['primary_key'] ?? 'id (ULID)' }}
                                </span>
                            </div>

                            <div class="overflow-x-auto max-h-96 overflow-y-auto custom-prd-scrollbar relative border-t border-zinc-200 dark:border-zinc-800">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead class="sticky top-0 z-10 bg-zinc-100/95 dark:bg-zinc-900/95 backdrop-blur-md">
                                        <tr class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 uppercase font-mono font-black text-[10px] tracking-wider select-none">
                                            <th class="py-2.5 px-3">Column Name</th>
                                            <th class="py-2.5 px-3">Tier 1 Label</th>
                                            <th class="py-2.5 px-3">Data Type</th>
                                            <th class="py-2.5 px-3">Index</th>
                                            <th class="py-2.5 px-3">Nullable</th>
                                            <th class="py-2.5 px-3">Description / Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-200/70 dark:divide-zinc-800/70 font-mono">
                                        @foreach($table['columns'] ?? [] as $col)
                                            <tr class="odd:bg-white dark:odd:bg-zinc-950 even:bg-zinc-50/60 dark:even:bg-zinc-900/40 hover:bg-zinc-100/90 dark:hover:bg-zinc-800/80 transition-colors group">
                                                <td class="py-2 px-3 font-bold text-zinc-900 dark:text-zinc-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 hover:ring-1 hover:ring-inset hover:ring-emerald-500/50 cursor-pointer">{{ $col['name'] }}</td>
                                                <td class="py-2 px-3 text-zinc-700 dark:text-zinc-300 font-sans hover:ring-1 hover:ring-inset hover:ring-emerald-500/50" x-text="erdLang === 'id' ? '{{ $col['label']['id'] ?? ($col['notes'] ?? '-') }}' : '{{ $col['label']['en'] ?? ($col['name'] ?? '-') }}'"></td>
                                                <td class="py-2 px-3 text-emerald-600 dark:text-emerald-400 font-bold hover:ring-1 hover:ring-inset hover:ring-emerald-500/50">{{ $col['type'] }}</td>
                                                <td class="py-2 px-3 hover:ring-1 hover:ring-inset hover:ring-emerald-500/50">
                                                    @if($col['index'] === 'PRIMARY')
                                                        <span class="px-1.5 py-0.5 bg-emerald-500 text-black font-black text-[9px] rounded-none">PRIMARY</span>
                                                    @elseif(str_contains(strtolower($col['type']), 'foreign') || str_contains($col['name'], '_id'))
                                                        <span class="px-1.5 py-0.5 bg-sky-500 text-white font-black text-[9px] rounded-none">FOREIGN</span>
                                                    @elseif($col['index'] === 'UNIQUE')
                                                        <span class="px-1.5 py-0.5 bg-purple-500 text-white font-black text-[9px] rounded-none">UNIQUE</span>
                                                    @elseif($col['index'] === 'INDEX')
                                                        <span class="px-1.5 py-0.5 bg-blue-500 text-white font-black text-[9px] rounded-none">INDEX</span>
                                                    @else
                                                        <span class="text-zinc-400 text-[10px]">-</span>
                                                    @endif
                                                </td>
                                                <td class="py-2 px-3 text-zinc-500 hover:ring-1 hover:ring-inset hover:ring-emerald-500/50">{{ $col['nullable'] ? 'YES' : 'NO' }}</td>
                                                <td class="py-2 px-3 font-sans text-zinc-600 dark:text-zinc-400 hover:ring-1 hover:ring-inset hover:ring-emerald-500/50">{{ $col['notes'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
                </div>
            </section>

            