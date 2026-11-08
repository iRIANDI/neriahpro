<?php

declare(strict_types=1);

namespace App\Http\Controllers\Blueprint;

use App\Http\Controllers\Blueprint\Concerns\AuthorizesBlueprintAccess;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateBlueprintArtifactJob;
use App\Models\BlueprintDownloadArtifact;
use App\Models\VisionBlueprint;
use App\Services\Entitlement\BlueprintEntitlementGateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BlueprintArtifactController extends Controller
{
    use AuthorizesBlueprintAccess;

    /**
     * Resolve a deterministic, privacy-safe unique client identifier.
     */
    protected function resolveClientIdentifier(Request $request, VisionBlueprint $blueprint): string
    {
        $user = Auth::user();
        if ($user) {
            return (string) $user->id;
        }

        $cookieId = $request->cookie('neriah_client_uuid');
        if ($cookieId && is_string($cookieId) && strlen($cookieId) >= 16) {
            return $cookieId;
        }

        if (! empty($blueprint->email)) {
            return hash('sha256', strtolower(trim((string) $blueprint->email)));
        }

        return hash('sha256', ($request->ip() ?: '127.0.0.1') . '|' . ($request->header('User-Agent') ?: 'agent'));
    }

    /**
     * Check if user is Superadmin (can monitor all client downloads).
     */
    protected function isSuperAdmin(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return (bool) ($user->isSuperAdmin() || $user->hasRole('super_admin') || str_contains((string) $user->email, 'neriahpro'));
    }

    /**
     * Request background generation of an artifact (PDF, Markdown, Scaffold ZIP).
     */
    public function requestArtifact(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $type = $request->input('artifact_type', BlueprintDownloadArtifact::TYPE_PDF);
        if (! in_array($type, [
            BlueprintDownloadArtifact::TYPE_PDF,
            BlueprintDownloadArtifact::TYPE_MARKDOWN,
            BlueprintDownloadArtifact::TYPE_SCAFFOLD,
        ], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe artefak tidak valid.',
            ], 422);
        }

        // Entitlement gate check for scaffold ZIP
        if ($type === BlueprintDownloadArtifact::TYPE_SCAFFOLD) {
            $gateService = app(BlueprintEntitlementGateService::class);
            $tier = $gateService->resolveUserTier(Auth::user(), $blueprint->slug);
            if (! $gateService->isArtifactEntitled($tier, 'scaffold_zip')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Ditolak: Pembuatan arsip ZIP Starter Kit Scaffold eksklusif untuk lisensi Retail Pro / Ultimate atau Proyek Mitra Turnkey.',
                ], 403);
            }
        }

        $activeLocale = $request->input('locale')
            ?: ($request->cookie('neriah_locale')
            ?: (session('locale') ?: ($blueprint->user_metadata['output_locale'] ?? app()->getLocale())));
        $locale = in_array(strtolower((string) $activeLocale), ['en', 'id'], true) ? strtolower((string) $activeLocale) : 'id';
        $isEn = ($locale === 'en');

        $clientIdentifier = $this->resolveClientIdentifier($request, $blueprint);
        $user = Auth::user();

        // Check if an active, completed artifact was recently created (within last 30 minutes) and still exists on disk
        if (! $request->boolean('force') && ! $request->boolean('instant') && ! $request->has('sync')) {
            $existing = BlueprintDownloadArtifact::where('vision_blueprint_id', $blueprint->id)
                ->where('artifact_type', $type)
                ->where('locale', $locale)
                ->where(function ($q) use ($user, $clientIdentifier) {
                    if ($user) {
                        $q->where('user_id', $user->id)->orWhere('client_identifier', $clientIdentifier);
                    } else {
                        $q->where('client_identifier', $clientIdentifier);
                    }
                })
                ->where('status', BlueprintDownloadArtifact::STATUS_COMPLETED)
                ->where('created_at', '>=', Carbon::now()->subMinutes(30))
                ->latest()
                ->first();

            if ($existing && $existing->fileExistsOnDisk() && ! $existing->isExpired()) {
                return response()->json([
                    'success' => true,
                    'is_cached' => true,
                    'artifact' => $this->formatArtifactPayload($existing, $locale),
                    'message' => $isEn ? 'Artifact is ready from cache.' : 'Berkas unduhan siap dari cache.',
                ]);
            }
        }

        // Determine title and filename
        $prefix = $isEn ? 'PRD_EN_' : 'PRD_';
        [$title, $filename] = match ($type) {
            BlueprintDownloadArtifact::TYPE_PDF => [
                $isEn ? 'PRD Enterprise Specification (.PDF)' : 'Dokumen Spesifikasi PRD Enterprise (.PDF)',
                $prefix . $blueprint->slug . '.pdf',
            ],
            BlueprintDownloadArtifact::TYPE_MARKDOWN => [
                $isEn ? 'PRD Raw Markdown + Canary Watermark (.MD)' : 'PRD Markdown Asli + Segel Canary (.MD)',
                $prefix . $blueprint->slug . '.md',
            ],
            BlueprintDownloadArtifact::TYPE_SCAFFOLD => [
                $isEn ? 'Scaffold Starter Kit Boilerplate (.ZIP)' : 'Paket Starter Kit Boilerplate Scaffold (.ZIP)',
                ($blueprint->slug ?: 'project') . '-starter-kit.zip',
            ],
        };

        // Create database artifact record with 24-hour expiration
        $artifact = BlueprintDownloadArtifact::create([
            'vision_blueprint_id' => $blueprint->id,
            'user_id' => $user?->id,
            'client_identifier' => $clientIdentifier,
            'artifact_type' => $type,
            'locale' => $locale,
            'title' => $title,
            'filename' => $filename,
            'status' => BlueprintDownloadArtifact::STATUS_QUEUED,
            'progress' => 5,
            'download_token' => Str::random(40),
            'expires_at' => Carbon::now()->addHours(BlueprintDownloadArtifact::RETENTION_HOURS),
        ]);

        // If instant mode requested, run synchronously
        if ($request->boolean('instant') || $request->has('sync')) {
            try {
                GenerateBlueprintArtifactJob::dispatchSync($artifact->id);
                $artifact->refresh();
                return response()->json([
                    'success' => true,
                    'artifact' => $this->formatArtifactPayload($artifact, $locale),
                    'message' => $isEn ? 'Artifact generated instantly.' : 'Berkas berhasil dibuat secara instan.',
                ]);
            } catch (\Throwable $e) {
                Log::error('Instant artifact generation failed: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat berkas secara instan: ' . $e->getMessage(),
                ], 500);
            }
        }

        // Dispatch background job (with dispatchAfterResponse fallback)
        try {
            GenerateBlueprintArtifactJob::dispatchAfterResponse($artifact->id);
        } catch (\Throwable $e) {
            GenerateBlueprintArtifactJob::dispatch($artifact->id);
        }

        return response()->json([
            'success' => true,
            'artifact' => $this->formatArtifactPayload($artifact, $locale),
            'message' => $isEn ? 'Background generation queued.' : 'Generasi berkas di latar belakang telah dijadwalkan.',
        ]);
    }

    /**
     * List all download artifacts for this blueprint (scoped to client, or all for Superadmin).
     */
    public function listArtifacts(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        // Run routine cleanup of expired artifacts
        BlueprintDownloadArtifact::cleanupExpired();

        $clientIdentifier = $this->resolveClientIdentifier($request, $blueprint);
        $user = Auth::user();
        $isSuperAdmin = $this->isSuperAdmin();

        $activeLocale = $request->input('locale')
            ?: ($request->cookie('neriah_locale')
            ?: (session('locale') ?: ($blueprint->user_metadata['output_locale'] ?? app()->getLocale())));
        $locale = in_array(strtolower((string) $activeLocale), ['en', 'id'], true) ? strtolower((string) $activeLocale) : 'id';

        $query = BlueprintDownloadArtifact::where('vision_blueprint_id', $blueprint->id);

        if (! $isSuperAdmin) {
            $query->forClientOrAdmin($user, $clientIdentifier);
        }

        $artifacts = $query->orderBy('created_at', 'desc')->limit(40)->get();

        $formatted = $artifacts->map(fn (BlueprintDownloadArtifact $art) => $this->formatArtifactPayload($art, $locale, $isSuperAdmin));

        return response()->json([
            'success' => true,
            'is_superadmin' => $isSuperAdmin,
            'client_identifier_masked' => substr($clientIdentifier, 0, 8) . '...' . substr($clientIdentifier, -4),
            'retention_hours' => BlueprintDownloadArtifact::RETENTION_HOURS,
            'total' => $formatted->count(),
            'artifacts' => $formatted,
        ]);
    }

    /**
     * Check status and progress of an artifact background job with auto-rescue fallback.
     */
    public function getArtifactStatus(Request $request, string $slug, string $id): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $artifact = BlueprintDownloadArtifact::where('id', $id)
            ->where('vision_blueprint_id', $blueprint->id)
            ->firstOrFail();

        $clientIdentifier = $this->resolveClientIdentifier($request, $blueprint);
        $user = Auth::user();
        $isSuperAdmin = $this->isSuperAdmin();

        // Check ownership if not superadmin
        if (! $isSuperAdmin) {
            $isOwner = ($user && $artifact->user_id === $user->id) || ($artifact->client_identifier === $clientIdentifier);
            if (! $isOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Ditolak: Anda tidak memiliki akses ke artefak ini.',
                ], 403);
            }
        }

        // Auto-rescue stalled jobs if queued on server with no queue worker daemon running
        if ($artifact->status === BlueprintDownloadArtifact::STATUS_QUEUED) {
            $createdAt = $artifact->created_at;
            if ($createdAt && Carbon::now()->diffInSeconds($createdAt) >= 2) {
                try {
                    GenerateBlueprintArtifactJob::dispatchSync($artifact->id);
                    $artifact->refresh();
                } catch (\Throwable $e) {
                    Log::error('Auto-rescue inline artifact job execution failed: ' . $e->getMessage());
                }
            }
        }

        $activeLocale = $request->input('locale')
            ?: ($request->cookie('neriah_locale')
            ?: (session('locale') ?: ($blueprint->user_metadata['output_locale'] ?? app()->getLocale())));
        $locale = in_array(strtolower((string) $activeLocale), ['en', 'id'], true) ? strtolower((string) $activeLocale) : 'id';

        return response()->json([
            'success' => true,
            'artifact' => $this->formatArtifactPayload($artifact, $locale, $isSuperAdmin),
        ]);
    }

    /**
     * Download the artifact with strict security token & client isolation validation.
     */
    public function downloadArtifact(Request $request, string $slug, string $id, string $token)
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $artifact = BlueprintDownloadArtifact::where('id', $id)
            ->where('vision_blueprint_id', $blueprint->id)
            ->where('download_token', $token)
            ->firstOrFail();

        $clientIdentifier = $this->resolveClientIdentifier($request, $blueprint);
        $user = Auth::user();
        $isSuperAdmin = $this->isSuperAdmin();

        // Enforce strict client-level access control
        if (! $isSuperAdmin) {
            $isOwner = ($user && $artifact->user_id === $user->id) || ($artifact->client_identifier === $clientIdentifier);
            if (! $isOwner) {
                abort(403, 'Akses Ditolak: Berkas unduhan ini bersifat terisolasi dan hanya dapat diunduh oleh ID klien pemiliknya.');
            }
        }

        // Check if expired
        if ($artifact->isExpired() || ! $artifact->fileExistsOnDisk()) {
            $artifact->deletePhysicalFile();
            $artifact->update([
                'status' => BlueprintDownloadArtifact::STATUS_EXPIRED,
                'file_path' => null,
            ]);

            return back()->with('error', 'Masa aktif berkas unduhan telah habis (retensi 24 jam) dan berkas otomatis dimusnahkan demi keamanan data. Silakan klik "Buat Ulang" di Pusat Unduhan.');
        }

        // Increment download count and update last downloaded timestamp
        $artifact->increment('download_count');
        $artifact->update(['last_downloaded_at' => Carbon::now()]);

        $disk = $artifact->disk ?: 'local';
        return Storage::disk($disk)->download(
            $artifact->file_path,
            $artifact->filename,
            [
                'Content-Type' => $artifact->mime_type ?: 'application/octet-stream',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            ]
        );
    }

    /**
     * Delete an artifact manually (client or Superadmin).
     */
    public function deleteArtifact(Request $request, string $slug, string $id): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $artifact = BlueprintDownloadArtifact::where('id', $id)
            ->where('vision_blueprint_id', $blueprint->id)
            ->firstOrFail();

        $clientIdentifier = $this->resolveClientIdentifier($request, $blueprint);
        $user = Auth::user();
        $isSuperAdmin = $this->isSuperAdmin();

        if (! $isSuperAdmin) {
            $isOwner = ($user && $artifact->user_id === $user->id) || ($artifact->client_identifier === $clientIdentifier);
            if (! $isOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Ditolak: Anda tidak memiliki wewenang untuk menghapus berkas ini.',
                ], 403);
            }
        }

        $artifact->deletePhysicalFile();
        $artifact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berkas unduhan berhasil dihapus secara permanen.',
        ]);
    }

    /**
     * Trigger manual purge of all expired artifacts for this blueprint.
     */
    public function cleanupExpired(Request $request, string $slug): JsonResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->authorizeBlueprintAccess($blueprint);

        $count = BlueprintDownloadArtifact::cleanupExpired();

        return response()->json([
            'success' => true,
            'purged_count' => $count,
            'message' => "Pembersihan selesai: {$count} berkas kadaluwarsa telah dihapus dari server.",
        ]);
    }

    /**
     * Helper to format artifact JSON payload with countdown and status metrics.
     */
    protected function formatArtifactPayload(BlueprintDownloadArtifact $art, string $locale, bool $isSuperAdmin = false): array
    {
        $remainingSecs = $art->remaining_seconds;
        $isExpired = $art->isExpired();

        // Calculate countdown percentage remaining (0% to 100%)
        $totalLifespanSecs = BlueprintDownloadArtifact::RETENTION_HOURS * 3600;
        $percentageRemaining = $isExpired ? 0 : (int) min(100, max(0, round(($remainingSecs / $totalLifespanSecs) * 100)));

        $downloadUrl = ($art->status === BlueprintDownloadArtifact::STATUS_COMPLETED && ! $isExpired)
            ? route('blueprint.artifacts.download', [
                'slug' => $art->blueprint?->slug ?: 'project',
                'id' => $art->id,
                'token' => $art->download_token,
            ])
            : null;

        $pollUrl = route('blueprint.artifacts.status', [
            'slug' => $art->blueprint?->slug ?: 'project',
            'id' => $art->id,
        ]);

        $payload = [
            'id' => $art->id,
            'artifact_type' => $art->artifact_type,
            'locale' => $art->locale,
            'title' => $art->title,
            'filename' => $art->filename,
            'status' => $art->status,
            'progress' => $art->progress,
            'file_size_formatted' => $art->formatted_file_size,
            'file_size_bytes' => $art->file_size_bytes,
            'download_url' => $downloadUrl,
            'poll_url' => $pollUrl,
            'download_count' => $art->download_count,
            'remaining_seconds' => $remainingSecs,
            'formatted_remaining_time' => $art->formattedRemainingTime($locale),
            'percentage_remaining' => $percentageRemaining,
            'is_expired' => $isExpired,
            'expires_at' => $art->expires_at?->toIso8601String(),
            'completed_at' => $art->completed_at?->toIso8601String(),
            'created_at' => $art->created_at?->toIso8601String(),
            'error_message' => $art->error_message,
        ];

        if ($isSuperAdmin) {
            $payload['superadmin_meta'] = [
                'user_id' => $art->user_id,
                'user_email' => $art->user?->email,
                'client_identifier' => $art->client_identifier,
                'ip_hash' => substr($art->client_identifier, 0, 12),
            ];
        }

        return $payload;
    }
}

