<?php

namespace App\Services\Prd\Strategies;

class DashboardGovernanceDomainStrategy extends AbstractDomainStrategy
{
    public function supports(string $title, string $desc, string $lower): bool
    {
        return str_contains($lower, 'dasbor') ||
            str_contains($lower, 'admin') ||
            str_contains($lower, 'rekap') ||
            str_contains($lower, 'kelola') ||
            str_contains($lower, 'approval');
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
                'scenario' => 'Pemuatan Dasbor Agregasi Cepat (<150ms)',
                'given' => 'Database memiliki puluhan ribu catatan transaksi.',
                'when' => 'Admin membuka halaman utama dasbor operasional.',
                'then' => 'Widget ringkasan metrik menampilkan data agregasi dari cache Redis / query terindeks tanpa menyebabkan slow query pada server.',
            ],
            [
                'scenario' => 'Tabel Administrasi dengan Filter Cepat & Inline Actions',
                'given' => 'Admin sedang mengelola antrean permohonan masuk.',
                'when' => 'Admin memfilter berdasarkan status (Pending, Disetujui, Ditolak).',
                'then' => 'Tabel Filament v5 menampilkan data relevan secara instan dengan aksi persetujuan (Approve/Reject) langsung dari baris tabel.',
            ],
            [
                'scenario' => 'Audit Trail Pencatatan Setiap Aksi Perubahan',
                'given' => 'Data penting mengalami perubahan status oleh staf admin.',
                'when' => 'Perubahan disimpan ke sistem.',
                'then' => 'Sistem mencatat identitas admin, alamat IP, timestamp waktu, dan diff nilai sebelum/sesudah di tabel audit trail.',
            ],
        ];

        $integration = [
            'endpoint' => 'GET /admin/resources & POST /admin/resources/action',
            'middleware' => ['web', 'auth:web'],
            'request_schema' => '{"action": "approve", "record_id": "01J...ULID", "reason": "Dokumen lengkap"}',
            'response_schema' => '{"success": true, "message": "Status berhasil diperbarui."}',
            'tasks' => [
                'Integrasikan audit trail observer pada model untuk mencatat aktivitas admin.',
                'Hubungkan trigger approval dengan background job dispatch notifikasi.',
            ],
        ];

        $mission = "Implementasikan Dasbor Administrasi Filament v5 dengan schema form aman dan tabel filter instan.";
        $targetFiles = ['app/Filament/Resources/RecordResource.php', 'app/Filament/Widgets/StatsOverview.php', 'app/Policies/RecordPolicy.php'];
        $verifyCommand = "php artisan test --filter=AdminPanelAccessTest";

        return [
            'category' => 'GOVERNANCE_OPERATIONS',
            'category_label' => 'Dasbor Operasional & Tata Kelola',
            'related_table' => $relatedTable['name'] ?? null,
            'related_table_data' => $relatedTable,
            'user_story' => "Sebagai {$primaryActor}, saya ingin memantau KPI bisnis, meninjau data masuk, dan memproses status approval dalam satu panel kendali terpusat, sehingga operasional harian berjalan tanpa hambatan.",
            'acceptance_criteria' => $ac,
            'frontend' => [
                'design_tokens' => 'Antarmuka Filament v5 Enterprise dengan dark mode onyx / light mode crisp, widget card dengan indikator pertumbuhan tren persentase, status badge bersahabat.',
                'states' => 'Skeleton table rows saat data berpindah halaman, modal dialog konfirmasi yang elegan untuk aksi destruktif (tanpa window.confirm).',
                'components' => ['AdminResourceTable.php', 'StatsOverviewWidget.php', 'ActionConfirmationModal.blade.php'],
                'tasks' => [
                    'Konfigurasi Filament v5 Resource lengkap dengan form input dan kolom tabel.',
                    'Tambahkan widget kartu statistik metrik utama (Total Data, Pending, Disetujui).',
                    'Terapkan custom badge semantik untuk indikasi status yang mudah terbaca.',
                ],
            ],
            'backend' => [
                'model_and_migration' => "Filament Resource Class `App\Filament\Resources\...Resource.php` dengan implementasi method form Schema v5.",
                'scalability_guardrail' => "Wajib menggunakan signature `form(\Filament\Schemas\Schema \$form): \Filament\Schemas\Schema` (Filament v5). Terapkan eager loading relasi untuk mencegah N+1.",
                'tasks' => [
                    'Buat Filament Resource dengan schema form dan table builder standar Filament v5.',
                    'Pasang Policy autorisasi hak akses berbasis peran pada Model terkait.',
                    'Optimasi query tabel admin dengan indeks kolom status dan created_at.',
                ],
            ],
            'integration' => $integration,
            'code_agent_directive' => $this->buildDirective(
                sprintf('FEAT-%s-%02d-ADMIN_OPS', $prefix, $index),
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
