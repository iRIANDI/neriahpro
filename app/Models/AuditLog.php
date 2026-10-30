<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    use HasUlids, Prunable;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'actor_name',
        'actor_type',
        'action',
        'description',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'context_url',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tentukan data audit yang dapat dipangkas (auto-prune) demi menghemat kapasitas disk.
     */
    public function prunable()
    {
        $days = (int) (CmsGlobalSetting::getVal('audit_log_retention_days', 90) ?: 90);
        return static::where('created_at', '<=', now()->subDays(max(7, $days)));
    }

    /**
     * Helper manual untuk membersihkan log lama seketika.
     */
    public static function pruneOldLogs(int $days = 90): int
    {
        return static::where('created_at', '<=', now()->subDays($days))->delete();
    }

    public static function record(
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $actorName = null,
        ?string $actorType = null,
        ?string $contextUrl = null,
        ?string $description = null
    ): ?self {
        if (app()->runningInConsole()) {
            $argv = $_SERVER['argv'] ?? [];
            foreach ($argv as $arg) {
                if (str_contains($arg, 'migrate') || str_contains($arg, 'db:wipe')) {
                    return null;
                }
            }
        }

        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('audit_logs')) {
                return null;
            }
        } catch (\Throwable) {
            return null;
        }

        try {
            $user = auth()->user();

            if (app()->bound('current_ai_agent_user')) {
                $aiUser = app('current_ai_agent_user');
                $resolvedActorName = $actorName ?: (($aiUser->name ?? 'Admin') . ' (via Neriah Pro AI Agent)');
                $resolvedActorType = $actorType ?: 'ai_agent';
                $userId = $aiUser->id ?? $user?->id;
            } else {
                $resolvedActorName = $actorName ?: ($user ? $user->name : 'Sistem Internal');
                $resolvedActorType = $actorType ?: ($user ? ($user->hasRole('super_admin') || $user->hasRole('developer') ? 'admin' : 'client') : 'system');
                $userId = $user?->id;
            }

            return static::create([
                'user_id' => $userId,
                'actor_name' => $resolvedActorName,
                'actor_type' => $resolvedActorType,
                'action' => $action,
                'description' => $description,
                'model_type' => $model ? get_class($model) : null,
                'model_id' => $model ? (string) $model->getKey() : null,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => Request::ip() ?: '127.0.0.1',
                'user_agent' => substr((string) Request::userAgent(), 0, 500),
                'context_url' => $contextUrl ?: substr((string) Request::fullUrl(), 0, 500),
            ]);
        } catch (\Throwable) {
            return null;
        }
    }
}

