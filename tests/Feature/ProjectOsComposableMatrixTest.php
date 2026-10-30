<?php

namespace Tests\Feature;

use App\Models\PosPrimitive;
use App\Models\PosProject;
use App\Models\PosRulePack;
use App\Models\PosSector;
use App\Models\VisionBlueprint;
use App\Services\MarkItDown\MarkItDownService;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Services\ScaffoldGeneratorService;
use Database\Seeders\ProjectOsMatrixSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectOsComposableMatrixTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_populates_twelve_sectors_twelve_primitives_and_rule_packs_with_strict_ulids(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);

        $this->assertSame(12, PosSector::query()->count());
        $this->assertSame(12, PosPrimitive::query()->count());
        $this->assertSame(6, PosRulePack::query()->count());

        $sector = PosSector::query()->where('code', 'SECTOR_01_COMMERCE_OMNICHANNEL')->with('primitives')->firstOrFail();
        $this->assertSame(26, strlen($sector->id));
        $this->assertNotEmpty($sector->primitives);

        $primitive = PosPrimitive::query()->where('code', 'PRIMITIVE_INVENTORY_LEDGER')->firstOrFail();
        $this->assertSame(26, strlen($primitive->id));
        $this->assertNotEmpty($primitive->schema_blueprints);
        $this->assertNotEmpty($primitive->state_machines);
        $this->assertNotEmpty($primitive->concurrency_contracts);
    }

    public function test_composite_business_resolves_cross_sector_primitives_automatically(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);

        $resolved = ProjectOsBlueprintCompilerService::resolveSectorAndPrimitives(
            'Klub Badminton terpadu dengan sistem booking sewa lapangan per jam, membership bulanan & akademi kelas pelatihan, serta kasir POS toko Pro Shop penjualan raket dan mutasi stok gudang.'
        );

        $this->assertNotNull($resolved['sector']);
        $this->assertSame('SECTOR_02_HOSPITALITY_SPACE', $resolved['sector']->code);
        $this->assertSame('COMPOSITE', $resolved['architecture_archetype']);

        $codes = $resolved['primitives']->pluck('code')->all();
        $this->assertContains('PRIMITIVE_TIME_SLOT_ENGINE', $codes);
        $this->assertContains('PRIMITIVE_SUBSCRIPTION_COHORT', $codes);
        $this->assertContains('PRIMITIVE_CATALOG_SKU', $codes);
        $this->assertContains('PRIMITIVE_INVENTORY_LEDGER', $codes);
        $this->assertContains('PRIMITIVE_DOUBLE_ENTRY_LEDGER', $codes);
    }

    public function test_vision_blueprint_api_accepts_react_island_camelcase_and_persists_pos_project_schema(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);

        $payload = [
            'client_name' => 'Budi Santoso',
            'client_email' => 'budi.smashclub@example.com',
            'client_whatsapp' => '81234567890',
            'client_whatsapp_code' => '+62',
            'namaBisnis' => 'Smash Arena Badminton & Pro Shop',
            'masalahUtama' => 'Sering terjadi bentrok jadwal booking lapangan dan selisih stok barang raket di toko klub.',
            'tujuanUtama' => 'Mengotomatiskan reservasi lapangan anti-bentrok, akademi member, dan POS stok Pro Shop.',
            'targetAudiens' => 'Pemain badminton komunitas, member akademi, dan kasir klub olahraga.',
            'aktorSistem' => "Owner Klub: Monitoring omzet & jurnal\nKasir Pro Shop: Transaksi POS & stok\nMember: Booking lapangan & langganan",
            'fiturWajib' => "Reservasi lapangan anti double-booking\nManajemen paket membership bulanan\nKasir POS penjualan perlengkapan olahraga",
            'alurKerja' => "1. Member memilih jadwal lapangan\n2. Pembayaran QRIS/Midtrans dikonfirmasi\n3. Kasir memproses pembelian raket dan memotong stok gudang",
            'primarySector' => 'SECTOR_02_HOSPITALITY_SPACE',
            'selectedPrimitives' => [
                'PRIMITIVE_TIME_SLOT_ENGINE',
                'PRIMITIVE_SUBSCRIPTION_COHORT',
                'PRIMITIVE_CATALOG_SKU',
                'PRIMITIVE_INVENTORY_LEDGER',
                'PRIMITIVE_DOUBLE_ENTRY_LEDGER',
            ],
            'stackPreset' => 'ANTI_SLOP_ENTERPRISE_V1',
        ];

        $response = $this->postJson('/api/vision-blueprint', $payload);
        $response->assertStatus(201);
        $response->assertJsonPath('success', true);

        $blueprintId = $response->json('data.id');
        $blueprint = VisionBlueprint::query()->findOrFail($blueprintId);

        $this->assertSame('Smash Arena Badminton & Pro Shop', $blueprint->nama_bisnis);
        $this->assertSame('budi.smashclub@example.com', $blueprint->email);
        $this->assertNotNull($blueprint->posProject);
        $this->assertSame(26, strlen($blueprint->posProject->id));
        $this->assertSame('COMPOSITE', $blueprint->posProject->architecture_archetype);

        $schema = $blueprint->prd_content['universal_blueprint_schema'] ?? null;
        $this->assertIsArray($schema);
        $this->assertSame('https://neriahpro.com/schemas/project-os-v1.json', $schema['$schema']);
        $this->assertNotEmpty($schema['data_dictionary']);
        $this->assertNotEmpty($schema['state_machines']);
        $this->assertNotEmpty($schema['domain_invariants']['concurrency_locks']);
        $this->assertNotEmpty($schema['rbac_matrix']['permissions']);
        $this->assertNotEmpty($schema['edge_case_playbooks']);

        // Verify public endpoints
        $this->get('/api/blueprint/matrix-catalog')
            ->assertStatus(200)
            ->assertJsonCount(12, 'catalog.sectors')
            ->assertJsonCount(12, 'catalog.primitives');

        $this->get(route('blueprint.download-schema-json', $blueprint->slug))
            ->assertStatus(200)
            ->assertJsonPath('project_metadata.primary_sector', 'SECTOR_02_HOSPITALITY_SPACE');

        $execMdResp = $this->get(route('blueprint.download-cursorrules', [
            'slug' => $blueprint->slug,
            'format' => 'execution-blueprint',
        ]));
        $execMdResp->assertStatus(200);
        $this->assertStringContainsString('<ZERO_ASSUMPTION_DIRECTIVES>', $execMdResp->getContent());

        $this->get(route('blueprint.show', $blueprint->slug))
            ->assertStatus(200)
            ->assertSee('SECTOR_02_HOSPITALITY_SPACE');
    }

    public function test_scaffold_generator_emits_zero_assumption_contracts_enums_and_pessimistic_lock_actions(): void
    {
        $this->seed(ProjectOsMatrixSeeder::class);

        $blueprint = VisionBlueprint::query()->create([
            'client_name' => 'Dr. Andi Wijaya',
            'email' => 'andi.klinik@example.com',
            'phone' => '+6281122334455',
            'nama_bisnis' => 'Klinik Medika Prima EMR & Apotek',
            'masalah_utama' => 'Rekam medis masih kertas dan stok obat apotek sering kedaluwarsa tanpa pelacakan batch.',
            'tujuan_utama' => 'Digitalisasi EMR klinis dengan audit trail ketat, antrean dokter, resep obat FEFO, dan kasir.',
            'target_audiens' => 'Dokter, Apoteker, Perawat, dan Pasien.',
            'aktor_sistem' => "Dokter: Input SOAP EMR & e-Resep\nApoteker: Dispensing obat batch FEFO\nKasir: Pembayaran klaim & kuitansi",
            'fitur_wajib' => "Rekam medis elektronik SOAP terkunci\nManajemen stok obat apotek batch expiry\nTagihan pasien & jurnal akuntansi",
            'alur_kerja' => "1. Pasien daftar antrean\n2. Dokter isi SOAP & tanda tangan\n3. Apoteker serahkan obat & potong stok batch",
            'user_metadata' => [
                'primary_sector' => 'SECTOR_03_HEALTHCARE_CLINICAL',
            ],
        ]);
        $blueprint->generateAndSavePrd();

        $files = app(ScaffoldGeneratorService::class)->generateFiles($blueprint->fresh());

        $this->assertArrayHasKey('PROJECT_OS_EXECUTION_BLUEPRINT.md', $files);
        $this->assertArrayHasKey('.agents/BLUEPRINT_SCHEMA.json', $files);
        $this->assertArrayHasKey('.agents/UI_UX_CONTRACT.md', $files);
        $this->assertArrayHasKey('.agents/ENGINEERING_RULES.md', $files);
        $this->assertArrayHasKey('.agents/AGENT_HARNESS_ECC.md', $files);
        $this->assertArrayHasKey('.agents/graphify/graph.json', $files);
        $this->assertArrayHasKey('railpack.json', $files);
        $this->assertArrayHasKey('Caddyfile', $files);
        $this->assertArrayHasKey('tests/Feature/DeterministicStateMachineContractTest.php', $files);

        $this->assertStringContainsString('<ZERO_ASSUMPTION_DIRECTIVES>', $files['PROJECT_OS_EXECUTION_BLUEPRINT.md']);
        $this->assertStringContainsString('rounded-full', $files['.agents/UI_UX_CONTRACT.md']);
        $this->assertStringContainsString('cursorPaginate', $files['.agents/ENGINEERING_RULES.md']);

        $enumFiles = array_filter(array_keys($files), fn (string $k) => str_starts_with($k, 'app/Enums/') && str_ends_with($k, '.php'));
        $actionFiles = array_filter(array_keys($files), fn (string $k) => str_starts_with($k, 'app/Actions/') && str_ends_with($k, 'Action.php'));

        $this->assertNotEmpty($enumFiles);
        $this->assertNotEmpty($actionFiles);

        $firstActionContent = $files[array_values($actionFiles)[0]];
        $this->assertStringContainsString('DB::transaction', $firstActionContent);
        $this->assertStringContainsString('lockForUpdate()', $firstActionContent);
    }

    public function test_markitdown_converts_local_documents_with_zero_ai_tokens_and_clean_image_ocr_fallback(): void
    {
        $service = app(MarkItDownService::class);

        $tmpCsv = tempnam(sys_get_temp_dir(), 'pos_csv_') . '.csv';
        file_put_contents($tmpCsv, "sku,nama_produk,stok\nRKT-01,Raket Carbon Pro,25\nSHUT-02,Shuttlecock Feather,120\n");

        $csvResult = $service->convert($tmpCsv, ['filename' => 'stok_proshop.csv']);
        @unlink($tmpCsv);

        $this->assertTrue($csvResult['zero_ai_tokens'] ?? false);
        $this->assertStringContainsString('Raket Carbon Pro', $csvResult['markdown'] ?? '');

        // Create minimal valid 1x1 PNG to verify 3-Tier Image OCR pipeline without burning AI tokens when allow_vision_ocr=false
        $tmpPng = tempnam(sys_get_temp_dir(), 'pos_img_') . '.png';
        file_put_contents(
            $tmpPng,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')
        );

        $imgResult = $service->convert($tmpPng, ['filename' => 'wireframe_dashboard.png', 'allow_vision_ocr' => false]);
        @unlink($tmpPng);

        $this->assertTrue($imgResult['zero_ai_tokens'] ?? false);
        $this->assertStringNotContainsString('CV Pro', $imgResult['markdown'] ?? '');
        $this->assertStringContainsString('wireframe_dashboard.png', $imgResult['markdown'] ?? '');
    }
}

