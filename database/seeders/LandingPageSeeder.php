<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsPage;

class LandingPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $homePage = CmsPage::firstOrNew(['slug' => 'home']);
        
        $homePage->title = [
            'en' => 'Neriah Pro // Enterprise Architecture & Digital Services Hub',
            'id' => 'Neriah Pro // Pusat Arsitektur & Layanan Rekayasa Digital'
        ];
        
        $homePage->meta_description = [
            'en' => 'High-retention digital architecture platform. Generate PRD blueprints, PostgreSQL Strict ULID schemas, contract lock, and enterprise systems.',
            'id' => 'Platform arsitektur digital teruji. Hasilkan PRD blueprint instan, skema database PostgreSQL Strict ULID, penguncian kontrak, dan sistem enterprise.'
        ];
        
        $homePage->is_published = true;
        
        $plugins = [
            [
                'type' => 'hero_section',
                'is_active' => true,
                'data' => [
                    'headline' => 'PUSAT ARSITEKTUR & REKAYASA DIGITAL UNTUK PROYEK BERSKALA TINGGI.',
                    'subheadline' => 'Ubah visi bisnis Anda menjadi Product Requirements Document (PRD) lengkap, skema basis data ERD PostgreSQL Strict ULID, alur kerja bertahap, dan penguncian kontrak kerja sama dalam hitungan menit.',
                    'cta_text' => 'Mulai Blueprint Lengkap',
                    'cta_link' => '/blueprint'
                ]
            ],
            [
                'type' => 'feature_grid',
                'is_active' => true,
                'data' => [
                    'title' => '4 Pilar Layanan Digital Hub.'
                ]
            ],
            [
                'type' => 'onboarding_form',
                'is_active' => true,
                'data' => [
                    'title' => 'Onboarding Engine & Discovery',
                    'description' => 'Sampaikan ide dan spesifikasi aplikasi Anda secara rahasia dan terenkripsi.'
                ]
            ]
        ];

        $homePage->plugins = $plugins;
        $homePage->save();

        // 2. Dedicated Pricing Page (/pricing)
        $pricingPage = CmsPage::firstOrNew(['slug' => 'pricing']);
        $pricingPage->title = [
            'en' => 'Pricing & Plans // CV Pro Studio & AI Career Suite',
            'id' => 'Pilihan Paket & Kelas Harga // CV Pro Studio & AI Career'
        ];
        $pricingPage->meta_description = [
            'en' => 'Choose your CV Pro plan. Free manual CV creation & PDF download, or unlock AI CV tailoring, mock interview simulator, and LinkedIn branding.',
            'id' => 'Pilih paket CV Pro Anda. Buat CV manual dan download PDF gratis selamanya, atau buka otomatisasi AI penyesuaian loker dan simulasi wawancara.'
        ];
        $pricingPage->is_published = true;
        $pricingPage->plugins = [
            [
                'type' => 'cv_pricing_table',
                'is_active' => true,
                'data' => [
                    'headline' => 'INVESTASI KARIR IMPIAN // PILIHAN KELAS & KUOTA CV PRO',
                    'subheadline' => 'Pilih paket yang sesuai dengan akselerasi karir Anda. Pengunjung gratis tetap dapat mengisi form secara manual dan mengunduh PDF secara cuma-cuma.',
                ]
            ],
            [
                'type' => 'feature_grid',
                'is_active' => true,
                'data' => [
                    'title' => 'Ekosistem Layanan Digital Terintegrasi.'
                ]
            ]
        ];
        $pricingPage->save();
        
        $this->command->info('Landing Page & Pricing Page seeded successfully with 4 Pillars & CV Pro Pricing Hub data!');
    }
}
