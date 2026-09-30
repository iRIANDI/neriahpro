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

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'company_whatsapp'],
            [
                'value' => '628123456789',
            ]
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'feature_enable_cv_pro'],
            ['value' => true]
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'feature_enable_cv_pricing'],
            ['value' => true]
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'feature_enable_cv_job_hub'],
            ['value' => true]
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'feature_enable_cv_keuangan'],
            ['value' => true]
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'feature_enable_cv_mock_interview'],
            ['value' => true]
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'feature_enable_cv_linkedin_suite'],
            ['value' => true]
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'feature_enable_vision_blueprint'],
            ['value' => true]
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'feature_enable_client_onboarding'],
            ['value' => true]
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'midtrans_compliance_strict_mode'],
            ['value' => false]
        );
    }
}
