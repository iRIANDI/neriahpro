<?php

declare(strict_types=1);

namespace App\Support;

/**
 * PosInclusiveCommercialCatalog — Project OS v8.0
 *
 * Implements the 6 Inclusive Commercial, UX & Security Pillars (inklusif.md):
 * 1. Adaptive Dual-Persona UX: 5-Step Zero-Jargon Business Storyboard Wizard <-> 3-Pane Architect Pro Studio
 * 2. 36 Concrete Visual Sector Frontend UI/UX Blueprints (12 Functional Sectors x 3 Visual Variants + 4 Nav Shells)
 * 3. Tier-1 Global Native Localization (ID, EN, ZH-CN, JA, AR with automatic RTL dir="rtl", ES, DE) & Jurisdiction-Aware Compliance
 * 4. Inclusive Hybrid Value-Metric Pricing Catalog (Sandbox, Impact/NGO CSR Grant, Single-App UMKM, Pro, Agency White-Label, Turnkey)
 * 5. 5 High-Retention Flywheel Hooks (Living CVE Alerts + Auto-Patch Prompt, Instant No-Login Builder, Active Vault, Viral Certified Badge, 70:30 Community Marketplace)
 * 6. 5-Layer Banking-Grade IP & Platform Security (AES-256 Envelope Vault, Zero-Width Unicode Canary Watermark, 1-Click MCP Token Revoke, Chained SHA-256 Audit, Milestone Escrow)
 */
class PosInclusiveCommercialCatalog
{
    public const VERSION = '8.0.0';

    public const PERSONA_BUSINESS_WIZARD = 'BUSINESS_WIZARD';
    public const PERSONA_ARCHITECT_PRO = 'ARCHITECT_PRO';

    /**
     * Zero-Width Unicode Steganographic Alphabet for Invisible Canary Watermarking.
     */
    private const ZW_BOUNDARY = "\u{200D}"; // Zero-Width Joiner
    private const ZW_BIT_ZERO = "\u{200B}"; // Zero-Width Space
    private const ZW_BIT_ONE  = "\u{200C}"; // Zero-Width Non-Joiner

    /**
     * Normalize Tier-1 Blueprint Output Locale code.
     */
    public static function normalizeOutputLocale(?string $locale): string
    {
        $raw = trim((string) ($locale ?: 'id'));
        $map = [
            'id' => 'id',
            'id-id' => 'id',
            'en' => 'en',
            'en-us' => 'en',
            'en-gb' => 'en',
            'en-sg' => 'en',
            'zh' => 'zh-CN',
            'zh-cn' => 'zh-CN',
            'zh-tw' => 'zh-CN',
            'ja' => 'ja',
            'ja-jp' => 'ja',
            'ar' => 'ar',
            'ar-sa' => 'ar',
            'ar-ae' => 'ar',
            'es' => 'es',
            'de' => 'de',
        ];

        return $map[strtolower($raw)] ?? 'id';
    }

    /**
     * Normalize Dual-Persona UX mode.
     */
    public static function normalizePersonaMode(?string $mode): string
    {
        $upper = strtoupper(trim((string) ($mode ?: '')));

        return in_array($upper, [self::PERSONA_BUSINESS_WIZARD, 'AWAM', 'BUSINESS', 'FOUNDER', 'STORYBOARD'], true)
            ? self::PERSONA_BUSINESS_WIZARD
            : self::PERSONA_ARCHITECT_PRO;
    }

