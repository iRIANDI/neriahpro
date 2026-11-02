<?php

namespace Tests\Feature;

use App\Livewire\BlueprintArchitectStudio;
use App\Services\Ai\AgenticActionExecutionEngine;
use App\Services\Ai\GeminiToolRegistry;
use App\Services\Entitlement\BlueprintEntitlementGateService;
use App\Support\PosProgressiveDisclosureCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        // Pastikan Tahap A Smart Idea Intake & Auto-Mapping hadir di ProjectBlueprintIsland.jsx
        $blueprintJsx = (string) file_get_contents(resource_path('js/Islands/ProjectBlueprintIsland.jsx'));
        $this->assertStringContainsString('id="btn-auto-detect-sector-idea"', $blueprintJsx);
        $this->assertStringContainsString('Deteksi Sektor & Modul Otomatis dari Ide Saya', $blueprintJsx);
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

        Livewire::withQueryParams(['tier' => 'spark'])
            ->test(BlueprintArchitectStudio::class)
            ->assertSet('entitlementTier', 'FREE_SANDBOX')
            ->assertSee('Bahaya Over-Planning (Scope Paralysis)')
            ->assertSee('Konversi ke Mitra Proyek (Potong DP 100%)')
            ->set('ideaDetectionInput', 'Aplikasi klinik gigi rekam medis dan apotek farmasi')
            ->call('runIdeaAutoDetection')
            ->assertSet('sectorCode', 'SECTOR_02_HEALTHCARE_CLINICAL')
            ->call('activateScopeFreezeAndExecuteStage', 'stage_1_migrations')
            ->assertSet('scopeFreezeLocked', true)
            ->assertSet('selectedFoundationStageCode', 'stage_1_migrations')
            ->call('convertRetailToMitraRescue')
            ->assertSet('engagementMode', 'MITRA_TURNKEY_PROJECT')
            ->assertSet('centerTab', 'commercial_mitra');
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
}
