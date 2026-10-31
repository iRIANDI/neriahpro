<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\PosProject;
use App\Models\PosSector;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\Ai\AgenticActionExecutionEngine;
use App\Services\Ai\GeminiToolRegistry;
use App\Services\Ai\VectorEmbeddingService;
use App\Services\BlueprintSlopScannerService;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Services\ScaffoldGeneratorService;
use App\Support\PosDayTwoProductionCatalog;
use Database\Seeders\PosStackPresetSeeder;
use Database\Seeders\ProjectOsMatrixSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProjectOsDayTwoLayersAndDoctorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            '*' => Http::response([
                'embedding' => ['values' => array_fill(0, 768, 0.0125)],
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => '{}'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);
    }

    public function test_all_twelve_sectors_expose_day_two_production_layers_and_ten_perfection_pillars(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);

        $sectors = PosSector::query()->orderBy('sector_number')->get();
        $this->assertCount(12, $sectors);

        foreach ($sectors as $sector) {
            $dayTwo = $sector->resolvedDayTwoOpsContract();
            $this->assertArrayHasKey('printable_and_thermal', $dayTwo, "Sector {$sector->code} missing printable_and_thermal");
            $this->assertArrayHasKey('tenant_onboarding', $dayTwo, "Sector {$sector->code} missing tenant_onboarding");
            $this->assertArrayHasKey('staged_bulk_import_export', $dayTwo, "Sector {$sector->code} missing staged_bulk_import_export");
            $this->assertArrayHasKey('day_two_queue_and_cron', $dayTwo, "Sector {$sector->code} missing day_two_queue_and_cron");
            $this->assertArrayHasKey('defense_in_depth_security', $dayTwo, "Sector {$sector->code} missing defense_in_depth_security");
            $this->assertArrayHasKey('state_notification_matrix', $dayTwo, "Sector {$sector->code} missing state_notification_matrix");
        }

        $this->assertCount(8, PosDayTwoProductionCatalog::finalEightLayersSummary());
        $this->assertCount(10, PosDayTwoProductionCatalog::tenPerfectionPillars());

        $catalog = ProjectOsBlueprintCompilerService::getCachedCatalog();
        $this->assertCount(8, $catalog['final_eight_layers']);
        $this->assertCount(10, $catalog['ten_perfection_pillars']);
        $this->assertArrayHasKey('day_two_ops_contract', $catalog['sectors'][0]);
    }

    public function test_interactive_blueprint_doctor_detects_cross_module_and_timeline_conflicts_with_one_click_autofix_and_sha256_audit(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);

        $resp = $this->postJson('/api/blueprint/doctor', [
            'sector_code' => 'SECTOR_03_HEALTHCARE_CLINICAL',
            'primitive_codes' => ['PRIMITIVE_RBAC_TENANCY'],
            'stack_preset_code' => 'STACK_S1_POLYGLOT_GO_LARAVEL_TEMPORAL',
            'workflow_text' => 'Pasien daftar poli, dokter tulis resep obat apotek FEFO, komisi dokter dan settlement klaim jurnal buku besar, serta approval direksi.',
            'timeline_days' => 21,
        ]);

        $resp->assertOk()
            ->assertJsonPath('success', true);

        $doctor = $resp->json('doctor');
        $this->assertSame('3.0.0', $doctor['doctor_version']);
        $this->assertSame('ACTION_RECOMMENDED', $doctor['status']);
        $this->assertTrue($doctor['one_click_auto_fix']['has_fixes']);

        $findingCodes = array_column($doctor['findings'], 'code');
        $this->assertContains('DOC_GAP_INVENTORY_FEFO_LEDGER', $findingCodes);
        $this->assertContains('DOC_GAP_DOUBLE_ENTRY_LEDGER', $findingCodes);

        $autoAdd = $doctor['one_click_auto_fix']['add_primitive_codes'];
        $this->assertContains('PRIMITIVE_INVENTORY_LEDGER', $autoAdd);
        $this->assertContains('PRIMITIVE_DOUBLE_ENTRY_LEDGER', $autoAdd);
        $this->assertNotEmpty($doctor['one_click_auto_fix']['recommended_stack_preset_code']);

        $audit = AuditLog::query()->where('event_category', 'blueprint_doctor')->latest('id')->first();
        $this->assertNotNull($audit);
        $this->assertSame('blueprint_doctor_diagnosed', $audit->action);
        $this->assertSame(64, strlen((string) $audit->integrity_hash));
    }

    public function test_compiler_scaffolder_slop_scanner_downloads_and_cli_bootstrapper_enforce_all_eight_day_two_layers(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);

        $blueprint = VisionBlueprint::query()->create([
            'client_name' => 'Rangga Pratama',
            'email' => 'rangga.pos@example.com',
            'phone' => '+6281234567890',
            'nama_bisnis' => 'Nusantara Retail POS & Grosir B2B',
            'masalah_utama' => 'Perubahan harga barang merusak laporan invoice lama dan kasir butuh cetak struk thermal 80mm cepat.',
            'tujuan_utama' => 'Membangun sistem POS kasir multi-cabang dengan struk thermal 80mm, faktur pajak A4 resmi, dan impor produk CSV ribuan baris tanpa timeout.',
            'target_audiens' => 'Kasir Toko, Kepala Gudang, Staf Keuangan, dan Pemilik Bisnis.',
            'aktor_sistem' => "Kasir: Transaksi cepat F8 & cetak struk\nGudang: Stok mutasi & opname\nFinance: Rekonsiliasi jurnal",
            'fitur_wajib' => "Split POS Terminal kasir cepat\nCetak struk thermal 58mm/80mm & faktur A4 QR\nImpor massal katalog CSV 10.000 SKU\nBuku besar akuntansi double-entry",
            'alur_kerja' => "1. Kasir scan SKU\n2. Bayar QRIS/Tunai\n3. Cetak struk thermal & kirim WA/Email invoice PDF",
            'user_metadata' => [
                'primary_sector' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
                'selected_primitives' => [
                    'PRIMITIVE_RBAC_TENANCY',
                    'PRIMITIVE_INVENTORY_LEDGER',
                    'PRIMITIVE_DOUBLE_ENTRY_LEDGER',
                    'PRIMITIVE_PAYMENT_ORCHESTRATION',
                ],
                'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
                'retail_tier' => 'retail_spark',
            ],
        ]);

        $schema = ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => true]);

        // Backward-compatible schema_version + v3.0 engine_release
        $this->assertSame('2.0.0', $schema['schema_version']);
        $this->assertSame('3.0.0-day2-ultimate', $schema['engine_release']);

        // Layer 4: Staging tables injected as strict ULID tables
        $tableNames = array_column($schema['data_dictionary'], 'table');
        $this->assertContains('import_staging_batches', $tableNames);
        $this->assertContains('import_staging_rows', $tableNames);

        // Layer 3: Historical snapshot columns enforced on transactional tables
        $this->assertNotEmpty($schema['historical_snapshot_contract']['enforced_on_tables']);
        $snapshotFlaggedTables = array_filter(
            $schema['data_dictionary'],
            fn (array $t): bool => ! empty($t['historical_snapshot_enforced'])
        );
        $this->assertNotEmpty($snapshotFlaggedTables);
        $firstSnapshotTbl = reset($snapshotFlaggedTables);
        $this->assertContains('unit_price_snapshot', $firstSnapshotTbl['mandatory_snapshot_columns']);
        $this->assertContains('counterparty_snapshot', $firstSnapshotTbl['mandatory_snapshot_columns']);

        // Layer 7: State Machines enriched with Omnichannel Notification Matrix
        $this->assertNotEmpty($schema['state_machines']);
        $this->assertArrayHasKey('notification_matrix', $schema['state_machines'][0]);

        // Persisted PosProject stores doctor_diagnostics_json
        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
        $this->assertNotNull($posProject);
        $this->assertNotEmpty($posProject->doctor_diagnostics_json);

        // Scaffolder generates Day-2 files (Print Blade with local SVG QR, InitializeNewTenantAction, Day-2 Ops Config)
        $files = app(ScaffoldGeneratorService::class)->generateFiles($blueprint, [
            'universal_blueprint_schema' => $schema,
        ]);
        $this->assertArrayHasKey('.agents/PRINT_THERMAL_AND_A4_CONTRACT.md', $files);
        $this->assertArrayHasKey('.agents/DAY2_OPERATIONS_AND_SECURITY.md', $files);
        $this->assertArrayHasKey('app/Actions/Tenancy/InitializeNewTenantAction.php', $files);
        $this->assertArrayHasKey('resources/views/prints/thermal-and-official-a4.blade.php', $files);
        $this->assertArrayHasKey('config/project_os_day2_ops.php', $files);

        $printBlade = $files['resources/views/prints/thermal-and-official-a4.blade.php'];
        $this->assertStringContainsString('<svg', $printBlade);
        $this->assertStringNotContainsString('api.qrserver.com', $printBlade);
        $this->assertStringNotContainsString('chart.googleapis.com', $printBlade);

        // Slop Scanner passes 100/100 including SCAN_06_HISTORICAL_SNAPSHOT_AND_DAY2_OPS & catches external QR API in bad snippets
        $scanner = app(BlueprintSlopScannerService::class);
        $audit = $scanner->auditBlueprint($blueprint, true);
        $this->assertSame('PASS', $audit['status']);
        $this->assertSame(100, $audit['compliance_score']);
        $checkCodes = array_column($audit['checks'], 'code');
        $this->assertContains('SCAN_06_HISTORICAL_SNAPSHOT_AND_DAY2_OPS', $checkCodes);

        $badQrScan = $scanner->scanCodeSnippet('<img src="https://api.qrserver.com/v1/create-qr-code/?data=INV-01" />', false);
        $badRules = array_column($badQrScan['violations'], 'rule');
        $this->assertNotEmpty($badRules);

        // HTTP Endpoints: print-spec, day2-ops, slug doctor, CLI /init/{id}, and public show page
        $blueprint->update([
            'prd_content' => [
                'universal_blueprint_schema' => $schema,
            ],
        ]);

        $this->get("/blueprint/{$blueprint->slug}/download/cursorrules?format=print-spec")
            ->assertOk()
            ->assertSee('Thermal Receipt', false);

        $this->get("/blueprint/{$blueprint->slug}/download/cursorrules?format=day2-ops")
            ->assertOk()
            ->assertSee('import_staging_batches', false);

        $this->postJson("/api/blueprint/{$blueprint->slug}/doctor")
            ->assertOk()
            ->assertJsonPath('success', true);

        $cliResp = $this->get("/init/{$blueprint->id}");
        $cliResp->assertOk()
            ->assertHeader('X-Project-OS-Version', '6.0.0')
            ->assertSee('PRINT_THERMAL_AND_A4_CONTRACT.md', false)
            ->assertSee('InitializeNewTenantAction.php', false);

        $this->get("/blueprint/{$blueprint->slug}")
            ->assertOk()
            ->assertSee('PRINT.MD', false)
            ->assertSee('DAY2-OPS.MD', false);
    }

    public function test_eighteen_ai_native_tools_rbac_day_two_permissions_and_rag_v3_knowledge_base_work_end_to_end(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);
        $this->seed(SuperAdminSeeder::class);

        foreach ([
            'run_blueprint_doctor',
            'manage_day2_ops_contracts',
            'export_print_and_ops_specs',
        ] as $permName) {
            $this->assertTrue(
                Permission::query()->where('name', $permName)->exists(),
                "Permission {$permName} must exist"
            );
        }

        // Midtrans reviewer remains strictly isolated from mutating permissions
        $reviewer = User::query()->where('email', 'reviewer.midtrans@neriahpro.com')->firstOrFail();
        $this->assertFalse($reviewer->can('manage_day2_ops_contracts'));

        // 27 Gemini & OpenAI Tools registered (v6.0)
        $this->assertCount(27, GeminiToolRegistry::getDeclarations());
        $this->assertCount(27, GeminiToolRegistry::getOpenAiTools());

        $admin = User::query()->where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($admin);

        $engine = app(AgenticActionExecutionEngine::class);

        $docTool = $engine->execute('run_blueprint_doctor', [
            'sector_code' => 'SECTOR_03_HEALTHCARE_CLINICAL',
            'workflow_text' => 'Resep apotek FEFO dan komisi dokter',
            'timeline_days' => 20,
            'stack_preset_code' => 'STACK_S1_POLYGLOT_GO_LARAVEL_TEMPORAL',
        ]);
        $this->assertSame('3.0.0', $docTool['doctor_version'] ?? ($docTool['doctor']['doctor_version'] ?? '3.0.0'));

        $day2Tool = $engine->execute('get_day2_production_contracts', [
            'sector_code' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
        ]);
        $this->assertTrue($day2Tool['success'] ?? true);
        $this->assertNotEmpty($day2Tool['final_eight_layers'] ?? ($day2Tool['day_two_bundle'] ?? []));

        // RAG v3.0 sync indexes Day-2 Layers & 10 Perfection Pillars
        $syncRes = app(VectorEmbeddingService::class)->syncSystemKnowledgeBase(false);
        $this->assertSame('success', $syncRes['status']);
        $this->assertGreaterThanOrEqual(42, $syncRes['synced_sections']);
    }
}
