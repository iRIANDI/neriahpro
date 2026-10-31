<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\PosProject;
use App\Models\VisionBlueprint;
use App\Services\BlueprintDeltaEvolutionService;
use App\Services\BlueprintDoctorService;
use App\Services\BlueprintSlopScannerService;
use App\Services\PosCommercialBridgeService;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Services\ScaffoldGeneratorService;
use App\Services\TopologicalPromptChunker;
use App\Support\PosEnterpriseGuaranteeCatalog;
use App\Support\PosLifecycleAndStudioCatalog;
use App\Support\PosSectorUiArchetypeCatalog;
use App\Support\PosStackPresetCatalog;
use App\Support\ProjectOsMatrixCatalog;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Component;

class BlueprintArchitectStudio extends Component
{
    #[Locked]
    public ?string $blueprintId = null;

    #[Locked]
    public ?string $posProjectId = null;

    #[Locked]
    public string $workspaceIsolationId = '';

    public ?string $slug = null;
    public string $projectName = 'Proyek Enterprise Baru';
    public string $currentVersion = 'v1.0';
    public string $sectorCode = 'SECTOR_01_COMMERCE_OMNICHANNEL';
    public string $stackCode = 'STACK_M1_LARAVEL_MONOLITH';
    public array $selectedPrimitives = [
        'PRIMITIVE_RBAC_TENANCY',
        'PRIMITIVE_CATALOG_SKU',
        'PRIMITIVE_INVENTORY_LEDGER',
        'PRIMITIVE_DOUBLE_ENTRY_LEDGER',
    ];
    public array $selectedMenus = [];

    // Dual-Mode Satu Pintu: 'RETAIL_SELF_DEV' vs 'MITRA_TURNKEY_PROJECT'
    public string $engagementMode = PosCommercialBridgeService::MODE_RETAIL;

    // 3-Pane Studio UI State
    public string $centerTab = 'schema'; // schema | states | ui_print | decisions | delta_verify | commercial_mitra | menus_glue
    public string $rightDockMode = 'phases'; // phases | dag_subparts | symbol_manifest | enterprise_menu | agents_md | graph_json | mcp_json | env_blueprint
    public int $activePhase = 1;
    public string $activeSubPartCode = '1.0';
    public bool $showWizardModal = false;
    public int $wizardStep = 1;
    public bool $showSlopDiffModal = false;

    // Mitra Commercial, SPK, UAT & Digital BAST State
    public array $uatChecklistState = [];
    public array $bastSignoffState = [];
    public array $latestSpkResult = [];
    public array $latestAddendumResult = [];
    public string $bastSignerName = '';
    public string $bastSignerEmail = '';

    // Custom Table & Unguarded Status Simulation (Blueprint Doctor Live Guard)
    public string $newCustomTableName = '';
    public bool $newCustomTableHasStatus = true;
    public array $customTables = [];
    public array $autoStateMachineTables = [];

    // Feature #3: Decision Matrix Interactive Builder
    public array $customDecisionRules = [];
    public string $newRuleCondA = '';
    public string $newRuleCondB = '';
    public string $newRuleCondC = '';
    public string $newRuleAction = '';
    public string $newRuleFormula = '';

    // Feature #1: Day-30 Delta Change Request Builder
    public string $deltaTitle = '';
    public string $deltaSummary = '';
    public array $latestDeltaResult = [];

    // Feature #2 & Screen 5: Phase Progress & Execution Drift Audit Terminal
    public array $phaseProgress = [];
    public string $verifyTerminalInput = '';
    public array $driftBadges = [
        'database_ulid_strict_pgsql' => '100% Pass',
        'zero_external_cdn_and_popups' => '100% Pass',
        'anti_slop_ui_geometry' => '100% Pass',
        'four_pillar_ai_and_audit_sync' => '100% Pass',
    ];