    /**
     * Navigation Shell Options selectable alongside the 36 Sector Visual UI Variants.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function navigationShellOptions(): array
    {
        return [
            'SIDEBAR_ERP_DENSE' => [
                'code' => 'SIDEBAR_ERP_DENSE',
                'name' => [
                    'id' => 'Sidebar Kiri Kolapsibel + Sticky Top Command Bar',
                    'en' => 'Collapsible Left ERP Sidebar + Sticky Command Bar',
                ],
                'viewport_css' => 'grid-cols-12 min-h-screen',
                'best_for' => ['Backoffice', 'ERP', 'Keuangan', 'HRIS', 'Yayasan'],
            ],
            'FULLSCREEN_OPERATIONAL_KIOSK' => [
                'code' => 'FULLSCREEN_OPERATIONAL_KIOSK',
                'name' => [
                    'id' => 'Layar Penuh Operasional 100vh Bebas Sidebar (Kasir / KDS / Gate)',
                    'en' => '100vh Zero-Sidebar Operational Terminal (POS / KDS / Gate)',
                ],
                'viewport_css' => 'h-screen overflow-hidden grid-cols-12',
                'best_for' => ['Kasir POS', 'Dapur KDS', 'Pabrik Shop Floor', 'Gate Tiket'],
            ],
            'DUAL_PANE_SPLIT_WORKBENCH' => [
                'code' => 'DUAL_PANE_SPLIT_WORKBENCH',
                'name' => [
                    'id' => 'Kokpit Multi-Panel Split-Screen + Slide-Over Drawer Kanan',
                    'en' => 'Multi-Pane Split Workbench + Right Slide-Over Drawer',
                ],
                'viewport_css' => 'grid-cols-12 h-screen overflow-hidden',
                'best_for' => ['Klinik EMR', 'Kalender Booking Gantt', 'Dispatcher Armada'],
            ],
            'MOBILE_FIRST_THUMB_PWA' => [
                'code' => 'MOBILE_FIRST_THUMB_PWA',
                'name' => [
                    'id' => 'PWA Mobile-First + Bottom Action Bar Satu Jempol + Desktop Admin',
                    'en' => 'Mobile-First 1-Thumb Bottom Bar PWA + Desktop Admin',
                ],
                'viewport_css' => 'max-w-md mx-auto pb-20 lg:max-w-7xl',
                'best_for' => ['Kurir PoD', 'QR Meja Resto', 'Absensi ESS', 'Relawan Lapangan'],
            ],
            'TOPBAR_EXECUTIVE_DECK' => [
                'code' => 'TOPBAR_EXECUTIVE_DECK',
                'name' => [
                    'id' => 'Topbar Eksekutif Bersih Tanpa Sidebar (Portal Klien / CFO / Donatur)',
                    'en' => 'Clean Executive Topbar Zero-Sidebar Deck (Client / CFO / Donor Portal)',
                ],
                'viewport_css' => 'max-w-7xl mx-auto px-4 sm:px-6 min-h-screen',
                'best_for' => ['CFO Treasury', 'Portal Klien White-Label', 'SIAKAD Wali', 'Portal Donatur Publik'],
            ],
        ];
    }

    /**
     * 36 Concrete Visual Sector Frontend UI/UX Blueprints (12 Functional Sectors x 3 Visual Variants).
     * Ensures clients visually pick and lock their exact frontend layout before a single line of code is written.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public static function allSectorVisualUiCatalog(): array
    {
        return [
            'SECTOR_01_COMMERCE_OMNICHANNEL' => [
                [
                    'code' => 'UI_01A_POS_SPLIT_TERMINAL',
                    'is_default' => true,
                    'default_shell' => 'FULLSCREEN_OPERATIONAL_KIOSK',
                    'badge' => 'SPEED CASHIER',
                    'name' => [
                        'id' => 'Opsi A — Terminal Kasir Split-Screen & Scan Barcode <30ms',
                        'en' => 'Option A — High-Velocity Split Cashier & <30ms Barcode Terminal',
                    ],
                    'summary' => [
                        'id' => 'Layar penuh tanpa sidebar: 8 kolom grid SKU & pencarian F1 di kiri, 4 kolom keranjang sticky + numpad sentuh & QRIS layar kedua di kanan.',
                        'en' => 'Zero-sidebar fullscreen: 8-col SKU grid & F1 barcode search on left, 4-col sticky cart + touch numpad & dual-screen QRIS on right.',
                    ],
                    'panes' => [
                        ['span' => 8, 'label' => '8-COL SKU & BARCODE', 'widgets' => ['[F1] Instant SKU/Barcode', '2D Size×Color Matrix', 'Live Atomic Stock Badge']],
                        ['span' => 4, 'label' => '4-COL STICKY CART', 'widgets' => ['Tiered Discount Lines', 'Touch Numpad', '[Enter] Pay & Thermal 80mm']],
                    ],
                ],
                [
                    'code' => 'UI_01B_OMNICHANNEL_TOUCH_KIOSK',
                    'is_default' => false,
                    'default_shell' => 'FULLSCREEN_OPERATIONAL_KIOSK',
                    'badge' => 'VISUAL RETAIL KIOSK',
                    'name' => [
                        'id' => 'Opsi B — Katalog Visual Touch Grid & Self-Checkout Kiosk',
                        'en' => 'Option B — Visual Touch Card Grid & Self-Checkout Kiosk',
                    ],
                    'summary' => [
                        'id' => 'Dirancang untuk butik, minimarket modern, & kiosk mandiri dengan tab kategori tegas, kartu foto produk kontras tinggi, dan checkout QRIS instan.',
                        'en' => 'Tailored for modern retail & self-checkout kiosks with sharp category tabs, high-contrast product cards, and instant QRIS checkout.',
                    ],
                    'panes' => [
                        ['span' => 2, 'label' => '2-COL CATEGORY RAIL', 'widgets' => ['Fast Category Rail', 'Promo Filter', 'Member Lookup']],
                        ['span' => 6, 'label' => '6-COL VISUAL CARDS', 'widgets' => ['3x3 Touch Product Cards', 'Quick Variant Drawer', 'Bundle Upsell']],
                        ['span' => 4, 'label' => '4-COL QRIS CHECKOUT', 'widgets' => ['Order Summary', 'Local Dynamic QRIS SVG', 'WhatsApp E-Receipt']],
                    ],
                ],
                [
                    'code' => 'UI_01C_B2B_WHOLESALE_MATRIX',
                    'is_default' => false,
                    'default_shell' => 'SIDEBAR_ERP_DENSE',
                    'badge' => 'B2B DISTRIBUTOR',
                    'name' => [
                        'id' => 'Opsi C — Matriks Grosir B2B, Multi-Gudang & Piutang Tempo',
                        'en' => 'Option C — B2B Wholesale Order Sheet & Multi-Warehouse ERP',
                    ],
                    'summary' => [
                        'id' => 'Tampilan tabel padat seperti spreadsheet untuk input ratusan karton/dus sekaligus, cek plafon kredit toko, dan alokasi stok lintas gudang.',
                        'en' => 'High-density spreadsheet order grid for bulk carton entry, distributor credit limit check, and multi-warehouse stock reservation.',
                    ],
                    'panes' => [
                        ['span' => 9, 'label' => '9-COL BULK ORDER GRID', 'widgets' => ['Dense SKU × Warehouse Qty', 'Tiered Carton Price', 'Credit Limit Bar']],
                        ['span' => 3, 'label' => '3-COL DO & INVOICE', 'widgets' => ['Surat Jalan Generator', 'TOP Tempo 30/60d', 'Maker-Checker Override']],
                    ],
                ],
            ],

            'SECTOR_02_HOSPITALITY_SPACE' => [
                [
                    'code' => 'UI_02A_HORIZONTAL_RESOURCE_GANTT',
                    'is_default' => true,
                    'default_shell' => 'DUAL_PANE_SPLIT_WORKBENCH',
                    'badge' => 'FRONTDESK GANTT',
                    'name' => [
                        'id' => 'Opsi A — Kalender Gantt Aset Horizontal & Slide-Over Booking',
                        'en' => 'Option A — Horizontal Resource Gantt & Instant Slide-Over',
                    ],
                    'summary' => [
                        'id' => 'Sumbu Y daftar lapangan/kamar/unit sticky di kiri, sumbu X jam 06:00–24:00 anti-bentrok di tengah, klik slot kosong langsung buka form kanan tanpa reload.',
                        'en' => 'Sticky Y-axis rooms/courts on left, 06:00–24:00 collision-free timeline in center, 0-reload right booking drawer on click.',
                    ],
                    'panes' => [
                        ['span' => 3, 'label' => '3-COL UNIT STATUS', 'widgets' => ['Court/Room Sticky List', 'CLEAN / IN-USE / MAINT', 'Quick Filter']],
                        ['span' => 6, 'label' => '6-COL TIME GANTT', 'widgets' => ['06:00-24:00 Slot Grid', '15m Hold Amber Pulse', 'Peak Rate Heatmap']],
                        ['span' => 3, 'label' => '3-COL WALK-IN DRAWER', 'widgets' => ['Exclusion Check Live', 'DP / Full QRIS', 'Turnstile QR Pass']],
                    ],
                ],
                [
                    'code' => 'UI_02B_VISUAL_COURT_FLOOR_MAP',
                    'is_default' => false,
                    'default_shell' => 'FULLSCREEN_OPERATIONAL_KIOSK',
                    'badge' => 'VENUE FLOOR MAP',
                    'name' => [
                        'id' => 'Opsi B — Peta Denah Visual Lapangan / Kamar & Timer Durasi',
                        'en' => 'Option B — Interactive Venue / Room Floor Map & Live Timer',
                    ],
                    'summary' => [
                        'id' => 'Denah visual 2D per gedung/lantai dengan hitung mundur sisa jam sewa, kontrol lampu/gate IoT, dan status housekeeping real-time.',
                        'en' => '2D visual venue floor map with live session countdowns, IoT relay/gate controls, and real-time housekeeping status.',
                    ],
                    'panes' => [
                        ['span' => 8, 'label' => '8-COL 2D VENUE MAP', 'widgets' => ['Visual Court/Villa Tiles', 'Live Remaining Timer', '1-Click Extend Slot']],
                        ['span' => 4, 'label' => '4-COL HOUSEKEEPING & POS', 'widgets' => ['Add-On Rental/Drink', 'Dirty -> Clean Queue', 'Shift Cash Summary']],
                    ],
                ],
                [
                    'code' => 'UI_02C_PUBLIC_BOOKING_CALENDAR',
                    'is_default' => false,
                    'default_shell' => 'MOBILE_FIRST_THUMB_PWA',
                    'badge' => 'GUEST SELF-BOOKING',
                    'name' => [
                        'id' => 'Opsi C — Portal Booking Pelanggan Mandiri & Paket Membership',
                        'en' => 'Option C — Guest Self-Booking Portal & Membership Pass',
                    ],
                    'summary' => [
                        'id' => 'Antarmuka cepat ramah HP bagi pelanggan memilih tanggal, jam kosong, kuota member bulanan, dan bayar QRIS otomatis dapat tiket QR gate.',
                        'en' => 'Mobile-first guest booking portal with instant slot picker, recurring member quota deduction, and automated QR gate pass.',
                    ],
                    'panes' => [
                        ['span' => 7, 'label' => '7-COL SLOT PICKER', 'widgets' => ['Date & Court Selector', 'Peak/Off-Peak Price Tag', 'Member Quota Wallet']],
                        ['span' => 5, 'label' => '5-COL INSTANT PASS', 'widgets' => ['15-Min Lock Timer', 'Midtrans QRIS/VA', 'Local SVG Gate QR']],
                    ],
                ],
            ],

            'SECTOR_03_HEALTHCARE_CLINICAL' => [
                [
                    'code' => 'UI_03A_CLINICAL_3_PANE_COCKPIT',
                    'is_default' => true,
                    'default_shell' => 'DUAL_PANE_SPLIT_WORKBENCH',
                    'badge' => 'ZERO-MODAL EMR',
                    'name' => [
                        'id' => 'Opsi A — Kokpit Dokter 3-Panel Bebas Modal (S-O-A-P + E-Resep)',
                        'en' => 'Option A — Zero-Modal 3-Pane Doctor S-O-A-P & FEFO Rx Cockpit',
                    ],
                    'summary' => [
                        'id' => 'Dokter melihat antrean & banner alergi obat merah pekat di kiri, mengetik S-O-A-P di tengah, dan meracik E-Resep stok apotek FEFO di kanan sekaligus.',
                        'en' => 'Doctors view queue & rose allergy banner on left, type S-O-A-P in center, and build FEFO pharmacy e-prescriptions on right simultaneously.',
                    ],
                    'panes' => [
                        ['span' => 2, 'label' => '2-COL ALLERGY & QUEUE', 'widgets' => ['STICKY ALLERGY ALERT', 'Poly Queue List', 'Vital Signs Summary']],
                        ['span' => 6, 'label' => '6-COL S-O-A-P & HISTORY', 'widgets' => ['Structured S-O-A-P', 'ICD-10 Autocomplete', 'Immutable Timeline']],
                        ['span' => 4, 'label' => '4-COL FEFO E-RX & LAB', 'widgets' => ['FEFO Batch Stock Picker', 'Dosage Builder', 'Sign & Send Pharmacy']],
                    ],
                ],
                [
                    'code' => 'UI_03B_OUTPATIENT_POLY_QUEUE_TV',
                    'is_default' => false,
                    'default_shell' => 'SIDEBAR_ERP_DENSE',
                    'badge' => 'ADMISSION & TRIAGE',
                    'name' => [
                        'id' => 'Opsi B — Admisi Pendaftaran, Triase Perawat & Layar TV Antrean',
                        'en' => 'Option B — Registration Desk, Nurse Triage & Live Queue Display',
                    ],
                    'summary' => [
                        'id' => 'Fokus kecepatan admisi NIK/BPJS, pemeriksaan tanda vital awal perawat, dan sinkronisasi layar panggil poli via WebSocket tanpa kedip.',
                        'en' => 'Fast NIK/insurance check-in, nurse vital-sign intake, and zero-flicker WebSocket waiting room queue display.',
                    ],
                    'panes' => [
                        ['span' => 5, 'label' => '5-COL FAST ADMISSION', 'widgets' => ['NIK / MRN Instant Lookup', 'BPJS / Insurance Check', 'Poly Ticket Thermal']],
                        ['span' => 7, 'label' => '7-COL TRIAGE & TV SYNC', 'widgets' => ['BP / SpO2 / Temp Intake', 'Doctor Room Load', 'WebSocket TV Caller']],
                    ],
                ],
                [
                    'code' => 'UI_03C_PHARMACY_DISPENSING_BENCH',
                    'is_default' => false,
                    'default_shell' => 'FULLSCREEN_OPERATIONAL_KIOSK',
                    'badge' => 'PHARMACY & BILLING',
                    'name' => [
                        'id' => 'Opsi C — Meja Racik Apotek FEFO, Etiket Barcode & Kasir Klinik',
                        'en' => 'Option C — Pharmacy FEFO Dispensing Bench & Unified Billing',
                    ],
                    'summary' => [
                        'id' => 'Layar farmasi antrean resep masuk real-time, penguncian batch obat mendekati kadaluarsa (FEFO), cetak etiket aturan pakai, dan kasir terpadu.',
                        'en' => 'Real-time incoming e-prescription queue, strict FEFO batch allocation, thermal dosage label print, and unified clinic cashier.',
                    ],
                    'panes' => [
                        ['span' => 4, 'label' => '4-COL INCOMING E-RX', 'widgets' => ['Live Signed Rx Queue', 'Allergy Cross-Check', 'Compounding Flag']],
                        ['span' => 5, 'label' => '5-COL FEFO BATCH ALLOC', 'widgets' => ['Auto-Pick Nearest Expiry', 'Substitute Generic', 'Print Dosage Label']],
                        ['span' => 3, 'label' => '3-COL CLINIC BILLING', 'widgets' => ['Consult + Lab + Drugs', 'Insurance Split', 'QRIS / Official A4']],
                    ],
                ],
            ],

            'SECTOR_04_FINTECH_LEDGER' => [
                [
                    'code' => 'UI_04A_SPREADSHEET_LEDGER_GRID',
                    'is_default' => true,
                    'default_shell' => 'SIDEBAR_ERP_DENSE',
                    'badge' => 'PRECISION LEDGER',
                    'name' => [
                        'id' => 'Opsi A — Grid Jurnal Buku Besar Padat & Pengunci Balance Otomatis',
                        'en' => 'Option A — High-Density Double-Entry Grid & Balance Gatekeeper',
                    ],
                    'summary' => [
                        'id' => 'Tabel jurnal padat tabular-nums DECIMAL(19,4) dengan baris total bawah sticky: tombol Post terkunci mutlak jika selisih Debit vs Kredit != 0.',
                        'en' => 'Compact tabular-nums DECIMAL(19,4) journal grid with sticky sum footer: Post button stays locked whenever Debit != Credit.',
                    ],
                    'panes' => [
                        ['span' => 8, 'label' => '8-COL COA JOURNAL GRID', 'widgets' => ['Compact py-1.5 CoA Rows', 'Cost Center Tag', 'Sticky SUM(Dr)==SUM(Cr)']],
                        ['span' => 4, 'label' => '4-COL AUDIT & PERIOD', 'widgets' => ['Fiscal Period Lock Badge', 'Reversal Voucher Action', 'Trial Balance Live']],
                    ],
                ],
                [
                    'code' => 'UI_04B_DUAL_BANK_RECONCILIATION',
                    'is_default' => false,
                    'default_shell' => 'DUAL_PANE_SPLIT_WORKBENCH',
                    'badge' => 'BANK RECONCILIATION',
                    'name' => [
                        'id' => 'Opsi B — Meja Rekonsiliasi Mutasi Bank Bersebelahan (50/50 Split)',
                        'en' => 'Option B — Side-by-Side Bank Feed & Ledger Reconciliation',
                    ],
                    'summary' => [
                        'id' => 'Panel kiri mutasi rekening koran/webhook pembayaran, panel kanan jurnal sistem dengan skor kecocokan otomatis dan tombol Match 1-klik.',
                        'en' => 'Left pane bank/webhook statement lines, right pane internal ledger entries with confidence scoring and 1-click Match.',
                    ],
                    'panes' => [
                        ['span' => 6, 'label' => '6-COL BANK STATEMENT', 'widgets' => ['Staged CSV / Webhook Feed', 'Unreconciled Filter', 'Penny Drift 0.0000']],
                        ['span' => 6, 'label' => '6-COL SYSTEM LEDGER', 'widgets' => ['Auto-Match 99% Pair', '[Ctrl+M] Reconcile Selected', 'Variance Exception Log']],
                    ],
                ],
                [
                    'code' => 'UI_04C_CFO_TREASURY_APPROVAL_DECK',
                    'is_default' => false,
                    'default_shell' => 'TOPBAR_EXECUTIVE_DECK',
                    'badge' => 'CFO TREASURY DECK',
                    'name' => [
                        'id' => 'Opsi C — Kokpit Kas Eksekutif CFO & Persetujuan Pencairan Berjenjang',
                        'en' => 'Option C — CFO Cashflow Command Deck & Maker-Checker Payouts',
                    ],
                    'summary' => [
                        'id' => 'Dirancang untuk Direktur Keuangan: posisi kas multi-bank real-time, umur piutang AR/AP, dan antrean persetujuan pencairan dana 4-mata.',
                        'en' => 'Built for CFOs: real-time multi-bank liquidity, AR/AP aging buckets, and 4-eyes Maker-Checker disbursement approval queue.',
                    ],
                    'panes' => [
                        ['span' => 7, 'label' => '7-COL LIQUIDITY & AGING', 'widgets' => ['Multi-Bank Cash Position', 'AR/AP 30-60-90d Aging', 'P&L Snapshot']],
                        ['span' => 5, 'label' => '5-COL MAKER-CHECKER', 'widgets' => ['Pending Payout Queue', 'Supporting Invoice PDF', 'Approve / Reject + Hash']],
                    ],
                ],
            ],

            'SECTOR_05_INDUSTRIAL_MRP' => [
                [
                    'code' => 'UI_05A_MULTI_LEVEL_BOM_TREE',
                    'is_default' => true,
                    'default_shell' => 'SIDEBAR_ERP_DENSE',
                    'badge' => 'BOM & HPP ROLL-UP',
                    'name' => [
                        'id' => 'Opsi A — Pohon BoM Bertingkat & Kalkulator HPP Produksi Presisi',
                        'en' => 'Option A — Multi-Level BoM Tree & Live Unit HPP Roll-Up',
                    ],
                    'summary' => [
                        'id' => 'Tabel pohon lipat (Barang Jadi -> WIP -> Bahan Baku) lengkap simulasi persentase scrap/susut dan akumulasi HPP per unit secara instan.',
                        'en' => 'Collapsible BoM tree (Finished Good -> Sub-Assembly -> Raw Material) with scrap/yield simulation and live unit COGS roll-up.',
                    ],
                    'panes' => [
                        ['span' => 7, 'label' => '7-COL BOM COST TREE', 'widgets' => ['Expandable BoM Hierarchy', 'Yield & Scrap Tolerance %', 'Live Unit HPP Roll-Up']],
                        ['span' => 5, 'label' => '5-COL MATERIAL CHECK', 'widgets' => ['Raw Stock Availability', 'Shortage Purchase Trigger', 'Release Work Order']],
                    ],
                ],
                [
                    'code' => 'UI_05B_GLOVE_TOUCH_SHOP_FLOOR',
                    'is_default' => false,
                    'default_shell' => 'FULLSCREEN_OPERATIONAL_KIOSK',
                    'badge' => 'GLOVE-FRIENDLY KIOSK',
                    'name' => [
                        'id' => 'Opsi B — Layar Tablet Lantai Pabrik Kontras Ekstrem (Tombol h-14)',
                        'en' => 'Option B — High-Contrast Shop Floor Operator Terminal (h-14 Touch)',
                    ],
                    'summary' => [
                        'id' => 'Khusus tablet dinding pabrik & operator bersarung tangan: tombol raksasa h-14 Start Batch, Catat Reject QC, dan Cetak Label Palet.',
                        'en' => 'Designed for factory wall tablets & gloved operators: giant h-14 Start Batch, Log QC Scrap, and Pallet Barcode Label buttons.',
                    ],
                    'panes' => [
                        ['span' => 5, 'label' => '5-COL ACTIVE WORK ORDER', 'widgets' => ['Batch Target vs Actual', 'Barcode Lot Raw Scan', 'Machine Timer']],
                        ['span' => 7, 'label' => '7-COL GIANT TOUCH PAD', 'widgets' => ['[START / PAUSE h-14]', '[LOG SCRAP + REASON]', '[FINISH & PRINT LOT QR]']],
                    ],
                ],
                [
                    'code' => 'UI_05C_WORK_CENTER_CAPACITY_BOARD',
                    'is_default' => false,
                    'default_shell' => 'DUAL_PANE_SPLIT_WORKBENCH',
                    'badge' => 'PPIC & QC KANBAN',
                    'name' => [
                        'id' => 'Opsi C — Papan Jadwal Mesin PPIC & Inspeksi Mutu (QC Gate)',
                        'en' => 'Option C — PPIC Machine Capacity Board & QC Inspection Gate',
                    ],
                    'summary' => [
                        'id' => 'Memantau beban jam mesin produksi, antrean Work Order lintas lini, dan gerbang lulus/tahan Quality Control sebelum masuk gudang barang jadi.',
                        'en' => 'Tracks work-center utilization bars, cross-line Work Order Kanban, and mandatory QC Pass/Quarantine gate before FG stock-in.',
                    ],
                    'panes' => [
                        ['span' => 8, 'label' => '8-COL WORK CENTER LANES', 'widgets' => ['Machine Load % Bar', 'Planned -> WIP -> QC Hold', 'Bottleneck Alert']],
                        ['span' => 4, 'label' => '4-COL QC RELEASE GATE', 'widgets' => ['Sample Checklist', 'Quarantine Lot Lock', 'Backflush to FG Warehouse']],
                    ],
                ],
            ],

            'SECTOR_06_LOGISTICS_FLEET' => [
                [
                    'code' => 'UI_06A_SPLIT_MAP_DISPATCHER',
                    'is_default' => true,
                    'default_shell' => 'DUAL_PANE_SPLIT_WORKBENCH',
                    'badge' => 'FLEET DISPATCHER',
                    'name' => [
                        'id' => 'Opsi A — Kokpit Dispatcher Muatan Truk & Pelacakan Manifes 50/50',
                        'en' => 'Option A — 50/50 Fleet Load Capacity & Manifest Dispatcher',
                    ],
                    'summary' => [
                        'id' => 'Sisi kiri daftar resi & bar kapasitas tonase/kubikasi armada; sisi kanan status milestone pengantaran real-time & rekonsiliasi setoran COD.',
                        'en' => 'Left pane unassigned waybills & vehicle kg/CBM load bars; right pane live delivery milestones & COD cash liability counter.',
                    ],
                    'panes' => [
                        ['span' => 6, 'label' => '6-COL LOAD & MANIFEST', 'widgets' => ['Truck Kg & CBM Load Bar', 'Batch Waybill Scanner', 'Print Surat Jalan QR']],
                        ['span' => 6, 'label' => '6-COL LIVE MILESTONES', 'widgets' => ['PICKED -> HUB -> DELIVERED', 'Failed Attempt SLA Alert', 'COD Cash Handover']],
                    ],
                ],
                [
                    'code' => 'UI_06B_COURIER_THUMB_POD_PWA',
                    'is_default' => false,
                    'default_shell' => 'MOBILE_FIRST_THUMB_PWA',
                    'badge' => 'COURIER 1-THUMB PWA',
                    'name' => [
                        'id' => 'Opsi B — Aplikasi PWA Kurir Lapangan 1-Jempol & Bukti Foto/TTD PoD',
                        'en' => 'Option B — 1-Thumb Courier Mobile PWA + Photo & Signature PoD',
                    ],
                    'summary' => [
                        'id' => 'Bebas tabel desktop sempit: kartu urutan titik antar dengan tombol jempol bawah untuk Scan Paket, Foto Kamera, Tanda Tangan Kanvas, & WA Penerima.',
                        'en' => 'Zero cramped desktop tables: route-ordered stop cards with bottom thumb bar for Scan, Camera PoD, Canvas Signature, & Recipient WA.',
                    ],
                    'panes' => [
                        ['span' => 5, 'label' => '5-COL MOBILE STOP CARDS', 'widgets' => ['Ordered Stop Queue', 'COD Collect Badge', ' Sticky Thumb Action Bar']],
                        ['span' => 7, 'label' => '7-COL HQ POD VERIFIER', 'widgets' => ['Geo-Timestamp Photo Grid', 'Digital Signature Preview', 'Driver Trip Settlement']],
                    ],
                ],
                [
                    'code' => 'UI_06C_WAREHOUSE_CROSSDOCK_SCANNER',
                    'is_default' => false,
                    'default_shell' => 'FULLSCREEN_OPERATIONAL_KIOSK',
                    'badge' => 'HUB SORTATION TERMINAL',
                    'name' => [
                        'id' => 'Opsi C — Terminal Sortir Gudang Hub Inbound/Outbound Kecepatan Tinggi',
                        'en' => 'Option C — High-Speed Hub Sortation & Cross-Docking Scanner',
                    ],
                    'summary' => [
                        'id' => 'Layar kontras tinggi untuk meja sortir gudang transit: bunyi/visual instan saat scan karung/resi, deteksi salah rute, dan cetak label bagasi.',
                        'en' => 'High-contrast sortation bench terminal: instant visual/audio feedback on bag/waybill scan, misroute guard, and master bag seal print.',
                    ],
                    'panes' => [
                        ['span' => 7, 'label' => '7-COL RAPID SCAN FEED', 'widgets' => ['<30ms Wedge Barcode Input', 'Destination Gate Big Code', 'Misroute Red Banner']],
                        ['span' => 5, 'label' => '5-COL BAG & DOCK SEAL', 'widgets' => ['Master Bag Counter', 'Seal & Weigh Manifest', 'Print Thermal Bag Tag']],
                    ],
                ],
            ],

            'SECTOR_07_EDTECH_MEMBERSHIP' => [
                [
                    'code' => 'UI_07A_COHORT_GRADEBOOK_MATRIX',
                    'is_default' => true,
                    'default_shell' => 'SIDEBAR_ERP_DENSE',
                    'badge' => 'GRADEBOOK & PRESENSI',
                    'name' => [
                        'id' => 'Opsi A — Matriks Buku Nilai 2D & Presensi 1-Klik (H/I/S/A)',
                        'en' => 'Option A — 2D Arrow-Key Gradebook & 1-Click H/I/S/A Matrix',
                    ],
                    'summary' => [
                        'id' => 'Pengajar menginput nilai & absensi 40 siswa × 16 pertemuan langsung di satu grid navigasi tombol panah tanpa membuka 40 popup terpisah.',
                        'en' => 'Instructors grade & mark attendance for 40 students × 16 sessions inline via arrow-key navigation with zero modal fatigue.',
                    ],
                    'panes' => [
                        ['span' => 9, 'label' => '9-COL 2D COHORT MATRIX', 'widgets' => ['Student × Session 1..16', '1-Key H / I / S / A Toggle', 'Weighted Final Grade']],
                        ['span' => 3, 'label' => '3-COL RUBRIC & EXPORT', 'widgets' => ['Competency Weighting', 'Remedial Flag', 'Official Transcript A4']],
                    ],
                ],
                [
                    'code' => 'UI_07B_DISTRACTION_FREE_EXAM_PLAYER',
                    'is_default' => false,
                    'default_shell' => 'FULLSCREEN_OPERATIONAL_KIOSK',
                    'badge' => 'LMS & CBT PLAYER',
                    'name' => [
                        'id' => 'Opsi B — Ruang Belajar Bebas Gangguan & Ujian CBT Timer Server',
                        'en' => 'Option B — Distraction-Free Course Player & Server-Timed CBT',
                    ],
                    'summary' => [
                        'id' => 'Menyembunyikan menu bising saat belajar/ujian: pohon modul lipat di kiri, materi/soal fokus di tengah, dan navigasi nomor soal + timer anti-curang di kanan.',
                        'en' => 'Hides global chrome during study/exams: collapsible syllabus on left, focused lesson/question in center, server-synced timer & question map on right.',
                    ],
                    'panes' => [
                        ['span' => 3, 'label' => '3-COL SYLLABUS TREE', 'widgets' => ['Prerequisite Lock', 'Module Progress %', 'Download Handout']],
                        ['span' => 6, 'label' => '6-COL FOCUS CONTENT', 'widgets' => ['Clean Reading / Video', 'Auto-Saved Quiz Answer', 'Code/Essay Box']],
                        ['span' => 3, 'label' => '3-COL CBT TIMER MAP', 'widgets' => ['Server-Synced Countdown', '1..50 Question Grid', 'Issue QR Certificate']],
                    ],
                ],
                [
                    'code' => 'UI_07C_CAMPUS_SIAKAD_FINANCE_PORTAL',
                    'is_default' => false,
                    'default_shell' => 'TOPBAR_EXECUTIVE_DECK',
                    'badge' => 'SIAKAD & SPP PORTAL',
                    'name' => [
                        'id' => 'Opsi C — Portal SIAKAD Wali Murid/Mahasiswa, KRS & Tagihan SPP',
                        'en' => 'Option C — Student/Parent SIAKAD, Course Enrollment & Tuition Hub',
                    ],
                    'summary' => [
                        'id' => 'Satu pintu bagi siswa & orang tua memantau jadwal kelas bentrok, presensi harian, rapor kompetensi, dan pelunasan SPP Virtual Account/QRIS.',
                        'en' => 'Unified student & parent portal for clash-free schedule, daily attendance feed, competency report cards, and VA/QRIS tuition billing.',
                    ],
                    'panes' => [
                        ['span' => 7, 'label' => '7-COL ACADEMIC & KRS', 'widgets' => ['Clash-Free Class Timetable', 'Attendance & GPA Summary', 'Digital Report PDF']],
                        ['span' => 5, 'label' => '5-COL TUITION & GRANT', 'widgets' => ['SPP / UKT Installments', 'Scholarship Subsidy Tag', 'Instant VA / QRIS Pay']],
                    ],
                ],
            ],

            'SECTOR_08_PROFESSIONAL_PSA' => [
                [
                    'code' => 'UI_08A_MILESTONE_KANBAN_TIMER',
                    'is_default' => true,
                    'default_shell' => 'SIDEBAR_ERP_DENSE',
                    'badge' => 'AGENCY PSA & TIMER',
                    'name' => [
                        'id' => 'Opsi A — Papan Milestone Proyek + Stopwatch Jam Kerja Melayang',
                        'en' => 'Option A — Deliverable Kanban + Persistent Billable Stopwatch',
                    ],
                    'summary' => [
                        'id' => 'Dilengkapi bar stopwatch jam kerja billable yang tetap hidup saat berpindah halaman dan konversi 1-klik timesheet menjadi invoice termin.',
                        'en' => 'Features a persistent billable timer bar surviving page navigation and 1-click conversion from approved timesheets into milestone invoices.',
                    ],
                    'panes' => [
                        ['span' => 8, 'label' => '8-COL SPRINT & KANBAN', 'widgets' => ['Persistent Top Timer Bar', 'Milestone Deliverable Lanes', 'Scope Hash Guard']],
                        ['span' => 4, 'label' => '4-COL WIP & INVOICING', 'widgets' => ['Billable vs Cap Hours', '1-Click Timesheet -> Inv', 'Addendum CR Calculator']],
                    ],
                ],
                [
                    'code' => 'UI_08B_CLIENT_PORTAL_SCOPE_SEAL',
                    'is_default' => false,
                    'default_shell' => 'TOPBAR_EXECUTIVE_DECK',
                    'badge' => 'WHITE-LABEL CLIENT PORTAL',
                    'name' => [
                        'id' => 'Opsi B — Portal Persetujuan Klien White-Label, UAT & BAST Digital',
                        'en' => 'Option B — White-Label Client Approval, UAT & Digital BAST Portal',
                    ],
                    'summary' => [
                        'id' => 'Portal bersih berlogo agensi Anda tanpa membocorkan tarif internal tim: klien memantau progres 7 milestone, mencentang UAT, dan tanda tangan BAST.',
                        'en' => 'Clean white-labeled client portal hiding internal staff cost rates: clients track 7 milestones, check UAT items, and sign Digital BAST.',
                    ],
                    'panes' => [
                        ['span' => 7, 'label' => '7-COL MILESTONE & UAT', 'widgets' => ['7-Phase Progress Bar', 'Interactive UAT Checklist', 'Live Staging Preview Link']],
                        ['span' => 5, 'label' => '5-COL SPK, BAST & TERMIN', 'widgets' => ['SHA-256 Scope Seal', 'Sign Digital BAST', 'Escrow Termin Payment']],
                    ],
                ],
                [
                    'code' => 'UI_08C_RETAINER_RESOURCE_UTILIZATION',
                    'is_default' => false,
                    'default_shell' => 'SIDEBAR_ERP_DENSE',
                    'badge' => 'LAW / CONSULTING FIRM',
                    'name' => [
                        'id' => 'Opsi C — Matriks Beban Konsultan, Retainer Saldo Jam & Dokumen Perkara',
                        'en' => 'Option C — Partner Utilization Matrix, Retainer Burn & Matter Vault',
                    ],
                    'summary' => [
                        'id' => 'Ideal untuk kantor hukum, KAP, & konsultan manajemen: pantau sisa kuota jam retainer klien, brankas dokumen rahasia bertanda waktu, & margin per proyek.',
                        'en' => 'Built for law, audit, & consulting firms: track client retainer burn-down, private signed-URL document vault, and project gross margin.',
                    ],
                    'panes' => [
                        ['span' => 6, 'label' => '6-COL RETAINER & MARGIN', 'widgets' => ['Retainer Quota Burn Bar', 'Consultant Allocation %', 'Project Margin Table']],
                        ['span' => 6, 'label' => '6-COL MATTER VAULT', 'widgets' => ['5-Min Signed URL Docs', 'Versioned Legal Drafts', 'Audit Trail SHA-256']],
                    ],
                ],
            ],

            'SECTOR_09_FOOD_BEVERAGE' => [
                [
                    'code' => 'UI_09A_DARK_KDS_AND_MODIFIER_POS',
                    'is_default' => true,
                    'default_shell' => 'FULLSCREEN_OPERATIONAL_KIOSK',
                    'badge' => 'DARK KDS + FAST POS',
                    'name' => [
                        'id' => 'Opsi A — Kasir Modifier Cepat (Gula/Es/Topping) & Layar Dapur Dark KDS',
                        'en' => 'Option A — 1-Tap Modifier Cashier & Forced-Dark Kitchen KDS',
                    ],
                    'summary' => [
                        'id' => 'Kasir pilih level gula/es/topping dalam 1 sentuhan tanpa dropdown lambat; tiket otomatis muncul di layar dapur Dark Mode dengan warna SLA menit.',
                        'en' => '1-tap sugar/ice/topping modifier grid with zero slow dropdowns; instant WebSocket push to forced-dark Kitchen Display with SLA timers.',
                    ],
                    'panes' => [
                        ['span' => 6, 'label' => '6-COL 1-TAP MODIFIER POS', 'widgets' => ['Menu + Instant Sugar/Ice', 'Dine-In / Takeaway / Ojol', 'Thermal Checker + Bill']],
                        ['span' => 6, 'label' => '6-COL FORCED-DARK KDS', 'widgets' => ['<5m Emerald | >10m Rose', '[Space] Bump Ready Ticket', 'Recipe Auto-Backflush']],
                    ],
                ],
                [
                    'code' => 'UI_09B_INTERACTIVE_DINEIN_TABLE_MAP',
                    'is_default' => false,
                    'default_shell' => 'DUAL_PANE_SPLIT_WORKBENCH',
                    'badge' => 'DINE-IN TABLE & SPLIT BILL',
                    'name' => [
                        'id' => 'Opsi B — Peta Meja Restoran (Indoor/VIP/Outdoor) & Pisah/Gabung Bill',
                        'en' => 'Option B — Dine-In Floor Table Map & Split/Merge Bill Workbench',
                    ],
                    'summary' => [
                        'id' => 'Untuk resto keluarga, beach club, & bistro: pantau meja terisi, pindah meja, pisah bayar per kursi/item, serta kalkulasi PB1 10% + Service Charge presisi.',
                        'en' => 'For full-service restaurants: live floor table occupancy, transfer/merge tables, split bill by item/guest, and exact service charge + tax.',
                    ],
                    'panes' => [
                        ['span' => 7, 'label' => '7-COL FLOOR TABLE ZONES', 'widgets' => ['Indoor / Outdoor / VIP Map', 'Seated Pax & Open Tab', 'Transfer / Merge Table']],
                        ['span' => 5, 'label' => '5-COL SPLIT BILL & TAX', 'widgets' => ['Split by Item or Equal', 'Service 5% + PB1 10%', 'QRIS / EDC Settlement']],
                    ],
                ],
                [
                    'code' => 'UI_09C_QR_TABLE_SELF_ORDER_MENU',
                    'is_default' => false,
                    'default_shell' => 'MOBILE_FIRST_THUMB_PWA',
                    'badge' => 'SCAN QR MEJA + CLOUD KITCHEN',
                    'name' => [
                        'id' => 'Opsi C — Menu Pesan Mandiri QR Meja (Tanpa Install App) & Multi-Outlet',
                        'en' => 'Option C — Zero-App Table QR Self-Ordering & Cloud Kitchen Hub',
                    ],
                    'summary' => [
                        'id' => 'Tamu scan QR unik ber-signature di meja, pilih menu & catatan alergi lewat HP, bayar QRIS di muka, pesanan langsung masuk KDS dapur.',
                        'en' => 'Guests scan signed table QR, pick dishes & modifiers on mobile, pay upfront via QRIS, and route orders straight to kitchen stations.',
                    ],
                    'panes' => [
                        ['span' => 6, 'label' => '6-COL GUEST QR MENU', 'widgets' => ['Signed Table Token Guard', 'Fast Photo Menu & Notes', 'Pay Before Kitchen Push']],
                        ['span' => 6, 'label' => '6-COL MULTI-STATION HUB', 'widgets' => ['Bar vs Hot Kitchen Split', '86 Sold-Out Instant Sync', 'Outlet Recipe Variance']],
                    ],
                ],
            ],

            'SECTOR_10_MEDIA_TICKETING' => [
                [
                    'code' => 'UI_10A_INTERACTIVE_SEAT_MAP_TTL',
                    'is_default' => true,
                    'default_shell' => 'DUAL_PANE_SPLIT_WORKBENCH',
                    'badge' => 'SEAT MAP + 10M TTL LOCK',
                    'name' => [
                        'id' => 'Opsi A — Peta Kursi Konser/Venue Interaktif & Hitung Mundur Kunci 10:00',
                        'en' => 'Option A — Interactive Venue Seat Map + Sticky 10:00 Hold Countdown',
                    ],
                    'summary' => [
                        'id' => 'Mencegah tiket ganda saat war tiket: peta kursi VIP/CAT interaktif dengan banner hitung mundur 10 menit tegas sebelum kursi dilepas otomatis.',
                        'en' => 'Prevents overselling during flash drops: interactive VIP/CAT seat map with sticky 10:00 hold countdown before atomic seat release.',
                    ],
                    'panes' => [
                        ['span' => 8, 'label' => '8-COL VENUE SEAT GRID', 'widgets' => ['VIP / CAT1 / Festival Map', 'Atomic Seat Hold Badge', 'Sticky 10:00 TTL Banner']],
                        ['span' => 4, 'label' => '4-COL FLASH CHECKOUT', 'widgets' => ['Attendee NIK Guard', 'Queue Fair-Play Token', 'Signed Dynamic QR Ticket']],
                    ],
                ],
                [
                    'code' => 'UI_10B_GATE_WRISTBAND_SCANNER',
                    'is_default' => false,
                    'default_shell' => 'FULLSCREEN_OPERATIONAL_KIOSK',
                    'badge' => 'GATE CHECK-IN <150MS',
                    'name' => [
                        'id' => 'Opsi B — Layar Scanner Gate Masuk Event & Anti-Tiket Ganda <150ms',
                        'en' => 'Option B — <150ms Gate QR/RFID Scanner & Anti-Passback Kiosk',
                    ],
                    'summary' => [
                        'id' => 'Layar kontras tinggi petugas pintu masuk: panel berubah HIJAU PENUH saat tiket sah atau MERAH PEKAT saat tiket duplikat/sudah dipakai.',
                        'en' => 'High-contrast gate crew terminal: flashes SOLID EMERALD on valid entry or SOLID ROSE with timestamp when duplicate passback is attempted.',
                    ],
                    'panes' => [
                        ['span' => 7, 'label' => '7-COL GATE VERIFY FLASH', 'widgets' => ['<30ms Scanner / Camera', 'VALID vs USED AT 19:04', 'Offline Encrypted Cache']],
                        ['span' => 5, 'label' => '5-COL ZONE CAPACITY', 'widgets' => ['Gate A/B/VIP Live Count', 'Wristband Top-Up Sync', 'Turnstile Relay Trigger']],
                    ],
                ],
                [
                    'code' => 'UI_10C_CREATOR_DAM_ROYALTY_DESK',
                    'is_default' => false,
                    'default_shell' => 'SIDEBAR_ERP_DENSE',
                    'badge' => 'CREATOR DAM & ROYALTY',
                    'name' => [
                        'id' => 'Opsi C — Galeri Lisensi Aset Digital (DAM) & Buku Besar Bagi Hasil Royalti',
                        'en' => 'Option C — Digital Asset Licensing Grid & Creator Royalty Split Ledger',
                    ],
                    'summary' => [
                        'id' => 'Untuk agensi media, stok foto/musik, & platform kreator: preview ber-watermark, unduhan Signed URL 5 menit, dan pembagian royalti otomatis.',
                        'en' => 'For media & creator platforms: watermarked asset previews, 5-min signed URL delivery, and transparent Gross/Fee/Net creator splits.',
                    ],
                    'panes' => [
                        ['span' => 7, 'label' => '7-COL DAM ASSET GRID', 'widgets' => ['Watermarked Preview', 'Commercial License Tier', '5-Min Signed Download']],
                        ['span' => 5, 'label' => '5-COL ROYALTY LEDGER', 'widgets' => ['70:30 Split Calculator', 'Withholding Tax Ledger', 'Creator Payout Batch']],
                    ],
                ],
            ],

            'SECTOR_11_HRIS_INTERNAL_OPS' => [
                [
                    'code' => 'UI_11A_SHIFT_ROSTER_CLASH_MATRIX',
                    'is_default' => true,
                    'default_shell' => 'SIDEBAR_ERP_DENSE',
                    'badge' => 'ROSTER & PAYROLL PPH21',
                    'name' => [
                        'id' => 'Opsi A — Matriks Roster Shift 2D + Peringatan Bentrok & Simulasi Gaji',
                        'en' => 'Option A — 2D Shift Roster Clash Matrix & 3-Col Payroll Simulator',
                    ],
                    'summary' => [
                        'id' => 'HR menyusun jadwal shift ratusan staf di grid Karyawan × Tanggal (otomatis merah jika Shift Malam langsung Shift Pagi) + 3 kolom simulasi THP.',
                        'en' => 'HR schedules shifts in an Employee × Date grid (red alert on Night->Morning rest violations) + 3-column Gross/Tax/THP simulator.',
                    ],
                    'panes' => [
                        ['span' => 7, 'label' => '7-COL ROSTER MATRIX', 'widgets' => ['Employee × Date 1..31', 'Night->Morning Clash Guard', 'Copy Previous Week']],
                        ['span' => 5, 'label' => '5-COL 3-COL PAYSLIP SIM', 'widgets' => ['Gross | Tax/BPJS | THP', 'MoM >20% Delta Alert', 'Lock & Bank CSV Export']],
                    ],
                ],
                [
                    'code' => 'UI_11B_ESS_ATTENDANCE_APPROVAL_HUB',
                    'is_default' => false,
                    'default_shell' => 'MOBILE_FIRST_THUMB_PWA',
                    'badge' => 'MOBILE ESS + ZKTeco',
                    'name' => [
                        'id' => 'Opsi B — Portal Mandiri Karyawan (ESS): Absen Geofence, Cuti & Klaim',
                        'en' => 'Option B — Mobile Employee Self-Service (ESS): Clock-In, Leave & Claims',
                    ],
                    'summary' => [
                        'id' => 'Nyaman di HP karyawan sekaligus terhubung mesin jari ZKTeco kantor: absen selfie radius GPS, pengajuan cuti/lembur, dan unduh slip gaji PDF.',
                        'en' => 'Mobile-friendly ESS synced with office ZKTeco biometric logs: geofenced selfie clock-in, leave/overtime requests, and private payslip PDF.',
                    ],
                    'panes' => [
                        ['span' => 5, 'label' => '5-COL MOBILE ESS CARD', 'widgets' => ['GPS Radius + Selfie', 'Leave Quota Balance', 'Private Payslip Download']],
                        ['span' => 7, 'label' => '7-COL MANAGER INBOX', 'widgets' => ['ZKTeco ADMS Sync Feed', 'Overtime & Claim Queue', 'Department Cost Center']],
                    ],
                ],
                [
                    'code' => 'UI_11C_INTERNAL_ASSET_PROCUREMENT',
                    'is_default' => false,
                    'default_shell' => 'SIDEBAR_ERP_DENSE',
                    'badge' => 'GA ASSET & PR/PO',
                    'name' => [
                        'id' => 'Opsi C — Manajemen Aset Inventaris Kantor, PR/PO & Persetujuan Anggaran',
                        'en' => 'Option C — Internal IT/GA Asset Custody, PR/PO & Budget Approval',
                    ],
                    'summary' => [
                        'id' => 'Melacak serah terima laptop/kendaraan dinas ber-QR, pengajuan pembelian barang (PR -> PO -> GRN), dan kontrol plafon anggaran per divisi.',
                        'en' => 'Tracks QR-tagged company assets, Purchase Request -> PO -> Goods Receipt workflows, and departmental budget ceiling locks.',
                    ],
                    'panes' => [
                        ['span' => 7, 'label' => '7-COL PR -> PO -> GRN', 'widgets' => ['Division Budget Ceiling', '3-Vendor Quote Compare', 'Maker-Checker Approval']],
                        ['span' => 5, 'label' => '5-COL QR ASSET CUSTODY', 'widgets' => ['Employee Handover BAST', 'Depreciation Schedule', 'Maintenance Due Alert']],
                    ],
                ],
            ],

            'SECTOR_12_GOVTECH_NONPROFIT' => [
                [
                    'code' => 'UI_12A_ISAK35_RESTRICTED_FUND_SPLITTER',
                    'is_default' => true,
                    'default_shell' => 'SIDEBAR_ERP_DENSE',
                    'badge' => 'ISAK 35 / ASC 958 LEDGER',
                    'name' => [
                        'id' => 'Opsi A — Buku Besar Pemisah Dana Terikat vs Operasional (Yayasan / NGO)',
                        'en' => 'Option A — Restricted vs Unrestricted Fund Accounting Workbench',
                    ],
                    'summary' => [
                        'id' => 'Memisahkan secara mutlak saldo Donasi/Zakat/Hibah Terikat dari kas operasional yayasan lengkap persetujuan pencairan berjenjang & laporan audit.',
                        'en' => 'Strictly segregates Restricted Program Grants/Zakat from operational funds with multi-tier disbursement approval and audit-ready statements.',
                    ],
                    'panes' => [
                        ['span' => 7, 'label' => '7-COL FUND SEGREGATION', 'widgets' => ['Restricted vs Unrestricted', 'Program Budget vs Spent', 'Zero Co-Mingling Lock']],
                        ['span' => 5, 'label' => '5-COL GRANT DISBURSEMENT', 'widgets' => ['Ketua + Bendahara Sign', 'Receipt & Geo Evidence', 'ISAK 35 / ASC 958 PDF']],
                    ],
                ],
                [
                    'code' => 'UI_12B_PUBLIC_DONOR_TRUST_PORTAL',
                    'is_default' => false,
                    'default_shell' => 'TOPBAR_EXECUTIVE_DECK',
                    'badge' => 'PUBLIC TRANSPARENCY PORTAL',
                    'name' => [
                        'id' => 'Opsi B — Portal Donasi Publik Transparan & Verifikasi Kwitansi SHA-256',
                        'en' => 'Option B — Public Donor Impact Portal & SHA-256 Receipt Verifier',
                    ],
                    'summary' => [
                        'id' => 'Meningkatkan kepercayaan donatur/jemaat 10x lipat: progress dana real-time, foto penyaluran lapangan ber-koordinat, dan kwitansi QR bersegel hash.',
                        'en' => 'Builds 10x donor trust: real-time campaign progress bars, geo-tagged field distribution timeline, and tamper-evident QR receipts.',
                    ],
                    'panes' => [
                        ['span' => 6, 'label' => '6-COL CAMPAIGN & IMPACT', 'widgets' => ['Live Collected vs Target', 'Field Geo/Photo Feed', 'Anonymous Donor Mask']],
                        ['span' => 6, 'label' => '6-COL FAST DONATE & HASH', 'widgets' => ['1-Tap QRIS / VA Donate', 'Instant WA Kwitansi PDF', 'Public SHA-256 Verifier']],
                    ],
                ],
                [
                    'code' => 'UI_12C_FIELD_AID_DISTRIBUTION_DESK',
                    'is_default' => false,
                    'default_shell' => 'MOBILE_FIRST_THUMB_PWA',
                    'badge' => 'FIELD BENEFICIARY DESK',
                    'name' => [
                        'id' => 'Opsi C — Pendataan Penerima Manfaat / Mustahiq Anti-Ganda & Penyaluran',
                        'en' => 'Option C — Beneficiary Deduplication & Field Aid Handover PWA',
                    ],
                    'summary' => [
                        'id' => 'Memudahkan relawan lapangan memverifikasi NIK penerima bantuan agar tidak menerima ganda, scan kupon QR bantuan, dan unggah foto serah terima.',
                        'en' => 'Empowers field volunteers to deduplicate beneficiary IDs, scan aid voucher QRs, and capture handover photo evidence.',
                    ],
                    'panes' => [
                        ['span' => 5, 'label' => '5-COL FIELD HANDOVER PWA', 'widgets' => ['NIK Duplicate Guard', 'Scan Aid Voucher QR', 'Capture Photo + GPS']],
                        ['span' => 7, 'label' => '7-COL DISTRIBUTION MAP', 'widgets' => ['Region Quota Progress', 'Approved Beneficiary List', 'Donor Impact Auto-Report']],
                    ],
                ],
            ],
        ];
    }

    /**
     * Resolve Visual Frontend UI/UX Options + Selected Variant Contract for a given Sector.
     *
     * @return array<string, mixed>
     */
    public static function visualUiContractForSector(
        string $sectorCode,
        ?string $selectedVariantCode = null,
        ?string $navigationShell = null,
        string $outputLocale = 'id'
    ): array {
        $normalizedSector = PosSectorUiArchetypeCatalog::normalizeSectorCode($sectorCode);
        $catalog = self::allSectorVisualUiCatalog();
        $variants = $catalog[$normalizedSector] ?? $catalog['SECTOR_01_COMMERCE_OMNICHANNEL'];

        $chosen = $variants[0];
        if ($selectedVariantCode !== null && trim($selectedVariantCode) !== '') {
            $needle = strtoupper(trim($selectedVariantCode));
            foreach ($variants as $v) {
                if (strtoupper((string) $v['code']) === $needle) {
                    $chosen = $v;
                    break;
                }
            }
        }

        $shells = self::navigationShellOptions();
        $resolvedShellCode = ($navigationShell && isset($shells[strtoupper(trim($navigationShell))]))
            ? strtoupper(trim($navigationShell))
            : (string) ($chosen['default_shell'] ?? 'SIDEBAR_ERP_DENSE');
        $resolvedShell = $shells[$resolvedShellCode] ?? $shells['SIDEBAR_ERP_DENSE'];

        $normLocale = self::normalizeOutputLocale($outputLocale);
        $isRtl = $normLocale === 'ar';

        return [
            'sector_code' => $normalizedSector,
            'selected_variant_code' => $chosen['code'],
            'selected_variant_badge' => $chosen['badge'],
            'selected_variant_name' => $chosen['name'],
            'selected_variant_summary' => $chosen['summary'],
            'selected_variant_panes' => $chosen['panes'],
            'navigation_shell_code' => $resolvedShellCode,
            'navigation_shell' => $resolvedShell,
            'output_locale' => $normLocale,
            'text_direction' => $isRtl ? 'rtl' : 'ltr',
            'html_dir_attribute' => $isRtl ? 'dir="rtl"' : 'dir="ltr"',
            'rtl_logical_css_mandate' => $isRtl
                ? 'MANDATORY RTL CONTRACT: Root <html lang="ar" dir="rtl">; replace all directional ml-*/mr-*/pl-*/pr-*/text-left/border-l-* classes with logical Tailwind properties ms-*/me-*/ps-*/pe-*/text-start/border-s-*.'
                : 'Standard LTR layout; logical spacing properties recommended for multi-locale readiness.',
            'available_variants' => $variants,
            'available_navigation_shells' => array_values($shells),
            'total_sector_variants' => count($variants),
            'total_catalog_blueprints' => 36,
        ];
    }

