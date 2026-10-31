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
        'event_category',
        'description',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'context_url',
        'integrity_hash',
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
        ?string $description = null,
        ?string $eventCategory = null
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

            $modelClass = $model ? get_class($model) : null;
            $modelKey = $model ? (string) $model->getKey() : null;
            $resolvedIp = Request::ip() ?: '127.0.0.1';
            $resolvedUrl = $contextUrl ?: substr((string) Request::fullUrl(), 0, 500);

            $resolvedCategory = $eventCategory ?: static::inferCategory($action, $resolvedActorType, $modelClass, $description);

            $canonicalPayload = json_encode([
                'user_id' => $userId,
                'actor_name' => $resolvedActorName,
                'actor_type' => $resolvedActorType,
                'action' => $action,
                'event_category' => $resolvedCategory,
                'model_type' => $modelClass,
                'model_id' => $modelKey,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $resolvedIp,
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            $payload = [
                'user_id' => $userId,
                'actor_name' => $resolvedActorName,
                'actor_type' => $resolvedActorType,
                'action' => $action,
                'description' => $description,
                'model_type' => $modelClass,
                'model_id' => $modelKey,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $resolvedIp,
                'user_agent' => substr((string) Request::userAgent(), 0, 500),
                'context_url' => $resolvedUrl,
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn('audit_logs', 'event_category')) {
                $payload['event_category'] = $resolvedCategory;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('audit_logs', 'integrity_hash')) {
                $payload['integrity_hash'] = hash('sha256', (string) $canonicalPayload);
            }

            return static::create($payload);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function inferCategory(string $action, ?string $actorType, ?string $modelClass, ?string $description): string
    {
        $haystack = strtolower($action . ' ' . ($modelClass ?? '') . ' ' . ($description ?? ''));

        if (str_contains($haystack, 'bast_issued') || str_contains($haystack, 'uat_bast') || str_contains($haystack, 'uat_scenario') || str_contains($haystack, 'sign_bast') || str_contains($haystack, 'berita acara')) {
            return 'uat_bast';
        }
        if (str_contains($haystack, 'addendum_approved') || str_contains($haystack, 'paid_addendum') || str_contains($haystack, 'npro-add-') || str_contains($haystack, 'addendum_invoiced')) {
            return 'paid_addendum';
        }
        if (str_contains($haystack, 'scope_locked_spk') || str_contains($haystack, 'commercial_contract') || str_contains($haystack, 'spk_appendix') || str_contains($haystack, 'mitra_quotation')) {
            return 'commercial_contract';
        }
        if (str_contains($haystack, 'delta_cr') || str_contains($haystack, 'poschangerequest') || str_contains($haystack, 'delta_evolution') || str_contains($haystack, 'delta_change')) {
            return 'delta_evolution';
        }
        if (str_contains($haystack, 'mcp_') || str_contains($haystack, 'mcp_bridge') || str_contains($haystack, 'neriahpro-mcp')) {
            return 'mcp_bridge';
        }
        if (str_contains($haystack, 'redteam') || str_contains($haystack, 'red_team') || str_contains($haystack, 'tenant_isolation')) {
            return 'redteam_security';
        }
        if (str_contains($haystack, 'pos:check-env') || str_contains($haystack, 'env_vault') || str_contains($haystack, 'env.blueprint')) {
            return 'env_vault';
        }
        if (str_contains($haystack, 'blueprint_doctor') || str_contains($haystack, 'doctor_diagnos')) {
            return 'blueprint_doctor';
        }
        if (str_contains($haystack, 'day2_ops') || str_contains($haystack, 'thermal_print') || str_contains($haystack, 'staged_import')) {
            return 'day2_ops';
        }
        if (str_contains($haystack, 'brownfield')) {
            return 'brownfield_import';
        }
        if (str_contains($haystack, 'slop') || str_contains($haystack, 'pos:audit') || str_contains($haystack, 'drift')) {
            return 'slop_audit';
        }
        if (str_contains($haystack, 'cli_bootstrap') || str_contains($haystack, '/init/')) {
            return 'cli_bootstrap';
        }
        if ($actorType === 'ai_agent' || str_contains($haystack, 'ai agent') || str_contains($haystack, 'copilot') || str_contains($haystack, 'rag')) {
            return 'ai_agent';
        }
        if (str_contains($haystack, 'pos') || str_contains($haystack, 'blueprint') || str_contains($haystack, 'document')) {
            return 'project_os';
        }
        if (str_contains($haystack, 'transaction') || str_contains($haystack, 'midtrans') || str_contains($haystack, 'voucher')) {
            return 'billing';
        }
        if (str_contains($haystack, 'role') || str_contains($haystack, 'permission') || str_contains($haystack, 'rbac')) {
            return 'rbac';
        }
        if (str_contains($haystack, 'login') || str_contains($haystack, 'threat') || str_contains($haystack, 'security')) {
            return 'security';
        }

        return 'system';
    }
}