    public function mount(?string $slug = null): void
    {
        ProjectOsMatrixCatalog::ensureSeeded();
        $this->workspaceIsolationId = 'ws_' . strtolower((string) Str::ulid());

        $blueprint = null;
        if ($slug && ! request()->boolean('new', false) && ! request()->boolean('reset', false)) {
            $blueprint = VisionBlueprint::query()
                ->where('slug', $slug)
                ->orWhere('id', $slug)
                ->first();
        }
        // HERMETIC ISOLATION (v6.0): NEVER auto-load the most recent database blueprint when $slug is omitted!

        if ($blueprint) {
            $this->hydrateFromBlueprint($blueprint);
        } else {
            $this->syncDefaultEnterpriseMenusForSector($this->sectorCode);
            $this->showWizardModal = request()->boolean('wizard', false);
        }

        // Satu Pintu URL Query Overrides (?mode=mitra|retail, ?track=mitra|retail, ?sector=..., ?stack=...)
        $queryMode = strtolower((string) (request()->query('mode') ?? request()->query('track') ?? ''));
        if (in_array($queryMode, ['mitra', 'mitra_turnkey_project', 'studio', 'project'], true)) {
            $this->engagementMode = PosCommercialBridgeService::MODE_MITRA;
            $this->centerTab = 'commercial_mitra';
        } elseif (in_array($queryMode, ['retail', 'retail_self_dev', 'self_service'], true)) {
            $this->engagementMode = PosCommercialBridgeService::MODE_RETAIL;
        }

        $querySector = (string) request()->query('sector', '');
        if ($querySector !== '') {
            $this->selectSector($querySector);
        }

        $queryStack = (string) request()->query('stack', '');
        if ($queryStack !== '') {
            $this->selectStack($queryStack);
        }
    }

