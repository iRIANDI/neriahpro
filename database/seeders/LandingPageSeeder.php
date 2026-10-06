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
                        'id' => 'INVESTASI TRANSPARAN & TEPAT SASARAN',
                        'en' => 'TRANSPARENT VALUE-BASED PRICING'
                    ],
                    'subheadline' => [
                        'id' => 'Dua skenario solusi rekayasa perangkat lunak berskala tinggi: Mulai dari blueprint teknis siap eksekusi hingga pengembangan penuh sistem monolit modern tanpa drama pembengkakan biaya.',
                        'en' => 'Two distinct high-scale software engineering scenarios: From production-ready technical blueprints to full modern monolith development without cost overruns.'
                    ],
                ]
            ],
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
            ]
        ];
        $pricingPage->save();
        
        $this->command->info('Landing Page & Pricing Page seeded successfully with 4 Pillars & CV Pro Pricing Hub data!');
    }
}
