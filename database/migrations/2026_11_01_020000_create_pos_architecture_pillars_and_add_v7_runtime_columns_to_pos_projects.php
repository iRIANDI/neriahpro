<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pos_architecture_pillars')) {
            Schema::create('pos_architecture_pillars', function (Blueprint $table): void {
                $table->ulid('id')->primary();
                $table->unsignedTinyInteger('pillar_number')->unique();
                $table->string('code', 80)->unique();
                $table->string('category', 48)->index();
                $table->json('name');
                $table->json('function_guarantee');
                $table->json('technical_contract');
                $table->json('artifacts_list')->nullable();
                $table->json('related_stage_dependencies')->nullable();
                $table->boolean('is_locked_freeze')->default(true);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'pillar_number'], 'pos_arch_pillars_active_num_idx');
            });
        }

        if (Schema::hasTable('pos_projects')) {
            Schema::table('pos_projects', function (Blueprint $table): void {
                if (! Schema::hasColumn('pos_projects', 'hardware_iot_contract_json')) {
                    $table->json('hardware_iot_contract_json')->nullable();
                }
                if (! Schema::hasColumn('pos_projects', 'websocket_contract_json')) {
                    $table->json('websocket_contract_json')->nullable();
                }
                if (! Schema::hasColumn('pos_projects', 'query_partition_contract_json')) {
                    $table->json('query_partition_contract_json')->nullable();
                }
                if (! Schema::hasColumn('pos_projects', 'syntax_lock_version')) {
                    $table->string('syntax_lock_version', 40)->nullable()->default('2026.7.0-FREEZE');
                }
                if (! Schema::hasColumn('pos_projects', 'client_sop_onboarding_json')) {
                    $table->json('client_sop_onboarding_json')->nullable();
                }
                if (! Schema::hasColumn('pos_projects', 'architectural_freeze_score')) {
                    $table->unsignedTinyInteger('architectural_freeze_score')->default(100);
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pos_projects')) {
            Schema::table('pos_projects', function (Blueprint $table): void {
                $dropCols = array_values(array_filter([
                    'hardware_iot_contract_json',
                    'websocket_contract_json',
                    'query_partition_contract_json',
                    'syntax_lock_version',
                    'client_sop_onboarding_json',
                    'architectural_freeze_score',
                ], static fn (string $col): bool => Schema::hasColumn('pos_projects', $col)));

                if (! empty($dropCols)) {
                    $table->dropColumn($dropCols);
                }
            });
        }

        Schema::dropIfExists('pos_architecture_pillars');
    }
};

