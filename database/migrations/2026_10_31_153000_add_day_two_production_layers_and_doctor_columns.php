<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pos_sectors') && ! Schema::hasColumn('pos_sectors', 'day_two_ops_contract')) {
            Schema::table('pos_sectors', function (Blueprint $table): void {
                $table->json('day_two_ops_contract')->nullable()->after('realistic_seeder_contract');
            });
        }

        if (Schema::hasTable('pos_projects') && ! Schema::hasColumn('pos_projects', 'doctor_diagnostics_json')) {
            Schema::table('pos_projects', function (Blueprint $table): void {
                $table->json('doctor_diagnostics_json')->nullable()->after('phased_prompts_json');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pos_sectors') && Schema::hasColumn('pos_sectors', 'day_two_ops_contract')) {
            Schema::table('pos_sectors', function (Blueprint $table): void {
                $table->dropColumn('day_two_ops_contract');
            });
        }

        if (Schema::hasTable('pos_projects') && Schema::hasColumn('pos_projects', 'doctor_diagnostics_json')) {
            Schema::table('pos_projects', function (Blueprint $table): void {
                $table->dropColumn('doctor_diagnostics_json');
            });
        }
    }
};
