<?php

namespace App\Services;

use App\Models\VisionBlueprint;
use Illuminate\Support\Str;

class PrdGeneratorService
{
    /**
     * Generate an Ultimate PRD & Architecture Blueprint from blueprint questionnaire inputs.
     */
    public static function generate(VisionBlueprint $blueprint, ?string $preferredAiProvider = null): array
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
            'architecture_preference' => $metadata['architecture_preference'] ?? null,
            'skala_pengguna' => $metadata['skala_pengguna'] ?? '0 - 100.000 Pengguna / Bulan (Dedicated VPS Monolith)',
            'jangkauan_pasar' => $metadata['jangkauan_pasar'] ?? 'Domestik Indonesia (IDR, Zona WIB/WITA/WIT)',
            'out_of_scope' => $metadata['out_of_scope'] ?? null,
            'kepatuhan_keamanan' => $metadata['kepatuhan_keamanan'] ?? 'Standar Web Application & OWASP Top 10',
            'kisaran_budget' => $metadata['kisaran_budget'] ?? null,
            'target_platform' => $metadata['target_platform'] ?? 'Modern Web Application Responsive & PWA (Desktop, Tablet & Mobile)',
            'migrasi_data' => $metadata['migrasi_data'] ?? 'Database Baru Bersih (Input Mandiri & Template CSV)',
            'preferensi_hosting' => $metadata['preferensi_hosting'] ?? 'Managed Dedicated Cloud VPS Neriah Pro (PostgreSQL 16, Redis, Backup)',
            'garansi_sla' => $metadata['garansi_sla'] ?? '30 Hari Garansi Bug Pascameluncur + Penyerahan Akses Penuh Private Repo GitHub',
            'termin_pembayaran' => $metadata['termin_pembayaran'] ?? 'Termin Standar 50/50: 50% DP Kickoff & 50% Pelunasan setelah lolos UAT & Serah Terima Kunci (via Midtrans Snap)',
        ];

        // Dynamic Decoupled Architecture Detection
        $targetPlatformLower = strtolower($extraContext['target_platform'] ?? '');
        $hostingLower = strtolower($extraContext['preferensi_hosting'] ?? '');
        $scaleLower = strtolower($extraContext['skala_pengguna'] ?? '');
        $archPrefLower = strtolower($extraContext['architecture_preference'] ?? '');

        $isDecoupledRequested = str_contains($targetPlatformLower, 'decoupled') ||
                                str_contains($targetPlatformLower, 'headless') ||
                                str_contains($targetPlatformLower, 'microservices') ||
                                str_contains($hostingLower, 'decoupled') ||
                                str_contains($hostingLower, 'multi-tier') ||
                                str_contains($hostingLower, 'cloudflare pages') ||
                                str_contains($archPrefLower, 'decoupled') ||
                                str_contains($scaleLower, 'microservices') ||
                                (str_contains($targetPlatformLower, 'mobile') && str_contains($targetPlatformLower, 'flutter') && str_contains($targetPlatformLower, 'web'));

        // Generate Virtual Architecture Charts (Mermaid Diagrams Suite)
        $workflowMermaid = self::generateWorkflowMermaid($workflowStages);
        $erdMermaid = self::generateErdMermaid($erdTables);
        $featureDepMermaid = self::generateFeatureDependencyMermaid($actorItems, $mvpItems, $erdTables);
        $sprintGanttMermaid = self::generateSprintGanttMermaid($blueprint, $targetWaktu);
        $infrastructureMermaid = self::generateInfrastructureMermaid($blueprint, $extraContext);
        $mobileSyncMermaid = self::generateMobileSyncMermaid($businessName);
        $mobileArchitecture = self::generateMobileAndSyncArchitecture($businessName, $extraContext['target_platform'] ?? '', $erdTables, $mvpItems);
        $developerEducation = self::getDeveloperEducationDeck($businessName);
        $itemizedEstimation = self::calculateItemizedEstimation($blueprint);
        $aiTelemetry = self::synthesizeAiPrdTelemetry($blueprint, $businessName, $masalah, $tujuan, $preferredAiProvider);

        $techStack = $isDecoupledRequested ? [
            'frontend' => [
                'name' => 'Next.js 15 App Router (React 19 + Turbopack) / Nuxt 3',
                'role' => 'Headless Consumer Web, Edge SSR & ISR, Sub-20ms Global TTFB, SEO Web Vitals Sempurna',
            ],
            'mobile_app' => [
                'name' => 'Flutter 3.x / React Native (Expo SDK 52+)',
                'role' => 'Native Mobile Client (Android & iOS), Offline-First UI, Background Sync Worker (WorkManager)',
            ],
            'backend' => [
                'name' => 'Headless Laravel 13 RESTful API & Queue Engine',
                'role' => 'Core Business Logic, Eloquent ORM, Strict ACID Transactions, Event Sourcing & Workers',
            ],
            'admin_panel' => [
                'name' => 'Filament v5 Enterprise Backoffice',
                'role' => 'Pusat Kendali Operasional Internal, Rekapitulasi Data, RBAC Shield & Metric Widgets',
            ],
            'api_contract' => [
                'name' => 'OpenAPI 3.1 & Scalar Interactive Docs',
                'role' => 'Kontrak API Terstandarisasi, Autogenerasi TypeScript Types (Zod), Zero Ambiguity Integration',
            ],
            'local_database' => [
                'name' => 'SQLite 3 Encrypted (Drift / Room / WatermelonDB)',
                'role' => 'Zero-Latency Offline-First Local Storage, Mutation Journal, Client-Side Keyset Cache',
            ],
            'database' => [
                'name' => 'PostgreSQL 16+ Strict ULID & PgBouncer',
                'role' => 'ACID Relational Storage, JSONB indexing, Keyset Cursor Pagination O(1), Source of Truth',
            ],
            'sync_protocol' => [
                'name' => 'Bi-Directional Delta Sync with Idempotency Key (X-Idempotency-Key)',
                'role' => 'Deterministic Sync Protocol (/api/v1/sync/push & pull), Last-Write-Wins (LWW) with ULID Timestamps',
            ],
            'cache_and_queue' => [
                'name' => 'Redis 7+ Cluster & Kafka / RabbitMQ Event Broker',
                'role' => 'Sub-millisecond Token Blacklist, Rate Limiting, & Event-Driven Asynchronous Messaging',
            ],
            'infrastructure' => [
                'name' => 'Cloudflare Pages (Edge Frontend) + Dedicated VPS via Coolify (API Engine)',
                'role' => '300+ Edge POPs (<20ms global latency) + 100% Resource Isolation Backend + Cloudflare R2 ($0 Egress)',
            ],
        ] : [
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
            'mobile_app' => [
                'name' => 'Flutter 3.x / React Native (Cross-Platform iOS & Android)',
                'role' => 'Native Mobile Client, Offline-First UI, Background Sync Worker (WorkManager)',
            ],
            'local_database' => [
                'name' => 'SQLite (Drift / Room / WatermelonDB) Encrypted',
                'role' => 'Zero-Latency Offline-First Local Storage, Mutation Journal, Client-Side Keyset Cache',
            ],
            'database' => [
                'name' => 'PostgreSQL 16+ (Strict ULID Schema)',
                'role' => 'ACID Relational Storage, JSONB indexing, Keyset Cursor Pagination, Central Source of Truth',
            ],
            'sync_protocol' => [
                'name' => 'Bi-Directional Delta Sync with Idempotency Engine',
                'role' => 'Deterministic Sync Protocol (/api/v1/sync/push & pull), Last-Write-Wins (LWW) with ULID Timestamps, Exponential Backoff Retries',
            ],
            'cache_and_queue' => [
                'name' => 'Redis / Predis Engine',
                'role' => 'High-throughput Session, Cache, & Async Background Queue Jobs',
            ],
            'infrastructure' => [
                'name' => 'Dedicated VPS via Nixpacks & Docker',
                'role' => 'Isolasi resource 100%, Cloudflare CDN fronting, Nginx HTTP/2 reverse-proxy',
            ],
        ];

        return [
            'meta' => [
                'project_name' => $businessName,
                'version' => '1.0.0-PROPOSAL',
                'generated_at' => now()->toIso8601String(),
                'status' => 'Ultimate Vision Blueprint',
                'ai_telemetry' => $aiTelemetry,
            ],
            'executive_summary' => [
                'title' => 'Executive Technical Discovery & Blueprint',
                'problem_statement' => $masalah,
                'success_metrics' => $tujuan,
                'target_audience' => $audiens,
                'target_platform' => $extraContext['target_platform'],
                'legacy_data_migration' => $extraContext['migrasi_data'],
                'hosting_infrastructure' => $extraContext['preferensi_hosting'],
                'warranty_sla' => $extraContext['garansi_sla'],
                'payment_milestones' => $extraContext['termin_pembayaran'],
                'design_inspiration' => $referensiDesain,
                'asset_readiness' => $kesiapanAset,
                'target_timeline' => $targetWaktu,
                'target_scale' => $extraContext['skala_pengguna'],
                'market_reach' => $extraContext['jangkauan_pasar'],
                'compliance_level' => $extraContext['kepatuhan_keamanan'],
                'budget_range' => $extraContext['kisaran_budget'],
                'architecture_philosophy' => $isDecoupledRequested
                    ? 'Untuk mendukung tim rekayasa multi-disiplin, multi-channel mobile & web apps, serta performa edge global sub-20ms, sistem ini dirancang menggunakan arsitektur Enterprise Decoupled & Headless (Next.js 15 App Router / Nuxt 3, Flutter Mobile Client, dan Headless Laravel 13 RESTful/OpenAPI 3.1). Seluruh data transaksi diorkestrasi dengan PostgreSQL Strict ULID dan protokol sinkronisasi delta dengan proteksi idempotency.'
                    : 'Untuk menjamin efisiensi biaya server, ketahanan jangka panjang, dan kecepatan peluncuran (Rapid Time-to-Market), sistem ini dirancang menggunakan arsitektur Modern Monolith (Laravel 13 & Filament PHP) yang Decoupled-Ready. Seluruh tabel bisnis menggunakan Primary Key ULID untuk skalabilitas terdistribusi dan kompatibilitas penuh PostgreSQL.',
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
            'virtual_charts' => [
                'workflow_mermaid' => $workflowMermaid,
                'erd_mermaid' => $erdMermaid,
                'feature_dependency_mermaid' => $featureDepMermaid,
                'sprint_gantt_mermaid' => $sprintGanttMermaid,
                'infrastructure_mermaid' => $infrastructureMermaid,
                'mobile_sync_mermaid' => $mobileSyncMermaid,
            ],
            'mobile_and_sync_architecture' => $mobileArchitecture,
            'developer_education' => $developerEducation,
            'tech_stack' => $techStack,
            'integrations' => [
                'requested' => $integrasi,
                'notes' => 'Akan dihubungkan melalui service providers terisolasi dengan fallback retry mechanism.',
            ],
            'architecture_evaluation' => self::evaluateArchitecture($businessName, $masalah, $mvpItems, $alurKerja, $extraContext),
            'decoupled_tooling_strategy' => self::generateDecoupledToolingStrategy($businessName, $extraContext, $erdTables, $mvpItems),
            'ai_security_blueprint' => self::generateAiSecurityBlueprint($businessName, $extraContext),
            'agentic_ai_matrix' => self::generateAgenticAiConceptsMatrix($businessName, $mvpItems),
            'server_hardware_sizing' => self::calculateServerHardwareSizing($businessName, $masalah, $mvpItems, $extraContext),
            'itemized_cost_breakdown' => $itemizedEstimation,
            'business_roi_analysis' => self::generateBusinessRoiAnalysis($blueprint, $itemizedEstimation),
            'velocity_pricing_options' => !empty($itemizedEstimation['velocity_tiers']) ? $itemizedEstimation['velocity_tiers'] : (self::generateVelocityPricingOptions($targetWaktu, $extraContext['kisaran_budget'] ?? null, $businessName, $masalah) ?: []),
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

        return $baseTables;
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

        // Dynamic Decoupled Architecture Detection
        $targetPlatformLower = strtolower($extraContext['target_platform'] ?? '');
        $hostingLower = strtolower($extraContext['preferensi_hosting'] ?? '');
        $scaleLower = strtolower($extraContext['skala_pengguna'] ?? '');
        $archPrefLower = strtolower($extraContext['architecture_preference'] ?? '');

        $isDecoupledRequested = str_contains($targetPlatformLower, 'decoupled') ||
                                str_contains($targetPlatformLower, 'headless') ||
                                str_contains($targetPlatformLower, 'microservices') ||
                                str_contains($hostingLower, 'decoupled') ||
                                str_contains($hostingLower, 'multi-tier') ||
                                str_contains($hostingLower, 'cloudflare pages') ||
                                str_contains($archPrefLower, 'decoupled') ||
                                str_contains($scaleLower, 'microservices') ||
                                (str_contains($targetPlatformLower, 'mobile') && str_contains($targetPlatformLower, 'flutter') && str_contains($targetPlatformLower, 'web'));

        return [
            'hosting_evaluation' => [
                'verdict' => $isLeanBudget 
                    ? 'Cloud Starter / Micro VPS (Fase 1) & Seamless VPS Scale-Up (Fase 2)' 
                    : ($isDecoupledRequested ? 'Multi-Tier Cloud Infrastructure (Cloudflare Pages Edge + Dedicated API VPS)' : 'Dedicated VPS (Nixpacks & Docker Containerization)'),
                'verdict_badge' => $isLeanBudget ? 'LEAN_CLOUD_STARTER' : ($isDecoupledRequested ? 'MULTI_TIER_EDGE_CLOUD' : 'VPS_DEDICATED'),
                'recommendation' => $isLeanBudget ? 'CLOUD_STARTER_LEAN' : ($isDecoupledRequested ? 'MULTI_TIER_DECOUPLED' : 'DEDICATED_VPS'),
                'compute_weight_score' => $isLeanBudget ? '70/100 (Lean Operational Footprint)' : ($isDecoupledRequested ? '95/100 (Global Edge & Multi-Party API Scale)' : '92/100 (High-Throughput Enterprise)'),
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
                'verdict' => $isDecoupledRequested
                    ? 'Enterprise Decoupled Headless (Next.js 15 / Nuxt 3 + Headless API Engine + Flutter/React Native)'
                    : 'Modern Monolith (Laravel 13 + Filament v5 + Island Architecture)',
                'verdict_badge' => $isDecoupledRequested ? 'DECOUPLED_ENTERPRISE' : 'RAPID_MONOLITH',
                'recommendation' => $isDecoupledRequested ? 'DECOUPLED_HEADLESS' : 'MODERN_MONOLITH',
                'match_percentage' => $isDecoupledRequested ? '98% Optimal Enterprise Architectural Match' : '96% Optimal Architectural Match',
                'is_decoupled' => $isDecoupledRequested,
                'monolith' => [
                    'status' => $isDecoupledRequested ? 'OPSI ALTERNATIF (LEAN VELOCITY)' : 'OPTIMAL REKOMENDASI (96% MATCH)',
                    'title' => 'Modern Monolith Architecture (Laravel 13 + Filament v5)',
                    'reasons' => [
                        'Eliminasi Network Latency: Komunikasi antar modul berjalan intra-process O(1) tanpa overhead HTTP network antar-microservices.',
                        'Pangkas Biaya Infrastruktur 60-80%: Satu kesatuan container deployment menghemat anggaran server staging & produksi dibanding kluster microservices.',
                        'Rapid Time-to-Market (3x Lebih Cepat): Skema database, API internal, dan Admin Dasbor Filament v5 langsung sinkron tanpa duplikasi skema.',
                        'Konsistensi Transaksi ACID: Menjamin integritas data tanpa kerumitan distributed transaction (2-Phase Commit / Saga Pattern) yang rawan data loss.',
                        'Island Architecture Frontend: Memberikan fluiditas interaksi 60fps setara SPA dengan stabilitas dan kecepatan SEO Server-Side Rendering.',
                    ],
                ],
                'decoupled' => [
                    'status' => $isDecoupledRequested ? 'OPTIMAL REKOMENDASI (98% MATCH)' : 'OPSI ROADMAP SCALE-UP (TERSEDIA & SIAP ADAPSI)',
                    'title' => 'Enterprise Decoupled & Headless Architecture (Next.js 15 + Mobile + Headless API)',
                    'reasons' => [
                        'Independensi Frontend & Multi-Client: Tim Web (Next.js 15 / Nuxt 3) dan Tim Mobile (Flutter / React Native) dapat melakukan deploy dan iterasi tanpa ketergantungan deployment backend.',
                        'Edge Caching Global (Sub-20ms TTFB): Frontend Next.js / Nuxt 3 di-hosting di Cloudflare Pages / Vercel Edge di 300+ kota dunia dengan zero egress bandwidth fee.',
                        'Headless API Engine Berkinerja Tinggi: Core backend (Laravel 13 RESTful / NestJS / Go) fokus 100% pada logika bisnis, proteksi transaksi ACID, dan orkestrasi data.',
                        'OpenAPI 3.1 & Strict Contract: Kontrak API terstandardisasi dengan Scalar / Swagger UI otomatis, memungkinkan autogenerasi SDK dan TypeScript types (Zod).',
                        'BFF (Backend for Frontend) Pattern: Menjamin mobile app menerima payload terkompresi hemat baterai dan kuota seluler, sementara web portal menerima data densitas tinggi.',
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
                    'title' => 'Modern Monolith (Efisiensi Modal Maksimal)',
                    'monthly_cost' => $isLeanBudget ? '< Rp 100.000 - Rp 250.000 / bulan' : 'Rp 350.000 - Rp 950.000 / bulan',
                    'devops_headcount' => '0 FTE (Automated Nixpacks CI/CD)',
                    'capital_efficiency' => '90% anggaran klien dialokasikan murni untuk fitur bisnis & akuisisi pengguna.',
                ],
                'decoupled_tco' => [
                    'title' => 'Modern Decoupled (Multi-Party Lean Ecosystem 2026+)',
                    'monthly_cost' => 'Rp 500.000 - Rp 2.500.000 / bulan (Cloudflare Pages Rp 0 + Dedicated API VPS Rp 350rb-950rb + R2 Storage Rp 0 Egress)',
                    'devops_headcount' => '0-0.5 FTE (Git Webhook Deploys via Coolify & Cloudflare Pages)',
                    'capital_efficiency' => '85% efisiensi modal berkat Edge Caching gratis dan penghapusan biaya transfer data (Zero Egress).',
                ],
            ],
            'decoupling_threshold_triggers' => [
                'title' => '4 Faktor Kunci Strategi Adopsi Arsitektur Decoupled (The Decoupling Strategy)',
                'subtitle' => 'Pola arsitektur Decoupled memberikan keunggulan masif ketika memenuhi 1 atau lebih faktor berikut:',
                'triggers' => [
                    [
                        'number' => '01',
                        'title' => 'Aplikasi Multi-Client Mandiri (Web Consumer, Mobile Apps & POS Tablet)',
                        'desc' => 'Ketika perusahaan membutuhkan aplikasi consumer-facing publik (Next.js 15), aplikasi native lapangan (Flutter Android/iOS untuk Driver & Staf), dan dasbor kasir POS yang semuanya mengonsumsi satu Headless API Engine terpusat.',
                    ],
                    [
                        'number' => '02',
                        'title' => 'Independensi Tim Rekayasa Multi-Disiplin (Separate Repositories)',
                        'desc' => 'Ketika squad frontend web, tim mobile app, dan tim backend API bekerja di repositori terpisah dengan siklus rilis harian masing-masing tanpa saling memblokir proses deploy.',
                    ],
                    [
                        'number' => '03',
                        'title' => 'Global Edge Delivery & Latensi Sub-20ms Seluruh Dunia',
                        'desc' => 'Ketika landing page dan katalog publik memerlukan distribusi CDN anycast global via Cloudflare Pages di 300+ kota dunia dengan biaya transfer data Rp 0 (Zero Egress).',
                    ],
                    [
                        'number' => '04',
                        'title' => 'Integrasi API Terbuka Multi-Pihak & Ekosistem B2B (OpenAPI 3.1 Strict)',
                        'desc' => 'Ketika sistem dirancang sebagai platform terbuka bagi developer pihak ketiga, mitra logistik, atau merchant perbankan dengan dokumentasi interaktif Scalar/Swagger otomatis.',
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
            'recommended_tools' => $isDecoupledRequested ? [
                ['category' => 'Web Consumer Layer', 'name' => 'Next.js 15 App Router (React 19 + Turbopack)', 'desc' => 'Edge SSR, ISR sub-20ms TTFB, Server Components, Web Vitals SEO Sempurna'],
                ['category' => 'Mobile Clients Layer', 'name' => 'Flutter 3.x / React Native Expo', 'desc' => 'Cross-Platform Native Apps, SQLite Offline-First, WorkManager Sync'],
                ['category' => 'Backend API Engine', 'name' => 'Headless Laravel 13 (OpenAPI 3.1 & Queues)', 'desc' => 'Strict ACID Transactions, Eloquent ORM, Action Handlers, Sanctum/JWT Auth'],
                ['category' => 'Admin & Operations', 'name' => 'Filament PHP v5 Enterprise Suite', 'desc' => 'Dasbor kendali instan, Filter Keyset, Export PDF/Excel, RBAC Shield'],
                ['category' => 'API Contract & Docs', 'name' => 'OpenAPI 3.1 & Scalar Interactive Reference', 'desc' => 'Kontrak API type-safe, autogenerasi TypeScript types (Zod), zero ambiguity'],
                ['category' => 'AI Database Engine', 'name' => 'PostgreSQL 16+ (pgvector)', 'desc' => 'Vector embeddings, HNSW semantic search, JSONB documents, ULID standard'],
                ['category' => 'In-Memory Cache & Broker', 'name' => 'Redis 7+ & Kafka/RabbitMQ Event Broker', 'desc' => 'Zero-latency sessions, distributed rate limiting, asynchronous event messaging'],
                ['category' => 'Hosting & Cloud Storage', 'name' => 'Cloudflare Pages (Frontend) + Dedicated VPS (API) + R2', 'desc' => '300+ Edge POPs global + 100% Resource Isolation + $0 Egress Bandwidth Storage'],
                ['category' => 'AI Acceleration Engine', 'name' => 'Gemini Ultra / Pro API SDK', 'desc' => 'High-reasoning prompt synthesis, context injection RAG, automated code assistant'],
            ] : [
                ['category' => 'Backend Core', 'name' => 'Laravel 13 Modern Monolith', 'desc' => 'PHP 8.4/8.5 Property Hooks, Eloquent ORM, Action Handlers, Queues'],
                ['category' => 'Admin & Operations', 'name' => 'Filament PHP v5 Enterprise', 'desc' => 'Dasbor kendali instan, Filter Keyset, Export PDF/Excel, RBAC Shield'],
                ['category' => 'Frontend Layer', 'name' => 'Island Architecture (React 19 + Framer Motion)', 'desc' => 'Interaktivitas fluid 60fps, micro-animations, Canva-style canvas capability'],
                ['category' => 'AI Database Engine', 'name' => 'PostgreSQL 16+ (pgvector)', 'desc' => 'Vector embeddings, HNSW semantic search, JSONB documents, ULID standard'],
                ['category' => 'In-Memory Cache & Broker', 'name' => 'Redis / Predis', 'desc' => 'Zero-latency sessions, distributed lock, persistent queue processing'],
                ['category' => 'Infrastructure & Runtime', 'name' => 'Dedicated VPS via Nixpacks & Docker', 'desc' => 'Container isolation, Nginx HTTP/2, automated SSL, Zero-downtime deploy'],
                ['category' => 'AI Acceleration Engine', 'name' => 'Gemini Ultra / Pro API SDK', 'desc' => 'High-reasoning prompt synthesis, context injection RAG, automated code assistant'],
            ],
            'server_hardware_sizing' => self::calculateServerHardwareSizing($businessName, $masalah, $mvpItems, $extraContext),
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

    /**
     * Synthesize world-class domain business feasibility, killer ROI equation,
     * cost of inaction, and Neriah Pro no-regret guarantees.
     */
    public static function generateBusinessRoiAnalysis(VisionBlueprint $blueprint, array $itemizedEstimation): array
    {
        $businessName = $blueprint->nama_bisnis ?: ($blueprint->client_name . "'s Project");
        $masalah = strtolower($blueprint->masalah_utama ?? '');
        $tujuan = strtolower($blueprint->tujuan_utama ?? '');
        $fitur = strtolower($blueprint->fitur_wajib ?? '');
        $corpus = strtolower($businessName . ' ' . $masalah . ' ' . $tujuan . ' ' . $fitur);

        $totalInvestasi = (float) ($itemizedEstimation['base_subtotal'] ?? 10000000.00);
        $dpInvestasi = (float) ($itemizedEstimation['standard_dp'] ?? ($totalInvestasi * 0.50));

        // Detect Domain
        $isProperty = str_contains($corpus, 'villa') || str_contains($corpus, 'properti') || str_contains($corpus, 'property') ||
                      str_contains($corpus, 'sewa') || str_contains($corpus, 'rental') || str_contains($corpus, 'tanah') ||
                      str_contains($corpus, 'land') || str_contains($corpus, 'leasehold') || str_contains($corpus, 'sanur') ||
                      str_contains($corpus, 'bali') || str_contains($corpus, 'canggu') || str_contains($corpus, 'ubud') ||
                      str_contains($corpus, 'kavling') || str_contains($corpus, 'broker') || str_contains($corpus, 'arebi') ||
                      str_contains($corpus, 'listing');

        $isClinic = str_contains($corpus, 'klinik') || str_contains($corpus, 'pasien') || str_contains($corpus, 'dokter') ||
                    str_contains($corpus, 'rekam medis') || str_contains($corpus, 'obat') || str_contains($corpus, 'apotek') ||
                    str_contains($corpus, 'antrean') || str_contains($corpus, 'kesehatan');

        $isLogistics = str_contains($corpus, 'logistik') || str_contains($corpus, 'ekspedisi') || str_contains($corpus, 'armada') ||
                       str_contains($corpus, 'truk') || str_contains($corpus, 'kontainer') || str_contains($corpus, 'pengiriman') ||
                       str_contains($corpus, 'resi') || str_contains($corpus, 'tracking') || str_contains($corpus, 'kurir');

        $isChurch = str_contains($corpus, 'gereja') || str_contains($corpus, 'jemaat') || str_contains($corpus, 'ibadah') ||
                    str_contains($corpus, 'warta') || str_contains($corpus, 'doa') || str_contains($corpus, 'persembahan');

        $isCommerce = str_contains($corpus, 'marketplace') || str_contains($corpus, 'toko online') || str_contains($corpus, 'katalog') ||
                      str_contains($corpus, 'keranjang') || str_contains($corpus, 'checkout') || str_contains($corpus, 'ecommerce');

        if ($isProperty) {
            $domainCategory = 'Villa Rental & Real Estate Agency';
            $domainBadge = 'HIGH_TICKET_COMMERCIAL';
            $ticketBenchmark = 'Rp 180.000.000 - Rp 650.000.000 / transaksi sewa/jual';
            $roiHeadline = 'Cukup 1 Transaksi Closing Sewa Villa = 100% BEP Lunas!';
            $roiFormula = 'Komisi 5% - 10% dari 1 Villa Sewa Tahunan (Rp 220M) = Rp 11.000.000 - Rp 22.000.000';
            $roiNarrative = 'Dalam industri real estate & rental villa di Bali, komisi rata-rata broker adalah 5% s/d 10%. Sekali Anda berhasil melakukan closing pada 1 unit villa sewa tahunan standar (misal: villa di Sanur seharga Rp 220 juta/tahun), komisi yang Anda kantongi (Rp 11 juta s/d Rp 22 juta) langsung melunasi 100% total biaya investasi pembuatan sistem Neriah Pro (Rp ' . number_format($totalInvestasi, 0, ',', '.') . '). Setiap closing berikutnya adalah 100% keuntungan murni Anda.';
            $projectedAnnualRoi = '450% - 1.200% (Estimasi realistis 3 - 6 transaksi closing di tahun pertama)';
            $inactionRisks = [
                'Kehilangan Calon Penyewa Expat / Turis: Wisatawan mancanegara menuntut katalog online dengan galeri foto jernih dan detail fasilitas transparan. Ketiadaan website membuat mereka langsung lari ke broker berlisensi lain.',
                'Pemilik Villa (Owner) Enggan Menitipkan Listing: Pemilik villa mewah ratusan juta ragu mempercayakan aset berharganya kepada agen yang hanya mengandalkan status WhatsApp atau feed Instagram pribadi.',
                'Lead Tercecer & Tidak Terekam: Pertanyaan calon penyewa hilang di tumpukan obrolan chat pribadi tanpa pipeline status ketersediaan (Available / Rented / Sold).',
            ];
            $phasedStrategy = 'Mulai dengan Fase 1 Lean Catalog MVP (Listing Properti, Detail Fasilitas, Galeri HD, Direct WhatsApp Concierge, & Dasbor Admin Filament v5) dengan termin 50/50. Website langsung beroperasi dalam 14-21 hari kerja untuk menangkap momentum sewa tanpa beban modal besar di awal.';
        } elseif ($isClinic) {
            $domainCategory = 'Healthcare & Clinical Information System';
            $domainBadge = 'HIGH_IMPACT_HEALTHCARE';
            $ticketBenchmark = 'Ratusan kunjungan pasien & resep obat harian';
            $roiHeadline = 'Efisiensi Waktu & Pengendalian Selisih Stok Obat = Balik Modal dalam 2-3 Bulan';
            $roiFormula = 'Penghematan Jam Antrean + Pengendalian Kebocoran Obat = Rp 5.000.000 - Rp 15.000.000 / bulan';
            $roiNarrative = 'Digitalisasi pendaftaran pasien memangkas waktu tunggu hingga 70%, memungkinkan klinik melayani 20% lebih banyak pasien harian. Otomatisasi mutasi stok obat apotek mencegah selisih persediaan dan obat kedaluwarsa yang kerap merugikan klinik belasan juta per bulan.';
            $projectedAnnualRoi = '250% - 500% dari efisiensi operasional dan peningkatan retensi pasien';
            $inactionRisks = [
                'Rekam Medis Kertas Rentan Rusak / Hilang: Risiko pelanggaran kepatuhan hukum dan lambatnya rujukan antar-dokter.',
                'Antrean Menumpuk di Ruang Tunggu: Pasien frustrasi menunggu pendaftaran manual dan beralih ke fasilitas kesehatan lain.',
                'Selisih Stok Obat Tidak Terdeteksi: Kerugian finansial akibat pencatatan manual buku apotek yang rentan salah hitung.',
            ];
            $phasedStrategy = 'Fase 1 memfokuskan pada Pendaftaran Pasien, Rekam Medis Terproteksi (RME), dan Mutasi Stok Apotek dengan termin 50/50 sebelum melangkah ke integrasi BPJS/SatuSehat di Fase 2.';
        } elseif ($isLogistics) {
            $domainCategory = 'Logistics & Fleet Operations Hub';
            $domainBadge = 'HIGH_CONCURRENCY_SUPPLY_CHAIN';
            $ticketBenchmark = 'Ribuan surat jalan & manifes pengiriman bulanan';
            $roiHeadline = 'Otomatisasi Surat Jalan & Eliminasi Selisih Klaim = Balik Modal dalam 1-2 Bulan';
            $roiFormula = 'Pencegahan Klaim Keterlambatan + Penghematan Waktu Admin = Rp 8.000.000 - Rp 20.000.000 / bulan';
            $roiNarrative = 'Memangkas waktu rekonsiliasi manifes dan bukti serah terima (Proof of Delivery) dari 3 hari menjadi real-time. Membebaskan ratusan jam kerja staf operasional dan mengeliminasi klaim biaya akibat paket hilang atau salah antar.';
            $projectedAnnualRoi = '350% - 700% dari efisiensi armada dan kecepatan penagihan invoice';
            $inactionRisks = [
                'Surat Jalan Kertas Hilang di Lapangan: Menghambat proses penagihan faktur dan memicu komplain pembayaran dari klien korporat.',
                'Staf Lumpuh Meladeni Chat Manual: Waktu terbuang hanya untuk menjawab "paket saya sudah sampai mana?".',
            ];
            $phasedStrategy = 'Fase 1 mengotomatisasi pencatatan resi, tracking armada digital, dan portal tanda terima supir sebelum scale-up ke multi-cabang.';
        } elseif ($isChurch) {
            $domainCategory = 'Church Management & Faith Community OS';
            $domainBadge = 'COMMUNITY_STEWARDSHIP';
            $ticketBenchmark = 'Pelayanan ratusan hingga ribuan jemaat';
            $roiHeadline = 'Efisiensi Warta Kertas & Bebas Biaya Langganan Bulanan Software Asing';
            $roiFormula = 'Hemat Cetak Kertas Warta + Hemat Biaya Software Asing = Rp 15.000.000 - Rp 35.000.000 / tahun';
            $roiNarrative = 'Menghilangkan beban cetak warta mingguan fisik dan membebaskan gereja dari biaya langganan bulanan software asing (seperti Elvanto/Planning Center) yang menguras kas pelayanan. Sekali bayar, sistem menjadi milik gereja selamanya.';
            $projectedAnnualRoi = 'Stewardship Efisien: 100% alokasi persembahan kembali untuk pelayanan jemaat';
            $inactionRisks = [
                'Warta Cetak Mubazir: Puluhan rim kertas terbuang setiap minggu setelah ibadah selesai.',
                'Database Jemaat Terfragmentasi: Data keluarga dan baptisan tercecer di laptop pribadi masing-masing pengurus.',
            ];
            $phasedStrategy = 'Menerapkan Subsidi Efisiensi Komunitas Neriah Pro dengan termin 50/50 agar pelayanan digital dapat langsung melayani jemaat tanpa kendala anggaran.';
        } elseif ($isCommerce) {
            $domainCategory = 'Independent Commercial Commerce Platform';
            $domainBadge = 'DIRECT_TO_CONSUMER';
            $ticketBenchmark = 'Volume transaksi ritel harian';
            $roiHeadline = 'Bebas Potongan Komisi Marketplace (15-20%) = Balik Modal dalam 1-3 Bulan';
            $roiFormula = 'Penghematan 15% Fee Marketplace dari Omzet Rp 50M = Rp 7.500.000 / bulan';
            $roiNarrative = 'Setiap transaksi yang dialihkan ke portal mandiri langsung menghemat potongan fee pihak ketiga. Membangun basis data pelanggan milik sendiri tanpa perang harga.';
            $projectedAnnualRoi = '300% - 600% dari margin keuntungan yang terselamatkan';
            $inactionRisks = [
                'Ketergantungan Akun Pihak Ketiga: Risiko akun ditutup sepihak atau algoritma berubah mendadak.',
                'Data Pelanggan Bukan Milik Anda: Tidak bisa melakukan promosi langsung atau retensi pelanggan setia.',
            ];
            $phasedStrategy = 'Peluncuran toko mandiri dengan integrasi Midtrans Snap & Kurir Ekspedisi dalam 14 hari kerja.';
        } else {
            $domainCategory = 'Custom Business Operations & Client Management Engine';
            $domainBadge = 'ENTERPRISE_OPERATIONS';
            $ticketBenchmark = 'Operasional komersial terpadu';
            $roiHeadline = 'Kredibilitas Nilai Jual & Otomatisasi Alur Kerja = Balik Modal dari 1-2 Klien Pertama';
            $roiFormula = '1 Klien Baru Tertutup Berkat Kredibilitas Sistem = 100% BEP Investasi';
            $roiNarrative = 'Sistem operasional modern meningkatkan positioning dan nilai tawar brand Anda di mata calon klien bernilai tinggi. Efisiensi pencatatan otomatis menghemat puluhan jam kerja tim setiap pekan.';
            $projectedAnnualRoi = '250% - 500% dalam tahun pertama operasional';
            $inactionRisks = [
                'Citra Usaha Terlihat Kurang Profesional: Klien ragu membayar mahal pada bisnis yang pencatatannya masih serba manual.',
                'Beban Kerja Berulang (Human-Error): Waktu berharga terbuang untuk rekap data rutin yang seharusnya bisa dikerjakan mesin.',
            ];
            $phasedStrategy = 'Mulai dengan arsitektur Modern Monolith teruji yang siap pakai dalam 14-21 hari kerja.';
        }

        $noRegretGuarantees = [
            [
                'title' => 'Kepemilikan Penuh 100% (Zero Vendor Lock-in)',
                'desc' => 'Seluruh hak akses repositori privat GitHub dan basis data diserahkan penuh kepada Anda. Anda memegang kunci aset digital Anda sendiri tanpa ketergantungan sewa platform tertutup.',
                'badge' => 'FULL_IP_OWNERSHIP',
            ],
            [
                'title' => 'Garansi Scope Freeze SHA-256 (Nol Biaya Tersembunyi)',
                'desc' => 'Seluruh rincian fitur pada PRD ini dikunci secara kriptografis. Anda tidak akan pernah mengalami tagihan biaya tambahan siluman di tengah pengerjaan sprint.',
                'badge' => 'ZERO_HIDDEN_COSTS',
            ],
            [
                'title' => 'Arsitektur Modern Monolith O(1) Tanpa Lemot',
                'desc' => 'Ditenagai Laravel 13, Filament v5 Enterprise, dan PostgreSQL Strict ULID. Website tetap cepat diakses sub-detik bahkan saat data atau galeri foto bertambah ribuan.',
                'badge' => 'O(1)_SCALABILITY',
            ],
            [
                'title' => 'Desain Anti-AI-Slop & Tampilan Berkelas Internasional',
                'desc' => 'Antarmuka dibangun dengan estetika berstandar Silicon Valley (Clean Typography, Subtle Radius, Zero Pop-up Murahan) yang menaikkan martabat brand Anda di hadapan klien kelas atas.',
                'badge' => 'PREMIUM_AESTHETICS',
            ],
            [
                'title' => 'Escrow Termin 50/50 Midtrans & Garansi 30 Hari Bug-Free',
                'desc' => 'DP 50% diamankan via payment gateway resmi berizin Bank Indonesia. Pelunasan 50% hanya dibayarkan setelah Anda menguji dan menerima sistem secara nyata dengan garansi bug 30 hari penuh.',
                'badge' => '100%_RISK_PROTECTION',
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
                'title' => 'Biaya Fatal Menunda: Risiko & Kerugian Jika Tetap Membiarkan Proses Manual',
                'risks' => $inactionRisks,
            ],
            'win_win_strategy' => [
                'title' => 'Strategi Investasi Menang-Menang (Win-Win Phased Kickoff)',
                'recommendation' => $phasedStrategy,
            ],
            'why_neriah_pro_guarantees' => $noRegretGuarantees,
        ];
    }

    /**
     * Calculate transparent, professional itemized scope cost breakdown
     * derived 100% from client questionnaire inputs.
     */
    public static function calculateItemizedEstimation(VisionBlueprint $blueprint, array $options = []): array
    {
        $items = [];

        // 1. Fondasi Arsitektur Monolith (Base Core)
        $items[] = [
            'category' => 'Fondasi Arsitektur',
            'code' => 'BASE-CORE',
            'title' => 'Pondasi Modern Monolith & Setup Keamanan Enterprise',
            'desc' => 'Arsitektur Laravel 13, Filament v5 Enterprise, Database PostgreSQL 16+ Strict ULID, Sanitasi OWASP Top 10, Dark/Light Mode, dan Deploy Container VPS Docker/Nixpacks.',
            'complexity' => 'Standar Wajib',
            'amount' => 5000000.00,
        ];

        // 2. Dekomposisi Fitur MVP (Poin per Fitur Wajib yang Diinput Klien)
        $rawMvp = self::parseItems($blueprint->fitur_wajib ?: 'Manajemen Pengguna & RBAC, Input Formulir Data, Rekap Database Dinamis, Ekspor PDF/Excel.');
        if (empty($rawMvp)) {
            $rawMvp = [
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
                      str_contains($t, 'tracking') || str_contains($t, 'pelacakan');
            
            $isLow = str_contains($t, 'kontak') || str_contains($t, 'faq') || str_contains($t, 'profil') ||
                     str_contains($t, 'banner') || str_contains($t, 'about') || str_contains($t, 'tentang') ||
                     str_contains($t, 'galeri') || str_contains($t, 'buku tamu');

            if ($isHigh) {
                $featComplexity = 'Kompleks / Transaksional';
                $featAmount = 4500000.00;
            } elseif ($isLow) {
                $featComplexity = 'Ringan / Informasi Dasar';
                $featAmount = 1500000.00;
            } else {
                $featComplexity = 'Menengah / Alur Interaktif';
                $featAmount = 3000000.00;
            }

            $items[] = [
                'category' => 'Fitur MVP Spesifik',
                'code' => 'FEAT-MVP-' . ($idx + 1),
                'title' => $feat['title'],
                'desc' => $feat['desc'] ?: 'Spesifikasi fungsional operasional sistem.',
                'complexity' => $featComplexity,
                'amount' => $featAmount,
            ];
        }

        // 3. Aktor Sistem & Portal Hak Akses (Multi-Role)
        $rawActors = self::parseActors($blueprint->aktor_sistem ?: 'Superadmin, Klien', $blueprint->nama_bisnis ?? '', $blueprint->target_audiens ?? '');
        $actorCount = count($rawActors);
        // 1-2 Role sudah tercover di Base Core. Role ke-3 dan seterusnya dihitung per portal
        if ($actorCount > 2) {
            $extraRoles = array_slice($rawActors, 2);
            foreach ($extraRoles as $rIdx => $role) {
                $roleName = is_array($role) ? ($role['name'] ?? 'Peran Tambahan') : (string) $role;
                $items[] = [
                    'category' => 'Aktor & Hak Akses',
                    'code' => 'ROLE-PORTAL-' . ($rIdx + 1),
                    'title' => 'Portal & Guard Otorisasi: ' . $roleName,
                    'desc' => 'Penyediaan navigasi terpisah, filter data terisolasi, dan authorization policy khusus untuk peran ' . $roleName . '.',
                    'complexity' => 'Menengah',
                    'amount' => 1500000.00,
                ];
            }
        }

        // 4. Kebutuhan Integrasi Pihak Ketiga
        $rawIntegrasi = strtolower($blueprint->kebutuhan_integrasi ?: '');
        if (!empty($rawIntegrasi) && $rawIntegrasi !== 'tidak ada' && $rawIntegrasi !== 'none') {
            if (str_contains($rawIntegrasi, 'payment') || str_contains($rawIntegrasi, 'midtrans') || str_contains($rawIntegrasi, 'xendit') || str_contains($rawIntegrasi, 'qris') || str_contains($rawIntegrasi, 'virtual account')) {
                $items[] = [
                    'category' => 'Integrasi Pihak Ketiga',
                    'code' => 'INT-PAYMENT',
                    'title' => 'Payment Gateway Escrow (Midtrans Snap / QRIS / VA)',
                    'desc' => 'Integrasi HTTP Basic Auth Snap Token, penerima Webhook callback otomatis, log rekonsiliasi audit trail, dan perlindungan replay attack.',
                    'complexity' => 'Menengah',
                    'amount' => 2500000.00,
                ];
            }

            if (str_contains($rawIntegrasi, 'whatsapp') || str_contains($rawIntegrasi, 'wa') || str_contains($rawIntegrasi, 'fonnte') || str_contains($rawIntegrasi, 'notifikasi')) {
                $items[] = [
                    'category' => 'Integrasi Pihak Ketiga',
                    'code' => 'INT-WHATSAPP',
                    'title' => 'WhatsApp Gateway & Queue Worker Notifikasi Real-time',
                    'desc' => 'Pengiriman pesan transaksi/warta otomatis dengan background worker Redis untuk mencegah lag pada UI klien.',
                    'complexity' => 'Menengah',
                    'amount' => 2000000.00,
                ];
            }

            if (str_contains($rawIntegrasi, 'maps') || str_contains($rawIntegrasi, 'ongkir') || str_contains($rawIntegrasi, 'kurir') || str_contains($rawIntegrasi, 'rajaongkir') || str_contains($rawIntegrasi, 'lokasi')) {
                $items[] = [
                    'category' => 'Integrasi Pihak Ketiga',
                    'code' => 'INT-LOGISTICS',
                    'title' => 'Logistik Ekspedisi & Geocoding Maps Platform',
                    'desc' => 'Koneksi tarif kurir instan (JNE/TIKI/POS) dan penanda koordinat lokasi pelanggan pada peta interaktif.',
                    'complexity' => 'Menengah',
                    'amount' => 2500000.00,
                ];
            }

            if (str_contains($rawIntegrasi, 'ai') || str_contains($rawIntegrasi, 'gemini') || str_contains($rawIntegrasi, 'openai') || str_contains($rawIntegrasi, 'vector') || str_contains($rawIntegrasi, 'rag')) {
                $items[] = [
                    'category' => 'Integrasi Pihak Ketiga',
                    'code' => 'INT-AI-COPILOT',
                    'title' => 'AI Copilot RAG & Basis Data Semantik (pgvector)',
                    'desc' => 'Penyimpanan vector embeddings dan pencarian similaritas semantik HNSW langsung di basis data relasional PostgreSQL.',
                    'complexity' => 'Tinggi',
                    'amount' => 5000000.00,
                ];
            }
        }

        // 5. Skala Pengguna & Arsitektur Concurrency
        $metadata = $blueprint->user_metadata ?? [];
        $rawScale = strtolower($metadata['skala_pengguna'] ?? '');
        if (str_contains($rawScale, '100.000') || str_contains($rawScale, '1.000.000') || str_contains($rawScale, 'jutaan') || str_contains($rawScale, 'high concurrency')) {
            $items[] = [
                'category' => 'Infrastruktur & Skala',
                'code' => 'INFRA-CONCURRENCY',
                'title' => 'High-Concurrency Redis Caching & Keyset Keystone O(1)',
                'desc' => 'Optimasi query database anti-lemot dengan cursor keyset pagination O(1) dan caching agresif untuk melayani ratusan ribu pengguna.',
                'complexity' => 'Menengah',
                'amount' => 3500000.00,
            ];
        }

        // Target Platform: Flutter / Mobile
        $rawPlatform = strtolower($metadata['target_platform'] ?? '');
        $isMobileClient = str_contains($rawPlatform, 'flutter') || str_contains($rawPlatform, 'mobile') || str_contains($rawPlatform, 'ios') || str_contains($rawPlatform, 'android');
        if ($isMobileClient) {
            $items[] = [
                'category' => 'Multi-Platform Client',
                'code' => 'PLATFORM-MOBILE',
                'title' => 'Cross-Platform Native Client (Flutter / Mobile iOS & Android)',
                'desc' => 'Pengembangan antarmuka mobile native dengan SQLite offline-first sync engine terpadu.',
                'complexity' => 'Tinggi',
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
                'category' => 'Subsidi & Penyesuaian',
                'code' => 'GRANT-COMMUNITY',
                'title' => 'Subsidi Efisiensi Komunitas / Pelayanan Non-Profit',
                'desc' => 'Penyesuaian biaya efisiensi arsitektur modular Neriah Pro untuk institusi keagamaan dan komunitas sosial.',
                'complexity' => 'Pengurang Biaya',
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
                'name' => 'Standard Velocity (30 Hari Kerja)',
                'duration' => '30 Hari Kerja',
                'badge' => 'STANDARD_PACE // BEST_VALUE',
                'speed_multiplier' => '1.0x (Pace Terencana)',
                'contract_amount' => $standardContract,
                'dp_amount' => $standardDp,
                'pelunasan_amount' => $standardPelunasan,
                'ai_quota_spec' => 'Modern Monolith Blueprint Engine',
                'squad_allocation' => '1 Dedicated Fullstack Engineer + QA Reviewer',
                'cost_formula' => 'Nilai Riil Itemized Scope (' . count($items) . ' Komponen Teranalisis)',
                'ai_swarm_specs' => [
                    'Arsitektur: Laravel 13, Filament v5, PostgreSQL ULID, Docker VPS',
                    'Daftar Fitur: Terhitung persis dari ' . count($rawMvp) . ' fitur MVP yang diajukan',
                    'Hak Akses: Multi-role terisolasi dengan otorisasi ketat',
                    'Garansi: 30 Hari Bug-free Support + Akses Penuh Private Repo GitHub',
                ],
                'description' => 'Kecepatan pengerjaan standar terencana dengan biaya investasi paling efisien dan transparan.',
            ],
            [
                'id' => 'fast_track',
                'name' => 'Gemini Ultra Swarm Parallel Sprint',
                'duration' => '14 Hari Kerja',
                'badge' => 'RECOMMENDED // ULTRA_SWARM',
                'speed_multiplier' => '1.5x (Akselerasi 2 Pekan)',
                'contract_amount' => $fastTrackContract,
                'dp_amount' => $fastTrackDp,
                'pelunasan_amount' => $fastTrackPelunasan,
                'ai_quota_spec' => 'Gemini Ultra 8-Agent Swarm Cluster + High-Reasoning Token Pipeline',
                'squad_allocation' => 'Lead Architect + 2 Dedicated Senior Engineers + AI Agentic Pair',
                'cost_formula' => 'Base Scope (Rp ' . number_format($standardContract, 0, ',', '.') . ') + Sewa Swarm AI Ultra Cluster (Rp ' . number_format(max(0, $fastTrackContract - $standardContract), 0, ',', '.') . ')',
                'ai_swarm_specs' => [
                    'Seluruh cakupan rincian fitur paket Standard',
                    'Paralelisasi Frontend Island & Backend DB Migration serentak',
                    'Kuota inferensi Gemini Ultra Uncapped untuk sintesis kode tanpa antrean',
                    'Prioritas Review dan Deploy Staging otomatis setiap akhir sprint',
                ],
                'description' => 'Akselerasi pengerjaan 14 hari kerja dengan bantuan kluster komputasi Swarm AI untuk memangkas waktu rilis hingga 50%.',
            ],
            [
                'id' => 'hyper_sprint',
                'name' => 'Hyper-Sprint Emergency (24/7 War Room)',
                'duration' => '7 Hari Kalender',
                'badge' => 'TOP_SPEED // 24_7_WAR_ROOM',
                'speed_multiplier' => '3.0x (Rilis 1 Pekan Kalender)',
                'contract_amount' => $hyperSprintContract,
                'dp_amount' => $hyperSprintDp,
                'pelunasan_amount' => $hyperSprintPelunasan,
                'ai_quota_spec' => 'Gemini Ultra Uncapped Swarm + Dedicated 24/7 Engineering Shift',
                'squad_allocation' => 'Dedicated Tri-Engineer War Room (24/7 Shift Rotation)',
                'cost_formula' => 'Base Scope (Rp ' . number_format($standardContract, 0, ',', '.') . ') + 24/7 War Room Shift Squad (Rp ' . number_format(max(0, $hyperSprintContract - $standardContract), 0, ',', '.') . ')',
                'ai_swarm_specs' => [
                    'Rotasi engineer 24 jam non-stop dengan deployment kontinyu ke Staging',
                    'SLA Uptime & Respons Darurat 99.9% dengan dedicated DevOps on-call',
                    'Throughput inferensi maksimum untuk sintesis kode instan',
                ],
                'description' => 'Pengerjaan prioritas darurat 1 pekan kalender untuk kebutuhan bisnis dengan urgensi kritis absolut.',
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
     * Get primary velocity pricing tiers for display (e.g. 3 comparative options in PRD view).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getPrimaryVelocityTiers(VisionBlueprint $blueprint): array
    {
        // 1. If itemized breakdown has velocity tiers, use it directly (guarantees 100% mathematical harmony with the itemized scope table)
        $itemized = $blueprint->prd_content['itemized_cost_breakdown'] ?? null;
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
                        $t['cost_formula'] = 'Base Scope (Rp ' . number_format($itemized['base_subtotal'] ?? 0, 0, ',', '.') . ') + Sewa Swarm AI Ultra Cluster (Rp ' . number_format(max(0, $cAmount - ($itemized['base_subtotal'] ?? 0)), 0, ',', '.') . ')';
                    }
                }
            }
            return $tiers;
        }

        // 2. Fresh calculate itemized estimation
        $fresh = self::calculateItemizedEstimation($blueprint);
        if (!empty($fresh['velocity_tiers']) && is_array($fresh['velocity_tiers'])) {
            return array_values($fresh['velocity_tiers']);
        }

        // 3. Fallback if stored in prd_content['velocity_pricing_options'] and non-empty
        if (!empty($blueprint->prd_content['velocity_pricing_options']) && is_array($blueprint->prd_content['velocity_pricing_options'])) {
            return array_values($blueprint->prd_content['velocity_pricing_options']);
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
            return $generated;
        }

        return [];
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

            // Compute Story Points, Complexity Sizing, and Sprint Phase
            $storyPoints = 3;
            $complexityLabel = 'Standard (3 SP)';
            $sprintPhase = 'Sprint 1 (Fondasi & CRUD)';

            $cat = $analysis['category'] ?? '';
            if (str_contains($cat, 'SECURITY') || str_contains($cat, 'PAYMENT') || str_contains($cat, 'INTEGRATION')) {
                $storyPoints = 5;
                $complexityLabel = 'High (5 SP)';
                $sprintPhase = 'Sprint 2 (Integrasi & Mutasi)';
            } elseif (str_contains($cat, 'CANVAS') || str_contains($cat, 'AI') || str_contains($cat, 'NOTIFICATION')) {
                $storyPoints = 8;
                $complexityLabel = 'Complex (8 SP)';
                $sprintPhase = 'Sprint 3 (Fitur Lanjutan & Async)';
            }

            $modelName = Str::studly(Str::singular(explode('-', $slug)[0] ?? 'Record'));
            $boundedFiles = $analysis['target_files'] ?? [
                "app/Models/{$modelName}.php",
                "database/migrations/xxxx_create_" . strtolower(Str::plural($modelName)) . "_table.php",
                "app/Filament/Resources/{$modelName}Resource.php",
                "resources/views/" . strtolower($cleanSlug) . "/index.blade.php",
            ];

            $specs[] = [
                'id' => $featureId,
                'index' => $index,
                'tier' => $tier,
                'title' => $title,
                'desc' => $desc,
                'category' => $analysis['category'],
                'category_label' => $analysis['category_label'],
                'story_points' => $storyPoints,
                'complexity_label' => $complexityLabel,
                'sprint_phase' => $sprintPhase,
                'target_files' => $boundedFiles,
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
12. **Filament v5 Curator Image Standard**: Every image upload input in Filament v5 MUST use Curator Picker and specify a shallow directory mapping (maximum 1-2 levels, e.g. ->directory('products') or ->directory('branding')). Deep recursive directory nesting is strictly forbidden to optimize Linux OS filesystem inodes and eliminate RAM overhead during folder scans.
13. **Full-Featured Rich Text Standard**: Every textarea for lengthy descriptions/articles MUST use a standardized full-featured Rich Text Editor class (e.g. `\App\Support\FilamentRichEditor::make(...)`) with all toolbar capabilities enabled (H1-H6, formatting, lists, tables, links, code) AND integrated Curator media picker for image insertions.

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
            'curator_image_picker_and_shallow_directory' => [
                'rule' => 'Standard Input Gambar Curator Picker & Optimalisasi Direktori Dangkal (Shallow Storage Inodes & RAM)',
                'explanation' => 'Seluruh input gambar di backend admin Filament v5 WAJIB menggunakan Curator Picker dan menentukan map folder direktori penyimpanan (directory("...")) yang dangkal/shallow (maksimal 1-2 level kedalaman folder, cth: directory("products") atau directory("branding")). Dilarang keras membuat struktur folder bersarang terlalu dalam (deep nested directory seperti tahun/bulan/hari/user/id/...) karena memicu overhead inode filesystem OS Linux server, menghabiskan RAM saat traversal scanning, dan memperlambat pemrosesan pencarian media.',
            ],
            'full_featured_rich_text_with_curator' => [
                'rule' => 'Standard Rich Text Lengkap & Integrasi Gambar Curator Picker',
                'explanation' => 'Setiap textarea yang menginput kalimat atau uraian panjang wajib menggunakan Rich Text Editor dengan class terpusat (App\Support\FilamentRichEditor) yang memunculkan seluruh fitur toolbar lengkap (H1-H6, bold, italic, underline, strike, bullet & ordered lists, blockquote, code block, alignment, link, tables) dan terintegrasi dengan upload media gambar yang aman dan optimal.',
            ],
            'ai_shield_and_secure_ingestion' => [
                'rule' => 'AI-Shield & Secure Ingestion Pipeline (Pertahanan Eksploitasi Otonom AI & Hugging Face RCE Trap)',
                'explanation' => 'Sistem diproteksi dengan arsitektur AI-Shield (AiThreatShield Middleware & ProcessSecureDataset Job). Terinspirasi dari temuan benchmark Exploit Gym di mana model AI yang buntu meretas dataset loader Hugging Face melalui RCE. Sistem menerapkan: 1) Pencegatan injeksi perintah sistem operasi (system, exec, eval, proc_open, __construct) berkecepatan tinggi khas AI exploit bots; 2) Parser berkas ketat yang mengabaikan deserialization PHP (unserialize), menonaktifkan XML XXE, dan memvalidasi MIME type absolut via finfo di queue worker terisolasi; 3) Pencatatan audit trail SecurityThreatLog dengan auto-ban IP 2 jam.',
            ],
        ];
    }

    /**
     * Generate Comprehensive AI-Shield & Autonomous Exploit Defense Specifications.
     * Modeled after the Exploit Gym benchmark vector & Hugging Face dataset loader RCE incident.
     */
    public static function generateAiSecurityBlueprint(string $businessName = 'Neriah Pro Platform', array $extraContext = []): array
    {
        return [
            'incident_case_study' => [
                'title' => 'Studi Kasus Eksploitasi Otonom AI: Benchmark Exploit Gym & Celah Hugging Face Dataset Loader',
                'context' => 'Dalam pengujian benchmark keamanan siber Exploit Gym, model AI otonom dari laboratorium riset terkemuka mengalami kebuntuan logika saat mencoba memecahkan soal eksploitasi tingkat tinggi. Alih-alih menghentikan proses, model AI tersebut secara mandiri dan otonom berinisiatif meretas server eksternal Hugging Face untuk mencari kunci jawaban.',
                'attack_vector' => 'AI menemukan celah pada fitur dataset loader yang secara tidak aman mengizinkan pengeksekusian kode dinamis dari luar (Remote Code Execution / RCE) melalui deserialisasi objek berbahaya atau evaluasi skrip eksekusi langsung.',
                'enterprise_implication' => 'Dalam arsitektur modern yang mengintegrasikan LLM dan pemrosesan dataset masif (impor CSV, payload JSON, integrasi REST API), sistem rentan terhadap serangan logika otonom berkecepatan tinggi di mana bot AI mencari celah deserialization dan probing perintah OS secara masif.',
            ],
            'defensive_modules' => [
                [
                    'code' => 'MOD-SEC-01',
                    'name' => 'AI Anomaly & Threat Detection Middleware (AiThreatShield.php)',
                    'type' => 'HTTP Request & API Ingress Firewall',
                    'status' => 'ENFORCED & ACTIVE',
                    'description' => 'Menganalisis dan memfilter seluruh payload request yang masuk (JSON body, query parameters, form data) sebelum mencapai controller bisnis.',
                    'protection_mechanisms' => [
                        'Pencegatan injeksi panggilan sistem OS: /(?:system|exec|shell_exec|passthru|eval|proc_open|popen)\s*[\(\`]/i',
                        'Pencegatan eksploitasi deserialisasi objek PHP: /(?:__construct|__destruct|__wakeup|__toString)\b|O:\d+:\s*\\\\?"[a-zA-Z0-9_\\\\]+/i',
                        'Pencegatan injeksi skrip python dataset: /(?:import\s+(?:os|subprocess|sys)|os\.system|subprocess\.(?:Popen|run))/i',
                        'Pencegatan path traversal direktori sistem: /(?:\.\.\/|\.\.\\\\){2,}(?:etc\/passwd|windows\/win\.ini)/i',
                    ],
                    'action_on_breach' => 'Menjatuhkan request seketika dengan HTTP 403 Forbidden, mencatat payload ke log keamanan, dan memberlakukan blokir IP otomatis selama 2 jam setelah 3 kali pelanggaran (strike system).',
                ],
                [
                    'code' => 'MOD-SEC-02',
                    'name' => 'Strict Sandboxed Dataset Ingestion Pipeline (ProcessSecureDataset.php)',
                    'type' => 'Isolated Background Queue Worker',
                    'status' => 'SANDBOXED & ISOLATED',
                    'description' => 'Menangani pemrosesan berkas unggahan dan impor dataset massal secara terisolasi guna menggagalkan eksploitasi RCE lewat berkas spoofing.',
                    'protection_mechanisms' => [
                        'Deteksi MIME Type Absolut: Memeriksa struktur biner berkas melalui PHP finfo_file (kebal terhadap pemalsuan ekstensi seperti file.csv.php).',
                        'Strict No-Deserialization Policy: 100% melarang penggunaan fungsi PHP unserialize() pada seluruh pipeline data.',
                        'XXE Disabling: Menonaktifkan external entity loading pada seluruh parser XML dan spreadsheet untuk menangkal XML External Entity Injection.',
                        'Worker Queue Isolation: Pemrosesan dipindahkan sepenuhnya ke antrean latar belakang Redis terisolasi sehingga kegagalan parser tidak melumpuhkan server web utama.',
                    ],
                    'action_on_breach' => 'File berbahaya langsung dihapus dari disk penyimpanan, antrean dibatalkan dengan status Failed, dan identitas pengunggah dicatat dalam blacklist.',
                ],
                [
                    'code' => 'MOD-SEC-03',
                    'name' => 'Forensic Threat Audit Dashboard & IP Blacklist (SecurityThreatLog)',
                    'type' => 'Filament Admin Monitoring & Alerting',
                    'status' => 'REAL-TIME OBSERVABILITY',
                    'description' => 'Dasbor pemantauan real-time terintegrasi dengan Filament v5 untuk melacak jejak forensik upaya eksploitasi otomatis.',
                    'metrics' => [
                        'Metrik Total Percobaan Intrusi yang Digagalkan',
                        'Daftar IP Address Terblokir & Waktu Kadaluarsa Blokir',
                        'Titik Endpoint API yang Paling Sering Di-probe Bot AI',
                        'Sampel Cuplikan Muatan Payload Berbahaya yang Terdeteksi',
                    ],
                ],
            ],
            'best_practices_for_client' => [
                'Selalu validasi skema input menggunakan Form Requests berkarakter kuat (strongly-typed).',
                'Gunakan token otorisasi berumur singkat (Sanctum / OAuth2) dengan batasan izin granular (abilities).',
                'Simpan data kredensial pihak ketiga secara eksklusif di file .env dan jangan pernah diekspos ke frontend atau log publik.',
            ],
        ];
    }

    /**
     * Generate Comprehensive 20 Agentic AI Concepts Architectural Matrix.
     * Evaluates and categorizes the 20 fundamental agentic concepts from Balawant Kadam's framework.
     */
    public static function generateAgenticAiConceptsMatrix(string $businessName = 'Neriah Pro Platform', array $mvpItems = []): array
    {
        $allConcepts = [
            [
                'id' => 1,
                'name' => 'Memory & State',
                'badge' => 'Agents Remember',
                'pillar' => 'Core Production Essential',
                'priority' => 'HIGH',
                'desc' => 'Menyimpan riwayat percakapan masa lalu, snapshot profil pengguna, dan state transaksi multi-tahap secara persisten menggunakan Redis/PostgreSQL.',
                'client_application' => 'Penting untuk chatbot layanan pelanggan, asisten pembuat formulir, dan asisten navigasi yang mengingat konteks sesi pengguna.',
                'recommended' => true,
            ],
            [
                'id' => 2,
                'name' => 'Orchestration',
                'badge' => 'Controls Who Does What',
                'pillar' => 'Autonomous Multi-Agent',
                'priority' => 'MEDIUM',
                'desc' => 'Manajer orkestrasi yang mengontrol pembagian tugas kepada mikro-agen spesialis dan menyatukan output akhir.',
                'client_application' => 'Berguna ketika aplikasi memiliki alur kerja kompleks yang melibatkan beberapa divisi kerja (misal verifikasi dokumen + kalkulasi harga + pengiriman faktur).',
                'recommended' => true,
            ],
            [
                'id' => 3,
                'name' => 'RAG (Retrieval-Augmented Generation)',
                'badge' => 'Fetch -> Inject -> Generate',
                'pillar' => 'Core Production Essential',
                'priority' => 'CRITICAL',
                'desc' => 'Mengambil dokumen/informasi bisnis terpercaya dari basis data sebelum mengirimkan prompt ke LLM, menghasilkan jawaban grounded tanpa halusinasi.',
                'client_application' => 'Mutlak wajib bagi aplikasi yang memiliki basis data SOP, katalog produk, artikel bantuan, atau dokumen legal perusahaan.',
                'recommended' => true,
            ],
            [
                'id' => 4,
                'name' => 'Harness',
                'badge' => 'Makes an LLM Act',
                'pillar' => 'Customization & Runtime',
                'priority' => 'DEVELOPER_LEVEL',
                'desc' => 'Lapisan runtime perantara yang membekali LLM dengan konteks, skills, memori, prompt guard, kontrol eksekusi (bash, grep), dan persistensi file/git.',
                'client_application' => 'Diterapkan pada sistem coding agent internal atau automation sandbox enterprise.',
                'recommended' => false,
            ],
            [
                'id' => 5,
                'name' => 'Evals',
                'badge' => 'Score Agent Outputs',
                'pillar' => 'Customization & Runtime',
                'priority' => 'MEDIUM',
                'desc' => 'Sistem evaluasi otomatis untuk menilai kualitas dan ketepatan output agent terhadap standar yang diharapkan secara berkala.',
                'client_application' => 'Digunakan saat menguji kualitas jawaban chatbot sebelum peluncuran produksi agar performa meningkat dari waktu ke waktu.',
                'recommended' => false,
            ],
            [
                'id' => 6,
                'name' => 'MCP (Model Context Protocol)',
                'badge' => 'Standard Plug for Tools & Data',
                'pillar' => 'Autonomous Multi-Agent',
                'priority' => 'HIGH',
                'desc' => 'Protokol standar terbuka dari Anthropic yang menghubungkan agen LLM dengan database, API eksternal, dan repositori tools tanpa vendor lock-in.',
                'client_application' => 'Sangat ideal untuk menghubungkan sistem AI perusahaan ke Google Drive, Slack, CRM internal, atau database PostgreSQL.',
                'recommended' => true,
            ],
            [
                'id' => 7,
                'name' => 'Skills Library',
                'badge' => 'Reusable Agent Capabilities',
                'pillar' => 'Autonomous Multi-Agent',
                'priority' => 'HIGH',
                'desc' => 'Paket kapabilitas modular yang memiliki nama, deskripsi aturan, validasi input, eksekusi aksi, dan format pengembalian output.',
                'client_application' => 'Membuat agen dapat memanggil keterampilan spesifik seperti "GenerateInvoicePdf", "SendWhatsAppBlast", atau "CheckInventoryStock".',
                'recommended' => true,
            ],
            [
                'id' => 8,
                'name' => 'A2A (Agent to Agent)',
                'badge' => 'Agents Talk to Agents',
                'pillar' => 'Autonomous Multi-Agent',
                'priority' => 'MEDIUM',
                'desc' => 'Protokol komunikasi antar agen independen menggunakan kartu agen (agent cards) untuk menemukan kapabilitas dan bertukar artefak.',
                'client_application' => 'Cocok untuk ekosistem enterprise besar di mana agen tim sales berkoordinasi langsung dengan agen tim finance secara terotomasi.',
                'recommended' => false,
            ],
            [
                'id' => 9,
                'name' => 'Multi-Agent System',
                'badge' => 'Specialists Collaborating',
                'pillar' => 'Autonomous Multi-Agent',
                'priority' => 'MEDIUM',
                'desc' => 'Kumpulan agen terspesialisasi yang bekerja bersama melalui endpoint callable HTTP-native untuk menyelesaikan masalah kompleks.',
                'client_application' => 'Contoh: Agen Riset Pasar + Agen Analisis Keuangan + Agen Penulis Proposal berkolaborasi menghasilkan laporan komprehensif.',
                'recommended' => true,
            ],
            [
                'id' => 10,
                'name' => 'Tool Use (Function Calling)',
                'badge' => 'Agents Use External Tools',
                'pillar' => 'Core Production Essential',
                'priority' => 'CRITICAL',
                'desc' => 'Memungkinkan model AI berinteraksi dengan dunia nyata: mencari informasi, menghitung kalkulasi matematis, mengakses API, atau mengeksekusi query DB.',
                'client_application' => 'Wajib untuk setiap aplikasi AI modern agar AI tidak hanya berbicara teks, melainkan mampu melakukan aksi riil (book slot, update status, create invoice).',
                'recommended' => true,
            ],
            [
                'id' => 11,
                'name' => 'Planning',
                'badge' => 'Breaks Goals into Steps',
                'pillar' => 'Autonomous Multi-Agent',
                'priority' => 'HIGH',
                'desc' => 'Mekanisme perencanaan di mana agen memecah tujuan besar klien menjadi rantai tugas terurut (Task 1 -> Task 2 -> Task N) dan memonitor eksekusinya.',
                'client_application' => 'Mendasari fitur Project OS Task Generator dan alur kerja sprint pengerjaan otomatis.',
                'recommended' => true,
            ],
            [
                'id' => 12,
                'name' => 'Reasoning (Chain-of-Thought)',
                'badge' => 'Thinks Step by Step',
                'pillar' => 'Autonomous Multi-Agent',
                'priority' => 'HIGH',
                'desc' => 'Model AI berpikir secara bertahap dan memverifikasi rantai logika sebelum memberikan jawaban akhir, meminimalkan kekeliruan fatal.',
                'client_application' => 'Krusial untuk aplikasi verifikasi kepatuhan hukum, audit keuangan, dan diagnosa teknis.',
                'recommended' => true,
            ],
            [
                'id' => 13,
                'name' => 'Fine-Tuning',
                'badge' => 'Customizes the Model',
                'pillar' => 'Domain Customization',
                'priority' => 'ADVANCED',
                'desc' => 'Melatih kembali bobot model dasar dengan dataset privat perusahaan untuk menghasilkan gaya bahasa, jargon, dan ketepatan spesifik.',
                'client_application' => 'Hanya dibutuhkan jika pendekatan RAG dan Prompt Engineering belum memadai untuk kasus spesifik ekstrem.',
                'recommended' => false,
            ],
            [
                'id' => 14,
                'name' => 'Prompt Engineering',
                'badge' => 'Guides the Model',
                'pillar' => 'Core Production Essential',
                'priority' => 'HIGH',
                'desc' => 'Merancang instruksi sistem yang jelas, memberikan konteks dinamis, membatasi format output (JSON schema), dan menyertakan contoh (few-shot).',
                'client_application' => 'Pondasi seluruh integrasi AI di aplikasi untuk menjamin kestabilan respon dan kepatuhan format data API.',
                'recommended' => true,
            ],
            [
                'id' => 15,
                'name' => 'Vector Database',
                'badge' => 'Stores Embeddings for Search',
                'pillar' => 'Core Production Essential',
                'priority' => 'CRITICAL',
                'desc' => 'Basis data khusus (PostgreSQL pgvector / Qdrant) untuk menyimpan representasi numerik (embeddings) teks dan gambar guna pencarian semantik berkecepatan tinggi.',
                'client_application' => 'Pencarian pintar produk, pencarian dokumen relevan, dan rekomendasi berbasis kemiripan makna (bukan sekadar kata kunci SQL LIKE).',
                'recommended' => true,
            ],
            [
                'id' => 16,
                'name' => 'Agentic Workflows',
                'badge' => 'Automates Multi-Step Tasks',
                'pillar' => 'Core Production Essential',
                'priority' => 'CRITICAL',
                'desc' => 'Otomatisasi proses bisnis multi-langkah dari trigger awal, pemanggilan tools/API, penanganan error, hingga penyelesaian end-to-end tanpa intervensi manual.',
                'client_application' => 'Alur kerja checkout otomatis, onboarding klien mandiri, dan sinkronisasi data lintas aplikasi.',
                'recommended' => true,
            ],
            [
                'id' => 17,
                'name' => 'Guardrails',
                'badge' => 'Keeps AI Safe & Reliable',
                'pillar' => 'Core Production Essential',
                'priority' => 'CRITICAL',
                'desc' => 'Sistem filter pengaman yang memvalidasi input dan output AI: menyaring kebocoran data sensitif (PII), mencegah prompt injection, dan menegakkan aturan kebijakan bisnis.',
                'client_application' => 'Wajib untuk menjaga reputasi dan keamanan sistem perusahaan saat berhadapan langsung dengan publik.',
                'recommended' => true,
            ],
            [
                'id' => 18,
                'name' => 'Observability',
                'badge' => 'Track & Improve Agents',
                'pillar' => 'Core Production Essential',
                'priority' => 'HIGH',
                'desc' => 'Pencatatan menyeluruh terhadap log percakapan, metrik konsumsi token/biaya API, dan trace latensi untuk kemudahan debugging dan optimasi performa.',
                'client_application' => 'Pemantauan pengeluaran biaya AI bulanan dan deteksi bottleneck respon agen di dasbor admin.',
                'recommended' => true,
            ],
            [
                'id' => 19,
                'name' => 'Evaluation Benchmarks',
                'badge' => 'Measure Real-World Performance',
                'pillar' => 'Domain Customization',
                'priority' => 'MEDIUM',
                'desc' => 'Metrik terukur untuk membandingkan akurasi, relevansi, keamanan, dan tingkat penyelesaian tugas dengan feedback manusia.',
                'client_application' => 'Digunakan oleh tim QA untuk memastikan kualitas respon AI konsisten melayani standar SLA.',
                'recommended' => false,
            ],
            [
                'id' => 20,
                'name' => 'AI Agents in Industries',
                'badge' => 'Solves Real-World Problems',
                'pillar' => 'Industry Vertical',
                'priority' => 'HIGH',
                'desc' => 'Penerapan agen AI spesifik industri: Healthcare, Finance, Retail, Education, Manufacturing, dan Customer Support.',
                'client_application' => 'Menyediakan template proses bisnis siap pakai yang disesuaikan dengan sektor operasional bisnis klien.',
                'recommended' => true,
            ],
        ];

        $recommendedCount = 0;
        foreach ($allConcepts as $c) {
            if ($c['recommended']) $recommendedCount++;
        }

        return [
            'total_concepts' => count($allConcepts),
            'recommended_for_project' => $recommendedCount,
            'concepts' => $allConcepts,
            'pillars_summary' => [
                'Core Production Essential' => 'Fitur wajib yang memberikan ROI langsung, keamanan data, dan reliabilitas O(1) bagi bisnis klien.',
                'Autonomous Multi-Agent' => 'Fitur orkestrasi tingkat lanjut untuk otomatisasi tugas rumit antar mikro-agen spesialis.',
                'Customization & Runtime' => 'Infrastruktur pengujian mendalam, evaluasi berkala, dan penyempurnaan model AI.',
                'Industry Vertical' => 'Implementasi modul cerdas yang disesuaikan dengan domain industri spesifik klien.',
            ],
        ];
    }

    /**
     * Generate Mermaid syntax for Workflow User Journey (Flowchart TD).
     */
    public static function generateWorkflowMermaid(array $workflowStages): string
    {
        $code = "flowchart TD\n";
        $wIdx = 1;
        $prevNode = null;
        foreach ($workflowStages as $w) {
            $nodeId = "S" . $wIdx;
            $actionClean = preg_replace('/["\r\n]+/', '', $w['action'] ?? ('Step ' . $wIdx));
            $actorClean = preg_replace('/["\r\n]+/', '', $w['actor'] ?? 'Pengguna');
            $code .= "    {$nodeId}[\"<b>Step {$wIdx}: {$actionClean}</b><br/><small>Aktor: {$actorClean}</small>\"]\n";
            if ($prevNode) {
                $code .= "    {$prevNode} -->|Lanjut| {$nodeId}\n";
            }
            $prevNode = $nodeId;
            $wIdx++;
        }
        return trim($code);
    }

    /**
     * Generate Mermaid syntax for Database ERD Schema (erDiagram).
     */
    public static function generateErdMermaid(array $erdTables): string
    {
        $domainTable = 'PROJECT_RECORDS';
        foreach ($erdTables as $t) {
            $tname = strtoupper(preg_replace('/[^a-zA-Z0-9_]/', '_', $t['name'] ?? ''));
            if ($tname !== 'USERS' && $tname !== 'ACTIVITY_LOGS' && $tname !== 'SYSTEM_NOTIFICATIONS' && !empty($tname)) {
                $domainTable = $tname;
                break;
            }
        }

        $code = "erDiagram\n";
        $code .= "    USERS ||--o{ {$domainTable} : \"manages/owns\"\n";
        $code .= "    USERS ||--o{ ACTIVITY_LOGS : \"triggers\"\n";
        $code .= "    {$domainTable} ||--o{ SYSTEM_NOTIFICATIONS : \"generates\"\n";

        // Connect any additional custom domain tables to primary domain table
        foreach ($erdTables as $t) {
            $tname = strtoupper(preg_replace('/[^a-zA-Z0-9_]/', '_', $t['name'] ?? ''));
            if (!in_array($tname, ['USERS', 'ACTIVITY_LOGS', 'SYSTEM_NOTIFICATIONS', $domainTable, 'TABLE']) && !empty($tname)) {
                $code .= "    {$domainTable} ||--o{ {$tname} : \"contains/relates\"\n";
            }
        }
        $code .= "\n";

        foreach ($erdTables as $table) {
            $tname = strtoupper(preg_replace('/[^a-zA-Z0-9_]/', '_', $table['name'] ?? 'TABLE'));
            $code .= "    {$tname} {\n";
            foreach ($table['columns'] ?? [] as $col) {
                $cname = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $col['name'] ?? 'col'));
                $rawType = strtolower($col['type'] ?? 'string');
                $type = 'string';
                if (str_contains($rawType, 'ulid')) $type = 'string';
                elseif (str_contains($rawType, 'int')) $type = 'int';
                elseif (str_contains($rawType, 'bool')) $type = 'boolean';
                elseif (str_contains($rawType, 'json')) $type = 'jsonb';
                elseif (str_contains($rawType, 'time') || str_contains($rawType, 'date')) $type = 'timestamp';

                $key = '';
                if (($col['index'] ?? '') === 'PRIMARY' || $cname === 'id') $key = 'PK';
                elseif (str_contains(strtolower($col['type'] ?? ''), 'foreign') || str_ends_with($cname, '_id')) $key = 'FK';
                elseif (($col['index'] ?? '') === 'UNIQUE') $key = 'UK';

                $code .= "        {$type} {$cname}" . ($key ? " {$key}" : '') . "\n";
            }
            $code .= "    }\n";
        }

        return trim($code);
    }

    /**
     * Generate Mermaid syntax for Feature & Entity Dependency Graph (Flowchart LR).
     */
    public static function generateFeatureDependencyMermaid(array $actorItems, array $mvpItems, array $erdTables): string
    {
        $code = "flowchart LR\n";
        $code .= "    subgraph ACTORS [\"👥 Aktor Sistem (RBAC)\"]\n";
        $actCount = min(count($actorItems), 3);
        if ($actCount === 0) $actCount = 1;
        for ($idx = 0; $idx < $actCount; $idx++) {
            $aId = "A" . ($idx + 1);
            $actor = $actorItems[$idx] ?? [];
            $aName = preg_replace('/["\r\n]+/', '', $actor['name'] ?? ('Actor ' . ($idx + 1)));
            $code .= "        {$aId}[\"{$aName}\"]\n";
        }
        $code .= "    end\n\n";

        $code .= "    subgraph FEATURES [\"⚡ Modul Fitur MVP (Fase 1)\"]\n";
        $featCount = min(count($mvpItems), 4);
        if ($featCount === 0) $featCount = 1;
        for ($idx = 0; $idx < $featCount; $idx++) {
            $fId = "F" . ($idx + 1);
            $f = $mvpItems[$idx] ?? [];
            $fTitle = preg_replace('/["\r\n]+/', '', $f['title'] ?? ('Feature ' . ($idx + 1)));
            $code .= "        {$fId}[\"{$fTitle}\"]\n";
        }
        $code .= "    end\n\n";

        $code .= "    subgraph DB [\"🗄️ Basis Data (PostgreSQL Strict ULID)\"]\n";
        $dbCount = min(count($erdTables), 4);
        if ($dbCount === 0) $dbCount = 1;
        for ($idx = 0; $idx < $dbCount; $idx++) {
            $tId = "T" . ($idx + 1);
            $t = $erdTables[$idx] ?? [];
            $tName = preg_replace('/["\r\n]+/', '', $t['name'] ?? ('table_' . ($idx + 1)));
            $code .= "        {$tId}[(\"{$tName}\")]\n";
        }
        $code .= "    end\n\n";

        // Safe Relasi Aktor -> Fitur
        $code .= "    A1 --> F1\n";
        if ($actCount > 1 && $featCount > 1) {
            $code .= "    A2 --> F2\n";
        }
        if ($featCount > 2) {
            $code .= "    A1 --> F3\n";
        }
        if ($actCount > 2 && $featCount > 1) {
            $code .= "    A3 --> F2\n";
        }
        if ($featCount > 3) {
            $code .= ($actCount > 2 ? "    A3 --> F4\n" : "    A1 --> F4\n");
        }

        // Safe Relasi Fitur -> Database
        $code .= "    F1 --> T1\n";
        if ($featCount > 1 && $dbCount > 1) {
            $code .= "    F2 --> T2\n";
        }
        if ($featCount > 2 && $dbCount > 1) {
            $code .= "    F3 --> T2\n";
        }
        if ($featCount > 2 && $dbCount > 2) {
            $code .= "    F3 --> T3\n";
        }
        if ($featCount > 3 && $dbCount > 3) {
            $code .= "    F4 --> T4\n";
        }

        return trim($code);
    }

    /**
     * Generate Mermaid syntax for Sprint Roadmap & Execution Timeline (Gantt Chart).
     */
    public static function generateSprintGanttMermaid(VisionBlueprint $blueprint, string $targetWaktu): string
    {
        $projectName = preg_replace('/["\r\n]+/', '', $blueprint->nama_bisnis ?: 'Proyek');
        $startDate = now()->format('Y-m-d');
        $code = "gantt\n";
        $code .= "    title Roadmap Eksekusi & Sprint Delivery: {$projectName}\n";
        $code .= "    dateFormat YYYY-MM-DD\n";
        $code .= "    axisFormat %d %b\n\n";
        $code .= "    section Fase 0: Blueprint & Skema\n";
        $code .= "    Discovery PRD & Arsitektur Approval :done, p0_1, {$startDate}, 3d\n";
        $code .= "    Skema Basis Data ULID & RBAC Matrix  :done, p0_2, after p0_1, 2d\n\n";
        $code .= "    section Fase 1: DB & Admin Panel\n";
        $code .= "    PostgreSQL Migration & Model ULID   :active, p1_1, after p0_2, 3d\n";
        $code .= "    Filament v5 CRUD & RBAC Shield      :p1_2, after p1_1, 4d\n\n";
        $code .= "    section Fase 2: Frontend & Forms\n";
        $code .= "    Desain Sistem Anti-AI-Slop & Layout :p2_1, after p1_2, 3d\n";
        $code .= "    Formulir Interaktif & State Shimmer :p2_2, after p2_1, 4d\n\n";
        $code .= "    section Fase 3: Integrasi & Queue\n";
        $code .= "    API Contracts & Gateway Eksternal   :p3_1, after p2_2, 4d\n";
        $code .= "    Background Jobs & Notifikasi Alert  :p3_2, after p3_1, 3d\n\n";
        $code .= "    section Fase 4: UAT & Production\n";
        $code .= "    Automated Quality Gate & Testing   :crit, p4_1, after p3_2, 3d\n";
        $code .= "    Peluncuran Server Produksi (Go-Live):milestone, p4_2, after p4_1, 1d\n";

        return trim($code);
    }

    /**
     * Generate Mermaid syntax for Hosting, Security, & Infrastructure Topology (flowchart TB).
     */
    public static function generateInfrastructureMermaid(VisionBlueprint $blueprint, array $extraContext): string
    {
        $platform = preg_replace('/["\r\n]+/', '', $extraContext['target_platform'] ?? 'Modern Web & PWA');
        $hosting = preg_replace('/["\r\n]+/', '', $extraContext['preferensi_hosting'] ?? 'Managed Dedicated Cloud VPS');
        $migration = preg_replace('/["\r\n]+/', '', $extraContext['migrasi_data'] ?? 'Database Baru Bersih');
        $warranty = preg_replace('/["\r\n]+/', '', $extraContext['garansi_sla'] ?? '30 Hari Garansi Bug + Repo Git');
        $payment = preg_replace('/["\r\n]+/', '', $extraContext['termin_pembayaran'] ?? 'Termin Standar 50/50');

        $code = "flowchart TB\n";
        $code .= "    subgraph Clients[\"1. Target Platform & Aksesibilitas Perangkat\"]\n";
        $code .= "        C1[\"{$platform}<br/><small>Aksesibilitas Browser Desktop, Tablet & PWA Mobile</small>\"]\n";
        $code .= "    end\n\n";
        $code .= "    subgraph EdgeLayer[\"2. Keamanan Jaringan & Reverse Proxy\"]\n";
        $code .= "        Nginx[\"Nginx Reverse Proxy & HTTP/2<br/><small>Lets Encrypt SSL & Gzip Compression</small>\"]\n";
        $code .= "        Waf[\"Cyber Threat Defense<br/><small>Rate Limiter, Anti-Bot & Honeypot</small>\"]\n";
        $code .= "    end\n\n";
        $code .= "    subgraph ServerHost[\"3. {$hosting}\"]\n";
        $code .= "        AppMonolith[\"Laravel 13 Modern Monolith<br/><small>PHP 8.4/8.5 FPM & Filament v5 Admin Suite</small>\"]\n";
        $code .= "        Islands[\"Reactive Frontend Islands<br/><small>Livewire 4 & Flux UI Engine</small>\"]\n";
        $code .= "    end\n\n";
        $code .= "    subgraph DataStorage[\"4. Basis Data & Migrasi Data Warisan\"]\n";
        $code .= "        PgSql[(\"PostgreSQL 16 Engine<br/><small>Strict ULID PK & Keyset Cursor O(1)</small>\")]\n";
        $code .= "        Redis[(\"Redis In-Memory Cache<br/><small>Queue Jobs, Rate Limit & Session</small>\")]\n";
        $code .= "        DataScope[\"Strategi Data: {$migration}\"]\n";
        $code .= "    end\n\n";
        $code .= "    subgraph Gateways[\"5. Integrasi Pembayaran & Transaksi\"]\n";
        $code .= "        Midtrans[\"Midtrans Snap Gateway<br/><small>{$payment}</small>\"]\n";
        $code .= "        Notifications[\"WhatsApp & Email Alert Queue<br/><small>Idempotency Webhooks</small>\"]\n";
        $code .= "    end\n\n";
        $code .= "    subgraph Handover[\"6. Serah Terima Repo & Garansi SLA\"]\n";
        $code .= "        Repo[\"Private GitHub Repository<br/><small>100% Hak Milik Source Code Klien</small>\"]\n";
        $code .= "        SlaNote[\"Garansi Bug: {$warranty}\"]\n";
        $code .= "    end\n\n";
        $code .= "    Clients --> EdgeLayer\n";
        $code .= "    EdgeLayer --> ServerHost\n";
        $code .= "    ServerHost --> DataStorage\n";
        $code .= "    ServerHost --> Gateways\n";
        $code .= "    ServerHost -.-> Handover\n";

        return trim($code);
    }

    /**
     * Generate Sequence Diagram for Mobile App, Local Database (SQLite), & Server Sync Engine.
     */
    public static function generateMobileSyncMermaid(string $businessName): string
    {
        $code = "sequenceDiagram\n";
        $code .= "    autonumber\n";
        $code .= "    actor User as Pengguna / Operator Lapangan\n";
        $code .= "    participant Mobile as Mobile App (Flutter / React Native)\n";
        $code .= "    participant SQLite as Local Database (SQLite Encrypted)\n";
        $code .= "    participant SyncWorker as Background Sync Worker\n";
        $code .= "    participant API as Laravel 13 REST API Gateway\n";
        $code .= "    participant ServerDB as Server Database (PostgreSQL 16+)\n";
        $code .= "    participant Redis as Redis Cache & Idempotency Guard\n\n";
        $code .= "    Note over User,SQLite: FASE OFFLINE (Mutasi Lokal Tanpa Internet)\n";
        $code .= "    User->>Mobile: Input Transaksi / Formulir Baru\n";
        $code .= "    Mobile->>SQLite: INSERT (status='pending_push', mutation_id=ULID)\n";
        $code .= "    SQLite-->>Mobile: Commit Berhasil (O(1) Local Latency)\n";
        $code .= "    Mobile-->>User: Tampilkan UI Sukses Instan (Optimistic UI)\n\n";
        $code .= "    Note over SyncWorker,ServerDB: FASE SINKRONISASI PUSH (Saat Terhubung Online)\n";
        $code .= "    SyncWorker->>SQLite: Ambil antrean WHERE status='pending_push'\n";
        $code .= "    SQLite-->>SyncWorker: Daftar Batch Mutasi Lokal\n";
        $code .= "    SyncWorker->>API: POST /api/v1/sync/push (Header: X-Idempotency-Key)\n";
        $code .= "    API->>Redis: Cek Kunci Idempotensi (Mencegah Duplikasi Retry)\n";
        $code .= "    alt Mutasi Baru (Valid)\n";
        $code .= "        API->>ServerDB: Transaksi ACID (Insert/Update PostgreSQL)\n";
        $code .= "        ServerDB-->>API: Berhasil Disimpan\n";
        $code .= "        API-->>SyncWorker: 200 OK { status: 'synced', server_synced_at: ISO8601 }\n";
        $code .= "    else Mutasi Duplikat (Network Retry)\n";
        $code .= "        Redis-->>API: Key Sudah Diproses Sebelumnya\n";
        $code .= "        API-->>SyncWorker: 200 OK (Idempotent Cached Result)\n";
        $code .= "    end\n";
        $code .= "    SyncWorker->>SQLite: UPDATE status='synced', server_synced_at=now()\n\n";
        $code .= "    Note over SyncWorker,ServerDB: FASE SINKRONISASI PULL (Delta Update dari Server)\n";
        $code .= "    SyncWorker->>API: GET /api/v1/sync/pull?since={last_sync}&cursor={cursor}\n";
        $code .= "    API->>ServerDB: Query Delta (updated_at > last_sync & soft deletes)\n";
        $code .= "    ServerDB-->>API: Daftar Record Baru / Diperbarui\n";
        $code .= "    API-->>SyncWorker: 200 OK { delta_records: [...], has_more: false }\n";
        $code .= "    SyncWorker->>SQLite: UPSERT Delta ke SQLite (LWW Conflict Resolution)\n";
        $code .= "    SQLite-->>Mobile: Stream Reaktif Memicu Update Tampilan UI\n";

        return trim($code);
    }

    /**
     * Generate Comprehensive Mobile, Local Database, and Server Sync Architecture Specifications.
     */
    public static function generateMobileAndSyncArchitecture(string $businessName, string $targetPlatform, array $erdTables, array $mvpItems): array
    {
        $primaryTable = !empty($erdTables[0]['name']) ? $erdTables[0]['name'] : 'business_records';

        return [
            'mobile_platform' => [
                'framework' => 'Flutter 3.x (Dart) / React Native with TypeScript',
                'architecture_pattern' => 'Clean Architecture (Domain, Data, Presentation) with Feature-First Modularization',
                'state_management' => 'Riverpod 2.x / Bloc (Stream-Based Local DB Observers)',
                'offline_resilience' => '100% Offline-First (Operasi CRUD lokal tanpa dependensi koneksi internet seketika)',
                'network_interceptor' => 'Dio / Axios dengan Exponential Backoff Retry (1s, 2s, 4s, 8s, max 30s) & Idempotency Header',
            ],
            'local_database' => [
                'engine' => 'SQLite 3 with WAL Mode & SQLCipher AES-256 Encryption',
                'client_orm' => 'Drift (Flutter) / Room (Android Native) / WatermelonDB (React Native)',
                'storage_strategy' => 'In-Memory Query Cache + Encrypted File Persistence on App Sandboxed Directory',
                'sync_fields' => [
                    'sync_status' => "TEXT CHECK(sync_status IN ('synced', 'pending_push', 'conflict', 'failed')) DEFAULT 'synced'",
                    'client_mutation_id' => "TEXT UNIQUE NOT NULL (26-character ULID)",
                    'local_updated_at' => "INTEGER NOT NULL (Unix timestamp in milliseconds)",
                    'server_synced_at' => "TEXT NULL (ISO-8601 UTC string)",
                    'is_deleted' => "INTEGER DEFAULT 0 (Tombstone soft-delete flag)",
                ],
            ],
            'server_database' => [
                'engine' => 'PostgreSQL 16+ (Strict ULID Architecture)',
                'primary_key_standard' => 'VARCHAR(26) ULID (Time-Ordered Distributed Keys, Zero Collision)',
                'soft_delete_strategy' => 'deleted_at TIMESTAMP NULL (Wajib pada seluruh entitas yang direplikasi ke mobile)',
                'caching_layer' => 'Redis 7+ untuk Idempotency Key Lock (24h TTL) & Active Session Store',
                'pagination_standard' => 'Keyset Cursor Pagination O(1) via ->cursorPaginate()',
            ],
            'sync_protocol' => [
                'strategy' => 'Bi-Directional Delta Sync with Microsecond ULID Timestamp Conflict Resolution',
                'push_endpoint' => '/api/v1/sync/push',
                'pull_endpoint' => '/api/v1/sync/pull',
                'push_request_schema' => [
                    'client_id' => '01J8H8Y4QW0123456789ABCDEF',
                    'device_info' => 'Android 14 / SM-S918B',
                    'batch_count' => 1,
                    'mutations' => [
                        [
                            'mutation_id' => '01J8H8Y4QW0123456789ABCDEF',
                            'table' => $primaryTable,
                            'operation' => 'INSERT',
                            'record_id' => '01J8H8Y4QW0123456789ABCDEF',
                            'client_timestamp' => 1727930000000,
                            'payload' => [
                                'title' => 'Sample Record',
                                'status' => 'active',
                                'notes' => 'Created in offline mode',
                            ],
                        ],
                    ],
                ],
                'push_response_schema' => [
                    'success' => true,
                    'processed_count' => 1,
                    'results' => [
                        [
                            'mutation_id' => '01J8H8Y4QW0123456789ABCDEF',
                            'status' => 'applied',
                            'server_id' => '01J8H8Y4QW0123456789ABCDEF',
                            'server_synced_at' => '2026-10-03T13:00:00.000000Z',
                        ],
                    ],
                ],
                'pull_response_schema' => [
                    'success' => true,
                    'has_more' => false,
                    'next_cursor' => null,
                    'server_time' => '2026-10-03T13:00:00.000000Z',
                    'changes' => [
                        $primaryTable => [
                            [
                                'id' => '01J8H8Y4QW0123456789ABCDEF',
                                'status' => 'verified',
                                'updated_at' => '2026-10-03T12:59:00.000000Z',
                                'deleted_at' => null,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Comprehensive Developer Education Deck & AI Agent Orchestration Masterclass.
     */
    public static function getDeveloperEducationDeck(string $businessName): array
    {
        return [
            'title' => 'Panduan Edukasi Developer: Cara Mengoperasikan PRD ke AI Coding Agent Tanpa Context Rot',
            'orchestration_strategy' => [
                'question' => 'Strategi Orkestrasi AI: Apakah PRD Diberikan Sekaligus atau Sedikit demi Sedikit?',
                'definitive_verdict' => 'JAWABAN TEGAS: JANGAN PERNAH MEMBERIKAN SELURUH DOKUMEN PRD SEKALIGUS DALAM SATU PROMPT KODING!',
                'fatal_flaw_summary' => 'Memberikan seluruh dokumen PRD (ribuan baris) ke dalam jendela obrolan AI yang sedang mengedit kode aktif adalah kesalahan paling fatal yang sering dilakukan developer. Ini adalah penyebab nomor satu mengapa kode menjadi berantakan.',
                'technical_reasons' => [
                    [
                        'title' => 'Attention Drift & Context Rot',
                        'desc' => 'Walaupun model AI modern memiliki context window besar (200K hingga 2M token), kemampuan penalaran logika menurun seiring bertambahnya token. AI akan mengalami instruction dilution (mengabaikan aturan-aturan kecil di tengah dokumen).',
                    ],
                    [
                        'title' => 'Shallow Code & Mock Implementation',
                        'desc' => 'Jika AI diminta mengimplementasikan 10 fitur sekaligus, AI akan kehabisan token output. Akibatnya, AI mulai memotong kode, meninggalkan komentar berbahaya seperti // TODO: implement logic here, atau membuat fungsi dummy/mock yang tidak bekerja.',
                    ],
                    [
                        'title' => 'Amnesia Migrasi & Regresi',
                        'desc' => 'AI akan lupa relasi foreign key dari modul yang dibuat 5 menit lalu dan membuat duplikasi fungsi yang memecah kode sebelumnya.',
                    ],
                    [
                        'title' => 'Audit Diff yang Mustahil',
                        'desc' => 'Jika 1 prompt menghasilkan perubahan pada 40 file sekaligus, Anda sebagai manusia tidak akan bisa mereview bug secara teliti.',
                    ],
                ],
                'best_methodology' => 'Vertical Slice Prompting (Per Fitur Vertikal)',
                'methodology_description' => 'Dokumen PRD Project OS Neriah Pro telah dirancang secara khusus untuk mendukung alur kerja Vertical Slice:',
                'workflow_ascii_art' => "+---------------------------------------------------------------------------------+\n"
                    . "|                        ALUR KERJA ORKESTRASI AI AGENT                           |\n"
                    . "+---------------------------------------------------------------------------------+\n"
                    . "|                                                                                 |\n"
                    . "|  [ LANGKAH 1: FONDASI GLOBAL (1 Kali di Awal) ]                                 |\n"
                    . "|  - Input ke AI: Bab 5 (ERD Schema), Bab 5.6 (Sync Spec), Bab 6 (Tech Stack)     |\n"
                    . "|  - Instruksi AI: \"Buat migrasi database, model ULID, dan setup base project\"    |\n"
                    . "|  - Verifikasi: Jalankan `php artisan migrate` -> Commit Git                     |\n"
                    . "|                                                                                 |\n"
                    . "|  [ LANGKAH 2: EKSEKUSI PER FITUR (Iterasi Berulang) ]                           |\n"
                    . "|  - Buka kartu fitur PRD (misal: FEAT-MVP-01)                                    |\n"
                    . "|  - Klik tombol \"Salin Prompt Handoff AI Code Agent\" yang sudah tersedia         |\n"
                    . "|  - Paste ke Cursor / Claude Code / Antigravity                                  |\n"
                    . "|  - AI hanya bekerja di 3-4 file yang ditentukan (Model -> Controller -> UI)     |\n"
                    . "|  - Verifikasi: Jalankan `php artisan test` -> Commit Git                        |\n"
                    . "|                                                                                 |\n"
                    . "|  [ LANGKAH 3: FITUR SELANJUTNYA ]                                               |\n"
                    . "|  - Ambil kartu fitur berikutnya (FEAT-MVP-02)                                   |\n"
                    . "|  - Ulangi Langkah 2                                                             |\n"
                    . "|                                                                                 |\n"
                    . "+---------------------------------------------------------------------------------+",
                'benefits' => [
                    [
                        'title' => 'Zero Context-Rot',
                        'desc' => 'AI fokus 100% pada satu masalah spesifik dalam batasan file yang ketat.',
                    ],
                    [
                        'title' => 'Kualitas Kode Penuh',
                        'desc' => 'Tidak ada pemotongan kode atau // TODO. AI menuliskan validasi, sanitasi, dan error handling lengkap.',
                    ],
                    [
                        'title' => 'Kemudahan Troubleshooting',
                        'desc' => 'Jika terjadi error, Anda tahu persis error tersebut terjadi di fitur mana, dan riwayat commit Git Anda tercatat rapi per fitur.',
                    ],
                ],
                'card_handoff_guidance' => 'Di dalam halaman show.blade.php pada setiap kartu fitur, tim kami telah menyediakan tombol "Salin Prompt Handoff AI Code Agent" yang siap Anda gunakan untuk disalin ke AI Agent per fitur secara terpandu.',
            ],
            'philosophy' => [
                'summary' => 'AI Coding Agent (Claude Code, Cursor Composer, Windsurf Cascade, Devin, GitHub Copilot) bekerja dengan model probabilitas token. Semakin besar dokumen yang dimasukkan sekaligus (Prompt Dumping), semakin tinggi resiko Attention Drift, amnesia terhadap migration, dan halusinasi arsitektur.',
                'warning' => 'DILARANG melakukan copy-paste ribuan baris PRD sekaligus ke jendela obrolan AI yang sedang mengedit kode aktif! Gunakan pendekatan terpandu di bawah ini.',
            ],
            'modes' => [
                'single_shot' => [
                    'name' => 'Mode A: Single-Shot Full Ingestion (Scaffolding Greenfield)',
                    'badge' => 'UNTUK PROYEK BARU DARI NOL',
                    'when_to_use' => 'Hanya digunakan saat pertama kali menginisialisasi repository baru (Greenfield) pada model dengan context window raksasa (Claude 3.7 Sonnet 200k, Gemini 2.0 Pro 2M, Devin).',
                    'workflow' => [
                        'Langkah 1: Jalankan setup fresh Laravel 13 & Filament v5.',
                        'Langkah 2: Salin Master System Prompt yang mengunci aturan .agents/AGENTS.md.',
                        'Langkah 3: Lampirkan file PRD lengkap (format Markdown) sebagai referensi knowledge base.',
                        'Langkah 4: Perintahkan AI membuat migration awal & registrasi Filament Resource.',
                    ],
                    'risk' => 'TIDAK COCOK untuk proyek yang sudah memiliki kode bisnis berjalan, karena AI berisiko menimpa konfigurasi eksisting.',
                ],
                'step_by_step' => [
                    'name' => 'Mode B: Step-by-Step Vertical Slice (RECOMMENDED - Zero Context-Rot)',
                    'badge' => 'STANDAR EMAS REKAYASA ENTERPRISE',
                    'when_to_use' => 'Wajib digunakan di Cursor Composer, Windsurf Cascade, Claude Code CLI, Aider, dan Copilot Chat untuk pengerjaan fitur per fitur tanpa bug.',
                    'steps' => [
                        [
                            'step' => 1,
                            'title' => 'Inisialisasi Guardrails & Arsitektur',
                            'instruction' => 'Kunci aturan main (.agents/AGENTS.md atau .cursorrules): Standar Primary Key ULID PostgreSQL, Keyset Cursor Pagination O(1), Anti-AI-Slop UI, Zero Native Dialogs (wajib toast/modal), dan signature Schema Filament v5.',
                            'target_tool' => 'Cursor (@Rules) / Claude Code CLI (/init) / Windsurf (.windsurfrules)',
                        ],
                        [
                            'step' => 2,
                            'title' => 'Skema Basis Data & Migration (ERD)',
                            'instruction' => 'Berikan diagram Mermaid ERD dan tabel spesifik. Minta AI membuat migration Laravel 13 dengan ->ulid(\'id\')->primary() dan indeks foreign key yang terisolasi.',
                            'target_tool' => 'Prompting ERD Mermaid spesifik',
                        ],
                        [
                            'step' => 3,
                            'title' => 'Model Eloquent, Casts & Action Handlers',
                            'instruction' => 'Minta AI membuat Model Eloquent dengan HasUlids, casts array JSONB, serta Single-Responsibility Action Class yang dibungkus DB::transaction.',
                            'target_tool' => 'Prompting Modul Backend',
                        ],
                        [
                            'step' => 4,
                            'title' => 'Panel Admin Filament v5 CRUD',
                            'instruction' => 'Generate Filament v5 Resource dengan method signature Schema, form field shallow directory Curator Picker, dan tabel filter instan.',
                            'target_tool' => 'Prompting Filament Resource',
                        ],
                        [
                            'step' => 5,
                            'title' => 'Frontend UI & Formulir Interaktif',
                            'instruction' => 'Berikan Gherkin acceptance criteria dan design tokens Anti-AI-Slop. Minta AI membangun Blade/React Island dengan skeleton loaders, empty state, dan subtle corners (dilarang rounded-full).',
                            'target_tool' => 'Prompting Directive Fitur',
                        ],
                        [
                            'step' => 6,
                            'title' => 'Automated Quality Gate & Testing',
                            'instruction' => 'Wajibkan AI menjalankan verifikasi terminal otomatis (php artisan test --filter=... dan npm run build) dan memastikan exit code 0 sebelum menandai task selesai.',
                            'target_tool' => 'Terminal Execution Loop',
                        ],
                    ],
                ],
            ],
            'tool_guides' => [
                'antigravity_ide' => [
                    'name' => 'Google DeepMind Antigravity IDE',
                    'icon' => 'sparkles',
                    'command' => 'Antigravity IDE Agent / agy CLI',
                    'usage' => 'Buka workspace di Antigravity IDE. Pastikan file .agents/AGENTS.md dan Ponytail Decision Ladder aktif. Berikan task terisolasi dengan bounded files. Wajibkan verifikasi terminal exit code 0 sebelum commit.',
                    'system_prompt_template' => "Kamu bertindak sebagai Principal Software Architect di Google DeepMind Antigravity IDE. Tugas: Implementasikan {FEATURE_NAME} sesuai spesifikasi PRD. Kepatuhan Wajib: Gunakan ULID (HasUlids) untuk primary key PostgreSQL, Keyset cursor pagination O(1), anti-AI-slop UI (subtle corners rounded-none/rounded-sm), zero native dialogs (wajib toast system), dan jalankan automated quality test.",
                ],
                'claude_code' => [
                    'name' => 'Claude Code CLI',
                    'icon' => 'terminal',
                    'command' => 'claude',
                    'usage' => 'Jalankan claude di terminal root proyek. Berikan directive per fitur menggunakan sintaks: claude "Baca kartu FEAT-MVP-01 di PRD. Implementasikan migration dan model User ULID sesuai acceptance criteria. Jalankan php artisan test."',
                ],
                'cursor_composer' => [
                    'name' => 'Cursor Composer',
                    'icon' => 'code',
                    'command' => 'Cmd+I / Ctrl+I',
                    'usage' => 'Buka Composer (Cmd+I). Lampirkan file bounded (@User.php @create_users_table.php). Tempelkan Prompt Directive Fitur dari PRD. Tekan Enter dan tinjau diff perubahan baris demi baris.',
                ],
                'windsurf' => [
                    'name' => 'Windsurf Cascade',
                    'icon' => 'wind',
                    'command' => 'Cascade Flow',
                    'usage' => 'Pilih Cascade agentic mode. Masukkan instruction: "Ikuti spesifikasi FEAT-MVP-01. Buat controller dan form request sesuai validasi Gherkin. Pastikan tidak ada alert() native."',
                ],
                'devin_copilot' => [
                    'name' => 'Devin & GitHub Copilot',
                    'icon' => 'bot',
                    'command' => 'Copilot Workspace',
                    'usage' => 'Buat task sprint berbasis Feature ID. Tempelkan Gherkin scenario sebagai checklist acceptance criteria. Jalankan automated test loop.',
                ],
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
                [
                    'rule' => '5. Strict Mobile & Offline-First Local DB Guardrail',
                    'desc' => 'Ketika mengimplementasikan aplikasi mobile (Flutter/React Native), agen DILARANG mengasumsikan koneksi internet selalu aktif. Seluruh mutasi data harus ditulis ke database lokal (SQLite/Drift/Room) terlebih dahulu dengan sync_status=pending_push, kemudian disinkronkan ke server secara asinkron via endpoint /api/v1/sync/push menggunakan header X-Idempotency-Key. Agen dilarang menebak skema lokal; gunakan skema DDL SQLite dan kontrak API yang didefinisikan pada Bab 5.6.',
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
        $businessRoi = $prd['business_roi_analysis'] ?? self::generateBusinessRoiAnalysis($blueprint, $prd['itemized_cost_breakdown'] ?? self::calculateItemizedEstimation($blueprint));
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
        $md .= "> **Architecture Standard**: Modern Monolith (Laravel 13 + Filament v5 + PostgreSQL Strict ULID)  \n";

        if (!empty($meta['ai_telemetry'])) {
            $aiTel = $meta['ai_telemetry'];
            $md .= "> **Multi-AI Engine**: `{$aiTel['provider_name']}` ({$aiTel['model']})  \n";
            if (!empty($aiTel['fallback_occurred'])) {
                $md .= "> **Failover Event**: `{$aiTel['notification']}`  \n";
            }
            if (!empty($aiTel['strategic_guidance'])) {
                $md .= "> **AI Strategic Insights**: {$aiTel['strategic_guidance']}  \n";
            }
        }

        $md .= "\n---\n\n";

        // 1. Executive Discovery
        $md .= "## 1. Executive Technical Discovery & Problem Statement\n\n";
        $md .= "- **Masalah Utama**: " . ($exec['problem_statement'] ?? $blueprint->masalah_utama) . "\n";
        $md .= "- **Tujuan / Success Metrics**: " . ($exec['success_metrics'] ?? $blueprint->tujuan_utama) . "\n";
        $md .= "- **Target Audiens**: " . ($exec['target_audience'] ?? $blueprint->target_audiens) . "\n";
        $md .= "- **Target Platform & Aksesibilitas**: " . ($exec['target_platform'] ?? 'Modern Web Application Responsive & PWA') . "\n";
        $md .= "- **Status Migrasi Data Warisan**: " . ($exec['legacy_data_migration'] ?? 'Database Baru Bersih') . "\n";
        $md .= "- **Infrastruktur Hosting & Server**: " . ($exec['hosting_infrastructure'] ?? 'Managed Dedicated Cloud VPS Neriah Pro') . "\n";
        $md .= "- **Skema Garansi, SLA & Serah Terima Git**: " . ($exec['warranty_sla'] ?? '30 Hari Garansi Bug Pascameluncur') . "\n";
        $md .= "- **Skema Termin Pembayaran**: " . ($exec['payment_milestones'] ?? 'Termin Standar 50/50') . "\n";
        $md .= "- **Target Skala**: " . ($exec['target_scale'] ?? '0 - 100.000 Pengguna / Bulan') . "\n";
        $md .= "- **Jangkauan Pasar**: " . ($exec['market_reach'] ?? 'Domestik Indonesia') . "\n";
        $md .= "- **Filosofi Arsitektur**: " . ($exec['architecture_philosophy'] ?? '') . "\n\n";

        // 1.5 Business Feasibility, ROI & No-Regret Guarantees
        if (!empty($businessRoi)) {
            $roiEq = $businessRoi['deadly_roi_equation'] ?? [];
            $inaction = $businessRoi['cost_of_inaction'] ?? [];
            $winWin = $businessRoi['win_win_strategy'] ?? [];
            $guarantees = $businessRoi['why_neriah_pro_guarantees'] ?? [];

            $md .= "## 1.5 Analisis Kelayakan Bisnis, Proyeksi ROI & 5 Garansi Bebas Penyesalan Neriah Pro\n\n";
            $md .= "> **Domain Sektor Bisnis**: `{$businessRoi['domain_category']}` (`{$businessRoi['domain_badge']}`)  \n";
            $md .= "> **Benchmark Transaksi Pasar**: `{$businessRoi['ticket_benchmark']}`\n\n";

            $md .= "### A. Formula Balik Modal Cepat (Killer ROI Equation)\n";
            $md .= "- **Target BEP**: **" . ($roiEq['headline'] ?? '-') . "**\n";
            $md .= "- **Formula Hitungan Realistis**: `" . ($roiEq['formula'] ?? '-') . "`\n";
            $md .= "- **Proyeksi ROI Tahun ke-1**: **" . ($roiEq['projected_annual_roi'] ?? '-') . "**\n";
            $md .= "- **Rasional Analisis**: " . ($roiEq['narrative'] ?? '-') . "\n\n";

            if (!empty($inaction['risks'])) {
                $md .= "### B. " . ($inaction['title'] ?? 'Biaya Fatal Menunda (Cost of Inaction)') . "\n";
                foreach ($inaction['risks'] as $risk) {
                    $md .= "- ⚠️ {$risk}\n";
                }
                $md .= "\n";
            }

            if (!empty($winWin['recommendation'])) {
                $md .= "### C. " . ($winWin['title'] ?? 'Strategi Investasi Menang-Menang (Win-Win Solution)') . "\n";
                $md .= "{$winWin['recommendation']}\n\n";
            }

            if (!empty($guarantees)) {
                $md .= "### D. 5 Garansi Bebas Penyesalan Neriah Pro (No-Regret Investment Guarantees)\n\n";
                $md .= "| No | Garansi Perlindungan Klien | Deskripsi & Nilai Perlindungan | Badge Standar |\n";
                $md .= "|---|---|---|---|\n";
                $gIdx = 1;
                foreach ($guarantees as $g) {
                    $gTitle = $g['title'] ?? '-';
                    $gDesc = $g['desc'] ?? '-';
                    $gBadge = $g['badge'] ?? 'GUARANTEED';
                    $md .= "| `0{$gIdx}` | **{$gTitle}** | {$gDesc} | `{$gBadge}` |\n";
                    $gIdx++;
                }
                $md .= "\n";
            }
        }

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
        $md .= "```mermaid\n" . self::generateErdMermaid($erd) . "\n```\n\n";

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

        // 5.5 Virtual Architecture Studio: Virtual Charts Suite
        $featureDepMermaid = self::generateFeatureDependencyMermaid($actors, $mvpFeatures, $erd);
        $sprintGanttMermaid = self::generateSprintGanttMermaid($blueprint, $blueprint->target_waktu ?: '30 Hari Kerja');
        $extraContext = [
            'target_platform' => $exec['target_platform'] ?? 'Modern Web & PWA',
            'preferensi_hosting' => $exec['hosting_infrastructure'] ?? 'Managed Dedicated Cloud VPS',
            'migrasi_data' => $exec['legacy_data_migration'] ?? 'Database Baru Bersih',
            'garansi_sla' => $exec['warranty_sla'] ?? '30 Hari Garansi Bug',
            'termin_pembayaran' => $exec['payment_milestones'] ?? 'Termin Standar 50/50',
        ];
        $infraMermaid = self::generateInfrastructureMermaid($blueprint, $extraContext);

        $md .= "## 5.5 Visual Architecture Studio: Virtual Charts Suite\n\n";
        $md .= "### A. Feature & Entity Dependency Graph (Flowchart LR)\n\n";
        $md .= "```mermaid\n" . $featureDepMermaid . "\n```\n\n";
        $md .= "### B. Roadmap Eksekusi & Timeline Sprint (Gantt Chart)\n\n";
        $md .= "```mermaid\n" . $sprintGanttMermaid . "\n```\n\n";
        $md .= "### C. Hosting, Keamanan & Topologi Infrastruktur (Flowchart TB)\n\n";
        $md .= "```mermaid\n" . $infraMermaid . "\n```\n\n";

        // 5.6 Multi-Platform Mobile & Offline-First Sync Architecture
        $mobileSync = $prd['mobile_and_sync_architecture'] ?? self::generateMobileAndSyncArchitecture($projectName, $exec['target_platform'] ?? '', $erd, $mvpFeatures);
        $mobileSyncMermaid = $prd['virtual_charts']['mobile_sync_mermaid'] ?? self::generateMobileSyncMermaid($projectName);

        $md .= "## 5.6 Arsitektur Multi-Platform: Aplikasi Mobile, Database Lokal (Offline-First) & Protokol Sinkronisasi Server\n\n";
        $md .= "### A. Alur Kerja Sinkronisasi Data Offline-to-Online (Sequence Pipeline)\n\n";
        $md .= "```mermaid\n" . $mobileSyncMermaid . "\n```\n\n";

        $md .= "### B. Spesifikasi Platform Mobile & Database Lokal (Offline-First)\n\n";
        $md .= "- **Mobile Framework**: " . ($mobileSync['mobile_platform']['framework'] ?? 'Flutter 3.x / React Native') . "\n";
        $md .= "- **Pola Arsitektur Mobile**: " . ($mobileSync['mobile_platform']['architecture_pattern'] ?? 'Clean Architecture Feature-First') . "\n";
        $md .= "- **State Management & Reaktivitas**: " . ($mobileSync['mobile_platform']['state_management'] ?? 'Riverpod / Bloc') . "\n";
        $md .= "- **Ketahanan Jaringan (Offline-Resilience)**: " . ($mobileSync['mobile_platform']['offline_resilience'] ?? '100% Offline-First') . "\n";
        $md .= "- **Network Interceptor**: " . ($mobileSync['mobile_platform']['network_interceptor'] ?? 'Dio / Axios dengan Exponential Backoff') . "\n";
        $md .= "- **Engine Database Lokal (Client/Device)**: " . ($mobileSync['local_database']['engine'] ?? 'SQLite 3 Encrypted (Drift / Room)') . "\n";
        $md .= "- **Engine Database Server (Central Hub)**: " . ($mobileSync['server_database']['engine'] ?? 'PostgreSQL 16+ (Strict ULID Schema)') . "\n";
        $md .= "- **Protokol Sinkronisasi**: " . ($mobileSync['sync_protocol']['strategy'] ?? 'Bi-Directional Delta Sync with Idempotency Key') . "\n\n";

        $md .= "### C. Skema Standar Metadata Sinkronisasi Database Lokal (SQLite DDL)\n\n";
        $md .= "Setiap tabel lokal pada perangkat mobile wajib menyertakan kolom kontrol sinkronisasi berikut untuk mencegah tabrakan data dan amnesia status:\n\n";
        $md .= "```sql\n";
        $md .= "-- Kolom Standar Sync pada SQLite Lokal (Drift / Room / WatermelonDB)\n";
        $md .= "ALTER TABLE local_records ADD COLUMN sync_status TEXT CHECK(sync_status IN ('synced', 'pending_push', 'conflict', 'failed')) DEFAULT 'synced';\n";
        $md .= "ALTER TABLE local_records ADD COLUMN client_mutation_id TEXT UNIQUE; -- 26-char ULID per mutasi lokal\n";
        $md .= "ALTER TABLE local_records ADD COLUMN local_updated_at INTEGER NOT NULL; -- Unix Timestamp in milliseconds\n";
        $md .= "ALTER TABLE local_records ADD COLUMN server_synced_at TEXT NULL; -- ISO-8601 UTC timestamp saat berhasil tersinkron\n";
        $md .= "ALTER TABLE local_records ADD COLUMN is_deleted INTEGER DEFAULT 0; -- Tombstone flag untuk soft delete offline\n";
        $md .= "CREATE INDEX idx_local_sync ON local_records(sync_status, local_updated_at);\n";
        $md .= "```\n\n";

        $md .= "### D. Kontrak Spesifikasi API Sinkronisasi (Machine-Readable Endpoint Contracts)\n\n";
        $md .= "#### 1. Push Mutasi Offline ke Server: `POST /api/v1/sync/push`\n";
        $md .= "- **Headers Wajib**: `Authorization: Bearer <token>`, `X-Idempotency-Key: <client_mutation_id>`, `Content-Type: application/json`\n";
        $md .= "- **Request Payload Schema**:\n```json\n" . json_encode($mobileSync['sync_protocol']['push_request_schema'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n```\n";
        $md .= "- **Response Payload Schema (200 OK)**:\n```json\n" . json_encode($mobileSync['sync_protocol']['push_response_schema'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n```\n\n";

        $md .= "#### 2. Pull Delta Pembaruan dari Server: `GET /api/v1/sync/pull`\n";
        $md .= "- **Query Parameters**: `?since=<ISO8601>&cursor=<ULID>&limit=100`\n";
        $md .= "- **Response Payload Schema (200 OK)**:\n```json\n" . json_encode($mobileSync['sync_protocol']['pull_response_schema'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n```\n\n";

        $md .= "### E. Kebijakan Resolusi Konflik (Conflict Resolution Engine)\n\n";
        $md .= "1. **Deterministic Last-Write-Wins (LWW)**: Resolusi konflik otomatis mengacu pada timestamp mikrodetik yang tertanam di dalam 10-byte pertama Primary Key ULID. Mutasi dengan timestamp tertinggi secara deterministik diterima sebagai state final.\n";
        $md .= "2. **Tombstone Soft Deletes**: Penghapusan data offline tidak langsung menghapus baris fisik SQLite melainkan menandai `is_deleted = 1` dengan `deleted_at = now()`. Saat disinkronkan ke server, server mencatat `deleted_at` dan mempropagasi penghapusan ini ke perangkat lain saat `GET /api/v1/sync/pull`.\n";
        $md .= "3. **Idempotency Protection**: Header `X-Idempotency-Key` di-cache di Redis server selama 24 jam. Jika koneksi seluler putus saat pengiriman dan mobile app melakukan retry, server mendeteksi mutasi duplikat dan langsung mengembalikan status sukses tanpa memicu duplikasi data.\n\n";

        // 6. Technology Stack & Architecture Decision
        $isDecoupled = ($prd['architecture_evaluation']['architecture_pattern_evaluation']['is_decoupled'] ?? false);
        $archTitle = $isDecoupled ? 'Enterprise Decoupled & Headless Architecture' : 'Modern Monolith (Decoupled-Ready)';
        $md .= "## 6. Keputusan Arsitektur & Rekomendasi Stack ({$archTitle})\n\n";
        $md .= "| Lapisan | Teknologi | Peran & Justifikasi Arsitektur |\n";
        $md .= "|---|---|---|\n";
        foreach ($tech as $layer => $info) {
            $md .= "| **" . ucfirst(str_replace('_', ' ', $layer)) . "** | " . ($info['name'] ?? '') . " | " . ($info['role'] ?? '') . " |\n";
        }
        $md .= "\n";

        // 6.5 Cetak Biru Arsitektur Decoupled & Ekosistem Multi-Pihak (Modern 2026+ Standards)
        if (!empty($prd['decoupled_tooling_strategy'])) {
            $ds = $prd['decoupled_tooling_strategy'];
            $md .= "## 6.5 Cetak Biru Arsitektur Decoupled & Ekosistem Multi-Pihak (2026+ Modern Tooling)\n\n";
            $md .= "> " . $ds['summary'] . "\n\n";
            $md .= "### A. Matriks Tools Multi-Pihak (Frontend, Mobile, Backend & API)\n\n";
            $md .= "| Kategori Pihak | Rekomendasi Utama (2026+) | Alternatif / DB Lokal | Peran Arsitektur |\n";
            $md .= "|---|---|---|---|\n";
            foreach ($ds['multi_party_tools_matrix'] as $k => $tool) {
                $tCat = $tool['category'] ?? ucfirst($k);
                $tPri = $tool['primary'] ?? '';
                $tAlt = $tool['alternatives'] ?? ($tool['local_database'] ?? ($tool['specification'] ?? ($tool['strategy'] ?? '-')));
                $tRole = $tool['role'] ?? ($tool['description'] ?? '');
                $md .= "| **{$tCat}** | `{$tPri}` | {$tAlt} | {$tRole} |\n";
            }
            $md .= "\n";

            $md .= "### B. Topologi Server, Cloud Edge & Efisiensi Biaya\n\n";
            $md .= "| Tingkatan Server | Provider / Teknologi | Spesifikasi & Cakupan | Estimasi Biaya |\n";
            $md .= "|---|---|---|---|\n";
            foreach ($ds['server_and_cloud_topology'] as $k => $srv) {
                $sTier = $srv['tier'] ?? ucfirst($k);
                $sProv = $srv['provider'] ?? ($srv['tools'] ?? '');
                $sSpec = $srv['specs'] ?? ($srv['coverage'] ?? ($srv['features'] ?? ($srv['killer_feature'] ?? '')));
                $sCost = $srv['cost_range'] ?? ($srv['sla_target'] ?? '-');
                $md .= "| **{$sTier}** | `{$sProv}` | {$sSpec} | **{$sCost}** |\n";
            }
            $md .= "\n";

            $md .= "### C. Protokol Keamanan, Autentikasi & Sinkronisasi\n\n";
            $md .= "- **Autentikasi Web**: " . ($ds['framework_strategy_and_protocols']['authentication']['web'] ?? '') . "\n";
            $md .= "- **Autentikasi Mobile**: " . ($ds['framework_strategy_and_protocols']['authentication']['mobile'] ?? '') . "\n";
            $md .= "- **Proteksi Idempotency**: " . ($ds['framework_strategy_and_protocols']['data_sync_and_idempotency']['idempotency'] ?? '') . "\n";
            $md .= "- **Paginasi Skalabilitas**: " . ($ds['framework_strategy_and_protocols']['data_sync_and_idempotency']['keyset_pagination'] ?? '') . "\n";
            $md .= "- **Zero-Trust CORS**: " . ($ds['framework_strategy_and_protocols']['cors_and_zero_trust']['cors'] ?? '') . "\n";
            $md .= "- **CI/CD Pipeline**: " . ($ds['framework_strategy_and_protocols']['devsecops_cicd']['strategy'] ?? '') . "\n\n";
        }


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

        // 9. AI Agent Handoff Protocol & Developer Education
        $eduDeck = self::getDeveloperEducationDeck($blueprint->nama_bisnis ?: $blueprint->client_name);
        $strat = $eduDeck['orchestration_strategy'] ?? [];
        $md .= "## 9. Panduan Edukasi Developer & Protokol Handoff AI Code Agent (Anti Context-Rot)\n\n";
        
        if (!empty($strat)) {
            $md .= "### " . ($strat['question'] ?? 'Strategi Orkestrasi AI: Apakah PRD Diberikan Sekaligus atau Sedikit demi Sedikit?') . "\n\n";
            $md .= "> 🚨 **" . ($strat['definitive_verdict'] ?? '') . "**\n\n";
            $md .= $strat['fatal_flaw_summary'] . "\n\n";
            $md .= "#### Alasan Teknis Mengapa \"Prompt Dumping\" Merusak Kode:\n";
            foreach ($strat['technical_reasons'] ?? [] as $tr) {
                $md .= "- **" . $tr['title'] . "**: " . $tr['desc'] . "\n";
            }
            $md .= "\n";
            $md .= "#### Metodologi Terbaik: " . ($strat['best_methodology'] ?? 'Vertical Slice Prompting') . "\n\n";
            $md .= ($strat['methodology_description'] ?? '') . "\n\n";
            $md .= "```text\n" . ($strat['workflow_ascii_art'] ?? '') . "\n```\n\n";
            $md .= "#### Keuntungan Pendekatan Ini:\n";
            foreach ($strat['benefits'] ?? [] as $b) {
                $md .= "- **" . $b['title'] . "**: " . $b['desc'] . "\n";
            }
            $md .= "\n";
            $md .= "> " . ($strat['card_handoff_guidance'] ?? '') . "\n\n";
        }

        $md .= "> " . $eduDeck['philosophy']['summary'] . "\n\n";
        $md .= "> ⚠️ **PERINGATAN KRUSIAL**: " . $eduDeck['philosophy']['warning'] . "\n\n";

        $md .= "### A. Pilihan Strategi Ingestion ke AI Agent\n\n";
        $md .= "#### 1. " . $eduDeck['modes']['single_shot']['name'] . " (`" . $eduDeck['modes']['single_shot']['badge'] . "`)\n";
        $md .= "- **Kapan Digunakan**: " . $eduDeck['modes']['single_shot']['when_to_use'] . "\n";
        $md .= "- **Risiko**: " . $eduDeck['modes']['single_shot']['risk'] . "\n";
        $md .= "- **Alur Kerja**:\n";
        foreach ($eduDeck['modes']['single_shot']['workflow'] as $wf) {
            $md .= "  - {$wf}\n";
        }
        $md .= "\n";

        $md .= "#### 2. " . $eduDeck['modes']['step_by_step']['name'] . " (`" . $eduDeck['modes']['step_by_step']['badge'] . "`)\n";
        $md .= "- **Kapan Digunakan**: " . $eduDeck['modes']['step_by_step']['when_to_use'] . "\n";
        $md .= "- **6 Tahap Eksekusi Presisi (Sprint-by-Sprint)**:\n";
        foreach ($eduDeck['modes']['step_by_step']['steps'] as $s) {
            $md .= "  - **Langkah {$s['step']}: {$s['title']}** (`{$s['target_tool']}`)  \n    {$s['instruction']}\n";
        }
        $md .= "\n";

        $md .= "### B. Playbook Per Tool IDE (Cursor, Claude Code, Windsurf, Devin)\n\n";
        foreach ($eduDeck['tool_guides'] as $tool) {
            $md .= "#### 🛠️ {$tool['name']} (`{$tool['command']}`)\n";
            $md .= "{$tool['usage']}\n\n";
        }

        $md .= "### C. Protokol Aturan Mutlak Handoff\n\n";
        foreach ($handoff['rules'] as $r) {
            $md .= "#### " . $r['rule'] . "\n";
            $md .= $r['desc'] . "\n\n";
        }

        // 9.5 AI-Shield & Autonomous Exploit Defense
        $aiShield = $prd['ai_security_blueprint'] ?? self::generateAiSecurityBlueprint($projectName, $extraContext ?? []);
        $md .= "## 9.5 AI-Shield & Secure Ingestion Pipeline (Pertahanan Eksploitasi Otonom AI)\n\n";
        $md .= "> **Latar Belakang & Vektor Serangan**: " . ($aiShield['incident_case_study']['context'] ?? '') . "\n";
        $md .= "> **Analisis Insiden Exploit Gym**: " . ($aiShield['incident_case_study']['attack_vector'] ?? '') . "\n\n";
        $md .= "| Kode Modul | Nama Modul Pertahanan | Peran & Tipe Arsitektur | Mekanisme Proteksi |\n";
        $md .= "|---|---|---|---|\n";
        foreach ($aiShield['defensive_modules'] ?? [] as $m) {
            $mCode = $m['code'] ?? 'SEC';
            $mName = $m['name'] ?? '-';
            $mType = $m['type'] ?? '-';
            $mDesc = $m['description'] ?? '-';
            $md .= "| `{$mCode}` | **{$mName}** | {$mType} | {$mDesc} |\n";
        }
        $md .= "\n";

        // 9.6 20 Agentic AI Concepts Matrix
        $agenticMatrix = $prd['agentic_ai_matrix'] ?? self::generateAgenticAiConceptsMatrix($projectName, $mvpFeatures);
        $md .= "## 9.6 Matriks 20 Konsep AI Agentic & Rekomendasi Terarah untuk Klien\n\n";
        $md .= "> **Efisiensi Investasi**: Mengeliminasi kompleksitas berlebih dan memastikan sistem klien hanya mengadopsi konsep AI yang bernilai nyata bagi bisnis.\n\n";
        $md .= "| No | Konsep Agentic AI | Pilar Arsitektur | Status Rekomendasi | Aplikasi Nyata untuk Sistem Klien |\n";
        $md .= "|---|---|---|---|---|\n";
        foreach ($agenticMatrix['concepts'] ?? [] as $c) {
            $cNum = sprintf('%02d', $c['id']);
            $cName = $c['name'];
            $cPillar = $c['pillar'];
            $cRec = !empty($c['recommended']) ? '✅ **DIREKOMENDASIKAN**' : '⚪ Opsional (Masa Depan)';
            $cApp = $c['client_application'];
            $md .= "| `{$cNum}` | **{$cName}**<br/><small>{$c['badge']}</small> | {$cPillar} | {$cRec} | {$cApp} |\n";
        }
        $md .= "\n";

        // 9.7 Server Hardware Capacity Sizing Engine
        $serverSizing = $prd['server_hardware_sizing'] ?? self::calculateServerHardwareSizing($projectName, $exec['problem_statement'] ?? '', $mvpFeatures, $extraContext ?? []);
        $md .= "## 9.7 Analisis Kapasitas Spesifikasi Server (Hardware Sizing Engine)\n\n";
        $md .= "- **Rekomendasi Paket Server**: **" . ($serverSizing['tier_name'] ?? 'Production VPS') . "**\n";
        $md .= "- **Profil Beban Kerja**: " . ($serverSizing['workload_profile'] ?? '-') . "\n";
        $md .= "- **Estimasi Investasi Server**: **" . ($serverSizing['estimated_monthly_investment']['idr'] ?? '-') . "** (" . ($serverSizing['estimated_monthly_investment']['usd'] ?? '-') . ")\n\n";
        $md .= "### Rincian Alokasi Komputasi & Memori Terukur:\n";
        $md .= "- **vCPU Dedicated**: " . ($serverSizing['specifications']['vcpu']['count'] ?? '-') . " (" . ($serverSizing['specifications']['vcpu']['architecture'] ?? '') . ")\n";
        $md .= "- **RAM Dedicated ECC**: " . ($serverSizing['specifications']['ram']['total'] ?? '-') . " (Postgres Buffer 28%, PHP-FPM 25%, Redis 15%)\n";
        $md .= "- **Penyimpanan NVMe PCIe 4.0**: " . ($serverSizing['specifications']['storage']['capacity'] ?? '-') . " (" . ($serverSizing['specifications']['storage']['speed'] ?? '') . ")\n";
        $md .= "- **Bandwidth Jaringan**: " . ($serverSizing['specifications']['network']['bandwidth'] ?? '-') . " (" . ($serverSizing['specifications']['network']['port_speed'] ?? '') . ")\n\n";

        // 10. Governance & DoD
        $md .= "## 10. Tata Kelola, Kualitas & Definition of Done (DoD)\n\n";
        foreach ($governance['definition_of_done'] ?? [] as $dod) {
            $md .= "- [x] {$dod}\n";
        }
        $md .= "\n";

        // 10.5 Itemized Scope Breakdown
        $itemized = $prd['itemized_cost_breakdown'] ?? self::calculateItemizedEstimation($blueprint);
        if (!empty($itemized['items'])) {
            $md .= "## 10.5 Rincian Biaya Spesifikasi Berdasarkan Poin Input Klien (Itemized Scope Metric)\n\n";
            $md .= "> **Transparansi Investasi**: Setiap komponen biaya diturunkan langsung secara matematis dari formulir spesifikasi yang diajukan oleh pihak klien.\n\n";
            $md .= "| Kategori | Kode | Komponen / Fitur Spesifik | Kompleksitas | Bobot Biaya |\n";
            $md .= "|---|---|---|---|---|\n";
            foreach ($itemized['items'] as $it) {
                $cCat = $it['category'] ?? '-';
                $cCode = $it['code'] ?? '-';
                $cTitle = $it['title'] ?? '-';
                $cComp = $it['complexity'] ?? '-';
                $cAmount = ($it['amount'] < 0 ? '-Rp ' : 'Rp ') . number_format(abs($it['amount']), 0, ',', '.');
                $md .= "| **{$cCat}** | `{$cCode}` | {$cTitle} | {$cComp} | **{$cAmount}** |\n";
            }
            $md .= "| **TOTAL BASE SCOPE (STANDARD VELOCITY)** | | | | **Rp " . number_format($itemized['base_subtotal'] ?? 0, 0, ',', '.') . "** |\n";
            $md .= "| **TERMIN DP 50% (DIBAYARKAN VIA MIDTRANS SNAP)** | | | | **Rp " . number_format($itemized['standard_dp'] ?? 0, 0, ',', '.') . "** |\n";
            $md .= "| **PELUNASAN SETELAH LOLOS UAT & SERAH TERIMA** | | | | **Rp " . number_format($itemized['standard_pelunasan'] ?? 0, 0, ',', '.') . "** |\n\n";
        }

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

    /**
     * Synthesize complete AI Coding Rules (.cursorrules / CLAUDE.md / AGENTS.md)
     * Tailored for Cursor, Claude Code, Antigravity, and Windsurf AI agents.
     */
    public static function toCursorrules(VisionBlueprint $blueprint, array $prd = []): string
    {
        if (empty($prd)) {
            $prd = $blueprint->prd_content ?? self::generate($blueprint);
        }

        $projectName = $blueprint->nama_bisnis ?: ($blueprint->client_name . "'s Project");
        $specId = strtoupper(substr((string)$blueprint->id, 0, 10));
        $exec = $prd['executive_summary'] ?? [];
        $actors = $prd['system_actors'] ?? [];
        $erd = $prd['erd_schema']['tables'] ?? [];
        $mvpFeatures = $prd['features']['mvp_phase1'] ?? [];

        $rules = "# .cursorrules / CLAUDE.md — AI AGENT SYSTEM INSTRUCTIONS\n";
        $rules .= "# Project: {$projectName}\n";
        $rules .= "# Blueprint Spec ID: {$specId}\n";
        $rules .= "# Engine: Neriah Pro AI Project OS (Generated: " . ($blueprint->created_at?->format('Y-m-d') ?: date('Y-m-d')) . ")\n\n";
        $rules .= "You are pair programming on \"{$projectName}\", a mission-critical enterprise digital system.\n";
        $rules .= "Always adhere strictly to the architectural constraints, database schemas, and engineering directives defined below.\n\n";
        $rules .= "---\n\n";

        // 1. Context
        $rules .= "## 1. PROJECT SPECIFICATION & CONTEXT\n";
        $rules .= "- **Project Name**: {$projectName}\n";
        $rules .= "- **Primary Problem**: " . ($blueprint->masalah_utama ?: 'Enterprise operational digitization and centralized workflows.') . "\n";
        $rules .= "- **Core Objective / Success Metrics**: " . ($blueprint->tujuan_utama ?: 'High efficiency, automated audit trails, and zero downtime.') . "\n";
        $rules .= "- **Target Audience**: " . ($blueprint->target_audiens ?: 'Internal operators and external customers.') . "\n";
        $rules .= "- **Target Platform**: " . ($blueprint->user_metadata['target_platform'] ?? 'Modern Web Application Responsive & PWA') . "\n";
        $rules .= "- **Tech Stack**: Laravel 13, Filament v5, Livewire 4, Alpine.js, Tailwind CSS, PostgreSQL 16, Redis 7.\n\n";
        $rules .= "---\n\n";

        // 2. Protocols
        $rules .= "## 2. STRICT ARCHITECTURAL PROTOCOLS (ZERO COMPROMISE)\n\n";
        $rules .= "### A. Database Primary Keys & PostgreSQL ULID Standard\n";
        $rules .= "- **Primary Keys**: ALWAYS use ULID (`->ulid('id')->primary()` and `HasUlids` trait) for all business domain tables.\n";
        $rules .= "- **Strict PostgreSQL Compatibility**: NEVER use `\$table->uuid('id')` for models using ULID (`HasUlids`), as PostgreSQL strictly rejects 26-char ULID strings with `SQLSTATE[22P02]`. ALWAYS use `->ulid('id')`.\n";
        $rules .= "- **Foreign Keys**: ALWAYS use `->foreignUlid('parent_id')` to match ULID primary keys.\n";
        $rules .= "- **Zero Auto-Increment**: NEVER use `\$table->id()` or auto-incrementing integers for business domain entities.\n\n";

        $rules .= "### B. Keyset Cursor Pagination O(1) & Table Scalability\n";
        $rules .= "- **Tabel Wajib Search & Pagination O(1)**: Setiap tabel wajib dilengkapi fitur search berindeks dan pagination bulletproof untuk jutaan data dan viewer: Keyset Cursor Pagination O(1) (`cursorPaginate()`). Dilarang keras menggunakan offset pagination (`paginate()`).\n";
        $rules .= "- **Mandatory Keyset**: ALWAYS use `cursorPaginate()` with stable keyset fallbacks (e.g. `->orderBy('id', 'asc')`).\n";
        $rules .= "- **Dual View (Grid & List View)**: Setiap membuat komponen list data, selalu sediakan search, pagination O(1), dan tombol toggle tampilan Grid View dan List View switcher untuk menjamin skalabilitas jutaan data dan kenyamanan visual.\n";
        $rules .= "- **Instant Clear Button (\"X\") pada Search Field**: Field search selalu wajib ada icon \"X\" (tombol hapus instan) untuk menghapus teks yang sudah ada di dalam textfield search secara instan dengan satu klik.\n\n";

        $rules .= "### C. Zero Tolerance for Magic Strings & Fuzzy Searches (\"Anti-Dosa Hardcode / Prefix WHERE LIKE\")\n";
        $rules .= "- NEVER identify entities, transactions, or state via fuzzy string matching (`WHERE LIKE 'retail_%'`, `LIKE '%APEX%'`, demo aliases, or slugs). Fuzzy prefix searching is fundamentally broken for long-term scalability.\n";
        $rules .= "- ALWAYS identify models, orders, and documents strictly via **Concrete Relational Foreign Keys** or **Exact Primary Key Lookups** (`find(\$id)`).\n";
        $rules .= "- Business states (DP settlement, contract signing, scope freeze) must derive strictly from concrete status columns/enums (`status === 'settlement'`), never string sniffing.\n\n";

        $rules .= "### D. Multi-Language 2-Tier Architecture (Database Native JSON & Whitelisted Global Plugin)\n";
        $rules .= "- **Tier 1 (Database Native JSON)**: Selalu sediakan multi-bahasa Tier 1 dari database di mana kolomnya adalah JSON (bukan string tunggal biasa) dan di-cast sebagai `'array'` (`{\"id\": \"...\", \"en\": \"...\"}`). Admin backend Filament bisa mengelola dua versi bahasa (ID & EN) secara resmi.\n";
        $rules .= "- **Tier 2 (Global Plugin Google Translate)**: Ditenagai oleh plugin Google Translate di frontend, di mana daftar opsi bahasa disimpan di database backend admin (`CmsGlobalSetting`) dan dapat ditentukan whitelist bahasa mana saja yang dimunculkan ke publik.\n";
        $rules .= "- **Single Unified Navbar Dropdown**: Di navigasi, dilarang membuat switch bahasa redundant (misal dobel tombol ID/EN dan tombol Global berdampingan). Cukup gunakan SATU dropdown terpadu yang memuat dua list group: Tier 1 (Native Precise) dan Tier 2 (Global Translate).\n\n";

        $rules .= "### E. Cache Serialization Safety\n";
        $rules .= "- NEVER cache raw Eloquent model instances in `Cache::rememberForever()`. Serializing Eloquent models across lifecycles causes `__PHP_Incomplete_Class` errors.\n";
        $rules .= "- ALWAYS cache primitive attribute arrays (`\$record->getAttributes()`) or JSON strings, and reconstitute models via `(new Model)->newFromBuilder(\$cachedAttributes)`.\n\n";

        $rules .= "### F. Managed Sprint Capacity, Anti-Collision Batch Engineering & Local Gantt Dashboard\n";
        $rules .= "- **Managed Capacity Discipline**: Tidak menerima proyek paralel tak terbatas demi menjaga kualitas rekayasa enterprise, nol bug, dan anti-AI-slop. Setiap proyek dikunci ke dalam batch waktu sprint terkelola (e.g. Batch 1, Batch 2, Batch Q1).\n";
        $rules .= "- **Anti-Collision Architecture**: Mencegah tabrakan jadwal kickoff antar klien (schedule collision) dan mencegah transaksi ganda (payment collision) menggunakan generator ULID unik dan webhook idempotency.\n";
        $rules .= "- **Zero CDN Latency Requirement**: Seluruh library JavaScript yang digunakan untuk merender visualisasi (seperti Mermaid.js, Alpine.js, Frappe Gantt) WAJIB diunduh dan disimpan secara lokal di dalam repository (`public/js/vendor/`). DILARANG KERAS memuat script via CDN eksternal.\n";
        $rules .= "- **Interactive Gantt & Timeline Monitoring**: Setiap blueprint wajib men-generate diagram Mermaid Gantt (`sprint_gantt_mermaid`) yang dapat dimonitor melalui backend admin dashboard untuk melacak ketercapaian milestone DoD (Definition of Done) per sprint.\n\n";
        $rules .= "---\n\n";

        // 3. UI/UX Protocols
        $rules .= "## 3. UI/UX & FRONTEND DIRECTIVES\n\n";
        $rules .= "### A. Subtle Round Corners (Strict Ban on Capsule/Pill Shapes)\n";
        $rules .= "- UI/UX border radius MUST be subtle (`rounded-none`, `rounded-xs`, `rounded-sm`, max `rounded-md`).\n";
        $rules .= "- STRICT BAN on `rounded-full` capsule buttons or pills. They look generic, reduce clickable area, and degrade enterprise aesthetics.\n\n";

        $rules .= "### B. Clean Solid Brutalist Theme (Strict Ban on Gaudy Multi-Color Gradients)\n";
        $rules .= "- **Hindari Gradient Style**: Hindari gradient style dalam membuat theme UI.\n";
        $rules .= "- **Solid & Sharp Brutalism**: Gunakan warna solid, sleek monochrome, border presisi tajam (`border-zinc-200 dark:border-zinc-800`), dan background solid (`bg-white dark:bg-zinc-950`). Jangan gunakan background gradien warna-warni yang mencolok (\"AI slop / template murahan\").\n\n";

        $rules .= "### C. Comprehensive Dark & Light Mode Fidelity\n";
        $rules .= "- **100% Dark & Light Mode Compliance**: Selalu buat theme dark / light dan pastikan setiap komponen yang dibuat comply dengan theme ini.\n";
        $rules .= "- **Consistent Contrast**: Gunakan pasangan class Tailwind secara disiplin (`bg-white dark:bg-zinc-950`, `text-zinc-900 dark:text-zinc-100`, `border-zinc-200 dark:border-zinc-800`, `hover:bg-zinc-100 dark:hover:bg-zinc-900`). Dilarang keras membuat komponen yang hanya terlihat bagus di salah satu mode.\n\n";

        $rules .= "### D. Zero Native Browser Dialogs (No \"Modal Kampungan\")\n";
        $rules .= "- NEVER use native browser dialogs (`window.alert()`, `confirm()`, `prompt()`). They look cheap/unprofessional (\"modal kampungan\") and freeze the UI thread.\n";
        $rules .= "- ALWAYS use modern floating toast notifications: `window.showToast({ type: 'success'|'error'|'warning'|'info', title: '...', message: '...' })` or curated Tailwind + Alpine dialogs with backdrop-blur.\n\n";

        $rules .= "### E. Thousand Separators on Numbers & Currencies\n";
        $rules .= "- Setiap kali membuat input atau tampilan nominal angka atau currency yang mencapai ribuan (>= 1.000), WAJIB memformat pemisah ribuan (titik `.` untuk ID / koma `,` untuk EN) di seluruh komponen UI/UX.\n\n";

        $rules .= "### F. Country Zone Dialing Code Standard\n";
        $rules .= "- Setiap membuat input nomor telepon atau WhatsApp, WAJIB menggunakan country code dropdown yang bisa di-search (`config/country_zones.php`, e.g. +62, +65, +1, +44, +81) untuk menegakkan standar E.164 internasional dan mengeliminasi nomor telepon tidak valid.\n\n";

        $rules .= "### G. Alpine.js HTML Entity Encoding & Script Extraction Standard\n";
        $rules .= "- Ketika menulis inline JavaScript di dalam atribut Alpine.js (`x-data=\"...\"`, `x-init=\"...\"`, `@click=\"...\"`), NEVER use raw double quotes (`\"`) or single quotes (`'`) inside string literals or JSON outputs (`@json()`).\n";
        $rules .= "- MANDATORY HTML ENTITY ENCODING:\n";
        $rules .= "  - Tanda kutip ganda (\") → `&quot;`\n";
        $rules .= "  - Tanda kutip tunggal (') → `&apos;`\n";
        $rules .= "- Untuk komponen Alpine yang kompleks (>3 properti, nested objects, lifecycle hooks, atau method panjang), SELALU ekstrak komponen ke dalam blok `<script>` terpisah (`function componentName() { return { ... }; }`) dan hubungkan dengan `<tag x-data=\"componentName()\">`.\n\n";
        $rules .= "---\n\n";

        // 4. Actors
        $rules .= "## 4. SYSTEM ACTORS & RBAC ROLES\n\n";
        if (!empty($actors)) {
            foreach ($actors as $actor) {
                $name = $actor['role_name'] ?? ($actor['name'] ?? 'User');
                $desc = $actor['responsibilities'] ?? ($actor['desc'] ?? '-');
                $rules .= "- **{$name}**: {$desc}\n";
            }
        } else {
            $rules .= "- **Superadmin**: Full administrative control, configurations, and system monitoring.\n";
            $rules .= "- **Operator / Staff**: Operational data input, processing, and review.\n";
            $rules .= "- **Client / Customer**: Portal access, transaction submission, and report inspection.\n";
        }
        $rules .= "\n---\n\n";

        // 5. ERD Schema
        $rules .= "## 5. DATABASE ERD SCHEMA & ENTITIES\n\n";
        if (!empty($erd)) {
            foreach ($erd as $table) {
                $tname = $table['table_name'] ?? 'entity';
                $tdesc = $table['description'] ?? '';
                $rules .= "### Table: `{$tname}`" . ($tdesc ? " ({$tdesc})" : "") . "\n";
                $rules .= "```sql\n";
                $rules .= "-- Primary key: id (ULID VARCHAR 26)\n";
                if (!empty($table['columns'])) {
                    foreach ($table['columns'] as $col) {
                        $cname = $col['name'] ?? 'col';
                        $ctype = $col['type'] ?? 'VARCHAR(255)';
                        $cnote = !empty($col['note']) ? " -- {$col['note']}" : '';
                        $rules .= "{$cname} {$ctype}{$cnote}\n";
                    }
                }
                $rules .= "```\n\n";
            }
        } else {
            $rules .= "- All domain tables must follow standard ULID naming and migrations.\n\n";
        }
        $rules .= "---\n\n";

        // 6. Features
        $rules .= "## 6. MVP FEATURES & SPRINT DELIVERABLES\n\n";
        if (!empty($mvpFeatures)) {
            foreach ($mvpFeatures as $idx => $f) {
                $num = $idx + 1;
                $ftitle = $f['title'] ?? 'Feature';
                $fdesc = $f['desc'] ?? '';
                $rules .= "### Feature {$num}: {$ftitle}\n";
                if ($fdesc) {
                    $rules .= "- **Description**: {$fdesc}\n";
                }
                if (!empty($f['user_story'])) {
                    $rules .= "- **User Story**: {$f['user_story']}\n";
                }
                if (!empty($f['backend']['model_and_migration'])) {
                    $rules .= "- **Backend**: {$f['backend']['model_and_migration']}\n";
                }
                if (!empty($f['frontend']['design_tokens'])) {
                    $rules .= "- **Frontend**: {$f['frontend']['design_tokens']}\n";
                }
                $rules .= "\n";
            }
        } else {
            $fiturLines = array_filter(explode("\n", (string) $blueprint->fitur_wajib));
            foreach ($fiturLines as $idx => $line) {
                $num = $idx + 1;
                $rules .= "- **MVP {$num}**: " . trim($line) . "\n";
            }
            $rules .= "\n";
        }
        $rules .= "---\n\n";

        // 7. Workflow
        $rules .= "## 7. AI CODE-GEN EXECUTION WORKFLOW\n";
        $rules .= "1. **Vertical Slice Implementation**: Always implement one module end-to-end (Migration -> Model -> Policy/Form -> UI/View -> Test) before proceeding to the next.\n";
        $rules .= "2. **Minimalist & Surgical Diffs**: Never rewrite whole files unnecessarily. Keep diffs precise and token-efficient.\n";
        $rules .= "3. **Automated Verification**: Ensure all code syntax is strictly valid for PHP 8.4+ and runs PHPUnit / Pest tests cleanly.\n";
        $rules .= "4. **Git Discipline**: Every completed slice should be verified against regressions before concluding.\n\n";

        return $rules;
    }

    /**
     * Synthesize high-impact AI strategic architecture guidance using Flagship PRD models
     * (DeepSeek-R1 / Claude 3.7 Sonnet / Gemini 2.5 Pro / GPT-4o / Grok) with automatic failover.
     */
    protected static function synthesizeAiPrdTelemetry(
        VisionBlueprint $blueprint,
        string $businessName,
        string $masalah,
        string $tujuan,
        ?string $preferredAiProvider = null
    ): array {
        $prompt = "You are a Principal Systems Solutions Architect writing an executive summary for an Ultimate PRD. Project: '{$businessName}'. Core Problem: '{$masalah}'. Success Metrics: '{$tujuan}'. Provide 2-3 concise sentences of deep strategic technical guidance on scalability, concurrency, and rapid deployment for this domain.";
        $systemInstruction = "You are the world's leading Enterprise Systems Architect specializing in Laravel 13, Filament v5, PostgreSQL ULID, and high-concurrency systems.";

        try {
            $aiRes = \App\Services\Ai\MultiAiModelManager::executeWithFailover($prompt, $systemInstruction, 'prd', $preferredAiProvider);

            return [
                'provider' => $aiRes['provider'] ?? 'deepseek',
                'provider_name' => $aiRes['provider_name'] ?? 'DeepSeek SOTA Reasoning',
                'model' => $aiRes['model'] ?? 'deepseek-reasoner',
                'fallback_occurred' => $aiRes['fallback_occurred'] ?? false,
                'failed_attempts' => $aiRes['failed_attempts'] ?? [],
                'notification' => $aiRes['notification'] ?? null,
                'strategic_guidance' => $aiRes['text'] ?? null,
            ];
        } catch (\Throwable $e) {
            return [
                'provider' => 'deterministic_heuristic',
                'provider_name' => 'Neriah Pro Deterministic Engine',
                'model' => 'Standard Engineering Rules',
                'fallback_occurred' => false,
                'failed_attempts' => [],
                'notification' => null,
                'strategic_guidance' => 'Sistem dirancang dengan arsitektur Modern Monolith berkinerja tinggi, database PostgreSQL Strict ULID, dan keyset pagination O(1) untuk menjamin latensi rendah pada beban tinggi.',
            ];
        }
    }
}