    /**
     * Pillar 1: 5-Step Zero-Jargon Business Storyboard Wizard Catalog (Mode Bisnis / Awam).
     * Maps plain-human everyday questions directly to canonical Sector, PosPrimitive, and RBAC codes!
     *
     * @return array<string, mixed>
     */
    public static function storyboardWizardCatalog(): array
    {
        return [
            'steps' => [
                [
                    'step' => 1,
                    'code' => 'STORY_STEP_1_BUSINESS_TYPE',
                    'question' => [
                        'id' => '1. Apa jenis usaha atau organisasi Anda?',
                        'en' => '1. What type of business or organization do you run?',
                    ],
                    'helper' => [
                        'id' => 'Pilih kartu yang paling mendekati kegiatan tempat Anda sehari-hari (tanpa istilah teknis).',
                        'en' => 'Pick the card that best matches your everyday operations (zero technical jargon).',
                    ],
                ],
                [
                    'step' => 2,
                    'code' => 'STORY_STEP_2_DAILY_ACTIVITIES',
                    'question' => [
                        'id' => '2. Apa saja aktivitas harian di tempat Anda?',
                        'en' => '2. What daily activities happen at your place?',
                    ],
                    'helper' => [
                        'id' => 'Centang kebutuhan Anda; sistem otomatis merakit mesin pengaman anti-bentrok & pembukuan di balik layar.',
                        'en' => 'Check your daily workflows; Project OS automatically wires anti-collision & ledger engines behind the scenes.',
                    ],
                ],
                [
                    'step' => 3,
                    'code' => 'STORY_STEP_3_TEAM_ROLES',
                    'question' => [
                        'id' => '3. Siapa saja yang akan memakai aplikasi ini?',
                        'en' => '3. Who will use this application every day?',
                    ],
                    'helper' => [
                        'id' => 'Pilih peran pengguna agar setiap orang hanya melihat menu yang menjadi hak dan tugasnya.',
                        'en' => 'Select staff & customer roles so everyone only sees the screens permitted for their job.',
                    ],
                ],
                [
                    'step' => 4,
                    'code' => 'STORY_STEP_4_VISUAL_SCREEN_PREVIEW',
                    'question' => [
                        'id' => '4. Pilih Desain Layar & Klik Simulasi Aplikasi Anda',
                        'en' => '4. Pick Your Visual Screen Layout & Click Live Prototype',
                    ],
                    'helper' => [
                        'id' => 'Bandingkan 3 pilihan tata letak layar visual khusus bidang usaha Anda dan coba klik menu operasionalnya.',
                        'en' => 'Compare 3 curated visual screen layouts for your sector and click through simulated menus.',
                    ],
                ],
                [
                    'step' => 5,
                    'code' => 'STORY_STEP_5_REALIZATION_PATH',
                    'question' => [
                        'id' => '5. Bagaimana Anda ingin mewujudkan aplikasi ini?',
                        'en' => '5. How would you like to bring this app to life?',
                    ],
                    'helper' => [
                        'id' => 'Pilih Opsi A (Bangun Mandiri mulai Gratis/Hemat) atau Opsi B (Terima Beres Dikodingkan Tim NeriahPro).',
                        'en' => 'Choose Option A (Self-Build with AI starting Free) or Option B (Turnkey Built by NeriahPro Team).',
                    ],
                ],
            ],
            'friendly_sectors' => [
                ['code' => 'SECTOR_01_COMMERCE_OMNICHANNEL', 'icon' => '🛒', 'label' => ['id' => 'Toko Ritel, Minimarket, Grosir & Toko Online', 'en' => 'Retail Shop, Supermarket, Wholesale & E-Commerce']],
                ['code' => 'SECTOR_02_HOSPITALITY_SPACE', 'icon' => '🏸', 'label' => ['id' => 'Klub Olahraga, Hotel/Villa & Sewa Tempat/Kendaraan', 'en' => 'Sports Club, Hotel/Villa & Space/Vehicle Rental']],
                ['code' => 'SECTOR_03_HEALTHCARE_CLINICAL', 'icon' => '🏥', 'label' => ['id' => 'Klinik Dokter, Praktek Bersama, Lab & Apotek', 'en' => 'Medical Clinic, Outpatient Care, Lab & Pharmacy']],
                ['code' => 'SECTOR_04_FINTECH_LEDGER', 'icon' => '💳', 'label' => ['id' => 'Koperasi, Keuangan, Akuntansi & Tagihan Rutin', 'en' => 'Finance, Co-op, Accounting & Recurring Billing']],
                ['code' => 'SECTOR_05_INDUSTRIAL_MRP', 'icon' => '🏭', 'label' => ['id' => 'Pabrik Produksi, Konveksi, Bengkel & Konstruksi', 'en' => 'Manufacturing Plant, Workshop & Construction']],
                ['code' => 'SECTOR_06_LOGISTICS_FLEET', 'icon' => '🚚', 'label' => ['id' => 'Ekspedisi Kargo, Kurir Pengiriman & Armada Truk', 'en' => 'Cargo Delivery, Courier Fleet & Distribution']],
                ['code' => 'SECTOR_07_EDTECH_MEMBERSHIP', 'icon' => '🎓', 'label' => ['id' => 'Sekolah, Kampus, Bimbel, Kursus & Akademi', 'en' => 'School, Campus, Course Center & Academy']],
                ['code' => 'SECTOR_08_PROFESSIONAL_PSA', 'icon' => '💼', 'label' => ['id' => 'Agensi Kreatif, Konsultan, Kantor Hukum & Kontraktor', 'en' => 'Agency, Consulting, Law Firm & Project Services']],
                ['code' => 'SECTOR_09_FOOD_BEVERAGE', 'icon' => '🍽️', 'label' => ['id' => 'Restoran, Cafe, Warung Makan, Katering & Cloud Kitchen', 'en' => 'Restaurant, Cafe, Bakery & Cloud Kitchen']],
                ['code' => 'SECTOR_10_MEDIA_TICKETING', 'icon' => '🎟️', 'label' => ['id' => 'Tiket Konser/Wisata, Event Organizer & Studio Kreator', 'en' => 'Event Ticketing, Attraction Gate & Creator Studio']],
                ['code' => 'SECTOR_11_HRIS_INTERNAL_OPS', 'icon' => '👥', 'label' => ['id' => 'Absensi Karyawan, Jadwal Shift, Gaji & Inventaris Kantor', 'en' => 'Employee Attendance, Shift Roster, Payroll & Assets']],
                ['code' => 'SECTOR_12_GOVTECH_NONPROFIT', 'icon' => '🤝', 'label' => ['id' => 'Yayasan Sosial, Rumah Ibadah, Lembaga Zakat & NGO', 'en' => 'Social Foundation, House of Worship, Zakat & NGO']],
            ],
            'daily_activities_to_primitives' => [
                [
                    'primitive_code' => 'PRIMITIVE_RBAC_TENANCY',
                    'locked' => true,
                    'human_label' => [
                        'id' => 'Memisahkan hak akses layar pemilik, kasir, dan staf agar aman',
                        'en' => 'Separate owner, cashier, and staff access safely',
                    ],
                    'behind_the_scenes' => 'PRIMITIVE_RBAC_TENANCY + Multi-Branch Isolation',
                ],
                [
                    'primitive_code' => 'PRIMITIVE_CATALOG_SKU',
                    'locked' => false,
                    'human_label' => [
                        'id' => 'Mengatur daftar produk, paket layanan, variasi harga, & diskon',
                        'en' => 'Manage products, service packages, variants, & discounts',
                    ],
                    'behind_the_scenes' => 'PRIMITIVE_CATALOG_SKU + Historical Price Snapshot',
                ],
                [
                    'primitive_code' => 'PRIMITIVE_INVENTORY_LEDGER',
                    'locked' => false,
                    'human_label' => [
                        'id' => 'Memantau keluar-masuk stok gudang/bahan agar tidak minus atau selisih',
                        'en' => 'Track stock & raw materials accurately with zero negative stock',
                    ],
                    'behind_the_scenes' => 'PRIMITIVE_INVENTORY_LEDGER + Atomic Stock Lock',
                ],
                [
                    'primitive_code' => 'PRIMITIVE_TIME_SLOT_ENGINE',
                    'locked' => false,
                    'human_label' => [
                        'id' => 'Pelanggan memesan jadwal jam/kamar/lapangan agar tidak pernah bentrok',
                        'en' => 'Let customers book time-slots/rooms/courts with zero double-booking',
                    ],
                    'behind_the_scenes' => 'PRIMITIVE_TIME_SLOT_ENGINE + lockForUpdate() + GiST Exclusion',
                ],
                [
                    'primitive_code' => 'PRIMITIVE_DOUBLE_ENTRY_LEDGER',
                    'locked' => false,
                    'human_label' => [
                        'id' => 'Mencatat uang masuk/keluar, pembayaran QRIS/VA & laporan laba-rugi otomatis',
                        'en' => 'Record cash in/out, QRIS/VA payments & automated financial reports',
                    ],
                    'behind_the_scenes' => 'PRIMITIVE_DOUBLE_ENTRY_LEDGER + DECIMAL(19,4) Immutable Journal',
                ],
                [
                    'primitive_code' => 'PRIMITIVE_APPROVAL_WORKFLOW',
                    'locked' => false,
                    'human_label' => [
                        'id' => 'Membutuhkan persetujuan atasan/ketua sebelum pencairan dana atau void transaksi',
                        'en' => 'Require manager/chairman approval before payouts or voiding bills',
                    ],
                    'behind_the_scenes' => 'PRIMITIVE_APPROVAL_WORKFLOW + 4-Eyes Maker-Checker',
                ],
                [
                    'primitive_code' => 'PRIMITIVE_CLINICAL_EMR_AUDIT',
                    'locked' => false,
                    'human_label' => [
                        'id' => 'Menyimpan riwayat rekam medis/layanan sensitif yang terkunci & berjejak audit',
                        'en' => 'Store confidential medical/service records with immutable audit logs',
                    ],
                    'behind_the_scenes' => 'PRIMITIVE_CLINICAL_EMR_AUDIT + Append-Only Addendum',
                ],
                [
                    'primitive_code' => 'PRIMITIVE_BOM_WORK_ORDER',
                    'locked' => false,
                    'human_label' => [
                        'id' => 'Menghitung resep/bahan baku otomatis menjadi barang jadi atau hidangan dapur',
                        'en' => 'Auto-deduct recipes/raw materials into finished goods or kitchen dishes',
                    ],
                    'behind_the_scenes' => 'PRIMITIVE_BOM_WORK_ORDER + Multi-Level Cost Roll-Up',
                ],
                [
                    'primitive_code' => 'PRIMITIVE_FLEET_DISPATCH_POD',
                    'locked' => false,
                    'human_label' => [
                        'id' => 'Melacak pengantaran kurir/relawan lengkap bukti foto kamera & tanda tangan',
                        'en' => 'Track field deliveries/aid with camera photo & digital signature proof',
                    ],
                    'behind_the_scenes' => 'PRIMITIVE_FLEET_DISPATCH_POD + Geo/Signature PoD',
                ],
                [
                    'primitive_code' => 'PRIMITIVE_SUBSCRIPTION_RECURRING',
                    'locked' => false,
                    'human_label' => [
                        'id' => 'Mengelola iuran bulanan member, SPP siswa, atau donatur rutin otomatis',
                        'en' => 'Manage recurring memberships, student tuition, or monthly donors',
                    ],
                    'behind_the_scenes' => 'PRIMITIVE_SUBSCRIPTION_RECURRING + Automated Dunning & Quota',
                ],
                [
                    'primitive_code' => 'PRIMITIVE_PAYROLL_ROSTER_PPH21',
                    'locked' => false,
                    'human_label' => [
                        'id' => 'Mengatur jadwal shift staf, absensi lokasi, lembur, & hitung gaji bersih',
                        'en' => 'Schedule staff shifts, GPS attendance, overtime, & net payroll',
                    ],
                    'behind_the_scenes' => 'PRIMITIVE_PAYROLL_ROSTER_PPH21 + Statutory Tax Engine',
                ],
                [
                    'primitive_code' => 'PRIMITIVE_FUND_GRANT_DISBURSEMENT',
                    'locked' => false,
                    'human_label' => [
                        'id' => 'Memisahkan dana donasi/amanah program dari kas operasional & laporan publik',
                        'en' => 'Separate restricted donor/grant funds from operational cash transparently',
                    ],
                    'behind_the_scenes' => 'PRIMITIVE_FUND_GRANT_DISBURSEMENT + SHA-256 Public Trust Ledger',
                ],
            ],
            'visual_actor_roles' => [
                ['code' => 'ROLE_OWNER_DIRECTOR', 'icon' => '👑', 'label' => ['id' => 'Pemilik Usaha / Direktur / Ketua Yayasan', 'en' => 'Business Owner / Director / Board Chair']],
                ['code' => 'ROLE_FRONTLINE_CASHIER', 'icon' => '🖥️', 'label' => ['id' => 'Kasir / Resepsionis / Admin Pendaftaran', 'en' => 'Cashier / Receptionist / Frontdesk']],
                ['code' => 'ROLE_FINANCE_TREASURY', 'icon' => '📊', 'label' => ['id' => 'Bagian Keuangan / Bendahara / Akuntan', 'en' => 'Finance / Treasurer / Accountant']],
                ['code' => 'ROLE_OPS_SUPERVISOR', 'icon' => '📦', 'label' => ['id' => 'Kepala Cabang / Gudang / Supervisor Operasional', 'en' => 'Branch / Warehouse / Operations Supervisor']],
                ['code' => 'ROLE_SPECIALIST_FIELD', 'icon' => '🩺', 'label' => ['id' => 'Dokter / Pengajar / Kurir / Operator Produksi', 'en' => 'Doctor / Instructor / Courier / Operator']],
                ['code' => 'ROLE_EXTERNAL_PORTAL', 'icon' => '🌐', 'label' => ['id' => 'Pelanggan / Member / Wali Murid / Donatur', 'en' => 'Customer / Member / Parent / Donor']],
            ],
        ];
    }

