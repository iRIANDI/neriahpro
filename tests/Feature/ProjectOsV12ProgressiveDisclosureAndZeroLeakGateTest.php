<?php

namespace Tests\Feature;

use App\Livewire\BlueprintArchitectStudio;
use App\Models\Document;
use App\Models\PosImpactGrantApplication;
use App\Models\VisionBlueprint;
use App\Services\Ai\AgenticActionExecutionEngine;
use App\Services\Ai\GeminiToolRegistry;
use App\Services\Entitlement\BlueprintEntitlementGateService;
use App\Services\MidtransSnapService;
use App\Support\PosProgressiveDisclosureCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectOsV12ProgressiveDisclosureAndZeroLeakGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_spark_free_audit_modal_has_single_unified_submit_button_and_smart_idea_auto_detect_in_blueprint_island(): void
    {
        $pricingJsx = (string) file_get_contents(resource_path('js/Islands/ArchitecturePricingIsland.jsx'));

        // Pastikan tidak ada tombol duplikat <a href="/blueprint?tier=spark">BUKA GENERATOR SPARK (GUEST MODE)</a> di dalam modal
        $this->assertStringNotContainsString('BUKA GENERATOR SPARK (GUEST MODE)', $pricingJsx);
        // Pastikan tombol submit utama di bawah formulir tetap ada dan menyatu dengan validasi form
        $this->assertStringContainsString('MULAI AUDIT IDE GRATIS (GUEST MODE)', $pricingJsx);
        // Pastikan Progressive Disclosure Flow & Anti-Over-Planning Scope Paralysis Guard tampil di /pricing
        $this->assertStringContainsString('PROGRESSIVE DISCLOSURE FLOW V12.0', $pricingJsx);
        $this->assertStringContainsString('Bahaya Over-Planning (Scope Paralysis)', $pricingJsx);
        $this->assertStringContainsString('Mulai Eksekusi Tahap 1: Buat File Migrasi Lengkap Database Internal neriahpro.com', $pricingJsx);

        // Pastikan Tahap A Smart Idea Intake & Auto-Mapping hadir di ProjectBlueprintIsland.jsx / BlueprintQuickIdeaSection.jsx
        $quickIdeaJsx = (string) file_get_contents(resource_path('js/Islands/Blueprint/components/BlueprintQuickIdeaSection.jsx'));
        $this->assertStringContainsString('id="btn-auto-detect-sector-idea"', $quickIdeaJsx);
        $this->assertStringContainsString('Deteksi Sektor & Modul Otomatis dari Ide Saya', $quickIdeaJsx);
    }

    public function test_zero_leak_server_side_entitlement_gate_withholds_phases_2_to_7_and_tables_3_plus_for_free_sandbox(): void
    {
        $gate = app(BlueprintEntitlementGateService::class);

        $samplePhases = [
            ['phase' => 1, 'title' => 'Phase 1 Foundation', 'prompt' => 'SECRET_PHASE_1_FOUNDATION_DDL_CONTENT'],
            ['phase' => 2, 'title' => 'Phase 2 Core Schema', 'prompt' => 'SECRET_PHASE_2_RBAC_LEDGER_DDL_CONTENT'],
            ['phase' => 3, 'title' => 'Phase 3 Locks', 'prompt' => 'SECRET_PHASE_3_SELECT_FOR_UPDATE_CONTENT'],
        ];

        $filteredFree = $gate->filterPhasedPromptsForTier($samplePhases, 'SECTOR_02_HEALTHCARE_CLINICAL', 'FREE_SANDBOX');

        // Phase 1 terbuka penuh di FREE_SANDBOX
        $this->assertFalse($filteredFree[0]['is_locked']);
        $this->assertStringContainsString('SECRET_PHASE_1_FOUNDATION_DDL_CONTENT', $filteredFree[0]['prompt']);

        // Phase 2 & 3 wajib terkunci di server (0% leak dari prompt asli)
        $this->assertTrue($filteredFree[1]['is_locked']);
        $this->assertStringNotContainsString('SECRET_PHASE_2_RBAC_LEDGER_DDL_CONTENT', $filteredFree[1]['prompt']);
        $this->assertStringContainsString('ZERO-LEAK SERVER-SIDE ENTITLEMENT GATE', $filteredFree[1]['prompt']);
        $this->assertNotEmpty($filteredFree[1]['upsell_advice']['risk_if_skipped']['id'] ?? '');

        $this->assertTrue($filteredFree[2]['is_locked']);
        $this->assertStringNotContainsString('SECRET_PHASE_3_SELECT_FOR_UPDATE_CONTENT', $filteredFree[2]['prompt']);

        // Pada tier berbayar (SINGLE_APP_PASS / PRO_RETAIL), semua fase terbuka penuh
        $filteredPaid = $gate->filterPhasedPromptsForTier($samplePhases, 'SECTOR_02_HEALTHCARE_CLINICAL', 'SINGLE_APP_PASS');
        $this->assertFalse($filteredPaid[1]['is_locked']);
        $this->assertStringContainsString('SECRET_PHASE_2_RBAC_LEDGER_DDL_CONTENT', $filteredPaid[1]['prompt']);
        $this->assertFalse($filteredPaid[2]['is_locked']);
        $this->assertStringContainsString('SECRET_PHASE_3_SELECT_FOR_UPDATE_CONTENT', $filteredPaid[2]['prompt']);

        // Filter Data Dictionary: FREE_SANDBOX hanya membuka DDL penuh untuk 2 tabel pertama
        $sampleTables = [
            ['table' => 'organizations', 'columns' => [['name' => 'id', 'type' => 'CHAR(26)']], 'raw_ddl' => 'CREATE TABLE organizations'],
            ['table' => 'users', 'columns' => [['name' => 'id', 'type' => 'CHAR(26)']], 'raw_ddl' => 'CREATE TABLE users'],
            ['table' => 'medical_records', 'columns' => [['name' => 'id', 'type' => 'CHAR(26)'], ['name' => 'diagnosis', 'type' => 'TEXT']], 'raw_ddl' => 'CREATE TABLE medical_records SECRET_DDL'],
        ];
        $filteredTablesFree = $gate->filterDataDictionaryForTier($sampleTables, 'FREE_SANDBOX');
        $this->assertFalse($filteredTablesFree[0]['ddl_locked']);
        $this->assertFalse($filteredTablesFree[1]['ddl_locked']);
        $this->assertTrue($filteredTablesFree[2]['ddl_locked']);
        $this->assertStringNotContainsString('SECRET_DDL', $filteredTablesFree[2]['raw_ddl']);
    }

    public function test_enterprise_upscale_advisor_and_retail_to_mitra_100_percent_dp_credit_rescue(): void
    {
        $gate = app(BlueprintEntitlementGateService::class);

        $advisor22 = $gate->buildEnterpriseUpscaleAdvisor(22, 'SECTOR_05_FINTECH_LEDGER', 'SINGLE_APP_PASS');
        $this->assertTrue($advisor22['is_triggered']);
        $this->assertSame(22, $advisor22['table_count']);
        $this->assertCount(3, $advisor22['recommended_pillars']);

        $rescue = $gate->calculateRetailToMitraRescueCredit(60000000, null, 399000);
        $this->assertTrue($rescue['eligible']);
        $this->assertSame(18000000, $rescue['original_dp_30_idr']);
        $this->assertSame(399000, $rescue['credited_retail_fee_idr']);
        $this->assertSame(17601000, $rescue['net_dp_payable_idr']);
    }

    public function test_scope_paralysis_guard_and_studio_progressive_disclosure_actions_work_end_to_end(): void
    {
        $guard = PosProgressiveDisclosureCatalog::scopeParalysisProtectionBlueprint();
        $this->assertTrue($guard['scope_freeze_enforced']);
        $this->assertStringContainsString('Bahaya Over-Planning (Scope Paralysis)', $guard['warning_title']['id']);
        $this->assertCount(3, $guard['execution_stages']);

        Livewire::withQueryParams(['tier' => 'spark', 'locale' => 'id', 'output_locale' => 'id'])
            ->test(BlueprintArchitectStudio::class)
            ->set('centerTab', 'proposal_wow')
            ->assertSet('entitlementTier', 'FREE_SANDBOX')
            ->assertSee('Bahaya Over-Planning (Scope Paralysis)')
            ->set('ideaDetectionInput', 'Aplikasi klinik gigi rekam medis dan apotek farmasi')
            ->call('runIdeaAutoDetection')
            ->assertSet('sectorCode', 'SECTOR_02_HEALTHCARE_CLINICAL')
            ->call('activateScopeFreezeAndExecuteStage', 'stage_1_migrations')
            ->assertSet('scopeFreezeLocked', true)
            ->assertSet('selectedFoundationStageCode', 'stage_1_migrations')
            ->call('convertRetailToMitraRescue')
            ->assertSet('engagementMode', 'MITRA_TURNKEY_PROJECT')
            ->assertSet('centerTab', 'commercial_mitra')
            ->assertSee('POTONG DP 100%');
    }

    public function test_ai_tool_45_evaluate_entitlement_and_scope_paralysis_guard_executes_cleanly(): void
    {
        $declarations = GeminiToolRegistry::getDeclarations();
        $toolNames = array_column($declarations, 'name');
        $this->assertContains('evaluate_entitlement_and_scope_paralysis_guard', $toolNames);
        $this->assertGreaterThanOrEqual(45, count($declarations));

        $engine = app(AgenticActionExecutionEngine::class);

        $detectRes = $engine->execute('evaluate_entitlement_and_scope_paralysis_guard', [
            'action' => 'auto_detect_idea',
            'idea_text' => 'Sistem kasir POS restoran dan manajemen stok gudang',
        ]);
        $this->assertTrue($detectRes['success'] ?? false);
        $this->assertSame('SECTOR_01_COMMERCE_OMNICHANNEL', $detectRes['detection']['detected_sector_code'] ?? '');

        $scopeRes = $engine->execute('evaluate_entitlement_and_scope_paralysis_guard', [
            'action' => 'scope_paralysis_status',
        ]);
        $this->assertTrue($scopeRes['success'] ?? false);
        $this->assertTrue($scopeRes['scope_paralysis_guard']['scope_freeze_enforced'] ?? false);
        $this->assertCount(3, $scopeRes['scope_paralysis_guard']['execution_stages'] ?? []);
    }

    public function test_midtrans_snap_service_bypasses_http_api_when_gross_amount_is_zero_or_negative(): void
    {
        // Testing that calling createSnapToken with gross_amount <= 0 never calls external Midtrans HTTP API
        // and safely returns is_free = true, preventing Midtrans SNAP validation errors and merchant emails
        $resZero = MidtransSnapService::createSnapToken([
            'transaction_details' => [
                'order_id' => 'NPRO-TEST-ZERO-'.time(),
                'gross_amount' => 0,
            ],
            'customer_details' => [
                'first_name' => 'Pastor Yohanes',
                'email' => 'yohanes@gerejakasih.org',
            ],
        ]);

        $this->assertTrue($resZero['success']);
        $this->assertTrue($resZero['is_free']);
        $this->assertNull($resZero['redirect_url']);
        $this->assertStringStartsWith('free_grant_zero_amount_', $resZero['token']);
    }

    public function test_vision_blueprint_contract_generation_with_zero_dp_does_not_call_midtrans(): void
    {
        $blueprint = VisionBlueprint::create([
            'slug' => 'csr-aplikasi-kasih-'.Str::random(6),
            'nama_bisnis' => 'Aplikasi Kasih Komunitas',
            'client_name' => 'Pastor Yohanes',
            'email' => 'yohanes@gerejakasih.org',
            'phone' => '081299887766',
            'package_type' => 'impact_nonprofit_grant',
            'status' => 'active',
            'sector' => 'SECTOR_12_GOVTECH_NONPROFIT',
        ]);

        $doc = $blueprint->convertToDigitalContract([
            'contract_amount' => 0,
            'dp_amount' => 0,
        ]);

        $this->assertNotNull($doc);
        $this->assertSame(0.0, (float) $doc->dp_amount);
        // Midtrans payment URL must be null for Rp 0 CSR grants, zero Midtrans call
        $this->assertNull($doc->content_clauses['midtrans_payment_url'] ?? null);
    }

    public function test_satu_pintu_wizard_v12_tier_1_bilingual_compliance_in_pricing_island(): void
    {
        $jsx = (string) file_get_contents(resource_path('js/Islands/ArchitecturePricingIsland.jsx'));

        // Pastikan Satu Pintu v12.0 telah menggunakan token bilingual dinamis (isEn ? 'ONE-STOP WIZARD V12.0' : 'SATU PINTU WIZARD V12.0')
        $this->assertStringContainsString("isEn ? 'ONE-STOP WIZARD V12.0' : 'SATU PINTU WIZARD V12.0'", $jsx);
        $this->assertStringContainsString("isEn ? 'RETAIL VERSION' : 'VERSI RETAIL'", $jsx);
        $this->assertStringContainsString("isEn ? 'PARTNER PROJECT VERSION' : 'VERSI MITRA PROYEK'", $jsx);
        $this->assertStringContainsString("isEn ? 'RETAIL SAAS // ONE-STOP V12.0' : 'RETAIL SAAS // SATU PINTU V12.0'", $jsx);
        $this->assertStringContainsString("isEn ? 'MITRA TURNKEY // MILESTONE WBS V12.0' : 'MITRA TURNKEY // MILESTONE RAB V12.0'", $jsx);
    }

    public function test_impact_grant_inquiry_requires_legal_decree_and_stores_isolated_vault_document_without_zombies(): void
    {
        Storage::fake('private');

        // 1. Validasi: Tanpa nomor SK dan dokumen, request gagal validasi
        $resFail = $this->postJson('/api/pricing/inquiry', [
            'name' => 'Pastor Yohanes',
            'email' => 'yohanes@gerejakasih.org',
            'phone' => '081299887766',
            'package_tier' => 'impact_nonprofit_grant',
        ]);
        $resFail->assertStatus(422);
        $resFail->assertJsonValidationErrors(['legal_decree_number', 'legal_document']);

        // 2. Submit dengan file legalitas resmi (PDF)
        $fakePdf = UploadedFile::fake()->create('akta_notaris_pendirian_gereja.pdf', 300, 'application/pdf');

        $resSuccess = $this->post('/api/pricing/inquiry', [
            'name' => 'Pastor Yohanes',
            'company' => 'Gereja Kasih Karunia',
            'email' => 'yohanes@gerejakasih.org',
            'phone' => '081299887766',
            'package_tier' => 'impact_nonprofit_grant',
            'organization_type' => 'RELIGIOUS_HOUSE',
            'legal_decree_number' => 'SK-KEMENAG-RI/GKK/2024/0991',
            'legal_document' => $fakePdf,
        ]);

        $resSuccess->assertStatus(200);
        $data = $resSuccess->json();
        $this->assertTrue($data['success']);
        $this->assertSame('impact_nonprofit_grant', $data['package_tier']);
        $this->assertSame(0, $data['gross_amount']);

        // 3. Verifikasi record PosImpactGrantApplication dan Document terbentuk
        $grant = PosImpactGrantApplication::where('official_email', 'yohanes@gerejakasih.org')->first();
        $this->assertNotNull($grant);
        $this->assertSame('SK-KEMENAG-RI/GKK/2024/0991', $grant->legal_decree_number);
        $this->assertSame('RELIGIOUS_HOUSE', $grant->organization_type);
        $this->assertNotEmpty($grant->private_document_path);

        // Path tersimpan di disk private dengan partisi per-entitas: legal_documents/impact_grants/{grant_id}/...
        $this->assertStringStartsWith('legal_documents/impact_grants/'.strtolower((string) $grant->id).'/', $grant->private_document_path);
        Storage::disk('private')->assertExists($grant->private_document_path);

        $docRecord = Document::where('document_type', 'legal_proof')->first();
        $this->assertNotNull($docRecord);
        $this->assertNotEmpty($docRecord->document_hash);

        // 4. Verifikasi Anti-Zombie: Saat model PosImpactGrantApplication dihapus, berkas di vault terhapus tuntas
        $filePath = $grant->private_document_path;
        $grant->delete();

        // Model deleting hook harus membersihkan direktori entitas dari storage private (zero zombie files)
        Storage::disk('private')->assertMissing($filePath);
    }

    public function test_spark_free_tier_has_strict_server_side_download_and_scaffold_entitlement_gates(): void
    {
        $sparkBlueprint = VisionBlueprint::create([
            'slug' => 'translator-spark-demo',
            'nama_bisnis' => 'Translator Spark Demo',
            'client_name' => 'Demo User',
            'email' => 'guest@example.com',
            'phone' => '08123456789',
            'project_status' => 'Idea In Review',
            'is_published' => true,
            'user_metadata' => [
                'retail_tier' => 'retail_spark',
                'package_tier' => 'retail_spark',
            ],
            'prd_content' => [
                'features' => ['Translation Engine'],
                'universal_blueprint_schema' => [
                    'data_dictionary' => [
                        'tables' => [
                            ['table' => 'orgs', 'columns' => [['name' => 'id', 'type' => 'CHAR(26)']]],
                            ['table' => 'users', 'columns' => [['name' => 'id', 'type' => 'CHAR(26)']]],
                            ['table' => 'secret_audit_table', 'columns' => [['name' => 'id', 'type' => 'CHAR(26)'], ['name' => 'secret', 'type' => 'TEXT']]],
                        ],
                    ],
                    'phased_prompt_sequencer' => [
                        ['phase' => 1, 'filename' => '01-phase.md', 'prompt' => 'PHASE_1_PROMPT_CONTENT'],
                        ['phase' => 2, 'filename' => '02-phase.md', 'prompt' => 'PHASE_2_PROMPT_CONTENT'],
                    ],
                ],
            ],
        ]);

        // 1. Spark TIDAK BOLEH unduh ZIP starter kit scaffold (403)
        $resScaffold = $this->get("/blueprint/{$sparkBlueprint->slug}/export/scaffold");
        $resScaffold->assertStatus(403);

        // 2. Spark TIDAK BOLEH unduh prompt Fase 2 ke atas (403)
        $resPhase2 = $this->get("/blueprint/{$sparkBlueprint->slug}/download/cursorrules?format=phase-2");
        $resPhase2->assertStatus(403);

        // 3. Spark TIDAK BOLEH unduh symbol-manifest / pg16-partition (403)
        $resManifest = $this->get("/blueprint/{$sparkBlueprint->slug}/download/cursorrules?format=symbol-manifest");
        $resManifest->assertStatus(403);

        $resPg16 = $this->get("/blueprint/{$sparkBlueprint->slug}/download/cursorrules?format=pg16-partition");
        $resPg16->assertStatus(403);

        // 4. Spark DIIZINKAN unduh prompt Fase 1 (200)
        $resPhase1 = $this->get("/blueprint/{$sparkBlueprint->slug}/download/cursorrules?format=phase-1");
        $resPhase1->assertStatus(200);
        $this->assertSame('PHASE_1_PROMPT_CONTENT', $resPhase1->getContent());

        // 5. Spark previewScaffold menyamarkan berkas produksi eksklusif
        $resPreview = $this->get("/blueprint/{$sparkBlueprint->slug}/scaffold/preview");
        $resPreview->assertStatus(200);
        $previewFiles = $resPreview->json('files');
        $this->assertStringContainsString('LOCKED IN FREE SANDBOX', $previewFiles['deploy.sh'] ?? '');

        // 6. Spark downloadSchemaJson menyaring tabel 3+ (DDL locked)
        $resSchema = $this->get("/blueprint/{$sparkBlueprint->slug}/download/schema-json");
        $resSchema->assertStatus(200);
        $schemaData = $resSchema->json();
        $tables = $schemaData['data_dictionary']['tables'];
        $this->assertFalse($tables[0]['ddl_locked'] ?? false);
        $this->assertTrue($tables[2]['ddl_locked'] ?? false);
    }
}
