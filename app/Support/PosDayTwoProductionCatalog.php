<?php

namespace App\Support;

use App\Models\PosSector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * PosDayTwoProductionCatalog
 *
 * Project OS v3.0 — The Final 8 Day-2 Production Layers & 10 Ultimate Perfection Pillars:
 * 1. Printable Document, Thermal Receipt (58mm/80mm ESC/POS) & Official A4 PDF Archetypes
 * 2. Tenant Onboarding, Zero-State Provisioning (`InitializeNewTenantAction`) & Guided Checklist
 * 3. Historical Master Data Immutability & Snapshot Contract (`*_snapshot`)
 * 4. Two-Step Staged Bulk CSV/XLSX Import & Corporate Migration Engine (`import_staging_*`)
 * 5. Day-2 Operations: 4-Lane Queue Priority Segregation & Deterministic Cron Scheduler
 * 6. Defense-in-Depth Security: Multi-Tenant IDOR Guard, 4-Tier Rate Limiter & Private Disk Signed URLs
 * 7. State-Transition Omnichannel Notification Matrix (`notification_dispatch`)
 * 8. Interactive "Blueprint Doctor" (Pre-Flight Conflict Resolver & Readiness Validator)
 */
class PosDayTwoProductionCatalog
{
    public static function enrichDatabaseSectors(): void
    {
        if (! Schema::hasTable('pos_sectors') || ! Schema::hasColumn('pos_sectors', 'day_two_ops_contract')) {
            return;
        }

        foreach (ProjectOsMatrixCatalog::sectors() as $sec) {
            $code = (string) ($sec['code'] ?? '');
            if ($code === '') {
                continue;
            }
            PosSector::query()->where('code', $code)->update([
                'day_two_ops_contract' => self::bundleForSector($code),
            ]);
        }

        Cache::forget(PosSector::CACHE_KEY);
    }

    public static function bundleForSector(string $sectorCode): array
    {
        $code = PosSectorUiArchetypeCatalog::normalizeSectorCode($sectorCode);

        return [
            'printable_and_thermal' => self::printableAndThermalContract($code),
            'tenant_onboarding' => self::tenantOnboardingContract($code),
            'staged_bulk_import_export' => self::stagedBulkImportExportContract($code),
            'day_two_queue_and_cron' => self::dayTwoOpsQueueAndCronContract($code),
            'defense_in_depth_security' => self::defenseInDepthSecurityContract(),
            'state_notification_matrix' => self::sectorNotificationMatrix($code),
        ];
    }

