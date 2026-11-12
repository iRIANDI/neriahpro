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
        'organization_id',
        'user_id',
        'actor_name',
        'actor_type',
        'execution_channel',
        'action',
        'event_category',
        'sector_code',
        'primitive_code',
        'description',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'context_url',
        'idempotency_key',
        'integrity_hash',
        'prev_hash',
        'current_hash',
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
        ?string $eventCategory = null,
        ?string $organizationId = null,
        ?string $sectorCode = null,
        ?string $primitiveCode = null,
        ?string $executionChannel = null,
        ?string $idempotencyKey = null
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
            $resolvedChannel = $executionChannel ?: ($resolvedActorType === 'ai_agent'
                ? 'AI_COPILOT_TOOL'
                : (app()->runningInConsole() ? 'CLI' : 'WEB_UI'));

            $resolvedSector = $sectorCode;
            if (! $resolvedSector && $model instanceof PosProject) {
                $resolvedSector = $model->sector_code;
            } elseif (! $resolvedSector && is_array($newValues) && ! empty($newValues['sector_code'])) {
                $resolvedSector = (string) $newValues['sector_code'];
            }

            $resolvedOrgId = $organizationId ?: ($model instanceof PosProject ? (string) $model->id : null);
            $integrityHash = static::computeCanonicalPayloadHash(
                userId: $userId ? (string) $userId : null,
                actorName: $resolvedActorName,
                actorType: $resolvedActorType,
                action: $action,
                eventCategory: $resolvedCategory,
                modelType: $modelClass,
                modelId: $modelKey,
                oldValues: $oldValues,
                newValues: $newValues,
                ipAddress: $resolvedIp
            );

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
                $payload['integrity_hash'] = $integrityHash;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('audit_logs', 'organization_id') && $resolvedOrgId) {
                $payload['organization_id'] = substr($resolvedOrgId, 0, 26);
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('audit_logs', 'sector_code') && $resolvedSector) {
                $payload['sector_code'] = substr($resolvedSector, 0, 64);
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('audit_logs', 'primitive_code') && $primitiveCode) {
                $payload['primitive_code'] = substr($primitiveCode, 0, 64);
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('audit_logs', 'execution_channel')) {
                $payload['execution_channel'] = substr($resolvedChannel, 0, 32);
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('audit_logs', 'idempotency_key') && $idempotencyKey) {
                $payload['idempotency_key'] = substr($idempotencyKey, 0, 96);
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('audit_logs', 'prev_hash') && \Illuminate\Support\Facades\Schema::hasColumn('audit_logs', 'current_hash')) {
                $latest = static::query()->orderByDesc('id')->select(['current_hash', 'integrity_hash'])->first();
                $prevHash = $latest?->current_hash ?: ($latest?->integrity_hash ?: 'GENESIS');
                $payload['prev_hash'] = $prevHash;
                $payload['current_hash'] = static::computeChainHash(
                    prevHash: $prevHash,
                    organizationId: $resolvedOrgId,
                    actorIdentifier: $userId ? (string) $userId : $resolvedActorName,
                    actionName: $action,
                    targetId: $modelKey,
                    integrityDigest: $integrityHash
                );
            }

            return static::create($payload);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Normalize nested array keys recursively so JSONB column storage in PostgreSQL never drifts SHA-256 verification.
     */
    public static function normalizeRecursive(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $isAssoc = array_keys($value) !== range(0, count($value) - 1);
        if ($isAssoc) {
            ksort($value);
        }

        foreach ($value as $k => $v) {
            $value[$k] = static::normalizeRecursive($v);
        }

        return $value;
    }

    public static function computeCanonicalPayloadHash(
        ?string $userId,
        ?string $actorName,
        ?string $actorType,
        string $action,
        ?string $eventCategory,
        ?string $modelType,
        ?string $modelId,
        ?array $oldValues,
        ?array $newValues,
        ?string $ipAddress
    ): string {
        $canonicalPayload = json_encode([
            'user_id' => $userId,
            'actor_name' => $actorName,
            'actor_type' => $actorType,
            'action' => $action,
            'event_category' => $eventCategory,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => static::normalizeRecursive($oldValues),
            'new_values' => static::normalizeRecursive($newValues),
            'ip_address' => $ipAddress,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return hash('sha256', (string) $canonicalPayload);
    }

    public static function computeChainHash(
        ?string $prevHash,
        ?string $organizationId,
        ?string $actorIdentifier,
        string $actionName,
        ?string $targetId,
        string $integrityDigest
    ): string {
        return hash(
            'sha256',
            ($prevHash ?: 'GENESIS') . '|' .
            ($organizationId ?: 'NERIAHPRO_CORE') . '|' .
            ($actorIdentifier ?: 'SYSTEM') . '|' .
            $actionName . '|' .
            ($targetId ?: '-') . '|' .
            $integrityDigest
        );
    }

    /**
     * Verify both row payload hash and SHA-256 cryptographic chain link (`VERIFIED_INTACT` vs `TAMPER_ALERT_MISMATCH`).
     */
    public function verifyRowIntegrity(?string $expectedPrevHash = null): string
    {
        if (empty($this->integrity_hash) && empty($this->current_hash)) {
            return 'LEGACY_UNSEALED';
        }

        $recomputedPayloadHash = static::computeCanonicalPayloadHash(
            userId: $this->user_id ? (string) $this->user_id : null,
            actorName: $this->actor_name,
            actorType: $this->actor_type,
            action: (string) $this->action,
            eventCategory: $this->event_category,
            modelType: $this->model_type,
            modelId: $this->model_id ? (string) $this->model_id : null,
            oldValues: is_array($this->old_values) ? $this->old_values : null,
            newValues: is_array($this->new_values) ? $this->new_values : null,
            ipAddress: $this->ip_address
        );

        if (! empty($this->integrity_hash) && ! hash_equals((string) $this->integrity_hash, $recomputedPayloadHash)) {
            return 'TAMPER_ALERT_MISMATCH';
        }

        if (! empty($this->current_hash)) {
            if ($expectedPrevHash !== null && ! empty($this->prev_hash) && ! hash_equals($expectedPrevHash, (string) $this->prev_hash)) {
                return 'TAMPER_ALERT_MISMATCH';
            }

            $expectedChain = static::computeChainHash(
                prevHash: $this->prev_hash ?: 'GENESIS',
                organizationId: $this->organization_id,
                actorIdentifier: $this->user_id ? (string) $this->user_id : $this->actor_name,
                actionName: (string) $this->action,
                targetId: $this->model_id ? (string) $this->model_id : null,
                integrityDigest: $recomputedPayloadHash
            );

            if (! hash_equals($expectedChain, (string) $this->current_hash)) {
                return 'TAMPER_ALERT_MISMATCH';
            }
        }

        return 'VERIFIED_INTACT';
    }

    /**
     * Verify chronological cryptographic chain across filtered or recent audit logs in O(1) cursor memory.
     *
     * @param  array<string, mixed>|int  $filters
     * @return array<string, mixed>
     */
    public static function verifyChainSummary(array|int $filters = [], int $maxScan = 2500): array
    {
        if (is_int($filters)) {
            $maxScan = max(1, $filters);
            $filters = [];
        }

        $query = static::query()->orderBy('id', 'asc');
        if (! empty($filters['organization_id'])) {
            $query->where('organization_id', (string) $filters['organization_id']);
        }
        if (! empty($filters['sector_code'])) {
            $query->where('sector_code', (string) $filters['sector_code']);
        }
        if (! empty($filters['execution_channel'])) {
            $query->where('execution_channel', (string) $filters['execution_channel']);
        }

        $totalScanned = 0;
        $verifiedCount = 0;
        $tamperedCount = 0;
        $legacyCount = 0;
        $tamperedIds = [];
        $runningPrevHash = null;
        $isUnfilteredChain = empty($filters['sector_code']) && empty($filters['execution_channel']) && empty($filters['organization_id']);

        foreach ($query->limit($maxScan)->cursor() as $row) {
            $totalScanned++;
            $status = $row->verifyRowIntegrity($isUnfilteredChain ? $runningPrevHash : null);
            if ($status === 'VERIFIED_INTACT') {
                $verifiedCount++;
            } elseif ($status === 'TAMPER_ALERT_MISMATCH') {
                $tamperedCount++;
                if (count($tamperedIds) < 10) {
                    $tamperedIds[] = $row->id;
                }
            } else {
                $legacyCount++;
            }
            $runningPrevHash = $row->current_hash ?: $row->integrity_hash;
        }

        $isIntact = $tamperedCount === 0;
        $headHash = $runningPrevHash ?: 'GENESIS';

        return [
            'status' => $isIntact ? 'VERIFIED_INTACT' : 'TAMPER_ALERT_DETECTED',
            'valid' => $isIntact,
            'is_valid' => $isIntact,
            'is_intact' => $isIntact,
            'is_chain_valid' => $isIntact,
            'total_scanned' => $totalScanned,
            'checked_rows' => $totalScanned,
            'verified_intact_count' => $verifiedCount,
            'verified_chained_rows' => $verifiedCount,
            'tampered_count' => $tamperedCount,
            'tampered_rows_count' => $tamperedCount,
            'legacy_unsealed_count' => $legacyCount,
            'tampered_audit_ulids' => $tamperedIds,
            'head_chain_hash' => $headHash,
            'latest_head_hash' => $headHash,
            'verified_at_utc' => now('UTC')->toIso8601String(),
        ];
    }

    /**
     * Alias for verifyChainSummary() providing O(1) cursor-based SHA-256 chain verification.
     *
     * @param  array<string, mixed>|int  $filters
     * @return array<string, mixed>
     */
    public static function verifyChainIntegrity(array|int $filters = [], int $maxScan = 2500): array
    {
        return static::verifyChainSummary($filters, $maxScan);
    }

    public static function inferCategory(string $action, ?string $actorType, ?string $modelClass, ?string $description): string
    {
        $haystack = strtolower($action . ' ' . ($modelClass ?? '') . ' ' . ($description ?? ''));

        if (str_contains($haystack, 'vip_superadmin_bypass') || str_contains($haystack, 'vip_owner_bypass') || str_contains($haystack, 'superadmin_bypass') || str_contains($haystack, 'universal_superadmin_bypass') || str_contains($haystack, 'bypass_all_payment_gates')) {
            return 'superadmin_bypass';
        }
        if (str_contains($haystack, 'cv_pro') || str_contains($haystack, 'cv_ats') || str_contains($haystack, 'cv_osint') || str_contains($haystack, 'cv_case_study') || str_contains($haystack, 'cv_typst') || str_contains($haystack, 'cv_recruiter') || str_contains($haystack, 'cv_portfolio') || str_contains($haystack, 'usercvquota') || str_contains($haystack, 'cvquotatransaction') || str_contains($haystack, 'cvfinancerecord') || str_contains($haystack, 'app\\models\\resume')) {
            return 'cv_pro_studio';
        }
        if (str_contains($haystack, 'aniwall') || str_contains($haystack, 'verified_testimonial') || str_contains($haystack, 'posverifiedtestimonial')) {
            return 'verified_aniwall_v10';
        }
        if (str_contains($haystack, 'impact_grant') || str_contains($haystack, 'affiliate') || str_contains($haystack, 'finders_fee') || str_contains($haystack, 'saas_billing') || str_contains($haystack, 'workspace_member')) {
            return 'saas_commercial_v10';
        }

        if (str_contains($haystack, 'agentshield') || str_contains($haystack, 'slopquatting') || str_contains($haystack, 'prompt_injection') || str_contains($haystack, 'rag_document_threat') || str_contains($haystack, 'dependency_manifest')) {
            return 'agentshield_security';
        }
        if (str_contains($haystack, 'codesign') || str_contains($haystack, 'co_design') || str_contains($haystack, 'approval_sign_off') || str_contains($haystack, 'dual_key')) {
            return 'codesign_alignment';
        }
        if (str_contains($haystack, 'crypto_audit') || str_contains($haystack, '3sheet') || str_contains($haystack, 'audit_chain_verified')) {
            return 'crypto_audit_explorer';
        }

        if (str_contains($haystack, 'canary_watermark') || str_contains($haystack, 'mcp_token_revoked') || str_contains($haystack, 'encrypted_vault') || str_contains($haystack, 'ip_canary')) {
            return 'ip_canary_security';
        }
        if (str_contains($haystack, 'living_cve') || str_contains($haystack, 'cve_alert') || str_contains($haystack, 'autopatch')) {
            return 'living_cve_alert';
        }
        if (str_contains($haystack, 'whitelabel') || str_contains($haystack, 'white_label') || str_contains($haystack, 'agency_partner')) {
            return 'whitelabel_agency';
        }
        if (str_contains($haystack, 'visual_ui') || str_contains($haystack, 'navigation_shell') || str_contains($haystack, '36_visual')) {
            return 'visual_ui_contract';
        }
        if (str_contains($haystack, 'jurisdiction') || str_contains($haystack, 'output_locale') || str_contains($haystack, 'rtl_layout')) {
            return 'jurisdiction_tier1';
        }
        if (str_contains($haystack, 'dual_persona') || str_contains($haystack, 'business_wizard') || str_contains($haystack, 'storyboard_wizard')) {
            return 'dual_persona_ux';
        }
        if (str_contains($haystack, 'pillars_freeze') || str_contains($haystack, 'fifteen_pillars') || str_contains($haystack, 'posarchitecturepillar') || str_contains($haystack, 'architectural_freeze')) {
            return 'pillars_freeze_100';
        }
        if (str_contains($haystack, 'client_sop') || str_contains($haystack, 'sop_training') || str_contains($haystack, 'onboarding_tour')) {
            return 'client_sop_onboarding';
        }
        if (str_contains($haystack, 'syntax_lock') || str_contains($haystack, 'troubleshooting_instincts')) {
            return 'syntax_lock_2026';
        }
        if (str_contains($haystack, 'websocket') || str_contains($haystack, 'zero_poll') || str_contains($haystack, 'reverb')) {
            return 'websocket_zero_poll';
        }
        if (str_contains($haystack, 'hardware') || str_contains($haystack, 'barcode_wedge') || str_contains($haystack, 'zkteco') || str_contains($haystack, 'turnstile')) {
            return 'hardware_iot_bridge';
        }
        if (str_contains($haystack, 'project_isolation') || str_contains($haystack, 'isolation_token') || str_contains($haystack, 'hermetic_isolation') || str_contains($haystack, 'clean_state')) {
            return 'project_isolation';
        }
        if (str_contains($haystack, 'symbol_manifest')) {
            return 'symbol_manifest';
        }
        if (str_contains($haystack, 'dag_chunker') || str_contains($haystack, 'topological_chunk') || str_contains($haystack, 'kahn')) {
            return 'dag_chunker';
        }
        if (str_contains($haystack, 'enterprise_glue') || str_contains($haystack, 'enterprise_menu') || str_contains($haystack, 'fiscal_period') || str_contains($haystack, 'maker_checker')) {
            return 'enterprise_glue';
        }
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

