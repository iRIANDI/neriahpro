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
            [
                'code' => 'PELAYANAN-KASIH',
                'description' => 'Program Pelayanan Kasih & Bantuan Aplikasi Gratis Komunitas (Rp 0 / 100% Free Bypass)',
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
        ];

        foreach ($vouchers as $voucher) {
            BlueprintVoucher::updateOrCreate(
                ['code' => $voucher['code']],
                $voucher
            );
        }
    }
}
