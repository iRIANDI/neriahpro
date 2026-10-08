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
        // Verify Schema.org JSON-LD exists
        $response->assertSee('OfferCatalog', false);
        $response->assertSee('FAQPage', false);
    }
}
