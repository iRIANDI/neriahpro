<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Pages\ProjectOsMatrixPage;
use App\Filament\Pages\SaasSystemHealthPulsePage;
use App\Filament\Pages\SmartGuideRetentionPage;
use App\Livewire\BlueprintArchitectStudio;
use App\Models\AiKnowledgeVector;
use App\Models\AuditLog;
use App\Models\Concerns\HasEncryptedPiiWithBlindIndex;
use App\Models\Permission;
use App\Models\PosChangeRequest;
use App\Models\PosOrganizationWorkspace;
use App\Models\PosPrivacyErasureRequest;
use App\Models\PosProject;
use App\Models\PosSector;
use App\Models\PosSlaMaintenanceTicket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\Ai\AgenticActionExecutionEngine;
use App\Services\Ai\GeminiToolRegistry;
use App\Services\Ai\LlmGatewayService;
use App\Services\Ai\VectorEmbeddingService;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Services\ScaffoldGeneratorService;
use App\Services\SeoGeo\GeoAuthorityGeneratorService;
use App\Support\InputHygieneStandard;
use App\Support\PosSeoGeoAndZeroFailureCatalog;
use Database\Seeders\PosArchitecturePillarSeeder;
use Database\Seeders\PosStackPresetSeeder;
use Database\Seeders\ProjectOsMatrixSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectOsV11SeoGeoZeroFailureAndSatuPintuGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'api.indexnow.org/*' => Http::response(['status' => 'accepted'], 202),
            '*' => Http::response([
                'embedding' => ['values' => array_fill(0, 768, 0.025)],
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

    public function test_satu_pintu_commercial_consistency_gate_blocks_unpaid_track_bypasses_and_routes_through_saas_tiers(): void
    {
        // 1. Direct URL bypass /blueprint?track=retail without an explicit tier/slug/draft_id MUST redirect to /pricing#retail
        $this->get('/blueprint?track=retail&sector=SECTOR_08_PROFESSIONAL_AGENCY')
            ->assertRedirect('/pricing#retail');

        // 2. Direct URL bypass /blueprint?track=mitra without an explicit tier/slug/draft_id MUST redirect to /pricing#project
        $this->get('/blueprint?track=mitra&sector=SECTOR_08_PROFESSIONAL_AGENCY')
            ->assertRedirect('/pricing#project');

        // 3. Explicit tier selection (e.g. from SaaS package checkout flow) is permitted cleanly
        $this->get('/blueprint?track=retail&tier=retail_spark&sector=SECTOR_08_PROFESSIONAL_AGENCY')
            ->assertOk();

        // 4. ArchitecturePricingIsland.jsx enforces LANGKAH F SaaS Package Picker + reactive right panes + updated Tab 04 & Tab 05 with zero unpaid /blueprint/studio bypass links
        $jsxContent = (string) file_get_contents(resource_path('js/Islands/ArchitecturePricingIsland.jsx'));
        $this->assertStringContainsString('launchSatuPintuCheckout', $jsxContent);
        $this->assertStringContainsString('LANGKAH F', $jsxContent);
        $this->assertStringContainsString('activeRetailPkg', $jsxContent);
        $this->assertStringContainsString('activeMitraPkg', $jsxContent);
        $this->assertStringContainsString('28 PARAMETER EVALUASI × 20 PILAR REKAYASA', $jsxContent);
        $this->assertStringContainsString('KNOWLEDGE BASE &amp; FAQ V11.0 PARIPURNA', $jsxContent);
        $this->assertStringNotContainsString('href={`/blueprint/studio?', $jsxContent);
        $this->assertStringNotContainsString('1. MULAI WIZARD BARU BERSIH', $jsxContent);
        $this->assertStringNotContainsString('1. BUKA STUDIO MITRA', $jsxContent);
    }

    public function test_12_sector_seo_schema_graph_llms_txt_indexnow_robots_sitemap_and_local_1200x630_og_citation_card(): void
    {
        // 1. All 12 Functional Sectors have mapped Schema.org @type nodes, statutory standards, and 50-word Answer Capsules
        $matrix = PosSeoGeoAndZeroFailureCatalog::sectorSeoGeoAuthorityMatrix();
        $this->assertCount(12, $matrix);
        foreach ($matrix as $sectorCode => $entry) {
            $this->assertNotEmpty($entry['schema_types'], "Sector {$sectorCode} missing Schema.org types");
            $this->assertNotEmpty($entry['statutory_standards'], "Sector {$sectorCode} missing statutory standards");
            $this->assertNotEmpty($entry['tldr']['id']);
            $this->assertNotEmpty($entry['tldr']['en']);

            $graph = GeoAuthorityGeneratorService::generateSchemaGraph(['sector_code' => $sectorCode]);
            $graphJson = json_encode($graph, JSON_UNESCAPED_SLASHES);
            $this->assertStringContainsString('CollectionPage', (string) $graphJson);
            foreach ($entry['schema_types'] as $expectedSchemaType) {
                $this->assertStringContainsString($expectedSchemaType, (string) $graphJson);
            }
        }

        // 2. Native /llms.txt & /llms-full.txt endpoints return text/plain UTF-8 with all 12 sectors
        $llmsRes = $this->get('/llms.txt')->assertOk();
        $this->assertStringContainsString('text/plain', (string) $llmsRes->headers->get('Content-Type'));
        $this->assertStringContainsString('NeriahPro Project OS', (string) $llmsRes->getContent());
        $this->assertStringContainsString('SECTOR_03_HEALTHCARE_CLINICAL', (string) $llmsRes->getContent());

        $llmsFullRes = $this->get('/llms-full.txt')->assertOk();
        $this->assertStringContainsString('20-Pillar', (string) $llmsFullRes->getContent());
        $this->assertStringContainsString('SECTOR_12_GOVTECH_NONPROFIT', (string) $llmsFullRes->getContent());

        // 3. IndexNow key verification endpoint & instant ping
        $keyRes = $this->get('/indexnow-key.txt')->assertOk();
        $this->assertGreaterThanOrEqual(16, strlen(trim((string) $keyRes->getContent())));

        $pingRes = GeoAuthorityGeneratorService::pingIndexNow([url('/explore'), url('/llms.txt')]);
        $this->assertTrue($pingRes['dispatched']);
        $this->assertSame(202, $pingRes['http_status']);

        // 4. robots.txt explicitly allows GEO AI Search Bots (OAI-SearchBot, GPTBot, ClaudeBot, PerplexityBot, Google-Extended)
        $robotsRes = $this->get('/robots.txt')->assertOk();
        $robotsBody = (string) $robotsRes->getContent();
        foreach (['OAI-SearchBot', 'GPTBot', 'ClaudeBot', 'PerplexityBot', 'Google-Extended'] as $aiBot) {
            $this->assertStringContainsString($aiBot, $robotsBody);
        }

        // 5. sitemap.xml publishes /llms.txt, /llms-full.txt, and /explore sector architecture URLs
        $sitemapRes = $this->get('/sitemap.xml')->assertOk();
        $sitemapBody = (string) $sitemapRes->getContent();
        $this->assertStringContainsString('/llms.txt', $sitemapBody);
        $this->assertStringContainsString('/llms-full.txt', $sitemapBody);
        $this->assertStringContainsString('/explore', $sitemapBody);

        // 6. /explore renders Multi-Graph Schema.org JSON-LD (@graph), Answer Capsule TL;DR, and Semantic HTML Comparison Table
        $exploreRes = $this->get('/explore')->assertOk();
        $exploreHtml = (string) $exploreRes->getContent();
        $this->assertStringContainsString('"@graph"', $exploreHtml);
        $this->assertStringContainsString('FAQPage', $exploreHtml);
        $this->assertStringContainsString('<table', $exploreHtml);

        // 7. Local 1200x630 PNG/SVG OpenGraph Citation Card with zero external CDN
        $ogPngRes = $this->get('/explore/og/klinik-medika-emr.png')->assertOk();
        $contentType = (string) $ogPngRes->headers->get('Content-Type');
        $this->assertTrue(
            str_contains($contentType, 'image/png') || str_contains($contentType, 'image/svg+xml'),
            "Expected image/png or image/svg+xml, got {$contentType}"
        );
        $this->assertNotEmpty($ogPngRes->getContent());
    }

    public function test_uu_pdp_encrypted_pii_blind_index_o1_lookup_portability_export_and_ledger_safe_right_to_erasure(): void
    {
        $client = User::query()->create([
            'name' => 'Budi Santoso PDP',
            'email' => 'budi.pdp@nusantara.co.id',
            'password' => bcrypt('SecretPass!2026'),
        ]);

        VisionBlueprint::query()->create([
            'nama_bisnis' => 'Apotek Sehat Nusantara',
            'slug' => 'apotek-sehat-nusantara-pdp',
            'client_name' => 'Budi Santoso PDP',
            'email' => 'budi.pdp@nusantara.co.id',
            'phone' => '+628123456789',
            'project_status' => 'Active Sprint',
            'prd_content' => ['ringkasan_eksekutif' => 'Sistem Apotek FEFO'],
        ]);

        $tx = Transaction::query()->create([
            'user_id' => $client->id,
            'midtrans_order_id' => 'NPRO-PDP-LEDGER-001',
            'status' => 'settlement',
            'total_idr' => 399000,
            'original_currency' => 'IDR',
            'original_amount' => 399000,
            'customer_details' => [
                'name' => 'Budi Santoso PDP',
                'email' => 'budi.pdp@nusantara.co.id',
                'phone' => '+628123456789',
            ],
        ]);

        $this->actingAs($client);

        // 1. 1-Click UU PDP Data Portability Export
        $exportRes = $this->getJson('/api/saas/privacy/export?phone=08123456789')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('archive.schema_version', 'neriahpro.uu_pdp.portability.v11');

        $receiptId = (string) $exportRes->json('archive.request_receipt_id');
        $this->assertSame(26, strlen($receiptId));

        // Verify raw DB column `requester_email` is AES-256 encrypted (NOT plaintext!) while `email_blind_index` is 64-char hex enabling O(1) lookup
        $rawEncryptedEmail = (string) DB::table('pos_privacy_erasure_requests')->where('id', $receiptId)->value('requester_email');
        $rawBlindIndex = (string) DB::table('pos_privacy_erasure_requests')->where('id', $receiptId)->value('email_blind_index');
        $this->assertStringNotContainsString('budi.pdp@nusantara.co.id', $rawEncryptedEmail);
        $this->assertSame(64, strlen($rawBlindIndex));
        $this->assertSame(HasEncryptedPiiWithBlindIndex::computeBlindIndex('budi.pdp@nusantara.co.id'), $rawBlindIndex);

        $foundViaBlindIndex = PosPrivacyErasureRequest::query()
            ->whereBlindIndex('email_blind_index', 'BUDI.PDP@NUSANTARA.CO.ID ')
            ->first();
        $this->assertNotNull($foundViaBlindIndex);
        $this->assertSame('budi.pdp@nusantara.co.id', $foundViaBlindIndex->requester_email);

        // 2. 1-Click Ledger-Safe Right-to-Erasure PII Redaction
        $redactRes = $this->postJson('/api/saas/privacy/redact', [
            'confirm_erasure' => true,
            'phone' => '08123456789',
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('financial_ledger_rows_preserved', 1);

        $redactionToken = (string) $redactRes->json('redaction_token');
        $this->assertStringStartsWith('[REDACTED_UU_PDP_', $redactionToken);

        // User & Blueprint PII anonymized, while Transaction financial amount & row remain 100% intact!
        $this->assertSame($redactionToken, $client->fresh()->name);
        $this->assertSame($redactionToken, VisionBlueprint::query()->where('slug', 'apotek-sehat-nusantara-pdp')->value('client_name'));
        $preservedTx = $tx->fresh();
        $this->assertNotNull($preservedTx);
        $this->assertSame(399000, (int) $preservedTx->total_idr);
        $this->assertSame($redactionToken, $preservedTx->customer_details['name'] ?? null);
    }

    public function test_post_bast_90_day_sla_warranty_activation_and_bug_vs_paid_delta_triage(): void
    {
        $owner = User::query()->where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();

        $blueprint = VisionBlueprint::query()->create([
            'client_name' => 'PT Logistik Prima',
            'email' => $owner->email,
            'phone' => '+628118889990',
            'nama_bisnis' => 'ERP Gudang & Armada Prima',
            'masalah_utama' => 'Stok gudang selisih dan surat jalan lambat.',
            'tujuan_utama' => 'WMS FEFO + Dispatch Armada.',
            'user_metadata' => [
                'primary_sector' => 'SECTOR_06_LOGISTICS_SUPPLY_CHAIN',
                'stack_preset' => 'STACK_M1_LARAVEL_MONOLITH',
            ],
        ]);

        ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => true]);
        $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->firstOrFail();
        $posProject->update(['user_id' => $owner->id]);

        $this->actingAs($owner);

        // Sign Digital BAST via HTTP -> must auto-activate 90-day Post-BAST SLA Warranty
        $this->postJson("/api/blueprint/{$blueprint->slug}/uat/toggle", [
            'scenario_id' => 'ALL',
            'passed' => true,
            'actor_name' => 'Direktur Operasional',
        ])->assertOk();

        $this->postJson("/api/blueprint/{$blueprint->slug}/uat/sign-bast", [
            'signer_name' => 'Direktur Operasional',
            'signer_email' => $owner->email,
        ])->assertOk()
            ->assertJsonPath('success', true);

        $posProject->refresh();
        $this->assertTrue((bool) $posProject->sla_maintenance_active);
        $this->assertSame(90, (int) $posProject->sla_warranty_days);
        $this->assertNotNull($posProject->sla_warranty_expires_at);

        // Case A: Regression Bug inside locked v1.0 scope -> BUG_WARRANTY_COVERED at Rp 0 + Developer Surgical Bugfix Prompt
        $bugRes = $this->postJson('/api/saas/sla-tickets', [
            'pos_project_id' => $posProject->id,
            'reporter_name' => 'Manajer Gudang',
            'reporter_email' => '  GUDANG@PRIMA.CO.ID <script> ',
            'reporter_phone_country_code' => '+62',
            'reporter_phone' => '0812-9988-7766',
            'issue_title' => 'Error 500 exception saat cetak Surat Jalan gudang',
            'issue_description' => 'Muncul SQLSTATE crash 500 ketika klik tombol cetak PDF surat jalan pada modul yang sudah dikunci di SPK v1.0.',
        ])->assertCreated()
            ->assertJsonPath('classification', PosSlaMaintenanceTicket::CLASS_BUG_WARRANTY)
            ->assertJsonPath('is_free_warranty_bug', true)
            ->assertJsonPath('estimated_delta_idr', 0);

        $this->assertNotEmpty($bugRes->json('developer_bugfix_prompt'));
        $savedBugTicket = PosSlaMaintenanceTicket::query()->findOrFail($bugRes->json('ticket_id'));
        // Verify strict email hygiene & leading '0' phone removal!
        $this->assertSame('gudang@prima.co.id', $savedBugTicket->reporter_email);
        $this->assertSame('81299887766', $savedBugTicket->reporter_phone);

        // Case B: Brand-New Feature outside v1.0 scope -> NEW_SCOPE_PAID_DELTA + auto-created PosChangeRequest Paid Delta v1.1
        $deltaRes = $this->postJson('/api/saas/sla-tickets', [
            'pos_project_id' => $posProject->id,
            'issue_title' => 'Request fitur baru integrasi marketplace TikTok Shop & modul AI forecasting',
            'issue_description' => 'Tolong tambahkan modul baru sinkronisasi katalog otomatis ke API eksternal dan dashboard prediksi stok AI baru.',
        ])->assertCreated()
            ->assertJsonPath('classification', PosSlaMaintenanceTicket::CLASS_NEW_SCOPE_DELTA)
            ->assertJsonPath('is_free_warranty_bug', false);

        $this->assertGreaterThan(0, (int) $deltaRes->json('estimated_delta_idr'));
        $linkedCrId = (string) $deltaRes->json('linked_change_request_id');
        $this->assertSame(26, strlen($linkedCrId));
        $this->assertTrue(PosChangeRequest::query()->whereKey($linkedCrId)->exists());
    }

    public function test_multi_entity_holding_branch_consolidation_intercompany_elimination_and_sandbox_webhook_simulator(): void
    {
        $holdingOwner = User::query()->create([
            'name' => 'CEO Holding Nusantara',
            'email' => 'ceo@holdingnusantara.id',
            'password' => bcrypt('HoldingPass!2026'),
        ]);
        $outsider = User::query()->create([
            'name' => 'Outsider User',
            'email' => 'outsider@othercorp.id',
            'password' => bcrypt('OutsiderPass!2026'),
        ]);

        // 1. Holding Workspace + 2 Child Branch Workspaces (26-char ULID self-FK)
        $holding = PosOrganizationWorkspace::query()->create([
            'owner_user_id' => $holdingOwner->id,
            'name' => 'PT Nusantara Group Holding',
            'slug' => 'nusantara-group-holding',
            'plan_tier' => 'AGENCY_STUDIO',
            'entity_level' => 'HOLDING_HQ',
            'seat_limit' => 10,
        ]);

        $branchJakarta = PosOrganizationWorkspace::query()->create([
            'owner_user_id' => $holdingOwner->id,
            'parent_organization_id' => $holding->id,
            'name' => 'Cabang Jakarta Selatan',
            'slug' => 'cabang-jaksel',
            'plan_tier' => 'RETAIL_PRO',
            'entity_level' => 'BRANCH_OUTLET',
            'elimination_account_code' => '3901-ELIM-JKT',
            'seat_limit' => 5,
        ]);

        $defaultSectorId = PosSector::query()->value('id');

        PosProject::query()->create([
            'user_id' => $holdingOwner->id,
            'organization_workspace_id' => $holding->id,
            'primary_sector_id' => $defaultSectorId,
            'app_name' => 'HQ Treasury Core',
            'architecture_archetype' => 'SECTOR_CANONICAL',
            'selected_primitive_ids' => [],
            'selected_rule_pack_ids' => [],
            'commercial_quotation_json' => ['quotation_idr' => 50000000],
        ]);

        PosProject::query()->create([
            'user_id' => $holdingOwner->id,
            'organization_workspace_id' => $branchJakarta->id,
            'primary_sector_id' => $defaultSectorId,
            'app_name' => 'POS Cabang Jaksel',
            'architecture_archetype' => 'SECTOR_CANONICAL',
            'selected_primitive_ids' => [],
            'selected_rule_pack_ids' => [],
            'commercial_quotation_json' => ['quotation_idr' => 30000000],
        ]);

        // Unauthorized outsider MUST receive 403 Forbidden
        $this->actingAs($outsider)
            ->getJson("/api/saas/holding/{$holding->id}/consolidated")
            ->assertForbidden();

        // Holding Owner gets accurate consolidated roll-up with inter-company elimination
        $elimQuery = http_build_query(['eliminations' => [$branchJakarta->id => 5000000]]);
        $rollupRes = $this->actingAs($holdingOwner)
            ->getJson("/api/saas/holding/{$holding->id}/consolidated?{$elimQuery}")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame(80000000, (int) $rollupRes->json('report.consolidated_gross_idr'));
        $this->assertSame(5000000, (int) $rollupRes->json('report.intercompany_elimination_idr'));
        $this->assertSame(75000000, (int) $rollupRes->json('report.consolidated_net_idr'));

        // 2. Non-Production Local Sandbox Webhook Chaos Simulator (2x Duplicate Attack Idempotency)
        $dupOrderId = 'SBX-DUP-TEST-001';
        $simRes = $this->actingAs($holdingOwner)
            ->postJson('/api/saas/sandbox/simulate-webhook', [
                'scenario' => 'duplicate_webhook_attack',
                'order_id' => $dupOrderId,
                'plan_tier' => 'RETAIL_PRO',
                'amount_idr' => 399000,
            ])
            ->assertOk();

        $this->assertSame(1, Transaction::query()->where('midtrans_order_id', $dupOrderId)->count());
        $this->assertTrue((bool) ($simRes->json('idempotent_duplicate_blocked') ?? true));

        // Strictly blocked (403) when environment is production
        $originalEnv = app()->environment();
        app()->detectEnvironment(fn () => 'production');
        try {
            $this->actingAs($holdingOwner)
                ->withoutMiddleware([
                    PreventRequestForgery::class,
                    ValidateCsrfToken::class,
                ])
                ->postJson('/api/saas/sandbox/simulate-webhook', [
                    'scenario' => 'qris_payment_success',
                ])
                ->assertForbidden();
        } finally {
            app()->detectEnvironment(fn () => $originalEnv);
        }
    }

    public function test_universal_input_hygiene_44_ai_tools_rbac_smart_guide_v11_offline_pwa_rollback_and_audit_chain(): void
    {
        // 1. Universal Input Hygiene Standard (Strict Email, Regional Thousand Separators, Country Zones + Auto-Strip Leading '0')
        $this->assertSame('founder@neriahpro.com', InputHygieneStandard::sanitizeStrictEmail('  FOUNDER@neriahpro.com <img src=x> !#$ '));
        $this->assertSame('1.500.000', InputHygieneStandard::formatRegionalNumber(1500000, 'id'));
        $this->assertSame('1,500,000', InputHygieneStandard::formatRegionalNumber(1500000, 'en'));
        $this->assertNotEmpty(InputHygieneStandard::countryZones());
        $this->assertSame('81234567890', InputHygieneStandard::sanitizePhoneLocalDigits('0812-3456-7890', '+62'));
        $this->assertSame('+6281234567890', InputHygieneStandard::toE164('+62', '0081234567890'));

        // 2. Offline Outbox PWA + Pre-Migration Atomic pg_dump Rollback + Exported Scaffold Artifacts
        $this->assertFileExists(resource_path('js/utils/offlineOutboxQueue.js'));
        $deployScript = (string) file_get_contents(base_path('deploy.sh'));
        $this->assertStringContainsString('pg_dump', $deployScript);
        $this->assertStringContainsString('7|rollback', $deployScript);

        $sampleBp = VisionBlueprint::query()->create([
            'nama_bisnis' => 'Retail POS Offline Ready',
            'slug' => 'retail-pos-offline-v11',
            'client_name' => 'Owner Retail',
            'email' => 'owner@retailpos.id',
            'user_metadata' => ['primary_sector' => 'SECTOR_01_COMMERCE_OMNICHANNEL'],
        ]);
        $compiled = ProjectOsBlueprintCompilerService::compileUniversalSchema($sampleBp, ['persist_project' => true]);
        $this->assertCount(20, $compiled['twenty_pillars_paripurna_matrix']);
        $this->assertContains(
            PosSeoGeoAndZeroFailureCatalog::PRIMITIVE_SEO_GEO_AUTHORITY,
            $compiled['project_metadata']['injected_primitives']
        );
        $this->assertSame(
            PosSeoGeoAndZeroFailureCatalog::PRIMITIVE_UU_PDP_VAULT,
            $compiled['uu_pdp_privacy_vault_contract']['primitive_code']
        );

        $scaffoldFiles = array_keys(ScaffoldGeneratorService::generateFiles($sampleBp));
        $this->assertContains('.agents/SEO_GEO_UU_PDP_AND_OFFLINE_OUTBOX.md', $scaffoldFiles);
        $this->assertContains('app/Models/Concerns/HasEncryptedPiiWithBlindIndex.php', $scaffoldFiles);
        $this->assertContains('public/sw-offline-outbox.js', $scaffoldFiles);

        // 3. 44 Native AI Tools (#41..#44), RAG Knowledge Sync, KAG Rule #12 & Strict RBAC
        $declarations = GeminiToolRegistry::getDeclarations();
        $this->assertCount(44, $declarations);

        foreach ([
            'manage_seo_geo_authority',
            'manage_uu_pdp_privacy_vault',
            'manage_sla_warranty_tickets',
            'run_sandbox_webhook_simulator',
        ] as $perm) {
            $this->assertTrue(Permission::query()->where('name', $perm)->exists(), "Missing RBAC permission {$perm}");
        }

        $reviewer = User::query()->where('email', 'reviewer.midtrans@neriahpro.com')->firstOrFail();
        $admin = User::query()->where('email', 'yoseph.iriandi.tambunan@gmail.com')->firstOrFail();
        $this->assertFalse($reviewer->can('manage_seo_geo_authority'), 'Reviewer must not have manage_seo_geo_authority');
        $this->assertTrue($admin->can('manage_seo_geo_authority'), 'Admin must have manage_seo_geo_authority');

        $syncRes = app(VectorEmbeddingService::class)->syncSystemKnowledgeBase(false);
        $this->assertSame('success', $syncRes['status']);
        $this->assertGreaterThanOrEqual(
            1,
            AiKnowledgeVector::query()->where('source_type', 'seo_geo_and_zero_failure_v11')->count()
        );
        $this->assertStringContainsString('/llms.txt', app(LlmGatewayService::class)->buildDeterministicKagRules());

        $engine = app(AgenticActionExecutionEngine::class);
        $this->actingAs($admin);

        $t41 = $engine->execute('generate_seo_geo_authority_pack', ['sector_code' => 'SECTOR_03_HEALTHCARE_CLINICAL', 'locale' => 'id']);
        $this->assertTrue((bool) ($t41['success'] ?? false), 'Tool 41 failed: '.json_encode($t41));

        $t42 = $engine->execute('audit_uu_pdp_blind_index_and_privacy', ['sample_identifier' => '3174012345670001']);
        $this->assertSame(64, $t42['deterministic_blind_hash_length'] ?? 0, 'Tool 42 failed: '.json_encode($t42));

        $t43 = $engine->execute('triage_post_bast_sla_maintenance_ticket', ['action' => 'list_tickets']);
        $this->assertTrue((bool) ($t43['success'] ?? false), 'Tool 43 failed: '.json_encode($t43));

        $t44 = $engine->execute('simulate_sandbox_webhook_and_holding_rollup', ['action' => 'holding_rollup']);
        $this->assertTrue((bool) ($t44['success'] ?? false), 'Tool 44 failed: '.json_encode($t44));

        // 4. Backend Filament v5 Pages, 3-Pane Studio Tab 12 & Customer Portal Tab 9 Render 200 OK
        Livewire::test(ProjectOsMatrixPage::class)
            ->call('setTab', 'seo_geo_v11')
            ->assertSee('20-PILLAR MASTER PARIPURNA VERIFICATION MATRIX');

        Livewire::test(SaasSystemHealthPulsePage::class)
            ->call('triggerIndexNowPing')
            ->assertSee('PRIMITIVE_SEO_GEO_AUTHORITY_ENGINE');

        Livewire::test(SmartGuideRetentionPage::class)
            ->assertSee('sec-v11-seo-geo-paripurna', false);

        Livewire::test(BlueprintArchitectStudio::class, ['slug' => $sampleBp->slug])
            ->set('centerTab', 'seo_geo_v11')
            ->assertOk();

        $this->get('/customer/dashboard')->assertOk()->assertSee('sla_privacy_holding', false);

        // 5. Cryptographic SHA-256 Audit Chain Integrity remains 100% intact
        $chainSummary = AuditLog::verifyChainSummary(200);
        $this->assertTrue((bool) $chainSummary['is_chain_valid'], 'Chain invalid: '.json_encode($chainSummary));
    }
}
