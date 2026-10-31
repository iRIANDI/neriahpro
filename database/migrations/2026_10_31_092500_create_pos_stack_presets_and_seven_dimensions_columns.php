<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Curated Tech Stack & Topology Presets (9 Presets across MONOLITH, DECOUPLED_HEADLESS, MICROSERVICES)
        if (! Schema::hasTable('pos_stack_presets')) {
            Schema::create('pos_stack_presets', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->string('code', 64)->unique(); // e.g., STACK_M1_LARAVEL_MONOLITH, STACK_D2_GO_SQLC_NEXTJS, STACK_S1_POLYGLOT_MESH
                $table->string('short_code', 8)->index(); // M1..M4, D1..D3, S1..S2
                $table->string('topology', 32)->index(); // MONOLITH, DECOUPLED_HEADLESS, MICROSERVICES
                $table->unsignedSmallInteger('sort_order')->default(1)->index();
                $table->jsonb('name'); // {"id": "...", "en": "..."}
                $table->string('primary_language', 64);
                $table->jsonb('best_use_cases')->nullable();
                $table->jsonb('layer_technologies'); // Backend, Frontend, Admin, ORM, DB, API Protocol, Message Broker, Saga Orchestrator
                $table->jsonb('directory_structure_contract'); // Exact directory tree AI must obey
                $table->jsonb('inter_layer_type_contract'); // Type-safety sync contract (Livewire Actions, Spatie Data -> TS, Drizzle+Zod, Scramble->Orval, sqlc, Protobuf)
                $table->jsonb('deployment_artifacts'); // Template manifests (railpack.json, Caddyfile, Dockerfile, sqlc.yaml, buf.gen.yaml, turbo.json)
                $table->text('stack_specific_anti_slop_rules'); // Explicit stack-specific bans
                $table->jsonb('detection_keywords')->nullable();
                $table->boolean('is_default')->default(false)->index();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        // 2. Enrich pos_primitives with the 4 Ultimate Zero-Assumption JSONB columns
        if (Schema::hasTable('pos_primitives')) {
            Schema::table('pos_primitives', function (Blueprint $table) {
                if (! Schema::hasColumn('pos_primitives', 'deterministic_formulas')) {
                    $table->jsonb('deterministic_formulas')->nullable()->after('edge_case_playbooks');
                }
                if (! Schema::hasColumn('pos_primitives', 'saga_and_webhook_contracts')) {
                    $table->jsonb('saga_and_webhook_contracts')->nullable()->after('deterministic_formulas');
                }
                if (! Schema::hasColumn('pos_primitives', 'ui_screen_archetypes')) {
                    $table->jsonb('ui_screen_archetypes')->nullable()->after('saga_and_webhook_contracts');
                }
                if (! Schema::hasColumn('pos_primitives', 'acceptance_test_scenarios')) {
                    $table->jsonb('acceptance_test_scenarios')->nullable()->after('ui_screen_archetypes');
                }
            });
        }

        // 3. Link pos_projects relationally to pos_stack_presets via ULID FK
        if (Schema::hasTable('pos_projects') && ! Schema::hasColumn('pos_projects', 'stack_preset_id')) {
            Schema::table('pos_projects', function (Blueprint $table) {
                $table->foreignUlid('stack_preset_id')
                    ->nullable()
                    ->after('primary_sector_id')
                    ->constrained('pos_stack_presets')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pos_projects') && Schema::hasColumn('pos_projects', 'stack_preset_id')) {
            Schema::table('pos_projects', function (Blueprint $table) {
                $table->dropConstrainedForeignId('stack_preset_id');
            });
        }

        if (Schema::hasTable('pos_primitives')) {
            Schema::table('pos_primitives', function (Blueprint $table) {
                $cols = array_filter(
                    ['deterministic_formulas', 'saga_and_webhook_contracts', 'ui_screen_archetypes', 'acceptance_test_scenarios'],
                    fn (string $c) => Schema::hasColumn('pos_primitives', $c)
                );
                if (! empty($cols)) {
                    $table->dropColumn(array_values($cols));
                }
            });
        }

        Schema::dropIfExists('pos_stack_presets');
    }
};

