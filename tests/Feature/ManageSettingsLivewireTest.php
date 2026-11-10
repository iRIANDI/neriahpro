<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CmsGlobalSetting;
use App\Filament\Pages\ManageSettings;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageSettingsLivewireTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SuperAdminSeeder::class);
        $this->seed(\Database\Seeders\CmsSeeder::class);
    }

    public function test_can_load_manage_settings_page(): void
    {
        $superadmin = User::where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($superadmin);

        Livewire::test(ManageSettings::class)
            ->assertSuccessful();
    }

    public function test_can_save_multi_language_settings(): void
    {
        $superadmin = User::where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($superadmin);

        Livewire::test(ManageSettings::class)
            ->set('data.company_whatsapp', '6281380448253')
            ->set('data.app_timezone', 'Asia/Jakarta')
            ->set('data.developer_entity_name', 'Neriah Pro Studio')
            ->set('data.developer_support_email', 'support@neriahpro.com')
            ->set('data.developer_pic_name', 'Yoseph Iriandi')
            ->set('data.developer_pic_title', 'Lead Architect')
            ->set('data.developer_seal_text', 'NERIAH PRO DIGITAL SEAL')
            ->set('data.midtrans_terms_checkbox_label_id', 'Saya menyetujui syarat & ketentuan')
            ->set('data.midtrans_terms_checkbox_label_en', 'I agree to the terms and conditions')
            ->set('data.ai_default_provider', 'relayrouter')
            ->set('data.seo_schema.organization.type', 'Organization')
            ->set('data.seo_schema.organization.name', 'Neriah Pro')
            ->set('data.default_frontend_locale', 'en')
            ->set('data.google_translate_enabled', true)
            ->set('data.google_translate_allowed_languages', ['en', 'id', 'ja'])
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSuccessful();

        $this->assertEquals('en', CmsGlobalSetting::getVal('default_frontend_locale'));
        $this->assertEquals(['en', 'id', 'ja'], CmsGlobalSetting::getVal('google_translate_allowed_languages'));
    }

    public function test_saving_without_manually_filling_all_tabs(): void
    {
        $superadmin = User::where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($superadmin);

        Livewire::test(ManageSettings::class)
            ->set('data.default_frontend_locale', 'en')
            ->call('submit')
            ->assertHasNoErrors();
    }

    public function test_save_action_alias_and_locale_middleware_fallback(): void
    {
        $superadmin = User::where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($superadmin);

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'default_frontend_locale'],
            ['value' => 'en']
        );

        $request = \Illuminate\Http\Request::create('/');
        $middleware = new \App\Http\Middleware\SetAppLocale();
        $middleware->handle($request, function ($req) {
            return new \Symfony\Component\HttpFoundation\Response();
        });

        $this->assertEquals('en', app()->getLocale());
    }

    public function test_developer_signature_modal_action_applies_and_saves(): void
    {
        $superadmin = User::where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($superadmin);

        $fakeBase64Sig = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $page = new ManageSettings();
        $page->mount();
        $action = $page->getDeveloperSignaturePadAction();
        $action->livewire($page);
        $action->call(['data' => ['modal_canvas_signature' => $fakeBase64Sig]]);

        $savedPath = CmsGlobalSetting::getVal('developer_signature_image');
        $this->assertNotEmpty($savedPath);
        $this->assertStringStartsWith('signatures/developer_sig_', $savedPath);
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('public')->exists($savedPath));

        Livewire::test(ManageSettings::class)
            ->call('submit')
            ->assertHasNoErrors();
    }

    public function test_country_zone_whatsapp_and_phone_save_e164(): void
    {
        $superadmin = User::where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($superadmin);

        Livewire::test(ManageSettings::class)
            ->set('data.company_whatsapp_country', 'SG')
            ->set('data.company_whatsapp_number', '91234567')
            ->set('data.developer_phone_country', 'ID')
            ->set('data.developer_phone_number', '81380448253')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertEquals('6591234567', CmsGlobalSetting::getVal('company_whatsapp'));
        $this->assertEquals('+6281380448253', CmsGlobalSetting::getVal('developer_phone'));
    }

    public function test_navigation_and_footer_format_mount_with_defaults_and_save_successfully(): void
    {
        $superadmin = User::where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($superadmin);

        $test = Livewire::test(ManageSettings::class);
        $test->assertSuccessful();

        $navFormat = $test->get('data.navigation_format');
        $footerFormat = $test->get('data.footer_format');

        $this->assertIsArray($navFormat);
        $this->assertNotEmpty($navFormat);
        $navBlocks = array_values($navFormat);
        $this->assertEquals('simple_nav', $navBlocks[0]['type']);

        $this->assertIsArray($footerFormat);
        $this->assertNotEmpty($footerFormat);
        $footerBlocks = array_values($footerFormat);
        $this->assertEquals('multi_column_footer', $footerBlocks[0]['type']);

        // Custom update
        $customNav = [
            'block-1' => [
                'type' => 'simple_nav',
                'data' => [
                    'links' => [
                        'item-1' => ['label' => 'Beranda Utama', 'url' => '/'],
                        'item-2' => ['label' => 'Katalog Harga', 'url' => '/pricing'],
                    ]
                ]
            ]
        ];

        $test->set('data.navigation_format', $customNav)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSuccessful();

        $savedNav = CmsGlobalSetting::getVal('navigation_format');
        $this->assertNotEmpty($savedNav);
        $savedBlocks = array_values($savedNav);
        $this->assertEquals('simple_nav', $savedBlocks[0]['type']);
        $this->assertEquals($savedNav, CmsGlobalSetting::getVal('main_navigation'));
    }
}
