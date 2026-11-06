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

class BlueprintScaffoldController extends Controller
{
    use AuthorizesBlueprintAccess;

    /**
     * Download Project Scaffold & Boilerplate ZIP Archive.
     */
    public function exportScaffold(string $slug)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        try {
            $zipPath = ScaffoldGeneratorService::createZipArchive($blueprint);
            $downloadFilename = ($blueprint->slug ?: 'project').'-starter-kit.zip';

            return response()->download($zipPath, $downloadFilename)->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Export scaffold failed: ' . $e->getMessage(), [
                'slug' => $slug,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Gagal membuat arsip scaffold: ' . $e->getMessage());
        }
    }

    /**
     * Preview Project Scaffold Files in JSON format for in-browser viewer.
     */
    public function previewScaffold(string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        try {
            $files = ScaffoldGeneratorService::generateFiles($blueprint);
            $customizations = $blueprint->user_metadata['scaffold_customizations'] ?? [];

            // Auto-clean any corrupted customization placeholders in database
            $cleanedCustom = [];
            $needsMetaClean = false;
            foreach ($customizations as $cKey => $cVal) {
                $trimmed = is_string($cVal) ? trim($cVal) : '';
                if ($trimmed === '' || $trimmed === 'Memuat berkas...' || str_starts_with($trimmed, '// Berkas ') || str_starts_with($trimmed, '// Memuat berkas')) {
                    $needsMetaClean = true;
                    continue;
                }
                $cleanedCustom[$cKey] = $cVal;
            }

            if ($needsMetaClean) {
                $uMeta = $blueprint->user_metadata ?? [];
                $uMeta['scaffold_customizations'] = $cleanedCustom;
                $blueprint->update(['user_metadata' => $uMeta]);
                $customizations = $cleanedCustom;
            }

            // Guaranteed presence of all 22 required preview tabs
            $requiredTabs = [
                'docs/product/PRODUCT-BRIEF.md' => fn() => ScaffoldGeneratorService::generateProductBrief($blueprint),
                'docs/architecture/ADR-001-postgresql-ulid.md' => fn() => ScaffoldGeneratorService::generateAdr001($blueprint),
                'docs/adr/ADR-001-architectural-paradigm.md' => fn() => ScaffoldGeneratorService::generateAdr001($blueprint),
                'docker-compose.yml' => fn() => ScaffoldGeneratorService::generateDockerCompose($blueprint, $blueprint->slug ?: 'app'),
                'openapi.json' => fn() => ScaffoldGeneratorService::generateOpenApiSpec($blueprint, []),
                'schema_complete.sql' => fn() => ScaffoldGeneratorService::generateDatabaseMigrationsSql($blueprint, []),
                'database/migrations/schema_complete.sql' => fn() => ScaffoldGeneratorService::generateDatabaseMigrationsSql($blueprint, []),
                'design/wireframes/screens.md' => fn() => ScaffoldGeneratorService::generateWireframeBlueprints($blueprint, []),
                'design/tokens.json' => fn() => ScaffoldGeneratorService::generateDesignTokens($blueprint),
                'routes/web.php' => fn() => ScaffoldGeneratorService::generateLaravelRoutes($blueprint, []),
                'routes/api.php' => fn() => ScaffoldGeneratorService::generateLaravelApiRoutes($blueprint, []),
                'app/Http/Controllers/HealthProbeController.php' => fn() => ScaffoldGeneratorService::generateHealthProbeController($blueprint->slug ?: 'app'),
                'app/api/route.ts' => fn() => ScaffoldGeneratorService::generateNextJsRoute($blueprint, []),
                'database/seeders/SyntheticDataSeeder.php' => fn() => ScaffoldGeneratorService::generateSyntheticDataSeeder($blueprint, []),
                '.cursorrules' => fn() => PrdGeneratorService::toCursorrules($blueprint, $blueprint->prd_content ?? []),
                'AGENTS.md' => fn() => PrdGeneratorService::toCursorrules($blueprint, $blueprint->prd_content ?? []),
                'tests/Feature/ApiContractTest.php' => fn() => ScaffoldGeneratorService::generateContractTest($blueprint, []),
                'tests/e2e/smoke.spec.ts' => fn() => ScaffoldGeneratorService::generatePlaywrightE2ETest($blueprint, []),
                'tests/e2e/critical-flows.spec.ts' => fn() => ScaffoldGeneratorService::generatePlaywrightE2ETest($blueprint, []),
                'deploy.sh' => fn() => ScaffoldGeneratorService::generateDeployScript($blueprint, $blueprint->slug ?: 'app'),
                '.github/workflows/deploy.yml' => fn() => ScaffoldGeneratorService::generateCiCdWorkflow($blueprint, $blueprint->slug ?: 'app'),
                'scripts/backup.sh' => fn() => ScaffoldGeneratorService::generateBackupScript($blueprint, $blueprint->slug ?: 'app'),
                'scripts/restore.sh' => fn() => ScaffoldGeneratorService::generateRestoreScript($blueprint, $blueprint->slug ?: 'app'),
                'docs/runbooks/disaster-recovery.md' => fn() => ScaffoldGeneratorService::generateDisasterRecoveryRunbook($blueprint, $blueprint->slug ?: 'app'),
                'docs/disaster-recovery-runbook.md' => fn() => ScaffoldGeneratorService::generateDisasterRecoveryRunbook($blueprint, $blueprint->slug ?: 'app'),
                'README.md' => fn() => ScaffoldGeneratorService::generateReadme($blueprint, $blueprint->nama_bisnis ?: 'Proyek', $blueprint->slug ?: 'app'),
            ];

            foreach ($requiredTabs as $reqKey => $generator) {
                if (empty($files[$reqKey]) || $files[$reqKey] === 'Memuat berkas...' || str_starts_with(trim($files[$reqKey]), '// Berkas ')) {
                    $files[$reqKey] = $generator();
                }
            }

            return response()->json([
                'success' => true,
                'slug' => $blueprint->slug,
                'project_name' => $blueprint->nama_bisnis ?: 'Proyek',
                'files' => $files,
                'customized_files' => array_keys($customizations),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Preview scaffold failed: ' . $e->getMessage(), [
                'slug' => $slug,
                'trace' => $e->getTraceAsString(),
            ]);

            // Resilient fallback so the in-browser scaffold modal NEVER fails with HTTP 500
            $fallbackFiles = ScaffoldGeneratorService::generateFallbackFiles($blueprint);

            return response()->json([
                'success' => true,
                'slug' => $blueprint->slug,
                'project_name' => $blueprint->nama_bisnis ?: 'Proyek',
                'files' => $fallbackFiles,
                'customized_files' => [],
                'warning' => 'Scaffold dimuat dalam mode tangguh (safe fallback).',
            ]);
        }
    }

    /**
     * Auto-save or update an elaborated scaffold file for this blueprint.
     */
    public function saveScaffoldFile(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $request->validate([
            'filename' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $filename = $request->input('filename');
        $content = $request->input('content');

        // Prevent path traversal
        if (str_contains($filename, '..') || str_starts_with($filename, '/')) {
            return response()->json([
                'success' => false,
                'message' => 'Nama file tidak valid.',
            ], 422);
        }

        // Prevent saving temporary loading state or placeholder comments into database
        $trimmedContent = trim($content);
        if ($trimmedContent === '' || $trimmedContent === 'Memuat berkas...' || str_starts_with($trimmedContent, '// Berkas ') || str_starts_with($trimmedContent, '// Memuat berkas')) {
            return response()->json([
                'success' => false,
                'message' => 'Konten tidak valid (placeholder tidak dapat disimpan).',
            ], 422);
        }

        $userMeta = $blueprint->user_metadata ?? [];
        $scaffoldCustom = $userMeta['scaffold_customizations'] ?? [];
        $scaffoldCustom[$filename] = $content;
        $userMeta['scaffold_customizations'] = $scaffoldCustom;

        $blueprint->update([
            'user_metadata' => $userMeta,
        ]);

        return response()->json([
            'success' => true,
            'filename' => $filename,
            'saved_at' => now()->format('H:i:s').' WIB',
            'message' => "Berkas {$filename} berhasil dielaborasi dan disimpan otomatis.",
        ]);
    }

    /**
     * Reset an elaborated scaffold file back to default AI synthesis.
     */
    public function resetScaffoldFile(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $request->validate([
            'filename' => 'required|string|max:255',
        ]);

        $filename = $request->input('filename');

        $userMeta = $blueprint->user_metadata ?? [];
        if (isset($userMeta['scaffold_customizations'][$filename])) {
            unset($userMeta['scaffold_customizations'][$filename]);
            $blueprint->update([
                'user_metadata' => $userMeta,
            ]);
        }

        // Generate clean default
        $allFiles = ScaffoldGeneratorService::generateFiles($blueprint);
        $defaultContent = $allFiles[$filename] ?? '';

        return response()->json([
            'success' => true,
            'filename' => $filename,
            'content' => $defaultContent,
            'saved_at' => now()->format('H:i:s').' WIB',
            'message' => "Berkas {$filename} berhasil dikembalikan ke sintesis AI default.",
        ]);
    }

    /**
     * Refine an active scaffold file using Grounded Multi-Model AI based on Source Corpus.
     */
    public function refineScaffoldFile(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        // Security check for scope locking unless Retail License or SuperAdmin
        if ($blueprint->scope_locked && ! ($blueprint->project_status === 'Retail License' || (auth()->user()?->isSuperAdmin() ?? false))) {
            return response()->json([
                'success' => false,
                'message' => 'Spesifikasi telah dikunci (Scope Frozen). Pengubahan memerlukan Change Request resmi.',
            ], 403);
        }

        $validated = $request->validate([
            'filename' => 'required|string|max:255',
            'prompt' => 'nullable|string|max:3000',
            'ai_provider' => 'nullable|string|in:auto,relayrouter,gemini,deepseek,anthropic,openai',
        ]);

        try {
            $refinementService = app(BlueprintRefinementService::class);
            $result = $refinementService->refineScaffoldFile(
                $blueprint,
                $validated['filename'],
                $validated['prompt'] ?? null,
                $validated['ai_provider'] ?? 'auto'
            );

            return response()->json($result);
        } catch (\Throwable $e) {
            Log::error("Failed to refine scaffold file {$validated['filename']} for blueprint {$slug}: ".$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Terjadi kesalahan saat memproses pendalaman file dengan AI.',
            ], 422);
        }
    }

}
