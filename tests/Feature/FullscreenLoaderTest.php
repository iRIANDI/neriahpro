<?php

namespace Tests\Feature;

use App\Models\CmsGlobalSetting;
use App\Support\BrandLogoHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FullscreenLoaderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        CmsGlobalSetting::purgeRuntimeCache();
    }

    public function test_brand_logo_helper_returns_default_when_no_setting(): void
    {
        $logo = BrandLogoHelper::getLogoUrl();
        $this->assertNotEmpty($logo);
        $this->assertStringContainsString('favicon.ico', $logo);
    }

    public function test_brand_logo_helper_returns_custom_site_logo_from_settings(): void
    {
        CmsGlobalSetting::updateOrCreate(
            ['key' => 'site_logo'],
            ['value' => 'branding/neriah-pro-logo.svg']
        );
        Cache::flush();

        $logo = BrandLogoHelper::getLogoUrl();
        $this->assertStringContainsString('branding/neriah-pro-logo.svg', $logo);
        $this->assertStringStartsWith('http', $logo);
    }

    public function test_brand_logo_helper_returns_external_url_when_set(): void
    {
        CmsGlobalSetting::updateOrCreate(
            ['key' => 'site_logo_url'],
            ['value' => 'https://cdn.neriahpro.com/assets/logo.png']
        );
        Cache::flush();

        $logo = BrandLogoHelper::getLogoUrl();
        $this->assertEquals('https://cdn.neriahpro.com/assets/logo.png', $logo);
    }

    public function test_brand_logo_helper_falls_back_to_seo_schema_logo(): void
    {
        CmsGlobalSetting::updateOrCreate(
            ['key' => 'seo_schema'],
            ['value' => [
                'organization' => [
                    'logo' => 'https://neriahpro.com/custom-seo-logo.png'
                ]
            ]]
        );
        Cache::flush();

        $logo = BrandLogoHelper::getLogoUrl();
        $this->assertEquals('https://neriahpro.com/custom-seo-logo.png', $logo);
    }

    public function test_fullscreen_loader_renders_project_os_context_correctly(): void
    {
        $html = Blade::render('<x-fullscreen-loader feature="project_os" />');

        $this->assertStringContainsString('id="neriah-fullscreen-loader"', $html);
        $this->assertStringContainsString('PROJECT OS // ARCHITECTURE HUB', $html);
        $this->assertStringContainsString('Menyiapkan Studio Project OS...', $html);
        $this->assertStringContainsString('Mengompilasi 26 parameter spesifikasi', $html);
        $this->assertStringContainsString('dismissNeriahLoader', $html);
        // Ensure no capsule / rounded-full violation
        $this->assertStringNotContainsString('rounded-full', $html);
    }

    public function test_fullscreen_loader_renders_cv_pro_context_correctly(): void
    {
        $html = Blade::render('<x-fullscreen-loader feature="cv_pro" />');

        $this->assertStringContainsString('id="neriah-fullscreen-loader"', $html);
        $this->assertStringContainsString('CV PRO', $html);
        $this->assertStringContainsString('AI RESUME', $html);
        $this->assertStringContainsString('Menyiapkan Studio CV Pro...', $html);
        $this->assertStringContainsString('MarkItDown', $html);
    }

    public function test_fullscreen_loader_renders_bilingual_tier1_in_english(): void
    {
        app()->setLocale('en');

        $html = Blade::render('<x-fullscreen-loader feature="project_os" />');

        $this->assertStringContainsString('Preparing Project OS Architecture Studio...', $html);
        $this->assertStringContainsString('Compiling 26 specification parameters', $html);

        app()->setLocale('id');
    }

    public function test_fullscreen_loader_renders_customer_portal_context(): void
    {
        $html = Blade::render('<x-fullscreen-loader feature="customer_portal" />');

        $this->assertStringContainsString('CLIENT PORTAL // SECURE DASHBOARD', $html);
        $this->assertStringContainsString('Menyiapkan Dashboard Klien...', $html);
    }

    public function test_fullscreen_loader_renders_checkout_context(): void
    {
        $html = Blade::render('<x-fullscreen-loader feature="checkout" />');

        $this->assertStringContainsString('COMMERCIAL // SPRINT CAPACITY', $html);
        $this->assertStringContainsString('Menyiapkan Ringkasan Pemesanan...', $html);
    }

    public function test_fullscreen_loader_does_not_leak_html_amp_entity_into_script(): void
    {
        app()->setLocale('en');

        $html = Blade::render('<x-fullscreen-loader feature="project_os" />');

        preg_match('/<script>(.*?)<\/script>/s', $html, $matches);
        $scriptContent = $matches[1] ?? '';

        $this->assertNotEmpty($scriptContent);
        // Ensure progress step string literals do not contain raw &amp; HTML entities
        $this->assertStringNotContainsString('"VERIFYING INTEGRITY &amp;', $scriptContent);
        $this->assertStringNotContainsString("'VERIFYING INTEGRITY &amp;", $scriptContent);
        $this->assertStringNotContainsString('&AMP;', $scriptContent);

        app()->setLocale('id');
    }
}
