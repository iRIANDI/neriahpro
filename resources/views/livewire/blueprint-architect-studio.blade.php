@php
    $tables = $schema['data_dictionary'] ?? [];
    $stateMachines = $schema['state_machines'] ?? [];
    $locks = $schema['domain_invariants']['concurrency_locks'] ?? [];
    $decisionRules = $schema['decision_tables_matrix']['rules'] ?? [];
    $secUi = $schema['sectoral_ui_ux_archetype'] ?? [];
    $printSpec = $schema['printable_and_thermal_contracts'] ?? [];
    $envVars = $schema['environment_secret_vault']['variables'] ?? [];
    $redTeamVectors = $schema['security_red_team_suite']['vectors'] ?? [];
    $enterpriseMenuMatrix = $schema['enterprise_menu_matrix'] ?? [];
    $enterpriseGlueList = $schema['five_enterprise_glue_primitives'] ?? [];
    $dagLayers = $schema['topological_dag_plan']['layers'] ?? [];
    $hwContract = $schema['hardware_iot_peripheral_contract'] ?? [];
    $wsContract = $schema['realtime_websocket_contract'] ?? [];
    $sopContract = $schema['client_sop_and_onboarding'] ?? [];
    $fifteenPillars = $schema['fifteen_architecture_pillars'] ?? [];
    $detScore = number_format((float) ($doctor['determinism_score'] ?? 100.0), 1);
    $isZeroAssumptionReady = !empty($doctor['zero_assumption_ready']);
    $verifiedCount = count(array_filter($phaseProgress, fn ($p) => is_array($p) && ($p['status'] ?? '') === 'VERIFIED'));

    $activeSectorObj = collect($catalog['sectors'] ?? [])->firstWhere('code', $sectorCode) ?? ($catalog['sectors'][0] ?? []);
    $activeStackObj = collect($catalog['stack_presets'] ?? [])->firstWhere('code', $stackCode) ?? ($catalog['stack_presets'][0] ?? []);
@endphp

<div
    x-data="{
        copyStudioBuffer() {
            const el = document.getElementById('studio-right-compiler-code');
            if (!el) return;
            navigator.clipboard.writeText(el.innerText || el.textContent || '');
            window.showToast({
                type: 'success',
                title: 'DISALIN KE CLIPBOARD',
                message: 'Artefak/Prompt berhasil disalin. Tempel langsung ke Claude Code, Cursor, atau Antigravity IDE.'
            });
        }
    }"
    x-on:keydown.window.ctrl.shift.c.prevent="copyStudioBuffer()"
    x-on:keydown.window.meta.shift.c.prevent="copyStudioBuffer()"
    class="flex flex-col flex-1 lg:h-screen lg:overflow-hidden bg-zinc-100 dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100 select-none"