    /**
     * Layer 1: Printable Document, Thermal Receipt (58mm/80mm ESC/POS) & Official A4 PDF Spec.
     */
    public static function printableAndThermalContract(string $sectorCode): array
    {
        $code = PosSectorUiArchetypeCatalog::normalizeSectorCode($sectorCode);

        $sectorDocs = [
            'SECTOR_01_COMMERCE_OMNICHANNEL' => [
                'thermal_mode' => 'REQUIRED_58MM_AND_80MM_ESCPOS',
                'thermal_artifacts' => ['Struk Kasir POS 58mm/80mm', 'Label Barcode Rak SKU', 'Slip Retur Kasir'],
                'official_a4_artifacts' => ['Faktur Penjualan / Pajak PPN', 'Purchase Order (PO) Supplier', 'Surat Jalan Pengiriman Gudang'],
                'watermarks' => ['LUNAS', 'BELUM DIBAYAR', 'RETUR / VOID'],
            ],
            'SECTOR_02_HOSPITALITY_SPACE' => [
                'thermal_mode' => 'OPTIONAL_80MM_FRONT_DESK',
                'thermal_artifacts' => ['Slip Check-In Cepat / Tiket Jam Lapangan 80mm'],
                'official_a4_artifacts' => [' Guest Folio / Invoice Menginap Resmi', 'Kontrak Sewa Unit / Alat Berat', 'Berita Acara Serah Terima Unit'],
                'watermarks' => ['CONFIRMED', 'CHECKED-IN', 'CANCELLED'],
            ],
            'SECTOR_03_HEALTHCARE_CLINICAL' => [
                'thermal_mode' => 'REQUIRED_58MM_QUEUE_AND_PHARMACY_LABEL',
                'thermal_artifacts' => ['Nomor Antrean Poli 58mm', 'Etiket Dosis Obat Apotek FEFO'],
                'official_a4_artifacts' => ['Resume Medis Pasien (EMR Signed-Off)', 'Surat Keterangan Sehat / Sakit Ber-QR', 'Kwitansi Klaim Asuransi / BPJS'],
                'watermarks' => ['SIGNED_OFF', 'CONFIDENTIAL MEDICAL RECORD', 'ADDENDUM'],
            ],
            'SECTOR_04_FINTECH_LEDGER' => [
                'thermal_mode' => 'OPTIONAL_80MM_TELLER_VOUCHER',
                'thermal_artifacts' => ['Bukti Setor / Tarik Kas Teller 80mm'],
                'official_a4_artifacts' => ['Laporan Jurnal Umum & Neraca Saldo PSAK', 'Invoice Tagihan Termin B2B', 'Bukti Potong & Kwitansi Bermeterai'],
                'watermarks' => ['AUDITED', 'POSTED IMMUTABLE', 'DRAFT'],
            ],
            'SECTOR_05_INDUSTRIAL_MRP' => [
                'thermal_mode' => 'REQUIRED_80MM_BATCH_BARCODE',
                'thermal_artifacts' => ['Label Lot Produksi & QR Palet Gudang 80mm'],
                'official_a4_artifacts' => ['Work Order Lantai Produksi (BoM Sheet)', 'Certificate of Analysis (CoA) QC', 'Berita Acara Scrap / Hasil Produksi'],
                'watermarks' => ['QC PASSED', 'IN PRODUCTION', 'QUARANTINE'],
            ],
            'SECTOR_06_LOGISTICS_FLEET' => [
                'thermal_mode' => 'REQUIRED_80MM_THERMAL_WAYBILL',
                'thermal_artifacts' => ['Stiker Resi Pengiriman Thermal 100x150mm / 80mm QR'],
                'official_a4_artifacts' => ['Manifest Muatan Armada', 'Surat Jalan & Proof of Delivery (PoD)', 'Invoice Rekap Tagihan Klien Korporat'],
                'watermarks' => ['DELIVERED POD', 'IN TRANSIT', 'DISPATCHED'],
            ],
            'SECTOR_07_EDTECH_MEMBERSHIP' => [
                'thermal_mode' => 'OPTIONAL_58MM_TUITION_RECEIPT',
                'thermal_artifacts' => ['Bukti Pembayaran SPP Loket 58mm'],
                'official_a4_artifacts' => ['Transkrip Nilai / Rapor Akademik Resmi', 'Sertifikat Kelulusan Ber-QR Kriptografis', 'Kartu Ujian Peserta'],
                'watermarks' => ['OFFICIAL TRANSCRIPT', 'VERIFIED GRADUATE', 'DRAFT'],
            ],
            'SECTOR_08_PROFESSIONAL_PSA' => [
                'thermal_mode' => 'NONE_A4_CORPORATE_ONLY',
                'thermal_artifacts' => [],
                'official_a4_artifacts' => ['Proposal & Kontrak Kerja Sama SPK (SHA-256)', 'Berita Acara Serah Terima (BAST) Milestone', 'Faktur Termin Konsultansi & Rekap Timesheet'],
                'watermarks' => ['SCOPE LOCKED', 'LUNAS', 'DRAFT KONTRAK'],
            ],
            'SECTOR_09_FOOD_BEVERAGE' => [
                'thermal_mode' => 'REQUIRED_58MM_AND_80MM_ESCPOS_KOT',
                'thermal_artifacts' => ['Kitchen Order Ticket (KOT) Dapur/Bar 80mm', 'Struk Tagihan Meja & Split-Bill 58mm/80mm'],
                'official_a4_artifacts' => ['Laporan Tutup Kasir Shift (Z-Report)', 'Purchase Order Bahan Baku Segar'],
                'watermarks' => ['LUNAS', 'BILL OPEN (BELUM BAYAR)', 'VOID'],
            ],
            'SECTOR_10_MEDIA_TICKETING' => [
                'thermal_mode' => 'REQUIRED_80MM_WRISTBAND_GATE_PASS',
                'thermal_artifacts' => ['Tiket Termal Pintu Masuk / Wristband QR 80mm'],
                'official_a4_artifacts' => ['E-Ticket Konser / Event A4 Ber-QR Unik', 'Statement Bagi Hasil Royalti Kreator / Promotor'],
                'watermarks' => ['VALID ENTRY', 'REDEEMED', 'REFUNDED'],
            ],
            'SECTOR_11_HRIS_INTERNAL_OPS' => [
                'thermal_mode' => 'NONE_A4_CONFIDENTIAL_ONLY',
                'thermal_artifacts' => [],
                'official_a4_artifacts' => ['Slip Gaji Karyawan Rahasia (PPh21 TER & BPJS)', 'Bukti Potong 1721-A1 Tahunan', 'Surat Keputusan / Kontrak PKWT-PKWTT'],
                'watermarks' => ['PRIVATE & CONFIDENTIAL', 'PAYROLL FINALIZED', 'SIMULATION'],
            ],
            'SECTOR_12_GOVTECH_NONPROFIT' => [
                'thermal_mode' => 'OPTIONAL_58MM_COUNTER_DONATION',
                'thermal_artifacts' => ['Tanda Terima Tunai Gerai Zakat/Donasi 58mm'],
                'official_a4_artifacts' => ['Bukti Setor Zakat / Sertifikat Wakaf Ber-QR SHA-256', 'Laporan Pertanggungjawaban Dana Terikat ISAK 35', 'Berita Acara Penyaluran Bantuan Lapangan'],
                'watermarks' => ['TERVERIFIKASI SHA-256', 'DANA TERIKAT ISAK 35', 'DRAFT'],
            ],
        ];

        $picked = $sectorDocs[$code] ?? $sectorDocs['SECTOR_01_COMMERCE_OMNICHANNEL'];

        return [
            'thermal_printer_spec' => [
                'mode' => $picked['thermal_mode'],
                'paper_widths' => ['58mm (32 chars/line)', '80mm (48 chars/line)'],
                'css_contract' => '@media print { @page { size: 80mm auto; margin: 0mm; } body { width: 80mm; margin: 0; font-family: ui-monospace, monospace; background: #fff !important; color: #000 !important; box-shadow: none !important; } }',
                'hardware_bridge' => 'WebUSB / WebSerial ESC/POS Direct Byte Stream + RawBT Intent Fallback (Android POS) tanpa cloud relay.',
                'artifacts' => $picked['thermal_artifacts'],
            ],
            'official_a4_pdf_spec' => [
                'page_size' => 'A4 Portrait / Landscape (210mm × 297mm, margin 14mm)',
                'pagination_safety' => 'Wajib memasang `thead { display: table-header-group; }` untuk kop surat berulang di tiap halaman dan `tr, .signature-block { break-inside: avoid; page-break-inside: avoid; }` agar tabel & tanda tangan tidak pernah terpotong.',
                'cryptographic_qr_verification' => 'QR Code verifikasi dokumen wajib dirender lokal sebagai inline SVG (Zero External QR API/CDN) memuat hash SHA-256 dokumen.',
                'status_watermarks' => $picked['watermarks'],
                'artifacts' => $picked['official_a4_artifacts'],
            ],
        ];
    }

