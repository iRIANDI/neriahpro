<?php

namespace Tests\Unit;

use App\Services\BlueprintKpiCascadeService;
use PHPUnit\Framework\TestCase;

class BlueprintKpiCascadeServiceTest extends TestCase
{
    public function test_elaborates_kpis_and_cascades_impacts_deterministically(): void
    {
        $service = new BlueprintKpiCascadeService();

        $payload = [
            'tujuanUtama' => "1. Otomatisasi reservasi online 24/7\n2. AI Itinerary Generator",
            'masalahUtama' => 'Penanganan reservasi masih manual dan sering terjadi jadwal bentrok.',
            'namaBisnis' => 'Bali Tour & Transport',
            'deskripsiBisnis' => 'Layanan sewa mobil dan tour harian di Bali.',
            'primarySector' => 'tourism_travel',
            'locale' => 'id',
        ];

        $result = $service->elaborateAndCascade($payload);

        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['kpis']);
        $this->assertCount(2, $result['kpis']);

        $kpi1 = $result['kpis'][0];
        $this->assertEquals(1, $kpi1['id']);
        $this->assertNotEmpty($kpi1['baseline']);
        $this->assertNotEmpty($kpi1['target']);
        $this->assertNotEmpty($kpi1['metric']);
        $this->assertNotEmpty($kpi1['verification']);
        $this->assertNotEmpty($kpi1['synthesized_text']);

        // Cascaded impacts
        $cascades = $result['cascaded_impacts'];
        $this->assertNotEmpty($cascades['features']);
        $this->assertNotEmpty($cascades['actors']);
        $this->assertNotEmpty($cascades['acceptance_criteria']);
        $this->assertNotEmpty($cascades['roi_summary']);
    }

    public function test_handles_english_locale_properly(): void
    {
        $service = new BlueprintKpiCascadeService();

        $payload = [
            'tujuanUtama' => "1. Automate 24/7 online booking\n2. Reduce fleet dispatch turnaround",
            'masalahUtama' => 'Manual dispatch coordination creates operational delays.',
            'namaBisnis' => 'Global Logistics Hub',
            'primarySector' => 'logistics',
            'locale' => 'en',
        ];

        $result = $service->elaborateAndCascade($payload);

        $this->assertTrue($result['success']);
        $this->assertEquals('en', $result['locale']);
        $this->assertStringContainsString('Target', $result['kpis'][0]['synthesized_text']);
        $this->assertNotEmpty($result['cascaded_impacts']['features']);
    }
}
