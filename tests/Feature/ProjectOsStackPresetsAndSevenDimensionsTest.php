<?php

namespace Tests\Feature;

use App\Models\PosPrimitive;
use App\Models\PosStackPreset;
use App\Models\VisionBlueprint;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Services\ScaffoldGeneratorService;
use Database\Seeders\PosStackPresetSeeder;
use Database\Seeders\ProjectOsMatrixSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectOsStackPresetsAndSevenDimensionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_populates_nine_curated_stack_presets_and_enriches_all_twelve_primitives_with_seven_dimensions(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);

        $this->assertSame(9, PosStackPreset::query()->count());
        $this->assertSame(4, PosStackPreset::query()->where('topology', 'MONOLITH')->count());
        $this->assertSame(3, PosStackPreset::query()->where('topology', 'DECOUPLED_HEADLESS')->count());
        $this->assertSame(2, PosStackPreset::query()->where('topology', 'MICROSERVICES')->count());

        $m1 = PosStackPreset::query()->where('code', 'STACK_M1_LARAVEL_MONOLITH')->firstOrFail();
        $this->assertSame(26, strlen($m1->id));
        $this->assertTrue($m1->is_default);
        $this->assertSame('M1', $m1->short_code);

        $s1 = PosStackPreset::query()->where('code', 'STACK_S1_POLYGLOT_GO_LARAVEL_TEMPORAL')->firstOrFail();
        $this->assertSame(26, strlen($s1->id));
        $this->assertSame('MICROSERVICES', $s1->topology);
        $this->assertNotEmpty($s1->inter_layer_type_contract);

        $primitives = PosPrimitive::query()->get();
        $this->assertCount(12, $primitives);

        foreach ($primitives as $primitive) {
            $this->assertNotEmpty(
                $primitive->deterministic_formulas,
                "Primitive {$primitive->code} must define deterministic_formulas"
            );
            $this->assertNotEmpty(
                $primitive->saga_and_webhook_contracts,
                "Primitive {$primitive->code} must define saga_and_webhook_contracts"
            );
            $this->assertNotEmpty(
                $primitive->ui_screen_archetypes,
                "Primitive {$primitive->code} must define ui_screen_archetypes"
            );
            $this->assertNotEmpty(
                $primitive->acceptance_test_scenarios,
                "Primitive {$primitive->code} must define acceptance_test_scenarios"
            );
        }
    }

    public function test_same_domain_blueprint_compiles_cleanly_across_monolith_go_headless_and_polyglot_microservices_adapters(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);

        $baseAttributes = [
            'client_name' => 'Rizky Pratama',
            'email' => 'rizky.fintech@example.com',
            'phone' => '+6281234567899',
            'nama_bisnis' => 'Nusantara Pay & Omnichannel Ledger',
            'masalah_utama' => 'Selisih pembulatan cicilan Rp 1, webhook pembayaran duplikat, dan stok flash-sale sering minus.',
            'tujuan_utama' => 'Membangun sistem ledger double-entry immutable, atomic stock reservation, dan rekonsiliasi webhook Midtrans idempoten.',
            'target_audiens' => 'Merchant B2B, Kasir Omnichannel, Finance Auditor, dan Pelanggan.',
            'aktor_sistem' => "Finance Auditor: Rekonsiliasi jurnal buku besar\nMerchant: Penjualan & reservasi stok\nCustomer: Pembayaran cicilan & QRIS",
            'fitur_wajib' => "Buku besar akuntansi double-entry immutable\nReservasi stok gudang atomik anti-overselling\nCicilan otomatis bebas selisih pembulatan 1 Rupiah",
            'alur_kerja' => "1. Order dibuat & stok dikunci\n2. Webhook Midtrans HMAC-SHA256 masuk ke vault\n3. Jurnal debit/kredit diposting atomik",
        ];

        // 1. Compile with M1 (Laravel 13 Monolith)
        $bpMonolith = VisionBlueprint::query()->create(array_merge($baseAttributes, [
            'user_metadata' => [
                'primary_sector' => 'SECTOR_04_FINTECH_LEDGER',
                'selected_primitives' => ['PRIMITIVE_DOUBLE_ENTRY_LEDGER', 'PRIMITIVE_INVENTORY_LEDGER'],
                'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
            ],
        ]));
        $bpMonolith->generateAndSavePrd();

        // 2. Compile identical domain blueprint with D2 (Golang + sqlc + Next.js Decoupled Headless)
        $bpGoHeadless = VisionBlueprint::query()->create(array_merge($baseAttributes, [
            'nama_bisnis' => 'Nusantara Pay Go Headless',
            'user_metadata' => [
                'primary_sector' => 'SECTOR_04_FINTECH_LEDGER',
                'selected_primitives' => ['PRIMITIVE_DOUBLE_ENTRY_LEDGER', 'PRIMITIVE_INVENTORY_LEDGER'],
                'stack_preset' => 'STACK_D2_GO_SQLC_NEXTJS',
            ],
        ]));
        $bpGoHeadless->generateAndSavePrd();

        // 3. Compile identical domain blueprint with S1 (Polyglot Go + Laravel + Temporal Microservices)
        $bpPolyglotMesh = VisionBlueprint::query()->create(array_merge($baseAttributes, [
            'nama_bisnis' => 'Nusantara Pay Polyglot Mesh',
            'user_metadata' => [
                'primary_sector' => 'SECTOR_04_FINTECH_LEDGER',
                'selected_primitives' => ['PRIMITIVE_DOUBLE_ENTRY_LEDGER', 'PRIMITIVE_INVENTORY_LEDGER'],
                'stack_preset' => 'STACK_S1_POLYGLOT_GO_LARAVEL_TEMPORAL',
            ],
        ]));
        $bpPolyglotMesh->generateAndSavePrd();

        $schemaM1 = $bpMonolith->fresh()->prd_content['universal_blueprint_schema'];
        $schemaD2 = $bpGoHeadless->fresh()->prd_content['universal_blueprint_schema'];
        $schemaS1 = $bpPolyglotMesh->fresh()->prd_content['universal_blueprint_schema'];

        // Domain tables & state machines stay 100% consistent across all 3 topologies
        $tablesM1 = array_column($schemaM1['data_dictionary'], 'table');
        $tablesD2 = array_column($schemaD2['data_dictionary'], 'table');
        $tablesS1 = array_column($schemaS1['data_dictionary'], 'table');

        $this->assertContains('journal_entries', $tablesM1);
        $this->assertContains('outbox_messages', $tablesM1);
        $this->assertContains('webhook_inbound_logs', $tablesM1);
        $this->assertSame($tablesM1, $tablesD2);
        $this->assertSame($tablesM1, $tablesS1);

        // Topology & Stack Contracts switch cleanly
        $this->assertSame('MONOLITH', $schemaM1['tech_stack_contract']['topology']);
        $this->assertSame('DECOUPLED_HEADLESS', $schemaD2['tech_stack_contract']['topology']);
        $this->assertSame('MICROSERVICES', $schemaS1['tech_stack_contract']['topology']);
        $this->assertStringContainsString(
            'CloudEvents v1.0',
            $schemaS1['api_and_envelope_contract']['inter_service_event_envelope']['protocol'] ?? ''
        );

        // Foreign key stack_preset_id persisted on PosProject
        $d2Preset = PosStackPreset::query()->where('code', 'STACK_D2_GO_SQLC_NEXTJS')->firstOrFail();
        $this->assertSame($d2Preset->id, $bpGoHeadless->fresh()->posProject?->stack_preset_id);

        // ScaffoldGeneratorService emits stack-specific adapter files
        $filesD2 = app(ScaffoldGeneratorService::class)->generateFiles($bpGoHeadless->fresh());
        $this->assertArrayHasKey('.agents/STACK_TOPOLOGY_CONTRACT.md', $filesD2);
        $this->assertArrayHasKey('tests/Feature/ZeroAssumptionAcceptanceMatrixTest.php', $filesD2);
        $this->assertArrayHasKey('backend-go/sqlc.yaml', $filesD2);
        $this->assertArrayHasKey('backend-go/db/queries/domain_queries.sql', $filesD2);
        $this->assertStringContainsString('FOR UPDATE', $filesD2['backend-go/db/queries/domain_queries.sql']);

        $filesS1 = app(ScaffoldGeneratorService::class)->generateFiles($bpPolyglotMesh->fresh());
        $this->assertArrayHasKey('proto/domain/v1/ledger_saga.proto', $filesS1);
        $this->assertArrayHasKey('services/temporal-sagas-go/saga_compensation_workflow.go', $filesS1);
        $this->assertArrayHasKey('deploy/docker-compose.polyglot-mesh.yml', $filesS1);
    }

    public function test_all_seven_zero_assumption_dimensions_and_public_endpoints_are_enforced(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);

        // Matrix catalog endpoint exposes 9 stack_presets & 9 comparison rows
        $catalogResp = $this->getJson('/api/blueprint/matrix-catalog');
        $catalogResp->assertStatus(200)
            ->assertJsonCount(12, 'catalog.sectors')
            ->assertJsonCount(12, 'catalog.primitives')
            ->assertJsonCount(9, 'catalog.stack_presets')
            ->assertJsonCount(9, 'catalog.stack_comparison_matrix');

        // Submit blueprint via public API selecting M3 (Next.js + tRPC + Drizzle ORM)
        $postResp = $this->postJson('/api/vision-blueprint', [
            'clientName' => 'Siti Rahma',
            'client_email' => 'siti.rahma@example.com',
            'client_whatsapp' => '81398765432',
            'namaBisnis' => 'Klinik & Apotek Sehat Sentosa',
            'masalahUtama' => 'Rekam medis pasien belum memiliki kebijakan anonimisasi UU PDP dan resep sering selisih stok.',
            'tujuanUtama' => 'Sistem EMR klinis SOAP terkunci, stok apotek FEFO, dan faktur pajak presisi.',
            'targetAudiens' => 'Dokter, Apoteker, Pasien, dan Admin Klinik.',
            'aktorSistem' => "Dokter: Rekam medis SOAP\nApoteker: Mutasi batch obat\nKasir: Pembayaran klaim",
            'fiturWajib' => "EMR SOAP immutable addendum\nStok obat FEFO pessimistic lock\nTagihan pasien & PPN",
            'alurKerja' => "1. Pasien registrasi\n2. Dokter tanda tangan SOAP\n3. Apoteker serahkan obat & kasir cetak kuitansi",
            'primarySector' => 'SECTOR_03_HEALTHCARE_CLINICAL',
            'stackPreset' => 'STACK_M3_NEXTJS_TRPC_DRIZZLE',
        ]);

        $postResp->assertStatus(201)->assertJsonPath('success', true);

        $blueprint = VisionBlueprint::query()->findOrFail($postResp->json('data.id'));
        $schema = ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint);

        // Verify Schema v2.0.0 & All 7 Dimensions
        $this->assertSame('2.0.0', $schema['schema_version']);
        $this->assertSame('STACK_M3_NEXTJS_TRPC_DRIZZLE', $schema['tech_stack_contract']['preset_code']);

        // Dim 1: RFC 9457 + Keyset Cursor + Headers
        $this->assertStringContainsString('RFC 9457', $schema['api_and_envelope_contract']['standard']);
        $this->assertSame(
            'application/problem+json',
            $schema['api_and_envelope_contract']['error_rfc9457_problem_envelope']['content_type']
        );
        $this->assertArrayHasKey('X-Idempotency-Key', $schema['api_and_envelope_contract']['mandatory_request_headers']);

        // Dim 2: Transactional Outbox & Compensating Actions
        $this->assertSame('outbox_messages', $schema['distributed_saga_and_outbox_contract']['outbox_table']);
        $this->assertNotEmpty($schema['distributed_saga_and_outbox_contract']['compensating_actions']);

        // Dim 3: Deterministic Formulas & Penny Rule
        $this->assertNotEmpty($schema['deterministic_formulas']);
        $this->assertSame('DECIMAL(19,4)', $schema['domain_invariants']['currency_handling']['type']);
        $this->assertSame('HALF_UP', $schema['domain_invariants']['currency_handling']['rounding_mode']);

        // Dim 4: External Integration Webhook Vault & Exponential Backoff
        $this->assertSame('webhook_inbound_logs', $schema['external_integration_contracts']['inbound_webhook_vault_table']);
        $this->assertSame([10, 60, 300, 1800], $schema['external_integration_contracts']['outbound_retry_schedule_seconds']);

        // Dim 5: Data Lifecycle, IMMUTABLE_LEDGER, ANONYMIZE_PII & ON DELETE RESTRICT
        $policies = collect($schema['data_lifecycle_and_pii_governance']['table_policies']);
        $this->assertTrue($policies->every(fn (array $p) => ($p['foreign_key_on_delete'] ?? '') === 'RESTRICT'));
        $this->assertTrue($policies->contains(fn (array $p) => ($p['deletion_policy'] ?? '') === 'IMMUTABLE_LEDGER'));

        // Dim 6 & Dim 7: UI Screen Archetypes & Pre-Built Acceptance Test Matrix
        $this->assertNotEmpty($schema['ui_screen_archetypes']);
        $this->assertNotEmpty($schema['acceptance_test_matrix']);

        // Download STACK_TOPOLOGY_CONTRACT.md & EXECUTION.MD
        $stackMdResp = $this->get(route('blueprint.download-cursorrules', [
            'slug' => $blueprint->slug,
            'format' => 'stack-topology',
        ]));
        $stackMdResp->assertStatus(200);
        $this->assertStringContainsString('STACK_M3_NEXTJS_TRPC_DRIZZLE', $stackMdResp->getContent());

        // M3 Drizzle & Zod files generated in scaffold
        $m3Files = app(ScaffoldGeneratorService::class)->generateFiles($blueprint->fresh());
        $this->assertArrayHasKey('src/db/schema.ts', $m3Files);
        $this->assertArrayHasKey('src/contracts/zod-schemas.ts', $m3Files);
    }
}

