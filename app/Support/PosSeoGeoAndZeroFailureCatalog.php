<?php

declare(strict_types=1);

namespace App\Support;

/**
 * PosSeoGeoAndZeroFailureCatalog — Project OS v11.0 Paripurna Edition
 *
 * Canonical single source of truth for:
 * 1. 12 Functional Sectors -> Schema.org @graph Types + Statutory Standards + GEO Answer Capsules
 * 2. High-Signal GEO Comparison Table (Generic AI / Legacy Dev vs NeriahPro Project OS)
 * 3. 3 v11 Composable Primitives (SEO/GEO Authority, Offline Outbox PWA, UU PDP Privacy Vault)
 * 4. 6 Final Enterprise & Statutory Zero-Failure Production Features
 * 5. 20-Pillar Master Verification Matrix (8 Dimensions — 100% Locked)
 */
final class PosSeoGeoAndZeroFailureCatalog
{
    public const VERSION = '11.0.0-SEO-GEO-PARIPURNA';

    public const PRIMITIVE_SEO_GEO_AUTHORITY = 'PRIMITIVE_SEO_GEO_AUTHORITY_ENGINE';

    public const PRIMITIVE_OFFLINE_OUTBOX = 'PRIMITIVE_OFFLINE_OUTBOX_PWA';

    public const PRIMITIVE_UU_PDP_VAULT = 'PRIMITIVE_UU_PDP_PRIVACY_VAULT';

    /**
     * Compile the complete v11.0 SEO/GEO Authority + 6 Zero-Failure Enterprise Contract for a specific sector.
     *
     * @return array<string, mixed>
     */
    public static function compileSectorSeoGeoAndZeroFailureContract(
        string $sectorCode,
        string $appName = 'NeriahPro Project OS',
        string $locale = 'id'
    ): array {
        $capsule = self::sectorCapsule($sectorCode, $locale);

        return [
            'version' => self::VERSION,
            'sector_code' => $capsule['sector_code'],
            'injected_v11_primitives' => array_values(array_filter([
                self::PRIMITIVE_SEO_GEO_AUTHORITY,
                self::PRIMITIVE_UU_PDP_VAULT,
                $capsule['offline_pwa_critical'] ? self::PRIMITIVE_OFFLINE_OUTBOX : null,
            ])),
            'seo_geo_authority_contract' => [
                'primitive_code' => self::PRIMITIVE_SEO_GEO_AUTHORITY,
                'schema_org_graph_types' => array_values(array_unique(array_merge(
                    ['Organization', 'WebSite', 'SoftwareApplication', 'ProfessionalService', 'FAQPage', 'AggregateRating', 'OfferCatalog'],
                    $capsule['schema_types']
                ))),
                'statutory_standards' => $capsule['statutory_standards'],
                'answer_capsule_tldr_50w' => $capsule['tldr_text'],
                'llms_txt_endpoints' => ['/llms.txt', '/llms-full.txt'],
                'indexnow_endpoint' => '/indexnow-key.txt',
                'local_og_citation_card' => '1200x630 Local PNG/SVG with 21x21 QR Verification Matrix (Zero External CDN)',
                'geo_ai_bot_allowlist' => ['OAI-SearchBot', 'GPTBot', 'ClaudeBot', 'Claude-Web', 'PerplexityBot', 'Google-Extended'],
                'comparison_table_rows' => self::comparisonMatrixRows($locale),
            ],
            'offline_outbox_pwa_contract' => [
                'primitive_code' => self::PRIMITIVE_OFFLINE_OUTBOX,
                'offline_critical_for_sector' => (bool) $capsule['offline_pwa_critical'],
                'client_queue_store' => 'IndexedDB (neriahpro_offline_outbox_v11)',
                'id_strategy' => 'Client-generated 26-char ULID + X-Idempotency-Key replay guard',
                'sync_protocol' => 'Background Sync + Exponential Backoff [2s, 10s, 30s] with zero duplicate ledger mutation',
            ],
            'uu_pdp_privacy_vault_contract' => [
                'primitive_code' => self::PRIMITIVE_UU_PDP_VAULT,
                'trait' => 'App\\Models\\Concerns\\HasEncryptedPiiWithBlindIndex',
                'action' => 'App\\Actions\\Privacy\\ExportAndRedactUserPiiAction',
                'encryption_at_rest' => 'AES-256-CBC Laravel encrypted cast on NIK, Passport, Tax ID, & Personal Phone',
                'search_complexity' => 'O(1) deterministic 64-char HMAC-SHA256 *_blind_index B-Tree lookup',
                'right_to_erasure_policy' => 'Ledger-Safe PII Anonymization ([REDACTED_UU_PDP_<ULID>]) preserving financial journal & Transaction rows',
            ],
            'sla_post_bast_warranty_contract' => [
                'default_warranty_days' => 90,
                'auto_activated_on' => 'Digital BAST Sign-Off (PosCommercialBridgeService::signOffUatAndIssueBast)',
                'triage_classifications' => [
                    'BUG_WARRANTY_COVERED' => 'Rp 0 Free Warranty Fix + Auto-Generated Surgical Developer Bugfix Prompt',
                    'NEW_SCOPE_PAID_DELTA' => 'Out-of-Scope Feature Linked Automatically to PosChangeRequest Paid Delta v1.1',
                ],
            ],
            'holding_multi_branch_contract' => [
                'hierarchy' => 'Holding (parent_organization_id = NULL) -> Subsidiary / Branch (26-char ULID self-FK)',
                'branch_isolation' => 'Branch staff strictly scoped to own organization_id; Holding Owner executes consolidated roll-up',
                'inter_company_elimination' => 'Eliminates internal branch-to-branch transfers from consolidated net revenue',
            ],
            'atomic_rollback_and_sandbox_contract' => [
                'pre_migration_guard' => 'deploy.sh Scenario 2 captures timestamped pg_dump .sql.gz in storage/backups/ (7-day retention)',
                'one_click_rollback' => './deploy.sh rollback (Scenario 7)',
                'local_webhook_simulator' => 'Non-production Chaos Simulator (qris_payment_success, payment_expired, duplicate_webhook_attack); HTTP 403 in production',
            ],
            'six_final_zero_failure_modules' => self::sixFinalZeroFailureModules($locale),
            'twenty_pillars_paripurna_matrix' => self::twentyPillarsParipurnaMatrix(),
            'app_name' => $appName,
        ];
    }

