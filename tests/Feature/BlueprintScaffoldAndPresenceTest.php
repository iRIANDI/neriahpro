<?php

namespace Tests\Feature;

use App\Models\VisionBlueprint;
use App\Models\User;
use App\Services\PrdGeneratorService;
use App\Services\ScaffoldGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlueprintScaffoldAndPresenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_scaffold_generator_service_produces_all_files(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Dr. Hendra Wijaya',
            'email' => 'hendra@example.com',
            'whatsapp' => '628123456789',
            'nama_bisnis' => 'Klinik Sehat Sejahtera',
            'tipe_aplikasi' => 'Web App / SaaS Platform',
            'target_pengguna' => 'Pasien & Dokter',
            'masalah_utama' => 'Antrean klinik membludak dan rekam medis manual',
            'solusi_diinginkan' => 'Aplikasi booking dokter dan rekam medis online',
            'target_waktu' => '30 Hari Kerja',
            'is_published' => true,
            'user_metadata' => [
                'mvp_features' => ['Booking Antrean Pasien', 'Rekam Medis Elektronik', 'Notifikasi WhatsApp Dokter'],
                'skala_pengguna' => '10.000 - 50.000 User',
            ],
        ]);

        $scaffoldService = new ScaffoldGeneratorService();
        $files = $scaffoldService->generateFiles($blueprint);

        $this->assertArrayHasKey('docker-compose.yml', $files);
        $this->assertArrayHasKey('database/migrations/schema_complete.sql', $files);
        $this->assertArrayHasKey('routes/web.php', $files);
        $this->assertArrayHasKey('routes/api.php', $files);
        $this->assertArrayHasKey('app/api/route.ts', $files);
        $this->assertArrayHasKey('README.md', $files);
        $this->assertArrayHasKey('.env.example', $files);

        // Assert Docker Compose contains PostgreSQL 16 & Redis 7
        $this->assertStringContainsString('postgres:16-alpine', $files['docker-compose.yml']);
        $this->assertStringContainsString('redis:7-alpine', $files['docker-compose.yml']);

        // Assert SQL Migration contains strict ULID primary keys
        $this->assertStringContainsString('VARCHAR(26) PRIMARY KEY', $files['database/migrations/schema_complete.sql']);

        // Assert Next.js Route contains App Router handlers
        $this->assertStringContainsString('export async function GET', $files['app/api/route.ts']);
        $this->assertStringContainsString('NextResponse.json', $files['app/api/route.ts']);
    }

    public function test_scaffold_preview_endpoint_returns_json_files(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'whatsapp' => '628111222333',
            'nama_bisnis' => 'Toko Mart Retail',
            'tipe_aplikasi' => 'E-Commerce Retail',
            'target_pengguna' => 'Pelanggan Toko',
            'masalah_utama' => 'Stok sering selisih',
            'solusi_diinginkan' => 'Sistem POS dan manajemen stok terpusat',
            'target_waktu' => '30 Hari Kerja',
            'is_published' => true,
        ]);

        $response = $this->getJson(route('blueprint.scaffold-preview', $blueprint->slug));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'slug',
            'files' => [
                'docker-compose.yml',
                'schema_complete.sql',
                'routes/web.php',
                'routes/api.php',
                'app/api/route.ts',
                'README.md',
            ],
        ]);
    }

    public function test_scaffold_export_downloads_zip(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Siti Rahma',
            'email' => 'siti@example.com',
            'whatsapp' => '628999888777',
            'nama_bisnis' => 'EduLearn Online',
            'tipe_aplikasi' => 'LMS / Edu Tech',
            'target_pengguna' => 'Siswa & Guru',
            'masalah_utama' => 'Materi belajar tidak terstruktur',
            'solusi_diinginkan' => 'Platform kursus video dan kuis interaktif',
            'target_waktu' => '30 Hari Kerja',
            'is_published' => true,
        ]);

        $response = $this->get(route('blueprint.export-scaffold', $blueprint->slug));

        $response->assertOk();
        $this->assertEquals('application/zip', $response->headers->get('content-type'));
        $this->assertStringContainsString('starter-kit.zip', $response->headers->get('content-disposition'));
    }

    public function test_presence_heartbeat_api_updates_and_returns_collaborators(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Andi Pratama',
            'email' => 'andi@example.com',
            'whatsapp' => '628555666777',
            'nama_bisnis' => 'FinTrack Pro',
            'tipe_aplikasi' => 'Fintech SaaS',
            'target_pengguna' => 'Akuntan & UMKM',
            'masalah_utama' => 'Pencatatan keuangan lambat',
            'solusi_diinginkan' => 'Aplikasi pembukuan otomatis AI',
            'target_waktu' => '30 Hari Kerja',
            'is_published' => true,
        ]);

        // Post presence update from client
        $response = $this->postJson(route('api.blueprint.presence.update', $blueprint->slug), [
            'client_id' => 'client_test_123',
            'x' => 45,
            'y' => 60,
            'section' => 'section-6',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'slug',
            'collaborators',
            'timestamp',
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['collaborators']);

        // Check GET presence endpoint
        $getResponse = $this->getJson(route('api.blueprint.presence.get', $blueprint->slug));
        $getResponse->assertOk();
        $this->assertTrue($getResponse->json('success'));
    }

    public function test_ai_security_blueprint_and_20_concepts_are_generated(): void
    {
        $sec = PrdGeneratorService::generateAiSecurityBlueprint('Neriah Pro Platform');
        $this->assertArrayHasKey('incident_case_study', $sec);
        $this->assertArrayHasKey('defensive_modules', $sec);
        $this->assertCount(3, $sec['defensive_modules']);

        $matrix = PrdGeneratorService::generateAgenticAiConceptsMatrix('Neriah Pro Platform', ['Feature A', 'Feature B']);
        $this->assertEquals(20, $matrix['total_concepts']);
        $this->assertGreaterThan(0, $matrix['recommended_for_project']);
        $this->assertCount(20, $matrix['concepts']);
    }
}
