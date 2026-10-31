<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pos_projects')) {
            Schema::table('pos_projects', function (Blueprint $table) {
                if (! Schema::hasColumn('pos_projects', 'isolation_token')) {
                    $table->string('isolation_token', 40)->nullable()->unique()->after('vision_blueprint_id');
                }
                if (! Schema::hasColumn('pos_projects', 'enterprise_menu_json')) {
                    $table->json('enterprise_menu_json')->nullable()->after('custom_entities');
                }
                if (! Schema::hasColumn('pos_projects', 'symbol_manifest_json')) {
                    $table->json('symbol_manifest_json')->nullable()->after('compiled_graph_json');
                }
                if (! Schema::hasColumn('pos_projects', 'topological_chunks_json')) {
                    $table->json('topological_chunks_json')->nullable()->after('phased_prompts_json');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pos_projects')) {
            Schema::table('pos_projects', function (Blueprint $table) {
                foreach ([
                    'isolation_token',
                    'enterprise_menu_json',
                    'symbol_manifest_json',
                    'topological_chunks_json',
                ] as $col) {
                    if (Schema::hasColumn('pos_projects', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};

