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
        // Check for arrow separators: -> or => or newline numbers
        if (str_contains($text, '->')) {
            $steps = explode('->', $text);
        } elseif (str_contains($text, '=>')) {
            $steps = explode('=>', $text);
        } else {
            $steps = preg_split('/[\r\n]+/', $text);
        }

        $stages = [];
        $index = 1;
        foreach ($steps as $step) {
            $step = trim($step, " \t\n\r\0\x0B-•*1234567890.)");
            if (empty($step)) {
                continue;
            }
            $stages[] = [
                'step' => $index++,
                'action' => $step,
                'description' => 'Tahapan validasi dan transmisi data alur kerja.',
            ];
        }

        if (empty($stages)) {
            $stages = [
                ['step' => 1, 'action' => 'Akses Portal & Registrasi', 'description' => 'Pengguna membuka aplikasi dan memasukkan kredensial / identitas.'],
                ['step' => 2, 'action' => 'Pengisian Data / Form Transaksi', 'description' => 'Validasi sisi klien dan transmisi ke backend Laravel.'],
                ['step' => 3, 'action' => 'Verifikasi & Notifikasi Otomatis', 'description' => 'Sistem mengirimkan konfirmasi instan dan mencatat audit trail.'],
                ['step' => 4, 'action' => 'Approval & Manajemen Dasbor Admin', 'description' => 'Pengelola memproses data melalui tabel Filament berkecepatan tinggi.'],
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
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'Unique Lexicographically Sortable ID'],
                    ['name' => 'name', 'type' => 'string(255)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Nama lengkap pengguna'],
                    ['name' => 'email', 'type' => 'string(255)', 'index' => 'UNIQUE', 'nullable' => false, 'notes' => 'Email unik untuk login'],
                    ['name' => 'password', 'type' => 'string(255)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Hashed Argon2id / Bcrypt'],
                    ['name' => 'role', 'type' => 'string(50)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'superadmin | staff | client'],
                    ['name' => 'is_active', 'type' => 'boolean', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Status aktif akun'],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Waktu pembuatan akun'],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'index' => 'NONE', 'nullable' => true, 'notes' => 'Waktu modifikasi'],
                ],
            ],
            [
                'name' => $domainSlug,
                'description' => 'Entitas data bisnis utama yang mengelola transaksi / formulir input spesifik proyek.',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'ULID primary key'],
                    ['name' => 'user_id', 'type' => 'foreignUlid', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Relasi ke tabel users.id'],
                    ['name' => 'code_reference', 'type' => 'string(50)', 'index' => 'UNIQUE', 'nullable' => false, 'notes' => 'Nomor referensi / resi otomatis'],
                    ['name' => 'title', 'type' => 'string(255)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Judul / Nama entitas data'],
                    ['name' => 'data_payload', 'type' => 'jsonb', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Payload dinamis format JSON valid'],
                    ['name' => 'status', 'type' => 'string(50)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'draft | pending | approved | completed'],
                    ['name' => 'verified_at', 'type' => 'timestamp', 'index' => 'NONE', 'nullable' => true, 'notes' => 'Waktu verifikasi approval'],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Keyset cursor pointer'],
                ],
            ],
            [
                'name' => 'activity_logs',
                'description' => 'Pencatatan riwayat audit (audit trail) untuk keamanan dan akuntabilitas sistem.',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'ULID audit record'],
                    ['name' => 'user_id', 'type' => 'foreignUlid', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Aktor yang melakukan aksi'],
                    ['name' => 'action', 'type' => 'string(100)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'create | update | delete | approve'],
                    ['name' => 'target_table', 'type' => 'string(100)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Nama tabel sasaran'],
                    ['name' => 'changes', 'type' => 'jsonb', 'index' => 'NONE', 'nullable' => true, 'notes' => 'Snapshot diff data sebelum & sesudah'],
                    ['name' => 'ip_address', 'type' => 'string(45)', 'index' => 'NONE', 'nullable' => true, 'notes' => 'Alamat IP pengguna'],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Timestamp kejadian'],
                ],
            ],
            [
                'name' => 'system_notifications',
                'description' => 'Log antrean notifikasi (Email / WhatsApp) untuk broadcast & status alert.',
                'primary_key' => 'id (ULID - VARCHAR 26)',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'index' => 'PRIMARY', 'nullable' => false, 'notes' => 'ULID notification identifier'],
                    ['name' => 'recipient', 'type' => 'string(255)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'Email atau Nomor WhatsApp'],
                    ['name' => 'channel', 'type' => 'string(20)', 'index' => 'NONE', 'nullable' => false, 'notes' => 'email | whatsapp | system_push'],
                    ['name' => 'message_body', 'type' => 'text', 'index' => 'NONE', 'nullable' => false, 'notes' => 'Isi konten notifikasi'],
                    ['name' => 'status', 'type' => 'string(30)', 'index' => 'INDEX', 'nullable' => false, 'notes' => 'queued | sent | failed'],
                    ['name' => 'sent_at', 'type' => 'timestamp', 'index' => 'NONE', 'nullable' => true, 'notes' => 'Waktu terkirim sukses'],
                    ['name' => 'created_at', 'type' => 'timestamp', 'index' => 'INDEX', 'nullable' => true, 'notes' => 'Waktu pemicu notifikasi'],
                ],
            ],
        ];
    }
}
