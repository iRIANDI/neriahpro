<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\CmsPage;
use App\Models\CmsGlobalSetting;
use App\Models\LeadContact;
use App\Models\BlueprintVoucher;
use Database\Seeders\BlueprintVoucherSeeder;
use Database\Seeders\LandingPageSeeder;
use Database\Seeders\CmsSeeder;

class PricingPageAndLeadInquiryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CmsSeeder::class);
        $this->seed(LandingPageSeeder::class);
        $this->seed(BlueprintVoucherSeeder::class);
    }

    public function test_pricing_page_is_accessible_and_renders_successfully()
    {
        $response = $this->get('/pricing');

        $response->assertStatus(200);
        $response->assertSee('Software Architecture');
        $response->assertSee('Schema.org', false);
        $response->assertSee('OfferCatalog', false);
        $response->assertSee('FAQPage', false);
    }

    public function test_pricing_inquiry_creates_lead_contact_and_returns_whatsapp_url()
    {
        $payload = [
            'name' => 'Budi Santoso',
            'company' => 'PT Maju Inovasi',
            'email' => 'budi@perusahaan.com',
            'country_code' => '+62',
            'phone' => '81234567890',
            'package_tier' => 'blueprint_advisory',
            'voucher_code' => 'UMKM-SUBSIDI-50',
            'notes' => 'Kami butuh PRD dan skema database PostgreSQL Strict ULID untuk sistem logistik kami.',
        ];

        $response = $this->postJson('/api/pricing/inquiry', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'message',
            'lead_id',
            'whatsapp_url'
        ]);

        $this->assertDatabaseHas('lead_contacts', [
            'name' => 'Budi Santoso',
            'company_name' => 'PT Maju Inovasi',
            'email' => 'budi@perusahaan.com',
            'status' => 'lead',
        ]);
    }

    public function test_pricing_inquiry_validates_required_fields()
    {
        $response = $this->postJson('/api/pricing/inquiry', [
            'company' => 'Anonymous Corp',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'phone', 'package_tier']);
    }

    public function test_pricing_inquiry_detects_bot_honeypot()
    {
        $payload = [
            'name' => 'Spam Bot',
            'email' => 'bot@spammer.com',
            'phone' => '12345678',
            'package_tier' => 'full_mvp',
            'honeypot' => 'malicious_bot_token',
        ];

        $response = $this->postJson('/api/pricing/inquiry', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Anti-bot security validation triggered.',
        ]);
    }

    public function test_vouchers_are_available_for_promotions()
    {
        $this->assertDatabaseHas('blueprint_vouchers', [
            'code' => 'UMKM-SUBSIDI-50',
            'discount_type' => 'percent',
            'discount_value' => 50.00,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('blueprint_vouchers', [
            'code' => 'CORP-INNOVATION-15M',
            'discount_type' => 'fixed',
            'discount_value' => 15000000.00,
            'is_active' => true,
        ]);
    }

    public function test_full_mvp_inquiry_reserves_sprint_batch_and_does_not_trigger_premature_snap_token()
    {
        $payload = [
            'name' => 'Hendro Wijaya',
            'company' => 'PT Distribusi Logistik Nusantara',
            'email' => 'hendro@logistik.id',
            'country_code' => '+62',
            'phone' => '81234567899',
            'package_tier' => 'full_mvp',
            'sprint_batch' => 'Batch 1 (15 Okt - 25 Nov 2026)',
            'kickoff_slot' => 'Sesi Pagi (09:00 - 11:30 WIB)',
            'notes' => 'Sistem monolit ERP logistik skala 50.000 order harian.',
        ];

        $response = $this->postJson('/api/pricing/inquiry', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_paid_package' => false,
            'snap_token' => null,
            'package_tier' => 'full_mvp',
        ]);
        $response->assertJsonStructure([
            'success',
            'message',
            'lead_id',
            'whatsapp_url',
            'is_paid_package',
        ]);

        $this->assertNotNull($response->json('whatsapp_url'));
        $this->assertStringContainsString('Yoseph', $response->json('whatsapp_url'));

        $this->assertDatabaseHas('lead_contacts', [
            'name' => 'Hendro Wijaya',
            'email' => 'hendro@logistik.id',
            'status' => 'lead',
        ]);
    }
}
