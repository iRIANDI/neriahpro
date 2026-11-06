<?php

namespace App\Services\Prd;

use App\Models\VisionBlueprint;
use App\Services\PrdGeneratorService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PrdErdSchemaService
{
    /**
     * Generate Tailored ERD Database Schema (ULID Primary Keys & PostgreSQL strict standards).
     * Integrates Full LLM Reasoning (DeepSeek-R1 / Claude 3.7 / Gemini 2.5 / OpenAI) with automatic failover
     * to synthesize 100% domain-specific relational entities from client document/corpus,
     * while retaining an instantaneous deterministic heuristic fallback.
     */
    public static function generateErdSchema(
        string $businessName, 
        array $mvpItems, 
        array $actorItems, 
        ?VisionBlueprint $blueprint = null, 
        ?string $preferredAiProvider = null,
        array $universalSchema = []
    ): array
    {
        $primitiveErdTables = self::convertUniversalDictionaryToErdTables($universalSchema['data_dictionary'] ?? []);

        // 1. Attempt Dynamic LLM Reasoning if Blueprint is provided, merged with Mandatory Composable Primitive Tables
        if ($blueprint) {
            try {
                $llmTables = self::synthesizeErdSchemaWithAi($businessName, $mvpItems, $actorItems, $blueprint, $preferredAiProvider);
                if (!empty($llmTables) && is_array($llmTables) && count($llmTables) >= 2) {
                    return self::mergeErdTablesUnique($llmTables, $primitiveErdTables);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::info("AI ERD synthesis fallback to deterministic heuristics: " . $e->getMessage());
            }
        }

        // 2. Deterministic Heuristic Fallback Engine
        $domainSlug = Str::slug($businessName, '_');
        $domainSlug = preg_replace('/[^a-zA-Z0-9_]/', '_', $domainSlug);
        $domainSlug = trim($domainSlug, '_');
        if (empty($domainSlug)) {
            $domainSlug = 'domain_records';
        }

        $baseTables = [
            [
                'name' => 'users',
                'description' => 'Menyimpan kredensial otentikasi semua aktor sistem (Admin, Staff, Klien).',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'Unique Lexicographically Sortable ID', 'label' => ['id' => 'ID Pengguna (ULID)', 'en' => 'User ID (ULID)']],
                    ['name' => 'name', 'type' => 'string(255)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Nama lengkap pengguna', 'label' => ['id' => 'Nama Lengkap', 'en' => 'Full Name']],
                    ['name' => 'email', 'type' => 'string(255)', 'index' => 'UNIQUE', 'nullable' => false, 'notes' => 'Email unik untuk login', 'label' => ['id' => 'Alamat Email', 'en' => 'Email Address']],
                    ['name' => 'password', 'type' => 'string(255)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Hashed Argon2id / Bcrypt', 'label' => ['id' => 'Kata Sandi Enkripsi', 'en' => 'Hashed Password']],
                    ['name' => 'role', 'type' => 'string(50)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'superadmin | staff | client', 'label' => ['id' => 'Peran Hak Akses', 'en' => 'Access Role']],
                    ['name' => 'is_active', 'type' => 'boolean', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Status aktif akun', 'label' => ['id' => 'Status Aktif', 'en' => 'Active Status']],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Waktu pembuatan akun', 'label' => ['id' => 'Waktu Dibuat', 'en' => 'Created At']],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'index' => 'NONE', 'nullable' => true, 'notes' => 'Waktu modifikasi', 'label' => ['id' => 'Waktu Diperbarui', 'en' => 'Updated At']],
                ],
            ],
            [
                'name' => $domainSlug,
                'description' => 'Entitas data bisnis utama yang mengelola transaksi / formulir input spesifik proyek.',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'ULID primary key', 'label' => ['id' => 'ID Entitas (ULID)', 'en' => 'Entity ID (ULID)']],
                    ['name' => 'user_id', 'type' => 'foreignUlid', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Relasi ke tabel users.id', 'label' => ['id' => 'ID Pengguna Terkait', 'en' => 'Related User ID']],
                    ['name' => 'code_reference', 'type' => 'string(50)', 'index' => 'UNIQUE', 'nullable' => false, 'notes' => 'Nomor referensi / resi otomatis', 'label' => ['id' => 'Kode Referensi / Resi', 'en' => 'Reference Code']],
                    ['name' => 'title', 'type' => 'jsonb', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Judul / Nama entitas data dwibahasa (ID/EN)', 'label' => ['id' => 'Nama / Judul Item (JSONB)', 'en' => 'Title / Item Name (JSONB)']],
                    ['name' => 'data_payload', 'type' => 'jsonb', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Payload dinamis format JSON valid', 'label' => ['id' => 'Payload Dinamis (JSONB)', 'en' => 'Dynamic Payload (JSONB)']],
                    ['name' => 'status', 'type' => 'string(50)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'draft | pending | approved | completed', 'label' => ['id' => 'Status Alur Kerja', 'en' => 'Workflow Status']],
                    ['name' => 'verified_at', 'type' => 'timestamp', 'index' => 'NONE', 'nullable' => true, 'notes' => 'Waktu verifikasi approval', 'label' => ['id' => 'Waktu Verifikasi', 'en' => 'Verified At']],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Keyset cursor pointer', 'label' => ['id' => 'Waktu Dibuat', 'en' => 'Created At']],
                ],
            ],
            [
                'name' => 'activity_logs',
                'description' => 'Pencatatan riwayat audit (audit trail) untuk keamanan dan akuntabilitas sistem.',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'ULID audit record', 'label' => ['id' => 'ID Audit (ULID)', 'en' => 'Audit ID (ULID)']],
                    ['name' => 'user_id', 'type' => 'foreignUlid', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Aktor yang melakukan aksi', 'label' => ['id' => 'Aktor Pengguna', 'en' => 'Actor User ID']],
                    ['name' => 'action', 'type' => 'string(100)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'create | update | delete | approve', 'label' => ['id' => 'Jenis Aksi Sistem', 'en' => 'System Action']],
                    ['name' => 'target_table', 'type' => 'string(100)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Nama tabel sasaran', 'label' => ['id' => 'Tabel Sasaran', 'en' => 'Target Table']],
                    ['name' => 'changes', 'type' => 'jsonb', 'index' => 'NONE', 'nullable' => true, 'notes' => 'Snapshot diff data sebelum & sesudah', 'label' => ['id' => 'Perubahan Diff (JSONB)', 'en' => 'Changes Diff (JSONB)']],
                    ['name' => 'ip_address', 'type' => 'string(45)', 'index' => 'NONE', 'nullable' => true, 'notes' => 'Alamat IP pengguna', 'label' => ['id' => 'Alamat IP Klien', 'en' => 'Client IP Address']],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Timestamp kejadian', 'label' => ['id' => 'Waktu Kejadian', 'en' => 'Timestamp']],
                ],
            ],
            [
                'name' => 'system_notifications',
                'description' => 'Log antrean notifikasi (Email / WhatsApp) untuk broadcast & status alert.',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'ULID notification identifier', 'label' => ['id' => 'ID Notifikasi (ULID)', 'en' => 'Notification ID (ULID)']],
                    ['name' => 'recipient', 'type' => 'string(255)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Email atau Nomor WhatsApp', 'label' => ['id' => 'Penerima Pesan', 'en' => 'Recipient Address']],
                    ['name' => 'channel', 'type' => 'string(20)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'email | whatsapp | system_push', 'label' => ['id' => 'Kanal Pengiriman', 'en' => 'Delivery Channel']],
                    ['name' => 'message_body', 'type' => 'jsonb', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Isi konten notifikasi dwibahasa (ID/EN)', 'label' => ['id' => 'Isi Konten Pesan (JSONB)', 'en' => 'Message Content (JSONB)']],
                    ['name' => 'status', 'type' => 'string(30)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'queued | sent | failed', 'label' => ['id' => 'Status Pengiriman', 'en' => 'Delivery Status']],
                    ['name' => 'sent_at', 'type' => 'timestamp', 'index' => 'NONE', 'nullable' => true, 'notes' => 'Waktu terkirim sukses', 'label' => ['id' => 'Waktu Terkirim', 'en' => 'Sent At']],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Waktu pemicu notifikasi', 'label' => ['id' => 'Waktu Antrean', 'en' => 'Queued At']],
                ],
            ],
        ];

        // Synthesize specialized domain entities from business name and MVP feature items
        $combinedText = strtolower($businessName . ' ' . implode(' ', array_map(function($item) {
            return ($item['title'] ?? '') . ' ' . ($item['desc'] ?? '');
        }, $mvpItems)));

        // Domain 1: Education / School / Akademik (e.g. Sekolah Advent, Bimbel, Kursus)
        if (str_contains($combinedText, 'sekolah') || str_contains($combinedText, 'siswa') || str_contains($combinedText, 'guru') || str_contains($combinedText, 'ppdb') || str_contains($combinedText, 'spp') || str_contains($combinedText, 'akademik') || str_contains($combinedText, 'rapor')) {
            $baseTables[] = [
                'name' => 'students',
                'description' => 'Master data induk siswa, NISN, kelas, dan kontak wali murid.',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'ULID primary key', 'label' => ['id' => 'ID Siswa (ULID)', 'en' => 'Student ID (ULID)']],
                    ['name' => 'user_id', 'type' => 'foreignUlid', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Relasi ke users.id', 'label' => ['id' => 'ID Akun Terkait', 'en' => 'Related User ID']],
                    ['name' => 'nisn', 'type' => 'string(20)', 'index' => 'UNIQUE', 'nullable' => false, 'notes' => 'Nomor Induk Siswa Nasional', 'label' => ['id' => 'NISN Siswa', 'en' => 'Student NISN']],
                    ['name' => 'full_name', 'type' => 'string(255)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Nama lengkap siswa', 'label' => ['id' => 'Nama Siswa', 'en' => 'Student Full Name']],
                    ['name' => 'class_grade', 'type' => 'string(50)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Tingkat kelas aktif', 'label' => ['id' => 'Kelas Siswa', 'en' => 'Class Grade']],
                    ['name' => 'parent_phone', 'type' => 'string(20)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Nomor WhatsApp wali untuk notifikasi', 'label' => ['id' => 'WhatsApp Orang Tua', 'en' => 'Parent WhatsApp']],
                    ['name' => 'status', 'type' => 'string(30)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'active | graduated | transferred', 'label' => ['id' => 'Status Akademik', 'en' => 'Academic Status']],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Waktu pendaftaran', 'label' => ['id' => 'Waktu Dibuat', 'en' => 'Created At']],
                ],
            ];

            $baseTables[] = [
                'name' => 'tuition_invoices',
                'description' => 'Tagihan SPP dan administrasi sekolah dengan integrasi VA & QRIS.',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'ULID primary key', 'label' => ['id' => 'ID Tagihan (ULID)', 'en' => 'Invoice ID (ULID)']],
                    ['name' => 'student_id', 'type' => 'foreignUlid', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Relasi ke students.id', 'label' => ['id' => 'ID Siswa', 'en' => 'Student ID']],
                    ['name' => 'invoice_number', 'type' => 'string(50)', 'index' => 'UNIQUE', 'nullable' => false, 'notes' => 'Nomor faktur unik', 'label' => ['id' => 'Nomor Tagihan', 'en' => 'Invoice Number']],
                    ['name' => 'billing_period', 'type' => 'string(50)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Bulan tagihan (e.g. 2026-10)', 'label' => ['id' => 'Periode SPP', 'en' => 'Billing Period']],
                    ['name' => 'amount', 'type' => 'decimal(15,2)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Nominal tagihan dalam IDR', 'label' => ['id' => 'Nominal Tagihan', 'en' => 'Invoice Amount']],
                    ['name' => 'payment_channel', 'type' => 'string(50)', 'index' => 'NONE', 'nullable' => true, 'notes' => 'qris | bca_va | mandiri_va | cash', 'label' => ['id' => 'Metode Bayar', 'en' => 'Payment Channel']],
                    ['name' => 'status', 'type' => 'string(30)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'unpaid | settlement | expired', 'label' => ['id' => 'Status Pembayaran', 'en' => 'Payment Status']],
                    ['name' => 'paid_at', 'type' => 'timestamp', 'index' => 'NONE', 'nullable' => true, 'notes' => 'Waktu pelunasan terkonfirmasi', 'label' => ['id' => 'Waktu Pelunasan', 'en' => 'Paid At']],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Waktu penerbitan faktur', 'label' => ['id' => 'Waktu Terbit', 'en' => 'Issued At']],
                ],
            ];

            $baseTables[] = [
                'name' => 'academic_evaluations',
                'description' => 'Rekam nilai dan e-rapor Kurikulum Merdeka (ulangan harian, UTS, UAS).',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'ULID primary key', 'label' => ['id' => 'ID Evaluasi (ULID)', 'en' => 'Evaluation ID (ULID)']],
                    ['name' => 'student_id', 'type' => 'foreignUlid', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Relasi ke students.id', 'label' => ['id' => 'ID Siswa', 'en' => 'Student ID']],
                    ['name' => 'subject_name', 'type' => 'string(100)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Nama mata pelajaran', 'label' => ['id' => 'Mata Pelajaran', 'en' => 'Subject Name']],
                    ['name' => 'assessment_type', 'type' => 'string(50)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'formative | summative | uts | uas', 'label' => ['id' => 'Jenis Asesmen', 'en' => 'Assessment Type']],
                    ['name' => 'score', 'type' => 'decimal(5,2)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Skor angka 0.00 - 100.00', 'label' => ['id' => 'Nilai Skor', 'en' => 'Score']],
                    ['name' => 'competency_note', 'type' => 'text', 'index' => 'NONE', 'nullable' => true, 'notes' => 'Catatan capaian kompetensi', 'label' => ['id' => 'Capaian Kompetensi', 'en' => 'Competency Note']],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Waktu input nilai', 'label' => ['id' => 'Waktu Input', 'en' => 'Created At']],
                ],
            ];
        }

        // Domain 2: Tourism, Travel, Airport Transfers & Smart Itinerary (e.g. PT Gayabalical, Bali Tours, Overland Jawa Timur)
        if (str_contains($combinedText, 'tour') || str_contains($combinedText, 'travel') || str_contains($combinedText, 'wisata') || 
            str_contains($combinedText, 'gayabalical') || str_contains($combinedText, 'itinerary') || str_contains($combinedText, 'ngurah rai') ||
            str_contains($combinedText, 'overland') || str_contains($combinedText, 'transfer in') || str_contains($combinedText, 'transfer out') ||
            str_contains($combinedText, 'armada') || str_contains($combinedText, 'kendaraan') || str_contains($combinedText, 'bromo') || str_contains($combinedText, 'ijen')) {
            
            $baseTables[] = [
                'name' => 'tour_packages',
                'description' => 'Katalog master paket tour Bali dan overland Jawa Timur (Bromo/Ijen) dengan rute dan batas kapasitas.',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'ULID primary key', 'label' => ['id' => 'ID Paket Tour (ULID)', 'en' => 'Tour Package ID (ULID)']],
                    ['name' => 'package_code', 'type' => 'string(50)', 'index' => 'UNIQUE', 'nullable' => false, 'notes' => 'Kode paket unik (e.g. PKG-BALI-01)', 'label' => ['id' => 'Kode Paket', 'en' => 'Package Code']],
                    ['name' => 'name', 'type' => 'jsonb', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Nama paket wisata dwibahasa (ID/EN)', 'label' => ['id' => 'Nama Paket Wisata (JSONB)', 'en' => 'Package Name (JSONB)']],
                    ['name' => 'destination_area', 'type' => 'string(100)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Bali Selatan | Ubud | Bedugul | Overland Bromo-Ijen', 'label' => ['id' => 'Wilayah Destinasi', 'en' => 'Destination Area']],
                    ['name' => 'duration_type', 'type' => 'string(50)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'half_day | full_day | overland_multi_day', 'label' => ['id' => 'Durasi Perjalanan', 'en' => 'Trip Duration']],
                    ['name' => 'base_price_idr', 'type' => 'decimal(15,2)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Tarif dasar paket wisata', 'label' => ['id' => 'Tarif Dasar (IDR)', 'en' => 'Base Price (IDR)']],
                    ['name' => 'included_features', 'type' => 'jsonb', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Daftar fasilitas include/exclude format JSON', 'label' => ['id' => 'Fasilitas Termasuk', 'en' => 'Included Features']],
                    ['name' => 'is_active', 'type' => 'boolean', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Status aktif paket', 'label' => ['id' => 'Status Aktif', 'en' => 'Active Status']],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Waktu pendaftaran paket', 'label' => ['id' => 'Waktu Dibuat', 'en' => 'Created At']],
                ],
            ];

            $baseTables[] = [
                'name' => 'vehicles',
                'description' => 'Master armada transportasi (Avanza, Innova Reborn, HiAce, Alphard) dengan kapasitas bagasi dan penumpang.',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'ULID primary key', 'label' => ['id' => 'ID Kendaraan (ULID)', 'en' => 'Vehicle ID (ULID)']],
                    ['name' => 'vehicle_name', 'type' => 'string(150)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Nama tipe kendaraan', 'label' => ['id' => 'Nama Kendaraan', 'en' => 'Vehicle Name']],
                    ['name' => 'vehicle_type', 'type' => 'string(50)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'mpv | standard_van | luxury_van | suv', 'label' => ['id' => 'Tipe Kendaraan', 'en' => 'Vehicle Type']],
                    ['name' => 'passenger_capacity', 'type' => 'int', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Kapasitas maksimal orang', 'label' => ['id' => 'Kapasitas Orang', 'en' => 'Passenger Capacity']],
                    ['name' => 'luggage_capacity', 'type' => 'int', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Kapasitas maksimal koper bagasi', 'label' => ['id' => 'Kapasitas Koper', 'en' => 'Luggage Capacity']],
                    ['name' => 'plate_number', 'type' => 'string(20)', 'index' => 'UNIQUE', 'nullable' => false, 'notes' => 'Nomor polisi resmi kendaraan', 'label' => ['id' => 'Nomor Polisi (Plat)', 'en' => 'Plate Number']],
                    ['name' => 'status', 'type' => 'string(30)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'ready | on_trip | maintenance', 'label' => ['id' => 'Status Operasional', 'en' => 'Fleet Status']],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Waktu pendaftaran armada', 'label' => ['id' => 'Waktu Dibuat', 'en' => 'Created At']],
                ],
            ];

            $baseTables[] = [
                'name' => 'bookings',
                'description' => 'Transaksi reservasi transfer bandara Ngurah Rai, day tour Bali, dan overland Jawa Timur.',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'ULID primary key', 'label' => ['id' => 'ID Reservasi (ULID)', 'en' => 'Booking ID (ULID)']],
                    ['name' => 'user_id', 'type' => 'foreignUlid', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Relasi ke users.id (Klien Pemesan)', 'label' => ['id' => 'ID Pemesan', 'en' => 'User ID']],
                    ['name' => 'tour_package_id', 'type' => 'foreignUlid', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Relasi ke tour_packages.id', 'label' => ['id' => 'ID Paket Tour', 'en' => 'Tour Package ID']],
                    ['name' => 'vehicle_id', 'type' => 'foreignUlid', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Relasi ke vehicles.id', 'label' => ['id' => 'ID Armada', 'en' => 'Vehicle ID']],
                    ['name' => 'booking_code', 'type' => 'string(50)', 'index' => 'UNIQUE', 'nullable' => false, 'notes' => 'Kode unik reservasi', 'label' => ['id' => 'Kode Reservasi', 'en' => 'Booking Code']],
                    ['name' => 'service_category', 'type' => 'string(50)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'airport_transfer_in | airport_transfer_out | day_tour_bali | overland_east_java', 'label' => ['id' => 'Kategori Layanan', 'en' => 'Service Category']],
                    ['name' => 'pickup_datetime', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Waktu penjemputan terjadwal', 'label' => ['id' => 'Waktu Jemput', 'en' => 'Pickup Datetime']],
                    ['name' => 'pickup_location', 'type' => 'string(255)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Titik jemput (Bandara / Hotel)', 'label' => ['id' => 'Lokasi Penjemputan', 'en' => 'Pickup Location']],
                    ['name' => 'flight_number', 'type' => 'string(30)', 'index' => 'NONE', 'nullable' => true, 'notes' => 'Nomor penerbangan jika transfer bandara', 'label' => ['id' => 'Nomor Penerbangan', 'en' => 'Flight Number']],
                    ['name' => 'passenger_count', 'type' => 'int', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Jumlah penumpang', 'label' => ['id' => 'Jumlah Orang', 'en' => 'Passenger Count']],
                    ['name' => 'total_price_idr', 'type' => 'decimal(15,2)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Total biaya keseluruhan', 'label' => ['id' => 'Total Biaya (IDR)', 'en' => 'Total Price (IDR)']],
                    ['name' => 'dp_amount_idr', 'type' => 'decimal(15,2)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Nominal DP 50% untuk lock armada', 'label' => ['id' => 'Uang Muka DP 50%', 'en' => 'Down Payment (50%)']],
                    ['name' => 'status', 'type' => 'string(50)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'pending_dp | dp_confirmed | driver_assigned | completed | cancelled', 'label' => ['id' => 'Status Reservasi', 'en' => 'Booking Status']],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Waktu reservasi dibuat', 'label' => ['id' => 'Waktu Dibuat', 'en' => 'Created At']],
                ],
            ];

            $baseTables[] = [
                'name' => 'itinerary_wishlists',
                'description' => 'Wish list kustom klien yang dianalisis oleh AI tanpa asumsi untuk menghasilkan rute & harga nyata dari database.',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'ULID primary key', 'label' => ['id' => 'ID Wishlist (ULID)', 'en' => 'Wishlist ID (ULID)']],
                    ['name' => 'user_id', 'type' => 'foreignUlid', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Relasi ke users.id (Pembuat Wishlist)', 'label' => ['id' => 'ID Pengguna', 'en' => 'User ID']],
                    ['name' => 'booking_id', 'type' => 'foreignUlid', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Relasi ke bookings.id saat dikonversi', 'label' => ['id' => 'ID Booking Terkait', 'en' => 'Related Booking ID']],
                    ['name' => 'wishlist_code', 'type' => 'string(50)', 'index' => 'UNIQUE', 'nullable' => false, 'notes' => 'Kode referensi analisis AI', 'label' => ['id' => 'Kode Wishlist', 'en' => 'Wishlist Code']],
                    ['name' => 'raw_destinations_input', 'type' => 'text', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Daftar destinasi mentah dari klien', 'label' => ['id' => 'Destinasi Impian Klien', 'en' => 'Raw Wishlist Input']],
                    ['name' => 'ai_analyzed_itinerary', 'type' => 'jsonb', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Itinerary tervalidasi hasil analisis AI', 'label' => ['id' => 'Itinerary Analisis AI', 'en' => 'AI Synthesized Itinerary']],
                    ['name' => 'calculated_price_idr', 'type' => 'decimal(15,2)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Kalkulasi harga matematis dari database', 'label' => ['id' => 'Kalkulasi Tarif (IDR)', 'en' => 'Calculated Price (IDR)']],
                    ['name' => 'status', 'type' => 'string(30)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'draft | analyzed | converted', 'label' => ['id' => 'Status Wishlist', 'en' => 'Wishlist Status']],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Waktu pembuatan wish list', 'label' => ['id' => 'Waktu Dibuat', 'en' => 'Created At']],
                ],
            ];

            $baseTables[] = [
                'name' => 'payments',
                'description' => 'Catatan transaksi pembayaran termin DP 50% dan pelunasan melalui Midtrans Snap QRIS / Virtual Account.',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'ULID primary key', 'label' => ['id' => 'ID Pembayaran (ULID)', 'en' => 'Payment ID (ULID)']],
                    ['name' => 'booking_id', 'type' => 'foreignUlid', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Relasi ke bookings.id', 'label' => ['id' => 'ID Reservasi Terkait', 'en' => 'Related Booking ID']],
                    ['name' => 'transaction_ref', 'type' => 'string(100)', 'index' => 'UNIQUE', 'nullable' => false, 'notes' => 'Order ID Midtrans / Gateway', 'label' => ['id' => 'Nomor Referensi Transaksi', 'en' => 'Transaction Reference']],
                    ['name' => 'payment_stage', 'type' => 'string(30)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'dp_50 | pelunasan_100', 'label' => ['id' => 'Tahap Termin Pembayaran', 'en' => 'Payment Stage']],
                    ['name' => 'payment_channel', 'type' => 'string(50)', 'index' => 'NONE', 'nullable' => true, 'notes' => 'qris | bca_va | bni_va | credit_card', 'label' => ['id' => 'Metode Pembayaran', 'en' => 'Payment Channel']],
                    ['name' => 'gross_amount_idr', 'type' => 'decimal(15,2)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Nominal transaksi dalam Rupiah', 'label' => ['id' => 'Nominal Transaksi (IDR)', 'en' => 'Gross Amount (IDR)']],
                    ['name' => 'status', 'type' => 'string(30)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'pending | settlement | expire | cancel', 'label' => ['id' => 'Status Transaksi Gateway', 'en' => 'Transaction Status']],
                    ['name' => 'settled_at', 'type' => 'timestamp', 'index' => 'NONE', 'nullable' => true, 'notes' => 'Waktu pelunasan sukses', 'label' => ['id' => 'Waktu Pelunasan', 'en' => 'Settled At']],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Waktu penerbitan tagihan', 'label' => ['id' => 'Waktu Dibuat', 'en' => 'Created At']],
                ],
            ];
        }

        return self::mergeErdTablesUnique($baseTables, $primitiveErdTables);
    }


    /**
     * Convert Universal Blueprint Schema v1.0 data_dictionary entries into PRD erd_schema table structures.
     */
    public static function convertUniversalDictionaryToErdTables(array $dataDictionary): array
    {
        $converted = [];
        foreach ($dataDictionary as $tbl) {
            $tableName = $tbl['table'] ?? '';
            if ($tableName === '') {
                continue;
            }
            $cols = [];
            foreach (($tbl['columns'] ?? []) as $col) {
                $rawType = strtoupper((string) ($col['type'] ?? 'VARCHAR(120)'));
                $erdType = match (true) {
                    $rawType === 'ULID' && ! empty($col['foreign']) => 'foreignUlid',
                    $rawType === 'ULID' => 'ulid',
                    str_starts_with($rawType, 'DECIMAL') => strtolower($rawType),
                    $rawType === 'JSONB' => 'jsonb',
                    $rawType === 'BOOLEAN' => 'boolean',
                    $rawType === 'TIMESTAMP' => 'timestamp',
                    $rawType === 'DATE' => 'date',
                    $rawType === 'TIME' => 'time',
                    $rawType === 'TEXT' => 'text',
                    str_starts_with($rawType, 'INT') || $rawType === 'SMALLINT' => 'integer',
                    default => strtolower(str_replace('VARCHAR', 'string', $rawType)),
                };
                $indexTag = ! empty($col['primary'])
                    ? 'PRIMARY'
                    : ((! empty($col['index']) || ! empty($col['foreign'])) ? 'INDEX' : 'NONE');
                $noteParts = [];
                if (! empty($col['foreign'])) {
                    $noteParts[] = 'FK -> ' . $col['foreign'];
                }
                if (! empty($col['enum_ref'])) {
                    $noteParts[] = 'Enum ' . $col['enum_ref'];
                }
                if (empty($noteParts)) {
                    $noteParts[] = 'Composable Primitive Field (' . ($tbl['source_module'] ?? 'CORE') . ')';
                }

                $cols[] = [
                    'name' => $col['name'],
                    'type' => $erdType,
                    'index' => $indexTag,
                    'nullable' => (bool) ($col['nullable'] ?? false),
                    'notes' => implode(' | ', $noteParts),
                    'label' => [
                        'id' => Str::headline($col['name']),
                        'en' => Str::headline($col['name']),
                    ],
                ];
            }
            $cols[] = [
                'name' => 'created_at',
                'type' => 'timestamp',
                'index' => 'INDEX',
                'nullable' => true,
                'notes' => 'Keyset cursor pointer O(1)',
                'label' => ['id' => 'Waktu Dibuat', 'en' => 'Created At'],
            ];

            $constraintsNote = '';
            if (! empty($tbl['unique_constraints'])) {
                $constraintsNote = ' Constraints: ' . implode('; ', array_map(fn ($u) => $u['rule'] ?? '', $tbl['unique_constraints']));
            }

            $converted[] = [
                'name' => $tableName,
                'description' => 'Tabel Kontrak Deterministik Modul ' . ($tbl['source_module'] ?? 'PROJECT_OS') . '.' . $constraintsNote,
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => $cols,
            ];
        }

        return $converted;
    }


    /**
     * Merge two ERD table arrays without duplicate table names.
     */
    public static function mergeErdTablesUnique(array $primary, array $secondary): array
    {
        $seen = [];
        $merged = [];
        foreach (array_merge($primary, $secondary) as $tbl) {
            $name = strtolower((string) ($tbl['name'] ?? ''));
            if ($name === '' || isset($seen[$name])) {
                continue;
            }
            $seen[$name] = true;
            $merged[] = $tbl;
        }

        return $merged;
    }


    /**
     * Synthesize domain-specific relational database entities using Flagship LLM Reasoning
     * with Strict ULID and Keyset Indexing standards.
     */
    public static function synthesizeErdSchemaWithAi(
        string $businessName, 
        array $mvpItems, 
        array $actorItems, 
        VisionBlueprint $blueprint, 
        ?string $preferredAiProvider = null
    ): array
    {
        $masalah = $blueprint->masalah_utama ?: 'Otomatisasi alur kerja operasional.';
        $tujuan = $blueprint->tujuan_utama ?: 'Efisiensi dan akurasi data.';
        $slug = $blueprint->slug ?: Str::slug($businessName);

        // Fetch corpus if available from disk
        $corpusContent = "";
        try {
            $corpusFile = storage_path("app/projects/{$slug}/markdown/synthesized_corpus.md");
            if (file_exists($corpusFile)) {
                $corpusContent = mb_substr(file_get_contents($corpusFile), 0, 8000);
            }
        } catch (\Throwable $e) {}

        if (empty($corpusContent) && !empty($blueprint->user_metadata['raw_corpus'])) {
            $corpusContent = mb_substr((string) $blueprint->user_metadata['raw_corpus'], 0, 8000);
        }

        $featuresStr = implode("\n", array_map(function($idx, $item) {
            return ($idx + 1) . ". " . ($item['title'] ?? '') . ": " . ($item['desc'] ?? '');
        }, array_keys($mvpItems), $mvpItems));

        $actorsStr = implode("\n", array_map(function($idx, $act) {
            return ($idx + 1) . ". " . ($act['name'] ?? '') . " (" . ($act['badge'] ?? '') . "): " . ($act['role'] ?? '');
        }, array_keys($actorItems), $actorItems));

        $prompt = <<<PROMPT
You are a Principal Enterprise Database Architect specializing in PostgreSQL 16 Strict ULID and high-concurrency systems (O(1) Keyset Cursor Pagination).
Analyze this system specification:
Project: "{$businessName}"
Problem Statement: "{$masalah}"
Success Metrics: "{$tujuan}"
MVP Features:
{$featuresStr}
System Actors & RBAC:
{$actorsStr}
Attached Brief / Corpus Context:
{$corpusContent}

Task:
Generate a comprehensive relational database schema comprising 4 to 8 domain-specific business entities that 100% reflect the client's actual business logic and attributes.

MANDATORY ARCHITECTURAL RULES:
1. Primary Key: Every table MUST have column 'id' of type 'ulid' (VARCHAR 26) with index 'PRIMARY'. NEVER use auto-increment or UUID.
2. Foreign Keys: Every foreign relation MUST end in '_id' and use type 'foreignUlid' (VARCHAR 26) with index 'INDEX'.
3. Keyset Cursor Pagination: Every table MUST have timestamp 'created_at' with index 'INDEX' for sub-millisecond O(1) cursor pagination.
4. Business Column Integrity: Include realistic, concrete columns for this specific domain (e.g. monetary decimals, quantities, codes, localized names, phone_e164, status enums, flags, audit timestamps). DO NOT just return lazy generic 'data_payload jsonb' for everything.
5. Naming: Tables must be lowercase snake_case plural (e.g., 'orders', 'order_items', 'vehicles', 'inspections', 'work_orders', 'invoices').
6. Output JSON Array ONLY with schema:
[
  {
    "name": "table_name_plural",
    "description": "Clear explanation of table business purpose",
    "primary_key": "id (ULID - VARCHAR 26)",
    "columns": [
      {
        "name": "column_name",
        "type": "ulid|foreignUlid|string(255)|integer|decimal(15,2)|jsonb|boolean|timestamp|text",
        "index": "PRIMARY|INDEX|UNIQUE|NONE",
        "nullable": false,
        "notes": "Clear purpose of this field",
        "label": {"id": "Nama Kolom Indonesia", "en": "English Column Name"}
      }
    ]
  }
]
PROMPT;

        $systemInstruction = "You are the world's leading PostgreSQL Database Architect and Laravel 13 Specialist. You output valid JSON array ONLY, zero markdown fences, zero conversational preamble.";

        $aiRes = \App\Services\Ai\MultiAiModelManager::executeWithFailover($prompt, $systemInstruction, 'discovery', $preferredAiProvider);

        if (empty($aiRes['text'])) {
            return [];
        }

        $jsonText = trim($aiRes['text']);
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/', $jsonText, $m)) {
            $jsonText = trim($m[1]);
        }

        $decoded = json_decode($jsonText, true);
        if (!is_array($decoded) || empty($decoded)) {
            return [];
        }

        $normalizedTables = [];
        $hasUsers = false;
        $hasLogs = false;

        foreach ($decoded as $t) {
            if (!is_array($t) || empty($t['name'])) continue;

            $tName = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', trim($t['name'])));
            if ($tName === 'users') $hasUsers = true;
            if ($tName === 'activity_logs' || $tName === 'audit_logs') $hasLogs = true;

            $cols = [];
            $hasId = false;
            $hasCreatedAt = false;

            if (!empty($t['columns']) && is_array($t['columns'])) {
                foreach ($t['columns'] as $c) {
                    if (!is_array($c) || empty($c['name'])) continue;
                    $cName = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', trim($c['name'])));
                    $cType = $c['type'] ?? 'string(255)';
                    $cIndex = strtoupper($c['index'] ?? 'NONE');
                    $cNullable = (bool)($c['nullable'] ?? false);
                    $cNotes = $c['notes'] ?? '';
                    $cLabel = $c['label'] ?? ['id' => Str::headline($cName), 'en' => Str::headline($cName)];

                    if ($cName === 'id') {
                        $hasId = true;
                        $cType = 'ulid';
                        $cIndex = 'PRIMARY';
                        $cNullable = false;
                    }

                    if ($cName === 'created_at') {
                        $hasCreatedAt = true;
                        $cType = 'timestamp';
                        $cIndex = 'INDEX';
                    }

                    if (str_ends_with($cName, '_id') && $cName !== 'id') {
                        $cType = 'foreignUlid';
                        $cIndex = 'INDEX';
                    }

                    $cols[] = [
                        'name' => $cName,
                        'type' => $cType,
                        'index' => $cIndex,
                        'nullable' => $cNullable,
                        'notes' => $cNotes,
                        'label' => is_array($cLabel) ? $cLabel : ['id' => $cLabel, 'en' => $cLabel],
                    ];
                }
            }

            if (!$hasId) {
                array_unshift($cols, [
                    'name' => 'id',
                    'type' => 'ulid',
                    'index' => 'PRIMARY',
                    'nullable' => false,
                    'notes' => 'ULID primary key (26-char string)',
                    'label' => ['id' => 'ID (ULID)', 'en' => 'ID (ULID)'],
                ]);
            }

            if (!$hasCreatedAt) {
                $cols[] = [
                    'name' => 'created_at',
                    'type' => 'timestamp',
                    'index' => 'INDEX',
                    'nullable' => true,
                    'notes' => 'Keyset cursor pointer O(1)',
                    'label' => ['id' => 'Waktu Dibuat', 'en' => 'Created At'],
                ];
            }

            $normalizedTables[] = [
                'name' => $tName,
                'description' => $t['description'] ?? 'Tabel domain bisnis spesifik.',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => $cols,
            ];
        }

        // Always ensure users table is present
        if (!$hasUsers) {
            array_unshift($normalizedTables, [
                'name' => 'users',
                'description' => 'Menyimpan kredensial otentikasi semua aktor sistem (Admin, Staff, Klien).',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'Unique Lexicographically Sortable ID', 'label' => ['id' => 'ID Pengguna (ULID)', 'en' => 'User ID (ULID)']],
                    ['name' => 'name', 'type' => 'string(255)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Nama lengkap pengguna', 'label' => ['id' => 'Nama Lengkap', 'en' => 'Full Name']],
                    ['name' => 'email', 'type' => 'string(255)', 'index' => 'UNIQUE', 'nullable' => false, 'notes' => 'Email unik untuk login', 'label' => ['id' => 'Alamat Email', 'en' => 'Email Address']],
                    ['name' => 'password', 'type' => 'string(255)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Hashed Argon2id / Bcrypt', 'label' => ['id' => 'Kata Sandi Enkripsi', 'en' => 'Hashed Password']],
                    ['name' => 'role', 'type' => 'string(50)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'superadmin | staff | client', 'label' => ['id' => 'Peran Hak Akses', 'en' => 'Access Role']],
                    ['name' => 'is_active', 'type' => 'boolean', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Status aktif akun', 'label' => ['id' => 'Status Aktif', 'en' => 'Active Status']],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Waktu pembuatan akun', 'label' => ['id' => 'Waktu Dibuat', 'en' => 'Created At']],
                ],
            ]);
        }

        // Always ensure activity logs table is present
        if (!$hasLogs) {
            $normalizedTables[] = [
                'name' => 'activity_logs',
                'description' => 'Pencatatan riwayat audit (audit trail) untuk keamanan dan akuntabilitas sistem.',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'ULID audit record', 'label' => ['id' => 'ID Audit (ULID)', 'en' => 'Audit ID (ULID)']],
                    ['name' => 'user_id', 'type' => 'foreignUlid', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Aktor yang melakukan aksi', 'label' => ['id' => 'Aktor Pengguna', 'en' => 'Actor User ID']],
                    ['name' => 'action', 'type' => 'string(100)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'create | update | delete | approve', 'label' => ['id' => 'Jenis Aksi Sistem', 'en' => 'System Action']],
                    ['name' => 'target_table', 'type' => 'string(100)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Nama tabel sasaran', 'label' => ['id' => 'Tabel Sasaran', 'en' => 'Target Table']],
                    ['name' => 'changes', 'type' => 'jsonb', 'index' => 'NONE', 'nullable' => true, 'notes' => 'Snapshot diff data sebelum & sesudah', 'label' => ['id' => 'Perubahan Diff (JSONB)', 'en' => 'Changes Diff (JSONB)']],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Timestamp kejadian', 'label' => ['id' => 'Waktu Kejadian', 'en' => 'Timestamp']],
                ],
            ];
        }

        return $normalizedTables;
    }


    /**
     * Intelligently match a feature with the most relevant table from the ERD schema.
     */
    public static function matchRelatedTable(string $title, string $desc, array $erdTables): ?array
    {
        if (empty($erdTables)) {
            return null;
        }

        $lower = strtolower($title . ' ' . $desc);

        // 1. Direct match on users / auth table
        if (str_contains($lower, 'user') || str_contains($lower, 'pengguna') || str_contains($lower, 'auth') || str_contains($lower, 'login') || str_contains($lower, 'rbac')) {
            foreach ($erdTables as $tbl) {
                if (($tbl['name'] ?? '') === 'users') {
                    return $tbl;
                }
            }
        }

        // 2. Direct exact or singular match on domain table names
        foreach ($erdTables as $tbl) {
            $tName = strtolower($tbl['name'] ?? '');
            if ($tName === 'users' || $tName === 'activity_logs') continue;

            $singular = Str::singular($tName);
            if (str_contains($lower, $tName) || str_contains($lower, $singular)) {
                return $tbl;
            }
        }

        // 3. Technical domain synonym mapping (Anti-Disconnection Bridge)
        $synonymMap = [
            'vehicle_check' => ['inspection', 'inspeksi', 'pemeriksaan', 'kondisi', 'qc'],
            'inspection' => ['vehicle_checks', 'inspections', 'work_orders', 'maintenance_logs', 'servis', 'cek'],
            'vehicle' => ['vehicles', 'cars', 'fleets', 'armada', 'kendaraan', 'motorcycles', 'truk'],
            'whatsapp_session' => ['sesi', 'session', 'openwa', 'baileys', 'connection', 'qr_code', 'socket', 'device'],
            'message_log' => ['message', 'pesan', 'broadcast', 'chat', 'outbox', 'inbox', 'dispatch'],
            'webhook_subscription' => ['webhook', 'callback', 'event', 'listener', 'subscriber'],
            'aircraft' => ['plane', 'pesawat', 'flight', 'hangar', 'model', 'aerodinamika'],
            'flight_mission' => ['mission', 'misi', 'checkpoint', 'course', 'lintasan', 'air_ring'],
            'leaderboard' => ['score', 'skor', 'rekor', 'ranking', 'papan_peringkat', 'anti_cheat', 'telemetri'],
            'invoice' => ['tagihan', 'billing', 'bill', 'receipt', 'kwitansi', 'pembayaran', 'payment'],
            'order' => ['pesanan', 'booking', 'reservasi', 'sewa', 'transaksi'],
            'dormitory' => ['asrama', 'kamar', 'boarding', 'room', 'dean', 'exeat'],
            'work_study' => ['kerja_tangan', 'work_study', 'labor', 'timesheet', 'vokasi'],
            'student' => ['siswa', 'santri', 'murid', 'pelajar', 'student'],
            'curriculum' => ['lms', 'pembelajaran', 'materi', 'ujian', 'cbt', 'nilai', 'rapor'],
        ];

        foreach ($synonymMap as $canonical => $synonyms) {
            $matchesConcept = false;
            foreach ($synonyms as $syn) {
                if (str_contains($lower, $syn)) {
                    $matchesConcept = true;
                    break;
                }
            }

            if ($matchesConcept) {
                foreach ($erdTables as $tbl) {
                    $tName = strtolower($tbl['name'] ?? '');
                    if (str_contains($tName, $canonical) || in_array($tName, $synonyms)) {
                        return $tbl;
                    }
                }
            }
        }

        // 4. Column attribute matching (Score based on how many columns match feature description)
        $bestTable = null;
        $highestScore = 0;

        foreach ($erdTables as $tbl) {
            $tName = strtolower($tbl['name'] ?? '');
            if ($tName === 'users' || $tName === 'activity_logs') continue;

            $score = 0;
            $columns = $tbl['columns'] ?? [];
            foreach ($columns as $col) {
                $cName = strtolower($col['name'] ?? '');
                if (in_array($cName, ['id', 'created_at', 'updated_at', 'deleted_at'])) continue;

                if (str_contains($lower, $cName)) {
                    $score += 3;
                }
            }

            // Word partial matches in table name
            $words = preg_split('/[\s_-]+/', $lower);
            foreach ($words as $w) {
                if (strlen($w) >= 4 && str_contains($tName, $w)) {
                    $score += 2;
                }
            }

            if ($score > $highestScore) {
                $highestScore = $score;
                $bestTable = $tbl;
            }
        }

        if ($bestTable && $highestScore >= 3) {
            return $bestTable;
        }

        // 5. Fallback to first domain table
        foreach ($erdTables as $tbl) {
            $tName = strtolower($tbl['name'] ?? '');
            if ($tName !== 'users' && $tName !== 'activity_logs') {
                return $tbl;
            }
        }

        return $erdTables[0] ?? null;
    }

}
