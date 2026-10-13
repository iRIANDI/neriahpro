<?php

namespace Tests\Feature;

use App\Models\CmsGlobalSetting;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetAppLocaleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CmsSeeder::class);
    }

    public function test_us_visitor_auto_switches_to_english(): void
    {
        $response = $this->withHeaders([
            'CF-IPCountry' => 'US',
        ])->get('/');

        $response->assertSuccessful();
        $this->assertEquals('en', app()->getLocale());
        $response->assertCookie('neriah_locale', 'en');
        $response->assertCookie('neriah_auto_detected', '1');
    }

    public function test_japan_visitor_auto_selects_tier_2_ja(): void
    {
        $response = $this->withHeaders([
            'CF-IPCountry' => 'JP',
        ])->get('/');

        $response->assertSuccessful();
        $this->assertEquals('id', app()->getLocale());
        $response->assertCookie('googtrans', '/id/ja');
        $response->assertCookie('neriah_auto_detected', '1');
    }

    public function test_indonesia_visitor_stays_in_indonesian(): void
    {
        $response = $this->withHeaders([
            'CF-IPCountry' => 'ID',
        ])->get('/');

        $response->assertSuccessful();
        $this->assertEquals('id', app()->getLocale());
        $response->assertCookie('neriah_locale', 'id');
        $response->assertCookie('neriah_auto_detected', '1');
    }

    public function test_manual_preference_cookie_is_strictly_respected(): void
    {
        // Visitor is from US, but previously clicked "ID" manually
        $response = $this->withHeaders([
            'CF-IPCountry' => 'US',
        ])->withCookies([
            'neriah_locale_manual' => '1',
            'neriah_locale' => 'id',
        ])->get('/');

        $response->assertSuccessful();
        $this->assertEquals('id', app()->getLocale());
    }

    public function test_explicit_query_parameter_sets_manual_locale(): void
    {
        $response = $this->get('/?lang=en');

        $response->assertSuccessful();
        $this->assertEquals('en', app()->getLocale());
        $response->assertCookie('neriah_locale', 'en');
        $response->assertCookie('neriah_locale_manual', '1');
    }
}
