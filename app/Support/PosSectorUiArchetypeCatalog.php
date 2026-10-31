<?php

namespace App\Support;

use App\Models\PosSector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * PosSectorUiArchetypeCatalog
 *
 * Canonical Sectoral UI/UX Archetype Contracts for all 12 Functional Business Sectors
 * + Realistic Indonesian Domain Seeders & Stress-Test Contracts
 * + 6 Zero-to-Production Killer Orchestration Specifications.
 */
class PosSectorUiArchetypeCatalog
{
    /**
     * Enrich existing pos_sectors rows in database with Sectoral UI/UX Archetypes & Realistic Seeders.
     */
    public static function enrichDatabaseSectors(): void
    {
        if (! Schema::hasTable('pos_sectors')) {
            return;
        }

        $hasArchetypeCol = Schema::hasColumn('pos_sectors', 'ui_ux_archetypes');
        $hasSeederCol = Schema::hasColumn('pos_sectors', 'realistic_seeder_contract');

        if (! $hasArchetypeCol && ! $hasSeederCol) {
            return;
        }

        $archetypes = self::sectorUiArchetypes();
        $seeders = self::sectorRealisticSeeders();

        foreach ($archetypes as $sectorCode => $archetypePayload) {
            $updates = [];
            if ($hasArchetypeCol) {
                $updates['ui_ux_archetypes'] = $archetypePayload;
            }
            if ($hasSeederCol && isset($seeders[$sectorCode])) {
                $updates['realistic_seeder_contract'] = $seeders[$sectorCode];
            }

            if (! empty($updates)) {
                PosSector::query()->where('code', $sectorCode)->update($updates);
            }
        }

        Cache::forget(PosSector::CACHE_KEY);
    }

    /**
     * Normalize legacy or shorthand sector code aliases to canonical DB codes.
     */
    public static function normalizeSectorCode(string $sectorCode): string
    {
        $upper = strtoupper(trim($sectorCode));
        $aliases = [
            'SECTOR_03_HEALTHCARE_EMR' => 'SECTOR_03_HEALTHCARE_CLINICAL',
            'SECTOR_07_EDTECH_LMS' => 'SECTOR_07_EDTECH_MEMBERSHIP',
            'SECTOR_09_FNB_CULINARY' => 'SECTOR_09_FOOD_BEVERAGE',
            'SECTOR_09_FNB_RESTAURANT' => 'SECTOR_09_FOOD_BEVERAGE',
        ];

        return $aliases[$upper] ?? $upper;
    }

    /**
     * Resolve UI/UX Archetype payload for a specific sector code.
     */
    public static function forSector(string $sectorCode): array
    {
        $all = self::sectorUiArchetypes();
        $code = self::normalizeSectorCode($sectorCode);

        return $all[$code] ?? $all['SECTOR_01_COMMERCE_OMNICHANNEL'];
    }

    /**
     * Resolve Realistic Domain Seeder contract for a specific sector code.
     */
    public static function seederForSector(string $sectorCode): array
    {
        $all = self::sectorRealisticSeeders();
        $code = self::normalizeSectorCode($sectorCode);

        return $all[$code] ?? $all['SECTOR_01_COMMERCE_OMNICHANNEL'];
    }

