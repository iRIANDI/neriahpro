<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pos_sectors')) {
            Schema::table('pos_sectors', function (Blueprint $table) {
                if (! Schema::hasColumn('pos_sectors', 'ui_ux_archetypes')) {
                    $table->json('ui_ux_archetypes')->nullable()->after('blueprint_invariants');
                }
                if (! Schema::hasColumn('pos_sectors', 'realistic_seeder_contract')) {
                    $table->json('realistic_seeder_contract')->nullable()->after('ui_ux_archetypes');
                }
            });
        }

        if (Schema::hasTable('pos_projects')) {
            Schema::table('pos_projects', function (Blueprint $table) {
                if (! Schema::hasColumn('pos_projects', 'phased_prompts_json')) {
                    $table->json('phased_prompts_json')->nullable()->after('compiled_graph_json');
                }
            });
        }

        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                if (! Schema::hasColumn('audit_logs', 'event_category')) {
                    $table->string('event_category', 48)->nullable()->index()->after('action');
                }
                if (! Schema::hasColumn('audit_logs', 'integrity_hash')) {
                    $table->string('integrity_hash', 64)->nullable()->index()->after('context_url');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pos_sectors')) {
            Schema::table('pos_sectors', function (Blueprint $table) {
                $cols = array_values(array_filter(['ui_ux_archetypes', 'realistic_seeder_contract'], fn (string $c) => Schema::hasColumn('pos_sectors', $c)));
                if (! empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('pos_projects') && Schema::hasColumn('pos_projects', 'phased_prompts_json')) {
            Schema::table('pos_projects', function (Blueprint $table) {
                $table->dropColumn('phased_prompts_json');
            });
        }

        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $cols = array_values(array_filter(['event_category', 'integrity_hash'], fn (string $c) => Schema::hasColumn('audit_logs', $c)));
                if (! empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};