    /**
     * Layer 2: Tenant Onboarding, Zero-State Provisioning & Guided Setup Checklist.
     */
    public static function tenantOnboardingContract(string $sectorCode): array
    {
        $code = PosSectorUiArchetypeCatalog::normalizeSectorCode($sectorCode);

        $sectorSeeds = [
            'SECTOR_01_COMMERCE_OMNICHANNEL' => ['1 Gudang Utama + 1 Outlet Kasir Default', '25 Akun Chart of Accounts (CoA) Ritel PSAK', 'Satuan Dasar (Pcs, Box, Lusin) & Profil Pajak PPN 11%', 'Metode Pembayaran Default (Tunai, QRIS, Transfer)'],
            'SECTOR_02_HOSPITALITY_SPACE' => ['Kategori Unit Default + Kalender Jam Operasional 06:00–23:00', 'Aturan Tarif Weekday vs Weekend & Hold TTL 15 Menit', '25 Akun CoA Pendapatan Sewa & Deposit Tamu'],
            'SECTOR_03_HEALTHCARE_CLINICAL' => ['Poli Umum + Instalasi Farmasi Utama (FEFO Aktif)', 'Template Rekam Medis S-O-A-P Standar Permenkes 24/2022', 'Katalog Awal ICD-10 Umum & Role Dokter/Apoteker/Admisi'],
            'SECTOR_04_FINTECH_LEDGER' => ['35 Akun Chart of Accounts (CoA) Standar PSAK Lengkap', 'Periode Fiskal Bulanan Aktif + Jurnal Saldo Awal', 'Akun Kas Kecil, Bank Operasional, & Akun Kliring Rekonsiliasi'],
            'SECTOR_05_INDUSTRIAL_MRP' => ['3 Lokasi Gudang (Bahan Baku, Work-In-Progress, Barang Jadi)', 'Work Center Produksi Default + Kategori Alasan Scrap QC', '25 Akun CoA HPP Manufaktur & Persediaan'],
            'SECTOR_06_LOGISTICS_FLEET' => ['Hub Origin Default + Zona Tarif Dasar Antar-Kota', 'Status Pipeline Manifest Standar & Alasan Gagal Kirim', 'Template Stiker Resi QR & Nomor Urut AWB'],
            'SECTOR_07_EDTECH_MEMBERSHIP' => ['Tahun Akademik / Batch Aktif Pertama', 'Komponen Bobot Penilaian Default (Tugas 30%, UTS 30%, UAS 40%)', 'Status Presensi (H/I/S/A) & Template Sertifikat'],
            'SECTOR_08_PROFESSIONAL_PSA' => ['Pipeline Milestone Proyek Standar (Discovery, Build, UAT, Go-Live)', 'Kategori Rate Card Jam Kerja (Senior, Mid, Associate)', 'Template Kontrak SPK & Faktur Termin'],
            'SECTOR_09_FOOD_BEVERAGE' => ['2 Stasiun KDS Default (Dapur Panas & Bar Minuman)', 'Zona Meja Dine-In + Grup Modifier Cepat (Level Gula/Es)', 'Kas Kasir Shift & Profil Pajak PB1 Restoran 10%'],
            'SECTOR_10_MEDIA_TICKETING' => ['Zona Tiket Default (VIP, Festival, Early Bird) + Hold TTL 10 Menit', 'Konfigurasi Scanner Gate Check-In & Skema Bagi Hasil Kreator', 'Akun Escrow Penampungan Penjualan Tiket'],
            'SECTOR_11_HRIS_INTERNAL_OPS' => ['3 Shift Kerja Default (Pagi, Sore, Non-Shift) + Kalender Libur Nasional', 'Tabel Tarif PPh21 TER (Kategori A/B/C) & Persentase BPJS TK/Kesehatan', 'Komponen Slip Gaji Pokok, Tunjangan, & Lembur'],
            'SECTOR_12_GOVTECH_NONPROFIT' => ['25 Akun CoA Yayasan Standar ISAK 35 (Dana Terikat vs Tidak Terikat)', 'Program Penyaluran Awal & Template Kwitansi Donasi SHA-256', 'Alur Persetujuan Pencairan Dana 2-Pengurus'],
        ];

        return [
            'action_class' => 'App\\Actions\\Tenancy\\InitializeNewTenantAction',
            'execution_guarantee' => 'Berjalan secara atomik di dalam satu DB::transaction() tepat saat organisasi/tenant baru mendaftar, menjamin 0 layar dashboard kosong yang buntu.',
            'atomic_foundation_records' => $sectorSeeds[$code] ?? $sectorSeeds['SECTOR_01_COMMERCE_OMNICHANNEL'],
            'guided_setup_widget' => [
                'component' => 'GuidedSetupChecklistWidget',
                'behavior' => 'Banner stepper progresif di bagian atas dashboard yang memandu 3 langkah aktivasi pertama dan otomatis tersembunyi permanen saat progres mencapai 100%.',
                'steps' => [
                    'Langkah 1: Verifikasi Identitas Bisnis, Logo Kop Surat & Zona Waktu Organisasi.',
                    'Langkah 2: Tambah Master Data Operasional Pertama (Produk / Unit / Poli / Program).',
                    'Langkah 3: Uji Transaksi Pertama & Cetak Dokumen Thermal/A4 Perdana.',
                ],
            ],
        ];
    }