    /**
     * Part 1: 12 Sectoral UI/UX Archetypes (Ergonomics, Keyboard Shortcuts, Forbidden UX & Wireframe Layout).
     */
    public static function sectorUiArchetypes(): array
    {
        return [
            'SECTOR_01_COMMERCE_OMNICHANNEL' => [
                'primary_archetype' => 'POS_SPLIT_TERMINAL',
                'secondary_archetype' => 'DENSE_SKU_MATRIX',
                'headline' => [
                    'id' => 'Terminal Kasir Split-Screen Kecepatan Tinggi & Matriks Varian SKU 2D',
                    'en' => 'High-Velocity Split-Screen POS Terminal & 2D SKU Variant Matrix',
                ],
                'ergonomics_and_layout' => [
                    'Layar Kasir (POS): Full-screen tanpa sidebar (100vh, overflow-hidden). Sisi kiri (8 kolom) untuk pencarian instan barcode/SKU dan grid produk; sisi kanan (4 kolom) untuk keranjang sticky dengan tombol bayar berukuran besar dan numpad virtual.',
                    'Manajemen Varian 2D: Matriks input 2 dimensi (Baris: Ukuran, Kolom: Warna) agar admin dapat mengisi harga grosir/eceran dan stok puluhan SKU dalam satu layar tanpa pindah halaman.',
                    'Indikator Stok Real-Time: Badge stok tersedia vs stok ter-reservasi (Atomic Hold) langsung pada kartu SKU.',
                ],
                'keyboard_shortcuts' => [
                    ['key' => 'F1', 'action' => 'Fokus Instan Cari Barcode / SKU'],
                    ['key' => 'F2', 'action' => 'Ubah Kuantitas Item Aktif'],
                    ['key' => 'F4', 'action' => 'Terapkan Diskon Item / Grosir'],
                    ['key' => 'F8', 'action' => 'Pilih Metode Pembayaran (QRIS / Tunai / EDC)'],
                    ['key' => 'Enter', 'action' => 'Kunci Pembayaran & Cetak Struk Thermal'],
                ],
                'forbidden_ux_patterns' => [
                    'Dilarang memakai tabel CRUD paginasi standar untuk layar transaksi kasir.',
                    'Dilarang memaksa kasir berpindah halaman untuk memilih varian warna/ukuran.',
                ],
                'wireframe_blueprint' => [
                    'viewport_mode' => '100vh_fullscreen_no_sidebar',
                    'grid_columns' => 12,
                    'panes' => [
                        ['span' => 8, 'title' => 'Instant Barcode Search + Dense SKU Grid (8 Col)', 'highlights' => ['[F1] Scan Barcode / SKU', '2D Size x Color Matrix', 'Live Available Qty Badge']],
                        ['span' => 4, 'title' => 'Sticky Cart + Virtual Numpad + Giant Pay CTA (4 Col)', 'highlights' => ['Line Items + Tiered Discount', 'Virtual Touch Numpad', '[F8 / Enter] BAYAR & CETAK STRUK']],
                    ],
                ],
            ],

            'SECTOR_02_HOSPITALITY_SPACE' => [
                'primary_archetype' => 'HORIZONTAL_RESOURCE_GANTT',
                'secondary_archetype' => 'TIME_SLOT_HEATMAP',
                'headline' => [
                    'id' => 'Kalender Gantt Aset Horizontal & Slide-Over Reservasi Instan',
                    'en' => 'Horizontal Resource Gantt Calendar & Instant Slide-Over Booking',
                ],
                'ergonomics_and_layout' => [
                    'Kalender Utama: Sumbu Y (kiri, sticky) menampilkan daftar aset (Kamar 101, Lapangan Badminton 1–4, Unit Kendaraan); Sumbu X (atas) menampilkan rentang jam (06:00–24:00) atau tanggal bulanan.',
                    'Visualisasi Blok Status Tegas: Confirmed (border-l-4 border-emerald-500), Checked-In (border-l-4 border-blue-500), Pending Hold 15m (border-l-4 border-amber-500 border-dashed animate-pulse), dan Maintenance/Dirty (bg-zinc-100 dark:bg-zinc-800 diagonal-stripes).',
                    'Interaksi Klik 0-Reload: Klik sel waktu kosong langsung membuka Slide-Over Drawer kanan untuk membuat reservasi instan tanpa me-reload kalender.',
                ],
                'keyboard_shortcuts' => [
                    ['key' => 'T', 'action' => 'Lompat ke Hari Ini / Jam Sekarang'],
                    ['key' => 'D / W / M', 'action' => 'Ganti Skala Jam Harian / Mingguan / Bulanan'],
                    ['key' => 'N', 'action' => 'Buka Slide-Over Reservasi Walk-In Cepat'],
                ],
                'forbidden_ux_patterns' => [
                    'Dilarang hanya menampilkan daftar booking berbentuk tabel baris tanpa visualisasi bentrok waktu horizontal.',
                    'Dilarang me-reload seluruh halaman saat resepsionis menggeser atau membuat booking baru.',
                ],
                'wireframe_blueprint' => [
                    'viewport_mode' => 'sticky_matrix_with_slideover',
                    'grid_columns' => 12,
                    'panes' => [
                        ['span' => 3, 'title' => 'Sticky Y-Axis Resource List (3 Col)', 'highlights' => ['Kamar 101 [CLEAN]', 'Court Badminton 01 [IN-USE]', 'Court Badminton 02 [MAINT]']],
                        ['span' => 6, 'title' => 'Horizontal Time-Slot Gantt 06:00–24:00 (6 Col)', 'highlights' => ['Confirmed (Emerald Border)', 'Hold 15m (Amber Dashed Pulse)', 'Peak Tariff Heatmap']],
                        ['span' => 3, 'title' => 'Right Slide-Over Instant Booking (3 Col)', 'highlights' => ['Exclusion Check Live', 'Peak/Weekend Price Calc', '1-Click Hold & QRIS']],
                    ],
                ],
            ],

            'SECTOR_03_HEALTHCARE_CLINICAL' => [
                'primary_archetype' => 'CLINICAL_3_PANE_COCKPIT',
                'secondary_archetype' => 'TIMELINE_SOAP_EMR',
                'headline' => [
                    'id' => 'Kokpit Klinis 3-Panel Bebas Modal & Rekam Medis S-O-A-P Terpadu',
                    'en' => 'Zero-Modal 3-Pane Clinical Cockpit & Timeline S-O-A-P EMR',
                ],
                'ergonomics_and_layout' => [
                    'Panel Kiri (2 Kolom): Antrean pasien aktif + Red-Flag Allergies Banner (alergi obat & riwayat penyakit kronis wajib selalu terlihat di bagian atas dengan aksen rose kontras tinggi).',
                    'Panel Tengah (6 Kolom): Form pemeriksaan metode S-O-A-P (Subjective, Objective, Assessment, Plan) + ICD-10 autocomplete dan timeline riwayat kunjungan vertikal.',
                    'Panel Kanan (4 Kolom): E-Prescription Builder terintegrasi stok apotek FEFO (First-Expired, First-Out) dan order pemeriksaan laboratorium.',
                ],
                'keyboard_shortcuts' => [
                    ['key' => 'Alt+1..4', 'action' => 'Lompat Cepat Kolom S / O / A / P'],
                    ['key' => 'Ctrl+R', 'action' => 'Fokus Tambah Obat E-Prescription FEFO'],
                    ['key' => 'Ctrl+Enter', 'action' => 'Finalisasi & Sign-Off Encounter Klinis (Immutable)'],
                ],
                'forbidden_ux_patterns' => [
                    'DILARANG KERAS menggunakan modal bertumpuk (stacked modals): dokter wajib bisa membaca riwayat medis & alergi sambil mengetik resep di layar yang sama.',
                    'Dilarang menyembunyikan banner alergi obat di balik tab tersembunyi.',
                ],
                'wireframe_blueprint' => [
                    'viewport_mode' => 'fixed_3_pane_clinical_cockpit',
                    'grid_columns' => 12,
                    'panes' => [
                        ['span' => 2, 'title' => 'Queue + Rose Allergy Banner (2 Col)', 'highlights' => ['ALERT: Alergi Penisilin', 'Antrean Poli Aktif', 'Vital Signs Ringkas']],
                        ['span' => 6, 'title' => 'S-O-A-P Exam & Vertical EMR History (6 Col)', 'highlights' => ['Subjective & Objective', 'ICD-10 Diagnosis Picker', 'Immutable Past Encounters']],
                        ['span' => 4, 'title' => 'FEFO E-Prescription & Lab Order (4 Col)', 'highlights' => ['Stok Batch & Expiry FEFO', 'Dosis & Aturan Pakai', 'Kirim ke Farmasi & Kasir']],
                    ],
                ],
            ],

            'SECTOR_04_FINTECH_LEDGER' => [
                'primary_archetype' => 'SPREADSHEET_LEDGER_GRID',
                'secondary_archetype' => 'SIDE_BY_SIDE_RECONCILIATION',
                'headline' => [
                    'id' => 'Grid Buku Besar Presisi Tinggi & Rekonsiliasi Bank Bersebelahan',
                    'en' => 'High-Density Spreadsheet Ledger Grid & Side-by-Side Reconciliation',
                ],
                'ergonomics_and_layout' => [
                    'Jurnal & Buku Besar: Tabel berkepadatan tinggi (compact row py-1.5), sticky header, dan Sticky Footer Summary Row yang menghitung SUM(Debit), SUM(Credit), serta Selisih (Out-of-Balance) secara real-time di sisi klien (Alpine.js).',
                    'Kunci Keseimbangan Mutlak: Tombol [Post Journal] otomatis terkunci (disabled) selama nilai selisih Debit vs Credit != 0.',
                    'Layar Rekonsiliasi Bank: Dua tabel bersebelahan (Kiri: Mutasi Rekening Koran CSV/Webhook; Kanan: Jurnal Sistem) dengan indikator Auto-Match Confidence dan tombol 1-klik [Match & Reconcile].',
                ],
                'keyboard_shortcuts' => [
                    ['key' => 'Ins / Alt+N', 'action' => 'Tambah Baris Ayat Jurnal Baru'],
                    ['key' => 'Alt+B', 'action' => 'Auto-Balance Selisih ke Baris Aktif'],
                    ['key' => 'Ctrl+M', 'action' => 'Match & Reconcile Pasangan Mutasi Terpilih'],
                ],
                'forbidden_ux_patterns' => [
                    'Dilarang mengizinkan submit form jurnal tanpa validasi real-time SUM(Debit) == SUM(Credit) di layar.',
                    'Dilarang menyediakan tombol Edit/Delete pada baris jurnal yang sudah berstatus POSTED (wajib via Reversal Entry).',
                ],
                'wireframe_blueprint' => [
                    'viewport_mode' => 'dual_reconciliation_with_sticky_sum_footer',
                    'grid_columns' => 12,
                    'panes' => [
                        ['span' => 6, 'title' => 'Left: Bank Statement / Webhook Feed (6 Col)', 'highlights' => ['CSV / Mutasi Bank Masuk', 'Auto-Match Score 99%', 'Filter Unreconciled']],
                        ['span' => 6, 'title' => 'Right: Immutable Double-Entry Journal (6 Col)', 'highlights' => ['Compact py-1.5COA Rows', 'Sticky Footer: SUM(Dr)=SUM(Cr)', '[POST JOURNAL] Locked if Delta != 0']],
                    ],
                ],
            ],

            'SECTOR_05_INDUSTRIAL_MRP' => [
                'primary_archetype' => 'MULTI_LEVEL_BOM_TREE',
                'secondary_archetype' => 'SHOP_FLOOR_KANBAN',
                'headline' => [
                    'id' => 'Pohon BoM Multi-Level HPP & Layar Tablet Pabrik Kontras Tinggi',
                    'en' => 'Multi-Level BoM Cost Tree & High-Contrast Shop Floor Tablet Mode',
                ],
                'ergonomics_and_layout' => [
                    'Bill of Materials (BoM): Collapsible Tree Table berjenjang (Barang Jadi -> Sub-Assembly -> Bahan Mentah) lengkap dengan kalkulasi akumulasi HPP (Roll-up Cost) per level.',
                    'Layar Pabrik (Shop Floor Mode): Mode kontras tinggi dengan tombol aksi berukuran ekstra besar (h-14 text-base) ramah operator bersarung tangan atau tablet dinding pabrik: [Start Production], [Log Scrap / Reject], [Finish Batch].',
                    'Indikator Kapasitas Mesin: Bar utilisasi work-center & peringatan kekurangan bahan baku sebelum Work Order dirilis.',
                ],
                'keyboard_shortcuts' => [
                    ['key' => 'Space', 'action' => 'Expand / Collapse Sub-Assembly BoM Node'],
                    ['key' => 'F5', 'action' => 'Start / Pause Work Order Batch'],
                    ['key' => 'F9', 'action' => 'Catat Kuantitas Scrap / Reject QC'],
                ],
                'forbidden_ux_patterns' => [
                    'Dilarang memakai tombol kecil (< 44px) atau teks abu-abu pudar pada layar operator lantai produksi.',
                    'Dilarang meratakan struktur BoM berjenjang menjadi tabel datar tanpa relasi induk-anak.',
                ],
                'wireframe_blueprint' => [
                    'viewport_mode' => 'bom_tree_plus_glove_friendly_shop_floor',
                    'grid_columns' => 12,
                    'panes' => [
                        ['span' => 7, 'title' => 'Collapsible Multi-Level BoM Roll-Up Tree (7 Col)', 'highlights' => ['Finished Good -> Sub-Assembly', 'Raw Material Yield & Scrap %', 'Live Roll-Up Unit HPP']],
                        ['span' => 5, 'title' => 'Shop Floor Operator Touch Mode h-14 (5 Col)', 'highlights' => ['[START PRODUCTION BATCH]', '[LOG SCRAP / QC REJECT]', '[FINISH & BACKFLUSH STOCK]']],
                    ],
                ],
            ],

            'SECTOR_06_LOGISTICS_FLEET' => [
                'primary_archetype' => 'SPLIT_MAP_DISPATCHER',
                'secondary_archetype' => 'STEPPER_MANIFEST_TRACKER',
                'headline' => [
                    'id' => 'Kokpit Dispatcher Muatan Armada & PWA Kurir Bukti Kirim (PoD)',
                    'en' => 'Split Manifest Load Dispatcher & Mobile-First Courier PoD PWA',
                ],
                'ergonomics_and_layout' => [
                    'Layar Dispatcher: Setengah layar kiri untuk daftar manifes pengiriman dan kapasitas muatan armada (Load Volume m3 & Weight kg Progress Bar); setengah layar kanan untuk milestone timeline status kurir.',
                    'Tampilan Kurir (Mobile-First PWA): Kartu pengantaran urut rute dengan Bottom Action Bar tetap di bawah jempol: Panggil Penerima, Buka Navigasi, Scan Barcode Paket, dan Upload Foto + Canvas Signature Pad untuk Proof of Delivery (PoD).',
                    'Rekonsiliasi Setoran COD: Panel khusus serah-terima uang tunai kurir vs total tagihan resi terkirim.',
                ],
                'keyboard_shortcuts' => [
                    ['key' => 'F2', 'action' => 'Scan Resi / Waybill Masuk Manifes'],
                    ['key' => 'Alt+A', 'action' => 'Auto-Assign Rute ke Armada Tersedia'],
                ],
                'forbidden_ux_patterns' => [
                    'Dilarang menampilkan tabel desktop 12 kolom pada layar PWA kurir lapangan.',
                    'Dilarang mengizinkan status DELIVERED tanpa lampiran foto PoD atau tanda tangan digital.',
                ],
                'wireframe_blueprint' => [
                    'viewport_mode' => 'split_dispatcher_and_mobile_pod_bar',
                    'grid_columns' => 12,
                    'panes' => [
                        ['span' => 6, 'title' => 'Manifest & Vehicle Load Capacity Bar (6 Col)', 'highlights' => ['BlindVan B-9120: 78% Kg / 65% CBM', 'Scan Waybill Batch', 'COD Cash Liability Counter']],
                        ['span' => 6, 'title' => 'Milestone Stepper & Courier PoD Card (6 Col)', 'highlights' => ['PICKED -> IN_TRANSIT -> DELIVERED', 'Canvas Signature + Geo Photo', 'Sticky Bottom Thumb Action Bar']],
                    ],
                ],
            ],

            'SECTOR_07_EDTECH_MEMBERSHIP' => [
                'primary_archetype' => 'COHORT_GRADEBOOK_MATRIX',
                'secondary_archetype' => 'DISTRACTION_FREE_PLAYER',
                'headline' => [
                    'id' => 'Matriks Buku Nilai Navigasi Panah & Ruang Belajar Bebas Gangguan',
                    'en' => 'Arrow-Key Cohort Gradebook Matrix & Distraction-Free Learning Player',
                ],
                'ergonomics_and_layout' => [
                    'Buku Nilai & Presensi: Matriks 2 arah (Baris: Nama Siswa/Atlet/Peserta; Kolom: Pertemuan 1 s/d 16 atau Modul Ujian) dengan navigasi tombol panah keyboard seperti spreadsheet dan toggle presensi 1-klik (Hadir / Izin / Sakit / Alfa).',
                    'Distraction-Free Player: Layar materi/ujian menyembunyikan navigasi global agar fokus penuh, dilengkapi timer server-side anti-cheat.',
                    'Rapor Perkembangan Skill: Grafik radar (Spider/Radar Chart) dan progress bar penguasaan kompetensi per kurikulum.',
                ],
                'keyboard_shortcuts' => [
                    ['key' => 'Arrow Keys', 'action' => 'Navigasi Antar Sel Nilai Siswa x Pertemuan'],
                    ['key' => 'H / I / S / A', 'action' => 'Toggle Cepat Presensi Hadir / Izin / Sakit / Alfa'],
                ],
                'forbidden_ux_patterns' => [
                    'Dilarang memaksa pengajar membuka halaman detail satu per satu untuk mengisi nilai 30 siswa.',
                ],
                'wireframe_blueprint' => [
                    'viewport_mode' => 'spreadsheet_gradebook_plus_focus_player',
                    'grid_columns' => 12,
                    'panes' => [
                        ['span' => 8, 'title' => '2D Cohort Gradebook & 1-Click Attendance (8 Col)', 'highlights' => ['Sticky Student Column', 'Session 01..16 Arrow-Key Cells', '[H | I | S | A] Instant Toggle']],
                        ['span' => 4, 'title' => 'Competency Radar & Curriculum Progress (4 Col)', 'highlights' => ['Skill Mastery Progress Bars', 'Exam Anti-Cheat Timer', 'Certificate QR Verification']],
                    ],
                ],
            ],

            'SECTOR_08_PROFESSIONAL_PSA' => [
                'primary_archetype' => 'MILESTONE_KANBAN_BOARD',
                'secondary_archetype' => 'PERSISTENT_FLOATING_TIMER',
                'headline' => [
                    'id' => 'Papan Kanban Milestone, Floating Stopwatch Billable & Portal Sign-Off Klien',
                    'en' => 'Milestone Kanban Board, Persistent Billable Timer & Clean Client Sign-Off Portal',
                ],
                'ergonomics_and_layout' => [
                    'Pelacak Waktu Kerja (Billable Hours): Widget Floating Stopwatch ringkas di pojok layar yang tetap berjalan meskipun konsultan berpindah halaman, lengkap dengan konversi 1-klik dari Timesheet Logs menjadi draf Client Invoice.',
                    'Papan Milestone & Scope: Kanban kolom status deliverable dengan indikator burn-rate jam kerja vs anggaran kontrak.',
                    'Portal Persetujuan Klien (hideNav): Halaman bersih tanpa menu internal tempat klien meninjau deliverable dan menekan [Setujui & Tanda Tangani Milestone].',
                ],
                'keyboard_shortcuts' => [
                    ['key' => 'Alt+T', 'action' => 'Start / Stop Persistent Billable Timer'],
                    ['key' => 'Alt+I', 'action' => 'Konversi Unbilled Timesheet ke Draft Invoice'],
                ],
                'forbidden_ux_patterns' => [
                    'Dilarang me-reset timer jam kerja konsultan saat berpindah menu halaman.',
                    'Dilarang mengekspos sidebar operasional internal kepada klien di halaman persetujuan deliverable.',
                ],
                'wireframe_blueprint' => [
                    'viewport_mode' => 'kanban_with_persistent_timer_and_clean_portal',
                    'grid_columns' => 12,
                    'panes' => [
                        ['span' => 8, 'title' => 'Deliverable Milestone Kanban & Burn-Rate (8 Col)', 'highlights' => ['Backlog -> Review -> Signed-Off', 'Retainer Hours Burn Bar', 'Change Request Scope Lock']],
                        ['span' => 4, 'title' => 'Persistent Timer + Clean Client Approval View (4 Col)', 'highlights' => ['Floating Timer 02:14:38 [BILLABLE]', '1-Click Timesheet -> Invoice', '[SETUJUI & TANDA TANGANI MILESTONE]']],
                    ],
                ],
            ],

            'SECTOR_09_FOOD_BEVERAGE' => [
                'primary_archetype' => 'KDS_TICKET_BOARD',
                'secondary_archetype' => 'FLOOR_TABLE_MAP',
                'headline' => [
                    'id' => 'Layar Dapur KDS Mode Gelap SLA & Peta Meja / Grid Modifier Cepat',
                    'en' => 'Forced-Dark SLA Kitchen Display System (KDS) & Fast Grid Modifier POS',
                ],
                'ergonomics_and_layout' => [
                    'Kitchen Display System (KDS): Layar gelap (Dark Mode Forced) berisi kartu Kitchen Order Tickets (KOT) dengan warna border dinamis sesuai durasi tunggu: < 5 menit (Emerald), 5–10 menit (Amber), > 10 menit (Rose berkedip).',
                    'Interaksi Dapur 1-Sentuh: Klik item mencoret menu yang sudah matang; tombol [Bump / Selesai] memindahkan tiket dan mengirim notifikasi ke pelayan/kasir.',
                    'Fast Grid Modifier Modal: Saat kasir memilih menu, langsung muncul tombol grid cepat untuk modifier wajib/opsional (Tingkat Kepedasan, Extra Topping, Less Sugar), bukan dropdown lambat.',
                ],
                'keyboard_shortcuts' => [
                    ['key' => '1..9', 'action' => 'Bump / Selesaikan Kartu Tiket KDS Nomor 1..9'],
                    ['key' => 'F3', 'action' => 'Pindah Meja / Gabung Bill (Split/Merge Table)'],
                ],
                'forbidden_ux_patterns' => [
                    'Dilarang memakai select dropdown bertingkat untuk memilih tingkat pedas/topping di kasir F&B.',
                    'Dilarang menampilkan background putih menyilaukan pada layar monitor dapur (KDS).',
                ],
                'wireframe_blueprint' => [
                    'viewport_mode' => 'forced_dark_kds_and_button_grid_modifiers',
                    'grid_columns' => 12,
                    'panes' => [
                        ['span' => 7, 'title' => 'Forced-Dark KDS Ticket Board with SLA Borders (7 Col)', 'highlights' => ['#KOT-104 Meja 07 [03:12 Emerald]', '#KOT-101 GoFood [11:05 Rose Pulse]', 'Tap Item Strikethrough + [BUMP]']],
                        ['span' => 5, 'title' => 'Floor Table Map & Instant Button-Grid Modifiers (5 Col)', 'highlights' => ['Meja Dine-In Occupancy Map', 'Grid Tombol: Pedas Lv.3 | Less Ice', 'Auto Recipe Backflush HPP']],
                    ],
                ],
            ],

            'SECTOR_10_MEDIA_TICKETING' => [
                'primary_archetype' => 'INTERACTIVE_SEAT_MAP',
                'secondary_archetype' => 'DAM_MASONRY_GRID',
                'headline' => [
                    'id' => 'Peta Kursi Interaktif dengan Countdown Lock & Grid Aset Royalti Kreator',
                    'en' => 'Interactive Seat Map with Hold Countdown & Creator Royalty DAM Grid',
                ],
                'ergonomics_and_layout' => [
                    'Pemilihan Kursi / Tiket: Peta kursi berbasis SVG/Grid interaktif dengan Countdown Timer Banner mencolok di bagian atas ("Kursi Anda dikunci selama 09:42") terhubung ke Redis/DB TTL lock.',
                    'Gate Check-In Scanner: Layar pemindai QR tiket berkecepatan tinggi dengan umpan balik warna layar penuh (Hijau: Valid Masuk, Merah: Tiket Sudah Dipakai / Palsu).',
                    'Manajemen Aset Digital & Royalti: Grid media (DAM) dengan preview instan dan tabel pembagian komisi/royalti otomatis per kreator.',
                ],
                'keyboard_shortcuts' => [
                    ['key' => 'Space', 'action' => 'Preview Cepat Aset Media Terpilih'],
                    ['key' => 'Esc', 'action' => 'Lepas Kunci Kursi Sementara'],
                ],
                'forbidden_ux_patterns' => [
                    'Dilarang membiarkan pembeli memilih kursi tanpa indikator hitung mundur batas waktu pembayaran yang jelas.',
                ],
                'wireframe_blueprint' => [
                    'viewport_mode' => 'seat_map_with_ttl_banner_and_dam_grid',
                    'grid_columns' => 12,
                    'panes' => [
                        ['span' => 7, 'title' => 'Interactive SVG Seat Map + Top Hold Timer (7 Col)', 'highlights' => ['BANNER: Kursi VVIP-A4 Dikunci 09:42', 'VIP / CAT-1 / Sold-Out Legend', 'Anti-Double-Sell Atomic Hold']],
                        ['span' => 5, 'title' => 'DAM Preview Grid & Creator Royalty Split (5 Col)', 'highlights' => ['Instant Media Preview', 'Auto Split: Kreator 70% / Platform 30%', 'Gate QR Anti-Passback Scanner']],
                    ],
                ],
            ],

            'SECTOR_11_HRIS_INTERNAL_OPS' => [
                'primary_archetype' => 'SHIFT_ROSTER_MATRIX',
                'secondary_archetype' => 'PAYSLIP_SIMULATOR_VIEW',
                'headline' => [
                    'id' => 'Matriks Jadwal Shift Anti-Bentrok & Komparasi Payroll 3-Kolom PPh 21',
                    'en' => 'Conflict-Aware Shift Roster Matrix & 3-Column Payroll Simulator View',
                ],
                'ergonomics_and_layout' => [
                    'Jadwal Shift (Roster): Matriks Karyawan vs Tanggal Mingguan/Bulanan dengan deteksi bentrok otomatis (peringatan merah tegas jika karyawan dijadwalkan Shift Malam lalu langsung Shift Pagi keesokan harinya atau saat cuti disetujui).',
                    'Layar Proses Payroll 3-Kolom: Komparasi berdampingan: Kolom Kiri (Gaji Pokok + Tunjangan Tetap/Variabel), Kolom Tengah (Potongan Absen + PPh 21 TER + BPJS Kesehatan/TK), Kolom Kanan (Take Home Pay Bersih + Selisih vs Bulan Lalu).',
                    'Approval Berlapis: Baris pengajuan cuti/reimburse menampilkan progres persetujuan (Supervisor -> HR -> Finance).',
                ],
                'keyboard_shortcuts' => [
                    ['key' => 'P / S / M / L', 'action' => 'Set Cepat Shift Pagi / Siang / Malam / Libur pada Sel Roster'],
                    ['key' => 'Alt+L', 'action' => 'Lock Periode Payroll & Generate Slip PDF'],
                ],
                'forbidden_ux_patterns' => [
                    'Dilarang menyembunyikan rincian potongan PPh 21 TER dan BPJS tanpa komparasi terhadap periode gaji sebelumnya.',
                ],
                'wireframe_blueprint' => [
                    'viewport_mode' => 'roster_matrix_and_3_col_payroll_comparator',
                    'grid_columns' => 12,
                    'panes' => [
                        ['span' => 4, 'title' => 'Col 1: Gross Salary + Allowances + Roster (4 Col)', 'highlights' => ['Gaji Pokok + Tunjangan Hadir', 'Alert Merah: Malam -> Pagi Conflict', 'Overtime Verified Hours']],
                        ['span' => 4, 'title' => 'Col 2: Deductions + PPh 21 TER + BPJS (4 Col)', 'highlights' => ['PPh 21 Kategori TER A/B/C', 'BPJS TK (JHT/JP) & Kesehatan', 'Potongan Kasbon / Alfa']],
                        ['span' => 4, 'title' => 'Col 3: Net Take Home Pay + MoM Delta (4 Col)', 'highlights' => ['THP Bersih Presisi Rupiah', 'Variance vs Bulan Lalu (+4.2%)', '[LOCK PAYROLL PERIOD]']],
                    ],
                ],
            ],

            'SECTOR_12_GOVTECH_NONPROFIT' => [
                'primary_archetype' => 'TRANSPARENCY_FUND_TRACKER',
                'secondary_archetype' => 'DONOR_IMPACT_PORTAL',
                'headline' => [
                    'id' => 'Pelacak Transparansi Dana Terikat/Tidak Terikat & Portal Audit SHA-256',
                    'en' => 'Restricted/Unrestricted Fund Tracker & SHA-256 Verified Donor Impact Portal',
                ],
                'ergonomics_and_layout' => [
                    'Kartu Program & Kampanye: Menampilkan rasio Dana Terkumpul vs Target, Dana Tersalurkan vs Saldo Mengendap, serta pemisahan visual tegas antara Dana Terikat (Restricted Fund) dan Dana Tidak Terikat (Unrestricted Fund) sesuai ISAK 35.',
                    'Jejak Audit Publik & Internal: Tabel penyaluran dana di lapangan lengkap dengan bukti foto ber-geotag, penerima manfaat, dan lencana verifikasi SHA-256 Cryptographic Hash.',
                    'Portal Donatur: Riwayat kontribusi dan unduhan kwitansi donasi resmi berstempel digital.',
                ],
                'keyboard_shortcuts' => [
                    ['key' => 'Alt+V', 'action' => 'Verifikasi Integritas Hash SHA-256 Bukti Penyaluran'],
                ],
                'forbidden_ux_patterns' => [
                    'Dilarang mencampur saldo Dana Terikat (misal: Wakaf/Beasiswa Spesifik) dengan kas operasional umum dalam satu angka tanpa pemisahan.',
                ],
                'wireframe_blueprint' => [
                    'viewport_mode' => 'isak35_fund_split_and_sha256_public_ledger',
                    'grid_columns' => 12,
                    'panes' => [
                        ['span' => 6, 'title' => 'Restricted vs Unrestricted Fund Ratio Cards (6 Col)', 'highlights' => ['Terkumpul vs Target & Saldo Mengendap', 'ISAK 35: Dana Terikat vs Operasional', 'Zero Cross-Fund Misuse Guard']],
                        ['span' => 6, 'title' => 'Field Disbursement Ledger + SHA-256 Proof (6 Col)', 'highlights' => ['Foto Bukti Lapangan + Koordinat Lokasi', 'Badge: SHA-256 Hash Terverifikasi', 'Unduh Laporan Dampak Publik']],
                    ],
                ],
            ],
        ];
    }

