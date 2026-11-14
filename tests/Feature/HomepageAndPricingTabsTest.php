<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\CmsSeeder;
use Database\Seeders\LandingPageSeeder;
use Database\Seeders\BlueprintVoucherSeeder;

class HomepageAndPricingTabsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CmsSeeder::class);
        $this->seed(LandingPageSeeder::class);
        $this->seed(BlueprintVoucherSeeder::class);
    }

    public function test_homepage_renders_hero_and_excludes_redundant_pricing_island()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Verify HeroIsland is mounted
        $response->assertSee('HeroIsland');
        // Verify ArchitecturePricingIsland is NOT mounted on homepage
        $response->assertDontSee('ArchitecturePricingIsland');
    }

    public function test_pricing_page_renders_pricing_island_with_all_structured_data()
    {
        $response = $this->get('/pricing');

        $response->assertStatus(200);
        // Verify ArchitecturePricingIsland is mounted
        $response->assertSee('ArchitecturePricingIsland');
        $response->assertSee('"pricing_show_cv_tab":true', false);
        // Verify Schema.org JSON-LD exists
        $response->assertSee('OfferCatalog', false);
        $response->assertSee('FAQPage', false);
    }

    public function test_navigation_cleaner_removes_legacy_standalone_project_os_and_duplicate_services_hub(): void
    {
        \App\Models\CmsGlobalSetting::updateOrCreate(
            ['key' => 'navigation_format'],
            [
                'value' => [
                    [
                        'type' => 'simple_nav',
                        'data' => [
                            'links' => [
                                ['label' => 'Home', 'url' => '/'],
                                ['label' => 'Project OS (PRD)', 'url' => '/pricing#retail'],
                                ['label' => 'Pricing', 'url' => '/pricing'],
                                ['label' => 'Services Hub', 'url' => '/#services'],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $migration = require database_path('migrations/2026_11_15_010000_clean_redundant_global_navigation_links.php');
        $migration->up();

        $updated = \App\Models\CmsGlobalSetting::where('key', 'navigation_format')->first();
        $links = $updated->value[0]['data']['links'] ?? [];

        $this->assertCount(2, $links);
        $this->assertSame(['Home', 'Pricing'], array_column($links, 'label'));
    }

    public function test_cv_pro_studio_docks_without_redundant_pt14_spacer(): void
    {
        \App\Models\CmsGlobalSetting::setVal('midtrans_compliance_strict_mode', false);
        \App\Models\CmsGlobalSetting::setVal('feature_enable_cv_pro', true);
        \App\Models\CmsGlobalSetting::purgeRuntimeCache();

        $pricingCvResponse = $this->get('/pricing?tab=cv-pro');
        $pricingCvResponse->assertStatus(200);
        $pricingCvResponse->assertSee('"pricing_show_cv_tab":true', false);
        $pricingCvResponse->assertSee('"midtrans_mode":false', false);

        $response = $this->get('/cv-pro');

        $response->assertStatus(200);
        $response->assertSee('CvProStudioIsland');
        $response->assertSee('<main class="flex-1">', false);
        $response->assertDontSee('class="flex-1 pt-14"', false);
    }
}
