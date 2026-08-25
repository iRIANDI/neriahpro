<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsPage;
use Illuminate\Support\Str;

class LandingPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $homePage = CmsPage::firstOrNew(['slug' => 'home']);
        
        $homePage->title = ['en' => 'Neriah Pro - Default Home', 'id' => 'Neriah Pro - Beranda Default'];
        $homePage->meta_description = ['en' => 'Neriah Pro Default Landing Page', 'id' => 'Halaman Landing Default Neriah Pro'];
        $homePage->is_published = true;
        
        $plugins = [
            [
                'type' => 'hero_section',
                'is_active' => true,
                'data' => [
                    'title' => 'Welcome to Neriah Pro',
                    'subtitle' => 'The ultimate platform for your needs',
                    'cta_text' => 'Get Started',
                    'cta_link' => '#pricing'
                ]
            ],
            [
                'type' => 'feature_grid',
                'is_active' => true,
                'data' => [
                    'features' => [
                        ['title' => 'Fast', 'description' => 'Lightning fast performance.'],
                        ['title' => 'Secure', 'description' => 'Top tier security.'],
                    ]
                ]
            ],
            [
                'type' => 'onboarding_form',
                'is_active' => true,
                'data' => [
                    'title' => 'Join Us Today',
                    'description' => 'Fill out the form below to get started.'
                ]
            ]
        ];

        $homePage->plugins = $plugins;
        $homePage->save();
        
        $this->command->info('Landing Page seeded successfully!');
    }
}
