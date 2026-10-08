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
        // 1. Dual-Track Homepage (/ or /home)
        $homePage = CmsPage::firstOrNew(['slug' => 'home']);
        
        $homePage->title = [
            'en' => 'Enterprise Architecture & Digital Engineering Platform // Neriah Pro',
            'id' => 'Pusat Arsitektur & Rekayasa Sistem Digital // Neriah Pro'
        ];
        
        $homePage->meta_description = [
            'en' => 'Enterprise software architecture & engineering platform. Choose between ready-to-code retail software licenses (PRD, DDL, Scaffold) or full dedicated turnkey engineering.',
            'id' => 'Platform arsitektur & rekayasa perangkat lunak enterprise. Pilih lisensi digital retail siap koding (PRD, DDL, Scaffold) atau rekayasa proyek kustom bersama tim dedicated engineer.'
        ];
        
        $homePage->is_published = true;
        
        $plugins = [
            [
                'type' => 'hero_section',
                'is_active' => true,
                'data' => [
                    'headline' => [
                        'id' => 'Pusat Arsitektur & Rekayasa Sistem Digital Kelas Enterprise.',
                        'en' => 'Enterprise Software Architecture & Engineering Platform.'
                    ],
                    'subheadline' => [
                        'id' => 'Dua jalur solusi rekayasa modern untuk bisnis dan founder: Beli lisensi arsitektur siap pakai (Retail) untuk di-deploy mandiri, atau bangun sistem skala besar bersama tim dedicated engineer kami (Project Studio).',
                        'en' => 'Two modern engineering paths: Acquire instant self-service software factory licenses (Retail) or build mission-critical systems with our dedicated engineering studio.'
                    ],
                    'cta_retail_text' => [
                        'id' => 'Jelajahi Lisensi Retail (Rp 99k - 1,49jt)',
                        'en' => 'Explore Retail Licenses'
                    ],
                    'cta_project_text' => [
                        'id' => 'Konsultasi Proyek Dedicated Studio',
                        'en' => 'Dedicated Engineering Studio'
                    ],
                    'cta_link' => '/pricing'
                ]
            ]
        ];

        $homePage->plugins = $plugins;
        $homePage->save();

        // 2. Dedicated Pricing Page (/pricing)
        $pricingPage = CmsPage::firstOrNew(['slug' => 'pricing']);
        $pricingPage->title = [
            'en' => 'Software Architecture & Development Pricing // Neriah Pro',
            'id' => 'Paket & Biaya Layanan Arsitektur Software // Neriah Pro'
        ];
        $pricingPage->meta_description = [
            'en' => 'Transparent pricing for digital retail licenses (Rp 99k - Rp 1.49M) and high-scale digital architecture: Standalone Advisory (Rp 2.5M), UMKM Stimulus (Rp 7.5M), and Full Rapid Monolith MVP (Rp 50M).',
            'id' => 'Biaya investasi transparan lisensi digital retail (Rp 99rb - Rp 1,49jt) dan arsitektur software berskala tinggi: Advisory (Rp 2.5 Juta), UMKM (Rp 7.5 Juta), dan Full MVP (Rp 50 Juta).'
        ];
        $pricingPage->is_published = true;
        $pricingPage->plugins = [
            [
                'type' => 'architecture_pricing',
                'is_active' => true,
                'data' => [
                    'headline' => [
                        'id' => 'INVESTASI LAYANAN REKAYASA SISTEM // NERIAH PRO',
                        'en' => 'SOFTWARE ARCHITECTURE & STUDIO PRICING'
                    ],
                    'subheadline' => [
                        'id' => 'Skema investasi transparan untuk founder & developer: dari cetak biru mandiri (Self-Service) hingga koding penuh turnkey Studio Monolith MVP.',
                        'en' => 'Standardized engineering investment for founders: From instant self-service blueprints to full turnkey Monolith MVP contracts.'
                    ],
                ]
            ],
            [
                'type' => 'cv_pricing_table',
                'is_active' => false,
                'data' => [
                    'headline' => [
                        'id' => 'INVESTASI KARIR IMPIAN // CV PRO STUDIO (SEGERA HADIR)',
                        'en' => 'CAREER ACCELERATION // CV PRO STUDIO (UPCOMING)'
                    ],
                    'subheadline' => [
                        'id' => 'Modul studio resume visual & portofolio klien sedang dalam pengembangan aktif untuk rilis Q4.',
                        'en' => 'Visual resume & portfolio studio module is currently under active development for Q4 release.'
                    ],
                ]
            ]
        ];
        $pricingPage->save();
        
        $this->command->info('Landing Page & Pricing Page seeded successfully with Dual-Track Retail & Project OS data!');
    }
}
