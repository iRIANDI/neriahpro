<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CvFinanceRecord;
use Illuminate\Database\Seeder;

class CvFinanceRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = [
            [
                'trx_code' => 'TRX-101',
                'transaction_date' => '2026-10-01',
                'client_name' => 'Budi Santoso (PT Solusi Cemerlang)',
                'client_email' => 'budi.santoso@solusicemerlang.com',
                'package_name' => 'Standard (ATS Optimized)',
                'amount_idr' => 75000,
                'payment_method' => 'Midtrans QRIS',
                'status' => 'paid',
                'notes' => 'Poles kata kerja aktif dan audit skor ATS 85+.',
            ],
            [
                'trx_code' => 'TRX-102',
                'transaction_date' => '2026-10-02',
                'client_name' => 'Siti Rahma, S.Kom',
                'client_email' => 'siti.rahma@gmail.com',
                'package_name' => 'Full Stack',
                'amount_idr' => 150000,
                'payment_method' => 'BCA Transfer',
                'status' => 'paid',
                'notes' => 'Paket Full Stack lengkap dengan cover letter dan profil LinkedIn.',
            ],
            [
                'trx_code' => 'TRX-103',
                'transaction_date' => '2026-10-03',
                'client_name' => 'Ahmad Fauzi, M.T.',
                'client_email' => 'ahmad.fauzi@outlook.com',
                'package_name' => 'Executive VIP',
                'amount_idr' => 300000,
                'payment_method' => 'Mandiri Transfer',
                'status' => 'paid',
                'notes' => 'Executive coaching + simulasi mock interview STAR.',
            ],
            [
                'trx_code' => 'TRX-104',
                'transaction_date' => '2026-10-04',
                'client_name' => 'Dian Permata',
                'client_email' => 'dian.permata@yahoo.com',
                'package_name' => 'Standard (ATS Optimized)',
                'amount_idr' => 75000,
                'payment_method' => 'Midtrans QRIS',
                'status' => 'pending',
                'notes' => 'Menunggu verifikasi pembayaran QRIS.',
            ],
        ];

        foreach ($records as $item) {
            CvFinanceRecord::updateOrCreate(
                ['trx_code' => $item['trx_code']],
                $item
            );
        }
    }
}

