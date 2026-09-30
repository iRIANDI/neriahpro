<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('notifications')) {
            $driver = DB::getDriverName();

            if ($driver === 'pgsql') {
                // PostgreSQL: Ensure any empty, null, or non-JSON values are sanitized to valid JSON object '{}'
                DB::statement("UPDATE notifications SET data = '{}' WHERE data IS NULL OR trim(data) = '' OR data = '' OR data NOT LIKE '{%';");
                // Alter data column type from text to jsonb with safe USING cast
                DB::statement("ALTER TABLE notifications ALTER COLUMN data TYPE jsonb USING (CASE WHEN data IS NULL OR trim(data) = '' OR data NOT LIKE '{%' THEN '{}'::jsonb ELSE data::jsonb END);");
            } elseif ($driver === 'sqlite') {
                // SQLite supports JSON natively in text/json without strict type enforcement
            } else {
                // MySQL / MariaDB
                Schema::table('notifications', function (Blueprint $table) {
                    $table->json('data')->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('notifications')) {
            $driver = DB::getDriverName();

            if ($driver === 'pgsql') {
                DB::statement("ALTER TABLE notifications ALTER COLUMN data TYPE text USING data::text;");
            } elseif ($driver === 'sqlite') {
                // SQLite
            } else {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->text('data')->change();
                });
            }
        }
    }
};
