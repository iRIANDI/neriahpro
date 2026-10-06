<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Product;
use App\Models\LegalPolicy;
use App\Models\VisionBlueprint;
use App\Models\Document;
use App\Models\Transaction;
use App\Models\LeadContact;

class WorkflowEndToEndSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. SEED LEGAL POLICIES (MANDATORY FOR MIDTRANS ONBOARDING)
        $this->seedLegalPolicies();

        // 2. SEED DIGITAL SERVICE PRODUCTS
        $products = $this->seedProducts();

        // 3. SEED LIVE VISION BLUEPRINT (PILAR 1: PROJECT OS)
        $blueprint = $this->seedVisionBlueprint();

        // 4. SEED CONNECTED DIGITAL CONTRACT WITH SCOPE LOCK (PILAR 2: DIGITAL CONTRACT)
        $contract = $this->seedDigitalContract($blueprint);

        // 5. SEED SETTLED DP TRANSACTION (PILAR 4: MIDTRANS PROOF)
        $this->seedTransaction($products['monolith'], $contract);

        // 6. SEED UNPAID / PENDING DP SAMPLE (FOR MIDTRANS REVIEWER SNAP POPUP DEMO)
        $this->seedUnpaidBlueprintAndContract();

        // 7. SEED CRM CLIENT INTAKE LEAD
        $this->seedLeadContacts();

        Schema::enableForeignKeyConstraints();

        $this->command->info('WorkflowEndToEndSeeder executed successfully: Blueprint, Digital Contract, Products, Policies, and Transactions are ready for Midtrans verification!');
    }

    private function seedLegalPolicies(): void
    {
        LegalPolicy::updateOrCreate(
            ['type' => 'terms_and_conditions'],
            [
                'title' => [
                    'id' => 'Syarat dan Ketentuan Layanan Neriah Pro',
                    'en' => 'Neriah Pro Terms and Conditions of Service'
                ],
                'content' => [
                    'id' => '<h3>1. Ruang Lingkup Layanan</h3><p>Neriah Pro menyediakan jasa rekayasa perangkat lunak berskala tinggi, penyusunan Product Requirements Document (PRD), dan kontrak kerja sama digital. Seluruh pengerjaan mengacu pada spesifikasi PRD yang telah dikunci (Scope Locked).</p><h3>2. Ketentuan Pembayaran DP (Down Payment)</h3><p>Pengerjaan sprint resmi dimulai setelah Klien menandatangani Kontrak Digital dan melunasi Uang Muka (DP) sebesar 50% melalui payment gateway Midtrans. Pelunasan 50% dilakukan saat serah terima sistem di server produksi.</p><h3>3. Perubahan Ruang Lingkup (Addendum)</h3><p>Setiap penambahan fitur di luar dokumen PRD terkunci akan dihitung sebagai Change Request (CR) / Addendum terpisah dengan tambahan biaya dan hari kerja tersendiri.</p>',
                    'en' => '<h3>1. Scope of Services</h3><p>Neriah Pro provides enterprise software architecture, automated PRD synthesis, and digital contract agreements. All development follows the agreed, scope-locked PRD specifications.</p><h3>2. Down Payment Terms</h3><p>Development sprints officially commence once the Client executes the Digital Contract and settles a 50% Down Payment (DP) via Midtrans. The remaining 50% balance is payable upon production deployment.</p><h3>3. Change Requests (Addendum)</h3><p>Any additional feature requests beyond the locked PRD document shall be handled via an independent Addendum with separate billing and timelines.</p>'
                ],
                'is_active' => true,
            ]
        );

        LegalPolicy::updateOrCreate(
            ['type' => 'privacy_policy'],
            [
                'title' => [
                    'id' => 'Kebijakan Privasi & Perlindungan Data',
                    'en' => 'Privacy and Data Protection Policy'
                ],
                'content' => [
                    'id' => '<h3>1. Kerahasiaan Ide & Arsitektur</h3><p>Kami menjamin 100% kerahasiaan ide bisnis, skema database, dan dokumen PRD Anda. Neriah Pro tidak akan menjual, menyewakan, atau mendistribusikan data klien kepada pihak ketiga mana pun.</p><h3>2. Penyimpanan Berstandar PostgreSQL ULID</h3><p>Seluruh identitas data dienkripsi dan disimpan menggunakan primary key ULID 26 karakter untuk menjamin integritas dan keamanan arsitektur data terdistribusi.</p>',
                    'en' => '<h3>1. Confidentiality of Client Ideas</h3><p>We guarantee 100% confidentiality regarding your business ideas, database schemas, and PRD specifications. Neriah Pro never sells, leases, or discloses client data to third parties.</p><h3>2. PostgreSQL ULID Security Standards</h3><p>All records are encrypted and indexed using 26-character ULID primary keys to maintain distributed database security and strict data isolation.</p>'
                ],
                'is_active' => true,
            ]
        );

        LegalPolicy::updateOrCreate(
            ['type' => 'refund_policy'],
            [
                'title' => [
                    'id' => 'Kebijakan Pengembalian Dana & Pembatalan',
                    'en' => 'Refund and Cancellation Policy'
                ],
                'content' => [
                    'id' => '<h3>1. Kebijakan Uang Muka (DP)</h3><p>Uang Muka (DP 50%) yang telah dibayarkan melalui Midtrans tidak dapat dikembalikan (non-refundable) apabila sprint pengerjaan arsitektur dan skema database telah resmi dimulai.</p><h3>2. Garansi Perbaikan Bug (30 Hari)</h3><p>Setelah serah terima sistem, Neriah Pro memberikan garansi pemeliharaan dan perbaikan bug secara gratis selama 30 hari kalender sesuai ruang lingkup kontrak.</p>',
                    'en' => '<h3>1. Down Payment Refund Terms</h3><p>The 50% Down Payment processed via Midtrans is non-refundable once architecture and database sprint development has commenced.</p><h3>2. 30-Day Bug Fix Warranty</h3><p>Upon production handover, Neriah Pro provides a 30-calendar-day warranty for defect fixes within the agreed scope of work at zero additional charge.</p>'
                ],
                'is_active' => true,
            ]
        );
    }

    private function seedProducts(): array
    {
        $products = [];

        $products['prd'] = Product::updateOrCreate(
            ['slug' => 'project-os-prd-architecture'],
            [
                'name' => [
                    'id' => 'Project OS & Architecture PRD Blueprint',
                    'en' => 'Project OS & Architecture PRD Blueprint'
                ],
                'description' => [
                    'id' => 'Layanan perancangan spesifikasi sistem lengkap: Product Requirements Document (PRD), skema tabel ERD PostgreSQL Strict ULID, alur kerja, dan pembagian 5 sprint proyek.',
                    'en' => 'Complete software specification service: Product Requirements Document (PRD), PostgreSQL Strict ULID ERD schema, user workflows, and 5 aligned project sprints.'
                ],
                'features' => [
                    'Kuesioner Discovery 4 Blok',
                    'Skema ERD Database PostgreSQL ULID',
                    'Matriks Scope MVP vs Roadmap Fase 2',
                    'Alokasi 5 Sprint Timeline Kerja',
                    'Dokumen Cetak PDF Siap Eksekusi'
                ],
                'price_idr' => 2500000.00,
                'price_usd' => 160.00,
                'is_active' => true,
            ]
        );

        $products['monolith'] = Product::updateOrCreate(
            ['slug' => 'rapid-monolith-mvp-development'],
            [
                'name' => [
                    'id' => 'Enterprise Rapid Monolith Development (Fase 1 MVP)',
                    'en' => 'Enterprise Rapid Monolith Development (Phase 1 MVP)'
                ],
                'description' => [
                    'id' => 'Pengembangan aplikasi bisnis lengkap berbasis Modern Monolith (Laravel 13, Filament v5, PostgreSQL Strict ULID, Redis) di dedicated VPS dengan termin DP 50% via Midtrans.',
                    'en' => 'Complete enterprise web application development powered by Modern Monolith (Laravel 13, Filament v5, PostgreSQL Strict ULID, Redis) on dedicated VPS.'
                ],
                'features' => [
                    'Backend Laravel 13 Monolith Berkecepatan Tinggi',
                    'Panel Admin Filament PHP v5 Enterprise',
                    'Paginasi Keyset O(1) Tanpa Limitasi Skala',
                    'Penguncian Kontrak Digital & Scope Freeze',
                    'Termin Pembayaran DP 50% via Midtrans'
                ],
                'price_idr' => 50000000.00,
                'price_usd' => 3200.00,
                'is_active' => true,
            ]
        );

        $products['cv'] = Product::updateOrCreate(
            ['slug' => 'canva-style-cv-portfolio-studio'],
            [
                'name' => [
                    'id' => 'Canva-Style CV & Portfolio Studio',
                    'en' => 'Canva-Style CV & Portfolio Studio'
                ],
                'description' => [
                    'id' => 'Studio perancangan resume dan portofolio profesional interaktif bergaya visual drag-and-drop dengan ekspor PDF tajam dan tautan publik portofolio.',
                    'en' => 'Interactive visual drag-and-drop resume and portfolio studio with sharp brutalist styling, high-resolution PDF export, and public shareable URLs.'
                ],
                'features' => [
                    'Kanvas WYSIWYG Drag & Drop',
                    'Format ATS-Friendly Internasional',
                    'Ekspor PDF Resolusi Tinggi',
                    'Sinkronisasi Cloud Real-Time'
                ],
                'price_idr' => 450000.00,
                'price_usd' => 30.00,
                'is_active' => true,
            ]
        );

        $products['contract'] = Product::updateOrCreate(
            ['slug' => 'digital-contract-e-sign'],
            [
                'name' => [
                    'id' => 'Digital Contract & Legal E-Signature',
                    'en' => 'Digital Contract & Legal E-Signature'
                ],
                'description' => [
                    'id' => 'Penyusunan surat perjanjian kerja sama resmi dengan tanda tangan digital sah touchscreen/mouse, audit trail IP address, dan enkripsi hash kriptografi SHA-256.',
                    'en' => 'Official agreement preparation with legal touchscreen/mouse digital signature, IP address audit trail, and SHA-256 cryptographic hashing.'
                ],
                'features' => [
                    'Tanda Tangan Digital Touchscreen & Mouse',
                    'Pencatatan Audit Trail IP Address & UTC Timestamp',
                    'Enkripsi Kriptografi SHA-256 Terverifikasi',
                    'Protokol Scope Freeze / Anti-Revisi Liar'
                ],
                'price_idr' => 1500000.00,
                'price_usd' => 95.00,
                'is_active' => true,
            ]
        );

        return $products;
    }

    private function seedVisionBlueprint(): VisionBlueprint
    {
        $blueprint = VisionBlueprint::updateOrCreate(
            ['slug' => 'apex-logistics-global-prd'],
            [
                'client_name' => 'Alexander Wijaya',
                'nama_bisnis' => 'Apex Logistics Global',
                'email' => 'alexander@apexlogistics.co.id',
                'phone' => '+62 812-8899-7711',
                'masalah_utama' => 'Pencatatan manual manifest armada antar 12 cabang menyebabkan rekonsiliasi data lambat (3 hari kerja), selisih muatan barang, dan pembuatan invoice tagihan ke klien sering terlambat.',
                'tujuan_utama' => 'Mewujudkan sistem manifest digital terpusat real-time dengan status pengiriman armada langsung terverifikasi, otomatisasi pembuatan invoice digital, dan pemantauan dasbor harian.',
                'target_audiens' => 'Staf gudang cabang, pengemudi armada logistik, manajer operasional pusat, dan klien korporat B2B.',
                'aktor_sistem' => '1. Superadmin Pusat (Kontrol Penuh & Audit Log), 2. Kepala Cabang (Validasi Masuk/Keluar Barang), 3. Driver/Kurir (Scan Manifest Surat Jalan), 4. Klien B2B (Pantau Resi & Unduh Invoice).',
                'fitur_wajib' => '1. Manajemen Data Armada & Cabang, 2. Manifest Pengiriman Digital dengan Barcode/QR, 3. Dasbor Analitik Operasional Harian Filament, 4. Ekspor Surat Jalan & Rekap Excel/PDF, 5. Pelacakan Status Pengiriman Real-Time.',
                'fitur_tambahan' => '1. Integrasi Notifikasi WhatsApp Gateway Otomatis, 2. Pelacakan GPS Telematika Armada, 3. Pembayaran Invoice via Midtrans, 4. Dasbor Analitik Performa Pengiriman.',
                'alur_kerja' => 'Admin Cabang Input Muatan -> Sistem Terbitkan Nomor Manifest ULID -> Driver Scan Barcode Pengiriman -> Status Berubah "In Transit" -> Penerima TTD Digital di Cabang Tujuan -> Invoice Otomatis Terbit.',
                'kebutuhan_integrasi' => 'Payment Gateway Midtrans, WhatsApp Business Gateway, Cloudflare R2 Cloud Storage.',
                'referensi_desain' => 'Linear.app, Vercel & Stripe Dashboard: Desain presisi monokromatik sharp (rounded-none), kontras tajam, dan font monospaced untuk kode manifest.',
                'kesiapan_aset' => 'Sudah Siap Lengkap',
                'target_waktu' => '30 Hari Kerja',
                'service_options' => ['Web Architecture', 'Rapid Monolith System', 'PostgreSQL ULID', 'Midtrans DP Ready'],
                'project_status' => 'Active Sprint',
                'is_published' => true,
                'ip_address' => '182.253.51.197',
                'signed_agreement' => true,
                'signer_ip' => '182.253.51.197',
                'signer_user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/128.0.0.0 Safari/537.36',
                'document_sha256' => hash('sha256', 'NERIAHPRO-APEX-CONTRACT-2026-SIGNED'),
                'signed_at' => now()->subDays(2),
                'staging_url' => 'https://apex-logistics-global.staging.neriahpro.com',
                'staging_provisioned_at' => now()->subDays(2),
                'user_metadata' => [
                    'source' => 'Midtrans Onboarding Verification Seeder',
                    'browser' => 'Chrome Enterprise',
                    'timestamp' => now()->toIso8601String(),
                ],
            ]
        );

        // Generate full structured PRD content
        $blueprint->generateAndSavePrd();

        return $blueprint;
    }

    private function seedDigitalContract(VisionBlueprint $blueprint): Document
    {
        $clauses = [
            'pasal_1_ruang_lingkup' => [
                'title' => 'Pasal 1: Ruang Lingkup Proyek (Scope Locked)',
                'description' => 'Pihak Kedua (Neriah Pro) sepakat untuk merancang dan membangun arsitektur perangkat lunak untuk Pihak Pertama (Apex Logistics Global) sesuai spesifikasi yang tertuang di dalam Dokumen Ultimate PRD ID: ' . strtoupper(substr($blueprint->id, 0, 10)) . '.',
            ],
            'pasal_2_timeline' => [
                'title' => 'Pasal 2: Alokasi Waktu Pengerjaan (30 Hari Kerja - 5 Sprint)',
                'description' => 'Pekerjaan dilaksanakan selama 30 (tiga puluh) hari kerja aktif yang dibagi menjadi: Sprint 1 (Hari 1-5: Setup Arsitektur & DB), Sprint 2 (Hari 6-18: Core MVP Logic & Dasbor Admin), Sprint 3 (Hari 19-25: User Flow & Integrasi), Sprint 4 (Hari 26-28: Security Audit & UAT), Sprint 5 (Hari 29-30: Deployment VPS & Serah Terima).',
            ],
            'pasal_3_pembayaran_dp' => [
                'title' => 'Pasal 3: Nilai Kontrak & Ketentuan Uang Muka (DP 50%)',
                'description' => 'Total nilai investasi proyek disepakati sebesar Rp 50.000.000 (Lima Puluh Juta Rupiah). Pembayaran dilakukan dalam 2 (dua) termin: Termin 1 Uang Muka (DP 50%) sebesar Rp 25.000.000 melalui payment gateway Midtrans sebelum pekerjaan dimulai, dan Termin 2 Pelunasan (50%) sebesar Rp 25.000.000 saat migrasi ke VPS produksi.',
            ],
            'pasal_4_scope_lock_cr' => [
                'title' => 'Pasal 4: Penguncian Ruang Lingkup & Addendum',
                'description' => 'Seluruh fitur di luar daftar MVP Fase 1 dinyatakan sebagai lingkup baru (Change Request) yang wajib dituangkan dalam Addendum terpisah dengan estimasi biaya dan waktu tersendiri tanpa mengubah tanggal jatuh tempo kontrak induk.',
            ],
            'pasal_5_tanda_tangan_elektronik' => [
                'title' => 'Pasal 5: Tanda Tangan Elektronik & Integritas Dokumen (SHA-256)',
                'description' => 'Surat perjanjian ini berkekuatan hukum tetap, ditandatangani secara digital dengan pencatatan audit trail IP Address 182.253.51.197, UTC timestamp, dan enkripsi verifikasi hash SHA-256.',
            ],
        ];

        return Document::updateOrCreate(
            ['title' => 'Perjanjian Kerja Sama Pengembangan Sistem - Apex Logistics Global'],
            [
                'document_type' => 'contract',
                'related_type' => VisionBlueprint::class,
                'related_id' => $blueprint->id,
                'status' => 'signed',
                'scope_locked' => true,
                'contract_amount' => 50000000.00,
                'dp_amount' => 25000000.00,
                'midtrans_order_id' => 'NPRO-DP-APEX-001',
                'midtrans_payment_url' => null,
                'signer_name' => 'Alexander Wijaya',
                'signer_email' => 'alexander@apexlogistics.co.id',
                'signer_ip_address' => '182.253.51.197',
                'signed_at' => now()->subDays(2),
                'document_hash' => hash('sha256', 'NERIAHPRO-APEX-CONTRACT-2026-SIGNED'),
                'digital_signature_image' => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="300" height="100" viewBox="0 0 300 100"><path d="M 20 60 Q 60 10, 100 50 T 180 40 T 260 70" fill="none" stroke="#0044cc" stroke-width="3" stroke-linecap="round"/><text x="20" y="90" font-family="monospace" font-size="12" fill="#666">Verified by Neriah Pro E-Sign</text></svg>'),
                'content_clauses' => $clauses,
            ]
        );
    }

    private function seedTransaction(Product $product, Document $contract): void
    {
        $reviewerUser = User::where('email', 'reviewer.midtrans@neriahpro.com')->first() 
            ?? User::where('email', 'yoseph.iriandi.tambunan@gmail.com')->first();

        Transaction::updateOrCreate(
            ['midtrans_order_id' => 'NPRO-DP-APEX-001'],
            [
                'user_id' => $reviewerUser?->id,
                'product_id' => $product->id,
                'midtrans_transaction_id' => 'TRX-MIDTRANS-' . strtoupper(Str::random(10)),
                'status' => 'settlement', // LUNAS (Settled by Midtrans)
                'total_idr' => 25000000.00,
                'original_currency' => 'IDR',
                'original_amount' => 25000000.00,
                'exchange_rate' => 1.0000,
                'customer_details' => [
                    'first_name' => 'Alexander',
                    'last_name' => 'Wijaya',
                    'email' => 'alexander@apexlogistics.co.id',
                    'phone' => '+62 812-8899-7711',
                    'company' => 'PT Apex Logistics Global',
                    'notes' => 'Pembayaran Uang Muka (DP 50%) Kontrak Pengembangan Sistem Apex Logistics Global',
                    'payment_type' => 'bank_transfer',
                    'bank' => 'bca',
                    'settled_at' => now()->subDays(2)->toIso8601String(),
                ],
            ]
        );
    }

    private function seedLeadContacts(): void
    {
        LeadContact::updateOrCreate(
            ['email' => 'alexander@apexlogistics.co.id'],
            [
                'name' => 'Alexander Wijaya',
                'company_name' => 'PT Apex Logistics Global',
                'job_title' => 'VP of Logistics Operations',
                'phone' => '+62 812-8899-7711',
                'status' => 'client',
                'metadata' => [
                    'inquiry' => 'Kebutuhan sistem manifest digital terpusat real-time dengan status pengiriman armada, invoice digital otomatis, dan gateway pembayaran Midtrans.',
                    'budget_range' => 'Rp 50.000.000 - Rp 100.000.000',
                    'service_interest' => 'Enterprise Rapid Monolith Development & Architecture PRD',
                    'lead_source' => 'Website Onboarding Intake Form',
                    'onboarded_at' => now()->subDays(5)->toIso8601String(),
                ],
            ]
        );
    }

    private function seedUnpaidBlueprintAndContract(): array
    {
        $blueprint = VisionBlueprint::updateOrCreate(
            ['slug' => 'medika-prima-telehealth-prd'],
            [
                'client_name' => 'dr. Hendra Pratama, Sp.A',
                'nama_bisnis' => 'Medika Prima Telehealth',
                'email' => 'dr.hendra@medikaprima.id',
                'phone' => '+62 811-2345-6789',
                'masalah_utama' => 'Pencatatan rekam medis dan reservasi konsultasi telemedisin dokter spesialis masih manual via spreadsheet terpisah, menyebabkan tumpang tindih jadwal praktek dan pembukuan pembayaran pasien sering tidak akurat.',
                'tujuan_utama' => 'Membangun platform portal konsultasi dokter online terenkripsi dengan integrasi rekam medis digital, pembayaran DP tindakan medis via Midtrans, dan resep digital otomatis.',
                'target_audiens' => 'Pasien rawat jalan, dokter spesialis, staf administrasi klinik, dan apoteker mitra.',
                'aktor_sistem' => '1. Superadmin Klinik (Manajemen Tarif & Dokter), 2. Dokter Spesialis (Telekonsultasi & E-Resep), 3. Pasien (Booking Jadwal & Pembayaran DP), 4. Apoteker (Verifikasi Obat).',
                'fitur_wajib' => '1. Portal Booking Dokter & Kalender Jadwal, 2. Konsultasi Chat/Video Telemedisin, 3. Pembayaran Uang Muka (DP) & Pelunasan via Midtrans, 4. Rekam Medis Elektronik (RME) Standar Kemenkes SATUSEHAT.',
                'fitur_tambahan' => '1. Notifikasi Pengingat Jadwal WhatsApp Gateway, 2. Modul Resep Obat Digital & Pengiriman Kurir.',
                'alur_kerja' => 'Pasien Pilih Dokter -> Sistem Terbitkan Kode Booking -> Pasien Bayar DP 50% via Midtrans Snap -> Jadwal Terkonfirmasi -> Sesi Konsultasi Berlangsung -> Resep Terbit.',
                'kebutuhan_integrasi' => 'Payment Gateway Midtrans Snap, WhatsApp Business API, SATUSEHAT Kemenkes API.',
                'referensi_desain' => 'Halodoc, Alodokter & Linear.app: Clean clinical interface, high contrast, accessibility compliant.',
                'kesiapan_aset' => 'Sudah Siap Lengkap',
                'target_waktu' => '14 Hari Kerja',
                'service_options' => ['Web Architecture', 'Rapid Monolith System', 'PostgreSQL ULID', 'Midtrans DP Ready', 'Gemini Ultra Swarm'],
                'project_status' => 'Awaiting DP Payment',
                'is_published' => true,
                'ip_address' => '180.252.120.44',
                'signed_agreement' => true,
                'signer_ip' => '180.252.120.44',
                'signer_user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/128.0.0.0 Safari/537.36',
                'document_sha256' => hash('sha256', 'NERIAHPRO-MEDIKA-CONTRACT-2026-PENDING-DP'),
                'signed_at' => now()->subHours(6),
                'staging_url' => null,
                'user_metadata' => [
                    'source' => 'Midtrans Reviewer Demonstration (Unpaid DP Sample)',
                    'note' => 'Contoh proyek siap bayar DP 50% untuk pengujian Midtrans Snap Popup',
                    'selected_velocity_tier' => 'fast_track',
                ],
            ]
        );

        $clauses = [
            'pasal_1_ruang_lingkup' => [
                'title' => 'Pasal 1: Ruang Lingkup Proyek (Scope Locked)',
                'description' => 'Pihak Kedua (Neriah Pro) sepakat untuk merancang dan membangun arsitektur perangkat lunak Medika Prima Telehealth sesuai spesifikasi yang tertuang di dalam Dokumen Ultimate PRD ID: ' . strtoupper(substr($blueprint->id, 0, 10)) . '.',
            ],
            'pasal_2_timeline' => [
                'title' => 'Pasal 2: Alokasi Waktu Pengerjaan (14 Hari Kerja - Gemini Ultra Swarm Parallel Sprint)',
                'description' => 'Pekerjaan dilaksanakan selama 14 (empat belas) hari kerja aktif dengan akselerasi Gemini Ultra Swarm Parallel Sprint terstruktur.',
            ],
            'pasal_3_pembayaran_dp' => [
                'title' => 'Pasal 3: Nilai Kontrak & Ketentuan Uang Muka (DP 50%)',
                'description' => 'Total nilai investasi proyek disepakati sebesar Rp 40.000.000 (Empat Puluh Juta Rupiah). Pembayaran dilakukan dalam 2 (dua) termin: Termin 1 Uang Muka (DP 50%) sebesar Rp 20.000.000 melalui payment gateway Midtrans sebelum pengerjaan sprint dimulai, dan Termin 2 Pelunasan (50%) sebesar Rp 20.000.000 saat serah terima sistem.',
            ],
            'pasal_4_scope_lock_cr' => [
                'title' => 'Pasal 4: Penguncian Ruang Lingkup & Addendum',
                'description' => 'Seluruh fitur di luar daftar MVP Fase 1 dinyatakan sebagai Change Request (CR) terpisah.',
            ],
            'pasal_5_tanda_tangan_elektronik' => [
                'title' => 'Pasal 5: Tanda Tangan Elektronik & Integritas Dokumen (SHA-256)',
                'description' => 'Dokumen ini telah disetujui dan ditandatangani secara digital oleh Klien (dr. Hendra Pratama, Sp.A) dan menunggu penyelesaian pembayaran Uang Muka (DP) 50% via Midtrans.',
            ],
        ];

        $contract = Document::updateOrCreate(
            ['title' => 'Perjanjian Kerja Sama Pengembangan Sistem - Medika Prima Telehealth'],
            [
                'document_type' => 'contract',
                'related_type' => VisionBlueprint::class,
                'related_id' => $blueprint->id,
                'status' => 'signed',
                'scope_locked' => true,
                'contract_amount' => 40000000.00,
                'dp_amount' => 20000000.00,
                'midtrans_order_id' => 'NPRO-DP-MEDIKA-002',
                'midtrans_payment_url' => null,
                'signer_name' => 'dr. Hendra Pratama, Sp.A',
                'signer_email' => 'dr.hendra@medikaprima.id',
                'signer_ip_address' => '180.252.120.44',
                'signed_at' => now()->subHours(6),
                'document_hash' => hash('sha256', 'NERIAHPRO-MEDIKA-CONTRACT-2026-PENDING-DP'),
                'digital_signature_image' => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="300" height="100" viewBox="0 0 300 100"><path d="M 30 70 Q 70 20, 110 60 T 190 30 T 270 80" fill="none" stroke="#059669" stroke-width="3" stroke-linecap="round"/><text x="30" y="90" font-family="monospace" font-size="12" fill="#059669">Signed by dr. Hendra Pratama</text></svg>'),
                'content_clauses' => $clauses,
            ]
        );

        // Regenerate PRD after contract creation so itemized calculation anchors to the signed contract
        $blueprint->generateAndSavePrd();
        $blueprint->refresh();

        $reviewerUser = User::where('email', 'reviewer.midtrans@neriahpro.com')->first();
        $product = Product::where('slug', 'rapid-mvp-monolith-system')->first() ?? Product::first();

        if ($product) {
            Transaction::updateOrCreate(
                ['midtrans_order_id' => 'NPRO-DP-MEDIKA-002'],
                [
                    'user_id' => $reviewerUser?->id,
                    'product_id' => $product->id,
                    'midtrans_transaction_id' => null,
                    'status' => 'pending',
                    'total_idr' => 20000000.00,
                    'original_currency' => 'IDR',
                    'original_amount' => 20000000.00,
                    'exchange_rate' => 1.0000,
                    'customer_details' => [
                        'first_name' => 'dr. Hendra',
                        'last_name' => 'Pratama',
                        'email' => 'dr.hendra@medikaprima.id',
                        'phone' => '+62 811-2345-6789',
                        'company' => 'Medika Prima Telehealth',
                        'notes' => 'Menunggu Pembayaran Uang Muka (DP 50%) Kontrak Medika Prima Telehealth via Midtrans Snap',
                    ],
                ]
            );
        }

        return [$blueprint, $contract];
    }
}
