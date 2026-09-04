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
        
        $this->command->info('Landing Page seeded successfully with 4 Pillars Hub data!');
    }
}
