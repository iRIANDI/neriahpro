<?php

namespace App\Services\Prd\Strategies;

class ReportingExportDomainStrategy extends AbstractDomainStrategy
{
    public function supports(string $title, string $desc, string $lower): bool
    {
        return str_contains($lower, 'ekspor') ||
            str_contains($lower, 'laporan') ||
            str_contains($lower, 'pdf') ||
            str_contains($lower, 'excel') ||
            str_contains($lower, 'cetak');
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
                'scenario' => 'Ekspor Excel Tanpa Kehabisan Memori Server (Chunking)',
                'given' => 'Terdapat ribuan baris data yang diekspor.',
                'when' => 'Admin menekan tombol Ekspor Excel.',
                'then' => 'Sistem men-streaming data menggunakan generator chunk (cursor), menghasilkan file .XLSX dengan header beku (freeze panes) dalam <3 detik tanpa lonjakan RAM.',
            ],
            [
                'scenario' => 'Cetak Dokumen PDF Terstandarisasi',
                'given' => 'Admin atau pengguna mencetak tanda bukti atau rangkuman laporan.',
                'when' => 'File PDF diunduh.',
                'then' => 'Tata letak mematuhi standar A4 cetak rapi, dilengkapi kop resmi, nomor halaman dinamis, dan verifikasi cryptographic hash atau QR code.',
            ],
        ];

        $integration = [
            'endpoint' => 'GET /admin/reports/export?format=xlsx&start_date=2026-01-01&end_date=2026-12-31',
            'middleware' => ['web', 'auth:web'],
            'request_schema' => 'Query parameters: format (xlsx|pdf), start_date, end_date, status',
            'response_schema' => 'Binary file stream with Content-Disposition attachment',
            'tasks' => [
                'Daftarkan route download laporan dengan proteksi otorisasi admin.',
            ],
        ];

        $mission = "Bangun fitur Ekspor Laporan PDF & Excel hemat memori menggunakan streaming cursor.";
        $targetFiles = ['app/Services/ReportExportService.php', 'app/Http/Controllers/ReportExportController.php', 'resources/views/pdf/report.blade.php'];
        $verifyCommand = "php artisan test --filter=ReportExportTest";

        return [
            'category' => 'REPORTING_EXPORT',
            'category_label' => 'Pelaporan & Ekspor Data (PDF / Excel)',
            'related_table' => $relatedTable['name'] ?? null,
            'related_table_data' => $relatedTable,
            'user_story' => "Sebagai {$primaryActor}, saya ingin mengunduh laporan rekapitulasi data dalam format PDF dan Excel terformat rapi, sehingga saya dapat menyajikan data resmi ke pemangku kepentingan tanpa perlu merapikan dokumen manual.",
            'acceptance_criteria' => $ac,
            'frontend' => [
                'design_tokens' => 'Modal pemilih filter tanggal (Date Range Picker), dropdown format (Excel XLSX, PDF Dokumen) dengan icon representatif, download progress feedback.',
                'states' => 'Tombol unduh beralih ke state animasi progress saat file disintesis, toast notifikasi sukses saat file mulai terunduh.',
                'components' => ['ExportReportModal.blade.php', 'ReportFormatSelector.blade.php'],
                'tasks' => [
                    'Buat modal dialog interaktif untuk memilih periode dan kolom ekspor.',
                    'Sediakan tombol unduh cepat dengan indikator visual proses unduhan.',
                ],
            ],
            'backend' => [
                'model_and_migration' => "Service class `App\Services\ReportExportService.php` dengan engine streaming (FastExcel / DomPDF).",
                'scalability_guardrail' => 'Dilarang memuat seluruh koleksi ke memory array ($query->get()). Wajib gunakan $query->cursor() untuk membatasi konsumsi RAM <32MB.',
                'tasks' => [
                    'Implementasikan export handler dengan streaming response langsung ke browser.',
                    'Desain template Blade khusus PDF (`resources/views/pdf/report.blade.php`) dengan styling ramah DomPDF.',
                ],
            ],
            'integration' => $integration,
            'code_agent_directive' => $this->buildDirective(
                sprintf('FEAT-%s-%02d-EXPORT_REPORT', $prefix, $index),
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
