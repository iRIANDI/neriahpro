<?php

namespace App\Services\Ai;

/**
 * GeminiToolRegistry: Registri 100% Native Tool Calling Google Gemini & Multi-LLM Gateway.
 * Mendaftarkan operasi modul sistem Neriah Pro Software Factory OS ke format resmi Google Gemini Function Declarations.
 */
class GeminiToolRegistry
{
    /**
     * Dapatkan seluruh deklarasi fungsi resmi untuk parameter tools di Google Gemini API.
     *
     * @return array[]
     */
    public static function getDeclarations(): array
    {
        $declarations = [
            // =========================================================================
            // 1. MODUL PROJECT OS BLUEPRINTS & PRD
            // =========================================================================
            [
                'name' => 'search_blueprints',
                'description' => 'Mencari daftar Project OS Blueprints / PRD berdasarkan kata kunci judul, nama klien, status pengerjaan, atau tier lisensi.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'query' => ['type' => 'STRING', 'description' => 'Kata kunci pencarian (judul proyek, nama klien, atau slug).'],
                        'status' => ['type' => 'STRING', 'description' => 'Filter status proyek: "Draft", "Review", "Scope Locked", "Active Sprint", "Completed".'],
                        'tier' => ['type' => 'STRING', 'description' => 'Filter tier: "retail_spark", "retail_lite", "retail_pro", "retail_ultimate", "studio_advisory", "studio_mvp", "studio_umkm".'],
                        'limit' => ['type' => 'INTEGER', 'description' => 'Jumlah hasil maksimal yang diambil (default: 5).'],
                    ],
                ],
            ],
            [
                'name' => 'get_blueprint_details',
                'description' => 'Mengambil rincian utuh satu blueprint: dokumen PRD 26 parameter, skema DDL PostgreSQL Strict ULID, atau diagram WBS Gantt Mermaid.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => ['type' => 'STRING', 'description' => 'ULID ID atau slug unik dari blueprint yang ingin diperiksa.'],
                    ],
                    'required' => ['blueprint_id'],
                ],
            ],
            [
                'name' => 'create_blueprint',
                'description' => 'Membuat blueprint arsitektur proyek baru ke dalam database Project OS.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'title' => ['type' => 'STRING', 'description' => 'Judul atau nama proyek software yang dirancang.'],
                        'client_name' => ['type' => 'STRING', 'description' => 'Nama pemilik proyek atau entitas klien.'],
                        'client_email' => ['type' => 'STRING', 'description' => 'Email klien untuk verifikasi dan pengiriman notifikasi.'],
                        'client_whatsapp' => ['type' => 'STRING', 'description' => 'Nomor WhatsApp klien berformat E.164 (misal: +6281234567890).'],
                        'service_tier' => ['type' => 'STRING', 'description' => 'Tier layanan: "retail_spark", "retail_lite", "retail_pro", "retail_ultimate", "studio_advisory", "studio_mvp", "studio_umkm". Default: "studio_mvp".'],
                        'idea_summary' => ['type' => 'STRING', 'description' => 'Ringkasan ide bisnis, masalah yang diselesaikan, dan modul utama aplikasi.'],
                    ],
                    'required' => ['title', 'client_name'],
                ],
            ],
            [
                'name' => 'update_blueprint_status',
                'description' => 'Memperbarui status pengerjaan atau tahapan siklus hidup blueprint.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => ['type' => 'STRING', 'description' => 'ULID ID dari blueprint yang ingin diubah.'],
                        'status' => ['type' => 'STRING', 'description' => 'Status baru: "Draft", "Review", "Scope Locked", "Active Sprint", "Completed".'],
                        'notes' => ['type' => 'STRING', 'description' => 'Catatan arsitek / alasan perubahan status.'],
                    ],
                    'required' => ['blueprint_id', 'status'],
                ],
            ],
            [
                'name' => 'convert_to_contract',
                'description' => 'Mengonversi blueprint PRD menjadi dokumen kontrak legal berstempel SHA-256 (Scope Lock) dan siap ditandatangani.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => ['type' => 'STRING', 'description' => 'ULID ID blueprint yang ingin dikunci ke dalam kontrak legal.'],
                        'contract_title' => ['type' => 'STRING', 'description' => 'Judul dokumen kontrak (opsional).'],
                    ],
                    'required' => ['blueprint_id'],
                ],
            ],

            // =========================================================================
            // 2. MODUL TRANSAKSI, BILLING & MIDTRANS
            // =========================================================================
            [
                'name' => 'search_transactions',
                'description' => 'Mencari data transaksi invoice pembayaran klien (DP 50%, Pelunasan 50%, Lisensi Retail).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'query' => ['type' => 'STRING', 'description' => 'Nomor invoice, order ID Midtrans, atau nama pembayar.'],
                        'status' => ['type' => 'STRING', 'description' => 'Status transaksi: "pending", "settlement", "expire", "cancel".'],
                        'limit' => ['type' => 'INTEGER', 'description' => 'Jumlah baris maksimal (default: 5).'],
                    ],
                ],
            ],

            // =========================================================================
            // 3. MODUL DOMAIN, HOSTING & SERVER ASSETS
            // =========================================================================
            [
                'name' => 'manage_domain_hosting',
                'description' => 'Mengelola aset infrastruktur klien (domain, hosting, VPS, dedicated server), memantau tanggal kedaluwarsa, atau mendaftarkan aset baru.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'action' => ['type' => 'STRING', 'description' => 'Tindakan: "list_expiring" (aset yang mendekati jatuh tempo), "search" (cari aset), "create" (catat aset baru).'],
                        'search' => ['type' => 'STRING', 'description' => 'Nama domain, host, atau nama aset yang dicari.'],
                        'domain_name' => ['type' => 'STRING', 'description' => 'Nama domain/hostname jika membuat aset baru (misal: "klienapp.com").'],
                        'service_name' => ['type' => 'STRING', 'description' => 'Nama layanan (misal: "Domain Utama Klien").'],
                        'asset_type' => ['type' => 'STRING', 'description' => 'Tipe aset: "domain", "hosting", "vps", "ssl".'],
                        'cost_price' => ['type' => 'NUMBER', 'description' => 'Biaya Modal Beli / Sewa (HPP).'],
                        'client_price' => ['type' => 'NUMBER', 'description' => 'Harga tagihan ke klien.'],
                        'expires_at' => ['type' => 'STRING', 'description' => 'Tanggal jatuh tempo (format: YYYY-MM-DD).'],
                    ],
                    'required' => ['action'],
                ],
            ],

            // =========================================================================
            // 4. MODUL KNOWLEDGE BASE (HYBRID RAG • CAG • KAG)
            // =========================================================================
            [
                'name' => 'query_knowledge_base',
                'description' => 'Melakukan pencarian semantik cerdas (Hybrid RAG RRF) ke repositori vektor pgvector: mencakup 10 Pilar Software Factory OS, Buku Panduan Smart Guide, SOP hukum PDP/ITE, dan Unit Economics.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'query' => ['type' => 'STRING', 'description' => 'Pertanyaan atau konsep arsitektur yang ingin dicari (misal: "Aturan PostgreSQL Strict ULID" atau "Skema garansi SLA Studio MVP").'],
                        'limit' => ['type' => 'INTEGER', 'description' => 'Jumlah kutipan teratas yang diambil (default: 4).'],
                    ],
                    'required' => ['query'],
                ],
            ],
            [
                'name' => 'sync_knowledge_base',
                'description' => 'Menyinkronkan ulang seluruh 10 Pilar Software Factory OS dan Buku Panduan ke basis data vektor (pgvector).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'force_refresh' => ['type' => 'BOOLEAN', 'description' => 'Apakah menghapus data vektor lama dan membuat ulang secara total (default: true).'],
                    ],
                ],
            ],

            // =========================================================================
            // 5. MODUL AUDIT LOG & CCTV TRACE
            // =========================================================================
            [
                'name' => 'get_audit_logs',
                'description' => 'Mengambil jejak audit trail keamanan (Audit Log) terbaru yang mencatat tindakan aktor/admin, IP address, dan diff perubahan data.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'action' => ['type' => 'STRING', 'description' => 'Filter tipe aksi: "create", "update", "delete", "login", "security".'],
                        'limit' => ['type' => 'INTEGER', 'description' => 'Jumlah baris audit log yang diambil (default: 5).'],
                    ],
                ],
            ],

            // =========================================================================
            // 6. MODUL EKSEKUTIF & LEADS
            // =========================================================================
            [
                'name' => 'get_system_stats',
                'description' => 'Mengambil statistik langsung kesehatan eksekutif sistem: jumlah blueprint aktif, transaksi, status kapasitas sprint, dan aset domain/hosting.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'scope' => [
                            'type' => 'STRING',
                            'description' => 'Filter cakupan data statistik: "all", "sprint", "blueprints", "assets", "revenue". Default: "all".',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'search_leads',
                'description' => 'Melihat daftar calon klien / leads yang masuk dari formulir konsultasi onboarding website publik.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'limit' => ['type' => 'INTEGER', 'description' => 'Jumlah leads maksimal yang diambil (default: 5).'],
                    ],
                ],
            ],

            // =========================================================================
            // 7. MODUL PROJECT OS v2.5: SECTORAL UI/UX & 6 KILLER ORCHESTRATION ENGINES
            // =========================================================================
            [
                'name' => 'get_sector_ui_archetype',
                'description' => 'Mengambil kontrak Sectoral Industrial UI/UX Archetype (Primary & Secondary Archetype, ergonomi panel, pintasan keyboard, larangan UX, wireframe blueprint, dan Realistic Domain Seeder Indonesia) untuk salah satu dari 12 Sektor Fungsional Project OS.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'sector_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode sektor (misal: "SECTOR_01_COMMERCE_OMNICHANNEL", "SECTOR_03_HEALTHCARE_CLINICAL", "SECTOR_04_FINTECH_LEDGER") atau kata kunci industri (misal: "klinik", "kasir pos", "hotel", "akuntansi", "kds restoran"). Kosongkan untuk daftar ringkas 12 sektor.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'generate_phased_prompts',
                'description' => 'Mengambil urutan Phased Prompt Sequencer (Fase 1 s/d 6 Anti-Context Overflow) beserta perintah One-Command CLI Bootstrapper (curl -sL .../init/{id} | bash) untuk sebuah Blueprint Project OS.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'ULID ID atau slug unik dari blueprint yang ingin diambil urutan prompt eksekusinya.',
                        ],
                        'phase_number' => [
                            'type' => 'INTEGER',
                            'description' => 'Nomor fase spesifik (1 sampai 6). Kosongkan jika ingin mengambil ringkasan seluruh 6 fase.',
                        ],
                    ],
                    'required' => ['blueprint_id'],
                ],
            ],
            [
                'name' => 'audit_brownfield_schema',
                'description' => 'Menjalankan Reverse-Engineer / Brownfield SQL & Migration Importer pada skema database warisan (CREATE TABLE SQL, Laravel Migration, atau Prisma) untuk mendeteksi pelanggaran (AUTO_INCREMENT, CASCADE, FLOAT, ketiadaan organization_id/lockForUpdate) dan menyusun rencana migrasi Shadow ULID Zero-Data-Loss.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'schema_text' => [
                            'type' => 'STRING',
                            'description' => 'Teks skema DDL SQL, migrasi Laravel, atau schema.prisma yang akan diaudit.',
                        ],
                        'project_hint' => [
                            'type' => 'STRING',
                            'description' => 'Konteks singkat domain bisnis proyek (opsional, misal: "Sistem Klinik & Apotek").',
                        ],
                    ],
                    'required' => ['schema_text'],
                ],
            ],
            [
                'name' => 'run_blueprint_slop_scanner',
                'description' => 'Menjalankan Blueprint Drift Auditor & Slop Scanner terhadap sebuah Blueprint atau potongan kode untuk memverifikasi kepatuhan Strict ULID, Zero UI Slop (tanpa rounded-full, gradien, alert()), 7 Dimensi Zero-Assumption, dan menghasilkan Auto-Fix Prompt.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'ULID ID atau slug blueprint yang ingin diaudit kepatuhannya (opsional jika memindai code_snippet).',
                        ],
                        'code_snippet' => [
                            'type' => 'STRING',
                            'description' => 'Potongan kode Blade, JSX, Migration, atau PHP yang ingin dipindai terhadap aturan Anti-Slop (opsional).',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'run_blueprint_doctor',
                'description' => 'Menjalankan Layer 8 Interactive Blueprint Doctor (Pre-Flight Conflict Resolver & Readiness Validator) pada sebuah Blueprint yang sudah ada atau konfigurasi sektor/modul untuk mendeteksi modul primitif yang terlewat (mis. Resep Apotek tanpa Inventory FEFO, pembayaran tanpa Double-Entry Ledger, Microservices pada jadwal mepet) beserta rekomendasi Auto-Fix 1-klik.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'ULID ID atau slug blueprint yang ingin diperiksa oleh Blueprint Doctor (opsional jika menggunakan sector_code).',
                        ],
                        'sector_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode sektor bisnis (misal: SECTOR_03_HEALTHCARE_CLINICAL, SECTOR_01_COMMERCE_OMNICHANNEL).',
                        ],
                        'stack_preset_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode stack preset (misal: STACK_M1_LARAVEL_MONOLITH, STACK_S1_POLYGLOT_GO_LARAVEL_TEMPORAL).',
                        ],
                        'workflow_text' => [
                            'type' => 'STRING',
                            'description' => 'Deskripsi alur kerja bisnis untuk mendeteksi kebutuhan lintas modul.',
                        ],
                        'timeline_days' => [
                            'type' => 'INTEGER',
                            'description' => 'Target durasi pengerjaan proyek dalam hari (opsional, default 45).',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'get_day2_production_contracts',
                'description' => 'Mengambil spesifikasi lengkap 8 Layer Day-2 Production (Thermal 58/80mm ESC/POS & Official A4 QR Print, InitializeNewTenantAction Zero-State, Historical *_snapshot Immutability, Staged Bulk CSV Importer, 4-Lane Redis Queue & Cron Registry, Defense-in-Depth Private Disk Signed URLs, & Omnichannel Notification Matrix) untuk sektor tertentu.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'sector_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode sektor bisnis (misal: SECTOR_01_COMMERCE_OMNICHANNEL s/d SECTOR_12_GOVTECH_NONPROFIT) atau kata kunci industri.',
                        ],
                    ],
                ],
            ],
        ];

        // Format sanitasi resmi Google Gemini Protobuf Map Schema
        // Mencegah konversi empty array [] menjadi JSON list yang memicu HTTP 400
        return array_map(function ($tool) {
            if (isset($tool['parameters'])) {
                if (!isset($tool['parameters']['properties']) || empty($tool['parameters']['properties'])) {
                    $tool['parameters']['properties'] = (object)[];
                }
            }
            return $tool;
        }, $declarations);
    }

    /**
     * Dapatkan deklarasi tools dalam format OpenAI / DeepSeek compatible (`type: "function"`).
     *
     * @return array[]
     */
    public static function getOpenAiTools(): array
    {
        $declarations = self::getDeclarations();
        $tools = [];

        foreach ($declarations as $decl) {
            $tools[] = [
                'type' => 'function',
                'function' => [
                    'name' => $decl['name'],
                    'description' => $decl['description'] ?? '',
                    'parameters' => self::normalizeOpenAiParameters($decl['parameters'] ?? []),
                ],
            ];
        }

        return $tools;
    }

    /**
     * Normalisasi tipe parameter huruf besar Gemini ('OBJECT', 'STRING') ke schema OpenAI ('object', 'string').
     */
    protected static function normalizeOpenAiParameters(array $params): array
    {
        $normalized = [];

        foreach ($params as $k => $v) {
            if ($k === 'type' && is_string($v)) {
                $normalized[$k] = strtolower($v);
            } elseif (is_array($v)) {
                $normalized[$k] = self::normalizeOpenAiParameters($v);
            } else {
                $normalized[$k] = $v;
            }
        }

        if (!isset($normalized['type'])) {
            $normalized['type'] = 'object';
        }

        if (isset($normalized['properties']) && empty($normalized['properties'])) {
            $normalized['properties'] = (object)[];
        }

        return $normalized;
    }
}

