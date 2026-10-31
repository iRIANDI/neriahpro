<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\PosSector;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\Ai\AgenticActionExecutionEngine;
use App\Services\Ai\GeminiToolRegistry;
use App\Services\Ai\VectorEmbeddingService;
use App\Services\BlueprintSlopScannerService;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Services\ScaffoldGeneratorService;
use Database\Seeders\PosStackPresetSeeder;
use Database\Seeders\ProjectOsMatrixSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectOsSectoralUiAndKillerFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response([
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

    public function test_all_twelve_sectors_carry_industrial_ui_archetypes_realistic_indonesian_seeders_and_six_killer_engines(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);

        $sectors = PosSector::query()->orderBy('sector_number')->get();
        $this->assertCount(12, $sectors);

        foreach ($sectors as $sector) {
            $this->assertSame(26, strlen($sector->id));
            $ui = $sector->resolvedUiArchetypes();
            $seeder = $sector->resolvedSeederContract();

            $this->assertNotEmpty($ui['primary_archetype'], "Sector {$sector->code} missing primary_archetype");
            $this->assertNotEmpty($ui['secondary_archetype'], "Sector {$sector->code} missing secondary_archetype");
            $this->assertNotEmpty($ui['wireframe_blueprint']['panes'] ?? [], "Sector {$sector->code} missing wireframe panes");
            $this->assertNotEmpty($ui['keyboard_shortcuts'] ?? [], "Sector {$sector->code} missing keyboard_shortcuts");
            $this->assertNotEmpty($seeder['seeder_class'] ?? '', "Sector {$sector->code} missing seeder_class");
            $this->assertNotEmpty($seeder['realistic_fixtures'] ?? [], "Sector {$sector->code} missing realistic_fixtures");
        }

        $catalog = ProjectOsBlueprintCompilerService::getCachedCatalog();
        $this->assertCount(12, $catalog['sectors']);
        $this->assertCount(6, $catalog['killer_features']);
        $this->assertSame('POS_SPLIT_TERMINAL', $catalog['sectors'][0]['ui_ux_archetypes']['primary_archetype']);
        $this->assertSame('TRANSPARENCY_FUND_TRACKER', $catalog['sectors'][11]['ui_ux_archetypes']['primary_archetype']);
    }

    public function test_compiler_scaffold_and_cli_bootstrapper_materialize_phased_prompts_realistic_seeder_and_verify_script_with_sha256_audit(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);

        $blueprint = VisionBlueprint::query()->create([
            'client_name' => 'dr. Bima Prakoso',
            'email' => 'bima.klinik@example.com',
            'phone' => '+6281199887766',
            'nama_bisnis' => 'Klinik Pratama Medika Nusantara',
            'masalah_utama' => 'Rekam medis pasien lambat karena harus pindah halaman dan stok obat batch sering kedaluwarsa.',
            'tujuan_utama' => 'Membangun EMR SOAP 3-pane cepat dengan FEFO stok apotek dan antrean poli real-time.',
            'target_audiens' => 'Dokter Poli, Apoteker, Resepsionis, dan Pasien.',
            'aktor_sistem' => "Dokter: Input SOAP & ICD-10\nApoteker: Dispense resep FEFO\nAdmin: Klaim BPJS",
            'fitur_wajib' => "Kokpit klinis 3 panel SOAP EMR\nManajemen stok obat FEFO per batch\nAudit log rekam medis immutable",
            'alur_kerja' => "1. Pasien daftar antrean\n2. Dokter isi SOAP & resep\n3. Apotek potong batch FEFO terdekat",
            'user_metadata' => [
                'primary_sector' => 'SECTOR_03_HEALTHCARE_CLINICAL',
                'selected_primitives' => ['PRIMITIVE_CLINICAL_EMR', 'PRIMITIVE_INVENTORY_LEDGER'],
                'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
            ],
        ]);
        $blueprint->generateAndSavePrd();

        $schema = ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => true]);
        $this->assertSame('CLINICAL_3_PANE_COCKPIT', $schema['sectoral_ui_ux_archetype']['primary_archetype']);
        $this->assertCount(7, $schema['phased_prompt_sequencer']);
        $this->assertSame('01-foundation.md', $schema['phased_prompt_sequencer'][0]['filename']);
        $this->assertSame('06-deployment.md', $schema['phased_prompt_sequencer'][5]['filename']);
        $this->assertSame('07-security-redteam.md', $schema['phased_prompt_sequencer'][6]['filename']);

        // Preview Scaffold files
        $previewFiles = ScaffoldGeneratorService::generateFiles($blueprint);
        $this->assertArrayHasKey('database/seeders/RealisticSectorSeeder.php', $previewFiles);
        $this->assertArrayHasKey('.agents/PONYTAIL.md', $previewFiles);
        $this->assertArrayHasKey('verify-blueprint.sh', $previewFiles);
        $this->assertArrayHasKey('.agents/prompts/01-foundation.md', $previewFiles);
        $this->assertArrayHasKey('.agents/prompts/04-verification-suite.md', $previewFiles);
        $this->assertArrayHasKey('.agents/prompts/05-sectoral-ui-ux.md', $previewFiles);
        $this->assertArrayHasKey('.agents/prompts/06-deployment.md', $previewFiles);
        $this->assertStringNotContainsString('Lorem ipsum', $previewFiles['database/seeders/RealisticSectorSeeder.php']);

        // Killer #6: One-Command CLI Workspace Bootstrapper (/init/{ULID})
        $initResponse = $this->get('/init/' . $blueprint->id);
        $initResponse->assertOk();
        $this->assertStringContainsString('text/x-shellscript', (string) $initResponse->headers->get('Content-Type'));
        $scriptBody = $initResponse->getContent();
        $this->assertStringContainsString('Materializing AGENTS.md', $scriptBody);
        $this->assertStringContainsString('Materializing .agents/prompts/01-foundation.md', $scriptBody);
        $this->assertStringContainsString('Materializing verify-blueprint.sh', $scriptBody);
        $this->assertStringContainsString('Materializing database/seeders/RealisticSectorSeeder.php', $scriptBody);

        // Verify SHA-256 AuditLog recorded for CLI bootstrap
        $audit = AuditLog::query()->where('action', 'cli_bootstrap_pulled')->latest('id')->first();
        $this->assertNotNull($audit);
        $this->assertSame('cli_bootstrap', $audit->event_category);
        $this->assertSame(64, strlen((string) $audit->integrity_hash));

        // Verify phase-4 & verify-blueprint download formats
        $phase4Resp = $this->get(route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'phase-4']));
        $phase4Resp->assertOk();
        $this->assertStringContainsString('FASE 4/6', $phase4Resp->getContent());

        $verifyShResp = $this->get(route('blueprint.download-cursorrules', ['slug' => $blueprint->slug, 'format' => 'verify-blueprint']));
        $verifyShResp->assertOk();
        $this->assertStringContainsString('#!/usr/bin/env bash', $verifyShResp->getContent());
    }

    public function test_brownfield_sql_importer_and_slop_scanner_detect_legacy_debt_and_slop_patterns(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);

        $legacySql = <<<'SQL'
CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    grand_total FLOAT DEFAULT 0,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);
CREATE TABLE journal_entries (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    debit DOUBLE NOT NULL
);
SQL;

        $resp = $this->postJson('/api/blueprint/brownfield-audit', [
            'schema_text' => $legacySql,
            'project_hint' => 'Sistem Akuntansi & Billing B2B',
        ]);

        $resp->assertOk()
            ->assertJsonPath('success', true);

        $payload = $resp->json();
        $this->assertGreaterThanOrEqual(2, $payload['detected_tables_count']);
        $violationCodes = array_column($payload['violations'], 'code');
        $this->assertContains('V01_AUTO_INCREMENT_PK', $violationCodes);
        $this->assertContains('V03_BLIND_CASCADE_DELETE', $violationCodes);
        $this->assertContains('V04_FLOAT_PRECISION_DRIFT', $violationCodes);
        $this->assertCount(4, $payload['zero_data_loss_migration_plan']);
        $this->assertNotEmpty($payload['ai_refactor_prompt']);

        $brownfieldAudit = AuditLog::query()->where('event_category', 'brownfield_import')->latest('id')->first();
        $this->assertNotNull($brownfieldAudit);
        $this->assertSame(64, strlen((string) $brownfieldAudit->integrity_hash));

        // Slop Scanner on bad snippet vs clean blueprint
        $scanner = app(BlueprintSlopScannerService::class);
        $badSnippet = '<button class="rounded-full bg-gradient-to-r from-pink-500 to-purple-500" onclick="window.alert(\'Hi\')">Save</button><?php User::paginate(10); $table->id();';
        $snippetScan = $scanner->scanCodeSnippet($badSnippet, true);

        $rulesHit = array_column($snippetScan['violations'], 'rule');
        $this->assertContains('UI_SLOP_CAPSULE', $rulesHit);
        $this->assertContains('UI_SLOP_GRADIENT', $rulesHit);
        $this->assertContains('NATIVE_BROWSER_DIALOG', $rulesHit);
        $this->assertContains('OFFSET_PAGINATION_SLOP', $rulesHit);
        $this->assertContains('DB_AUTO_INCREMENT_ID', $rulesHit);
        $this->assertLessThan(50, $snippetScan['compliance_score']);

        // Artisan pos:audit command runs cleanly
        $this->artisan('pos:audit --json')->assertExitCode(0);
    }

    public function test_sixteen_ai_native_tools_rbac_mutation_guards_and_rag_v25_sync_work_end_to_end(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);
        $this->seed(SuperAdminSeeder::class);

        // Verify RBAC permissions registered
        foreach ([
            'view_project_os_matrix',
            'manage_project_os_matrix',
            'execute_brownfield_importer',
            'run_slop_drift_scanner',
            'export_cli_bootstrapper',
            'execute_ai_native_tools',
            'export_audit_logs',
        ] as $permName) {
            $this->assertTrue(
                Permission::query()->where('name', $permName)->exists(),
                "Permission {$permName} must be seeded by SuperAdminSeeder"
            );
        }

        // Verify 24 valid Gemini & OpenAI Tool Declarations (Project OS v5.0)
        $geminiTools = GeminiToolRegistry::getDeclarations();
        $openAiTools = GeminiToolRegistry::getOpenAiTools();
        $this->assertCount(24, $geminiTools);
        $this->assertCount(24, $openAiTools);

        $engine = app(AgenticActionExecutionEngine::class);

        // Tool #13: get_sector_ui_archetype
        $archRes = $engine->execute('get_sector_ui_archetype', [
            'sector_code' => 'SECTOR_09_FNB_CULINARY',
        ]);
        $this->assertSame('KDS_TICKET_BOARD', $archRes['ui_ux_archetype']['primary_archetype']);

        // RBAC Guard: midtrans_reviewer cannot execute mutating AI tools
        $reviewer = User::query()->where('email', 'reviewer.midtrans@neriahpro.com')->firstOrFail();
        $this->actingAs($reviewer);

        $denied = $engine->execute('create_blueprint', [
            'nama_bisnis' => 'Unauthorized Test',
            'client_name' => 'Reviewer',
            'masalah_utama' => 'Test',
            'fitur_wajib' => 'Test',
        ]);
        $this->assertArrayHasKey('error', $denied);
        $this->assertTrue(AuditLog::query()->where('action', 'rbac_tool_denied')->exists());

        // Super Admin can execute mutating AI tools & sync Knowledge Base v2.5
        $admin = User::query()->where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($admin);

        $created = $engine->execute('create_blueprint', [
            'nama_bisnis' => 'Dapur Nusantara KDS',
            'client_name' => 'Chef Hendra',
            'masalah_utama' => 'Tiket pesanan dapur sering tercecer saat jam makan siang.',
            'fitur_wajib' => 'Kitchen Display System real-time dan denah meja kasir',
        ]);
        $this->assertTrue($created['success'] ?? false);

        // Tool #14 & #16 on newly created blueprint
        $phased = $engine->execute('generate_phased_prompts', [
            'blueprint_id' => $created['blueprint_id'],
        ]);
        $this->assertSame(7, $phased['total_phases']);

        $slopCheck = $engine->execute('run_blueprint_slop_scanner', [
            'blueprint_id' => $created['blueprint_id'],
        ]);
        $this->assertSame(100, $slopCheck['compliance_score']);
        $this->assertSame('PASS', $slopCheck['status']);

        // RAG v2.5 sync indexes 10 Pillars + 12 Sectoral UI + 9 Stacks + 6 Killer Engines
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response([
                'embedding' => ['values' => array_fill(0, 768, 0.0125)],
            ], 200),
        ]);
        $syncResult = app(VectorEmbeddingService::class)->syncSystemKnowledgeBase(false);
        $this->assertSame('success', $syncResult['status']);
        $this->assertGreaterThanOrEqual(35, $syncResult['synced_sections']);
    }
}

