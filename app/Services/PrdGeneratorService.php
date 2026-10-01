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
            'architecture_evaluation' => self::evaluateArchitecture($businessName, $masalah, $mvpItems, $alurKerja),
            'velocity_pricing_options' => self::generateVelocityPricingOptions($targetWaktu),
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
        if (empty($domainSlug)) {
            $domainSlug = 'project_records';
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
    public static function evaluateArchitecture(string $businessName, string $masalah, array $mvpItems, string $alurKerja): array
    {
        return [
            'hosting_evaluation' => [
                'verdict' => 'Dedicated VPS (Mandatory Enterprise Standard)',
                'verdict_badge' => 'VPS_MANDATORY',
                'recommendation' => 'DEDICATED_VPS',
                'compute_weight_score' => '88/100 (High Compute & Worker Queue Required)',
                'shared_hosting' => [
                    'status' => 'REJECTED (TIDAK MEMADAI)',
                    'title' => 'Shared Hosting Tradisional (cPanel / Apache)',
                    'reasons' => [
                        'Ketiadaan Ekstensi Kernel pgvector: Shared hosting tidak mendukung kompilasi binary native C PostgreSQL untuk pgvector AI similarity search.',
                        'Timeout PHP max_execution_time (30-60 detik): Eksekusi prompt AI reasoning atau proses batch data akan diputus paksa oleh server hosting.',
                        'Ketiadaan Process Supervisor & Redis Queue: Tidak dapat menjalankan background worker 24/7 untuk notifikasi & audit log secara persistent.',
                        'Risiko Tenant Crowding: Pembagian resource CPU/RAM bersama ratusan situs lain rentan memicu crash saat traffic melonjak.',
                    ],
                ],
                'dedicated_vps' => [
                    'status' => 'RECOMMENDED (STANDAR WAJIB ENTERPRISE)',
                    'title' => 'Dedicated VPS (Nixpacks & Docker Containerization)',
                    'reasons' => [
                        'Isolasi Resource 100%: Alokasi CPU & RAM terdedikasi menjamin throughput data tinggi tanpa gangguan tenant lain.',
                        'Native PostgreSQL 16+ pgvector Support: Penyimpanan representasi vektor berdimensi tinggi untuk AI embeddings & semantic RAG.',
                        'Redis In-Memory Queue & Worker 24/7: Menjalankan pemrosesan background jobs asinkron tanpa batas timeout.',
                        'Nginx HTTP/2 Reverse Proxy & Cloudflare CDN: Latensi minimal dengan proteksi SSL otomatis dan isolasi container Docker.',
                    ],
                ],
            ],
            'architecture_pattern_evaluation' => [
                'verdict' => 'Modern Monolith (Laravel 13 + Filament v5 + Island Architecture)',
                'verdict_badge' => 'RAPID_MONOLITH',
                'recommendation' => 'MODERN_MONOLITH',
                'match_percentage' => '95% Optimal Architectural Match',
                'monolith' => [
                    'status' => 'OPTIMAL REKOMENDASI (95% MATCH)',
                    'title' => 'Modern Monolith Architecture',
                    'reasons' => [
                        'Eliminasi Network Latency: Komunikasi antar modul berjalan intra-process O(1) tanpa overhead HTTP network antar-microservices.',
                        'Pangkas Biaya Infrastruktur 60%: Satu kesatuan container deployment menghemat anggaran server staging & produksi dibanding kluster microservices.',
                        'Rapid Time-to-Market (3x Lebih Cepat): Skema database, API internal, dan Admin Dasbor Filament v5 langsung sinkron tanpa duplikasi skema.',
                        'Konsistensi Transaksi ACID: Menjamin integritas data tanpa kerumitan distributed transaction (2-Phase Commit / Saga Pattern) yang rawan data loss.',
                        'Island Architecture Frontend: Memberikan fluiditas interaksi 60fps setara SPA dengan stabilitas dan kecepatan SEO Server-Side Rendering.',
                    ],
                ],
                'decoupled' => [
                    'status' => 'NOT RECOMMENDED (OVERKILL UNTUK FASE 1)',
                    'title' => 'Decoupled / Microservices Pattern',
                    'reasons' => [
                        'Hanya diperlukan jika tim pengembang berjumlah lebih dari 10-15 engineer yang bekerja di repositori terpisah.',
                        'Menambah biaya operasional server terpisah (Backend API server + Frontend Next.js node cluster terpisah).',
                        'Meningkatkan latensi round-trip HTTP dan beban autentikasi token JWT di setiap request interaksi.',
                    ],
                ],
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
     * Generate tiered velocity pricing options with AI accelerator costs.
     */
    public static function generateVelocityPricingOptions(string $targetWaktu): array
    {
        return [
            [
                'id' => 'standard',
                'name' => 'Standard Velocity (Regular)',
                'duration' => '30 Hari Kerja',
                'badge' => 'STANDARD_SPRINT',
                'speed_multiplier' => '1.0x (Normal Pace)',
                'contract_amount' => 50000000.00,
                'dp_amount' => 25000000.00,
                'pelunasan_amount' => 25000000.00,
                'ai_quota_spec' => 'Gemini Pro standard reasoning assistant',
                'squad_allocation' => '1 Lead Fullstack Engineer + QA Reviewer',
                'description' => 'Pengerjaan reguler terencana dengan siklus sprint standar 30 hari kerja. Pilihan ideal untuk validasi konsep tanpa urgensi waktu ketat.',
            ],
            [
                'id' => 'fast_track',
                'name' => 'Fast-Track Sprint (Gemini Ultra Accelerator)',
                'duration' => '14 Hari Kerja',
                'badge' => '2X_SPEED // RECOMMENDED',
                'speed_multiplier' => '2.0x (Pangkas 50% Waktu)',
                'contract_amount' => 75000000.00,
                'dp_amount' => 37500000.00,
                'pelunasan_amount' => 37500000.00,
                'ai_quota_spec' => 'Gemini Ultra High-Reasoning AI Tokens + Deep Architecture Automation',
                'squad_allocation' => '2 Dedicated Senior Engineers + AI Agentic Pair Programming',
                'description' => 'Akselerasi peluncuran 2x lebih cepat (selesai dalam 2 pekan). Biaya tambahan mencakup alokasi kuota komputasi cloud Gemini Ultra untuk auto-generating boilerplate, automated unit test, dan dual-engineer parallel sprint.',
            ],
            [
                'id' => 'hyper_sprint',
                'name' => 'Hyper-Sprint Emergency (24/7 Squad)',
                'duration' => '7 Hari Kerja',
                'badge' => '4X_SPEED // EMERGENCY',
                'speed_multiplier' => '4.0x (Rilis 1 Pekan)',
                'contract_amount' => 100000000.00,
                'dp_amount' => 50000000.00,
                'pelunasan_amount' => 50000000.00,
                'ai_quota_spec' => 'Gemini Ultra Max-Context Window + 24/7 High-Capacity Inference',
                'squad_allocation' => 'Dedicated Tri-Engineer War Room (24/7 Shift Rotation)',
                'description' => 'Peluncuran darurat dalam 1 pekan kalender. Prioritas tertinggi dengan war-room engineering 24 jam non-stop dan kuota inferensi Gemini Ultra tak terbatas untuk integrasi kilat.',
            ],
        ];
    }
}
