<?php

namespace Tests\Feature;

use App\Models\VisionBlueprint;
use App\Services\PrdGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectOsTenPillarsTest extends TestCase
{
    use RefreshDatabase;
    public function test_software_factory_os_defines_all_10_pillars_at_100_percent_maturity(): void
    {
        $pillars = PrdGeneratorService::generateSoftwareFactoryOsPillars('Test Enterprise');

        $this->assertCount(10, $pillars);

        foreach ($pillars as $idx => $pillar) {
            $this->assertEquals($idx + 1, $pillar['pillar']);
            $this->assertStringContainsString('100%', $pillar['maturity_score']);
            $this->assertEquals('BULLETPROOF_PROVEN', $pillar['status']);
            $this->assertNotEmpty($pillar['capabilities']);
            $this->assertNotEmpty($pillar['world_class_standard']);
        }

        // Verify specific pillar titles
        $this->assertStringContainsString('Intake & Discovery', $pillars[0]['name']);
        $this->assertStringContainsString('PRD & Architecture', $pillars[1]['name']);
        $this->assertStringContainsString('Deterministic Database', $pillars[2]['name']);
        $this->assertStringContainsString('Automated Scaffolding', $pillars[3]['name']);
        $this->assertStringContainsString('Containerization', $pillars[4]['name']);
        $this->assertStringContainsString('Automated Testing', $pillars[5]['name']);
        $this->assertStringContainsString('CI/CD Pipeline', $pillars[6]['name']);
        $this->assertStringContainsString('Legal Contract', $pillars[7]['name']);
        $this->assertStringContainsString('Managed Sprint Capacity', $pillars[8]['name']);
        $this->assertStringContainsString('Client Transparency Portal', $pillars[9]['name']);
    }

    public function test_world_class_standard_advisory_provides_enterprise_options(): void
    {
        $advisory = PrdGeneratorService::generateWorldClassStandardAdvisory('Test Enterprise');

        $this->assertArrayHasKey('strategic_pillars', $advisory);
        $this->assertCount(5, $advisory['strategic_pillars']);
        $this->assertArrayHasKey('implementation_roadmap', $advisory);
        $this->assertCount(3, $advisory['implementation_roadmap']);

        // Check key enterprise topics
        $areas = array_column($advisory['strategic_pillars'], 'area');
        $this->assertStringContainsString('Skalabilitas & Kinerja Ekstrem', $areas[0]);
        $this->assertStringContainsString('Keamanan Tingkat FinTech', $areas[1]);
        $this->assertStringContainsString('Ketahanan & Ketersediaan', $areas[2]);
        $this->assertStringContainsString('Inteligensi Bisnis Berbasis AI', $areas[3]);
        $this->assertStringContainsString('Tata Kelola Rekayasa Perangkat Lunak', $areas[4]);
    }

    public function test_prd_markdown_renders_10_pillars_and_world_class_advisory_sections(): void
    {
        $blueprint = new VisionBlueprint([
            'id' => '01JBC000000000000000000000',
            'nama_bisnis' => 'Neriah Cloud OS',
            'client_name' => 'Enterprise Client',
            'email' => 'client@enterprise.com',
            'masalah_utama' => 'Efisiensi operasional manual',
            'tujuan_utama' => 'Otomatisasi bisnis skala jutaan data',
        ]);

        $prd = [
            'meta' => [
                'generated_at' => now()->toIso8601String(),
            ],
            'executive_summary' => [
                'problem_statement' => 'Efisiensi operasional manual',
                'success_metrics' => 'Otomatisasi bisnis skala jutaan data',
                'target_audience' => 'Pengguna internal & klien',
            ],
            'system_actors' => [],
            'features' => [
                'mvp_phase1' => [],
                'phase2_roadmap' => [],
            ],
            'software_factory_os_pillars' => PrdGeneratorService::generateSoftwareFactoryOsPillars('Neriah Cloud OS'),
            'world_class_standard_advisory' => PrdGeneratorService::generateWorldClassStandardAdvisory('Neriah Cloud OS'),
        ];

        $markdown = PrdGeneratorService::toMarkdown($blueprint, $prd);

        $this->assertStringContainsString('## 13. Matriks 10 Pilar Software Factory OS (100% Tingkat Kematangan & Standar Turnkey)', $markdown);
        $this->assertStringContainsString('100% Ultimate Maturity', $markdown);
        $this->assertStringContainsString('## 14. Rekomendasi & Opsi Strategis Menuju World Class Enterprise Standard', $markdown);
        $this->assertStringContainsString('Roadmap Implementasi Bertahap Menuju Skala Enterprise', $markdown);
    }
}
