<?php

namespace App\Http\Controllers\Blueprint;

use App\Http\Controllers\Blueprint\Concerns\AuthorizesBlueprintAccess;
use App\Http\Controllers\Controller;
use App\Mail\BlueprintReadyNotificationMail;
use App\Models\AuditLog;
use App\Models\BlueprintVoucher;
use App\Models\CmsGlobalSetting;
use App\Models\Document;
use App\Models\LeadContact;
use App\Models\PosChangeRequest;
use App\Models\PosImpactGrantApplication;
use App\Models\PosProject;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\BlueprintDeltaEvolutionService;
use App\Services\BlueprintDiscoveryService;
use App\Services\BlueprintDoctorService;
use App\Services\BlueprintKpiCascadeService;
use App\Services\BlueprintRefinementService;
use App\Services\BlueprintSlopScannerService;
use App\Services\BrownfieldMigrationAuditorService;
use App\Services\Legal\LegalDocumentVaultService;
use App\Services\MidtransSnapService;
use App\Services\PosCommercialBridgeService;
use App\Services\PrdGeneratorService;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Services\ScaffoldGeneratorService;
use App\Services\Security\DocumentThreatSanitizerService;
use App\Services\TopologicalPromptChunker;
use App\Support\ContractLegalHelper;
use App\Support\InputHygieneStandard;
use App\Support\PosAgentShieldAndCoDesignCatalog;
use App\Support\PosDualZoneFrontendAndSaasCatalog;
use App\Support\PosEnterpriseGuaranteeCatalog;
use App\Support\PosInclusiveCommercialCatalog;
use App\Support\PosRealWorldRuntimeCatalog;
use App\Support\PosSectorUiArchetypeCatalog;
use App\Support\PosStackPresetCatalog;
use App\Support\PricingRegistry;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlueprintAuditController extends Controller
{
    use AuthorizesBlueprintAccess;

    /**
     * Killer Feature #6: One-Command CLI Bootstrapper (`curl -sL https://neriahpro.com/init/{identifier} | bash`).
     * Resolves VisionBlueprint (by exact ULID `id` or exact `slug`) or PosProject (by exact ULID `id`).
     */
    public function cliInitScript(Request $request, string $identifier)
    {
        $blueprint = VisionBlueprint::query()->where('id', $identifier)->first()
            ?? VisionBlueprint::query()->where('slug', $identifier)->first();

        $posProject = null;
        if (! $blueprint && Schema::hasTable('pos_projects')) {
            $posProject = PosProject::query()->where('id', $identifier)->first();
            if ($posProject && $posProject->vision_blueprint_id) {
                $blueprint = VisionBlueprint::query()->find($posProject->vision_blueprint_id);
            }
        }

        if (! $blueprint && ! $posProject) {
            $notFoundScript = "#!/usr/bin/env bash\necho \"[Project OS] Error 404: Blueprint '{$identifier}' tidak ditemukan.\" >&2\nexit 1\n";

            return response($notFoundScript, 404, [
                'Content-Type' => 'text/x-shellscript; charset=UTF-8',
            ]);
        }

        if ($blueprint) {
            $prd = $blueprint->prd_content ?? [];
            if (empty($prd)) {
                $prd = PrdGeneratorService::generate($blueprint);
            }
            $schema = $prd['universal_blueprint_schema']
                ?? ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => true]);
            $appName = $blueprint->nama_bisnis ?: ($schema['project_metadata']['name'] ?? 'Enterprise App');
            $cursorRules = PrdGeneratorService::toCursorrules($blueprint, $prd);
        } else {
            $schema = json_decode((string) $posProject->compiled_schema_json, true) ?: [];
            $appName = $posProject->app_name ?: 'Enterprise App';
            $cursorRules = (string) $posProject->compiled_agents_md;
        }

        $phasedPrompts = $schema['phased_prompt_sequencer']
            ?? ProjectOsBlueprintCompilerService::compilePhasedPrompts($schema);

        $filesToMaterialize = [
            'AGENTS.md' => ProjectOsBlueprintCompilerService::renderUnifiedAgentsMarkdown($schema),
            'CLAUDE.md' => $cursorRules,
            '.cursorrules' => $cursorRules,
            '.claude/settings.json' => DocumentThreatSanitizerService::renderClaudeSettingsJson($schema),
            '.agents/APPROVED_DEPENDENCY_MANIFEST.json' => (string) json_encode(
                $schema['approved_dependency_manifest'] ?? DocumentThreatSanitizerService::approvedDependencyManifest(
                    (string) ($schema['project_metadata']['stack_preset'] ?? 'STACK_M1_LARAVEL_MONOLITH'),
                    (string) ($schema['project_metadata']['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL')
                ),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ),
            'PROJECT_OS_EXECUTION_BLUEPRINT.md' => ProjectOsBlueprintCompilerService::renderExecutionBlueprintMarkdown($schema),
            '.agents/PONYTAIL.md' => ScaffoldGeneratorService::generatePonytailProtocolMarkdown(),
            '.agents/STACK_TOPOLOGY_CONTRACT.md' => ProjectOsBlueprintCompilerService::renderStackTopologyContractMarkdown($schema),
            '.agents/UI_UX_CONTRACT.md' => ProjectOsBlueprintCompilerService::renderUiUxContractMarkdown(),
            '.agents/ENGINEERING_RULES.md' => ProjectOsBlueprintCompilerService::renderEngineeringRulesMarkdown(),
            '.agents/AGENT_HARNESS_ECC.md' => ProjectOsBlueprintCompilerService::renderAgentHarnessEccMarkdown(),
            '.agents/PRINT_THERMAL_AND_A4_CONTRACT.md' => ProjectOsBlueprintCompilerService::renderPrintSpecMarkdown($schema),
            '.agents/DAY2_OPERATIONS_AND_SECURITY.md' => ProjectOsBlueprintCompilerService::renderDayTwoOpsMarkdown($schema),
            '.agents/DECISION_TABLES_CONTRACT.md' => ProjectOsBlueprintCompilerService::renderDecisionTablesMarkdown($schema),
            '.agents/SYNTAX_LOCK_2026.md' => PosRealWorldRuntimeCatalog::renderSyntaxLock2026Markdown($schema),
            '.agents/DUAL_ZONE_PUBLIC_UX_AND_INPUT_HYGIENE.md' => PosDualZoneFrontendAndSaasCatalog::renderDualZoneAndInputHygieneMarkdown($schema),
            '.agents/TROUBLESHOOTING_INSTINCTS.md' => PosRealWorldRuntimeCatalog::renderTroubleshootingInstinctsMarkdown($schema),
            '.agents/CLIENT_SOP_AND_TRAINING_MANUAL.md' => PosRealWorldRuntimeCatalog::renderClientSopManualMarkdown($schema, 'id'),
            '.agents/BLUEPRINT_SCHEMA.json' => (string) json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            '.agents/SYMBOL_MANIFEST.json' => (string) json_encode(
                $schema['symbol_manifest'] ?? TopologicalPromptChunker::generateSymbolManifest($schema),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ),
            '.agents/ENTERPRISE_MENU_MATRIX.md' => PosEnterpriseGuaranteeCatalog::renderEnterpriseMenuMarkdown(
                $schema['enterprise_menu_matrix'] ?? PosEnterpriseGuaranteeCatalog::enterpriseMenuMatrixForSector(
                    (string) ($schema['project_metadata']['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL')
                )
            ),
            '.agents/graphify/graph.json' => ProjectOsBlueprintCompilerService::renderInitialGraphifyJson($schema),
            'app/Actions/Tenancy/InitializeNewTenantAction.php' => ScaffoldGeneratorService::generateInitializeNewTenantAction($schema),
            'app/Actions/Audit/ExportFilteredAuditExcelAction.php' => (string) (@file_get_contents(app_path('Actions/Audit/ExportFilteredAuditExcelAction.php')) ?: ''),
            'app/Services/Security/DocumentThreatSanitizerService.php' => (string) (@file_get_contents(app_path('Services/Security/DocumentThreatSanitizerService.php')) ?: ''),
            'app/Domains/Rules/DeterministicDecisionMatrix.php' => ScaffoldGeneratorService::generateDeterministicDecisionMatrixClass($schema, $appName),
            'app/Filament/Pages/SmartGuide.php' => PosRealWorldRuntimeCatalog::renderClientSmartGuidePhp($schema),
            'database/sql/pg16_range_partitioning.sql' => PosRealWorldRuntimeCatalog::renderPg16RangePartitioningSql($schema['data_dictionary'] ?? [], $appName),
            'resources/js/peripherals/barcode-wedge-and-dual-screen.js' => PosRealWorldRuntimeCatalog::renderHardwareBridgeJs($schema),
            'tests/Feature/Security/TenantIsolationSecurityTest.php' => ScaffoldGeneratorService::generateTenantIsolationSecurityTest($schema, $appName),
            'resources/views/prints/thermal-and-official-a4.blade.php' => ScaffoldGeneratorService::generateThermalAndOfficialPrintView($schema),
            'config/project_os_day2_ops.php' => ScaffoldGeneratorService::generateDayTwoOpsConfig($schema),
            '.env.blueprint' => ScaffoldGeneratorService::generateEnvBlueprintFile($schema, $appName),
            '.mcp.json' => ScaffoldGeneratorService::generateMcpJsonFile($blueprint),
            'database/seeders/RealisticSectorSeeder.php' => ScaffoldGeneratorService::generateRealisticSectorSeeder($schema),
            'verify-blueprint.sh' => BlueprintSlopScannerService::generateVerifyBlueprintBashScript($appName),
        ];

        foreach ($phasedPrompts as $pItem) {
            $fname = $pItem['filename'] ?? ('0'.($pItem['phase'] ?? 1).'-phase.md');
            $filesToMaterialize['.agents/prompts/'.$fname] = (string) ($pItem['prompt'] ?? '');
        }

        $subChunkedPrompts = $schema['sub_chunked_prompts']
            ?? TopologicalPromptChunker::compileSubChunkedPrompts($schema);
        foreach ($subChunkedPrompts as $spItem) {
            $spName = $spItem['filename'] ?? ('part-'.($spItem['part_code'] ?? '1.0').'.md');
            $filesToMaterialize['.agents/prompts/subparts/'.$spName] = (string) ($spItem['prompt'] ?? '');
        }

        $safeAppBanner = preg_replace('/[^a-zA-Z0-9 _\-\.\(\)]/', '', $appName);
        $sectorCode = $schema['project_metadata']['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL';
        $stackCode = $schema['project_metadata']['stack_preset'] ?? 'STACK_M1_LARAVEL_FILAMENT_LIVEWIRE';
        $primaryUi = $schema['sectoral_ui_ux_archetype']['primary_archetype'] ?? 'POS_SPLIT_TERMINAL';

        $lines = [
            '#!/usr/bin/env bash',
            '# ==============================================================================',
            '# Neriah Pro Project OS v6.0 — One-Command CLI Bootstrapper',
            "# Target Project : {$safeAppBanner}",
            "# Sector / UI    : {$sectorCode} ({$primaryUi})",
            "# Stack Adapter  : {$stackCode}",
            '# ==============================================================================',
            'set -euo pipefail',
            '',
            'decode_b64() {',
            '  if base64 --decode </dev/null >/dev/null 2>&1; then',
            '    base64 --decode',
            '  else',
            '    base64 -D',
            '  fi',
            '}',
            '',
            'echo "=================================================================="',
            "echo \" [Project OS v9.0] Bootstrapping 100% Frozen + AgentShield Blueprint: {$safeAppBanner}\"",
            "echo \" [Sector UI] {$sectorCode} -> {$primaryUi}\"",
            "echo \" [Stack]     {$stackCode}\"",
            'echo "=================================================================="',
            'mkdir -p .claude .agents/graphify .agents/prompts/subparts app/Actions/Tenancy app/Actions/Audit app/Services/Security app/Domains/Rules app/Filament/Pages database/sql resources/js/peripherals tests/Feature/Security resources/views/prints config database/seeders',
            '',
        ];

        foreach ($filesToMaterialize as $relPath => $rawContent) {
            $b64 = base64_encode($rawContent);
            $lines[] = "echo \"  -> Materializing {$relPath}\"";
            $lines[] = "cat <<'EOF_B64' | tr -d '\\n\\r' | decode_b64 > \"{$relPath}\"";
            $lines[] = chunk_split($b64, 76, "\n").'EOF_B64';
            if ($relPath === 'verify-blueprint.sh') {
                $lines[] = 'chmod +x verify-blueprint.sh';
            }
            $lines[] = '';
        }

        $lines[] = 'echo "=================================================================="';
        $lines[] = 'echo " [SUCCESS] Project OS v3.0 Workspace Initialized!"';
        $lines[] = 'echo " Next Step 1: Buka AI Agent (Claude Code / Cursor / Antigravity)"';
        $lines[] = 'echo " Next Step 2: Jalankan secara berurutan .agents/prompts/01-foundation.md s/d 06-deployment.md"';
        $lines[] = 'echo " Next Step 3: Verifikasi anti-slop kapan saja dengan ./verify-blueprint.sh"';
        $lines[] = 'echo "=================================================================="';

        AuditLog::record(
            action: 'cli_bootstrap_pulled',
            model: $blueprint ?? $posProject,
            newValues: [
                'identifier' => $identifier,
                'app_name' => $appName,
                'sector' => $sectorCode,
                'stack_preset' => $stackCode,
                'files_count' => count($filesToMaterialize),
            ],
            description: "One-Command CLI Bootstrapper ditarik untuk '{$appName}' ({$identifier}).",
            eventCategory: 'cli_bootstrap'
        );

        return response(implode("\n", $lines)."\n", 200, [
            'Content-Type' => 'text/x-shellscript; charset=UTF-8',
            'X-Project-OS-Version' => '9.0.0',
        ]);
    }

    /**
     * Killer Feature #4: Reverse-Engineer / Brownfield SQL & Migration Importer API.
     */
    public function brownfieldAudit(Request $request, BrownfieldMigrationAuditorService $auditor): JsonResponse
    {
        $validated = $request->validate([
            'schema_text' => 'required|string|max:150000',
            'project_hint' => 'nullable|string|max:255',
        ]);

        $report = $auditor->auditSchema(
            $validated['schema_text'],
            $validated['project_hint'] ?? null,
            true
        );

        return response()->json($report);
    }

    /**
     * Killer Feature #5: Blueprint Drift Auditor & Slop Scanner API.
     */
    public function slopAudit(Request $request, string $slug, BlueprintSlopScannerService $scanner): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $report = $scanner->auditBlueprint($blueprint, true);

        return response()->json($report);
    }

    /**
     * Layer 8: Interactive Pre-Flight Blueprint Doctor API (before or during blueprint creation).
     */
    public function blueprintDoctor(Request $request, BlueprintDoctorService $doctor): JsonResponse
    {
        $validated = $request->validate([
            'sector_code' => 'nullable|string|max:120',
            'primitive_codes' => 'nullable|array',
            'primitive_codes.*' => 'string|max:120',
            'stack_preset_code' => 'nullable|string|max:120',
            'workflow_text' => 'nullable|string|max:20000',
            'timeline_days' => 'nullable|integer|min:1|max:730',
        ]);

        return response()->json([
            'success' => true,
            'doctor' => $doctor->diagnose($validated, true),
        ]);
    }

    /**
     * Layer 8: Blueprint Doctor Live Audit on a persisted VisionBlueprint.
     */
    public function blueprintSlugDoctor(Request $request, string $slug, BlueprintDoctorService $doctor): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        return response()->json([
            'success' => true,
            'doctor' => $doctor->diagnoseBlueprint($blueprint, true),
        ]);
    }

    /**
     * Lifecycle #1: Create a Surgical Delta Change Request (v1.0 -> v1.1) for Day-30+ Feature Evolution.
     */
    public function createDeltaChangeRequest(
        Request $request,
        string $slug,
        BlueprintDeltaEvolutionService $deltaService
    ): JsonResponse {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'change_summary' => 'nullable|string|max:10000',
            'added_primitives' => 'nullable|array',
            'added_primitives.*' => 'string|max:120',
            'added_tables' => 'nullable|array',
            'added_decision_rules' => 'nullable|array',
        ]);

        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
        $result = $deltaService->createChangeRequest($blueprint, $posProject, $validated, true);

        return response()->json($result, 201);
    }

    /**
     * Lifecycle #1: O(1) Keyset Cursor-Paginated Change Request History for a Blueprint.
     */
    public function listDeltaChangeRequests(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        if (! Schema::hasTable('pos_change_requests')) {
            return response()->json([
                'success' => true,
                'items' => [],
                'next_cursor' => null,
            ]);
        }

        $cursorPage = PosChangeRequest::query()
            ->where('vision_blueprint_id', $blueprint->id)
            ->orderBy('id', 'desc')
            ->cursorPaginate(15);

        return response()->json([
            'success' => true,
            'items' => $cursorPage->items(),
            'next_cursor' => $cursorPage->nextCursor()?->encode(),
            'prev_cursor' => $cursorPage->previousCursor()?->encode(),
        ]);
    }

    /**
     * Mitra Commercial #3: Client Approves Paid Change-Order Addendum & Generates Delta Invoice.
     */
    public function approveDeltaAddendum(
        Request $request,
        string $slug,
        string $crId,
        PosCommercialBridgeService $bridge
    ): JsonResponse {
        $blueprint = VisionBlueprint::where('slug', $slug)->orWhere('id', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $cr = PosChangeRequest::query()
            ->where('id', $crId)
            ->where('vision_blueprint_id', $blueprint->id)
            ->firstOrFail();

        $validated = $request->validate([
            'signer_name' => 'nullable|string|max:160',
            'signer_email' => 'nullable|email|max:160',
        ]);

        $result = $bridge->approveAddendumAndCreateInvoice($cr, $validated);

        return response()->json($result);
    }

}
