<?php

declare(strict_types=1);

namespace App\Services\Legal;

use App\Models\LeadContact;
use App\Models\PosImpactGrantApplication;
use App\Models\VisionBlueprint;
use App\Services\Security\DocumentThreatSanitizerService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LegalDocumentVaultService
{
    public const DISK = 'private';

    public const MAX_SIZE_KB = 10240; // 10 MB

    public const ALLOWED_MIMES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    /**
     * Store legal document into an isolated, partitioned per-entity directory.
     * Guarantees files do not pile up in a single flat folder.
     *
     * Directory format:
     * legal_documents/{entityType}/{entityId}/{timestamp}_{safeFilename}.{ext}
     *
     * Example:
     * legal_documents/impact_grants/01J.../20261102_183000_akta_notaris.pdf
     *
     * @return array<string, mixed>
     */
    public static function storeLegalDocument(
        UploadedFile $file,
        string $entityType,
        string $entityId,
        array $extraMetadata = []
    ): array {
        $mime = $file->getMimeType() ?: $file->getClientMimeType();
        if (! in_array($mime, self::ALLOWED_MIMES, true)) {
            throw new \InvalidArgumentException('Format file tidak didukung. Harap unggah dokumen bertipe PDF, JPG, atau PNG.');
        }

        if ($file->getSize() > (self::MAX_SIZE_KB * 1024)) {
            throw new \InvalidArgumentException('Ukuran dokumen melebihi batas maksimal 10 MB.');
        }

        // SHA-256 Checksum for document authenticity and tamper-proofing
        $realPath = $file->getRealPath();
        $sha256 = hash_file('sha256', $realPath);

        // Security Layer: Strip EXIF metadata from images to eliminate trackers or indirect injection
        if (str_starts_with($mime, 'image/')) {
            try {
                DocumentThreatSanitizerService::verifyAndStripExifIfImage($realPath);
            } catch (\Throwable $e) {
                Log::warning("LegalDocumentVaultService EXIF strip notice: {$e->getMessage()}");
            }
        }

        $originalName = $file->getClientOriginalName();
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = strtolower($file->getClientOriginalExtension() ?: ($mime === 'application/pdf' ? 'pdf' : 'jpg'));
        $safeBase = Str::slug(substr($baseName, 0, 50)) ?: 'legal_document';
        $timestamp = date('Ymd_His');
        $fileName = "{$timestamp}_{$safeBase}.{$extension}";

        // Deterministic per-entity directory partitioning
        $safeEntityType = Str::slug($entityType, '_');
        $safeEntityId = Str::slug($entityId, '_');
        $directory = "legal_documents/{$safeEntityType}/{$safeEntityId}";

        $storagePath = Storage::disk(self::DISK)->putFileAs($directory, $file, $fileName);
        if (! $storagePath) {
            throw new \RuntimeException('Gagal menyimpan file dokumen ke brankas penyimpanan.');
        }

        return [
            'disk' => self::DISK,
            'storage_path' => $storagePath,
            'directory' => $directory,
            'file_name' => $fileName,
            'original_name' => $originalName,
            'mime_type' => $mime,
            'size_bytes' => $file->getSize(),
            'sha256' => $sha256,
            'uploaded_at' => now()->toIso8601String(),
            'extra' => $extraMetadata,
        ];
    }

    /**
     * Purge all documents belonging to a specific entity directory.
     * Prevents orphan/zombie files when an entity is deleted.
     */
    public static function purgeEntityDocuments(string $entityType, string $entityId): bool
    {
        $safeEntityType = Str::slug($entityType, '_');
        $safeEntityId = Str::slug($entityId, '_');
        $directory = "legal_documents/{$safeEntityType}/{$safeEntityId}";

        if (Storage::disk(self::DISK)->exists($directory)) {
            Log::info("LegalDocumentVaultService: Purged entity directory {$directory}");

            return Storage::disk(self::DISK)->deleteDirectory($directory);
        }

        return true;
    }

    /**
     * Anti-Zombie File Garbage Collector:
     * Scans legal_documents partition and removes orphan directories whose parent
     * entities no longer exist in the database.
     */
    public static function pruneZombieFiles(int $olderThanHours = 24): int
    {
        $purgedCount = 0;
        $disk = Storage::disk(self::DISK);

        $categories = ['impact_grants', 'projects', 'leads'];
        foreach ($categories as $cat) {
            $catDir = "legal_documents/{$cat}";
            if (! $disk->exists($catDir)) {
                continue;
            }

            $entityDirs = $disk->directories($catDir);
            foreach ($entityDirs as $dir) {
                $entityId = basename($dir);
                $isAlive = false;

                if ($cat === 'impact_grants') {
                    $isAlive = PosImpactGrantApplication::query()->where('id', $entityId)->exists()
                        || LeadContact::query()->where('id', $entityId)->exists();
                } elseif ($cat === 'projects') {
                    $isAlive = VisionBlueprint::query()->where('id', $entityId)->orWhere('slug', $entityId)->exists();
                } elseif ($cat === 'leads') {
                    $isAlive = LeadContact::query()->where('id', $entityId)->exists();
                }

                if (! $isAlive) {
                    $disk->deleteDirectory($dir);
                    $purgedCount++;
                    Log::info("LegalDocumentVaultService: Purged zombie orphan directory: {$dir}");
                }
            }
        }

        return $purgedCount;
    }
}