>
    <!-- =================================================================== -->
    <!-- TOP BAR: PROJECT IDENTITY + DETERMINISM SCORE + WIZARD / MCP / DIFF -->
    <!-- =================================================================== -->
    <header class="shrink-0 bg-white dark:bg-zinc-900 border-b border-zinc-300 dark:border-zinc-800 px-3 py-2 flex flex-wrap items-center justify-between gap-2 z-30">
        <div class="flex flex-wrap items-center gap-2">
            <a href="/blueprint?tier=retail_spark&amp;new=1" class="px-2 py-1 bg-zinc-950 text-white dark:bg-emerald-500 dark:text-zinc-950 font-mono text-[11px] font-black uppercase tracking-wider rounded-none hover:opacity-90 transition">
                [NeriahPro OS v9.0 • 100% FREEZE]
            </a>
            <div class="flex items-center gap-1.5 font-mono text-xs">
                <input
                    type="text"
                    wire:model.live.debounce.400ms="projectName"
                    aria-label="Nama Proyek"
                    class="px-2 py-0.5 w-40 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 font-black text-zinc-950 dark:text-zinc-100 rounded-xs text-xs"
                />
                <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/90 dark:text-emerald-200 dark:border-emerald-700 text-[10px] font-black rounded-xs tabular-nums">
                    {{ $currentVersion }}
                </span>
                <button
                    type="button"
                    wire:click="startFreshIsolatedProject"
                    class="px-2 py-0.5 bg-amber-100 hover:bg-amber-200 text-amber-950 border border-amber-400 dark:bg-amber-950/80 dark:hover:bg-amber-900 dark:text-amber-200 dark:border-amber-700 text-[10px] font-black rounded-xs cursor-pointer transition"
                    title="Reset workspace bersih 100% terisolasi tanpa menyentuh proyek sebelumnya"
                >
                    + Proyek Baru Bersih
                </button>
            </div>

            <!-- Dual-Mode Switcher: Retail Self-Dev vs Mitra Proyek Turnkey (Satu Pintu) -->
            <div class="inline-flex p-0.5 bg-zinc-100 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 rounded-xs font-mono text-[10px]">
                <button
                    type="button"
                    wire:click="switchEngagementMode('RETAIL_SELF_DEV')"
                    class="px-2 py-0.5 rounded-none cursor-pointer transition {{ $engagementMode === 'RETAIL_SELF_DEV' ? 'bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 font-black' : 'text-zinc-700 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-zinc-100 font-bold' }}"
                >
                    Mode Retail (Self-Dev AI)
                </button>
                <button
                    type="button"
                    wire:click="switchEngagementMode('MITRA_TURNKEY_PROJECT')"
                    class="px-2 py-0.5 rounded-none cursor-pointer transition {{ $engagementMode === 'MITRA_TURNKEY_PROJECT' ? 'bg-emerald-600 text-white dark:bg-emerald-500 dark:text-zinc-950 font-black' : 'text-zinc-700 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-zinc-100 font-bold' }}"
                >
                    Mode Mitra Proyek (Kontrak &amp; BAST)
                </button>
            </div>

            <!-- v8.0 Adaptive Dual-Persona UX Toggle (Business Storyboard vs Architect Pro) -->
            <div class="inline-flex p-0.5 bg-zinc-100 dark:bg-zinc-950 border border-emerald-500/70 dark:border-emerald-700 rounded-xs font-mono text-[10px]">
                <button
                    type="button"
                    wire:click="switchPersonaMode('BUSINESS_WIZARD')"
                    class="px-2 py-0.5 rounded-none cursor-pointer transition {{ $personaMode === 'BUSINESS_WIZARD' ? 'bg-emerald-600 text-white dark:bg-emerald-400 dark:text-zinc-950 font-black' : 'text-zinc-800 dark:text-zinc-300 font-bold' }}"
                >
                    🧭 Mode Bisnis / Awam
                </button>
                <button
                    type="button"
                    wire:click="switchPersonaMode('ARCHITECT_PRO')"
                    class="px-2 py-0.5 rounded-none cursor-pointer transition {{ $personaMode === 'ARCHITECT_PRO' ? 'bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 font-black' : 'text-zinc-800 dark:text-zinc-300 font-bold' }}"
                >
                    ⚡ Mode Arsitek Pro
                </button>
            </div>

            <!-- v8.0 Tier-1 Jurisdiction & RTL Output Locale Selector -->
            <select
                wire:change="selectOutputLocale($event.target.value)"
                aria-label="Bahasa & Regulasi Output Blueprint"
                class="px-2 py-1 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 font-mono text-[10px] font-black rounded-none"
            >
                @foreach([
                    'id' => '🇮🇩 ID • UU PDP / PSAK',
                    'en' => '🇺🇸 EN • SOC2 / ASC 958',
                    'ar' => '🇸🇦 AR • RTL / ZATCA',
                    'ja' => '🇯🇵 JA • APPI / JCT',
                    'zh-CN' => '🇨🇳 ZH • PIPL / Fapiao',
                    'de' => '🇩🇪 DE • GDPR / GoBD',
                    'es' => '🇪🇸 ES • GDPR / CFDI',
                ] as $locCode => $locLabel)
                    <option value="{{ $locCode }}" @selected($outputLocale === $locCode)>{{ $locLabel }}</option>
                @endforeach
            </select>
        </div>

        <!-- Screen 3: Blueprint Doctor + Scope Hash + Mitra RAB Badge -->
        <div class="flex flex-wrap items-center gap-1.5 font-mono text-[11px]">
            <div class="hidden xl:flex items-center gap-1.5 px-2 py-1 bg-zinc-100 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 rounded-xs tabular-nums text-[10px]">
                <span class="text-zinc-600 dark:text-zinc-400">Scope Hash:</span>
                <code class="font-black text-zinc-950 dark:text-emerald-300">{{ $commercialQuotation['scope_hash_short'] ?? '-' }}</code>
            </div>
            @if($engagementMode === 'MITRA_TURNKEY_PROJECT')
                <button
                    type="button"
                    wire:click="$set('centerTab', 'commercial_mitra')"
                    class="px-2 py-1 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/90 dark:text-emerald-200 dark:border-emerald-700 rounded-xs font-black tabular-nums text-[10px] cursor-pointer"
                >
                    RAB: {{ $commercialQuotation['formatted_quotation_idr'] ?? 'Rp 0' }} &bull; {{ $commercialQuotation['timeline_days'] ?? 0 }} Hari
                </button>
            @endif
            <div class="flex items-center gap-1.5 px-2.5 py-1 border rounded-xs tabular-nums {{ $isZeroAssumptionReady ? 'bg-emerald-100 text-emerald-950 border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700' : 'bg-amber-100 text-amber-950 border-amber-400 dark:bg-amber-950/80 dark:text-amber-200 dark:border-amber-700' }}">
                <span class="font-black">Determinism Score: {{ $detScore }}%</span>
                <span>{{ $isZeroAssumptionReady ? 'Zero-Assumption Ready ✅' : '⚠️ Celah Asumsi Terdeteksi' }}</span>
                @if(!empty($doctor['one_click_auto_fix']['has_fixes']))
                    <button
                        type="button"
                        wire:click="applyDoctorAutoFix"
                        class="ml-1 px-1.5 py-0.5 bg-zinc-950 text-white dark:bg-amber-400 dark:text-zinc-950 font-black text-[10px] rounded-none cursor-pointer hover:opacity-90"
                    >
                        + Buat Otomatis
                    </button>
                @endif
            </div>

            <button
                type="button"
                wire:click="$set('showWizardModal', true)"
                class="px-2.5 py-1 bg-zinc-100 hover:bg-zinc-200 text-zinc-950 border border-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-100 dark:border-zinc-700 font-bold rounded-xs cursor-pointer transition"
            >
                ⚡ 60s Wizard
            </button>

            <button
                type="button"
                wire:click="$set('showSlopDiffModal', true)"
                class="px-2.5 py-1 bg-zinc-100 hover:bg-zinc-200 text-zinc-950 border border-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-100 dark:border-zinc-700 font-bold rounded-xs cursor-pointer transition"
            >
                ⚖️ Slop Diff
            </button>

            <button
                type="button"
                wire:click="$set('rightDockMode', 'mcp_json')"
                class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:text-zinc-950 font-black rounded-xs cursor-pointer transition"
            >
                🔌 Export / MCP
            </button>

            @if($slug)
                <a
                    href="{{ route('blueprint.show', $slug) }}"
                    class="px-2.5 py-1 bg-white hover:bg-zinc-100 text-zinc-950 border border-zinc-300 dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 font-bold rounded-xs transition"
                >
                    📄 Dokumen PRD
                </a>
            @endif

            <button
                type="button"
                onclick="window.toggleStudioTheme()"
                class="px-2 py-1 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 border border-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-700 font-bold rounded-xs cursor-pointer"
                title="Switch Dark / Light Theme"
            >
                ◐ THEME
            </button>
        </div>
    </header>

    <!-- =================================================================== -->
    <!-- END-TO-END 5-STEP SATU PINTU CLIENT JOURNEY STEPPER                 -->
    <!-- =================================================================== -->
    <div class="shrink-0 bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 px-3 py-1 flex items-center justify-between gap-2 overflow-x-auto font-mono text-[10px]">
        <div class="flex items-center gap-1.5 whitespace-nowrap">
            <span class="font-black text-emerald-900 dark:text-emerald-400 uppercase">ALUR SATU PINTU:</span>
            <a href="/pricing" class="px-1.5 py-0.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 hover:border-emerald-500">01. Pilih Paket &amp; Sektor</a>
            <span class="text-zinc-400">&rarr;</span>
            <span class="px-1.5 py-0.5 bg-zinc-950 text-white dark:bg-emerald-500 dark:text-zinc-950 font-black">02. Rakit 3-Pane Studio [AKTIF]</span>
            <span class="text-zinc-400">&rarr;</span>
            <button type="button" wire:click="$set('centerTab', 'commercial_mitra')" class="px-1.5 py-0.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 hover:border-emerald-500 cursor-pointer">03. Kunci SPK SHA-256 / Lisensi</button>
            <span class="text-zinc-400">&rarr;</span>
            <button type="button" wire:click="$set('rightDockMode', 'mcp_json')" class="px-1.5 py-0.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 hover:border-emerald-500 cursor-pointer">04. Live 7-Fase MCP ({{ $verifiedCount }}/7)</button>
            <span class="text-zinc-400">&rarr;</span>
            <button type="button" wire:click="$set('centerTab', 'commercial_mitra')" class="px-1.5 py-0.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 hover:border-emerald-500 cursor-pointer">05. UAT ({{ $uatChecklist['progress_percent'] ?? 0 }}%) &amp; BAST</button>
        </div>
        <a href="/customer/dashboard" class="whitespace-nowrap px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 font-black hover:opacity-90">
            Portal Klien &rarr;
        </a>
    </div>

    <!-- =================================================================== -->
    <!-- CROSS-STAGE LIVE CAUSALITY RIBBON (v7.0 — 15 PILLAR FREEZE)         -->
    <!-- =================================================================== -->
    <div class="shrink-0 bg-emerald-50/70 dark:bg-zinc-900/90 border-b border-emerald-300 dark:border-zinc-800 px-3 py-1 flex items-center justify-between gap-2 overflow-x-auto font-mono text-[10px] tabular-nums">
        <div class="flex items-center gap-1.5 whitespace-nowrap">
            <span class="px-1.5 py-0.5 bg-zinc-950 text-white dark:bg-emerald-400 dark:text-zinc-950 font-black">RANTAI KAUSAL v7.0:</span>
            <span class="text-zinc-800 dark:text-zinc-200 font-bold">{{ count($enterpriseMenuMatrix['v1_mvp_menus'] ?? []) }}/25 Menu</span>
            <span class="text-emerald-700 dark:text-emerald-400">&rarr;</span>
            <span class="text-zinc-800 dark:text-zinc-200 font-bold">{{ $hwContract['active_bridges_count'] ?? 1 }} HW Bridge &bull; {{ count($wsContract['channels'] ?? []) }} WS Zero-Poll</span>
            <span class="text-emerald-700 dark:text-emerald-400">&rarr;</span>
            <span class="text-zinc-800 dark:text-zinc-200 font-bold">{{ count($tables) }} ULID PG16 &bull; {{ count($subParts ?? []) }} DAG Parts</span>
            <span class="text-emerald-700 dark:text-emerald-400">&rarr;</span>
            <span class="text-zinc-800 dark:text-zinc-200 font-bold">{{ $sopContract['role_count'] ?? 4 }} Role SOP</span>
            <span class="text-emerald-700 dark:text-emerald-400">&rarr;</span>
            <span class="text-emerald-900 dark:text-emerald-300 font-black">{{ $commercialQuotation['formatted_quotation_idr'] ?? 'Rp 0' }} ({{ $commercialQuotation['scope_hash_short'] ?? '-' }})</span>
        </div>
        <button type="button" wire:click="$set('centerTab', 'runtime_sop_v7')" class="whitespace-nowrap px-2 py-0.5 bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 font-black cursor-pointer">
            15 Pilar Freeze (100%) &rarr;
        </button>
    </div>

    <!-- Optional Blueprint Doctor Warning Banner when Assumption Gap Exists -->
    @if(!empty($doctor['findings']))
        <div class="shrink-0 bg-amber-100 dark:bg-amber-950/90 border-b border-amber-400 dark:border-amber-700 px-3 py-1.5 flex flex-wrap items-center justify-between gap-2 text-xs text-amber-950 dark:text-amber-100">
            <div class="font-mono font-bold">
                {{ $doctor['findings'][0]['title']['id'] ?? 'Peringatan Blueprint Doctor' }} —
                <span class="font-sans font-normal">{{ $doctor['findings'][0]['message']['id'] ?? '' }}</span>
            </div>
            @if(!empty($doctor['one_click_auto_fix']['has_fixes']))
                <button
                    type="button"
                    wire:click="applyDoctorAutoFix"
                    class="px-2.5 py-0.5 bg-zinc-950 text-white dark:bg-amber-300 dark:text-zinc-950 font-mono text-[10px] font-black uppercase rounded-none cursor-pointer"
                >
                    [ + Buat Otomatis &amp; Kunci 100% ]
                </button>
            @endif
        </div>
    @endif

    <!-- =================================================================== -->
    <!-- MAIN 3-PANE IDE WORKBENCH (LEFT 3 | CENTER 5 | RIGHT 4)             -->
    <!-- =================================================================== -->
    <main class="flex-1 grid grid-cols-1 lg:grid-cols-12 lg:overflow-hidden divide-y lg:divide-y-0 lg:divide-x divide-zinc-300 dark:divide-zinc-800 select-text">

        <!-- =============================================================== -->
        <!-- LEFT PANE (col-span-3): ARCHITECTURE EXPLORER TREE              -->
        <!-- =============================================================== -->
        <aside class="lg:col-span-3 bg-white dark:bg-zinc-950 flex flex-col lg:overflow-y-auto studio-scroll p-3 space-y-3">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-1.5">
                <span class="font-mono text-[10px] font-black uppercase tracking-widest text-zinc-700 dark:text-zinc-400">
                    ARCHITECTURE EXPLORER
                </span>
                <span class="font-mono text-[10px] font-bold text-emerald-800 dark:text-emerald-400 tabular-nums">
                    {{ count($selectedPrimitives) }}/12 Primitives
                </span>
            </div>

            <!-- 1. Stack Preset Selector -->
            <div class="space-y-1">
                <label class="block font-mono text-[10px] font-black uppercase text-zinc-800 dark:text-zinc-300">
                    &gt; 1. Stack Topology Adapter (9)
                </label>
                <select
                    wire:change="selectStack($event.target.value)"
                    class="w-full px-2 py-1.5 text-xs font-mono bg-zinc-50 dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs"
                >
                    @foreach(($catalog['stack_presets'] ?? []) as $sp)
                        <option value="{{ $sp['code'] }}" @selected($stackCode === $sp['code'])>
                            [{{ $sp['short_code'] }}] {{ $sp['topology'] }} — {{ $sp['name']['id'] ?? $sp['code'] }}
                        </option>
                    @endforeach
                </select>
                @php $sz = $sizingProfiles[$stackCode] ?? null; @endphp
                @if($sz)
                    <div class="p-2 bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 rounded-xs text-[10px] font-mono text-zinc-700 dark:text-zinc-300 space-y-0.5">
                        <div>Speed: <strong class="text-zinc-950 dark:text-zinc-100">{{ $sz['build_speed'] }}</strong></div>
                        <div>Cap  : <strong class="text-emerald-800 dark:text-emerald-300 tabular-nums">{{ $sz['concurrency'] }}</strong></div>
                        <div>VPS  : {{ $sz['coolify_sizing'] }}</div>
                    </div>
                @endif
            </div>

            <!-- 2. Functional Sector Selector -->
            <div class="space-y-1">
                <label class="block font-mono text-[10px] font-black uppercase text-zinc-800 dark:text-zinc-300">
                    &gt; 2. Functional Sector (12)
                </label>
                <select
                    wire:change="selectSector($event.target.value)"
                    class="w-full px-2 py-1.5 text-xs font-mono bg-zinc-50 dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs"
                >
                    @foreach(($catalog['sectors'] ?? []) as $sec)
                        <option value="{{ $sec['code'] }}" @selected($sectorCode === $sec['code'])>
                            {{ sprintf('%02d', $sec['sector_number'] ?? 1) }}. {{ $sec['name']['id'] ?? $sec['code'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Reactive Composable Primitives Checkboxes -->
            <div class="space-y-1.5">
                <div class="font-mono text-[10px] font-black uppercase text-zinc-800 dark:text-zinc-300">
                    v 3. Active Composable Primitives
                </div>
                <div class="space-y-1">
                    @foreach(($catalog['primitives'] ?? []) as $prim)
                        @php
                            $pCode = $prim['code'];
                            $isChecked = in_array($pCode, $selectedPrimitives, true);
                            $isMandatory = in_array($pCode, $activeSectorObj['mandatory_primitives'] ?? [], true);
                        @endphp
                        <label class="flex items-start gap-2 p-1.5 border rounded-xs cursor-pointer transition {{ $isChecked ? 'bg-emerald-50/90 border-emerald-400 text-zinc-950 dark:bg-emerald-950/40 dark:border-emerald-700 dark:text-zinc-100' : 'bg-zinc-50 hover:bg-zinc-100 border-zinc-200 text-zinc-800 dark:bg-zinc-900/50 dark:hover:bg-zinc-900 dark:border-zinc-800 dark:text-zinc-300' }}">
                            <input
                                type="checkbox"
                                wire:click="togglePrimitive('{{ $pCode }}')"
                                @checked($isChecked)
                                class="mt-0.5 rounded-none border-zinc-400 text-emerald-600 focus:ring-emerald-500"
                            />
                            <div class="flex-1 min-w-0">
                                <div class="font-mono text-[11px] font-bold leading-tight truncate">
                                    {{ $prim['name']['id'] ?? $pCode }}
                                </div>
                                <div class="flex items-center gap-1 mt-0.5 font-mono text-[9px]">
                                    <span class="text-zinc-600 dark:text-zinc-400 truncate">{{ str_replace('PRIMITIVE_', '', $pCode) }}</span>
                                    @if($isMandatory)
                                        <span class="px-1 bg-zinc-900 text-white dark:bg-zinc-200 dark:text-zinc-950 font-black">CORE</span>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- 4. Anti-Slop Shield & 5. AI RAG + 21 Native Tools Summary -->
            <div class="p-2.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs space-y-1 font-mono text-[10px]">
                <div class="font-black text-zinc-950 dark:text-zinc-100 uppercase">&gt; 4. Anti-Slop &amp; Day-2 Shield</div>
                <div class="text-emerald-900 dark:text-emerald-300">✓ ULID CHAR(26) + Keyset O(1)</div>
                <div class="text-emerald-900 dark:text-emerald-300">✓ 5 Historical *_snapshot Columns</div>
                <div class="text-emerald-900 dark:text-emerald-300">✓ Zero CDN / Zero Capsule / Local SVG QR</div>
                <div class="font-black text-zinc-950 dark:text-zinc-100 uppercase pt-1">&gt; 5. 4-Pillar AI &amp; v7.0 15-Pillar Freeze</div>
                <div class="text-emerald-900 dark:text-emerald-300">✓ 25 Sub-Menu L1–L4 + 5 Enterprise Glue</div>
                <div class="text-emerald-900 dark:text-emerald-300">✓ &lt;30ms HW Wedge + Zero-Poll WS + 0% N+1</div>
                <div class="text-emerald-900 dark:text-emerald-300">✓ SYNTAX_LOCK_2026 + Role SOP BAST</div>
                <div class="text-emerald-900 dark:text-emerald-300">✓ AgentShield 5-Layer + Dual-Key Co-Design</div>
                <div class="text-zinc-700 dark:text-zinc-300">RAG pgvector + CAG + KAG + 36 Tools + 3-Sheet SHA-256 Audit</div>
            </div>
        </aside>

        <!-- =============================================================== -->
        <!-- CENTER CANVAS (col-span-5): INTERACTIVE BLUEPRINT WORKBENCH     -->
        <!-- =============================================================== -->
        <section class="lg:col-span-5 bg-zinc-50 dark:bg-zinc-950 flex flex-col lg:overflow-hidden" @if(($jurisdictionProfile['direction'] ?? 'ltr') === 'rtl') dir="rtl" @endif>
            <!-- Center Tabs -->
            <div class="shrink-0 bg-white dark:bg-zinc-900 border-b border-zinc-300 dark:border-zinc-800 px-2.5 py-2 flex flex-wrap gap-1 font-mono text-[11px]">
                @foreach([
                    'schema' => '1. Schema & Locks',
                    'states' => '2. State Machine',
                    'ui_print' => '3. 36 Visual UI & Print',
                    'decisions' => '4. Decision Matrix',
                    'delta_verify' => '5. Delta v1.1 & Verify',
                    'commercial_mitra' => '6. Kontrak, RAB & BAST',
                    'menus_glue' => '7. L1–L4 Menu (25) & 5 Glue',
                    'runtime_sop_v7' => '8. ⚡ 5 Runtime, SOP & 15 Pilar',
                    'inclusive_security_v8' => '9. 🛡️ CVE, White-Label & IP',
                    'codesign_room' => '10. 🤝 Co-Design & Sign-Off',
                    'agentshield_tasks' => '11. 🧱 AgentShield & 5-Sprint',
                ] as $tabKey => $tabLabel)
                    <button
                        type="button"
                        wire:click="$set('centerTab', '{{ $tabKey }}')"
                        class="px-2.5 py-1 border rounded-xs cursor-pointer transition {{ $centerTab === $tabKey ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-500 dark:text-zinc-950 dark:border-emerald-400 font-black' : ($tabKey === 'commercial_mitra' && $engagementMode === 'MITRA_TURNKEY_PROJECT' ? 'bg-emerald-100 text-emerald-950 border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-black' : 'bg-zinc-100 hover:bg-zinc-200 text-zinc-900 border-zinc-300 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-700 font-bold') }}"
                    >
                        {{ $tabLabel }}
                    </button>
                @endforeach
            </div>

            <!-- Scrollable Canvas Body -->
            <div class="flex-1 lg:overflow-y-auto studio-scroll p-3 space-y-3">

                <!-- v8.0 ADAPTIVE DUAL-PERSONA UX: 5-STEP ZERO-JARGON BUSINESS STORYBOARD WIZARD -->
                @if($personaMode === 'BUSINESS_WIZARD')
                    <div class="p-3.5 bg-white dark:bg-zinc-900 border-2 border-emerald-600 dark:border-emerald-500 rounded-none space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2 pb-2 border-b border-zinc-200 dark:border-zinc-800">
                            <div>
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[10px] font-black uppercase">
                                    🧭 MODE BISNIS / AWAM (ZERO-JARGON 5-STEP STORYBOARD)
                                </span>
                                <div class="text-xs font-black text-zinc-950 dark:text-zinc-100 mt-1">
                                    {{ $storyboardWizard['headline'] ?? 'Rancang Sistem Bisnis Anda dalam 5 Langkah Bahasa Manusia' }}
                                </div>
                            </div>
                            <button
                                type="button"
                                wire:click="switchPersonaMode('ARCHITECT_PRO')"
                                class="px-2 py-1 bg-zinc-950 text-white dark:bg-zinc-800 dark:text-emerald-300 font-mono text-[10px] font-black rounded-none cursor-pointer"
                            >
                                Beralih ke Mode Arsitek Teknis &rarr;
                            </button>
                        </div>

                        <div class="grid grid-cols-5 gap-1 font-mono text-[10px]">
                            @foreach(($storyboardWizard['steps'] ?? []) as $sbStep)
                                @php $stepNum = (int) ($sbStep['step'] ?? 1); @endphp
                                <button
                                    type="button"
                                    wire:click="$set('storyboardStep', {{ $stepNum }})"
                                    class="p-1.5 border text-left rounded-none cursor-pointer {{ $storyboardStep === $stepNum ? 'bg-zinc-950 text-emerald-400 border-zinc-950 dark:bg-emerald-500 dark:text-zinc-950 dark:border-emerald-400 font-black' : 'bg-zinc-50 text-zinc-800 border-zinc-300 dark:bg-zinc-950 dark:text-zinc-300 dark:border-zinc-800 font-bold' }}"
                                >
                                    0{{ $stepNum }}. {{ $sbStep['short_label'] ?? $sbStep['title'] ?? '' }}
                                </button>
                            @endforeach
                        </div>

                        @php
                            $currentSb = collect($storyboardWizard['steps'] ?? [])->firstWhere('step', $storyboardStep) ?? ($storyboardWizard['steps'][0] ?? []);
                            $availVariants = $visualUiContract['available_variants'] ?? [];
                        @endphp
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 space-y-2">
                            <div class="text-xs font-black text-zinc-950 dark:text-zinc-100">{{ $currentSb['question'] ?? '' }}</div>
                            <p class="text-[11px] text-zinc-700 dark:text-zinc-300">{{ $currentSb['helper'] ?? '' }}</p>

                            @if($storyboardStep === 1)
                                <select wire:change="selectSector($event.target.value)" class="w-full px-2.5 py-2 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-xs font-bold font-mono rounded-none">
                                    @foreach(($catalog['sectors'] ?? []) as $secItem)
                                        <option value="{{ $secItem['code'] }}" @selected($sectorCode === $secItem['code'])>
                                            {{ $secItem['name']['id'] ?? $secItem['code'] }}
                                        </option>
                                    @endforeach
                                </select>
                            @elseif($storyboardStep === 2)
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    @foreach($availVariants as $vrItem)
                                        <button
                                            type="button"
                                            wire:click="selectUiVariant('{{ $vrItem['code'] }}', '{{ $vrItem['default_shell'] ?? '' }}')"
                                            class="p-2 text-left border rounded-none cursor-pointer {{ $selectedUiVariant === $vrItem['code'] ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-950 dark:text-white dark:border-emerald-400 font-black' : 'bg-white text-zinc-900 border-zinc-300 dark:bg-zinc-900 dark:text-zinc-200 dark:border-zinc-700 font-bold' }}"
                                        >
                                            <div class="font-mono text-[9px] text-emerald-500">{{ $vrItem['badge'] ?? '' }}</div>
                                            <div class="text-[11px] mt-0.5 leading-snug">{{ $vrItem['name']['id'] ?? $vrItem['code'] }}</div>
                                        </button>
                                    @endforeach
                                </div>
                            @elseif($storyboardStep === 3)
                                <div class="text-[11px] font-mono text-emerald-900 dark:text-emerald-300 font-bold">
                                    ✓ {{ count($selectedMenus) }}/25 Menu Operasional L1–L4 &amp; {{ count($selectedPrimitives) }} Modul Anti-Selisih Aktif (Bisa dikustom di Tab 7).
                                </div>
                            @elseif($storyboardStep === 4)
                                <div class="text-[11px] font-mono text-zinc-900 dark:text-zinc-100 font-bold">
                                    Regulasi Aktif: <strong>{{ $jurisdictionProfile['jurisdiction_code'] ?? 'ID_UU_PDP_PSAK' }}</strong> ({{ implode(' • ', $jurisdictionProfile['accounting_standards'] ?? []) }}) • Arah Layar: <strong>{{ strtoupper($jurisdictionProfile['direction'] ?? 'ltr') }}</strong>
                                </div>
                            @else
                                <div class="flex flex-wrap gap-2 font-mono text-[10px]">
                                    <button type="button" wire:click="switchEngagementMode('RETAIL_SELF_DEV')" class="px-2.5 py-1.5 bg-zinc-950 text-white font-black rounded-none cursor-pointer">
                                        ⚡ Unduh Blueprint Mandiri (Retail)
                                    </button>
                                    <button type="button" wire:click="switchEngagementMode('MITRA_TURNKEY_PROJECT')" class="px-2.5 py-1.5 bg-emerald-600 text-white font-black rounded-none cursor-pointer">
                                        🤝 Terima Beres Dikodingkan Tim ({{ $commercialQuotation['formatted_quotation_idr'] ?? 'Rp 0' }})
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- TAB 1: SCHEMA & LOCKS -->
                @if($centerTab === 'schema')
                    <div class="p-2.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xs">
                        <div class="font-mono text-[10px] font-black uppercase text-zinc-700 dark:text-zinc-400 mb-1.5">
                            + Tambah Tabel Domain Kustom (Diuji Otomatis oleh Blueprint Doctor)
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <input
                                type="text"
                                wire:model="newCustomTableName"
                                placeholder="misal: member_penalties"
                                class="flex-1 min-w-40 px-2.5 py-1 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 rounded-xs"
                            />
                            <label class="flex items-center gap-1 text-[11px] font-mono text-zinc-800 dark:text-zinc-300">
                                <input type="checkbox" wire:model="newCustomTableHasStatus" class="rounded-none" />
                                Kolom `status`
                            </label>
                            <button
                                type="button"
                                wire:click="addCustomTable"
                                class="px-2.5 py-1 bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 font-mono text-[11px] font-black rounded-xs cursor-pointer"
                            >
                                + Tambah ULID
                            </button>
                        </div>
                    </div>

                    @if(!empty($locks))
                        <div class="p-2.5 bg-red-50 dark:bg-red-950/40 border border-red-300 dark:border-red-800 rounded-xs space-y-1.5">
                            <div class="font-mono text-[10px] font-black uppercase text-red-950 dark:text-red-200">
                                🔒 Active Pessimistic Concurrency Locks ({{ count($locks) }} SELECT ... FOR UPDATE)
                            </div>
                            @foreach($locks as $lk)
                                <div class="flex items-center justify-between gap-2 text-[11px] font-mono">
                                    <span class="font-bold text-zinc-950 dark:text-zinc-100">{{ $lk['operation'] ?? ($lk['resource'] ?? 'Critical Mutation') }}</span>
                                    <span class="px-1.5 py-0.5 bg-red-200 text-red-950 dark:bg-red-900 dark:text-red-100 font-black text-[9px] rounded-none">
                                        {{ $lk['mechanism'] ?? 'SELECT ... FOR UPDATE' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                        @foreach($tables as $tbl)
                            <div class="p-2.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs">
                                <div class="flex items-center justify-between gap-1 border-b border-zinc-200 dark:border-zinc-800 pb-1 mb-1.5">
                                    <span class="font-mono text-xs font-black text-zinc-950 dark:text-zinc-100 truncate">
                                        {{ $tbl['table'] ?? 'table' }}
                                    </span>
                                    <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[9px] font-black">
                                        {{ $tbl['deletion_policy'] ?? 'ULID' }}
                                    </span>
                                </div>
                                <div class="space-y-0.5 font-mono text-[10px] text-zinc-800 dark:text-zinc-300">
                                    @foreach(array_slice($tbl['columns'] ?? [], 0, 5) as $col)
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="font-bold">{{ $col['name'] ?? '' }}</span>
                                            <span class="text-zinc-600 dark:text-zinc-400 truncate max-w-32">{{ $col['type'] ?? 'VARCHAR' }}</span>
                                        </div>
                                    @endforeach
                                    @if(!empty($tbl['historical_snapshot_enforced']))
                                        <div class="pt-1 text-[9px] font-black text-emerald-900 dark:text-emerald-300">
                                            ✓ +5 Frozen *_snapshot Columns
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- TAB 2: STATE MACHINE FLOW -->
                @if($centerTab === 'states')
                    <div class="space-y-2.5">
                        @foreach($stateMachines as $sm)
                            <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-2">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="font-mono text-xs font-black text-zinc-950 dark:text-zinc-100">{{ $sm['entity'] ?? 'Entity' }}</span>
                                    <span class="font-mono text-[10px] font-bold text-emerald-900 dark:text-emerald-300">{{ $sm['enum_class'] ?? '' }}</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-1 font-mono text-[10px]">
                                    @foreach(($sm['states'] ?? []) as $idx => $st)
                                        <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 font-black text-zinc-950 dark:text-zinc-100 rounded-none">
                                            {{ $st }}
                                        </span>
                                        @if($idx < count($sm['states'] ?? []) - 1)
                                            <span class="text-emerald-700 dark:text-emerald-400 font-black">&rarr;</span>
                                        @endif
                                    @endforeach
                                </div>
                                @if(!empty($sm['notification_matrix']))
                                    <div class="pt-1.5 border-t border-zinc-200 dark:border-zinc-800 flex flex-wrap gap-1">
                                        @foreach(array_slice($sm['notification_matrix'], 0, 3) as $nm)
                                            <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[9px] font-bold rounded-none">
                                                🔔 {{ $nm['on_state'] ?? 'EVENT' }}: {{ implode(' + ', $nm['channels'] ?? []) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- TAB 3: 36 VISUAL SECTOR FRONTEND UI BLUEPRINTS & PRINT -->
                @if($centerTab === 'ui_print')
                    @php
                        $availUiVariants = $visualUiContract['available_variants'] ?? [];
                        $pickedUiObj = $visualUiContract['selected_variant'] ?? ($availUiVariants[0] ?? []);
                    @endphp
                    <div class="p-3 bg-white dark:bg-zinc-900 border-2 border-emerald-600 dark:border-emerald-500 rounded-xs space-y-2.5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <div class="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300">
                                    V8.0 VISUAL FRONTEND UI/UX BLUEPRINT (3 VARIANTS PER SECTOR = 36 BLUEPRINTS)
                                </div>
                                <div class="font-mono text-xs font-black text-zinc-950 dark:text-zinc-100">
                                    Terkunci: {{ $pickedUiObj['code'] ?? $selectedUiVariant }} &bull; Shell: {{ $visualUiContract['navigation_shell']['code'] ?? $navigationShell }}
                                </div>
                            </div>
                            <span class="px-2 py-0.5 bg-zinc-950 text-emerald-400 font-mono text-[10px] font-black">
                                DIR={{ strtoupper($jurisdictionProfile['direction'] ?? 'ltr') }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            @foreach($availUiVariants as $vOpt)
                                @php $isVarSelected = ($pickedUiObj['code'] ?? '') === ($vOpt['code'] ?? ''); @endphp
                                <button
                                    type="button"
                                    wire:click="selectUiVariant('{{ $vOpt['code'] }}', '{{ $vOpt['default_shell'] ?? '' }}')"
                                    class="p-2.5 text-left border rounded-none cursor-pointer transition {{ $isVarSelected ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-950 dark:text-white dark:border-emerald-400 font-black' : 'bg-zinc-50 hover:bg-zinc-100 text-zinc-900 border-zinc-300 dark:bg-zinc-950 dark:text-zinc-200 dark:border-zinc-800 font-bold' }}"
                                >
                                    <div class="font-mono text-[9px] uppercase {{ $isVarSelected ? 'text-emerald-400' : 'text-emerald-900 dark:text-emerald-300' }}">{{ $vOpt['badge'] ?? '' }}</div>
                                    <div class="text-[11px] leading-snug mt-0.5">{{ $vOpt['name']['id'] ?? $vOpt['code'] }}</div>
                                </button>
                            @endforeach
                        </div>

                        @if(!empty($pickedUiObj['panes']))
                            <div class="grid grid-cols-12 gap-2 pt-1">
                                @foreach($pickedUiObj['panes'] as $vp)
                                    @php
                                        $vpSpan = (int) ($vp['span'] ?? 6);
                                        $vpCol = match ($vpSpan) {
                                            2 => 'md:col-span-2', 3 => 'md:col-span-3', 4 => 'md:col-span-4',
                                            5 => 'md:col-span-5', 7 => 'md:col-span-7', 8 => 'md:col-span-8',
                                            9 => 'md:col-span-9', default => 'md:col-span-6',
                                        };
                                    @endphp
                                    <div class="col-span-12 {{ $vpCol }} p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-dashed border-zinc-400 dark:border-zinc-700 font-mono text-[10px]">
                                        <div class="font-black text-emerald-900 dark:text-emerald-300">[{{ $vp['label'] ?? 'PANE' }}]</div>
                                        <div class="mt-1 space-y-0.5 text-zinc-800 dark:text-zinc-200 font-bold">
                                            @foreach(($vp['widgets'] ?? []) as $wg)
                                                <div>• {{ $wg }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-2.5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <div class="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300">PRIMARY INDUSTRIAL UI ARCHETYPE</div>
                                <div class="font-mono text-sm font-black text-zinc-950 dark:text-zinc-100">{{ $secUi['primary_archetype'] ?? 'POS_SPLIT_TERMINAL' }}</div>
                            </div>
                            <span class="px-2 py-0.5 bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 font-mono text-[10px] font-black">
                                {{ $secUi['secondary_archetype'] ?? 'LEDGER_VIEW' }}
                            </span>
                        </div>
                        <p class="text-xs text-zinc-800 dark:text-zinc-300">{{ $secUi['headline']['id'] ?? '' }}</p>

                        <!-- 12-Col Wireframe Preview -->
                        @if(!empty($secUi['wireframe_blueprint']['panes']))
                            <div class="grid grid-cols-12 gap-2 pt-1">
                                @foreach($secUi['wireframe_blueprint']['panes'] as $pane)
                                    @php
                                        $span = (int) ($pane['span'] ?? 6);
                                        $mdColClass = match ($span) {
                                            2 => 'md:col-span-2',
                                            3 => 'md:col-span-3',
                                            4 => 'md:col-span-4',
                                            5 => 'md:col-span-5',
                                            7 => 'md:col-span-7',
                                            8 => 'md:col-span-8',
                                            9 => 'md:col-span-9',
                                            10 => 'md:col-span-10',
                                            12 => 'md:col-span-12',
                                            default => 'md:col-span-6',
                                        };
                                    @endphp
                                    <div class="col-span-12 {{ $mdColClass }} p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-none">
                                        <div class="font-mono text-[10px] font-black text-zinc-950 dark:text-zinc-100 mb-1">{{ $pane['title'] ?? '' }}</div>
                                        <ul class="space-y-0.5 text-[10px] font-mono text-zinc-700 dark:text-zinc-300">
                                            @foreach(($pane['highlights'] ?? []) as $hl)
                                                <li>• {{ $hl }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-1.5 font-mono text-xs">
                        <div class="text-[10px] font-black uppercase text-zinc-700 dark:text-zinc-400">
                            LAYER 1: THERMAL ESC/POS (58/80MM) &amp; OFFICIAL A4 LOCAL SVG QR
                        </div>
                        <div class="font-bold text-zinc-950 dark:text-zinc-100">
                            Mode: {{ $printSpec['thermal_printer_spec']['mode'] ?? 'REQUIRED_80MM' }}
                        </div>
                        <div class="text-[11px] font-sans text-zinc-700 dark:text-zinc-300">
                            Thermal: {{ implode(', ', $printSpec['thermal_printer_spec']['artifacts'] ?? []) }} |
                            A4 Resmi: {{ implode(', ', $printSpec['official_a4_pdf_spec']['artifacts'] ?? []) }}
                        </div>
                    </div>
                @endif

                <!-- TAB 4: DECISION & FORMULA MATRIX (ANTI-NESTED IF-ELSE) -->
                @if($centerTab === 'decisions')
                    <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-[11px] font-black uppercase text-zinc-950 dark:text-zinc-100">
                                Deterministic Decision Matrix Builder (Zero Nested If-Else)
                            </span>
                            <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[9px] font-black">
                                PHP 8.4+ match()
                            </span>
                        </div>

                        <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-800">
                            <table class="w-full text-[10px] font-mono border-collapse">
                                <thead class="bg-zinc-100 dark:bg-zinc-800 text-zinc-950 dark:text-zinc-100">
                                    <tr>
                                        <th class="p-1.5 text-left border-b border-zinc-300 dark:border-zinc-700">ID</th>
                                        <th class="p-1.5 text-left border-b border-zinc-300 dark:border-zinc-700">Condition A + B + C</th>
                                        <th class="p-1.5 text-left border-b border-zinc-300 dark:border-zinc-700">Action &amp; Formula DECIMAL(19,4)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                    @foreach($decisionRules as $dr)
                                        <tr class="odd:bg-white dark:odd:bg-zinc-900 even:bg-zinc-50 dark:even:bg-zinc-950/60">
                                            <td class="p-1.5 font-black text-emerald-900 dark:text-emerald-300 whitespace-nowrap">{{ $dr['rule_id'] ?? '' }}</td>
                                            <td class="p-1.5 text-zinc-800 dark:text-zinc-300">
                                                {{ $dr['condition_a'] ?? '' }} <span class="text-emerald-700 dark:text-emerald-400">AND</span> {{ $dr['condition_b'] ?? '' }}
                                            </td>
                                            <td class="p-1.5 text-zinc-950 dark:text-zinc-100">
                                                <div class="font-black">{{ $dr['action_code'] ?? '' }}</div>
                                                <div class="text-zinc-600 dark:text-zinc-400">{{ $dr['exact_formula'] ?? '' }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Add Custom Truth-Table Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-1.5 pt-1">
                            <input type="text" wire:model="newRuleCondA" placeholder="Kondisi A (mis: qty >= 50)" class="px-2 py-1 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xs" />
                            <input type="text" wire:model="newRuleCondB" placeholder="Kondisi B (mis: tier == 'VIP')" class="px-2 py-1 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xs" />
                            <input type="text" wire:model="newRuleAction" placeholder="Action (mis: APPLY_TIER_BONUS)" class="px-2 py-1 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xs" />
                            <input type="text" wire:model="newRuleFormula" placeholder="Rumus (mis: bonus = ROUND_HALF_UP(sub * 0.04, 4))" class="px-2 py-1 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xs" />
                        </div>
                        <button
                            type="button"
                            wire:click="addDecisionRule"
                            class="px-3 py-1 bg-zinc-950 text-white dark:bg-emerald-500 dark:text-zinc-950 font-mono text-[11px] font-black rounded-xs cursor-pointer"
                        >
                            + Tambah Baris Matriks Keputusan
                        </button>

                        <pre class="p-2.5 bg-zinc-950 text-zinc-100 border border-zinc-800 rounded-xs text-[10px] font-mono overflow-x-auto max-h-48 studio-scroll">{{ $compiledDecisionPhp }}</pre>
                    </div>
                @endif

                <!-- TAB 5: ITERATIVE DELTA CR (v1.0 -> v1.1) & EXECUTION DRIFT TERMINAL -->
                @if($centerTab === 'delta_verify')
                    <!-- Feature #1: Day-30 Iterative Change Request Generator -->
                    <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-[11px] font-black uppercase text-zinc-950 dark:text-zinc-100">
                                1. Day-30 Iterative Change Request ({{ $currentVersion }} &rarr; Next Minor)
                            </span>
                            <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[9px] font-black">
                                SURGICAL DELTA
                            </span>
                        </div>
                        <input
                            type="text"
                            wire:model="deltaTitle"
                            placeholder="Judul CR: Tambahkan Komisi Sales Bertingkat & Split-Payment..."
                            class="w-full px-2.5 py-1.5 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xs"
                        />
                        <textarea
                            wire:model="deltaSummary"
                            rows="2"
                            placeholder="Jelaskan perubahan fitur bulan ke-2+ tanpa merusak tabel produksi v1.0..."
                            class="w-full px-2.5 py-1.5 text-xs font-sans bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xs"
                        ></textarea>
                        <button
                            type="button"
                            wire:click="generateDeltaPrompt"
                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:text-zinc-950 font-mono text-[11px] font-black rounded-xs cursor-pointer"
                        >
                            ⚡ Generate Surgical Delta Prompt (ALTER/CREATE ULID + Regression Test)
                        </button>

                        @if(!empty($latestDeltaResult['surgical_delta_prompt']))
                            @if(!empty($latestDeltaResult['paid_change_order']))
                                @php $pco = $latestDeltaResult['paid_change_order']; @endphp
                                <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-400 dark:border-emerald-700 rounded-xs flex flex-wrap items-center justify-between gap-2 font-mono text-[11px]">
                                    <div>
                                        <div class="font-black text-emerald-950 dark:text-emerald-200">
                                            PAID CHANGE-ORDER ADDENDUM ({{ $latestDeltaResult['from_version'] }} &rarr; {{ $latestDeltaResult['to_version'] }})
                                        </div>
                                        <div class="text-zinc-800 dark:text-zinc-300 tabular-nums">
                                            +{{ $pco['delta_man_hours'] }} Jam Kerja &bull; +{{ $pco['delta_working_days'] }} Hari Kerja &bull; <strong>{{ $pco['formatted_delta_quotation'] }}</strong>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="approveStudioAddendum"
                                        class="px-2.5 py-1 bg-zinc-950 text-white dark:bg-emerald-400 dark:text-zinc-950 font-black text-[10px] rounded-none cursor-pointer"
                                    >
                                        [ Setujui Addendum &amp; Bayar Invoice Delta ]
                                    </button>
                                </div>
                            @endif
                            @if(!empty($latestAddendumResult['midtrans_order_id']))
                                <div class="p-2 bg-zinc-950 text-emerald-300 border border-emerald-600 font-mono text-[10px]">
                                    ✓ Invoice Addendum Aktif: <strong>{{ $latestAddendumResult['midtrans_order_id'] }}</strong> ({{ $latestAddendumResult['formatted_delta_quotation'] }}) — Siap dibayar via Portal Klien / Midtrans Snap!
                                </div>
                            @endif
                            <pre class="p-2.5 bg-zinc-950 text-emerald-200 border border-zinc-800 rounded-xs text-[10px] font-mono overflow-x-auto max-h-52 studio-scroll">{{ $latestDeltaResult['surgical_delta_prompt'] }}</pre>
                        @endif
                    </div>

                    <!-- Screen 5: Execution Tracker & Drift Audit Terminal -->
                    <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-2">
                        <div class="font-mono text-[11px] font-black uppercase text-zinc-950 dark:text-zinc-100">
                            2. Execution Tracker &amp; Drift Audit Terminal (./verify-blueprint.sh)
                        </div>
                        <div class="grid grid-cols-2 gap-1.5 font-mono text-[10px]">
                            @foreach([
                                'database_ulid_strict_pgsql' => 'Database ULID & Strict PGSQL',
                                'zero_external_cdn_and_popups' => 'Zero External CDN & Native Popups',
                                'anti_slop_ui_geometry' => 'Anti-Slop UI Geometry',
                                'four_pillar_ai_and_audit_sync' => '4-Pillar AI & Audit Sync',
                            ] as $bKey => $bTitle)
                                @php $bVal = $driftBadges[$bKey] ?? '100% Pass'; @endphp
                                <div class="p-2 border rounded-none flex items-center justify-between {{ str_contains($bVal, '100%') ? 'bg-emerald-50 border-emerald-400 text-emerald-950 dark:bg-emerald-950/50 dark:border-emerald-700 dark:text-emerald-200' : 'bg-amber-50 border-amber-400 text-amber-950 dark:bg-amber-950/50 dark:border-amber-700 dark:text-amber-200' }}">
                                    <span class="font-bold truncate">{{ $bTitle }}</span>
                                    <span class="font-black whitespace-nowrap ml-1">{{ $bVal }}</span>
                                </div>
                            @endforeach
                        </div>
                        <textarea
                            wire:model="verifyTerminalInput"
                            rows="2"
                            placeholder="Tempel log ./verify-blueprint.sh atau potongan kode di sini untuk memverifikasi kepatuhan..."
                            class="w-full px-2.5 py-1.5 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xs"
                        ></textarea>
                        <button
                            type="button"
                            wire:click="runDriftVerification"
                            class="px-3 py-1 bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 font-mono text-[11px] font-black rounded-xs cursor-pointer"
                        >
                            🔍 Verifikasi Kepatuhan Proyek
                        </button>
                    </div>

                    <!-- Phase 7 Red-Team Vectors Summary -->
                    <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-1.5">
                        <div class="font-mono text-[10px] font-black uppercase text-red-800 dark:text-red-300">
                            🛡️ Phase 7 Automated Security Red-Team Suite ({{ count($redTeamVectors) }} Vectors)
                        </div>
                        @foreach($redTeamVectors as $rv)
                            <div class="text-[11px] text-zinc-800 dark:text-zinc-300">
                                <strong class="font-mono text-zinc-950 dark:text-zinc-100">{{ $rv['vector_id'] }}:</strong> {{ $rv['title'] }}
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- TAB 6: MITRA COMMERCIAL BRIDGE (RAB, WBS, SPK SHA-256, MILESTONE & DIGITAL BAST) -->
                @if($centerTab === 'commercial_mitra')
                    <!-- 1. Algorithmic Scope-to-Quotation & WBS Estimator -->
                    <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-2">
                            <div>
                                <div class="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300">
                                    1. ALGORITHMIC SCOPE-TO-QUOTATION &amp; WBS ESTIMATOR (ANTI-UNDERPRICING)
                                </div>
                                <div class="text-xs font-bold text-zinc-950 dark:text-zinc-100">
                                    Estimasi Kontrak &amp; Jadwal Pengerjaan Tim Mitra Proyek (Satu Pintu)
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 font-mono text-[11px] font-black tabular-nums rounded-none">
                                Durasi: {{ $commercialQuotation['timeline_days'] ?? 0 }} Hari Kerja ({{ $commercialQuotation['stack_multiplier'] ?? 1 }}x Stack)
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 font-mono tabular-nums">
                            <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 rounded-xs">
                                <div class="text-[10px] font-bold uppercase text-zinc-600 dark:text-zinc-400">Total Beban Kerja</div>
                                <div class="text-base font-black text-zinc-950 dark:text-zinc-100 mt-0.5">{{ $commercialQuotation['man_hours'] ?? 0 }} Jam</div>
                                <div class="text-[9px] text-zinc-600 dark:text-zinc-400">{{ $commercialQuotation['formatted_man_hour_rate'] ?? '' }}</div>
                            </div>
                            <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-400 dark:border-emerald-700 rounded-xs">
                                <div class="text-[10px] font-bold uppercase text-emerald-900 dark:text-emerald-300">Nilai Proyek (RAB)</div>
                                <div class="text-base font-black text-emerald-950 dark:text-emerald-200 mt-0.5">{{ $commercialQuotation['formatted_quotation_idr'] ?? 'Rp 0' }}</div>
                                <div class="text-[9px] text-emerald-900 dark:text-emerald-300">{{ $commercialQuotation['counts']['tables'] ?? 0 }} Tabel &bull; {{ $commercialQuotation['counts']['states'] ?? 0 }} State &bull; {{ $commercialQuotation['counts']['locks'] ?? 0 }} Lock</div>
                            </div>
                            <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 rounded-xs">
                                <div class="text-[10px] font-bold uppercase text-zinc-600 dark:text-zinc-400">Kunci Scope SPK</div>
                                <div class="text-[11px] font-black text-zinc-950 dark:text-zinc-100 mt-1 truncate">SHA256:{{ $commercialQuotation['scope_hash_short'] ?? '-' }}</div>
                                <div class="text-[9px] text-zinc-600 dark:text-zinc-400">Anti-Scope Creep Seal</div>
                            </div>
                        </div>

                        <!-- Termin Payment Schedule -->
                        <div class="space-y-1.5">
                            <div class="font-mono text-[10px] font-black uppercase text-zinc-800 dark:text-zinc-300">
                                Tahapan Termin Pembayaran &amp; Milestone Serah Terima
                            </div>
                            @foreach(($commercialQuotation['termin_schedule'] ?? []) as $tItem)
                                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 flex flex-wrap items-center justify-between gap-2 text-[11px] font-mono tabular-nums">
                                    <span class="text-zinc-900 dark:text-zinc-200">{{ $tItem['label'] }}</span>
                                    <span class="font-black text-emerald-900 dark:text-emerald-300">{{ $tItem['formatted_amount'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Cryptographic Scope Freeze & Print Pack Actions -->
                        <div class="flex flex-wrap items-center gap-1.5 pt-1">
                            <button
                                type="button"
                                wire:click="freezeScopeAndIssueSpk"
                                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:text-zinc-950 font-mono text-[11px] font-black rounded-xs cursor-pointer"
                            >
                                🔒 Kunci Scope SHA-256 &amp; Terbitkan Kontrak SPK
                            </button>
                            @if($slug)
                                <a href="{{ url('/blueprint/' . $slug . '/mitra-document/rab') }}" target="_blank" class="px-2.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-950 border border-zinc-300 dark:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700 font-mono text-[10px] font-bold rounded-xs">
                                    🖨️ Cetak RAB A4
                                </a>
                                <a href="{{ url('/blueprint/' . $slug . '/mitra-document/spk-appendix') }}" target="_blank" class="px-2.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-950 border border-zinc-300 dark:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700 font-mono text-[10px] font-bold rounded-xs">
                                    📜 Lampiran SPK SHA-256
                                </a>
                                <a href="{{ url('/blueprint/' . $slug . '/mitra-document/bast') }}" target="_blank" class="px-2.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-950 border border-zinc-300 dark:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700 font-mono text-[10px] font-bold rounded-xs">
                                    ✅ Cetak BAST &amp; UAT
                                </a>
                            @endif
                        </div>

                        @if(!empty($latestSpkResult['scope_hash_full']))
                            <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-400 dark:border-emerald-700 font-mono text-[10px] text-emerald-950 dark:text-emerald-200 space-y-1">
                                <div class="font-black">✓ SCOPE TERKUNCI KRIPTOGRAFIS: {{ $latestSpkResult['document_title'] ?? '' }}</div>
                                <div class="break-all">SHA-256: {{ $latestSpkResult['scope_hash_full'] }}</div>
                                @if(!empty($latestSpkResult['sign_url']))
                                    <div>
                                        <a href="{{ $latestSpkResult['sign_url'] }}" target="_blank" class="underline font-black">Buka Halaman E-Sign Kontrak SPK &rarr;</a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- 2. Phase-to-Milestone Gantt (7 Milestones) & Live Staging -->
                    <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-2">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="font-mono text-[10px] font-black uppercase text-zinc-950 dark:text-zinc-100">
                                2. 7-MILESTONE LIVE TIMELINE &amp; STAGING PREVIEW ({{ $milestoneGantt['completion_percent'] ?? 0 }}%)
                            </span>
                            <span class="font-mono text-[10px] text-emerald-900 dark:text-emerald-300 truncate max-w-60">
                                {{ $milestoneGantt['staging_url'] ?? '' }}
                            </span>
                        </div>
                        <div class="space-y-1 font-mono text-[10px] tabular-nums">
                            @foreach(($milestoneGantt['milestones'] ?? []) as $ms)
                                <div class="p-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-2">
                                    <span class="truncate text-zinc-900 dark:text-zinc-200">{{ $ms['title'] }} ({{ $ms['working_days'] }}h)</span>
                                    <span class="px-1.5 py-0.5 font-black whitespace-nowrap {{ $ms['status'] === 'VERIFIED' ? 'bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700' : 'bg-zinc-200 text-zinc-900 dark:bg-zinc-800 dark:text-zinc-300' }}">
                                        {{ $ms['badge'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 3. Automated UAT Sign-Off (Given-When-Then) & Digital BAST -->
                    <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-2.5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <div class="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300">
                                    3. INTERACTIVE UAT CHECKLIST (GIVEN-WHEN-THEN) &amp; DIGITAL BAST
                                </div>
                                <div class="text-xs font-bold text-zinc-950 dark:text-zinc-100 tabular-nums">
                                    Progres Uji Terima Klien: {{ $uatChecklist['passed_scenarios'] ?? 0 }}/{{ $uatChecklist['total_scenarios'] ?? 0 }} Skenario ({{ $uatChecklist['progress_percent'] ?? 0 }}%)
                                </div>
                            </div>
                            <button
                                type="button"
                                wire:click="toggleStudioUatScenario('ALL')"
                                class="px-2 py-1 bg-zinc-100 hover:bg-zinc-200 text-zinc-950 border border-zinc-300 dark:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700 font-mono text-[10px] font-bold rounded-none cursor-pointer"
                            >
                                Centang Semua UAT
                            </button>
                        </div>

                        <div class="space-y-1.5 max-h-56 overflow-y-auto studio-scroll pr-1">
                            @foreach(($uatChecklist['scenarios'] ?? []) as $sc)
                                <label class="flex items-start gap-2 p-2 border rounded-xs cursor-pointer {{ !empty($sc['passed']) ? 'bg-emerald-50 border-emerald-400 dark:bg-emerald-950/40 dark:border-emerald-700' : 'bg-zinc-50 border-zinc-200 dark:bg-zinc-950 dark:border-zinc-800' }}">
                                    <input
                                        type="checkbox"
                                        wire:click="toggleStudioUatScenario('{{ $sc['id'] }}')"
                                        @checked(!empty($sc['passed']))
                                        class="mt-0.5 rounded-none"
                                    />
                                    <div class="text-[11px] leading-snug">
                                        <div class="font-mono font-black text-zinc-950 dark:text-zinc-100">[{{ $sc['id'] }}] {{ $sc['title'] }}</div>
                                        <div class="text-zinc-700 dark:text-zinc-300"><strong>Given:</strong> {{ $sc['given'] }} | <strong>When:</strong> {{ $sc['when'] }} | <strong class="text-emerald-900 dark:text-emerald-300">Then:</strong> {{ $sc['then'] }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 pt-1">
                            <input type="text" wire:model="bastSignerName" placeholder="Nama PIC Penandatangan BAST" class="px-2 py-1 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xs" />
                            <input type="email" wire:model="bastSignerEmail" placeholder="Email Resmi PIC Klien" class="px-2 py-1 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-xs" />
                        </div>

                        <button
                            type="button"
                            wire:click="signStudioDigitalBast"
                            class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:text-zinc-950 font-mono text-xs font-black rounded-xs cursor-pointer"
                        >
                            ✍️ Sahkan UAT 100%, Terbitkan BAST Digital Resmi &amp; Tagihkan Termin Akhir
                        </button>

                        @if(!empty($bastSignoffState['bast_number']))
                            <div class="p-2.5 bg-emerald-100 text-emerald-950 border border-emerald-500 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-600 font-mono text-[10px] space-y-1">
                                <div class="font-black">🎉 BAST DIGITAL RESMI DITERBITKAN: {{ $bastSignoffState['bast_number'] }}</div>
                                <div>Disahkan oleh: {{ $bastSignoffState['signer_name'] ?? '-' }} &bull; Termin Akhir: <strong>{{ $bastSignoffState['formatted_final_termin'] ?? '-' }}</strong></div>
                                <div class="break-all">SHA-256 BAST Seal: {{ $bastSignoffState['bast_sha256'] ?? '-' }}</div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- TAB 7 (v6.0): L1–L4 ENTERPRISE MENU MATRIX (25 SUB-MENUS) & 5 CORPORATE GLUE + KAHN DAG -->
                @if($centerTab === 'menus_glue')
                    <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-2.5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <div class="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300">
                                    JAMINAN 1: L1–L4 ENTERPRISE MENU &amp; CAPABILITY MATRIX (ANTI-SHALLOW APP)
                                </div>
                                <div class="text-xs font-bold text-zinc-950 dark:text-zinc-100 tabular-nums">
                                    {{ $enterpriseMenuMatrix['included_v1_count'] ?? 25 }}/{{ $enterpriseMenuMatrix['total_menus'] ?? 25 }} Sub-Menu Operasional Aktif di v1.0 (5 Divisi Standar Korporat)
                                </div>
                            </div>
                            <button
                                type="button"
                                wire:click="selectAllEnterpriseMenus"
                                class="px-2 py-1 bg-zinc-950 text-white dark:bg-emerald-500 dark:text-zinc-950 font-mono text-[10px] font-black rounded-none cursor-pointer"
                            >
                                Aktifkan Semua 25 Menu
                            </button>
                        </div>

                        <div class="space-y-2">
                            @foreach(($enterpriseMenuMatrix['divisions'] ?? []) as $div)
                                <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xs space-y-1.5">
                                    <div class="flex items-center justify-between gap-2 font-mono text-[11px]">
                                        <span class="font-black text-zinc-950 dark:text-zinc-100">
                                            Divisi {{ $div['division_number'] }}: {{ $div['division_name']['id'] ?? '' }}
                                        </span>
                                        <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 text-[9px] font-black rounded-none tabular-nums">
                                            Part 5.{{ $div['division_number'] }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-1 gap-1">
                                        @foreach(($div['menus'] ?? []) as $mItem)
                                            @php $isMenuChecked = !empty($mItem['included_in_v1']); @endphp
                                            <label class="flex items-start justify-between gap-2 p-1.5 border rounded-none cursor-pointer {{ $isMenuChecked ? 'bg-white border-emerald-400 dark:bg-zinc-900 dark:border-emerald-700' : 'bg-zinc-100/70 border-zinc-200 dark:bg-zinc-900/40 dark:border-zinc-800 opacity-75' }}">
                                                <div class="flex items-start gap-2 min-w-0">
                                                    <input
                                                        type="checkbox"
                                                        wire:click="toggleEnterpriseMenu('{{ $mItem['code'] }}')"
                                                        @checked($isMenuChecked)
                                                        class="mt-0.5 rounded-none"
                                                    />
                                                    <div class="text-[11px] leading-tight">
                                                        <div class="font-mono font-bold text-zinc-950 dark:text-zinc-100">
                                                            [{{ $mItem['code'] }}] {{ $mItem['title'] }}
                                                        </div>
                                                        <div class="text-[10px] font-mono text-zinc-600 dark:text-zinc-400">
                                                            L4: {{ implode(' • ', $mItem['l4_capabilities'] ?? []) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="shrink-0 px-1.5 py-0.5 font-mono text-[9px] font-black border {{ $isMenuChecked ? 'bg-emerald-100 text-emerald-950 border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700' : 'bg-zinc-200 text-zinc-800 border-zinc-400 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700' }}">
                                                    {{ $isMenuChecked ? 'v1.0 CORE' : 'v1.1 BACKLOG' }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-2">
                        <div class="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300">
                            JAMINAN 2: THE 5 CORPORATE "ENTERPRISE GLUE" PRIMITIVES (AUTO-INJECTED)
                        </div>
                        <div class="space-y-1.5">
                            @foreach($enterpriseGlueList as $gp)
                                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none font-mono text-[10px] space-y-0.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-black text-zinc-950 dark:text-zinc-100">{{ $gp['name']['id'] ?? $gp['code'] }}</span>
                                        <code class="px-1.5 py-0.5 bg-zinc-900 text-white dark:bg-emerald-500 dark:text-zinc-950 font-black">{{ $gp['table'] }}</code>
                                    </div>
                                    <div class="text-zinc-700 dark:text-zinc-300">{{ $gp['invariant'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-2">
                        <div class="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300">
                            JAMINAN 4: KAHN'S ALGORITHM TOPOLOGICAL MIGRATION LAYERS (ZERO FK ERROR)
                        </div>
                        <div class="space-y-1 font-mono text-[10px]">
                            @foreach($dagLayers as $layerObj)
                                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none flex flex-wrap items-center gap-1.5">
                                    <span class="px-1.5 py-0.5 bg-zinc-950 text-white dark:bg-zinc-200 dark:text-zinc-950 font-black">
                                        Layer {{ $layerObj['layer_index'] }}
                                    </span>
                                    @foreach(($layerObj['tables'] ?? []) as $ltName)
                                        <code class="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 font-bold">{{ $ltName }}</code>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- TAB 8: 5 REAL-WORLD RUNTIME CONTRACTS, CLIENT SOP & MASTER 15 PILLARS (v7.0 FREEZE) -->
                @if($centerTab === 'runtime_sop_v7')
                    <div class="space-y-3">
                        <div class="p-3 bg-white dark:bg-zinc-900 border-2 border-emerald-600 dark:border-emerald-500 rounded-xs space-y-2">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300">
                                    PROJECT OS v7.0 • 100% ARCHITECTURAL FREEZE (5 REAL-WORLD ELEMENTS + 15 MASTER PILLARS)
                                </span>
                                @if($slug)
                                    <a href="{{ route('blueprint.mitra-document', ['slug' => $slug, 'docType' => 'sop-training']) }}" target="_blank" class="px-2 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950 dark:text-amber-200 dark:border-amber-700 font-mono text-[10px] font-black">
                                        🖨️ Cetak Buku SOP A4 (BAST)
                                    </a>
                                @endif
                            </div>
                            <p class="text-[11px] text-zinc-700 dark:text-zinc-300 leading-relaxed">
                                Mengunci jembatan perangkat keras fisik (&lt;30ms Barcode Wedge &amp; Layar Kedua QRIS lokal), WebSocket Tanpa Polling (<code>organization.{ulid}.{module}</code>), larangan N+1 Query + Partisi Kuartalan PostgreSQL 16, <code>SYNTAX_LOCK_2026.md</code>, serta Buku SOP Role Interaktif.
                            </p>
                            <div class="flex flex-wrap gap-1.5 font-mono text-[10px]">
                                <button type="button" wire:click="$set('rightDockMode', 'syntax_lock_md')" class="px-2 py-1 bg-zinc-950 text-white dark:bg-emerald-500 dark:text-zinc-950 font-black cursor-pointer">
                                    Lihat SYNTAX_LOCK_2026.md &rarr;
                                </button>
                                <button type="button" wire:click="$set('rightDockMode', 'client_sop_md')" class="px-2 py-1 bg-zinc-100 hover:bg-zinc-200 text-zinc-950 border border-zinc-300 dark:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700 font-bold cursor-pointer">
                                    Lihat CLIENT_SOP.md &rarr;
                                </button>
                                <button type="button" wire:click="$set('rightDockMode', 'pg16_partition_sql')" class="px-2 py-1 bg-zinc-100 hover:bg-zinc-200 text-zinc-950 border border-zinc-300 dark:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-700 font-bold cursor-pointer">
                                    Lihat pg16_partition.sql &rarr;
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 font-mono text-[10px]">
                            <div class="p-2.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-1">
                                <div class="font-black text-emerald-900 dark:text-emerald-300 uppercase">1. Hardware &amp; Dual-Screen</div>
                                <div>Barcode/RFID &lt;30ms: <strong>{{ !empty($hwContract['barcode_rfid_wedge']['enabled']) ? 'AKTIF ✅' : 'STANDBY' }}</strong></div>
                                <div>Dual-Screen + Local QRIS: <strong>{{ !empty($hwContract['customer_dual_screen']['enabled']) ? 'AKTIF ✅' : 'STANDBY' }}</strong></div>
                                <div>Biometric ZKTeco / Gate &lt;150ms: <strong>{{ !empty($hwContract['biometric_and_turnstile_iot']['enabled']) ? 'AKTIF ✅' : 'STANDBY' }}</strong></div>
                            </div>
                            <div class="p-2.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-1">
                                <div class="font-black text-emerald-900 dark:text-emerald-300 uppercase">2. Zero-Poll WebSocket &amp; 0% N+1</div>
                                <div>Engine: <strong>{{ $wsContract['engine'] ?? 'Laravel Reverb' }}</strong></div>
                                <div>Channels: <strong>{{ count($wsContract['channels'] ?? []) }} Tenant Channels</strong></div>
                                <div class="text-rose-900 dark:text-rose-300 font-black">Ban wire:poll.&lt;30s &bull; PG16 Range Partition</div>
                            </div>
                        </div>

                        <!-- 15 Master Pillars Compact List -->
                        <div class="p-2.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-1.5">
                            <div class="font-mono text-[10px] font-black uppercase text-zinc-950 dark:text-zinc-100">
                                Master 15 Canonical Architecture Pillars ({{ count($fifteenPillars) }}/15 — 100% Lock &amp; Freeze)
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                @foreach($fifteenPillars as $fp)
                                    <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-none">
                                        <div class="flex items-center justify-between font-mono text-[9px]">
                                            <span class="font-black text-emerald-900 dark:text-emerald-300">PILAR {{ sprintf('%02d', $fp['pillar_number'] ?? 0) }}</span>
                                            <span class="font-black text-zinc-950 dark:text-zinc-100">{{ $fp['maturity_percent'] ?? 100 }}%</span>
                                        </div>
                                        <div class="text-[11px] font-black text-zinc-950 dark:text-zinc-100 mt-0.5 leading-snug">{{ $fp['name']['id'] ?? $fp['code'] }}</div>
                                        <div class="font-mono text-[9px] text-zinc-600 dark:text-zinc-400 truncate mt-0.5">{{ $fp['artifact_contract'] ?? '' }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- TAB 9: V8.0 LIVING CVE SECURITY ALERTS, AGENCY WHITE-LABEL, CANARY IP & 70:30 MARKETPLACE -->
                @if($centerTab === 'inclusive_security_v8')
                    <div class="space-y-3">
                        <!-- 1. Living Architecture CVE Security Alerts + 1-Click Auto-Patch Prompt -->
                        <div class="p-3 bg-white dark:bg-zinc-900 border-2 border-amber-500 dark:border-amber-600 rounded-none space-y-2.5">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950 dark:text-amber-200 dark:border-amber-700 font-mono text-[10px] font-black uppercase">
                                    🛡️ LIVING ARCHITECTURE CVE WATCHER ({{ count($cveAlerts ?? []) }} ACTIVE ADVISORIES)
                                </span>
                                <span class="font-mono text-[10px] font-bold text-zinc-700 dark:text-zinc-300">
                                    Stack: {{ $stackCode }}
                                </span>
                            </div>
                            @foreach(($cveAlerts ?? []) as $idxCve => $cve)
                                <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 space-y-1.5 font-mono text-[10px]">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-black text-rose-900 dark:text-rose-300">{{ $cve['cve_id'] ?? '' }} • {{ $cve['severity'] ?? 'HIGH' }}</span>
                                        <button
                                            type="button"
                                            x-on:click="navigator.clipboard.writeText($el.nextElementSibling.innerText); window.showToast({ type: &apos;success&apos;, title: &apos;AUTO-PATCH PROMPT COPIED&apos;, message: &apos;Tempel prompt patch keamanan ini langsung ke AI IDE Anda.&apos; })"
                                            class="px-2 py-0.5 bg-zinc-950 text-emerald-400 font-black rounded-none cursor-pointer"
                                        >
                                            📋 Salin Prompt Auto-Patch 1-Klik
                                        </button>
                                        <pre class="hidden">{{ $cve['one_click_autopatch_prompt'] ?? '' }}</pre>
                                    </div>
                                    <div class="font-bold text-zinc-950 dark:text-zinc-100 font-sans text-xs">{{ $cve['title']['id'] ?? ($cve['cve_id'] ?? '') }}</div>
                                    <div class="text-zinc-700 dark:text-zinc-300 font-sans text-[11px]">{{ $cve['remediation_summary']['id'] ?? '' }}</div>
                                </div>
                            @endforeach
                        </div>

                        <!-- 2. Agency Partner White-Label Branding & PDF Export -->
                        <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-none space-y-2.5">
                            <div class="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300">
                                🏢 AGENCY PARTNER WHITE-LABEL BRANDING (A4 PROPOSAL, RAB, SPK &amp; BAST)
                            </div>
                            <label class="flex items-center gap-2 text-xs font-bold text-zinc-900 dark:text-zinc-100 cursor-pointer">
                                <input type="checkbox" wire:model.live="whiteLabelEnabled" class="rounded-none" />
                                <span>Aktifkan Mode White-Label (Sembunyikan merek Neriah Pro pada PDF Klien)</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <input
                                    type="text"
                                    wire:model.live.debounce.400ms="whiteLabelAgencyName"
                                    placeholder="Nama Software House Anda (mis: Nusantara Digital Studio)"
                                    class="px-2.5 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs font-mono rounded-none"
                                />
                                <input
                                    type="text"
                                    wire:model.live.debounce.400ms="whiteLabelLegalEntity"
                                    placeholder="Entitas Hukum (mis: PT Nusantara Rekayasa Sistem)"
                                    class="px-2.5 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-xs font-mono rounded-none"
                                />
                            </div>
                            <div class="flex flex-wrap items-center gap-2 font-mono text-[10px]">
                                <button
                                    type="button"
                                    wire:click="saveStudioWhiteLabelBranding"
                                    class="px-2.5 py-1.5 bg-zinc-950 text-white dark:bg-emerald-500 dark:text-zinc-950 font-black rounded-none cursor-pointer"
                                >
                                    Simpan Branding White-Label
                                </button>
                                @if($slug && $whiteLabelAgencyName !== '')
                                    <a
                                        href="{{ route('blueprint.mitra-document', ['slug' => $slug, 'docType' => 'boq-rab', 'agency_name' => $whiteLabelAgencyName, 'agency_entity' => $whiteLabelLegalEntity]) }}"
                                        target="_blank"
                                        class="px-2.5 py-1.5 bg-amber-100 text-amber-950 border border-amber-400 dark:bg-amber-950 dark:text-amber-200 dark:border-amber-700 font-black rounded-none"
                                    >
                                        🖨️ Cetak RAB A4 Ber-Logo {{ $whiteLabelAgencyName }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- 3. 5-Layer Banking-Grade IP Security: MCP Token Revoke + Canary Watermark -->
                        <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-none space-y-2 font-mono text-[10px]">
                            <div class="font-black uppercase text-zinc-950 dark:text-zinc-100">
                                🔐 5-LAYER BANKING-GRADE IP &amp; PLATFORM SECURITY
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                    <div class="font-black text-emerald-900 dark:text-emerald-300">Zero-Width Canary Signature</div>
                                    <div class="text-zinc-900 dark:text-zinc-100 font-bold mt-0.5 tabular-nums">{{ $schema['canary_watermark_sig'] ?? 'ACTIVE_ON_EXPORT' }}</div>
                                    <div class="text-zinc-600 dark:text-zinc-400 mt-0.5">Melacak kebocoran dokumen tanpa mengubah tampilan kode.</div>
                                </div>
                                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex flex-col justify-between">
                                    <div>
                                        <div class="font-black text-rose-900 dark:text-rose-300">Scoped MCP Token Control</div>
                                        <div class="text-zinc-700 dark:text-zinc-300 mt-0.5">Cabut akses IDE freelancer secara instan saat kontrak selesai.</div>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="revokeStudioMcpToken"
                                        class="mt-1.5 px-2 py-1 bg-rose-700 hover:bg-rose-600 text-white font-black rounded-none cursor-pointer"
                                    >
                                        🔒 Cabut &amp; Rotasi Token MCP 1-Klik
                                    </button>
                                </div>
                            </div>
                            @if($mcpRevokeNotice !== '')
                                <div class="p-2 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 font-black">
                                    {{ $mcpRevokeNotice }}
                                </div>
                            @endif
                        </div>

                        <!-- 4. 70:30 Verified Community Primitives Marketplace -->
                        <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-none space-y-2">
                            <div class="flex items-center justify-between font-mono text-[10px]">
                                <span class="font-black uppercase text-zinc-950 dark:text-zinc-100">🧩 COMMUNITY PRIMITIVES MARKETPLACE (70:30 CREATOR REVENUE SHARE)</span>
                                <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 font-black">100% DOCTOR VERIFIED</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 font-mono text-[10px]">
                                @foreach(($communityMarketplace ?? []) as $mp)
                                    <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                        <div class="flex items-center justify-between font-black text-emerald-900 dark:text-emerald-300">
                                            <span>{{ $mp['code'] ?? '' }}</span>
                                            <span>Rp {{ number_format((int) ($mp['price_idr'] ?? 0), 0, ',', '.') }}</span>
                                        </div>
                                        <div class="font-bold text-zinc-950 dark:text-zinc-100 font-sans text-xs mt-0.5">{{ $mp['name']['id'] ?? '' }}</div>
                                        <div class="text-zinc-600 dark:text-zinc-400 mt-0.5">Oleh {{ $mp['author'] ?? 'Verified Partner' }} &bull; Bagi Hasil {{ $mp['revenue_split'] ?? '70:30' }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- =============================================================== -->
                <!-- TAB 10: v9.0 THE CO-DESIGN ALIGNMENT & SIGN-OFF ROOM            -->
                <!-- ("SATU DATA, DUA KACAMATA" — DEVELOPER NERIAH <-> KLIEN MITRA)  -->
                <!-- =============================================================== -->
                @if($centerTab === 'codesign_room')
                    @php
                        $cdModules = $codesignContract['modules'] ?? [];
                        $activeCdMod = collect($cdModules)->firstWhere('module_code', $selectedCoDesignModuleCode) ?? ($cdModules[0] ?? []);
                        $cdCeremony = $codesignContract['dual_key_ceremony'] ?? [];
                        $isDualKeySealed = !empty($cdCeremony['is_locked']);
                        $cdPercent = (int) ($codesignContract['approval_progress_percent'] ?? 0);
                        $isEnLoc = $outputLocale === 'en';
                    @endphp
                    <div class="space-y-3">
                        <div class="p-3 bg-white dark:bg-zinc-900 border-2 border-emerald-600 dark:border-emerald-500 rounded-none space-y-2">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <span class="px-2 py-0.5 bg-zinc-950 text-emerald-400 font-mono text-[10px] font-black uppercase">
                                        🤝 {{ $isEnLoc ? 'CO-DESIGN ALIGNMENT & DUAL-KEY SIGN-OFF ROOM v9.0' : 'RUANG KOLABORASI CO-DESIGN & SIGN-OFF DUAL-KEY v9.0' }}
                                    </span>
                                    <div class="text-xs font-black text-zinc-950 dark:text-zinc-100 mt-1">
                                        {{ $isEnLoc ? 'One Data, Two Lenses: Live Client Business Wireframe <-> Architect Technical Contract' : 'Satu Data, Dua Kacamata: Sinkronisasi Visual Bisnis Klien <-> Skema Teknis Arsitek' }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 font-mono text-[10px] tabular-nums">
                                    <span class="px-2 py-1 border font-black {{ $cdPercent === 100 ? 'bg-emerald-100 text-emerald-950 border-emerald-500 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-600' : 'bg-amber-100 text-amber-950 border-amber-500 dark:bg-amber-950 dark:text-amber-200 dark:border-amber-600' }}">
                                        {{ $codesignContract['approved_modules_count'] ?? 0 }}/{{ $codesignContract['total_modules_count'] ?? 0 }} APPROVED ({{ $cdPercent }}%)
                                    </span>
                                    @if(!$isDualKeySealed)
                                        <button
                                            type="button"
                                            wire:click="signCoDesignModule('ALL', 'APPROVED')"
                                            class="px-2 py-1 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-black rounded-none cursor-pointer"
                                        >
                                            ☑️ {{ $isEnLoc ? 'Approve All' : 'Setujui Semua' }}
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @if($codesignStatusNotice !== '')
                                <div class="p-2 bg-zinc-100 dark:bg-zinc-950 border border-zinc-400 dark:border-zinc-700 font-mono text-[10px] font-bold text-zinc-950 dark:text-emerald-300">
                                    {{ $codesignStatusNotice }}
                                </div>
                            @endif
                        </div>

                        <!-- PANEL 1: BUSINESS MODULES EXPLORER + ADD CLIENT REQUIREMENT -->
                        <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-none space-y-2.5">
                            <div class="flex items-center justify-between font-mono text-[10px]">
                                <span class="font-black uppercase text-zinc-950 dark:text-zinc-100">
                                    PANEL 1 // {{ $isEnLoc ? 'BUSINESS MODULES (L1–L4 & CUSTOM CLIENT ADDITIONS)' : 'DAFTAR MODUL BISNIS (DIVISI L1–L4 & KEBUTUHAN TAMBAHAN KLIEN)' }}
                                </span>
                                <span class="text-zinc-600 dark:text-zinc-400 tabular-nums">
                                    Extra Hours: +{{ number_format((int) ($codesignContract['extra_codesign_man_hours'] ?? 0)) }}h
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 font-mono text-[10px]">
                                @foreach($cdModules as $cdMod)
                                    @php
                                        $mCode = (string) ($cdMod['module_code'] ?? '');
                                        $mStatus = (string) ($cdMod['status'] ?? 'PENDING');
                                        $isSelMod = ($activeCdMod['module_code'] ?? '') === $mCode;
                                    @endphp
                                    <button
                                        type="button"
                                        wire:click="selectCoDesignModule('{{ $mCode }}')"
                                        class="p-2 text-left border rounded-none cursor-pointer transition flex items-start justify-between gap-2 {{ $isSelMod ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-950 dark:text-white dark:border-emerald-400' : 'bg-zinc-50 hover:bg-zinc-100 text-zinc-950 border-zinc-300 dark:bg-zinc-950 dark:hover:bg-zinc-800 dark:text-zinc-100 dark:border-zinc-800' }}"
                                    >
                                        <div class="min-w-0">
                                            <div class="font-black truncate">
                                                0{{ $cdMod['module_number'] ?? 1 }}. {{ $cdMod['localized_title'] ?? $mCode }}
                                            </div>
                                            <div class="text-[9px] mt-0.5 {{ $isSelMod ? 'text-zinc-300' : 'text-zinc-600 dark:text-zinc-400' }}">
                                                {{ $cdMod['client_business_lens']['formatted_cost_idr'] ?? 'Rp 0' }} &bull; {{ $cdMod['client_business_lens']['estimated_days'] ?? 1 }}d
                                            </div>
                                        </div>
                                        <span class="px-1.5 py-0.5 text-[9px] font-black border shrink-0 {{ $mStatus === 'APPROVED' ? 'bg-emerald-200 text-emerald-950 border-emerald-500 dark:bg-emerald-900 dark:text-emerald-100 dark:border-emerald-600' : ($mStatus === 'DISCUSSION' ? 'bg-amber-200 text-amber-950 border-amber-500 dark:bg-amber-900 dark:text-amber-100 dark:border-amber-600' : 'bg-zinc-200 text-zinc-900 border-zinc-400 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700') }}">
                                            {{ $mStatus }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>

                            @if(!$isDualKeySealed)
                                <div class="pt-2 border-t border-zinc-200 dark:border-zinc-800 flex flex-wrap items-center gap-1.5 font-mono text-[10px]">
                                    <input
                                        type="text"
                                        wire:model="newCustomModuleTitle"
                                        placeholder="{{ $isEnLoc ? '+ Add Custom Client Requirement (auto-recalculates RAB)...' : '+ Tambah Kebutuhan Klien (otomatis hitung ulang RAB & Scope Hash)...' }}"
                                        class="flex-1 min-w-44 px-2 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 rounded-none"
                                    />
                                    <input
                                        type="number"
                                        min="4"
                                        max="240"
                                        wire:model="newCustomModuleHours"
                                        aria-label="Estimasi Jam Kerja"
                                        class="w-16 px-2 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 tabular-nums rounded-none"
                                    />
                                    <button
                                        type="button"
                                        wire:click="addCustomCoDesignModule"
                                        class="px-2.5 py-1.5 bg-zinc-950 text-white dark:bg-emerald-500 dark:text-zinc-950 font-black rounded-none cursor-pointer"
                                    >
                                        + {{ $isEnLoc ? 'Add Module' : 'Tambah Modul' }}
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- PANEL 2: DUAL-MIRROR SCREEN (CLIENT BUSINESS LENS <-> ARCHITECT TECHNICAL LENS) + PER-MODULE SIGN-OFF -->
                        @if(!empty($activeCdMod))
                            <div class="p-3 bg-white dark:bg-zinc-900 border-2 border-zinc-300 dark:border-zinc-800 rounded-none space-y-2.5">
                                <div class="flex flex-wrap items-center justify-between gap-2 pb-2 border-b border-zinc-200 dark:border-zinc-800">
                                    <div class="font-mono text-xs font-black text-zinc-950 dark:text-zinc-100">
                                        PANEL 2 // [{{ $activeCdMod['module_code'] }}] {{ $activeCdMod['localized_title'] ?? '' }}
                                    </div>
                                    <div class="inline-flex p-0.5 bg-zinc-100 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 font-mono text-[10px]">
                                        <button
                                            type="button"
                                            wire:click="toggleCoDesignLens('CLIENT_BUSINESS')"
                                            class="px-2 py-0.5 rounded-none cursor-pointer {{ $codesignLensMode === 'CLIENT_BUSINESS' ? 'bg-emerald-600 text-white dark:bg-emerald-400 dark:text-zinc-950 font-black' : 'text-zinc-800 dark:text-zinc-300 font-bold' }}"
                                        >
                                            👔 {{ $isEnLoc ? 'Client Business Lens' : 'Kacamata Bisnis Klien' }}
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="toggleCoDesignLens('ARCHITECT_TECHNICAL')"
                                            class="px-2 py-0.5 rounded-none cursor-pointer {{ $codesignLensMode === 'ARCHITECT_TECHNICAL' ? 'bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 font-black' : 'text-zinc-800 dark:text-zinc-300 font-bold' }}"
                                        >
                                            ⚙️ {{ $isEnLoc ? 'Architect Technical Lens' : 'Kacamata Teknis Arsitek' }}
                                        </button>
                                    </div>
                                </div>

                                @if($codesignLensMode === 'CLIENT_BUSINESS')
                                    <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 font-mono text-[10px] space-y-2">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <span class="font-black text-emerald-950 dark:text-emerald-300">
                                                UI WIREFRAME: [{{ $activeCdMod['client_business_lens']['wireframe_pane_label'] ?? 'PRIMARY SCREEN' }}] ({{ $activeCdMod['client_business_lens']['visual_ui_variant'] ?? '' }})
                                            </span>
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 font-black tabular-nums">
                                                {{ $activeCdMod['client_business_lens']['formatted_cost_idr'] ?? 'Rp 0' }} &bull; {{ $activeCdMod['client_business_lens']['estimated_days'] ?? 1 }} {{ $isEnLoc ? 'Working Days' : 'Hari Kerja' }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach(($activeCdMod['client_business_lens']['included_menus'] ?? []) as $incMenu)
                                                <span class="px-1.5 py-0.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 font-bold">
                                                    ▣ {{ $incMenu }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="p-2.5 bg-zinc-950 text-zinc-100 border border-zinc-800 font-mono text-[10px] space-y-1.5">
                                        <div><span class="text-emerald-400 font-black">ULID Tables:</span> {{ implode(', ', $activeCdMod['architect_technical_lens']['ulid_tables'] ?? []) }}</div>
                                        <div><span class="text-emerald-400 font-black">State Machines:</span> {{ implode(' | ', $activeCdMod['architect_technical_lens']['state_machines'] ?? []) }}</div>
                                        <div><span class="text-amber-400 font-black">Concurrency Locks:</span> {{ implode(' | ', $activeCdMod['architect_technical_lens']['concurrency_locks'] ?? []) }}</div>
                                        <div><span class="text-cyan-300 font-black">Private Reverb WS:</span> {{ $activeCdMod['architect_technical_lens']['websocket_channel'] ?? '' }}</div>
                                    </div>
                                @endif

                                <!-- Per-Module Clause Sign-Off Controls -->
                                <div class="flex flex-wrap items-center justify-between gap-2 pt-1 font-mono text-[10px]">
                                    <div class="text-zinc-700 dark:text-zinc-300">
                                        @if(!empty($activeCdMod['approved_at']))
                                            <span class="font-bold text-emerald-900 dark:text-emerald-300">
                                                ✓ Signed by {{ $activeCdMod['approved_by_name'] }} (IP: {{ $activeCdMod['approved_by_ip'] }}) @ {{ $activeCdMod['approved_at'] }}
                                            </span>
                                        @else
                                            <span>{{ $isEnLoc ? 'Sign off this module to lock scope & prevent disputes.' : 'Klik persetujuan klausa per modul (tercatat di Audit Log SHA-256).' }}</span>
                                        @endif
                                    </div>
                                    @if(!$isDualKeySealed)
                                        <div class="flex items-center gap-1.5">
                                            <button
                                                type="button"
                                                wire:click="signCoDesignModule('{{ $activeCdMod['module_code'] }}', 'DISCUSSION')"
                                                class="px-2 py-1 bg-amber-100 hover:bg-amber-200 text-amber-950 border border-amber-500 dark:bg-amber-950 dark:text-amber-200 dark:border-amber-600 font-black rounded-none cursor-pointer"
                                            >
                                                💬 {{ $isEnLoc ? 'Needs Discussion' : 'Perlu Diskusi' }}
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="signCoDesignModule('{{ $activeCdMod['module_code'] }}', 'APPROVED')"
                                                class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-black rounded-none cursor-pointer"
                                            >
                                                ☑️ {{ $isEnLoc ? 'Client Approves This Module' : 'Klien Setuju Modul Ini' }}
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- PANEL 3: ANNOTATIONS, LIVE RAB IMPACT & DUAL-KEY CRYPTOGRAPHIC SCOPE LOCK CEREMONY -->
                        <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-none space-y-3 font-mono text-[10px]">
                            <div class="font-black uppercase text-zinc-950 dark:text-zinc-100">
                                PANEL 3 // {{ $isEnLoc ? 'CONTEXTUAL ANNOTATIONS, LIVE RAB & DUAL-KEY SCOPE LOCK CEREMONY' : 'ANOTASI KONTEKSTUAL, DAMPAK RAB LIVE & UPACARA KUNCI GANDA DUAL-KEY' }}
                            </div>

                            @if(!empty($activeCdMod['annotations']))
                                <div class="space-y-1 max-h-28 overflow-y-auto studio-scroll">
                                    @foreach($activeCdMod['annotations'] as $ann)
                                        <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
                                            <span class="font-black text-emerald-900 dark:text-emerald-300">[{{ $ann['role'] ?? 'PARTICIPANT' }} • {{ $ann['author'] ?? '' }}]:</span>
                                            <span class="text-zinc-900 dark:text-zinc-100 font-sans">{{ $ann['note'] ?? '' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex flex-wrap items-center gap-1.5">
                                <select wire:model="newCoDesignAuthorRole" class="px-2 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 font-bold rounded-none">
                                    <option value="KLIEN_MITRA">👔 PIC Klien Mitra</option>
                                    <option value="ARSITEK_NERIAH">⚙️ Arsitek NeriahPro</option>
                                </select>
                                <input
                                    type="text"
                                    wire:model="newCoDesignNote"
                                    placeholder="{{ $isEnLoc ? 'Write contextual note on this module...' : 'Tulis catatan kesepakatan pada modul ini...' }}"
                                    class="flex-1 min-w-44 px-2 py-1.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 rounded-none"
                                />
                                <button
                                    type="button"
                                    wire:click="addCoDesignAnnotation"
                                    class="px-2.5 py-1.5 bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 font-black rounded-none cursor-pointer"
                                >
                                    💬 {{ $isEnLoc ? 'Send Note' : 'Kirim Catatan' }}
                                </button>
                            </div>

                            <div class="p-2.5 bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-400 dark:border-emerald-800 space-y-2">
                                <div class="flex flex-wrap items-center justify-between gap-2 tabular-nums">
                                    <span class="font-black text-emerald-950 dark:text-emerald-200">
                                        LIVE RAB: {{ $commercialQuotation['formatted_quotation_idr'] ?? 'Rp 0' }} ({{ $commercialQuotation['timeline_days'] ?? 0 }} Hari)
                                    </span>
                                    <span class="font-black text-zinc-950 dark:text-emerald-300">
                                        SHA-256: {{ $commercialQuotation['scope_hash_short'] ?? '-' }}
                                    </span>
                                </div>

                                @if($isDualKeySealed)
                                    <div class="p-2 bg-zinc-950 text-emerald-300 border border-emerald-500 font-bold space-y-0.5 tabular-nums">
                                        <div>🔒 SCOPE v1.0 SEALED READ-ONLY (DUAL-KEY CRYPTOGRAPHIC LOCK)</div>
                                        <div>Architect Key: {{ $cdCeremony['architect_signed_by'] ?? '-' }} | Client Key: {{ $cdCeremony['client_signed_by'] ?? '-' }}</div>
                                        <div class="truncate">Master SHA-256: {{ $cdCeremony['master_scope_hash_sha256'] ?? '' }}</div>
                                    </div>
                                @else
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                        <input
                                            type="text"
                                            wire:model="codesignArchitectSignerName"
                                            placeholder="Nama Lead Architect NeriahPro"
                                            class="px-2 py-1.5 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 rounded-none"
                                        />
                                        <input
                                            type="text"
                                            wire:model="codesignClientSignerName"
                                            placeholder="Nama PIC Klien Mitra Resmi"
                                            class="px-2 py-1.5 bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 text-zinc-950 dark:text-zinc-100 rounded-none"
                                        />
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="executeDualKeyScopeLock"
                                        @disabled(empty($codesignContract['can_execute_dual_key_lock']))
                                        class="w-full py-2 px-3 font-black uppercase rounded-none transition {{ !empty($codesignContract['can_execute_dual_key_lock']) ? 'bg-zinc-950 hover:bg-zinc-800 text-emerald-400 dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:text-zinc-950 cursor-pointer' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 cursor-not-allowed' }}"
                                    >
                                        🔐 {{ $isEnLoc ? 'EXECUTE DUAL-KEY SCOPE LOCK v1.0 & ISSUE SPK SHA-256' : 'KUNCI BERSAMA SCOPE v1.0 (DUAL-KEY CEREMONY) & TERBITKAN SPK SHA-256' }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- =============================================================== -->
                <!-- TAB 11: v9.0 AGENTSHIELD 5-LAYER, 12-SECTOR AUDIT & 5-SPRINT    -->
                <!-- =============================================================== -->
                @if($centerTab === 'agentshield_tasks')
                    @php
                        $asLayers = $agentShieldContract['layers'] ?? [];
                        $sprintList = $worldClassTaskMatrix['sprints'] ?? [];
                        $secAuditPreset = $cryptoAuditContract['sector_audit_preset'] ?? [];
                    @endphp
                    <div class="space-y-3 font-mono text-[10px]">
                        <div class="p-3 bg-white dark:bg-zinc-900 border-2 border-emerald-600 dark:border-emerald-500 rounded-none space-y-2">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="font-black uppercase text-emerald-950 dark:text-emerald-300 text-xs">
                                    🧱 NERIAHPRO AGENTSHIELD v9.0 (5-LAYER AI DEFENSE) &amp; 3-SHEET CRYPTO AUDIT
                                </span>
                                <div class="flex gap-1">
                                    <button type="button" wire:click="$set('rightDockMode', 'claude_settings_json')" class="px-2 py-0.5 bg-zinc-950 text-emerald-400 font-black rounded-none cursor-pointer">
                                        .claude/settings.json &rarr;
                                    </button>
                                    <button type="button" wire:click="$set('rightDockMode', 'dependency_manifest')" class="px-2 py-0.5 bg-emerald-600 text-zinc-950 font-black rounded-none cursor-pointer">
                                        APPROVED_DEPS.json &rarr;
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-1.5">
                                @foreach($asLayers as $lyr)
                                    @php
                                        $lyrGuarantee = is_array($lyr['guarantee'] ?? null)
                                            ? ($lyr['guarantee'][$outputLocale === 'en' ? 'en' : 'id'] ?? $lyr['guarantee']['id'] ?? '')
                                            : ($lyr['guarantee'] ?? '');
                                    @endphp
                                    <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800">
                                        <div class="font-black text-zinc-950 dark:text-zinc-100">
                                            L{{ $lyr['layer'] ?? '' }} // {{ $lyr['code'] ?? '' }} — {{ $lyr['service'] ?? ($lyr['name'] ?? '') }}
                                        </div>
                                        <div class="text-zinc-700 dark:text-zinc-300 font-sans text-[11px] mt-0.5">{{ $lyrGuarantee }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-none space-y-1.5">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="font-black uppercase text-zinc-950 dark:text-zinc-100">
                                    🔍 PRIMITIVE_CRYPTO_AUDIT_EXPLORER (3-SHEET .XLSX + STREAMING .CSV)
                                </span>
                                <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 font-black">
                                    {{ $secAuditPreset['badge'] ?? 'SHA-256 CHAIN' }}
                                </span>
                            </div>
                            <div class="text-zinc-800 dark:text-zinc-200 font-sans text-xs font-bold">
                                {{ $secAuditPreset['localized_title'] ?? '' }}
                            </div>
                            <div class="text-zinc-600 dark:text-zinc-400">
                                Watched Actions: {{ implode(' • ', $secAuditPreset['watched_actions'] ?? []) }}
                            </div>
                        </div>

                        <div class="p-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-none space-y-2">
                            <div class="font-black uppercase text-zinc-950 dark:text-zinc-100">
                                🚀 WORLD-CLASS 5-SPRINT ENGINEERING TASK MATRIX ({{ $worldClassTaskMatrix['total_world_class_tasks'] ?? 13 }} TASKS • UI {{ $worldClassTaskMatrix['locked_visual_ui_variant'] ?? '' }})
                            </div>
                            @foreach($sprintList as $spRow)
                                <div class="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 space-y-1">
                                    <div class="font-black text-emerald-950 dark:text-emerald-300">{{ $spRow['title'] ?? '' }}</div>
                                    @foreach(($spRow['tasks'] ?? []) as $tkRow)
                                        <div class="pl-2 border-l-2 border-emerald-500 text-zinc-900 dark:text-zinc-200">
                                            <strong>{{ $tkRow['task_code'] ?? '' }}</strong> [{{ $tkRow['role'] ?? '' }}]: {{ $tkRow['title'] ?? '' }}
                                            <span class="text-zinc-600 dark:text-zinc-400">(&rarr; {{ $tkRow['deliverable'] ?? '' }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- =============================================================== -->
        <!-- RIGHT PANE (col-span-4): LIVE COMPILER & 7-PHASE PROMPT DOCK    -->
        <!-- =============================================================== -->
        <aside class="lg:col-span-4 bg-white dark:bg-zinc-950 flex flex-col lg:overflow-hidden">
            <!-- Artifact Switcher -->
            <div class="shrink-0 bg-zinc-100 dark:bg-zinc-900 border-b border-zinc-300 dark:border-zinc-800 px-2.5 py-2 space-y-2">
                <div class="flex flex-wrap items-center justify-between gap-1">
                    <span class="font-mono text-[10px] font-black uppercase tracking-wider text-zinc-800 dark:text-zinc-300">
                        LIVE COMPILER &amp; PHASED PROMPT DOCK
                    </span>
                    <span class="font-mono text-[10px] font-bold text-emerald-900 dark:text-emerald-300 tabular-nums">
                        {{ $verifiedCount }}/7 Verified ✅
                    </span>
                </div>

                <div class="flex flex-wrap gap-1 font-mono text-[10px]">
                    @foreach([
                        'phases' => 'Prompts (1–7)',
                        'dag_subparts' => 'DAG Sub-Parts (' . count($subParts ?? []) . ')',
                        'symbol_manifest' => 'SYMBOL_MANIFEST.json',
                        'enterprise_menu' => 'MENU_MATRIX.md',
                        'syntax_lock_md' => 'SYNTAX_LOCK_2026.md',
                        'client_sop_md' => 'CLIENT_SOP.md',
                        'pg16_partition_sql' => 'pg16_partition.sql',
                        'agents_md' => 'AGENTS.md',
                        'graph_json' => 'graph.json',
                        'mcp_json' => '.mcp.json',
                        'env_blueprint' => '.env.blueprint',
                        'claude_settings_json' => '.claude/settings.json',
                        'dependency_manifest' => 'APPROVED_DEPS.json',
                    ] as $mKey => $mLabel)
                        <button
                            type="button"
                            wire:click="$set('rightDockMode', '{{ $mKey }}')"
                            class="px-2 py-0.5 border rounded-none cursor-pointer {{ $rightDockMode === $mKey ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-500 dark:text-zinc-950 dark:border-emerald-400 font-black' : 'bg-white text-zinc-800 border-zinc-300 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700 font-bold' }}"
                        >
                            {{ $mLabel }}
                        </button>
                    @endforeach
                </div>

                @if($rightDockMode === 'phases')
                    <div class="grid grid-cols-7 gap-1 font-mono text-[10px]">
                        @for($p = 1; $p <= count($phases); $p++)
                            @php
                                $isDone = ($phaseProgress['phase_' . $p]['status'] ?? '') === 'VERIFIED';
                                $isActiveP = $activePhase === $p;
                            @endphp
                            <button
                                type="button"
                                wire:click="$set('activePhase', {{ $p }})"
                                class="py-1 border text-center cursor-pointer tabular-nums rounded-none {{ $isActiveP ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-500 dark:text-zinc-950 dark:border-emerald-400 font-black' : ($isDone ? 'bg-emerald-100 text-emerald-950 border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 font-bold' : 'bg-white text-zinc-800 border-zinc-300 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700') }}"
                            >
                                P{{ $p }}{{ $isDone ? '✓' : '' }}
                            </button>
                        @endfor
                    </div>
                @elseif($rightDockMode === 'dag_subparts')
                    <div class="flex flex-wrap gap-1 font-mono text-[10px] max-h-24 overflow-y-auto studio-scroll">
                        @foreach(($subParts ?? []) as $spBtn)
                            <button
                                type="button"
                                wire:click="$set('activeSubPartCode', '{{ $spBtn['part_code'] }}')"
                                class="px-2 py-0.5 border tabular-nums cursor-pointer rounded-none {{ $activeSubPartCode === $spBtn['part_code'] ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-500 dark:text-zinc-950 font-black' : 'bg-white text-zinc-800 border-zinc-300 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700 font-bold' }}"
                                title="{{ $spBtn['title'] }}"
                            >
                                Part {{ $spBtn['part_code'] }}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Selected Phase Title & Actions -->
            <div class="shrink-0 px-3 py-2 bg-zinc-50 dark:bg-zinc-900/60 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-2">
                <div class="min-w-0">
                    @if($rightDockMode === 'phases')
                        <div class="font-mono text-xs font-black text-zinc-950 dark:text-zinc-100 truncate">
                            {{ $selectedPhaseObj['title'] ?? 'Phase Prompt' }}
                        </div>
                    @elseif($rightDockMode === 'dag_subparts')
                        <div class="font-mono text-xs font-black text-zinc-950 dark:text-zinc-100 truncate">
                            {{ $selectedSubPartObj['title'] ?? 'DAG Sub-Part Prompt' }}
                        </div>
                    @else
                        <div class="font-mono text-xs font-black text-zinc-950 dark:text-zinc-100 uppercase">
                            Compiled Artifact: {{ $rightDockMode }}
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    @if($rightDockMode === 'phases')
                        @php $currVerified = ($phaseProgress['phase_' . $activePhase]['status'] ?? '') === 'VERIFIED'; @endphp
                        <button
                            type="button"
                            wire:click="togglePhaseVerified({{ $activePhase }})"
                            class="px-2 py-1 border font-mono text-[10px] font-black rounded-xs cursor-pointer {{ $currVerified ? 'bg-emerald-100 text-emerald-950 border-emerald-500 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-600' : 'bg-white text-zinc-800 border-zinc-300 dark:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700' }}"
                        >
                            {{ $currVerified ? 'Verified ✅' : 'Tandai Selesai' }}
                        </button>
                    @endif
                    <button
                        type="button"
                        @click="copyStudioBuffer()"
                        class="px-2.5 py-1 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:text-zinc-950 font-mono text-[10px] font-black rounded-xs cursor-pointer"
                        title="Ctrl+Shift+C"
                    >
                        📋 Copy {{ $rightDockMode === 'phases' ? 'Phase ' . $activePhase : 'Code' }}
                    </button>
                </div>
            </div>

            <!-- Live Code / Prompt Inspector -->
            <div class="flex-1 lg:overflow-y-auto studio-scroll p-3 bg-zinc-950 text-zinc-100">
                <pre id="studio-right-compiler-code" class="text-[11px] font-mono whitespace-pre-wrap leading-relaxed">{{ $compiledRightContent }}</pre>
            </div>
        </aside>
    </main>

    <!-- =================================================================== -->
    <!-- BOTTOM IDE STATUS BAR (TABULAR-NUMS)                                -->
    <!-- =================================================================== -->
    <footer class="shrink-0 bg-zinc-900 text-zinc-200 border-t border-zinc-800 px-3 py-1.5 flex flex-wrap items-center justify-between gap-2 font-mono text-[10px] tabular-nums">
        <div class="flex flex-wrap items-center gap-3">
            <span><strong class="text-emerald-400">{{ count($tables) }}</strong> Tables (ULID)</span>
            <span>|</span>
            <span><strong class="text-emerald-400">{{ count($stateMachines) }}</strong> State Machines</span>
            <span>|</span>
            <span><strong class="text-emerald-400">{{ count($locks) }}</strong> Pessimistic Locks</span>
            <span>|</span>
            <span><strong class="text-emerald-400">{{ count($decisionRules) }}</strong> Decision Rules</span>
            <span>|</span>
            <span><strong class="text-emerald-400">{{ count($envVars) }}</strong> .env Vault Keys</span>
        </div>
        <div class="flex items-center gap-3">
            <span>Shortcut: <strong class="text-white">Ctrl+Shift+C</strong> Copy Prompt</span>
            <span class="text-emerald-400 font-black">Est. Token Saved: 64%</span>
        </div>
    </footer>

    <!-- =================================================================== -->
    <!-- SCREEN 1 MODAL: THE 60-SECOND ARCHITECTURE WIZARD                   -->
    <!-- =================================================================== -->
    @if($showWizardModal)
        <div class="fixed inset-0 z-50 bg-black/75 backdrop-blur-xs flex items-center justify-center p-4 select-text">
            <div class="w-full max-w-5xl bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-sm shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="px-4 py-3 bg-zinc-950 text-white flex items-center justify-between border-b border-zinc-800">
                    <div class="font-mono text-xs font-black uppercase tracking-wider">
                        ⚡ THE 60-SECOND ARCHITECTURE WIZARD — STEP {{ $wizardStep }}/3
                    </div>
                    <button type="button" wire:click="$set('showWizardModal', false)" class="px-2 py-0.5 bg-zinc-800 hover:bg-zinc-700 text-xs font-mono font-bold cursor-pointer">
                        ✕ TUTUP
                    </button>
                </div>

                <div class="p-4 overflow-y-auto studio-scroll space-y-4">
                    <div class="flex gap-2 font-mono text-xs">
                        @foreach([1 => '1. Pilih Sektor Bisnis (12)', 2 => '2. Pilih Stack & Sizing (9)', 3 => '3. Anti-Slop Shield & Kunci'] as $stNum => $stLabel)
                            <button
                                type="button"
                                wire:click="$set('wizardStep', {{ $stNum }})"
                                class="px-3 py-1.5 border rounded-xs cursor-pointer {{ $wizardStep === $stNum ? 'bg-zinc-950 text-white border-zinc-950 dark:bg-emerald-500 dark:text-zinc-950 font-black' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700 font-bold' }}"
                            >
                                {{ $stLabel }}
                            </button>
                        @endforeach
                    </div>

                    @if($wizardStep === 1)
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                            <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach(($catalog['sectors'] ?? []) as $sec)
                                    <button
                                        type="button"
                                        wire:click="selectSector('{{ $sec['code'] }}')"
                                        class="p-2.5 text-left border rounded-xs cursor-pointer transition {{ $sectorCode === $sec['code'] ? 'bg-emerald-50 border-emerald-500 dark:bg-emerald-950/60 dark:border-emerald-500' : 'bg-white hover:bg-zinc-50 border-zinc-300 dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:border-zinc-800' }}"
                                    >
                                        <div class="font-mono text-[10px] font-black text-emerald-900 dark:text-emerald-300">
                                            SECTOR {{ sprintf('%02d', $sec['sector_number'] ?? 1) }}
                                        </div>
                                        <div class="text-xs font-black text-zinc-950 dark:text-zinc-100 mt-0.5">
                                            {{ $sec['name']['id'] ?? $sec['code'] }}
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                            <div class="lg:col-span-5 p-3.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-800 rounded-xs space-y-2.5">
                                <div class="font-mono text-[10px] font-black uppercase text-emerald-900 dark:text-emerald-300">LIVE SECTOR PREVIEW</div>
                                <div class="text-sm font-black text-zinc-950 dark:text-zinc-100">{{ $activeSectorObj['name']['id'] ?? $sectorCode }}</div>
                                <div class="text-xs font-mono text-zinc-800 dark:text-zinc-200">
                                    UI Layout: <strong>{{ $activeSectorObj['ui_ux_archetypes']['primary_archetype'] ?? '' }}</strong>
                                </div>
                                <div class="text-[11px] font-mono text-zinc-700 dark:text-zinc-300">
                                    Auto-Active Primitives: {{ implode(', ', $activeSectorObj['mandatory_primitives'] ?? []) }}
                                </div>
                                <div class="text-[11px] font-mono text-amber-950 dark:text-amber-300">
                                    Regulasi &amp; Pajak: {{ implode(', ', $activeSectorObj['default_compliance_standards'] ?? []) }}
                                </div>
                            </div>
                        </div>
                    @elseif($wizardStep === 2)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5">
                            @foreach(($catalog['stack_presets'] ?? []) as $sp)
                                @php $spSize = $sizingProfiles[$sp['code']] ?? []; @endphp
                                <button
                                    type="button"
                                    wire:click="selectStack('{{ $sp['code'] }}')"
                                    class="p-3 text-left border rounded-xs cursor-pointer {{ $stackCode === $sp['code'] ? 'bg-emerald-50 border-emerald-500 dark:bg-emerald-950/60 dark:border-emerald-500' : 'bg-white border-zinc-300 dark:bg-zinc-900 dark:border-zinc-800' }}"
                                >
                                    <div class="font-mono text-xs font-black text-zinc-950 dark:text-zinc-100">[{{ $sp['short_code'] }}] {{ $sp['topology'] }}</div>
                                    <div class="text-[11px] font-bold text-zinc-800 dark:text-zinc-200 mt-0.5">{{ $sp['name']['id'] ?? '' }}</div>
                                    <div class="mt-2 pt-2 border-t border-zinc-200 dark:border-zinc-800 font-mono text-[10px] text-zinc-700 dark:text-zinc-300 space-y-0.5">
                                        <div>{{ $spSize['build_speed'] ?? '' }}</div>
                                        <div>{{ $spSize['concurrency'] ?? '' }}</div>
                                        <div>{{ $spSize['coolify_sizing'] ?? '' }}</div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="space-y-3">
                            <div>
                                <label class="block font-mono text-xs font-black mb-1">Nama Proyek / Produk SaaS</label>
                                <input type="text" wire:model="projectName" class="w-full px-3 py-2 text-sm font-mono bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xs" />
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 font-mono text-xs">
                                <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-400 dark:border-emerald-700 text-emerald-950 dark:text-emerald-200 font-bold">✓ Strict 26-Char ULID Primary Keys</div>
                                <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-400 dark:border-emerald-700 text-emerald-950 dark:text-emerald-200 font-bold">✓ Zero External CDN &amp; Inline SVG QR</div>
                                <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-400 dark:border-emerald-700 text-emerald-950 dark:text-emerald-200 font-bold">✓ Linear-Style Crisp Geometry (Zero Capsule)</div>
                                <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-400 dark:border-emerald-700 text-emerald-950 dark:text-emerald-200 font-bold">✓ Ponytail + ECC + 4-Pillar AI Sync</div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-4 py-3 bg-zinc-100 dark:bg-zinc-900 border-t border-zinc-300 dark:border-zinc-800 flex items-center justify-between">
                    <span class="font-mono text-xs text-zinc-700 dark:text-zinc-300">Determinism Score: <strong>{{ $detScore }}%</strong></span>
                    <div class="flex gap-2">
                        @if($wizardStep < 3)
                            <button type="button" wire:click="$set('wizardStep', {{ $wizardStep + 1 }})" class="px-4 py-1.5 bg-zinc-950 text-white dark:bg-emerald-500 dark:text-zinc-950 font-mono text-xs font-black rounded-xs cursor-pointer">
                                Lanjut &rarr;
                            </button>
                        @else
                            <button type="button" wire:click="$set('showWizardModal', false)" class="px-4 py-1.5 bg-emerald-600 text-white dark:bg-emerald-500 dark:text-zinc-950 font-mono text-xs font-black rounded-xs cursor-pointer">
                                🚀 Terapkan ke 3-Pane Studio
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- =================================================================== -->
    <!-- SCREEN 4 MODAL: SIDE-BY-SIDE SLOP DIFF PREVIEWER                    -->
    <!-- =================================================================== -->
    @if($showSlopDiffModal)
        <div class="fixed inset-0 z-50 bg-black/75 backdrop-blur-xs flex items-center justify-center p-4 select-text">
            <div class="w-full max-w-5xl bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-sm shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="px-4 py-3 bg-zinc-950 text-white flex items-center justify-between border-b border-zinc-800">
                    <span class="font-mono text-xs font-black uppercase tracking-wider">
                        ⚖️ SIDE-BY-SIDE ANTI-SLOP DIFF PREVIEWER (RAW AI PROMPT VS NERIAHPRO PROJECT OS v4.0)
                    </span>
                    <button type="button" wire:click="$set('showSlopDiffModal', false)" class="px-2 py-0.5 bg-zinc-800 hover:bg-zinc-700 text-xs font-mono font-bold cursor-pointer">
                        ✕ TUTUP
                    </button>
                </div>
                <div class="p-4 overflow-y-auto studio-scroll space-y-4">
                    @foreach($slopDiffPairs as $pair)
                        <div class="border border-zinc-300 dark:border-zinc-800 rounded-xs overflow-hidden">
                            <div class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-900 font-mono text-xs font-black text-zinc-950 dark:text-zinc-100 border-b border-zinc-300 dark:border-zinc-800">
                                {{ $pair['dimension']['id'] ?? '' }}
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-zinc-300 dark:divide-zinc-800">
                                <div class="p-3 bg-red-50/60 dark:bg-red-950/20 space-y-1.5">
                                    <span class="px-1.5 py-0.5 bg-red-200 text-red-950 dark:bg-red-900 dark:text-red-100 font-mono text-[10px] font-black">{{ $pair['raw_ai_slop']['badge'] }}</span>
                                    <p class="text-xs text-zinc-800 dark:text-zinc-300">{{ $pair['raw_ai_slop']['summary']['id'] }}</p>
                                    <pre class="p-2 bg-zinc-950 text-red-200 text-[10px] font-mono overflow-x-auto">{{ $pair['raw_ai_slop']['code'] }}</pre>
                                </div>
                                <div class="p-3 bg-emerald-50/60 dark:bg-emerald-950/20 space-y-1.5">
                                    <span class="px-1.5 py-0.5 bg-emerald-200 text-emerald-950 dark:bg-emerald-900 dark:text-emerald-100 font-mono text-[10px] font-black">{{ $pair['project_os_v4']['badge'] }}</span>
                                    <p class="text-xs text-zinc-800 dark:text-zinc-300">{{ $pair['project_os_v4']['summary']['id'] }}</p>
                                    <pre class="p-2 bg-zinc-950 text-emerald-200 text-[10px] font-mono overflow-x-auto">{{ $pair['project_os_v4']['code'] }}</pre>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