    /**
     * Layer 3: Historical Master Data Immutability & Snapshot Contract (`*_snapshot`).
     */
    public static function historicalSnapshotContract(array $dataDictionary): array
    {
        $transactionalKeywords = [
            'order', 'item', 'line', 'invoice', 'booking', 'transaction', 'journal',
            'prescription', 'payslip', 'manifest', 'ticket', 'disbursement', 'donation', 'movement',
        ];

        $enforcedTables = [];
        foreach ($dataDictionary as $tbl) {
            $tName = strtolower((string) ($tbl['table'] ?? ''));
            if ($tName === '' || in_array($tName, ['organizations', 'outbox_messages', 'webhook_inbound_logs', 'import_staging_batches', 'import_staging_rows'], true)) {
                continue;
            }
            foreach ($transactionalKeywords as $kw) {
                if (str_contains($tName, $kw)) {
                    $enforcedTables[] = $tName;
                    break;
                }
            }
        }

        if (empty($enforcedTables)) {
            $enforcedTables = ['order_items', 'invoice_items', 'journal_lines'];
        }

        return [
            'core_invariant' => 'Perubahan harga produk, nama item, tarif PPN, atau alamat pelanggan di masa depan DILARANG KERAS mengubah nilai invoice, laporan penjualan, slip gaji, atau rekam medis historis di masa lalu.',
            'mandatory_snapshot_columns' => [
                ['column' => 'item_name_snapshot', 'type' => 'VARCHAR(255) NOT NULL', 'purpose' => 'Membekukan nama produk/layanan/komponen pada detik transaksi dikunci.'],
                ['column' => 'sku_or_code_snapshot', 'type' => 'VARCHAR(100) NULL', 'purpose' => 'Membekukan kode SKU / kode akun / ICD-10 saat transaksi.'],
                ['column' => 'unit_price_snapshot', 'type' => 'DECIMAL(19,4) NOT NULL', 'purpose' => 'Membekukan harga satuan eksak pada detik transaksi.'],
                ['column' => 'tax_rate_snapshot', 'type' => 'DECIMAL(5,2) NOT NULL DEFAULT 0', 'purpose' => 'Membekukan persentase pajak (mis. PPN 11% vs 12%) saat transaksi.'],
                ['column' => 'counterparty_snapshot', 'type' => 'JSONB NOT NULL', 'purpose' => 'Membekukan nama legal, NPWP/NIK ter-masking, dan alamat tagih pelanggan/vendor.'],
            ],
            'foreign_key_usage_rule' => 'Relasi `foreignUlid` ke tabel master (`products`, `users`, `bookable_units`) HANYA boleh digunakan untuk filter analitik dan tautan navigasi, BUKAN untuk merender ulang nominal/judul dokumen historis.',
            'enforced_on_tables' => array_values(array_unique($enforcedTables)),
        ];
    }

    /**
     * Layer 4: Bulk Data Import/Export & Corporate Legacy Migration Engine.
     */
    public static function stagedBulkImportExportContract(string $sectorCode): array
    {
        $code = PosSectorUiArchetypeCatalog::normalizeSectorCode($sectorCode);
        $entityExamples = [
            'SECTOR_01_COMMERCE_OMNICHANNEL' => '5.000+ SKU Barang, Varian Harga Grosir & Saldo Stok Awal',
            'SECTOR_03_HEALTHCARE_CLINICAL' => 'Katalog Obat Farmasi Batch/FEFO, Tarif Tindakan & Data Pasien Lama',
            'SECTOR_07_EDTECH_MEMBERSHIP' => '1.200+ Data Siswa/Mahasiswa, Kelas, & Nilai Semester',
            'SECTOR_11_HRIS_INTERNAL_OPS' => '500+ Master Karyawan, PTKP, Nomor BPJS, & Roster Shift Bulanan',
        ];

        return [
            'pattern' => 'TWO_STEP_STAGED_IMPORT_WITH_CHUNKED_QUEUE',
            'target_corporate_load' => $entityExamples[$code] ?? 'Ribuan baris data master & saldo awal korporat dari CSV / XLSX tanpa 504 Gateway Timeout',
            'step_1_validate_and_stage' => 'Unggah berkas CSV/XLSX secara streaming ke tabel sementara `import_staging_batches` & `import_staging_rows` (ULID). Validasi per baris tanpa menyentuh tabel produksi; tampilkan ringkasan ("495 baris valid, 5 baris gagal di kolom Harga — Unduh CSV Error").',
            'step_2_idempotent_chunk_commit' => 'Klik [Commit Valid Rows] men-dispatch job ke `queue:low-batch` per 250 baris menggunakan `X-Idempotency-Key` dan memperbarui `processed_rows` secara real-time untuk Progress Bar UI.',
            'staging_tables' => ['import_staging_batches', 'import_staging_rows'],
        ];
    }

