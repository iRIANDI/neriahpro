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
                    'headline' => [
                        'id' => 'Pusat Arsitektur & Rekayasa Digital untuk Proyek Berskala Tinggi.',
                        'en' => 'Digital Architecture & Enterprise Software Hub for High-Scale Projects.'
                    ],
                    'subheadline' => [
                        'id' => 'Ubah visi bisnis Anda menjadi Product Requirements Document (PRD) lengkap, skema basis data ERD PostgreSQL Strict ULID, alur kerja bertahap, dan penguncian kontrak kerja sama dalam hitungan menit.',
                        'en' => 'Transform your business vision into comprehensive Product Requirements Documents (PRDs), distributed PostgreSQL Strict ULID schemas, sprint milestones, and locked contracts in minutes.'
                    ],
                    'cta_text' => [
                        'id' => 'Mulai Blueprint Lengkap',
                        'en' => 'Launch Architecture Blueprint'
                    ],
                    'cta_link' => '/blueprint'
                ]
            ],
            [
                'type' => 'feature_grid',
                'is_active' => true,
                'data' => [
                    'title' => [
                        'id' => '4 Pilar Layanan Digital Hub.',
                        'en' => '4 Pillars of Our Digital Hub.'
                    ]
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
                    'headline' => [
                        'id' => 'INVESTASI KARIR IMPIAN // PILIHAN KELAS & KUOTA CV PRO',
                        'en' => 'CAREER ACCELERATION INVESTMENT // CV PRO TIERS & QUOTA'
                    ],
                    'subheadline' => [
                        'id' => 'Pilih paket yang sesuai dengan akselerasi karir Anda. Pengunjung gratis tetap dapat mengisi form secara manual dan mengunduh PDF secara cuma-cuma.',
                        'en' => 'Select the tier tailored to your career trajectory. Free tier includes full manual resume creation and complimentary PDF export.'
                    ],
                ]
            ],
            [
                'type' => 'feature_grid',
                'is_active' => true,
                'data' => [
                    'title' => [
                        'id' => 'Ekosistem Layanan Digital Terintegrasi.',
                        'en' => 'Integrated Digital Services Ecosystem.'
                    ]
                ]
            ]
        ];
        $pricingPage->save();
        
        $this->command->info('Landing Page & Pricing Page seeded successfully with 4 Pillars & CV Pro Pricing Hub data!');
    }
}
