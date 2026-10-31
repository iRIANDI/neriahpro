<?php

namespace Tests\Feature;

use App\Livewire\BlueprintArchitectStudio;
use App\Livewire\Studio\BlueprintWorkbench;
use App\Models\AuditLog;
use App\Models\CmsGlobalSetting;
use App\Models\Document;
use App\Models\Permission;
use App\Models\PosChangeRequest;
use App\Models\PosProject;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\Ai\AgenticActionExecutionEngine;
use App\Services\Ai\GeminiToolRegistry;
use App\Services\Ai\VectorEmbeddingService;
use App\Services\BlueprintDeltaEvolutionService;
use App\Services\PosCommercialBridgeService;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Support\PricingRegistry;
use Database\Seeders\PosStackPresetSeeder;
use Database\Seeders\ProjectOsMatrixSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectOsMitraCommercialAndSatuPintuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            '*' => Http::response([
                'token' => 'snap-test-token-v50',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-test-token-v50',
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
    }

    private function createSampleMitraBlueprint(User $owner): VisionBlueprint
    {
        $blueprint = VisionBlueprint::query()->create([
            'client_name' => 'dr. Ratna Kusuma, MARS',
            'email' => $owner->email,
            'phone' => '+6281199887766',
            'nama_bisnis' => 'RSIA Sentra Medika & Klinik Terpadu',
            'masalah_utama' => 'Rekam medis SOAP tersebar, stok obat apotek kadaluarsa tidak terlacak FEFO, dan klaim asuransi lambat.',
            'tujuan_utama' => 'Membangun Clinical 3-Pane Cockpit + Apotek Batch/FEFO + Buku Besar Double-Entry + Klaim Asuransi.',
            'target_audiens' => 'Dokter Spesialis, Perawat Poli, Apoteker, Kasir Rawat Jalan, Manajemen RS.',
            'aktor_sistem' => "Dokter: Input SOAP & Resep FEFO\nApoteker: Dispense batch obat\nKasir: Split bill klaim & tunai",
            'fitur_wajib' => "Clinical 3-Pane Cockpit\nApotek FEFO Batch Lock\nJurnal Double-Entry Immutable",
            'alur_kerja' => "1. Antrean poli\n2. Pemeriksaan SOAP & cek alergi\n3. Kunci stok batch FEFO FOR UPDATE & jurnal pembayaran",
            'staging_url' => 'https://staging-sentramedika.neriahpro.com',
            'is_published' => true,
            'user_metadata' => [
                'primary_sector' => 'SECTOR_03_HEALTHCARE_CLINICAL',
                'selected_primitives' => [
                    'PRIMITIVE_RBAC_TENANCY',
                    'PRIMITIVE_APPOINTMENT_AVAILABILITY',
                    'PRIMITIVE_INVENTORY_LEDGER',
                    'PRIMITIVE_DOUBLE_ENTRY_LEDGER',
                    'PRIMITIVE_AUDIT_IMMUTABILITY',
                ],
                'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
            ],
        ]);

        $schema = ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => true]);
        $blueprint->update([
            'prd_content' => [
                'universal_blueprint_schema' => $schema,
            ],
        ]);

        return $blueprint->fresh();
    }

    public function test_satu_pintu_pricing_registry_propagates_dynamic_admin_settings_and_matrix_catalog(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);

        CmsGlobalSetting::setVal('mitra_man_hour_rate_idr', '400000');
        CmsGlobalSetting::setVal('pricing_advisory_price', '2.750.000');
        CmsGlobalSetting::setVal('pricing_umkm_price', '3.950.000');

        $estimator = PricingRegistry::getCommercialEstimatorConfig();
        $this->assertSame(400000, $estimator['man_hour_rate_idr']);

        $catalog = PricingRegistry::getSatuPintuCatalog();
        $this->assertCount(7, $catalog);
        $this->assertSame('RETAIL_SELF_DEV', $catalog[PricingRegistry::TIER_RETAIL_LITE]['track_mode']);
        $this->assertSame('MITRA_TURNKEY_PROJECT', $catalog[PricingRegistry::TIER_FULL_MVP]['track_mode']);
        $this->assertSame(2750000.0, PricingRegistry::getBaseAmount(PricingRegistry::TIER_BLUEPRINT_ADVISORY));
        $this->assertSame(3950000.0, PricingRegistry::getBaseAmount(PricingRegistry::TIER_UMKM_STARTER));

        $res = $this->getJson('/api/blueprint/matrix-catalog')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertCount(12, $res->json('catalog.sectors'));
        $this->assertCount(5, $res->json('commercial_bridge_features'));
        $this->assertCount(5, $res->json('guided_client_journey'));
        $this->assertCount(7, $res->json('satu_pintu_packages'));
        $this->assertSame(400000, $res->json('commercial_estimator_config.man_hour_rate_idr'));
    }

    public function test_feature_1_and_2_algorithmic_quotation_wbs_and_cryptographic_spk_scope_freeze(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);
        $this->seed(SuperAdminSeeder::class);

        // Compare M1 (1.0x) vs S1 Polyglot (1.5x) via API
        $m1Res = $this->postJson('/api/blueprint/commercial-estimate', [
            'sector_code' => 'SECTOR_04_FINTECH_LEDGER',
            'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
            'termin_scheme' => '30_50_20',
        ])->assertOk();

        $s1Res = $this->postJson('/api/blueprint/commercial-estimate', [
            'sector_code' => 'SECTOR_04_FINTECH_LEDGER',
            'stack_preset' => 'STACK_S1_POLYGLOT_GO_LARAVEL_TEMPORAL',
            'termin_scheme' => '30_50_20',
        ])->assertOk();

        $m1Quote = $m1Res->json('quotation');
        $s1Quote = $s1Res->json('quotation');

        $this->assertSame(1.0, (float) $m1Quote['stack_multiplier']);
        $this->assertSame(1.5, (float) $s1Quote['stack_multiplier']);
        $this->assertGreaterThan($m1Quote['quotation_idr'], $s1Quote['quotation_idr']);
        $this->assertCount(7, $m1Quote['wbs_7_milestones']);
        $this->assertCount(3, $m1Quote['termin_schedule']);

        // Penny-exact termin sum == quotation_idr
        $terminSum = array_sum(array_column($m1Quote['termin_schedule'], 'amount_idr'));
        $this->assertSame($m1Quote['quotation_idr'], $terminSum);
        $this->assertSame(64, strlen((string) $m1Quote['scope_hash_full']));

        // Feature 2: Lock Scope & Issue Official SPK Document with SHA-256 Scope Hash
        $admin = User::query()->where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($admin);
        $blueprint = $this->createSampleMitraBlueprint($admin);

        $lockRes = $this->postJson("/api/blueprint/{$blueprint->slug}/lock-scope-spk", [
            'termin_scheme' => '30_50_20',
        ])->assertOk()
            ->assertJsonPath('success', true);

        $scopeHash = (string) $lockRes->json('scope_hash_full');
        $this->assertSame(64, strlen($scopeHash));

        $doc = Document::query()->findOrFail($lockRes->json('document_id'));
        $this->assertTrue((bool) $doc->scope_locked);
        $this->assertSame($scopeHash, $doc->document_hash);
        $this->assertStringContainsString($scopeHash, (string) json_encode($doc->content_clauses));

        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->firstOrFail();
        $this->assertSame(PosCommercialBridgeService::MODE_MITRA, $posProject->engagement_mode);
        $this->assertSame($scopeHash, $posProject->scope_hash_sha256);

        $this->assertTrue(
            AuditLog::query()
                ->where('event_category', 'commercial_contract')
                ->where('action', 'mitra_scope_frozen_and_spk_generated')
                ->exists()
        );
    }

    public function test_feature_3_paid_change_order_delta_calculator_and_midtrans_addendum_invoice(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);
        $this->seed(SuperAdminSeeder::class);

        $admin = User::query()->where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($admin);
        $blueprint = $this->createSampleMitraBlueprint($admin);

        $deltaService = app(BlueprintDeltaEvolutionService::class);
        $deltaResult = $deltaService->createChangeRequest($blueprint, null, [
            'title' => 'Integrasi Klaim BPJS V-Claim & E-Resep Kurir',
            'change_summary' => 'Menambahkan modul pengiriman obat last-mile & rekonsiliasi klaim otomatis.',
            'added_primitives' => ['PRIMITIVE_LOGISTICS_WAYPOINT'],
            'added_tables' => [
                [
                    'name' => 'bpjs_claim_batches',
                    'description' => 'Rekap pengajuan klaim V-Claim RSIA',
                ],
            ],
        ], true);

        $cr = PosChangeRequest::query()->findOrFail($deltaResult['id']);
        $this->assertGreaterThan(0, (int) $cr->delta_man_hours);
        $this->assertGreaterThan(0, (int) $cr->delta_working_days);
        $this->assertGreaterThan(0, (float) $cr->delta_quotation_idr);

        $approveRes = $this->postJson("/api/blueprint/{$blueprint->slug}/delta-requests/{$cr->id}/approve", [
            'signer_name' => 'dr. Ratna Kusuma, MARS',
            'signer_email' => 'ratna@sentramedika.co.id',
        ])->assertOk()
            ->assertJsonPath('success', true);

        $orderId = (string) $approveRes->json('midtrans_order_id');
        $this->assertStringStartsWith('NPRO-ADD-', $orderId);

        $tx = Transaction::query()->where('midtrans_order_id', $orderId)->firstOrFail();
        $this->assertEquals((float) $cr->delta_quotation_idr, (float) $tx->total_idr);

        $cr->refresh();
        $this->assertSame($orderId, $cr->midtrans_addendum_order_id);
        $this->assertNotNull($cr->approved_by_client_at);

        $this->assertTrue(
            AuditLog::query()
                ->where('event_category', 'paid_addendum')
                ->where('action', 'paid_change_order_addendum_approved')
                ->exists()
        );
    }

    public function test_feature_4_and_5_milestone_gantt_uat_checklist_digital_bast_and_printable_a4_pack(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);
        $this->seed(SuperAdminSeeder::class);

        $admin = User::query()->where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($admin);
        $blueprint = $this->createSampleMitraBlueprint($admin);
        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->firstOrFail();

        $bridge = app(PosCommercialBridgeService::class);
        $gantt = $bridge->buildMilestoneGanttAndStaging($blueprint, $posProject);
        $this->assertCount(7, $gantt['milestones']);
        $this->assertSame('https://staging-sentramedika.neriahpro.com', $gantt['staging_url']);

        // Signing BAST before 100% UAT pass (with mark_all_passed = false) must fail cleanly (422)
        $this->postJson("/api/blueprint/{$blueprint->slug}/uat/sign-bast", [
            'signer_name' => 'dr. Ratna Kusuma',
            'mark_all_passed' => false,
        ])->assertStatus(422)
            ->assertJsonPath('success', false);

        // Toggle all UAT scenarios to passed
        $toggleRes = $this->postJson("/api/blueprint/{$blueprint->slug}/uat/toggle", [
            'scenario_id' => 'ALL',
            'passed' => true,
            'actor_name' => 'dr. Ratna Kusuma',
        ])->assertOk();

        $this->assertSame(100.0, (float) $toggleRes->json('uat.progress_percent'));
        $this->assertTrue((bool) $toggleRes->json('uat.all_passed'));

        // Sign Digital BAST -> issues official BAST number + SHA-256 + Final Settlement Invoice (NPRO-BAST-...)
        $bastRes = $this->postJson("/api/blueprint/{$blueprint->slug}/uat/sign-bast", [
            'signer_name' => 'dr. Ratna Kusuma, MARS',
            'signer_email' => 'ratna@sentramedika.co.id',
        ])->assertOk()
            ->assertJsonPath('success', true);

        $bastNumber = (string) $bastRes->json('bast.bast_number');
        $this->assertStringStartsWith('BAST/NPRO/', $bastNumber);
        $this->assertSame(64, strlen((string) $bastRes->json('bast.bast_sha256')));

        $finalOrderId = (string) $bastRes->json('bast.final_midtrans_order_id');
        $this->assertStringStartsWith('NPRO-BAST-', $finalOrderId);
        $this->assertTrue(Transaction::query()->where('midtrans_order_id', $finalOrderId)->exists());

        $this->assertTrue(
            AuditLog::query()
                ->where('event_category', 'uat_bast')
                ->where('action', 'uat_signed_off_and_digital_bast_issued')
                ->exists()
        );

        // Printable Official A4 Mitra Commercial Pack (`rab`, `spk-appendix`, `bast`)
        foreach (['rab', 'spk-appendix', 'bast'] as $docType) {
            $this->get("/blueprint/{$blueprint->slug}/mitra-document/{$docType}")
                ->assertOk()
                ->assertSee($blueprint->nama_bisnis);
        }

        // Verify Backend Filament ProjectOsMatrixPage ('mitra' tab) renders cleanly with Live RAB Simulator
        Livewire::test(\App\Filament\Pages\ProjectOsMatrixPage::class)
            ->set('activeTab', 'mitra')
            ->assertSee('ALGORITHMIC SCOPE-TO-QUOTATION SIMULATOR');
    }

    public function test_dual_mode_3_pane_studio_workbench_rbac_and_24_native_ai_tools(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);
        $this->seed(PosStackPresetSeeder::class);
        $this->seed(SuperAdminSeeder::class);

        foreach ([
            'manage_mitra_quotations',
            'freeze_blueprint_scope_spk',
            'approve_paid_change_orders',
            'signoff_uat_and_issue_bast',
        ] as $perm) {
            $this->assertTrue(Permission::query()->where('name', $perm)->exists(), "Missing permission {$perm}");
        }

        $reviewer = User::query()->where('email', 'reviewer.midtrans@neriahpro.com')->firstOrFail();
        $admin = User::query()->where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();

        $this->assertFalse($reviewer->can('freeze_blueprint_scope_spk'));
        $this->assertFalse($reviewer->can('signoff_uat_and_issue_bast'));
        $this->assertTrue($admin->can('freeze_blueprint_scope_spk'));

        $blueprint = $this->createSampleMitraBlueprint($admin);

        // Dual-Mode Livewire Studio & BlueprintWorkbench alias
        $this->assertTrue(is_subclass_of(BlueprintWorkbench::class, BlueprintArchitectStudio::class));

        $this->actingAs($admin);
        Livewire::test(BlueprintWorkbench::class, ['slug' => $blueprint->slug])
            ->call('switchEngagementMode', PosCommercialBridgeService::MODE_MITRA)
            ->assertSet('engagementMode', PosCommercialBridgeService::MODE_MITRA)
            ->assertSet('centerTab', 'commercial_mitra')
            ->call('freezeScopeAndIssueSpk')
            ->call('toggleStudioUatScenario', 'ALL')
            ->call('signStudioDigitalBast')
            ->assertSet('uatChecklistState.all_passed', true);

        // Verify 27 Native AI Tools + RBAC Mutation Guard on Mitra Tools #23 & #24
        $this->assertCount(27, GeminiToolRegistry::getDeclarations());
        $this->assertCount(27, GeminiToolRegistry::getOpenAiTools());
        $engine = app(AgenticActionExecutionEngine::class);

        $this->actingAs($reviewer);
        $deniedSpk = $engine->execute('freeze_scope_and_generate_spk_contract', [
            'blueprint_identifier' => $blueprint->slug,
        ]);
        $this->assertArrayHasKey('error', $deniedSpk);

        $deniedBast = $engine->execute('manage_uat_checklist_and_digital_bast', [
            'blueprint_identifier' => $blueprint->slug,
            'operation' => 'sign_bast',
        ]);
        $this->assertArrayHasKey('error', $deniedBast);

        // Read-only status operation is allowed even for reviewer, and mutating tools succeed for Super Admin
        $statusOnly = $engine->execute('manage_uat_checklist_and_digital_bast', [
            'blueprint_identifier' => $blueprint->slug,
            'operation' => 'status',
        ]);
        $this->assertTrue((bool) ($statusOnly['success'] ?? false));

        $this->actingAs($admin);
        $quoteTool = $engine->execute('calculate_mitra_quotation_and_wbs', [
            'blueprint_identifier' => $blueprint->slug,
            'termin_scheme' => '30_50_20',
        ]);
        $this->assertTrue((bool) ($quoteTool['success'] ?? false));
        $this->assertGreaterThan(0, (int) ($quoteTool['quotation']['quotation_idr'] ?? 0));

        // RAG v5.0 indexes Mitra Commercial Bridge & Satu Pintu chunks (>= 54 sections)
        $ragSync = app(VectorEmbeddingService::class)->syncSystemKnowledgeBase(false);
        $this->assertSame('success', $ragSync['status']);
        $this->assertGreaterThanOrEqual(54, $ragSync['synced_sections']);
    }
}
