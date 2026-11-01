<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class PosProject extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'pos_projects';

    protected $fillable = [
        'user_id',
        'vision_blueprint_id',
        'isolation_token',
        'primary_sector_id',
        'stack_preset_id',
        'app_name',
        'architecture_archetype',
        'tenancy_model',
        'tenant_identifier',
        'stack_preset',
        'current_version',
        'selected_primitive_ids',
        'selected_rule_pack_ids',
        'custom_entities',
        'enterprise_menu_json',
        'compiled_schema_json',
        'compiled_execution_md',
        'compiled_agents_md',
        'compiled_graph_json',
        'symbol_manifest_json',
        'phased_prompts_json',
        'topological_chunks_json',
        'doctor_diagnostics_json',
        'mcp_token_hash',
        'mcp_token_prefix',
        'decision_tables_json',
        'phase_progress_json',
        'drift_verification_json',
        'last_mcp_synced_at',
        'engagement_mode',
        'persona_mode',
        'selected_ui_variant',
        'navigation_shell',
        'output_locale',
        'jurisdiction_profile',
        'scope_hash_sha256',
        'scope_locked_at',
        'commercial_quotation_json',
        'uat_checklist_json',
        'bast_signoff_json',
        'hardware_iot_contract_json',
        'websocket_contract_json',
        'query_partition_contract_json',
        'syntax_lock_version',
        'client_sop_onboarding_json',
        'white_label_branding_json',
        'encrypted_custom_vault',
        'canary_watermark_sig',
        'cve_alerts_ack_json',
        'architectural_freeze_score',
    ];

    protected function casts(): array
    {
        return [
            'selected_primitive_ids' => 'array',
            'selected_rule_pack_ids' => 'array',
            'custom_entities' => 'array',
            'enterprise_menu_json' => 'array',
            'symbol_manifest_json' => 'array',
            'phased_prompts_json' => 'array',
            'topological_chunks_json' => 'array',
            'doctor_diagnostics_json' => 'array',
            'decision_tables_json' => 'array',
            'phase_progress_json' => 'array',
            'drift_verification_json' => 'array',
            'last_mcp_synced_at' => 'datetime',
            'scope_locked_at' => 'datetime',
            'commercial_quotation_json' => 'array',
            'uat_checklist_json' => 'array',
            'bast_signoff_json' => 'array',
            'hardware_iot_contract_json' => 'array',
            'websocket_contract_json' => 'array',
            'query_partition_contract_json' => 'array',
            'client_sop_onboarding_json' => 'array',
            'white_label_branding_json' => 'array',
            'encrypted_custom_vault' => 'encrypted:array',
            'cve_alerts_ack_json' => 'array',
            'architectural_freeze_score' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function visionBlueprint(): BelongsTo
    {
        return $this->belongsTo(VisionBlueprint::class, 'vision_blueprint_id');
    }

    public function primarySector(): BelongsTo
    {
        return $this->belongsTo(PosSector::class, 'primary_sector_id');
    }

    public function stackPreset(): BelongsTo
    {
        return $this->belongsTo(PosStackPreset::class, 'stack_preset_id');
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(PosChangeRequest::class, 'pos_project_id')->orderByDesc('id');
    }

    /**
     * Issue or deterministically derive an MCP token for IDE bridges (Cursor, Claude Code, Antigravity)
     * while storing ONLY its SHA-256 hash in the database.
     */
    public function issueMcpToken(bool $rotate = false): string
    {
        $seed = $rotate
            ? bin2hex(random_bytes(16))
            : hash_hmac('sha256', 'neriahpro-mcp-v4:' . $this->id, (string) config('app.key', 'neriahpro-pos-v4'));

        $plainToken = 'npro_mcp_' . substr($seed, 0, 32);
        $hash = hash('sha256', $plainToken);
        $prefix = substr($plainToken, 0, 14);

        if ($this->exists && Schema::hasColumn('pos_projects', 'mcp_token_hash')) {
            if ($rotate || empty($this->mcp_token_hash)) {
                $this->forceFill([
                    'mcp_token_hash' => $hash,
                    'mcp_token_prefix' => $prefix,
                ])->saveQuietly();
            }
        }

        return $plainToken;
    }

    public function verifyMcpToken(string $candidateToken): bool
    {
        $clean = trim($candidateToken);
        if ($clean === '') {
            return false;
        }

        if (! empty($this->mcp_token_hash)) {
            return hash_equals((string) $this->mcp_token_hash, hash('sha256', $clean));
        }

        $deterministic = 'npro_mcp_' . substr(
            hash_hmac('sha256', 'neriahpro-mcp-v4:' . $this->id, (string) config('app.key', 'neriahpro-pos-v4')),
            0,
            32
        );

        return hash_equals($deterministic, $clean);
    }

    public function getSectorCodeAttribute(): ?string
    {
        return $this->primarySector?->code;
    }

    /**
     * Banking-Grade Security Layer #3 (v8.0): 1-Click Scoped MCP Token Revocation & Rotation.
     * Immediately invalidates any prior token hash in DB and emits a chained SHA-256 AuditLog record.
     */
    public function revokeMcpToken(?string $actorUserId = null): string
    {
        $newPlainToken = $this->issueMcpToken(true);

        if (class_exists(AuditLog::class)) {
            AuditLog::record(
                action: 'mcp_scoped_token_revoked',
                model: $this,
                newValues: [
                    'pos_project_id' => $this->id,
                    'new_prefix' => $this->mcp_token_prefix,
                    'actor_user_id' => $actorUserId,
                ],
                actorType: $actorUserId ? 'user' : 'system',
                description: "Scoped MCP token revoked & rotated for PosProject [{$this->app_name}] (New prefix: {$this->mcp_token_prefix})",
                eventCategory: 'ip_canary_security'
            );
        }

        return $newPlainToken;
    }

    /**
     * Banking-Grade Security Layer #1 (v8.0): Store custom business secrets in AES-256 encrypted column vault.
     *
     * @param  array<string, mixed>  $secrets
     */
    public function storeEncryptedVault(array $secrets): void
    {
        if (! Schema::hasColumn('pos_projects', 'encrypted_custom_vault')) {
            return;
        }

        $this->encrypted_custom_vault = $secrets;
        $this->saveQuietly();
    }

    /**
     * Read decrypted custom business secrets from AES-256 envelope vault.
     *
     * @return array<string, mixed>
     */
    public function readEncryptedVault(): array
    {
        if (! Schema::hasColumn('pos_projects', 'encrypted_custom_vault')) {
            return [];
        }

        $val = $this->encrypted_custom_vault;

        return is_array($val) ? $val : [];
    }
}

