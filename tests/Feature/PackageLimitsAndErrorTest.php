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
        $monthKey = 'spark_quota_' . md5($ip) . '_' . date('Y_m');

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
        $monthKey = 'spark_quota_' . md5($superadmin->email) . '_' . date('Y_m');
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