    /**
     * Strict ULID schema definitions for `import_staging_batches` & `import_staging_rows` injected into Data Dictionary.
     */
    public static function stagingTablesDefinition(): array
    {
        return [
            [
                'table' => 'import_staging_batches',
                'source_module' => 'CORE_DAY2_STAGED_IMPORTER',
                'tenant_scoped' => true,
                'deletion_policy' => 'SOFT_ARCHIVE',
                'foreign_key_on_delete' => 'RESTRICT',
                'retention_period' => '90_DAYS_STAGING_LOG',
                'pii_columns' => [],
                'columns' => [
                    ['name' => 'id', 'type' => 'CHAR(26) ULID', 'primary' => true],
                    ['name' => 'organization_id', 'type' => 'CHAR(26) ULID NOT NULL'],
                    ['name' => 'uploaded_by', 'type' => 'CHAR(26) ULID NOT NULL'],
                    ['name' => 'target_entity', 'type' => 'VARCHAR(100) NOT NULL'],
                    ['name' => 'idempotency_key', 'type' => 'VARCHAR(120) UNIQUE NOT NULL'],
                    ['name' => 'status', 'type' => 'VARCHAR(32) NOT NULL DEFAULT \'STAGED\''],
                    ['name' => 'total_rows', 'type' => 'INTEGER NOT NULL DEFAULT 0'],
                    ['name' => 'valid_rows', 'type' => 'INTEGER NOT NULL DEFAULT 0'],
                    ['name' => 'invalid_rows', 'type' => 'INTEGER NOT NULL DEFAULT 0'],
                    ['name' => 'processed_rows', 'type' => 'INTEGER NOT NULL DEFAULT 0'],
                ],
                'indexes' => ['INDEX (organization_id, status, id)'],
            ],
            [
                'table' => 'import_staging_rows',
                'source_module' => 'CORE_DAY2_STAGED_IMPORTER',
                'tenant_scoped' => true,
                'deletion_policy' => 'SOFT_ARCHIVE',
                'foreign_key_on_delete' => 'RESTRICT',
                'retention_period' => '30_DAYS_STAGING_ROWS',
                'pii_columns' => ['raw_payload'],
                'columns' => [
                    ['name' => 'id', 'type' => 'CHAR(26) ULID', 'primary' => true],
                    ['name' => 'organization_id', 'type' => 'CHAR(26) ULID NOT NULL'],
                    ['name' => 'batch_id', 'type' => 'CHAR(26) ULID NOT NULL REFERENCES import_staging_batches(id) ON DELETE RESTRICT'],
                    ['name' => 'row_number', 'type' => 'INTEGER NOT NULL'],
                    ['name' => 'validation_status', 'type' => 'VARCHAR(24) NOT NULL'],
                    ['name' => 'raw_payload', 'type' => 'JSONB NOT NULL'],
                    ['name' => 'validation_errors', 'type' => 'JSONB NULL'],
                ],
                'indexes' => ['INDEX (batch_id, validation_status, row_number)'],
            ],
        ];
    }

    /**
     * Layer 5: Day-2 Operations — 4-Lane Queue Priority Segregation & Deterministic Cron Scheduler.
     */
    public static function dayTwoOpsQueueAndCronContract(string $sectorCode): array
    {
        $code = PosSectorUiArchetypeCatalog::normalizeSectorCode($sectorCode);

        $sectorCrons = [
            'SECTOR_01_COMMERCE_OMNICHANNEL' => [
                ['cron' => '* * * * *', 'command' => 'pos:release-expired-stock-holds', 'purpose' => 'Melepaskan reservasi stok keranjang/checkout yang melewati TTL 15 menit.'],
                ['cron' => '10 0 * * *', 'command' => 'pos:reconcile-warehouse-ledger', 'purpose' => 'Memvalidasi kesesuaian saldo mutasi inventory_movements di seluruh gudang.'],
            ],
            'SECTOR_02_HOSPITALITY_SPACE' => [
                ['cron' => '* * * * *', 'command' => 'booking:release-unpaid-holds', 'purpose' => 'Membuka kembali slot kamar/lapangan berstatus PENDING_PAYMENT yang lewat TTL.'],
                ['cron' => '0 6 * * *', 'command' => 'booking:dispatch-checkin-reminders', 'purpose' => 'Mengirim pengingat check-in H-1 beserta kode QR ke tamu.'],
            ],
            'SECTOR_03_HEALTHCARE_CLINICAL' => [
                ['cron' => '0 7 * * *', 'command' => 'pharmacy:scan-fefo-expiring-batches', 'purpose' => 'Mendeteksi batch obat mendekati kedaluwarsa (<60 hari) & mengunci batch expired otomatis.'],
                ['cron' => '0 18 * * *', 'command' => 'clinic:send-followup-control-alerts', 'purpose' => 'Mengirim pengingat jadwal kontrol pasien kronis H-1 via WhatsApp.'],
            ],
            'SECTOR_04_FINTECH_LEDGER' => [
                ['cron' => '5 0 * * *', 'command' => 'ledger:verify-daily-trial-balance', 'purpose' => 'Audit otomatis SUM(debit) == SUM(credit) harian & deteksi selisih mutasi webhook.'],
                ['cron' => '0 1 1 * *', 'command' => 'billing:generate-recurring-invoices', 'purpose' => 'Menerbitkan invoice langganan bulanan secara idempoten tiap tanggal 1.'],
            ],
        ];

        $extraCrons = $sectorCrons[$code] ?? [
            ['cron' => '* * * * *', 'command' => 'ops:process-expired-holds-and-outbox', 'purpose' => 'Memproses ulang outbox_messages tertunda & melepaskan kunci transaksi kedaluwarsa.'],
            ['cron' => '5 0 * * *', 'command' => 'ops:daily-integrity-and-audit-digest', 'purpose' => 'Rekonsiliasi integritas saldo/status harian & verifikasi rantai hash audit_logs.'],
        ];

        return [
            'queue_lanes' => [
                ['lane' => 'queue:critical', 'sla_target' => '< 2 detik', 'workloads' => 'Webhook pembayaran, pelepasan kunci stok/slot, pengiriman kode OTP login.'],
                ['lane' => 'queue:default', 'sla_target' => '< 15 detik', 'workloads' => 'Notifikasi WhatsApp/Email transaksional, pembuatan PDF Invoice/Surat Jalan resmi.'],
                ['lane' => 'queue:ai-rag', 'sla_target' => '< 60 detik', 'workloads' => 'Ekstraksi MarkItDown, VectorEmbeddingService, sinkronisasi indeks HNSW pgvector.'],
                ['lane' => 'queue:low-batch', 'sla_target' => '< 10 menit', 'workloads' => 'Commit Staged CSV/XLSX 250-baris/chunk, ekspor jutaan baris cursor, laporan tahunan YoY.'],
            ],
            'worker_supervisor_command' => 'php artisan queue:work redis --queue=critical,default,ai-rag,low-batch --tries=3 --backoff=10,60,300 --max-time=3600',
            'scheduler_registry' => array_merge($extraCrons, [
                ['cron' => '30 2 * * *', 'command' => 'ops:encrypted-pgdump-and-prune-staging', 'purpose' => 'Backup terenkripsi PostgreSQL 16 harian & pembersihan import_staging_rows > 30 hari.'],
            ]),
        ];
    }