    /**
     * Part 2 (Killer Feature #2): Domain-Realistic Indonesian Seeders & Stress-Test Contracts per Sector.
     */
    public static function sectorRealisticSeeders(): array
    {
        return [
            'SECTOR_01_COMMERCE_OMNICHANNEL' => [
                'seeder_class' => 'CommerceOmnichannelRealisticSeeder',
                'forbidden_dummy_strings' => ['Test Name 1', 'Product ABC', 'Lorem ipsum', ' nominal 100'],
                'seed_volumes' => ['warehouses' => 3, 'sku_variants' => 48, 'tiered_price_rules' => 12, 'sales_orders' => 120],
                'realistic_fixtures' => [
                    '3 Gudang nyata (Gudang Pusat Sunter, Hub Surabaya Rungkut, Outlet Flagship Tanah Abang).',
                    '48 SKU matriks Ukuran (S/M/L/XL) x Warna dengan konversi UoM (1 Karton = 24 Pcs) dan harga grosir bertingkat.',
                    'Stress-test: 10 pesanan flash-sale konkuren pada 1 SKU bersisa stok 3 unit untuk memvalidasi SELECT ... FOR UPDATE.',
                ],
            ],
            'SECTOR_02_HOSPITALITY_SPACE' => [
                'seeder_class' => 'HospitalitySpaceRealisticSeeder',
                'forbidden_dummy_strings' => ['Room 1', 'Test Guest', 'Lorem ipsum'],
                'seed_volumes' => ['bookable_units' => 12, 'pricing_rules' => 6, 'active_members' => 25, 'bookings' => 110],
                'realistic_fixtures' => [
                    '4 Lapangan Badminton / Padel + 8 Kamar Villa dengan tarif Peak Hours (18:00–22:00 & Akhir Pekan) vs Off-Peak.',
                    '25 Member aktif Indonesia dengan kuota paket bulanan dan deposit.',
                    'Stress-test: Skenario overlapping booking jam 19:00–21:00 vs 20:00–22:00 dan hold TTL 15 menit kedaluwarsa.',
                ],
            ],
            'SECTOR_03_HEALTHCARE_CLINICAL' => [
                'seeder_class' => 'HealthcareClinicalRealisticSeeder',
                'forbidden_dummy_strings' => ['Patient 1', 'Drug X', 'Lorem ipsum'],
                'seed_volumes' => ['practitioners' => 8, 'patients_masked' => 40, 'drug_fefo_batches' => 30, 'encounters' => 105],
                'realistic_fixtures' => [
                    '8 Dokter Spesialis/Umum dengan jadwal praktik poli, 40 pasien (NIK terenkripsi + Red-Flag alergi Amoxicillin/NSAID).',
                    '30 Batch obat apotek dengan tanggal kedaluwarsa berbeda untuk menguji pengurangan stok FEFO otomatis.',
                    'Stress-test: Percobaan mengubah rekam medis S-O-A-P yang sudah SIGNED_OFF (wajib ditolak & dialihkan ke Addendum).',
                ],
            ],
            'SECTOR_04_FINTECH_LEDGER' => [
                'seeder_class' => 'FintechDoubleEntryRealisticSeeder',
                'forbidden_dummy_strings' => ['Account 1', 'Test Tx', 'amount 100'],
                'seed_volumes' => ['coa_accounts' => 36, 'journal_entries' => 150, 'bank_statement_lines' => 120],
                'realistic_fixtures' => [
                    '36 Akun Bagan Akun (COA) standar PSAK Indonesia (Kas, Bank BCA/Mandiri, Piutang, Pendapatan Ditangguhkan, PPN Keluaran 11%).',
                    '150 Jurnal seimbang presisi DECIMAL(19,4) termasuk kasus cicilan Rp 100.000 / 3 bulan = [33333, 33333, 33334].',
                    'Stress-test: Percobaan posting jurnal selisih Rp 1 dan replay webhook pembayaran ganda.',
                ],
            ],
            'SECTOR_05_INDUSTRIAL_MRP' => [
                'seeder_class' => 'IndustrialMrpRealisticSeeder',
                'forbidden_dummy_strings' => ['Item A', 'Factory 1'],
                'seed_volumes' => ['bom_trees' => 6, 'raw_materials' => 35, 'work_orders' => 100],
                'realistic_fixtures' => [
                    '6 Pohon Bill of Materials 3-level (Barang Jadi -> Sub-Assembly -> Bahan Baku) dengan persentase susut/scrap realistis.',
                    '100 Work Order produksi lengkap dengan pencatatan konsumsi lot bahan baku dan kalkulasi HPP aktual.',
                ],
            ],
            'SECTOR_06_LOGISTICS_FLEET' => [
                'seeder_class' => 'LogisticsFleetRealisticSeeder',
                'forbidden_dummy_strings' => ['Driver 1', 'City A'],
                'seed_volumes' => ['vehicles' => 10, 'couriers' => 15, 'waybills' => 140],
                'realistic_fixtures' => [
                    '10 Armada (Blind Van, CDE Box, Wingbox) dengan batas beban kg & kubikasi CBM nyata rute Jabodetabek–Bandung–Surabaya.',
                    '140 Resi pengiriman (Reguler, Same-Day, COD) lengkap dengan milestone timeline dan rekonsiliasi setoran COD kurir.',
                ],
            ],
            'SECTOR_07_EDTECH_MEMBERSHIP' => [
                'seeder_class' => 'EdtechCohortRealisticSeeder',
                'forbidden_dummy_strings' => ['Student 1', 'Course Test'],
                'seed_volumes' => ['cohorts' => 4, 'students' => 60, 'sessions' => 64, 'submissions' => 240],
                'realistic_fixtures' => [
                    '4 Kelas Cohort aktif masing-masing 16 pertemuan dengan matriks nilai bobot tugas/UTS/UAS dan rekap presensi.',
                    'Bank soal ujian berbatas waktu server-side dengan sertifikat terverifikasi hash unik.',
                ],
            ],
            'SECTOR_08_PROFESSIONAL_PSA' => [
                'seeder_class' => 'ProfessionalPsaRealisticSeeder',
                'forbidden_dummy_strings' => ['Client 1', 'Task Test'],
                'seed_volumes' => ['engagements' => 8, 'milestones' => 32, 'timesheet_logs' => 180],
                'realistic_fixtures' => [
                    '8 Kontrak Retainer & Fixed-Milestone agensi/konsultan dengan tarif per jam senior/associate berbeda.',
                    '180 Log timesheet billable vs non-billable siap dikonversi menjadi draf faktur termin klien.',
                ],
            ],
            'SECTOR_09_FOOD_BEVERAGE' => [
                'seeder_class' => 'FnbCulinaryKdsRealisticSeeder',
                'forbidden_dummy_strings' => ['Food 1', 'Table X'],
                'seed_volumes' => ['tables' => 20, 'menu_items' => 45, 'modifier_groups' => 10, 'kot_tickets' => 130],
                'realistic_fixtures' => [
                    '20 Meja Dine-In + kanal QR Table & Delivery dengan 45 menu, resep HPP gramasi bahan segar, dan grup modifier cepat.',
                    'Antrean tiket dapur (KDS) aktif pada 3 ambang waktu (<5m, 5–10m, >10m) beserta rekonsiliasi kasir pergantian shift.',
                ],
            ],
            'SECTOR_10_MEDIA_TICKETING' => [
                'seeder_class' => 'MediaTicketingRealisticSeeder',
                'forbidden_dummy_strings' => ['Event 1', 'Seat 1'],
                'seed_volumes' => ['events' => 3, 'seat_inventory' => 200, 'royalty_contracts' => 12, 'ticket_orders' => 120],
                'realistic_fixtures' => [
                    'Denah kursi konser/stadion 200 kursi (VVIP, VIP, Festival) dengan simulasi hold TTL 10 menit dan QR check-in anti-passback.',
                    ' Kontrak bagi hasil royalti multi-kreator otomatis terhitung per transaksi lunas.',
                ],
            ],
            'SECTOR_11_HRIS_INTERNAL_OPS' => [
                'seeder_class' => 'HrisPayrollRealisticSeeder',
                'forbidden_dummy_strings' => ['Employee 1', 'Dept A'],
                'seed_volumes' => ['employees' => 45, 'shift_rosters' => 300, 'payslips' => 90],
                'realistic_fixtures' => [
                    '45 Karyawan lintas status PTKP (TK/0, K/1, K/2) lengkap dengan perhitungan PPh 21 TER 2024+, BPJS Kesehatan & Ketenagakerjaan.',
                    'Matriks jadwal 3 shift mingguan termasuk kasus uji bentrok Shift Malam ke Shift Pagi.',
                ],
            ],
            'SECTOR_12_GOVTECH_NONPROFIT' => [
                'seeder_class' => 'GovtechNonprofitIsak35RealisticSeeder',
                'forbidden_dummy_strings' => ['Campaign 1', 'Donor Test'],
                'seed_volumes' => ['coa_isak35' => 28, 'campaigns' => 5, 'donations' => 150, 'disbursements' => 45],
                'realistic_fixtures' => [
                    'Bagan Akun sesuai ISAK 35 memisahkan Aset Neto Terikat (Wakaf/Beasiswa Spesifik) dan Tidak Terikat (Operasional).',
                    '5 Program kemanusiaan & pendidikan dengan 150 mutasi donasi dan 45 penyaluran lapangan berstempel hash SHA-256.',
                ],
            ],
        ];
    }

