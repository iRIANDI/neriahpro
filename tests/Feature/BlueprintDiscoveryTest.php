<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class BlueprintDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_synthesize_idea_from_raw_text()
    {
        $response = $this->postJson('/api/blueprint/analyze-idea', [
            'idea_text' => 'Saya ingin membangun platform ekspedisi dan manajemen armada truk antar pulau dengan pelacakan kontainer dan faktur digital.',
            'locale' => 'id'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'draft_id',
            'redirect_url',
            'data' => [
                'namaBisnis',
                'masalahUtama',
                'tujuanUtama',
                'targetAudiens',
                'aktorSistem',
                'fiturWajib',
                'fiturTambahan',
                'alurKerja',
                'kebutuhanIntegrasi',
                'outOfScope',
                'kisaranBudget',
            ]
        ]);

        $draftId = $response->json('draft_id');
        $this->assertNotEmpty($draftId);
        $this->assertTrue(Cache::has('blueprint_draft_' . $draftId));

        // Test that blueprint create page renders with preloaded draft data
        $viewResponse = $this->get('/blueprint?draft_id=' . $draftId);
        $viewResponse->assertStatus(200);
    }

    public function test_anti_spam_rejects_empty_short_input()
    {
        $response = $this->postJson('/api/blueprint/analyze-idea', [
            'idea_text' => 'halo test',
        ]);

        $response->assertStatus(422);
    }

    public function test_anti_spam_honeypot_silently_intercepts_bots()
    {
        $response = $this->postJson('/api/blueprint/analyze-idea', [
            'idea_text' => 'Saya ingin membuat sistem reservasi hotel dan penerbangan.',
            '_hp_check' => 'I am a spam bot',
        ]);

        $response->assertStatus(200);
        $this->assertEquals(route('blueprint.create'), $response->json('redirect_url'));
    }

    public function test_markitdown_converts_uploaded_text_document()
    {
        $file = UploadedFile::fake()->createWithContent(
            'spesifikasi_proyek.txt',
            "# Sistem Klinik Medis Digital\nMasalah: Antrean pasien menumpuk dan rekam medis kertas sering hilang.\nFitur: Pendaftaran online, Rekam Medis Elektronik, Apotek."
        );

        $response = $this->postJson('/api/blueprint/analyze-idea', [
            'idea_text' => 'Berikut berkas lampiran sistem klinik medis.',
            'files' => [$file],
            'locale' => 'id'
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data['namaBisnis']);
        $this->assertNotEmpty($data['fiturWajib']);
        $this->assertEquals(1, $data['_meta']['files_processed']);
        $this->assertNotEmpty($data['proactive_suggestions']);
        $this->assertArrayHasKey('completeness', $data);
    }

    public function test_can_supplement_idea_proactively()
    {
        $blueprint = [
            'namaBisnis' => 'Aplikasi Logistik Ekspres',
            'masalahUtama' => 'Pengiriman barang terlambat tanpa status resi yang akurat.',
            'tujuanUtama' => 'Meningkatkan kepuasan pelanggan dengan tracking live.',
            'aktorSistem' => "1. Superadmin\n2. Driver",
            'fiturWajib' => "1. Manajemen Armada Truk\n2. Pelacakan GPS",
            'fiturTambahan' => "1. AI Route Optimizer",
            'alurKerja' => "1. Order dibuat\n2. Driver berangkat\n3. Barang sampai",
            'kebutuhanIntegrasi' => 'Google Maps API',
            'outOfScope' => 'Tidak membuat app native store',
        ];

        $response = $this->postJson('/api/blueprint/supplement-idea', [
            'supplement_text' => 'Tolong tambahkan cetak struk kasir thermal dan notifikasi WhatsApp otomatis saat armada tiba',
            'blueprint' => $blueprint,
            'locale' => 'id'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'affected_fields',
            'data'
        ]);

        $affected = $response->json('affected_fields');
        $this->assertContains('fiturWajib', $affected);
        $this->assertContains('kebutuhanIntegrasi', $affected);

        $data = $response->json('data');
        $this->assertStringContainsString('WhatsApp', $data['kebutuhanIntegrasi']);
        $this->assertStringContainsString('cetak struk', $data['fiturWajib']);
        $this->assertArrayHasKey('completeness', $data);
    }
}

