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
use App\Support\PosInclusiveCommercialCatalog;
use App\Support\PosLifecycleAndStudioCatalog;
use App\Support\PosRealWorldRuntimeCatalog;
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

    // v8.0 Inclusive Dual-Persona UX + 36 Visual UI Blueprints + Tier-1 Jurisdiction + White-Label Security
    public string $personaMode = PosInclusiveCommercialCatalog::PERSONA_ARCHITECT_PRO;
    public int $storyboardStep = 1;
    public string $selectedUiVariant = '';
    public string $navigationShell = '';
    public string $outputLocale = 'id';
    public bool $whiteLabelEnabled = false;
    public string $whiteLabelAgencyName = '';
    public string $whiteLabelLegalEntity = '';
    public string $mcpRevokeNotice = '';

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

        $queryPersona = (string) request()->query('persona', '');
        if ($queryPersona !== '') {
            $this->personaMode = PosInclusiveCommercialCatalog::normalizePersonaMode($queryPersona);
        }

        $queryUiVariant = (string) request()->query('ui_variant', '');
        if ($queryUiVariant !== '') {
            $this->selectedUiVariant = strtoupper(trim($queryUiVariant));
        }

        $queryShell = (string) request()->query('shell', '');
        if ($queryShell !== '') {
            $this->navigationShell = strtoupper(trim($queryShell));
        }

        $queryLocale = (string) request()->query('output_locale', '');
        if ($queryLocale !== '') {
            $this->outputLocale = PosInclusiveCommercialCatalog::normalizeOutputLocale($queryLocale);
        }

        if ($this->selectedUiVariant === '') {
            $uiDefault = PosInclusiveCommercialCatalog::visualUiContractForSector($this->sectorCode, null, null, $this->outputLocale);
            $this->selectedUiVariant = (string) ($uiDefault['selected_variant_code'] ?? 'UI_01A_POS_SPLIT_TERMINAL');
            $this->navigationShell = (string) ($uiDefault['navigation_shell']['code'] ?? 'FULLSCREEN_OPERATIONAL_KIOSK');
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
            if (! empty($posProject->persona_mode)) {
                $this->personaMode = PosInclusiveCommercialCatalog::normalizePersonaMode((string) $posProject->persona_mode);
            }
            if (! empty($posProject->selected_ui_variant)) {
                $this->selectedUiVariant = (string) $posProject->selected_ui_variant;
            }
            if (! empty($posProject->navigation_shell)) {
                $this->navigationShell = (string) $posProject->navigation_shell;
            }
            if (! empty($posProject->output_locale)) {
                $this->outputLocale = PosInclusiveCommercialCatalog::normalizeOutputLocale((string) $posProject->output_locale);
            }
            if (is_array($posProject->white_label_branding_json)) {
                $this->whiteLabelEnabled = ! empty($posProject->white_label_branding_json['enabled']);
                $this->whiteLabelAgencyName = (string) ($posProject->white_label_branding_json['agency_name'] ?? '');
                $this->whiteLabelLegalEntity = (string) ($posProject->white_label_branding_json['legal_entity'] ?? '');
            }
        }

        $meta = is_array($blueprint->user_metadata) ? $blueprint->user_metadata : [];
        if ($this->selectedUiVariant === '' && ! empty($meta['selected_ui_variant'])) {
            $this->selectedUiVariant = (string) $meta['selected_ui_variant'];
        }
        if ($this->navigationShell === '' && ! empty($meta['navigation_shell'])) {
            $this->navigationShell = (string) $meta['navigation_shell'];
        }
        if (! empty($meta['output_locale']) && $this->outputLocale === 'id') {
            $this->outputLocale = PosInclusiveCommercialCatalog::normalizeOutputLocale((string) $meta['output_locale']);
        }
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

        if ($this->selectedUiVariant === '') {
            $uiDefault = PosInclusiveCommercialCatalog::visualUiContractForSector($this->sectorCode, null, null, $this->outputLocale);
            $this->selectedUiVariant = (string) ($uiDefault['selected_variant_code'] ?? '');
            $this->navigationShell = (string) ($uiDefault['navigation_shell']['code'] ?? '');
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

        $uiDefault = PosInclusiveCommercialCatalog::visualUiContractForSector($normalized, null, null, $this->outputLocale);
        $this->selectedUiVariant = (string) ($uiDefault['selected_variant_code'] ?? '');
        $this->navigationShell = (string) ($uiDefault['navigation_shell']['code'] ?? '');
    }

    public function switchPersonaMode(string $mode): void
    {
        $this->personaMode = PosInclusiveCommercialCatalog::normalizePersonaMode($mode);
        if ($this->posProjectId) {
            $proj = PosProject::query()->find($this->posProjectId);
            if ($proj && \Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'persona_mode')) {
                $proj->forceFill(['persona_mode' => $this->personaMode])->saveQuietly();
            }
        }
    }

    public function selectUiVariant(string $variantCode, ?string $shell = null): void
    {
        $resolved = PosInclusiveCommercialCatalog::visualUiContractForSector(
            $this->sectorCode,
            $variantCode,
            $shell,
            $this->outputLocale
        );
        $this->selectedUiVariant = (string) ($resolved['selected_variant_code'] ?? $variantCode);
        $this->navigationShell = (string) ($resolved['navigation_shell']['code'] ?? ($shell ?: 'SIDEBAR_ERP_DENSE'));

        if ($this->posProjectId) {
            $proj = PosProject::query()->find($this->posProjectId);
            if ($proj && \Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'selected_ui_variant')) {
                $proj->forceFill([
                    'selected_ui_variant' => $this->selectedUiVariant,
                    'navigation_shell' => $this->navigationShell,
                ])->saveQuietly();
            }
        }
    }

    public function selectOutputLocale(string $locale): void
    {
        $this->outputLocale = PosInclusiveCommercialCatalog::normalizeOutputLocale($locale);
        $jurisdiction = PosInclusiveCommercialCatalog::jurisdictionComplianceProfile($this->outputLocale);

        if ($this->posProjectId) {
            $proj = PosProject::query()->find($this->posProjectId);
            if ($proj && \Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'output_locale')) {
                $proj->forceFill([
                    'output_locale' => $this->outputLocale,
                    'jurisdiction_profile' => (string) ($jurisdiction['jurisdiction_code'] ?? 'ID_UU_PDP_PSAK'),
                ])->saveQuietly();
            }
        }
    }

    public function setStoryboardStep(int $step): void
    {
        $this->storyboardStep = max(1, min(5, $step));
    }

    public function selectVisualUiVariant(string $variantCode, ?string $shell = null): void
    {
        $this->selectUiVariant($variantCode, $shell);
    }

    public function switchOutputLocale(string $locale): void
    {
        $this->selectOutputLocale($locale);
    }

    public function persistStudioBlueprint(): void
    {
        $bp = $this->blueprintId ? VisionBlueprint::query()->find($this->blueprintId) : null;
        if (! $bp) {
            $baseSlug = Str::slug($this->projectName ?: 'proyek-enterprise') ?: 'proyek-enterprise';
            $user = auth()->user();
            $bp = VisionBlueprint::query()->create([
                'user_id' => $user?->id,
                'nama_bisnis' => $this->projectName ?: 'Proyek Enterprise Baru',
                'slug' => $baseSlug . '-' . strtolower(substr((string) Str::ulid(), -6)),
                'client_name' => $user?->name ?: 'Studio Principal Architect',
                'email' => $user?->email ?: 'studio@neriahpro.com',
                'phone' => '+628110000000',
                'project_status' => 'Draft',
                'prd_content' => ['ringkasan_eksekutif' => $this->projectName],
                'user_metadata' => [
                    'isolation_token' => $this->workspaceIsolationId,
                    'primary_sector' => $this->sectorCode,
                    'selected_primitives' => $this->selectedPrimitives,
                    'selected_menus' => $this->selectedMenus,
                    'stack_preset' => $this->stackCode,
                    'persona_mode' => $this->personaMode,
                    'selected_ui_variant' => $this->selectedUiVariant ?: null,
                    'navigation_shell' => $this->navigationShell ?: null,
                    'output_locale' => $this->outputLocale,
                ],
            ]);
            $this->blueprintId = $bp->id;
            $this->slug = $bp->slug;
        }

        $savedProject = app(ProjectOsBlueprintCompilerService::class)->compileAndPersist($bp, [
            'app_name' => $this->projectName,
            'sector_code' => $this->sectorCode,
            'primitive_codes' => $this->selectedPrimitives,
            'selected_menus' => $this->selectedMenus,
            'stack_preset' => $this->stackCode,
            'persona_mode' => $this->personaMode,
            'selected_ui_variant' => $this->selectedUiVariant ?: null,
            'navigation_shell' => $this->navigationShell ?: null,
            'output_locale' => $this->outputLocale,
            'isolation_token' => $this->workspaceIsolationId,
            'pos_project_id' => $this->posProjectId,
        ]);

        if ($savedProject->exists) {
            $this->posProjectId = $savedProject->id;
        }
    }

    public function saveStudioWhiteLabelBranding(): void
    {
        $payload = [
            'enabled' => $this->whiteLabelEnabled,
            'agency_name' => trim($this->whiteLabelAgencyName),
            'legal_entity' => trim($this->whiteLabelLegalEntity),
            'updated_at' => now()->toIso8601String(),
        ];

        if ($this->posProjectId) {
            $proj = PosProject::query()->find($this->posProjectId);
            if ($proj && \Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'white_label_branding_json')) {
                $proj->forceFill(['white_label_branding_json' => $payload])->saveQuietly();
                \App\Models\AuditLog::record(
                    action: 'whitelabel_branding_updated',
                    model: $proj,
                    newValues: $payload,
                    description: "White-Label Agency branding updated for PosProject [{$proj->id}]: " . ($payload['agency_name'] ?: 'Disabled'),
                    eventCategory: 'whitelabel_agency'
                );
            }
        }
    }

    public function revokeStudioMcpToken(): void
    {
        if ($this->posProjectId) {
            $proj = PosProject::query()->find($this->posProjectId);
            if ($proj) {
                $newToken = $proj->revokeMcpToken(auth()->id() ? (string) auth()->id() : null);
                $this->mcpRevokeNotice = 'Token MCP lama dicabut instan! Token baru: ' . substr($newToken, 0, 18) . '...';

                return;
            }
        }

        $this->mcpRevokeNotice = 'Token MCP simulasi berhasil dirotasi (npro_mcp_' . substr(strtolower((string) Str::ulid()), -10) . '...).';
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
            'persona_mode' => $this->personaMode,
            'selected_ui_variant' => $this->selectedUiVariant ?: null,
            'navigation_shell' => $this->navigationShell ?: null,
            'output_locale' => $this->outputLocale,
            'white_label_branding' => [
                'enabled' => $this->whiteLabelEnabled,
                'agency_name' => $this->whiteLabelAgencyName,
                'legal_entity' => $this->whiteLabelLegalEntity,
            ],
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
            'syntax_lock_md' => PosRealWorldRuntimeCatalog::renderSyntaxLock2026Markdown($schema),
            'troubleshooting_md' => PosRealWorldRuntimeCatalog::renderTroubleshootingInstinctsMarkdown($schema),
            'client_sop_md' => PosRealWorldRuntimeCatalog::renderClientSopManualMarkdown($schema, 'id'),
            'pg16_partition_sql' => PosRealWorldRuntimeCatalog::renderPg16RangePartitioningSql($schema['data_dictionary'] ?? [], $this->projectName),
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
            'storyboardWizard' => PosInclusiveCommercialCatalog::businessStoryboardWizard($this->outputLocale),
            'cveAlerts' => $schema['living_cve_security_alerts'] ?? PosInclusiveCommercialCatalog::livingCveSecurityAlerts($this->stackCode, $this->sectorCode),
            'communityMarketplace' => PosInclusiveCommercialCatalog::communityPrimitivesMarketplace(),
            'visualUiContract' => $schema['visual_frontend_ui_contract'] ?? [],
            'jurisdictionProfile' => $schema['jurisdiction_compliance_profile'] ?? [],
        ])->layout('components.layouts.studio', [
            'title' => 'Project OS Architect Studio v7.0 / v8.0 (Dual-Persona, 36 Visual UI & 15-Pillar Freeze)',
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

