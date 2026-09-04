<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsGlobalSetting;

class CmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Global Settings
        CmsGlobalSetting::updateOrCreate(
            ['key' => 'main_navigation'],
            [
                'value' => ['enabled' => true],
            ]
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'footer_links'],
            [
                'value' => ['enabled' => true],
            ]
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'app_timezone'],
            [
                'value' => 'UTC',
            ]
        );
    }
}
