<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\AuditLog;
use App\Models\BlueprintDownloadArtifact;
use App\Models\PosProject;
use App\Models\VisionBlueprint;
use App\Services\PrdGeneratorService;
use App\Services\ScaffoldGeneratorService;
use App\Support\PosInclusiveCommercialCatalog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateBlueprintArtifactJob implements ShouldQueue
{
    use Queueable;

    /**
     * Timeout for the generation job.
     */
    public int $timeout = 300;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 2;

    public function __construct(
        public string $artifactId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $artifact = BlueprintDownloadArtifact::find($this->artifactId);
        if (! $artifact) {
            Log::warning("GenerateBlueprintArtifactJob: Artifact {$this->artifactId} not found.");
            return;
        }

        if ($artifact->status === BlueprintDownloadArtifact::STATUS_COMPLETED && $artifact->fileExistsOnDisk()) {
            return;
        }

        $blueprint = VisionBlueprint::find($artifact->vision_blueprint_id);
        if (! $blueprint) {
            $artifact->update([
                'status' => BlueprintDownloadArtifact::STATUS_FAILED,
                'error_message' => 'Cetak biru (VisionBlueprint) tidak ditemukan.',
            ]);
            return;
        }

        try {
            $artifact->update([
                'status' => BlueprintDownloadArtifact::STATUS_PROCESSING,
                'progress' => 15,
            ]);

            $locale = in_array(strtolower((string) $artifact->locale), ['en', 'id'], true) ? strtolower((string) $artifact->locale) : 'id';
            $isEn = ($locale === 'en');

            $relDir = "exports/artifacts/{$artifact->id}";
            $filename = $artifact->filename ?: ($blueprint->slug . '.' . ($artifact->artifact_type === BlueprintDownloadArtifact::TYPE_PDF ? 'pdf' : 'md'));
            $relPath = "{$relDir}/{$filename}";
            $disk = $artifact->disk ?: 'local';

            $fileSize = 0;
            $mimeType = 'application/octet-stream';

            if ($artifact->artifact_type === BlueprintDownloadArtifact::TYPE_PDF) {
                // Step 1: Ensure PRD content exists (matching locale)
                $artifact->update(['progress' => 30]);
                $prd = $blueprint->prd_content;
                $prdLocale = $prd['output_locale'] ?? $prd['meta']['output_locale'] ?? 'id';
                if (empty($prd) || ! is_array($prd) || empty($prd['features']) || ($isEn && $prdLocale !== 'en')) {
                    try {
                        $prd = PrdGeneratorService::generate($blueprint, null, $locale);
                        if (! $isEn) {
                            $blueprint->prd_content = $prd;
                            $blueprint->saveQuietly();
                        }
                    } catch (\Throwable $pe) {
                        Log::warning('GenerateBlueprintArtifactJob: Auto-generate PRD failed: ' . $pe->getMessage());
                        $prd = $prd ?: [];
                    }
                }

                // Step 2: Render DomPDF with memory & network protection
                $artifact->update(['progress' => 60]);
                try {
                    $pdf = Pdf::loadView('pdf.blueprint', [
                        'blueprint' => $blueprint,
                        'prd' => $prd,
                        'locale' => $locale,
                        'isEn' => $isEn,
                    ])->setPaper('a4', 'portrait')
                        ->setOptions([
                            'isHtml5ParserEnabled' => true,
                            'isRemoteEnabled' => false,
                            'defaultFont' => 'Helvetica',
                        ]);

                    $artifact->update(['progress' => 85]);
                    $pdfContent = $pdf->output();
                    $fileSize = strlen($pdfContent);
                    $mimeType = 'application/pdf';

                    Storage::disk($disk)->put($relPath, $pdfContent);
                } catch (\Throwable $pdfError) {
                    Log::error("DomPDF rendering failed for artifact {$artifact->id}: " . $pdfError->getMessage(), [
                        'trace' => $pdfError->getTraceAsString(),
                    ]);
                    $artifact->update([
                        'status' => BlueprintDownloadArtifact::STATUS_FAILED,
                        'error_message' => 'Gagal merender dokumen PDF: ' . $pdfError->getMessage(),
                    ]);
                    throw $pdfError;
                }
            } elseif ($artifact->artifact_type === BlueprintDownloadArtifact::TYPE_MARKDOWN) {
                // Step 1: Generate PRD Markdown
                $artifact->update(['progress' => 35]);
                $prd = $blueprint->prd_content ?? [];
                $posProject = PosProject::where('vision_blueprint_id', $blueprint->id)->first();
                $wl = is_array($posProject?->white_label_branding_json) ? $posProject->white_label_branding_json : null;
                $actorSeed = (string) ($artifact->user_id ?: ($blueprint->email ?: $artifact->client_identifier));

                $md = PrdGeneratorService::toMarkdown($blueprint, $prd, $isEn);
                $artifact->update(['progress' => 65]);

                // Step 2: Add viral certified badge & canary watermark
                $md .= PosInclusiveCommercialCatalog::renderViralCertifiedBadgeMarkdown($wl);
                $md = PosInclusiveCommercialCatalog::injectCanaryWatermark($md, $actorSeed);

                $fileSize = strlen($md);
                $mimeType = 'text/markdown; charset=UTF-8';

                Storage::disk($disk)->put($relPath, $md);
            } elseif ($artifact->artifact_type === BlueprintDownloadArtifact::TYPE_SCAFFOLD) {
                // Step 1: Synthesize and package scaffold zip
                $artifact->update(['progress' => 40]);
                $customFolder = $blueprint->user_metadata['short_slug'] ?? null;
                $zipAbsPath = ScaffoldGeneratorService::createZipArchive($blueprint, $customFolder);

                if (file_exists($zipAbsPath)) {
                    $zipContent = file_get_contents($zipAbsPath);
                    $fileSize = strlen($zipContent);
                    $mimeType = 'application/zip';
                    Storage::disk($disk)->put($relPath, $zipContent);
                } else {
                    throw new \RuntimeException('Gagal menemukan berkas ZIP arsip yang dibuat oleh ScaffoldGeneratorService.');
                }
            } else {
                throw new \InvalidArgumentException("Jenis artefak tidak didukung: {$artifact->artifact_type}");
            }

            // Step 3: Finalize artifact record with 24-hour expiration lifecycle
            $now = Carbon::now();
            $expiresAt = $now->copy()->addHours(BlueprintDownloadArtifact::RETENTION_HOURS);

            $artifact->update([
                'status' => BlueprintDownloadArtifact::STATUS_COMPLETED,
                'progress' => 100,
                'file_path' => $relPath,
                'file_size_bytes' => $fileSize,
                'mime_type' => $mimeType,
                'completed_at' => $now,
                'expires_at' => $expiresAt,
                'error_message' => null,
            ]);

            // Audit log
            try {
                if (class_exists(AuditLog::class)) {
                    AuditLog::record(
                        action: 'blueprint_artifact_generated',
                        model: $artifact,
                        newValues: [
                            'artifact_id' => $artifact->id,
                            'artifact_type' => $artifact->artifact_type,
                            'blueprint_slug' => $blueprint->slug,
                            'file_size' => $fileSize,
                            'expires_at' => $expiresAt->toIso8601String(),
                        ],
                        description: "Background generation completed for {$artifact->title} [{$blueprint->slug}]",
                        eventCategory: 'document_export'
                    );
                }
            } catch (\Throwable $ae) {
                Log::warning('GenerateBlueprintArtifactJob: AuditLog record failed: ' . $ae->getMessage());
            }

            Log::info("GenerateBlueprintArtifactJob completed successfully for artifact {$artifact->id} ({$artifact->title}).");
        } catch (\Throwable $e) {
            Log::error("GenerateBlueprintArtifactJob failed for artifact {$artifact->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $artifact->update([
                'status' => BlueprintDownloadArtifact::STATUS_FAILED,
                'error_message' => 'Gagal memproses berkas di latar belakang: ' . $e->getMessage(),
            ]);

            throw $e;
        }
    }
}