    /**
     * All 12 Functional Sectors mapped to Schema.org types, statutory compliance, and 50-word Answer Capsules.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function sectorSeoGeoAuthorityMatrix(): array
    {
        return [
            'SECTOR_01_COMMERCE_OMNICHANNEL' => [
                'sector_code' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
                'schema_types' => ['Product', 'Offer', 'MerchantReturnPolicy', 'Brand'],
                'statutory_standards' => ['PMSE Kemendag', 'PPN 11%/12% e-Faktur', 'QRIS BI SNAP', 'UU PDP No. 27/2022'],
                'offline_pwa_critical' => true,
                'tldr' => [
                    'id' => 'Arsitektur Commerce, POS & Distribusi Omnichannel NeriahPro Project OS adalah cetak biru sistem kasir 100vh bebas-mouse dan gudang multi-cabang berbasis PostgreSQL 16 Strict ULID, Pessimistic Stock Lock FOR UPDATE, Offline-First IndexedDB Outbox, serta kepatuhan PMSE & PPN dengan waktu respons Keyset O(1) di bawah 10ms.',
                    'en' => 'NeriahPro Project OS Commerce, POS & Omnichannel Architecture is a production-grade mouse-free 100vh split terminal and multi-warehouse system built on PostgreSQL 16 Strict ULID, Pessimistic FOR UPDATE stock locks, Offline-First IndexedDB Outbox queues, and sub-10ms O(1) keyset cursor pagination.',
                ],
            ],
            'SECTOR_02_HOSPITALITY_SPACE' => [
                'sector_code' => 'SECTOR_02_HOSPITALITY_SPACE',
                'schema_types' => ['LodgingBusiness', 'SportsActivityLocation', 'Schedule', 'ReserveAction'],
                'statutory_standards' => ['PBJT Pajak Hotel & Hiburan', 'GiST Exclusion Anti-Overbooking', 'UU PDP No. 27/2022'],
                'offline_pwa_critical' => false,
                'tldr' => [
                    'id' => 'Arsitektur Perhotelan, Properti & Reservasi Ruang NeriahPro Project OS menjamin nol bentrok jadwal (Zero Double-Booking) menggunakan constraint PostgreSQL 16 GiST tstzrange exclusion tingkat kernel database, visualisasi Horizontal Resource Gantt interaktif, rekonsiliasi deposit folio otomatis, dan kepatuhan pajak daerah PBJT.',
                    'en' => 'NeriahPro Project OS Hospitality, Property & Space Booking Architecture guarantees zero double-booking collisions via PostgreSQL 16 kernel-level GiST tstzrange exclusion constraints, interactive Horizontal Resource Gantt timelines, automated deposit folio ledgers, and regional hospitality tax compliance.',
                ],
            ],
            'SECTOR_03_HEALTHCARE_CLINICAL' => [
                'sector_code' => 'SECTOR_03_HEALTHCARE_CLINICAL',
                'schema_types' => ['MedicalClinic', 'Hospital', 'Physician', 'MedicalSpecialty'],
                'statutory_standards' => ['Permenkes No. 24/2022 RME', 'HL7 FHIR SatuSehat Kemenkes', 'ICD-10 / ICD-9-CM', 'UU PDP Enkripsi NIK/Medis'],
                'offline_pwa_critical' => false,
                'tldr' => [
                    'id' => 'Arsitektur Klinik EMR, Rumah Sakit & Apotek NeriahPro Project OS menghadirkan Clinical 3-Pane Cockpit bebas modal bertumpuk, penguncian stok obat apotek FEFO per nomor batch, enkripsi rekam medis & Blind Index NIK sesuai Permenkes No. 24/2022 & UU PDP, serta kesiapan interoperabilitas HL7 FHIR SatuSehat.',
                    'en' => 'NeriahPro Project OS Clinical EMR & Pharmacy Architecture delivers a zero-modal 3-Pane Clinical Cockpit, batch-level FEFO prescription locking, encrypted patient PII with HMAC-SHA256 blind indexing under UU PDP & Permenkes 24/2022, and HL7 FHIR SatuSehat interoperability.',
                ],
            ],
            'SECTOR_04_FINTECH_LEDGER' => [
                'sector_code' => 'SECTOR_04_FINTECH_LEDGER',
                'schema_types' => ['FinancialProduct', 'AccountingService', 'PaymentService'],
                'statutory_standards' => ['PSAK 109 / PSAK 71', 'Double-Entry Immutable Ledger', 'BI SNAP X-Idempotency-Key', 'DECIMAL(19,4) Exact Math'],
                'offline_pwa_critical' => false,
                'tldr' => [
                    'id' => 'Arsitektur Keuangan, Akuntansi & Fintech Ledger NeriahPro Project OS menegakkan buku besar Double-Entry immutable berpresisi DECIMAL(19,4) tanpa tipe FLOAT, validasi keseimbangan SUM(Debit)==SUM(Kredit) sebelum posting, penguncian saldo paralel SELECT FOR UPDATE, serta kepatuhan PSAK 109 dan idempotensi webhook pembayaran.',
                    'en' => 'NeriahPro Project OS Fintech & Accounting Ledger Architecture enforces immutable DECIMAL(19,4) double-entry bookkeeping with zero floating-point drift, pre-post SUM(Debit)==SUM(Credit) gates, SELECT FOR UPDATE balance locks, PSAK 109 compliance, and cryptographic webhook idempotency.',
                ],
            ],
            'SECTOR_05_INDUSTRIAL_MRP' => [
                'sector_code' => 'SECTOR_05_INDUSTRIAL_MRP',
                'schema_types' => ['Organization', 'ProductModel', 'OfferForPurchase'],
                'statutory_standards' => ['ISO 9001 Traceability', 'Multi-Level BoM HPP Roll-Up', 'TKDN Audit Trail'],
                'offline_pwa_critical' => true,
                'tldr' => [
                    'id' => 'Arsitektur Manufaktur, Pabrik MRP & Konstruksi NeriahPro Project OS memadukan pohon Bill of Materials (BoM) multi-level dengan kalkulasi HPP bahan baku ke barang jadi otomatis, antarmuka lantai produksi ramah sarung tangan (h-14), pelacakan lot/scrap, dan sinkronisasi offline gudang.',
                    'en' => 'NeriahPro Project OS Industrial MRP & Shop Floor Architecture unifies multi-level Bill of Materials (BoM) COGS roll-up trees, glove-friendly h-14 high-contrast shop floor operator terminals, lot/scrap yield traceability, and offline warehouse outbox synchronization.',
                ],
            ],
            'SECTOR_06_LOGISTICS_FLEET' => [
                'sector_code' => 'SECTOR_06_LOGISTICS_FLEET',
                'schema_types' => ['DeliveryEvent', 'ParcelDelivery', 'MovingCompany'],
                'statutory_standards' => ['e-Waybill & Manifest QR', 'Offline Proof-of-Delivery (PoD)', 'UU PDP Geolocation Audit'],
                'offline_pwa_critical' => true,
                'tldr' => [
                    'id' => 'Arsitektur Logistik, Rantai Pasok & Armada NeriahPro Project OS menyediakan Split Dispatcher kubikasi/tonase truk dan PWA kurir lapangan tahan sinyal buruk (Offline Outbox IndexedDB) untuk tanda tangan kanvas Proof-of-Delivery (PoD), pemindaian resi QR, serta rekonsiliasi COD terproteksi.',
                    'en' => 'NeriahPro Project OS Logistics, Supply Chain & Fleet Architecture provides split load-capacity dispatchers and bad-signal-resilient courier PWAs (IndexedDB ULID Outbox) for canvas Proof-of-Delivery signatures, QR manifest scanning, and locked COD reconciliation.',
                ],
            ],
            'SECTOR_07_EDTECH_LMS' => [
                'sector_code' => 'SECTOR_07_EDTECH_LMS',
                'schema_types' => ['Course', 'EducationalOrganization', 'CourseInstance'],
                'statutory_standards' => ['PDDIKTI Feeder Ready', 'Server-Synced Anti-Cheat Exam Timer', 'Verifikasi Sertifikat SHA-256'],
                'offline_pwa_critical' => false,
                'tldr' => [
                    'id' => 'Arsitektur EdTech, SIAKAD Kampus & Pelatihan NeriahPro Project OS menggantikan puluhan modal lambat dengan matriks nilai & presensi 2D cepat, ruang ujian bebas distraksi ber-timer sinkron server anti-manipulasi, pelaporan PDDIKTI, serta sertifikat kelulusan bersegel kriptografi SHA-256.',
                    'en' => 'NeriahPro Project OS EdTech, Campus SIAKAD & LMS Architecture replaces modal fatigue with inline 2D cohort gradebook matrices, distraction-free exam players with tamper-proof server-synced timers, PDDIKTI reporting schemas, and SHA-256 verifiable digital certificates.',
                ],
            ],
            'SECTOR_08_PROFESSIONAL_PSA' => [
                'sector_code' => 'SECTOR_08_PROFESSIONAL_PSA',
                'schema_types' => ['ProfessionalService', 'Service', 'OfferCatalog'],
                'statutory_standards' => ['SPK/PKS SHA-256 Scope Lock', 'PPh 23 Jasa Profesional', 'Digital BAST & SLA Escrow'],
                'offline_pwa_critical' => false,
                'tldr' => [
                    'id' => 'Arsitektur Jasa Profesional, Agensi & Konsultan Hukum (PSA) NeriahPro Project OS mengintegrasikan Kanban milestone dengan stopwatch jam kerja melayang anti-putus, konversi timesheet otomatis ke invoice termin & PPh 23, brankas dokumen klien terisolasi, serta persetujuan BAST Digital SHA-256.',
                    'en' => 'NeriahPro Project OS Professional Services, Agency & Legal PSA Architecture integrates milestone Kanban boards with persistent billable timers, automated timesheet-to-termin invoicing with withholding tax, isolated client portals, and SHA-256 Digital BAST sign-off.',
                ],
            ],
            'SECTOR_09_FNB_CULINARY' => [
                'sector_code' => 'SECTOR_09_FNB_CULINARY',
                'schema_types' => ['Restaurant', 'Menu', 'MenuItem', 'AcceptsReservations'],
                'statutory_standards' => ['PB1 / PBJT Restoran 10%', 'Resep HPP Gramasi Bahan Baku', 'SJPH Halal Traceability'],
                'offline_pwa_critical' => true,
                'tldr' => [
                    'id' => 'Arsitektur Kuliner, Restoran, Cloud Kitchen & KDS NeriahPro Project OS menghadirkan Kitchen Display System (KDS) Dark Mode berindikator SLA detik, peta meja interaktif pisah/gabung tagihan, pemotongan stok bahan baku gramasi otomatis saat bayar, serta antrean kasir Offline-First.',
                    'en' => 'NeriahPro Project OS F&B, Restaurant & Cloud Kitchen Architecture delivers forced-dark SLA Kitchen Display Systems (KDS), interactive split/merge floor table maps, automatic recipe-grammage inventory deduction upon payment, and offline-first POS resilience.',
                ],
            ],
            'SECTOR_10_MEDIA_TICKETING' => [
                'sector_code' => 'SECTOR_10_MEDIA_TICKETING',
                'schema_types' => ['Event', 'Ticket', 'Place'],
                'statutory_standards' => ['Pajak Hiburan Daerah', 'Redis + Row Lock Anti-Oversell War Tiket', 'Offline Gate QR Scanner'],
                'offline_pwa_critical' => true,
                'tldr' => [
                    'id' => 'Arsitektur Event Ticketing, Konser & Ekonomi Kreator NeriahPro Project OS menangani lonjakan puluhan ribu pembeli bersamaan (war tiket) tanpa overselling melalui kombinasi atomic lock kursi berbatas TTL dan PWA pemindai QR pintu masuk venue yang tetap akurat meski internet stadion padam.',
                    'en' => 'NeriahPro Project OS Event Ticketing, Venue & Creator Architecture handles high-concurrency ticket drops with zero overselling via TTL atomic seat locks and offline-capable venue gate QR scanners built on local IndexedDB ULID sync queues.',
                ],
            ],
            'SECTOR_11_HRIS_INTERNAL_OPS' => [
                'sector_code' => 'SECTOR_11_HRIS_INTERNAL_OPS',
                'schema_types' => ['EmployerAggregateRating', 'Organization', 'Occupation'],
                'statutory_standards' => ['PPh 21 TER (PP 58/2023)', 'BPJS Kesehatan & Ketenagakerjaan', 'UU Cipta Kerja PKWT/PKWTT', 'UU PDP Data Karyawan'],
                'offline_pwa_critical' => false,
                'tldr' => [
                    'id' => 'Arsitektur HRIS, Payroll PPh 21 TER & Operasional Internal NeriahPro Project OS mengotomasi penggajian ratusan karyawan dengan kalkulasi presisi PPh 21 Tarif Efektif Rata-Rata (TER), BPJS, presensi geofence anti-fake-GPS, enkripsi NIK/gaji sesuai UU PDP, serta slip gaji terkunci.',
                    'en' => 'NeriahPro Project OS HRIS, Payroll & Internal Ops Architecture automates enterprise payroll runs with exact Indonesian PPh 21 TER and BPJS statutory math, anti-spoof geofenced attendance, UU PDP encrypted salary/ID vaults, and immutable payslip ledgers.',
                ],
            ],
            'SECTOR_12_GOVTECH_NONPROFIT' => [
                'sector_code' => 'SECTOR_12_GOVTECH_NONPROFIT',
                'schema_types' => ['NGO', 'DonateAction', 'Project', 'MonetaryGrant'],
                'statutory_standards' => ['ISAK 35 / PSAK 45 Entitas Nonlaba', 'SPBE Arsitektur Pemerintahan', 'Dana Terikat vs Tidak Terikat', 'Audit Log Rantai SHA-256'],
                'offline_pwa_critical' => true,
                'tldr' => [
                    'id' => 'Arsitektur Yayasan, Lembaga Sosial, Rumah Ibadah & GovTech NeriahPro Project OS menegakkan akuntabilitas dana publik sesuai standar akuntansi ISAK 35 (pemisahan ketat Dana Terikat & Tidak Terikat), portal transparansi donatur real-time, pendataan relawan lapangan offline, dan rantai audit SHA-256.',
                    'en' => 'NeriahPro Project OS Non-Profit, NGO, Faith & GovTech Architecture enforces public fund accountability under ISAK 35 accounting standards (strict Restricted vs Unrestricted fund ledgers), real-time donor transparency portals, offline field surveys, and SHA-256 audit chains.',
                ],
            ],
        ];
    }

    /**
     * Resolve localized SEO/GEO metadata for a single sector (or global ALL overview).
     *
     * @return array<string, mixed>
     */
    public static function sectorCapsule(string $sectorCode, string $locale = 'id'): array
    {
        $loc = $locale === 'en' ? 'en' : 'id';
        $matrix = self::sectorSeoGeoAuthorityMatrix();

        if (isset($matrix[$sectorCode])) {
            $row = $matrix[$sectorCode];

            return [
                'sector_code' => $sectorCode,
                'schema_types' => $row['schema_types'],
                'statutory_standards' => $row['statutory_standards'],
                'offline_pwa_critical' => $row['offline_pwa_critical'],
                'tldr_text' => $row['tldr'][$loc] ?? $row['tldr']['id'],
            ];
        }

        return [
            'sector_code' => 'ALL',
            'schema_types' => ['SoftwareApplication', 'ProfessionalService', 'FAQPage', 'OfferCatalog', 'AggregateRating'],
            'statutory_standards' => ['PSAK 109 & ISAK 35', 'Permenkes 24/2022 & SatuSehat', 'PPh 21 TER', 'UU PDP No. 27/2022'],
            'offline_pwa_critical' => true,
            'tldr_text' => $loc === 'en'
                ? 'NeriahPro Project OS v11.0 is an enterprise deterministic software architecture compiler and turnkey engineering studio spanning 12 Functional Sectors, 36 Operational UIs, 6 Public UX Presets, PostgreSQL 16 Strict ULID, Pessimistic Locking, UU PDP Privacy Vaults, and SHA-256 Scope Contracts.'
                : 'NeriahPro Project OS v11.0 adalah sistem operasi kompilasi arsitektur perangkat lunak deterministik dan studio rekayasa terima-beres lintas 12 Sektor Fungsional, 36 Visual UI Operasional, 6 Preset UX Publik, PostgreSQL 16 Strict ULID, Pessimistic Lock, Brankas Privasi UU PDP, dan Kontrak SHA-256.',
        ];
    }