    /**
     * Localized 5-Step Storyboard Wizard view-model for Livewire Studio & Frontend.
     *
     * @return array<string, mixed>
     */
    public static function businessStoryboardWizard(string $locale = 'id'): array
    {
        $lang = $locale === 'id' ? 'id' : 'en';
        $raw = self::storyboardWizardCatalog();

        return [
            'headline' => $lang === 'id'
                ? 'Rancang Sistem Bisnis Anda dalam 5 Langkah Bahasa Manusia (Tanpa Istilah Koding)'
                : 'Design Your Business System in 5 Everyday Human Steps (Zero Coding Jargon)',
            'steps' => [
                [
                    'step' => 1,
                    'code' => 'STORY_STEP_1_BUSINESS_TYPE',
                    'short_label' => $lang === 'id' ? 'Jenis Usaha' : 'Business Type',
                    'question' => $lang === 'id' ? '1. Apa jenis usaha atau organisasi Anda?' : '1. What type of business or organization do you run?',
                    'helper' => $lang === 'id'
                        ? 'Pilih bidang usaha Anda; sistem otomatis merakit tabel ULID, alur persetujuan, dan pengaman anti-selisih di balik layar.'
                        : 'Select your business sector; Project OS automatically wires ULID schemas, approvals, and anti-collision guards.',
                ],
                [
                    'step' => 2,
                    'code' => 'STORY_STEP_2_VISUAL_UI',
                    'short_label' => $lang === 'id' ? 'Desain Layar' : 'Screen Design',
                    'question' => $lang === 'id' ? '2. Pilih Desain Layar Visual Aplikasi Anda (3 Opsi Spesifik Sektor)' : '2. Choose Your Visual Screen Blueprint (3 Curated Sector Layouts)',
                    'helper' => $lang === 'id'
                        ? 'Pilih tata letak layar depan yang paling nyaman dipakai kasir, staf operasional, manajer, atau pelanggan Anda.'
                        : 'Pick the exact frontend screen layout best suited for your cashiers, field staff, managers, or customers.',
                ],
                [
                    'step' => 3,
                    'code' => 'STORY_STEP_3_MENUS_ROLES',
                    'short_label' => $lang === 'id' ? 'Menu & Peran' : 'Menus & Roles',
                    'question' => $lang === 'id' ? '3. Menu Operasional Harian & Pembagian Hak Akses Tim' : '3. Daily Operational Menus & Staff Access Roles',
                    'helper' => $lang === 'id'
                        ? '25 menu L1–L4 dan pemisahan hak akses peran sudah otomatis aktif sesuai standar industri terbaik.'
                        : 'All 25 L1–L4 enterprise menus and role-based access rules are automatically configured for your sector.',
                ],
                [
                    'step' => 4,
                    'code' => 'STORY_STEP_4_LOCALE_COMPLIANCE',
                    'short_label' => $lang === 'id' ? 'Bahasa & Regulasi' : 'Locale & Rules',
                    'question' => $lang === 'id' ? '4. Bahasa Aplikasi, Mata Uang & Kepatuhan Hukum/Akuntansi' : '4. Output Language, Currency & Statutory Accounting Rules',
                    'helper' => $lang === 'id'
                        ? 'Mendukung 7 bahasa utama (termasuk Arab RTL otomatis) dengan penyesuaian standar pajak & akuntansi negara tujuan.'
                        : 'Supports 7 Tier-1 locales (including automatic Arabic RTL) with jurisdiction-aware tax & accounting compliance.',
                ],
                [
                    'step' => 5,
                    'code' => 'STORY_STEP_5_REALIZATION_PATH',
                    'short_label' => $lang === 'id' ? 'Wujudkan App' : 'Build Path',
                    'question' => $lang === 'id' ? '5. Bagaimana Anda ingin mewujudkan aplikasi ini?' : '5. How would you like to bring this application to life?',
                    'helper' => $lang === 'id'
                        ? 'Pilih Opsi Retail (Bangun Mandiri dibantu AI mulai Gratis/Hemat) atau Opsi Mitra Turnkey (Terima Beres Dikodingkan Tim NeriahPro).'
                        : 'Choose Self-Dev Retail (Build with AI starting Free) or Turnkey Mitra Studio (Engineered end-to-end by NeriahPro).',
                ],
            ],
            'friendly_sectors' => $raw['friendly_sectors'] ?? [],
            'daily_activities_to_primitives' => $raw['daily_activities_to_primitives'] ?? [],
            'visual_actor_roles' => $raw['visual_actor_roles'] ?? [],
        ];
    }

