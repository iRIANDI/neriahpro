<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\BlueprintVoucher;
use App\Models\ClientIncidentReport;
use App\Models\VisionBlueprint;
use App\Services\Incident\ClientIncidentReportService;
use App\Services\ScaffoldGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ClientIncidentAndDualDatabaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);
        RateLimiter::clear('incident-report:127.0.0.1');
    }

    /**
     * Test client incident reporting triggers automatic SLA compensation (Rp 150.000 voucher + 30 days warranty).
     */
    public function test_client_incident_reporting_triggers_automatic_sla_compensation(): void
    {
        $blueprint = VisionBlueprint::create([
            'nama_bisnis' => 'Retail Church Translation App',
            'client_name' => 'Yoseph Iriandi',
            'email' => 'yoseph@example.com',
            'phone' => '+6281380448253',
            'masalah_utama' => 'Need church real-time multilingual translator application.',
            'tujuan_utama' => 'Provide real-time translation for international congregation.',
            'target_audiens' => 'Church congregation and foreign visitors',
            'aktor_sistem' => 'Admin, Translator, Listener',
            'fitur_wajib' => 'Language channels, live audio streaming, moderation chat',
            'alur_kerja' => 'Translator speaks -> Audio streamed to listener channel',
            'project_status' => 'Retail License',
            'signed_agreement' => true,
            'is_published' => true,
            'user_metadata' => [
                'retail_tier' => 'retail_pro',
            ],
        ]);

        $payload = [
            'error_message' => 'Operation timed out after 18000 milliseconds with 0 bytes received from AI provider.',
            'client_email' => 'yoseph@example.com',
            'client_name' => 'Yoseph Iriandi',
            'client_phone' => '+6281380448253',
            'error_type' => 'AI_SYNTHESIS_TIMEOUT',
            'vision_blueprint_id' => $blueprint->id,
            'client_notes' => 'I was synthesizing a large specification document and experienced latency.',
            'technical_diagnostics' => [
                'provider' => 'deepseek',
                'action' => 'regenerate_prd',
            ],
        ];

        $response = $this->postJson('/api/blueprint/report-incident', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'compensation_amount_idr' => 150000,
                'extra_sla_warranty_days' => 30,
                'vip_fast_track' => true,
                'status' => 'COMPENSATED',
            ],
        ]);

        $responseData = $response->json('data');
        $this->assertNotEmpty($responseData['incident_code']);
        $this->assertStringStartsWith('INC-', $responseData['incident_code']);
        $this->assertNotEmpty($responseData['voucher_code']);
        $this->assertStringStartsWith('SLA-COMP-', $responseData['voucher_code']);

        // Assert record persisted in client_incident_reports table
        $report = ClientIncidentReport::where('incident_code', $responseData['incident_code'])->first();
        $this->assertNotNull($report);
        $this->assertEquals('yoseph@example.com', $report->client_email);
        $this->assertEquals(150000, $report->compensation_amount_idr);
        $this->assertEquals(30, $report->extra_sla_warranty_days);

        // Assert BlueprintVoucher created and valid
        $voucher = BlueprintVoucher::where('code', $responseData['voucher_code'])->first();
        $this->assertNotNull($voucher);
        $this->assertEquals(150000.0, (float) $voucher->discount_value);
        $this->assertTrue($voucher->is_active);
        $this->assertTrue($voucher->isValid());

        // Assert VisionBlueprint metadata was enriched
        $blueprint->refresh();
        $this->assertEquals(30, $blueprint->user_metadata['extra_sla_warranty_days']);
        $this->assertTrue($blueprint->user_metadata['vip_fast_track']);
        $this->assertEquals($responseData['voucher_code'], $blueprint->user_metadata['sla_compensation_voucher']);
    }

    /**
     * Test incident report idempotency prevents spamming duplicate vouchers.
     */
    public function test_incident_report_idempotency_prevents_duplicate_vouchers(): void
    {
        $payload = [
            'error_message' => 'Repetitive server timeout notice.',
            'client_email' => 'client@example.com',
            'client_name' => 'John Doe',
            'error_type' => 'SERVER_HTTP_ERROR',
        ];

        $res1 = $this->postJson('/api/blueprint/report-incident', $payload);
        $res1->assertStatus(200);
        $code1 = $res1->json('data.voucher_code');

        $initialVoucherCount = BlueprintVoucher::count();

        // Second immediate call with identical payload
        $res2 = $this->postJson('/api/blueprint/report-incident', $payload);
        $res2->assertStatus(200);
        $code2 = $res2->json('data.voucher_code');

        // Should return existing voucher without duplicating rows
        $this->assertEquals($code1, $code2);
        $this->assertEquals($initialVoucherCount, BlueprintVoucher::count());
    }

    /**
     * Test bot rejection via honeypot field.
     */
    public function test_honeypot_rejects_bot_incident_submissions(): void
    {
        $payload = [
            'error_message' => 'Spam submission',
            'client_email' => 'bot@spammer.com',
            '_website' => 'http://spam-site.com',
        ];

        $response = $this->postJson('/api/blueprint/report-incident', $payload);
        $response->assertStatus(422);
    }

    /**
     * Test Project OS Dual-Engine Database DDL generation (PostgreSQL 16 & MySQL 8.0+).
     */
    public function test_project_os_dual_engine_database_migrations_generation(): void
    {
        $blueprint = VisionBlueprint::create([
            'nama_bisnis' => 'OmniRetail Enterprise',
            'client_name' => 'Enterprise Client',
            'email' => 'corp@example.com',
            'phone' => '+628123456789',
            'masalah_utama' => 'Unified inventory and omni-channel retail management.',
            'tujuan_utama' => 'High-concurrency data persistence with sub-10ms response times.',
            'target_audiens' => 'Warehouse staff, store cashiers, enterprise directors',
            'aktor_sistem' => 'Superadmin, Cashier, Inventory Manager',
            'fitur_wajib' => 'Inventory Tracking, POS Checkout, Sales Journal',
            'alur_kerja' => 'Stock in -> Sales made -> Ledger reconciled',
            'project_status' => 'Prospecting',
            'is_published' => true,
        ]);

        $erdTables = [
            [
                'name' => 'inventory_items',
                'description' => 'Master produk dan inventaris toko',
                'columns' => [
                    ['name' => 'sku', 'type' => 'string(50)', 'index' => 'UNIQUE'],
                    ['name' => 'name', 'type' => 'string(255)', 'index' => 'NONE'],
                    ['name' => 'stock_qty', 'type' => 'integer', 'index' => 'NONE'],
                    ['name' => 'unit_price', 'type' => 'decimal(15,2)', 'index' => 'NONE'],
                    ['name' => 'attributes', 'type' => 'json', 'index' => 'NONE'],
                    ['name' => 'is_active', 'type' => 'boolean', 'index' => 'INDEX'],
                ],
            ],
            [
                'name' => 'pos_orders',
                'description' => 'Transaksi kasir omnichannel',
                'columns' => [
                    ['name' => 'order_number', 'type' => 'string(50)', 'index' => 'UNIQUE'],
                    ['name' => 'total_amount', 'type' => 'decimal(15,2)', 'index' => 'NONE'],
                    ['name' => 'status', 'type' => 'string(50)', 'index' => 'INDEX'],
                    ['name' => 'user_id', 'type' => 'foreignUlid', 'index' => 'INDEX'],
                ],
            ],
        ];

        // 1. PostgreSQL DDL Generation
        $pgSql = ScaffoldGeneratorService::generateDatabaseMigrationsSql($blueprint, $erdTables);
        $this->assertStringContainsString('CREATE EXTENSION IF NOT EXISTS "pgcrypto"', $pgSql);
        $this->assertStringContainsString('id VARCHAR(26) PRIMARY KEY', $pgSql);
        $this->assertStringContainsString('TIMESTAMPTZ', $pgSql);
        $this->assertStringContainsString('JSONB', $pgSql);

        // 2. MySQL 8.0+ DDL Generation
        $mySql = ScaffoldGeneratorService::generateDatabaseMigrationsMySql($blueprint, $erdTables);
        $this->assertStringContainsString('SET FOREIGN_KEY_CHECKS = 0;', $mySql);
        $this->assertStringContainsString('`id` CHAR(26) CHARACTER SET ascii COLLATE ascii_bin NOT NULL', $mySql);
        $this->assertStringContainsString('ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC', $mySql);
        $this->assertStringContainsString('`created_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)', $mySql);
        $this->assertStringContainsString('KEY `idx_inventory_items_cursor` (`created_at`, `id`)', $mySql);
        $this->assertStringContainsString('KEY `idx_pos_orders_cursor` (`created_at`, `id`)', $mySql);

        // 3. Test Scaffolding files contain both database scripts
        $scaffoldFiles = ScaffoldGeneratorService::generateFiles($blueprint);
        $this->assertArrayHasKey('database/migrations/schema_complete_postgresql.sql', $scaffoldFiles);
        $this->assertArrayHasKey('database/migrations/schema_complete_mysql.sql', $scaffoldFiles);
        $this->assertArrayHasKey('schema_complete.sql', $scaffoldFiles);
    }
}