    protected function hydrateFromBlueprint(VisionBlueprint $blueprint): void
    {
        $this->blueprintId = $blueprint->id;
        $this->slug = $blueprint->slug;
        $this->projectName = (string) ($blueprint->nama_bisnis ?: 'Enterprise Blueprint');
        $this->bastSignerName = (string) ($blueprint->client_name ?: 'PIC Klien Resmi');
        $this->bastSignerEmail = (string) ($blueprint->email ?: 'client@neriahpro.com');

        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
        if (! $posProject) {
            $posProject = app(ProjectOsBlueprintCompilerService::class)->compileAndPersist($blueprint);
        }

        if ($posProject) {
            $this->posProjectId = $posProject->exists ? $posProject->id : null;
            $this->currentVersion = (string) ($posProject->current_version ?: 'v1.0');
            $this->phaseProgress = is_array($posProject->phase_progress_json) ? $posProject->phase_progress_json : [];
            if (! empty($posProject->drift_verification_json) && is_array($posProject->drift_verification_json)) {
                $this->driftBadges = array_merge($this->driftBadges, $posProject->drift_verification_json);
            }
            if (! empty($posProject->engagement_mode)) {
                $this->engagementMode = (string) $posProject->engagement_mode;
            }
            if (is_array($posProject->uat_checklist_json)) {
                $this->uatChecklistState = $posProject->uat_checklist_json;
            }
            if (is_array($posProject->bast_signoff_json)) {
                $this->bastSignoffState = $posProject->bast_signoff_json;
            }
        }

        $meta = is_array($blueprint->user_metadata) ? $blueprint->user_metadata : [];
        $tier = (string) ($meta['package_tier'] ?? $meta['retail_tier'] ?? '');
        if ($tier !== '' && \App\Support\PricingRegistry::isStudioContract($tier) && empty($posProject?->engagement_mode)) {
            $this->engagementMode = PosCommercialBridgeService::MODE_MITRA;
        }
        $schema = $posProject && ! empty($posProject->compiled_schema_json)
            ? (is_array($posProject->compiled_schema_json) ? $posProject->compiled_schema_json : (json_decode((string) $posProject->compiled_schema_json, true) ?: []))
            : [];

        $this->sectorCode = PosSectorUiArchetypeCatalog::normalizeSectorCode(
            (string) ($meta['primary_sector'] ?? $schema['project_metadata']['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL')
        );
        $this->stackCode = PosStackPresetCatalog::normalizePresetCode(
            (string) ($meta['stack_preset'] ?? $schema['project_metadata']['stack_preset'] ?? 'STACK_M1_LARAVEL_MONOLITH')
        );
        $injected = (array) ($meta['selected_primitives'] ?? $schema['project_metadata']['injected_primitives'] ?? []);
        if (! empty($injected)) {
            $this->selectedPrimitives = array_values(array_unique(array_map('strval', $injected)));
        }

        $savedMenus = (array) ($meta['selected_menus'] ?? []);
        if (! empty($savedMenus)) {
            $this->selectedMenus = array_values(array_unique(array_map('strval', $savedMenus)));
        } else {
            $this->syncDefaultEnterpriseMenusForSector($this->sectorCode);
        }
    }

    protected function syncDefaultEnterpriseMenusForSector(string $sectorCode): void
    {
        $matrix = PosEnterpriseGuaranteeCatalog::enterpriseMenuMatrixForSector($sectorCode);
        $codes = [];
        foreach (($matrix['divisions'] ?? []) as $div) {
            foreach (($div['menus'] ?? []) as $m) {
                if (! empty($m['code'])) {
                    $codes[] = (string) $m['code'];
                }
            }
        }
        $this->selectedMenus = array_values(array_unique($codes));
    }

    public function startFreshIsolatedProject(): void
    {
        $this->blueprintId = null;
        $this->posProjectId = null;
        $this->slug = null;
        $this->workspaceIsolationId = 'ws_' . strtolower((string) Str::ulid());
        $this->projectName = 'Proyek Enterprise Baru';
        $this->currentVersion = 'v1.0';
        $this->customTables = [];
        $this->autoStateMachineTables = [];
        $this->customDecisionRules = [];
        $this->phaseProgress = [];
        $this->uatChecklistState = [];
        $this->bastSignoffState = [];
        $this->latestSpkResult = [];
        $this->latestAddendumResult = [];
        $this->latestDeltaResult = [];
        $this->deltaTitle = '';
        $this->deltaSummary = '';
        $this->bastSignerName = '';
        $this->bastSignerEmail = '';
        $this->selectSector('SECTOR_01_COMMERCE_OMNICHANNEL');
    }

    public function toggleEnterpriseMenu(string $menuCode): void
    {
        if (in_array($menuCode, $this->selectedMenus, true)) {
            $this->selectedMenus = array_values(array_diff($this->selectedMenus, [$menuCode]));
        } else {
            $this->selectedMenus[] = $menuCode;
            $this->selectedMenus = array_values(array_unique($this->selectedMenus));
        }
    }

    public function selectAllEnterpriseMenus(): void
    {
        $this->syncDefaultEnterpriseMenusForSector($this->sectorCode);
    }

    public function selectSector(string $code): void
    {
        $normalized = PosSectorUiArchetypeCatalog::normalizeSectorCode($code);
        $this->sectorCode = $normalized;

        $catalog = ProjectOsBlueprintCompilerService::getCachedCatalog();
        foreach (($catalog['sectors'] ?? []) as $sec) {
            if (($sec['code'] ?? '') === $normalized) {
                $mandatory = (array) ($sec['mandatory_primitives'] ?? []);
                if (! empty($mandatory)) {
                    $this->selectedPrimitives = array_values(array_unique(array_merge(['PRIMITIVE_RBAC_TENANCY'], $mandatory)));
                }
                break;
            }
        }

        $this->syncDefaultEnterpriseMenusForSector($normalized);
    }

    public function selectStack(string $code): void
    {
        $this->stackCode = PosStackPresetCatalog::normalizePresetCode($code);
    }

    public function togglePrimitive(string $code): void
    {
        if (in_array($code, $this->selectedPrimitives, true)) {
            if ($code === 'PRIMITIVE_RBAC_TENANCY') {
                return;
            }
            $this->selectedPrimitives = array_values(array_diff($this->selectedPrimitives, [$code]));
        } else {
            $this->selectedPrimitives[] = $code;
            $this->selectedPrimitives = array_values(array_unique($this->selectedPrimitives));
        }
    }

    public function addCustomTable(): void
    {
        $clean = preg_replace('/[^a-z0-9_]/', '', strtolower(trim($this->newCustomTableName)));
        if ($clean === '') {
            return;
        }

        $cols = [
            ['name' => 'id', 'type' => 'ULID (CHAR 26)', 'primary' => true],
            ['name' => 'organization_id', 'type' => 'FOREIGN_ULID'],
            ['name' => 'amount_snapshot', 'type' => 'DECIMAL(19,4)'],
        ];
        if ($this->newCustomTableHasStatus) {
            $cols[] = ['name' => 'status', 'type' => 'VARCHAR(32)'];
        }

        $this->customTables[] = [
            'table' => $clean,
            'deletion_policy' => 'IMMUTABLE_LEDGER',
            'historical_snapshot_enforced' => true,
            'columns' => $cols,
        ];
        $this->newCustomTableName = '';
    }

    public function applyDoctorAutoFix(): void
    {
        $doctor = $this->computeDoctorReport($this->compileCurrentStudioSchema());
        $fix = $doctor['one_click_auto_fix'] ?? [];

        if (! empty($fix['merged_primitive_codes'])) {
            $this->selectedPrimitives = array_values(array_unique($fix['merged_primitive_codes']));
        }
        if (! empty($fix['recommended_stack_preset_code'])) {
            $this->stackCode = (string) $fix['recommended_stack_preset_code'];
        }
        if (! empty($fix['auto_state_machine_tables'])) {
            $this->autoStateMachineTables = array_values(array_unique(array_merge(
                $this->autoStateMachineTables,
                (array) $fix['auto_state_machine_tables']
            )));
        }
    }

    public function addDecisionRule(): void
    {
        if (trim($this->newRuleCondA) === '' || trim($this->newRuleAction) === '') {
            return;
        }

        $idx = count($this->customDecisionRules) + 1;
        $this->customDecisionRules[] = [
            'rule_id' => sprintf('DEC-CUSTOM-%02d', $idx),
            'priority' => 40 + ($idx * 5),
            'condition_a' => trim($this->newRuleCondA),
            'condition_b' => trim($this->newRuleCondB) ?: 'organization_active == true',
            'condition_c' => trim($this->newRuleCondC) ?: 'amount >= 0.0000',
            'action_code' => strtoupper(preg_replace('/[^A-Z0-9_]/i', '_', trim($this->newRuleAction))),
            'exact_formula' => trim($this->newRuleFormula) ?: 'net_amount = ROUND_HALF_UP(gross_amount, 4)',
            'fallback_behavior' => 'Gunakan tarif standar organisasi.',
        ];

        $this->newRuleCondA = '';
        $this->newRuleCondB = '';
        $this->newRuleCondC = '';
        $this->newRuleAction = '';
        $this->newRuleFormula = '';
    }

    public function togglePhaseVerified(int $phaseNum): void
    {
        $phaseNum = max(1, min(7, $phaseNum));
        $key = 'phase_' . $phaseNum;
        $current = $this->phaseProgress[$key]['status'] ?? 'PENDING';

        if ($current === 'VERIFIED') {
            unset($this->phaseProgress[$key]);
        } else {
            $this->phaseProgress[$key] = [
                'phase' => $phaseNum,
                'status' => 'VERIFIED',
                'badge' => 'Verified ✅',
                'ide_client' => 'architect-studio-web',
                'verified_at' => now()->toIso8601String(),
            ];
        }

        if ($this->posProjectId) {
            $proj = PosProject::query()->find($this->posProjectId);
            if ($proj && \Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'phase_progress_json')) {
                $proj->forceFill(['phase_progress_json' => $this->phaseProgress])->saveQuietly();
            }
        }
    }

