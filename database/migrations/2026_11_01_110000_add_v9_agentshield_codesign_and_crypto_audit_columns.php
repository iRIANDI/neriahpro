<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table): void {
                if (! Schema::hasColumn('audit_logs', 'event_category')) {
                    $table->string('event_category', 64)->nullable()->after('action')->index();
                }
                if (! Schema::hasColumn('audit_logs', 'integrity_hash')) {
                    $table->string('integrity_hash', 64)->nullable()->after('context_url');
                }
                if (! Schema::hasColumn('audit_logs', 'organization_id')) {
                    $table->string('organization_id', 26)->nullable()->after('id')->index();
                }
                if (! Schema::hasColumn('audit_logs', 'sector_code')) {
                    $table->string('sector_code', 64)->nullable()->after('event_category')->index();
                }
                if (! Schema::hasColumn('audit_logs', 'primitive_code')) {
                    $table->string('primitive_code', 64)->nullable()->after('sector_code')->index();
                }
                if (! Schema::hasColumn('audit_logs', 'execution_channel')) {
                    $table->string('execution_channel', 32)->default('WEB_UI')->after('actor_type')->index();
                }
                if (! Schema::hasColumn('audit_logs', 'idempotency_key')) {
                    $table->string('idempotency_key', 96)->nullable()->after('context_url')->index();
                }
                if (! Schema::hasColumn('audit_logs', 'prev_hash')) {
                    $table->string('prev_hash', 64)->nullable()->after('integrity_hash');
                }
                if (! Schema::hasColumn('audit_logs', 'current_hash')) {
                    $table->string('current_hash', 64)->nullable()->after('prev_hash')->index();
                }
            });
        }

        if (Schema::hasTable('pos_projects')) {
            Schema::table('pos_projects', function (Blueprint $table): void {
                if (! Schema::hasColumn('pos_projects', 'codesign_modules_json')) {
                    $table->json('codesign_modules_json')->nullable()->after('cve_alerts_ack_json');
                }
                if (! Schema::hasColumn('pos_projects', 'codesign_dual_key_json')) {
                    $table->json('codesign_dual_key_json')->nullable()->after('codesign_modules_json');
                }
                if (! Schema::hasColumn('pos_projects', 'approved_dependency_manifest_json')) {
                    $table->json('approved_dependency_manifest_json')->nullable()->after('codesign_dual_key_json');
                }
                if (! Schema::hasColumn('pos_projects', 'agentshield_policy_json')) {
                    $table->json('agentshield_policy_json')->nullable()->after('approved_dependency_manifest_json');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('audit_logs')) {
            $dropAuditCols = array_values(array_filter([
                Schema::hasColumn('audit_logs', 'organization_id') ? 'organization_id' : null,
                Schema::hasColumn('audit_logs', 'sector_code') ? 'sector_code' : null,
                Schema::hasColumn('audit_logs', 'primitive_code') ? 'primitive_code' : null,
                Schema::hasColumn('audit_logs', 'execution_channel') ? 'execution_channel' : null,
                Schema::hasColumn('audit_logs', 'idempotency_key') ? 'idempotency_key' : null,
                Schema::hasColumn('audit_logs', 'prev_hash') ? 'prev_hash' : null,
                Schema::hasColumn('audit_logs', 'current_hash') ? 'current_hash' : null,
            ]));

            if (! empty($dropAuditCols)) {
                Schema::table('audit_logs', function (Blueprint $table) use ($dropAuditCols): void {
                    $table->dropColumn($dropAuditCols);
                });
            }
        }

        if (Schema::hasTable('pos_projects')) {
            $dropPosCols = array_values(array_filter([
                Schema::hasColumn('pos_projects', 'codesign_modules_json') ? 'codesign_modules_json' : null,
                Schema::hasColumn('pos_projects', 'codesign_dual_key_json') ? 'codesign_dual_key_json' : null,
                Schema::hasColumn('pos_projects', 'approved_dependency_manifest_json') ? 'approved_dependency_manifest_json' : null,
                Schema::hasColumn('pos_projects', 'agentshield_policy_json') ? 'agentshield_policy_json' : null,
            ]));

            if (! empty($dropPosCols)) {
                Schema::table('pos_projects', function (Blueprint $table) use ($dropPosCols): void {
                    $table->dropColumn($dropPosCols);
                });
            }
        }
    }
};

