<?php

namespace Tests\Feature;

use App\Models\CmsGlobalSetting;
use App\Models\CmsPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CmsCacheIncompleteClassResilienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_cached_returns_models_and_caches_primitives_only(): void
    {
        CmsGlobalSetting::updateOrCreate(
            ['key' => 'feature_enable_cv_pro'],
            ['value' => true]
        );

        $settings = CmsGlobalSetting::getAllCached();
        $this->assertTrue($settings->has('feature_enable_cv_pro'));
        $this->assertInstanceOf(CmsGlobalSetting::class, $settings->get('feature_enable_cv_pro'));
        $this->assertTrue((bool) $settings->get('feature_enable_cv_pro')->value);

        // Verify the cached data is a primitive array, not raw Eloquent models
        $cachedData = Cache::get('cms_global_settings_data');
        $this->assertIsArray($cachedData);
        $this->assertArrayHasKey('feature_enable_cv_pro', $cachedData);
        $this->assertIsArray($cachedData['feature_enable_cv_pro']);
    }

    public function test_application_self_heals_when_cache_contains_php_incomplete_class(): void
    {
        // Simulate a corrupted cache containing __PHP_Incomplete_Class
        $incompleteClass = unserialize('O:34:"FakeLegacyCorruptedClassForTesting":0:{}');
        $this->assertInstanceOf(\__PHP_Incomplete_Class::class, $incompleteClass);

        Cache::forever('cms_global_settings', $incompleteClass);
        Cache::forever('cms_global_settings_data', $incompleteClass);
        Cache::forever('cms_page_data_home', $incompleteClass);

        // Seed basic page
        CmsPage::create([
            'slug' => 'home',
            'title' => ['en' => 'Test Home', 'id' => 'Beranda Tes'],
            'meta_description' => ['en' => 'Test Meta', 'id' => 'Meta Tes'],
            'is_published' => true,
            'plugins' => [],
        ]);

        CmsGlobalSetting::create([
            'key' => 'feature_enable_cv_pro',
            'value' => true,
        ]);

        // Attempting to visit / should NOT crash with:
        // "Cannot use object of type __PHP_Incomplete_Class as array"
        $response = $this->get('/');

        $response->assertStatus(200);

        // Verify that the corrupt cache was flushed and healed
        $healed = Cache::get('cms_global_settings_data');
        $this->assertNotInstanceOf(\__PHP_Incomplete_Class::class, $healed);
        $this->assertIsArray($healed);
    }

    public function test_view_guards_safely_handle_incomplete_class_in_global_settings(): void
    {
        $incompleteClass = unserialize('O:32:"AnotherFakeLegacyIncompleteClass":0:{}');

        // Test CmsGlobalSetting::getAllCached() auto-purges incomplete class
        Cache::forever('cms_global_settings', $incompleteClass);
        Cache::forever('cms_global_settings_data', $incompleteClass);

        $recovered = CmsGlobalSetting::getAllCached();
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $recovered);
    }
}
