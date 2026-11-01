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
use App\Support\PosInclusiveCommercialCatalog;
use App\Support\PricingRegistry;
use App\Support\ProjectOsMatrixCatalog;
use Database\Seeders\PosArchitecturePillarSeeder;
use Database\Seeders\PosStackPresetSeeder;
use Database\Seeders\ProjectOsMatrixSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectOsV8InclusiveDualPersonaAndVisualUiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            '*' => Http::response([
                'embedding' => ['values' => array_fill(0, 768, 0.02)],
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

    public function test_all_12_sectors_have_36_visual_frontend_ui_blueprints_and_5_navigation_shells_with_cross_stage_causality(): void
    {
        $catalog = PosInclusiveCommercialCatalog::allSectorVisualUiCatalog();
        $sectors = ProjectOsMatrixCatalog::sectors();
        $this->assertCount(12, $sectors);
        $this->assertCount(12, $catalog);

        $totalVariants = 0;
        foreach ($sectors as $sector) {
            $code = $sector['code'];
            $contract = PosInclusiveCommercialCatalog::visualUiContractForSector($code);
            $this->assertCount(3, $contract['available_variants'], "Sector {$code} must provide 3 visual UI variants.");
            $totalVariants += count($contract['available_variants']);
        }
        $this->assertSame(36, $totalVariants);
        $this->assertCount(5, PosInclusiveCommercialCatalog::navigationShellOptions());

        // Cross-Stage Causality: switching visual UI variant or navigation shell MUST mutate Phase 5 prompt, RAB IDR & SHA-256 Scope Hash
        $schemaOptionA = ProjectOsBlueprintCompilerService::compileSchemaFromPayload([
            'app_name' => 'Retail Omni Nusantara',
            'sector_code' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
            'selected_ui_variant' => 'UI_01A_POS_SPLIT_TERMINAL',
            'navigation_shell' => 'FULLSCREEN_OPERATIONAL_KIOSK',
            'output_locale' => 'id',
            'persona_mode' => 'BUSINESS_WIZARD',
        ]);

        $schemaOptionC = ProjectOsBlueprintCompilerService::compileSchemaFromPayload([
            'app_name' => 'Retail Omni Nusantara',
            'sector_code' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
            'selected_ui_variant' => 'UI_01C_B2B_WHOLESALE_MATRIX',
            'navigation_shell' => 'SIDEBAR_ERP_DENSE',
            'output_locale' => 'ar',
            'persona_mode' => 'ARCHITECT_PRO',
        ]);

        $this->assertSame('UI_01A_POS_SPLIT_TERMINAL', $schemaOptionA['visual_frontend_ui_contract']['selected_variant_code']);
        $this->assertSame('UI_01C_B2B_WHOLESALE_MATRIX', $schemaOptionC['visual_frontend_ui_contract']['selected_variant_code']);
        $this->assertSame('ltr', $schemaOptionA['visual_frontend_ui_contract']['text_direction']);
        $this->assertSame('rtl', $schemaOptionC['visual_frontend_ui_contract']['text_direction']);

        // Phase 5 prompt must reflect exact chosen visual UI & RTL directives
        $phase5A = collect($schemaOptionA['phased_prompt_sequencer'])->firstWhere('phase', 5)['prompt'] ?? '';
        $phase5C = collect($schemaOptionC['phased_prompt_sequencer'])->firstWhere('phase', 5)['prompt'] ?? '';
        $this->assertStringContainsString('UI_01A_POS_SPLIT_TERMINAL', $phase5A);
        $this->assertStringContainsString('UI_01C_B2B_WHOLESALE_MATRIX', $phase5C);
        $this->assertStringContainsString('dir="rtl"', $phase5C);
        $this->assertStringContainsString('ZATCA Phase 2 Fatoora', $phase5C);

        // Causality SHA-256 hash MUST change deterministically
        $this->assertNotSame(
            $schemaOptionA['cross_stage_causality_graph']['causality_fingerprint_sha256'],
            $schemaOptionC['cross_stage_causality_graph']['causality_fingerprint_sha256']
        );
    }

    public function test_tier1_jurisdiction_compliance_switches_statutory_rules_across_7_locales(): void
    {
        $idNonprofit = PosInclusiveCommercialCatalog::jurisdictionComplianceProfile('id', 'SECTOR_12_GOVTECH_NONPROFIT');
        $enNonprofit = PosInclusiveCommercialCatalog::jurisdictionComplianceProfile('en', 'SECTOR_12_GOVTECH_NONPROFIT');
        $enHealth = PosInclusiveCommercialCatalog::jurisdictionComplianceProfile('en', 'SECTOR_03_HEALTHCARE_CLINICAL');
        $arFintech = PosInclusiveCommercialCatalog::jurisdictionComplianceProfile('ar', 'SECTOR_04_FINTECH_LEDGER');
        $deErp = PosInclusiveCommercialCatalog::jurisdictionComplianceProfile('de', 'SECTOR_05_INDUSTRIAL_MRP');

        $this->assertStringContainsString('ISAK 35', implode(' ', $idNonprofit['accounting_standards']));
        $this->assertStringContainsString('PSAK 109', implode(' ', $idNonprofit['accounting_standards']));
        $this->assertStringContainsString('FASB ASC 958', implode(' ', $enNonprofit['accounting_standards']));
        $this->assertStringContainsString('HIPAA', implode(' ', $enHealth['privacy_and_sector_mandates']));
        $this->assertSame('rtl', $arFintech['direction']);
        $this->assertStringContainsString('ZATCA', implode(' ', $arFintech['tax_and_invoicing_rules']));
        $this->assertStringContainsString('GoBD', implode(' ', $deErp['accounting_standards']));
        $this->assertCount(7, $idNonprofit['supported_tier1_locales']);
    }

    public function test_dual_persona_livewire_studio_shares_state_between_business_wizard_and_architect_pro(): void
    {
        Livewire::test(BlueprintArchitectStudio::class)
            ->call('switchPersonaMode', 'BUSINESS_WIZARD')
            ->assertSet('personaMode', 'BUSINESS_WIZARD')
            ->call('setStoryboardStep', 1)
            ->call('selectSector', 'SECTOR_12_GOVTECH_NONPROFIT')
            ->assertSet('sectorCode', 'SECTOR_12_GOVTECH_NONPROFIT')
            ->assertSet('selectedUiVariant', 'UI_12A_ISAK35_RESTRICTED_FUND_SPLITTER')
            ->call('selectVisualUiVariant', 'UI_12B_PUBLIC_DONOR_TRUST_PORTAL')
            ->assertSet('selectedUiVariant', 'UI_12B_PUBLIC_DONOR_TRUST_PORTAL')
            ->assertSet('navigationShell', 'TOPBAR_EXECUTIVE_DECK')
            ->call('switchOutputLocale', 'ar')
            ->assertSet('outputLocale', 'ar')
            ->call('switchPersonaMode', 'ARCHITECT_PRO')
            ->assertSet('personaMode', 'ARCHITECT_PRO')
            // Exact state preserved across persona switch!
            ->assertSet('sectorCode', 'SECTOR_12_GOVTECH_NONPROFIT')
            ->assertSet('selectedUiVariant', 'UI_12B_PUBLIC_DONOR_TRUST_PORTAL')
            ->assertSet('outputLocale', 'ar')
            ->call('persistStudioBlueprint')
            ->assertNotSet('posProjectId', null);

        $savedProject = PosProject::query()->latest('created_at')->firstOrFail();
        $this->assertSame('SECTOR_12_GOVTECH_NONPROFIT', $savedProject->sector_code);
        $this->assertSame('UI_12B_PUBLIC_DONOR_TRUST_PORTAL', $savedProject->selected_ui_variant);
        $this->assertSame('TOPBAR_EXECUTIVE_DECK', $savedProject->navigation_shell);
        $this->assertSame('ar', $savedProject->output_locale);
        $this->assertSame('MENA_AAOIFI_ZATCA_RTL', $savedProject->jurisdiction_profile);
    }

    public function test_inclusive_pricing_inquiry_and_agency_whitelabel_pdf_work_end_to_end(): void
    {
        // Preserve Satu Pintu core 7 count while exposing 3 Inclusive PLG SKUs (total 10 in allPackages)
        $this->assertCount(7, PricingRegistry::getSatuPintuCatalog());
        $this->assertCount(3, PricingRegistry::getInclusiveValueMetricCatalog());
        $this->assertCount(10, PricingRegistry::allPackages());

        // 1. Impact & Non-Profit CSR Grant (Rp 0 free checkout) via /api/pricing/inquiry
        $grantRes = $this->postJson('/api/pricing/inquiry', [
            'name' => 'Yayasan Harapan Bangsa',
            'email' => 'ketua@harapanbangsa.org',
            'country_code' => '+62',
            'phone' => '81299887766',
            'company' => 'Yayasan Harapan Bangsa',
            'package_tier' => PricingRegistry::TIER_IMPACT_NONPROFIT_GRANT,
            'notes' => 'Transparansi donasi pendidikan anak asuh.',
        ]);
        $grantRes->assertOk()->assertJsonPath('success', true);

        // 2. Agency White-Label Proposal & SPK A4 Print View
        $blueprint = VisionBlueprint::query()->create([
            'nama_bisnis' => 'Klinik Sehat Sentosa',
            'slug' => 'klinik-sehat-sentosa-v8',
            'client_name' => 'dr. Rina',
            'email' => 'rina@kliniksentosa.id',
            'phone' => '+6281122334455',
            'project_status' => 'Draft',
            'prd_content' => ['ringkasan_eksekutif' => 'EMR Klinik & Apotek'],
            'user_metadata' => [
                'primary_sector' => 'SECTOR_03_HEALTHCARE_CLINICAL',
                'selected_ui_variant' => 'UI_03A_CLINICAL_3PANE_SOAP',
                'output_locale' => 'ar',
            ],
        ]);
        ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => true]);

        $printRes = $this->get(route('blueprint.mitra-document', [
            'slug' => $blueprint->slug,
            'docType' => 'rab',
            'agency_name' => 'PT Nusantara Digital Studio',
            'agency_website' => 'https://nusantarastudio.id',
        ]));
        $printRes->assertOk();
        $printRes->assertSee('PT Nusantara Digital Studio');
        $printRes->assertSee('dir="rtl"', false);
    }

    public function test_five_layer_banking_grade_ip_security_vault_encryption_canary_watermark_and_mcp_revocation(): void
    {
        $blueprint = VisionBlueprint::query()->create([
            'nama_bisnis' => 'Fintech Amanah Ledger',
            'slug' => 'fintech-amanah-ledger-v8',
            'client_name' => 'Ahmad Fauzi',
            'email' => 'ahmad@amanahledger.id',
            'phone' => '+6281355667788',
            'project_status' => 'Active Sprint',
            'signed_agreement' => true,
            'prd_content' => ['ringkasan_eksekutif' => 'Core ledger double entry DECIMAL(19,4).'],
            'user_metadata' => [
                'primary_sector' => 'SECTOR_04_FINTECH_LEDGER',
                'selected_ui_variant' => 'UI_04A_BALANCED_SPREADSHEET_JOURNAL',
            ],
        ]);

        ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => true]);
        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->firstOrFail();

        // Layer 1: AES-256 Envelope Encryption on encrypted_custom_vault
        $posProject->storeEncryptedVault([
            'proprietary_margin_formula' => 'net_yield = gross_spread * 0.875 - reserve_floor',
            'internal_webhook_hmac_hint' => 'ROTATE_Q4_2026',
        ]);
        $rawCiphertext = (string) DB::table('pos_projects')->where('id', $posProject->id)->value('encrypted_custom_vault');
        $this->assertNotEmpty($rawCiphertext);
        $this->assertStringNotContainsString('proprietary_margin_formula', $rawCiphertext);
        $this->assertSame(
            'net_yield = gross_spread * 0.875 - reserve_floor',
            $posProject->fresh()->readEncryptedVault()['proprietary_margin_formula'] ?? null
        );

        // Layer 2: Invisible 64-Bit Zero-Width Unicode Canary Watermark embedded on rawMd download + forensic extraction
        $admin = User::query()->where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->actingAs($admin);

        $rawMdRes = $this->get(route('blueprint.raw-md', ['slug' => $blueprint->slug]));
        $rawMdRes->assertOk();
        $exportedMarkdown = $rawMdRes->getContent();

        $extractedHex = PosInclusiveCommercialCatalog::extractCanaryWatermark((string) $exportedMarkdown);
        $this->assertNotNull($extractedHex);
        $this->assertSame(16, strlen($extractedHex));
        $this->assertSame($extractedHex, $posProject->fresh()->canary_watermark_sig);
        $this->assertStringContainsString('100% Zero-Slop Certified', (string) $exportedMarkdown);

        // Layer 3: 1-Click Scoped MCP Token Revocation & Rotation (invalidates prior token hash immediately)
        $oldPlainToken = 'npro_mcp_compromised_old_token_123456';
        $posProject->update(['mcp_token_hash' => hash('sha256', $oldPlainToken)]);
        $this->assertTrue($posProject->fresh()->verifyMcpToken($oldPlainToken));
        $newPlainToken = $posProject->revokeMcpToken((string) $admin->id);
        $this->assertFalse($posProject->fresh()->verifyMcpToken($oldPlainToken));
        $this->assertTrue($posProject->fresh()->verifyMcpToken($newPlainToken));
    }

    public function test_33_native_ai_tools_rbac_isolation_rag_sync_matrix_and_smartguide_v8(): void
    {
        $declarations = GeminiToolRegistry::getDeclarations();
        $this->assertCount(33, $declarations);
        $this->assertCount(33, GeminiToolRegistry::getOpenAiTools());

        // Ensure zero empty list [] properties that could trigger Gemini HTTP 400
        foreach ($declarations as $decl) {
            $this->assertTrue(
                is_object($decl['parameters']['properties']) || (is_array($decl['parameters']['properties']) && ! array_is_list($decl['parameters']['properties'])),
                "Tool {$decl['name']} parameters.properties must serialize as a JSON Map."
            );
        }

        // Verify v8.0 RBAC permissions seeded for super_admin and strictly denied for midtrans_reviewer
        $admin = User::query()->where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $reviewer = User::query()->where('email', 'reviewer.midtrans@neriahpro.com')->firstOrFail();
        $this->assertTrue(Permission::query()->where('name', 'verify_canary_ip_watermarks')->exists());
        $this->assertTrue($admin->can('verify_canary_ip_watermarks'));
        $this->assertFalse($reviewer->can('verify_canary_ip_watermarks'));

        $sectorId = \App\Models\PosSector::query()->where('code', 'SECTOR_01_COMMERCE_OMNICHANNEL')->value('id');
        $posProject = PosProject::query()->create([
            'primary_sector_id' => $sectorId,
            'app_name' => 'RBAC Canary Test App',
            'architecture_archetype' => 'OMNICHANNEL_COMMERCE',
            'tenancy_model' => 'MULTI_TENANT_SHARED_DB_COLUMN_SCOPED',
            'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
            'selected_primitive_ids' => [],
            'selected_rule_pack_ids' => [],
            'mcp_token_hash' => hash('sha256', 'live-token-xyz'),
        ]);

        $engine = app(AgenticActionExecutionEngine::class);

        // Reviewer blocked from mutating project visual UI or revoking MCP token
        $this->actingAs($reviewer);
        $deniedUi = $engine->execute('configure_visual_ui_and_jurisdiction_locale', [
            'sector_code' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
            'selected_ui_variant' => 'UI_01B_OMNICHANNEL_TOUCH_KIOSK',
            'project_ulid' => $posProject->id,
        ]);
        $this->assertArrayHasKey('error', $deniedUi);

        $deniedRevoke = $engine->execute('manage_ip_canary_watermark_and_vault_security', [
            'action' => 'revoke_mcp_token',
            'project_ulid' => $posProject->id,
        ]);
        $this->assertArrayHasKey('error', $deniedRevoke);

        // Super Admin succeeds on all 3 new v8.0 Native AI Tools
        $this->actingAs($admin);
        $okUi = $engine->execute('configure_visual_ui_and_jurisdiction_locale', [
            'sector_code' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
            'selected_ui_variant' => 'UI_01B_OMNICHANNEL_TOUCH_KIOSK',
            'navigation_shell' => 'FULLSCREEN_OPERATIONAL_KIOSK',
            'output_locale' => 'ja',
            'persona_mode' => 'BUSINESS_WIZARD',
            'project_ulid' => $posProject->id,
        ]);
        $this->assertTrue($okUi['success']);
        $this->assertSame('UI_01B_OMNICHANNEL_TOUCH_KIOSK', $posProject->fresh()->selected_ui_variant);
        $this->assertSame('ja', $posProject->fresh()->output_locale);

        $okCve = $engine->execute('audit_living_cve_alerts_and_autopatch', [
            'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
            'sector_code' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
            'project_ulid' => $posProject->id,
            'acknowledge_all' => true,
        ]);
        $this->assertTrue($okCve['success']);
        $this->assertGreaterThanOrEqual(2, $okCve['alerts_count']);

        $watermarked = PosInclusiveCommercialCatalog::injectCanaryWatermark('# Confidential Blueprint', 'founder@client.co.id');
        $okVerify = $engine->execute('manage_ip_canary_watermark_and_vault_security', [
            'action' => 'verify_canary',
            'leaked_markdown_sample' => $watermarked,
        ]);
        $this->assertTrue($okVerify['detected']);

        $okRevoke = $engine->execute('manage_ip_canary_watermark_and_vault_security', [
            'action' => 'revoke_mcp_token',
            'project_ulid' => $posProject->id,
        ]);
        $this->assertTrue($okRevoke['mcp_token_rotated']);

        // AuditLog SHA-256 integrity recorded
        $this->assertTrue(AuditLog::query()->where('event_category', 'ip_canary_security')->whereNotNull('integrity_hash')->exists());

        // RAG Sync includes v8.0 chunks
        $ragSync = app(VectorEmbeddingService::class)->syncSystemKnowledgeBase(true);
        $this->assertSame('success', $ragSync['status']);

        // Matrix Page inclusive_v8 tab + Forensic Canary Verifier + Smart Guide Section 15 render cleanly for Super Admin
        Livewire::test(ProjectOsMatrixPage::class)
            ->call('setTab', 'inclusive_v8')
            ->assertSet('activeTab', 'inclusive_v8')
            ->assertSee('36 VISUAL UI BLUEPRINTS')
            ->call('loadSampleWatermarkedMd')
            ->call('verifyCanarySample')
            ->assertSet('canaryVerificationResult.detected', true);

        Livewire::test(SmartGuideRetentionPage::class)
            ->assertOk()
            ->assertSee('36 VISUAL UI');
    }
}
