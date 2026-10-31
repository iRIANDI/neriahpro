<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\PosProject;
use App\Models\VisionBlueprint;
use App\Services\BlueprintDeltaEvolutionService;
use App\Services\BlueprintDoctorService;
use App\Services\BlueprintSlopScannerService;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Services\ScaffoldGeneratorService;
use App\Support\PosLifecycleAndStudioCatalog;
use App\Support\PosSectorUiArchetypeCatalog;
use App\Support\PosStackPresetCatalog;
use App\Support\ProjectOsMatrixCatalog;
use Livewire\Attributes\Locked;
use Livewire\Component;

class BlueprintArchitectStudio extends Component
{
    #[Locked]
    public ?string $blueprintId = null;

    #[Locked]
    public ?string $posProjectId = null;

    public ?string $slug = null;
    public string $projectName = 'Nusantara Enterprise Workspace';
    public string $currentVersion = 'v1.0';
    public string $sectorCode = 'SECTOR_02_HOSPITALITY_SPACE';
    public string $stackCode = 'STACK_M1_LARAVEL_MONOLITH';
    public array $selectedPrimitives = [
        'PRIMITIVE_RBAC_TENANCY',
        'PRIMITIVE_BOOKING_TIMESLOT',
        'PRIMITIVE_DOUBLE_ENTRY_LEDGER',
    ];

    // 3-Pane Studio UI State
    public string $centerTab = 'schema'; // schema | states | ui_print | decisions | delta_verify
    public string $rightDockMode = 'phases'; // phases | agents_md | graph_json | mcp_json | env_blueprint
    public int $activePhase = 1;
    public bool $showWizardModal = false;
    public int $wizardStep = 1;
    public bool $showSlopDiffModal = false;

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

        $blueprint = null;
        if ($slug) {
            $blueprint = VisionBlueprint::query()
                ->where('slug', $slug)
                ->orWhere('id', $slug)
                ->first();
        } else {
            $blueprint = VisionBlueprint::query()->orderBy('id', 'desc')->first();
        }

        if ($blueprint) {
            $this->hydrateFromBlueprint($blueprint);
        } else {
            $this->showWizardModal = request()->boolean('wizard', false);
        }
    }

    protected function hydrateFromBlueprint(VisionBlueprint $blueprint): void
    {
        $this->blueprintId = $blueprint->id;
        $this->slug = $blueprint->slug;
        $this->projectName = (string) ($blueprint->nama_bisnis ?: 'Enterprise Blueprint');

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
        }

        $meta = is_array($blueprint->user_metadata) ? $blueprint->user_metadata : [];
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
                'slug' => $this->slug ?: 'studio-live-workspace',
                'user_metadata' => [
                    'primary_sector' => $this->sectorCode,
                    'selected_primitives' => $this->selectedPrimitives,
                    'stack_preset' => $this->stackCode,
                ],
            ]);

        $schema = ProjectOsBlueprintCompilerService::compileUniversalSchema($bp, [
            'sector_code' => $this->sectorCode,
            'primitive_codes' => $this->selectedPrimitives,
            'stack_preset' => $this->stackCode,
            'custom_decision_rules' => $this->customDecisionRules,
            'persist_project' => false,
        ]);

        if (! empty($this->customTables)) {
            $schema['data_dictionary'] = array_merge($schema['data_dictionary'] ?? [], $this->customTables);
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

        $bpDummy = $this->blueprintId ? VisionBlueprint::query()->find($this->blueprintId) : null;
        $compiledRightContent = match ($this->rightDockMode) {
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

        return view('livewire.blueprint-architect-studio', [
            'catalog' => $catalog,
            'schema' => $schema,
            'doctor' => $doctor,
            'phases' => $phases,
            'selectedPhaseObj' => $selectedPhaseObj,
            'compiledRightContent' => $compiledRightContent,
            'compiledDecisionPhp' => $compiledDecisionPhp,
            'sizingProfiles' => PosLifecycleAndStudioCatalog::stackServerSizingProfiles(),
            'slopDiffPairs' => PosLifecycleAndStudioCatalog::slopVsProjectOsDiffPairs(),
        ])->layout('components.layouts.studio', [
            'title' => 'Project OS Architect Studio v4.0',
        ]);
    }
}

