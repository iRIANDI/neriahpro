<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\PosProject;
use App\Models\VisionBlueprint;
use App\Services\BlueprintDeltaEvolutionService;
use App\Services\BlueprintDoctorService;
use App\Services\BlueprintSlopScannerService;
use App\Services\ProjectOsBlueprintCompilerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * Feature #2 (Day-30+ Lifecycle): Live MCP Server Bridge (`neriahpro-mcp`).
 *
 * Implements Model Context Protocol (MCP) Streamable HTTP JSON-RPC 2.0 so Claude Code,
 * Cursor, and Antigravity IDE can natively invoke:
 * - `neriahpro_get_blueprint`
 * - `neriahpro_get_next_phase`
 * - `neriahpro_report_completion` (auto-marks phase Verified ✅ on web dashboard & client portal!)
 * - `neriahpro_create_delta_cr`
 * - `neriahpro_verify_drift`
 */
class ProjectOsMcpController extends Controller
{
    public const MCP_PROTOCOL_VERSION = '2025-03-26';

    /**
     * Generate ready-to-paste `.mcp.json` configuration for a specific Blueprint.
     */
    public function mcpConfig(Request $request, string $slug, ProjectOsBlueprintCompilerService $compiler): JsonResponse
    {
        $blueprint = VisionBlueprint::query()
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        $posProject = $compiler->compileAndPersist($blueprint);
        $rotate = (bool) $request->boolean('rotate');
        $plainToken = $posProject->issueMcpToken($rotate);

        $endpointUrl = url('/api/mcp/project-os');
        $mcpConfig = [
            'mcpServers' => [
                'neriahpro-mcp' => [
                    'type' => 'http',
                    'url' => $endpointUrl,
                    'headers' => [
                        'Authorization' => 'Bearer ' . $plainToken,
                        'X-Project-Identifier' => $blueprint->slug,
                    ],
                ],
            ],
        ];

        return response()->json([
            'success' => true,
            'protocol_version' => self::MCP_PROTOCOL_VERSION,
            'project_slug' => $blueprint->slug,
            'project_id' => $posProject->id,
            'current_version' => $posProject->current_version ?: 'v1.0',
            'mcp_endpoint' => $endpointUrl,
            'mcp_token_prefix' => substr($plainToken, 0, 14) . '...',
            'mcp_config' => $mcpConfig,
            'mcp_json_pretty' => json_encode($mcpConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'available_tools' => array_column($this->toolDefinitions(), 'name'),
        ]);
    }

    /**
     * Handle MCP JSON-RPC 2.0 requests (`initialize`, `ping`, `tools/list`, `tools/call`).
     */
    public function handleRpc(
        Request $request,
        ProjectOsBlueprintCompilerService $compiler,
        BlueprintDeltaEvolutionService $deltaService,
        BlueprintSlopScannerService $slopScanner,
        BlueprintDoctorService $doctor
    ): JsonResponse {
        $rpcId = $request->input('id', 1);
        $method = (string) $request->input('method', 'tools/list');
        $params = (array) $request->input('params', []);

        return match ($method) {
            'initialize' => $this->rpcSuccess($rpcId, [
                'protocolVersion' => self::MCP_PROTOCOL_VERSION,
                'capabilities' => [
                    'tools' => ['listChanged' => false],
                ],
                'serverInfo' => [
                    'name' => 'neriahpro-mcp',
                    'version' => '4.0.0',
                ],
            ]),
            'notifications/initialized', 'ping' => $this->rpcSuccess($rpcId, ['ok' => true]),
            'tools/list' => $this->rpcSuccess($rpcId, [
                'tools' => $this->toolDefinitions(),
            ]),
            'tools/call' => $this->dispatchToolCall($request, $rpcId, $params, $compiler, $deltaService, $slopScanner, $doctor),
            default => $this->rpcError($rpcId, -32601, "MCP method '{$method}' is not supported."),
        };
    }

    /**
     * @param  array<string, mixed>  $params
     */
    protected function dispatchToolCall(
        Request $request,
        mixed $rpcId,
        array $params,
        ProjectOsBlueprintCompilerService $compiler,
        BlueprintDeltaEvolutionService $deltaService,
        BlueprintSlopScannerService $slopScanner,
        BlueprintDoctorService $doctor
    ): JsonResponse {
        $toolName = (string) ($params['name'] ?? '');
        $args = (array) ($params['arguments'] ?? []);

        $identifier = trim((string) (
            $args['project_identifier']
            ?? $args['slug']
            ?? $args['blueprint_id']
            ?? $request->header('X-Project-Identifier', '')
        ));

        if ($identifier === '') {
            return $this->rpcError($rpcId, -32602, 'Missing required argument: project_identifier (slug or ULID).');
        }

        [$blueprint, $posProject] = $this->resolveTargetProject($identifier, $compiler);
        if (! $blueprint && ! $posProject) {
            return $this->rpcError($rpcId, -32004, "Project Blueprint '{$identifier}' not found.");
        }

        $requiresAuth = in_array($toolName, [
            'neriahpro_report_completion',
            'neriahpro_create_delta_cr',
            'neriahpro_verify_drift',
        ], true) || $request->hasHeader('Authorization') || ! empty($posProject?->mcp_token_hash);

        if ($requiresAuth && ! $this->isAuthorizedForMutation($request, $posProject)) {
            AuditLog::record(
                action: 'mcp_unauthorized_mutation_blocked',
                model: $posProject ?? $blueprint,
                newValues: ['tool' => $toolName, 'identifier' => $identifier],
                description: "MCP Bridge menolak akses '{$toolName}' tanpa token MCP / sesi valid.",
                eventCategory: 'security'
            );

            return $this->rpcError($rpcId, -32001, 'Unauthorized MCP request: valid Bearer npro_mcp_* token or authenticated Architect session required.', 401);
        }

        $payload = match ($toolName) {
            'neriahpro_get_blueprint' => $this->toolGetBlueprint($blueprint, $posProject, $doctor),
            'neriahpro_get_next_phase' => $this->toolGetNextPhase($blueprint, $posProject),
            'neriahpro_report_completion' => $this->toolReportCompletion($blueprint, $posProject, $args),
            'neriahpro_create_delta_cr' => $deltaService->createChangeRequest($blueprint, $posProject, [
                'title' => $args['title'] ?? 'IDE MCP Change Request',
                'change_summary' => $args['change_summary'] ?? '',
                'added_primitives' => $args['added_primitives'] ?? [],
                'added_tables' => $args['added_tables'] ?? [],
            ], true),
            'neriahpro_verify_drift' => $this->toolVerifyDrift($blueprint, $posProject, $args, $slopScanner),
            default => null,
        };

        if ($payload === null) {
            return $this->rpcError($rpcId, -32601, "Unknown MCP tool '{$toolName}'.");
        }

        if ($posProject && $posProject->exists && Schema::hasColumn('pos_projects', 'last_mcp_synced_at')) {
            $posProject->forceFill(['last_mcp_synced_at' => now()])->saveQuietly();
        }

        AuditLog::record(
            action: 'mcp_tool_executed',
            model: $posProject ?? $blueprint,
            newValues: [
                'tool' => $toolName,
                'identifier' => $identifier,
            ],
            description: "MCP Server Bridge mengeksekusi tool '{$toolName}' untuk '{$identifier}'.",
            eventCategory: 'mcp_bridge'
        );

        return $this->rpcSuccess($rpcId, [
            'content' => [
                [
                    'type' => 'text',
                    'text' => (string) json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                ],
            ],
            'structuredContent' => $payload,
            'isError' => false,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function toolGetBlueprint(?VisionBlueprint $blueprint, ?PosProject $posProject, BlueprintDoctorService $doctor): array
    {
        $schema = $this->extractSchema($blueprint, $posProject);
        $docReport = $blueprint ? $doctor->diagnoseBlueprint($blueprint, false) : $doctor->diagnose([], false);

        return [
            'project_id' => $posProject?->id,
            'blueprint_id' => $blueprint?->id,
            'slug' => $blueprint?->slug,
            'app_name' => $posProject?->app_name ?? $blueprint?->nama_bisnis,
            'current_version' => $posProject?->current_version ?: 'v1.0',
            'determinism_score' => $docReport['determinism_score'] ?? 100.0,
            'zero_assumption_ready' => ($docReport['determinism_score'] ?? 100.0) >= 98.0,
            'primary_sector' => $schema['project_metadata']['primary_sector'] ?? null,
            'stack_preset' => $schema['project_metadata']['stack_preset'] ?? null,
            'tables_count' => count($schema['data_dictionary'] ?? []),
            'state_machines_count' => count($schema['state_machines'] ?? []),
            'decision_tables' => $schema['decision_tables_matrix'] ?? [],
            'phase_progress' => $posProject?->phase_progress_json ?? [],
            'universal_schema' => $schema,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function toolGetNextPhase(?VisionBlueprint $blueprint, ?PosProject $posProject): array
    {
        $schema = $this->extractSchema($blueprint, $posProject);
        $phases = $schema['phased_prompt_sequencer']
            ?? ProjectOsBlueprintCompilerService::compilePhasedPrompts($schema);
        $progress = is_array($posProject?->phase_progress_json) ? $posProject->phase_progress_json : [];

        foreach ($phases as $phaseItem) {
            $num = (int) ($phaseItem['phase'] ?? 1);
            $key = 'phase_' . $num;
            $status = strtoupper((string) ($progress[$key]['status'] ?? 'PENDING'));
            if ($status !== 'VERIFIED') {
                return [
                    'all_completed' => false,
                    'next_phase_number' => $num,
                    'total_phases' => count($phases),
                    'phase' => $phaseItem,
                ];
            }
        }

        return [
            'all_completed' => true,
            'next_phase_number' => null,
            'total_phases' => count($phases),
            'message' => 'Seluruh Fase 1 s/d ' . count($phases) . ' telah VERIFIED ✅! Proyek siap produksi atau siap mengajukan Delta Change Request (v1.1).',
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array<string, mixed>
     */
    protected function toolReportCompletion(?VisionBlueprint $blueprint, ?PosProject $posProject, array $args): array
    {
        $phaseNum = max(1, min(7, (int) ($args['phase_number'] ?? 1)));
        $notes = trim((string) ($args['summary'] ?? "Phase {$phaseNum} completed & verified via MCP IDE Bridge."));
        $ideClient = trim((string) ($args['ide_client'] ?? 'mcp-ide-agent'));

        $progress = is_array($posProject?->phase_progress_json) ? $posProject->phase_progress_json : [];
        $progress['phase_' . $phaseNum] = [
            'phase' => $phaseNum,
            'status' => 'VERIFIED',
            'badge' => 'Verified ✅',
            'summary' => $notes,
            'ide_client' => $ideClient,
            'verified_at' => now()->toIso8601String(),
        ];

        $verifiedCount = count(array_filter(
            $progress,
            fn ($p) => is_array($p) && ($p['status'] ?? '') === 'VERIFIED'
        ));
        $completionPercent = (int) min(100, round(($verifiedCount / 7) * 100));

        if ($posProject && $posProject->exists && Schema::hasColumn('pos_projects', 'phase_progress_json')) {
            $posProject->forceFill([
                'phase_progress_json' => $progress,
                'last_mcp_synced_at' => now(),
            ])->saveQuietly();
        }

        // Also mirror into VisionBlueprint dev_checkpoints so /customer/dashboard live roadmap turns Green!
        if ($blueprint && $blueprint->exists) {
            $prd = is_array($blueprint->prd_content) ? $blueprint->prd_content : [];
            $devCheckpoints = is_array($prd['dev_checkpoints'] ?? null) ? $prd['dev_checkpoints'] : [];
            $devCheckpoints['phase_' . $phaseNum] = true;
            $prd['dev_checkpoints'] = $devCheckpoints;
            $prd['mcp_completion_percent'] = $completionPercent;
            $blueprint->forceFill(['prd_content' => $prd])->saveQuietly();
        }

        return [
            'success' => true,
            'phase_number' => $phaseNum,
            'dashboard_status' => 'VERIFIED ✅',
            'verified_phases_count' => $verifiedCount,
            'total_phases' => 7,
            'completion_percent' => $completionPercent,
            'phase_progress' => $progress,
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array<string, mixed>
     */
    protected function toolVerifyDrift(
        ?VisionBlueprint $blueprint,
        ?PosProject $posProject,
        array $args,
        BlueprintSlopScannerService $scanner
    ): array {
        $terminalLog = trim((string) ($args['verify_script_output'] ?? ''));
        $codeSnippet = trim((string) ($args['code_snippet'] ?? ''));

        $audit = $blueprint
            ? $scanner->auditBlueprint($blueprint, false)
            : ['compliance_score' => 100, 'status' => 'PASS', 'violations' => []];

        if ($codeSnippet !== '') {
            $snippetAudit = $scanner->scanCodeSnippet($codeSnippet, false);
            $audit['snippet_scan'] = $snippetAudit;
            if (! empty($snippetAudit['violations'])) {
                $audit['status'] = 'DRIFT_DETECTED';
                $audit['compliance_score'] = min((int) $audit['compliance_score'], (int) $snippetAudit['compliance_score']);
            }
        }

        $cliClean = $terminalLog === '' || (stripos($terminalLog, 'FAIL') === false && stripos($terminalLog, 'VIOLATION') === false);
        $isPass = ($audit['status'] ?? 'PASS') === 'PASS' && $cliClean;

        $badges = [
            'database_ulid_strict_pgsql' => $isPass ? '100% Pass' : 'Drift Detected',
            'zero_external_cdn_and_popups' => $isPass ? '100% Pass' : 'Drift Detected',
            'anti_slop_ui_geometry' => $isPass ? '100% Pass' : 'Drift Detected',
            'four_pillar_ai_and_audit_sync' => $isPass ? '100% Pass' : 'Drift Detected',
            'verified_at' => now()->toIso8601String(),
            'compliance_score' => $audit['compliance_score'] ?? 100,
        ];

        if ($posProject && $posProject->exists && Schema::hasColumn('pos_projects', 'drift_verification_json')) {
            $posProject->forceFill(['drift_verification_json' => $badges])->saveQuietly();
        }

        return [
            'success' => true,
            'status' => $isPass ? 'PASS' : 'DRIFT_DETECTED',
            'badges' => $badges,
            'audit' => $audit,
        ];
    }

    /**
     * @return array{0: ?VisionBlueprint, 1: ?PosProject}
     */
    protected function resolveTargetProject(string $identifier, ProjectOsBlueprintCompilerService $compiler): array
    {
        $blueprint = VisionBlueprint::query()
            ->where('id', $identifier)
            ->orWhere('slug', $identifier)
            ->first();

        if ($blueprint) {
            $posProject = $compiler->compileAndPersist($blueprint);

            return [$blueprint, $posProject];
        }

        if (Schema::hasTable('pos_projects')) {
            $posProject = PosProject::query()->where('id', $identifier)->first();
            if ($posProject) {
                $bp = $posProject->vision_blueprint_id
                    ? VisionBlueprint::query()->find($posProject->vision_blueprint_id)
                    : null;

                return [$bp, $posProject];
            }
        }

        return [null, null];
    }

    protected function isAuthorizedForMutation(Request $request, ?PosProject $posProject): bool
    {
        $bearer = (string) ($request->bearerToken() ?: $request->header('X-MCP-Token', ''));
        if ($bearer !== '' && $posProject && $posProject->verifyMcpToken($bearer)) {
            return true;
        }

        $user = Auth::user();
        if ($user) {
            if ($user->hasAnyRole(['midtrans_reviewer'])) {
                return false;
            }

            return $user->hasAnyRole(['super_admin', 'developer', 'client_retail', 'client_partner'])
                || $user->can('connect_mcp_bridge')
                || ($posProject && $posProject->user_id && $posProject->user_id === $user->id);
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    protected function extractSchema(?VisionBlueprint $blueprint, ?PosProject $posProject): array
    {
        if ($posProject && ! empty($posProject->compiled_schema_json)) {
            $decoded = is_array($posProject->compiled_schema_json)
                ? $posProject->compiled_schema_json
                : json_decode((string) $posProject->compiled_schema_json, true);
            if (is_array($decoded) && ! empty($decoded)) {
                return $decoded;
            }
        }

        if ($blueprint) {
            return ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => true]);
        }

        return [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function toolDefinitions(): array
    {
        return [
            [
                'name' => 'neriahpro_get_blueprint',
                'description' => 'Fetch the compiled Zero-Assumption Universal Blueprint Schema, Decision Tables, Determinism Score, and current phase progress from NeriahPro Project OS.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_identifier' => ['type' => 'string', 'description' => 'Blueprint slug or ULID'],
                    ],
                    'required' => ['project_identifier'],
                ],
            ],
            [
                'name' => 'neriahpro_get_next_phase',
                'description' => 'Retrieve the next uncompleted execution prompt (Phase 1 to Phase 7 Red-Team Security) for sequential anti-context-rot execution.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_identifier' => ['type' => 'string', 'description' => 'Blueprint slug or ULID'],
                    ],
                    'required' => ['project_identifier'],
                ],
            ],
            [
                'name' => 'neriahpro_report_completion',
                'description' => 'Mark an executed phase (1..7) as VERIFIED ✅ on NeriahPro Studio and automatically sync live progress to the Client Dashboard.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_identifier' => ['type' => 'string', 'description' => 'Blueprint slug or ULID'],
                        'phase_number' => ['type' => 'integer', 'description' => 'Completed phase number (1 to 7)'],
                        'summary' => ['type' => 'string', 'description' => 'Summary of files created and tests passed'],
                        'ide_client' => ['type' => 'string', 'description' => 'IDE name (e.g. cursor, claude-code, antigravity)'],
                    ],
                    'required' => ['project_identifier', 'phase_number'],
                ],
            ],
            [
                'name' => 'neriahpro_create_delta_cr',
                'description' => 'Generate a Surgical Delta Blueprint (v1.0 -> v1.1) for a Day-30+ feature addition without corrupting existing production tables.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_identifier' => ['type' => 'string', 'description' => 'Blueprint slug or ULID'],
                        'title' => ['type' => 'string', 'description' => 'Change request title'],
                        'change_summary' => ['type' => 'string', 'description' => 'Detailed feature evolution description'],
                    ],
                    'required' => ['project_identifier', 'title'],
                ],
            ],
            [
                'name' => 'neriahpro_verify_drift',
                'description' => 'Submit verify-blueprint.sh output or code snippet to update the 4 Anti-Slop Compliance Badges in the Studio Execution Tracker.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'project_identifier' => ['type' => 'string', 'description' => 'Blueprint slug or ULID'],
                        'verify_script_output' => ['type' => 'string', 'description' => 'Terminal output from ./verify-blueprint.sh'],
                        'code_snippet' => ['type' => 'string', 'description' => 'Optional code snippet to scan for slop'],
                    ],
                    'required' => ['project_identifier'],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $result
     */
    protected function rpcSuccess(mixed $id, array $result): JsonResponse
    {
        return response()->json([
            'jsonrpc' => '2.0',
            'id' => $id,
            'result' => $result,
        ]);
    }

    protected function rpcError(mixed $id, int $code, string $message, int $httpStatus = 200): JsonResponse
    {
        return response()->json([
            'jsonrpc' => '2.0',
            'id' => $id,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ], $httpStatus);
    }
}
