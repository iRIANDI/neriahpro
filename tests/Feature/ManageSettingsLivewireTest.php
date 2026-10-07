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
            ->set('data.company_whatsapp', '628123456789')
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
}
