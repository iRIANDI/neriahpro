<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Concerns\HasTranslatableTabs;
use App\Filament\Pages\ProjectOsMatrixPage;
use App\Filament\Pages\SmartGuideRetentionPage;
use App\Filament\Resources\PosArchitecturePillars\PosArchitecturePillarResource;
use App\Livewire\BlueprintArchitectStudio;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\PosArchitecturePillar;
use App\Models\PosProject;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\Ai\AgenticActionExecutionEngine;
use App\Services\Ai\GeminiToolRegistry;
use App\Services\Ai\VectorEmbeddingService;
use App\Services\BlueprintSlopScannerService;
use App\Services\PosCommercialBridgeService;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Services\ScaffoldGeneratorService;
use App\Support\PosEnterpriseGuaranteeCatalog;
use Database\Seeders\PosArchitecturePillarSeeder;
use Database\Seeders\PosStackPresetSeeder;
use Database\Seeders\ProjectOsMatrixSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectOsV7RealWorldFreezeAndTier1LocaleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            '*' => Http::response([
                'embedding' => ['values' => array_fill(0, 768, 0.015)],
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

        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);
        $this->seed(PosArchitecturePillarSeeder::class);
        $this->seed(SuperAdminSeeder::class);
    }

    public function test_master_15_pillars_seed_with_ulid_and_cache_auto_invalidation_plus_rbac_isolation(): void
    {
        $this->assertSame(15, PosArchitecturePillar::query()->count());

        $first = PosArchitecturePillar::query()->where('pillar_number', 1)->firstOrFail();
        $this->assertSame(26, strlen((string) $first->id));
        $this->assertIsArray($first->name);
        $this->assertArrayHasKey('id', $first->name);
        $this->assertArrayHasKey('en', $first->name);

        $cachedBefore = PosArchitecturePillar::getCachedMatrix();
        $this->assertCount(15, $cachedBefore);

        // Mutate pillar title -> booted() observer must flush cache immediately without overwriting admin edits
        $first->update([
            'name' => [
                'id' => 'Intake Multi-Modal Diperbarui',
                'en' => 'Updated Multi-Modal Intake',
            ],
        ]);

        $cachedAfter = PosArchitecturePillar::getCachedMatrix();
        $this->assertSame('Intake Multi-Modal Diperbarui', $cachedAfter[0]['name']['id']);
        $this->assertSame('Updated Multi-Modal Intake', $cachedAfter[0]['name']['en']);

        // RBAC Check on PosArchitecturePillarResource
        $admin = User::query()->where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $reviewer = User::query()->where('email', 'reviewer.midtrans@neriahpro.com')->firstOrFail();

        $this->actingAs($admin);
        $this->assertTrue(PosArchitecturePillarResource::canAccess());

        $this->actingAs($reviewer);
        $this->assertFalse(PosArchitecturePillarResource::canAccess());
    }

    public function test_five_real_world_elements_scaffold_cli_init_and_printable_sop_work_end_to_end(): void
    {
        $blueprint = VisionBlueprint::query()->create([
            'nama_bisnis' => 'Nusantara Retail POS & Apotek',
            'slug' => 'nusantara-retail-pos-v7',
            'client_name' => 'Budi Santoso',
            'email' => 'budi@nusantarapos.id',
            'phone' => '+6281234567899',
            'project_status' => 'Draft',
            'prd_content' => ['ringkasan_eksekutif' => 'Sistem POS multi-cabang dengan scanner barcode dan layar pelanggan QRIS.'],
            'user_metadata' => [
                'primary_sector' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
                'selected_primitives' => [
                    'PRIMITIVE_RBAC_TENANCY',
                    'PRIMITIVE_CATALOG_SKU',
                    'PRIMITIVE_INVENTORY_LEDGER',
                    'PRIMITIVE_DOUBLE_ENTRY_LEDGER',
                ],
                'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
            ],
        ]);

        $schema = ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, [
            'persist_project' => true,
        ]);

        // Verify v7.0 contracts in compiled schema
        $this->assertSame('7.0.0-architectural-freeze-15-pillars', $schema['guarantees_release']);
        $this->assertSame(100, $schema['architectural_freeze']['score']);
        $this->assertCount(15, $schema['fifteen_architecture_pillars']);
        $this->assertTrue($schema['hardware_iot_peripheral_contract']['barcode_rfid_wedge']['enabled']);
        $this->assertTrue($schema['hardware_iot_peripheral_contract']['customer_dual_screen']['enabled']);
        $this->assertTrue($schema['realtime_websocket_contract']['strict_zero_high_freq_polling']);
        $this->assertStringContainsString('Model::shouldBeStrict', $schema['strict_query_and_partition_contract']['eloquent_strict_mode_boot']);

        // Verify Tenant Composite Indexes auto-injected on tenant tables
        $tenantTablesWithCompositeIdx = array_filter(
            $schema['data_dictionary'] ?? [],
            fn (array $t): bool => ! empty($t['composite_indexes'])
        );
        $this->assertNotEmpty($tenantTablesWithCompositeIdx);

        // Verify PosProject persisted v7.0 columns
        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->firstOrFail();
        $this->assertSame(100, (int) $posProject->architectural_freeze_score);
        $this->assertNotEmpty($posProject->hardware_iot_contract_json);
        $this->assertNotEmpty($posProject->websocket_contract_json);
        $this->assertNotEmpty($posProject->query_partition_contract_json);
        $this->assertNotEmpty($posProject->client_sop_onboarding_json);
        $this->assertNotEmpty($posProject->syntax_lock_version);

        // Verify ScaffoldGeneratorService outputs all 6 v7.0 files
        $files = ScaffoldGeneratorService::generateFiles($blueprint);
        $paths = array_keys($files);
        $this->assertContains('.agents/SYNTAX_LOCK_2026.md', $paths);
        $this->assertContains('.agents/TROUBLESHOOTING_INSTINCTS.md', $paths);
        $this->assertContains('.agents/CLIENT_SOP_AND_TRAINING_MANUAL.md', $paths);
        $this->assertContains('database/sql/pg16_range_partitioning.sql', $paths);
        $this->assertContains('resources/js/peripherals/barcode-wedge-and-dual-screen.js', $paths);
        $this->assertContains('app/Filament/Pages/SmartGuide.php', $paths);

        // Authenticate as super admin for protected blueprint downloads & printable A4 SOP BAST
        $admin = User::query()->where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($admin);

        // Verify CLI /init/{id} & artifact download endpoints + Printable A4 SOP BAST
        $this->get("/init/{$blueprint->id}")
            ->assertOk()
            ->assertHeader('X-Project-OS-Version', '9.0.0')
            ->assertSee('SYNTAX_LOCK_2026.md', false)
            ->assertSee('CLIENT_SOP_AND_TRAINING_MANUAL.md', false)
            ->assertSee('pg16_range_partitioning.sql', false)
            ->assertSee('barcode-wedge-and-dual-screen.js', false);

        $this->get("/blueprint/{$blueprint->slug}/download/cursorrules?format=syntax-lock")
            ->assertOk()
            ->assertSee('SYNTAX_LOCK_2026', false);

        $this->get("/blueprint/{$blueprint->slug}/download/cursorrules?format=client-sop")
            ->assertOk()
            ->assertSee('SOP', false);

        $this->get("/blueprint/{$blueprint->slug}/download/cursorrules?format=pg16-partition")
            ->assertOk()
            ->assertSee('PARTITION BY RANGE', false);

        $this->get("/blueprint/{$blueprint->slug}/mitra-document/sop-training")
            ->assertOk()
            ->assertSee('Nusantara Retail POS', false);
    }

    public function test_cross_stage_causality_dynamically_updates_hardware_websocket_sop_rab_and_scope_hash(): void
    {
        $menuMatrix = PosEnterpriseGuaranteeCatalog::enterpriseMenuMatrixForSector('SECTOR_01_COMMERCE_OMNICHANNEL');
        $allMenus25 = array_column($menuMatrix['all_menus_flat'], 'code');
        $trimmedMenus10 = array_slice($allMenus25, 0, 10);

        $schemaFull = ProjectOsBlueprintCompilerService::compileUniversalSchema(null, [
            'sector_code' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
            'primitive_codes' => ['PRIMITIVE_RBAC_TENANCY', 'PRIMITIVE_CATALOG_SKU', 'PRIMITIVE_INVENTORY_LEDGER'],
            'selected_menus' => $allMenus25,
            'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
            'persist_project' => false,
        ]);

        $schemaTrimmed = ProjectOsBlueprintCompilerService::compileUniversalSchema(null, [
            'sector_code' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
            'primitive_codes' => ['PRIMITIVE_RBAC_TENANCY', 'PRIMITIVE_CATALOG_SKU', 'PRIMITIVE_INVENTORY_LEDGER'],
            'selected_menus' => $trimmedMenus10,
            'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
            'persist_project' => false,
        ]);

        $bridge = app(PosCommercialBridgeService::class);
        $quoteFull = $bridge->calculateScopeQuotation($schemaFull);
        $quoteTrimmed = $bridge->calculateScopeQuotation($schemaTrimmed);

        // Fewer active v1.0 menus MUST reduce quotation IDR & produce a distinct SHA-256 scope hash!
        $this->assertGreaterThan($quoteTrimmed['quotation_idr'], $quoteFull['quotation_idr']);
        $this->assertNotSame($quoteFull['scope_hash_full'], $quoteTrimmed['scope_hash_full']);
        $this->assertGreaterThan(
            $schemaTrimmed['client_sop_and_onboarding']['active_v1_menus_covered'],
            $schemaFull['client_sop_and_onboarding']['active_v1_menus_covered']
        );

        // Switching Stack M1 -> S1 & Sector 01 -> 07 updates Biometric IoT & NATS WebSocket engine
        $schemaMicro = ProjectOsBlueprintCompilerService::compileUniversalSchema(null, [
            'sector_code' => 'SECTOR_07_EDUCATION_LMS',
            'primitive_codes' => ['PRIMITIVE_RBAC_TENANCY'],
            'stack_preset' => 'STACK_S1_POLYGLOT_GO_LARAVEL_TEMPORAL',
            'persist_project' => false,
        ]);
        $this->assertTrue($schemaMicro['hardware_iot_peripheral_contract']['biometric_and_turnstile_iot']['enabled']);
        $this->assertStringContainsString('NATS', $schemaMicro['realtime_websocket_contract']['engine']);
    }

    public function test_tier_1_multi_language_compliance_has_zero_page_corner_locale_switchers_and_auto_translates(): void
    {
        $filamentFiles = File::allFiles(app_path('Filament'));
        foreach ($filamentFiles as $file) {
            if ($file->getFilename() === 'SmartGuideRetentionPage.php') {
                continue;
            }
            $contents = File::get($file->getPathname());
            $this->assertStringNotContainsString(
                'LocaleSwitcher::make',
                $contents,
                "Forbidden page-corner LocaleSwitcher found in {$file->getRelativePathname()}"
            );
            $this->assertStringNotContainsString(
                'LaraZeus\\SpatieTranslatable',
                $contents,
                "Forbidden LaraZeus SpatieTranslatable found in {$file->getRelativePathname()}"
            );
        }

        // Verify HasTranslatableTabs auto-populates missing EN from ID seamlessly
        $harness = new class {
            use HasTranslatableTabs;

            public function exposeAutoTranslate(array $data): array
            {
                return $this->autoTranslateMissingLocales($data);
            }
        };

        $filled = $harness->exposeAutoTranslate([
            'name' => [
                'id' => 'Sistem Kasir Enterprise Terpadu',
                'en' => '',
            ],
        ]);

        $this->assertNotEmpty($filled['name']['en']);
    }

    public function test_all_seven_subsystems_30_ai_tools_slop_scanner_audit_log_smart_guide_and_studios_pass(): void
    {
        // Subsystem #2: 3 new RBAC permissions seeded; midtrans_reviewer strictly isolated
        foreach ([
            'manage_15_architecture_pillars',
            'configure_hardware_and_websocket_contracts',
            'generate_client_sop_and_onboarding_pack',
        ] as $perm) {
            $this->assertTrue(Permission::query()->where('name', $perm)->exists(), "Missing permission {$perm}");
        }

        $admin = User::query()->where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $reviewer = User::query()->where('email', 'reviewer.midtrans@neriahpro.com')->firstOrFail();

        $this->assertTrue($admin->can('manage_15_architecture_pillars'));
        $this->assertFalse($reviewer->can('manage_15_architecture_pillars'));

        // Subsystem #3: 40 Native Tools & valid JSON object map for every Gemini tool declaration (zero HTTP 400 list-to-map bug)
        $declarations = GeminiToolRegistry::getDeclarations();
        $this->assertCount(40, $declarations);
        $this->assertCount(40, GeminiToolRegistry::getOpenAiTools());

        foreach ($declarations as $idx => $decl) {
            $encoded = json_encode($decl['parameters']);
            $this->assertStringNotContainsString('"properties":[]', (string) $encoded, "Tool #{$idx} ({$decl['name']}) has empty array list for properties!");
        }

        $engine = app(AgenticActionExecutionEngine::class);

        // RBAC Mutation Guard blocks reviewer when persist_sync=true on Tool #30
        $this->actingAs($reviewer);
        $denied = $engine->execute('audit_fifteen_pillars_architectural_freeze', [
            'persist_sync' => true,
        ]);
        $this->assertArrayHasKey('error', $denied);

        // Super Admin executes Tools #28, #29, #30 + RAG retrieval + AuditLog SHA-256
        $this->actingAs($admin);
        $res28 = $engine->execute('inspect_hardware_and_websocket_runtime_contracts', [
            'sector_code' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
            'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
        ]);
        $this->assertTrue($res28['success'] ?? false, json_encode($res28));

        $res29 = $engine->execute('generate_syntax_lock_and_client_sop_pack', [
            'sector_code' => 'SECTOR_03_HEALTHCARE_CLINICAL',
            'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
            'locale' => 'en',
        ]);
        $this->assertTrue($res29['success'] ?? false, json_encode($res29));
        $this->assertStringContainsString('SYNTAX_LOCK_2026', $res29['syntax_lock_markdown_preview']);

        $res30 = $engine->execute('audit_fifteen_pillars_architectural_freeze', [
            'persist_sync' => true,
        ]);
        $this->assertTrue($res30['success'] ?? false, json_encode($res30));
        $this->assertSame(15, $res30['pillars_count']);

        // Subsystem #4: Verify SHA-256 Forensic AuditLog categories
        $log = AuditLog::record(
            action: 'v7_architectural_freeze_verified',
            model: PosArchitecturePillar::query()->first(),
            newValues: ['pillars' => 15, 'score' => 100],
            description: 'Verifikasi 15 Pilar Architectural Freeze v7.0',
            eventCategory: 'pillars_freeze_100'
        );
        $this->assertNotNull($log);
        $this->assertSame(64, strlen((string) $log->integrity_hash));

        // RAG Vector Knowledge Base indexes 15 Pillars & 5 Real-World Elements
        $vector = app(VectorEmbeddingService::class);
        $vector->syncSystemKnowledgeBase(true);
        $ragHits = $vector->retrieveRelevantContext('SYNTAX_LOCK_2026 Zero-Polling WebSocket 15 Pillars Freeze', 4);
        $this->assertNotEmpty($ragHits);

        // Slop Scanner catches wire:poll.2s and LocaleSwitcher::make()
        $scanner = app(BlueprintSlopScannerService::class);
        $slopScan = $scanner->scanCodeSnippet('<div wire:poll.2s="refreshBoard">{{ LocaleSwitcher::make() }}</div>', false);
        $this->assertGreaterThanOrEqual(2, $slopScan['violations_count']);

        // Subsystem #1 & #6: SmartGuide Section 14, ProjectOsMatrixPage freeze_15_pillars tab, and 3-Pane Studio v7.0
        Livewire::test(SmartGuideRetentionPage::class)
            ->assertOk()
            ->assertSee('100% Architectural Freeze', false);

        Livewire::test(ProjectOsMatrixPage::class)
            ->call('setTab', 'freeze_15_pillars')
            ->assertSet('activeTab', 'freeze_15_pillars')
            ->assertSee('PILLAR_01', false);

        Livewire::test(BlueprintArchitectStudio::class)
            ->set('centerTab', 'runtime_sop_v7')
            ->set('rightDockMode', 'syntax_lock_md')
            ->assertSee('SYNTAX_LOCK_2026', false);
    }
}
