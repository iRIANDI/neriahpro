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
            ['key' => 'feature_enable_digital_contract'],
            ['value' => true]
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'midtrans_compliance_strict_mode'],
            ['value' => true]
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_advisory_price'],
            ['value' => '2.500.000']
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_mvp_price'],
            ['value' => '50.000.000']
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_umkm_price'],
            ['value' => '7.500.000']
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_active_promo_banner'],
            ['value' => 'Gunakan Kode Voucher "UMKM-SUBSIDI-50" untuk subsidi 50% atau "CORP-INNOVATION-15M" untuk potongan Rp 15 Juta!']
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_sales_pic_email'],
            ['value' => 'sales@neriahpro.com']
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_consultation_sla'],
            ['value' => 'Maksimal 2 Jam Kerja']
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_enable_instant_whatsapp'],
            ['value' => true]
        );
    }
}
