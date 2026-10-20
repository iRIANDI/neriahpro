<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('cms_global_settings')) {
            $existingWa = DB::table('cms_global_settings')->where('key', 'company_whatsapp')->first();
            if ($existingWa) {
                DB::table('cms_global_settings')->where('key', 'company_whatsapp')->update([
                    'value' => json_encode('6281260840428'),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('cms_global_settings')->insert([
                    'id' => (string) Str::ulid(),
                    'key' => 'company_whatsapp',
                    'value' => json_encode('6281260840428'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $existingPhone = DB::table('cms_global_settings')->where('key', 'developer_phone')->first();
            if ($existingPhone) {
                DB::table('cms_global_settings')->where('key', 'developer_phone')->update([
                    'value' => json_encode('+6281260840428'),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('cms_global_settings')->insert([
                    'id' => (string) Str::ulid(),
                    'key' => 'developer_phone',
                    'value' => json_encode('+6281260840428'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op rollback to preserve official contact information
    }
};
