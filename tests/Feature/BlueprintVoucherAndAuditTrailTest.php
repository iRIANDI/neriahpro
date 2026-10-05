<?php

namespace Tests\Feature;

use App\Models\BlueprintVoucher;
use App\Models\PaymentWebhookLog;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Policies\BlueprintVoucherPolicy;
use App\Filament\Resources\BlueprintVouchers\BlueprintVoucherResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlueprintVoucherAndAuditTrailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed initial vouchers
        $this->seed(\Database\Seeders\BlueprintVoucherSeeder::class);
    }

    public function test_voucher_validation_endpoint(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Budi Santoso',
            'nama_bisnis' => 'Klinik Sehat Bersama',
            'email' => 'budi@kliniksehat.id',
            'phone' => '08123456789',
            'masalah_utama' => 'Sistem rekam medis manual',
            'tujuan_utama' => 'Digitalisasi klinik',
            'is_published' => true,
        ]);

        // 1. Invalid voucher
        $response = $this->postJson(route('blueprint.voucher.validate', $blueprint->slug), [
            'code' => 'INVALID-CODE',
        ]);
        $response->assertStatus(422)
            ->assertJson(['valid' => false]);

        // 2. Valid 100% Free Bypass voucher
        $response = $this->postJson(route('blueprint.voucher.validate', $blueprint->slug), [
            'code' => 'PELAYANAN-KASIH',
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'code' => 'PELAYANAN-KASIH',
                'is_free_bypass' => true,
            ]);
    }

    public function test_claiming_free_voucher_requires_agreement_sign_off(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Pastor Yohanes',
            'nama_bisnis' => 'Aplikasi Kasih Komunitas',
            'email' => 'yohanes@gerejakasih.org',
            'phone' => '081299887766',
            'masalah_utama' => 'Pelayanan jemaat',
            'tujuan_utama' => 'Platform donasi dan doa',
            'is_published' => true,
        ]);

        // Claiming without agree_sign_off fails
        $response = $this->postJson(route('blueprint.voucher.claim', $blueprint->slug), [
            'code' => 'PELAYANAN-KASIH',
            'agree_sign_off' => false,
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['agree_sign_off']);

        // Claiming with agree_sign_off succeeds
        $response = $this->postJson(route('blueprint.voucher.claim', $blueprint->slug), [
            'code' => 'PELAYANAN-KASIH',
            'agree_sign_off' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $blueprint->refresh();

        // Verify Free Grant attributes
        $this->assertTrue($blueprint->is_free_grant);
        $this->assertEquals('PELAYANAN-KASIH', $blueprint->voucher_code);
        $this->assertEquals('In Development (Free Grant)', $blueprint->project_status);

        // Verify Cryptographic SHA-256 Sign-off
        $this->assertTrue($blueprint->signed_agreement);
        $this->assertNotEmpty($blueprint->document_sha256);
        $this->assertNotNull($blueprint->signed_at);
        $this->assertEquals(64, strlen($blueprint->document_sha256));

        // Verify Auto-provisioned Staging URL
        $this->assertNotEmpty($blueprint->staging_url);
        $this->assertStringContainsString('staging.neriahpro.com', $blueprint->staging_url);
        $this->assertNotNull($blueprint->staging_provisioned_at);

        // Verify Voucher usage incremented
        $voucher = BlueprintVoucher::where('code', 'PELAYANAN-KASIH')->first();
        $this->assertEquals(1, $voucher->used_count);
    }

    public function test_midtrans_webhook_dead_letter_queue_and_fulfillment(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Sari Handayani',
            'nama_bisnis' => 'Sari Logistics Hub',
            'email' => 'sari@sarilogistics.id',
            'phone' => '081344556677',
            'masalah_utama' => 'Pelacakan armada logistik',
            'tujuan_utama' => 'Dashboard real-time',
            'is_published' => true,
        ]);

        $orderId = 'NP-BP-' . strtoupper(substr($blueprint->id, 0, 8)) . '-' . time();

        $payload = [
            'order_id' => $orderId,
            'status_code' => '200',
            'gross_amount' => '25000000.00',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'payment_type' => 'bank_transfer',
        ];

        $response = $this->postJson(route('webhook.midtrans'), $payload);
        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        // Verify DLQ log created and marked processed
        $webhookLog = PaymentWebhookLog::where('order_id', $orderId)->first();
        $this->assertNotNull($webhookLog);
        $this->assertEquals('processed', $webhookLog->status);
        $this->assertEquals('midtrans', $webhookLog->gateway);
        $this->assertEquals('settlement', $webhookLog->event_type);
        $this->assertNotNull($webhookLog->processed_at);

        // Verify blueprint status updated and staging auto-provisioned
        $blueprint->refresh();
        $this->assertEquals('In Development (DP Paid)', $blueprint->project_status);
        $this->assertNotEmpty($blueprint->staging_url);
        $this->assertNotNull($blueprint->staging_provisioned_at);
        $this->assertTrue($blueprint->signed_agreement);
    }

    public function test_strict_rbac_voucher_management_only_for_yoseph(): void
    {
        // 1. Regular user
        $regularUser = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'staff@neriahpro.com',
        ]);

        // 2. Midtrans Reviewer user
        $reviewerUser = User::factory()->create([
            'name' => 'Midtrans Reviewer',
            'email' => 'reviewer.midtrans@neriahpro.com',
        ]);

        // 3. Super Admin Yoseph
        $yosephUser = User::factory()->create([
            'name' => 'Yoseph Iriandi Tambunan',
            'email' => 'yoseph.iriandi.tambunan@gmail.com',
        ]);

        $policy = new BlueprintVoucherPolicy();
        $voucher = BlueprintVoucher::where('code', 'PELAYANAN-KASIH')->first();

        // Regular user should be DENIED
        $this->assertFalse($policy->viewAny($regularUser));
        $this->assertFalse($policy->create($regularUser));
        $this->assertFalse($policy->update($regularUser, $voucher));
        $this->assertFalse($policy->delete($regularUser, $voucher));

        // Reviewer user should be DENIED
        $this->assertFalse($policy->viewAny($reviewerUser));
        $this->assertFalse($policy->create($reviewerUser));

        // Yoseph should be GRANTED FULL ACCESS
        $this->assertTrue($policy->viewAny($yosephUser));
        $this->assertTrue($policy->create($yosephUser));
        $this->assertTrue($policy->update($yosephUser, $voucher));
        $this->assertTrue($policy->delete($yosephUser, $voucher));

        // Filament Resource RBAC Gatekeeper checks
        $this->actingAs($regularUser);
        $this->assertFalse(BlueprintVoucherResource::canViewAny());
        $this->assertFalse(BlueprintVoucherResource::canCreate());
        $this->assertFalse(BlueprintVoucherResource::canEdit($voucher));
        $this->assertFalse(BlueprintVoucherResource::canDelete($voucher));

        $this->actingAs($yosephUser);
        $this->assertTrue(BlueprintVoucherResource::canViewAny());
        $this->assertTrue(BlueprintVoucherResource::canCreate());
        $this->assertTrue(BlueprintVoucherResource::canEdit($voucher));
        $this->assertTrue(BlueprintVoucherResource::canDelete($voucher));
    }

    public function test_partial_discount_voucher_rejected_by_free_claim_endpoint(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Ahmad Dani',
            'nama_bisnis' => 'Dani Store',
            'email' => 'dani@danistore.id',
            'phone' => '08123456781',
            'masalah_utama' => 'E-commerce platform',
            'tujuan_utama' => 'Online shop',
            'is_published' => true,
        ]);

        $voucher = BlueprintVoucher::create([
            'code' => 'DISC-500K',
            'description' => 'Diskon 500 Ribu',
            'discount_type' => 'fixed',
            'discount_value' => 500000,
            'is_active' => true,
        ]);

        // Attempting to claim partial voucher as free grant must be rejected
        $response = $this->postJson(route('blueprint.voucher.claim', $blueprint->slug), [
            'code' => 'DISC-500K',
            'agree_sign_off' => true,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $blueprint->refresh();
        $this->assertFalse($blueprint->is_free_grant);
    }

    public function test_scope_lock_prevents_prd_regeneration(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Citra Lestari',
            'nama_bisnis' => 'Citra Bakery',
            'email' => 'citra@bakery.com',
            'phone' => '08123456782',
            'masalah_utama' => 'Order tracking',
            'tujuan_utama' => 'Automate orders',
            'is_published' => true,
            'signed_agreement' => true,
            'document_sha256' => hash('sha256', 'locked_spec'),
            'prd_content' => [
                'meta' => ['project_name' => 'Citra Bakery'],
                'engineering_specs' => ['status' => 'locked_original_content']
            ]
        ]);

        // Accessing with ?regenerate=1 must NOT regenerate when scope is locked
        $response = $this->get(route('blueprint.show', ['slug' => $blueprint->slug, 'regenerate' => 1]));
        $response->assertStatus(200);

        $blueprint->refresh();
        $this->assertEquals('locked_original_content', $blueprint->prd_content['engineering_specs']['status']);
    }

    public function test_cart_voucher_apply_and_remove(): void
    {
        BlueprintVoucher::create([
            'code' => 'MITRA-1JT',
            'description' => 'Subsidi Mitra 1 Juta',
            'discount_type' => 'fixed',
            'discount_value' => 1000000,
            'is_active' => true,
        ]);

        // 1. Apply voucher
        $response = $this->post(route('cart.voucher.apply'), [
            'voucher_code' => 'MITRA-1JT',
        ]);
        $response->assertRedirect(route('cart.index'));
        $this->assertEquals('MITRA-1JT', session('neriah_cart_voucher')['code']);

        // 2. Remove voucher
        $response = $this->post(route('cart.voucher.remove'));
        $response->assertRedirect(route('cart.index'));
        $this->assertNull(session('neriah_cart_voucher'));
    }

    public function test_snap_token_creation_does_not_consume_voucher_quota_prematurely(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Ferry Pratama',
            'nama_bisnis' => 'Ferry Auto Repair',
            'email' => 'ferry@autorepair.id',
            'phone' => '08123456783',
            'masalah_utama' => 'Booking servis mobil',
            'tujuan_utama' => 'Aplikasi bengkel',
            'is_published' => true,
        ]);

        $voucher = BlueprintVoucher::create([
            'code' => 'BENGKEL-PROMO',
            'description' => 'Diskon Bengkel 10%',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'max_uses' => 1,
            'used_count' => 0,
            'is_active' => true,
        ]);

        // Request Snap token with voucher
        $response = $this->postJson(route('blueprint.snap-token', $blueprint->slug), [
            'tier' => 'standard',
            'agree_sign_off' => true,
            'voucher_code' => 'BENGKEL-PROMO',
        ]);

        $response->assertStatus(200);

        // Voucher usage count must STILL be 0 because payment has NOT settled yet!
        $voucher->refresh();
        $this->assertEquals(0, $voucher->used_count);

        // Blueprint must remember voucher_code
        $blueprint->refresh();
        $this->assertEquals('BENGKEL-PROMO', $blueprint->voucher_code);
    }

    public function test_npro_dp_webhook_fulfillment_and_deferred_voucher_increment(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Maya Anggraini',
            'nama_bisnis' => 'Maya Boutique Online',
            'email' => 'maya@boutique.id',
            'phone' => '08123456784',
            'masalah_utama' => 'Katalog butik',
            'tujuan_utama' => 'Penjualan baju online',
            'is_published' => true,
            'voucher_code' => 'BUTIK-500K',
        ]);

        $voucher = BlueprintVoucher::create([
            'code' => 'BUTIK-500K',
            'description' => 'Subsidi Butik 500rb',
            'discount_type' => 'fixed',
            'discount_value' => 500000,
            'max_uses' => 1,
            'used_count' => 0,
            'is_active' => true,
        ]);

        $orderId = 'NPRO-DP-' . strtoupper(substr($blueprint->id, 0, 8)) . '-' . time();

        $payload = [
            'order_id' => $orderId,
            'status_code' => '200',
            'gross_amount' => '12000000.00',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
        ];

        $response = $this->postJson(route('webhook.midtrans'), $payload);
        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        // 1. Blueprint status updated & staging URL provisioned
        $blueprint->refresh();
        $this->assertEquals('In Development (DP Paid)', $blueprint->project_status);
        $this->assertNotEmpty($blueprint->staging_url);
        $this->assertTrue($blueprint->signed_agreement);

        // 2. Voucher quota strictly incremented upon settlement!
        $voucher->refresh();
        $this->assertEquals(1, $voucher->used_count);
    }

    public function test_cart_order_webhook_fulfillment(): void
    {
        $bp1 = VisionBlueprint::create([
            'client_name' => 'Rudy Hartono',
            'nama_bisnis' => 'Rudy Badminton Academy',
            'email' => 'rudy@badminton.id',
            'phone' => '08123456785',
            'is_published' => true,
        ]);

        $bp2 = VisionBlueprint::create([
            'client_name' => 'Rudy Hartono',
            'nama_bisnis' => 'Rudy Pro Shop',
            'email' => 'rudy@badminton.id',
            'phone' => '08123456785',
            'is_published' => true,
        ]);

        $voucher = BlueprintVoucher::create([
            'code' => 'RUDY-CART-DISC',
            'description' => 'Diskon Paket Cart 15%',
            'discount_type' => 'percent',
            'discount_value' => 15,
            'used_count' => 0,
            'is_active' => true,
        ]);

        // Set cart session
        $this->withSession([
            'neriah_cart' => [
                $bp1->slug => ['slug' => $bp1->slug, 'contract_amount' => 25000000, 'dp_amount' => 12500000],
                $bp2->slug => ['slug' => $bp2->slug, 'contract_amount' => 25000000, 'dp_amount' => 12500000],
            ],
            'neriah_cart_voucher' => [
                'code' => $voucher->code,
                'discount_type' => $voucher->discount_type,
                'discount_value' => $voucher->discount_value,
            ],
        ]);

        // Request Cart Snap Token
        $snapRes = $this->postJson(route('cart.snap-token'));
        $snapRes->assertStatus(200);
        $orderId = $snapRes->json('order_id');
        $this->assertStringStartsWith('NP-CART-', $orderId);

        // Voucher count must NOT be incremented yet
        $voucher->refresh();
        $this->assertEquals(0, $voucher->used_count);

        // Send Midtrans Webhook settlement for Cart Order
        $payload = [
            'order_id' => $orderId,
            'status_code' => '200',
            'gross_amount' => '21250000.00',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
        ];

        $webhookRes = $this->postJson(route('webhook.midtrans'), $payload);
        $webhookRes->assertStatus(200);

        // Verify both blueprints are fulfilled
        $bp1->refresh();
        $bp2->refresh();
        $this->assertEquals('In Development (DP Paid)', $bp1->project_status);
        $this->assertEquals('In Development (DP Paid)', $bp2->project_status);
        $this->assertNotEmpty($bp1->staging_url);
        $this->assertNotEmpty($bp2->staging_url);

        // Verify voucher count incremented once for the cart order
        $voucher->refresh();
        $this->assertEquals(1, $voucher->used_count);
    }

    public function test_task_editor_updates_prd_and_recomputes_hash(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Hendro Wijaya',
            'nama_bisnis' => 'Hendro Smart Warehouse',
            'email' => 'hendro@smartwarehouse.id',
            'phone' => '08123456786',
            'is_published' => true,
            'signed_agreement' => false,
        ]);

        $initialHash = $blueprint->calculatePrdHash();

        $mvpTasks = [
            ['title' => 'Barcode Scanning Inbound', 'desc' => 'Scanner kamera via Web & PWA'],
            ['title' => 'Rak Lokasi 3D Grid', 'desc' => 'Visualisasi bin location'],
        ];

        $phase2Tasks = [
            ['title' => 'Integrasi RFID IoT', 'desc' => 'Otomatisasi gate checking'],
        ];

        $response = $this->postJson(route('blueprint.tasks.update', $blueprint->slug), [
            'mvp_tasks' => $mvpTasks,
            'phase2_tasks' => $phase2Tasks,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'mvp_count' => 2,
                'phase2_count' => 1,
            ]);

        $blueprint->refresh();
        $this->assertStringContainsString('Barcode Scanning Inbound', $blueprint->fitur_wajib);
        $this->assertStringContainsString('Integrasi RFID IoT', $blueprint->fitur_tambahan);
        $this->assertCount(2, $blueprint->prd_content['features']['mvp_phase1']);
        $this->assertCount(1, $blueprint->prd_content['features']['phase2_roadmap']);

        // Cryptographic hash recomputed
        $this->assertNotEquals($initialHash, $blueprint->document_sha256);
    }

    public function test_task_editor_rejected_when_scope_is_locked(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Dewi Sartika',
            'nama_bisnis' => 'Dewi Edu Hub',
            'email' => 'dewi@eduhub.id',
            'phone' => '08123456787',
            'is_published' => true,
            'signed_agreement' => true, // Scope is locked
        ]);

        $response = $this->postJson(route('blueprint.tasks.update', $blueprint->slug), [
            'mvp_tasks' => [
                ['title' => 'New Feature After Lock', 'desc' => 'Should be rejected'],
            ],
            'phase2_tasks' => [],
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);
    }
}
