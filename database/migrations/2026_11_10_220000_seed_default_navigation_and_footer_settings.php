<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\CmsGlobalSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $defaultNavigation = [
            [
                'type' => 'simple_nav',
                'data' => [
                    'links' => [
                        ['label' => 'Home', 'url' => '/'],
                        ['label' => 'Project OS (PRD)', 'url' => '/pricing#retail'],
                        ['label' => 'Pricing', 'url' => '/pricing'],
                        ['label' => 'Services Hub', 'url' => '/#services'],
                    ]
                ]
            ]
        ];

        $defaultFooter = [
            [
                'type' => 'multi_column_footer',
                'data' => [
                    'copyright_text' => '2026 NERIAH PRO by Yoseph Iriandi. Professional Independent Developer Services.',
                    'columns' => [
                        [
                            'title' => 'Solutions',
                            'links' => [
                                ['label' => 'Project OS (PRD)', 'url' => '/blueprint'],
                                ['label' => 'CV Pro Studio', 'url' => '/cv-pro'],
                            ]
                        ],
                        [
                            'title' => 'Legal & Policy',
                            'links' => [
                                ['label' => 'Terms of Service', 'url' => '/terms'],
                                ['label' => 'Privacy Policy', 'url' => '/privacy-policy'],
                                ['label' => 'Refund & Warranty Policy', 'url' => '/refund-policy'],
                                ['label' => 'Smart Client Guide', 'url' => '/smart-guide'],
                            ]
                        ],
                        [
                            'title' => 'Developer Skills',
                            'links' => [
                                ['label' => 'Full-Stack Web Systems', 'url' => '/explore'],
                                ['label' => 'PostgreSQL & Strict ULID', 'url' => '/pricing#retail'],
                                ['label' => 'Midtrans API & Webhooks', 'url' => '/terms'],
                                ['label' => 'Clean Code & Linux VPS', 'url' => '/smart-guide'],
                            ]
                        ],
                    ]
                ]
            ]
        ];

        $currentNav = CmsGlobalSetting::where('key', 'navigation_format')->first();
        if (!$currentNav || empty($currentNav->value) || isset($currentNav->value['enabled'])) {
            CmsGlobalSetting::updateOrCreate(
                ['key' => 'navigation_format'],
                ['value' => $defaultNavigation]
            );
            CmsGlobalSetting::updateOrCreate(
                ['key' => 'main_navigation'],
                ['value' => $defaultNavigation]
            );
        }

        $currentFooter = CmsGlobalSetting::where('key', 'footer_format')->first();
        if (!$currentFooter || empty($currentFooter->value) || isset($currentFooter->value['enabled'])) {
            CmsGlobalSetting::updateOrCreate(
                ['key' => 'footer_format'],
                ['value' => $defaultFooter]
            );
            CmsGlobalSetting::updateOrCreate(
                ['key' => 'footer_links'],
                ['value' => $defaultFooter]
            );
        }

        \Illuminate\Support\Facades\Cache::forget('cms_global_settings');
        \Illuminate\Support\Facades\Cache::forget('cms_global_settings_data');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive rollback
    }
};