    /**
     * Layer 6: Defense-in-Depth Security, Tiered Rate Limiting & Private Media Disk Isolation.
     */
    public static function defenseInDepthSecurityContract(): array
    {
        return [
            'multi_tenant_idor_shield' => 'Wajib memasang Global Scope `BelongsToOrganization` (`WHERE organization_id = ?`) pada setiap model domain. Percobaan akses ULID lintas tenant wajib melempar `404 ModelNotFoundException` dan mencatat event `security` berstempel SHA-256 di `audit_logs`.',
            'tiered_rate_limiters' => [
                ['limiter' => 'auth_otp', 'limit' => '5 req / menit / IP', 'scope' => 'Endpoint Login, Request OTP, & Reset Kredensial'],
                ['limiter' => 'checkout_booking', 'limit' => '15 req / menit / user', 'scope' => 'Endpoint Pembuatan Pesanan, Kunci Kursi/Slot, & Pengajuan Pencairan'],
                ['limiter' => 'ai_copilot', 'limit' => '10 req / menit / user', 'scope' => 'Endpoint Chat AI Copilot, Sintesis Dokumen, & Native Tool Execution'],
                ['limiter' => 'inbound_webhook', 'limit' => 'HMAC-SHA256 Raw Body + IP Allowlist', 'scope' => 'Endpoint Webhook Pembayaran & Logistik (tanpa session cookie)'],
            ],
            'media_disk_isolation' => [
                'private_disk' => 'Dokumen rahasia (KTP/KYC, Rekam Medis EMR, Slip Gaji, Kontrak SPK, Bukti Transfer) WAJIB disimpan di `disk = "private"` di luar `public/` dan diakses eksklusif melalui `URL::temporarySignedRoute()` berdurasi 5 menit.',
                'public_disk' => 'Hanya foto katalog produk publik, avatar umum, dan aset CMS yang boleh masuk ke `disk = "public"`.',
                'upload_sanitization' => 'Validasi MIME absolut via `finfo_buffer` (tolak spoofing ekstensi `.php`/`.svg` berskrip), nonaktifkan `LIBXML_NONET` (anti-XXE), dan matikan `session.upload_progress` pada Livewire 4.',
            ],
        ];
    }

    /**
     * Layer 7: Canonical Sector-Level Omnichannel Notification Matrix.
     */
    public static function sectorNotificationMatrix(string $sectorCode): array
    {
        $code = PosSectorUiArchetypeCatalog::normalizeSectorCode($sectorCode);

        return [
            'engine' => 'Omnichannel State-Transition Dispatcher (WhatsApp Template/QR + Email Official A4 PDF + Filament/In-App Toast)',
            'deduplication_rule' => 'Idempotent dedup_key = "{entity}:{ulid}:{target_state}" stored in Redis/DB to prevent duplicate alerts on job retries.',
            'default_rules' => [
                array_merge(['on_state' => 'PAID_OR_CONFIRMED'], self::resolveTransitionNotification('Transaction', 'CONFIRMED', $code)),
                array_merge(['on_state' => 'PENDING_APPROVAL'], self::resolveTransitionNotification('ApprovalRequest', 'PENDING_APPROVAL', $code)),
                array_merge(['on_state' => 'COMPLETED_OR_SIGNED_OFF'], self::resolveTransitionNotification('Document', 'SIGNED_OFF', $code)),
                array_merge(['on_state' => 'CANCELLED_OR_REJECTED'], self::resolveTransitionNotification('Transaction', 'CANCELLED', $code)),
            ],
        ];
    }

    /**
     * Layer 7: Enrich State Machine transitions with deterministic Omnichannel Notification Matrix (`notification_dispatch` & `notification_matrix`).
     */
    public static function enrichStateMachinesWithNotifications(array $stateMachines, string $sectorCode): array
    {
        foreach ($stateMachines as &$sm) {
            $entity = (string) ($sm['entity'] ?? 'Record');
            $matrix = [];

            if (! empty($sm['transitions']) && is_array($sm['transitions'])) {
                foreach ($sm['transitions'] as &$tr) {
                    $to = strtoupper((string) ($tr['to'] ?? 'UPDATED'));
                    if (empty($tr['notification_dispatch'])) {
                        $tr['notification_dispatch'] = self::resolveTransitionNotification($entity, $to, $sectorCode);
                    }
                    $matrix[$to] = array_merge(
                        [
                            'on_state' => $to,
                            'target_state' => $to,
                        ],
                        $tr['notification_dispatch']
                    );
                }
                unset($tr);
            } else {
                // Synthesize canonical notification matrix from states when explicit transitions array is compact
                $states = $sm['states'] ?? [];
                foreach (array_slice($states, 1) as $targetState) {
                    $to = strtoupper((string) $targetState);
                    $matrix[$to] = array_merge(
                        [
                            'on_state' => (string) $targetState,
                            'target_state' => (string) $targetState,
                        ],
                        self::resolveTransitionNotification($entity, $to, $sectorCode)
                    );
                }
            }

            if (empty($sm['notification_matrix'])) {
                $sm['notification_matrix'] = array_values($matrix);
            }
        }
        unset($sm);

        return $stateMachines;
    }

