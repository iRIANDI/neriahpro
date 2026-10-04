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
}
