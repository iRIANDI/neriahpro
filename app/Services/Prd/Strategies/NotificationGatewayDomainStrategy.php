<?php

namespace App\Services\Prd\Strategies;

class NotificationGatewayDomainStrategy extends AbstractDomainStrategy
{
    public function supports(string $title, string $desc, string $lower): bool
    {
        return str_contains($lower, 'notifikasi') ||
            str_contains($lower, 'whatsapp') ||
            str_contains($lower, 'gateway') ||
            str_contains($lower, 'email') ||
            str_contains($lower, 'wa');
    }

    public function analyze(
        string $title,
        string $desc,
        string $businessName,
        string $clientActor,
        string $primaryActor,
        int $index,
        string $prefix,
        array $erdTables = [],
        bool $isEn = false
    ): array {
        $relatedTable = $this->matchTable($title, $desc, $erdTables);

        $ac = [
            [
                'scenario' => 'Pengiriman Notifikasi WhatsApp via Background Queue',
                'given' => 'Terjadi event pemicu (misal formulir baru atau update status).',
                'when' => 'Event didispatch oleh sistem.',
                'then' => 'Pesan WhatsApp dikirimkan melalui background queue job dalam <5 detik tanpa memperlambat respon halaman web bagi pengguna.',
            ],
            [
                'scenario' => 'Ketahanan Terhadap API Gateway Downtime (Retry Backoff)',
                'given' => 'API provider WhatsApp mengalami network timeout atau error sementara.',
                'when' => 'Job pengiriman notifikasi dieksekusi.',
                'then' => 'Sistem otomatis mencoba ulang (retry 3x dengan jeda bertingkat) dan mencatat status di log sistem tanpa merusak data transaksi utama.',
            ],
        ];

        $integration = [
            'endpoint' => 'POST /api/webhooks/whatsapp/delivery-report',
            'middleware' => ['api'],
            'request_schema' => '{"message_id": "WA-12345", "status": "DELIVERED", "timestamp": 1790866000}',
            'response_schema' => '{"success": true}',
            'tasks' => [
                'Daftarkan webhook listener untuk menerima status laporan pengiriman (DLR) dari gateway WhatsApp.',
            ],
        ];

        $mission = "Bangun sistem Notifikasi WhatsApp asinkron dengan Queue Jobs dan mekanisme retry cerdas.";
        $targetFiles = ['app/Services/WhatsAppGatewayService.php', 'app/Jobs/SendWhatsAppNotificationJob.php', 'database/migrations/xxxx_create_notification_logs_table.php'];
        $verifyCommand = "php artisan test --filter=WhatsAppNotificationTest";

        return [
            'category' => 'COMMUNICATION_DISPATCH',
            'category_label' => 'Notifikasi WhatsApp & Gateway Terotomasi',
            'related_table' => $relatedTable['name'] ?? 'notification_logs',
            'related_table_data' => $relatedTable,
            'user_story' => "Sebagai {$clientActor} dan {$primaryActor}, saya ingin menerima notifikasi status real-time via WhatsApp dan Email setiap kali ada aktivitas transaksi penting, sehingga seluruh pihak mendapatkan konfirmasi instan tanpa koordinasi manual.",
            'acceptance_criteria' => $ac,
            'frontend' => [
                'design_tokens' => 'Badge status notifikasi terkirim (Warna emerald untuk Terkirim, amber untuk Dalam Antrean, rose untuk Gagal kirim), preview template pesan.',
                'states' => 'Indikator live sync status pesan di tabel admin.',
                'components' => ['WhatsAppPreviewModal.blade.php', 'DeliveryStatusBadge.blade.php'],
                'tasks' => [
                    'Tampilkan riwayat pengiriman notifikasi di panel admin.',
                    'Sediakan tombol kirim ulang pesan (Resend WhatsApp) jika terjadi kegagalan transmisi.',
                ],
            ],
            'backend' => [
                'model_and_migration' => "Tabel `notification_logs` dengan `->ulid('id')->primary()`, target_phone, message_body, status, error_details, timestamps.",
                'scalability_guardrail' => 'Wajib menggunakan Laravel Queues (ShouldQueue) pada Redis/Database connection. Dilarang melakukan synchronous HTTP call di controller.',
                'tasks' => [
                    'Buat Service class `WhatsAppGatewayService` dengan format nomor E.164 otomatis.',
                    'Buat Job class `SendWhatsAppNotificationJob` yang mengimplementasikan `ShouldQueue`.',
                ],
            ],
            'integration' => $integration,
            'code_agent_directive' => $this->buildDirective(
                sprintf('FEAT-%s-%02d-WA_NOTIF', $prefix, $index),
                $title,
                $businessName,
                $mission,
                $targetFiles,
                $verifyCommand,
                $relatedTable,
                $ac,
                $integration,
                $index
            ),
        ];
    }
}
