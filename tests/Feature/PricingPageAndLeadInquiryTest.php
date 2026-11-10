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

    public function test_impact_nonprofit_grant_application_is_pending_review_and_triggers_fonnte_whatsapp_alert()
    {
        \Illuminate\Support\Facades\Http::fake([
            'https://api.fonnte.com/send' => \Illuminate\Support\Facades\Http::response([
                'status' => true,
                'target' => '6281380448253',
                'id' => 'FONNTE-MSG-001',
            ], 200),
        ]);

        CmsGlobalSetting::updateOrCreate(
            ['key' => 'fonnte_api_token'],
            ['value' => 'TEST_FONNTE_TOKEN_123', 'group' => 'notification', 'type' => 'text']
        );
        CmsGlobalSetting::updateOrCreate(
            ['key' => 'admin_notification_whatsapp'],
            ['value' => '081380448253', 'group' => 'notification', 'type' => 'text']
        );
        CmsGlobalSetting::updateOrCreate(
            ['key' => 'enable_admin_whatsapp_notifications'],
            ['value' => true, 'group' => 'notification', 'type' => 'boolean']
        );

        $fakePdf = \Illuminate\Http\UploadedFile::fake()->create('akta_pendirian_yayasan.pdf', 300, 'application/pdf');

        $payload = [
            'name' => 'Ustadz Ahmad Fauzi',
            'company' => 'Yayasan Rumah Yatim Dhuafa Mandiri',
            'email' => 'ahmad@dhuafamandiri.org',
            'country_code' => '+62',
            'phone' => '81380448253',
            'package_tier' => 'impact_nonprofit_grant',
            'organization_type' => 'NONPROFIT_YAYASAN',
            'legal_decree_number' => 'AHU-0012345.AH.01.04.Tahun 2021',
            'notes' => 'Aplikasi transparansi donasi dan inventaris asrama yatim piatu berbasis PSAK 109.',
            'legal_document' => $fakePdf,
        ];

        $response = $this->postJson('/api/pricing/inquiry', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_paid_package' => false,
            'package_tier' => 'impact_nonprofit_grant',
        ]);

        // Verify that the grant is NOT instantly active, but PENDING_REVIEW
        $this->assertDatabaseHas('pos_impact_grant_applications', [
            'applicant_name' => 'Ustadz Ahmad Fauzi',
            'organization_name' => 'Yayasan Rumah Yatim Dhuafa Mandiri',
            'legal_decree_number' => 'AHU-0012345.AH.01.04.Tahun 2021',
            'status' => 'PENDING_REVIEW',
            'requested_project_budget_idr' => 0,
        ]);

        // Verify document was registered into legal vault
        $this->assertDatabaseHas('documents', [
            'document_type' => 'legal_proof',
            'scope_locked' => true,
        ]);

        // Verify Fonnte WhatsApp API was called targeting 6281380448253
        \Illuminate\Support\Facades\Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
            return $request->url() === 'https://api.fonnte.com/send'
                && $request['target'] === '6281380448253'
                && str_contains($request['message'], 'PENGAJUAN HIBAH CSR / IMPACT GRANT')
                && str_contains($request['message'], 'Yayasan Rumah Yatim Dhuafa Mandiri')
                && str_contains($request['message'], 'AHU-0012345.AH.01.04.Tahun 2021')
                && str_contains($request['message'], 'PENDING REVIEW');
        });
    }

    public function test_impact_grant_can_be_approved_by_admin_issuing_100_percent_voucher()
    {
        $superAdmin = \App\Models\User::factory()->create([
            'email' => 'admin.grant@neriahpro.com',
        ]);
        $superAdminRole = \App\Models\Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->assignRole($superAdminRole);

        $grantApp = \App\Models\PosImpactGrantApplication::create([
            'applicant_name' => 'Dr. Bambang Riset',
            'organization_name' => 'Pusat Studi Biomedika Nusantara',
            'organization_type' => 'ACADEMIC_CAMPUS',
            'official_email' => 'bambang@biomedika.ac.id',
            'phone_country_code' => '+62',
            'phone_number' => '81298765432',
            'legal_decree_number' => 'SK-REKTOR-2025-089',
            'requested_project_budget_idr' => 0,
            'mission_statement' => ['id' => 'Riset open-access genomika'],
            'status' => 'PENDING_REVIEW',
        ]);

        $result = \App\Services\Saas\PosSaasCommercialEngineService::approveImpactGrantOneClick(
            $grantApp,
            $superAdmin,
            'Validasi SK Rektor Lolos Verifikasi'
        );

        $this->assertNotEmpty($result['voucher_code']);
        $this->assertStringStartsWith('GRANT-', $result['voucher_code']);

        $grantApp->refresh();
        $this->assertEquals('APPROVED_IMPACT_GRANT', $grantApp->status);
        $this->assertEquals($result['voucher_code'], $grantApp->granted_voucher_code);

        $this->assertDatabaseHas('blueprint_vouchers', [
            'code' => $result['voucher_code'],
            'discount_type' => 'free',
            'discount_value' => 100,
            'is_active' => true,
        ]);
    }
}
