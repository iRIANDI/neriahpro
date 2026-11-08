<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\AuditLog;
use App\Models\VisionBlueprint;
use App\Services\ScaffoldGeneratorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GenerateBlueprintScaffoldJob implements ShouldQueue
{
    use Queueable;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 300;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 2;

    public function __construct(
        public string $blueprintId
    ) {}

    /**
     * Execute the background job.
     */
    public function handle(): void
    {
        $blueprint = VisionBlueprint::find($this->blueprintId);
        if (! $blueprint) {
            Log::warning("GenerateBlueprintScaffoldJob: VisionBlueprint with ID {$this->blueprintId} not found.");
            return;
        }

        $cacheKeyId = "scaffold_job_status:{$this->blueprintId}";
        $cacheKeySlug = "scaffold_job_status:{$blueprint->slug}";

        $updateStatus = function (string $status, int $progress, string $message, array $extra = []) use ($cacheKeyId, $cacheKeySlug) {
            $payload = array_merge([
                'status' => $status,
                'progress' => $progress,
                'message' => $message,
                'updated_at' => now()->toIso8601String(),
            ], $extra);

            Cache::put($cacheKeyId, $payload, now()->addDay());
            Cache::put($cacheKeySlug, $payload, now()->addDay());
        };

        try {
            $updateStatus('processing', 10, 'Menyiapkan mesin scaffold dan membaca spesifikasi arsitektur PRD...');

            // Step 1: Synthesize all 22+ architecture files
            $updateStatus('processing', 35, 'Mengompilasi 22+ berkas arsitektur, ERD PostgreSQL Strict ULID, dan Docker Compose...');
            $files = ScaffoldGeneratorService::generateFiles($blueprint);

            // Apply any customized files saved in user metadata
            $customizations = $blueprint->user_metadata['scaffold_customizations'] ?? [];
            if (! empty($customizations) && is_array($customizations)) {
                foreach ($customizations as $cPath => $cContent) {
                    if (is_string($cContent) && trim($cContent) !== '') {
                        $files[$cPath] = $cContent;
                    }
                }
            }

            // Step 2: Package files into permanent ZIP Archive
            $updateStatus('processing', 70, 'Mengemas berkas ke dalam arsip ZIP Starter Kit (.ZIP) berstandar Project OS...');
            $zipPath = ScaffoldGeneratorService::createZipArchive($blueprint);

            // Step 3: Update blueprint metadata and mark ready
            $updateStatus('processing', 90, 'Menyelaraskan metadata arsip cetak biru dan izin entitas...');
            $uMeta = $blueprint->user_metadata ?? [];
            $uMeta['scaffold_ready'] = true;
            $uMeta['scaffold_zip_path'] = $zipPath;
            $uMeta['scaffold_file_count'] = count($files);
            $uMeta['scaffold_generated_at'] = now()->toIso8601String();
            $blueprint->update(['user_metadata' => $uMeta]);

            // Step 4: Record audit log for compliance
            try {
                if (class_exists(AuditLog::class)) {
                    AuditLog::recordEvent(
                        category: 'blueprint',
                        action: 'scaffold_async_generated',
                        targetType: VisionBlueprint::class,
                        targetId: $blueprint->id,
                        meta: [
                            'slug' => $blueprint->slug,
                            'file_count' => count($files),
                            'zip_path' => basename($zipPath),
                            'execution_channel' => 'BACKGROUND_JOB',
                        ]
                    );
                }
            } catch (\Throwable $auditEx) {
                Log::warning('GenerateBlueprintScaffoldJob audit log failed: ' . $auditEx->getMessage());
            }

            $updateStatus('completed', 100, 'Arsip Scaffold Starter Kit berhasil dibuat dan siap diunduh!', [
                'file_count' => count($files),
                'zip_filename' => ($blueprint->slug ?: 'project') . '-starter-kit.zip',
                'download_url' => route('blueprint.export-scaffold', $blueprint->slug),
                'completed_at' => now()->format('Y-m-d H:i:s') . ' WIB',
            ]);

            Log::info("GenerateBlueprintScaffoldJob completed successfully for blueprint {$blueprint->slug}. Total files: " . count($files));
        } catch (\Throwable $e) {
            Log::error("GenerateBlueprintScaffoldJob failed for blueprint {$this->blueprintId}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $updateStatus('failed', 0, 'Gagal membuat arsip scaffold di latar belakang: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}

