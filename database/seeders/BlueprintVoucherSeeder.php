<?php

namespace Database\Seeders;

use App\Models\BlueprintVoucher;
use Illuminate\Database\Seeder;

class BlueprintVoucherSeeder extends Seeder
{
    /**
     * Seed initial promo and free-grant vouchers.
     */
    public function run(): void
    {
        $vouchers = [
            // -------------------------------------------------------------------------
            // 1. Program Sosial, Yayasan, & Komunitas (Pelayanan Kasih)
            // -------------------------------------------------------------------------
            [
                'code' => 'PELAYANAN-KASIH',
                'description' => 'Program Pelayanan Kasih & Bantuan Aplikasi Gratis Komunitas/Gereja (Rp 0 / 100% Free Bypass)',
                'discount_type' => 'free_bypass',
                'discount_value' => 100.00,
                'max_uses' => null,
                'used_count' => 0,
                'is_active' => true,
                'expires_at' => null,
                'created_by' => 'yoseph.iriandi.tambunan@gmail.com',
            ],
            [
                'code' => 'NERIAH-FREE',
                'description' => 'Voucher Promosi Pelayanan Gratis Neriah Pro (Rp 0 / 100% Free Bypass)',
                'discount_type' => 'free_bypass',
                'discount_value' => 100.00,
                'max_uses' => 50,
                'used_count' => 0,
                'is_active' => true,
                'expires_at' => null,
                'created_by' => 'yoseph.iriandi.tambunan@gmail.com',
            ],
            [
                'code' => 'NERIAH-PROMO50',
                'description' => 'Voucher Diskon Peluncuran 50% Termin DP Proyek',
                'discount_type' => 'percent',
                'discount_value' => 50.00,
                'max_uses' => 20,
                'used_count' => 0,
                'is_active' => true,
                'expires_at' => now()->addMonths(6),
                'created_by' => 'yoseph.iriandi.tambunan@gmail.com',
            ],

            // -------------------------------------------------------------------------
            // 2. Skema Khusus UMKM (Usaha Mikro, Kecil, & Menengah)
            // -------------------------------------------------------------------------
            [
                'code' => 'UMKM-DIGITAL-100',
                'description' => 'Program Hibah Digitalisasi UMKM Nasional: Akselerasi Aplikasi Usaha Mikro Terpilih (100% Free Grant)',
                'discount_type' => 'free_bypass',
                'discount_value' => 100.00,
                'max_uses' => 25,
                'used_count' => 0,
                'is_active' => true,
                'expires_at' => null,
                'created_by' => 'yoseph.iriandi.tambunan@gmail.com',
            ],
            [
                'code' => 'UMKM-SUBSIDI-50',
                'description' => 'Subsidi Inovasi UMKM Naik Kelas: Potongan 50% Uang Muka (DP) Pengerjaan Sistem & Katalog Digital',
                'discount_type' => 'percent',
                'discount_value' => 50.00,
                'max_uses' => 50,
                'used_count' => 0,
                'is_active' => true,
                'expires_at' => now()->addYear(),
                'created_by' => 'yoseph.iriandi.tambunan@gmail.com',
            ],
            [
                'code' => 'UMKM-CASHBACK-5M',
                'description' => 'Bantuan Langsung Tunai Operasional UMKM: Potongan Tetap Rp 5.000.000 untuk Pembangunan MVP Aplikasi',
                'discount_type' => 'fixed',
                'discount_value' => 5000000.00,
                'max_uses' => 100,
                'used_count' => 0,
                'is_active' => true,
                'expires_at' => now()->addMonths(12),
                'created_by' => 'yoseph.iriandi.tambunan@gmail.com',
            ],

            // -------------------------------------------------------------------------
            // 3. Skema Perusahaan, Startup Scale-up, & Korporat (Enterprise)
            // -------------------------------------------------------------------------
            [
                'code' => 'CORP-INNOVATION-15M',
                'description' => 'Corporate Innovation Grant: Potongan Investasi Rp 15.000.000 untuk Proof-of-Concept (PoC) Arsitektur Enterprise',
                'discount_type' => 'fixed',
                'discount_value' => 15000000.00,
                'max_uses' => 20,
                'used_count' => 0,
                'is_active' => true,
                'expires_at' => now()->addMonths(12),
                'created_by' => 'yoseph.iriandi.tambunan@gmail.com',
            ],
            [
                'code' => 'ENTERPRISE-SPRINT-25',
                'description' => 'Kemitraan Korporat & B2B: Diskon 25% Termin DP Proyek Monolith Skala Besar & Integrasi API Kemenkes/Bank',
                'discount_type' => 'percent',
                'discount_value' => 25.00,
                'max_uses' => 30,
                'used_count' => 0,
                'is_active' => true,
                'expires_at' => now()->addMonths(12),
                'created_by' => 'yoseph.iriandi.tambunan@gmail.com',
            ],
            [
                'code' => 'CORP-PILOT-SANDBOX',
                'description' => 'Enterprise Sandbox Discovery: 100% Free Bypass untuk Penyusunan Blueprint Arsitektur PRD & Evaluasi Teknis Vendor',
                'discount_type' => 'free_bypass',
                'discount_value' => 100.00,
                'max_uses' => 15,
                'used_count' => 0,
                'is_active' => true,
                'expires_at' => null,
                'created_by' => 'yoseph.iriandi.tambunan@gmail.com',
            ],
        ];

        foreach ($vouchers as $voucher) {
            BlueprintVoucher::updateOrCreate(
                ['code' => $voucher['code']],
                $voucher
            );
        }
    }
}
