<?php

namespace App\Services;

use App\Models\VisionBlueprint;
use Illuminate\Support\Str;

class PrdGeneratorService
{
    /**
     * Generate an Ultimate PRD & Architecture Blueprint from blueprint questionnaire inputs.
     */
    public static function generate(VisionBlueprint $blueprint): array
    {
        $businessName = $blueprint->nama_bisnis ?: ($blueprint->client_name . "'s Project");
        $masalah = $blueprint->masalah_utama ?: 'Otomatisasi proses bisnis manual dan sentralisasi data.';
        $tujuan = $blueprint->tujuan_utama ?: 'Meningkatkan efisiensi operasional dan akurasi pelaporan.';
        $audiens = $blueprint->target_audiens ?: 'Pengguna internal organisasi dan klien eksternal.';
        $aktor = $blueprint->aktor_sistem ?: 'Superadmin (Full Control), Staff/Operator (Input & Review), Pengunjung/Klien (Pengisian Form).';
        $fiturWajib = $blueprint->fitur_wajib ?: 'Manajemen Pengguna & RBAC, Input Formulir Data, Rekap Database Dinamis, Ekspor PDF/Excel.';
        $fiturTambahan = $blueprint->fitur_tambahan ?: 'Notifikasi WhatsApp/Email otomatis, Audit Trail Log, Dark Mode.';
        $alurKerja = $blueprint->alur_kerja ?: 'Pengguna membuka web -> Mengisi Formulir -> Sistem memvalidasi & menyimpan data -> Admin menerima notifikasi -> Verifikasi di Dasbor.';
        $integrasi = $blueprint->kebutuhan_integrasi ?: 'Payment Gateway, WhatsApp Gateway, Cloud Storage S3.';
        $referensiDesain = $blueprint->referensi_desain ?: 'Clean, modern minimalist dengan estetika Linear / Stripe.';
        $kesiapanAset = $blueprint->kesiapan_aset ?: 'Sedang Disiapkan';
        $targetWaktu = $blueprint->target_waktu ?: 'Fase 1 rilis dalam 3-4 pekan kerja.';

        // Parse actors into structured array
        $actorItems = self::parseItems($aktor);
        if (empty($actorItems)) {
            $actorItems = [
                ['name' => 'Superadmin', 'role' => 'Akses penuh seluruh konfigurasi, audit log, dan data sistem.'],
                ['name' => 'Staff / Operator', 'role' => 'Memproses data masuk, verifikasi berkas, dan rekap laporan.'],
                ['name' => 'Pengguna / Klien', 'role' => 'Mengisi data transaksi/formulir dan melihat status pengerjaan.'],
            ];
        }

        // Parse MVP Features
        $mvpItems = self::parseItems($fiturWajib);
        if (empty($mvpItems)) {
            $mvpItems = [
                ['title' => 'Autentikasi & RBAC', 'desc' => 'Login aman dengan Role-Based Access Control dan ULID identifiers.'],
                ['title' => 'Formulir Intake Terstruktur', 'desc' => 'Pengumpulan data tervalidasi dengan proteksi Anti-Spam.'],
                ['title' => 'Dasbor Administrasi', 'desc' => 'Pusat kendali berbasis Filament PHP dengan tabel filter data instan.'],
                ['title' => 'Laporan & Ekspor Data', 'desc' => 'Fitur ekspor format PDF dan Excel untuk kebutuhan rekonsiliasi harian.'],
            ];
        }

        // Parse Phase 2 Features
        $phase2Items = self::parseItems($fiturTambahan);

        // Parse Workflow Stages
        $workflowStages = self::parseWorkflow($alurKerja);

        // Generate tailored ERD Database Schema with strict ULID standards
        $erdTables = self::generateErdSchema($businessName, $mvpItems, $actorItems);

        // Extract ultimate blueprint context from metadata
        $metadata = $blueprint->user_metadata ?? [];
        $extraContext = [
            'skala_pengguna' => $metadata['skala_pengguna'] ?? '0 - 100.000 Pengguna / Bulan (Dedicated VPS Monolith)',
            'jangkauan_pasar' => $metadata['jangkauan_pasar'] ?? 'Domestik Indonesia (IDR, Zona WIB/WITA/WIT)',
            'out_of_scope' => $metadata['out_of_scope'] ?? null,
            'kepatuhan_keamanan' => $metadata['kepatuhan_keamanan'] ?? 'Standar Web Application & OWASP Top 10',
            'kisaran_budget' => $metadata['kisaran_budget'] ?? 'Rp 50.000.000 - Rp 100.000.000 (Growth Production)',
        ];

        return [
            'meta' => [
                'project_name' => $businessName,
                'version' => '1.0.0-PROPOSAL',
                'generated_at' => now()->toIso8601String(),
                'status' => 'Ultimate Vision Blueprint',
            ],
            'executive_summary' => [
                'title' => 'Executive Technical Discovery & Blueprint',
                'problem_statement' => $masalah,
                'success_metrics' => $tujuan,
                'target_audience' => $audiens,
                'design_inspiration' => $referensiDesain,
                'asset_readiness' => $kesiapanAset,
                'target_timeline' => $targetWaktu,
                'target_scale' => $extraContext['skala_pengguna'],
                'market_reach' => $extraContext['jangkauan_pasar'],
                'compliance_level' => $extraContext['kepatuhan_keamanan'],
                'budget_range' => $extraContext['kisaran_budget'],
                'architecture_philosophy' => 'Untuk menjamin efisiensi biaya server, ketahanan jangka panjang, dan kecepatan peluncuran (Rapid Time-to-Market), sistem ini dirancang menggunakan arsitektur Modern Monolith (Laravel 13 & Filament PHP). Seluruh tabel bisnis menggunakan Primary Key ULID untuk skalabilitas terdistribusi dan kompatibilitas penuh PostgreSQL.',
            ],
            'system_actors' => $actorItems,
            'features' => [
                'mvp_phase1' => $mvpItems,
                'phase2_roadmap' => $phase2Items,
            ],
            'workflow' => $workflowStages,
            'erd_schema' => [
                'standard' => 'PostgreSQL Strict / Laravel 13 ULID Architecture',
                'description' => 'Skema basis data dengan kompleksitas O(1) keystone pagination, UUID-agnostic ULID 26-char string primary keys, dan integritas relasi foreign key terisolasi.',
                'tables' => $erdTables,
            ],
            'tech_stack' => [
                'backend' => [
                    'name' => 'Laravel 13 Modern Monolith',
                    'role' => 'Core Business Engine, Eloquent ORM, RESTful/Action Handlers, Queues',
                ],
                'admin_panel' => [
                    'name' => 'Filament v5 Enterprise Suite',
                    'role' => 'Admin Panel, Rapid Data Filtering, Metric Widgets, RBAC Shield',
                ],
                'frontend' => [
                    'name' => 'Island Architecture (React 19 + Framer Motion + Tailwind CSS)',
                    'role' => 'Ultra-fluid interactive forms, Canva-style canvas capability, 60fps micro-animations',
                ],
                'database' => [
                    'name' => 'PostgreSQL 16+ (Strict ULID Schema)',
                    'role' => 'ACID Relational Storage, JSONB indexing, Keyset Cursor Pagination',
                ],
                'cache_and_queue' => [
                    'name' => 'Redis / Predis Engine',
                    'role' => 'High-throughput Session, Cache, & Async Background Queue Jobs',
                ],
                'infrastructure' => [
                    'name' => 'Dedicated VPS via Nixpacks & Docker',
                    'role' => 'Isolasi resource 100%, Cloudflare CDN fronting, Nginx HTTP/2 reverse-proxy',
                ],
            ],
            'integrations' => [
                'requested' => $integrasi,
                'notes' => 'Akan dihubungkan melalui service providers terisolasi dengan fallback retry mechanism.',
            ],
            'architecture_evaluation' => self::evaluateArchitecture($businessName, $masalah, $mvpItems, $alurKerja, $extraContext),
            'velocity_pricing_options' => self::generateVelocityPricingOptions($targetWaktu, $extraContext['kisaran_budget'] ?? null, $businessName, $masalah),
            'governance_and_sla' => [
                'title' => 'Tata Kelola, Standar Kualitas & SLA Serah Terima (Strict Governance & Handoff)',
                'definition_of_done' => [
                    'Seluruh fitur MVP Fase 1 berjalan sesuai spesifikasi di server Staging & Production.',
                    'Lolos audit keamanan dasar (CSRF token, sanitasi input XSS, proteksi SQL Injection, & HTTPS SSL).',
                    'Skema basis data relasional PostgreSQL dengan Primary Key ULID terverifikasi.',
                    'Dasbor admin Filament v5 dapat diakses oleh peran Superadmin / Staff yang ditunjuk.',
                    'Sesi pelatihan administrasi singkat dan serah terima kredensial resmi sistem.',
                ],
                'browser_device_matrix' => [
                    'supported' => 'Google Chrome, Apple Safari, Mozilla Firefox, Microsoft Edge (rilis 2 tahun terakhir); iOS Safari 15+; Android Chrome 100+.',
                    'unsupported' => 'Internet Explorer 11, Opera Mini data-saving mode, UC Browser legacy rendering engine, dan peramban ponsel non-standar.',
                ],
                'warranty_policy' => [
                    'duration' => '30 Hari Kalender Sejak Tanggal Peluncuran (Go-Live)',
                    'coverage' => 'Perbaikan bug, error sistem, atau ketidaksesuaian fungsi dari ruang lingkup MVP Fase 1 tanpa biaya tambahan.',
                    'exclusions' => 'Permintaan desain baru, penambahan field/tabel baru di luar PRD, atau kerusakan akibat modifikasi pihak ketiga di luar tim Neriah Pro.',
                ],
                'content_handoff_clause' => [
                    'rule' => 'Klien wajib menyerahkan aset konten resmi (teks, logo, foto) maksimal 7 hari kerja sejak approval blueprint.',
                    'fallback' => 'Apabila terjadi keterlambatan dari pihak klien, tim pengembang berhak menggunakan dummy/placeholder text standar industri agar timeline rilis dan jadwal serah terima tidak tertunda.',
                ],
                'recurring_cost_transparency' => [
                    'domain' => 'Rp 150.000 - Rp 250.000 / tahun (Dibayarkan langsung ke registrar domain resmi)',
                    'hosting_starter' => 'Rp 50.000 - Rp 150.000 / bulan (Untuk paket non-profit/komunitas pada cloud server efisien)',
                    'hosting_enterprise' => 'Rp 350.000 - Rp 1.500.000 / bulan (Dedicated VPS Nixpacks & Docker untuk high-traffic scale)',
                ],
            ],
            'action_plan' => [
                ['phase' => 'Fase 0: Blueprint & Skema Approval', 'duration' => 'Hari ke 1-3', 'status' => 'Active'],
                ['phase' => 'Fase 1: Database Migration & Admin Filament CRUD', 'duration' => 'Pekan 1', 'status' => 'Pending'],
                ['phase' => 'Fase 2: Frontend Interactive UI & Client Form', 'duration' => 'Pekan 2', 'status' => 'Pending'],
                ['phase' => 'Fase 3: Integrasi API, Notifikasi & Ekspor Laporan', 'duration' => 'Pekan 3', 'status' => 'Pending'],
                ['phase' => 'Fase 4: UAT, Stress Test, & Production VPS Deployment', 'duration' => 'Pekan 4', 'status' => 'Pending'],
            ],
        ];
    }