    /**
     * Part 2: Canonical Metadata of the 6 Killer Developer-Retention Features.
     */
    public static function killerFeaturesSummary(): array
    {
        return [
            [
                'number' => 1,
                'code' => 'KILLER_01_PHASED_PROMPT_SEQUENCER',
                'badge' => 'ANTI-OVERFLOW',
                'name' => 'Phased Prompt Sequencer (Phase 1 to 6)',
                'title' => [
                    'id' => '1. Phased Prompt Sequencer (Fase 1 s/d 6)',
                    'en' => '1. Phased Prompt Sequencer (Phase 1 to 6)',
                ],
                'tagline' => 'Anti-Context Overflow Engine: Eksekusi bertahap Fase 1–6 tanpa degradasi memori AI.',
                'desc' => [
                    'id' => 'Memecah eksekusi ke .agents/prompts/01-foundation.md s/d 06-deployment.md. Mewajibkan 100% Acceptance Test hijau di Fase 4 sebelum menyentuh satu baris UI pun di Fase 5.',
                    'en' => 'Slices execution into .agents/prompts/01-foundation.md through 06-deployment.md. Enforces 100% green Acceptance Tests in Phase 4 before touching UI in Phase 5.',
                ],
                'deliverable_path' => '.agents/prompts/01-foundation.md .. 06-deployment.md',
            ],
            [
                'number' => 2,
                'code' => 'KILLER_02_REALISTIC_DOMAIN_SEEDERS',
                'badge' => 'INDONESIAN DATA',
                'name' => 'Realistic Domain Seeders & Stress-Test Factory',
                'title' => [
                    'id' => '2. Realistic Domain Seeders & Stress-Test Factory',
                    'en' => '2. Realistic Domain Seeders & Stress-Test Factory',
                ],
                'tagline' => '100+ data awal realistis sesuai industri Indonesia & skenario uji race-condition nyata.',
                'desc' => [
                    'id' => 'Menghasilkan data operasional Indonesia asli sesuai sektor (SKU ritel, ICD-10 klinis, CoA PSAK, PPh21 TER) beserta uji bentrok konkuren siap demo ke investor.',
                    'en' => 'Generates authentic Indonesian sector fixtures (retail SKUs, ICD-10 codes, PSAK CoA ledgers, PPh21 TER) and concurrency stress fixtures.',
                ],
                'deliverable_path' => 'database/seeders/RealisticSectorSeeder.php',
            ],
            [
                'number' => 3,
                'code' => 'KILLER_03_VISUAL_ARCH_WIREFRAME_PREVIEWER',
                'badge' => 'LIVE WIREFRAME',
                'name' => 'Visual Architecture & Sectoral Wireframe Previewer',
                'title' => [
                    'id' => '3. Visual Architecture & Sectoral Wireframe Previewer',
                    'en' => '3. Visual Architecture & Sectoral Wireframe Previewer',
                ],
                'tagline' => 'Live Mermaid ERD + Interactive State Machine + Pratinjau Kerangka UI Spesifik Industri.',
                'desc' => [
                    'id' => 'Menampilkan topologi panel layar operasional spesifik profesi (POS Split, Clinical 3-Pane, KDS, ISAK 35), ERD ULID, dan transisi State Machine sebelum koding dimulai.',
                    'en' => 'Renders industry-specific multi-pane cockpit wireframes, ULID ERDs, and State Machine transitions visually before coding begins.',
                ],
                'deliverable_path' => '/blueprint/{slug}#visual-previewer',
            ],
            [
                'number' => 4,
                'code' => 'KILLER_04_BROWNFIELD_SCHEMA_IMPORTER',
                'badge' => 'ZERO DATA LOSS',
                'name' => 'Reverse-Engineer / Brownfield Schema Importer',
                'title' => [
                    'id' => '4. Reverse-Engineer / Brownfield SQL & Migration Importer',
                    'en' => '4. Reverse-Engineer / Brownfield SQL & Migration Importer',
                ],
                'tagline' => 'Audit schema.sql / migrasi lama, deteksi dosa arsitektur, & susun rencana migrasi ULID tanpa hilang data.',
                'desc' => [
                    'id' => 'Memindai DDL SQL lama, migrasi Laravel, atau schema.prisma untuk mendeteksi utang AUTO_INCREMENT / FLOAT / CASCADE dan mencetak rencana migrasi Shadow ULID 4 tahap.',
                    'en' => 'Scans legacy SQL DDL, Laravel migrations, or Prisma schemas to detect AUTO_INCREMENT / FLOAT / CASCADE debt and generate a 4-stage Shadow ULID plan.',
                ],
                'deliverable_path' => 'POST /api/blueprint/brownfield-audit',
            ],
            [
                'number' => 5,
                'code' => 'KILLER_05_SLOP_DRIFT_AUDITOR',
                'badge' => 'CI GATEKEEPER',
                'name' => 'Blueprint Drift Auditor & Slop Scanner',
                'title' => [
                    'id' => '5. Blueprint Drift Auditor & ./verify-blueprint.sh',
                    'en' => '5. Blueprint Drift Auditor & ./verify-blueprint.sh',
                ],
                'tagline' => 'Script otomatis ./verify-blueprint.sh & php artisan pos:audit pencetak Auto-Fix Prompt.',
                'desc' => [
                    'id' => 'Menyertakan skrip POSIX verify-blueprint.sh dan CLI php artisan pos:audit yang menangkap kapsul rounded-full, gradien, alert(), atau offset pagination beserta Auto-Fix Prompt.',
                    'en' => 'Ships standalone POSIX verify-blueprint.sh and php artisan pos:audit catching rounded-full, gradients, alert(), and offset pagination with Auto-Fix Prompts.',
                ],
                'deliverable_path' => 'verify-blueprint.sh & php artisan pos:audit',
            ],
            [
                'number' => 6,
                'code' => 'KILLER_06_ONE_COMMAND_CLI_BOOTSTRAPPER',
                'badge' => '3-SEC INIT',
                'name' => 'One-Command CLI Bootstrapper',
                'title' => [
                    'id' => '6. One-Command CLI Workspace Bootstrapper',
                    'en' => '6. One-Command CLI Workspace Bootstrapper',
                ],
                'tagline' => '1 perintah terminal (curl -sL neriahpro.com/init/ULID | bash) membangun seluruh fondasi proyek.',
                'desc' => [
                    'id' => 'Menanamkan AGENTS.md, .cursorrules, .agents/PONYTAIL.md, graph.json, 6 fase prompt, RealisticSectorSeeder.php, dan verify-blueprint.sh dalam 3 detik tercatat SHA-256.',
                    'en' => 'Materializes AGENTS.md, .cursorrules, .agents/PONYTAIL.md, graph.json, 6 phased prompts, RealisticSectorSeeder.php, and verify-blueprint.sh in 3 seconds.',
                ],
                'deliverable_path' => 'GET /init/{ulid_or_slug}',
            ],
        ];
    }
}

