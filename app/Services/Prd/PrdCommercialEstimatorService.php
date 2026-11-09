<?php

namespace App\Services\Prd;

use App\Models\VisionBlueprint;
use App\Services\PrdGeneratorService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PrdCommercialEstimatorService
{
    /**
     * Synthesize world-class domain business feasibility, killer ROI equation,
     * cost of inaction, and Neriah Pro no-regret guarantees.
     */
    public static function generateBusinessRoiAnalysis(VisionBlueprint $blueprint, array $itemizedEstimation, bool $isEn = false): array
    {
        $businessName = $blueprint->nama_bisnis ?: ($blueprint->client_name . "'s Project");
        $masalah = strtolower($blueprint->masalah_utama ?? '');
        $tujuan = strtolower($blueprint->tujuan_utama ?? '');
        $fitur = strtolower($blueprint->fitur_wajib ?? '');
        $corpus = strtolower($businessName . ' ' . $masalah . ' ' . $tujuan . ' ' . $fitur);

        $totalInvestasi = (float) ($itemizedEstimation['base_subtotal'] ?? 10000000.00);
        $dpInvestasi = (float) ($itemizedEstimation['standard_dp'] ?? ($totalInvestasi * 0.50));

        // Detect Domain
        $isTravel = str_contains($corpus, 'tour') || str_contains($corpus, 'travel') || str_contains($corpus, 'wisata') ||
                    str_contains($corpus, 'itinerary') || str_contains($corpus, 'trip') || str_contains($corpus, 'overland') ||
                    str_contains($corpus, 'ngurah rai') || str_contains($corpus, 'gayabalical') || str_contains($corpus, 'day tour') ||
                    str_contains($corpus, 'transfer in') || str_contains($corpus, 'transfer out') || str_contains($corpus, 'bandara');

        $isProperty = !$isTravel && (
                      str_contains($corpus, 'villa') || str_contains($corpus, 'properti') || str_contains($corpus, 'property') ||
                      str_contains($corpus, 'sewa villa') || str_contains($corpus, 'sewa properti') || str_contains($corpus, 'rental villa') ||
                      str_contains($corpus, 'tanah') || str_contains($corpus, 'land') || str_contains($corpus, 'leasehold') ||
                      str_contains($corpus, 'kavling') || str_contains($corpus, 'broker properti') || str_contains($corpus, 'arebi') ||
                      str_contains($corpus, 'listing properti') || str_contains($corpus, 'real estate'));

        $isClinic = str_contains($corpus, 'klinik') || str_contains($corpus, 'pasien') || str_contains($corpus, 'dokter') ||
                    str_contains($corpus, 'rekam medis') || str_contains($corpus, 'obat') || str_contains($corpus, 'apotek') ||
                    str_contains($corpus, 'antrean') || str_contains($corpus, 'kesehatan');

        $isLogistics = str_contains($corpus, 'logistik') || str_contains($corpus, 'ekspedisi') || str_contains($corpus, 'armada truk') ||
                       str_contains($corpus, 'truk kontainer') || str_contains($corpus, 'kontainer') || str_contains($corpus, 'pengiriman kargo') ||
                       str_contains($corpus, 'surat jalan') || str_contains($corpus, 'resi ekspedisi') || str_contains($corpus, 'waybill');

        $isChurch = str_contains($corpus, 'gereja') || str_contains($corpus, 'jemaat') || str_contains($corpus, 'ibadah') ||
                    str_contains($corpus, 'warta') || str_contains($corpus, 'doa') || str_contains($corpus, 'persembahan');

        $isCommerce = str_contains($corpus, 'marketplace') || str_contains($corpus, 'toko online') || str_contains($corpus, 'ecommerce') ||
                      str_contains($corpus, 'keranjang belanja') || str_contains($corpus, 'checkout produk');

        if ($isTravel) {
            $domainCategory = 'Tour & Travel, Ground Transport & Smart Itinerary Hub';
            $domainBadge = 'HIGH_VOLUME_TOURISM_EXPERIENCE';
            $ticketBenchmark = $isEn ? '$45 - $1,250 / tour package & fleet rental (Rp 650K - Rp 18.5M)' : 'Rp 650.000 - Rp 18.500.000 / paket tour & sewa armada';
            $roiHeadline = $isEn ? 'Direct Booking Automation with 0% OTA Commissions (15-25% saved) = Full ROI in 1-2 Holiday Season Months!' : 'Otomatisasi Booking Langsung Bebas Komisi OTA (15-25%) = Balik Modal dalam 1-2 Bulan Musim Liburan!';
            $roiFormula = $isEn ? 'Save 20% OTA Fee + Fleet & AI Itinerary Efficiency = $800 - $2,300 / month' : 'Hemat Potongan OTA 20% + Efisiensi Armada & Itinerary AI = Rp 12.000.000 - Rp 35.000.000 / bulan';
            $roiNarrative = $isEn
                ? ('In the tourism industry, third-party OTA platforms (such as Viator, Klook, Traveloka) take 15% to 25% commissions on every booking. By operating a self-hosted unified reservation platform (' . $businessName . '), 100% of revenue from airport transfers, day tours, and overland trips flows directly to the business treasury. The AI Itinerary Wish List generator accelerates traveler booking conversions from days to minutes, recouping the entire system investment (Rp ' . number_format($totalInvestasi, 0, ',', '.') . ') within the first 15-25 tour transactions.')
                : ('Dalam industri pariwisata Bali & Jawa Timur, platform OTA pihak ketiga (seperti Traveloka, Viator, Klook) memotong komisi 15% s/d 25% untuk setiap pemesanan. Dengan memiliki platform reservasi mandiri terpadu (' . $businessName . '), setiap pemesanan transfer bandara Ngurah Rai, paket day tour Bali, dan overland Bromo/Ijen langsung masuk 100% ke kas perusahaan. Fitur Wish List Itinerary AI mempercepat penutupan booking wisatawan dari hitungan hari menjadi hitungan menit, melunasi total investasi sistem (Rp ' . number_format($totalInvestasi, 0, ',', '.') . ') hanya dari 15-25 transaksi reservasi tour pertama.');
            $projectedAnnualRoi = $isEn ? '380% - 950% from saved OTA commissions and accelerated booking conversions' : '380% - 950% dari penghematan komisi OTA dan lonjakan closing booking wisatawan';
            $inactionRisks = $isEn ? [
                'Loss of International & Domestic Travelers: Tourists require an online catalog with vehicle types, luggage capacities, and transparent pricing. The absence of a professional website drives prospective clients to competitors.',
                'Manual Itinerary Planning Is Excessively Slow: Calculating overland routes, distances, and pricing manually keeps clients waiting for hours until they book elsewhere.',
                'Schedule Collision & No-Show Risks: Without a centralized fleet management system and automated booking deposits, driver scheduling conflicts and uncompensated cancellations harm daily operations.',
            ] : [
                'Kehilangan Wisatawan Mancanegara & Domestik: Turis menuntut katalog online dengan pilihan jenis kendaraan, kapasitas penumpang/bagasi, dan harga transparan. Ketiadaan website profesional membuat calon klien beralih ke biro perjalanan lain.',
                'Proses Penyusunan Itinerary Manual Sangat Lambat: Menghitung rute, jarak, dan harga overland Jawa Timur secara manual membuat calon wisatawan menunggu berjam-jam hingga akhirnya memesan di tempat lain.',
                'Risiko Jadwal Bentrok & No-Show: Tanpa sistem pencatatan armada terpadu dan pembayaran uang muka (DP) otomatis, risiko jadwal supir bentrok dan pembatalan sepihak merugikan operasional harian.',
            ];
            $phasedStrategy = $isEn
                ? 'Start with Phase 1 Turnkey MVP (Airport Transfer Reservations, Day Tour Packages, Overland Trips, AI Wish List Generator, WhatsApp Integration, & Filament v5 Fleet Dashboard) with 50/50 milestones. Production-ready within 14-21 business days.'
                : 'Mulai dengan Fase 1 Turnkey MVP (Reservasi Transfer Bandara, Paket Day Tour Bali, Overland Bromo-Ijen, Generator Wish List AI, Integrasi WhatsApp, & Dasbor Armada Filament v5) dengan termin 50/50. Sistem siap go-live dalam 14-21 hari kerja.';
        } elseif ($isProperty) {
            $domainCategory = 'Villa Rental & Real Estate Agency';
            $domainBadge = 'HIGH_TICKET_COMMERCIAL';
            $ticketBenchmark = $isEn ? '$12,000 - $45,000 / sale or rental deal' : 'Rp 180.000.000 - Rp 650.000.000 / transaksi sewa/jual';
            $roiHeadline = $isEn ? 'Just 1 Closed Villa Rental Deal = 100% System Investment Breakeven!' : 'Cukup 1 Transaksi Closing Sewa Villa = 100% BEP Lunas!';
            $roiFormula = $isEn ? '5% - 10% Commission on 1 Annual Villa Lease ($15k) = $750 - $1,500' : 'Komisi 5% - 10% dari 1 Villa Sewa Tahunan (Rp 220M) = Rp 11.000.000 - Rp 22.000.000';
            $roiNarrative = $isEn
                ? ('In the real estate and villa rental industry, average agency commissions range from 5% to 10%. Closing just one standard annual villa lease pays off 100% of the entire Neriah Pro system investment. Every closing thereafter is 100% pure profit.')
                : ('Dalam industri real estate & rental villa di Bali, komisi rata-rata broker adalah 5% s/d 10%. Sekali Anda berhasil melakukan closing pada 1 unit villa sewa tahunan standar (misal: villa di Sanur seharga Rp 220 juta/tahun), komisi yang Anda kantongi (Rp 11 juta s/d Rp 22 juta) langsung melunasi 100% total biaya investasi pembuatan sistem Neriah Pro (Rp ' . number_format($totalInvestasi, 0, ',', '.') . '). Setiap closing berikutnya adalah 100% keuntungan murni Anda.');
            $projectedAnnualRoi = $isEn ? '450% - 1,200% (Realistic estimate of 3 - 6 closed deals in Year 1)' : '450% - 1.200% (Estimasi realistis 3 - 6 transaksi closing di tahun pertama)';
            $inactionRisks = $isEn ? [
                'Loss of Expat / High-Net-Worth Renters: High-value clients demand a verified online catalog with HD photography and transparent amenities. Without a professional web platform, they immediately turn to licensed agencies.',
                'Villa Owners Reluctant to Entrust Listings: Multi-million dollar property owners will not entrust valuable listings to agents relying only on personal WhatsApp status or social media feeds.',
                'Unstructured & Lost Leads: Buyer inquiries get buried in personal chat histories with zero pipeline visibility into property availability status (Available / Rented / Sold).',
            ] : [
                'Kehilangan Calon Penyewa Expat / Turis: Wisatawan mancanegara menuntut katalog online dengan galeri foto jernih dan detail fasilitas transparan. Ketiadaan website membuat mereka langsung lari ke broker berlisensi lain.',
                'Pemilik Villa (Owner) Enggan Menitipkan Listing: Pemilik villa mewah ratusan juta ragu mempercayakan aset berharganya kepada agen yang hanya mengandalkan status WhatsApp atau feed Instagram pribadi.',
                'Lead Tercecer & Tidak Terekam: Pertanyaan calon penyewa hilang di tumpukan obrolan chat pribadi tanpa pipeline status ketersediaan (Available / Rented / Sold).',
            ];
            $phasedStrategy = $isEn
                ? 'Start with Phase 1 Lean Catalog MVP (Property Listings, Amenities Breakdown, HD Gallery, Direct WhatsApp Concierge, & Filament v5 Admin) with 50/50 milestones. Live within 14-21 business days.'
                : 'Mulai dengan Fase 1 Lean Catalog MVP (Listing Properti, Detail Fasilitas, Galeri HD, Direct WhatsApp Concierge, & Dasbor Admin Filament v5) dengan termin 50/50. Website langsung beroperasi dalam 14-21 hari kerja untuk menangkap momentum sewa tanpa beban modal besar di awal.';
        } elseif ($isClinic) {
            $domainCategory = 'Healthcare & Clinical Information System';
            $domainBadge = 'HIGH_IMPACT_HEALTHCARE';
            $ticketBenchmark = $isEn ? 'Hundreds of daily patient visits & prescriptions' : 'Ratusan kunjungan pasien & resep obat harian';
            $roiHeadline = $isEn ? 'Time Savings & Pharmacy Inventory Leakage Control = Full ROI in 2-3 Months' : 'Efisiensi Waktu & Pengendalian Selisih Stok Obat = Balik Modal dalam 2-3 Bulan';
            $roiFormula = $isEn ? 'Reduced Waiting Hours + Pharmacy Loss Elimination = $400 - $1,100 / month' : 'Penghematan Jam Antrean + Pengendalian Kebocoran Obat = Rp 5.000.000 - Rp 15.000.000 / bulan';
            $roiNarrative = $isEn
                ? 'Digitalized patient intake slashes wait times by 70%, allowing the clinic to serve 20% more patients daily. Automated pharmacy stock reconciliation eliminates inventory discrepancies and expired drug write-offs that cost clinics thousands monthly.'
                : 'Digitalisasi pendaftaran pasien memangkas waktu tunggu hingga 70%, memungkinkan klinik melayani 20% lebih banyak pasien harian. Otomatisasi mutasi stok obat apotek mencegah selisih persediaan dan obat kedaluwarsa yang kerap merugikan klinik belasan juta per bulan.';
            $projectedAnnualRoi = $isEn ? '250% - 500% from operational efficiency and enhanced patient retention' : '250% - 500% dari efisiensi operasional dan peningkatan retensi pasien';
            $inactionRisks = $isEn ? [
                'Paper Medical Records Vulnerable to Damage / Loss: Regulatory non-compliance risks and slow cross-physician referrals.',
                'Overcrowded Waiting Rooms: Patients frustrated by manual queues switch to competing medical facilities.',
                'Undetected Pharmacy Inventory Discrepancies: Financial losses from manual registry books prone to accounting errors.',
            ] : [
                'Rekam Medis Kertas Rentan Rusak / Hilang: Risiko pelanggaran kepatuhan hukum dan lambatnya rujukan antar-dokter.',
                'Antrean Menumpuk di Ruang Tunggu: Pasien frustrasi menunggu pendaftaran manual dan beralih ke fasilitas kesehatan lain.',
                'Selisih Stok Obat Tidak Terdeteksi: Kerugian finansial akibat pencatatan manual buku apotek yang rentan salah hitung.',
            ];
            $phasedStrategy = $isEn
                ? 'Phase 1 focuses on Patient Registration, Protected Electronic Medical Records (EMR), and Pharmacy Stock Audits with 50/50 milestones before scaling to external health network integrations in Phase 2.'
                : 'Fase 1 memfokuskan pada Pendaftaran Pasien, Rekam Medis Terproteksi (RME), dan Mutasi Stok Apotek dengan termin 50/50 sebelum melangkah ke integrasi BPJS/SatuSehat di Fase 2.';
        } elseif ($isLogistics) {
            $domainCategory = 'Logistics & Fleet Operations Hub';
            $domainBadge = 'HIGH_CONCURRENCY_SUPPLY_CHAIN';
            $ticketBenchmark = $isEn ? 'Thousands of monthly waybills and shipping manifests' : 'Ribuan surat jalan & manifes pengiriman bulanan';
            $roiHeadline = $isEn ? 'Automated Waybills & Elimination of Disputed Claims = Full ROI in 1-2 Months' : 'Otomatisasi Surat Jalan & Eliminasi Selisih Klaim = Balik Modal dalam 1-2 Bulan';
            $roiFormula = $isEn ? 'Dispute Prevention + Admin Labor Savings = $600 - $1,500 / month' : 'Pencegahan Klaim Keterlambatan + Penghematan Waktu Admin = Rp 8.000.000 - Rp 20.000.000 / bulan';
            $roiNarrative = $isEn
                ? 'Slashes manifest reconciliation and Proof of Delivery (POD) processing from 3 days to real-time. Frees hundreds of staff hours and eliminates costly lost or misdelivered freight claims.'
                : 'Memangkas waktu rekonsiliasi manifes dan bukti serah terima (Proof of Delivery) dari 3 hari menjadi real-time. Membebaskan ratusan jam kerja staf operasional dan mengeliminasi klaim biaya akibat paket hilang atau salah antar.';
            $projectedAnnualRoi = $isEn ? '350% - 700% from fleet utilization and faster invoice collections' : '350% - 700% dari efisiensi armada dan kecepatan penagihan invoice';
            $inactionRisks = $isEn ? [
                'Paper Waybills Lost in Transit: Delays invoice collections and triggers payment disputes from corporate shippers.',
                'Support Team Paralyzed by Manual Inquiries: Valuable operational hours wasted answering "where is my shipment?".',
            ] : [
                'Surat Jalan Kertas Hilang di Lapangan: Menghambat proses penagihan faktur dan memicu komplain pembayaran dari klien korporat.',
                'Staf Lumpuh Meladeni Chat Manual: Waktu terbuang hanya untuk menjawab "paket saya sudah sampai mana?".',
            ];
            $phasedStrategy = $isEn
                ? 'Phase 1 automates digital waybills, GPS fleet tracking, and driver POD portal before scaling to multi-hub networks.'
                : 'Fase 1 mengotomatisasi pencatatan resi, tracking armada digital, dan portal tanda terima supir sebelum scale-up ke multi-cabang.';
        } elseif ($isChurch) {
            $domainCategory = 'Church Management & Faith Community OS';
            $domainBadge = 'COMMUNITY_STEWARDSHIP';
            $ticketBenchmark = $isEn ? 'Ministry serving hundreds to thousands of congregants' : 'Pelayanan ratusan hingga ribuan jemaat';
            $roiHeadline = $isEn ? 'Paperless Bulletins & Elimination of Expensive Recurring Foreign SaaS Subscriptions' : 'Efisiensi Warta Kertas & Bebas Biaya Langganan Bulanan Software Asing';
            $roiFormula = $isEn ? 'Saved Bulletin Printing + Zero Monthly SaaS Fees = $1,000 - $2,500 / year' : 'Hemat Cetak Kertas Warta + Hemat Biaya Software Asing = Rp 15.000.000 - Rp 35.000.000 / tahun';
            $roiNarrative = $isEn
                ? 'Eliminates weekly bulletin printing overhead and liberates the church from expensive recurring subscriptions to foreign software. Pay once, and the platform belongs to the church permanently.'
                : 'Menghilangkan beban cetak warta mingguan fisik dan membebaskan gereja dari biaya langganan bulanan software asing (seperti Elvanto/Planning Center) yang menguras kas pelayanan. Sekali bayar, sistem menjadi milik gereja selamanya.';
            $projectedAnnualRoi = $isEn ? 'Stewardship Efficiency: 100% of ministry resources directed back to community pastoral care' : 'Stewardship Efisien: 100% alokasi persembahan kembali untuk pelayanan jemaat';
            $inactionRisks = $isEn ? [
                'Wasted Physical Bulletins: Reams of discarded paper after every Sunday service.',
                'Fragmented Congregant Records: Family and baptismal records scattered across personal laptops of volunteers.',
            ] : [
                'Warta Cetak Mubazir: Puluhan rim kertas terbuang setiap minggu setelah ibadah selesai.',
                'Database Jemaat Terfragmentasi: Data keluarga dan baptisan tercecer di laptop pribadi masing-masing pengurus.',
            ];
            $phasedStrategy = $isEn
                ? 'Apply Neriah Pro Community Stewardship Subsidy with 50/50 milestones so digital ministry can launch immediately without financial strain.'
                : 'Menerapkan Subsidi Efisiensi Komunitas Neriah Pro dengan termin 50/50 agar pelayanan digital dapat langsung melayani jemaat tanpa kendala anggaran.';
        } elseif ($isCommerce) {
            $domainCategory = 'Independent Commercial Commerce Platform';
            $domainBadge = 'DIRECT_TO_CONSUMER';
            $ticketBenchmark = $isEn ? 'Daily retail transaction volume' : 'Volume transaksi ritel harian';
            $roiHeadline = $isEn ? 'Zero Marketplace Commission Cuts (15-20% saved) = Full ROI in 1-3 Months' : 'Bebas Potongan Komisi Marketplace (15-20%) = Balik Modal dalam 1-3 Bulan';
            $roiFormula = $isEn ? 'Save 15% Marketplace Fees on Gross Volume = $600 / month' : 'Penghematan 15% Fee Marketplace dari Omzet Rp 50M = Rp 7.500.000 / bulan';
            $roiNarrative = $isEn
                ? 'Every purchase routed through your independent portal preserves third-party platform cuts. Build a proprietary customer database with zero price wars.'
                : 'Setiap transaksi yang dialihkan ke portal mandiri langsung menghemat potongan fee pihak ketiga. Membangun basis data pelanggan milik sendiri tanpa perang harga.';
            $projectedAnnualRoi = $isEn ? '300% - 600% from preserved gross operating margins' : '300% - 600% dari margin keuntungan yang terselamatkan';
            $inactionRisks = $isEn ? [
                'Third-Party Marketplace Vulnerability: Risk of sudden account suspensions or algorithmic downranking.',
                'Zero Customer Data Ownership: Inability to run direct retention campaigns or private loyalty rewards.',
            ] : [
                'Ketergantungan Akun Pihak Ketiga: Risiko akun ditutup sepihak atau algoritma berubah mendadak.',
                'Data Pelanggan Bukan Milik Anda: Tidak bisa melakukan promosi langsung atau retensi pelanggan setia.',
            ];
            $phasedStrategy = $isEn
                ? 'Independent storefront launch with integrated Payment Gateway & Express Logistics within 14 business days.'
                : 'Peluncuran toko mandiri dengan integrasi Midtrans Snap & Kurir Ekspedisi dalam 14 hari kerja.';
        } else {
            $domainCategory = 'Custom Business Operations & Client Management Engine';
            $domainBadge = 'ENTERPRISE_OPERATIONS';
            $ticketBenchmark = $isEn ? 'Unified commercial operations' : 'Operasional komersial terpadu';
            $roiHeadline = $isEn ? 'Brand Credibility & Workflow Automation = Full ROI from First 1-2 Clients' : 'Kredibilitas Nilai Jual & Otomatisasi Alur Kerja = Balik Modal dari 1-2 Klien Pertama';
            $roiFormula = $isEn ? '1 New Enterprise Client Won via System Credibility = 100% System Investment Breakeven' : '1 Klien Baru Tertutup Berkat Kredibilitas Sistem = 100% BEP Investasi';
            $roiNarrative = $isEn
                ? 'A modern operational platform elevates your brand positioning before high-value enterprise clients. Automated record-keeping saves dozens of manual staff hours every week.'
                : 'Sistem operasional modern meningkatkan positioning dan nilai tawar brand Anda di mata calon klien bernilai tinggi. Efisiensi pencatatan otomatis menghemat puluhan jam kerja tim setiap pekan.';
            $projectedAnnualRoi = $isEn ? '250% - 500% within the first operational year' : '250% - 500% dalam tahun pertama operasional';
            $inactionRisks = $isEn ? [
                'Unprofessional Brand Impression: High-value clients hesitate to pay premium rates to businesses relying on manual spreadsheets.',
                'Human-Error Repetitive Workload: Valuable leadership time lost to manual data re-entry that machines should execute.',
            ] : [
                'Citra Usaha Terlihat Kurang Profesional: Klien ragu membayar mahal pada bisnis yang pencatatannya masih serba manual.',
                'Beban Kerja Berulang (Human-Error): Waktu berharga terbuang untuk rekap data rutin yang seharusnya bisa dikerjakan mesin.',
            ];
            $phasedStrategy = $isEn
                ? 'Launch with battle-tested Modern Monolith architecture ready for deployment in 14-21 business days.'
                : 'Mulai dengan arsitektur Modern Monolith teruji yang siap pakai dalam 14-21 hari kerja.';
        }

        $isRetail = $blueprint->isRetailLicense();

        $noRegretGuarantees = [
            [
                'title' => $isEn ? '100% Full IP Ownership (Zero Vendor Lock-in)' : 'Kepemilikan Penuh 100% (Zero Vendor Lock-in)',
                'desc' => $isRetail
                    ? ($isEn
                        ? 'All architecture blueprint rights, database DDL, and starter code are 100% transferred to you. Free to develop with your internal team with zero recurring proprietary lock-in.'
                        : 'Seluruh hak pakai blueprint arsitektur, DDL database, dan starter code diserahkan penuh kepada Anda. Anda bebas mengembangkannya bersama tim internal tanpa ketergantungan sewa platform tertutup.')
                    : ($isEn
                        ? 'All private GitHub repository access and database rights are 100% transferred to you. You hold the keys to your digital assets with zero vendor lock-in.'
                        : 'Seluruh hak akses repositori privat GitHub dan basis data diserahkan penuh kepada Anda. Anda memegang kunci aset digital Anda sendiri tanpa ketergantungan sewa platform tertutup.'),
                'badge' => 'FULL_IP_OWNERSHIP',
            ],
            [
                'title' => $isEn ? 'Cryptographic Scope Freeze SHA-256 (Zero Hidden Fees)' : 'Garansi Scope Freeze SHA-256 (Nol Biaya Tersembunyi)',
                'desc' => $isRetail
                    ? ($isEn
                        ? 'All feature specifications in this PRD are cryptographically hashed. The architecture specifications you purchase are final, tamper-proof, and free of stealth license fees.'
                        : 'Seluruh rincian fitur pada PRD ini dikunci secara kriptografis. Spesifikasi arsitektur yang Anda beli bersifat final, terverifikasi integritasnya, dan bebas biaya lisensi siluman.')
                    : ($isEn
                        ? 'All feature specifications in this PRD are cryptographically hashed. You will never experience surprise change-order invoices mid-sprint.'
                        : 'Seluruh rincian fitur pada PRD ini dikunci secara kriptografis. Anda tidak akan pernah mengalami tagihan biaya tambahan siluman di tengah pengerjaan sprint.'),
                'badge' => 'ZERO_HIDDEN_COSTS',
            ],
            [
                'title' => $isEn ? 'Modern Monolith O(1) Architecture Without Performance Degradation' : 'Arsitektur Modern Monolith O(1) Tanpa Lemot',
                'desc' => $isEn
                    ? 'Powered by Laravel 13, Filament v5 Enterprise, and PostgreSQL Strict ULID. The application stays sub-second fast even as records and photo galleries grow into the tens of thousands.'
                    : 'Ditenagai Laravel 13, Filament v5 Enterprise, dan PostgreSQL Strict ULID. Website tetap cepat diakses sub-detik bahkan saat data atau galeri foto bertambah ribuan.',
                'badge' => 'O(1)_SCALABILITY',
            ],
            [
                'title' => $isEn ? 'Anti-AI-Slop Design & International-Class Aesthetics' : 'Desain Anti-AI-Slop & Tampilan Berkelas Internasional',
                'desc' => $isEn
                    ? 'Crafted with Silicon Valley tier aesthetics (Clean Typography, Subtle Radius, Zero cheap popups) elevating your brand credibility before enterprise clients.'
                    : 'Antarmuka dibangun dengan estetika berstandar Silicon Valley (Clean Typography, Subtle Radius, Zero Pop-up Murahan) yang menaikkan martabat brand Anda di hadapan klien kelas atas.',
                'badge' => 'PREMIUM_AESTHETICS',
            ],
            [
                'title' => $isRetail
                    ? ($isEn ? 'One-Time Digital Architecture License (100% Debt-Free)' : 'Lisensi Digital 1x Bayar & Bebas Utang Termin (100% Lunas)')
                    : ($isEn ? '50/50 Milestones & 30-Day Bug-Free Guarantee' : 'Termin 50/50 Midtrans & Garansi 30 Hari Bug-Free'),
                'desc' => $isRetail
                    ? ($isEn
                        ? 'One-time independent code license purchase with zero recurring installment debt, zero hidden fees, and zero monthly subscriptions. You immediately download the complete PRD, Strict ULID SQL DDL, Docker scaffolding, and AI coding agent rules permanently.'
                        : 'Pembelian lisensi kode arsitektur mandiri 1x bayar tanpa cicilan termin DP 50%, tanpa biaya tersembunyi, dan tanpa langganan bulanan. Anda langsung mengunduh seluruh spesifikasi PRD, DDL SQL Strict ULID, Docker scaffold, dan rules AI coding agent secara permanen.')
                    : ($isEn
                        ? '50% initial milestone secured via authorized payment gateway. The remaining 50% is only payable after you thoroughly test and accept the working system with a comprehensive 30-day warranty.'
                        : 'DP 50% diamankan via payment gateway resmi berizin Bank Indonesia. Pelunasan 50% hanya dibayarkan setelah Anda menguji dan menerima sistem secara nyata dengan garansi bug 30 hari penuh.'),
                'badge' => $isRetail ? 'ONE_TIME_LICENSE' : '100%_RISK_PROTECTION',
            ],
        ];

        return [
            'domain_category' => $domainCategory,
            'domain_badge' => $domainBadge,
            'ticket_benchmark' => $ticketBenchmark,
            'deadly_roi_equation' => [
                'headline' => $roiHeadline,
                'formula' => $roiFormula,
                'narrative' => $roiNarrative,
                'projected_annual_roi' => $projectedAnnualRoi,
            ],
            'cost_of_inaction' => [
                'title' => $isEn ? 'Fatal Cost of Inaction: Risks & Losses of Maintaining Manual Workflows' : 'Biaya Fatal Menunda: Risiko & Kerugian Jika Tetap Membiarkan Proses Manual',
                'risks' => $inactionRisks,
            ],
            'win_win_strategy' => [
                'title' => $isEn ? 'Win-Win Phased Kickoff Strategy' : 'Strategi Investasi Menang-Menang (Win-Win Phased Kickoff)',
                'recommendation' => $phasedStrategy,
            ],
            'why_neriah_pro_guarantees' => $noRegretGuarantees,
        ];
    }


    /**
     * Calculate transparent, professional itemized scope cost breakdown
     * derived 100% from client questionnaire inputs.
     */
    public static function calculateItemizedEstimation(VisionBlueprint $blueprint, array $options = [], bool $isEn = false): array
    {
        $items = [];

        // 1. Fondasi Arsitektur Monolith (Base Core)
        $items[] = [
            'category' => $isEn ? 'Architectural Foundation' : 'Fondasi Arsitektur',
            'code' => 'BASE-CORE',
            'title' => $isEn ? 'Modern Monolith Foundation & Enterprise Security Setup' : 'Pondasi Modern Monolith & Setup Keamanan Enterprise',
            'desc' => $isEn
                ? 'Laravel 13 architecture, Filament v5 Enterprise, PostgreSQL 16+ Strict ULID database, OWASP Top 10 sanitization, Dark/Light Mode, and Docker/Nixpacks VPS container deployment.'
                : 'Arsitektur Laravel 13, Filament v5 Enterprise, Database PostgreSQL 16+ Strict ULID, Sanitasi OWASP Top 10, Dark/Light Mode, dan Deploy Container VPS Docker/Nixpacks.',
            'complexity' => $isEn ? 'Mandatory Standard' : 'Standar Wajib',
            'amount' => 5000000.00,
        ];

        // 2. Dekomposisi Fitur MVP (Poin per Fitur Wajib yang Diinput Klien)
        $rawMvp = PrdGeneratorService::parseItems($blueprint->fitur_wajib ?: ($isEn ? 'User Management & RBAC, Structured Intake Forms, Dynamic Database Recap, PDF/Excel Export.' : 'Manajemen Pengguna & RBAC, Input Formulir Data, Rekap Database Dinamis, Ekspor PDF/Excel.'));
        if (empty($rawMvp)) {
            $rawMvp = $isEn ? [
                ['title' => 'Authentication & RBAC', 'desc' => 'Secure authentication with Role-Based Access Control and ULID identifiers.'],
                ['title' => 'Structured Intake Forms', 'desc' => 'Validated data collection with Anti-Spam protection.'],
                ['title' => 'Administrative Dashboard', 'desc' => 'Filament PHP control center with instant data filter tables.'],
                ['title' => 'Reporting & Data Export', 'desc' => 'PDF and Excel export features for daily reconciliation needs.'],
            ] : [
                ['title' => 'Autentikasi & RBAC', 'desc' => 'Login aman dengan Role-Based Access Control dan ULID identifiers.'],
                ['title' => 'Formulir Intake Terstruktur', 'desc' => 'Pengumpulan data tervalidasi dengan proteksi Anti-Spam.'],
                ['title' => 'Dasbor Administrasi', 'desc' => 'Pusat kendali berbasis Filament PHP dengan tabel filter data instan.'],
                ['title' => 'Laporan & Ekspor Data', 'desc' => 'Fitur ekspor format PDF dan Excel untuk kebutuhan rekonsiliasi harian.'],
            ];
        }

        foreach ($rawMvp as $idx => $feat) {
            $t = strtolower($feat['title'] . ' ' . ($feat['desc'] ?? ''));
            
            // Check feature complexity
            $isHigh = str_contains($t, 'transaksi') || str_contains($t, 'pembayaran') || str_contains($t, 'checkout') ||
                      str_contains($t, 'booking') || str_contains($t, 'rekam medis') || str_contains($t, 'escrow') ||
                      str_contains($t, 'chat') || str_contains($t, 'real-time') || str_contains($t, 'realtime') ||
                      str_contains($t, 'payroll') || str_contains($t, 'pos') || str_contains($t, 'kasir') ||
                      str_contains($t, 'tracking') || str_contains($t, 'pelacakan') || str_contains($t, 'payment');
            
            $isLow = str_contains($t, 'kontak') || str_contains($t, 'faq') || str_contains($t, 'profil') ||
                     str_contains($t, 'banner') || str_contains($t, 'about') || str_contains($t, 'tentang') ||
                     str_contains($t, 'galeri') || str_contains($t, 'buku tamu') || str_contains($t, 'contact');

            if ($isHigh) {
                $featComplexity = $isEn ? 'Complex / Transactional' : 'Kompleks / Transaksional';
                $featAmount = 4500000.00;
            } elseif ($isLow) {
                $featComplexity = $isEn ? 'Light / Informational' : 'Ringan / Informasi Dasar';
                $featAmount = 1500000.00;
            } else {
                $featComplexity = $isEn ? 'Medium / Interactive Workflow' : 'Menengah / Alur Interaktif';
                $featAmount = 3000000.00;
            }

            $items[] = [
                'category' => $isEn ? 'Specific MVP Features' : 'Fitur MVP Spesifik',
                'code' => 'FEAT-MVP-' . ($idx + 1),
                'title' => $feat['title'],
                'desc' => $feat['desc'] ?: ($isEn ? 'System functional operational specification.' : 'Spesifikasi fungsional operasional sistem.'),
                'complexity' => $featComplexity,
                'amount' => $featAmount,
            ];
        }

        // 3. Aktor Sistem & Portal Hak Akses (Multi-Role)
        $rawActors = PrdGeneratorService::parseActors($blueprint->aktor_sistem ?: ($isEn ? 'Superadmin, Client' : 'Superadmin, Klien'), $blueprint->nama_bisnis ?? '', $blueprint->target_audiens ?? '', $isEn);
        $actorCount = count($rawActors);
        // 1-2 Role sudah tercover di Base Core. Role ke-3 dan seterusnya dihitung per portal
        if ($actorCount > 2) {
            $extraRoles = array_slice($rawActors, 2);
            foreach ($extraRoles as $rIdx => $role) {
                $roleName = is_array($role) ? ($role['name'] ?? ($isEn ? 'Additional Role' : 'Peran Tambahan')) : (string) $role;
                $items[] = [
                    'category' => $isEn ? 'Actors & Access Rights' : 'Aktor & Hak Akses',
                    'code' => 'ROLE-PORTAL-' . ($rIdx + 1),
                    'title' => ($isEn ? 'Portal & Authorization Guard: ' : 'Portal & Guard Otorisasi: ') . $roleName,
                    'desc' => $isEn
                        ? ('Dedicated navigation, isolated data scope, and specific authorization policies for the ' . $roleName . ' role.')
                        : ('Penyediaan navigasi terpisah, filter data terisolasi, dan authorization policy khusus untuk peran ' . $roleName . '.'),
                    'complexity' => $isEn ? 'Medium' : 'Menengah',
                    'amount' => 1500000.00,
                ];
            }
        }

        // 4. Kebutuhan Integrasi Pihak Ketiga
        $rawIntegrasi = strtolower($blueprint->kebutuhan_integrasi ?: '');
        if (!empty($rawIntegrasi) && $rawIntegrasi !== 'tidak ada' && $rawIntegrasi !== 'none') {
            if (str_contains($rawIntegrasi, 'payment') || str_contains($rawIntegrasi, 'midtrans') || str_contains($rawIntegrasi, 'xendit') || str_contains($rawIntegrasi, 'qris') || str_contains($rawIntegrasi, 'virtual account') || str_contains($rawIntegrasi, 'stripe')) {
                $items[] = [
                    'category' => $isEn ? 'Third-Party Integrations' : 'Integrasi Pihak Ketiga',
                    'code' => 'INT-PAYMENT',
                    'title' => $isEn ? 'Payment Gateway Integration (Midtrans Snap / Stripe / QRIS / VA)' : 'Payment Gateway Integration (Midtrans Snap / QRIS / VA)',
                    'desc' => $isEn
                        ? 'Snap Token HTTP Basic Auth, automatic Webhook callback listener, audit trail reconciliation logs, and replay attack prevention.'
                        : 'Integrasi HTTP Basic Auth Snap Token, penerima Webhook callback otomatis, log rekonsiliasi audit trail, dan perlindungan replay attack.',
                    'complexity' => $isEn ? 'Medium' : 'Menengah',
                    'amount' => 2500000.00,
                ];
            }

            if (str_contains($rawIntegrasi, 'whatsapp') || str_contains($rawIntegrasi, 'wa') || str_contains($rawIntegrasi, 'fonnte') || str_contains($rawIntegrasi, 'notifikasi')) {
                $items[] = [
                    'category' => $isEn ? 'Third-Party Integrations' : 'Integrasi Pihak Ketiga',
                    'code' => 'INT-WHATSAPP',
                    'title' => $isEn ? 'WhatsApp Gateway & Real-Time Notification Queue Worker' : 'WhatsApp Gateway & Queue Worker Notifikasi Real-time',
                    'desc' => $isEn
                        ? 'Automated transactional messaging via background Redis queues preventing client UI lag.'
                        : 'Pengiriman pesan transaksi/warta otomatis dengan background worker Redis untuk mencegah lag pada UI klien.',
                    'complexity' => $isEn ? 'Medium' : 'Menengah',
                    'amount' => 2000000.00,
                ];
            }

            if (str_contains($rawIntegrasi, 'maps') || str_contains($rawIntegrasi, 'ongkir') || str_contains($rawIntegrasi, 'kurir') || str_contains($rawIntegrasi, 'rajaongkir') || str_contains($rawIntegrasi, 'lokasi')) {
                $items[] = [
                    'category' => $isEn ? 'Third-Party Integrations' : 'Integrasi Pihak Ketiga',
                    'code' => 'INT-LOGISTICS',
                    'title' => $isEn ? 'Logistics Courier & Geocoding Maps Platform' : 'Logistik Ekspedisi & Geocoding Maps Platform',
                    'desc' => $isEn
                        ? 'Instant courier rates connection and interactive customer location pinning.'
                        : 'Koneksi tarif kurir instan (JNE/TIKI/POS) dan penanda koordinat lokasi pelanggan pada peta interaktif.',
                    'complexity' => $isEn ? 'Medium' : 'Menengah',
                    'amount' => 2500000.00,
                ];
            }

            if (str_contains($rawIntegrasi, 'ai') || str_contains($rawIntegrasi, 'gemini') || str_contains($rawIntegrasi, 'openai') || str_contains($rawIntegrasi, 'vector') || str_contains($rawIntegrasi, 'rag')) {
                $items[] = [
                    'category' => $isEn ? 'Third-Party Integrations' : 'Integrasi Pihak Ketiga',
                    'code' => 'INT-AI-COPILOT',
                    'title' => $isEn ? 'AI Copilot RAG & Semantic Database (pgvector)' : 'AI Copilot RAG & Basis Data Semantik (pgvector)',
                    'desc' => $isEn
                        ? 'Vector embeddings storage and HNSW semantic similarity search directly in PostgreSQL relational database.'
                        : 'Penyimpanan vector embeddings dan pencarian similaritas semantik HNSW langsung di basis data relasional PostgreSQL.',
                    'complexity' => $isEn ? 'High' : 'Tinggi',
                    'amount' => 5000000.00,
                ];
            }
        }

        // 5. Skala Pengguna & Arsitektur Concurrency
        $metadata = $blueprint->user_metadata ?? [];
        $rawScale = strtolower($metadata['skala_pengguna'] ?? '');
        if (str_contains($rawScale, '100.000') || str_contains($rawScale, '1.000.000') || str_contains($rawScale, 'jutaan') || str_contains($rawScale, 'high concurrency')) {
            $items[] = [
                'category' => $isEn ? 'Infrastructure & Scale' : 'Infrastruktur & Skala',
                'code' => 'INFRA-CONCURRENCY',
                'title' => $isEn ? 'High-Concurrency Redis Caching & Keyset Keystone O(1)' : 'High-Concurrency Redis Caching & Keyset Keystone O(1)',
                'desc' => $isEn
                    ? 'Sub-10ms query optimization with keyset cursor pagination O(1) and aggressive caching to serve hundreds of thousands of users.'
                    : 'Optimasi query database anti-lemot dengan cursor keyset pagination O(1) dan caching agresif untuk melayani ratusan ribu pengguna.',
                'complexity' => $isEn ? 'Medium' : 'Menengah',
                'amount' => 3500000.00,
            ];
        }

        // Target Platform: Flutter / Mobile
        $rawPlatform = strtolower($metadata['target_platform'] ?? '');
        $isMobileClient = str_contains($rawPlatform, 'flutter') || str_contains($rawPlatform, 'mobile') || str_contains($rawPlatform, 'ios') || str_contains($rawPlatform, 'android');
        if ($isMobileClient) {
            $items[] = [
                'category' => $isEn ? 'Multi-Platform Client' : 'Multi-Platform Client',
                'code' => 'PLATFORM-MOBILE',
                'title' => $isEn ? 'Cross-Platform Native Client (Flutter / Mobile iOS & Android)' : 'Cross-Platform Native Client (Flutter / Mobile iOS & Android)',
                'desc' => $isEn
                    ? 'Cross-platform native mobile interface with integrated SQLite offline-first sync engine.'
                    : 'Pengembangan antarmuka mobile native dengan SQLite offline-first sync engine terpadu.',
                'complexity' => $isEn ? 'High' : 'Tinggi',
                'amount' => 8500000.00,
            ];
        }

        // Hitung Base Subtotal (Standard Velocity: 30 Hari Kerja)
        $baseSubtotal = 0;
        foreach ($items as $it) {
            $baseSubtotal += (float) $it['amount'];
        }

        // Toleransi Budget Bracket & Gereja / Komunitas (Community Discount / Reality Guard)
        $rawBudget = strtolower($metadata['kisaran_budget'] ?? '');
        $combinedText = strtolower(($blueprint->nama_bisnis ?? '') . ' ' . ($blueprint->masalah_utama ?? '') . ' ' . $rawBudget);
        $isChurch = str_contains($combinedText, 'gereja') || str_contains($combinedText, 'jemaat') || str_contains($combinedText, 'ibadah');

        // Jika gereja/komunitas non-profit, berikan subsidi efisiensi modular agar realistis
        if ($isChurch && $baseSubtotal > 15000000) {
            $discount = round(($baseSubtotal - 10000000) * 0.4, -5);
            $items[] = [
                'category' => $isEn ? 'Subsidies & Adjustments' : 'Subsidi & Penyesuaian',
                'code' => 'GRANT-COMMUNITY',
                'title' => $isEn ? 'Community Efficiency Subsidy / Non-Profit Ministry' : 'Subsidi Efisiensi Komunitas / Pelayanan Non-Profit',
                'desc' => $isEn
                    ? 'Neriah Pro modular architecture cost adjustment for religious institutions and social non-profits.'
                    : 'Penyesuaian biaya efisiensi arsitektur modular Neriah Pro untuk institusi keagamaan dan komunitas sosial.',
                'complexity' => $isEn ? 'Fee Reduction' : 'Pengurang Biaya',
                'amount' => -$discount,
            ];
            $baseSubtotal -= $discount;
        }

        // Bulatkan ke ratusan ribu bersih
        $standardContract = max(5000000.00, round($baseSubtotal, -5));
        $standardDp = $standardContract * 0.50;
        $standardPelunasan = $standardContract - $standardDp;

        // Hitung 3 Velocity Tiers:
        $fastTrackContract = round($standardContract * 1.35, -5);

        // Harmonize with existing signed contract document if present
        $existingContract = $blueprint->getContractDocument();
        if ($existingContract && $existingContract->status === 'signed' && (float)$existingContract->contract_amount > 0) {
            $cAmount = (float)$existingContract->contract_amount;
            if ($cAmount >= $standardContract) {
                $fastTrackContract = $cAmount;
            } else {
                $standardContract = $cAmount;
                $standardDp = (float)($existingContract->dp_amount ?: ($standardContract * 0.50));
                $standardPelunasan = $standardContract - $standardDp;
            }
        }

        $fastTrackDp = $fastTrackContract * 0.50;
        $fastTrackPelunasan = $fastTrackContract - $fastTrackDp;

        $hyperSprintContract = round(max($fastTrackContract * 1.35, $standardContract * 1.8), -5);
        $hyperSprintDp = $hyperSprintContract * 0.50;
        $hyperSprintPelunasan = $hyperSprintContract - $hyperSprintDp;

        $velocityTiers = [
            [
                'id' => 'standard',
                'name' => $isEn ? 'Standard Velocity (30 Business Days)' : 'Standard Velocity (30 Hari Kerja)',
                'duration' => $isEn ? '30 Business Days' : '30 Hari Kerja',
                'badge' => 'STANDARD_PACE // BEST_VALUE',
                'speed_multiplier' => $isEn ? '1.0x (Planned Pace)' : '1.0x (Pace Terencana)',
                'contract_amount' => $standardContract,
                'dp_amount' => $standardDp,
                'pelunasan_amount' => $standardPelunasan,
                'ai_quota_spec' => 'Modern Monolith Blueprint Engine',
                'squad_allocation' => $isEn ? '1 Dedicated Fullstack Engineer + QA Reviewer' : '1 Dedicated Fullstack Engineer + QA Reviewer',
                'cost_formula' => $isEn
                    ? ('Real Itemized Scope Value (' . count($items) . ' Analyzed Components)')
                    : ('Nilai Riil Itemized Scope (' . count($items) . ' Komponen Teranalisis)'),
                'ai_swarm_specs' => $isEn ? [
                    'Architecture: Laravel 13, Filament v5, PostgreSQL ULID, Docker VPS',
                    'Feature List: Exact match of ' . count($rawMvp) . ' requested MVP features',
                    'Access Control: Multi-role isolated with strict authorization',
                    'Warranty: 30-Day Bug-free Support + Full Private GitHub Repo Access',
                ] : [
                    'Arsitektur: Laravel 13, Filament v5, PostgreSQL ULID, Docker VPS',
                    'Daftar Fitur: Terhitung persis dari ' . count($rawMvp) . ' fitur MVP yang diajukan',
                    'Hak Akses: Multi-role terisolasi dengan otorisasi ketat',
                    'Garansi: 30 Hari Bug-free Support + Akses Penuh Private Repo GitHub',
                ],
                'description' => $isEn
                    ? 'Planned standard velocity with the most transparent and cost-efficient investment.'
                    : 'Kecepatan pengerjaan standar terencana dengan biaya investasi paling efisien dan transparan.',
            ],
            [
                'id' => 'fast_track',
                'name' => $isEn ? 'Accelerated Dedicated Squad (14 Business Days)' : 'Accelerated Dedicated Squad (14 Hari Kerja)',
                'duration' => $isEn ? '14 Business Days' : '14 Hari Kerja',
                'badge' => 'RECOMMENDED // DEDICATED_SQUAD',
                'speed_multiplier' => $isEn ? '1.5x (2-Week Acceleration)' : '1.5x (Akselerasi 2 Pekan)',
                'contract_amount' => $fastTrackContract,
                'dp_amount' => $fastTrackDp,
                'pelunasan_amount' => $fastTrackPelunasan,
                'ai_quota_spec' => $isEn ? 'Dual-Track Concurrent Architecture (Simultaneous Frontend & Backend Sprints)' : 'Dual-Track Concurrent Architecture (Paralelisasi Frontend Island & Backend DB Sprints)',
                'squad_allocation' => $isEn ? 'Lead Architect + 2 Dedicated Senior Fullstack Engineers' : 'Lead Architect + 2 Dedicated Senior Fullstack Engineers',
                'cost_formula' => $isEn
                    ? ('Base Scope (Rp ' . number_format($standardContract, 0, ',', '.') . ') + Dedicated Senior Squad & Dual-Track Acceleration (Rp ' . number_format(max(0, $fastTrackContract - $standardContract), 0, ',', '.') . ')')
                    : ('Base Scope (Rp ' . number_format($standardContract, 0, ',', '.') . ') + Dedicated Senior Squad & Akselerasi Paralel (Rp ' . number_format(max(0, $fastTrackContract - $standardContract), 0, ',', '.') . ')'),
                'ai_swarm_specs' => $isEn ? [
                    'All feature specifications included in Standard tier',
                    'Parallelized Frontend Island & Backend DB Migration simultaneously (Dual-Track Sprints)',
                    'Dedicated Senior Engineers exclusively assigned with zero client-queue delay',
                    'Priority Code Review and automated Staging deployment at each sprint end',
                ] : [
                    'Seluruh cakupan rincian fitur paket Standard',
                    'Paralelisasi Frontend Island & Backend DB Migration serentak (Dual-Track Sprints)',
                    'Alokasi eksklusif Senior Engineers tanpa distraksi antrean proyek lain',
                    'Prioritas Review dan Deploy Staging otomatis setiap akhir sprint',
                ],
                'description' => $isEn
                    ? 'Accelerated 14 business days execution powered by dedicated senior engineers in dual-track concurrent sprints.'
                    : 'Akselerasi pengerjaan 14 hari kerja dengan alokasi skuad senior engineer berdedikasi dan pengerjaan sprint paralel serentak.',
            ],
            [
                'id' => 'hyper_sprint',
                'name' => $isEn ? 'Hyper-Sprint Emergency (24/7 War Room)' : 'Hyper-Sprint Emergency (24/7 War Room)',
                'duration' => $isEn ? '7 Calendar Days' : '7 Hari Kalender',
                'badge' => 'TOP_SPEED // 24_7_WAR_ROOM',
                'speed_multiplier' => $isEn ? '3.0x (1 Calendar Week Release)' : '3.0x (Rilis 1 Pekan Kalender)',
                'contract_amount' => $hyperSprintContract,
                'dp_amount' => $hyperSprintDp,
                'pelunasan_amount' => $hyperSprintPelunasan,
                'ai_quota_spec' => $isEn ? 'Continuous Deployment War Room (24/7 Shift Rotation & Real-Time Test Loop)' : 'Continuous Deployment War Room (Rotasi Shift 24/7 & Real-Time Test Loop)',
                'squad_allocation' => $isEn ? 'Dedicated Tri-Engineer War Room (24/7 Shift Rotation & DevOps SLA)' : 'Dedicated Tri-Engineer War Room (Rotasi Shift 24/7 & DevOps SLA)',
                'cost_formula' => $isEn
                    ? ('Base Scope (Rp ' . number_format($standardContract, 0, ',', '.') . ') + Intensive 24/7 War Room Shift Squad (Rp ' . number_format(max(0, $hyperSprintContract - $standardContract), 0, ',', '.') . ')')
                    : ('Base Scope (Rp ' . number_format($standardContract, 0, ',', '.') . ') + Intensive 24/7 War Room Shift Squad (Rp ' . number_format(max(0, $hyperSprintContract - $standardContract), 0, ',', '.') . ')'),
                'ai_swarm_specs' => $isEn ? [
                    'Non-stop 24-hour engineer rotation with continuous staging deployment',
                    '99.9% Uptime SLA & Emergency Response with dedicated on-call DevOps',
                    'Automated E2E Regression Test Suite guaranteeing production stability',
                ] : [
                    'Rotasi engineer 24 jam non-stop dengan deployment kontinyu ke Staging',
                    'SLA Uptime & Respons Darurat 99.9% dengan dedicated DevOps on-call',
                    'Automated E2E Regression Test Suite untuk menjamin stabilitas rilis',
                ],
                'description' => $isEn
                    ? 'Emergency 1 calendar week sprint for business needs with absolute critical urgency.'
                    : 'Pengerjaan prioritas darurat 1 pekan kalender untuk kebutuhan bisnis dengan urgensi kritis absolut.',
            ],
        ];

        return [
            'items' => $items,
            'items_count' => count($items),
            'mvp_features_count' => count($rawMvp),
            'roles_count' => $actorCount,
            'base_subtotal' => $standardContract,
            'standard_dp' => $standardDp,
            'standard_pelunasan' => $standardPelunasan,
            'velocity_tiers' => $velocityTiers,
        ];
    }


    /**
     * Generate tiered velocity pricing options with dynamic agnostic continuum from UMKM to Enterprise.
     */
    public static function generateVelocityPricingOptions(string $targetWaktu, ?string $budgetRange = null, string $businessName = '', string $masalah = ''): array
    {
        $rawBudget = strtolower($budgetRange ?? '');
        $combinedText = strtolower($businessName . ' ' . $masalah . ' ' . $rawBudget);

        $isChurch = str_contains($combinedText, 'gereja') || str_contains($combinedText, 'jemaat') || str_contains($combinedText, 'ibadah');

        // Detect budget bracket with unambiguous priority
        $isTierEnterprise = str_contains($rawBudget, '> 75') ||
                            str_contains($rawBudget, '> rp 75') ||
                            str_contains($rawBudget, '100.000.000') ||
                            str_contains($rawBudget, 'enterprise');

        $isTierScale = !$isTierEnterprise && (
            str_contains($rawBudget, '35.000.000 - rp 75.000.000') ||
            str_contains($rawBudget, '40.000.000') ||
            str_contains($rawBudget, '50.000.000') ||
            str_contains($rawBudget, '60.000.000') ||
            str_contains($rawBudget, '75.000.000') ||
            str_contains($rawBudget, 'scale')
        );

        $isTierGrowth = !$isTierEnterprise && !$isTierScale && (
            str_contains($rawBudget, '15.000.000 - rp 35.000.000') ||
            str_contains($rawBudget, '20.000.000') ||
            str_contains($rawBudget, '25.000.000') ||
            str_contains($rawBudget, '30.000.000') ||
            str_contains($rawBudget, 'growth')
        );

        $isTierStarter = !$isTierEnterprise && !$isTierScale && !$isTierGrowth && (
            str_contains($rawBudget, '5.000.000 - rp 15.000.000') ||
            str_contains($rawBudget, '5.000.000') ||
            str_contains($rawBudget, '10.000.000') ||
            str_contains($rawBudget, '12.000.000') ||
            str_contains($rawBudget, 'starter') ||
            str_contains($rawBudget, 'umkm') ||
            str_contains($rawBudget, 'komunitas') ||
            $isChurch
        );

        // 1. TIER STARTER / LEAN (Rp 5M - Rp 15M)
        if ($isTierStarter) {
            $starterModules = $isChurch 
                ? 'Profil Gereja/Organisasi, Jadwal Ibadah & Kegiatan, Form Warta/Doa, dan Donasi Persembahan QRIS'
                : 'Profil Bisnis/Katalog Produk, Form Pemesanan/Reservasi Langsung, WhatsApp Direct CTA, dan Pembayaran QRIS';
            
            $plusModules = $isChurch
                ? 'Integrasi broadcast WhatsApp warta jemaat, sistem presensi relawan QR Code, dan arsip dokumen/khotbah'
                : 'Notifikasi WhatsApp transaksi otomatis ke klien & admin, konfirmasi pembayaran otomatis, dan rekaman order';

            return [
                [
                    'id' => 'starter_lean',
                    'name' => 'Starter Lean MVP (Pondasi Cepat Rilis)',
                    'duration' => '5 - 7 Hari Kerja',
                    'badge' => 'LEAN_STARTER // REALISTIC_MVP',
                    'speed_multiplier' => '1.0x (Pondasi Siap Pakai)',
                    'contract_amount' => 5000000.00,
                    'dp_amount' => 2500000.00,
                    'pelunasan_amount' => 2500000.00,
                    'ai_quota_spec' => 'Pre-Built Modular Monolith Blueprint Engine',
                    'squad_allocation' => '1 Dedicated Fullstack Specialist + Template Deployer',
                    'cost_formula' => 'Base Modular Setup (Rp 5.000.000) - Efisiensi Arsitektur Neriah OS',
                    'ai_swarm_specs' => [
                        'Arsitektur: Pre-built Lean Monolith Web Portal (Laravel 13 & Filament v5)',
                        'Modul Fungsional: ' . $starterModules,
                        'Infrastruktur: Setup Cloud Starter hemat biaya (< Rp 100rb/bln) atau Micro VPS',
                        'Handoff: Panduan video operasional mandiri & serah terima kredensial resmi',
                    ],
                    'description' => 'Solusi ideal dan ramah anggaran bagi UMKM, profesional perorangan, atau komunitas yang ingin memiliki website modern fungsional dalam waktu singkat tanpa beban biaya bulanan server yang tinggi.',
                ],
                [
                    'id' => 'starter_plus',
                    'name' => 'Growth Extended (WhatsApp Gateway & Pembayaran)',
                    'duration' => '10 Hari Kerja',
                    'badge' => 'RECOMMENDED // BEST_VALUE',
                    'speed_multiplier' => '1.5x (Otomatisasi Penuh)',
                    'contract_amount' => 10000000.00,
                    'dp_amount' => 5000000.00,
                    'pelunasan_amount' => 5000000.00,
                    'ai_quota_spec' => 'WhatsApp Gateway API & Transactional Queue Integration',
                    'squad_allocation' => '1 Fullstack Engineer + Integration Specialist',
                    'cost_formula' => 'Base Starter (Rp 5M) + Integrasi Gateway Pembayaran & Notifikasi WA (Rp 5M)',
                    'ai_swarm_specs' => [
                        'Seluruh fitur paket Starter Lean MVP',
                        'Modul Lanjutan: ' . $plusModules,
                        'Payment Gateway: Midtrans Snap (QRIS, Virtual Account BCA/Mandiri/BRI)',
                        'Export Data: Rekapitulasi laporan dalam format Microsoft Excel & PDF terformat',
                    ],
                    'description' => 'Paket terpopuler untuk bisnis atau organisasi yang ingin mengotomatiskan alur konfirmasi dan penerimaan transaksi digital secara profesional.',
                ],
                [
                    'id' => 'starter_pro',
                    'name' => 'Professional Custom Hub (Multi-Role & Dedicated DB)',
                    'duration' => '14 Hari Kerja',
                    'badge' => 'ADVANCED // MULTI_ROLE',
                    'speed_multiplier' => '2.0x (Skala Kustom)',
                    'contract_amount' => 15000000.00,
                    'dp_amount' => 7500000.00,
                    'pelunasan_amount' => 7500000.00,
                    'ai_quota_spec' => 'Multi-Role RBAC & Isolated PostgreSQL Database',
                    'squad_allocation' => 'Lead Architect + Fullstack Engineer',
                    'cost_formula' => 'Base Starter (Rp 5M) + Multi-Role Hak Akses & Dedicated Database (Rp 10M)',
                    'ai_swarm_specs' => [
                        'Seluruh fitur paket Growth Extended',
                        'Multi-Role RBAC: Hak akses terpisah untuk Superadmin, Staff Operasional, dan Klien/Publik',
                        'Isolasi Basis Data: PostgreSQL relasional strict ULID terdedikasi',
                        'Cloudflare CDN & Automated Daily Database Backup',
                    ],
                    'description' => 'Tingkat komprehensif bagi entitas bisnis atau organisasi yang memerlukan pemisahan hak akses staf serta keamanan basis data terisolasi.',
                ],
            ];
        }

        // 2. TIER GROWTH (Rp 15M - Rp 35M)
        if ($isTierGrowth) {
            return [
                [
                    'id' => 'growth_core',
                    'name' => 'Business Core Solution (Custom Workflow)',
                    'duration' => '10 Hari Kerja',
                    'badge' => 'BUSINESS_CORE',
                    'speed_multiplier' => '1.0x (Pace Terencana)',
                    'contract_amount' => 17500000.00,
                    'dp_amount' => 8750000.00,
                    'pelunasan_amount' => 8750000.00,
                    'ai_quota_spec' => 'Custom Filament v5 Operations Suite & Automated Invoicing',
                    'squad_allocation' => '1 Lead Fullstack Engineer + QA Reviewer',
                    'cost_formula' => 'Base Architecture (Rp 12.5M) + Custom Workflow & Invoicing (Rp 5M)',
                    'ai_swarm_specs' => [
                        'Arsitektur: Modern Monolith (Laravel 13 & Filament v5 Enterprise)',
                        'Manajemen Workflow Bisnis Kustom dengan Status Pipeline & Approval',
                        'Invoice Otomatis Terbit dengan QR Code Verifikasi Dokumen',
                        'Basis Data: PostgreSQL 16+ dengan Primary Key ULID terisolasi',
                    ],
                    'description' => 'Dirancang untuk digitalisasi operasional bisnis spesifik (seperti klinik, logistik cabang tunggal, rental, atau agensi) yang ingin membuang pencatatan kertas dan spreadsheet manual.',
                ],
                [
                    'id' => 'growth_pro',
                    'name' => 'Business Operations Pro (Automated Gateway & SLA)',
                    'duration' => '14 Hari Kerja',
                    'badge' => 'RECOMMENDED // BUSINESS_PRO',
                    'speed_multiplier' => '1.5x (Akselerasi Terpadu)',
                    'contract_amount' => 25000000.00,
                    'dp_amount' => 12500000.00,
                    'pelunasan_amount' => 12500000.00,
                    'ai_quota_spec' => 'Midtrans Payment Gateway, WhatsApp Gateway & Background Redis Workers',
                    'squad_allocation' => '2 Fullstack Specialists (Frontend Island + Backend Architect)',
                    'cost_formula' => 'Base Core (Rp 17.5M) + Integrasi Gateway Lengkap & Dedicated VPS Setup (Rp 7.5M)',
                    'ai_swarm_specs' => [
                        'Seluruh kapabilitas Business Core Solution',
                        'Dedicated VPS Nixpacks & Docker setup dengan Nginx HTTP/2 reverse proxy',
                        'Integrasi Payment Gateway Midtrans (Kartu Kredit, QRIS, Virtual Account)',
                        'Background Worker Redis 24/7 untuk notifikasi & rekapitulasi real-time',
                    ],
                    'description' => 'Paket rekomendasi utama untuk perusahaan yang membutuhkan keandalan transaksi tinggi, dashboard operasional lengkap, dan deployment VPS terisolasi.',
                ],
                [
                    'id' => 'growth_sprint',
                    'name' => 'High-Velocity Accelerated Sprint',
                    'duration' => '7 - 10 Hari Kerja',
                    'badge' => 'FAST_TRACK // SPRINT',
                    'speed_multiplier' => '2.0x (Pangkas 40% Waktu Rilis)',
                    'contract_amount' => 35000000.00,
                    'dp_amount' => 17500000.00,
                    'pelunasan_amount' => 17500000.00,
                    'ai_quota_spec' => 'Gemini Pro Swarm Assistance + Parallel Engineering Shifts',
                    'squad_allocation' => 'Lead Architect + 2 Senior Fullstack Engineers',
                    'cost_formula' => 'Base Pro (Rp 25M) + AI Pair Programming Acceleration & Priority Concurrency (Rp 10M)',
                    'ai_swarm_specs' => [
                        'Seluruh kapabilitas Business Operations Pro',
                        'Prioritas Eksekusi Paralel (Frontend & Backend dikerjakan serentak)',
                        'Automated Unit & Feature Test Suite untuk pencegahan regresi',
                        'SLA Responsif: Dukungan prioritas 60 hari kalender pasca peluncuran',
                    ],
                    'description' => 'Peluncuran kilat untuk bisnis dengan deadline mendesak, memastikan sistem siap beroperasi penuh dalam 7-10 hari kerja tanpa kompromi kualitas kode.',
                ],
            ];
        }

        // 3. TIER SCALE-UP (Rp 35M - Rp 75M)
        if ($isTierScale) {
            return [
                [
                    'id' => 'scale_standard',
                    'name' => 'Standard High-Concurrency Monolith',
                    'duration' => '21 Hari Kerja',
                    'badge' => 'SCALE_STANDARD',
                    'speed_multiplier' => '1.0x (Normal Pace)',
                    'contract_amount' => 45000000.00,
                    'dp_amount' => 22500000.00,
                    'pelunasan_amount' => 22500000.00,
                    'ai_quota_spec' => 'PostgreSQL 16+ pgvector Ready + Redis Cluster Architecture',
                    'squad_allocation' => 'Lead System Architect + Senior Engineer + QA Specialist',
                    'cost_formula' => 'Base Enterprise Engineering (Rp 45.000.000)',
                    'ai_swarm_specs' => [
                        'Arsitektur: Modern Monolith dengan Keyset Cursor Pagination O(1)',
                        'Kapasitas Beban: Siap melayani 100.000 - 1.000.000 transaksi / bulan',
                        'Dedicated VPS Docker Environment dengan Zero-Downtime Deployment',
                    ],
                    'description' => 'Fondasi enterprise untuk platform komersial yang membutuhkan integritas transaksi finansial ketat dan kesiapan skala jutaan baris data.',
                ],
                [
                    'id' => 'scale_fast',
                    'name' => 'Accelerated Dedicated Squad (14 Hari Kerja)',
                    'duration' => '14 Hari Kerja',
                    'badge' => '2X_SPEED // RECOMMENDED',
                    'speed_multiplier' => '1.5x (Rilis 2 Pekan)',
                    'contract_amount' => 60000000.00,
                    'dp_amount' => 30000000.00,
                    'pelunasan_amount' => 30000000.00,
                    'ai_quota_spec' => 'Dual-Track Concurrent Architecture (Paralelisasi Frontend & Backend Sprints)',
                    'squad_allocation' => 'Lead Architect + 2 Dedicated Senior Fullstack Engineers',
                    'cost_formula' => 'Base Fee (Rp 45M) + Dedicated Senior Squad & Akselerasi Paralel (Rp 15M)',
                    'ai_swarm_specs' => [
                        'Paralelisasi Frontend Island & Backend DB Migration serentak (Dual-Track Sprints)',
                        'Alokasi eksklusif Senior Engineers tanpa antrean proyek lain',
                        'Prioritas Review dan Deploy Staging otomatis setiap akhir sprint',
                    ],
                    'description' => 'Akselerasi peluncuran 2 pekan dengan alokasi skuad senior engineer berdedikasi untuk mempercepat integrasi kompleks dan verifikasi arsitektur.',
                ],
                [
                    'id' => 'scale_priority',
                    'name' => 'Priority Enterprise Delivery (War Room)',
                    'duration' => '10 Hari Kerja',
                    'badge' => 'PRIORITY // HIGH_SPEED',
                    'speed_multiplier' => '2.1x (Rilis 10 Hari)',
                    'contract_amount' => 75000000.00,
                    'dp_amount' => 37500000.00,
                    'pelunasan_amount' => 37500000.00,
                    'ai_quota_spec' => 'Continuous Deployment War Room (Rotasi Shift & Continuous Integration)',
                    'squad_allocation' => '3 Senior Engineers (Dedicated War Room)',
                    'cost_formula' => 'Base Fee (Rp 45M) + Dedicated Concurrent Squad (Rp 15M) + Dedicated War Room Squad (Rp 15M)',
                    'ai_swarm_specs' => [
                        'War-Room Engineering intensif dengan pemantauan deployment real-time',
                        'Dedicated on-call DevOps & zero-downtime hotfix loop',
                    ],
                    'description' => 'Pengerjaan prioritas tinggi dengan alokasi skuad penuh untuk mengejar momentum peluncuran bisnis strategis.',
                ],
            ];
        }

        // 4. TIER ENTERPRISE (> Rp 75M+)
        if ($isTierEnterprise) {
            return [
                [
                    'id' => 'enterprise_standard',
                    'name' => 'Enterprise Scaled Platform (Regular)',
                    'duration' => '21 Hari Kerja',
                    'badge' => 'ENTERPRISE_REGULAR',
                    'speed_multiplier' => '1.0x (Pace Enterprise)',
                    'contract_amount' => 75000000.00,
                    'dp_amount' => 37500000.00,
                    'pelunasan_amount' => 37500000.00,
                    'ai_quota_spec' => 'Full AI Database Vector Engine (pgvector) + Multi-Tenant RBAC',
                    'squad_allocation' => 'Principal Architect + 2 Senior Engineers + Security Auditor',
                    'cost_formula' => 'Base Enterprise Platform Fee (Rp 75.000.000)',
                    'ai_swarm_specs' => [
                        'pgvector Semantic Search & AI RAG Integration',
                        'High-Availability PostgreSQL Cluster & Read Replicas Ready',
                        'Kepatuhan Standar UU PDP & OWASP Top 10 Enterprise Audit Trail',
                    ],
                    'description' => 'Platform enterprise berskala penuh dengan kapabilitas pencarian AI semantik, keamanan data audit-trail mendalam, dan arsitektur berdaya tahan tinggi.',
                ],
                [
                    'id' => 'enterprise_fast',
                    'name' => 'Accelerated Dedicated Squad (14 Hari Kerja)',
                    'duration' => '14 Hari Kerja',
                    'badge' => 'RECOMMENDED // DEDICATED_SQUAD',
                    'speed_multiplier' => '1.5x (Akselerasi 2 Pekan)',
                    'contract_amount' => 100000000.00,
                    'dp_amount' => 50000000.00,
                    'pelunasan_amount' => 50000000.00,
                    'ai_quota_spec' => 'Dual-Track Concurrent Architecture (Paralelisasi Frontend Island & Backend DB Sprints)',
                    'squad_allocation' => 'Lead Architect + 3 Dedicated Senior Engineers',
                    'cost_formula' => 'Base Enterprise (Rp 75M) + Dedicated Senior Squad & Akselerasi Paralel (Rp 25M)',
                    'ai_swarm_specs' => [
                        'Paralelisasi Frontend Island & Backend DB Migration serentak (Dual-Track Sprints)',
                        'Alokasi eksklusif Senior Engineers tanpa antrean proyek lain',
                        'Automated E2E Regression Test Suite untuk menjamin stabilitas rilis',
                    ],
                    'description' => 'Kombinasi tenaga ahli senior dan alokasi skuad berdedikasi untuk meluncurkan sistem enterprise berstandar industri dalam 14 hari kerja.',
                ],
                [
                    'id' => 'enterprise_hyper',
                    'name' => 'Hyper-Sprint Emergency (24/7 War Room)',
                    'duration' => '7 Hari Kalender',
                    'badge' => 'EMERGENCY // 24_7_WAR_ROOM',
                    'speed_multiplier' => '3.0x (Rilis 1 Pekan Kalender)',
                    'contract_amount' => 125000000.00,
                    'dp_amount' => 62500000.00,
                    'pelunasan_amount' => 62500000.00,
                    'ai_quota_spec' => 'Continuous Deployment War Room (Rotasi Shift 24/7 & Emergency DevOps SLA)',
                    'squad_allocation' => 'Dedicated Tri-Engineer War Room (24/7 Shift Rotation)',
                    'cost_formula' => 'Base Enterprise (Rp 75M) + Intensive War Room Squad (Rp 30M) + Shift Rotation 24/7 (Rp 20M)',
                    'ai_swarm_specs' => [
                        'Rotasi engineer 24 jam non-stop dengan deployment synchronization kontinyu',
                        'SLA Uptime & Respons Darurat 99.9% dengan dedicated DevOps on-call',
                    ],
                    'description' => 'Peluncuran darurat dalam 1 pekan kalender untuk kebutuhan bisnis dengan urgensi kritis absolut.',
                ],
            ];
        }

        // DEFAULT BALANCED CONTINUUM (If budget unspecified)
        return [
            [
                'id' => 'starter_lean',
                'name' => 'Starter Lean MVP (Pondasi Cepat Rilis)',
                'duration' => '7 Hari Kerja',
                'badge' => 'LEAN_STARTER // REALISTIC_MVP',
                'speed_multiplier' => '1.0x (Pace Standar)',
                'contract_amount' => 7500000.00,
                'dp_amount' => 3750000.00,
                'pelunasan_amount' => 3750000.00,
                'ai_quota_spec' => 'Pre-Built Modular Monolith Blueprint Engine',
                'squad_allocation' => '1 Dedicated Fullstack Specialist',
                'cost_formula' => 'Base Modular Setup (Rp 7.500.000)',
                'ai_swarm_specs' => [
                    'Arsitektur: Pre-built Lean Monolith Web Portal (Laravel 13 & Filament v5)',
                    'Modul: Profil Bisnis/Katalog, Form Pemesanan, Notifikasi WhatsApp, & QRIS',
                    'Infrastruktur: Setup Cloud Starter hemat biaya (< Rp 100rb/bln)',
                    'Handoff: Panduan video operasional mandiri & serah terima kredensial',
                ],
                'description' => 'Titik mulai yang sangat ramah dan realistis bagi UMKM atau komunitas untuk memiliki sistem digital profesional tanpa hambatan modal besar.',
            ],
            [
                'id' => 'growth_pro',
                'name' => 'Business Operations Pro (Custom Portal & VPS)',
                'duration' => '14 Hari Kerja',
                'badge' => 'RECOMMENDED // BEST_BALANCE',
                'speed_multiplier' => '1.5x (Akselerasi Terencana)',
                'contract_amount' => 25000000.00,
                'dp_amount' => 12500000.00,
                'pelunasan_amount' => 12500000.00,
                'ai_quota_spec' => 'Midtrans Payment Gateway, Multi-Role RBAC & Redis Worker',
                'squad_allocation' => 'Lead Architect + Senior Fullstack Specialist',
                'cost_formula' => 'Base Professional (Rp 17.5M) + Dedicated VPS & Payment Gateway (Rp 7.5M)',
                'ai_swarm_specs' => [
                    'Arsitektur Modern Monolith dengan isolasi basis data PostgreSQL ULID',
                    'Dashboard Filament v5 dengan filter instan dan ekspor laporan otomatis',
                    'Integrasi Payment Gateway Midtrans (QRIS, Kartu Kredit, Virtual Account)',
                    'Dedicated VPS Container via Nixpacks & Docker',
                ],
                'description' => 'Keseimbangan terbaik antara kelengkapan fitur bisnis kustom, performa server terisolasi, dan nilai investasi yang proporsional.',
            ],
            [
                'id' => 'scale_fast',
                'name' => 'Enterprise High-Speed Sprint (Dedicated Squad)',
                'duration' => '21 Hari Kerja',
                'badge' => 'SCALE_UP // HIGH_SPEED',
                'speed_multiplier' => '2.0x (Skala Komersial)',
                'contract_amount' => 50000000.00,
                'dp_amount' => 25000000.00,
                'pelunasan_amount' => 25000000.00,
                'ai_quota_spec' => 'PostgreSQL 16+ pgvector AI Ready + Concurrent Squad Acceleration',
                'squad_allocation' => 'Principal Architect + 2 Senior Engineers',
                'cost_formula' => 'Base Enterprise (Rp 35M) + Dedicated Concurrent Squad (Rp 15M)',
                'ai_swarm_specs' => [
                    'Arsitektur High-Concurrency berskala jutaan data dengan keyset pagination O(1)',
                    'Integrasi pgvector AI database untuk pencarian semantik cerdas',
                    'SLA Prioritas & Garansi Pemeliharaan Penuh 60 Hari',
                ],
                'description' => 'Solusi berdaya tahan tinggi bagi entitas bisnis yang siap bersaing di pasar komersial dengan volume transaksi masif.',
            ],
        ];
    }


    /**
     * Get primary velocity pricing tiers for display (e.g. 3 comparative options in PRD view).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getPrimaryVelocityTiers(VisionBlueprint $blueprint): array
    {
        $isEn = (request()->query('locale') === 'en') 
            || (request()->query('lang') === 'en')
            || (request()->cookie('neriah_locale') === 'en')
            || (session('locale') === 'en')
            || (($blueprint->user_metadata['output_locale'] ?? '') === 'en')
            || (app()->getLocale() === 'en');

        $itemized = $blueprint->prd_content['itemized_cost_breakdown'] ?? null;
        $baseSubtotal = (float)($itemized['base_subtotal'] ?? 0);

        // 1. If itemized breakdown has velocity tiers, use it with guaranteed normalization & sanitization
        if (!empty($itemized['velocity_tiers']) && is_array($itemized['velocity_tiers'])) {
            $tiers = array_values($itemized['velocity_tiers']);
            $existingContract = $blueprint->getContractDocument();
            if ($existingContract && $existingContract->status === 'signed' && (float)$existingContract->contract_amount > 0) {
                $cAmount = (float)$existingContract->contract_amount;
                foreach ($tiers as &$t) {
                    if (($t['id'] ?? '') === 'fast_track' || (str_contains(strtolower($t['name'] ?? ''), 'swarm') || str_contains(strtolower($t['name'] ?? ''), 'fast'))) {
                        $t['contract_amount'] = $cAmount;
                        $t['dp_amount'] = (float)($existingContract->dp_amount ?: ($cAmount * 0.50));
                        $t['pelunasan_amount'] = $t['contract_amount'] - $t['dp_amount'];
                    }
                }
            }
            return self::normalizeVelocityTiers($tiers, $baseSubtotal, $isEn);
        }

        // 2. Fresh calculate itemized estimation
        $fresh = self::calculateItemizedEstimation($blueprint, [], $isEn);
        if (!empty($fresh['velocity_tiers']) && is_array($fresh['velocity_tiers'])) {
            $freshSubtotal = (float)($fresh['base_subtotal'] ?? $baseSubtotal);
            return self::normalizeVelocityTiers(array_values($fresh['velocity_tiers']), $freshSubtotal, $isEn);
        }

        // 3. Fallback if stored in prd_content['velocity_pricing_options'] and non-empty
        if (!empty($blueprint->prd_content['velocity_pricing_options']) && is_array($blueprint->prd_content['velocity_pricing_options'])) {
            return self::normalizeVelocityTiers(array_values($blueprint->prd_content['velocity_pricing_options']), $baseSubtotal, $isEn);
        }

        // 4. Derive from blueprint budget and context
        $contextBudget = $blueprint->user_metadata['kisaran_budget'] 
            ?? ($blueprint->prd_content['engineering_specs']['budget_range'] ?? null);

        $generated = self::generateVelocityPricingOptions(
            $blueprint->target_waktu ?? '30 Hari Kerja',
            $contextBudget,
            $blueprint->nama_bisnis ?? $blueprint->client_name,
            $blueprint->masalah_utama ?? ''
        );

        if (!empty($generated)) {
            return self::normalizeVelocityTiers($generated, $baseSubtotal, $isEn);
        }

        return [];
    }

    /**
     * Normalize, sanitize, and provide Tier 1 bilingual fields for velocity pricing tiers.
     * Guarantees that legacy or cached "Gemini Ultra" / "Swarm AI" text is dynamically rewritten
     * into professional enterprise engineering squad metrics.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function normalizeVelocityTiers(array $tiers, float $baseSubtotal = 0, bool $isEn = false): array
    {
        $normalized = [];
        $subtotal = $baseSubtotal > 0 ? $baseSubtotal : 54500000;

        foreach ($tiers as $idx => $t) {
            $id = $t['id'] ?? '';
            $name = strtolower($t['name'] ?? '');
            $contractAmount = (float)($t['contract_amount'] ?? 0);
            $dpAmount = (float)($t['dp_amount'] ?? ($contractAmount * 0.5));
            $pelunasan = max(0, $contractAmount - $dpAmount);
            $extra = max(0, $contractAmount - $subtotal);

            if ($id === 'fast_track' || str_contains($name, 'fast') || str_contains($name, 'swarm') || str_contains($name, 'accelerat')) {
                $t['id'] = 'fast_track';
                $t['name_id'] = 'Accelerated Dedicated Squad (14 Hari Kerja)';
                $t['name_en'] = 'Accelerated Dedicated Squad (14 Business Days)';
                $t['name'] = $isEn ? $t['name_en'] : $t['name_id'];
                $t['badge_id'] = 'RECOMMENDED // DEDICATED_SQUAD';
                $t['badge_en'] = 'RECOMMENDED // DEDICATED_SQUAD';
                $t['badge'] = 'RECOMMENDED // DEDICATED_SQUAD';
                $t['duration_id'] = '14 Hari Kerja';
                $t['duration_en'] = '14 Business Days';
                $t['duration'] = $isEn ? $t['duration_en'] : $t['duration_id'];
                $t['speed_multiplier_id'] = '1.5x (Akselerasi 2 Pekan)';
                $t['speed_multiplier_en'] = '1.5x (2-Week Acceleration)';
                $t['speed_multiplier'] = $isEn ? $t['speed_multiplier_en'] : $t['speed_multiplier_id'];
                $t['cost_formula_id'] = 'Base Scope (Rp ' . number_format($subtotal, 0, ',', '.') . ') + Dedicated Senior Squad & Akselerasi Paralel (Rp ' . number_format($extra, 0, ',', '.') . ')';
                $t['cost_formula_en'] = 'Base Scope (Rp ' . number_format($subtotal, 0, ',', '.') . ') + Dedicated Senior Squad & Dual-Track Acceleration (Rp ' . number_format($extra, 0, ',', '.') . ')';
                $t['cost_formula'] = $isEn ? $t['cost_formula_en'] : $t['cost_formula_id'];
                $t['squad_allocation_id'] = 'Lead Architect + 2 Dedicated Senior Fullstack Engineers';
                $t['squad_allocation_en'] = 'Lead Architect + 2 Dedicated Senior Fullstack Engineers';
                $t['squad_allocation'] = $isEn ? $t['squad_allocation_en'] : $t['squad_allocation_id'];
                $t['ai_quota_spec_id'] = 'Dual-Track Concurrent Architecture (Paralelisasi Frontend Island & Backend DB Sprints)';
                $t['ai_quota_spec_en'] = 'Dual-Track Concurrent Architecture (Simultaneous Frontend & Backend Sprints)';
                $t['ai_quota_spec'] = $isEn ? $t['ai_quota_spec_en'] : $t['ai_quota_spec_id'];
                $t['ai_swarm_specs_id'] = [
                    'Seluruh cakupan rincian fitur paket Standard',
                    'Paralelisasi Frontend Island & Backend DB Migration serentak (Dual-Track Sprints)',
                    'Alokasi eksklusif Senior Engineers tanpa distraksi antrean proyek lain',
                    'Prioritas Review dan Deploy Staging otomatis setiap akhir sprint',
                ];
                $t['ai_swarm_specs_en'] = [
                    'All feature specifications included in Standard tier',
                    'Parallelized Frontend Island & Backend DB Migration simultaneously (Dual-Track Sprints)',
                    'Dedicated Senior Engineers exclusively assigned with zero client-queue delay',
                    'Priority Code Review and automated Staging deployment at each sprint end',
                ];
                $t['ai_swarm_specs'] = $isEn ? $t['ai_swarm_specs_en'] : $t['ai_swarm_specs_id'];
            } elseif ($id === 'hyper_sprint' || str_contains($name, 'hyper') || str_contains($name, 'war_room') || str_contains($name, 'emergency') || str_contains($name, 'top_speed')) {
                $t['id'] = 'hyper_sprint';
                $t['name_id'] = 'Hyper-Sprint Emergency (24/7 War Room)';
                $t['name_en'] = 'Hyper-Sprint Emergency (24/7 War Room)';
                $t['name'] = $isEn ? $t['name_en'] : $t['name_id'];
                $t['badge_id'] = 'TOP_SPEED // 24_7_WAR_ROOM';
                $t['badge_en'] = 'TOP_SPEED // 24_7_WAR_ROOM';
                $t['badge'] = 'TOP_SPEED // 24_7_WAR_ROOM';
                $t['duration_id'] = '7 Hari Kalender';
                $t['duration_en'] = '7 Calendar Days';
                $t['duration'] = $isEn ? $t['duration_en'] : $t['duration_id'];
                $t['speed_multiplier_id'] = '3.0x (Rilis 1 Pekan Kalender)';
                $t['speed_multiplier_en'] = '3.0x (1 Calendar Week Release)';
                $t['speed_multiplier'] = $isEn ? $t['speed_multiplier_en'] : $t['speed_multiplier_id'];
                $t['cost_formula_id'] = 'Base Scope (Rp ' . number_format($subtotal, 0, ',', '.') . ') + Intensive 24/7 War Room Shift Squad (Rp ' . number_format($extra, 0, ',', '.') . ')';
                $t['cost_formula_en'] = 'Base Scope (Rp ' . number_format($subtotal, 0, ',', '.') . ') + Intensive 24/7 War Room Shift Squad (Rp ' . number_format($extra, 0, ',', '.') . ')';
                $t['cost_formula'] = $isEn ? $t['cost_formula_en'] : $t['cost_formula_id'];
                $t['squad_allocation_id'] = 'Dedicated Tri-Engineer War Room (Rotasi Shift 24/7 & DevOps SLA)';
                $t['squad_allocation_en'] = 'Dedicated Tri-Engineer War Room (24/7 Shift Rotation & DevOps SLA)';
                $t['squad_allocation'] = $isEn ? $t['squad_allocation_en'] : $t['squad_allocation_id'];
                $t['ai_quota_spec_id'] = 'Continuous Deployment War Room (Rotasi Shift 24/7 & Real-Time Test Loop)';
                $t['ai_quota_spec_en'] = 'Continuous Deployment War Room (24/7 Shift Rotation & Real-Time Test Loop)';
                $t['ai_quota_spec'] = $isEn ? $t['ai_quota_spec_en'] : $t['ai_quota_spec_id'];
                $t['ai_swarm_specs_id'] = [
                    'Rotasi engineer 24 jam non-stop dengan deployment kontinyu ke Staging',
                    'SLA Uptime & Respons Darurat 99.9% dengan dedicated DevOps on-call',
                    'Automated E2E Regression Test Suite untuk menjamin stabilitas rilis',
                ];
                $t['ai_swarm_specs_en'] = [
                    'Non-stop 24-hour engineer rotation with continuous staging deployment',
                    '99.9% Uptime SLA & Emergency Response with dedicated on-call DevOps',
                    'Automated E2E Regression Test Suite guaranteeing production stability',
                ];
                $t['ai_swarm_specs'] = $isEn ? $t['ai_swarm_specs_en'] : $t['ai_swarm_specs_id'];
            } else {
                $t['id'] = $id ?: 'standard';
                $t['name_id'] = 'Standard Velocity (30 Hari Kerja)';
                $t['name_en'] = 'Standard Velocity (30 Business Days)';
                $t['name'] = $isEn ? $t['name_en'] : $t['name_id'];
                $t['badge_id'] = 'STANDARD_PACE // BEST_VALUE';
                $t['badge_en'] = 'STANDARD_PACE // BEST_VALUE';
                $t['badge'] = 'STANDARD_PACE // BEST_VALUE';
                $t['duration_id'] = '30 Hari Kerja';
                $t['duration_en'] = '30 Business Days';
                $t['duration'] = $isEn ? $t['duration_en'] : $t['duration_id'];
                $t['speed_multiplier_id'] = '1.0x (Pace Terencana)';
                $t['speed_multiplier_en'] = '1.0x (Planned Pace)';
                $t['speed_multiplier'] = $isEn ? $t['speed_multiplier_en'] : $t['speed_multiplier_id'];
                $t['cost_formula_id'] = 'Bobot Nilai Ruang Lingkup Riil (Komponen Teranalisis)';
                $t['cost_formula_en'] = 'Real Itemized Scope Value (Analyzed Components)';
                $t['cost_formula'] = $isEn ? $t['cost_formula_en'] : $t['cost_formula_id'];
                $t['squad_allocation_id'] = '1 Dedicated Fullstack Lead + QA Reviewer';
                $t['squad_allocation_en'] = '1 Dedicated Fullstack Lead + QA Reviewer';
                $t['squad_allocation'] = $isEn ? $t['squad_allocation_en'] : $t['squad_allocation_id'];
                $t['ai_quota_spec_id'] = 'Modern Monolith Engine (Laravel 13, Filament v5, PostgreSQL Strict ULID)';
                $t['ai_quota_spec_en'] = 'Modern Monolith Engine (Laravel 13, Filament v5, PostgreSQL Strict ULID)';
                $t['ai_quota_spec'] = $isEn ? $t['ai_quota_spec_en'] : $t['ai_quota_spec_id'];
                $t['ai_swarm_specs_id'] = [
                    'Arsitektur: Laravel 13, Filament v5, PostgreSQL Strict ULID, Docker VPS',
                    'Hak Akses: Multi-role terisolasi dengan otorisasi ketat',
                    'Garansi: 30 Hari Bug-free Support + Akses Penuh Private Repo GitHub',
                ];
                $t['ai_swarm_specs_en'] = [
                    'Architecture: Laravel 13, Filament v5, PostgreSQL Strict ULID, Docker VPS',
                    'Access Control: Multi-role isolated with strict authorization',
                    'Warranty: 30-Day Bug-free Support + Full Private GitHub Repo Access',
                ];
                $t['ai_swarm_specs'] = $isEn ? $t['ai_swarm_specs_en'] : $t['ai_swarm_specs_id'];
            }

            $normalized[] = $t;
        }

        return $normalized;
    }


    /**
     * Resolve all available velocity pricing tiers for a blueprint, ensuring complete consistency
     * between the PRD view, Cart, Midtrans Snap checkout, and Digital Contracts.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getAvailableVelocityTiers(VisionBlueprint $blueprint): array
    {
        $tiers = [];

        // 1. Primary continuum tiers (UMKM -> Growth -> Scale -> Enterprise)
        foreach (self::getPrimaryVelocityTiers($blueprint) as $t) {
            if (!empty($t['id'])) {
                $tiers[$t['id']] = $t;
            }
        }

        // 2. Ensure budget-aware continuum tiers are also available
        $contextBudget = $blueprint->user_metadata['kisaran_budget'] 
            ?? ($blueprint->prd_content['engineering_specs']['budget_range'] ?? null);
        $generated = self::generateVelocityPricingOptions(
            $blueprint->target_waktu ?? '30 Hari Kerja',
            $contextBudget,
            $blueprint->nama_bisnis ?? $blueprint->client_name,
            $blueprint->masalah_utama ?? ''
        );
        foreach ($generated as $t) {
            if (!empty($t['id']) && !isset($tiers[$t['id']])) {
                $tiers[$t['id']] = $t;
            }
        }

        // 3. Ensure itemized cost breakdown tiers (standard, fast_track, hyper_sprint) are also available
        $itemized = $blueprint->prd_content['itemized_cost_breakdown'] ?? null;
        if (empty($itemized) || empty($itemized['velocity_tiers'])) {
            $itemized = self::calculateItemizedEstimation($blueprint);
        }
        if (!empty($itemized['velocity_tiers']) && is_array($itemized['velocity_tiers'])) {
            foreach ($itemized['velocity_tiers'] as $t) {
                if (!empty($t['id']) && !isset($tiers[$t['id']])) {
                    $tiers[$t['id']] = $t;
                }
            }
        }

        return array_values($tiers);
    }


    /**
     * Find a specific velocity tier by its ID with smart matching and safe fallback.
     *
     * @return array<string, mixed>
     */
    public static function resolveVelocityTier(VisionBlueprint $blueprint, ?string $tierId): array
    {
        $allTiers = self::getAvailableVelocityTiers($blueprint);

        if (!empty($tierId)) {
            // 1. Exact match
            foreach ($allTiers as $t) {
                if (($t['id'] ?? '') === $tierId) {
                    return $t;
                }
            }

            // 2. Intelligent fuzzy/keyword match (e.g. 'enterprise_fast' matching 'fast' or 'swarm')
            $cleanTier = strtolower(trim($tierId));
            foreach ($allTiers as $t) {
                $tid = strtolower($t['id'] ?? '');
                if ((str_contains($cleanTier, 'fast') || str_contains($cleanTier, 'swarm')) && (str_contains($tid, 'fast') || str_contains($tid, 'swarm'))) {
                    return $t;
                }
                if ((str_contains($cleanTier, 'hyper') || str_contains($cleanTier, 'emergency') || str_contains($cleanTier, 'war_room')) && (str_contains($tid, 'hyper') || str_contains($tid, 'emergency'))) {
                    return $t;
                }
                if (str_contains($cleanTier, 'standard') && str_contains($tid, 'standard')) {
                    return $t;
                }
            }
        }

        if (!empty($allTiers)) {
            // Default to middle recommended tier if available, otherwise first tier
            return count($allTiers) >= 2 ? $allTiers[1] : $allTiers[0];
        }

        return [
            'id' => 'standard',
            'name' => 'Standard Velocity (30 Hari Kerja)',
            'duration' => '30 Hari Kerja',
            'contract_amount' => 50000000.00,
            'dp_amount' => 25000000.00,
            'pelunasan_amount' => 25000000.00,
        ];
    }


    /**
     * Compute tailored server hardware capacity sizing based on business workload.
     */
    public static function calculateServerHardwareSizing(string $businessName, string $masalah, array $mvpItems, array $extraContext = []): array
    {
        $scaleDeclared = strtolower($extraContext['skala_pengguna'] ?? '');
        $isHighTraffic = str_contains($scaleDeclared, '500.000') || str_contains($scaleDeclared, '1.000.000') || str_contains($scaleDeclared, 'enterprise');
        $isMediumTraffic = str_contains($scaleDeclared, '100.000') || str_contains($scaleDeclared, '250.000');

        $hasAi = false;
        $hasMedia = false;
        $hasPayment = false;

        foreach ($mvpItems as $item) {
            $text = strtolower(($item['title'] ?? '') . ' ' . ($item['desc'] ?? ''));
            if (str_contains($text, 'ai') || str_contains($text, 'vector') || str_contains($text, 'cerdas') || str_contains($text, 'rekomendasi')) $hasAi = true;
            if (str_contains($text, 'upload') || str_contains($text, 'foto') || str_contains($text, 'gambar') || str_contains($text, 'media') || str_contains($text, 'berkas')) $hasMedia = true;
            if (str_contains($text, 'bayar') || str_contains($text, 'midtrans') || str_contains($text, 'checkout') || str_contains($text, 'transaksi')) $hasPayment = true;
        }

        if ($isHighTraffic) {
            $tierName = 'Enterprise High-Throughput Dedicated VPS';
            $vcpu = 4;
            $ramGb = 8;
            $storageNvmeGb = 160;
            $bandwidthTb = 10;
            $monthlyCostIdr = 'Rp 450.000 - Rp 950.000';
            $monthlyCostUsd = '$30 - $60';
            $profile = 'High-Concurrency OLTP & Asynchronous Worker Cluster';
        } elseif ($isMediumTraffic || $hasAi) {
            $tierName = 'Production Performance Dedicated VPS (Rekomendasi Utama)';
            $vcpu = 2;
            $ramGb = 4;
            $storageNvmeGb = 80;
            $bandwidthTb = 5;
            $monthlyCostIdr = 'Rp 200.000 - Rp 450.000';
            $monthlyCostUsd = '$15 - $30';
            $profile = 'Balanced Web Application, PostgreSQL In-Memory Buffer & Background Queues';
        } else {
            $tierName = 'Lean Cloud Starter / Micro VPS';
            $vcpu = 2;
            $ramGb = 2;
            $storageNvmeGb = 40;
            $bandwidthTb = 2;
            $monthlyCostIdr = 'Rp 90.000 - Rp 250.000';
            $monthlyCostUsd = '$6 - $15';
            $profile = 'Lean Resource Footprint for Early-Stage Validation & Business Profile';
        }

        return [
            'tier_name' => $tierName,
            'workload_profile' => $profile,
            'specifications' => [
                'vcpu' => [
                    'count' => $vcpu . ' vCPU Cores (Dedicated High-Frequency)',
                    'architecture' => 'x86_64 AMD EPYC / Intel Xeon Cascade Lake (3.0 GHz+)',
                    'allocation' => [
                        'Web Server & PHP-FPM Workers' => round($vcpu * 0.5, 1) . ' vCPU (Penanganan 25-50 HTTP req/detik)',
                        'PostgreSQL 16 Database Engine' => round($vcpu * 0.3, 1) . ' vCPU (Eksekusi query B-Tree & ULID cursor)',
                        'Redis In-Memory Queue & Scheduler' => round($vcpu * 0.2, 1) . ' vCPU (Pemrosesan background jobs asinkron)',
                    ],
                ],
                'ram' => [
                    'total' => $ramGb . ' GB RAM DDR4 / DDR5 ECC',
                    'budget_distribution' => [
                        ['component' => 'Linux OS Kernel & Base Daemons', 'size' => round($ramGb * 0.12, 2) . ' GB', 'pct' => '12%'],
                        ['component' => 'PHP-FPM Worker Pool (15-30 Processes)', 'size' => round($ramGb * 0.25, 2) . ' GB', 'pct' => '25%'],
                        ['component' => 'PostgreSQL 16 shared_buffers & work_mem', 'size' => round($ramGb * 0.28, 2) . ' GB', 'pct' => '28%'],
                        ['component' => 'Redis Cache, Sessions & Queues', 'size' => round($ramGb * 0.15, 2) . ' GB', 'pct' => '15%'],
                        ['component' => 'Safety Headroom for Traffic Bursts & PDF/Excel Exports', 'size' => round($ramGb * 0.20, 2) . ' GB', 'pct' => '20%'],
                    ],
                ],
                'storage' => [
                    'capacity' => $storageNvmeGb . ' GB NVMe SSD (PCIe Gen 4.0)',
                    'speed' => 'Read/Write hingga 3.500 MB/s (Zero I/O Wait)',
                    'distribution' => [
                        ['use' => 'OS Linux, Nixpacks Runtimes & Container Engine', 'size' => '12 GB'],
                        ['use' => 'PostgreSQL 16 Data Tables, WAL & Indices (~10M Baris Data)', 'size' => round($storageNvmeGb * 0.35) . ' GB'],
                        ['use' => 'Curator Media Storage & Dokumen Attachment', 'size' => round($storageNvmeGb * 0.35) . ' GB'],
                        ['use' => 'Cadangan Snapshot Lokal & Rolling DB Dump', 'size' => round($storageNvmeGb * 0.15) . ' GB'],
                    ],
                ],
                'network' => [
                    'port_speed' => '1 Gbps Uplink Dedicated Port',
                    'bandwidth' => $bandwidthTb . ' TB / Bulan (Unmetered Fair Usage)',
                    'latency_target' => '< 25 ms Domestik Indonesia via Cloudflare Global CDN Edge',
                ],
            ],
            'estimated_monthly_investment' => [
                'idr' => $monthlyCostIdr . ' / bulan',
                'usd' => $monthlyCostUsd . ' / month',
            ],
            'benchmark_providers' => [
                [
                    'name' => 'Marketplace & Promo Registrar Lokal (Shopee / Tokopedia / Domainesia / Rumahweb)',
                    'badge' => 'OPSI UMKM ULTRA-LEAN (< RP 500.000 / TAHUN)',
                    'plan' => 'Cloud Web Hosting cPanel / NAT Micro VPS + Domain Lokal (.my.id / .biz.id)',
                    'est_cost' => 'Rp 25.000 - Rp 40.000 / bln (Total < Rp 500.000 / TAHUN)',
                    'pros' => 'Pilihan sangat ekonomis untuk UMKM perintis & validasi ide awal. Neriah Pro mendukung mode arsitektur SQLite/MySQL ringan yang langsung aktif tanpa setup server rumit.',
                ],
                [
                    'name' => 'Managed Dedicated Cloud VPS Neriah Pro',
                    'badge' => 'REKOMENDASI TERPADU (AGENCY MANAGED)',
                    'plan' => 'Custom Cloud Container (Postgres 16, Redis, Automated Backup)',
                    'est_cost' => $monthlyCostIdr . ' / bulan',
                    'pros' => 'Terima beres, zero DevOps maintenance bagi klien, backup rolling otomatis setiap tengah malam.',
                ],
                [
                    'name' => 'IDCloudHost / Biznet Gio (Domestik Indonesia)',
                    'badge' => 'DATA CENTER LOKAL (IIX)',
                    'plan' => 'Cloud VPS Pro ' . $vcpu . 'C/' . $ramGb . 'GB',
                    'est_cost' => 'Rp ' . number_format($vcpu * 90000 + $ramGb * 35000, 0, ',', '.') . ' / bulan',
                    'pros' => 'Data tersimpan di wilayah hukum Indonesia, latensi transfer perbankan lokal optimal.',
                ],
                [
                    'name' => 'Hetzner Cloud / DigitalOcean (Global Hyperscaler)',
                    'badge' => 'GLOBAL COST-TO-PERFORMANCE LEADER',
                    'plan' => 'CX/CPX Series (' . $vcpu . ' vCPU / ' . $ramGb . 'GB RAM)',
                    'est_cost' => $monthlyCostUsd . ' / bulan (Rp ' . number_format($vcpu * 85000 + $ramGb * 30000, 0, ',', '.') . ' / bln)',
                    'pros' => 'Hardware AMD EPYC kelas atas dengan stabilitas SLA 99.95%.',
                ],
            ],
            'spectrum_tiers' => [
                'umkm_lean' => [
                    'id' => 'umkm_lean',
                    'name' => 'Tier 1: UMKM Ultra-Lean (Promo Online Shop & Registrar Lokal)',
                    'badge' => 'HEMAT BIAYA (< RP 500.000 / TAHUN)',
                    'badge_color' => 'amber',
                    'annual_total' => 'Rp 290.000 - Rp 490.000 / TAHUN',
                    'monthly_equivalent' => '~Rp 25.000 - Rp 40.000 / bulan',
                    'target_use' => 'Validasi Ide, Etalase Produk UMKM, Profil Bisnis & Toko Lokal (0 - 5.000 Pengunjung/Bulan)',
                    'domain_choice' => '.my.id (Rp 15.000/thn) atau .biz.id (Rp 25.000/thn) atau .com promo (Rp 99.000/thn)',
                    'hosting_choice' => 'Shared Hosting cPanel / Cloud Starter / Micro VPS Promo Tokopedia / Shopee',
                    'specs_label' => '1 vCPU Virtual • 1 GB RAM • 10-20 GB Storage • Unmetered Shared Bandwidth',
                    'db_profile' => 'SQLite 3 Standar / MySQL Shared (Sangat ringan, zero daemon background RAM)',
                    'byoh_supported' => true,
                    'pros' => 'Modal awal super ringan, risiko finansial nol, langsung online dan bisa dibeli mandiri di Shopee/Tokopedia.',
                    'cons' => 'Resource komputasi dibagi dengan website lain; tidak disarankan untuk background queue berat atau ribuan transaksi detik yang sama.',
                ],
                'vps_production' => [
                    'id' => 'vps_production',
                    'name' => 'Tier 2: Production Performance Dedicated VPS',
                    'badge' => 'REKOMENDASI SCALE-UP (KVM DEDICATED)',
                    'badge_color' => 'emerald',
                    'annual_total' => 'Rp 1.200.000 - Rp 2.800.000 / TAHUN',
                    'monthly_equivalent' => 'Rp 90.000 - Rp 250.000 / bulan',
                    'target_use' => 'Bisnis Berjalan, Webhook Midtrans Instan, Multi-Kasir POS, 5.000 - 100.000 Pengguna/Bulan',
                    'domain_choice' => '.com (Rp 120.000/thn) atau .id (Rp 200.000/thn)',
                    'hosting_choice' => 'Dedicated KVM VPS (Biznet Gio / IDCloudHost / Hetzner / DigitalOcean)',
                    'specs_label' => $vcpu . ' vCPU Dedicated • ' . $ramGb . ' GB ECC RAM • ' . $storageNvmeGb . ' GB NVMe SSD • ' . $bandwidthTb . ' TB Bandwidth',
                    'db_profile' => 'PostgreSQL 16 Strict ULID + Redis In-Memory Cache',
                    'byoh_supported' => true,
                    'pros' => 'Isolasi resource 100%, PostgreSQL 16 Strict ULID, Redis queue 24/7, performa kencang tanpa lag.',
                    'cons' => 'Ada komitmen biaya operasional bulanan.',
                ],
                'enterprise_cluster' => [
                    'id' => 'enterprise_cluster',
                    'name' => 'Tier 3: Enterprise High-Throughput Cluster',
                    'badge' => 'HIGH-CONCURRENCY (> 100.000 PENGGUNA)',
                    'badge_color' => 'sky',
                    'annual_total' => 'Rp 5.400.000 - Rp 11.400.000+ / TAHUN',
                    'monthly_equivalent' => 'Rp 450.000 - Rp 950.000+ / bulan',
                    'target_use' => 'Platform E-Commerce Skala Nasional, Fintech, Logistik Armada, High-Frequency API',
                    'domain_choice' => 'Multi-Domain Brand Protection (.id, .com, .co.id)',
                    'hosting_choice' => 'Clustered Nodes + Managed Database + Anycast Global Edge',
                    'specs_label' => '4-8 vCPU Dedicated • 8-16 GB ECC RAM • 160-320 GB NVMe • 10 TB Bandwidth',
                    'db_profile' => 'High-Availability PostgreSQL Cluster + Read Replicas + Redis Cluster',
                    'byoh_supported' => true,
                    'pros' => 'SLA 99.95%, failover otomatis, siap melayani lonjakan kampanye flash sale tanpa downtime.',
                    'cons' => 'Investasi infrastruktur tinggi untuk skala besar.',
                ],
            ],
            'domain_comparison_matrix' => [
                [
                    'tld' => '.my.id',
                    'category' => 'Ultra-Hemat UMKM & Personal',
                    'est_price' => 'Rp 12.000 - Rp 25.000 / tahun',
                    'authority' => 'Resmi PANDI Indonesia (Bebas KTP/Syarat Rumit)',
                    'best_for' => 'Portofolio digital, usaha kuliner rumahan, jasa perseorangan, & tes pasar awal.',
                ],
                [
                    'tld' => '.biz.id',
                    'category' => 'Khusus Bisnis & UMKM Indonesia',
                    'est_price' => 'Rp 15.000 - Rp 35.000 / tahun',
                    'authority' => 'Resmi PANDI Indonesia (Reputasi Usaha Terpercaya)',
                    'best_for' => 'Toko retail online, warung makan, jasa servis lokal, & brand rintisan.',
                ],
                [
                    'tld' => '.com',
                    'category' => 'Standar Komersial Global',
                    'est_price' => 'Rp 99.000 - Rp 140.000 (Promo Thn 1)',
                    'authority' => 'Global ICANN (Top-of-Mind Pengguna)',
                    'best_for' => 'Bisnis komersial umum, target pasar nasional hingga internasional.',
                ],
                [
                    'tld' => '.id',
                    'category' => 'Identitas Korporat Premium Indonesia',
                    'est_price' => 'Rp 200.000 - Rp 250.000 / tahun',
                    'authority' => 'Resmi PANDI (Kredibilitas Hukum Tertinggi)',
                    'best_for' => 'Perusahaan mapan, institusi resmi, perlindungan hak merek paten.',
                ],
            ],
            'hosting_freedom_protocol' => [
                'title' => 'Protokol Kebebasan Infrastruktur Klien (Bring Your Own Hosting / BYOH)',
                'principles' => [
                    'Zero Vendor Lock-In: Neriah Pro tidak pernah mengunci Anda ke provider hosting tertentu. Anda bebas membeli domain & hosting murah sendiri di Shopee, Tokopedia, Niagahoster, Domainesia, Rumahweb, atau marketplace mana pun.',
                    'Demokratisasi Teknologi untuk UMKM: Jangan bakar uang untuk server mahal jika bisnis Anda baru mulai! Validasi produk Anda dulu dengan opsi hemat < Rp 500.000 / tahun.',
                    'Tangga Pertumbuhan Mulus (Seamless Growth Ladder): Kode sumber yang dibangun oleh Project OS (Laravel 13, Strict ULID, Keyset Cursor Pagination) bersifat portabel. Anda dapat memindahkan aplikasi dari hosting murah ke VPS Dedicated kapan saja dalam 5 menit tanpa merombak ulang sistem.',
                ],
            ],
            'scaling_thresholds' => [
                'CPU Utilization: Rata-rata penggunaan melampaui 75% selama 15 menit berturut-turut.',
                'RAM Saturation: Penggunaan memori riil konsisten di atas 85% dari total kapasitas.',
                'Disk Capacity: Ruang kosong NVMe tersisa kurang dari 20%.',
                'Pencegahan: Naikkan tier server hanya dalam 2 menit tanpa perlu memprogram ulang aplikasi (Seamless Vertical Scaling).',
            ],
        ];
    }


    /**
     * Generate Comprehensive Modern Decoupled & Multi-Party Architecture Blueprint (2026+ Standards).
     * Specifies exact frontend frameworks, mobile clients, headless APIs, cloud servers, and integration protocols.
     */
    public static function generateDecoupledToolingStrategy(string $businessName, array $extraContext = [], array $erdTables = [], array $mvpItems = []): array
    {
        $targetPlatform = $extraContext['target_platform'] ?? 'Modern Web & Mobile Cross-Platform';
        $hostingPref = $extraContext['preferensi_hosting'] ?? 'Decoupled Multi-Tier Cloud';
        $scale = $extraContext['skala_pengguna'] ?? 'Enterprise Scale';

        return [
            'title' => 'Cetak Biru Arsitektur Decoupled & Ekosistem Multi-Pihak (Modern 2026+ Standards)',
            'badge' => 'ENTERPRISE_DECOUPLED_SUITE_2026',
            'summary' => 'Panduan komprehensif bagi perusahaan yang memilih pola arsitektur terpisah (Decoupled Headless / Microservices). Memisahkan presentation consumer layer dari core transaction engine via kontrak terbuka OpenAPI 3.1, menjamin independensi rilis antar tim, zero-downtime micro-frontends, dan performa edge global.',
            'multi_party_tools_matrix' => [
                'web_consumer' => [
                    'category' => 'Web Consumer & Landing Portal',
                    'primary' => 'Next.js 15 (App Router, Turbopack, React 19 Server Components)',
                    'alternatives' => 'Nuxt 3 (Vue 3 + Nitro) atau Astro 4 (Content-driven island architecture)',
                    'role' => 'Menangani rendering SSR/SSG/ISR publik, portal checkout e-commerce, Web Vitals SEO sempurna, dan edge caching sub-20ms.',
                    'justification' => 'Pemisahan repositori memungkinkan tim UI/UX frontend merilis perubahan visual setiap hari tanpa menyentuh atau membahayakan kestabilan kode backend transaksi.',
                ],
                'mobile_clients' => [
                    'category' => 'Mobile Cross-Platform Clients (iOS & Android)',
                    'primary' => 'Flutter 3.x (Dart 3) / React Native (Expo SDK 52+ New Architecture)',
                    'local_database' => 'Drift / SQLite 3 Encrypted (Flutter) atau WatermelonDB (React Native)',
                    'role' => 'Aplikasi native untuk Driver, Kurir, Operator Lapangan, Kasir POS Tablet, dan Customer Mobile.',
                    'offline_strategy' => '100% Offline-First dengan mutation journal lokal. Pekerja lapangan tetap dapat input data dan cetak bukti transaksi di area blank spot internet.',
                ],
                'backend_core' => [
                    'category' => 'Core Business Engine & Headless API',
                    'primary' => 'Headless Laravel 13 (OpenAPI 3.1 RESTful Resource & Queues)',
                    'admin_backoffice' => 'Filament v5 Enterprise Backoffice (Dedicated Internal Ops, RBAC Shield)',
                    'high_concurrency_extensions' => 'Go (Fiber/Gin) untuk real-time WebSocket telemetri, Python (FastAPI) untuk AI Agent RAG orchestration.',
                    'role' => 'Menjaga integritas data ACID, eksekusi state machine transaksi, mutasi finansial, dan audit logging terenkripsi.',
                ],
                'api_contracts' => [
                    'category' => 'Standar Kontrak & Dokumentasi API Multi-Pihak',
                    'specification' => 'OpenAPI 3.1 / JSON Schema Strict Contract',
                    'interactive_docs' => 'Scalar Interactive API Reference & Swagger UI (/api/docs)',
                    'codegen_support' => 'TypeSpec / openapi-typescript-codegen untuk autogenerasi TypeScript types & Zod validation schema pada Next.js secara instan.',
                    'role' => 'Mencegah miskomunikasi antar tim frontend, mobile, dan backend developer; 100% type-safe end-to-end.',
                ],
                'bff_layer' => [
                    'category' => 'BFF (Backend for Frontend) Pattern',
                    'strategy' => 'Dedicated API Route Handlers per Consumer Client',
                    'description' => 'Memisahkan payload desktop (data analitik padat) dengan payload mobile (data terkompresi Brotli ringan), memangkas konsumsi bandwidth seluler hingga 70% dan menghemat baterai perangkat pengguna.',
                ],
            ],
            'server_and_cloud_topology' => [
                'edge_frontend' => [
                    'tier' => 'Lapisan Frontend & Edge CDN Global',
                    'provider' => 'Cloudflare Pages / Vercel Edge Network',
                    'coverage' => '300+ Point of Presence (POP) Anycast di seluruh dunia (Jakarta, Surabaya, Singapura, Tokyo, Frankfurt, dll).',
                    'specs' => 'Sub-20ms TTFB, Unlimited Bandwidth, HTTP/3, TLS 1.3, Automasi SSL Zero-Config.',
                    'cost_range' => 'Rp 0 - Rp 300.000 / bulan (Free Tier mencakup jutaan request)',
                ],
                'api_backend_vps' => [
                    'tier' => 'Lapisan API Core & Background Workers',
                    'provider' => 'Dedicated Cloud VPS via Coolify Docker Engine / AWS ECS Fargate',
                    'specs' => '4 - 8 vCPU Dedicated High-Frequency, 8 - 16 GB ECC RAM, NVMe Gen 4 Storage.',
                    'role' => 'Menjalankan container Docker API headless, worker Redis 24/7, scheduler cron, dan reverse proxy Nginx HTTP/2.',
                    'cost_range' => 'Rp 350.000 - Rp 950.000 / bulan (Hemat 80% dibanding membakar anggaran di cloud hyperscaler)',
                ],
                'database_cluster' => [
                    'tier' => 'Basis Data Relasional & AI Vector Hub',
                    'provider' => 'Managed PostgreSQL 16+ dengan PgBouncer Connection Pooling',
                    'features' => 'Primary Key ULID (skalabilitas terdistribusi), pgvector (HNSW search AI embeddings), JSONB indexing, Keyset Cursor Pagination O(1).',
                    'cost_range' => 'Termasuk dalam VPS terdedikasi atau Rp 400.000 / bln untuk managed Supabase/Neon DB.',
                ],
                'cache_and_event_bus' => [
                    'tier' => 'In-Memory Cache & Message Broker',
                    'provider' => 'Redis 7+ Cluster / DragonFlyDB + Apache Kafka / RabbitMQ',
                    'role' => 'Penyimpanan sesi token sub-millisecond, distributed rate limiting, dan antrean event-driven antar-layanan microservices.',
                ],
                'object_storage' => [
                    'tier' => 'Penyimpanan Berkas Media & Dokumen',
                    'provider' => 'Cloudflare R2 (100% S3 Compatible)',
                    'killer_feature' => 'RP 0 BIAYA EGRESS BANDWIDTH (Eliminasi biaya transfer data keluar tak terduga yang menjadi momok AWS S3).',
                    'cost_range' => 'Mulai Rp 0 / bulan (10 GB gratis pertama, $0.015 per GB tambahan)',
                ],
                'observability_apm' => [
                    'tier' => 'Pemantauan & Audit Keamanan Real-Time',
                    'tools' => 'OpenTelemetry tracing + Sentry exception tracker + BetterStack uptime ping + Grafana metrics.',
                    'sla_target' => '99.9% Uptime dengan MTTR (Mean Time to Resolution) < 15 menit.',
                ],
            ],
            'framework_strategy_and_protocols' => [
                'authentication' => [
                    'strategy' => 'Dual-Mode Authentication (HttpOnly Cookies & Stateless Asymmetric JWT)',
                    'web' => 'HttpOnly, Secure, SameSite=Lax Cookie via Subdomain CNAME (api.bisnis.com & app.bisnis.com) -> Mencegah 100% pencurian kredensial via serangan XSS.',
                    'mobile' => 'Stateless Asymmetric JWT (RS256) dengan Access Token 15 menit dan Sliding Refresh Token Rotation yang tersimpan aman di Android Keystore / iOS Keychain.',
                ],
                'data_sync_and_idempotency' => [
                    'strategy' => 'Deterministic Delta Sync & Idempotency Key Engine',
                    'idempotency' => 'Setiap request mutasi wajib mengirimkan header X-Idempotency-Key (UUIDv4). Server mengunci kunci selama 24 jam di Redis untuk mencegah double-order atau double-charge saat sinyal HP drop.',
                    'keyset_pagination' => 'Seluruh endpoint listing wajib menggunakan cursorPaginate() dengan pointer ULID (WHERE id > ? LIMIT 20), mengeliminasi query OFFSET yang membeku pada jutaan baris data.',
                    'conflict_resolution' => 'Last-Write-Wins (LWW) dengan stempel waktu ULID milidetik deterministik.',
                ],
                'cors_and_zero_trust' => [
                    'strategy' => 'Strict Origin Whitelisting & Defense-in-Depth',
                    'cors' => 'Origin whitelisting ketat (hanya domain frontend terdaftar). Dilarang keras menggunakan wildcard (*) pada endpoint terautentikasi.',
                    'bot_defense' => 'Cloudflare Turnstile CAPTCHA tak kasat mata pada endpoint publik sensitif (registrasi, reset sandi, submit form).',
                    'rate_limiting' => 'Token Bucket algorithm bertingkat berbasis Redis per IP dan per User ID.',
                ],
                'devsecops_cicd' => [
                    'strategy' => 'Automated GitHub Actions Pipeline',
                    'stages' => [
                        '1. Static Code Analysis (PHPStan level 8, ESLint, Prettier, TypeScript check)',
                        '2. Automated Unit & Integration Tests (Pest PHP & Jest/Vitest)',
                        '3. Docker Multi-Stage Build & Security Vulnerability Scan (Trivy)',
                        '4. Automated Database Migration Runner with Lock Guard',
                        '5. Zero-Downtime Blue-Green Rolling Deployment via Coolify Webhook',
                    ],
                ],
            ],
            'decoupling_migration_playbook' => [
                'title' => 'Panduan Transisi Bertahap: Dari Monolith Menuju Decoupled (Zero Risk)',
                'steps' => [
                    [
                        'phase' => 'Langkah 1: Standardisasi API Resources',
                        'desc' => 'Seluruh Controller bisnis diisolasi menggunakan Laravel API Resource (JsonResource) dengan format response JSON standar.',
                    ],
                    [
                        'phase' => 'Langkah 2: Terbitkan Kontrak OpenAPI 3.1',
                        'desc' => 'Generate spesifikasi OpenAPI 3.1 otomatis menggunakan Scalar/L5-Swagger sehingga tim frontend memiliki acuan kontrak resmi.',
                    ],
                    [
                        'phase' => 'Langkah 3: Bangun Frontend Next.js 15 Terpisah',
                        'desc' => 'Tim frontend membangun consumer portal Next.js 15 menggunakan types yang diautogenerasi dari kontrak OpenAPI 3.1.',
                    ],
                    [
                        'phase' => 'Langkah 4: Deploy Frontend ke Cloudflare Pages',
                        'desc' => 'Deploy frontend ke Cloudflare Pages dengan routing CNAME, mengalihkan trafik publik ke edge tanpa downtime backend.',
                    ],
                ],
            ],
        ];
    }

}