    /**
     * Pillar 2: Tier-1 Global Native Localization & Jurisdiction-Aware Compliance Switching.
     * Supports ID, EN (US/UK/SG), ZH-CN, JA, AR (with automatic dir="rtl"), ES, and DE.
     *
     * @return array<string, mixed>
     */
    public static function jurisdictionComplianceProfile(string $outputLocale = 'id', string $sectorCode = 'SECTOR_01_COMMERCE_OMNICHANNEL'): array
    {
        $loc = self::normalizeOutputLocale($outputLocale);
        $normSector = PosSectorUiArchetypeCatalog::normalizeSectorCode($sectorCode);

        $profiles = [
            'id' => [
                'locale_code' => 'id',
                'language_label' => '🇮🇩 Bahasa Indonesia (ID — Domestik & Regulasi NKRI)',
                'jurisdiction_code' => 'ID_NKRI_STATUTORY',
                'direction' => 'ltr',
                'currency' => 'IDR (Rp)',
                'thousand_decimal_format' => '1.250.000,00 (Titik ribuan, Koma desimal)',
                'accounting_standards' => match ($normSector) {
                    'SECTOR_12_GOVTECH_NONPROFIT' => ['ISAK 35 (Entitas Berorientasi Nonlaba)', 'PSAK 109 (Akuntansi Zakat & Infak/Sedekah)', 'PP No. 93 Tahun 2010'],
                    'SECTOR_04_FINTECH_LEDGER' => ['PSAK 71/72/73 (SAK Indonesia)', 'Standar Open API Pembayaran BI SNAP'],
                    default => ['SAK Indonesia / SAK EP (Entitas Privat)', 'Jurnal Berpasangan Imutabel DECIMAL(19,4)'],
                },
                'tax_and_invoicing_rules' => ['PPN 11%/12% & CoreTax DJP E-Faktur XML', 'PPh 21 Tarif Efektif Rata-Rata (TER PP 58/2023)', 'PPh 23 Jasa & PPh Final UMKM 0,5% (PP 55/2022)'],
                'privacy_and_sector_mandates' => match ($normSector) {
                    'SECTOR_03_HEALTHCARE_CLINICAL' => ['UU PDP No. 27 Tahun 2022', 'Permenkes No. 24 Tahun 2022 (Rekam Medis Elektronik)', 'Interoperabilitas FHIR SatuSehat Kemenkes & BPJS V-Claim'],
                    default => ['UU Perlindungan Data Pribadi (UU PDP No. 27 Tahun 2022)', 'PSE Komdigi & Enkripsi Kolom PII'],
                },
            ],
            'en' => [
                'locale_code' => 'en',
                'language_label' => '🇬🇧🇺🇸 English (EN — US / UK / SG Global Compliance)',
                'jurisdiction_code' => 'GLOBAL_GAAP_IFRS_SOC2',
                'direction' => 'ltr',
                'currency' => 'USD ($) / SGD / GBP',
                'thousand_decimal_format' => '1,250,000.00 (Comma thousands, Dot decimal)',
                'accounting_standards' => match ($normSector) {
                    'SECTOR_12_GOVTECH_NONPROFIT' => ['FASB ASC 958 (Not-for-Profit Entities Net Asset Classes)', 'IRS 501(c)(3) Restricted Grant Fund Accounting', 'IFRS Foundation Guidance'],
                    default => ['US GAAP (ASC 606 Revenue Recognition) & IFRS 15', 'Immutable Double-Entry Sub-Ledger DECIMAL(19,4)'],
                },
                'tax_and_invoicing_rules' => ['Sales Tax / VAT / GST Multi-Jurisdiction Nexus', 'Stripe Tax / Peppol UBL E-Invoicing', 'W-2 / 1099 / PAYE Statutory Payroll'],
                'privacy_and_sector_mandates' => match ($normSector) {
                    'SECTOR_03_HEALTHCARE_CLINICAL' => ['HIPAA Security & Privacy Rule (PHI Audit Trail)', 'HL7 FHIR R4 Interoperability', 'SOC 2 Type II Access Controls'],
                    default => ['SOC 2 Type II Trust Principles', 'GDPR Art. 17/32 & CCPA Privacy Controls'],
                },
            ],
            'zh-CN' => [
                'locale_code' => 'zh-CN',
                'language_label' => '🇨🇳 中文 (ZH-CN — Mandarin / APAC Cross-Border)',
                'jurisdiction_code' => 'CN_CAS_PIPL_APAC',
                'direction' => 'ltr',
                'currency' => 'CNY (¥) / SGD',
                'thousand_decimal_format' => '1,250,000.00',
                'accounting_standards' => ['CAS (Chinese Accounting Standards for Business Enterprises)', 'IFRS Cross-Border Consolidation'],
                'tax_and_invoicing_rules' => ['Golden Tax Phase IV Digital Fapiao (全电发票)', 'VAT 13%/9%/6% Tiered Output Ledger'],
                'privacy_and_sector_mandates' => ['PIPL (Personal Information Protection Law)', 'MLPS 2.0 Audit Logging'],
            ],
            'ja' => [
                'locale_code' => 'ja',
                'language_label' => '🇯🇵 日本語 (JA — Japan J-GAAP & Qualified Invoice)',
                'jurisdiction_code' => 'JP_JGAAP_APPI',
                'direction' => 'ltr',
                'currency' => 'JPY (¥)',
                'thousand_decimal_format' => '1,250,000 (Zero-decimal JPY integer-safe)',
                'accounting_standards' => ['J-GAAP / IFRS Japan', 'Electronic Books Preservation Act (電子帳簿保存法) Tamper-Evident Timestamps'],
                'tax_and_invoicing_rules' => ['Qualified Invoice System (適格請求書等保存方式 / T-Number)', 'Consumption Tax 10% Standard / 8% Reduced Food Split'],
                'privacy_and_sector_mandates' => ['APPI (Act on the Protection of Personal Information)', 'MyNumber Strict Encryption Vault'],
            ],
            'ar' => [
                'locale_code' => 'ar',
                'language_label' => '🇸🇦🇦🇪 العربية (AR — MENA RTL Layout + ZATCA / AAOIFI)',
                'jurisdiction_code' => 'MENA_AAOIFI_ZATCA_RTL',
                'direction' => 'rtl',
                'currency' => 'SAR (ر.س) / AED (د.إ)',
                'thousand_decimal_format' => '1,250,000.00 (Tabular Western/Eastern Arabic numerals)',
                'accounting_standards' => match ($normSector) {
                    'SECTOR_12_GOVTECH_NONPROFIT', 'SECTOR_04_FINTECH_LEDGER' => ['AAOIFI Sharia & Waqf/Zakat Accounting Standards', 'IFRS MENA Adoption'],
                    default => ['IFRS (SOCPA / UAE MoF)', 'AAOIFI Financial Accounting Standards'],
                },
                'tax_and_invoicing_rules' => ['ZATCA Phase 2 Fatoora E-Invoicing (TLV Cryptographic QR + XML)', 'GCC VAT 15% (KSA) / 5% (UAE)'],
                'privacy_and_sector_mandates' => ['Saudi PDPL & UAE Federal Data Protection Law', 'Mandatory HTML dir="rtl" + Logical CSS Properties (ms-*/me-*/ps-*/pe-*)'],
            ],
            'es' => [
                'locale_code' => 'es',
                'language_label' => '🇪🇸 Español (ES — EU & LATAM Electronic Invoicing)',
                'jurisdiction_code' => 'ES_NIIF_FACTURAE_GDPR',
                'direction' => 'ltr',
                'currency' => 'EUR (€) / USD ($)',
                'thousand_decimal_format' => '1.250.000,00',
                'accounting_standards' => ['PGC España / NIIF (IFRS LATAM)', 'Veri*factu Anti-Fraud Immutable Billing Chain'],
                'tax_and_invoicing_rules' => ['IVA 21%/10%/4% & Facturae / CFDI XML', 'SII Immediate Supply of Information'],
                'privacy_and_sector_mandates' => ['RGPD (EU GDPR) & LOPDGDD', 'Strict Right-to-Erasure Anonymization'],
            ],
            'de' => [
                'locale_code' => 'de',
                'language_label' => '🇩🇪 Deutsch (DE — DACH GoBD & DSGVO Precision)',
                'jurisdiction_code' => 'DE_HGB_GOBD_DSGVO',
                'direction' => 'ltr',
                'currency' => 'EUR (€)',
                'thousand_decimal_format' => '1.250.000,00',
                'accounting_standards' => ['HGB (Handelsgesetzbuch) & IFRS EU', 'GoBD Immutable Accounting & Archiving Principles'],
                'tax_and_invoicing_rules' => ['USt 19%/7% & EN 16931 XRechnung / ZUGFeRD Hybrid E-Invoice', 'TSE (Technische Sicherheitseinrichtung) POS Signature'],
                'privacy_and_sector_mandates' => ['DSGVO (EU GDPR Strict German Enforcement)', 'BSI IT-Grundschutz Audit Trail'],
            ],
        ];

        $selected = $profiles[$loc] ?? $profiles['id'];
        $selected['supported_tier1_locales'] = array_map(
            static fn (array $p): array => [
                'code' => $p['locale_code'],
                'label' => $p['language_label'],
                'direction' => $p['direction'],
                'jurisdiction_code' => $p['jurisdiction_code'],
            ],
            array_values($profiles)
        );

        return $selected;
    }

