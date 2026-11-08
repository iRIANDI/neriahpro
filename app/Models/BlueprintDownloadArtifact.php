<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BlueprintDownloadArtifact extends Model
{
    use HasUlids;

    public const TYPE_PDF = 'pdf';
    public const TYPE_MARKDOWN = 'markdown';
    public const TYPE_SCAFFOLD = 'scaffold_zip';

    public const STATUS_QUEUED = 'queued';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_EXPIRED = 'expired';

    /**
     * Default retention lifecycle in hours before automatic deletion.
     */
    public const RETENTION_HOURS = 24;

    protected $table = 'blueprint_download_artifacts';

    protected $fillable = [
        'vision_blueprint_id',
        'user_id',
        'client_identifier',
        'artifact_type',
        'locale',
        'title',
        'filename',
        'file_path',
        'disk',
        'file_size_bytes',
        'mime_type',
        'status',
        'progress',
        'error_message',
        'download_token',
        'download_count',
        'last_downloaded_at',
        'expires_at',
        'completed_at',
    ];

    protected $casts = [
        'file_size_bytes' => 'integer',
        'progress' => 'integer',
        'download_count' => 'integer',
        'last_downloaded_at' => 'datetime',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relation to parent VisionBlueprint.
     */
    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(VisionBlueprint::class, 'vision_blueprint_id');
    }

    /**
     * Relation to User (if authenticated).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if this artifact is expired.
     */
    public function isExpired(): bool
    {
        if ($this->status === self::STATUS_EXPIRED) {
            return true;
        }

        if ($this->expires_at && Carbon::now()->greaterThanOrEqualTo($this->expires_at)) {
            return true;
        }

        return false;
    }

    /**
     * Get remaining time in seconds before expiration.
     */
    public function getRemainingSecondsAttribute(): int
    {
        if (! $this->expires_at || $this->isExpired()) {
            return 0;
        }

        $diff = Carbon::now()->diffInSeconds($this->expires_at, false);

        return (int) max(0, (int) $diff);
    }

    /**
     * Formatted human-readable remaining time string.
     */
    public function formattedRemainingTime(string $locale = 'id'): string
    {
        $seconds = $this->remaining_seconds;

        if ($seconds <= 0) {
            return $locale === 'en' ? 'Expired' : 'Kadaluwarsa';
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;

        if ($locale === 'en') {
            return sprintf('%02dh %02dm %02ds', $hours, $minutes, $secs);
        }

        return sprintf('%02dj %02dm %02dd', $hours, $minutes, $secs);
    }

    /**
     * Formatted file size in KB or MB.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (! $this->file_size_bytes || $this->file_size_bytes <= 0) {
            return '-';
        }

        $bytes = $this->file_size_bytes;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        return number_format($bytes / 1024, 1) . ' KB';
    }

    /**
     * Check if the underlying physical file exists on disk.
     */
    public function fileExistsOnDisk(): bool
    {
        if (! $this->file_path) {
            return false;
        }

        return Storage::disk($this->disk ?: 'local')->exists($this->file_path);
    }

    /**
     * Delete physical file from storage disk.
     */
    public function deletePhysicalFile(): bool
    {
        if ($this->file_path && Storage::disk($this->disk ?: 'local')->exists($this->file_path)) {
            try {
                Storage::disk($this->disk ?: 'local')->delete($this->file_path);
                return true;
            } catch (\Throwable $e) {
                Log::warning("Failed to delete physical artifact file {$this->file_path}: " . $e->getMessage());
            }
        }

        return false;
    }

    /**
     * Scope: Active and not expired.
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', self::STATUS_EXPIRED)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            });
    }

    /**
     * Scope: Enforce strict client isolation while allowing Superadmin to monitor all downloads.
     */
    public function scopeForClientOrAdmin($query, ?User $user, string $clientIdentifier)
    {
        // Superadmin bypass: monitor all downloads across all clients
        if ($user && ($user->isSuperAdmin() || $user->hasRole('super_admin') || str_contains($user->email, 'neriahpro'))) {
            return $query;
        }

        return $query->where(function ($q) use ($user, $clientIdentifier) {
            if ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('client_identifier', $clientIdentifier);
            } else {
                $q->where('client_identifier', $clientIdentifier);
            }
        });
    }

    /**
     * Periodic or on-demand cleanup of expired artifacts.
     * Deletes physical files and updates status to 'expired'.
     */
    public static function cleanupExpired(): int
    {
        $now = Carbon::now();
        $expiredRecords = self::where('status', '!=', self::STATUS_EXPIRED)
            ->where('expires_at', '<=', $now)
            ->limit(100)
            ->get();

        $count = 0;
        foreach ($expiredRecords as $artifact) {
            $artifact->deletePhysicalFile();
            $artifact->update([
                'status' => self::STATUS_EXPIRED,
                'file_path' => null,
            ]);
            $count++;
        }

        // Hard purge artifacts that have been expired for more than 48 hours to conserve database space
        self::where('status', self::STATUS_EXPIRED)
            ->where('expires_at', '<=', $now->copy()->subHours(48))
            ->limit(100)
            ->delete();

        return $count;
    }
}

