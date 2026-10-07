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

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_retail_spark_price'],
            ['value' => '0']
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_retail_lite_price'],
            ['value' => '99.000']
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_retail_pro_price'],
            ['value' => '399.000']
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_retail_ultimate_price'],
            ['value' => '1.490.000']
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_retail_spark_limit'],
            ['value' => '2x Audit Ide / Bulan (Reset tiap tanggal 1)']
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_retail_lite_limit'],
            ['value' => '1 Proyek PRD (Revisi Form 30 Hari & Unduh Selamanya)']
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_retail_pro_limit'],
            ['value' => '1 Proyek PRD (Unlimited AI Regen & Revisi 6 Bulan)']
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_retail_ultimate_limit'],
            ['value' => '1 Proyek Enterprise (1 Tahun Prioritas & 1-on-1 Call 60 Menit)']
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_retail_login_policy'],
            ['value' => 'Guest Mode untuk Spark (Free). Wajib Login / Daftar Akun untuk paket Lite, Pro, & Ultimate guna proteksi dokumen & lisensi.']
        );

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'pricing_retail_disclaimer'],
            ['value' => 'Paket Instant Architectural Blueprint 100% Self-Service: Dihasilkan instan oleh AI Project OS untuk Anda atau tim developer Anda kerjakan sendiri. Tidak ada koding atau pembuatan aplikasi oleh Neriah Pro.']
        );
    }
}
