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

    public function test_blueprint_show_renders_valid_javascript_without_entity_corruption(): void
    {
        $blueprint = \App\Models\VisionBlueprint::create([
            'nama_bisnis' => 'Apex Logistics Global',
            'client_name' => 'Alexander Wijaya',
            'email' => 'alexander@example.com',
            'phone' => '+6281234567890',
            'slug' => 'apex-logistics-global-prd',
            'masalah_utama' => 'Pelacakan armada',
            'tujuan_utama' => 'Efisiensi',
            'target_audiens' => 'B2B Logistics',
            'aktor_sistem' => 'Superadmin, Driver',
            'fitur_wajib' => 'GPS Tracker, Proof of Delivery',
            'alur_kerja' => 'Workflow tracking',
            'is_published' => true,
            'prd_content' => [
                'meta' => ['project_name' => 'Apex Logistics Global'],
                'features' => [
                    'mvp_phase1' => [['title' => 'GPS Tracking', 'desc' => 'Track armada']],
                    'phase2_roadmap' => [['title' => 'AI Routing', 'desc' => 'Optimize route']]
                ]
            ]
        ]);

        $response = $this->get(route('blueprint.show', ['slug' => $blueprint->slug]));
        $response->assertStatus(200);

        $content = $response->getContent();
        // Ensure no HTML entity corruption inside tierAmounts or script
        $this->assertStringNotContainsString('tierAmounts: {&quot;', $content);
        $this->assertStringContainsString('function blueprintApp()', $content);
        $this->assertStringContainsString('openScaffoldModal()', $content);
    }

    public function test_can_accurately_parse_and_synthesize_structured_brief_with_property_and_no_payment_gateway(): void
    {
        $brief = "Nama Bisnis\tBali Luxe Villa Living (atau nama usaha teman Anda)\n" .
                 "Masalah Utama\tPemasaran sewa dan jual villa di Bali saat ini masih manual via grup WA dan feed Instagram yang berantakan, membuat calon penyewa/investor sulit melihat ketersediaan unit dan galeri detail villa.\n" .
                 "Tujuan Utama\tMembangun website katalog katalog properti villa di Bali yang elegan, cepat dibuka di HP, dengan filter sewa/beli dan direct chat WhatsApp untuk survey lokasi.\n" .
                 "Fitur Wajib\t1. Katalog Listing Villa (Sewa & Beli) dengan Filter Lokasi & Fasilitas\n" .
                 "2. Halaman Detail Villa (Galeri Foto HD, Spesifikasi & Lokasi Maps)\n" .
                 "3. Tombol Inquiry Cepat ke WhatsApp Broker/Owner\n" .
                 "4. Dasbor Admin untuk Update Foto, Harga, & Status Villa\n" .
                 "Aktor Sistem\tSuperadmin (Pengelola Villa), Pengunjung Web (Penyewa / Pembeli)\n" .
                 "Kebutuhan Integrasi\tWhatsApp Click-to-Chat API, Google Maps Embed (tulis tanpa payment gateway)\n" .
                 "Alokasi Budget (Field 21)\tRp 5.000.000 - Rp 7.000.000 (Starter Catalog & Lead Gen)\n" .
                 "Target Waktu\t14 - 30 Hari Kerja";

        $response = $this->postJson('/api/blueprint/analyze-idea', [
            'idea_text' => $brief,
            'locale' => 'id'
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals('Bali Luxe Villa Living', $data['namaBisnis']);
        $this->assertEquals('property', $data['domain']);
        $this->assertStringContainsString('Pemasaran sewa dan jual villa di Bali', $data['masalahUtama']);
        $this->assertStringContainsString('Katalog Listing Villa', $data['fiturWajib']);
        $this->assertStringContainsString('Dasbor Admin', $data['fiturWajib']);
        $this->assertStringContainsString('Superadmin (Pengelola Villa)', $data['aktorSistem']);
        $this->assertStringContainsString('WhatsApp Click-to-Chat API', $data['kebutuhanIntegrasi']);
        $this->assertStringContainsString('Google Maps Embed', $data['kebutuhanIntegrasi']);
        $this->assertStringNotContainsString('Midtrans', $data['kebutuhanIntegrasi']);
        $this->assertStringNotContainsString('payment gateway', strtolower($data['kebutuhanIntegrasi']));
        $this->assertStringContainsString('5.000.000', $data['kisaranBudget']);
        $this->assertStringContainsString('14 - 30 Hari Kerja', $data['targetWaktu']);
        $this->assertEquals(100, $data['completeness']['score']);
    }
}