    protected static function resolveTransitionNotification(string $entity, string $toState, string $sectorCode): array
    {
        if (str_contains($toState, 'CONFIRMED') || str_contains($toState, 'PAID') || str_contains($toState, 'SETTLED')) {
            return [
                'channels' => ['whatsapp_template_qr', 'in_app_operational_toast', 'email_official_pdf'],
                'recipients' => ['CUSTOMER', 'OPERATIONAL_STAFF'],
                'queue_lane' => 'queue:default',
                'dedup_key' => strtolower($entity) . ':{id}:' . strtolower($toState),
                'summary' => "Kirim WhatsApp Bukti/QR + Email PDF Resmi ke Pelanggan & In-App Alert ke Staf ({$sectorCode}).",
            ];
        }

        if (str_contains($toState, 'APPROV') || str_contains($toState, 'PENDING') || str_contains($toState, 'REVIEW')) {
            return [
                'channels' => ['whatsapp_actionable_link', 'filament_database_notification'],
                'recipients' => ['NEXT_APPROVER', 'TENANT_OWNER'],
                'queue_lane' => 'queue:default',
                'dedup_key' => strtolower($entity) . ':{id}:' . strtolower($toState),
                'summary' => 'Kirim WhatsApp Actionable Link + Notifikasi Database Filament ke Pejabat Otorisasi berikutnya.',
            ];
        }

        if (str_contains($toState, 'CANCEL') || str_contains($toState, 'REJECT') || str_contains($toState, 'EXPIRED') || str_contains($toState, 'FAILED')) {
            return [
                'channels' => ['whatsapp_status_alert', 'in_app_operational_toast'],
                'recipients' => ['CUSTOMER', 'OPERATIONAL_STAFF'],
                'queue_lane' => 'queue:default',
                'dedup_key' => strtolower($entity) . ':{id}:' . strtolower($toState),
                'summary' => 'Kirim pemberitahuan pembatalan/penolakan beserta alasan jelas & instruksi tindak lanjut.',
            ];
        }

        if (str_contains($toState, 'CLOSED') || str_contains($toState, 'POSTED') || str_contains($toState, 'SIGNED') || str_contains($toState, 'COMPLETED') || str_contains($toState, 'DELIVERED')) {
            return [
                'channels' => ['email_official_pdf', 'filament_database_notification'],
                'recipients' => ['TENANT_OWNER', 'FINANCE_AUDITOR', 'CUSTOMER'],
                'queue_lane' => 'queue:default',
                'dedup_key' => strtolower($entity) . ':{id}:' . strtolower($toState),
                'summary' => 'Kirim Email lampiran PDF resmi ber-QR SHA-256 & arsipkan notifikasi audit.',
            ];
        }

        return [
            'channels' => ['filament_database_notification'],
            'recipients' => ['OPERATIONAL_STAFF'],
            'queue_lane' => 'queue:default',
            'dedup_key' => strtolower($entity) . ':{id}:' . strtolower($toState),
            'summary' => 'Catat pembaruan status ke pusat notifikasi operasional internal tanpa spam kanal eksternal.',
        ];
    }

    /**
     * Summary metadata for all 8 Final Day-2 Production Layers.
     */
    public static function finalEightLayersSummary(): array
    {
        return [
            [
                'number' => 1,
                'code' => 'LAYER_01_PRINT_THERMAL_A4',
                'badge' => '58/80MM + A4 QR',
                'title' => [
                    'id' => '1. Printable Document, Thermal Receipt (58/80mm) & Official A4 PDF',
                    'en' => '1. Printable Document, Thermal Receipt (58/80mm) & Official A4 PDF',
                ],
                'desc' => [
                    'id' => 'Aturan CSS @media print presisi struk kasir ESC/POS 58mm/80mm (WebUSB/RawBT) serta dokumen A4 anti-potong ber-QR SVG lokal & watermark status.',
                    'en' => 'Exact 58mm/80mm ESC/POS thermal rules (WebUSB/RawBT) plus page-break proof A4 documents with local SVG QR verification & watermarks.',
                ],
            ],
            [
                'number' => 2,
                'code' => 'LAYER_02_TENANT_ZERO_STATE_ONBOARDING',
                'badge' => 'ZERO EMPTY DASHBOARD',
                'title' => [
                    'id' => '2. Tenant Onboarding & Atomic Zero-State Provisioning',
                    'en' => '2. Tenant Onboarding & Atomic Zero-State Provisioning',
                ],
                'desc' => [
                    'id' => 'InitializeNewTenantAction men-seed Gudang Utama, 25 CoA PSAK/ISAK 35, Kas Default, & GuidedSetupChecklistWidget dalam 1 transaksi DB saat Sign-Up.',
                    'en' => 'InitializeNewTenantAction atomically seeds Main Warehouse, 25 CoA accounts, Default Cash, & GuidedSetupChecklistWidget upon Sign-Up.',
                ],
            ],
            [
                'number' => 3,
                'code' => 'LAYER_03_HISTORICAL_SNAPSHOT_IMMUTABILITY',
                'badge' => 'ANTI DATA CORRUPTION',
                'title' => [
                    'id' => '3. Historical Master Data Immutability & Snapshot Contract',
                    'en' => '3. Historical Master Data Immutability & Snapshot Contract',
                ],
                'desc' => [
                    'id' => 'Membekukan item_name_snapshot, unit_price_snapshot, tax_rate_snapshot, & counterparty_snapshot agar perubahan harga/PPN baru tidak merusak laporan masa lalu.',
                    'en' => 'Freezes item_name_snapshot, unit_price_snapshot, tax_rate_snapshot, & counterparty_snapshot so master edits never corrupt past invoices.',
                ],
            ],
            [
                'number' => 4,
                'code' => 'LAYER_04_STAGED_BULK_CSV_IMPORTER',
                'badge' => 'NO 504 TIMEOUT',
                'title' => [
                    'id' => '4. Two-Step Staged Bulk CSV/XLSX Import & Corporate Migration',
                    'en' => '4. Two-Step Staged Bulk CSV/XLSX Import & Corporate Migration',
                ],
                'desc' => [
                    'id' => 'Validasi per baris di import_staging_rows + laporan CSV error sebelum commit chunk 250 baris di queue:low-batch ber-Idempotency Key.',
                    'en' => 'Row-level validation in import_staging_rows + error CSV download before 250-row chunked background commits with Idempotency Keys.',
                ],
            ],
            [
                'number' => 5,
                'code' => 'LAYER_05_QUEUE_PRIORITY_AND_CRON',
                'badge' => '4 REDIS LANES',
                'title' => [
                    'id' => '5. Day-2 Ops: 4-Lane Queue Segregation & Deterministic Cron',
                    'en' => '5. Day-2 Ops: 4-Lane Queue Segregation & Deterministic Cron',
                ],
                'desc' => [
                    'id' => 'Memisahkan queue:critical (<2s), default, ai-rag, & low-batch agar blast laporan tidak pernah memacetkan pembayaran kasir, plus jadwal routes/console.php.',
                    'en' => 'Segregates queue:critical (<2s), default, ai-rag, & low-batch so bulk jobs never block checkout webhooks, plus sectoral cron schedules.',
                ],
            ],
            [
                'number' => 6,
                'code' => 'LAYER_06_DEFENSE_IN_DEPTH_SECURITY',
                'badge' => 'ANTI-IDOR & SIGNED URL',
                'title' => [
                    'id' => '6. Defense-in-Depth: Tenant Global Scope, Rate Limiter & Private Disk',
                    'en' => '6. Defense-in-Depth: Tenant Global Scope, Rate Limiter & Private Disk',
                ],
                'desc' => [
                    'id' => 'Global Scope organization_id (anti-IDOR 404), 4-Tier Rate Limiter, serta isolasi disk="private" dengan Signed URL 5 menit untuk KTP/EMR/Kontrak.',
                    'en' => 'Global organization_id scope (anti-IDOR 404), 4-Tier Rate Limiter, and disk="private" 5-minute Signed URLs for KYC/EMR/Contract files.',
                ],
            ],
            [
                'number' => 7,
                'code' => 'LAYER_07_OMNICHANNEL_NOTIFICATION_MATRIX',
                'badge' => 'WA + PDF + IN-APP',
                'title' => [
                    'id' => '7. State-Transition Omnichannel Notification Matrix',
                    'en' => '7. State-Transition Omnichannel Notification Matrix',
                ],
                'desc' => [
                    'id' => 'Memetakan properti notification_dispatch di setiap transisi State Machine (WhatsApp QR/Action Link, Email PDF Resmi, atau In-App Toast) bebas spam.',
                    'en' => 'Maps deterministic notification_dispatch rules onto every State Machine transition across WhatsApp, Official PDF Email, and In-App alerts.',
                ],
            ],
            [
                'number' => 8,
                'code' => 'LAYER_08_INTERACTIVE_BLUEPRINT_DOCTOR',
                'badge' => 'PRE-FLIGHT AUTO-FIX',
                'title' => [
                    'id' => '8. Interactive "Blueprint Doctor" (Pre-Flight Conflict Resolver)',
                    'en' => '8. Interactive "Blueprint Doctor" (Pre-Flight Conflict Resolver)',
                ],
                'desc' => [
                    'id' => 'Memeriksa celah lintas modul secara live sebelum ekspor (mis. Resep Apotek tanpa Inventory FEFO) lengkap dengan tombol Auto-Fix 1-klik.',
                    'en' => 'Validates cross-module completeness live before export (e.g., Pharmacy Rx without FEFO Inventory) with 1-click Auto-Fix resolution.',
                ],
            ],
        ];
    }

