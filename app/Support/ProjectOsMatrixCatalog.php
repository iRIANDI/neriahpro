<?php

namespace App\Support;

use App\Models\PosPrimitive;
use App\Models\PosRulePack;
use App\Models\PosSector;
use App\Models\PosSectorPrimitive;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class ProjectOsMatrixCatalog
{
    /**
     * Ensure all 12 sectors, 12 core primitives, sector-primitive pivots, and rule packs exist in DB.
     */
    public static function ensureSeeded(): void
    {
        try {
            if (! Schema::hasTable('pos_sectors') || ! Schema::hasTable('pos_primitives')) {
                return;
            }

            $presetsReady = ! Schema::hasTable('pos_stack_presets')
                || \App\Models\PosStackPreset::query()->count() >= 9;
            $ultimateColsReady = ! Schema::hasColumn('pos_primitives', 'deterministic_formulas')
                || PosPrimitive::query()->whereNotNull('deterministic_formulas')->count() >= 12;
            $sectorUiColsReady = ! Schema::hasColumn('pos_sectors', 'ui_ux_archetypes')
                || PosSector::query()->whereNotNull('ui_ux_archetypes')->count() >= 12;

            if (
                PosSector::query()->count() >= 12
                && PosPrimitive::query()->count() >= 12
                && PosRulePack::query()->count() >= 6
                && $presetsReady
                && $ultimateColsReady
                && $sectorUiColsReady
            ) {
                return;
            }

            self::seedDatabase();
        } catch (\Throwable) {
            // Safe zero-downtime fallback prior to migration execution
        }
    }

    /**
     * Idempotently seed the complete 12-Sector Composable Blueprint Matrix, 9 Stack Presets & Anti-Slop Rule Packs.
     */
    public static function seedDatabase(): void
    {
        $hasUltimateCols = Schema::hasTable('pos_primitives') && Schema::hasColumn('pos_primitives', 'deterministic_formulas');
        $primitiveMap = [];
        foreach (self::primitives() as $primData) {
            if (! $hasUltimateCols) {
                unset(
                    $primData['deterministic_formulas'],
                    $primData['saga_and_webhook_contracts'],
                    $primData['ui_screen_archetypes'],
                    $primData['acceptance_test_scenarios']
                );
            }
            $primitive = PosPrimitive::query()->updateOrCreate(
                ['code' => $primData['code']],
                $primData
            );
            $primitiveMap[$primData['code']] = $primitive;
        }

        $hasSectorUiCols = Schema::hasTable('pos_sectors') && Schema::hasColumn('pos_sectors', 'ui_ux_archetypes');
        foreach (self::sectors() as $sectorDef) {
            $mandatoryCodes = $sectorDef['mandatory_primitives'] ?? [];
            $optionalCodes = $sectorDef['optional_primitives'] ?? [];
            unset($sectorDef['mandatory_primitives'], $sectorDef['optional_primitives']);
            if (! $hasSectorUiCols) {
                unset($sectorDef['ui_ux_archetypes'], $sectorDef['realistic_seeder_contract']);
            }

            $sector = PosSector::query()->updateOrCreate(
                ['code' => $sectorDef['code']],
                $sectorDef
            );

            $order = 1;
            foreach ($mandatoryCodes as $pCode) {
                if (isset($primitiveMap[$pCode])) {
                    PosSectorPrimitive::query()->updateOrCreate(
                        [
                            'sector_id' => $sector->id,
                            'primitive_id' => $primitiveMap[$pCode]->id,
                        ],
                        [
                            'is_mandatory' => true,
                            'sort_order' => $order++,
                        ]
                    );
                }
            }

            foreach ($optionalCodes as $pCode) {
                if (isset($primitiveMap[$pCode])) {
                    PosSectorPrimitive::query()->updateOrCreate(
                        [
                            'sector_id' => $sector->id,
                            'primitive_id' => $primitiveMap[$pCode]->id,
                        ],
                        [
                            'is_mandatory' => false,
                            'sort_order' => $order++,
                        ]
                    );
                }
            }
        }

        foreach (self::rulePacks() as $ruleData) {
            PosRulePack::query()->updateOrCreate(
                ['slug' => $ruleData['slug']],
                $ruleData
            );
        }

        PosStackPresetCatalog::seedDatabase();
        PosSectorUiArchetypeCatalog::enrichDatabaseSectors();

        Cache::forget(PosSector::CACHE_KEY);
    }

    /**
     * 12 Functional Domain-Driven Business Sectors.
     */
    public static function sectors(): array
    {
        $baseSectors = [
            [
                'code' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
                'sector_number' => 1,
                'name' => [
                    'id' => '1. Perdagangan, Ritel & Distribusi (Commerce & Omnichannel)',
                    'en' => '1. Commerce, Retail & Distribution (Omnichannel)',
                ],
                'description' => [
                    'id' => 'Ritel fisik (POS), e-commerce B2C, grosir B2B bertingkat, dan social commerce multi-gudang.',
                    'en' => 'Physical retail (POS), B2C e-commerce, tiered B2B wholesale, and multi-warehouse social commerce.',
                ],
                'domain_terminology' => [
                    'customer' => 'Buyer / Reseller',
                    'item' => 'SKU / Product Variant',
                    'transaction' => 'Sales Order',
                    'location' => 'Warehouse / Outlet',
                ],
                'blueprint_invariants' => [
                    'Multi-warehouse inventory ledger tanpa update kolom stock_qty secara langsung',
                    'SKU variant matrix dengan konversi satuan (UoM: 1 Box = N Pcs) & harga grosir bertingkat',
                    'Atomic stock reservation (SELECT ... FOR UPDATE) mencegah overselling saat flash sale',
                    'Mesin diskon bertingkat & alur retur/refund terhubung ke jurnal pembalik',
                ],
                'default_compliance_standards' => ['UU PDP No. 27/2022', 'PP 80/2019 PMSE Kemendag', 'PSAK 14 (Persediaan)', 'OWASP ASVS L2'],
                'detection_keywords' => ['toko', 'ritel', 'retail', 'pos', 'kasir', 'ecommerce', 'e-commerce', 'grosir', 'distributor', 'sku', 'katalog', 'belanja', 'checkout', 'cart', 'omnichannel', 'marketplace', 'apotek ritel'],
                'is_active' => true,
                'mandatory_primitives' => ['PRIMITIVE_CATALOG_SKU', 'PRIMITIVE_INVENTORY_LEDGER'],
                'optional_primitives' => ['PRIMITIVE_DOUBLE_ENTRY_LEDGER', 'PRIMITIVE_APPROVAL_WORKFLOW'],
            ],
            [
                'code' => 'SECTOR_02_HOSPITALITY_SPACE',
                'sector_number' => 2,
                'name' => [
                    'id' => '2. Perhotelan, Properti & Reservasi Aset (Hospitality, Travel & Space)',
                    'en' => '2. Hospitality, Property & Asset Reservation (Travel & Space)',
                ],
                'description' => [
                    'id' => 'Hotel, manajemen vila, sewa kendaraan/alat berat, coworking space, dan klub olahraga (sewa lapangan).',
                    'en' => 'Hotels, villa management, vehicle/heavy equipment rental, coworking spaces, and sports court booking.',
                ],
                'domain_terminology' => [
                    'customer' => 'Guest / Member',
                    'item' => 'Room / Court / Fleet Unit',
                    'transaction' => 'Reservation / Booking',
                    'location' => 'Property / Venue',
                ],
                'blueprint_invariants' => [
                    'Time-slot availability engine dengan validasi irisan waktu atomik (start_at < existing_end AND end_at > existing_start)',
                    'Pencegahan double-booking via Pessimistic Row Lock pada parent resource + Hold TTL otomatis',
                    'Tarif dinamis (seasonal, weekend, peak-hour pricing) dengan presisi DECIMAL(19,4)',
                    'State machine status kesiapan unit fisik (CLEAN, DIRTY, OCCUPIED, MAINTENANCE)',
                ],
                'default_compliance_standards' => ['UU PDP No. 27/2022', 'PSAK 73 (Sewa)', 'Pajak Daerah Perhotelan/Hiburan (PB1)', 'OWASP ASVS L2'],
                'detection_keywords' => ['hotel', 'villa', 'resort', 'penginapan', 'sewa lapangan', 'booking lapangan', 'lapangan', 'klub olahraga', 'klub badminton', 'gor', 'court', 'per jam', 'badminton', 'padel', 'futsal', 'tennis', 'coworking', 'rental mobil', 'alat berat', 'booking', 'reservasi', 'venue', 'gedung', 'properti'],
                'is_active' => true,
                'mandatory_primitives' => ['PRIMITIVE_TIME_SLOT_ENGINE'],
                'optional_primitives' => ['PRIMITIVE_DOUBLE_ENTRY_LEDGER', 'PRIMITIVE_SUBSCRIPTION_COHORT', 'PRIMITIVE_CATALOG_SKU', 'PRIMITIVE_INVENTORY_LEDGER'],
            ],
            [
                'code' => 'SECTOR_03_HEALTHCARE_CLINICAL',
                'sector_number' => 3,
                'name' => [
                    'id' => '3. Layanan Kesehatan & Klinis (Healthcare & Life Sciences)',
                    'en' => '3. Healthcare, Clinical & Life Sciences',
                ],
                'description' => [
                    'id' => 'Rumah sakit, klinik utama/pratama, apotek klinis, laboratorium diagnostik, dan telemedicine.',
                    'en' => 'Hospitals, clinics, clinical pharmacies, diagnostic laboratories, and telemedicine platforms.',
                ],
                'domain_terminology' => [
                    'customer' => 'Patient (Pasien)',
                    'item' => 'Clinical Procedure / Drug Batch',
                    'transaction' => 'Clinical Encounter / Episode',
                    'location' => 'Polyclinic / Ward / Pharmacy',
                ],
                'blueprint_invariants' => [
                    'Rekam Medis Elektronik (EMR/RME) append-only dengan cryptographic audit trail (koreksi hanya via addendum)',
                    'Antrean dokter-pasien & penjadwalan tindakan medis bebas bentrok slot',
                    'Manajemen resep farmasi dengan alokasi stok obat FEFO (First-Expired, First-Out) berbasis nomor batch & tanggal kedaluwarsa',
                    'Alur verifikasi & rekonsiliasi klaim asuransi / penjamin (BPJS / Swasta)',
                ],
                'default_compliance_standards' => ['Permenkes No. 24/2022 (RME)', 'SATUSEHAT FHIR HL7 Interoperability', 'UU PDP Data Kesehatan Spesifik', 'OWASP ASVS L3'],
                'detection_keywords' => ['rumah sakit', 'klinik', 'dokter', 'pasien', 'rekam medis', 'emr', 'rme', 'apotek', 'farmasi', 'obat', 'resep', 'laboratorium', 'telemedicine', 'kesehatan', 'medis', 'bpjs kesehatan'],
                'is_active' => true,
                'mandatory_primitives' => ['PRIMITIVE_CLINICAL_EMR_AUDIT', 'PRIMITIVE_TIME_SLOT_ENGINE', 'PRIMITIVE_INVENTORY_LEDGER', 'PRIMITIVE_CATALOG_SKU'],
                'optional_primitives' => ['PRIMITIVE_DOUBLE_ENTRY_LEDGER', 'PRIMITIVE_APPROVAL_WORKFLOW'],
            ],
            [
                'code' => 'SECTOR_04_FINTECH_LEDGER',
                'sector_number' => 4,
                'name' => [
                    'id' => '4. Keuangan, Akuntansi & Pembayaran (Fintech, Billing & Ledger)',
                    'en' => '4. Financial Services, Accounting & Billing (Fintech & Ledger)',
                ],
                'description' => [
                    'id' => 'Pembayaran digital, koperasi simpan pinjam/BPR, crowdfunding komersial, asuransi, dan multi-tenant SaaS billing.',
                    'en' => 'Digital payments, credit unions/microfinance, commercial crowdfunding, insurance, and multi-tenant SaaS billing.',
                ],
                'domain_terminology' => [
                    'customer' => 'Account Holder / Subscriber',
                    'item' => 'Financial Instrument / Plan',
                    'transaction' => 'Journal Entry / Settlement',
                    'location' => 'Ledger Book / Cost Center',
                ],
                'blueprint_invariants' => [
                    'Immutable double-entry bookkeeping (SUM(debit) === SUM(credit), dilarang UPDATE/DELETE saldo, koreksi wajib jurnal balik)',
                    'Wajib Idempotency-Key unik pada setiap mutasi pembayaran & webhook gateway',
                    'Rekonsiliasi mutasi bank/payment gateway otomatis dengan status UNMATCHED/MATCHED/DISPUTED',
                    'Mekanisme Escrow hold & multi-party split payment presisi DECIMAL(19,4) HALF_UP',
                ],
                'default_compliance_standards' => ['PSAK 71 / IFRS 9', 'POJK / PBI Tata Kelola Sistem Elektronik', 'PCI-DSS Tokenization Standard', 'UU PDP No. 27/2022'],
                'detection_keywords' => ['fintech', 'keuangan', 'akuntansi', 'buku besar', 'ledger', 'koperasi', 'simpan pinjam', 'bpr', 'billing', 'payment', 'pembayaran', 'escrow', 'asuransi', 'polis', 'wallet', 'dompet digital', 'saas billing'],
                'is_active' => true,
                'mandatory_primitives' => ['PRIMITIVE_DOUBLE_ENTRY_LEDGER', 'PRIMITIVE_SUBSCRIPTION_COHORT'],
                'optional_primitives' => ['PRIMITIVE_APPROVAL_WORKFLOW'],
            ],
            [
                'code' => 'SECTOR_05_INDUSTRIAL_MRP',
                'sector_number' => 5,
                'name' => [
                    'id' => '5. Manufaktur, Konstruksi & Rantai Pasok (Industrial, MRP & Engineering)',
                    'en' => '5. Manufacturing, Construction & Supply Chain (Industrial & MRP)',
                ],
                'description' => [
                    'id' => 'Pabrik pengolahan, perakitan, kontraktor proyek (EPC/konstruksi), dan agribisnis/perkebunan.',
                    'en' => 'Processing plants, assembly lines, EPC/construction contractors, and agribusiness/plantations.',
                ],
                'domain_terminology' => [
                    'customer' => 'Offtaker / Project Owner',
                    'item' => 'Raw Material / Finished Good / BoM',
                    'transaction' => 'Work Order / Progress Termin',
                    'location' => 'Plant / Job Site / Silo',
                ],
                'blueprint_invariants' => [
                    'Multi-level Bill of Materials (BoM) dengan rasio konversi bahan baku ke barang jadi & toleransi scrap/yield',
                    'State machine Work Order (WO) & pelacakan Work-in-Progress (WIP) lintas stasiun produksi',
                    'Kalkulasi HPP (COGS) aktual vs standar secara atomik saat penyelesaian produksi',
                    'Termin Progress Billing berbasis berita acara opname / persentase penyelesaian proyek',
                ],
                'default_compliance_standards' => ['ISO 9001 Batch Traceability', 'PSAK 14 & PSAK 72 (Kontrak Konstruksi)', 'SMK3 / K3L Audit Trail', 'OWASP ASVS L2'],
                'detection_keywords' => ['manufaktur', 'pabrik', 'produksi', 'bom', 'bill of material', 'work order', 'mrp', 'erp', 'konstruksi', 'kontraktor', 'epc', 'perkebunan', 'agribisnis', 'sawit', 'tambang', 'bahan baku', 'barang jadi', 'hpp'],
                'is_active' => true,
                'mandatory_primitives' => ['PRIMITIVE_BOM_WORK_ORDER', 'PRIMITIVE_INVENTORY_LEDGER', 'PRIMITIVE_CATALOG_SKU', 'PRIMITIVE_APPROVAL_WORKFLOW'],
                'optional_primitives' => ['PRIMITIVE_DOUBLE_ENTRY_LEDGER'],
            ],
            [
                'code' => 'SECTOR_06_LOGISTICS_FLEET',
                'sector_number' => 6,
                'name' => [
                    'id' => '6. Logistik, Armada & Pengiriman (Logistics, Fleet & Last-Mile)',
                    'en' => '6. Logistics, Fleet Management & Last-Mile Delivery',
                ],
                'description' => [
                    'id' => 'Ekspedisi kargo darat/laut/udara, kurir last-mile, manajemen armada kendaraan, dan freight forwarding.',
                    'en' => 'Cargo expedition, last-mile courier, fleet management, and international/domestic freight forwarding.',
                ],
                'domain_terminology' => [
                    'customer' => 'Shipper / Consignee',
                    'item' => 'Parcel / Container / Vehicle Unit',
                    'transaction' => 'Waybill (AWB) / Manifest',
                    'location' => 'Hub / Sorting Center / Waypoint',
                ],
                'blueprint_invariants' => [
                    'State machine manifes pengiriman & resi (AWB) dengan penjagaan urutan hub transit yang ketat',
                    'Kalkulasi chargeable weight deterministik: MAX(actual_weight_kg, (L*W*H)/volumetric_divisor)',
                    'Pelacakan waypoint/geofencing & Proof of Delivery (PoD) wajib foto, tanda tangan penerima, dan koordinat GPS',
                    'Penjadwalan preventive maintenance kendaraan berbasis ambang odometer/jam kerja mesin',
                ],
                'default_compliance_standards' => ['PM Perhubungan Penyelenggaraan Angkutan Barang', 'ISO 28000 Supply Chain Security', 'UU PDP No. 27/2022', 'OWASP ASVS L2'],
                'detection_keywords' => ['logistik', 'ekspedisi', 'kargo', 'cargo', 'kurir', 'pengiriman', 'armada', 'fleet', 'resi', 'awb', 'waybill', 'manifest', 'freight', 'forwarding', 'trucking', 'gudang transit', 'last mile'],
                'is_active' => true,
                'mandatory_primitives' => ['PRIMITIVE_LOGISTICS_MANIFEST_POD', 'PRIMITIVE_INVENTORY_LEDGER'],
                'optional_primitives' => ['PRIMITIVE_DOUBLE_ENTRY_LEDGER', 'PRIMITIVE_APPROVAL_WORKFLOW'],
            ],
            [
                'code' => 'SECTOR_07_EDTECH_MEMBERSHIP',
                'sector_number' => 7,
                'name' => [
                    'id' => '7. Pendidikan, Pelatihan & Komunitas (EdTech, LMS & Membership)',
                    'en' => '7. Education, Training & Community (EdTech, LMS & Membership)',
                ],
                'description' => [
                    'id' => 'Sekolah, universitas, bootcamp/lembaga kursus, klub pelatihan olahraga/hobi, dan lembaga sertifikasi profesi.',
                    'en' => 'Schools, universities, bootcamps/academies, sports coaching clubs, and professional certification bodies.',
                ],
                'domain_terminology' => [
                    'customer' => 'Student / Trainee / Club Member',
                    'item' => 'Course / Curriculum Module / Cohort',
                    'transaction' => 'Enrollment / Tuition Cycle',
                    'location' => 'Classroom / Virtual Cohort / Arena',
                ],
                'blueprint_invariants' => [
                    'Struktur kurikulum berjenjang dengan prasyarat kelulusan modul (prerequisite guard)',
                    'Manajemen cohort/kelas paralel, presensi sesi, dan buku nilai (gradebook) berjejak audit',
                    'Penjadwalan ruangan & instruktur/pelatih bebas bentrok waktu',
                    'Manajemen SPP / iuran keanggotaan berulang (recurring membership) dengan aturan grace period otomatis',
                ],
                'default_compliance_standards' => ['UU PDP Perlindungan Data Pelajar/Anak', 'Standar Skema PDDikti / Dapodik', 'BNSP Sertifikasi Kompetensi', 'OWASP ASVS L2'],
                'detection_keywords' => ['sekolah', 'universitas', 'kampus', 'akademik', 'lms', 'edtech', 'kursus', 'bootcamp', 'pelatihan', 'sertifikasi', 'siswa', 'mahasiswa', 'kurikulum', 'kelas', 'cohort', 'klub', 'komunitas', 'membership'],
                'is_active' => true,
                'mandatory_primitives' => ['PRIMITIVE_SUBSCRIPTION_COHORT', 'PRIMITIVE_TIME_SLOT_ENGINE'],
                'optional_primitives' => ['PRIMITIVE_DOUBLE_ENTRY_LEDGER', 'PRIMITIVE_CATALOG_SKU'],
            ],
            [
                'code' => 'SECTOR_08_PROFESSIONAL_PSA',
                'sector_number' => 8,
                'name' => [
                    'id' => '8. Jasa Profesional & Agensi (Professional Services & PSA)',
                    'en' => '8. Professional Services Automation & Agencies (PSA)',
                ],
                'description' => [
                    'id' => 'Firma hukum, konsultan pajak/akuntan/IT, agensi kreatif, biro arsitek, dan software house.',
                    'en' => 'Law firms, tax/IT consultancies, creative agencies, architecture studios, and software houses.',
                ],
                'domain_terminology' => [
                    'customer' => 'Client Organization',
                    'item' => 'Deliverable / Milestone / Billable Hour',
                    'transaction' => 'Statement of Work (SoW) / Retainer',
                    'location' => 'Practice Desk / Project Workspace',
                ],
                'blueprint_invariants' => [
                    'Manajemen proyek berbasis milestone WBS dengan penguncian ruang lingkup (SHA-256 Scope Lock)',
                    'Pencatatan timesheet & billable hours yang otomatis memotong kuota jam retainer atau menerbitkan tagihan termin',
                    'Repositori dokumen kerja berversi (versioned deliverables) dengan kontrol akses per klien',
                    'Multi-stage Client Approval Workflow untuk berita acara serah terima (BAST) & change request',
                ],
                'default_compliance_standards' => ['PSAK 72 (Pengakuan Pendapatan Kontrak)', 'UU ITE Dokumen & Tanda Tangan Elektronik', 'UU PDP Kerahasiaan Klien (NDA)', 'OWASP ASVS L2'],
                'detection_keywords' => ['firma hukum', 'law firm', 'notaris', 'konsultan', 'agensi', 'agency', 'software house', 'arsitek', 'timesheet', 'retainer', 'milestone', 'bast', 'spk', 'jasa profesional', 'audit'],
                'is_active' => true,
                'mandatory_primitives' => ['PRIMITIVE_APPROVAL_WORKFLOW', 'PRIMITIVE_TIME_SLOT_ENGINE'],
                'optional_primitives' => ['PRIMITIVE_DOUBLE_ENTRY_LEDGER', 'PRIMITIVE_SUBSCRIPTION_COHORT'],
            ],
            [
                'code' => 'SECTOR_09_FOOD_BEVERAGE',
                'sector_number' => 9,
                'name' => [
                    'id' => '9. Kuliner, Restoran & Cloud Kitchen (Food & Beverage)',
                    'en' => '9. Culinary, Restaurant & Cloud Kitchen (Food & Beverage)',
                ],
                'description' => [
                    'id' => 'Restoran dine-in, jaringan kafe/coffee shop, katering korporat, cloud kitchen, dan waralaba (franchise) F&B.',
                    'en' => 'Dine-in restaurants, cafe chains, corporate catering, cloud kitchens, and F&B franchises.',
                ],
                'domain_terminology' => [
                    'customer' => 'Diner / Guest',
                    'item' => 'Menu Item / Modifier / Recipe',
                    'transaction' => 'Table Bill / Kitchen Ticket',
                    'location' => 'Outlet / Kitchen Station (Bar/Hot)',
                ],
                'blueprint_invariants' => [
                    'Manajemen denah meja, open tab, serta operasi Split Bill & Merge Bill yang 100% atomik tanpa selisih pembulatan',
                    'Kitchen Display System (KDS) station routing otomatis (memecah 1 pesanan ke tiket Bar, Dapur Utama, dan Pastry)',
                    'Recipe-level inventory depletion: penjualan 1 porsi menu otomatis memotong stok bahan baku sesuai resep dengan urutan FEFO',
                    'Matriks modifier & add-on menu (level gula, ukuran, ekstra topping) yang mempengaruhi harga & pemakaian bahan baku',
                ],
                'default_compliance_standards' => ['Pajak Restoran Daerah (PB1) & Service Charge', 'BPOM / Sertifikasi Halal SJPH Traceability', 'PSAK 14 FEFO Perishable Inventory', 'OWASP ASVS L2'],
                'detection_keywords' => ['restoran', 'restaurant', 'kafe', 'cafe', 'coffee', 'kopi', 'kuliner', 'f&b', 'fnb', 'cloud kitchen', 'katering', 'catering', 'dapur', 'kds', 'meja', 'split bill', 'resep makanan', 'franchise kuliner'],
                'is_active' => true,
                'mandatory_primitives' => ['PRIMITIVE_CATALOG_SKU', 'PRIMITIVE_BOM_WORK_ORDER', 'PRIMITIVE_INVENTORY_LEDGER'],
                'optional_primitives' => ['PRIMITIVE_DOUBLE_ENTRY_LEDGER'],
            ],
            [
                'code' => 'SECTOR_10_MEDIA_TICKETING',
                'sector_number' => 10,
                'name' => [
                    'id' => '10. Media, Kreator & Penyiaran (Media, Entertainment & Ticketing)',
                    'en' => '10. Media, Creator Economy, Streaming & Event Ticketing',
                ],
                'description' => [
                    'id' => 'Event organizer (tiket konser/pertandingan), manajemen talenta/kreator, portal berita premium, dan streaming.',
                    'en' => 'Event organizers (concert/sports ticketing), talent management, paywalled news portals, and streaming platforms.',
                ],
                'domain_terminology' => [
                    'customer' => 'Attendee / Audience / Subscriber',
                    'item' => 'Seat Ticket / Digital Asset / Episode',
                    'transaction' => 'Ticket Pass / Royalty Statement',
                    'location' => 'Stadium Section / Streaming Channel',
                ],
                'blueprint_invariants' => [
                    'High-concurrency seat/ticket locking (SELECT ... FOR UPDATE + 10m Hold TTL) tahan lonjakan ribuan request per detik',
                    'Mesin pembagian royalti & komisi kreator/promotor otomatis ke buku besar terpisah',
                    'Digital Asset Management (DAM) dengan signed URL berbatas waktu, watermark, & validasi hak akses (entitlement/paywall)',
                    'Anti-scalping QR/pass check-in sekali pakai dengan tanda tangan kriptografis HMAC-SHA256',
                ],
                'default_compliance_standards' => ['UU Hak Cipta & Royalti Digital', 'Pajak Hiburan Daerah & PPh 23 Royalti', 'UU PDP No. 27/2022', 'OWASP ASVS L2'],
                'detection_keywords' => ['tiket', 'ticketing', 'konser', 'event organizer', 'eo', 'stadion', 'kursi', 'seat', 'media', 'berita', 'portal berita', 'streaming', 'kreator', 'royalti', 'talenta', 'paywall', 'penyiaran'],
                'is_active' => true,
                'mandatory_primitives' => ['PRIMITIVE_HIGH_CONCURRENCY_TICKETING', 'PRIMITIVE_TIME_SLOT_ENGINE', 'PRIMITIVE_SUBSCRIPTION_COHORT'],
                'optional_primitives' => ['PRIMITIVE_DOUBLE_ENTRY_LEDGER'],
            ],
            [
                'code' => 'SECTOR_11_HRIS_INTERNAL_OPS',
                'sector_number' => 11,
                'name' => [
                    'id' => '11. Sumber Daya Manusia & Operasional Internal (HRIS, Payroll & Internal Ops)',
                    'en' => '11. Human Resources, Payroll & Internal Operations (HRIS)',
                ],
                'description' => [
                    'id' => 'Fondasi horizontal perusahaan: struktur organisasi, absensi shift/roster, payroll PPh 21/BPJS, reimbursement, & aset kantor.',
                    'en' => 'Horizontal enterprise backbone: org hierarchy, shift/roster attendance, PPh 21/BPJS payroll, reimbursements, & IT assets.',
                ],
                'domain_terminology' => [
                    'customer' => 'Employee / Department',
                    'item' => 'Shift Roster / Payroll Component / Office Asset',
                    'transaction' => 'Payroll Run / Reimbursement Claim',
                    'location' => 'Branch Office / Cost Center',
                ],
                'blueprint_invariants' => [
                    'Hierarki struktur organisasi & matriks persetujuan (approval matrix) berbasis jabatan dan plafon nominal',
                    'Manajemen jadwal shift/roster, lembur, cuti, dan validasi absensi geofence',
                    'Mesin kalkulasi payroll deterministik (gaji pokok, tunjangan tetap/tidak tetap, potongan, PPh 21 TER, BPJS TK & Kesehatan) dengan snapshot slip immutable',
                    'Alur klaim reimbursement berjenjang & pelacakan serah-terima aset inventaris kantor (BAST aset)',
                ],
                'default_compliance_standards' => ['UU Ketenagakerjaan / PP Pengupahan', 'PMK Tarif Efektif Rata-Rata (TER) PPh 21', 'Regulasi BPJS Ketenagakerjaan & Kesehatan', 'UU PDP Data Karyawan'],
                'detection_keywords' => ['hris', 'hrd', 'sdm', 'karyawan', 'pegawai', 'payroll', 'gaji', 'penggajian', 'absensi', 'presensi', 'shift', 'roster', 'cuti', 'lembur', 'reimbursement', 'pph 21', 'bpjs', 'aset kantor'],
                'is_active' => true,
                'mandatory_primitives' => ['PRIMITIVE_HRIS_PAYROLL_ROSTER', 'PRIMITIVE_APPROVAL_WORKFLOW'],
                'optional_primitives' => ['PRIMITIVE_DOUBLE_ENTRY_LEDGER'],
            ],
            [
                'code' => 'SECTOR_12_GOVTECH_NONPROFIT',
                'sector_number' => 12,
                'name' => [
                    'id' => '12. Sektor Publik, NGO & Organisasi Nirlaba (GovTech, Foundation & Non-Profit)',
                    'en' => '12. Public Sector, NGO, Philanthropy & Non-Profit (GovTech)',
                ],
                'description' => [
                    'id' => 'Yayasan sosial, lembaga filantropi/zakat/donasi, organisasi keagamaan/pelayanan, dan portal layanan publik.',
                    'en' => 'Social foundations, philanthropic/charity funds, faith-based organizations, and public service portals.',
                ],
                'domain_terminology' => [
                    'customer' => 'Donor / Beneficiary / Citizen',
                    'item' => 'Program Fund / Grant / Aid Package',
                    'transaction' => 'Donation Receipt / Disbursement Voucher',
                    'location' => 'Program Region / Chapter',
                ],
                'blueprint_invariants' => [
                    'Fund Accounting ketat: pemisahan mutlak saldo dana terikat (RESTRICTED) vs tidak terikat (UNRESTRICTED) per program',
                    'Pelacakan penyaluran dana (disbursement tracking) dari kas program hingga bukti tanda terima penerima manfaat',
                    'Pengukuran indikator dampak (impact metrics) terverifikasi dan laporan akuntabilitas publik real-time',
                    'Manajemen relawan, donatur tetap, dan kuitansi donasi berstempel hash SHA-256 anti-manipulasi',
                ],
                'default_compliance_standards' => ['ISAK 35 (Pelaporan Entitas Berorientasi Nonlaba)', 'PSAK 109 (Akuntansi Zakat/Infak/Sedekah)', 'UU Yayasan & Keterbukaan Informasi Publik', 'UU PDP No. 27/2022'],
                'detection_keywords' => ['yayasan', 'ngo', 'nirlaba', 'nonprofit', 'non-profit', 'donasi', 'filantropi', 'zakat', 'infak', 'wakaf', 'gereja', 'masjid', 'pelayanan', 'relawan', 'volunteer', 'bantuan sosial', 'govtech', 'pemerintah', 'desa'],
                'is_active' => true,
                'mandatory_primitives' => ['PRIMITIVE_DOUBLE_ENTRY_LEDGER', 'PRIMITIVE_FUND_DISBURSEMENT_IMPACT', 'PRIMITIVE_APPROVAL_WORKFLOW'],
                'optional_primitives' => ['PRIMITIVE_SUBSCRIPTION_COHORT'],
            ],
        ];

        $uiArchetypes = PosSectorUiArchetypeCatalog::sectorUiArchetypes();
        $realisticSeeders = PosSectorUiArchetypeCatalog::sectorRealisticSeeders();

        return array_map(static function (array $sector) use ($uiArchetypes, $realisticSeeders): array {
            $code = $sector['code'] ?? '';
            $sector['ui_ux_archetypes'] = $uiArchetypes[$code] ?? ($sector['ui_ux_archetypes'] ?? []);
            $sector['realistic_seeder_contract'] = $realisticSeeders[$code] ?? ($sector['realistic_seeder_contract'] ?? []);

            return $sector;
        }, $baseSectors);
    }

    /**
     * 12 Composable Cross-Sector Core Primitives (7 Universal Core + 5 Specialized Domain Engines).
     */
    public static function primitives(): array
    {
        $basePrimitives = [
            // 1. PRIMITIVE_CATALOG_SKU
            [
                'code' => 'PRIMITIVE_CATALOG_SKU',
                'name' => [
                    'id' => 'Katalog Produk, Matriks Varian SKU & Harga Grosir Bertingkat',
                    'en' => 'Product Catalog, SKU Variant Matrix & Tiered Pricing',
                ],
                'category' => 'COMMERCE',
                'description' => [
                    'id' => 'Tabel products, product_variants, unit_conversions, dan price_tiers. Mendukung konversi satuan (1 Box = 12 Pcs) dan harga bertingkat.',
                    'en' => 'Tables products, product_variants, unit_conversions, and price_tiers supporting UoM conversion and wholesale tiers.',
                ],
                'detection_keywords' => ['katalog', 'produk', 'sku', 'varian', 'grosir', 'toko', 'pro shop', 'merchandise', 'menu', 'obat'],
                'schema_blueprints' => [
                    [
                        'table' => 'products',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'code', 'type' => 'VARCHAR(64)', 'nullable' => false, 'index' => true],
                            ['name' => 'name', 'type' => 'JSONB', 'nullable' => false],
                            ['name' => 'base_uom', 'type' => 'VARCHAR(24)', 'nullable' => false],
                            ['name' => 'is_perishable', 'type' => 'BOOLEAN', 'nullable' => false],
                            ['name' => 'status', 'type' => 'VARCHAR(32)', 'enum_ref' => 'CatalogItemStatus'],
                        ],
                        'unique_constraints' => [
                            ['name' => 'products_org_code_unique', 'rule' => 'UNIQUE (organization_id, code)'],
                        ],
                    ],
                    [
                        'table' => 'product_variants',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'product_id', 'type' => 'ULID', 'foreign' => 'products.id', 'index' => true],
                            ['name' => 'sku', 'type' => 'VARCHAR(64)', 'nullable' => false],
                            ['name' => 'attributes_json', 'type' => 'JSONB', 'nullable' => false],
                            ['name' => 'uom_code', 'type' => 'VARCHAR(24)', 'nullable' => false],
                            ['name' => 'base_uom_factor', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'base_price', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                        ],
                        'unique_constraints' => [
                            ['name' => 'variants_org_sku_unique', 'rule' => 'UNIQUE (organization_id, sku)'],
                        ],
                    ],
                    [
                        'table' => 'price_tiers',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'product_variant_id', 'type' => 'ULID', 'foreign' => 'product_variants.id', 'index' => true],
                            ['name' => 'customer_segment', 'type' => 'VARCHAR(32)', 'nullable' => false],
                            ['name' => 'min_qty', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'unit_price', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                        ],
                        'unique_constraints' => [
                            ['name' => 'price_tiers_variant_segment_min_unique', 'rule' => 'UNIQUE (product_variant_id, customer_segment, min_qty)'],
                        ],
                    ],
                ],
                'state_machines' => [
                    [
                        'entity' => 'CatalogItem',
                        'enum_class' => 'App\\Enums\\CatalogItemStatus',
                        'initial_state' => 'DRAFT',
                        'states' => ['DRAFT', 'ACTIVE', 'DISCONTINUED', 'ARCHIVED'],
                        'transitions' => [
                            [
                                'from' => ['DRAFT'],
                                'to' => 'ACTIVE',
                                'action' => 'PublishCatalogItemAction',
                                'guards' => ['HasAtLeastOneActiveSkuVariantGuard', 'BasePriceIsNonNegativeDecimalGuard'],
                                'side_effects' => ['FlushTenantCatalogCache', 'RecordAuditTrailAction'],
                            ],
                            [
                                'from' => ['ACTIVE'],
                                'to' => 'DISCONTINUED',
                                'action' => 'DiscontinueCatalogItemAction',
                                'guards' => ['NoOpenPurchaseOrderGuard'],
                                'side_effects' => ['BlockNewSalesOrderLineGuard', 'FlushTenantCatalogCache'],
                            ],
                        ],
                    ],
                ],
                'concurrency_contracts' => [
                    [
                        'operation' => 'Tiered Price & UoM Conversion Resolution',
                        'mechanism' => 'SNAPSHOT_PRICE_AT_ORDER_LOCK',
                        'timeout_seconds' => 3,
                        'fallback_action' => 'Persist resolved unit_price & base_uom_factor onto order_lines inside DB::transaction() so future price edits never mutate historical orders.',
                    ],
                ],
                'rbac_permissions' => [
                    ['action' => 'catalog.manage', 'allowed' => ['SUPER_ADMIN', 'TENANT_OWNER', 'OPERATIONAL_STAFF']],
                    ['action' => 'catalog.override_tier_price', 'allowed' => ['TENANT_OWNER'], 'requires_reason' => true, 'audit_logged' => true],
                ],
                'edge_case_playbooks' => [
                    [
                        'scenario' => 'Pelanggan membeli 2 Box (1 Box = 12 Pcs) dan 3 Pcs eceran dalam satu keranjang transaksi yang sama.',
                        'resolution_rule' => 'Konversi seluruh baris SKU yang berinduk sama ke base_uom (2*12 + 3 = 27 Pcs) sebelum mengevaluasi ambang min_qty pada price_tiers dan sebelum melakukan reservasi stok.',
                    ],
                ],
                'gemini_tool_declarations' => [
                    [
                        'name' => 'lookup_sku_pricing_tiers',
                        'description' => 'Mengecek matriks varian SKU, rasio konversi satuan, dan harga grosir bertingkat.',
                        'parameters' => ['sku' => 'string', 'qty' => 'number'],
                    ],
                ],
                'is_active' => true,
            ],

            // 2. PRIMITIVE_INVENTORY_LEDGER
            [
                'code' => 'PRIMITIVE_INVENTORY_LEDGER',
                'name' => [
                    'id' => 'Buku Besar Mutasi Stok Multi-Gudang (FIFO / FEFO Batch)',
                    'en' => 'Immutable Multi-Warehouse Inventory Movement Ledger (FIFO / FEFO)',
                ],
                'category' => 'COMMERCE',
                'description' => [
                    'id' => 'Dilarang update kolom stock_qty statis tanpa mutasi. Seluruh stok direkonsiliasi melalui inventory_batches dan inventory_movements.',
                    'en' => 'Forbids arbitrary static stock_qty updates. All stock derives from immutable inventory_batches and inventory_movements.',
                ],
                'detection_keywords' => ['stok', 'gudang', 'inventory', 'persediaan', 'warehouse', 'mutasi barang', 'batch', 'expired', 'fefo', 'fifo', 'pos'],
                'schema_blueprints' => [
                    [
                        'table' => 'warehouses',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'code', 'type' => 'VARCHAR(32)', 'nullable' => false],
                            ['name' => 'name', 'type' => 'VARCHAR(120)', 'nullable' => false],
                        ],
                        'unique_constraints' => [
                            ['name' => 'warehouses_org_code_unique', 'rule' => 'UNIQUE (organization_id, code)'],
                        ],
                    ],
                    [
                        'table' => 'inventory_batches',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'warehouse_id', 'type' => 'ULID', 'foreign' => 'warehouses.id', 'index' => true],
                            ['name' => 'product_variant_id', 'type' => 'ULID', 'foreign' => 'product_variants.id', 'index' => true],
                            ['name' => 'batch_number', 'type' => 'VARCHAR(64)', 'nullable' => false],
                            ['name' => 'expiry_date', 'type' => 'DATE', 'nullable' => true, 'index' => true],
                            ['name' => 'unit_cost', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'available_qty', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'reserved_qty', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                        ],
                        'unique_constraints' => [
                            ['name' => 'inv_batches_wh_sku_batch_unique', 'rule' => 'UNIQUE (warehouse_id, product_variant_id, batch_number) + CHECK (available_qty >= 0 AND reserved_qty >= 0)'],
                        ],
                    ],
                    [
                        'table' => 'inventory_movements',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'inventory_batch_id', 'type' => 'ULID', 'foreign' => 'inventory_batches.id', 'index' => true],
                            ['name' => 'movement_type', 'type' => 'VARCHAR(24)', 'enum_ref' => 'InventoryMovementType'],
                            ['name' => 'signed_qty', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'reference_type', 'type' => 'VARCHAR(64)', 'nullable' => false],
                            ['name' => 'reference_id', 'type' => 'ULID', 'nullable' => false, 'index' => true],
                            ['name' => 'idempotency_key', 'type' => 'VARCHAR(96)', 'nullable' => false],
                        ],
                        'unique_constraints' => [
                            ['name' => 'inventory_movements_idempotency_unique', 'rule' => 'UNIQUE (organization_id, idempotency_key)'],
                        ],
                    ],
                ],
                'state_machines' => [
                    [
                        'entity' => 'StockReservation',
                        'enum_class' => 'App\\Enums\\StockReservationStatus',
                        'initial_state' => 'RESERVED',
                        'states' => ['RESERVED', 'COMMITTED_OUT', 'RELEASED', 'RETURNED_IN'],
                        'transitions' => [
                            [
                                'from' => ['RESERVED'],
                                'to' => 'COMMITTED_OUT',
                                'action' => 'CommitReservedInventoryAction',
                                'guards' => ['OrderPaymentSettledGuard', 'BatchNotExpiredAtDispatchGuard'],
                                'side_effects' => ['AppendInventoryMovementOut', 'DispatchCogsJournalEntryAction'],
                            ],
                            [
                                'from' => ['RESERVED'],
                                'to' => 'RELEASED',
                                'action' => 'ReleaseExpiredReservationAction',
                                'guards' => ['ReservationHoldTtlExpiredOrCancelledGuard'],
                                'side_effects' => ['AppendInventoryMovementRelease', 'RestoreBatchAvailableQty'],
                            ],
                        ],
                    ],
                ],
                'concurrency_contracts' => [
                    [
                        'operation' => 'Atomic FEFO/FIFO Batch Stock Reservation',
                        'mechanism' => 'PESSIMISTIC_LOCK (SELECT ... FOR UPDATE ORDER BY expiry_date ASC NULLS LAST, id ASC)',
                        'timeout_seconds' => 5,
                        'fallback_action' => "Throw DomainException('Stok SKU tidak mencukupi atau sedang dikunci transaksi lain')",
                    ],
                ],
                'rbac_permissions' => [
                    ['action' => 'inventory.reserve', 'allowed' => ['TENANT_OWNER', 'OPERATIONAL_STAFF', 'CUSTOMER']],
                    ['action' => 'inventory.stock_opname_adjust', 'allowed' => ['TENANT_OWNER', 'FINANCE_AUDITOR'], 'requires_reason' => true, 'audit_logged' => true],
                ],
                'edge_case_playbooks' => [
                    [
                        'scenario' => 'Pesanan membutuhkan 10 unit, tetapi Batch A (kedaluwarsa bulan depan) hanya tersisa 4 unit dan Batch B (kedaluwarsa tahun depan) memiliki 20 unit.',
                        'resolution_rule' => 'Alokasikan secara atomik menggunakan FEFO split: kunci & potong 4 unit dari Batch A + 6 unit dari Batch B dalam satu DB::transaction(), serta catat HPP tertimbang aktual dari kedua batch tersebut.',
                    ],
                ],
                'gemini_tool_declarations' => [
                    [
                        'name' => 'inspect_warehouse_batch_stock',
                        'description' => 'Memeriksa ketersediaan stok per gudang beserta batch kedaluwarsa terdekat (FEFO).',
                        'parameters' => ['warehouse_code' => 'string', 'sku' => 'string'],
                    ],
                ],
                'is_active' => true,
            ],

            // 3. PRIMITIVE_TIME_SLOT_ENGINE
            [
                'code' => 'PRIMITIVE_TIME_SLOT_ENGINE',
                'name' => [
                    'id' => 'Mesin Ketersediaan Slot Waktu & Anti-Double-Booking',
                    'en' => 'Time-Slot Availability & Anti-Double-Booking Engine',
                ],
                'category' => 'SCHEDULING',
                'description' => [
                    'id' => 'Tabel allocatable_resources, operating_hours, blackout_dates, dan bookings dengan Pessimistic Lock saat validasi irisan waktu.',
                    'en' => 'Tables allocatable_resources, operating_hours, blackout_dates, and bookings with row-level lockForUpdate() overlap prevention.',
                ],
                'detection_keywords' => ['booking', 'reservasi', 'jadwal', 'slot', 'lapangan', 'kamar', 'ruangan', 'sewa', 'appointment', 'antrean', 'venue'],
                'schema_blueprints' => [
                    [
                        'table' => 'allocatable_resources',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'name', 'type' => 'VARCHAR(120)', 'nullable' => false],
                            ['name' => 'resource_type', 'type' => 'VARCHAR(32)', 'nullable' => false],
                            ['name' => 'unit_condition', 'type' => 'VARCHAR(24)', 'enum_ref' => 'ResourceConditionStatus'],
                            ['name' => 'hourly_base_rate', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'weekend_multiplier', 'type' => 'DECIMAL(8,4)', 'nullable' => false],
                        ],
                        'unique_constraints' => [
                            ['name' => 'allocatable_resources_org_name_unique', 'rule' => 'UNIQUE (organization_id, name)'],
                        ],
                    ],
                    [
                        'table' => 'operating_hours',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'resource_id', 'type' => 'ULID', 'foreign' => 'allocatable_resources.id', 'index' => true],
                            ['name' => 'day_of_week', 'type' => 'SMALLINT', 'nullable' => false],
                            ['name' => 'opens_at', 'type' => 'TIME', 'nullable' => false],
                            ['name' => 'closes_at', 'type' => 'TIME', 'nullable' => false],
                        ],
                        'unique_constraints' => [
                            ['name' => 'operating_hours_resource_day_unique', 'rule' => 'UNIQUE (resource_id, day_of_week)'],
                        ],
                    ],
                    [
                        'table' => 'blackout_dates',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'resource_id', 'type' => 'ULID', 'foreign' => 'allocatable_resources.id', 'index' => true],
                            ['name' => 'starts_at', 'type' => 'TIMESTAMP', 'nullable' => false],
                            ['name' => 'ends_at', 'type' => 'TIMESTAMP', 'nullable' => false],
                            ['name' => 'reason', 'type' => 'VARCHAR(190)', 'nullable' => false],
                        ],
                        'unique_constraints' => [],
                    ],
                    [
                        'table' => 'bookings',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'resource_id', 'type' => 'ULID', 'foreign' => 'allocatable_resources.id', 'index' => true],
                            ['name' => 'customer_user_id', 'type' => 'ULID', 'foreign' => 'users.id', 'nullable' => true],
                            ['name' => 'start_at', 'type' => 'TIMESTAMP', 'nullable' => false, 'index' => true],
                            ['name' => 'end_at', 'type' => 'TIMESTAMP', 'nullable' => false, 'index' => true],
                            ['name' => 'hold_expires_at', 'type' => 'TIMESTAMP', 'nullable' => true, 'index' => true],
                            ['name' => 'status', 'type' => 'VARCHAR(32)', 'enum_ref' => 'BookingStatus'],
                            ['name' => 'total_amount', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                        ],
                        'unique_constraints' => [
                            [
                                'name' => 'prevent_overlapping_active_booking',
                                'rule' => "Lock parent allocatable_resources row via lockForUpdate() + reject if EXISTS(start_at < :end_at AND end_at > :start_at AND status IN ('PENDING_PAYMENT','CONFIRMED','CHECKED_IN'))",
                            ],
                        ],
                    ],
                ],
                'state_machines' => [
                    [
                        'entity' => 'Booking',
                        'enum_class' => 'App\\Enums\\BookingStatus',
                        'initial_state' => 'DRAFT',
                        'states' => ['DRAFT', 'PENDING_PAYMENT', 'CONFIRMED', 'CHECKED_IN', 'COMPLETED', 'CANCELLED', 'EXPIRED'],
                        'transitions' => [
                            [
                                'from' => ['DRAFT'],
                                'to' => 'PENDING_PAYMENT',
                                'action' => 'LockSlotAndCreateInvoiceAction',
                                'guards' => ['SlotIsAvailableGuard', 'ResourceNotUnderMaintenanceGuard', 'UserHasNoUnpaidPenaltyGuard'],
                                'side_effects' => ['HoldSlotTTL(15m)', 'DispatchPaymentWebhookListener'],
                            ],
                            [
                                'from' => ['PENDING_PAYMENT'],
                                'to' => 'CONFIRMED',
                                'action' => 'ConfirmBookingPaymentAction',
                                'guards' => ['PaymentVerifiedByGatewayOrAdminGuard'],
                                'side_effects' => ['RecordLedgerEntryAction', 'SendBookingConfirmationNotification'],
                            ],
                            [
                                'from' => ['PENDING_PAYMENT'],
                                'to' => 'EXPIRED',
                                'action' => 'ExpireUnpaidBookingAction',
                                'guards' => ['HoldTTLHasPassedGuard'],
                                'side_effects' => ['ReleaseSlotLock'],
                            ],
                            [
                                'from' => ['CONFIRMED'],
                                'to' => 'CHECKED_IN',
                                'action' => 'CheckInBookingGuestAction',
                                'guards' => ['ResourceUnitIsCleanGuard'],
                                'side_effects' => ['SetResourceConditionOccupied'],
                            ],
                            [
                                'from' => ['CHECKED_IN'],
                                'to' => 'COMPLETED',
                                'action' => 'CompleteBookingAndMarkHousekeepingAction',
                                'guards' => ['AllIncidentalChargesSettledGuard'],
                                'side_effects' => ['SetResourceConditionDirtyForTurnaround'],
                            ],
                        ],
                    ],
                ],
                'concurrency_contracts' => [
                    [
                        'operation' => 'Time-Slot Overlap Reservation',
                        'mechanism' => 'PESSIMISTIC_LOCK (SELECT ... FROM allocatable_resources WHERE id = ? FOR UPDATE)',
                        'timeout_seconds' => 5,
                        'fallback_action' => "Throw DomainException('Slot jadwal sedang dikunci pengguna lain, silakan pilih jam lain')",
                    ],
                ],
                'rbac_permissions' => [
                    ['action' => 'booking.create', 'allowed' => ['TENANT_OWNER', 'OPERATIONAL_STAFF', 'CUSTOMER']],
                    ['action' => 'booking.force_cancel', 'allowed' => ['TENANT_OWNER'], 'requires_reason' => true, 'audit_logged' => true],
                    ['action' => 'resource.toggle_maintenance', 'allowed' => ['TENANT_OWNER', 'OPERATIONAL_STAFF'], 'audit_logged' => true],
                ],
                'edge_case_playbooks' => [
                    [
                        'scenario' => 'Webhook pembayaran sukses masuk 2 detik SETELAH booking berstatus EXPIRED karena batas waktu Hold TTL 15 menit habis.',
                        'resolution_rule' => 'Kunci baris allocatable_resources dengan lockForUpdate(). Jika slot BELUM diambil pengguna lain, promosikan EXPIRED -> CONFIRMED dan catat jurnal pendapatan. Jika slot SUDAH diambil pengguna lain, kreditkan dana secara atomik ke Refundable Deposit/Wallet pelanggan dan kirim notifikasi kompensasi rescheduling instan.',
                    ],
                ],
                'gemini_tool_declarations' => [
                    [
                        'name' => 'check_resource_slot_availability',
                        'description' => 'Memeriksa slot jadwal kosong pada lapangan/kamar/ruangan tertentu pada rentang tanggal.',
                        'parameters' => ['resource_id' => 'string', 'date' => 'string'],
                    ],
                ],
                'is_active' => true,
            ],

            // 4. PRIMITIVE_DOUBLE_ENTRY_LEDGER
            [
                'code' => 'PRIMITIVE_DOUBLE_ENTRY_LEDGER',
                'name' => [
                    'id' => 'Buku Besar Akuntansi Berpasangan Immutable (Double-Entry & Fund Accounting)',
                    'en' => 'Immutable Double-Entry Accounting & Fund Restriction Ledger',
                ],
                'category' => 'LEDGER',
                'description' => [
                    'id' => 'Tabel accounts (COA), journal_entries, dan journal_lines dengan invarian wajib SUM(debit) === SUM(credit) serta fund_restriction_type.',
                    'en' => 'Tables accounts (COA), journal_entries, and journal_lines enforcing SUM(debit) === SUM(credit) and fund_restriction_type.',
                ],
                'detection_keywords' => ['jurnal', 'akuntansi', 'ledger', 'buku besar', 'keuangan', 'pembayaran', 'saldo', 'kas', 'donasi', 'dana terikat', 'escrow'],
                'schema_blueprints' => [
                    [
                        'table' => 'accounts',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'code', 'type' => 'VARCHAR(24)', 'nullable' => false],
                            ['name' => 'name', 'type' => 'VARCHAR(140)', 'nullable' => false],
                            ['name' => 'account_type', 'type' => 'VARCHAR(24)', 'enum_ref' => 'LedgerAccountType'],
                            ['name' => 'fund_restriction_type', 'type' => 'VARCHAR(24)', 'nullable' => false],
                            ['name' => 'cached_balance', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                        ],
                        'unique_constraints' => [
                            ['name' => 'accounts_org_code_unique', 'rule' => 'UNIQUE (organization_id, code)'],
                        ],
                    ],
                    [
                        'table' => 'journal_entries',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'entry_number', 'type' => 'VARCHAR(48)', 'nullable' => false],
                            ['name' => 'idempotency_key', 'type' => 'VARCHAR(96)', 'nullable' => false],
                            ['name' => 'posted_at', 'type' => 'TIMESTAMP', 'nullable' => false, 'index' => true],
                            ['name' => 'status', 'type' => 'VARCHAR(24)', 'enum_ref' => 'JournalEntryStatus'],
                            ['name' => 'reversal_of_entry_id', 'type' => 'ULID', 'nullable' => true],
                            ['name' => 'sha256_seal', 'type' => 'VARCHAR(64)', 'nullable' => false],
                        ],
                        'unique_constraints' => [
                            ['name' => 'journal_entries_org_idempotency_unique', 'rule' => 'UNIQUE (organization_id, idempotency_key)'],
                        ],
                    ],
                    [
                        'table' => 'journal_lines',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'journal_entry_id', 'type' => 'ULID', 'foreign' => 'journal_entries.id', 'index' => true],
                            ['name' => 'account_id', 'type' => 'ULID', 'foreign' => 'accounts.id', 'index' => true],
                            ['name' => 'debit', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'credit', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'memo', 'type' => 'VARCHAR(190)', 'nullable' => true],
                        ],
                        'unique_constraints' => [
                            ['name' => 'journal_lines_non_negative_check', 'rule' => 'CHECK (debit >= 0 AND credit >= 0 AND NOT (debit > 0 AND credit > 0))'],
                        ],
                    ],
                ],
                'state_machines' => [
                    [
                        'entity' => 'JournalEntry',
                        'enum_class' => 'App\\Enums\\JournalEntryStatus',
                        'initial_state' => 'POSTED',
                        'states' => ['POSTED', 'REVERSED'],
                        'transitions' => [
                            [
                                'from' => ['POSTED'],
                                'to' => 'REVERSED',
                                'action' => 'CreateReversingJournalEntryAction',
                                'guards' => ['AccountingPeriodIsOpenGuard', 'DebitEqualsCreditZeroSumGuard'],
                                'side_effects' => ['AppendMirrorReversingLines', 'ComputeChainedSha256LedgerSeal'],
                            ],
                        ],
                    ],
                ],
                'concurrency_contracts' => [
                    [
                        'operation' => 'Immutable Ledger Posting & Balance Rollup',
                        'mechanism' => 'PESSIMISTIC_LOCK (SELECT ... FROM accounts WHERE id IN (...) ORDER BY id ASC FOR UPDATE) + Idempotency Key',
                        'timeout_seconds' => 5,
                        'fallback_action' => "Reject duplicate idempotency_key silently (return existing journal_entry) or throw UnbalancedJournalException if SUM(debit) !== SUM(credit).",
                    ],
                ],
                'rbac_permissions' => [
                    ['action' => 'ledger.view_trial_balance', 'allowed' => ['SUPER_ADMIN', 'TENANT_OWNER', 'FINANCE_AUDITOR']],
                    ['action' => 'ledger.reverse_entry', 'allowed' => ['TENANT_OWNER', 'FINANCE_AUDITOR'], 'requires_reason' => true, 'audit_logged' => true],
                ],
                'edge_case_playbooks' => [
                    [
                        'scenario' => 'Payment gateway mengirimkan webhook SETTLEMENT ganda (retry 3 kali bersamaan) untuk transaksi yang sama.',
                        'resolution_rule' => 'Gunakan UNIQUE constraint (organization_id, idempotency_key) pada journal_entries. Request pertama mengunci akun terurut berdasarkan ULID (mencegah deadlock) dan menulis jurnal; 2 request duplikat tertahan constraint dan mengembalikan HTTP 200 idempoten tanpa menggandakan saldo.',
                    ],
                ],
                'gemini_tool_declarations' => [
                    [
                        'name' => 'audit_ledger_trial_balance',
                        'description' => 'Memverifikasi keseimbangan neraca saldo (SUM debit === SUM credit) dan integritas hash jurnal.',
                        'parameters' => ['period_month' => 'string'],
                    ],
                ],
                'is_active' => true,
            ],

            // 5. PRIMITIVE_BOM_WORK_ORDER
            [
                'code' => 'PRIMITIVE_BOM_WORK_ORDER',
                'name' => [
                    'id' => 'Bill of Materials (BoM) / Resep & Konversi Produksi Atomik',
                    'en' => 'Multi-Level Bill of Materials (BoM), Recipe & Work Order Engine',
                ],
                'category' => 'WORKFLOW',
                'description' => [
                    'id' => 'Tabel recipes_or_boms, bom_items, dan production_runs untuk pemotongan bahan mentah otomatis & pencatatan barang jadi/menu.',
                    'en' => 'Tables recipes_or_boms, bom_items, and production_runs automating raw material depletion and finished goods intake.',
                ],
                'detection_keywords' => ['bom', 'resep', 'produksi', 'manufaktur', 'work order', 'bahan baku', 'dapur', 'kds', 'perakitan'],
                'schema_blueprints' => [
                    [
                        'table' => 'recipes_or_boms',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'output_variant_id', 'type' => 'ULID', 'foreign' => 'product_variants.id', 'index' => true],
                            ['name' => 'version_number', 'type' => 'INTEGER', 'nullable' => false],
                            ['name' => 'standard_output_qty', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'is_active', 'type' => 'BOOLEAN', 'nullable' => false],
                        ],
                        'unique_constraints' => [
                            ['name' => 'boms_output_version_unique', 'rule' => 'UNIQUE (output_variant_id, version_number)'],
                        ],
                    ],
                    [
                        'table' => 'bom_items',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'bom_id', 'type' => 'ULID', 'foreign' => 'recipes_or_boms.id', 'index' => true],
                            ['name' => 'component_variant_id', 'type' => 'ULID', 'foreign' => 'product_variants.id'],
                            ['name' => 'required_qty', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'scrap_allowance_pct', 'type' => 'DECIMAL(8,4)', 'nullable' => false],
                        ],
                        'unique_constraints' => [
                            ['name' => 'bom_items_bom_component_unique', 'rule' => 'UNIQUE (bom_id, component_variant_id)'],
                        ],
                    ],
                    [
                        'table' => 'production_runs',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'bom_id', 'type' => 'ULID', 'foreign' => 'recipes_or_boms.id'],
                            ['name' => 'warehouse_id', 'type' => 'ULID', 'foreign' => 'warehouses.id'],
                            ['name' => 'planned_qty', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'actual_yield_qty', 'type' => 'DECIMAL(19,4)', 'nullable' => true],
                            ['name' => 'actual_total_cost', 'type' => 'DECIMAL(19,4)', 'nullable' => true],
                            ['name' => 'status', 'type' => 'VARCHAR(32)', 'enum_ref' => 'ProductionRunStatus'],
                        ],
                        'unique_constraints' => [],
                    ],
                ],
                'state_machines' => [
                    [
                        'entity' => 'ProductionRun',
                        'enum_class' => 'App\\Enums\\ProductionRunStatus',
                        'initial_state' => 'PLANNED',
                        'states' => ['PLANNED', 'MATERIALS_ISSUED_WIP', 'COMPLETED', 'ABORTED'],
                        'transitions' => [
                            [
                                'from' => ['PLANNED'],
                                'to' => 'MATERIALS_ISSUED_WIP',
                                'action' => 'IssueBomMaterialsToWipAction',
                                'guards' => ['AllBomComponentsHaveSufficientFefoStockGuard'],
                                'side_effects' => ['DepleteRawMaterialBatchesAtomically', 'RecordWipCostSnapshot'],
                            ],
                            [
                                'from' => ['MATERIALS_ISSUED_WIP'],
                                'to' => 'COMPLETED',
                                'action' => 'CompleteProductionRunAndReceiveYieldAction',
                                'guards' => ['ActualYieldIsPositiveGuard'],
                                'side_effects' => ['CreateFinishedGoodBatchWithActualUnitCost', 'PostProductionVarianceJournal'],
                            ],
                        ],
                    ],
                ],
                'concurrency_contracts' => [
                    [
                        'operation' => 'Atomic Multi-Ingredient Depletion & Finished Good Intake',
                        'mechanism' => 'PESSIMISTIC_LOCK on sorted component inventory_batches rows inside single DB::transaction()',
                        'timeout_seconds' => 5,
                        'fallback_action' => "Rollback entire Work Order / Kitchen Ticket if any required ingredient falls below 0.",
                    ],
                ],
                'rbac_permissions' => [
                    ['action' => 'production.manage_bom', 'allowed' => ['TENANT_OWNER']],
                    ['action' => 'production.execute_run', 'allowed' => ['TENANT_OWNER', 'OPERATIONAL_STAFF']],
                ],
                'edge_case_playbooks' => [
                    [
                        'scenario' => 'Hasil aktual produksi (actual_yield_qty) hanya 92 unit dari rencana 100 unit akibat penyusutan/kerusakan bahan.',
                        'resolution_rule' => 'Hitung unit_cost batch barang jadi berdasarkan total biaya bahan aktual dibagi 92 unit jika dalam batas scrap_allowance_pct; jika melampaui ambang toleransi, pisahkan selisihnya ke akun Beban Kerugian Scrap Abnormal dan wajibkan otorisasi Manajer.',
                    ],
                ],
                'gemini_tool_declarations' => [
                    [
                        'name' => 'simulate_bom_material_requirements',
                        'description' => 'Menghitung kebutuhan bahan baku dan potensi kekurangan stok untuk target kuantitas produksi.',
                        'parameters' => ['bom_id' => 'string', 'planned_qty' => 'number'],
                    ],
                ],
                'is_active' => true,
            ],

            // 6. PRIMITIVE_APPROVAL_WORKFLOW
            [
                'code' => 'PRIMITIVE_APPROVAL_WORKFLOW',
                'name' => [
                    'id' => 'Mesin Persetujuan Berjenjang Polimorfik (Multi-Stage Sign-Off)',
                    'en' => 'Polymorphic Multi-Stage Approval & Sign-Off Workflow Engine',
                ],
                'category' => 'WORKFLOW',
                'description' => [
                    'id' => 'Tabel polimorfik approval_requests dan approval_steps untuk persetujuan berlapis (Staff -> Manajer -> Finance / Klien).',
                    'en' => 'Polymorphic approval_requests and approval_steps tables enforcing multi-stage sequential/quorum sign-off.',
                ],
                'detection_keywords' => ['approval', 'persetujuan', 'otorisasi', 'reimbursement', 'klaim', 'pengajuan', 'bast', 'verifikasi'],
                'schema_blueprints' => [
                    [
                        'table' => 'approval_requests',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'approvable_type', 'type' => 'VARCHAR(80)', 'nullable' => false],
                            ['name' => 'approvable_id', 'type' => 'ULID', 'nullable' => false, 'index' => true],
                            ['name' => 'requested_by_user_id', 'type' => 'ULID', 'foreign' => 'users.id'],
                            ['name' => 'current_step_order', 'type' => 'SMALLINT', 'nullable' => false],
                            ['name' => 'amount_value', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'status', 'type' => 'VARCHAR(24)', 'enum_ref' => 'ApprovalRequestStatus'],
                        ],
                        'unique_constraints' => [],
                    ],
                    [
                        'table' => 'approval_steps',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'approval_request_id', 'type' => 'ULID', 'foreign' => 'approval_requests.id', 'index' => true],
                            ['name' => 'step_order', 'type' => 'SMALLINT', 'nullable' => false],
                            ['name' => 'required_role', 'type' => 'VARCHAR(48)', 'nullable' => false],
                            ['name' => 'acted_by_user_id', 'type' => 'ULID', 'nullable' => true],
                            ['name' => 'decision', 'type' => 'VARCHAR(24)', 'nullable' => false],
                            ['name' => 'decision_note', 'type' => 'TEXT', 'nullable' => true],
                            ['name' => 'decided_at', 'type' => 'TIMESTAMP', 'nullable' => true],
                        ],
                        'unique_constraints' => [
                            ['name' => 'approval_steps_req_order_unique', 'rule' => 'UNIQUE (approval_request_id, step_order)'],
                        ],
                    ],
                ],
                'state_machines' => [
                    [
                        'entity' => 'ApprovalRequest',
                        'enum_class' => 'App\\Enums\\ApprovalRequestStatus',
                        'initial_state' => 'PENDING_STEP',
                        'states' => ['PENDING_STEP', 'APPROVED', 'REJECTED', 'REVISION_REQUESTED'],
                        'transitions' => [
                            [
                                'from' => ['PENDING_STEP'],
                                'to' => 'APPROVED',
                                'action' => 'ApproveCurrentWorkflowStepAction',
                                'guards' => ['ActorHasRequiredStepRoleGuard', 'RequesterCannotSelfApproveGuard', 'IsFinalApprovalStepGuard'],
                                'side_effects' => ['UnlockParentEntityExecutionAction', 'AppendSha256ApprovalAuditLog'],
                            ],
                            [
                                'from' => ['PENDING_STEP'],
                                'to' => 'REJECTED',
                                'action' => 'RejectWorkflowStepAction',
                                'guards' => ['ActorHasRequiredStepRoleGuard', 'DecisionReasonIsNotEmptyGuard'],
                                'side_effects' => ['NotifyRequesterWithRejectionReason'],
                            ],
                        ],
                    ],
                ],
                'concurrency_contracts' => [
                    [
                        'operation' => 'Sequential Step Sign-Off Decision',
                        'mechanism' => 'PESSIMISTIC_LOCK (SELECT ... FROM approval_requests WHERE id = ? FOR UPDATE)',
                        'timeout_seconds' => 5,
                        'fallback_action' => "Prevent double-click or dual-manager race approval on the same step_order.",
                    ],
                ],
                'rbac_permissions' => [
                    ['action' => 'approval.submit', 'allowed' => ['OPERATIONAL_STAFF', 'TENANT_OWNER', 'CUSTOMER']],
                    ['action' => 'approval.decide', 'allowed' => ['TENANT_OWNER', 'FINANCE_AUDITOR'], 'requires_reason' => true, 'audit_logged' => true],
                ],
                'edge_case_playbooks' => [
                    [
                        'scenario' => 'Pengaju (requester) juga memegang role Manajer dan mencoba menyetujui klaim pengajuannya sendiri.',
                        'resolution_rule' => 'Tolak melalui RequesterCannotSelfApproveGuard (Segregation of Duties): eskalasikan langkah persetujuan secara otomatis ke atasan satu tingkat di atasnya atau TENANT_OWNER.',
                    ],
                ],
                'gemini_tool_declarations' => [
                    [
                        'name' => 'list_pending_approvals',
                        'description' => 'Menampilkan daftar pengajuan persetujuan yang tertahan beserta usia SLA antrean.',
                        'parameters' => ['status' => 'string'],
                    ],
                ],
                'is_active' => true,
            ],

            // 7. PRIMITIVE_SUBSCRIPTION_COHORT
            [
                'code' => 'PRIMITIVE_SUBSCRIPTION_COHORT',
                'name' => [
                    'id' => 'Langganan Berulang, Keanggotaan, Cohort & Masa Tenggang (Grace Period)',
                    'en' => 'Recurring Subscription, Membership, Cohort & Grace Period Engine',
                ],
                'category' => 'LEDGER',
                'description' => [
                    'id' => 'Tabel plans, subscriptions, dan usage_records untuk siklus SPP/membership/SaaS, masa tenggang, dan pembekuan akses otomatis.',
                    'en' => 'Tables plans, subscriptions, and usage_records governing recurring billing cycles, grace periods, and entitlement suspension.',
                ],
                'detection_keywords' => ['langganan', 'subscription', 'membership', 'member', 'iuran', 'spp', 'kelas', 'cohort', 'kuota', 'bulanan'],
                'schema_blueprints' => [
                    [
                        'table' => 'plans',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'code', 'type' => 'VARCHAR(48)', 'nullable' => false],
                            ['name' => 'name', 'type' => 'JSONB', 'nullable' => false],
                            ['name' => 'billing_interval', 'type' => 'VARCHAR(24)', 'nullable' => false],
                            ['name' => 'price_amount', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'grace_period_days', 'type' => 'SMALLINT', 'nullable' => false],
                            ['name' => 'entitlements_json', 'type' => 'JSONB', 'nullable' => false],
                        ],
                        'unique_constraints' => [
                            ['name' => 'plans_org_code_unique', 'rule' => 'UNIQUE (organization_id, code)'],
                        ],
                    ],
                    [
                        'table' => 'subscriptions',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'plan_id', 'type' => 'ULID', 'foreign' => 'plans.id'],
                            ['name' => 'subscriber_user_id', 'type' => 'ULID', 'foreign' => 'users.id', 'index' => true],
                            ['name' => 'current_period_ends_at', 'type' => 'TIMESTAMP', 'nullable' => false, 'index' => true],
                            ['name' => 'grace_period_ends_at', 'type' => 'TIMESTAMP', 'nullable' => true],
                            ['name' => 'status', 'type' => 'VARCHAR(24)', 'enum_ref' => 'SubscriptionStatus'],
                        ],
                        'unique_constraints' => [],
                    ],
                    [
                        'table' => 'usage_records',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'subscription_id', 'type' => 'ULID', 'foreign' => 'subscriptions.id', 'index' => true],
                            ['name' => 'metric_key', 'type' => 'VARCHAR(48)', 'nullable' => false],
                            ['name' => 'consumed_units', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'recorded_at', 'type' => 'TIMESTAMP', 'nullable' => false],
                        ],
                        'unique_constraints' => [],
                    ],
                ],
                'state_machines' => [
                    [
                        'entity' => 'Subscription',
                        'enum_class' => 'App\\Enums\\SubscriptionStatus',
                        'initial_state' => 'ACTIVE',
                        'states' => ['ACTIVE', 'PAST_DUE_GRACE', 'SUSPENDED', 'CANCELLED'],
                        'transitions' => [
                            [
                                'from' => ['ACTIVE'],
                                'to' => 'PAST_DUE_GRACE',
                                'action' => 'EnterSubscriptionGracePeriodAction',
                                'guards' => ['CurrentPeriodEndedWithoutRenewalPaymentGuard'],
                                'side_effects' => ['ScheduleDunningReminderNotifications'],
                            ],
                            [
                                'from' => ['PAST_DUE_GRACE'],
                                'to' => 'SUSPENDED',
                                'action' => 'SuspendDelinquentSubscriptionAction',
                                'guards' => ['GracePeriodDeadlinePassedGuard'],
                                'side_effects' => ['RevokeMemberEntitlementAccessCache'],
                            ],
                            [
                                'from' => ['PAST_DUE_GRACE', 'SUSPENDED'],
                                'to' => 'ACTIVE',
                                'action' => 'ReactivateSubscriptionOnSettlementAction',
                                'guards' => ['RenewalInvoiceSettledGuard'],
                                'side_effects' => ['AdvanceBillingCycleAnchor', 'RestoreMemberEntitlementAccessCache'],
                            ],
                        ],
                    ],
                ],
                'concurrency_contracts' => [
                    [
                        'operation' => 'Quota / Session Credit Deduction',
                        'mechanism' => 'PESSIMISTIC_LOCK on subscriptions row before inserting usage_records',
                        'timeout_seconds' => 4,
                        'fallback_action' => "Throw DomainException('Kuota sesi keanggotaan Anda telah habis pada periode ini')",
                    ],
                ],
                'rbac_permissions' => [
                    ['action' => 'subscription.enroll', 'allowed' => ['TENANT_OWNER', 'OPERATIONAL_STAFF', 'CUSTOMER']],
                    ['action' => 'subscription.waive_late_fee', 'allowed' => ['TENANT_OWNER'], 'requires_reason' => true, 'audit_logged' => true],
                ],
                'edge_case_playbooks' => [
                    [
                        'scenario' => 'Anggota melakukan upgrade paket di tengah bulan berjalan (hari ke-15 dari 30 hari).',
                        'resolution_rule' => 'Hitung kredit sisa hari paket lama secara proporsional (prorata DECIMAL(19,4) HALF_UP) terhadap harga paket baru, terbitkan selisih invoice instan, dan perbarui hak akses (entitlements) hanya setelah selisih terbayar lunas.',
                    ],
                ],
                'gemini_tool_declarations' => [
                    [
                        'name' => 'check_member_subscription_status',
                        'description' => 'Memeriksa status aktif keanggotaan, masa tenggang, dan sisa kuota sesi.',
                        'parameters' => ['subscriber_id' => 'string'],
                    ],
                ],
                'is_active' => true,
            ],

            // 8. PRIMITIVE_CLINICAL_EMR_AUDIT (Sector 3)
            [
                'code' => 'PRIMITIVE_CLINICAL_EMR_AUDIT',
                'name' => [
                    'id' => 'Rekam Medis Elektronik (EMR) Append-Only, Resep & Klaim Klinis',
                    'en' => 'Append-Only Clinical EMR, Prescription & Insurance Claim Engine',
                ],
                'category' => 'CLINICAL',
                'description' => [
                    'id' => 'Tabel patient_encounters, emr_clinical_notes (append-only addendum), dan prescriptions terhubung ke batch obat FEFO.',
                    'en' => 'Tables patient_encounters, append-only emr_clinical_notes, and FEFO-linked prescriptions.',
                ],
                'detection_keywords' => ['rekam medis', 'emr', 'rme', 'pasien', 'dokter', 'resep', 'klinis', 'diagnosa', 'icd10', 'klaim asuransi'],
                'schema_blueprints' => [
                    [
                        'table' => 'patient_encounters',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'patient_user_id', 'type' => 'ULID', 'foreign' => 'users.id', 'index' => true],
                            ['name' => 'practitioner_user_id', 'type' => 'ULID', 'foreign' => 'users.id'],
                            ['name' => 'icd10_primary_code', 'type' => 'VARCHAR(16)', 'nullable' => true],
                            ['name' => 'status', 'type' => 'VARCHAR(32)', 'enum_ref' => 'ClinicalEncounterStatus'],
                        ],
                        'unique_constraints' => [],
                    ],
                    [
                        'table' => 'emr_clinical_notes',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'encounter_id', 'type' => 'ULID', 'foreign' => 'patient_encounters.id', 'index' => true],
                            ['name' => 'amends_note_id', 'type' => 'ULID', 'nullable' => true],
                            ['name' => 'soap_payload_encrypted', 'type' => 'TEXT', 'nullable' => false],
                            ['name' => 'signed_by_practitioner_id', 'type' => 'ULID', 'foreign' => 'users.id'],
                            ['name' => 'sha256_integrity_hash', 'type' => 'VARCHAR(64)', 'nullable' => false],
                        ],
                        'unique_constraints' => [],
                    ],
                ],
                'state_machines' => [
                    [
                        'entity' => 'ClinicalEncounter',
                        'enum_class' => 'App\\Enums\\ClinicalEncounterStatus',
                        'initial_state' => 'QUEUED',
                        'states' => ['QUEUED', 'IN_CONSULTATION', 'PHARMACY_DISPENSING', 'CLAIM_SUBMITTED', 'DISCHARGED'],
                        'transitions' => [
                            [
                                'from' => ['QUEUED'],
                                'to' => 'IN_CONSULTATION',
                                'action' => 'StartPractitionerConsultationAction',
                                'guards' => ['AssignedPractitionerAuthenticatedGuard'],
                                'side_effects' => ['LogPatientPrivacyAccessAudit'],
                            ],
                            [
                                'from' => ['IN_CONSULTATION'],
                                'to' => 'PHARMACY_DISPENSING',
                                'action' => 'SignEmrAndIssuePrescriptionAction',
                                'guards' => ['SoapNoteSignedWithIcd10Guard'],
                                'side_effects' => ['SealEmrNoteSha256', 'ReservePharmacyFefoDrugBatches'],
                            ],
                        ],
                    ],
                ],
                'concurrency_contracts' => [
                    [
                        'operation' => 'Immutable EMR Note Sealing',
                        'mechanism' => 'APPEND_ONLY_LEDGER (Block Eloquent updating/deleting events on EmrClinicalNote)',
                        'timeout_seconds' => 3,
                        'fallback_action' => "Throw ImmutableRecordException('Catatan medis yang telah ditandatangani tidak dapat diubah; buat catatan Addendum baru').",
                    ],
                ],
                'rbac_permissions' => [
                    ['action' => 'emr.read_encounter', 'allowed' => ['TENANT_OWNER', 'OPERATIONAL_STAFF'], 'audit_logged' => true],
                    ['action' => 'emr.append_addendum', 'allowed' => ['OPERATIONAL_STAFF'], 'requires_reason' => true, 'audit_logged' => true],
                ],
                'edge_case_playbooks' => [
                    [
                        'scenario' => 'Dokter menemukan kesalahan penulisan dosis pada catatan SOAP yang sudah ditandatangani 10 menit lalu.',
                        'resolution_rule' => 'Dilarang melakukan UPDATE pada baris emr_clinical_notes lama. Buat baris baru dengan amends_note_id menunjuk ke ULID catatan sebelumnya, tampilkan catatan lama dengan penanda Superseded (tetap utuh untuk audit hukum medis), dan batalkan reservasi batch obat lama jika belum diserahkan apoteker.',
                    ],
                ],
                'gemini_tool_declarations' => [
                    [
                        'name' => 'verify_clinical_audit_chain',
                        'description' => 'Memeriksa keutuhan hash SHA-256 catatan rekam medis elektronik dan kepatuhan jejak akses.',
                        'parameters' => ['encounter_id' => 'string'],
                    ],
                ],
                'is_active' => true,
            ],

            // 9. PRIMITIVE_LOGISTICS_MANIFEST_POD (Sector 6)
            [
                'code' => 'PRIMITIVE_LOGISTICS_MANIFEST_POD',
                'name' => [
                    'id' => 'Manifes Pengiriman, Tarif Volumetrik & Proof of Delivery (PoD)',
                    'en' => 'Waybill Manifest State Machine, Volumetric Tariff & Proof of Delivery',
                ],
                'category' => 'LOGISTICS',
                'description' => [
                    'id' => 'Tabel shipments, shipment_waypoints, dan proof_of_deliveries dengan kalkulasi berat volumetrik dan verifikasi GPS PoD.',
                    'en' => 'Tables shipments, shipment_waypoints, and proof_of_deliveries enforcing volumetric weight and GPS-verified PoD.',
                ],
                'detection_keywords' => ['resi', 'awb', 'waybill', 'manifest', 'kargo', 'kurir', 'pengiriman', 'pod', 'volumetrik', 'ekspedisi'],
                'schema_blueprints' => [
                    [
                        'table' => 'shipments',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'awb_number', 'type' => 'VARCHAR(40)', 'nullable' => false],
                            ['name' => 'actual_weight_kg', 'type' => 'DECIMAL(12,4)', 'nullable' => false],
                            ['name' => 'volumetric_weight_kg', 'type' => 'DECIMAL(12,4)', 'nullable' => false],
                            ['name' => 'chargeable_weight_kg', 'type' => 'DECIMAL(12,4)', 'nullable' => false],
                            ['name' => 'freight_amount', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'status', 'type' => 'VARCHAR(32)', 'enum_ref' => 'ShipmentStatus'],
                        ],
                        'unique_constraints' => [
                            ['name' => 'shipments_org_awb_unique', 'rule' => 'UNIQUE (organization_id, awb_number)'],
                        ],
                    ],
                    [
                        'table' => 'proof_of_deliveries',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'shipment_id', 'type' => 'ULID', 'foreign' => 'shipments.id', 'index' => true],
                            ['name' => 'recipient_name', 'type' => 'VARCHAR(120)', 'nullable' => false],
                            ['name' => 'gps_latitude', 'type' => 'DECIMAL(10,7)', 'nullable' => false],
                            ['name' => 'gps_longitude', 'type' => 'DECIMAL(10,7)', 'nullable' => false],
                            ['name' => 'photo_sha256', 'type' => 'VARCHAR(64)', 'nullable' => false],
                        ],
                        'unique_constraints' => [],
                    ],
                ],
                'state_machines' => [
                    [
                        'entity' => 'Shipment',
                        'enum_class' => 'App\\Enums\\ShipmentStatus',
                        'initial_state' => 'BOOKED',
                        'states' => ['BOOKED', 'IN_TRANSIT_HUB', 'OUT_FOR_DELIVERY', 'DELIVERED_POD', 'FAILED_ATTEMPT', 'RETURNED_TO_SHIPPER'],
                        'transitions' => [
                            [
                                'from' => ['OUT_FOR_DELIVERY'],
                                'to' => 'DELIVERED_POD',
                                'action' => 'CompleteDeliveryWithPodAction',
                                'guards' => ['HasPhotoSignatureAndGpsCoordinatesGuard'],
                                'side_effects' => ['ReleaseCodEscrowToLedgerAction', 'SendConsigneePodReceipt'],
                            ],
                        ],
                    ],
                ],
                'concurrency_contracts' => [
                    [
                        'operation' => 'Hub Barcode Scan & Manifest Handoff',
                        'mechanism' => 'PESSIMISTIC_LOCK on shipments row + idempotent scan event key',
                        'timeout_seconds' => 4,
                        'fallback_action' => 'Ignore duplicate handheld scanner bursts within 60s window.',
                    ],
                ],
                'rbac_permissions' => [
                    ['action' => 'shipment.scan_hub', 'allowed' => ['OPERATIONAL_STAFF', 'TENANT_OWNER']],
                    ['action' => 'shipment.override_weight', 'allowed' => ['TENANT_OWNER', 'FINANCE_AUDITOR'], 'requires_reason' => true, 'audit_logged' => true],
                ],
                'edge_case_playbooks' => [
                    [
                        'scenario' => 'Kurir gagal menyerahkan paket COD karena penerima tidak di tempat sebanyak 3 kali berturut-turut.',
                        'resolution_rule' => 'Setelah percobaan FAILED_ATTEMPT ke-3, otomatis transisikan status ke RETURNED_TO_SHIPPER, kunci penagihan ongkir retur sesuai kontrak pengirim, dan batalkan ekspektasi piutang COD pada buku besar.',
                    ],
                ],
                'gemini_tool_declarations' => [
                    [
                        'name' => 'track_awb_shipment_status',
                        'description' => 'Melacak posisi hub terakhir, berat tertagih, dan status Proof of Delivery nomor resi AWB.',
                        'parameters' => ['awb_number' => 'string'],
                    ],
                ],
                'is_active' => true,
            ],

            // 10. PRIMITIVE_HIGH_CONCURRENCY_TICKETING (Sector 10)
            [
                'code' => 'PRIMITIVE_HIGH_CONCURRENCY_TICKETING',
                'name' => [
                    'id' => 'Penguncian Kursi/Tiket Konkurensi Tinggi & Pembagian Royalti Kreator',
                    'en' => 'High-Concurrency Seat Locking, Anti-Scalp Pass & Creator Royalty Engine',
                ],
                'category' => 'MEDIA',
                'description' => [
                    'id' => 'Tabel event_seats dan royalty_splits untuk war tiket bebas bentrok, QR check-in HMAC sekali pakai, dan bagi hasil kreator.',
                    'en' => 'Tables event_seats and royalty_splits handling flash-sale seat locks, single-scan HMAC passes, and royalty splits.',
                ],
                'detection_keywords' => ['tiket', 'kursi', 'seat', 'konser', 'stadion', 'royalti', 'komisi kreator', 'event'],
                'schema_blueprints' => [
                    [
                        'table' => 'event_seats',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'event_code', 'type' => 'VARCHAR(48)', 'nullable' => false, 'index' => true],
                            ['name' => 'seat_label', 'type' => 'VARCHAR(32)', 'nullable' => false],
                            ['name' => 'price_amount', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'locked_until', 'type' => 'TIMESTAMP', 'nullable' => true],
                            ['name' => 'checkin_hmac_token', 'type' => 'VARCHAR(64)', 'nullable' => true],
                            ['name' => 'status', 'type' => 'VARCHAR(24)', 'enum_ref' => 'EventSeatStatus'],
                        ],
                        'unique_constraints' => [
                            ['name' => 'event_seats_org_event_seat_unique', 'rule' => 'UNIQUE (organization_id, event_code, seat_label)'],
                        ],
                    ],
                ],
                'state_machines' => [
                    [
                        'entity' => 'EventSeat',
                        'enum_class' => 'App\\Enums\\EventSeatStatus',
                        'initial_state' => 'AVAILABLE',
                        'states' => ['AVAILABLE', 'HELD_TTL', 'ISSUED', 'REDEEMED_GATE'],
                        'transitions' => [
                            [
                                'from' => ['AVAILABLE'],
                                'to' => 'HELD_TTL',
                                'action' => 'AcquireSeatHoldAction',
                                'guards' => ['MaxTicketsPerIdentityNotExceededGuard'],
                                'side_effects' => ['SetSeatHoldTtl10Minutes'],
                            ],
                            [
                                'from' => ['ISSUED'],
                                'to' => 'REDEEMED_GATE',
                                'action' => 'RedeemGateQrTicketAction',
                                'guards' => ['ValidHmacSignatureAndFirstScanGuard'],
                                'side_effects' => ['InvalidateQrPassAgainstReplay'],
                            ],
                        ],
                    ],
                ],
                'concurrency_contracts' => [
                    [
                        'operation' => 'Flash-Sale Seat Acquisition & Gate Turnstile Scan',
                        'mechanism' => 'PESSIMISTIC_LOCK (SELECT ... FROM event_seats WHERE id = ? FOR UPDATE SKIP LOCKED for best-available)',
                        'timeout_seconds' => 3,
                        'fallback_action' => "Throw DomainException('Kursi baru saja dikunci pembeli lain; silakan pilih kursi sebelah').",
                    ],
                ],
                'rbac_permissions' => [
                    ['action' => 'ticket.acquire_hold', 'allowed' => ['CUSTOMER', 'OPERATIONAL_STAFF']],
                    ['action' => 'ticket.gate_scan', 'allowed' => ['OPERATIONAL_STAFF', 'TENANT_OWNER'], 'audit_logged' => true],
                ],
                'edge_case_playbooks' => [
                    [
                        'scenario' => 'Dua penonton memindai tangkapan layar QR tiket yang sama di dua pintu masuk stadion berbeda pada detik yang sama.',
                        'resolution_rule' => 'Gunakan SELECT ... FOR UPDATE pada baris event_seats saat scan gate. Pemindaian pertama mengubah ISSUED -> REDEEMED_GATE dalam <5ms; pemindaian kedua langsung ditolak dengan peringatan duplikasi pintu dan lokasi gerbang pertama.',
                    ],
                ],
                'gemini_tool_declarations' => [
                    [
                        'name' => 'get_event_seat_occupancy',
                        'description' => 'Menghitung kapasitas tiket terjual, kursi sedang ditahan (TTL), dan estimasi royalti bersih.',
                        'parameters' => ['event_code' => 'string'],
                    ],
                ],
                'is_active' => true,
            ],

            // 11. PRIMITIVE_HRIS_PAYROLL_ROSTER (Sector 11)
            [
                'code' => 'PRIMITIVE_HRIS_PAYROLL_ROSTER',
                'name' => [
                    'id' => 'Roster Shift Absensi & Mesin Penggajian (Payroll PPh 21 / BPJS)',
                    'en' => 'Shift Roster Attendance & Deterministic Payroll Engine (PPh 21 / BPJS)',
                ],
                'category' => 'HR',
                'description' => [
                    'id' => 'Tabel employees, payroll_runs, dan payroll_payslips dengan snapshot komponen tunjangan, potongan, PPh 21 TER, dan BPJS.',
                    'en' => 'Tables employees, payroll_runs, and immutable payroll_payslips calculating allowances, deductions, tax, and social security.',
                ],
                'detection_keywords' => ['payroll', 'gaji', 'hris', 'karyawan', 'slip gaji', 'pph 21', 'bpjs', 'shift', 'roster', 'absensi'],
                'schema_blueprints' => [
                    [
                        'table' => 'employees',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'employee_number', 'type' => 'VARCHAR(32)', 'nullable' => false],
                            ['name' => 'ptkp_tax_category', 'type' => 'VARCHAR(16)', 'nullable' => false],
                            ['name' => 'base_salary', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'status', 'type' => 'VARCHAR(24)', 'nullable' => false],
                        ],
                        'unique_constraints' => [
                            ['name' => 'employees_org_number_unique', 'rule' => 'UNIQUE (organization_id, employee_number)'],
                        ],
                    ],
                    [
                        'table' => 'payroll_runs',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'period_ym', 'type' => 'CHAR(7)', 'nullable' => false],
                            ['name' => 'gross_total', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'tax_pph21_total', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'net_disbursed_total', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'status', 'type' => 'VARCHAR(24)', 'enum_ref' => 'PayrollRunStatus'],
                        ],
                        'unique_constraints' => [
                            ['name' => 'payroll_runs_org_period_unique', 'rule' => 'UNIQUE (organization_id, period_ym)'],
                        ],
                    ],
                ],
                'state_machines' => [
                    [
                        'entity' => 'PayrollRun',
                        'enum_class' => 'App\\Enums\\PayrollRunStatus',
                        'initial_state' => 'DRAFT_CALCULATION',
                        'states' => ['DRAFT_CALCULATION', 'LOCKED_FOR_APPROVAL', 'DISBURSED_SEALED'],
                        'transitions' => [
                            [
                                'from' => ['LOCKED_FOR_APPROVAL'],
                                'to' => 'DISBURSED_SEALED',
                                'action' => 'FinalizeAndDisbursePayrollRunAction',
                                'guards' => ['DualExecutiveAndFinanceApprovalGuard'],
                                'side_effects' => ['PostPayrollExpenseAndTaxPayableJournal', 'SealPayslipSnapshotsImmutable'],
                            ],
                        ],
                    ],
                ],
                'concurrency_contracts' => [
                    [
                        'operation' => 'Monthly Payroll Run Lock & Disbursement',
                        'mechanism' => 'UNIQUE (organization_id, period_ym) + PESSIMISTIC_LOCK on payroll_runs',
                        'timeout_seconds' => 10,
                        'fallback_action' => 'Block attendance retro-edits once period enters LOCKED_FOR_APPROVAL; carry adjustments to next period.',
                    ],
                ],
                'rbac_permissions' => [
                    ['action' => 'payroll.calculate', 'allowed' => ['TENANT_OWNER', 'FINANCE_AUDITOR']],
                    ['action' => 'payroll.disburse', 'allowed' => ['TENANT_OWNER'], 'audit_logged' => true],
                ],
                'edge_case_playbooks' => [
                    [
                        'scenario' => 'Karyawan baru bergabung pada tanggal 12 atau resign pada tanggal 20 di tengah periode penggajian.',
                        'resolution_rule' => 'Hitung gaji pokok secara prorata berbasis hari kerja efektif kalender perusahaan dengan pembulatan DECIMAL(19,4) HALF_UP, sementara tarif PPh 21 TER dihitung terhadap penghasilan bruto aktual yang diterima bulan tersebut.',
                    ],
                ],
                'gemini_tool_declarations' => [
                    [
                        'name' => 'summarize_payroll_period_totals',
                        'description' => 'Menampilkan ringkasan total gaji bruto, potongan PPh 21/BPJS, dan take-home pay periode tertentu.',
                        'parameters' => ['period_ym' => 'string'],
                    ],
                ],
                'is_active' => true,
            ],

            // 12. PRIMITIVE_FUND_DISBURSEMENT_IMPACT (Sector 12)
            [
                'code' => 'PRIMITIVE_FUND_DISBURSEMENT_IMPACT',
                'name' => [
                    'id' => 'Akuntabilitas Penyaluran Dana Program (Restricted Fund) & Metrik Dampak',
                    'en' => 'Restricted Program Fund Disbursement & Verified Impact Metrics Engine',
                ],
                'category' => 'GOVTECH',
                'description' => [
                    'id' => 'Tabel fund_programs dan fund_disbursements untuk menjaga dana amanah/terikat (ISAK 35 / PSAK 109) tidak tercampur operasional.',
                    'en' => 'Tables fund_programs and fund_disbursements preventing cross-subsidy of restricted donor funds.',
                ],
                'detection_keywords' => ['yayasan', 'donasi', 'penyaluran', 'program sosial', 'zakat', 'infak', 'relawan', 'penerima manfaat', 'isak 35'],
                'schema_blueprints' => [
                    [
                        'table' => 'fund_programs',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'code', 'type' => 'VARCHAR(40)', 'nullable' => false],
                            ['name' => 'title', 'type' => 'JSONB', 'nullable' => false],
                            ['name' => 'restriction_type', 'type' => 'VARCHAR(24)', 'nullable' => false],
                            ['name' => 'ledger_account_id', 'type' => 'ULID', 'foreign' => 'accounts.id'],
                            ['name' => 'collected_amount', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'disbursed_amount', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                        ],
                        'unique_constraints' => [
                            ['name' => 'fund_programs_org_code_unique', 'rule' => 'UNIQUE (organization_id, code) + CHECK (disbursed_amount <= collected_amount)'],
                        ],
                    ],
                    [
                        'table' => 'fund_disbursements',
                        'tenant_scoped' => true,
                        'soft_deletes' => false,
                        'columns' => [
                            ['name' => 'id', 'type' => 'ULID', 'primary' => true],
                            ['name' => 'organization_id', 'type' => 'ULID', 'foreign' => 'organizations.id', 'index' => true],
                            ['name' => 'fund_program_id', 'type' => 'ULID', 'foreign' => 'fund_programs.id', 'index' => true],
                            ['name' => 'beneficiary_count', 'type' => 'INTEGER', 'nullable' => false],
                            ['name' => 'amount', 'type' => 'DECIMAL(19,4)', 'nullable' => false],
                            ['name' => 'evidence_sha256', 'type' => 'VARCHAR(64)', 'nullable' => true],
                            ['name' => 'status', 'type' => 'VARCHAR(24)', 'enum_ref' => 'FundDisbursementStatus'],
                        ],
                        'unique_constraints' => [],
                    ],
                ],
                'state_machines' => [
                    [
                        'entity' => 'FundDisbursement',
                        'enum_class' => 'App\\Enums\\FundDisbursementStatus',
                        'initial_state' => 'PROPOSED',
                        'states' => ['PROPOSED', 'APPROVED_FOR_RELEASE', 'DISBURSED', 'IMPACT_VERIFIED'],
                        'transitions' => [
                            [
                                'from' => ['APPROVED_FOR_RELEASE'],
                                'to' => 'DISBURSED',
                                'action' => 'ReleaseRestrictedProgramFundAction',
                                'guards' => ['ProgramRestrictedBalanceIsSufficientGuard'],
                                'side_effects' => ['PostRestrictedFundReductionJournal', 'EmitPublicTransparencyReceipt'],
                            ],
                        ],
                    ],
                ],
                'concurrency_contracts' => [
                    [
                        'operation' => 'Restricted Fund Balance Guard on Disbursement',
                        'mechanism' => 'PESSIMISTIC_LOCK on fund_programs row inside DB::transaction()',
                        'timeout_seconds' => 5,
                        'fallback_action' => "Throw DomainException('Dana terikat (RESTRICTED) pada program ini tidak mencukupi; dilarang memotong lintas program tanpa persetujuan amandemen donatur').",
                    ],
                ],
                'rbac_permissions' => [
                    ['action' => 'fund.propose_disbursement', 'allowed' => ['OPERATIONAL_STAFF', 'TENANT_OWNER']],
                    ['action' => 'fund.verify_impact', 'allowed' => ['FINANCE_AUDITOR', 'TENANT_OWNER'], 'audit_logged' => true],
                ],
                'edge_case_playbooks' => [
                    [
                        'scenario' => 'Program bantuan bencana darurat telah selesai 100%, namun masih terdapat sisa saldo dana terikat (RESTRICTED) sebesar Rp 4.500.000.',
                        'resolution_rule' => 'Sistem melarang pemindahan saldo langsung ke kas operasional umum. Wajib melalui ReclassifyRestrictedSurplusAction yang mencatat dokumen persetujuan dewan pengawas/donatur (sesuai ISAK 35) untuk mengalihkan sisa dana ke Program Tanggap Bencana Berikutnya.',
                    ],
                ],
                'gemini_tool_declarations' => [
                    [
                        'name' => 'audit_program_fund_accountability',
                        'description' => 'Memeriksa rasio dana terkumpul vs tersalurkan serta kelengkapan bukti dampak per program yayasan.',
                        'parameters' => ['program_code' => 'string'],
                    ],
                ],
                'is_active' => true,
            ],
        ];

        $ultimateContracts = PosStackPresetCatalog::primitiveUltimateContracts();

        return array_map(static function (array $prim) use ($ultimateContracts): array {
            $code = $prim['code'] ?? '';
            $extra = $ultimateContracts[$code] ?? [];

            $prim['schema_blueprints'] = array_map(static function (array $tbl): array {
                if (! isset($tbl['deletion_policy'])) {
                    $policy = PosStackPresetCatalog::resolveTableDeletionPolicy((string) ($tbl['table'] ?? ''));
                    $tbl = array_merge($tbl, $policy);
                }

                return $tbl;
            }, $prim['schema_blueprints'] ?? []);

            $prim['deterministic_formulas'] = $extra['deterministic_formulas'] ?? ($prim['deterministic_formulas'] ?? []);
            $prim['saga_and_webhook_contracts'] = $extra['saga_and_webhook_contracts'] ?? ($prim['saga_and_webhook_contracts'] ?? []);
            $prim['ui_screen_archetypes'] = $extra['ui_screen_archetypes'] ?? ($prim['ui_screen_archetypes'] ?? []);
            $prim['acceptance_test_scenarios'] = $extra['acceptance_test_scenarios'] ?? ($prim['acceptance_test_scenarios'] ?? []);

            return $prim;
        }, $basePrimitives);
    }

    /**
     * Master Anti-Slop Rule Packs (UI/UX, Database, Backend, Infrastructure, Agent Harness).
     */
    public static function rulePacks(): array
    {
        return [
            [
                'slug' => 'anti-slop-ui-linear-style',
                'name' => [
                    'id' => 'Zero-Slop UI/UX Design System Contract (Linear / Vercel / Stripe Tier)',
                    'en' => 'Zero-Slop UI/UX Design System Contract (Linear / Vercel / Stripe Tier)',
                ],
                'layer' => 'UI_UX',
                'enforcement_level' => 'HARAM',
                'bad_pattern_example' => '<button class="rounded-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 shadow-2xl hover:scale-105" onclick="alert(\'Saved\')">Save</button>',
                'good_pattern_example' => '<button type="button" class="h-9 px-3.5 rounded-md bg-zinc-900 dark:bg-emerald-600 text-white dark:text-zinc-950 border border-zinc-900 dark:border-emerald-500 text-sm font-medium transition-colors">Simpan</button>',
                'markdown_directive' => 'HARAM rounded-full capsule buttons, cheesy multi-color gradients, thick drop shadows (shadow-2xl), hover:scale-105, and native alert()/confirm()/prompt(). WAJIB subtle corners (rounded-xs/rounded-sm/rounded-md max 6px), crisp 1px hairline borders, tabular-nums on all financial/metric digits, Width::Full + 12-column asymmetrical grid on Filament, and in-place TranslatableTabs (no global LocaleSwitcher).',
                'is_global_default' => true,
            ],
            [
                'slug' => 'strict-pgsql16-ulid-cursor',
                'name' => [
                    'id' => 'PostgreSQL 16 Strict ULID Primary Keys & Keyset Cursor Pagination O(1)',
                    'en' => 'PostgreSQL 16 Strict ULID Primary Keys & Keyset Cursor Pagination O(1)',
                ],
                'layer' => 'DATABASE',
                'enforcement_level' => 'WAJIB',
                'bad_pattern_example' => '$table->id(); // or $table->uuid(\'id\') with HasUlids; Model::paginate(15); ->whereRaw("YEAR(created_at) = 2026");',
                'good_pattern_example' => '$table->ulid(\'id\')->primary(); $table->foreignUlid(\'organization_id\'); Model::query()->orderBy(\'id\', \'desc\')->cursorPaginate(20);',
                'markdown_directive' => 'WAJIB $table->ulid(\'id\')->primary() + HasUlids + foreignUlid() pada semua tabel bisnis. HARAM $table->id() AUTO_INCREMENT dan HARAM $table->uuid(\'id\') untuk ULID di PostgreSQL (SQLSTATE[22P02]). HARAM ->paginate() OFFSET O(N); WAJIB ->cursorPaginate() O(1). HARAM fungsi spesifik MySQL (YEAR(), JSON_UNQUOTE(), string kosong "" pada JSONB).',
                'is_global_default' => true,
            ],
            [
                'slug' => 'immutable-decimal-ledger-lock',
                'name' => [
                    'id' => 'Integritas Finansial DECIMAL(19,4), Immutable Ledger & Pessimistic Lock',
                    'en' => 'DECIMAL(19,4) Financial Precision, Immutable Ledger & Pessimistic Locking',
                ],
                'layer' => 'BACKEND',
                'enforcement_level' => 'HARAM',
                'bad_pattern_example' => '$table->float(\'amount\'); $wallet->update([\'balance\' => $wallet->balance - $price]);',
                'good_pattern_example' => 'DB::transaction(function () use ($resourceId) { $res = Resource::whereKey($resourceId)->lockForUpdate()->firstOrFail(); /* append journal_lines / inventory_movements */ });',
                'markdown_directive' => 'HARAM float/double untuk uang atau stok; WAJIB DECIMAL(19,4) (pembulatan HALF_UP) atau BIGINT minor units. HARAM UPDATE saldo/stok langsung tanpa baris mutasi; WAJIB melalui Action class invokable di dalam DB::transaction() dengan lockForUpdate().',
                'is_global_default' => true,
            ],
            [
                'slug' => 'local-asset-sovereignty-livewire4',
                'name' => [
                    'id' => '100% Local Asset Sovereignty (Zero CDN) & Livewire 4 FilePond Protection',
                    'en' => '100% Local Asset Sovereignty (Zero CDN) & Livewire 4 FilePond Protection',
                ],
                'layer' => 'BACKEND',
                'enforcement_level' => 'HARAM',
                'bad_pattern_example' => '<script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script> // TextInput::make(\'title\')->live(onBlur: true) next to FileUpload',
                'good_pattern_example' => '@vite([\'resources/css/app.css\', \'resources/js/app.js\']) // Local public/js/vendor/mermaid.min.js + client-side Alpine slugging',
                'markdown_directive' => 'HARAM memuat CDN eksternal (unpkg, cdnjs, jsdelivr, external QR API). Semua aset wajib lokal via Vite atau public/js/vendor/. HARAM memasang ->live(onBlur: true) pada form dengan FileUpload/CuratorPicker di Livewire 4. HARAM memasukkan kolom Spatie $translatable ke dalam $casts array.',
                'is_global_default' => true,
            ],
            [
                'slug' => 'railpack-frankenphp-rag-native-tools',
                'name' => [
                    'id' => 'Railpack FrankenPHP Deployment, pgvector RAG & Native Gemini Tool Calling',
                    'en' => 'Railpack FrankenPHP Deployment, pgvector RAG & Native Gemini Tool Calling',
                ],
                'layer' => 'INFRASTRUCTURE',
                'enforcement_level' => 'WAJIB',
                'bad_pattern_example' => '// nixpacks.toml legacy config or parsing fake <agentic_actions> SQL strings from LLM text output',
                'good_pattern_example' => '// railpack.json + FrankenPHP Caddyfile + deploy.sh 6-scenario + Gemini tools.functionDeclarations with 120s idempotency guard & SHA-256 audit log',
                'markdown_directive' => 'WAJIB menggunakan Railpack (railpack.json) + FrankenPHP Caddyfile + deploy.sh 6-skenario. Pisahkan Read AI (PostgreSQL pgvector 768-dim HNSW) dari Write AI (resmi Gemini functionDeclarations + Validator::make() + DB::transaction() + 120s Idempotency Guard + SHA-256 Audit Log).',
                'is_global_default' => true,
            ],
            [
                'slug' => 'ponytail-ecc-6stage-agent-harness',
                'name' => [
                    'id' => 'Ponytail 4-Rung Ladder + 6-Stage ECC Loop & File-Context Instincts',
                    'en' => 'Ponytail 4-Rung Ladder + 6-Stage ECC Loop & File-Context Instincts',
                ],
                'layer' => 'AGENT_HARNESS',
                'enforcement_level' => 'WAJIB',
                'bad_pattern_example' => '// Blind full-repo file scanning, skipping Pest tests for state guards, rewriting entire files without updating graph.json',
                'good_pattern_example' => '// 1. PLAN (graph.json O(1)) -> 2. TEST (Pest Guards & Locks) -> 3. IMPLEMENT (Action + ULID) -> 4. REVIEW (Anti-Slop) -> 5. VERIFY -> 6. REMEMBER & DEPLOY',
                'markdown_directive' => 'WAJIB mematuhi Ponytail 4-Rung Ladder (Native First, Graphify O(1) Lookup, Declarative Over Procedural, Surgical Diff) dan siklus 6-tahap ECC (PLAN -> TEST -> IMPLEMENT -> REVIEW -> VERIFY -> REMEMBER & DEPLOY) beserta File-Context Instincts otomatis.',
                'is_global_default' => true,
            ],
        ];
    }
}