    /**
     * Pillar 4 (#1): Living Architecture Lock-In — Real-Time Stack CVE & Drift Security Advisories + 1-Click Auto-Patch Prompts.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function livingCveSecurityAlerts(string $stackCode = 'STACK_M1_LARAVEL_MONOLITH', string|array $sectorCode = 'SECTOR_01_COMMERCE_OMNICHANNEL'): array
    {
        $sectorCode = is_array($sectorCode) ? 'SECTOR_01_COMMERCE_OMNICHANNEL' : $sectorCode;
        $normStack = PosStackPresetCatalog::normalizePresetCode($stackCode);
        $isPhp = str_contains($normStack, 'LARAVEL');
        $isNextNode = str_contains($normStack, 'NEXTJS') || str_contains($normStack, 'NESTJS');
        $isGoRust = str_contains($normStack, 'GO') || str_contains($normStack, 'RUST') || str_contains($normStack, 'POLYGLOT');

        $alerts = [];

        if ($isPhp) {
            $alerts[] = [
                'advisory_id' => 'NPRO-SEC-2026-01',
                'cve_code' => 'CVE-2026-109618',
                'severity' => 'CRITICAL',
                'affected_layer' => 'PHP Runtime (session.upload_progress) + Caddyfile / Nginx',
                'headline' => [
                    'id' => 'Peringatan Keamanan: Hardening php.ini (session.upload_progress=Off) & Header Caddyfile/Nginx',
                    'en' => 'Security Advisory: Harden php.ini (session.upload_progress=Off) & Caddyfile/Nginx Headers',
                ],
                'auto_patch_prompt' => "TUGAS AUTO-PATCH KEAMANAN [CVE-2026-109618]:\n1. Di docker/php/php.ini tambahkan `session.upload_progress.enabled = 0`, `expose_php = Off`, dan `session.use_strict_mode = 1`.\n2. Di Caddyfile / nginx.conf blokir eksekusi `.php` di dalam `/storage` & `/uploads`, serta kunci `X-Content-Type-Options: nosniff`.\n3. Jalankan `./verify-blueprint.sh` dan pastikan tes keamanan lulus Exit Code 0.",
            ];
        }

        if ($isNextNode) {
            $alerts[] = [
                'advisory_id' => 'NPRO-SEC-2026-02',
                'cve_code' => 'GHSA-NEXT-MW-2026',
                'severity' => 'HIGH',
                'affected_layer' => 'Next.js App Router / Edge Header Spoofing Guard',
                'headline' => [
                    'id' => 'Peringatan Keamanan: Strip Header Internal x-middleware-subrequest di Reverse Proxy & DAL Auth Check',
                    'en' => 'Security Advisory: Strip x-middleware-subrequest at Reverse Proxy & Enforce DAL Auth Check',
                ],
                'auto_patch_prompt' => "TUGAS AUTO-PATCH KEAMANAN [GHSA-NEXT-MW-2026]:\n1. Hapus header masuk `x-middleware-subrequest` pada Caddyfile/Cloudflare/Nginx.\n2. Wajibkan verifikasi sesi/tenant langsung di dalam Data Access Layer (`server-only`) sebelum query database, jangan hanya mengandalkan `middleware.ts`.",
            ];
        }

        if ($isGoRust) {
            $alerts[] = [
                'advisory_id' => 'NPRO-SEC-2026-03',
                'cve_code' => 'GO-HTTP2-RAPID-RESET-GUARD',
                'severity' => 'HIGH',
                'affected_layer' => 'Go / Rust High-Throughput API Gateway',
                'headline' => [
                    'id' => 'Peringatan Keamanan: Batasi ReadHeaderTimeout, MaxConcurrentStreams & Idempotency Payload Size',
                    'en' => 'Security Advisory: Enforce ReadHeaderTimeout, MaxConcurrentStreams & Body Size Limits',
                ],
                'auto_patch_prompt' => "TUGAS AUTO-PATCH KEAMANAN [GO/RUST GATEWAY]:\n1. Set `ReadHeaderTimeout: 5 * time.Second`, `IdleTimeout: 60 * time.Second`, dan `MaxRequestBodySize: 10MB`.\n2. Wajibkan validasi HMAC-SHA256 sebelum deserialisasi JSON webhook pembayaran.",
            ];
        }

        // Universal PostgreSQL 16 + Tenant IDOR Guard
        $alerts[] = [
            'advisory_id' => 'NPRO-DRIFT-PG16-RLS',
            'cve_code' => 'SYNTAX-LOCK-2026.07',
            'severity' => 'ADVISORY',
            'affected_layer' => 'PostgreSQL 16 Tenant Composite Index & Strict ULID Cast',
            'headline' => [
                'id' => "Pembaruan Active Vault ({$sectorCode}): Sinkronisasi SYNTAX_LOCK_2026.md & Proteksi Anti-IDOR Multi-Tenant",
                'en' => "Active Vault Update ({$sectorCode}): Sync SYNTAX_LOCK_2026.md & Multi-Tenant Anti-IDOR Guard",
            ],
            'auto_patch_prompt' => "TUGAS SINKRONISASI ACTIVE VAULT [SYNTAX-LOCK-2026.07]:\n1. Pastikan `Model::shouldBeStrict(!app()->isProduction())` aktif di `AppServiceProvider::boot()`.\n2. Verifikasi seluruh query model bisnis memfilter `organization_id` dan memakai `CHAR(26)` ULID (bukan tipe `uuid` native PG16).\n3. Jalankan `php artisan test --filter=TenantIsolationSecurityTest`.",
        ];

        return $alerts;
    }

    /**
     * Pillar 4 (#5): Verified Community Primitives Marketplace (70% Creator : 30% Platform Revenue Share).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function communityPrimitivesMarketplace(): array
    {
        return [
            [
                'code' => 'MOD_BPJS_VCLAIM_SATUSEHAT',
                'author' => 'Verified HealthTech Architect',
                'revenue_share' => '70% Creator / 30% NeriahPro',
                'price_idr' => 149000,
                'target_sector' => 'SECTOR_03_HEALTHCARE_CLINICAL',
                'maps_to_primitive' => 'PRIMITIVE_CLINICAL_EMR_AUDIT',
                'title' => [
                    'id' => 'Modul Integrasi BPJS V-Claim 2.0 & FHIR SatuSehat Kemenkes',
                    'en' => 'BPJS V-Claim 2.0 & Kemenkes SatuSehat FHIR Bridge Module',
                ],
                'summary' => [
                    'id' => 'Kontrak enkripsi LZString HMAC-SHA256 BPJS, antrean JKN Mobile, & sinkronisasi Encounter/Condition FHIR R4.',
                    'en' => 'Complete LZString HMAC-SHA256 BPJS client, JKN queue sync, & FHIR R4 Encounter/Condition outbox.',
                ],
            ],
            [
                'code' => 'MOD_CORETAX_DJP_EFAKTUR_XML',
                'author' => 'Verified FinTax Principal',
                'revenue_share' => '70% Creator / 30% NeriahPro',
                'price_idr' => 179000,
                'target_sector' => 'SECTOR_04_FINTECH_LEDGER',
                'maps_to_primitive' => 'PRIMITIVE_DOUBLE_ENTRY_LEDGER',
                'title' => [
                    'id' => 'Modul Ekspor Pajak CoreTax DJP E-Faktur XML & Bukti Potong Unifikasi',
                    'en' => 'CoreTax DJP E-Faktur XML & Withholding Tax Slip Generator',
                ],
                'summary' => [
                    'id' => 'Skema NPWP 16-digit/NIK, penomoran faktur pajak otomatis, dan generator XML siap unggah ke portal CoreTax DJP.',
                    'en' => '16-digit NPWP/NIK validation, tax invoice sequence lock, and bulk XML export ready for DJP CoreTax.',
                ],
            ],
            [
                'code' => 'MOD_IOT_DIGITAL_SCALE_LAUNDRY_POS',
                'author' => 'Verified Retail IoT Maker',
                'revenue_share' => '70% Creator / 30% NeriahPro',
                'price_idr' => 99000,
                'target_sector' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
                'maps_to_primitive' => 'PRIMITIVE_CATALOG_SKU',
                'title' => [
                    'id' => 'Modul Kasir Timbangan Digital WebSerial (Laundry Kiloan / Buah / Kargo)',
                    'en' => 'WebSerial Digital Scale POS Bridge (Laundry / Fresh Grocery / Cargo)',
                ],
                'summary' => [
                    'id' => 'Membaca berat gram/kg otomatis dari timbangan RS232/USB ke layar kasir tanpa ketik manual (anti-kecurangan kasir).',
                    'en' => 'Streams live weight from RS232/USB digital scales straight into POS cart lines with zero manual typing.',
                ],
            ],
            [
                'code' => 'MOD_ZATCA_FATOORA_QR_MENA',
                'author' => 'Verified MENA Cloud Architect',
                'revenue_share' => '70% Creator / 30% NeriahPro',
                'price_idr' => 199000,
                'target_sector' => 'SECTOR_04_FINTECH_LEDGER',
                'maps_to_primitive' => 'PRIMITIVE_DOUBLE_ENTRY_LEDGER',
                'title' => [
                    'id' => 'Modul E-Invoicing Arab Saudi ZATCA Fatoora Phase 2 + Layout RTL',
                    'en' => 'Saudi ZATCA Fatoora Phase 2 TLV QR & UBL XML Module (RTL Ready)',
                ],
                'summary' => [
                    'id' => 'Generator TLV Base64 QR Code ZATCA, tanda tangan ECDSA faktur, & template cetak termal/A4 dwibahasa Arab-Inggris.',
                    'en' => 'ZATCA TLV Base64 QR encoder, ECDSA invoice hash chain, & bilingual Arabic-English thermal/A4 templates.',
                ],
            ],
        ];
    }

    /**
     * Pillar 4 (#4): Viral Loop Badge ("Architecture Verified by NeriahPro Project OS — 100% Zero-Slop Certified").
     */
    public static function renderViralCertifiedBadgeMarkdown(?array $whiteLabel = null): string
    {
        if (! empty($whiteLabel['enabled']) && ! empty($whiteLabel['agency_name'])) {
            $agency = trim((string) $whiteLabel['agency_name']);
            $url = trim((string) ($whiteLabel['agency_website'] ?? ''));

            return $url !== ''
                ? "\n\n---\n> 🛡️ **Architecture Engineered & Scope-Verified by {$agency}** ({$url}) — *100% Zero-Slop Enterprise Standard*\n"
                : "\n\n---\n> 🛡️ **Architecture Engineered & Scope-Verified by {$agency}** — *100% Zero-Slop Enterprise Standard*\n";
        }

        return "\n\n---\n> 🛡️ **[Architecture Verified by NeriahPro Project OS — 100% Zero-Slop Certified](https://neriahpro.com/pricing?utm_source=verified_badge)**  \n> *Strict 26-Char ULID • Keyset O(1) Pagination • Zero AI Slop • Cryptographic SHA-256 Scope Lock*\n";
    }

