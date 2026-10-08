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
                // Check if 'data' column is already json or jsonb in PostgreSQL
                $isAlreadyJson = false;
                try {
                    $col = DB::selectOne("
                        SELECT data_type 
                        FROM information_schema.columns 
                        WHERE table_name = 'notifications' AND column_name = 'data'
                    ");
                    if ($col && in_array(strtolower($col->data_type), ['json', 'jsonb'])) {
                        $isAlreadyJson = true;
                    }
                } catch (\Throwable) {
                    $colType = Schema::getColumnType('notifications', 'data');
                    if (in_array(strtolower($colType), ['json', 'jsonb'])) {
                        $isAlreadyJson = true;
                    }
                }

                // If already json/jsonb, do not perform redundant alter
                if ($isAlreadyJson) {
                    return;
                }

                // PostgreSQL: Ensure any empty, null, or non-JSON values are sanitized to valid JSON object '{}'
                // Explicitly cast data::text to avoid pg_catalog.btrim(json) error
                DB::statement("UPDATE notifications SET data = '{}'::jsonb WHERE data IS NULL OR trim(data::text) = '' OR data::text NOT LIKE '{%';");
                // Alter data column type from text to jsonb with safe USING cast
                DB::statement("ALTER TABLE notifications ALTER COLUMN data TYPE jsonb USING (CASE WHEN data IS NULL OR trim(data::text) = '' OR data::text NOT LIKE '{%' THEN '{}'::jsonb ELSE data::jsonb END);");
            } elseif ($driver === 'sqlite') {
                // SQLite supports JSON natively in text/json without strict type enforcement
            } else {
                // MySQL / MariaDB
                $colType = Schema::getColumnType('notifications', 'data');
                if (strtolower($colType) !== 'json') {
                    Schema::table('notifications', function (Blueprint $table) {
                        $table->json('data')->change();
                    });
                }
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
                try {
                    $col = DB::selectOne("
                        SELECT data_type 
                        FROM information_schema.columns 
                        WHERE table_name = 'notifications' AND column_name = 'data'
                    ");
                    if ($col && in_array(strtolower($col->data_type), ['json', 'jsonb'])) {
                        DB::statement("ALTER TABLE notifications ALTER COLUMN data TYPE text USING data::text;");
                    }
                } catch (\Throwable) {
                    // Fallback safely
                }
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
