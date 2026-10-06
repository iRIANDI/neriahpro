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
}