    /**
     * Parse multiline text or comma-separated text into structured items.
     */
    protected static function parseItems(string $text): array
    {
        $lines = preg_split('/[\r\n]+/', trim($text));
        $items = [];

        foreach ($lines as $line) {
            $line = trim($line, " \t\n\r\0\x0B-•*1234567890.)");
            if (empty($line)) {
                continue;
            }

            // Check if there's a colon or dash separator for title vs description
            if (str_contains($line, ':')) {
                [$title, $desc] = explode(':', $line, 2);
                $items[] = [
                    'title' => trim($title),
                    'desc' => trim($desc),
                ];
            } elseif (str_contains($line, ' - ')) {
                [$title, $desc] = explode(' - ', $line, 2);
                $items[] = [
                    'title' => trim($title),
                    'desc' => trim($desc),
                ];
            } else {
                $items[] = [
                    'title' => $line,
                    'desc' => 'Spesifikasi fitur utama untuk operasional sistem.',
                ];
            }
        }

        return $items;
    }

    /**
     * Parse workflow text into sequenced step cards.
     */
    protected static function parseWorkflow(string $text): array
    {
        // 1. First attempt split by -> or => or newlines
        if (str_contains($text, '->')) {
            $rawSteps = explode('->', $text);
        } elseif (str_contains($text, '=>')) {
            $rawSteps = explode('=>', $text);
        } else {
            $rawSteps = preg_split('/[\r\n]+/', $text);
        }

        $steps = [];
        foreach ($rawSteps as $s) {
            $cleaned = trim($s, " \t\n\r\0\x0B-•*1234567890.)");
            if (!empty($cleaned)) {
                $steps[] = $cleaned;
            }
        }

        // 2. If it resulted in only 1 long step, intelligently split by comma / transitional keywords
        if (count($steps) === 1 && strlen($steps[0]) > 35) {
            $textSingle = $steps[0];
            $splitParts = preg_split('/,\s*(?:lalu\s+|kemudian\s+|setelah itu\s+|selanjutnya\s+)?|\s+(?:lalu|kemudian|setelah itu|selanjutnya)\s+/i', $textSingle);
            
            $refined = [];
            foreach ($splitParts as $part) {
                $part = trim($part, " \t\n\r\0\x0B-•*1234567890.),");
                if (strlen($part) > 3) {
                    $refined[] = $part;
                }
            }
            if (count($refined) > 1) {
                $steps = $refined;
            }
        }

        $stages = [];
        $index = 1;
        foreach ($steps as $step) {
            $step = trim($step, " \t\n\r\0\x0B-•*1234567890.)");
            if (empty($step)) {
                continue;
            }

            $lower = strtolower($step);
            $actor = 'Pengguna / Pengunjung';
            $badge = 'INTERACTION';
            $type = 'client';

            if (str_contains($lower, 'buka') || str_contains($lower, 'katalog') || str_contains($lower, 'lihat')) {
                $actor = 'Pengguna / Klien';
                $badge = 'DISCOVERY';
                $type = 'client';
            } elseif (str_contains($lower, 'filter') || str_contains($lower, 'cari') || str_contains($lower, 'search')) {
                $actor = 'Sistem / Search Engine';
                $badge = 'QUERY_FILTER';
                $type = 'filter';
            } elseif (str_contains($lower, 'notifikasi') || str_contains($lower, 'berlangganan') || str_contains($lower, 'alert') || str_contains($lower, 'email')) {
                $actor = 'Notification Engine';
                $badge = 'NOTIFICATION';
                $type = 'notification';
            } elseif (str_contains($lower, 'validasi') || str_contains($lower, 'simpan') || str_contains($lower, 'database')) {
                $actor = 'PostgreSQL / Laravel ORM';
                $badge = 'DATABASE';
                $type = 'database';
            } elseif (str_contains($lower, 'admin') || str_contains($lower, 'verifikasi') || str_contains($lower, 'dasbor')) {
                $actor = 'Administrator / Operator';
                $badge = 'APPROVAL';
                $type = 'admin';
            }

            $stages[] = [
                'step' => $index++,
                'action' => $step,
                'description' => 'Tahapan validasi, interaksi antarmuka, dan transmisi data alur kerja sistem.',
                'actor' => $actor,
                'badge' => $badge,
                'type' => $type,
            ];
        }

        if (empty($stages)) {
            $stages = [
                ['step' => 1, 'action' => 'Akses Portal & Registrasi', 'description' => 'Pengguna membuka aplikasi dan memasukkan kredensial / identitas.', 'actor' => 'Pengguna', 'badge' => 'AUTH', 'type' => 'client'],
                ['step' => 2, 'action' => 'Pengisian Data / Form Transaksi', 'description' => 'Validasi sisi klien dan transmisi ke backend Laravel.', 'actor' => 'Klien / Sistem', 'badge' => 'INPUT', 'type' => 'client'],
                ['step' => 3, 'action' => 'Verifikasi & Notifikasi Otomatis', 'description' => 'Sistem mengirimkan konfirmasi instan dan mencatat audit trail.', 'actor' => 'Notifikasi', 'badge' => 'NOTIFICATION', 'type' => 'notification'],
                ['step' => 4, 'action' => 'Approval & Manajemen Dasbor Admin', 'description' => 'Pengelola memproses data melalui tabel Filament berkecepatan tinggi.', 'actor' => 'Admin', 'badge' => 'APPROVAL', 'type' => 'admin'],
            ];
        }

        return $stages;
    }

