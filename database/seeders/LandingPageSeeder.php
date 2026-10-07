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
            'en' => 'Transparent pricing for high-scale digital architecture: Standalone Advisory PRD Blueprint (Rp 2.5M), Full Rapid Monolith MVP (Rp 50M - 50% DP), and UMKM Stimulus Subsidies.',
            'id' => 'Biaya investasi transparan arsitektur software berskala tinggi: Jasa Advisory Blueprint PRD (Rp 2.5 Juta), Full MVP Rapid Monolith (Rp 50 Juta - DP 50%), dan Program Subsidi UMKM.'
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
        
        $this->command->info('Landing Page & Pricing Page seeded successfully with 4 Pillars & CV Pro Pricing Hub data!');
    }
}