    /**
     * Canonical Map of the "10 Pillars of 100% Ultimate Blueprint" on neriahpro.com.
     */
    public static function tenPerfectionPillars(): array
    {
        return [
            ['num' => '01', 'title' => 'Taxonomy & Primitives', 'detail' => '12 Sektor Bisnis Fungsional + 12 Modul Primitif Composable Lintas Sektor.'],
            ['num' => '02', 'title' => 'Topology Presets', 'detail' => '9 Adapter Stack Terkurasi (Monolith M1–M4, Decoupled D1–D3, Microservices S1–S2).'],
            ['num' => '03', 'title' => 'Database Rigor & Snapshot', 'detail' => 'PostgreSQL 16 Strict ULID, Cursor Pagination O(1), ON DELETE RESTRICT, & Historical Snapshot Immutability.'],
            ['num' => '04', 'title' => 'Transaction & Concurrency', 'detail' => 'Deterministic State Machine, SELECT ... FOR UPDATE, Idempotency 120s, & Transactional Outbox Saga.'],
            ['num' => '05', 'title' => 'Calculation Engine', 'detail' => 'DECIMAL(19,4) HALF_UP, urutan pajak PPN/PB1/PPh21 TER, HPP, & Penny-Allocation 0 selisih sen.'],
            ['num' => '06', 'title' => 'Sectoral UI/UX & Print', 'detail' => '12 Kokpit Multi-Panel Spesifik Profesi + Anti-Slop Design + Thermal 58/80mm & A4 QR Print Specs.'],
            ['num' => '07', 'title' => 'Day-2 Ops & Security', 'detail' => 'InitializeNewTenantAction, Staged CSV Importer, 4-Lane Redis Queue, Rate Limiter, Private Disk, & Notification Matrix.'],
            ['num' => '08', 'title' => 'AI Copilot Tri-Hybrid', 'detail' => 'Closed-Loop 18 Native Function Calling Tools + RAG pgvector 768-dim RRF + Live CAG + Deterministic KAG.'],
            ['num' => '09', 'title' => 'Execution Harness', 'detail' => 'Ponytail 4-Rung Ladder + ECC 6-Stage Loop + Graphify O(1) Lookup + SHA-256 Forensic Audit Trail.'],
            ['num' => '10', 'title' => 'Delivery & Verification', 'detail' => 'Blueprint Doctor Pre-Flight + 6-Phase Prompt Sequencer + ./verify-blueprint.sh + CLI /init/{ULID} + Railpack Deploy.'],
        ];
    }
}