    /**
     * Generate Tailored ERD Database Schema (ULID Primary Keys & PostgreSQL strict standards).
     */
    protected static function generateErdSchema(string $businessName, array $mvpItems, array $actorItems): array
    {
        $domainSlug = Str::slug($businessName, '_');
        $domainSlug = preg_replace('/[^a-zA-Z0-9_]/', '_', $domainSlug);
        $domainSlug = trim($domainSlug, '_');
        if (empty($domainSlug)) {
            $domainSlug = 'domain_records';
        }

        return [
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
                    ['name' => 'title', 'type' => 'string(255)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Judul / Nama entitas data', 'label' => ['id' => 'Nama / Judul Item', 'en' => 'Title / Item Name']],
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
                    ['name' => 'message_body', 'type' => 'text', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Isi konten notifikasi', 'label' => ['id' => 'Isi Konten Pesan', 'en' => 'Message Content']],
                    ['name' => 'status', 'type' => 'string(30)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'queued | sent | failed', 'label' => ['id' => 'Status Pengiriman', 'en' => 'Delivery Status']],
                    ['name' => 'sent_at', 'type' => 'timestamp', 'index' => 'NONE', 'nullable' => true, 'notes' => 'Waktu terkirim sukses', 'label' => ['id' => 'Waktu Terkirim', 'en' => 'Sent At']],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Waktu pemicu notifikasi', 'label' => ['id' => 'Waktu Antrean', 'en' => 'Queued At']],
                ],
            ],
        ];
    }