    public function generateDeltaPrompt(BlueprintDeltaEvolutionService $deltaService): void
    {
        if (trim($this->deltaTitle) === '') {
            $this->deltaTitle = 'Penambahan Komisi Sales Berjenjang & Split-Payment';
        }

        $bp = $this->blueprintId ? VisionBlueprint::query()->find($this->blueprintId) : null;
        $proj = $this->posProjectId ? PosProject::query()->find($this->posProjectId) : null;

        $res = $deltaService->createChangeRequest($bp, $proj, [
            'title' => $this->deltaTitle,
            'change_summary' => $this->deltaSummary ?: $this->deltaTitle,
            'added_decision_rules' => $this->customDecisionRules,
        ], $bp !== null || $proj !== null);

        $this->latestDeltaResult = $res;
        $this->currentVersion = (string) ($res['to_version'] ?? $this->currentVersion);
    }

    public function runDriftVerification(BlueprintSlopScannerService $scanner): void
    {
        $snippetScan = $this->verifyTerminalInput !== ''
            ? $scanner->scanCodeSnippet($this->verifyTerminalInput, false)
            : ['violations' => [], 'compliance_score' => 100];

        $hasFailText = stripos($this->verifyTerminalInput, '[FAIL]') !== false;
        $clean = empty($snippetScan['violations']) && ! $hasFailText;

        $this->driftBadges = [
            'database_ulid_strict_pgsql' => $clean ? '100% Pass' : 'Action Needed',
            'zero_external_cdn_and_popups' => $clean ? '100% Pass' : 'Action Needed',
            'anti_slop_ui_geometry' => $clean ? '100% Pass' : 'Action Needed',
            'four_pillar_ai_and_audit_sync' => $clean ? '100% Pass' : 'Action Needed',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function compileCurrentStudioSchema(): array
    {
        $bp = $this->blueprintId
            ? VisionBlueprint::query()->find($this->blueprintId)
            : new VisionBlueprint([
                'nama_bisnis' => $this->projectName,
                'slug' => $this->slug ?: ('studio-' . substr($this->workspaceIsolationId ?: 'isolated', -8)),
                'user_metadata' => [
                    'primary_sector' => $this->sectorCode,
                    'selected_primitives' => $this->selectedPrimitives,
                    'selected_menus' => $this->selectedMenus,
                    'isolation_token' => $this->workspaceIsolationId ?: ('ws_' . strtolower((string) Str::ulid())),
                    'stack_preset' => $this->stackCode,
                ],
            ]);

        $schema = ProjectOsBlueprintCompilerService::compileUniversalSchema($bp, [
            'sector_code' => $this->sectorCode,
            'primitive_codes' => $this->selectedPrimitives,
            'selected_menus' => ! empty($this->selectedMenus) ? $this->selectedMenus : null,
            'isolation_token' => $this->workspaceIsolationId ?: ('ws_' . strtolower((string) Str::ulid())),
            'stack_preset' => $this->stackCode,
            'custom_decision_rules' => $this->customDecisionRules,
            'persist_project' => false,
        ]);

        if (! empty($this->customTables)) {
            $mergedTables = array_merge($schema['data_dictionary'] ?? [], $this->customTables);
            $dagPlan = TopologicalPromptChunker::buildTopologicalTableLayers($mergedTables);
            $schema['data_dictionary'] = $dagPlan['ordered_tables'];
            $schema['topological_dag_plan'] = $dagPlan;
            $schema['symbol_manifest'] = TopologicalPromptChunker::generateSymbolManifest($schema);
            $schema['sub_chunked_prompts'] = TopologicalPromptChunker::compileSubChunkedPrompts($schema, $schema['symbol_manifest']);
        }

        foreach ($this->autoStateMachineTables as $tblName) {
            $entity = ucfirst(rtrim($tblName, 's'));
            $schema['state_machines'][] = [
                'entity' => $entity,
                'enum_class' => "App\\Enums\\{$entity}Status",
                'states' => ['DRAFT', 'PENDING_REVIEW', 'APPROVED', 'ARCHIVED'],
                'notification_matrix' => [
                    ['on_state' => 'APPROVED', 'channels' => ['whatsapp_template_qr', 'filament_database_notification']],
                ],
            ];
        }

        return $schema;
    }

    /**
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    protected function computeDoctorReport(array $schema): array
    {
        $stateEntities = array_map(
            fn ($sm) => (string) ($sm['entity'] ?? ''),
            $schema['state_machines'] ?? []
        );

        return app(BlueprintDoctorService::class)->diagnose([
            'sector_code' => $this->sectorCode,
            'primitive_codes' => $this->selectedPrimitives,
            'stack_preset_code' => $this->stackCode,
            'custom_tables' => $this->customTables,
            'state_machine_entities' => $stateEntities,
            'compiled_metrics' => [
                'ulid_tables_count' => count($schema['data_dictionary'] ?? []),
                'state_machines_count' => count($schema['state_machines'] ?? []),
                'pessimistic_locks_count' => count($schema['domain_invariants']['concurrency_locks'] ?? []),
                'acceptance_tests_count' => count($schema['acceptance_test_matrix'] ?? []),
            ],
        ], false);
    }

    public function render()
    {
        $catalog = ProjectOsBlueprintCompilerService::getCachedCatalog();
        $schema = $this->compileCurrentStudioSchema();
        $doctor = $this->computeDoctorReport($schema);
        $phases = $schema['phased_prompt_sequencer'] ?? [];
        $selectedPhaseObj = $phases[max(0, $this->activePhase - 1)] ?? ($phases[0] ?? []);
        $subParts = $schema['sub_chunked_prompts'] ?? [];
        $selectedSubPartObj = collect($subParts)->firstWhere('part_code', $this->activeSubPartCode) ?? ($subParts[0] ?? []);

        $bpDummy = $this->blueprintId ? VisionBlueprint::query()->find($this->blueprintId) : null;
        $compiledRightContent = match ($this->rightDockMode) {
            'dag_subparts' => (string) ($selectedSubPartObj['prompt'] ?? ''),
            'symbol_manifest' => (string) json_encode($schema['symbol_manifest'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'enterprise_menu' => PosEnterpriseGuaranteeCatalog::renderEnterpriseMenuMarkdown($schema['enterprise_menu_matrix'] ?? []),
            'agents_md' => ProjectOsBlueprintCompilerService::renderUnifiedAgentsMarkdown($schema),
            'graph_json' => ProjectOsBlueprintCompilerService::renderInitialGraphifyJson($schema),
            'mcp_json' => ScaffoldGeneratorService::generateMcpJsonFile($bpDummy),
            'env_blueprint' => ScaffoldGeneratorService::generateEnvBlueprintFile($schema, $this->projectName),
            default => (string) ($selectedPhaseObj['prompt'] ?? ''),
        };

        $compiledDecisionPhp = PosLifecycleAndStudioCatalog::compileDecisionTablesToPhpClass(
            $schema['decision_tables_matrix'] ?? [],
            $this->projectName
        );

        $bridge = app(PosCommercialBridgeService::class);
        $posProj = $this->posProjectId ? PosProject::query()->find($this->posProjectId) : null;
        $commercialQuotation = $bridge->calculateScopeQuotation($schema);
        $uatChecklist = $bridge->buildUatChecklist($schema, $this->uatChecklistState);
        $milestoneGantt = $bridge->buildMilestoneGanttAndStaging($bpDummy, $posProj, $schema);

        return view('livewire.blueprint-architect-studio', [
            'catalog' => $catalog,
            'schema' => $schema,
            'doctor' => $doctor,
            'phases' => $phases,
            'subParts' => $subParts,
            'selectedSubPartObj' => $selectedSubPartObj,
            'selectedPhaseObj' => $selectedPhaseObj,
            'compiledRightContent' => $compiledRightContent,
            'compiledDecisionPhp' => $compiledDecisionPhp,
            'sizingProfiles' => PosLifecycleAndStudioCatalog::stackServerSizingProfiles(),
            'slopDiffPairs' => PosLifecycleAndStudioCatalog::slopVsProjectOsDiffPairs(),
            'commercialQuotation' => $commercialQuotation,
            'uatChecklist' => $uatChecklist,
            'milestoneGantt' => $milestoneGantt,
            'guidedJourneySteps' => PosCommercialBridgeService::guidedClientJourneySteps(),
        ])->layout('components.layouts.studio', [
            'title' => 'Project OS Architect Studio v6.0 (5 Deterministic Guarantees & Hermetic Isolation)',
        ]);
    }

    public function switchEngagementMode(string $mode): void
    {
        $normalized = $mode === PosCommercialBridgeService::MODE_MITRA
            ? PosCommercialBridgeService::MODE_MITRA
            : PosCommercialBridgeService::MODE_RETAIL;

        $this->engagementMode = $normalized;

        if ($normalized === PosCommercialBridgeService::MODE_MITRA && $this->centerTab === 'schema') {
            $this->centerTab = 'commercial_mitra';
        } elseif ($normalized === PosCommercialBridgeService::MODE_RETAIL && $this->centerTab === 'commercial_mitra') {
            $this->centerTab = 'schema';
        }

        if ($this->posProjectId) {
            $proj = PosProject::query()->find($this->posProjectId);
            if ($proj && \Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'engagement_mode')) {
                $proj->forceFill(['engagement_mode' => $normalized])->saveQuietly();
            }
        }
    }

    public function freezeScopeAndIssueSpk(PosCommercialBridgeService $bridge): void
    {
        $bp = $this->blueprintId ? VisionBlueprint::query()->find($this->blueprintId) : null;
        if (! $bp) {
            $schema = $this->compileCurrentStudioSchema();
            $quot = $bridge->calculateScopeQuotation($schema);
            $this->latestSpkResult = [
                'success' => true,
                'scope_hash_full' => $quot['scope_hash_full'],
                'scope_hash_short' => $quot['scope_hash_short'],
                'quotation' => $quot,
                'document_title' => 'Simulasi SPK & Lampiran Scope SHA-256 - ' . $this->projectName,
            ];

            return;
        }

        $proj = $this->posProjectId ? PosProject::query()->find($this->posProjectId) : null;
        $this->latestSpkResult = $bridge->lockScopeAndGenerateSpk($bp, $proj);
        $this->engagementMode = PosCommercialBridgeService::MODE_MITRA;
    }

    public function approveStudioAddendum(PosCommercialBridgeService $bridge): void
    {
        $crId = $this->latestDeltaResult['id'] ?? null;
        if (! $crId) {
            return;
        }

        $cr = \App\Models\PosChangeRequest::query()->find($crId);
        if (! $cr) {
            return;
        }

        $this->latestAddendumResult = $bridge->approveAddendumAndCreateInvoice($cr, [
            'signer_name' => $this->bastSignerName ?: 'PIC Klien Resmi',
            'signer_email' => $this->bastSignerEmail ?: 'client@neriahpro.com',
        ]);
    }

    public function toggleStudioUatScenario(string $scenarioId, PosCommercialBridgeService $bridge): void
    {
        $schema = $this->compileCurrentStudioSchema();
        $proj = $this->posProjectId ? PosProject::query()->find($this->posProjectId) : null;

        if ($proj) {
            $this->uatChecklistState = $bridge->toggleUatScenario(
                $proj,
                $schema,
                $scenarioId,
                null,
                $this->bastSignerName ?: 'Client UAT Reviewer'
            );
        } else {
            $current = $bridge->buildUatChecklist($schema, $this->uatChecklistState);
            $next = [];
            foreach ($current['scenarios'] as $sc) {
                if ($scenarioId === 'ALL' || $sc['id'] === $scenarioId) {
                    $sc['passed'] = ! $sc['passed'];
                    $sc['checked_at'] = $sc['passed'] ? now()->toIso8601String() : null;
                }
                $next[] = $sc;
            }
            $this->uatChecklistState = $bridge->buildUatChecklist($schema, ['scenarios' => $next]);
        }
    }

    public function signStudioDigitalBast(PosCommercialBridgeService $bridge): void
    {
        $bp = $this->blueprintId ? VisionBlueprint::query()->find($this->blueprintId) : null;
        $proj = $this->posProjectId ? PosProject::query()->find($this->posProjectId) : null;

        if ($bp) {
            $res = $bridge->signOffUatAndIssueBast($bp, $proj, [
                'signer_name' => $this->bastSignerName ?: 'PIC Klien Resmi',
                'signer_email' => $this->bastSignerEmail ?: 'client@neriahpro.com',
                'mark_all_passed' => true,
            ]);
            if (! empty($res['bast'])) {
                $this->bastSignoffState = $res['bast'];
                $this->uatChecklistState = $res['uat'] ?? $this->uatChecklistState;
            }
        } else {
            $schema = $this->compileCurrentStudioSchema();
            $quot = $bridge->calculateScopeQuotation($schema);
            $this->toggleStudioUatScenario('ALL', $bridge);
            $this->bastSignoffState = [
                'bast_number' => 'BAST/NPRO/' . now()->format('Y/m') . '/SIMUL',
                'bast_sha256' => hash('sha256', $this->projectName . '|' . $quot['scope_hash_full']),
                'signer_name' => $this->bastSignerName ?: 'PIC Klien Resmi',
                'signed_at' => now()->toIso8601String(),
                'formatted_final_termin' => $quot['termin_schedule'][count($quot['termin_schedule']) - 1]['formatted_amount'] ?? 'Rp 0',
            ];
        }
    }
}

