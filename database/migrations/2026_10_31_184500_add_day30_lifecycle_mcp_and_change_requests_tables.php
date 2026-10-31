<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pos_projects')) {
            Schema::table('pos_projects', function (Blueprint $table): void {
                if (! Schema::hasColumn('pos_projects', 'current_version')) {
                    $table->string('current_version', 24)->default('v1.0')->after('stack_preset');
                }
                if (! Schema::hasColumn('pos_projects', 'mcp_token_hash')) {
                    $table->string('mcp_token_hash', 64)->nullable()->index()->after('doctor_diagnostics_json');
                }
                if (! Schema::hasColumn('pos_projects', 'mcp_token_prefix')) {
                    $table->string('mcp_token_prefix', 24)->nullable()->after('mcp_token_hash');
                }
                if (! Schema::hasColumn('pos_projects', 'decision_tables_json')) {
                    $table->json('decision_tables_json')->nullable()->after('mcp_token_prefix');
                }
                if (! Schema::hasColumn('pos_projects', 'phase_progress_json')) {
                    $table->json('phase_progress_json')->nullable()->after('decision_tables_json');
                }
                if (! Schema::hasColumn('pos_projects', 'drift_verification_json')) {
                    $table->json('drift_verification_json')->nullable()->after('phase_progress_json');
                }
                if (! Schema::hasColumn('pos_projects', 'last_mcp_synced_at')) {
                    $table->timestamp('last_mcp_synced_at')->nullable()->after('drift_verification_json');
                }
            });
        }

        if (! Schema::hasTable('pos_change_requests')) {
            Schema::create('pos_change_requests', function (Blueprint $table): void {
                $table->ulid('id')->primary();
                $table->foreignUlid('pos_project_id')->nullable()->constrained('pos_projects')->restrictOnDelete();
                $table->foreignUlid('vision_blueprint_id')->nullable()->constrained('vision_blueprints')->restrictOnDelete();
                $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('from_version', 24)->default('v1.0');
                $table->string('to_version', 24)->default('v1.1');
                $table->string('title', 255);
                $table->text('change_summary')->nullable();
                $table->json('added_primitives')->nullable();
                $table->json('added_tables')->nullable();
                $table->json('added_decision_rules')->nullable();
                $table->longText('delta_migrations_sql')->nullable();
                $table->json('delta_graph_diff')->nullable();
                $table->json('delta_tool_declarations')->nullable();
                $table->json('regression_test_scenarios')->nullable();
                $table->longText('surgical_delta_prompt')->nullable();
                $table->string('status', 32)->default('READY')->index();
                $table->timestamps();

                $table->index(['vision_blueprint_id', 'id'], 'pos_cr_blueprint_cursor_idx');
                $table->index(['pos_project_id', 'id'], 'pos_cr_project_cursor_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_change_requests');

        if (Schema::hasTable('pos_projects')) {
            Schema::table('pos_projects', function (Blueprint $table): void {
                $cols = array_filter([
                    Schema::hasColumn('pos_projects', 'current_version') ? 'current_version' : null,
                    Schema::hasColumn('pos_projects', 'mcp_token_hash') ? 'mcp_token_hash' : null,
                    Schema::hasColumn('pos_projects', 'mcp_token_prefix') ? 'mcp_token_prefix' : null,
                    Schema::hasColumn('pos_projects', 'decision_tables_json') ? 'decision_tables_json' : null,
                    Schema::hasColumn('pos_projects', 'phase_progress_json') ? 'phase_progress_json' : null,
                    Schema::hasColumn('pos_projects', 'drift_verification_json') ? 'drift_verification_json' : null,
                    Schema::hasColumn('pos_projects', 'last_mcp_synced_at') ? 'last_mcp_synced_at' : null,
                ]);
                if (! empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};

