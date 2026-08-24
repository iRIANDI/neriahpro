<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CmsPage;
use App\Models\CmsPlugin;
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

        // Home Page
        $homePage = CmsPage::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => json_encode(['en' => 'Home - Neriah Pro', 'id' => 'Beranda - Neriah Pro']),
                'meta_description' => json_encode(['en' => 'Welcome to Neriah Pro', 'id' => 'Selamat datang di Neriah Pro']),
                'is_published' => true,
            ]
        );

        // Plugins for Home Page
        CmsPlugin::updateOrCreate(
            ['cms_page_id' => $homePage->id, 'plugin_type' => 'hero_section'],
            [
                'order' => 1,
                'is_active' => true,
                'content_data' => [
                    'headline' => 'Build The Future.',
                    'subheadline' => 'World-class enterprise OS and CMS built for scale.',
                    'cta_text' => 'Initialize Now',
                    'cta_link' => '#onboarding'
                ],
            ]
        );

        CmsPlugin::updateOrCreate(
            ['cms_page_id' => $homePage->id, 'plugin_type' => 'feature_grid'],
            [
                'order' => 2,
                'is_active' => true,
                'content_data' => [
                    'title' => 'Our Arsenal.',
                ],
            ]
        );

        CmsPlugin::updateOrCreate(
            ['cms_page_id' => $homePage->id, 'plugin_type' => 'onboarding_form'],
            [
                'order' => 3,
                'is_active' => true,
                'content_data' => [],
            ]
        );
    }
}
