<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Models\Document;
use App\Models\Transaction;
use App\Models\LeadContact;
use Database\Seeders\CmsSeeder;
use Database\Seeders\SuperAdminSeeder;
use Database\Seeders\LandingPageSeeder;
use Database\Seeders\BlueprintVoucherSeeder;
use Illuminate\Support\Facades\Cache;

class MidtransRetailAndPelunasanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SuperAdminSeeder::class);
        $this->seed(CmsSeeder::class);
        $this->seed(LandingPageSeeder::class);
        $this->seed(BlueprintVoucherSeeder::class);
    }

    public function test_retail_lite_99k_pricing_inquiry_returns_snap_token()
    {
        $payload = [
            'name' => 'John Doe',
            'company' => 'JocC Tech',
            'email' => 'john.doe@jocc.com',
            'country_code' => '+62',
            'phone' => '81380448253',
            'package_tier' => 'retail_lite',
            'notes' => 'Self-service PRD specification order for in-house team',
        ];

        $response = $this->postJson('/api/pricing/inquiry', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_paid_package' => true,
            'net_price' => 99000,
        ]);
        $response->assertJsonStructure([
            'success',
            'is_paid_package',
            'order_id',
            'snap_token',
            'net_price',
            'formatted_net_price',
            'whatsapp_url'
        ]);

        $orderId = $response->json('order_id');
        $this->assertStringStartsWith('NPRO-LIC-', $orderId);
        $this->assertNotNull($response->json('snap_token'));

        // Assert lead contact was created
        $this->assertDatabaseHas('lead_contacts', [
            'email' => 'john.doe@jocc.com',
            'company_name' => 'JocC Tech',
            'status' => 'lead',
        ]);
    }

    public function test_midtrans_webhook_fulfills_retail_license_order()
    {
        $lead = LeadContact::create([
            'name' => 'John Doe Retail',
            'company_name' => 'JocC Tech',
            'email' => 'john.retail@jocc.com',
            'phone' => '+6281380448253',
            'status' => 'lead',
        ]);

        $orderId = "NPRO-LIC-{$lead->id}-1728345678-4321";
        $orderData = [
            'lead_id' => $lead->id,
            'type' => 'retail_license',
            'package_tier' => 'retail_lite',
            'name' => 'John Doe Retail',
            'company' => 'JocC Tech',
            'email' => 'john.retail@jocc.com',
            'phone' => '+6281380448253',
            'gross_amount' => 99000,
            'is_retail' => true,
            'display_package' => 'Lite PRD Generator (Rp 99.000)',
            'created_at' => now()->toIso8601String(),
        ];
        Cache::put("pricing_order_{$orderId}", $orderData, now()->addHours(24));

        $serverKey = config('midtrans.server_key');
        $statusCode = '200';
        $grossAmount = '99000.00';
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $webhookPayload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_id' => 'midtrans-trx-retail-001',
        ];

        $response = $this->postJson('/api/webhook/midtrans', $webhookPayload);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // Assert user was created
        $user = User::where('email', 'john.retail@jocc.com')->first();
        $this->assertNotNull($user);

        // Assert retail blueprint was generated
        $blueprint = VisionBlueprint::where('email', $user->email)->first();
        $this->assertNotNull($blueprint);
        $this->assertEquals('Retail License', $blueprint->project_status);
        $this->assertEquals('retail_lite', $blueprint->user_metadata['retail_tier'] ?? null);
        $this->assertEquals($user->id, $blueprint->user_metadata['user_id'] ?? null);

        // Assert transaction was recorded
        $this->assertDatabaseHas('transactions', [
            'midtrans_order_id' => $orderId,
            'status' => 'settlement',
        ]);

        // Assert lead contact became customer
        $this->assertDatabaseHas('lead_contacts', [
            'email' => 'john.retail@jocc.com',
            'status' => 'customer',
        ]);
    }

    public function test_pelunasan_snap_token_rejects_if_dp_not_confirmed()
    {
        $blueprint = VisionBlueprint::create([
            'nama_bisnis' => 'Pending DP Project',
            'client_name' => 'Alice Client',
            'email' => 'alice@test.com',
            'slug' => 'pending-dp-project',
            'project_status' => 'Awaiting DP',
        ]);

        $response = $this->postJson("/blueprint/{$blueprint->slug}/pelunasan-snap-token");

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('Uang Muka (DP 50%) belum diselesaikan', $response->json('message'));
    }

    public function test_pelunasan_snap_token_endpoint_calculates_50_percent_balance()
    {
        $blueprint = VisionBlueprint::create([
            'nama_bisnis' => 'Active Sprint Project',
            'client_name' => 'Bob Client',
            'email' => 'bob@test.com',
            'slug' => 'active-sprint-project',
            'project_status' => 'Active Sprint',
            'prd_content' => [
                'itemized_cost_breakdown' => [
                    'base_subtotal' => 50000000.00
                ]
            ],
            'user_metadata' => [
                'dev_progress_percent' => 100
            ]
        ]);

        Document::create([
            'related_id' => $blueprint->id,
            'related_type' => VisionBlueprint::class,
            'title' => 'Perjanjian Kontrak Kerja SPK',
            'document_type' => 'contract',
            'status' => 'signed',
            'contract_amount' => 50000000.00,
            'dp_amount' => 25000000.00,
        ]);

        // Simulate confirmed DP payment
        Transaction::create([
            'user_id' => null,
            'midtrans_order_id' => 'NPRO-DP-' . $blueprint->id . '-123',
            'gross_amount' => 25000000,
            'total_idr' => 25000000,
            'status' => 'settlement',
            'customer_details' => ['tier' => 'mvp_monolith']
        ]);

        $this->assertTrue($blueprint->isDpConfirmed());
        $this->assertFalse($blueprint->isPelunasanConfirmed());

        $response = $this->postJson("/blueprint/{$blueprint->slug}/pelunasan-snap-token");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'pelunasan_amount' => 25000000,
        ]);
        $response->assertJsonStructure([
            'success',
            'token',
            'order_id',
            'pelunasan_amount',
            'formatted_pelunasan_amount',
            'redirect_url'
        ]);

        $orderId = $response->json('order_id');
        $this->assertStringStartsWith('NPRO-FINAL-' . $blueprint->id . '-', $orderId);
        $this->assertNotNull($response->json('token'));
    }

    public function test_midtrans_webhook_fulfills_pelunasan_final_settlement()
    {
        $blueprint = VisionBlueprint::create([
            'nama_bisnis' => 'Ready For Settlement Project',
            'client_name' => 'Charlie Client',
            'email' => 'charlie@test.com',
            'slug' => 'ready-for-settlement-project',
            'project_status' => 'Active Sprint',
            'prd_content' => [
                'itemized_cost_breakdown' => [
                    'base_subtotal' => 50000000.00
                ]
            ]
        ]);

        // Attach contract document
        $contract = Document::create([
            'related_id' => $blueprint->id,
            'related_type' => VisionBlueprint::class,
            'title' => 'Perjanjian Kontrak Kerja SPK',
            'document_type' => 'contract',
            'status' => 'signed',
            'contract_amount' => 50000000.00,
            'dp_amount' => 25000000.00,
        ]);

        $orderId = "NPRO-FINAL-{$blueprint->id}-999";
        $serverKey = config('midtrans.server_key');
        $statusCode = '200';
        $grossAmount = '25000000.00';
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $webhookPayload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'midtrans-trx-final-999',
        ];

        $response = $this->postJson('/api/webhook/midtrans', $webhookPayload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // Assert blueprint status changed to completed
        $blueprint->refresh();
        $this->assertEquals('Completed & Fully Settled', $blueprint->project_status);
        $this->assertTrue($blueprint->isPelunasanConfirmed());

        // Assert contract settlement was registered in clauses
        $contract->refresh();
        $this->assertTrue(!empty($contract->content_clauses['is_fully_settled']));

        // Assert transaction was saved
        $this->assertDatabaseHas('transactions', [
            'midtrans_order_id' => $orderId,
            'status' => 'settlement',
        ]);
    }

    public function test_project_os_retail_products_exist_in_database()
    {
        $this->assertDatabaseHas('products', [
            'slug' => 'project-os-retail-lite-prd',
            'price_idr' => 99000.00,
        ]);

        $this->assertDatabaseHas('products', [
            'slug' => 'project-os-retail-pro-blueprint',
            'price_idr' => 399000.00,
        ]);

        $this->assertDatabaseHas('products', [
            'slug' => 'project-os-retail-ultimate-factory-os',
            'price_idr' => 1490000.00,
        ]);
    }

    public function test_cart_snap_token_creates_pending_transaction_and_resets_cleanly()
    {
        $blueprint = VisionBlueprint::create([
            'slug' => 'test-cart-flow-slug',
            'nama_bisnis' => 'Retail Cart Co',
            'client_name' => 'Bob Marley',
            'email' => 'bob@cartflow.com',
            'phone' => '081299887766',
            'project_status' => 'Draft',
        ]);

        // Put in cart session
        $this->withSession([
            'neriah_cart' => [
                $blueprint->slug => [
                    'slug' => $blueprint->slug,
                    'nama_bisnis' => $blueprint->nama_bisnis,
                    'tier' => 'standard',
                    'contract_amount' => 50000000,
                    'dp_amount' => 25000000,
                    'added_at' => now()->toIso8601String(),
                ]
            ]
        ]);

        // Request Snap Token
        $response = $this->postJson('/cart/snap-token');
        $response->assertStatus(200);
        $orderId = $response->json('order_id');
        $grossAmount = $response->json('gross_amount');
        $this->assertNotEmpty($orderId);
        $this->assertGreaterThan(0, $grossAmount);

        // Assert pending transaction exists in database
        $this->assertDatabaseHas('transactions', [
            'midtrans_order_id' => $orderId,
            'status' => 'pending',
            'total_idr' => $grossAmount,
        ]);

        // Visit cart page and verify pending card is rendered
        $cartPage = $this->get('/cart');
        $cartPage->assertStatus(200);
        $cartPage->assertSee($orderId);
        $cartPage->assertSee('Menunggu Pembayaran');
        $cartPage->assertSee('Lanjutkan Bayar');

        // Reset / cancel pending session
        $resetResponse = $this->post('/cart/reset-pending');
        $resetResponse->assertRedirect('/cart');

        // Assert transaction status transitioned to cancel
        $this->assertDatabaseHas('transactions', [
            'midtrans_order_id' => $orderId,
            'status' => 'cancel',
        ]);

        // Assert session pending order is cleared
        $this->assertNull(session('neriah_cart_pending_order'));
    }
}