    /**
     * High-Signal Semantic HTML Comparison Table for GEO Citation Magnets (Perplexity / SearchGPT / Gemini).
     *
     * @return array<int, array<string, string>>
     */
    public static function comparisonMatrixRows(string $locale = 'id'): array
    {
        $isEn = $locale === 'en';

        return [
            [
                'dimension' => $isEn ? 'Primary Key & Pagination' : 'Primary Key & Pagination Database',
                'generic' => $isEn ? 'AUTO_INCREMENT id() + slow OFFSET paginate() O(N)' : 'AUTO_INCREMENT id() bocor volume + OFFSET paginate() lambat O(N)',
                'neriahpro' => $isEn ? '26-char CHAR(26) Strict ULID + Keyset cursorPaginate() O(1) <10ms' : 'Strict ULID CHAR(26) terdistribusi + Keyset cursorPaginate() O(1) <10ms',
            ],
            [
                'dimension' => $isEn ? 'Concurrency & Stock/Balance Safety' : 'Keamanan Transaksi Paralel & Stok/Saldo',
                'generic' => $isEn ? 'Race-condition prone PHP checks (negative stock & double booking)' : 'Cek saldo naif di PHP (rawan stok minus, saldo bocor & jadwal bentrok)',
                'neriahpro' => $isEn ? 'PostgreSQL 16 SELECT ... FOR UPDATE + GiST Exclusion + X-Idempotency-Key' : 'PostgreSQL 16 SELECT ... FOR UPDATE + GiST Exclusion + X-Idempotency-Key',
            ],
            [
                'dimension' => $isEn ? 'UU PDP No. 27/2022 & PII Search' : 'Kepatuhan UU PDP No. 27/2022 & Pencarian PII',
                'generic' => $isEn ? 'Plaintext NIK/Phone in DB or unsearchable encrypted blobs' : 'NIK/Telepon teks telanjang di DB atau terenkripsi tapi tidak bisa dicari',
                'neriahpro' => $isEn ? 'AES-256 Encrypted PII + HMAC-SHA256 Blind Index O(1) + Ledger-Safe Redaction' : 'Enkripsi PII AES-256 + Blind Index HMAC-SHA256 O(1) + Redaksi Aman Buku Besar',
            ],
            [
                'dimension' => $isEn ? 'UI/UX Architecture (Front vs Back)' : 'Kepastian Visual UI/UX (Publik vs Operasional)',
                'generic' => $isEn ? 'Generic AI-slop gradients, capsule buttons, & identical CRUD tables' : 'Template gradasi AI-slop, tombol kapsul murahan, & tabel CRUD generik seragam',
                'neriahpro' => $isEn ? 'Dual-Zone UI: 36 Sector Cockpits (Zone A) + 6 60fps Public Presets (Zone B)' : 'Dual-Zone UI: 36 Cockpit Sektoral (Zona A) + 6 Preset Publik 60fps (Zona B)',
            ],
            [
                'dimension' => $isEn ? 'Bad-Signal / Offline Field Resilience' : 'Ketahanan Sinyal Buruk / Offline Lapangan',
                'generic' => $isEn ? 'Transactions fail or duplicate when cellular connection drops' : 'Kasir/kurir lumpuh atau data terinput ganda saat sinyal seluler putus-nyambung',
                'neriahpro' => $isEn ? 'Local IndexedDB ULID Outbox Queue + Background Sync Idempotency' : 'Antrean Lokal IndexedDB ULID Outbox + Sinkronisasi Latar Idempoten Otomatis',
            ],
            [
                'dimension' => $isEn ? 'SEO, GEO & AI Search Discoverability' : 'Otoritas SEO Google & GEO Mesin Pencari AI',
                'generic' => $isEn ? 'Client-side SPA invisible to crawlers, missing Schema.org & llms.txt' : 'SPA kosong di mata crawler, tanpa struktur Schema.org spesifik & tanpa llms.txt',
                'neriahpro' => $isEn ? '12-Sector Multi-Graph JSON-LD + /llms.txt + IndexNow + Local 1200x630 OG' : 'Multi-Graph JSON-LD 12 Sektor + /llms.txt + IndexNow Instan + OG Lokal 1200x630',
            ],
            [
                'dimension' => $isEn ? 'Post-Handover SLA & Deployment Safety' : 'Garansi Pasca-BAST & Keamanan Rilis Produksi',
                'generic' => $isEn ? 'Endless scope-creep disputes & risky unbacked database migrations' : 'Sengketa revisi tanpa batas & migrasi database nekat tanpa backup otomatis',
                'neriahpro' => $isEn ? 'SHA-256 Scope Lock + AI SLA Bug vs Paid Delta Triage + Atomic pg_dump Rollback' : 'Kunci Scope SHA-256 + Triase AI Garansi Bug vs Addendum + Rollback pg_dump Atomik',
            ],
        ];
    }

