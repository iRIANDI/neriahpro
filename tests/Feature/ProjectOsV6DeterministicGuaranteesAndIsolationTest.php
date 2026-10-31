<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Pages\ProjectOsMatrixPage;
use App\Filament\Pages\SmartGuideRetentionPage;
use App\Livewire\BlueprintArchitectStudio;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\PosProject;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\Ai\AgenticActionExecutionEngine;
use App\Services\Ai\GeminiToolRegistry;
use App\Services\Ai\VectorEmbeddingService;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Support\PosEnterpriseGuaranteeCatalog;
use App\Support\ProjectOsMatrixCatalog;
use Database\Seeders\PosStackPresetSeeder;
use Database\Seeders\ProjectOsMatrixSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectOsV6DeterministicGuaranteesAndIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            '*' => Http::response([
                'token' => 'snap-test-token-v60',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-test-token-v60',
                'embedding' => ['values' => array_fill(0, 768, 0.012)],
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

    public function test_jaminan_1_and_2_all_12_sectors_bloom_300_l1_l4_submenus_and_inject_5_enterprise_glue_tables(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);

        $sectors = ProjectOsMatrixCatalog::sectors();
        $this->assertCount(12, $sectors);

        $totalSubMenus = 0;
        foreach ($sectors as $sector) {
            $code = (string) $sector['code'];
            $matrix = PosEnterpriseGuaranteeCatalog::enterpriseMenuMatrixForSector($code);
            $this->assertSame($code, $matrix['sector_code']);
            $this->assertCount(5, $matrix['divisions'], "Sector {$code} must have 5 Operational Divisions");

            $sectorCount = 0;
            foreach ($matrix['divisions'] as $division) {
                $this->assertCount(5, $division['menus'], "Each division in {$code} must have 5 concrete L3 sub-menus");
                $sectorCount += count($division['menus']);
            }
            $this->assertSame(25, $sectorCount);
            $totalSubMenus += $sectorCount;
        }
        $this->assertSame(300, $totalSubMenus);

        // Even a minimal 5-word idea ("Buat aplikasi klub badminton") blooms 25 enterprise sub-menus + 5 Enterprise Glue tables
        $blueprint = VisionBlueprint::query()->create([
            'client_name' => 'Budi Hartono',
            'email' => 'budi@smashclub.id',
            'phone' => '+6281234567890',
            'nama_bisnis' => 'Smash Arena Badminton Club',
            'masalah_utama' => 'Buat aplikasi klub badminton',
            'tujuan_utama' => 'Reservasi lapangan badminton, kasir shuttlecock, dan member klub.',
            'target_audiens' => 'Member klub, kasir resepsionis, pelatih.',
            'aktor_sistem' => "Resepsionis: Booking lapangan\nMember: Pilih slot jam",
            'fitur_wajib' => 'Booking lapangan anti bentrok',
            'alur_kerja' => 'Pilih jam -> bayar -> main',
            'user_metadata' => [
                'primary_sector' => 'SECTOR_02_HOSPITALITY_SPACE',
                'selected_menus' => [
                    'SECTOR_02_D1_M01',
                    'SECTOR_02_D1_M02',
                    'SECTOR_02_D2_M01',
                    'SECTOR_02_D4_M01',
                    'SECTOR_02_D5_M01',
                ],
                'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
            ],
        ]);

        $schema = ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => true]);

        // Verify v1.0 vs v1.1-backlog menu scoping
        $menuMatrix = $schema['enterprise_menu_matrix'] ?? [];
        $this->assertSame(25, $menuMatrix['total_sub_menus']);
        $this->assertSame(5, $menuMatrix['v1_selected_count']);
        $this->assertSame(20, $menuMatrix['v1_1_backlog_count']);

        // Verify all 5 Enterprise Glue Primitives are automatically injected into data_dictionary
        $tableNames = array_column($schema['data_dictionary'] ?? [], 'table');
        foreach ([
            'sys_document_sequences',
            'sys_accounting_periods',
            'sys_approval_requests',
            'sys_reason_evidences',
            'sys_integration_logs',
        ] as $glueTable) {
            $this->assertContains($glueTable, $tableNames, "Enterprise Glue table {$glueTable} must be auto-injected");
        }
    }

    public function test_jaminan_3_4_and_5_kahn_dag_topological_sort_symbol_manifest_and_mandatory_verification_gates(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);

        // Live preview endpoint /api/blueprint/topological-preview
        $previewRes = $this->postJson('/api/blueprint/topological-preview', [
            'sector_code' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
            'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
            'app_name' => 'Neriah Retail ERP',
            'primitive_codes' => [
                'PRIMITIVE_RBAC_TENANCY',
                'PRIMITIVE_CATALOG_SKU',
                'PRIMITIVE_INVENTORY_LEDGER',
                'PRIMITIVE_DOUBLE_ENTRY_LEDGER',
                'PRIMITIVE_APPROVAL_WORKFLOW',
            ],
        ])->assertOk()
            ->assertJsonPath('success', true);

        $dag = $previewRes->json('topological_dag_plan');
        $manifest = $previewRes->json('symbol_manifest');
        $subPartsSummary = $previewRes->json('sub_chunked_prompts');

        // JAMINAN 4: Kahn's Algorithm puts anchor `organizations` in Layer 0 before dependent FK tables
        $this->assertNotEmpty($dag['layers']);
        $layer0Tables = $dag['layers'][0] ?? [];
        $this->assertContains('organizations', $layer0Tables);

        $orderedTables = $manifest['topological_migration_order'] ?? [];
        $orgIndex = array_search('organizations', $orderedTables, true);
        $this->assertNotFalse($orgIndex);

        foreach (($dag['dependency_edges'] ?? []) as $edge) {
            $parentIdx = array_search($edge['depends_on'], $orderedTables, true);
            $childIdx = array_search($edge['from_table'], $orderedTables, true);
            if ($parentIdx !== false && $childIdx !== false) {
                $this->assertLessThan(
                    $childIdx,
                    $parentIdx,
                    "Parent table {$edge['depends_on']} must precede child table {$edge['from_table']} in Kahn DAG order"
                );
            }
        }

        // Token-safe sub-chunks: max 5 entities per schema sub-part
        foreach ($dag['schema_sub_chunks'] as $chunk) {
            $this->assertLessThanOrEqual(5, count($chunk['tables']));
        }

        // JAMINAN 3: Exact Symbol Manifest Lock (`SYMBOL_MANIFEST.json`)
        $this->assertSame('1.0.0', $manifest['manifest_version']);
        $this->assertArrayHasKey('Organization', $manifest['entities']);
        $this->assertArrayHasKey('SysDocumentSequence', $manifest['entities']);
        foreach ($manifest['entities'] as $entityDef) {
            foreach (($entityDef['bound_actions'] ?? []) as $actionDef) {
                $this->assertStringContainsString('__invoke(', (string) ($actionDef['signature'] ?? ''));
            }
        }

        // JAMINAN 5: Every Sub-Part (`Part 1.0 .. Part 7.0`) embeds `<MANDATORY_VERIFICATION_GATE>` + Exit Code 0
        $this->assertGreaterThanOrEqual(7, count($subPartsSummary));
        $fullSchema = ProjectOsBlueprintCompilerService::compileSchemaFromPayload([
            'app_name' => 'Neriah Retail ERP',
            'sector_code' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
            'stack_preset_code' => 'STACK_M1_LARAVEL_MONOLITH',
        ]);
        foreach (($fullSchema['sub_chunked_prompts'] ?? []) as $sp) {
            $promptText = (string) ($sp['prompt'] ?? '');
            $this->assertStringContainsString('<MANDATORY_VERIFICATION_GATE>', $promptText);
            $this->assertStringContainsString('VERIFIED — ZERO ERRORS', $promptText);
            $this->assertStringContainsString('SYMBOL_MANIFEST', $promptText);
        }
    }

    public function test_hermetic_project_isolation_prevents_any_cross_project_remnants_or_overwrites(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);

        // 1. Submit Project A ("Klinik Sentosa") via public API
        $resA = $this->postJson('/api/vision-blueprint', [
            'client_name' => 'dr. Andi Wijaya',
            'email' => 'andi@kliniksentosa-a.id',
            'phone' => '+628111000111',
            'nama_bisnis' => 'Klinik Sentosa',
            'masalah_utama' => 'Antrean poli manual dan rekam medis kertas sering tercecer di cabang Jakarta.',
            'tujuan_utama' => 'Digitalisasi EMR SOAP dan stok obat apotek cabang Jakarta.',
            'target_audiens' => 'Dokter umum, perawat, kasir klinik Jakarta.',
            'aktor_sistem' => "Dokter: Input SOAP\nApoteker: Resep obat",
            'fitur_wajib' => 'EMR SOAP Jakarta & Stok Apotek',
            'alur_kerja' => 'Daftar -> Poli -> Apotek -> Kasir',
            'primary_sector' => 'SECTOR_03_HEALTHCARE_CLINICAL',
            'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
        ])->assertStatus(201);

        $slugA = (string) $resA->json('data.slug');
        $bpA = VisionBlueprint::query()->where('slug', $slugA)->firstOrFail();
        $stagingA = $bpA->provisionStagingUrl();
        $projA = PosProject::query()->where('vision_blueprint_id', $bpA->id)->firstOrFail();

        // 2. Fresh arrival on GET /blueprint?tier=retail_pro MUST NOT leak Project A's session/data
        $freshPage = $this->get('/blueprint?tier=retail_pro')->assertOk();
        $freshHtml = $freshPage->getContent();
        $this->assertStringNotContainsString('Antrean poli manual dan rekam medis kertas sering tercecer di cabang Jakarta', $freshHtml);
        $this->assertStringContainsString('is_fresh_workspace', $freshHtml);
        $this->assertStringContainsString('draft_token', $freshHtml);

        // 3. Auto-save on a new workspace (even if client passes Project A's slug without `is_explicit_slug_edit`) MUST NEVER mutate Project A in DB
        $this->postJson('/api/blueprint/autosave', [
            'slug' => $bpA->slug,
            'draft_id' => 'draft_new_isolated_client',
            'is_explicit_slug_edit' => false,
            'blueprint' => [
                'namaBisnis' => 'Proyek Baru Klien B',
                'masalahUtama' => 'Masalah baru gudang logistik Surabaya',
            ],
        ])->assertOk()
            ->assertJsonPath('persisted_to_db', false);

        $bpA->refresh();
        $this->assertSame('Klinik Sentosa', $bpA->nama_bisnis);
        $this->assertStringContainsString('cabang Jakarta', $bpA->masalah_utama);

        // 4. Submit Project B with the EXACT SAME business name ("Klinik Sentosa") for a different clinic!
        //    Must get unique ULID, unique slug, unique staging URL, and its OWN separate PosProject (NEVER overwriting Project A!)
        $resB = $this->postJson('/api/vision-blueprint', [
            'client_name' => 'dr. Siska Pratama',
            'email' => 'siska@kliniksentosa-b.id',
            'phone' => '+628222000222',
            'nama_bisnis' => 'Klinik Sentosa',
            'masalah_utama' => 'Manajemen klaim laboratorium diagnostik cabang Medan belum otomatis.',
            'tujuan_utama' => 'Otomasi hasil lab & tagihan korporat cabang Medan.',
            'target_audiens' => 'Analis lab, dokter patologi, admin klaim Medan.',
            'aktor_sistem' => "Analis: Validasi hasil lab\nKasir: Tagihan B2B",
            'fitur_wajib' => 'LIS Laboratorium Medan & Billing Korporat',
            'alur_kerja' => 'Order lab -> Sampel -> Validasi -> Kirim hasil PDF',
            'primary_sector' => 'SECTOR_03_HEALTHCARE_CLINICAL',
            'stack_preset' => 'STACK_M2_LARAVEL_INERTIA_REACT',
        ])->assertStatus(201);

        $slugB = (string) $resB->json('data.slug');
        $bpB = VisionBlueprint::query()->where('slug', $slugB)->firstOrFail();
        $stagingB = $bpB->provisionStagingUrl();
        $projB = PosProject::query()->where('vision_blueprint_id', $bpB->id)->firstOrFail();

        $this->assertNotSame($bpA->id, $bpB->id);
        $this->assertNotSame($bpA->slug, $bpB->slug);
        $this->assertNotSame($stagingA, $stagingB);
        $this->assertNotSame($projA->id, $projB->id);
        $this->assertNotSame($projA->isolation_token, $projB->isolation_token);
        $this->assertSame(2, PosProject::query()->count(), 'Project B with identical business name must NOT overwrite Project A in pos_projects');

        // 5. Mounting 3-Pane Architect Studio WITHOUT `{slug}` MUST start 100% clean (never loading Project B or Project A!)
        Livewire::test(BlueprintArchitectStudio::class)
            ->assertSet('blueprintId', null)
            ->assertSet('posProjectId', null)
            ->assertSet('slug', null)
            ->assertSet('projectName', 'Proyek Enterprise Baru');

        // Mounting WITH explicit slug loads that exact project, and clicking `startFreshIsolatedProject` wipes all remnants cleanly
        Livewire::test(BlueprintArchitectStudio::class, ['slug' => $bpA->slug])
            ->assertSet('blueprintId', $bpA->id)
            ->assertSet('projectName', 'Klinik Sentosa')
            ->call('startFreshIsolatedProject')
            ->assertSet('blueprintId', null)
            ->assertSet('posProjectId', null)
            ->assertSet('slug', null)
            ->assertSet('projectName', 'Proyek Enterprise Baru');
    }

    public function test_all_6_subsystems_smart_guide_rbac_27_ai_tools_rag_audit_log_cli_and_backend_matrix_v6(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);
        $this->seed(SuperAdminSeeder::class);

        // Subsystem #2: RBAC Permissions (3 new v6.0 permissions seeded, Midtrans reviewer strictly denied)
        foreach ([
            'manage_enterprise_menu_matrix',
            'compile_symbol_manifest_and_dag_chunks',
            'override_fiscal_and_maker_checker_locks',
        ] as $perm) {
            $this->assertTrue(Permission::query()->where('name', $perm)->exists(), "Missing v6.0 permission: {$perm}");
        }

        $reviewer = User::query()->where('email', 'reviewer.midtrans@neriahpro.com')->firstOrFail();
        $admin = User::query()->where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();

        $this->assertFalse($reviewer->can('manage_enterprise_menu_matrix'));
        $this->assertFalse($reviewer->can('override_fiscal_and_maker_checker_locks'));
        $this->assertTrue($admin->can('manage_enterprise_menu_matrix'));
        $this->assertTrue($admin->can('compile_symbol_manifest_and_dag_chunks'));

        // Subsystem #3: 27 Native AI Tools + RAG Vector Knowledge Base v6.0
        $this->assertCount(27, GeminiToolRegistry::getDeclarations());
        $this->assertCount(27, GeminiToolRegistry::getOpenAiTools());

        $vectorService = app(VectorEmbeddingService::class);
        $syncRes = $vectorService->syncSystemKnowledgeBase(true);
        $this->assertSame('success', $syncRes['status']);

        $retrieved = $vectorService->retrieveRelevantContext('SYMBOL_MANIFEST Kahn Topological Sub-Chunker Hermetic Project Isolation', 4);
        $this->assertNotEmpty($retrieved);

        $this->actingAs($admin);
        $engine = app(AgenticActionExecutionEngine::class);

        // Tool #25: expand_l1_l4_enterprise_menu_matrix
        $menuRes = $engine->execute('expand_l1_l4_enterprise_menu_matrix', [
            'sector_code' => 'SECTOR_05_MANUFACTURING_MRP',
        ]);
        $this->assertTrue((bool) ($menuRes['success'] ?? false));
        $this->assertSame(25, $menuRes['total_sub_menus']);
        $this->assertCount(5, $menuRes['enterprise_glue_primitives']);

        // Tool #26: compile_symbol_manifest_and_dag_chunks
        $dagRes = $engine->execute('compile_symbol_manifest_and_dag_chunks', [
            'sector_code' => 'SECTOR_04_FINTECH_LEDGER',
            'project_name' => 'Neriah Core Ledger',
            'max_entities_per_part' => 5,
        ]);
        $this->assertTrue((bool) ($dagRes['success'] ?? false));
        $this->assertGreaterThanOrEqual(7, $dagRes['sub_chunked_parts_count']);

        // Tool #27: verify_project_isolation_and_clean_state
        $isoRes = $engine->execute('verify_project_isolation_and_clean_state', []);
        $this->assertTrue((bool) ($isoRes['success'] ?? false));
        $this->assertTrue((bool) ($isoRes['hermetic_isolation_healthy'] ?? false));

        // Subsystem #4: Forensic Audit Log categories (`enterprise_glue`, `symbol_manifest`, `project_isolation`)
        $this->assertTrue(AuditLog::query()->where('event_category', 'enterprise_glue')->exists());
        $this->assertTrue(AuditLog::query()->where('event_category', 'symbol_manifest')->exists());
        $this->assertTrue(AuditLog::query()->where('event_category', 'project_isolation')->exists());

        // Subsystem #1 & #6: Smart Guide Backend Admin (Section 13) & ProjectOsMatrixPage (`guarantees_v6` tab)
        Livewire::test(SmartGuideRetentionPage::class)
            ->assertSee('5 Deterministic Guarantee Engines', false)
            ->assertSee('SYMBOL_MANIFEST.json', false);

        Livewire::test(ProjectOsMatrixPage::class)
            ->set('activeTab', 'guarantees_v6')
            ->set('decisionPreviewSector', 'SECTOR_06_LOGISTICS_FLEET')
            ->assertSee('GUARANTEE_01_L1_L4_ENTERPRISE_MENU_MATRIX', false)
            ->assertSee('sys_document_sequences', false);

        // CLI `/init/{slug}` & Download Formats (`symbol-manifest`, `enterprise-menu`, `subpart-1.0`)
        $bp = VisionBlueprint::query()->create([
            'client_name' => 'PT Nusantara Fleet',
            'email' => 'ops@nusantarafleet.id',
            'phone' => '+6281399887766',
            'nama_bisnis' => 'Nusantara Fleet TMS',
            'masalah_utama' => 'Pelacakan manifes truk antar pulau belum terintegrasi jurnal akuntansi.',
            'tujuan_utama' => 'TMS Waypoint + PoD + Double-Entry Ledger otomatis.',
            'target_audiens' => 'Dispatcher, Supir, Finance.',
            'aktor_sistem' => "Dispatcher: Buat manifes\nSupir: Upload PoD",
            'fitur_wajib' => 'Manifes Pengiriman & Jurnal Otomatis',
            'alur_kerja' => 'Order -> Manifes -> Waypoint -> PoD -> Invoice',
            'is_published' => true,
            'user_metadata' => [
                'primary_sector' => 'SECTOR_06_LOGISTICS_FLEET',
                'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
            ],
        ]);
        $bp->generateAndSavePrd();

        $this->get("/blueprint/{$bp->slug}/download/cursorrules?format=symbol-manifest")
            ->assertOk()
            ->assertSee('"manifest_version": "1.0.0"', false);

        $this->get("/blueprint/{$bp->slug}/download/cursorrules?format=enterprise-menu")
            ->assertOk()
            ->assertSee('ENTERPRISE MENU & CAPABILITY MATRIX', false);

        $this->get("/blueprint/{$bp->slug}/download/cursorrules?format=subpart-1.0")
            ->assertOk()
            ->assertSee('<MANDATORY_VERIFICATION_GATE', false);

        $this->get("/init/{$bp->slug}")
            ->assertOk()
            ->assertSee('.agents/SYMBOL_MANIFEST.json', false)
            ->assertSee('.agents/ENTERPRISE_MENU_MATRIX.md', false)
            ->assertSee('.agents/prompts/subparts/', false);
    }
}
