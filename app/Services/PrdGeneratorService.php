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

        // Parse actors into structured array with intelligent role inference & RBAC normalization
        $actorItems = self::parseActors($aktor, $businessName, $audiens);

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

        // Generate Deep Granular Engineering Specs for MVP & Phase 2
        $detailedMvpSpecs = self::generateDetailedFeatureSpecs($mvpItems, $businessName, $actorItems, 'mvp');
        $detailedPhase2Specs = self::generateDetailedFeatureSpecs($phase2Items, $businessName, $actorItems, 'phase2');

        // Parse Workflow Stages with granular engineering decomposition
        $workflowStages = self::parseWorkflow($alurKerja, $businessName);

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
                'mvp_phase1' => $detailedMvpSpecs,
                'phase2_roadmap' => $detailedPhase2Specs,
            ],
            'engineering_specs' => [
                'mvp_specs' => $detailedMvpSpecs,
                'phase2_specs' => $detailedPhase2Specs,
                'anti_ai_slop_guidelines' => self::getAntiAiSlopDesignSystem(),
                'backend_scalability_manifesto' => self::getBackendScalabilityManifesto(),
                'agent_handoff_protocol' => self::getAgentHandoffProtocol($businessName),
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
        if (count($lines) === 1 && str_contains($lines[0], ',')) {
            $lines = explode(',', $lines[0]);
        }
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
     * Parse system actors with intelligent role inference and RBAC normalization.
     */
    protected static function parseActors(string $text, string $businessName = '', string $audiens = ''): array
    {
        $rawLines = preg_split('/[\r\n;]+/', trim($text));
        $normalizedLines = [];
        foreach ($rawLines as $l) {
            $l = trim($l);
            if (empty($l)) {
                continue;
            }
            if (str_contains($l, ',') && !str_contains($l, ':') && !str_contains($l, ' - ')) {
                $parts = explode(',', $l);
                foreach ($parts as $p) {
                    $subParts = preg_split('/\s+(?:dan|serta|and)\s+/i', trim($p));
                    foreach ($subParts as $sp) {
                        $sp = trim($sp, " \t\n\r\0\x0B-•*1234567890.)");
                        if (!empty($sp)) {
                            $normalizedLines[] = $sp;
                        }
                    }
                }
            } else {
                $normalizedLines[] = $l;
            }
        }

        $actors = [];
        $hasAdmin = false;

        foreach ($normalizedLines as $line) {
            $line = trim($line, " \t\n\r\0\x0B-•*1234567890.)");
            if (empty($line)) {
                continue;
            }

            $name = '';
            $role = '';

            if (str_contains($line, ':')) {
                [$n, $r] = explode(':', $line, 2);
                $name = trim($n);
                $role = trim($r);
            } elseif (str_contains($line, ' - ')) {
                [$n, $r] = explode(' - ', $line, 2);
                $name = trim($n);
                $role = trim($r);
            } elseif (preg_match('/^(.*?)\s*\((.*?)\)$/', $line, $matches)) {
                $name = trim($matches[1]);
                $role = trim($matches[2]);
            } else {
                $name = $line;
                $role = '';
            }

            if (empty($name)) {
                continue;
            }

            $lower = strtolower($name . ' ' . $role);
            if (str_contains($lower, 'admin')) {
                $hasAdmin = true;
            }

            if (empty($role) || strlen($role) < 10) {
                if (str_contains($lower, 'pembeli') || str_contains($lower, 'buyer') || str_contains($lower, 'konsumen') || str_contains($lower, 'pelanggan')) {
                    $role = "Menjelajahi katalog {$businessName}, melakukan pencarian & filter spesifikasi mendalam, mengajukan penawaran/inquiry, serta menerima notifikasi status pesanan secara real-time.";
                    $badge = 'PEMBELI / KLIEN';
                    $permissions = ['Katalog Publik', 'Filter Pencarian', 'Ajukan Order / Inquiry', 'Riwayat Pesanan'];
                } elseif (str_contains($lower, 'member') || str_contains($lower, 'mitra') || str_contains($lower, 'partner') || str_contains($lower, 'agen') || str_contains($lower, 'penjual')) {
                    $role = "Mengelola profil mitra terverifikasi, mempublikasikan listing/produk, memantau analitik performa komisi, dan berinteraksi langsung dengan calon pembeli.";
                    $badge = 'MITRA / MEMBER';
                    $permissions = ['Manajemen Profil', 'Posting Listing / Aset', 'Dasbor Mitra', 'Chat / Inquiry Inbound'];
                } elseif (str_contains($lower, 'superadmin') || str_contains($lower, 'owner') || str_contains($lower, 'direktur')) {
                    $role = "Memegang kendali penuh atas konfigurasi platform {$businessName}, manajemen peran Spatie Shield RBAC, audit log forensik, dan rekonsiliasi keuangan.";
                    $badge = 'SUPERADMIN';
                    $permissions = ['Full Access', 'Konfigurasi Sistem', 'RBAC Shield', 'Audit Log', 'Billing Settlement'];
                } elseif (str_contains($lower, 'staff') || str_contains($lower, 'operator') || str_contains($lower, 'admin')) {
                    $role = "Memproses validasi berkas pengguna harian, memverifikasi kelaikan transaksi, menindaklanjuti keluhan layanan, dan mengekspor laporan berkala.";
                    $badge = 'OPERATOR';
                    $permissions = ['Review Data Masuk', 'Verifikasi Dokumen', 'Ekspor PDF/Excel', 'Update Status Operasional'];
                } elseif (str_contains($lower, 'tamu') || str_contains($lower, 'guest') || str_contains($lower, 'pengunjung')) {
                    $role = "Mengakses halaman pendaratan (landing page), mengecek informasi umum, dan melakukan registrasi akun awal.";
                    $badge = 'GUEST / PUBLIK';
                    $permissions = ['Akses Landing Page', 'Baca Dokumentasi', 'Registrasi Akun'];
                } else {
                    $role = "Pengguna terdaftar dengan hak akses interaktif terhadap modul sistem {$businessName} sesuai batasan otorisasi.";
                    $badge = 'PENGGUNA';
                    $permissions = ['Akses Modul Inti', 'Input Form', 'Lihat Notifikasi'];
                }
            } else {
                if (str_contains($lower, 'superadmin')) {
                    $badge = 'SUPERADMIN';
                    $permissions = ['Full Access', 'Konfigurasi Sistem', 'RBAC Shield', 'Audit Log'];
                } elseif (str_contains($lower, 'admin') || str_contains($lower, 'staff') || str_contains($lower, 'operator')) {
                    $badge = 'ADMIN / STAFF';
                    $permissions = ['Review Data', 'Verifikasi Berkas', 'Ekspor Laporan'];
                } elseif (str_contains($lower, 'mitra') || str_contains($lower, 'member')) {
                    $badge = 'MITRA / MEMBER';
                    $permissions = ['Dasbor Mitra', 'Kelola Listing', 'Inquiry Inbound'];
                } else {
                    $badge = 'KLIEN / USER';
                    $permissions = ['Akses Formulir', 'Lihat Status', 'Riwayat Transaksi'];
                }
            }

            $actors[] = [
                'name' => $name,
                'title' => $name,
                'role' => $role,
                'desc' => $role,
                'badge' => $badge,
                'permissions' => $permissions,
            ];
        }

        if (empty($actors)) {
            $actors = [
                [
                    'name' => 'Superadmin',
                    'title' => 'Superadmin',
                    'role' => 'Akses penuh seluruh konfigurasi, audit log, otorisasi peran, dan data sistem.',
                    'desc' => 'Akses penuh seluruh konfigurasi, audit log, otorisasi peran, dan data sistem.',
                    'badge' => 'SUPERADMIN',
                    'permissions' => ['Full Control', 'System Config', 'RBAC Shield', 'Audit Log'],
                ],
                [
                    'name' => 'Staff / Operator',
                    'title' => 'Staff / Operator',
                    'role' => 'Memproses data masuk harian, verifikasi kelayakan berkas, dan ekspor laporan berkala.',
                    'desc' => 'Memproses data masuk harian, verifikasi kelayakan berkas, dan ekspor laporan berkala.',
                    'badge' => 'OPERATOR',
                    'permissions' => ['Data Processing', 'Document Verification', 'Report Export'],
                ],
                [
                    'name' => 'Pengguna / Klien',
                    'title' => 'Pengguna / Klien',
                    'role' => 'Mengakses fitur publik/portal, mengisi data formulir, dan melihat status riwayat transaksi.',
                    'desc' => 'Mengakses fitur publik/portal, mengisi data formulir, dan melihat status riwayat transaksi.',
                    'badge' => 'CLIENT',
                    'permissions' => ['Portal Access', 'Form Submission', 'Notification Stream'],
                ],
            ];
            $hasAdmin = true;
        }

        // Always ensure Superadmin is present for enterprise RBAC compliance
        if (!$hasAdmin) {
            array_unshift($actors, [
                'name' => 'Superadmin Platform',
                'title' => 'Superadmin Platform',
                'role' => "Kendali penuh atas seluruh tata kelola {$businessName}, verifikasi akun pengguna, pengaturan modul, pemantauan transaksi, dan audit trail.",
                'desc' => "Kendali penuh atas seluruh tata kelola {$businessName}, verifikasi akun pengguna, pengaturan modul, pemantauan transaksi, dan audit trail.",
                'badge' => 'SUPERADMIN',
                'permissions' => ['Akses Penuh / Root', 'Konfigurasi Global', 'RBAC Security Shield', 'Audit Forensik'],
            ]);
        }

        return $actors;
    }

    /**
     * Parse workflow text into sequenced step cards with deep engineering parameters.
     */
    protected static function parseWorkflow(string $text, string $businessName = ''): array
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

            $description = "Interaksi pengguna pada modul {$businessName} dengan transmisi data tervalidasi.";
            $trigger = "Pengguna menjalankan aksi '{$step}' melalui antarmuka web.";
            $systemProcess = "Controller memvalidasi request FormRequest, menjalankan query Eloquent berindeks, dan memperbarui state.";
            $outputState = "Antarmuka diperbarui secara reaktif (HTTP 200 OK) dengan notifikasi visual.";
            $edgeCase = "Penanganan validasi gagal dengan error state inline dan retry mechanism.";

            if (str_contains($lower, 'buka') || str_contains($lower, 'katalog') || str_contains($lower, 'lihat') || str_contains($lower, 'daftar')) {
                $actor = 'Pengguna / Pengunjung';
                $badge = 'DISCOVERY';
                $type = 'client';
                $description = "Pengguna mengakses portal publik {$businessName}, sistem memuat katalog dinamis dengan skeleton loader, dan melakukan prefetching aset CDN.";
                $trigger = "Pengunjung membuka URL beranda / katalog dari peramban desktop maupun ponsel.";
                $systemProcess = "HTTP GET route memanggil Controller, membaca Cache::rememberForever() untuk konfigurasi CMS & Schema.org JSON-LD, render Blade & React Islands.";
                $outputState = "Halaman ter-render instan (<100ms TTFB) dengan Core Web Vitals optimal (LCP < 1.2s, CLS = 0).";
                $edgeCase = "Jika terjadi gangguan jaringan, service worker menampilkan graceful offline fallback dan tombol muat ulang.";
            } elseif (str_contains($lower, 'filter') || str_contains($lower, 'cari') || str_contains($lower, 'search') || str_contains($lower, 'advance')) {
                $actor = 'Sistem / Search Engine';
                $badge = 'QUERY_FILTER';
                $type = 'filter';
                $description = "Pengguna menerapkan parameter filter lanjutan (kategori, rentang budget, spesifikasi). Sistem mengeksekusi query database terindeks secara instan.";
                $trigger = "Input teks pada kolom pencarian atau interaksi toggle/chip filter oleh pengguna.";
                $systemProcess = "Eksekusi Keyset Cursor Pagination O(1) dengan PostgreSQL B-Tree Indexing, debounce 300ms untuk menekan beban server.";
                $outputState = "Daftar entitas hasil filter diperbarui secara reaktif tanpa reload halaman penuh (Full Page Refresh = 0).";
                $edgeCase = "Jika hasil pencarian 0 record, sistem menampilkan rekomendasi cerdas dan tombol 'Reset Semua Filter'.";
            } elseif (str_contains($lower, 'langganan') || str_contains($lower, 'berlangganan') || str_contains($lower, 'order') || str_contains($lower, 'beli') || str_contains($lower, 'transaksi')) {
                $actor = 'Payment & Transaction Engine';
                $badge = 'TRANSACTION';
                $type = 'billing';
                $description = "Pengguna memilih paket berlangganan atau mengajukan transaksi. Sistem mengunci kalkulasi harga, menerbitkan invoice ber-ULID, dan membuka gateway pembayaran.";
                $trigger = "Klik tombol 'Berlangganan Sekarang' atau 'Checkout' oleh pengguna terautentikasi.";
                $systemProcess = "Penerbitan ULID transaksi baru, verifikasi kuota/stok dengan ACID database transaction, handshake API ke Payment Gateway (Midtrans Snap).";
                $outputState = "Snap payment modal terbuka di layar klien; database mencatat status PENDING_PAYMENT.";
                $edgeCase = "Idempotency key mencegah double charge jika klien menekan tombol bayar berulang kali.";
            } elseif (str_contains($lower, 'notifikasi') || str_contains($lower, 'email') || str_contains($lower, 'alert') || str_contains($lower, 'wa')) {
                $actor = 'Notification Engine';
                $badge = 'NOTIFICATION';
                $type = 'notification';
                $description = "Sistem mendistribusikan notifikasi status transaksi otomatis ke email profesional pengguna (Resend API) dan nomor WhatsApp.";
                $trigger = "Event model tersimpan (OrderSettled, InquiryCreated, atau StatusUpdated).";
                $systemProcess = "Dispatched asynchronous Job ke Redis Queue Workers, render email template responsif, fallback retry 3x dengan backoff eksponensial.";
                $outputState = "Notifikasi terkirim ke inbox email dan nomor kontak pengguna dalam tempo <5 detik.";
                $edgeCase = "Pencatatan kegagalan transmisi ke tabel `notification_logs` dan tombol Resend otomatis di panel admin.";
            } elseif (str_contains($lower, 'validasi') || str_contains($lower, 'simpan') || str_contains($lower, 'database') || str_contains($lower, 'isi')) {
                $actor = 'PostgreSQL / Laravel ORM';
                $badge = 'DATABASE';
                $type = 'database';
                $description = "Penyimpanan data formulir terstruktur dengan validasi ketat, sanitasi anti-XSS, dan penguncian relasi foreign key ber-ULID.";
                $trigger = "Pengiriman formulir via HTTP POST / Action Handler.";
                $systemProcess = "Sanitasi payload input, hashing berkas sensitif, penyimpanan ke tabel PostgreSQL Strict dengan ULID primary key.";
                $outputState = "Flash session atau toast notifikasi sukses muncul, audit trail log mencatat IP dan timestamp pengirim.";
                $edgeCase = "Penolakan instan dengan HTTP 422 Unprocessable Entity dan pemetaan pesan error spesifik jika input tidak valid.";
            } elseif (str_contains($lower, 'admin') || str_contains($lower, 'verifikasi') || str_contains($lower, 'dasbor') || str_contains($lower, 'approval')) {
                $actor = 'Administrator / Operator';
                $badge = 'APPROVAL';
                $type = 'admin';
                $description = "Pengelola meninjau dan memverifikasi data transaksi atau berkas masuk melalui panel kendali Filament v5 enterprise.";
                $trigger = "Admin membuka antarmuka manajemen data di dashboard `/admin`.";
                $systemProcess = "Verifikasi otorisasi Spatie Shield RBAC, evaluasi integritas dokumen, eksekusi approval dengan database lock.";
                $outputState = "Status record diperbarui menjadi APPROVED / ACTIVE, memicu event notifikasi tahap berikutnya.";
                $edgeCase = "Jika data ditolak, admin wajib menyertakan alasan penolakan yang otomatis diteruskan ke pengguna.";
            }

            $stages[] = [
                'step' => $index++,
                'action' => $step,
                'description' => $description,
                'actor' => $actor,
                'badge' => $badge,
                'type' => $type,
                'trigger' => $trigger,
                'system_process' => $systemProcess,
                'output_state' => $outputState,
                'edge_case' => $edgeCase,
            ];
        }

        if (empty($stages)) {
            $stages = [
                [
                    'step' => 1,
                    'action' => 'Akses Portal & Registrasi Akun',
                    'description' => 'Pengguna membuka aplikasi dan memasukkan kredensial identitas terotentikasi.',
                    'actor' => 'Pengguna',
                    'badge' => 'AUTH',
                    'type' => 'client',
                    'trigger' => 'Pengguna mengakses halaman login / register.',
                    'system_process' => 'Sanitasi kredensial, verifikasi password bcrypt, penerbitan session terenkripsi.',
                    'output_state' => 'Pengguna diarahkan ke dasbor utama dengan token sesi valid.',
                    'edge_case' => 'Rate limiter membatasi percobaan login maksimal 5x per menit.',
                ],
                [
                    'step' => 2,
                    'action' => 'Pengisian Data / Form Transaksi',
                    'description' => 'Validasi sisi klien dan transmisi data ke backend Laravel.',
                    'actor' => 'Klien / Sistem',
                    'badge' => 'INPUT',
                    'type' => 'client',
                    'trigger' => 'Pengguna mengisi formulir dan menekan submit.',
                    'system_process' => 'Validasi FormRequest, sanitasi XSS, penyimpanan ACID database.',
                    'output_state' => 'Toast sukses muncul dan record baru ber-ULID terbit.',
                    'edge_case' => 'Penolakan HTTP 422 jika format data tidak sesuai spesifikasi.',
                ],
                [
                    'step' => 3,
                    'action' => 'Verifikasi & Notifikasi Otomatis',
                    'description' => 'Sistem mengirimkan konfirmasi instan via email Resend dan mencatat audit trail.',
                    'actor' => 'Notification Engine',
                    'badge' => 'NOTIFICATION',
                    'type' => 'notification',
                    'trigger' => 'Event model tersimpan di database.',
                    'system_process' => 'Queue worker memproses pengiriman email asinkron.',
                    'output_state' => 'Email konfirmasi mendarat di inbox pengguna dalam hitungan detik.',
                    'edge_case' => 'Retry otomatis 3x jika koneksi API gateway mengalami timeout.',
                ],
                [
                    'step' => 4,
                    'action' => 'Approval & Manajemen Dasbor Admin',
                    'description' => 'Pengelola memproses data melalui tabel Filament berkecepatan tinggi.',
                    'actor' => 'Admin / Operator',
                    'badge' => 'APPROVAL',
                    'type' => 'admin',
                    'trigger' => 'Operator membuka panel antrean data baru.',
                    'system_process' => 'Pengecekan RBAC, review berkas, pembaruan status transaksi.',
                    'output_state' => 'Status berubah menjadi VERIFIED dan tercatat di audit log.',
                    'edge_case' => 'Log audit forensik mencatat alasan penolakan jika verifikasi dibatalkan.',
                ],
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

    /**
     * Generate granular engineering specifications for each feature.
     */
    protected static function generateDetailedFeatureSpecs(array $rawItems, string $businessName, array $actorItems, string $tier = 'mvp'): array
    {
        $specs = [];
        $index = 1;
        $prefix = ($tier === 'mvp') ? 'MVP' : 'ROADMAP';

        $primaryActor = $actorItems[0]['name'] ?? 'Superadmin';
        $clientActor = $actorItems[count($actorItems) - 1]['name'] ?? 'Pengguna / Klien';

        foreach ($rawItems as $item) {
            $title = $item['title'] ?? ('Fitur ' . $index);
            $desc = $item['desc'] ?? 'Spesifikasi fungsional inti untuk operasional sistem.';
            $slug = Str::slug($title);
            $cleanSlug = strtoupper(substr(str_replace('-', '_', $slug), 0, 14));
            $featureId = sprintf('FEAT-%s-%02d-%s', $prefix, $index, $cleanSlug);

            $analysis = self::analyzeFeatureDomain($title, $desc, $businessName, $clientActor, $primaryActor, $index, $prefix);

            $specs[] = [
                'id' => $featureId,
                'index' => $index,
                'tier' => $tier,
                'title' => $title,
                'desc' => $desc,
                'category' => $analysis['category'],
                'category_label' => $analysis['category_label'],
                'user_story' => $analysis['user_story'],
                'acceptance_criteria' => $analysis['acceptance_criteria'],
                'frontend' => $analysis['frontend'],
                'backend' => $analysis['backend'],
                'integration' => $analysis['integration'],
                'code_agent_directive' => $analysis['code_agent_directive'],
            ];

            $index++;
        }

        return $specs;
    }

    /**
     * Intelligently analyze feature domain and extract concrete engineering tasks.
     */
    protected static function analyzeFeatureDomain(string $title, string $desc, string $businessName, string $clientActor, string $primaryActor, int $index, string $prefix): array
    {
        $lower = strtolower($title . ' ' . $desc);
        $titleSlug = Str::slug($title);
        $modelName = Str::studly(Str::singular(explode('-', $titleSlug)[0] ?? 'Record'));
        if (in_array(strtolower($modelName), ['fitur', 'manajemen', 'sistem', 'data'])) {
            $modelName = 'BusinessRecord';
        }

        // Domain 1: AUTH & RBAC
        if (str_contains($lower, 'autentikasi') || str_contains($lower, 'login') || str_contains($lower, 'rbac') || str_contains($lower, 'user') || str_contains($lower, 'pengguna')) {
            return [
                'category' => 'SECURITY_RBAC',
                'category_label' => 'Otentikasi & Keamanan Akses',
                'user_story' => "Sebagai {$primaryActor} atau {$clientActor}, saya ingin dapat mengautentikasi diri secara aman dengan sesi terisolasi dan izin akses berbasis peran (RBAC), sehingga data sensitif organisasi terlindungi dari akses ilegal.",
                'acceptance_criteria' => [
                    [
                        'scenario' => 'Login Berhasil dengan Kredensial Valid',
                        'given' => "Pengguna telah terdaftar di database dengan peran yang sah ({$primaryActor} atau {$clientActor}).",
                        'when' => 'Pengguna memasukkan email dan kata sandi yang sesuai pada form login.',
                        'then' => 'Sistem mengautentikasi sesi, meregenerasi Session ID untuk mencegah session fixation, dan mengarahkan pengguna ke dasbor sesuai peran.',
                    ],
                    [
                        'scenario' => 'Blokir Akses Ilegal (RBAC Boundary)',
                        'given' => "Pengguna terautentikasi dengan peran {$clientActor}.",
                        'when' => 'Pengguna mencoba mengakses URL atau endpoint khusus milik Superadmin.',
                        'then' => 'Sistem menolak request dengan status HTTP 403 Forbidden, menampilkan halaman error yang ramah, dan mencatat log audit percobaan akses.',
                    ],
                    [
                        'scenario' => 'Proteksi Brute-Force Rate Limiting',
                        'given' => 'Form login menerima permintaan percobaan otentikasi bertubi-tubi.',
                        'when' => 'Terjadi 5 kali kegagalan login dalam kurun waktu 1 menit dari IP yang sama.',
                        'then' => 'Sistem mengunci sementara proses login selama 60 detik dengan respons HTTP 429 Too Many Requests.',
                    ],
                ],
                'frontend' => [
                    'design_tokens' => 'Form auth high-contrast (bukan template abu-abu hambar), surface zinc-900 di dark mode / crisp white di light mode, focus-ring emerald-500 dengan transition-all duration-200.',
                    'states' => 'Loading pulse bar pada tombol submit saat auth diproses, error banner inline dengan warna rose-500, auto-focus pada field email.',
                    'components' => ['LoginForm.blade.php', 'PasswordInputToggle.js', 'AuthToastNotifier.js'],
                    'tasks' => [
                        'Desain formulir otentikasi responsif dengan WCAG AAA contrast ratio.',
                        'Implementasikan toggle visibilitas kata sandi (eye icon SVG fluid).',
                        'Integrasikan notifikasi toast floating saat login gagal tanpa dialog modal native.',
                    ],
                ],
                'backend' => [
                    'model_and_migration' => 'Tabel `users` dengan `->ulid("id")->primary()`, kolom `role`, `status`, `remember_token`, timestamps.',
                    'scalability_guardrail' => 'Index pada kolom `email`, password hashing Argon2id / Bcrypt 12 rounds, keyset cursor pagination untuk listing user.',
                    'tasks' => [
                        'Buat migration tabel `users` dengan Primary Key ULID standar PostgreSQL.',
                        'Implementasikan Form Request `LoginRequest` dengan validasi email ketat dan sanitasi string.',
                        'Terapkan middleware `CheckRole` atau Filament Shield Policy untuk isolasi hak akses.',
                    ],
                ],
                'integration' => [
                    'endpoint' => 'POST /api/v1/auth/login & POST /logout',
                    'middleware' => ['web', 'throttle:5,1'],
                    'request_schema' => '{"email": "admin@' . Str::slug($businessName) . '.com", "password": "SecurePassword123!", "remember": true}',
                    'response_schema' => '{"success": true, "user": {"id": "01J...ULID", "name": "Admin", "role": "superadmin"}, "redirect_url": "/admin"}',
                    'tasks' => [
                        'Daftarkan route auth dengan pembatasan laju (throttling 5 request/menit).',
                        'Pastikan token CSRF divalidasi pada setiap request mutasi.',
                    ],
                ],
                'code_agent_directive' => self::buildAgentPrompt(
                    sprintf('FEAT-%s-%02d-AUTH_RBAC', $prefix, $index),
                    $title,
                    $businessName,
                    "Implementasikan modul Otentikasi & RBAC dengan Laravel 13, Filament v5, dan PostgreSQL Strict ULID.",
                    ['app/Models/User.php', 'database/migrations/xxxx_create_users_table.php', 'app/Http/Requests/Auth/LoginRequest.php', 'resources/views/auth/login.blade.php'],
                    "php artisan test --filter=AuthenticationTest"
                ),
            ];
        }

        // Domain 2: KATALOG / LISTING / PENCARIAN / FILTER
        if (str_contains($lower, 'katalog') || str_contains($lower, 'filter') || str_contains($lower, 'cari') || str_contains($lower, 'search') || str_contains($lower, 'produk') || str_contains($lower, 'properti') || str_contains($lower, 'daftar')) {
            return [
                'category' => 'DISCOVERY_CATALOG',
                'category_label' => 'Katalog & Pencarian O(1)',
                'user_story' => "Sebagai {$clientActor}, saya ingin menelusuri katalog data {$businessName} dengan filter dinamis dan pencarian instan, sehingga saya dapat menemukan informasi yang paling cocok dengan kebutuhan saya dalam waktu singkat.",
                'acceptance_criteria' => [
                    [
                        'scenario' => 'Filter Reaktif Sub-100ms Tanpa Reload Halaman Penuh',
                        'given' => 'Katalog memiliki data yang siap ditampilkan.',
                        'when' => 'Pengguna memilih parameter filter (kategori, kisaran harga, atau lokasi).',
                        'then' => 'Daftar item diperbarui secara reaktif dalam waktu <100ms dengan mempertahankan posisi scroll dan riwayat URL query string.',
                    ],
                    [
                        'scenario' => 'Ketersediaan Skeleton Loader dan Empty State',
                        'given' => 'Pencarian dilakukan dengan kata kunci spesifik.',
                        'when' => 'Data sedang diunduh dari server ATAU tidak ada hasil yang cocok.',
                        'then' => 'Sistem menampilkan Skeleton Loader shimmer (anti layout-shift) saat loading, atau Empty State empatik dengan tombol Reset Filter jika data nihil.',
                    ],
                    [
                        'scenario' => 'Stabilitas Paginasi Keyset Cursor O(1)',
                        'given' => 'Katalog menampung puluhan ribu data.',
                        'when' => 'Pengguna melakukan navigasi halaman selanjutnya (Load More).',
                        'then' => 'Sistem menggunakan cursorPaginate() berbasis pointer ID ULID sehingga performa query database tetap O(1) konstan tanpa beban memory leak.',
                    ],
                ],
                'frontend' => [
                    'design_tokens' => 'Grid asimetris modern (1 kolom mobile, 2 kolom tablet, 3-4 kolom desktop), aspect-ratio 16:9/4:3 gambar dengan object-cover (anti squish), badge kategori HSL berkarakter.',
                    'states' => 'Skeleton cards beranimasi shimmer saat fetch data, hover card lift effect (hover:-translate-y-1 hover:shadow-lg transition-all duration-300), empty state dengan tombol Reset.',
                    'components' => ['CatalogGrid.blade.php', 'CatalogCard.blade.php', 'CatalogFilterBar.blade.php', 'SkeletonCard.blade.php'],
                    'tasks' => [
                        'Bangun komponen grid katalog yang mobile-first dan responsif.',
                        'Implementasikan filter drawer interaktif dengan binding URL query string.',
                        'Sediakan skeleton loader shimmer untuk mencegah cumulative layout shift (CLS = 0).',
                    ],
                ],
                'backend' => [
                    'model_and_migration' => "Tabel `catalog_items` dengan `->ulid('id')->primary()`, indexes compound `[status, created_at]`, foreignUlid, soft-deletes.",
                    'scalability_guardrail' => "Hindari standard paginate() (OFFSET). Wajib gunakan cursorPaginate() dengan ->orderBy('id', 'desc') untuk stabilitas kursor O(1).",
                    'tasks' => [
                        'Buat migration tabel katalog dengan ULID primary key dan indeks pada kolom-kolom filter.',
                        'Implementasikan Query Scope `scopeFilter()` di Model untuk menangani parameter pencarian secara efisien.',
                        'Pastikan eager loading `with(...)` diterapkan untuk mematikan ancaman N+1 query problem.',
                    ],
                ],
                'integration' => [
                    'endpoint' => 'GET /api/v1/catalog?cursor=...&category=...&q=...',
                    'middleware' => ['web', 'throttle:60,1'],
                    'request_schema' => '{"q": "Jakarta", "category_id": "01J...", "min_price": 50000000, "cursor": "eyJpZCI..."}',
                    'response_schema' => '{"data": [{"id": "01J...", "title": "Unit A", "price": 150000000}], "next_cursor": "eyJpZCI...", "has_more": true}',
                    'tasks' => [
                        'Daftarkan endpoint API / Livewire Action dengan response JSON terformat.',
                        'Integrasikan ETag dan HTTP Cache-Control (max-age=60) untuk optimasi bandwidth.',
                    ],
                ],
                'code_agent_directive' => self::buildAgentPrompt(
                    sprintf('FEAT-%s-%02d-CATALOG_FILTER', $prefix, $index),
                    $title,
                    $businessName,
                    "Bangun modul Katalog & Filter Data Interaktif dengan Keyset Cursor Pagination O(1) dan UI Anti-AI-Slop.",
                    ['app/Models/CatalogItem.php', 'database/migrations/xxxx_create_catalog_items_table.php', 'app/Http/Controllers/CatalogController.php', 'resources/views/catalog/index.blade.php'],
                    "php artisan test --filter=CatalogFilterTest"
                ),
            ];
        }

        // Domain 3: INTAKE / FORMULIR / PESANAN / RESERVASI / TRANSAKSI / DOA
        if (str_contains($lower, 'form') || str_contains($lower, 'intake') || str_contains($lower, 'reservasi') || str_contains($lower, 'pesan') || str_contains($lower, 'transaksi') || str_contains($lower, 'doa') || str_contains($lower, 'warta') || str_contains($lower, 'konsultasi')) {
            return [
                'category' => 'DATA_INTAKE_TRANSACTION',
                'category_label' => 'Formulir Intake & Transaksi Terverifikasi',
                'user_story' => "Sebagai {$clientActor}, saya ingin mengisi formulir intake/reservasi dengan alur yang terpandu dan validasi instan, sehingga permintaan saya tercatat akurat dan langsung mendapatkan konfirmasi resmi.",
                'acceptance_criteria' => [
                    [
                        'scenario' => 'Validasi Data Masukan Secara Real-Time',
                        'given' => 'Pengguna sedang mengisi kolom nomor telepon, email, dan data wajib.',
                        'when' => 'Pengguna mengetik format yang keliru (misal menginput angka 0 di awal nomor WhatsApp ber-country code).',
                        'then' => 'Sistem otomatis memfilter atau menampilkan petunjuk inline ramah sebelum formulir dikirim.',
                    ],
                    [
                        'scenario' => 'Pencegahan Double-Submit via Idempotency Key',
                        'given' => 'Formulir siap dikirim dengan payload valid.',
                        'when' => 'Pengguna menekan tombol submit berkali-kali karena koneksi internet lambat.',
                        'then' => 'Sistem memproses request pertama dengan token idempotency unik, mendisabled tombol, dan mengabaikan request duplikat tanpa error.',
                    ],
                    [
                        'scenario' => 'Pencatatan Transaksi & Penerbitan Kode Referensi',
                        'given' => 'Data formulir berhasil lolos validasi server-side.',
                        'when' => 'Transaksi disimpan ke database.',
                        'then' => 'Sistem membungkus operasi dalam DB::transaction, menghasilkan kode referensi unik, mencatat IP & timestamp audit trail, serta mendispatch event background notifikasi.',
                    ],
                ],
                'frontend' => [
                    'design_tokens' => 'Floating input labels, selector kode negara bendera interaktif, step progress bar indikatif, validasi error inline dengan aksen rose-500, focus ring emerald-500.',
                    'states' => 'Tombol kirim memiliki state loading spinner micro, konfirmasi sukses menggunakan modal modern backdrop-blur atau redirect halaman terima kasih ber-QR code.',
                    'components' => ['IntakeForm.blade.php', 'PhoneCountryCodePicker.blade.php', 'StepProgressBar.blade.php'],
                    'tasks' => [
                        'Buat form intake multi-langkah yang nyaman diakses lewat smartphone.',
                        'Pasang filter anti angka 0 di awal untuk nomor telepon setelah country code.',
                        'Tampilkan feedback konfirmasi menggunakan floating Toast sistem, bukan alert native.',
                    ],
                ],
                'backend' => [
                    'model_and_migration' => "Tabel `transactions` / `intakes` dengan `->ulid('id')->primary()`, `reference_code` unique, `status` enum, audit metadata JSONB.",
                    'scalability_guardrail' => 'Gunakan DB::transaction untuk integritas data ACID, dispatch event asinkron agar response time HTTP tetap <80ms.',
                    'tasks' => [
                        'Migration tabel intake dengan Primary Key ULID dan foreign keys tervalidasi.',
                        'Form Request `StoreIntakeRequest` dengan sanitasi anti-XSS dan rules ketat.',
                        'Action class `CreateIntakeAction` yang menangani logika bisnis secara terisolasi.',
                    ],
                ],
                'integration' => [
                    'endpoint' => 'POST /api/v1/intake',
                    'middleware' => ['web', 'throttle:15,1'],
                    'request_schema' => '{"name": "Budi Santoso", "country_code": "62", "phone": "81234567890", "notes": "Pengajuan minat unit A"}',
                    'response_schema' => '{"success": true, "reference_code": "NPRO-TRX-2026-9812", "message": "Pendaftaran berhasil dicatat."}',
                    'tasks' => [
                        'Daftarkan endpoint API intake dengan header `X-Idempotency-Key`.',
                        'Hubungkan submission dengan queue worker untuk pengiriman notifikasi instan.',
                    ],
                ],
                'code_agent_directive' => self::buildAgentPrompt(
                    sprintf('FEAT-%s-%02d-DATA_INTAKE', $prefix, $index),
                    $title,
                    $businessName,
                    "Bangun modul Formulir Intake & Transaksi dengan validasi server-side ketat, idempotency guard, dan UI modern.",
                    ['app/Models/Intake.php', 'database/migrations/xxxx_create_intakes_table.php', 'app/Http/Requests/StoreIntakeRequest.php', 'app/Actions/CreateIntakeAction.php'],
                    "php artisan test --filter=IntakeSubmissionTest"
                ),
            ];
        }

        // Domain 4: DASBOR / ADMIN / GOVERNANCE / FILAMENT
        if (str_contains($lower, 'dasbor') || str_contains($lower, 'admin') || str_contains($lower, 'rekap') || str_contains($lower, 'kelola') || str_contains($lower, 'approval')) {
            return [
                'category' => 'GOVERNANCE_OPERATIONS',
                'category_label' => 'Dasbor Operasional & Tata Kelola',
                'user_story' => "Sebagai {$primaryActor}, saya ingin memantau KPI bisnis, meninjau data masuk, dan memproses status approval dalam satu panel kendali terpusat, sehingga operasional harian berjalan tanpa hambatan.",
                'acceptance_criteria' => [
                    [
                        'scenario' => 'Pemuatan Dasbor Agregasi Cepat (<150ms)',
                        'given' => 'Database memiliki puluhan ribu catatan transaksi.',
                        'when' => 'Admin membuka halaman utama dasbor operasional.',
                        'then' => 'Widget ringkasan metrik menampilkan data agregasi dari cache Redis / query terindeks tanpa menyebabkan slow query pada server.',
                    ],
                    [
                        'scenario' => 'Tabel Administrasi dengan Filter Cepat & Inline Actions',
                        'given' => 'Admin sedang mengelola antrean permohonan masuk.',
                        'when' => 'Admin memfilter berdasarkan status (Pending, Disetujui, Ditolak).',
                        'then' => 'Tabel Filament v5 menampilkan data relevan secara instan dengan aksi persetujuan (Approve/Reject) langsung dari baris tabel.',
                    ],
                    [
                        'scenario' => 'Audit Trail Pencatatan Setiap Aksi Perubahan',
                        'given' => 'Data penting mengalami perubahan status oleh staf admin.',
                        'when' => 'Perubahan disimpan ke sistem.',
                        'then' => 'Sistem mencatat identitas admin, alamat IP, timestamp waktu, dan diff nilai sebelum/sesudah di tabel audit trail.',
                    ],
                ],
                'frontend' => [
                    'design_tokens' => 'Antarmuka Filament v5 Enterprise dengan dark mode onyx / light mode crisp, widget card dengan indikator pertumbuhan tren persentase, status badge bersahabat.',
                    'states' => 'Skeleton table rows saat data berpindah halaman, modal dialog konfirmasi yang elegan untuk aksi destruktif (tanpa window.confirm).',
                    'components' => ['AdminResourceTable.php', 'StatsOverviewWidget.php', 'ActionConfirmationModal.blade.php'],
                    'tasks' => [
                        'Konfigurasi Filament v5 Resource lengkap dengan form input dan kolom tabel.',
                        'Tambahkan widget kartu statistik metrik utama (Total Data, Pending, Disetujui).',
                        'Terapkan custom badge semantik untuk indikasi status yang mudah terbaca.',
                    ],
                ],
                'backend' => [
                    'model_and_migration' => "Filament Resource Class `App\Filament\Resources\...Resource.php` dengan implementasi method form Schema v5.",
                    'scalability_guardrail' => "Wajib menggunakan signature `form(\Filament\Schemas\Schema \$form): \Filament\Schemas\Schema` (Filament v5). Terapkan eager loading relasi untuk mencegah N+1.",
                    'tasks' => [
                        'Buat Filament Resource dengan schema form dan table builder standar Filament v5.',
                        'Pasang Policy autorisasi hak akses berbasis peran pada Model terkait.',
                        'Optimasi query tabel admin dengan indeks kolom status dan created_at.',
                    ],
                ],
                'integration' => [
                    'endpoint' => 'GET /admin/resources & POST /admin/resources/action',
                    'middleware' => ['web', 'auth:web'],
                    'request_schema' => '{"action": "approve", "record_id": "01J...ULID", "reason": "Dokumen lengkap"}',
                    'response_schema' => '{"success": true, "message": "Status berhasil diperbarui."}',
                    'tasks' => [
                        'Integrasikan audit trail observer pada model untuk mencatat aktivitas admin.',
                        'Hubungkan trigger approval dengan background job dispatch notifikasi.',
                    ],
                ],
                'code_agent_directive' => self::buildAgentPrompt(
                    sprintf('FEAT-%s-%02d-ADMIN_OPS', $prefix, $index),
                    $title,
                    $businessName,
                    "Implementasikan Dasbor Administrasi Filament v5 dengan schema form aman dan tabel filter instan.",
                    ['app/Filament/Resources/RecordResource.php', 'app/Filament/Widgets/StatsOverview.php', 'app/Policies/RecordPolicy.php'],
                    "php artisan test --filter=AdminPanelAccessTest"
                ),
            ];
        }

        // Domain 5: EKSPOR / LAPORAN / PDF / EXCEL
        if (str_contains($lower, 'ekspor') || str_contains($lower, 'laporan') || str_contains($lower, 'pdf') || str_contains($lower, 'excel') || str_contains($lower, 'cetak')) {
            return [
                'category' => 'REPORTING_EXPORT',
                'category_label' => 'Pelaporan & Ekspor Data (PDF / Excel)',
                'user_story' => "Sebagai {$primaryActor}, saya ingin mengunduh laporan rekapitulasi data dalam format PDF dan Excel terformat rapi, sehingga saya dapat menyajikan data resmi ke pemangku kepentingan tanpa perlu merapikan dokumen manual.",
                'acceptance_criteria' => [
                    [
                        'scenario' => 'Ekspor Excel Tanpa Kehabisan Memori Server (Chunking)',
                        'given' => 'Terdapat ribuan baris data yang diekspor.',
                        'when' => 'Admin menekan tombol Ekspor Excel.',
                        'then' => 'Sistem men-streaming data menggunakan generator chunk (cursor), menghasilkan file .XLSX dengan header beku (freeze panes) dalam <3 detik tanpa lonjakan RAM.',
                    ],
                    [
                        'scenario' => 'Cetak Dokumen PDF Terstandarisasi',
                        'given' => 'Admin atau pengguna mencetak tanda bukti atau rangkuman laporan.',
                        'when' => 'File PDF diunduh.',
                        'then' => 'Tata letak mematuhi standar A4 cetak rapi, dilengkapi kop resmi, nomor halaman dinamis, dan verifikasi cryptographic hash atau QR code.',
                    ],
                ],
                'frontend' => [
                    'design_tokens' => 'Modal pemilih filter tanggal (Date Range Picker), dropdown format (Excel XLSX, PDF Dokumen) dengan icon representatif, download progress feedback.',
                    'states' => 'Tombol unduh beralih ke state animasi progress saat file disintesis, toast notifikasi sukses saat file mulai terunduh.',
                    'components' => ['ExportReportModal.blade.php', 'ReportFormatSelector.blade.php'],
                    'tasks' => [
                        'Buat modal dialog interaktif untuk memilih periode dan kolom ekspor.',
                        'Sediakan tombol unduh cepat dengan indikator visual proses unduhan.',
                    ],
                ],
                'backend' => [
                    'model_and_migration' => "Service class `App\Services\ReportExportService.php` dengan engine streaming (FastExcel / DomPDF).",
                    'scalability_guardrail' => 'Dilarang memuat seluruh koleksi ke memory array ($query->get()). Wajib gunakan $query->cursor() untuk membatasi konsumsi RAM <32MB.',
                    'tasks' => [
                        'Implementasikan export handler dengan streaming response langsung ke browser.',
                        'Desain template Blade khusus PDF (`resources/views/pdf/report.blade.php`) dengan styling ramah DomPDF.',
                    ],
                ],
                'integration' => [
                    'endpoint' => 'GET /admin/reports/export?format=xlsx&start_date=2026-01-01&end_date=2026-12-31',
                    'middleware' => ['web', 'auth:web'],
                    'request_schema' => 'Query parameters: format (xlsx|pdf), start_date, end_date, status',
                    'response_schema' => 'Binary file stream with Content-Disposition attachment',
                    'tasks' => [
                        'Daftarkan route download laporan dengan proteksi otorisasi admin.',
                    ],
                ],
                'code_agent_directive' => self::buildAgentPrompt(
                    sprintf('FEAT-%s-%02d-EXPORT_REPORT', $prefix, $index),
                    $title,
                    $businessName,
                    "Bangun fitur Ekspor Laporan PDF & Excel hemat memori menggunakan streaming cursor.",
                    ['app/Services/ReportExportService.php', 'app/Http/Controllers/ReportExportController.php', 'resources/views/pdf/report.blade.php'],
                    "php artisan test --filter=ReportExportTest"
                ),
            ];
        }

        // Domain 6: NOTIFIKASI & WHATSAPP GATEWAY
        if (str_contains($lower, 'notifikasi') || str_contains($lower, 'whatsapp') || str_contains($lower, 'gateway') || str_contains($lower, 'email') || str_contains($lower, 'wa')) {
            return [
                'category' => 'COMMUNICATION_DISPATCH',
                'category_label' => 'Notifikasi WhatsApp & Gateway Terotomasi',
                'user_story' => "Sebagai {$clientActor} dan {$primaryActor}, saya ingin menerima notifikasi status real-time via WhatsApp dan Email setiap kali ada aktivitas transaksi penting, sehingga seluruh pihak mendapatkan konfirmasi instan tanpa koordinasi manual.",
                'acceptance_criteria' => [
                    [
                        'scenario' => 'Pengiriman Notifikasi WhatsApp via Background Queue',
                        'given' => 'Terjadi event pemicu (misal formulir baru atau update status).',
                        'when' => 'Event didispatch oleh sistem.',
                        'then' => 'Pesan WhatsApp dikirimkan melalui background queue job dalam <5 detik tanpa memperlambat respon halaman web bagi pengguna.',
                    ],
                    [
                        'scenario' => 'Ketahanan Terhadap API Gateway Downtime (Retry Backoff)',
                        'given' => 'API provider WhatsApp mengalami network timeout atau error sementara.',
                        'when' => 'Job pengiriman notifikasi dieksekusi.',
                        'then' => 'Sistem otomatis mencoba ulang (retry 3x dengan jeda bertingkat) dan mencatat status di log sistem tanpa merusak data transaksi utama.',
                    ],
                ],
                'frontend' => [
                    'design_tokens' => 'Badge status notifikasi terkirim (Warna emerald untuk Terkirim, amber untuk Dalam Antrean, rose untuk Gagal kirim), preview template pesan.',
                    'states' => 'Indikator live sync status pesan di tabel admin.',
                    'components' => ['WhatsAppPreviewModal.blade.php', 'DeliveryStatusBadge.blade.php'],
                    'tasks' => [
                        'Tampilkan riwayat pengiriman notifikasi di panel admin.',
                        'Sediakan tombol kirim ulang pesan (Resend WhatsApp) jika terjadi kegagalan transmisi.',
                    ],
                ],
                'backend' => [
                    'model_and_migration' => "Tabel `notification_logs` dengan `->ulid('id')->primary()`, target_phone, message_body, status, error_details, timestamps.",
                    'scalability_guardrail' => 'Wajib menggunakan Laravel Queues (ShouldQueue) pada Redis/Database connection. Dilarang melakukan synchronous HTTP call di controller.',
                    'tasks' => [
                        'Buat Service class `WhatsAppGatewayService` dengan format nomor E.164 otomatis.',
                        'Buat Job class `SendWhatsAppNotificationJob` yang mengimplementasikan `ShouldQueue`.',
                    ],
                ],
                'integration' => [
                    'endpoint' => 'POST /api/webhooks/whatsapp/delivery-report',
                    'middleware' => ['api'],
                    'request_schema' => '{"message_id": "WA-12345", "status": "DELIVERED", "timestamp": 1790866000}',
                    'response_schema' => '{"success": true}',
                    'tasks' => [
                        'Daftarkan webhook listener untuk menerima status laporan pengiriman (DLR) dari gateway WhatsApp.',
                    ],
                ],
                'code_agent_directive' => self::buildAgentPrompt(
                    sprintf('FEAT-%s-%02d-WA_NOTIF', $prefix, $index),
                    $title,
                    $businessName,
                    "Bangun sistem Notifikasi WhatsApp asinkron dengan Queue Jobs dan mekanisme retry cerdas.",
                    ['app/Services/WhatsAppGatewayService.php', 'app/Jobs/SendWhatsAppNotificationJob.php', 'database/migrations/xxxx_create_notification_logs_table.php'],
                    "php artisan test --filter=WhatsAppNotificationTest"
                ),
            ];
        }

        // Domain 7: GENERAL BUSINESS CORE SPECIFICATION
        $cleanSlug = strtoupper(substr(str_replace('-', '_', $titleSlug), 0, 14));
        return [
            'category' => 'CORE_FEATURE',
            'category_label' => 'Modul Inti Bisnis',
            'user_story' => "Sebagai {$clientActor}, saya ingin menggunakan fitur {$title} pada sistem {$businessName}, sehingga {$desc}.",
            'acceptance_criteria' => [
                [
                    'scenario' => 'Eksekusi Fungsional Utama Berjalan Normal',
                    'given' => "Pengguna memiliki hak akses terhadap modul {$title}.",
                    'when' => 'Pengguna menjalankan alur utama fitur sesuai petunjuk sistem.',
                    'then' => 'Sistem memproses data secara akurat, menyimpan hasil dengan integritas ACID, dan memperbarui status tampilan secara reaktif.',
                ],
                [
                    'scenario' => 'Penanganan Error dan Validasi Input',
                    'given' => 'Data yang diberikan tidak memenuhi syarat spesifikasi.',
                    'when' => 'Data dikirimkan ke server.',
                    'then' => 'Sistem mengembalikan pesan peringatan yang jelas dan mencegah penyimpanan data yang rusak.',
                ],
            ],
            'frontend' => [
                'design_tokens' => 'Desain komponen berkarakter dengan tipografi tajam, margin konsisten, transisi interaksi halus, dan kompatibilitas dark/light mode.',
                'states' => 'Loading skeleton saat proses komputasi berlangsung, feedback aksi menggunakan sistem Toast, bukan dialog alert native.',
                'components' => [$modelName . 'Component.blade.php', $modelName . 'Card.blade.php'],
                'tasks' => [
                    "Bangun antarmuka pengguna untuk fitur {$title} dengan prinsip Anti-AI-Slop.",
                    'Pastikan pengalaman navigasi mulus di perangkat seluler maupun desktop.',
                ],
            ],
            'backend' => [
                'model_and_migration' => "Tabel `" . Str::snake(Str::plural($modelName)) . "` dengan `->ulid('id')->primary()`, proper indexation, soft deletes.",
                'scalability_guardrail' => 'Terapkan Keyset Cursor Pagination O(1) untuk seluruh endpoint listing data.',
                'tasks' => [
                    "Migration tabel untuk fitur {$title} dengan Primary Key ULID standar PostgreSQL.",
                    "Service atau Action class untuk merangkum logika bisnis secara independen.",
                ],
            ],
            'integration' => [
                'endpoint' => '/api/v1/' . Str::kebab(Str::plural($modelName)),
                'middleware' => ['web'],
                'request_schema' => '{"title": "Sample Record", "status": "active"}',
                'response_schema' => '{"success": true, "data": {"id": "01J...", "title": "Sample Record"}}',
                'tasks' => [
                    'Daftarkan endpoint RESTful atau Livewire Handler dengan kontrak data ketat.',
                ],
            ],
            'code_agent_directive' => self::buildAgentPrompt(
                sprintf('FEAT-%s-%02d-%s', $prefix, $index, $cleanSlug),
                $title,
                $businessName,
                "Implementasikan modul {$title} sesuai spesifikasi arsitektur Laravel 13, Filament v5, dan PostgreSQL ULID.",
                ['app/Models/' . $modelName . '.php', 'app/Actions/' . $modelName . 'Action.php'],
                "php artisan test --filter=" . $modelName . "Test"
            ),
        ];
    }

    /**
     * Build an exact copy-pasteable prompt template for AI Coding Agents.
     */
    protected static function buildAgentPrompt(string $featureId, string $title, string $businessName, string $mission, array $targetFiles, string $verifyCommand): string
    {
        $filesStr = implode("\n- ", $targetFiles);
        return <<<PROMPT
### AI CODE AGENT MISSION // SPEC-DRIVEN DIRECTIVE
**Feature ID**: `{$featureId}`
**Feature Title**: {$title}
**Project Scope**: {$businessName}

#### Context & Architectural Guardrails:
1. **Framework & Engine**: Laravel 13, Filament v5, Livewire 4, PostgreSQL 16+.
2. **Primary Key Standard**: ALWAYS use ULID (`->ulid('id')->primary()`) on business tables. NEVER use AUTO_INCREMENT, ->id(), or ->uuid().
3. **Pagination Rule**: ALWAYS use Keyset Cursor Pagination (`cursorPaginate()`) with `->orderBy('id', 'asc')`. NEVER use offset `paginate()`.
4. **Anti-AI-Slop & Precision UI**:
   - **Zero Native Dialogs**: Strict ban on `window.alert()`, `confirm()`, and `prompt()`. ALWAYS use centralized floating Toast (`window.showToast`) and curated Tailwind backdrop-blur modals.
   - **Subtle Round Corners**: Use thin, crisp borders/corners (`rounded-xs`, `rounded-sm`, max `rounded-md`). STRICTLY AVOID capsule/pill shapes (`rounded-full`).
   - **Interactive & 3D**: High-contrast typography pairing, loading skeletons, Framer Motion or Three.js 60fps micro-animations, avoiding generic AI slop.
   - **Thousand Separators**: Every number or currency >= 1,000 must use thousand separator masking (e.g. `10.000.000` / `10,000,000`).
   - **Local FontAwesome Icons**: Use representative local SVG icons (`config/fontawesome.php`), never external icon fonts with CDN latency.
5. **Multi-Language Architecture (Frontend & Backend)**:
   - **Backend**: Filament v5 native dual-language (English & Indonesia) with multilingual database columns stored as JSON (`{"id": "...", "en": "..."}`).
   - **Frontend**: 2-Tier Language support: Tier 1 Native ID/EN switcher + Tier 2 Google Translate plugin configured from backend admin.
6. **Decoupled CMS & Global Settings**:
   - **Modular Page Plugins**: Page features are built as configurable, educational, and user-friendly plugins.
   - **Isolated Global Layout**: Top/sidebar navigation, footer, and global alerts MUST be decoupled outside page plugins so they can be adjusted independently.
   - **Centralized Global Settings**: Manage Company Profile, Contact Info, Social Links, and SEO Schema.org with strict privacy (never exposing internal API secrets).
   - **Phone Inputs**: ALWAYS use Country Zone selector (`config/country_zones.php`, e.g. +62, +65, +1) with E.164 format.
7. **Bulletproof Scalability (Millions of Records & Viewers)**:
   - Cache forever (`Cache::rememberForever()`) on Redis/memory for CMS pages, global settings, and Schema.org.
   - Automatic Event-Driven Reset: Invalidate caches immediately on model `saved` and `deleted` hooks.
8. **SEO Optimization Standard**: Tab browser titles MUST follow `[NAMA DOMAIN - NAMA PAGE]` alongside comprehensive Schema.org JSON-LD structured data.
9. **Anti-AI Malware Security Suite**: Modern multi-layered defense (Honeypot bot traps, CSP, rate limiting) with educational feedback for visitors.
10. **Admin AI Agentic Engine**: Knowledge base, RAG (Retrieval-Augmented Generation), and native tool calling to prevent hallucination and empower executive decision-making.
11. **Filament v5 Form Rule**: Always use `\Filament\Schemas\Schema` method signature for `form()`.

#### Target Files to Create / Modify:
- {$filesStr}

#### Mission Description:
{$mission}

#### Verification & Quality Gate:
Execute the following verification command and ensure exit code 0 before marking the task complete:
```bash
{$verifyCommand}
```
PROMPT;
    }

    /**
     * Anti-AI-Slop Frontend Design System Guide.
     */
    public static function getAntiAiSlopDesignSystem(): array
    {
        return [
            'philosophy' => [
                'title' => 'Filosofi Desain Anti-AI-Slop & Precision UI/UX',
                'description' => 'Menolak estetika generik template AI (kartu ungu/biru gradien tanpa makna, typography tanpa hirarki kontras, ketiadaan micro-state, dan dialog browser native window.alert/confirm yang merusak kredibilitas profesional). Setiap elemen antarmuka dibangun dengan tujuan fungsional, ritme visual terukur, dan performa fluid 60fps.',
            ],
            'subtle_corners' => [
                'title' => 'Sudut Tipis & Presisi (Strict Ban on Capsule/Pill Shapes)',
                'description' => 'Gunakan round corner tipis dan presisi (rounded-none, rounded-xs, rounded-sm, max rounded-md). Dilarang keras menggunakan border-radius ekstrem atau efek kapsul (rounded-full / pill buttons) yang menimbulkan kesan murahan ("AI slop") dan membuang area klik fungsional.',
            ],
            'multilingual_two_tier' => [
                'title' => 'Dukungan Multi-Bahasa 2-Tier (Frontend & Backend Native JSON)',
                'description' => 'Backend Filament v5 dan Frontend mendukung Bahasa Inggris dan Indonesia secara native. Data konten multibahasa disimpan dalam database kolom JSON (title->id, title->en). Tier 1 menggunakan terjemahan native internal berpresisi tinggi. Tier 2 menyediakan plugin Google Translate di frontend dengan pilihan bahasa yang dapat dikontrol dari backend admin.',
            ],
            'country_zone_phone_inputs' => [
                'title' => 'Input Nomor Telepon dengan Country Zone Selector',
                'description' => 'Seluruh input telepon/WhatsApp wajib menggunakan Country Zone selector standar (berbasis config/country_zones.php, e.g. +62, +65, +1, +44, +81, +61) dengan validasi E.164 untuk mencegah nomor salah ketik atau data invalid.',
            ],
            'thousands_separator_formatting' => [
                'title' => 'Formatting Angka Ribuan (Thousand Separator UX)',
                'description' => 'Setiap angka nominal, currency, metrik statistik, atau kalkulasi yang mencapai ribuan wajib memiliki pemisah ribuan otomatis (titik "." untuk format Indonesia atau koma "," untuk format internasional) baik pada input mask maupun tampilan teks agar mudah dibaca.',
            ],
            'local_fontawesome_library' => [
                'title' => 'Library FontAwesome Lokal Tanpa Ketergantungan Eksternal',
                'description' => 'Seluruh icon representatif untuk navigasi, footer, global alert, dan plugin wajib menggunakan library FontAwesome lokal (config/fontawesome.php dan helper SVG lokal) guna menghindari delay CDN pihak ketiga dan mencegah layout broken saat offline/downtime.',
            ],
            'typography' => [
                'title' => 'Kurasi Tipografi & Skala Kontras',
                'display' => 'Plus Jakarta Sans / Outfit (Font Display berbobot tebal, geometris modern, tracking -0.02em untuk heading)',
                'body' => 'Inter / DM Sans (Font body ramah baca, 14-16px, line-height 1.6, batas panjang baris 65-75ch)',
                'mono' => 'Geist Mono / JetBrains Mono (Untuk data teknis, ID ULID, nominal uang, status badge, dan parameter API)',
            ],
            'color_tokens' => [
                'title' => 'Palet Warna HSL & Surface Hierarchy',
                'dark_mode' => 'Base: #09090b (zinc-950), Cards: #18181b (zinc-900), Border: #27272a (zinc-800)',
                'light_mode' => 'Base: #ffffff, Cards: #f4f4f5 (zinc-100), Border: #e4e4e7 (zinc-200)',
                'semantic_accents' => [
                    'emerald' => 'Status Aktif, Sukses, Verifikasi (Emerald-500)',
                    'amber' => 'Status Pending, Peringatan, Sprint Akselerasi (Amber-500)',
                    'rose' => 'Status Ditolak, Error, Aksi Destruktif (Rose-500)',
                    'cyan' => 'Status Discovery, Filter, Query Metadata (Cyan-500)',
                ],
            ],
            'interactive_and_3d' => [
                'title' => 'Web Design Interaktif, Intuitif & Elemen 3D Edukatif',
                'description' => 'Frontpage & landing page wajib interaktif dan intuitif, memanfaatkan canvas/WebGL/Three.js 3D teroptimasi jika relevan, micro-animations 60fps dengan Framer Motion / Alpine.js, dan skema warna curated non-generik yang memukau pengguna pada pandangan pertama.',
            ],
            'cms_plugins_and_isolated_layout' => [
                'title' => 'Arsitektur CMS Modular Berbasis Plugin & Layout Terisolasi',
                'description' => 'Setiap section pada frontpage dan sub-page dibangun sebagai plugin independen yang dapat diatur via admin panel. Navigasi global (topbar, sidebar), footer, dan global alert WAJIB terpisah di luar plugin page agar konsisten dan dapat disesuaikan secara sentral.',
            ],
            'educational_plugin_ux' => [
                'title' => 'Prinsip Desain Plugin: Edukatif & User-Friendly',
                'description' => 'Setiap plugin tidak boleh sekadar menampilkan teks statis, melainkan menyajikan informasi kontekstual, panduan alur interaktif, tooltip penjelasan istilah, dan onboarding intuitif yang mendidik pengguna.',
            ],
            'component_primitives' => [
                'primitives' => 'Headless primitives (Radix UI / Flux UI / Alpine.js) untuk menjamin aksesibilitas WAI-ARIA penuh tanpa bloating bundle JS.',
                'states' => 'Setiap komponen input dan tombol wajib memiliki 5 status visual: default, hover, focus-visible, loading (micro-spinner/pulse), disabled.',
                'skeletons' => 'Skeleton loader shimmer effect beranimasi pulse dengan ukuran proporsional untuk mencegah Cumulative Layout Shift (CLS = 0).',
                'empty_states' => 'Empty state empatik dengan ilustrasi vektor minimalis, headline informatif, dan tombol aksi pemulihan langsung (bukan teks "Data Kosong" hambar).',
                'dialog_and_toasts' => '100% melarang window.alert() dan window.confirm(). Wajib menggunakan centralized floating Toast notification system dan backdrop-blur Tailwind dialog modals.',
            ],
        ];
    }

    /**
     * Backend Scalability Manifesto.
     */
    public static function getBackendScalabilityManifesto(): array
    {
        return [
            'primary_keys' => [
                'rule' => 'Strict ULID Primary Keys (VARCHAR 26)',
                'explanation' => 'Seluruh tabel bisnis wajib menggunakan ->ulid("id")->primary(). Mencegah sequence lock contention di PostgreSQL, ramah partisi database terdistribusi, dan aman dari tebakan ID sekuensial oleh scraper luar.',
            ],
            'pagination' => [
                'rule' => 'Keyset Cursor-Based Pagination O(1)',
                'explanation' => 'NEVER use offset-based paginate(). Always use cursorPaginate() with explicit keyset pointers (->orderBy("id", "asc")). Menjamin query tetap berkecepatan sub-10ms meskipun tabel mencapai jutaan baris data.',
            ],
            'modern_php' => [
                'rule' => 'PHP 8.4/8.5 Standards & Property Hooks',
                'explanation' => 'Memanfaatkan Property Hooks, First-Class Callables, Typed DTOs, dan Form Requests untuk menjamin integritas tipe data dan mencegah type-juggling bugs.',
            ],
            'concurrency' => [
                'rule' => 'Redis-Backed Queue Workers & Idempotency Keys',
                'explanation' => 'Seluruh pemrosesan asinkron (notifikasi WhatsApp, email, ekspor laporan, integrasi payment gateway) didelegasikan ke Redis Queue Workers dengan retry backoff 3x dan header X-Idempotency-Key.',
            ],
            'cms_page_forever_cache' => [
                'rule' => 'CMS Page Bulletproof Cache Forever (Million Viewers Resilience)',
                'explanation' => 'Setiap landing page dan sub-page yang dirender oleh CMS wajib memanfaatkan Cache::rememberForever() berbasis Redis/file cache untuk melayani jutaan hit dengan latensi sub-1ms (O(1)). Cache otomatis di-flush menggunakan Eloquent Model Observer (booted: saved & deleted) pada CmsPage dan CmsGlobalSetting begitu terjadi perubahan data.',
            ],
            'centralized_global_settings' => [
                'rule' => 'Global Setting Backend Admin dengan Aturan Privasi Ketat',
                'explanation' => 'Menyediakan modul CmsGlobalSetting di backend admin untuk mengatur Company Profile, Social Links, Hotline Kontak, dan SEO Schema.org yang digunakan berulang kali di frontend. Sistem menerapkan isolasi privasi ketat: credential rahasia/API keys tidak boleh diekspos ke frontend publik.',
            ],
            'schema_org_and_cache' => [
                'rule' => 'Mandatory Schema.org Structured Data & Admin Backend Adjustments',
                'explanation' => 'Setiap entitas publik wajib mengekspos Schema.org JSON-LD (Organization, WebSite, SoftwareApplication, Product, Breadcrumbs). Disediakan modul backend admin untuk mengkustomisasi payload Schema.org. Seluruh data Schema.org WAJIB di-cache menggunakan Cache::rememberForever() dan otomatis di-reset saat ada perubahan data di admin.',
            ],
            'seo_page_title_format' => [
                'rule' => 'Optimasi SEO Tab Browser [NAMA DOMAIN - NAMA PAGE]',
                'explanation' => 'Setiap halaman wajib mengimplementasikan struktur judul browser standar industri: [NAMA DOMAIN - NAMA PAGE] (e.g. "neriahpro.com - Layanan Arsitektur Perangkat Lunak"), dilengkapi meta tags Open Graph, Twitter Cards, canonical URL, dan Schema.org JSON-LD.',
            ],
            'anti_ai_malware_security' => [
                'rule' => 'Pertahanan Berlapis Anti-AI Malware & Bot Scraping Edukatif',
                'explanation' => 'Implementasi standar keamanan siber terlengkap untuk menangkal AI Malware, Honeypot traps untuk automated AI scrapers, rate-limiting adaptif, Content Security Policy (CSP) ketat, validasi tanda tangan HMAC, serta edukasi keamanan interaktif bagi pengunjung website.',
            ],
            'admin_ai_agentic_suite' => [
                'rule' => 'Fitur AI Agentic Cerdas di Backend Admin (RAG, Knowledge Base & Tools Calling)',
                'explanation' => 'Backend admin wajib dilengkapi mesin AI Agentic yang mengintegrasikan Knowledge Base terstruktur, Retrieval-Augmented Generation (RAG) untuk mencegah halusinasi, dan Native Tool Calling (database query inspection, system health audit, automated reporting) guna membantu admin dalam pengambilan keputusan strategis.',
            ],
        ];
    }

    /**
     * AI Code Agent Handoff Protocol (Zero Context-Rot Strategy).
     */
    public static function getAgentHandoffProtocol(string $businessName): array
    {
        return [
            'title' => 'Protokol Handoff AI Code Agent (Anti Context-Rot)',
            'objective' => "Menjamin AI Code Agent (Cursor Composer, Claude Code, GitHub Copilot, Antigravity, Aider) mengimplementasikan fitur {$businessName} secara presisi tanpa halusinasi, amnesia arsitektur, atau modifikasi file yang tidak diinginkan.",
            'rules' => [
                [
                    'rule' => '1. Vertical Slice Prompting (1 Prompt = 1 Fitur Vertikal)',
                    'desc' => 'Jangan pernah memasukkan seluruh PRD ke dalam satu prompt raksasa. Jalankan pengerjaan per modul vertikal (Database -> Model -> Action -> UI -> Test) menggunakan directive prompt khusus yang telah disediakan di tiap kartu fitur.',
                ],
                [
                    'rule' => '2. Explicit Bounded File Context',
                    'desc' => 'Cantumkan target file yang boleh dibuat atau dimodifikasi secara spesifik. Larang agen mengedit file global di luar batas modul.',
                ],
                [
                    'rule' => '3. Machine-Readable Acceptance Contracts',
                    'desc' => 'Gunakan skenario Gherkin (Given-When-Then) dan schema request/response JSON yang tercantum di PRD sebagai patokan kebenaran mutlak. Agen tidak boleh menebak payload.',
                ],
                [
                    'rule' => '4. Automated Verification Quality Gate',
                    'desc' => 'Wajibkan agen menjalankan perintah verifikasi terminal otomatis (cth: php artisan test --filter=... dan npm run build) dan memastikan exit code 0 sebelum menandai task selesai.',
                ],
            ],
        ];
    }

    /**
     * Convert entire Vision Blueprint PRD into a complete, professional Markdown document.
     */
    public static function toMarkdown(VisionBlueprint $blueprint, array $prd): string
    {
        $meta = $prd['meta'] ?? [];
        $exec = $prd['executive_summary'] ?? [];
        $actors = $prd['system_actors'] ?? [];
        $mvpFeatures = $prd['features']['mvp_phase1'] ?? [];
        $phase2Features = $prd['features']['phase2_roadmap'] ?? [];
        $workflow = $prd['workflow'] ?? [];
        $erd = $prd['erd_schema']['tables'] ?? [];
        $tech = $prd['tech_stack'] ?? [];
        $governance = $prd['governance_and_sla'] ?? [];
        $pricing = $prd['velocity_pricing_options'] ?? [];
        $slop = self::getAntiAiSlopDesignSystem();
        $scalability = self::getBackendScalabilityManifesto();
        $handoff = self::getAgentHandoffProtocol($blueprint->nama_bisnis ?: $blueprint->client_name);

        $projectName = $blueprint->nama_bisnis ?: ($blueprint->client_name . "'s Project");
        $specId = strtoupper(substr($blueprint->id, 0, 10));

        $md = "# ULTIMATE PRODUCT REQUIREMENTS DOCUMENT (PRD) & SYSTEM BLUEPRINT\n";
        $md .= "## {$projectName}\n\n";

        $md .= "> **SPEC_ID**: `{$specId}`  \n";
        $md .= "> **Client PIC**: {$blueprint->client_name} ({$blueprint->email})  \n";
        $md .= "> **Generated**: " . ($meta['generated_at'] ?? now()->toIso8601String()) . "  \n";
        $md .= "> **Target Timeline**: " . ($blueprint->target_waktu ?? '30 Hari Kerja') . "  \n";
        $md .= "> **Architecture Standard**: Modern Monolith (Laravel 13 + Filament v5 + PostgreSQL Strict ULID)\n\n";

        $md .= "---\n\n";

        // 1. Executive Discovery
        $md .= "## 1. Executive Technical Discovery & Problem Statement\n\n";
        $md .= "- **Masalah Utama**: " . ($exec['problem_statement'] ?? $blueprint->masalah_utama) . "\n";
        $md .= "- **Tujuan / Success Metrics**: " . ($exec['success_metrics'] ?? $blueprint->tujuan_utama) . "\n";
        $md .= "- **Target Audiens**: " . ($exec['target_audience'] ?? $blueprint->target_audiens) . "\n";
        $md .= "- **Target Skala**: " . ($exec['target_scale'] ?? '0 - 100.000 Pengguna / Bulan') . "\n";
        $md .= "- **Jangkauan Pasar**: " . ($exec['market_reach'] ?? 'Domestik Indonesia') . "\n";
        $md .= "- **Filosofi Arsitektur**: " . ($exec['architecture_philosophy'] ?? '') . "\n\n";

        // 2. System Actors
        $md .= "## 2. Aktor Sistem & Matriks Hak Akses (RBAC)\n\n";
        $md .= "| Aktor | Peran & Batasan Tanggung Jawab | Hak Akses & Permissions |\n";
        $md .= "|---|---|---|\n";
        foreach ($actors as $actor) {
            $name = $actor['name'] ?? $actor['title'] ?? 'Aktor Sistem';
            $role = $actor['role'] ?? $actor['desc'] ?? '-';
            $badge = $actor['badge'] ?? 'ROLE';
            $perms = !empty($actor['permissions']) ? ('`' . implode('`, `', $actor['permissions']) . '`') : 'Hak Akses Standar';
            $md .= "| **{$name}** (`{$badge}`) | {$role} | {$perms} |\n";
        }
        $md .= "\n";

        // 3. Feature Breakdown
        $md .= "## 3. Spesifikasi Rinci Fitur & Task Breakdown (Engineering Specs)\n\n";
        
        $md .= "### A. Fitur Wajib (Fase 1 - MVP Peluncuran)\n\n";
        foreach ($mvpFeatures as $f) {
            $fid = $f['id'] ?? 'FEAT-MVP';
            $ftitle = $f['title'] ?? 'Fitur MVP';
            $fcat = $f['category_label'] ?? ($f['category'] ?? 'CORE');
            $md .= "#### [{$fid}] {$ftitle}\n";
            $md .= "**Kategori**: `{$fcat}`  \n";
            $md .= "**Deskripsi**: " . ($f['desc'] ?? '-') . "  \n";
            $md .= "**User Story**: *" . ($f['user_story'] ?? '-') . "*\n\n";

            if (!empty($f['acceptance_criteria'])) {
                $md .= "**Acceptance Criteria (Gherkin Format)**:\n";
                foreach ($f['acceptance_criteria'] as $ac) {
                    $md .= "- **Skenario: " . ($ac['scenario'] ?? 'Skenario') . "**\n";
                    $md .= "  - **Given**: " . ($ac['given'] ?? '-') . "\n";
                    $md .= "  - **When**: " . ($ac['when'] ?? '-') . "\n";
                    $md .= "  - **Then**: " . ($ac['then'] ?? '-') . "\n";
                }
                $md .= "\n";
            }

            if (!empty($f['frontend'])) {
                $fe = $f['frontend'];
                $md .= "**Frontend Tasks (Anti-AI-Slop Specs)**:\n";
                $md .= "- **Design Tokens**: " . ($fe['design_tokens'] ?? '-') . "\n";
                $md .= "- **States**: " . ($fe['states'] ?? '-') . "\n";
                if (!empty($fe['tasks'])) {
                    $md .= "- **Task Checklist**:\n";
                    foreach ($fe['tasks'] as $t) {
                        $md .= "  - [ ] {$t}\n";
                    }
                }
                $md .= "\n";
            }

            if (!empty($f['backend'])) {
                $be = $f['backend'];
                $md .= "**Backend Tasks (Scalability & Models)**:\n";
                $md .= "- **Model & Migration**: " . ($be['model_and_migration'] ?? '-') . "\n";
                $md .= "- **Scalability Guardrail**: " . ($be['scalability_guardrail'] ?? '-') . "\n";
                if (!empty($be['tasks'])) {
                    $md .= "- **Task Checklist**:\n";
                    foreach ($be['tasks'] as $t) {
                        $md .= "  - [ ] {$t}\n";
                    }
                }
                $md .= "\n";
            }

            if (!empty($f['integration'])) {
                $in = $f['integration'];
                $md .= "**API & Integration Contract**:\n";
                $md .= "- **Endpoint**: `{$in['endpoint']}`\n";
                $md .= "- **Request Payload**:\n```json\n" . ($in['request_schema'] ?? '{}') . "\n```\n";
                $md .= "- **Response Payload**:\n```json\n" . ($in['response_schema'] ?? '{}') . "\n```\n\n";
            }

            if (!empty($f['code_agent_directive'])) {
                $md .= "<details><summary>🤖 <strong>Prompt Handoff AI Code Agent ({$fid})</strong></summary>\n\n";
                $md .= "```markdown\n" . $f['code_agent_directive'] . "\n```\n";
                $md .= "</details>\n\n";
            }

            $md .= "---\n\n";
        }

        if (!empty($phase2Features)) {
            $md .= "### B. Fitur Tambahan (Fase 2 - Roadmap Masa Depan)\n\n";
            foreach ($phase2Features as $f) {
                $fid = $f['id'] ?? 'FEAT-ROADMAP';
                $ftitle = $f['title'] ?? 'Fitur Roadmap';
                $md .= "#### [{$fid}] {$ftitle}\n";
                $md .= "- **Deskripsi**: " . ($f['desc'] ?? '-') . "\n";
                $md .= "- **User Story**: *" . ($f['user_story'] ?? '-') . "*\n\n";
            }
        }

        // 4. User Flow
        $md .= "## 4. Alur Kerja Pengguna (User Flow) & Mermaid Pipeline\n\n";
        $md .= "```mermaid\nflowchart TD\n";
        $wIdx = 1;
        $prevNode = null;
        foreach ($workflow as $w) {
            $nodeId = "S" . $wIdx;
            $actionClean = addslashes($w['action'] ?? ('Step ' . $wIdx));
            $actorClean = addslashes($w['actor'] ?? 'Sistem');
            $md .= "    {$nodeId}[\"<b>Step {$wIdx}: {$actionClean}</b><br/>Aktor: {$actorClean}\"]\n";
            if ($prevNode) {
                $md .= "    {$prevNode} -->|Lanjut| {$nodeId}\n";
            }
            $prevNode = $nodeId;
            $wIdx++;
        }
        $md .= "```\n\n";

        $md .= "### Rincian Rekayasa Alur Bertahap (Step-by-Step Engineering Details)\n\n";
        $md .= "| Step | Aksi Utama | Aktor | Pemicu (Trigger) | Proses Backend & Database | Respon / Output & Edge Case |\n";
        $md .= "|---|---|---|---|---|---|\n";
        foreach ($workflow as $w) {
            $sNum = $w['step'] ?? 1;
            $sAct = $w['action'] ?? '-';
            $sActor = $w['actor'] ?? 'Pengguna';
            $sTrig = $w['trigger'] ?? '-';
            $sProc = $w['system_process'] ?? '-';
            $sOut = ($w['output_state'] ?? '-') . '<br/>**Edge Case**: ' . ($w['edge_case'] ?? '-');
            $md .= "| `0{$sNum}` | **{$sAct}** | `{$sActor}` | {$sTrig} | {$sProc} | {$sOut} |\n";
        }
        $md .= "\n";

        // 5. Database ERD
        $md .= "## 5. Skema Basis Data (PostgreSQL Strict ULID) & Mermaid ERD\n\n";
        $md .= "```mermaid\nerDiagram\n";
        $md .= "    users ||--o{ domain_records : \"manages\"\n";
        $md .= "    users ||--o{ activity_logs : \"triggers\"\n";
        $md .= "    users ||--o{ system_notifications : \"receives\"\n";
        $md .= "```\n\n";

        foreach ($erd as $table) {
            $tname = $table['name'] ?? 'table';
            $tdesc = $table['description'] ?? '';
            $md .= "### Tabel: `{$tname}`\n";
            $md .= "_{$tdesc}_\n\n";
            $md .= "| Kolom | Tipe Data | Indeks | Nullable | Keterangan |\n";
            $md .= "|---|---|---|---|---|\n";
            foreach ($table['columns'] ?? [] as $col) {
                $cname = $col['name'] ?? '';
                $ctype = $col['type'] ?? '';
                $cindex = $col['index'] ?? 'NONE';
                $cnull = ($col['nullable'] ?? false) ? 'YES' : 'NO';
                $cnotes = $col['notes'] ?? '';
                $md .= "| `{$cname}` | `{$ctype}` | `{$cindex}` | {$cnull} | {$cnotes} |\n";
            }
            $md .= "\n";
        }

        // 6. Technology Stack & Architecture Decision
        $md .= "## 6. Keputusan Arsitektur & Rekomendasi Stack (Modern Monolith)\n\n";
        $md .= "| Lapisan | Teknologi | Peran & Justifikasi Arsitektur |\n";
        $md .= "|---|---|---|\n";
        foreach ($tech as $layer => $info) {
            $md .= "| **" . ucfirst(str_replace('_', ' ', $layer)) . "** | " . ($info['name'] ?? '') . " | " . ($info['role'] ?? '') . " |\n";
        }
        $md .= "\n";

        // 7. Anti-AI-Slop Frontend Design System
        $md .= "## 7. Panduan Rekayasa Frontend: Desain Sistem Anti-AI-Slop\n\n";
        $md .= "### " . $slop['philosophy']['title'] . "\n";
        $md .= $slop['philosophy']['description'] . "\n\n";
        $md .= "- **Display Typography**: " . $slop['typography']['display'] . "\n";
        $md .= "- **Body Typography**: " . $slop['typography']['body'] . "\n";
        $md .= "- **Monospaced Data**: " . $slop['typography']['mono'] . "\n";
        $md .= "- **Subtle Corners**: " . ($slop['subtle_corners']['description'] ?? 'Sudut tipis rounded-sm/md, dilarang efek kapsul rounded-full.') . "\n";
        $md .= "- **Dark Surface**: " . $slop['color_tokens']['dark_mode'] . "\n";
        $md .= "- **Light Surface**: " . $slop['color_tokens']['light_mode'] . "\n";
        $md .= "- **Interactive & 3D**: " . ($slop['interactive_and_3d']['description'] ?? 'Micro-animations 60fps & WebGL/Canvas interaktif.') . "\n";
        $md .= "- **Multi-Language (2-Tier)**: " . ($slop['multilingual_two_tier']['description'] ?? 'Dukungan Tier 1 ID/EN & Tier 2 Google Translate plugin.') . "\n";
        $md .= "- **Country Zone Phone**: " . ($slop['country_zone_phone_inputs']['description'] ?? 'Input nomor telepon wajib menggunakan country zone.') . "\n";
        $md .= "- **Thousand Separators**: " . ($slop['thousands_separator_formatting']['description'] ?? 'Pemisah ribuan otomatis untuk angka dan mata uang.') . "\n";
        $md .= "- **Local FontAwesome**: " . ($slop['local_fontawesome_library']['description'] ?? 'Library icon lokal SVG tanpa CDN eksternal.') . "\n";
        $md .= "- **Modular CMS Plugins**: " . ($slop['cms_plugins_and_isolated_layout']['description'] ?? 'Fitur berbasis plugin dengan navigasi/footer global terisolasi.') . "\n";
        $md .= "- **Educational UX**: " . ($slop['educational_plugin_ux']['description'] ?? 'Komponen informatif dan membimbing pengguna.') . "\n";
        $md .= "- **State Primitives**: " . $slop['component_primitives']['states'] . "\n";
        $md .= "- **Skeleton Loaders**: " . $slop['component_primitives']['skeletons'] . "\n";
        $md .= "- **Zero Native Dialogs**: " . $slop['component_primitives']['dialog_and_toasts'] . "\n\n";

        // 8. Backend Scalability Manifesto
        $md .= "## 8. Manifesto Skalabilitas Backend & Standar PostgreSQL\n\n";
        foreach ($scalability as $item) {
            $md .= "### " . $item['rule'] . "\n";
            $md .= $item['explanation'] . "\n\n";
        }

        // 9. AI Agent Handoff Protocol
        $md .= "## 9. Protokol Handoff AI Code Agent (Anti Context-Rot)\n\n";
        $md .= "> " . $handoff['objective'] . "\n\n";
        foreach ($handoff['rules'] as $r) {
            $md .= "### " . $r['rule'] . "\n";
            $md .= $r['desc'] . "\n\n";
        }

        // 10. Governance & DoD
        $md .= "## 10. Tata Kelola, Kualitas & Definition of Done (DoD)\n\n";
        foreach ($governance['definition_of_done'] ?? [] as $dod) {
            $md .= "- [x] {$dod}\n";
        }
        $md .= "\n";

        // 11. Velocity Pricing
        $md .= "## 11. Opsi Akselerasi Peluncuran (Velocity Pricing Continuum)\n\n";
        $md .= "| Opsi Paket | Estimasi Durasi | Nilai Investasi | Termin DP (50%) | Alokasi Squad & Engine |\n";
        $md .= "|---|---|---|---|---|\n";
        foreach ($pricing as $p) {
            $pname = $p['name'] ?? '';
            $pduration = $p['duration'] ?? '';
            $pamount = 'Rp ' . number_format($p['contract_amount'] ?? 0, 0, ',', '.');
            $pdp = 'Rp ' . number_format($p['dp_amount'] ?? 0, 0, ',', '.');
            $psquad = $p['squad_allocation'] ?? ($p['ai_quota_spec'] ?? '-');
            $md .= "| **{$pname}** | {$pduration} | {$pamount} | {$pdp} | {$psquad} |\n";
        }
        $md .= "\n";

        return $md;
    }
}