    /**
     * 6 Final Enterprise & Statutory Zero-Failure Production Features (SEO-GEO.md Bagian 2).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function sixFinalZeroFailureModules(string $locale = 'id'): array
    {
        $isEn = $locale === 'en';

        return [
            [
                'number' => 1,
                'code' => 'PRIMITIVE_OFFLINE_OUTBOX_PWA',
                'badge' => 'BAD-SIGNAL RESILIENCE',
                'title' => $isEn ? 'Offline-First PWA & Bad-Signal IndexedDB Outbox' : 'Offline-First PWA & Antrean Outbox IndexedDB Tahan Sinyal Buruk',
                'summary' => $isEn
                    ? 'Generates client-side 26-char ULIDs in IndexedDB when offline (POS, Venue Gate, Field NGO) and replays atomically with X-Idempotency-Key upon reconnection.'
                    : 'Membangkitkan ULID 26-karakter di IndexedDB lokal saat internet mati (Kasir POS, Gate Tiket, Survei Desa) dan menyinkronkan otomatis dengan X-Idempotency-Key saat sinyal pulih.',
            ],
            [
                'number' => 2,
                'code' => 'PRIMITIVE_UU_PDP_PRIVACY_VAULT',
                'badge' => 'UU PDP NO. 27/2022',
                'title' => $isEn ? 'UU PDP Field-Level Encryption, Blind Index & Ledger-Safe Redaction' : 'Brankas Privasi UU PDP, Blind Index O(1) & Redaksi Aman Buku Besar',
                'summary' => $isEn
                    ? 'Encrypts NIK/Passport/Phone at rest via HasEncryptedPiiWithBlindIndex while keeping O(1) exact search via HMAC-SHA256 blind indexes and 1-click Right-to-Erasure.'
                    : 'Mengenkripsi NIK/Paspor/Telepon via HasEncryptedPiiWithBlindIndex dengan pencarian cepat O(1) HMAC-SHA256 serta fitur 1-klik Ekspor & Hapus Data Pribadi tanpa merusak jurnal akuntansi.',
            ],
            [
                'number' => 3,
                'code' => 'SLA_POST_BAST_WARRANTY_TRIAGE',
                'badge' => 'ANTI-DISPUTE SLA',
                'title' => $isEn ? 'Contractual SLA Bug-Warranty vs Paid Delta v1.1 Tracker' : 'Pelacak Garansi Bug SLA Pasca-BAST vs Fitur Baru Berbayar (Delta v1.1)',
                'summary' => $isEn
                    ? 'Automatically activates a 90-day SLA warranty upon Digital BAST and classifies client tickets against the SHA-256 Scope Lock into Free Bugfix Prompt vs Paid Change-Order.'
                    : 'Aktif otomatis 90 hari pasca BAST Digital; mengaudit tiket klien terhadap SHA-256 Scope v1.0 menjadi Perbaikan Bug Gratis (lengkap Prompt Developer) atau Addendum Delta v1.1.',
            ],
            [
                'number' => 4,
                'code' => 'ZERO_DOWNTIME_PG_DUMP_ROLLBACK',
                'badge' => 'ATOMIC ROLLBACK',
                'title' => $isEn ? 'Pre-Migration Atomic pg_dump Snapshot & 1-Click Rollback' : 'Snapshot pg_dump Atomik Pra-Migrasi & Rollback 1-Klik',
                'summary' => $isEn
                    ? 'Every ./deploy.sh migration captures a timestamped database snapshot in storage/backups/ (7-day retention) and supports instant ./deploy.sh rollback if healthchecks fail.'
                    : 'Setiap migrasi ./deploy.sh otomatis mencadangkan snapshot database ke storage/backups/ (retensi 7 hari) dan mendukung ./deploy.sh rollback instan jika healthcheck gagal.',
            ],
            [
                'number' => 5,
                'code' => 'SANDBOX_WEBHOOK_CHAOS_SIMULATOR',
                'badge' => 'NON-PROD SANDBOX',
                'title' => $isEn ? 'Local Webhook Simulator & Payment Chaos Console' : 'Simulator Webhook Lokal & Konsol Uji Idempotensi Pembayaran',
                'summary' => $isEn
                    ? 'Test QRIS Success, Payment Expiry, and 2x Duplicate Webhook Attacks locally without live gateway credentials; strictly locked (403) in production.'
                    : 'Uji skenario Lunas QRIS, Kedaluwarsa, hingga Serangan Webhook Ganda 2x tanpa kredensial bank asli; otomatis terkunci rapat (HTTP 403) di server production.',
            ],
            [
                'number' => 6,
                'code' => 'MULTI_ENTITY_HOLDING_CONSOLIDATION',
                'badge' => 'HOLDING ROLL-UP',
                'title' => $isEn ? 'Multi-Entity Holding, Branch Isolation & Inter-Company Elimination' : 'Konsolidasi Holding Multi-PT, Isolasi Cabang & Eliminasi Antar-Entitas',
                'summary' => $isEn
                    ? 'Self-referencing ULID hierarchy (Holding -> Subsidiary -> Branch) rolling up consolidated net financials with inter-company elimination while isolating branch staff.'
                    : 'Hierarki ULID bertingkat (Holding -> Anak Usaha -> Cabang) yang menggulung laporan konsolidasi bersih dengan eliminasi transaksi antar-cabang namun tetap mengisolasi staf cabang.',
            ],
        ];
    }

    /**
     * Master 20-Pillar Paripurna Verification Matrix across 8 Enterprise Dimensions (SEO-GEO.md Final Lock).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function twentyPillarsParipurnaMatrix(): array
    {
        return [
            ['pillar' => 'P01', 'dimension' => 'Core Architecture', 'title' => '12 Sectoral Taxonomies, 9 Stack Adapters (M1–S2) & Composable Primitives', 'status' => '100% LOCKED'],
            ['pillar' => 'P02', 'dimension' => 'Core Architecture', 'title' => 'PostgreSQL 16 Strict CHAR(26) ULID Primary Keys & Keyset Cursor Pagination O(1)', 'status' => '100% LOCKED'],
            ['pillar' => 'P03', 'dimension' => 'Core Architecture', 'title' => 'Pessimistic Row Locking (SELECT FOR UPDATE), GiST Exclusion & DECIMAL(19,4) Ledger', 'status' => '100% LOCKED'],
            ['pillar' => 'P04', 'dimension' => 'Visual & Dual-Zone UI', 'title' => '36 Operational Sector UI Blueprints (Zone A Dense Back-Stage Cockpits)', 'status' => '100% LOCKED'],
            ['pillar' => 'P05', 'dimension' => 'Visual & Dual-Zone UI', 'title' => '6 Scrollytelling & Public UX Presets (Zone B 60fps Compositor-Only + Reduced Motion)', 'status' => '100% LOCKED'],
            ['pillar' => 'P06', 'dimension' => 'Visual & Dual-Zone UI', 'title' => 'Clean Solid Brutalism (Zero Capsule/Pill, Zero Gaudy Gradients, WCAG AAA Light/Dark)', 'status' => '100% LOCKED'],
            ['pillar' => 'P07', 'dimension' => 'AI & Context Engine', 'title' => 'KAG + CAG + RAG Hybrid Memory + 44 Whitelisted Native AI Tool Functions', 'status' => '100% LOCKED'],
            ['pillar' => 'P08', 'dimension' => 'AI & Context Engine', 'title' => '7-Phase Prompt Sequencer, SYMBOL_MANIFEST.json (70% Token Save) & Live MCP Bridge', 'status' => '100% LOCKED'],
            ['pillar' => 'P09', 'dimension' => 'Security & Privacy', 'title' => 'AgentShield Red-Team Policy, Document Threat Sanitizer & Canary IP Watermark', 'status' => '100% LOCKED'],
            ['pillar' => 'P10', 'dimension' => 'Security & Privacy', 'title' => 'Immutable SHA-256 Cryptographic Audit Log Chain & RBAC Least-Privilege Isolation', 'status' => '100% LOCKED'],
            ['pillar' => 'P11', 'dimension' => 'Security & Privacy', 'title' => 'UU PDP No. 27/2022 Encrypted PII Vault, HMAC-SHA256 Blind Index & Ledger-Safe Redaction', 'status' => '100% LOCKED'],
            ['pillar' => 'P12', 'dimension' => 'Commercial & SaaS', 'title' => 'Satu Pintu Unified Commercial Gate (7 Retail SaaS Tiers + 3 Mitra Turnkey Contracts)', 'status' => '100% LOCKED'],
            ['pillar' => 'P13', 'dimension' => 'Commercial & SaaS', 'title' => 'Dual-Gateway Idempotent Billing (Midtrans + Global), Entitlements & Affiliate Ledger', 'status' => '100% LOCKED'],
            ['pillar' => 'P14', 'dimension' => 'Commercial & SaaS', 'title' => 'Cryptographic SPK Scope Freeze, Paid Delta v1.1 Calculator & Digital BAST Sign-Off', 'status' => '100% LOCKED'],
            ['pillar' => 'P15', 'dimension' => 'Commercial & SaaS', 'title' => 'Post-BAST 90-Day Contractual SLA Bug-Warranty Triage vs Out-of-Scope Addendum', 'status' => '100% LOCKED'],
            ['pillar' => 'P16', 'dimension' => 'SEO & GEO Authority', 'title' => '12-Sector Multi-Graph Schema.org JSON-LD, Semantic Answer Capsules & Local 1200x630 OG', 'status' => '100% LOCKED'],
            ['pillar' => 'P17', 'dimension' => 'SEO & GEO Authority', 'title' => 'Native /llms.txt & /llms-full.txt AI Citation Engine + IndexNow + Smart Bot robots.txt', 'status' => '100% LOCKED'],
            ['pillar' => 'P18', 'dimension' => 'Field & Enterprise Ops', 'title' => 'Offline-First PWA IndexedDB ULID Outbox Queue & Background Sync Idempotency', 'status' => '100% LOCKED'],
            ['pillar' => 'P19', 'dimension' => 'Field & Enterprise Ops', 'title' => 'Multi-Entity Holding -> Subsidiary -> Branch Roll-Up & Inter-Company Elimination', 'status' => '100% LOCKED'],
            ['pillar' => 'P20', 'dimension' => 'DevOps & Zero-Downtime', 'title' => 'Pre-Migration Atomic pg_dump Snapshot Guard, 1-Click Rollback & Sandbox Chaos Console', 'status' => '100% LOCKED'],
        ];
    }
}