    /**
     * Pillar 5 (#2): Deterministic 16-Hex-Char HMAC-SHA256 Fingerprint for Invisible Canary Watermarking.
     */
    public static function computeCanaryFingerprint(string $actorIdentifier): string
    {
        $cleanActor = trim($actorIdentifier) !== '' ? trim($actorIdentifier) : 'anonymous_guest';
        $secret = (string) config('app.key', 'neriahpro-canary-ip-shield-v8');

        return substr(hash_hmac('sha256', 'npro-canary-v8:' . $cleanActor, $secret), 0, 16);
    }

    /**
     * Embed an Invisible Zero-Width Unicode Canary Watermark encoding the 16-hex-char fingerprint (64 bits)
     * into exported/copied Markdown without altering visible characters or breaking Markdown parsers.
     */
    public static function injectCanaryWatermark(string $markdown, string $actorIdentifier): string
    {
        $fingerprintHex = preg_match('/^[0-9a-f]{16}$/i', $actorIdentifier)
            ? strtolower($actorIdentifier)
            : self::computeCanaryFingerprint($actorIdentifier);

        // Strip any prior watermark first so re-exporting remains idempotent
        $cleanMarkdown = self::stripCanaryWatermark($markdown);

        $bits = '';
        for ($i = 0; $i < 16; $i++) {
            $nibble = hexdec($fingerprintHex[$i]);
            $bits .= str_pad(decbin($nibble), 4, '0', STR_PAD_LEFT);
        }

        $encodedZw = self::ZW_BOUNDARY;
        $len = strlen($bits);
        for ($b = 0; $b < $len; $b++) {
            $encodedZw .= ($bits[$b] === '1') ? self::ZW_BIT_ONE : self::ZW_BIT_ZERO;
        }
        $encodedZw .= self::ZW_BOUNDARY;

        // Inject right after the first Markdown heading line if present, else at start
        $newlinePos = strpos($cleanMarkdown, "\n");
        if ($newlinePos !== false) {
            return substr($cleanMarkdown, 0, $newlinePos) . $encodedZw . substr($cleanMarkdown, $newlinePos);
        }

        return $cleanMarkdown . $encodedZw;
    }

