<?php

namespace Tests\Feature;

use App\Models\VisionBlueprint;
use App\Services\BlueprintDiscoveryService;
use App\Services\PrdGeneratorService;
use App\Services\ScaffoldGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectOsMultiPlatformAndSprintTasksTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test WhatsApp Gateway project generation includes Node.js, BullMQ, Baileys & Sprint Tasks.
     */
    public function test_whatsapp_gateway_generates_comprehensive_scaffold_and_sprint_tasks(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Pak Hendro',
            'email' => 'hendro@gateway.local',
            'whatsapp' => '628123456789',
            'nama_bisnis' => 'WAPush Gateway Enterprise',
            'tipe_aplikasi' => 'SaaS API Gateway WhatsApp OpenWA Baileys',
            'target_pengguna' => 'Developer & Korporat',
            'masalah_utama' => 'Butuh API gateway kirim pesan WhatsApp massal cepat tanpa banned',
            'solusi_diinginkan' => 'Platform SaaS WhatsApp API Gateway dengan BullMQ dan webhook HMAC',
            'target_waktu' => '30 Hari Kerja',
            'is_published' => true,
        ]);

        $prd = PrdGeneratorService::generate($blueprint);
        $blueprint->update(['prd_content' => $prd]);

        $files = ScaffoldGeneratorService::generateFiles($blueprint);

        // 1. Assert Sprint Tasks files exist
        $this->assertArrayHasKey('sprint-tasks/README.md', $files);
        $this->assertArrayHasKey('sprint-tasks/01_foundation_database_and_core.md', $files);
        $this->assertArrayHasKey('sprint-tasks/99_quality_gate_and_deployment.md', $files);
        $this->assertStringContainsString('JANGAN MENYALIN SELURUH PRD SEKALIGUS', $files['sprint-tasks/README.md']);

        // 2. Assert WhatsApp Gateway specific scaffold files
        $this->assertArrayHasKey('gateway/package.json', $files);
        $this->assertArrayHasKey('gateway/src/index.js', $files);
        $this->assertArrayHasKey('gateway/src/whatsapp-session.js', $files);
        $this->assertArrayHasKey('gateway/Dockerfile', $files);
        $this->assertStringContainsString('bullmq', $files['gateway/package.json']);
        $this->assertStringContainsString('/api/v1/messages/send', $files['gateway/src/index.js']);

        // 3. Assert Individual Eloquent migrations & models exist
        $hasMigration = false;
        $hasModel = false;
        foreach (array_keys($files) as $filePath) {
            if (str_starts_with($filePath, 'database/migrations/2026_01_01_') && str_ends_with($filePath, '.php')) {
                $hasMigration = true;
            }
            if (str_starts_with($filePath, 'app/Models/') && str_ends_with($filePath, '.php')) {
                $hasModel = true;
                $this->assertStringContainsString('use HasFactory, HasUlids;', $files[$filePath]);
            }
        }
        $this->assertTrue($hasMigration, 'Scaffold must contain individual Eloquent migrations');
        $this->assertTrue($hasModel, 'Scaffold must contain individual Eloquent models with HasUlids');

        // 4. Assert Playwright E2E contains WhatsApp gateway protocol
        $this->assertStringContainsString('WhatsApp Session Engine QR & Ingress Security Protocol', $files['tests/e2e/critical-flows.spec.ts']);
    }

    /**
     * Test 3D Flight Simulator game generation includes Three.js, HUD telemetry & WebGL Canvas.
     */
    public function test_flight_simulator_3d_generates_threejs_and_hud_scaffold(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Kapten Danu',
            'email' => 'danu@aerosim.local',
            'whatsapp' => '628198765432',
            'nama_bisnis' => 'AeroSim 3D Flight Game',
            'tipe_aplikasi' => 'Video Game 3D Pesawat Flight Simulator WebGL',
            'target_pengguna' => 'Gamer & Pilot Trainee',
            'masalah_utama' => 'Simulator penerbangan web yang ringan 60fps dengan HUD instrumen lengkap',
            'solusi_diinginkan' => 'Game 3D pesawat Three.js WebGL/WebGPU dengan fisika 6-DOF dan instrumen altimeter',
            'target_waktu' => '45 Hari Kerja',
            'is_published' => true,
        ]);

        $prd = PrdGeneratorService::generate($blueprint);
        $blueprint->update(['prd_content' => $prd]);

        $files = ScaffoldGeneratorService::generateFiles($blueprint);

        // 1. Assert Game specific scaffold files
        $this->assertArrayHasKey('game/package.json', $files);
        $this->assertArrayHasKey('game/index.html', $files);
        $this->assertArrayHasKey('game/src/flight-sim.js', $files);
        $this->assertArrayHasKey('game/Dockerfile', $files);
        $this->assertStringContainsString('three', $files['game/package.json']);
        $this->assertStringContainsString('INSTRUMENTS HUD', $files['game/index.html']);
        $this->assertStringContainsString('flight-canvas', $files['game/index.html']);
        $this->assertStringContainsString('THREE.WebGLRenderer', $files['game/src/flight-sim.js']);

        // 2. Assert Playwright E2E verifies 60 FPS flight canvas & HUD
        $this->assertStringContainsString('3D Flight Simulation Canvas & Telemetry HUD initialize at 60 FPS', $files['tests/e2e/critical-flows.spec.ts']);
    }

    /**
     * Test Decoupled SaaS generation includes Next.js 15 & typed API client.
     */
    public function test_decoupled_saas_generates_nextjs_and_api_client(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Sinta Wijaya',
            'email' => 'sinta@decoupled.local',
            'whatsapp' => '628155566677',
            'nama_bisnis' => 'OmniDesk Decoupled SaaS',
            'tipe_aplikasi' => 'Decoupled Web App (Frontend terpisah dari Backend)',
            'target_pengguna' => 'Enterprise Customers',
            'masalah_utama' => 'Frontend butuh kecepatan edge sub-20ms sedangkan backend Laravel headless',
            'solusi_diinginkan' => 'Next.js 15 App Router frontend terpisah dari Headless Laravel 13 API',
            'target_waktu' => '30 Hari Kerja',
            'is_published' => true,
        ]);

        $prd = PrdGeneratorService::generate($blueprint);
        $blueprint->update(['prd_content' => $prd]);

        $files = ScaffoldGeneratorService::generateFiles($blueprint);

        $this->assertArrayHasKey('frontend/package.json', $files);
        $this->assertArrayHasKey('frontend/src/app/page.tsx', $files);
        $this->assertArrayHasKey('frontend/src/lib/api-client.ts', $files);
        $this->assertStringContainsString('next', $files['frontend/package.json']);
        $this->assertStringContainsString('fetchCursorPage', $files['frontend/src/lib/api-client.ts']);
    }

    /**
     * Test Hierarchical Map-Reduce Digest prevents middle-content truncation on >15k chars.
     */
    public function test_large_corpus_hierarchical_digest_preserves_middle_chapters(): void
    {
        // Build 25,000 character corpus with critical spec in the exact middle (around 12,000 - 15,000 char mark)
        $intro = str_repeat("Bab 1: Pendahuluan dan Latar Belakang Bisnis Umum. ", 150); // ~7.5k chars
        $middleChapter = "\n\n## Bab 4: Spesifikasi Fungsional Inti (Krusial)\n- Fitur Rahasia: Algoritma Pencocokan Dinamis AI.\n- Tabel Database: dynamic_matcher_engines dan candidate_profiles.\n- Parameter: Strict O(1) latency under 5ms.\n\n";
        $outro = str_repeat("Bab 8: Lampiran dan Daftar Istilah Tambahan Regulasi. ", 150); // ~7.5k chars

        $largeDocument = $intro . $middleChapter . $outro;
        $this->assertGreaterThan(15000, mb_strlen($largeDocument));

        $blueprint = VisionBlueprint::create([
            'client_name' => 'Direktur Utama',
            'email' => 'dirut@enterprise.local',
            'whatsapp' => '628177788899',
            'nama_bisnis' => 'Mega Enterprise Matcher',
            'tipe_aplikasi' => 'Enterprise System',
            'target_pengguna' => 'Internal HR',
            'masalah_utama' => 'Dokumen TOR raksasa 40 halaman',
            'solusi_diinginkan' => 'Sistem pemroses data dengan dokumen TOR raksasa',
            'target_waktu' => '60 Hari Kerja',
            'is_published' => true,
            'dokumen_lampiran' => $largeDocument,
        ]);

        // 1. Verify Hierarchical Map-Reduce Digest directly preserves middle chapter
        $digest = BlueprintDiscoveryService::extractComprehensiveCorpusDigest($largeDocument, 15000);
        $this->assertLessThanOrEqual(15000, mb_strlen($digest));
        $this->assertStringContainsString('Spesifikasi Fungsional Inti (Krusial)', $digest);
        $this->assertStringContainsString('dynamic_matcher_engines', $digest);

        // 2. Verify Synthesis succeeds on large corpus
        $discoveryService = app(BlueprintDiscoveryService::class);
        $synthesis = $discoveryService->synthesize($largeDocument, [], 'id', 'Mega Enterprise Matcher');

        $this->assertIsArray($synthesis);
        $this->assertNotEmpty($synthesis['masalahUtama']);
        $this->assertNotEmpty($synthesis['tujuanUtama']);
        $this->assertNotEmpty($synthesis['fiturWajib']);
    }
}
