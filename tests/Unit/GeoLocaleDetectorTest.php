<?php

namespace Tests\Unit;

use App\Models\CmsGlobalSetting;
use App\Services\GeoLocaleDetector;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class GeoLocaleDetectorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CmsSeeder::class);
    }

    public function test_detects_indonesia_as_tier_1_id(): void
    {
        $request = Request::create('/', 'GET');
        $request->headers->set('CF-IPCountry', 'ID');

        $result = GeoLocaleDetector::detect($request);

        $this->assertEquals('ID', $result['country']);
        $this->assertEquals('id', $result['locale']);
        $this->assertEquals(1, $result['tier']);
        $this->assertNull($result['tier2_lang']);
        $this->assertTrue($result['is_indonesia']);
    }

    public function test_detects_japan_as_tier_2_ja(): void
    {
        $request = Request::create('/', 'GET');
        $request->headers->set('CF-IPCountry', 'JP');

        $result = GeoLocaleDetector::detect($request);

        $this->assertEquals('JP', $result['country']);
        $this->assertEquals(2, $result['tier']);
        $this->assertEquals('ja', $result['tier2_lang']);
        $this->assertFalse($result['is_indonesia']);
    }

    public function test_detects_germany_as_tier_2_de(): void
    {
        $request = Request::create('/', 'GET');
        $request->headers->set('X-Country-Code', 'DE');

        $result = GeoLocaleDetector::detect($request);

        $this->assertEquals('DE', $result['country']);
        $this->assertEquals(2, $result['tier']);
        $this->assertEquals('de', $result['tier2_lang']);
        $this->assertFalse($result['is_indonesia']);
    }

    public function test_detects_france_as_tier_2_fr(): void
    {
        $request = Request::create('/', 'GET');
        $request->headers->set('CloudFront-Viewer-Country', 'FR');

        $result = GeoLocaleDetector::detect($request);

        $this->assertEquals('FR', $result['country']);
        $this->assertEquals(2, $result['tier']);
        $this->assertEquals('fr', $result['tier2_lang']);
        $this->assertFalse($result['is_indonesia']);
    }

    public function test_detects_saudi_arabia_as_tier_2_ar(): void
    {
        $request = Request::create('/', 'GET');
        $request->headers->set('CF-IPCountry', 'SA');

        $result = GeoLocaleDetector::detect($request);

        $this->assertEquals('SA', $result['country']);
        $this->assertEquals(2, $result['tier']);
        $this->assertEquals('ar', $result['tier2_lang']);
        $this->assertFalse($result['is_indonesia']);
    }

    public function test_detects_usa_as_tier_1_en(): void
    {
        $request = Request::create('/', 'GET');
        $request->headers->set('CF-IPCountry', 'US');

        $result = GeoLocaleDetector::detect($request);

        $this->assertEquals('US', $result['country']);
        $this->assertEquals('en', $result['locale']);
        $this->assertEquals(1, $result['tier']);
        $this->assertNull($result['tier2_lang']);
        $this->assertFalse($result['is_indonesia']);
    }

    public function test_detects_singapore_as_tier_1_en(): void
    {
        $request = Request::create('/', 'GET');
        $request->headers->set('CF-IPCountry', 'SG');

        $result = GeoLocaleDetector::detect($request);

        $this->assertEquals('SG', $result['country']);
        $this->assertEquals('en', $result['locale']);
        $this->assertEquals(1, $result['tier']);
        $this->assertNull($result['tier2_lang']);
        $this->assertFalse($result['is_indonesia']);
    }

    public function test_falls_back_to_en_if_tier_2_not_in_whitelist(): void
    {
        // Admin only allows Japanese and Indonesian in Tier 2
        CmsGlobalSetting::setVal('google_translate_allowed_languages', ['id', 'ja']);

        // German visitor arrives
        $request = Request::create('/', 'GET');
        $request->headers->set('CF-IPCountry', 'DE');

        $result = GeoLocaleDetector::detect($request);

        // Since German ('de') is not in admin whitelist, auto-switch to English
        $this->assertEquals('DE', $result['country']);
        $this->assertEquals('en', $result['locale']);
        $this->assertEquals(1, $result['tier']);
        $this->assertNull($result['tier2_lang']);
    }

    public function test_accept_language_detection_fallback(): void
    {
        $request = Request::create('/', 'GET');
        $request->headers->set('Accept-Language', 'ja-JP,ja;q=0.9,en-US;q=0.8');

        $result = GeoLocaleDetector::detect($request);

        $this->assertEquals('JP', $result['country']);
        $this->assertEquals(2, $result['tier']);
        $this->assertEquals('ja', $result['tier2_lang']);
    }
}