    /**
     * Forensically extract and decode the 16-hex-char Canary Watermark fingerprint from leaked Markdown text.
     */
    public static function extractCanaryWatermark(string $markdown): ?string
    {
        $pattern = '/' . preg_quote(self::ZW_BOUNDARY, '/')
            . '([' . preg_quote(self::ZW_BIT_ZERO . self::ZW_BIT_ONE, '/') . ']{64})'
            . preg_quote(self::ZW_BOUNDARY, '/') . '/u';

        if (! preg_match($pattern, $markdown, $matches)) {
            return null;
        }

        $zwPayload = $matches[1];
        $chars = preg_split('//u', $zwPayload, -1, PREG_SPLIT_NO_EMPTY);
        if (! is_array($chars) || count($chars) !== 64) {
            return null;
        }

        $bits = '';
        foreach ($chars as $ch) {
            $bits .= ($ch === self::ZW_BIT_ONE) ? '1' : '0';
        }

        $hex = '';
        for ($i = 0; $i < 64; $i += 4) {
            $hex .= dechex(bindec(substr($bits, $i, 4)));
        }

        return strtolower($hex);
    }

    /**
     * Remove Zero-Width Canary Watermark payload cleanly if needed.
     */
    public static function stripCanaryWatermark(string $markdown): string
    {
        $pattern = '/' . preg_quote(self::ZW_BOUNDARY, '/')
            . '[' . preg_quote(self::ZW_BIT_ZERO . self::ZW_BIT_ONE, '/') . ']{64}'
            . preg_quote(self::ZW_BOUNDARY, '/') . '/u';

        return (string) preg_replace($pattern, '', $markdown);
    }

    /**
     * Summary of the 5 High-Retention Flywheel Hooks & 5 Banking-Grade IP Security Layers (v8.0).
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public static function retentionAndSecurityMatrix(): array
    {
        return [
            'five_retention_hooks' => [
                [
                    'number' => 1,
                    'code' => 'LIVING_ARCHITECTURE_CVE_ALERTS',
                    'badge' => '24/7 SECURITY SHIELD',
                    'title' => [
                        'id' => 'Living Architecture Lock-In (Peringatan Celah CVE & Auto-Patch Prompt)',
                        'en' => 'Living Architecture Lock-In (Drift & CVE Security Auto-Patch Alerts)',
                    ],
                    'desc' => [
                        'id' => 'Mendeteksi celah keamanan baru pada stack proyek (misal CVE-2026-109618 php.ini/Caddyfile) & menerbitkan Prompt Auto-Patch 1-klik ke IDE.',
                        'en' => 'Monitors project stacks against live CVEs and generates 1-click Auto-Patch Prompts directly for your IDE.',
                    ],
                ],
                [
                    'number' => 2,
                    'code' => 'INSTANT_VALUE_NO_LOGIN_BUILDER',
                    'badge' => '15-SEC AHA MOMENT',
                    'title' => [
                        'id' => 'Interactive Instant-Value Builder di Halaman Depan/Pricing (Tanpa Login)',
                        'en' => 'No-Login Interactive Blueprint & Visual UI Previewer in 15 Seconds',
                    ],
                    'desc' => [
                        'id' => 'Pengunjung langsung memilih 12 Sektor, membandingkan 36 opsi UI visual, melihat estimasi hemat token & RAB tanpa dipaksa login di awal.',
                        'en' => 'Visitors explore 12 Sectors, 36 visual UI wireframes, token savings, and RAB estimates in 15 seconds with zero upfront login wall.',
                    ],
                ],
                [
                    'number' => 3,
                    'code' => 'ACTIVE_MAINTENANCE_PROJECT_VAULT',
                    'badge' => 'ANTI-CHURN VAULT',
                    'title' => [
                        'id' => 'Active Maintenance Vault & Riwayat Evolusi Versi (v1.0 -> v2.4)',
                        'en' => 'Active Maintenance Vault & SYNTAX_LOCK Version History',
                    ],
                    'desc' => [
                        'id' => 'Menjaga proyek tetap mutakhir dengan pembaruan otomatis SYNTAX_LOCK_2026.md dan riwayat perubahan skema aditif lintas versi.',
                        'en' => 'Keeps dormant and active repositories current with SYNTAX_LOCK_2026.md updates and additive version history.',
                    ],
                ],
                [
                    'number' => 4,
                    'code' => 'VIRAL_ZERO_SLOP_CERTIFIED_BADGE',
                    'badge' => 'PLG VIRAL LOOP',
                    'title' => [
                        'id' => 'Lencana Viral "Architecture Verified by NeriahPro — 100% Zero-Slop Certified"',
                        'en' => 'Viral "100% Zero-Slop Certified" Architecture Verification Badge',
                    ],
                    'desc' => [
                        'id' => 'Menyematkan segel kualitas arsitektur pada dokumen ekspor (atau logo agensi sendiri pada paket Agency White-Label).',
                        'en' => 'Embeds a verifiable engineering seal on exported docs (or custom agency branding on Agency White-Label tier).',
                    ],
                ],
                [
                    'number' => 5,
                    'code' => 'VERIFIED_COMMUNITY_PRIMITIVES_MARKETPLACE',
                    'badge' => '70:30 ECOSYSTEM',
                    'title' => [
                        'id' => 'Marketplace Modul Primitif Terverifikasi (Bagi Hasil Kreator 70:30)',
                        'en' => 'Verified Community Primitives Marketplace (70:30 Revenue Share)',
                    ],
                    'desc' => [
                        'id' => 'Ekosistem modul spesialis siap pasang (BPJS V-Claim, CoreTax DJP E-Faktur, Timbangan Digital Kasir, ZATCA Arab) dengan bagi hasil 70:30.',
                        'en' => 'Plug-and-play verified domain modules (BPJS V-Claim, CoreTax DJP, IoT Scale POS, ZATCA MENA) with 70:30 creator revenue share.',
                    ],
                ],
            ],
            'five_security_layers' => [
                [
                    'number' => 1,
                    'code' => 'ZERO_KNOWLEDGE_ENVELOPE_ENCRYPTION',
                    'badge' => 'AES-256 VAULT',
                    'title' => [
                        'id' => 'Enkripsi Kolom Brankas Kustom (encrypted:array / AES-256)',
                        'en' => 'Column-Level Envelope Encryption (encrypted:array / AES-256)',
                    ],
                    'desc' => [
                        'id' => 'Rahasia logika bisnis kustom & catatan sensitif klien dienkripsi AES-256 pada kolom encrypted_custom_vault sehingga aman dari intipan dump DB.',
                        'en' => 'Encrypts sensitive custom business logic and vault secrets at rest with AES-256 so raw DB dumps expose zero plaintext.',
                    ],
                ],
                [
                    'number' => 2,
                    'code' => 'ANTI_SCRAPING_AND_CANARY_WATERMARK',
                    'badge' => 'ZERO-WIDTH CANARY',
                    'title' => [
                        'id' => 'Rate Limit Ketat & Invisible Canary Watermark (Zero-Width Hash)',
                        'en' => 'Anti-Scraping Rate Limit & Steganographic Zero-Width Canary Watermark',
                    ],
                    'desc' => [
                        'id' => 'Membatasi kompilasi bot & menyuntikkan sidik jari Zero-Width Unicode unik per user_id pada setiap Markdown yang disalin/diunduh.',
                        'en' => 'Throttles automated harvesting and embeds a 64-bit Zero-Width Unicode actor fingerprint inside exported Markdown.',
                    ],
                ],
                [
                    'number' => 3,
                    'code' => 'SCOPED_REVOCABLE_MCP_TOKENS',
                    'badge' => '1-CLICK REVOKE',
                    'title' => [
                        'id' => 'Zero Raw External Secret & Pencabutan Token MCP 1-Klik',
                        'en' => 'Zero Raw Secret Storage & 1-Click Scoped MCP Token Revocation',
                    ],
                    'desc' => [
                        'id' => 'Hanya menyimpan hash SHA-256 token MCP di database dan menyediakan tombol cabut/rotasi instan 1-klik kapan saja.',
                        'en' => 'Stores only SHA-256 hashes of scoped MCP tokens and supports instant 1-click token revocation from Studio/Dashboard.',
                    ],
                ],
                [
                    'number' => 4,
                    'code' => 'STRICT_ULID_AND_SHA256_AUDIT_CHAIN',
                    'badge' => 'SHA-256 AUDIT',
                    'title' => [
                        'id' => 'Isolasi Multi-Tenant ULID & Jejak Audit Berantai SHA-256',
                        'en' => 'Strict ULID Multi-Tenant Isolation & Tamper-Evident SHA-256 AuditLog',
                    ],
                    'desc' => [
                        'id' => 'Seluruh aksi mutasi blueprint, lisensi, dan persetujuan SPK/BAST dicatat ke AuditLog ber-PK ULID dengan segel integritas SHA-256.',
                        'en' => 'Every blueprint mutation, license checkout, and SPK/BAST approval is sealed into ULID AuditLog rows with SHA-256 hashes.',
                    ],
                ],
                [
                    'number' => 5,
                    'code' => 'MILESTONE_ESCROW_PAYMENT_GATE',
                    'badge' => 'VERIFIED ESCROW',
                    'title' => [
                        'id' => 'Proteksi Pembayaran Termin Terkunci Verification Gate & Scope Hash',
                        'en' => 'Milestone Payment Protection Locked to Verification Gate & Scope Hash',
                    ],
                    'desc' => [
                        'id' => 'Pada Mode Mitra Proyek, pencairan termin dikaitkan ke penyelesaian Verification Gate & checklist UAT serta dilindungi kunci SHA-256.',
                        'en' => 'Links Mitra turnkey milestone settlements directly to verified sprint gates, UAT checklists, and frozen SHA-256 scope hashes.',
                    ],
                ],
            ],
        ];
    }
}
