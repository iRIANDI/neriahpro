<?php

namespace Tests\Feature;

use App\Models\VisionBlueprint;
use App\Models\Document;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlueprintDpStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_dp_confirmed_and_is_contract_signed(): void
    {
        // 1. New draft blueprint (unpaid, unsigned)
        $draft = VisionBlueprint::create([
            'client_name' => 'Draft Client',
            'nama_bisnis' => 'Draft Startup',
            'email' => 'draft@startup.id',
            'phone' => '08123456789',
            'masalah_utama' => 'Manual workflow',
            'tujuan_utama' => 'Automated workflow',
            'project_status' => 'Draft',
            'is_published' => true,
        ]);

        $this->assertFalse($draft->isDpConfirmed());
        $this->assertFalse($draft->isContractSigned());
        $this->assertFalse($draft->isScopeFrozen());

        // 2. Blueprint with Active Sprint / DP Paid status
        $paidBp = VisionBlueprint::create([
            'client_name' => 'Alexander Wijaya',
            'nama_bisnis' => 'Apex Logistics Global',
            'email' => 'alexander@apexlogistics.co.id',
            'phone' => '081288997711',
            'masalah_utama' => 'Manual manifest',
            'tujuan_utama' => 'Digital manifest',
            'project_status' => 'Active Sprint',
            'signed_agreement' => true,
            'is_published' => true,
            'staging_url' => 'https://apex-logistics-global.staging.neriahpro.com',
        ]);

        $this->assertTrue($paidBp->isDpConfirmed());
        $this->assertTrue($paidBp->isContractSigned());
        $this->assertTrue($paidBp->isScopeFrozen());

        // 3. Blueprint with Free Grant
        $freeBp = VisionBlueprint::create([
            'client_name' => 'Pastor Yohanes',
            'nama_bisnis' => 'Gereja Komunitas Kasih',
            'email' => 'yohanes@gerejakasih.org',
            'phone' => '08123456789',
            'masalah_utama' => 'Data jemaat manual',
            'tujuan_utama' => 'Aplikasi jemaat',
            'is_free_grant' => true,
            'voucher_code' => 'PELAYANAN-KASIH',
            'is_published' => true,
        ]);

        $this->assertTrue($freeBp->isDpConfirmed());
        $this->assertTrue($freeBp->isContractSigned());
    }

    public function test_ui_shows_active_sprint_cockpit_when_dp_confirmed(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Alexander Wijaya',
            'nama_bisnis' => 'Apex Logistics Global',
            'email' => 'alexander@apexlogistics.co.id',
            'phone' => '081288997711',
            'masalah_utama' => 'Manual manifest',
            'tujuan_utama' => 'Digital manifest',
            'project_status' => 'Active Sprint',
            'signed_agreement' => true,
            'signed_at' => now()->subDays(2),
            'signer_ip' => '182.253.51.197',
            'staging_url' => 'https://apex-logistics-global.staging.neriahpro.com',
            'is_published' => true,
        ]);

        $document = Document::create([
            'title' => 'Perjanjian Kerja Sama - Apex Logistics Global',
            'document_type' => 'contract',
            'related_type' => VisionBlueprint::class,
            'related_id' => $blueprint->id,
            'status' => 'signed',
            'scope_locked' => true,
            'contract_amount' => 50000000.00,
            'dp_amount' => 25000000.00,
            'midtrans_order_id' => 'NPRO-DP-APEX-001',
            'signed_at' => now()->subDays(2),
        ]);

        $response = $this->get(route('blueprint.show', $blueprint->slug));
        $response->assertStatus(200);

        // Active sprint cockpit assertions
        $response->assertSee('STATUS: PEMBAYARAN DP TERVERIFIKASI &bull; SPRINT AKTIF', false);
        $response->assertSee('SCOPE FROZEN &amp; DP CONFIRMED', false);
        $response->assertSee('Lihat Kontrak Digital');
        $response->assertSee('Buka Sandbox Staging');

        // Action buttons must NOT be present
        $response->assertDontSee('Tanda Tangani Kontrak &amp; Kunci Scope', false);
        $response->assertDontSee('Bayar DP / Klaim Voucher');
    }

    public function test_cart_and_snap_reject_if_dp_is_confirmed(): void
    {
        $blueprint = VisionBlueprint::create([
            'client_name' => 'Alexander Wijaya',
            'nama_bisnis' => 'Apex Logistics Global',
            'email' => 'alexander@apexlogistics.co.id',
            'phone' => '081288997711',
            'masalah_utama' => 'Manual manifest',
            'tujuan_utama' => 'Digital manifest',
            'project_status' => 'Active Sprint',
            'signed_agreement' => true,
            'is_published' => true,
        ]);

        // 1. Cart Add rejects
        $response = $this->post(route('cart.add', $blueprint->slug), [
            'tier' => 'standard',
        ]);
        $response->assertRedirect(route('blueprint.show', $blueprint->slug));
        $response->assertSessionHas('warning');

        // 2. Snap token rejects
        $snapResponse = $this->postJson(route('blueprint.snap-token', $blueprint->slug), [
            'tier' => 'standard',
        ]);
        $snapResponse->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Uang muka (DP) untuk proyek ini sudah terkonfirmasi / lunas. Tidak memerlukan pembayaran ulang.',
            ]);
    }
}
