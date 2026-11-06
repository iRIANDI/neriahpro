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

class BlueprintExportController extends Controller
{
    use AuthorizesBlueprintAccess;

    /**
     * Download PRD as PDF
     */
    public function downloadPdf(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $prd = $blueprint->prd_content;
        if (empty($prd) || ! is_array($prd) || empty($prd['features'])) {
            try {
                $prd = PrdGeneratorService::generate($blueprint);
                $blueprint->prd_content = $prd;
                $blueprint->saveQuietly();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal auto-generate prd_content untuk PDF: '.$e->getMessage());
                $prd = $prd ?: [];
            }
        }

        $pdf = Pdf::loadView('pdf.blueprint', [
            'blueprint' => $blueprint,
            'prd' => $prd,
        ])->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Helvetica',
            ]);

        return $pdf->download('PRD_'.$blueprint->slug.'.pdf');
    }

    /**
     * Download PRD as Markdown (with Invisible Zero-Width Canary Watermark & Viral/White-Label Seal v8.0)
     */
    public function downloadMd(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);
        $prd = $blueprint->prd_content ?? [];

        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
        $wl = is_array($posProject?->white_label_branding_json) ? $posProject->white_label_branding_json : null;
        $actorSeed = (string) (auth()->id() ?: ($blueprint->email ?: request()->ip()));
        $canarySig = PosInclusiveCommercialCatalog::computeCanaryFingerprint($actorSeed);

        $md = PrdGeneratorService::toMarkdown($blueprint, $prd);
        $md .= PosInclusiveCommercialCatalog::renderViralCertifiedBadgeMarkdown($wl);
        $md = PosInclusiveCommercialCatalog::injectCanaryWatermark($md, $actorSeed);

        if ($posProject && Schema::hasColumn('pos_projects', 'canary_watermark_sig')) {
            $posProject->forceFill(['canary_watermark_sig' => $canarySig])->saveQuietly();
        }

        if (class_exists(AuditLog::class)) {
            AuditLog::record(
                action: 'canary_watermark_embedded',
                model: $blueprint,
                newValues: [
                    'slug' => $blueprint->slug,
                    'canary_sig' => $canarySig,
                ],
                description: "Zero-Width Canary Watermark embedded into downloaded PRD Markdown [{$blueprint->slug}]",
                eventCategory: 'ip_canary_security'
            );
        }

        return response($md, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="PRD_'.$blueprint->slug.'.md"',
        ]);
    }

    /**
     * Get Raw PRD Markdown for AI Code Agent clipboard copy (with Invisible Zero-Width Canary Watermark v8.0)
     */
    public function rawMd(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);
        $prd = $blueprint->prd_content ?? [];

        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
        $wl = is_array($posProject?->white_label_branding_json) ? $posProject->white_label_branding_json : null;
        $actorSeed = (string) (auth()->id() ?: ($blueprint->email ?: request()->ip()));
        $canarySig = PosInclusiveCommercialCatalog::computeCanaryFingerprint($actorSeed);

        $md = PrdGeneratorService::toMarkdown($blueprint, $prd);
        $md .= PosInclusiveCommercialCatalog::renderViralCertifiedBadgeMarkdown($wl);
        $md = PosInclusiveCommercialCatalog::injectCanaryWatermark($md, $actorSeed);

        if ($posProject && Schema::hasColumn('pos_projects', 'canary_watermark_sig')) {
            $posProject->forceFill(['canary_watermark_sig' => $canarySig])->saveQuietly();
        }

        if (class_exists(AuditLog::class)) {
            AuditLog::record(
                action: 'canary_watermark_embedded',
                model: $blueprint,
                newValues: [
                    'slug' => $blueprint->slug,
                    'canary_sig' => $canarySig,
                ],
                description: "Zero-Width Canary Watermark embedded into raw PRD Markdown [{$blueprint->slug}]",
                eventCategory: 'ip_canary_security'
            );
        }

        return response($md, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    /**
     * Download compiled Universal Blueprint Schema v1.0 JSON (.agents/BLUEPRINT_SCHEMA.json).
     */
    public function downloadSchemaJson(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);
        $prd = $blueprint->prd_content ?? [];
        $schema = $prd['universal_blueprint_schema']
            ?? ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint);

        return response(
            json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            200,
            [
                'Content-Type' => 'application/json; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="BLUEPRINT_SCHEMA.json"',
            ]
        );
    }

    /**
     * Download AI IDE Rules (.cursorrules / CLAUDE.md / AGENTS.md / PROJECT_OS_EXECUTION_BLUEPRINT.md)
     */
    public function downloadCursorrules(Request $request, string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);
        $prd = $blueprint->prd_content ?? [];
        if (empty($prd)) {
            $prd = PrdGeneratorService::generate($blueprint);
        }

        $format = strtolower((string) $request->query('format', 'cursorrules'));
        $univSchema = $prd['universal_blueprint_schema']
            ?? ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint);

        $phasedPrompts = $univSchema['phased_prompt_sequencer']
            ?? ProjectOsBlueprintCompilerService::compilePhasedPrompts($univSchema);

        if (preg_match('/^phase-([1-7])$/', $format, $m)) {
            $phaseIdx = ((int) $m[1]) - 1;
            $phaseItem = $phasedPrompts[$phaseIdx] ?? null;
            if ($phaseItem) {
                return response((string) ($phaseItem['prompt'] ?? ''), 200, [
                    'Content-Type' => 'text/markdown; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="'.($phaseItem['filename'] ?? ("0{$m[1]}-phase.md")).'"',
                ]);
            }
        }

        if (preg_match('/^subpart-([0-9]+\.[0-9]+)$/', $format, $sm)) {
            $subParts = $univSchema['sub_chunked_prompts']
                ?? TopologicalPromptChunker::compileSubChunkedPrompts($univSchema);
            foreach ($subParts as $sp) {
                if (($sp['part_code'] ?? '') === $sm[1]) {
                    return response((string) ($sp['prompt'] ?? ''), 200, [
                        'Content-Type' => 'text/markdown; charset=UTF-8',
                        'Content-Disposition' => 'attachment; filename="'.($sp['filename'] ?? ("part-{$sm[1]}.md")).'"',
                    ]);
                }
            }
        }

        if ($format === 'symbol-manifest') {
            $manifest = $univSchema['symbol_manifest']
                ?? TopologicalPromptChunker::generateSymbolManifest($univSchema);

            return response(
                (string) json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                200,
                [
                    'Content-Type' => 'application/json; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="SYMBOL_MANIFEST.json"',
                ]
            );
        }

        if ($format === 'claude-settings') {
            return response(
                DocumentThreatSanitizerService::renderClaudeSettingsJson($univSchema),
                200,
                [
                    'Content-Type' => 'application/json; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="settings.json"',
                ]
            );
        }

        if ($format === 'dependency-manifest') {
            $depManifest = $univSchema['approved_dependency_manifest'] ?? DocumentThreatSanitizerService::approvedDependencyManifest(
                (string) ($univSchema['project_metadata']['stack_preset'] ?? 'STACK_M1_LARAVEL_MONOLITH'),
                (string) ($univSchema['project_metadata']['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL')
            );

            return response(
                (string) json_encode($depManifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                200,
                [
                    'Content-Type' => 'application/json; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="APPROVED_DEPENDENCY_MANIFEST.json"',
                ]
            );
        }

        if ($format === 'audit-exporter') {
            $exporterCode = @file_get_contents(app_path('Actions/Audit/ExportFilteredAuditExcelAction.php')) ?: '';

            return response($exporterCode, 200, [
                'Content-Type' => 'text/plain; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="ExportFilteredAuditExcelAction.php"',
            ]);
        }

        [$content, $filename] = match ($format) {
            'enterprise-menu' => [
                PosEnterpriseGuaranteeCatalog::renderEnterpriseMenuMarkdown(
                    $univSchema['enterprise_menu_matrix'] ?? PosEnterpriseGuaranteeCatalog::enterpriseMenuMatrixForSector(
                        (string) ($univSchema['project_metadata']['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL')
                    )
                ),
                'ENTERPRISE_MENU_MATRIX.md',
            ],
            'execution-blueprint' => [
                ProjectOsBlueprintCompilerService::renderExecutionBlueprintMarkdown($univSchema),
                'PROJECT_OS_EXECUTION_BLUEPRINT.md',
            ],
            'stack-topology' => [
                ProjectOsBlueprintCompilerService::renderStackTopologyContractMarkdown($univSchema),
                'STACK_TOPOLOGY_CONTRACT.md',
            ],
            'ui-ux-contract' => [
                ProjectOsBlueprintCompilerService::renderUiUxContractMarkdown(),
                'UI_UX_CONTRACT.md',
            ],
            'engineering-rules' => [
                ProjectOsBlueprintCompilerService::renderEngineeringRulesMarkdown(),
                'ENGINEERING_RULES.md',
            ],
            'ecc-harness' => [
                ProjectOsBlueprintCompilerService::renderAgentHarnessEccMarkdown(),
                'AGENT_HARNESS_ECC.md',
            ],
            'verify-blueprint' => [
                BlueprintSlopScannerService::generateVerifyBlueprintBashScript($blueprint->nama_bisnis ?: 'Enterprise App'),
                'verify-blueprint.sh',
            ],
            'print-spec' => [
                ProjectOsBlueprintCompilerService::renderPrintSpecMarkdown($univSchema),
                'PRINT_THERMAL_AND_A4_CONTRACT.md',
            ],
            'day2-ops' => [
                ProjectOsBlueprintCompilerService::renderDayTwoOpsMarkdown($univSchema),
                'DAY2_OPERATIONS_AND_SECURITY.md',
            ],
            'decision-tables' => [
                ProjectOsBlueprintCompilerService::renderDecisionTablesMarkdown($univSchema),
                'DECISION_TABLES_CONTRACT.md',
            ],
            'syntax-lock' => [
                PosRealWorldRuntimeCatalog::renderSyntaxLock2026Markdown($univSchema),
                'SYNTAX_LOCK_2026.md',
            ],
            'dual-zone-ux' => [
                PosDualZoneFrontendAndSaasCatalog::renderDualZoneAndInputHygieneMarkdown($univSchema, app()->getLocale()),
                'DUAL_ZONE_PUBLIC_UX_AND_INPUT_HYGIENE.md',
            ],
            'troubleshooting' => [
                PosRealWorldRuntimeCatalog::renderTroubleshootingInstinctsMarkdown($univSchema),
                'TROUBLESHOOTING_INSTINCTS.md',
            ],
            'client-sop' => [
                PosRealWorldRuntimeCatalog::renderClientSopManualMarkdown($univSchema, app()->getLocale()),
                'CLIENT_SOP_AND_TRAINING_MANUAL.md',
            ],
            'pg16-partition' => [
                PosRealWorldRuntimeCatalog::renderPg16RangePartitioningSql($univSchema['data_dictionary'] ?? [], $blueprint->nama_bisnis ?: 'Enterprise App'),
                'pg16_range_partitioning.sql',
            ],
            'env-blueprint' => [
                ScaffoldGeneratorService::generateEnvBlueprintFile($univSchema, $blueprint->nama_bisnis ?: 'Enterprise App'),
                '.env.blueprint',
            ],
            'mcp-json' => [
                ScaffoldGeneratorService::generateMcpJsonFile($blueprint),
                '.mcp.json',
            ],
            'claude' => [
                PrdGeneratorService::toCursorrules($blueprint, $prd),
                'CLAUDE.md',
            ],
            'agents' => [
                ProjectOsBlueprintCompilerService::renderUnifiedAgentsMarkdown($univSchema),
                'AGENTS.md',
            ],
            default => [
                PrdGeneratorService::toCursorrules($blueprint, $prd),
                '.cursorrules',
            ],
        };

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

}
