<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VisionBlueprint;
use App\Models\BlueprintVoucher;
use App\Mail\BlueprintReadyNotificationMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PackageLimitsAndErrorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /**
     * Test that Spark free tier strictly enforces the 2x per month limit.
     */
    public function test_spark_free_tier_enforces_two_times_per_month_limit(): void
    {
        $ip = '203.0.113.42';
        $monthKey = 'spark_quota_' . md5($ip) . '_' . now()->format('Y_m');

        // Initial state: usage is 0
        $this->assertEquals(0, (int) Cache::get($monthKey, 0));

        // 1st request succeeds
        $res1 = $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->postJson('/api/blueprint/analyze-idea', [
                'idea_text' => 'Ide sistem manajemen inventaris gudang logistik pintar.',
            ]);
        $res1->assertStatus(200);
        $this->assertEquals(1, (int) Cache::get($monthKey, 0));

        // 2nd request succeeds
        $res2 = $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->postJson('/api/blueprint/analyze-idea', [
                'idea_text' => 'Ide marketplace lelang kendaraan online terpercaya.',
            ]);
        $res2->assertStatus(200);
        $this->assertEquals(2, (int) Cache::get($monthKey, 0));

        // 3rd request MUST be rejected with HTTP 429
        $res3 = $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->postJson('/api/blueprint/analyze-idea', [
                'idea_text' => 'Ide aplikasi akuntansi multi cabang otomatis.',
            ]);
        $res3->assertStatus(429);
        $res3->assertJson([
            'success' => false,
            'error_code' => 'SPARK_QUOTA_EXCEEDED',
            'limit' => 2,
            'current_usage' => 2,
        ]);
        $this->assertStringContainsString('Batas kuota gratis paket Spark', $res3->json('message'));
    }

    /**
     * Test that Super Admin is exempt from Spark quota limit.
     */
    public function test_superadmin_is_exempt_from_spark_quota_limit(): void
    {
        $superadmin = User::create([
            'name' => 'Yoseph Iriandi Tambunan',
            'email' => 'yoseph.iriandi.tambunan@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($superadmin);

        // Pre-fill usage to 5
        $monthKey = 'spark_quota_' . md5($superadmin->email) . '_' . now()->format('Y_m');
        Cache::put($monthKey, 5, now()->addDays(10));

        // Superadmin request still succeeds
        $response = $this->postJson('/api/blueprint/analyze-idea', [
            'idea_text' => 'Ide arsitektur core banking high-throughput.',
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test that Lite package enforces 30-day revision window and rejects expired modifications.
     */
    public function test_lite_package_enforces_thirty_day_revision_window(): void
    {
        // Create an existing blueprint belonging to 'lite' tier, created 35 days ago
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Budi Santoso',
            'nama_bisnis' => 'Logistik Cepat 30 Hari',
            'email' => 'budi@logistikcepat.com',
            'masalah_utama' => 'Masalah pelacakan armada armada.',
            'tujuan_utama' => 'Otomasi rute delivery.',
            'target_audiens' => 'Driver dan Admin.',
            'aktor_sistem' => 'Admin, Driver, Pelanggan.',
            'fitur_wajib' => 'GPS real time, POD digital.',
            'alur_kerja' => 'Driver scan resi lalu jalan.',
            'user_metadata' => [
                'retail_tier' => 'lite',
            ],
        ]);

        \Illuminate\Support\Facades\DB::table('vision_blueprints')
            ->where('id', $blueprint->id)
            ->update(['created_at' => now()->subDays(35)]);

        $blueprint->refresh();

        // Attempt to supplement idea on expired blueprint
        $response = $this->postJson('/api/blueprint/supplement-idea', [
            'supplement_text' => 'Tambahkan fitur chat langsung antara driver dan customer.',
            'blueprint' => [
                '_meta' => [
                    'is_editing_slug' => $blueprint->slug,
                ],
                'namaBisnis' => $blueprint->nama_bisnis,
            ],
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'error_code' => 'REVISION_WINDOW_EXPIRED',
        ]);
        $this->assertStringContainsString('Jendela revisi 30 hari untuk paket Lite telah berakhir', $response->json('message'));
    }

    /**
     * Test Spark free tier auto-resets on the 1st of the next month.
     */
    public function test_spark_quota_resets_automatically_on_new_month_cycle(): void
    {
        $ip = '203.0.113.88';

        // 1. Consume 2 free analyses in current month
        $currentMonthKey = 'spark_quota_' . md5($ip) . '_' . now()->format('Y_m');
        Cache::put($currentMonthKey, 2, now()->endOfMonth()->addDay());

        // 3rd attempt in current month is throttled
        $resThrottled = $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->postJson('/api/blueprint/analyze-idea', [
                'idea_text' => 'Ide marketplace lokal.',
            ]);
        $resThrottled->assertStatus(429);

        // 2. Simulate month rollover (1st of next month)
        \Carbon\Carbon::setTestNow(now()->addMonth()->startOfMonth());

        $newMonthKey = 'spark_quota_' . md5($ip) . '_' . now()->format('Y_m');
        $this->assertEquals(0, (int) Cache::get($newMonthKey, 0));

        // 3. First request in new month succeeds immediately
        $resNewMonth = $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->postJson('/api/blueprint/analyze-idea', [
                'idea_text' => 'Ide sistem ERP terdistribusi.',
            ]);
        $resNewMonth->assertStatus(200);
        $this->assertEquals(1, (int) Cache::get($newMonthKey, 0));

        \Carbon\Carbon::setTestNow(); // Reset time mock
    }

    /**
     * Test Lite package allows modifications within its 30-day revision window.
     */
    public function test_lite_package_allows_modifications_within_thirty_days(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Budi Santoso',
            'nama_bisnis' => 'Logistik Cepat 15 Hari',
            'email' => 'budi15@logistikcepat.com',
            'masalah_utama' => 'Masalah armada pengiriman.',
            'tujuan_utama' => 'Otomasi rute delivery.',
            'target_audiens' => 'Driver dan Admin.',
            'aktor_sistem' => 'Admin, Driver.',
            'fitur_wajib' => 'GPS real time, POD digital.',
            'alur_kerja' => 'Driver scan resi.',
            'user_metadata' => [
                'retail_tier' => 'lite',
            ],
        ]);

        \Illuminate\Support\Facades\DB::table('vision_blueprints')
            ->where('id', $blueprint->id)
            ->update(['created_at' => now()->subDays(15)]);

        $blueprint->refresh();

        $response = $this->postJson('/api/blueprint/supplement-idea', [
            'supplement_text' => 'Tambahkan push notifikasi otomatis ke WhatsApp customer.',
            'blueprint' => [
                '_meta' => [
                    'is_editing_slug' => $blueprint->slug,
                ],
                'namaBisnis' => $blueprint->nama_bisnis,
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Test Pro package allows revisions at day 45 but rejects past 180 days (6 months).
     */
    public function test_pro_package_allows_modifications_at_day_45_but_rejects_past_180_days(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Siti Rahma',
            'nama_bisnis' => 'Retail Pro Fashion Hub',
            'email' => 'siti@fashionhub.co.id',
            'masalah_utama' => 'Sinkronisasi stok omnichannel.',
            'tujuan_utama' => 'Integrasi marketplace.',
            'target_audiens' => 'Staff gudang dan customer.',
            'aktor_sistem' => 'Admin, Staff, Customer.',
            'fitur_wajib' => 'Multi-channel sync, barcode stock opname.',
            'alur_kerja' => 'Order masuk auto-deduct inventori.',
            'user_metadata' => [
                'retail_tier' => 'pro',
            ],
        ]);

        // 1. Day 45: Would fail on Lite (30d limit), but MUST pass on Pro (180d limit)
        \Illuminate\Support\Facades\DB::table('vision_blueprints')
            ->where('id', $blueprint->id)
            ->update(['created_at' => now()->subDays(45)]);

        $blueprint->refresh();

        $resDay45 = $this->postJson('/api/blueprint/supplement-idea', [
            'supplement_text' => 'Integrasi dengan printer label thermal Bluetooth.',
            'blueprint' => [
                '_meta' => [
                    'is_editing_slug' => $blueprint->slug,
                ],
                'namaBisnis' => $blueprint->nama_bisnis,
            ],
        ]);
        $resDay45->assertStatus(200);
        $resDay45->assertJson(['success' => true]);

        // 2. Day 185: Past Pro 180 days window, MUST be rejected
        \Illuminate\Support\Facades\DB::table('vision_blueprints')
            ->where('id', $blueprint->id)
            ->update(['created_at' => now()->subDays(185)]);

        $blueprint->refresh();

        $resDay185 = $this->postJson('/api/blueprint/supplement-idea', [
            'supplement_text' => 'Tambahkan integrasi TikTok Shop sync.',
            'blueprint' => [
                '_meta' => [
                    'is_editing_slug' => $blueprint->slug,
                ],
                'namaBisnis' => $blueprint->nama_bisnis,
            ],
        ]);
        $resDay185->assertStatus(403);
        $resDay185->assertJson([
            'success' => false,
            'error_code' => 'REVISION_WINDOW_EXPIRED',
        ]);
        $this->assertStringContainsString('180 hari untuk paket Pro telah berakhir', $resDay185->json('message'));
    }

    /**
     * Test Ultimate package allows revisions at day 200 but rejects past 365 days (1 year).
     */
    public function test_ultimate_package_allows_modifications_at_day_200_but_rejects_past_365_days(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Direktur Handoko',
            'nama_bisnis' => 'Enterprise Health Network',
            'email' => 'handoko@healthnet.co.id',
            'masalah_utama' => 'Silo rekam medis 12 rumah sakit.',
            'tujuan_utama' => 'Interoperabilitas SATUSEHAT FHIR.',
            'target_audiens' => 'Dokter spesialis dan rekam medis.',
            'aktor_sistem' => 'Dokter, Perawat, Direksi, Auditor.',
            'fitur_wajib' => 'FHIR mapper, audit trail HIPAA, encryption at rest.',
            'alur_kerja' => 'RME terhubung otomatis ke kemenkes.',
            'user_metadata' => [
                'retail_tier' => 'ultimate',
            ],
        ]);

        // 1. Day 200: Would fail on Pro (180d limit), but MUST pass on Ultimate (365d limit)
        \Illuminate\Support\Facades\DB::table('vision_blueprints')
            ->where('id', $blueprint->id)
            ->update(['created_at' => now()->subDays(200)]);

        $blueprint->refresh();

        $resDay200 = $this->postJson('/api/blueprint/supplement-idea', [
            'supplement_text' => 'Tambahkan dukungan PACS DICOM viewer terintegrasi.',
            'blueprint' => [
                '_meta' => [
                    'is_editing_slug' => $blueprint->slug,
                ],
                'namaBisnis' => $blueprint->nama_bisnis,
            ],
        ]);
        $resDay200->assertStatus(200);
        $resDay200->assertJson(['success' => true]);

        // 2. Day 370: Past Ultimate 365 days window, MUST be rejected
        \Illuminate\Support\Facades\DB::table('vision_blueprints')
            ->where('id', $blueprint->id)
            ->update(['created_at' => now()->subDays(370)]);

        $blueprint->refresh();

        $resDay370 = $this->postJson('/api/blueprint/supplement-idea', [
            'supplement_text' => 'Tambahkan modul AI radiology triage.',
            'blueprint' => [
                '_meta' => [
                    'is_editing_slug' => $blueprint->slug,
                ],
                'namaBisnis' => $blueprint->nama_bisnis,
            ],
        ]);
        $resDay370->assertStatus(403);
        $resDay370->assertJson([
            'success' => false,
            'error_code' => 'REVISION_WINDOW_EXPIRED',
        ]);
        $this->assertStringContainsString('365 hari untuk paket Ultimate telah berakhir', $resDay370->json('message'));
    }

    /**
     * Test Studio MVP Turnkey package enforces strict Scope Lock on supplement and autosave.
     */
    public function test_studio_mvp_turnkey_enforces_scope_lock_on_supplement_and_autosave(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Alexander Wijaya',
            'nama_bisnis' => 'Apex Logistics Turnkey',
            'email' => 'alex@apexlogistics.com',
            'masalah_utama' => 'Turnkey enterprise shipment dispatch.',
            'tujuan_utama' => 'Autonomous freight forwarding.',
            'target_audiens' => 'Global freight handlers.',
            'aktor_sistem' => 'Dispatcher, Carrier, Customs Broker.',
            'fitur_wajib' => 'Customs EDI, Automated BOL, IoT Tracking.',
            'alur_kerja' => 'Container arrives, automated customs clearance.',
            'project_status' => 'Active Sprint',
            'signed_agreement' => true,
            'document_sha256' => hash('sha256', 'studio-mvp-contract-apex-50m'),
            'user_metadata' => [
                'retail_tier' => 'studio_mvp',
            ],
        ]);

        $this->assertTrue($blueprint->isScopeFrozen());

        // 1. Supplement Idea MUST be rejected with HTTP 403 SCOPE_LOCKED
        $supplementRes = $this->postJson('/api/blueprint/supplement-idea', [
            'supplement_text' => 'Coba ubah arsitektur ke microservices baru.',
            'blueprint' => [
                '_meta' => [
                    'is_editing_slug' => $blueprint->slug,
                ],
                'namaBisnis' => $blueprint->nama_bisnis,
            ],
        ]);
        $supplementRes->assertStatus(403);
        $supplementRes->assertJson([
            'success' => false,
            'error_code' => 'SCOPE_LOCKED',
        ]);
        $this->assertStringContainsString('Ruang lingkup (scope) proyek ini telah dikunci', $supplementRes->json('message'));

        // 2. AutoSave MUST also be rejected with HTTP 403 SCOPE_LOCKED
        $autoSaveRes = $this->postJson('/api/blueprint/autosave', [
            'blueprint' => [
                '_meta' => [
                    'is_editing_slug' => $blueprint->slug,
                ],
                'targetPlatform' => 'Native iOS & Android App',
            ],
        ]);
        $autoSaveRes->assertStatus(403);
        $autoSaveRes->assertJson([
            'success' => false,
            'error_code' => 'SCOPE_LOCKED',
        ]);
        $this->assertStringContainsString('Ruang lingkup (scope) proyek ini telah dikunci', $autoSaveRes->json('message'));
    }

    /**
     * Test Super Admin is exempt and can override scope lock and revision window.
     */
    public function test_superadmin_can_override_scope_lock_and_revision_window(): void
    {
        $superadmin = User::create([
            'name' => 'Yoseph Iriandi Tambunan',
            'email' => 'yoseph.iriandi.tambunan@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($superadmin);

        $blueprint = VisionBlueprint::create([
            'client_name' => 'Alexander Wijaya',
            'nama_bisnis' => 'Apex Logistics Turnkey Superadmin Edit',
            'email' => 'alex@apexlogistics.com',
            'masalah_utama' => 'Turnkey enterprise shipment dispatch.',
            'tujuan_utama' => 'Autonomous freight forwarding.',
            'target_audiens' => 'Global freight handlers.',
            'aktor_sistem' => 'Dispatcher, Carrier.',
            'fitur_wajib' => 'Customs EDI, Automated BOL.',
            'alur_kerja' => 'Container arrives, automated customs clearance.',
            'project_status' => 'Active Sprint',
            'signed_agreement' => true,
            'document_sha256' => hash('sha256', 'studio-mvp-contract-apex-50m'),
            'user_metadata' => [
                'retail_tier' => 'studio_mvp',
            ],
        ]);

        $this->assertTrue($blueprint->isScopeFrozen());

        // Super Admin supplement idea succeeds even on locked scope
        $res = $this->postJson('/api/blueprint/supplement-idea', [
            'supplement_text' => 'Super Admin adds critical compliance patch.',
            'blueprint' => [
                '_meta' => [
                    'is_editing_slug' => $blueprint->slug,
                ],
                'namaBisnis' => $blueprint->nama_bisnis,
            ],
        ]);
        $res->assertStatus(200);
        $res->assertJson(['success' => true]);
    }

    /**
     * Test invalid voucher codes are properly rejected with 422.
     */
    public function test_invalid_voucher_code_is_rejected(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Demo User',
            'nama_bisnis' => 'Test Toko Online',
            'email' => 'test@toko.com',
            'masalah_utama' => 'Masalah penjualan.',
            'tujuan_utama' => 'Peningkatan omset.',
            'target_audiens' => 'Masyarakat umum.',
            'aktor_sistem' => 'User, Admin.',
            'fitur_wajib' => 'Katalog, Checkout.',
            'alur_kerja' => 'Pilih barang lalu bayar.',
        ]);

        // Empty voucher code
        $resEmpty = $this->postJson("/blueprint/{$blueprint->slug}/voucher/validate", [
            'code' => '',
        ]);
        $resEmpty->assertStatus(422);

        // Fake/non-existent voucher code
        $resFake = $this->postJson("/blueprint/{$blueprint->slug}/voucher/validate", [
            'code' => 'KODE_PALSU_123',
        ]);
        $resFake->assertStatus(422);
        $resFake->assertJson([
            'valid' => false,
        ]);
        $this->assertStringContainsString('Kode voucher tidak valid', $resFake->json('message'));
    }

    /**
     * Test email notification dispatch when vision blueprint is submitted.
     */
    public function test_blueprint_submission_dispatches_email_notification(): void
    {
        Mail::fake();

        $clientEmail = 'hendra@perusahaanbaru.com';
        $response = $this->postJson('/api/vision-blueprint', [
            'client_name' => 'Hendra Wijaya',
            'nama_bisnis' => 'Perusahaan Baru Solusi',
            'email' => $clientEmail,
            'phone' => '081234567890',
            'masalah_utama' => 'Efisiensi pengiriman internal.',
            'tujuan_utama' => 'Platform routing logistik.',
            'target_audiens' => 'Operator gudang.',
            'aktor_sistem' => 'Operator, Manager.',
            'fitur_wajib' => 'Scan barcode, laporan harian.',
            'alur_kerja' => 'Operator input box barcode.',
            'kesiapan_aset' => 'Sudah ada flow diagram',
            'target_waktu' => '30 Hari',
        ]);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);

        Mail::assertSent(BlueprintReadyNotificationMail::class, function ($mail) use ($clientEmail) {
            return $mail->hasTo($clientEmail)
                && $mail->blueprint->nama_bisnis === 'Perusahaan Baru Solusi';
        });
    }

    /**
     * Test email notification dispatch when a 100% free voucher is claimed.
     */
    public function test_free_voucher_claim_dispatches_email_notification(): void
    {
        Mail::fake();

        $voucher = BlueprintVoucher::create([
            'code' => 'GRATISPROYEK100',
            'description' => 'Voucher 100% Subsidi Penuh',
            'discount_type' => 'free_bypass',
            'discount_value' => 100,
            'max_uses' => 10,
            'used_count' => 0,
            'is_active' => true,
        ]);

        $blueprint = VisionBlueprint::create([
            'client_name' => 'Rina Hartati',
            'nama_bisnis' => 'Startup Edukasi Rina',
            'email' => 'rina@edukasiku.id',
            'masalah_utama' => 'Pendidikan daerah terpencil.',
            'tujuan_utama' => 'Platform belajar offline-first.',
            'target_audiens' => 'Siswa dan Guru.',
            'aktor_sistem' => 'Guru, Siswa, Admin.',
            'fitur_wajib' => 'Video modul offline sync.',
            'alur_kerja' => 'Download modul saat ada wifi.',
        ]);

        $response = $this->postJson("/blueprint/{$blueprint->slug}/voucher/claim", [
            'code' => $voucher->code,
            'agree_sign_off' => '1',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        Mail::assertSent(BlueprintReadyNotificationMail::class, function ($mail) {
            return $mail->hasTo('rina@edukasiku.id');
        });
    }
}
