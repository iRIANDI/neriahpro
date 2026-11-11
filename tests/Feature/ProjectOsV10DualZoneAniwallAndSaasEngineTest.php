<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Pages\ProjectOsMatrixPage;
use App\Filament\Pages\SaasSystemHealthPulsePage;
use App\Filament\Pages\SmartGuideRetentionPage;
use App\Filament\Resources\PosVerifiedTestimonials\PosVerifiedTestimonialResource;
use App\Livewire\BlueprintArchitectStudio;
use App\Models\AiKnowledgeVector;
use App\Models\AuditLog;
use App\Models\BlueprintVoucher;
use App\Models\PosAffiliateReferral;
use App\Models\PosImpactGrantApplication;
use App\Models\PosOrganizationWorkspace;
use App\Models\PosVerifiedTestimonial;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\Ai\AgenticActionExecutionEngine;
use App\Services\Ai\GeminiToolRegistry;
use App\Services\Ai\LlmGatewayService;
use App\Services\Ai\VectorEmbeddingService;
use App\Services\BlueprintDiscoveryService;
use App\Services\BlueprintSlopScannerService;
use App\Services\PosCommercialBridgeService;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Services\Saas\PosSaasCommercialEngineService;
use App\Support\InputHygieneStandard;
use App\Support\PosDualZoneFrontendAndSaasCatalog;
use App\Support\ProjectOsMatrixCatalog;
use Database\Seeders\PosArchitecturePillarSeeder;
use Database\Seeders\PosStackPresetSeeder;
use Database\Seeders\ProjectOsMatrixSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectOsV10DualZoneAniwallAndSaasEngineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');

        Http::fake([
            '*' => Http::response([
                'embedding' => ['values' => array_fill(0, 768, 0.03)],
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => json_encode([
                                    'nama_bisnis' => 'Nusantara Sky Venue & Ticketing',
                                    'jenis_industri' => 'Media, Event & Spatial Concert Ticketing',
                                    'target_pengguna' => 'Penonton Konser, Event Promotor, Petugas Gate QR, Finance',
                                    'masalah_utama' => 'War tiket sering bentrok kursi ganda dan denah VIP sulit dipilih secara visual.',
                                    'fitur_inti' => "1. Interactive SVG/360 Seat Map Explorer\n2. Redis Atomic Seat Hold 10 Menit\n3. QR Dynamic Gate Scanner\n4. Settlement Split Promotor",
                                ])],
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

    public function test_universal_input_hygiene_email_regional_thousand_separators_country_zones_and_leading_zero_strip(): void
    {
        // 1. Strict Email Character Filter ([a-zA-Z0-9._%+\-@], lowercase, zero spaces/HTML/scripts)
        $dirtyEmail = '  Budi()<script> "!#$@Perusahaan-Maju.CO.ID  ';
        $cleanEmail = InputHygieneStandard::sanitizeStrictEmail($dirtyEmail);
        $this->assertSame('budi@perusahaan-maju.co.id', $cleanEmail);
        $this->assertTrue(InputHygieneStandard::isValidStrictEmail('founder.tech+vip@neriahpro.com'));
        $this->assertFalse(InputHygieneStandard::isValidStrictEmail('invalid space@neriahpro.com'));

        // 2. Regional Thousand Separators (ID dot '.' vs EN comma ',')
        $this->assertSame('25.500.000', InputHygieneStandard::formatRegionalNumber(25500000, 'id'));
        $this->assertSame('25,500,000', InputHygieneStandard::formatRegionalNumber(25500000, 'en'));
        $this->assertSame(25500000, InputHygieneStandard::parseRegionalInteger('25.500.000'));
        $this->assertSame(25500000, InputHygieneStandard::parseRegionalInteger('25,500,000'));

        // 3. Country Zones from config/country_zones.php + Instant Leading-'0' Stripper
        $zones = InputHygieneStandard::countryZones();
        $this->assertNotEmpty($zones);
        $dialCodes = array_column($zones, 'dial_code');
        $this->assertContains('+62', $dialCodes);
        $this->assertContains('+65', $dialCodes);
        $this->assertContains('+1', $dialCodes);

        // Typing '0' first or '0812-3456-7890' immediately strips leading '0'
        $this->assertSame('', InputHygieneStandard::sanitizePhoneLocalDigits('0', '+62'));
        $this->assertSame('81234567890', InputHygieneStandard::sanitizePhoneLocalDigits('0812-3456-7890', '+62'));
        $this->assertSame('+6281234567890', InputHygieneStandard::toE164('+62', '0812-3456-7890'));
        $this->assertSame('+6591234567', InputHygieneStandard::toE164('+65', '09123 4567'));

        // 4. End-to-End API enforcement on VisionBlueprint, Onboarding & Customer Profile
        $visionRes = $this->postJson('/api/vision-blueprint', [
            'client_name' => 'Rina Kusuma',
            'nama_bisnis' => 'Klinik Sehat Sentosa',
            'email' => '  Rina.Kusuma<>!@KlinikSehat.ID ',
            'country_code' => '+62',
            'phone' => '0813-9988-7766',
            'masalah_utama' => 'Antrean poliklinik manual dan rekam medis tersebar.',
            'tujuan_utama' => 'Digitalisasi EMR ICD-10 dan reservasi dokter online.',
            'target_audiens' => 'Pasien, Dokter, Apoteker, Kasir',
            'aktor_sistem' => 'Dokter, Perawat, Farmasi, Admin',
            'fitur_wajib' => 'Booking jadwal dokter, SOAP EMR, Resep Farmasi FEFO',
            'alur_kerja' => 'Pasien booking -> Triase -> SOAP Dokter -> Tebus Obat -> Bayar QRIS',
            'primary_sector' => 'SECTOR_03_HEALTHCARE_CLINICAL',
            'public_ux_preset' => PosDualZoneFrontendAndSaasCatalog::UX_F5,
        ]);

        $visionRes->assertStatus(201);
        $bp = VisionBlueprint::query()->where('id', $visionRes->json('data.id'))->firstOrFail();
        $this->assertSame('rina.kusuma@kliniksehat.id', $bp->email);
        $this->assertSame('+6281399887766', $bp->phone);
        $this->assertSame(PosDualZoneFrontendAndSaasCatalog::UX_F5, $bp->user_metadata['public_ux_preset'] ?? null);

        $customer = User::factory()->create([
            'name' => 'Andi Pratama',
            'email' => 'andi@client.id',
        ]);
        $profileRes = $this->actingAs($customer)->postJson('/api/customer/profile', [
            'name' => 'Andi Pratama Updated',
            'phone_country_code' => '+62',
            'phone' => '0857-1122-3344',
        ]);
        $profileRes->assertOk();
        $this->assertSame('85711223344', $profileRes->json('data.phone'));
        $this->assertSame('+62', $profileRes->json('data.phone_country_code'));
    }

    public function test_12_sectors_dual_zone_ui_6_public_ux_presets_5_iron_laws_and_ai_idea_synthesis_causality(): void
    {
        $sectors = ProjectOsMatrixCatalog::sectors();
        $this->assertCount(12, $sectors);

        $presets = PosDualZoneFrontendAndSaasCatalog::sixPublicUxPresets('id');
        $this->assertCount(6, $presets);
        $this->assertCount(5, PosDualZoneFrontendAndSaasCatalog::fiveIronAnimationLaws('id'));

        // Verify every single one of the 12 Sectors resolves deterministic Zone A (Back-Stage) + Zone B (Public Front-Stage) + Sprint Tasks
        foreach ($sectors as $sector) {
            $sectorCode = $sector['code'];
            $schema = ProjectOsBlueprintCompilerService::compileSchemaFromPayload([
                'name' => 'Enterprise '.$sectorCode,
                'sector_code' => $sectorCode,
                'primitive_codes' => $sector['mandatory_primitives'] ?? ['PRIM_IAM_MULTI_TENANT'],
            ]);

            $this->assertArrayHasKey('dual_zone_ui_contract', $schema, "Missing dual_zone_ui_contract for {$sectorCode}");
            $this->assertArrayHasKey('input_hygiene_contract', $schema, "Missing input_hygiene_contract for {$sectorCode}");

            $zoneA = $schema['dual_zone_ui_contract']['zone_a_operational_backstage'] ?? [];
            $zoneB = $schema['dual_zone_ui_contract']['zone_b_public_frontstage'] ?? [];
            $this->assertTrue((bool) ($zoneA['scrollytelling_banned'] ?? false));
            $this->assertNotEmpty($zoneA['selected_operational_variant']);
            $this->assertContains($zoneB['selected_public_ux_preset'], array_keys($presets));

            // Verify World-Class 5-Sprint Task Matrix includes TSK-304 (Zone B Public UX) & TSK-305 (Universal Input Hygiene)
            $dagJson = json_encode($schema['world_class_task_matrix'] ?? []);
            $this->assertStringContainsString('TSK-304', (string) $dagJson);
            $this->assertStringContainsString('TSK-305', (string) $dagJson);
        }

        // Verify AI Idea Synthesis (BlueprintDiscoveryService) auto-matches Sector + Zone A + Zone B Public UX
        $discovery = app(BlueprintDiscoveryService::class)->synthesize(
            rawIdeaText: 'Saya ingin membangun platform tiket konser musik stadion dengan denah pilih kursi 360 derajat VIP dan gate QR scanner anti-calo.',
            uploadedFiles: []
        );
        $this->assertNotEmpty($discovery['primarySector'] ?? null);
        $this->assertNotEmpty($discovery['selectedUiVariant'] ?? null);
        $this->assertNotEmpty($discovery['publicUxPreset'] ?? null);
        $this->assertArrayHasKey('dualZoneUiContract', $discovery);

        // Verify Cross-Stage Causality: changing public_ux_preset changes RAB man-hours & scope_hash_short
        $previewF2 = $this->postJson('/api/blueprint/topological-preview', [
            'primary_sector' => 'SECTOR_02_HOSPITALITY_SPACE',
            'selected_primitives' => ['PRIM_BOOKING_CALENDAR', 'PRIM_PAYMENT_GATEWAY'],
            'public_ux_preset' => PosDualZoneFrontendAndSaasCatalog::UX_F2,
        ])->assertOk();

        $previewF3 = $this->postJson('/api/blueprint/topological-preview', [
            'primary_sector' => 'SECTOR_02_HOSPITALITY_SPACE',
            'selected_primitives' => ['PRIM_BOOKING_CALENDAR', 'PRIM_PAYMENT_GATEWAY'],
            'public_ux_preset' => PosDualZoneFrontendAndSaasCatalog::UX_F3,
        ])->assertOk();

        $this->assertSame(
            PosDualZoneFrontendAndSaasCatalog::UX_F3,
            $previewF3->json('dual_zone_ui_contract.zone_b_public_frontstage.selected_public_ux_preset')
        );
        $this->assertGreaterThan(
            (int) $previewF2->json('live_commercial_sync.quotation_idr'),
            (int) $previewF3->json('live_commercial_sync.quotation_idr')
        );
        $this->assertNotSame(
            $previewF2->json('live_commercial_sync.scope_hash_short'),
            $previewF3->json('live_commercial_sync.scope_hash_short')
        );

        // Verify Anti-Slop Scanner catches layout-reflow animation violations (Iron Law #2)
        $slopReport = app(BlueprintSlopScannerService::class)->scanCodeSnippet(
            '.hero-card { transition: top 0.4s ease, left 0.4s ease, width 0.3s; } @keyframes badMove { 0% { top: 0px; } 100% { top: 50px; } }',
            false
        );
        $violationCodes = array_column($slopReport['findings'] ?? $slopReport['violations'] ?? [], 'code');
        $this->assertContains('LAYOUT_REFLOW_ANIMATION_SLOP', $violationCodes);
    }

    public function test_cryptographically_verified_testimonials_auto_issued_on_bast_and_phase6_mcp_completion(): void
    {
        $owner = User::factory()->create([
            'name' => 'Hendra Wijaya',
            'email' => 'hendra@logistiknusantara.id',
        ]);

        // 1. Mitra Track: Digital BAST Sign-Off auto-creates MITRA_BAST_SIGNED Verified Testimonial
        $mitraBp = VisionBlueprint::create([
            'client_name' => 'Hendra Wijaya',
            'nama_bisnis' => 'PT Nusantara ColdChain Fleet',
            'email' => 'hendra@logistiknusantara.id',
            'phone' => '+628119988776',
            'masalah_utama' => 'Pelacakan suhu armada pendingin antar pulau belum terintegrasi.',
            'tujuan_utama' => 'Sistem TMS & WMS berbasis sensor dengan bukti serah terima digital.',
            'target_audiens' => 'Manajer Armada, Supir, Gudang, Klien B2B',
            'aktor_sistem' => 'Dispatcher, Supir, Admin Gudang',
            'fitur_wajib' => 'Live GPS Dispatch, Cold-Chain Telemetry, Digital POD',
            'alur_kerja' => 'Order B2B -> Alokasi Truk -> Muat Gudang -> Pengiriman -> Digital BAST',
            'project_status' => 'Active Sprint',
            'is_published' => true,
            'user_metadata' => [
                'user_id' => $owner->id,
                'primary_sector' => 'SECTOR_06_LOGISTICS_FLEET',
                'public_ux_preset' => PosDualZoneFrontendAndSaasCatalog::UX_F5,
            ],
        ]);

        $mitraPos = app(ProjectOsBlueprintCompilerService::class)->compileAndPersist($mitraBp);
        $mitraPos->forceFill(['user_id' => $owner->id])->save();

        $bastResult = app(PosCommercialBridgeService::class)->signOffUatAndIssueBast(
            $mitraBp,
            $mitraPos,
            [
                'mark_all_passed' => true,
                'signer_name' => 'Hendra Wijaya',
                'signer_email' => 'hendra@logistiknusantara.id',
            ]
        );

        $this->assertTrue((bool) ($bastResult['success'] ?? false));
        $mitraTestimonial = PosVerifiedTestimonial::query()
            ->where('pos_project_id', $mitraPos->id)
            ->first();
        $this->assertNotNull($mitraTestimonial);
        $this->assertSame('MITRA_BAST_SIGNED', $mitraTestimonial->verification_trigger);
        $this->assertGreaterThanOrEqual(12, (int) ($mitraTestimonial->architecture_receipt_json['ulid_tables_count'] ?? 0));
        $this->assertNotEmpty($mitraTestimonial->architecture_receipt_json['scope_hash_sha256'] ?? '');

        // 2. Retail Track: Livewire Studio Zone B UX Selection + Phase 6 Verification auto-creates RETAIL_PHASE6_VERIFIED
        $retailBp = VisionBlueprint::create([
            'client_name' => 'Siti Rahma',
            'nama_bisnis' => 'Butik Batik Heritage',
            'email' => 'siti@batikheritage.id',
            'phone' => '+6281277665544',
            'masalah_utama' => 'Stok katalog online dan butik fisik sering selisih saat koleksi baru rilis.',
            'tujuan_utama' => 'Omnichannel Lookbook Commerce & POS sinkron real-time.',
            'target_audiens' => 'Kolektor Batik, Kasir Butik, Admin Gudang',
            'aktor_sistem' => 'Pelanggan, Kasir, Admin',
            'fitur_wajib' => 'Lookbook Katalog, Slide-Over Cart, POS Kasir, Kartu Stok',
            'alur_kerja' => 'Pilih Motif -> Quick Drawer Checkout -> Bayar QRIS -> Kurangi Stok',
            'project_status' => 'Prospecting',
            'is_published' => true,
            'user_metadata' => [
                'user_id' => $owner->id,
                'primary_sector' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
            ],
        ]);
        $retailPos = app(ProjectOsBlueprintCompilerService::class)->compileAndPersist($retailBp);

        Livewire::actingAs($owner)
            ->test(BlueprintArchitectStudio::class, ['slug' => $retailBp->slug])
            ->call('selectPublicUxPreset', PosDualZoneFrontendAndSaasCatalog::UX_F4)
            ->assertSet('publicUxPreset', PosDualZoneFrontendAndSaasCatalog::UX_F4)
            ->call('togglePhaseVerified', 6);

        $retailTestimonial = PosVerifiedTestimonial::query()
            ->where('pos_project_id', $retailPos->id)
            ->first();
        $this->assertNotNull($retailTestimonial);
        $this->assertSame('RETAIL_PHASE6_VERIFIED', $retailTestimonial->verification_trigger);
    }

    public function test_six_critical_saas_modules_billing_webhook_affiliates_workspaces_impact_grants_and_seo_explore(): void
    {
        $superAdmin = User::query()->whereHas('roles', fn ($q) => $q->where('name', 'super_admin'))->firstOrFail();
        $partnerReferrer = User::factory()->create([
            'name' => 'Agensi Partner Digital',
            'email' => 'partner@agensi.id',
        ]);
        $refCode = PosSaasCommercialEngineService::ensureUserReferralCode($partnerReferrer);

        // Module #2 (Part A): Capture ?ref=CODE via SetAppLocale middleware
        $this->get('/?ref='.$refCode)->assertStatus(200);
        $this->assertSame($refCode, session('neriah_ref_code'));

        $buyer = User::factory()->create([
            'name' => 'Startup Founder',
            'email' => 'founder@saasbaru.id',
        ]);
        PosSaasCommercialEngineService::recordReferralAttribution($buyer, $refCode);
        $buyer->refresh();

        // Module #1: Feature Gate starts locked on SPARK_FREE for Phase 3-6, then unlocks via HMAC Billing Webhook (<2s + idempotent)
        $bp = VisionBlueprint::create([
            'client_name' => 'Startup Founder',
            'nama_bisnis' => 'FinLedger Pro',
            'email' => 'founder@saasbaru.id',
            'masalah_utama' => 'Rekonsiliasi pembayaran multi-bank masih manual.',
            'tujuan_utama' => 'Otomasi buku besar double-entry dan webhook settlement.',
            'target_audiens' => 'CFO, Akuntan, Merchant',
            'aktor_sistem' => 'Admin Finance, Auditor',
            'fitur_wajib' => 'Double-Entry Ledger, Escrow Split, Rekonsiliasi',
            'alur_kerja' => 'Transaksi Masuk -> Jurnal Otomatis -> Rekonsiliasi -> Laporan',
            'is_published' => true,
            'user_metadata' => ['user_id' => $buyer->id, 'primary_sector' => 'SECTOR_04_FINTECH_LEDGER'],
        ]);
        app(ProjectOsBlueprintCompilerService::class)->compileAndPersist($bp);

        $lockedGate = $this->actingAs($buyer)->getJson("/api/saas/feature-gate/{$bp->slug}")->assertOk();
        $this->assertFalse((bool) $lockedGate->json('can_copy_phase_3_to_6'));

        // Forged HMAC signature MUST be rejected with 401
        $orderId = 'NPRO-SUB-PRO-001';
        $this->postJson('/api/saas/billing/webhook', [
            'order_id' => $orderId,
            'user_id' => $buyer->id,
            'target_tier' => PosSaasCommercialEngineService::TIER_RETAIL_PRO,
            'amount_idr' => 1500000,
            'gateway' => 'STRIPE_USD',
            'original_currency' => 'USD',
            'original_amount' => 99,
        ], ['X-NeriahPro-Signature' => 'forged-signature'])->assertStatus(401);

        // Valid HMAC signature unlocks RETAIL_PRO & reserves 20% Retail Affiliate Commission (Rp 300.000)
        $secret = (string) (config('services.saas_webhook.secret') ?: config('app.key'));
        $validSig = hash_hmac('sha256', implode('|', [
            $orderId,
            $buyer->id,
            PosSaasCommercialEngineService::TIER_RETAIL_PRO,
            '1500000',
        ]), $secret);

        $webhookRes = $this->postJson('/api/saas/billing/webhook', [
            'order_id' => $orderId,
            'user_id' => $buyer->id,
            'target_tier' => PosSaasCommercialEngineService::TIER_RETAIL_PRO,
            'amount_idr' => 1500000,
            'gateway' => 'STRIPE_USD',
            'original_currency' => 'USD',
            'original_amount' => 99,
        ], ['X-NeriahPro-Signature' => $validSig])->assertOk();

        $this->assertFalse((bool) $webhookRes->json('idempotent_replay'));
        $this->assertSame(PosSaasCommercialEngineService::TIER_RETAIL_PRO, $webhookRes->json('tier'));

        // Replay of the exact same webhook MUST be 100% idempotent (zero duplicate transactions/commissions)
        $replayRes = $this->postJson('/api/saas/billing/webhook', [
            'order_id' => $orderId,
            'user_id' => $buyer->id,
            'target_tier' => PosSaasCommercialEngineService::TIER_RETAIL_PRO,
            'amount_idr' => 1500000,
        ], ['X-NeriahPro-Signature' => $validSig])->assertOk();
        $this->assertTrue((bool) $replayRes->json('idempotent_replay'));

        $buyer->refresh();
        $unlockedGate = $this->actingAs($buyer)->getJson("/api/saas/feature-gate/{$bp->slug}")->assertOk();
        $this->assertTrue((bool) $unlockedGate->json('can_copy_phase_3_to_6'));
        $this->assertTrue((bool) $unlockedGate->json('can_connect_mcp_bridge'));

        $retailComm = PosAffiliateReferral::query()
            ->where('referrer_user_id', $partnerReferrer->id)
            ->whereNotNull('transaction_id')
            ->firstOrFail();
        $this->assertSame('RETAIL_RECURRING_20', $retailComm->commission_type);
        $this->assertSame(300000, (int) $retailComm->commission_amount_idr);

        // High-Ticket Mitra Turnkey DP Settlement reserves 7.5% Finder's Fee (Rp 25.000.000 * 7.5% = Rp 1.875.000)
        $mitraOrderId = 'NPRO-SPK-MITRA-001';
        $mitraSig = hash_hmac('sha256', implode('|', [
            $mitraOrderId,
            $buyer->id,
            PosSaasCommercialEngineService::TIER_MITRA_ENTERPRISE,
            '25000000',
        ]), $secret);
        $this->postJson('/api/saas/billing/webhook', [
            'order_id' => $mitraOrderId,
            'user_id' => $buyer->id,
            'target_tier' => PosSaasCommercialEngineService::TIER_MITRA_ENTERPRISE,
            'amount_idr' => 25000000,
            'blueprint_id' => $bp->id,
        ], ['X-NeriahPro-Signature' => $mitraSig])->assertOk();

        $mitraComm = PosAffiliateReferral::query()
            ->where('referrer_user_id', $partnerReferrer->id)
            ->where('commission_type', 'MITRA_FINDERS_FEE')
            ->firstOrFail();
        $this->assertSame(1875000, (int) $mitraComm->commission_amount_idr);

        // Module #3: Multi-Seat Organization Workspace & Agency White-Label Branding Vault
        $wsRes = $this->actingAs($partnerReferrer)->postJson('/api/saas/workspaces', [
            'name' => 'Nusantara Software House',
            'agency_name' => 'PT Nusantara Digital Kreasi',
            'brand_primary_hex' => '#10b981',
            'contact_email' => '  Hello<>@NusantaraDigital.ID ',
            'phone_country_code' => '+62',
            'phone_number' => '0811-2233-4455',
            'seat_limit' => 2,
        ])->assertOk();

        $wsId = (string) $wsRes->json('workspace.id');
        $wsModel = PosOrganizationWorkspace::query()->findOrFail($wsId);
        $this->assertSame('hello@nusantaradigital.id', $wsModel->white_label_config_json['contact_email']);
        $this->assertSame('81122334455', $wsModel->white_label_config_json['phone_number']);

        // Seat #2 (LEAD_ARCHITECT) succeeds; Seat #3 exceeds seat_limit=2 -> 422
        $this->actingAs($partnerReferrer)->postJson("/api/saas/workspaces/{$wsId}/members", [
            'email' => '  Lead.Arch!@NusantaraDigital.ID ',
            'workspace_role' => 'LEAD_ARCHITECT',
            'phone_country_code' => '+62',
            'phone_number' => '081299001122',
        ])->assertStatus(201);

        $this->actingAs($partnerReferrer)->postJson("/api/saas/workspaces/{$wsId}/members", [
            'email' => 'junior2@nusantaradigital.id',
            'workspace_role' => 'JUNIOR_DEV',
        ])->assertStatus(422);

        // Module #4: 1-Click Non-Profit / NGO Impact Grant Workflow (private disk + AgentShield + 100% Free Grant Voucher)
        $ngoUser = User::factory()->create([
            'name' => 'Ketua Yayasan Harapan',
            'email' => 'ketua@yayasanharapan.org',
        ]);
        $grantRes = $this->actingAs($ngoUser)->post('/api/saas/impact-grants', [
            'organization_name' => 'Yayasan Pendidikan Harapan Bangsa',
            'organization_type' => 'NGO',
            'pic_email' => '  Ketua<>@YayasanHarapan.ORG ',
            'country_code' => '+62',
            'pic_phone' => '0813-5566-7788',
            'legal_decree_number' => 'AHU-0012345.AH.01.04',
            'annual_beneficiaries_display' => '15.000.000',
            'social_mission_summary' => 'Transparansi beasiswa pendidikan anak yatim sesuai standar ISAK 35.',
            'legality_document' => UploadedFile::fake()->create('sk_kemenkumham.pdf', 120, 'application/pdf'),
        ]);
        $grantRes->assertStatus(201);

        $grantApp = PosImpactGrantApplication::query()->findOrFail($grantRes->json('application_id'));
        $this->assertSame('ketua@yayasanharapan.org', $grantApp->official_email);
        $this->assertSame('81355667788', $grantApp->phone_number);
        $this->assertSame(15000000, $grantApp->requested_project_budget_idr);
        Storage::disk('private')->assertExists($grantApp->private_document_path);

        $approval = PosSaasCommercialEngineService::approveImpactGrantOneClick($grantApp, $superAdmin, 'Verified SK Kemenkumham');
        $this->assertTrue($approval['success']);
        $ngoUser->refresh();
        $this->assertSame(PosSaasCommercialEngineService::TIER_IMPACT_GRANT, $ngoUser->saas_subscription_tier);
        $this->assertTrue(BlueprintVoucher::query()->where('code', $approval['granted_voucher_code'])->where('discount_type', 'free')->exists());

        // Module #5: Programmatic SEO Architecture Library (/explore, /explore/{sector}, /explore/{sector}/{slug}, /explore/og/{slug}.svg, /sitemap.xml)
        $this->get('/explore')
            ->assertOk()
            ->assertSee('application/ld+json', false)
            ->assertSee('CollectionPage', false);

        $this->get('/explore/sector_12_govtech_nonprofit')->assertOk();

        // Seed a published verified testimonial to test programmatic detail page & OG image
        $sampleTestimonial = PosVerifiedTestimonial::create([
            'pos_project_id' => null,
            'vision_blueprint_id' => $bp->id,
            'user_id' => $buyer->id,
            'sector_code' => 'SECTOR_12_GOVTECH_NONPROFIT',
            'stack_code' => 'M1 Laravel 13 + Flux UI',
            'public_ux_preset' => PosDualZoneFrontendAndSaasCatalog::UX_F1,
            'operational_ui_variant' => 'UI_12A_NGO_DONATION_LEDGER',
            'verification_trigger' => 'MITRA_BAST_SIGNED',
            'client_name' => 'Dr. Haryanto',
            'client_role' => ['id' => 'Direktur Eksekutif', 'en' => 'Executive Director'],
            'client_company' => 'Yayasan Nusantara Bersatu',
            'quote' => ['id' => 'Sistem ledger donasi transparan', 'en' => 'Transparent donation ledger system'],
            'case_study_summary' => ['id' => 'Ringkasan studi kasus', 'en' => 'Case study summary'],
            'architecture_receipt_json' => ['ulid_tables_count' => 18, 'concurrency_locks_count' => 4, 'token_saved_percent' => 70],
            'anonymized_blueprint_json' => ['seo_slug' => 'yayasan-nusantara-bersatu'],
            'is_published' => true,
            'verified_at' => now(),
        ]);
        Cache::forget('project_os:verified_testimonials_aniwall:v10:id');
        Cache::forget('project_os:verified_testimonials_aniwall:v10:en');

        $catalogFirst = PosDualZoneFrontendAndSaasCatalog::getVerifiedTestimonialsCatalog('id')[0];
        $firstSectorSlug = strtolower($catalogFirst['sector_code']);
        $firstItemSlug = $catalogFirst['seo_slug'] ?? 'yayasan-nusantara-bersatu';

        $this->get("/explore/{$firstSectorSlug}/{$firstItemSlug}")
            ->assertOk()
            ->assertSee('SoftwareApplication', false);

        $ogRes = $this->get("/explore/og/{$firstItemSlug}.svg")->assertOk();
        $this->assertStringContainsString('image/svg+xml', (string) $ogRes->headers->get('Content-Type'));
        $this->assertStringContainsString('VERIFIED ARCHITECTURE RECEIPT', $ogRes->getContent());

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('/explore', false);
    }

    public function test_40_ai_native_tools_rbac_isolation_smart_guide_v10_bilingual_and_sha256_audit_chain(): void
    {
        // 1. 51 Native AI Tool Declarations (incl. Biometrics & Geofence) + RAG/CAG/KAG v12.0 Knowledge Sync
        $declarations = GeminiToolRegistry::getDeclarations();
        $this->assertCount(51, $declarations);

        $syncRes = app(VectorEmbeddingService::class)->syncSystemKnowledgeBase(true);
        $this->assertSame('success', $syncRes['status']);
        $this->assertGreaterThanOrEqual(
            2,
            AiKnowledgeVector::query()->where('source_type', 'dual_zone_aniwall_saas_v10')->count()
        );

        $instructionText = app(LlmGatewayService::class)->buildDeterministicKagRules();
        $this->assertStringContainsString('UX_F1_SCROLLYTELLING', $instructionText);

        $superAdmin = User::query()->whereHas('roles', fn ($q) => $q->where('name', 'super_admin'))->firstOrFail();
        $midtransReviewer = User::factory()->create(['email' => 'reviewer@midtrans-test.com']);
        $midtransReviewer->assignRole('midtrans_reviewer');

        $engine = app(AgenticActionExecutionEngine::class);

        // Execute v10.0 AI Native Tools 37..40 as Super Admin
        $t37 = $engine->execute('configure_dual_zone_public_ux_preset', [
            'sector_code' => 'SECTOR_10_MEDIA_TICKETING',
            'idea_text' => 'Konser musik stadion denah kursi 360 VIP',
        ], null, $superAdmin);
        $this->assertTrue((bool) ($t37['success'] ?? false));
        $this->assertSame(PosDualZoneFrontendAndSaasCatalog::UX_F3, $t37['selected_public_ux_preset']);

        // Seed a published verified testimonial record to test list_aniwall tool
        PosVerifiedTestimonial::create([
            'pos_project_id' => null,
            'vision_blueprint_id' => null,
            'user_id' => $superAdmin->id,
            'sector_code' => 'SECTOR_12_GOVTECH_NONPROFIT',
            'stack_code' => 'M1 Laravel 13 + Flux UI',
            'public_ux_preset' => PosDualZoneFrontendAndSaasCatalog::UX_F1,
            'operational_ui_variant' => 'UI_12A_NGO_DONATION_LEDGER',
            'verification_trigger' => 'MITRA_BAST_SIGNED',
            'client_name' => 'Dr. Haryanto',
            'client_role' => ['id' => 'Direktur Eksekutif', 'en' => 'Executive Director'],
            'client_company' => 'Yayasan Nusantara Bersatu',
            'quote' => ['id' => 'Sistem ledger donasi transparan', 'en' => 'Transparent donation ledger system'],
            'case_study_summary' => ['id' => 'Ringkasan studi kasus', 'en' => 'Case study summary'],
            'architecture_receipt_json' => ['ulid_tables_count' => 18, 'concurrency_locks_count' => 4, 'token_saved_percent' => 70],
            'anonymized_blueprint_json' => ['seo_slug' => 'yayasan-nusantara-bersatu'],
            'is_published' => true,
            'verified_at' => now(),
        ]);
        Cache::forget('project_os:verified_testimonials_aniwall:v10:id');
        Cache::forget('project_os:verified_testimonials_aniwall:v10:en');

        $t38 = $engine->execute('manage_verified_testimonials_aniwall', [
            'action' => 'list_aniwall',
            'sector_code' => 'SECTOR_12_GOVTECH_NONPROFIT',
        ], null, $superAdmin);
        $this->assertTrue((bool) ($t38['success'] ?? false));
        $this->assertGreaterThanOrEqual(1, (int) $t38['total_verified_testimonials']);

        $t39 = $engine->execute('manage_affiliate_and_impact_grants', [
            'action' => 'summary',
        ], null, $superAdmin);
        $this->assertTrue((bool) ($t39['success'] ?? false));
        $this->assertEquals(20.0, (float) $t39['retail_affiliate_rate_percent']);
        $this->assertEquals(7.5, (float) $t39['mitra_finders_fee_rate_percent']);

        $t40 = $engine->execute('inspect_saas_system_health_and_billing_gate', [
            'feature' => 'copy_phase_3_6',
        ], null, $superAdmin);
        $this->assertTrue((bool) ($t40['success'] ?? false));
        $this->assertArrayHasKey('system_health_pulse', $t40);

        // 2. Strict RBAC Isolation: midtrans_reviewer MUST be blocked from mutating AI tools & v10 Admin Pages/Resources
        $deniedTool = $engine->execute('manage_affiliate_and_impact_grants', [
            'action' => 'approve_grant',
            'application_id' => '01J00000000000000000000000',
        ], null, $midtransReviewer);
        $this->assertArrayHasKey('error', $deniedTool);

        $this->actingAs($midtransReviewer);
        $this->assertFalse(SaasSystemHealthPulsePage::canAccess());
        $this->assertFalse(PosVerifiedTestimonialResource::canAccess());

        $this->actingAs($superAdmin);
        $this->assertTrue(SaasSystemHealthPulsePage::canAccess());
        $this->assertTrue(PosVerifiedTestimonialResource::canAccess());

        // 3. Backend Admin Pages (SaasSystemHealthPulsePage, ProjectOsMatrixPage v10 tab, SmartGuideRetentionPage Section 17 ID & EN)
        Livewire::actingAs($superAdmin)
            ->test(SaasSystemHealthPulsePage::class)
            ->assertOk()
            ->assertSee('OVERALL PULSE STATUS');

        Livewire::actingAs($superAdmin)
            ->test(ProjectOsMatrixPage::class)
            ->set('activeTab', 'dual_zone_v10')
            ->assertOk()
            ->assertSee('UX_F1_SCROLLYTELLING');

        app()->setLocale('id');
        Livewire::actingAs($superAdmin)
            ->test(SmartGuideRetentionPage::class)
            ->assertOk()
            ->assertSee('sec-v10-dual-zone-aniwall-saas', false);

        app()->setLocale('en');
        Livewire::actingAs($superAdmin)
            ->test(SmartGuideRetentionPage::class)
            ->assertOk()
            ->assertSee('sec-v10-dual-zone-aniwall-saas', false);

        // 4. Cryptographic SHA-256 Hash Chain Integrity across all v10.0 AuditLog events
        $chainVerification = AuditLog::verifyChainIntegrity();
        $this->assertTrue((bool) ($chainVerification['valid'] ?? $chainVerification['is_valid'] ?? false));
    }
}