    /**
     * Evaluate hosting, architecture pattern, and AI database requirements.
     */
    public static function evaluateArchitecture(string $businessName, string $masalah, array $mvpItems, string $alurKerja, array $extraContext = []): array
    {
        // Parse dynamic out of scope items from client input
        $customOutScope = [];
        if (!empty($extraContext['out_of_scope'])) {
            $lines = preg_split('/[\r\n]+/', trim($extraContext['out_of_scope']));
            foreach ($lines as $l) {
                $l = trim($l, " \t\n\r\0\x0B-•*1234567890.)");
                if (!empty($l)) {
                    $customOutScope[] = $l;
                }
            }
        }
        $outOfScopeList = array_merge(
            $customOutScope,
            [
                'Aplikasi native Android/iOS terpisah (Sistem Fase 1 disediakan dalam arsitektur Web Mobile-First / PWA).',
                'Integrasi kustom dengan sistem ERP legacy internal yang belum memiliki open RESTful API terdokumentasi.',
                'Fitur multi-warehouse internasional lintas benua dengan kalkulasi bea cukai dinamis.',
                'Seluruh penambahan fitur baru di luar daftar ini dikunci secara hukum dan akan diakomodasikan melalui Change Request (CR) / Addendum terpisah.',
            ]
        );

        $budgetDeclared = $extraContext['kisaran_budget'] ?? '';
        $rawDeclared = strtolower($budgetDeclared);
        $isLeanBudget = str_contains($rawDeclared, '5.000.000 - rp 15.000.000') ||
                        (str_contains($rawDeclared, '5.000.000') && !str_contains($rawDeclared, '35.000.000') && !str_contains($rawDeclared, '75.000.000')) ||
                        str_contains($rawDeclared, 'starter') ||
                        str_contains($rawDeclared, 'komunitas') ||
                        str_contains(strtolower($businessName), 'gereja');

        return [
            'hosting_evaluation' => [
                'verdict' => $isLeanBudget 
                    ? 'Cloud Starter / Micro VPS (Fase 1) & Seamless VPS Scale-Up (Fase 2)' 
                    : 'Dedicated VPS (Nixpacks & Docker Containerization)',
                'verdict_badge' => $isLeanBudget ? 'LEAN_CLOUD_STARTER' : 'VPS_DEDICATED',
                'recommendation' => $isLeanBudget ? 'CLOUD_STARTER_LEAN' : 'DEDICATED_VPS',
                'compute_weight_score' => $isLeanBudget ? '70/100 (Lean Operational Footprint)' : '92/100 (High-Throughput Enterprise)',
                'shared_hosting' => [
                    'status' => $isLeanBudget ? 'RECOMMENDED FOR LEAN PHASE 1 (HEMAT BIAYA)' : 'SUFFICIENT FOR STATICS / LIMITED FOR AI',
                    'title' => 'Cloud Starter / Shared Hosting Efisien (< Rp 100.000 / bln)',
                    'reasons' => [
                        'Pilihan Cerdas Tahap Awal: Sangat efisien untuk validasi pasar, website profil bisnis, katalog UMKM, atau organisasi komunitas tanpa beban sewa server besar.',
                        'Zero DevOps Overhead: Konfigurasi instan dan ramah pemula, langsung aktif dengan proteksi SSL gratis.',
                        'Catatan Skalabilitas: Untuk pemrosesan AI berbobot tinggi atau background worker 24/7, sistem dapat di-upgrade ke Dedicated VPS dengan 1-klik tanpa ganti struktur kode.',
                    ],
                ],
                'dedicated_vps' => [
                    'status' => 'HIGH-PERFORMANCE / SCALE-UP READY (Rp 350.000+ / bln)',
                    'title' => 'Dedicated VPS Container (Nixpacks & Docker)',
                    'reasons' => [
                        'Isolasi Resource 100%: Alokasi CPU & RAM terdedikasi menjamin throughput data tinggi tanpa gangguan tenant lain.',
                        'Native PostgreSQL 16+ pgvector Support: Penyimpanan representasi vektor berdimensi tinggi untuk AI embeddings & semantic search.',
                        'Redis In-Memory Queue & Worker 24/7: Menjalankan pemrosesan background jobs asinkron tanpa batas timeout.',
                        'Nginx HTTP/2 Reverse Proxy & Cloudflare CDN: Latensi minimal dengan proteksi SSL otomatis dan isolasi container Docker.',
                    ],
                ],
            ],
            'architecture_pattern_evaluation' => [
                'verdict' => 'Modern Monolith (Laravel 13 + Filament v5 + Island Architecture)',
                'verdict_badge' => 'RAPID_MONOLITH',
                'recommendation' => 'MODERN_MONOLITH',
                'match_percentage' => '96% Optimal Architectural Match',
                'monolith' => [
                    'status' => 'OPTIMAL REKOMENDASI (96% MATCH)',
                    'title' => 'Modern Monolith Architecture',
                    'reasons' => [
                        'Eliminasi Network Latency: Komunikasi antar modul berjalan intra-process O(1) tanpa overhead HTTP network antar-microservices.',
                        'Pangkas Biaya Infrastruktur 60-80%: Satu kesatuan container deployment menghemat anggaran server staging & produksi dibanding kluster microservices.',
                        'Rapid Time-to-Market (3x Lebih Cepat): Skema database, API internal, dan Admin Dasbor Filament v5 langsung sinkron tanpa duplikasi skema.',
                        'Konsistensi Transaksi ACID: Menjamin integritas data tanpa kerumitan distributed transaction (2-Phase Commit / Saga Pattern) yang rawan data loss.',
                        'Island Architecture Frontend: Memberikan fluiditas interaksi 60fps setara SPA dengan stabilitas dan kecepatan SEO Server-Side Rendering.',
                    ],
                ],
                'decoupled' => [
                    'status' => 'NOT RECOMMENDED (OVERKILL UNTUK FASE 1)',
                    'title' => 'Decoupled / Microservices Pattern',
                    'reasons' => [
                        'Hanya diperlukan jika tim pengembang berjumlah lebih dari 15-20 engineer yang bekerja di repositori terpisah.',
                        'Menambah biaya operasional server terpisah (Backend API server + Frontend Next.js node cluster terpisah).',
                        'Meningkatkan latensi round-trip HTTP dan beban autentikasi token JWT di setiap request interaksi.',
                    ],
                ],
            ],
            'strategic_guidance' => [
                'title' => 'Panduan Edukatif Hulu ke Hilir: Do\'s & Don\'ts serta Pro\'s & Con\'s',
                'subtitle' => 'Edukasi komprehensif bagi pemangku kepentingan agar investasi teknologi tepat sasaran, efisien, dan bebas risiko scope creep.',
                'dos' => [
                    [
                        'tag' => 'HULU // STRATEGI',
                        'title' => 'Mulai dari Web Mobile-First / PWA Terlebih Dahulu',
                        'desc' => 'Menghemat 70-80% modal awal dibanding langsung membangun native iOS/Android di App Store. Web responsif langsung dapat diakses lewat tautan WhatsApp tanpa hambatan install.',
                    ],
                    [
                        'tag' => 'HULU // ARSITEKTUR',
                        'title' => 'Gunakan Modern Monolith untuk Merilis MVP Cepat',
                        'desc' => 'Framework terpadu (Laravel 13 & Filament v5) memangkas waktu pembuatan admin dari berbulan-bulan menjadi 5-10 hari dengan biaya server paling hemat.',
                    ],
                    [
                        'tag' => 'TENGAH // SCOPE LOCK',
                        'title' => 'Kunci 3-5 Fitur Inti yang Menyelesaikan Masalah Kritis',
                        'desc' => 'Fokuskan energi peluncuran pada alur kerja utama pengguna. Fitur tambahan di luar fungsi krusial dijadwalkan pada Fase 2 setelah ada data penggunaan nyata.',
                    ],
                    [
                        'tag' => 'HILIR // OPERASIONAL',
                        'title' => 'Otomatisasi Kanal Komunikasi (WhatsApp & Pembayaran Digital)',
                        'desc' => 'Integrasikan QRIS dan notifikasi WhatsApp instan untuk mengurangi beban kerja manual tim administrasi hingga 90%.',
                    ],
                    [
                        'tag' => 'HILIR // BIAYA',
                        'title' => 'Transparansi Biaya Rutin Domain & Hosting Sejak Awal',
                        'desc' => 'Pahami biaya perpanjangan domain tahunan dan server bulanan agar operasional sistem berjalan tanpa kendala arus kas tak terduga.',
                    ],
                ],
                'donts' => [
                    [
                        'tag' => 'HULU // OVERKILL',
                        'title' => 'Jangan Memaksakan Microservices di Awal',
                        'desc' => 'Microservices pada tahap awal hanya menambah kerumitan latensi, bug terdistribusi, dan membakar puluhan juta rupiah untuk sewa kluster cloud yang kosong.',
                    ],
                    [
                        'tag' => 'HULU // BIAYA',
                        'title' => 'Jangan Membeli Server Kelas Enterprise Terlalu Dini',
                        'desc' => 'Gunakan cloud starter hemat (< Rp 100rb/bln) saat validasi ide. Upgrade ke Dedicated VPS dapat dilakukan kapan saja hanya dalam hitungan menit tanpa migrasi ulang.',
                    ],
                    [
                        'tag' => 'TENGAH // SCOPE CREEP',
                        'title' => 'Jangan Menambah Fitur Baru di Tengah Sprint Pengerjaan',
                        'desc' => 'Penambahan ide dadakan tanpa evaluasi tertulis akan merusak timeline peluncuran dan memperbesar risiko kegagalan proyek.',
                    ],
                    [
                        'tag' => 'HILIR // ASUMSI',
                        'title' => 'Jangan Menunda Peluncuran Menunggu Kesempurnaan',
                        'desc' => 'Aplikasi terbaik adalah aplikasi yang hidup dan dipakai oleh pengguna nyata. Rilis cepat, dapatkan masukan, dan lakukan iterasi terarah.',
                    ],
                ],
                'pros_and_cons' => [
                    [
                        'dimension' => 'Pola Arsitektur Sistem',
                        'option_a' => [
                            'name' => 'Modern Monolith (Laravel 13 + Filament)',
                            'pros' => ['Paling cepat rilis (3x)', 'Biaya server hemat 70%', 'Integritas data transaksi ACID terjamin', '1 tim pengembang terpadu'],
                            'cons' => ['Perlu disiplin pemisahan modul agar kode tetap rapi saat skala membesar'],
                            'verdict' => 'PILIHAN EMAS: Direkomendasikan untuk 98% proyek dari UMKM hingga platform skala jutaan pengguna.',
                        ],
                        'option_b' => [
                            'name' => 'Decoupled Microservices / Multi-Repo',
                            'pros' => ['Deploy independen per divisi besar (>20 engineer)', 'Isolasi kegagalan per service'],
                            'cons' => ['Biaya server membengkak 5x-10x', 'Latensi jaringan antar-API', 'Sangat rumit untuk debugging & testing'],
                            'verdict' => 'TUNDA KE FASE 3: Hanya diperlukan jika tim pengembang sudah >20 orang dan trafik >50 juta user/bulan.',
                        ],
                    ],
                    [
                        'dimension' => 'Infrastruktur Hosting & Server',
                        'option_a' => [
                            'name' => 'Cloud Starter / Shared Efisien',
                            'pros' => ['Sangat terjangkau (< Rp 100rb/bln)', 'Setup instan tanpa pusing DevOps', 'Cukup untuk web profil, katalog, & warta'],
                            'cons' => ['Resource komputasi dibagi dengan penyewa lain', 'Terbatas untuk worker AI continuous'],
                            'verdict' => 'IDEAL FASE 1: Solusi paling rasional untuk UMKM, komunitas, dan proyek validasi modal minim.',
                        ],
                        'option_b' => [
                            'name' => 'Dedicated VPS (Nixpacks & Docker)',
                            'pros' => ['100% isolasi performa', 'Mendukung database AI pgvector', 'Queue worker 24/7 tanpa timeout', 'Zero-downtime deploy'],
                            'cons' => ['Biaya bulanan mulai Rp 350rb/bln', 'Memerlukan manajemen container terkelola'],
                            'verdict' => 'STANDAR SCALE-UP: Wajib untuk sistem transaksi bisnis harian, multi-cabang, atau integrasi AI cerdas.',
                        ],
                    ],
                    [
                        'dimension' => 'Engine Basis Data (Database)',
                        'option_a' => [
                            'name' => 'PostgreSQL Relasional Strict ULID',
                            'pros' => ['Performa query O(1) kilat', 'Integritas referensial kuat', 'Skalabilitas tanpa batas berkat ID ULID terdistribusi'],
                            'cons' => ['Pencarian berbasis teks kata persis (belum memahami konteks semantik makna kalimat)'],
                            'verdict' => 'FONDASI UTAMA: Standar mutlak untuk menyimpan data bisnis, keuangan, dan pengguna.',
                        ],
                        'option_b' => [
                            'name' => 'PostgreSQL + pgvector (AI-Augmented)',
                            'pros' => ['Mendukung pencarian semantik makna', 'Rekomendasi cerdas berbasis vector embeddings', 'Co-located tanpa biaya vendor vector DB terpisah'],
                            'cons' => ['Membutuhkan kapasitas RAM server sedikit lebih besar untuk indeks vektor'],
                            'verdict' => 'NILAI TAMBAH CERDAS: Diaktifkan untuk fitur asisten AI, pencarian katalog pintar, atau pencocokan otomatis.',
                        ],
                    ],
                ],
            ],
            'global_scale_analysis' => [
                'title' => 'Analisis Potensi Skala Jangkauan Pengguna Dunia (Global Reach Matrix)',
                'summary' => 'Modern Monolith mampu melayani 99.5% startup dan enterprise global hingga 5-10 juta Monthly Active Users (MAU) sebelum membutuhkan pemisahan microservices.',
                'tiers' => [
                    [
                        'scale' => '0 - 100.000 Pengguna / Bulan',
                        'architecture' => 'Single Dedicated VPS Monolith (4 vCPU / 8GB RAM) / Cloud Starter',
                        'status' => 'SANGAT EFISIEN',
                        'verdict' => 'Response time sub-50ms. Biaya server sangat hemat. Zero DevOps maintenance.',
                    ],
                    [
                        'scale' => '100.000 - 5.000.000 Pengguna / Bulan',
                        'architecture' => 'Horizontal Scaled Monolith (Stateless Nodes + Managed Postgres + Redis)',
                        'status' => 'ENTERPRISE PRODUCTION',
                        'verdict' => 'Terbukti pada raksasa dunia (Shopify, GitHub, Basecamp). Bebas latensi antar-service, skalabilitas horizontal instan via load balancer.',
                    ],
                    [
                        'scale' => '50.000.000+ Pengguna Global Lintas Benua',
                        'architecture' => 'Decoupled Multi-Region Microservices Mesh',
                        'status' => 'ROADMAP FASE 3',
                        'verdict' => 'Hanya relevan jika ada regulasi data residency lokal terpisah (GDPR Eropa vs US vs Asia) dan tim multi-divisi >50 engineer.',
                    ],
                ],
            ],
            'budget_tco_analysis' => [
                'title' => 'Analisis Anggaran Klien & Efisiensi Modal (TCO Comparison)',
                'client_budget_declared' => $extraContext['kisaran_budget'] ?? 'Rp 15.000.000 - Rp 35.000.000',
                'monolith_tco' => [
                    'title' => 'Modern Monolith (Efisiensi Modal 90%)',
                    'monthly_cost' => $isLeanBudget ? '< Rp 100.000 - Rp 250.000 / bulan' : 'Rp 350.000 - Rp 1.500.000 / bulan',
                    'devops_headcount' => '0 FTE (Automated Nixpacks CI/CD)',
                    'capital_efficiency' => '90% anggaran klien dialokasikan murni untuk fitur bisnis & akuisisi pengguna.',
                ],
                'decoupled_tco' => [
                    'title' => 'Decoupled Microservices (Beban Modal Tinggi)',
                    'monthly_cost' => 'Rp 8.000.000 - Rp 25.000.000+ / bulan',
                    'devops_headcount' => '1-2 Dedicated DevOps Engineers (Rp 20-40 jt/bln)',
                    'capital_efficiency' => '60% anggaran tersedot hanya untuk biaya operasional kluster Kubernetes, API Gateway, dan distributed tracing.',
                ],
            ],
            'decoupling_threshold_triggers' => [
                'title' => '4 Faktor Penentu Mutlak Kapan Sistem Wajib Decoupled (The Decoupling Threshold)',
                'subtitle' => 'Jangan pernah memecah sistem menjadi microservices kecuali 1 atau lebih pemicu mutlak berikut terpenuhi:',
                'triggers' => [
                    [
                        'number' => '01',
                        'title' => "Conway's Law & Skala Organisasi Tim (>15-20 Engineer)",
                        'desc' => 'Ketika jumlah tim pengembang internal sudah melebihi 15-20 orang dalam beberapa squad bisnis mandiri (misal Tim Checkout, Tim Logistik, Tim Fraud) yang sering mengalami antrean merge git dan bottleneck rilis bersama.',
                    ],
                    [
                        'number' => '02',
                        'title' => 'Beban Komputasi Asimetris Ekstrim (Asymmetric Compute Bottleneck)',
                        'desc' => 'Ketika terdapat satu modul komputasi yang sangat berat (seperti video rendering 4K real-time, training model AI lokal, atau high-frequency stock stream) yang jika digabung akan membekukan thread web server utama.',
                    ],
                    [
                        'number' => '03',
                        'title' => 'Kepatuhan Regulasi Ketat & Blast-Radius Containment',
                        'desc' => 'Ketika modul pembayaran kartu kredit harus tersertifikasi PCI-DSS Level 1 dan terisolasi di private network terpisah agar audit kepatuhan tidak mencakup seluruh kode aplikasi bisnis.',
                    ],
                    [
                        'number' => '04',
                        'title' => 'Kebutuhan Mutlak Polyglot Technology Stack',
                        'desc' => 'Ketika ada modul spesifik yang mutlak harus ditulis dalam bahasa pemrograman lain dengan performa mikro-detik (misal Rust/C++ untuk engine kalkulasi matematika, atau Python untuk ekosistem PyTorch).',
                    ],
                ],
            ],
            'scope_boundaries' => [
                'title' => 'Matriks Batasan Ruang Lingkup (Strict Scope Lock & Anti-Feature Creep)',
                'client_scale' => $extraContext['skala_pengguna'] ?? '0 - 100.000 Pengguna / Bulan (Dedicated VPS Monolith)',
                'client_market' => $extraContext['jangkauan_pasar'] ?? 'Domestik Indonesia (IDR, Zona WIB/WITA/WIT)',
                'client_compliance' => $extraContext['kepatuhan_keamanan'] ?? 'Standar Web Application & OWASP Top 10',
                'client_budget' => $extraContext['kisaran_budget'] ?? 'Rp 15.000.000 - Rp 35.000.000 (Growth Production)',
                'in_scope' => [
                    'Spesifikasi fitur inti MVP Fase 1 yang tertera dalam dokumen PRD ini.',
                    'Skema basis data relasional PostgreSQL dengan Primary Key ULID standar enterprise.',
                    'Pusat kendali operasional (Admin Panel) berbasis Filament PHP v5 dengan RBAC Shield.',
                    'Frontend Island Architecture (React 19) dengan interaktivitas fluid 60fps.',
                    'Integrasi gerbang pembayaran otomatis (Midtrans Escrow) & audit logging transaksi.',
                ],
                'out_of_scope' => $outOfScopeList,
            ],
            'ai_database_blueprint' => [
                'engine' => 'PostgreSQL 16+ with pgvector & Strict ULID standard',
                'engine_badge' => 'AI_READY_PGVECTOR',
                'features' => [
                    'Ekstensi pgvector: Menyimpan vector embeddings (1536-dim / 3072-dim) langsung berdampingan dengan data relasional bisnis tanpa butuh vector DB terpisah (seperti Pinecone/Milvus).',
                    'Indeks HNSW (Hierarchical Navigable Small World): Pencarian similaritas semantik dan RAG dokumen berkecepatan sub-millisecond O(log N).',
                    'JSONB Dynamic Indexing: Mendukung penyimpanan context history fleksibel untuk metadata agen AI Gemini Ultra.',
                    'Strict ULID Primary Key: Menjamin partisi data terdistribusi dan keystone cursor pagination O(1) tanpa sequence lock.',
                ],
            ],
            'recommended_tools' => [
                ['category' => 'Backend Core', 'name' => 'Laravel 13 Modern Monolith', 'desc' => 'PHP 8.4/8.5 Property Hooks, Eloquent ORM, Action Handlers, Queues'],
                ['category' => 'Admin & Operations', 'name' => 'Filament PHP v5 Enterprise', 'desc' => 'Dasbor kendali instan, Filter Keyset, Export PDF/Excel, RBAC Shield'],
                ['category' => 'Frontend Layer', 'name' => 'Island Architecture (React 19 + Framer Motion)', 'desc' => 'Interaktivitas fluid 60fps, micro-animations, Canva-style canvas capability'],
                ['category' => 'AI Database Engine', 'name' => 'PostgreSQL 16+ (pgvector)', 'desc' => 'Vector embeddings, HNSW semantic search, JSONB documents, ULID standard'],
                ['category' => 'In-Memory Cache & Broker', 'name' => 'Redis / Predis', 'desc' => 'Zero-latency sessions, distributed lock, persistent queue processing'],
                ['category' => 'Infrastructure & Runtime', 'name' => 'Dedicated VPS via Nixpacks & Docker', 'desc' => 'Container isolation, Nginx HTTP/2, automated SSL, Zero-downtime deploy'],
                ['category' => 'AI Acceleration Engine', 'name' => 'Gemini Ultra / Pro API SDK', 'desc' => 'High-reasoning prompt synthesis, context injection RAG, automated code assistant'],
            ],
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
                    'ai_quota_spec' => 'Midtrans Payment Escrow, WhatsApp Gateway & Background Redis Workers',
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
                    'name' => 'Fast-Track Sprint (Gemini Ultra Accelerator)',
                    'duration' => '14 Hari Kerja',
                    'badge' => '2X_SPEED // RECOMMENDED',
                    'speed_multiplier' => '1.5x (Rilis 2 Pekan)',
                    'contract_amount' => 60000000.00,
                    'dp_amount' => 30000000.00,
                    'pelunasan_amount' => 30000000.00,
                    'ai_quota_spec' => 'Gemini Ultra 4-Agent Parallel Swarm + High-Reasoning Token Pipeline',
                    'squad_allocation' => '2 Dedicated Senior Engineers + AI Agentic Pair Programming',
                    'cost_formula' => 'Base Fee (Rp 45M) + Sewa Swarm AI Ultra Cloud (Rp 15M)',
                    'ai_swarm_specs' => [
                        'Concurrency: 4 Parallel AI Agents untuk boilerplate, migration, test synthesis',
                        'Context Window: High-Context Reasoning 1M Tokens',
                        'Prioritas Cloud Inference Zero-Queue',
                    ],
                    'description' => 'Akselerasi peluncuran 2 pekan dengan bantuan kluster komputasi Gemini Ultra untuk mempercepat integrasi kompleks dan verifikasi arsitektur.',
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
                    'ai_quota_spec' => 'Gemini Ultra 6-Agent Swarm + Continuous Integration Pipeline',
                    'squad_allocation' => '3 Senior Engineers (Dedicated War Room)',
                    'cost_formula' => 'Base Fee (Rp 45M) + AI Ultra Swarm (Rp 15M) + Dedicated War Room Squad (Rp 15M)',
                    'ai_swarm_specs' => [
                        'War-Room Engineering intensif dengan pemantauan deployment real-time',
                        'Throughput inferensi maksimum untuk sintesis kode tanpa antrean',
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
                    'name' => 'Gemini Ultra Swarm Parallel Sprint',
                    'duration' => '14 Hari Kerja',
                    'badge' => 'RECOMMENDED // ULTRA_SWARM',
                    'speed_multiplier' => '1.5x (Akselerasi 2 Pekan)',
                    'contract_amount' => 100000000.00,
                    'dp_amount' => 50000000.00,
                    'pelunasan_amount' => 50000000.00,
                    'ai_quota_spec' => 'Gemini Ultra 8-Agent Swarm Cluster + 2M Max Context Uncapped TPS',
                    'squad_allocation' => 'Lead Architect + 3 Dedicated Senior Engineers + AI Agentic Pair',
                    'cost_formula' => 'Base Enterprise (Rp 75M) + Sewa Swarm AI Ultra Cluster (Rp 25M)',
                    'ai_swarm_specs' => [
                        '8 Parallel AI Subagents untuk refactoring real-time dan automatic code auditing',
                        'Context Window: Maximum 2M Tokens Full-Repository Context',
                        'Alokasi Komputasi Priority Uncapped',
                    ],
                    'description' => 'Kombinasi tenaga ahli senior dan kluster AI agent Gemini Ultra untuk meluncurkan sistem enterprise berstandar industri dalam 14 hari kerja.',
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
                    'ai_quota_spec' => 'Gemini Ultra Uncapped Swarm + Dedicated 24/7 Engineering Shift',
                    'squad_allocation' => 'Dedicated Tri-Engineer War Room (24/7 Shift Rotation)',
                    'cost_formula' => 'Base Enterprise (Rp 75M) + Swarm AI Ultra Uncapped (Rp 30M) + War Room Tri-Shift 24/7 (Rp 20M)',
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
                'name' => 'Enterprise High-Speed Sprint (Gemini Ultra)',
                'duration' => '21 Hari Kerja',
                'badge' => 'SCALE_UP // HIGH_SPEED',
                'speed_multiplier' => '2.0x (Skala Komersial)',
                'contract_amount' => 50000000.00,
                'dp_amount' => 25000000.00,
                'pelunasan_amount' => 25000000.00,
                'ai_quota_spec' => 'PostgreSQL 16+ pgvector AI Ready + Gemini Swarm Acceleration',
                'squad_allocation' => 'Principal Architect + 2 Senior Engineers',
                'cost_formula' => 'Base Enterprise (Rp 35M) + AI Ultra Swarm & Concurrency (Rp 15M)',
                'ai_swarm_specs' => [
                    'Arsitektur High-Concurrency berskala jutaan data dengan keyset pagination O(1)',
                    'Integrasi pgvector AI database untuk pencarian semantik cerdas',
                    'SLA Prioritas & Garansi Pemeliharaan Penuh 60 Hari',
                ],
                'description' => 'Solusi berdaya tahan tinggi bagi entitas bisnis yang siap bersaing di pasar komersial dengan volume transaksi masif.',
            ],
        ];
    }
}
