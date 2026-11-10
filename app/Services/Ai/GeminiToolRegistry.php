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
            [
                'name' => 'generate_delta_change_request',
                'description' => 'Membuat Iterative Change Request & Surgical Delta Blueprint (v1.0 -> v1.1) pada proyek/blueprint aktif. Menghasilkan HANYA migrasi aditif strict-ULID, diff graph.json, delta tool declarations, dan skenario DeltaRegressionTest agar aplikasi produksi v1.0 tidak rusak oleh AI slop.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'ULID ID atau slug blueprint yang ingin dievolusikan dari v1.0 ke v1.1.',
                        ],
                        'title' => [
                            'type' => 'STRING',
                            'description' => 'Judul ringkas penambahan fitur baru (misal: "Program Loyalty Poin & Cabang Surabaya").',
                        ],
                        'change_summary' => [
                            'type' => 'STRING',
                            'description' => 'Deskripsi rinci kebutuhan fitur evolusi bisnis untuk dikompilasi menjadi Surgical Delta Prompt.',
                        ],
                    ],
                    'required' => ['blueprint_id', 'change_summary'],
                ],
            ],
            [
                'name' => 'compile_decision_table_matrix',
                'description' => 'Mengambil dan mengompilasi Deterministic Business Rule Decision Tables (Anti-Nested if-else Slop) untuk suatu sektor atau blueprint menjadi tabel kebenaran spreadsheet dan class PHP 8.4+ match() (DeterministicDecisionMatrix.php) berpresisi DECIMAL(19,4) HALF_UP.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'ULID ID atau slug blueprint (opsional jika menggunakan sector_code).',
                        ],
                        'sector_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode sektor bisnis (misal: SECTOR_01_COMMERCE_OMNICHANNEL s/d SECTOR_12_GOVTECH_NONPROFIT).',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'audit_environment_and_redteam_readiness',
                'description' => 'Mengambil kontrak Environment & Secret Vault (.env.blueprint + php artisan pos:check-env), Phase 7 Automated Security Red-Team & Tenant-Leakage Test Suite (IDOR, Livewire Locked, Private Disk 403, Prompt Injection), serta status Live MCP Server Bridge (neriahpro-mcp) untuk suatu blueprint atau sektor.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'ULID ID atau slug blueprint (opsional jika menggunakan sector_code).',
                        ],
                        'sector_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode sektor bisnis (misal: SECTOR_04_FINTECH_BILLING_LEDGER).',
                        ],
                        'stack_preset_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode stack preset (misal: STACK_M1_LARAVEL_FILAMENT_LIVEWIRE).',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'calculate_mitra_quotation_and_wbs',
                'description' => 'Menghitung RAB / Quotation algoritmik Mitra Proyek (Man-Hours, Hari Kerja, Pengali Stack M1-S2, WBS 7 Fase, Jadwal Termin 30/50/20 atau 50/50, SHA-256 Scope Hash, dan perbandingan paket Satu Pintu Retail vs Mitra) berdasarkan blueprint atau kombinasi sektor + stack.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'ULID ID atau slug blueprint (opsional jika menggunakan sector_code).',
                        ],
                        'sector_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode 12 sektor bisnis (misal: SECTOR_01_COMMERCE_OMNICHANNEL s/d SECTOR_12_GOVTECH_NONPROFIT).',
                        ],
                        'stack_preset_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode 9 stack adapter (misal: STACK_M1_LARAVEL_FILAMENT_LIVEWIRE, STACK_D1_LARAVEL_NEXTJS, STACK_S1_GO_NEXTJS).',
                        ],
                        'termin_scheme' => [
                            'type' => 'STRING',
                            'description' => 'Skema termin pembayaran: 30_50_20 (default) atau 50_50.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'freeze_scope_and_generate_spk_contract',
                'description' => 'Membekukan ruang lingkup teknis Blueprint v1.0 dengan segel kriptografi SHA-256 Scope Hash dan menerbitkan dokumen legal SPK / PKS (Lampiran Teknis SPK) secara otomatis untuk mencegah scope creep.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'ULID ID atau slug blueprint yang ingin dibekukan ruang lingkup SPK-nya.',
                        ],
                        'termin_scheme' => [
                            'type' => 'STRING',
                            'description' => 'Skema termin pembayaran kontrak: 30_50_20 atau 50_50.',
                        ],
                    ],
                    'required' => ['blueprint_id'],
                ],
            ],
            [
                'name' => 'manage_uat_checklist_and_digital_bast',
                'description' => 'Memeriksa status Interactive UAT Checklist (Given-When-Then), menandai skenario UAT terverifikasi, atau menandatangani Digital BAST (Berita Acara Serah Terima bersegel SHA-256) beserta penerbitan Invoice Pelunasan Akhir.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'ULID ID atau slug blueprint.',
                        ],
                        'action' => [
                            'type' => 'STRING',
                            'description' => 'Aksi yang dijalankan: inspect (baca daftar UAT & milestone), verify_all_uat (tandai semua skenario UAT lulus), atau sign_bast (terbitkan BAST Digital & invoice pelunasan).',
                        ],
                        'signer_name' => [
                            'type' => 'STRING',
                            'description' => 'Nama penandatangan BAST dari pihak klien/admin saat action = sign_bast.',
                        ],
                        'signer_title' => [
                            'type' => 'STRING',
                            'description' => 'Jabatan penandatangan BAST (misal: Direktur Utama / Product Owner).',
                        ],
                    ],
                    'required' => ['blueprint_id'],
                ],
            ],
            [
                'name' => 'expand_l1_l4_enterprise_menu_matrix',
                'description' => 'Mekarkan matriks kapabilitas menu L1–L4 (5 Divisi Operasional × 5 Sub-Menu = 25 layar korporat lengkap) beserta 5 Primitif Perekat Korporat (Enterprise Glue) untuk salah satu dari 12 Sektor Fungsional Project OS v6.0.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'sector_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode sektor fungsional (misal: SECTOR_01_COMMERCE_OMNICHANNEL, SECTOR_03_HEALTHCARE_CLINICAL, SECTOR_04_FINTECH_LEDGER).',
                        ],
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'Opsional ULID atau slug VisionBlueprint jika ingin mengambil pilihan menu spesifik proyek.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'compile_symbol_manifest_and_dag_chunks',
                'description' => 'Kompilasi kontrak simbol eksak SYMBOL_MANIFEST.json, pengurutan migrasi tabel berbasis Kahn Topological Sort (DAG), dan pemecahan prompt sub-part anti-truncation (Part 1.0 s/d Part 7.0 dengan <MANDATORY_VERIFICATION_GATE>).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'Opsional ULID atau slug VisionBlueprint yang sudah tersimpan.',
                        ],
                        'sector_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode sektor fungsional bila melakukan simulasi tanpa blueprint_id.',
                        ],
                        'stack_preset' => [
                            'type' => 'STRING',
                            'description' => 'Kode preset stack (misal: STACK_M1_LARAVEL_MONOLITH, STACK_D1_LARAVEL_NEXTJS).',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'verify_project_isolation_and_clean_state',
                'description' => 'Audit isolasi hermetik lintas proyek (memastikan setiap proyek memiliki ULID, slug, isolation_token, dan staging_url unik tanpa menimpa proyek lama) atau bersihkan sisa draf sesi (action = purge_stale_drafts).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'action' => [
                            'type' => 'STRING',
                            'description' => 'Mode operasi: inspect (audit isolasi seluruh proyek read-only) atau purge_stale_drafts (bersihkan sisa draf sesi aktif — wajib izin mutasi RBAC).',
                        ],
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'Opsional ULID atau slug proyek tertentu yang ingin diperiksa keunikan isolasinya.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'inspect_hardware_and_websocket_runtime_contracts',
                'description' => 'Inspeksi Kontrak Elemen Dunia Nyata #1, #2 & #3: Jembatan Hardware Fisik (Scanner Barcode Keyboard Wedge <30ms, Dual-Screen QRIS Lokal tanpa CDN, Biometrik ZKTeco/Turnstile), Kanal WebSocket Zero-Polling (larangan wire:poll <30s), serta Composite Index Tenant & Partisi Kuartal PostgreSQL 16.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'Opsional ULID atau slug VisionBlueprint yang sudah tersimpan.',
                        ],
                        'sector_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode sektor fungsional bila simulasi tanpa blueprint_id (misal: SECTOR_01_COMMERCE_OMNICHANNEL, SECTOR_03_HEALTHCARE_CLINICAL).',
                        ],
                        'stack_preset' => [
                            'type' => 'STRING',
                            'description' => 'Kode preset stack (misal: STACK_M1_LARAVEL_MONOLITH, STACK_S1_POLYGLOT_GO_LARAVEL_TEMPORAL).',
                        ],
                        'locale' => [
                            'type' => 'STRING',
                            'description' => 'Bahasa output Tier-1: id atau en.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'generate_syntax_lock_and_client_sop_pack',
                'description' => 'Kompilasi Kontrak Elemen Dunia Nyata #4 & #5: Kamus Sintaks Anti-Halusinasi SYNTAX_LOCK_2026.md & TROUBLESHOOTING_INSTINCTS.md sesuai Stack serta Panduan In-App SmartGuide.php & Buku SOP Training Staf (CLIENT_SOP_AND_TRAINING_MANUAL.md) per Role untuk serah terima BAST.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'Opsional ULID atau slug VisionBlueprint yang sudah tersimpan.',
                        ],
                        'sector_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode sektor fungsional bila simulasi tanpa blueprint_id.',
                        ],
                        'stack_preset' => [
                            'type' => 'STRING',
                            'description' => 'Kode preset stack target (M1..S2).',
                        ],
                        'locale' => [
                            'type' => 'STRING',
                            'description' => 'Bahasa buku SOP Tier-1: id atau en.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'audit_tier1_multilanguage_and_schema_integrity',
                'description' => 'Audit kepatuhan skema basis data ERD terhadap standar Multi-Language 2-Tier Architecture (Tier 1 Native JSONB) vs Integritas Skalar (Strict ULID PK/FK, Unique Code/Slug/Email, State Machine Enums). Memeriksa bahwa seluruh kolom narasi/konten manusiawi (title, name, description, reason, memo, message_body, decision_note, tier_name) bertipe JSONB, dan seluruh kunci/indeks/status tetap skalar VARCHAR biasa.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'Opsional ULID atau slug VisionBlueprint yang ingin diaudit skema ERD-nya. Jika kosong, mengaudit blueprint aktif terakhir.',
                        ],
                        'auto_heal' => [
                            'type' => 'BOOLEAN',
                            'description' => 'Jika true, secara otomatis menyelaraskan tipe kolom narasi yang tertinggal ke JSONB dalam prd_content blueprint.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'audit_fifteen_pillars_architectural_freeze',
                'description' => 'Audit status 15 Pilar Master Project OS Ultimate 100% (100% Architectural Freeze), verifikasi sinkronisasi kausalitas lintas tahap (Cross-Stage Causality Graph), dan opsional sinkronkan ulang katalog 15 pilar ke database (persist_sync = true memerlukan izin mutasi RBAC).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'Opsional ULID atau slug VisionBlueprint untuk mengaudit kausalitas lintas tahap proyek tersebut.',
                        ],
                        'persist_sync' => [
                            'type' => 'BOOLEAN',
                            'description' => 'Jika true, menyinkronkan ulang 15 Pilar Master ke tabel pos_architecture_pillars dan mengkompilasi ulang proyek (wajib izin mutasi RBAC).',
                        ],
                        'locale' => [
                            'type' => 'STRING',
                            'description' => 'Bahasa output Tier-1: id atau en.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'configure_visual_ui_and_jurisdiction_locale',
                'description' => 'Inspeksi atau simpan konfigurasi Visual Frontend UI/UX dari 36 varian terkurasi (12 Sektor × 3 Varian UI + 5 Navigation Shells), mode Dual-Persona (BUSINESS_WIZARD / ARCHITECT_PRO), dan Yurisdiksi Global Tier-1 (id, en, zh-CN, ja, ar dengan dir=rtl otomatis, es, de) beserta kalkulasi kausalitas RAB & SHA-256.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'sector_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode sektor fungsional (SECTOR_01_COMMERCE_OMNICHANNEL s/d SECTOR_12_GOVTECH_NONPROFIT).',
                        ],
                        'selected_ui_variant' => [
                            'type' => 'STRING',
                            'description' => 'Opsional kode varian UI visual (misal UI_01A_POS_SPLIT_TERMINAL, UI_03A_CLINICAL_3PANE_SOAP, UI_12B_PUBLIC_DONOR_TRUST_PORTAL).',
                        ],
                        'navigation_shell' => [
                            'type' => 'STRING',
                            'description' => 'Opsional kerangka navigasi: SIDEBAR_ERP_DENSE, FULLSCREEN_OPERATIONAL_KIOSK, DUAL_PANE_SPLIT_WORKBENCH, MOBILE_FIRST_THUMB_PWA, atau TOPBAR_EXECUTIVE_DECK.',
                        ],
                        'output_locale' => [
                            'type' => 'STRING',
                            'description' => 'Kode yurisdiksi output Tier-1: id, en, zh-CN, ja, ar (otomatis RTL), es, atau de.',
                        ],
                        'persona_mode' => [
                            'type' => 'STRING',
                            'description' => 'Mode pengalaman pengguna: BUSINESS_WIZARD (Awam 5 langkah) atau ARCHITECT_PRO (3-Pane Studio).',
                        ],
                        'project_ulid' => [
                            'type' => 'STRING',
                            'description' => 'Opsional ULID PosProject untuk menyimpan perubahan secara persisten (memerlukan izin mutasi RBAC).',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'audit_living_cve_alerts_and_autopatch',
                'description' => 'Audit peringatan keamanan aktif Living Architecture (CVE-2026-109618, GHSA-NEXT-MW-2026, SYNTAX-LOCK-2026.07), hasilkan 1-Click Auto-Patch Prompt siap tempel ke IDE, dan tampilkan katalog Verified Community Primitives Marketplace (bagi hasil 70:30).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'stack_preset' => [
                            'type' => 'STRING',
                            'description' => 'Kode preset stack (misal STACK_M1_LARAVEL_MONOLITH, STACK_D1_NEXTJS_LARAVEL_API, STACK_S1_GO_POLYGLOT_FINTECH).',
                        ],
                        'sector_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode sektor fungsional untuk konteks Active Vault.',
                        ],
                        'project_ulid' => [
                            'type' => 'STRING',
                            'description' => 'Opsional ULID PosProject untuk mencatat status acknowledge CVE (memerlukan izin RBAC jika mutasi).',
                        ],
                        'acknowledge_all' => [
                            'type' => 'BOOLEAN',
                            'description' => 'Jika true dan project_ulid diberikan, menandai CVE telah di-acknowledge pada proyek tersebut.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'manage_ip_canary_watermark_and_vault_security',
                'description' => 'Kelola 5-Layer Banking-Grade IP & Platform Security: dekode sidik jari forensik Zero-Width Unicode Canary Watermark 64-bit dari teks Markdown bocor (verify_canary), cabut/rotasi token MCP 1-klik (revoke_mcp_token), atau inspeksi matriks keamanan AES-256 Vault & Escrow (inspect_layers).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'action' => [
                            'type' => 'STRING',
                            'description' => 'Operasi keamanan: inspect_layers, verify_canary, atau revoke_mcp_token.',
                        ],
                        'leaked_markdown_sample' => [
                            'type' => 'STRING',
                            'description' => 'Teks Markdown yang ingin didekode watermark Zero-Width Canary-nya (untuk verify_canary).',
                        ],
                        'project_ulid' => [
                            'type' => 'STRING',
                            'description' => 'Opsional ULID PosProject untuk pencabutan token MCP 1-klik (wajib izin mutasi RBAC).',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'sanitize_and_audit_agentshield',
                'description' => 'Jalankan NeriahPro AgentShield 5-Layer Defense: sanitasi teks dokumen klien dari Indirect Prompt Injection & zero-width Unicode (<UNTRUSTED_CLIENT_DOCUMENT_DATA>), periksa daftar package dari serangan Slopquatting terhadap APPROVED_DEPENDENCY_MANIFEST, atau inspeksi File-Path Jail .claude/settings.json.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'operation' => [
                            'type' => 'STRING',
                            'description' => 'Operasi AgentShield: sanitize_document, audit_packages, atau inspect_sandbox.',
                        ],
                        'document_text' => [
                            'type' => 'STRING',
                            'description' => 'Teks mentah dokumen klien untuk dipindai dan disanitasi (untuk sanitize_document).',
                        ],
                        'packages' => [
                            'type' => 'ARRAY',
                            'items' => ['type' => 'STRING'],
                            'description' => 'Daftar nama package Composer/NPM/Go untuk diaudit terhadap APPROVED_DEPENDENCY_MANIFEST (untuk audit_packages).',
                        ],
                        'stack_preset' => [
                            'type' => 'STRING',
                            'description' => 'Kode Stack Preset (misal STACK_M1_LARAVEL_MONOLITH, STACK_D1_LARAVEL_NEXTJS, STACK_S1_POLYGLOT_GO_LARAVEL).',
                        ],
                        'sector_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode Sektor Fungsional (SECTOR_01 s/d SECTOR_12).',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'manage_codesign_alignment_room',
                'description' => 'Kelola The Co-Design Alignment & Sign-Off Room (Dual-Mirror Client Wireframe <-> Architect ULID): inspeksi status persetujuan per modul & World-Class 5-Sprint Tasks (inspect_room), setujui klausa modul (sign_module), tambah kebutuhan klien baru + rekalkulasi RAB live (add_custom_module), atau eksekusi Dual-Key Cryptographic Scope Lock v1.0 (dual_key_lock).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'action' => [
                            'type' => 'STRING',
                            'description' => 'Aksi: inspect_room, sign_module, add_custom_module, atau dual_key_lock.',
                        ],
                        'blueprint_id' => [
                            'type' => 'STRING',
                            'description' => 'ULID atau slug VisionBlueprint proyek.',
                        ],
                        'module_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode modul divisi untuk sign_module (misal DIV_01_CORE_POS).',
                        ],
                        'status' => [
                            'type' => 'STRING',
                            'description' => 'Status persetujuan modul: APPROVED, DISCUSSION, atau PENDING.',
                        ],
                        'module_title' => [
                            'type' => 'STRING',
                            'description' => 'Judul kebutuhan tambahan klien untuk add_custom_module.',
                        ],
                        'extra_man_hours' => [
                            'type' => 'INTEGER',
                            'description' => 'Tambahan jam kerja estimasi untuk modul tambahan klien.',
                        ],
                        'signer_name' => [
                            'type' => 'STRING',
                            'description' => 'Nama perwakilan penandatangan klien atau arsitek.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'verify_and_export_crypto_audit_trail',
                'description' => 'Verifikasi keutuhan rantai hash SHA-256 append-only (prev_hash -> current_hash) pada tabel audit_logs (verify_chain), ambil 12 Sector-Specific Audit Filter Presets (list_presets), atau siapkan ringkasan ekspor 3-Sheet .xlsx / .csv sesuai kepatuhan PSAK 109 / ISAK 35 / ISO 27001 / RME Kemenkes (prepare_export_summary).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'operation' => [
                            'type' => 'STRING',
                            'description' => 'Operasi: verify_chain, list_presets, atau prepare_export_summary.',
                        ],
                        'sector_preset' => [
                            'type' => 'STRING',
                            'description' => 'Opsional kode sektor (SECTOR_01 s/d SECTOR_12) untuk memfilter preset industri.',
                        ],
                        'execution_channel' => [
                            'type' => 'STRING',
                            'description' => 'Opsional filter kanal eksekusi: WEB_UI, AI_COPILOT_TOOL, WEBHOOK, atau CLI.',
                        ],
                        'limit' => [
                            'type' => 'INTEGER',
                            'description' => 'Batas jumlah baris verifikasi rantai terbaru (default 200).',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'configure_dual_zone_public_ux_preset',
                'description' => 'Inspeksi atau simpan konfigurasi Dual-Zone UI Architecture Project OS v10.0: Zone A Operational Back-Stage (Linear/Stripe Crisp 36 UIs, 0% parallax) + Zone B Public Front-Stage (6 Preset Advanced UX: UX_F1_SCROLLYTELLING, UX_F2_KINETIC_ANIWALL, UX_F3_SPATIAL_SHOWCASE, UX_F4_LOOKBOOK_COMMERCE, UX_F5_TRUST_SIMULATOR, UX_F6_GAMIFIED_PATHWAY), 5 Hukum Besi Animasi Anti-Slop (GPU translate3d/opacity, CLS 0.00, 0% CDN), serta Universal Input Hygiene (email karakter ketat, pemisah ribuan regional, country_zones.php hapus 0 depan).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'sector_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode sektor fungsional (SECTOR_01_COMMERCE_OMNICHANNEL s/d SECTOR_12_GOVTECH_NONPROFIT).',
                        ],
                        'public_ux_preset' => [
                            'type' => 'STRING',
                            'description' => 'Opsional kode preset Zone B: UX_F1_SCROLLYTELLING, UX_F2_KINETIC_ANIWALL, UX_F3_SPATIAL_SHOWCASE, UX_F4_LOOKBOOK_COMMERCE, UX_F5_TRUST_SIMULATOR, atau UX_F6_GAMIFIED_PATHWAY.',
                        ],
                        'idea_text' => [
                            'type' => 'STRING',
                            'description' => 'Opsional teks ide proyek untuk auto-matching preset UX publik paling relevan.',
                        ],
                        'project_ulid' => [
                            'type' => 'STRING',
                            'description' => 'Opsional ULID PosProject untuk menyimpan perubahan secara persisten & mengalkulasi ulang RAB + SHA-256 (wajib izin RBAC).',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'manage_verified_testimonials_aniwall',
                'description' => 'Inspeksi katalog Infinite Kinetic Aniwall (PRIMITIVE_VERIFIED_SOCIAL_PROOF — Testimoni Klien Terverifikasi BAST / Phase 6 lengkap Telemetry Architecture Receipt) lintas 12 Sektor, atau terbitkan Architecture Receipt baru untuk PosProject yang telah lolos BAST / Phase 6 (issue_receipt wajib izin mutasi RBAC).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'action' => [
                            'type' => 'STRING',
                            'description' => 'Aksi: list_aniwall (baca katalog per sektor) atau issue_receipt (terbitkan testimoni terverifikasi untuk PosProject).',
                        ],
                        'sector_code' => [
                            'type' => 'STRING',
                            'description' => 'Opsional filter kode sektor (SECTOR_01..SECTOR_12).',
                        ],
                        'project_ulid' => [
                            'type' => 'STRING',
                            'description' => 'ULID PosProject bila action = issue_receipt.',
                        ],
                        'verification_trigger' => [
                            'type' => 'STRING',
                            'description' => 'MITRA_BAST_SIGNED atau RETAIL_PHASE6_VERIFIED.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'manage_affiliate_and_impact_grants',
                'description' => 'Inspeksi buku besar Komisi Afiliasi & Mitra Finder\'s Fee (?ref=CODE — 20% Retail Recurring + 7.5% Mitra Turnkey), tinjau antrean permohonan Non-Profit/Yayasan/Akademik Impact Grant, atau setujui 1-klik permohonan Impact Grant beserta penerbitan Voucher Gratis 100% (approve_grant wajib izin mutasi RBAC).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'action' => [
                            'type' => 'STRING',
                            'description' => 'Aksi: summary (ringkasan afiliasi & grant), list_pending_grants, atau approve_grant (wajib RBAC).',
                        ],
                        'application_id' => [
                            'type' => 'STRING',
                            'description' => 'ULID PosImpactGrantApplication bila action = approve_grant.',
                        ],
                        'review_notes' => [
                            'type' => 'STRING',
                            'description' => 'Catatan persetujuan kurasi Impact Grant.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'inspect_saas_system_health_and_billing_gate',
                'description' => 'Inspeksi real-time Super Admin SaaS System Health Pulse (Queue/Failed Jobs, Multi-AI LLM Quota Health, Conversion Funnel Retail/Mitra, Komisi Afiliasi, Disk VPS) dan cek status Feature Gate langganan Dual-Gateway IDR/USD (copy_phase_3_6 / mcp_bridge).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'feature' => [
                            'type' => 'STRING',
                            'description' => 'Nama fitur yang dicek pada Feature Gate (default copy_phase_3_6).',
                        ],
                        'project_ulid' => [
                            'type' => 'STRING',
                            'description' => 'Opsional ULID PosProject untuk pengecekan entitlement.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'generate_seo_geo_authority_pack',
                'description' => 'Kompilasi paket otoritas SEO (Multi-Graph Schema.org @graph 12 Sektor) & GEO (Generative Engine Optimization: 50-word Answer Capsule TL;DR, Comparison Table, /llms.txt, /llms-full.txt, Local 1200x630 OG Citation Card) atau picu IndexNow Instant Ping (ping_indexnow=true wajib izin mutasi RBAC).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'sector_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode sektor fungsional (SECTOR_01_COMMERCE_OMNICHANNEL s/d SECTOR_12_GOVTECH_NONPROFIT atau ALL).',
                        ],
                        'locale' => [
                            'type' => 'STRING',
                            'description' => 'Bahasa output Tier-1: id atau en.',
                        ],
                        'ping_indexnow' => [
                            'type' => 'BOOLEAN',
                            'description' => 'Jika true, kirim IndexNow instant ping untuk URL katalog /explore & /llms.txt (wajib RBAC).',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'audit_uu_pdp_blind_index_and_privacy',
                'description' => 'Audit kepatuhan UU PDP No. 27/2022 (`PRIMITIVE_UU_PDP_PRIVACY_VAULT`): uji komputasi deterministik HMAC-SHA256 Blind Index O(1) (`HasEncryptedPiiWithBlindIndex`), tinjau log permohonan Portabilitas/Redaksi PII (`PosPrivacyErasureRequest`), serta verifikasi kebijakan Ledger-Safe Right-to-Erasure.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'sample_identifier' => [
                            'type' => 'STRING',
                            'description' => 'Opsional contoh NIK/email/telepon untuk simulasi verifikasi hash 64-char HMAC-SHA256 Blind Index tanpa menyimpan plaintext.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'triage_post_bast_sla_maintenance_ticket',
                'description' => 'Inspeksi status Garansi Bug SLA 90-Hari Pasca-BAST Digital atau lakukan triase otomatis tiket laporan klien terhadap Kunci Scope SHA-256 v1.0 menjadi `BUG_WARRANTY_COVERED` (Rp 0 + Surgical Developer Bugfix Prompt) vs `NEW_SCOPE_PAID_DELTA` (terhubung ke Addendum Delta v1.1).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'action' => [
                            'type' => 'STRING',
                            'description' => 'Aksi: list_tickets (daftar tiket SLA terbaru) atau submit_ticket (triase & simpan tiket baru, wajib RBAC).',
                        ],
                        'project_ulid' => [
                            'type' => 'STRING',
                            'description' => 'Opsional ULID PosProject yang diaudit.',
                        ],
                        'issue_title' => [
                            'type' => 'STRING',
                            'description' => 'Judul laporan bug atau permintaan fitur baru bila action = submit_ticket.',
                        ],
                        'issue_description' => [
                            'type' => 'STRING',
                            'description' => 'Deskripsi detail kronologi error atau spesifikasi fitur baru.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'simulate_sandbox_webhook_and_holding_rollup',
                'description' => 'Jalankan laporan konsolidasi keuangan Multi-Entity Holding -> Anak Usaha -> Cabang beserta eliminasi transaksi antar-entitas (`action = holding_rollup`), atau jalankan Simulator Webhook Lokal Non-Production (`action = simulate_webhook`: qris_payment_success, payment_expired, duplicate_webhook_attack; diblokir di production).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'action' => [
                            'type' => 'STRING',
                            'description' => 'Aksi: holding_rollup atau simulate_webhook.',
                        ],
                        'workspace_ulid' => [
                            'type' => 'STRING',
                            'description' => 'Opsional ULID PosOrganizationWorkspace induk (Holding) untuk holding_rollup.',
                        ],
                        'scenario' => [
                            'type' => 'STRING',
                            'description' => 'Skenario simulator bila action = simulate_webhook: qris_payment_success, payment_expired, atau duplicate_webhook_attack.',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'evaluate_entitlement_and_scope_paralysis_guard',
                'description' => 'Evaluasi Progressive Disclosure Flow v12.0, Zero-Leak Server-Side Entitlement Gate (FREE_SANDBOX vs Paid Tiers), deteksi sektor otomatis dari ide teks bebas (`action = auto_detect_idea`), kalkulasi kredit konversi Retail ke Mitra Proyek (`action = calculate_rescue_credit`), serta status Scope Paralysis Guard & 3 Tahap Eksekusi Fondasi (`action = scope_paralysis_status`).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'action' => [
                            'type' => 'STRING',
                            'description' => 'Aksi: auto_detect_idea, evaluate_gate, calculate_rescue_credit, atau scope_paralysis_status.',
                        ],
                        'idea_text' => [
                            'type' => 'STRING',
                            'description' => 'Teks ide aplikasi bebas untuk action = auto_detect_idea.',
                        ],
                        'tier_code' => [
                            'type' => 'STRING',
                            'description' => 'Kode tier lisensi (FREE_SANDBOX, SINGLE_APP_PASS, PRO_RETAIL, AGENCY_PARTNER, MITRA_TURNKEY).',
                        ],
                        'phase_number' => [
                            'type' => 'INTEGER',
                            'description' => 'Nomor fase prompt (1..7) untuk evaluasi Zero-Leak Gate.',
                        ],
                        'mitra_quotation_idr' => [
                            'type' => 'INTEGER',
                            'description' => 'Nominal RAB Mitra Proyek IDR untuk simulasi potongan DP 100% dari biaya Retail.',
                        ],
                        'retail_paid_idr' => [
                            'type' => 'INTEGER',
                            'description' => 'Nominal paket Retail yang sudah dibayar klien (misal 299000 atau 399000).',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'audit_cv_pro_compliance_and_governance',
                'description' => 'Audit 4 Syarat Kepatuhan Hukum CV Pro Studio & Resume Generator: 1) UU PDP No. 27/2022 Anti-Data Brokering & Right to Erasure, 2) Disclaimer of Warranty atas kebenaran kualifikasi karir pengguna, 3) Lisensi Tipografi Bebas Royalti (SIL OFL: Inter, Roboto, Merriweather), dan 4) Kebijakan Produk Digital & Token AI Non-Refundable.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'action' => [
                            'type' => 'STRING',
                            'description' => 'Aksi: audit_compliance (skor kepatuhan 4 pilar), purge_user_cv_data (eksekusi Hak Penghapusan Data UU PDP), atau get_governance_report.',
                        ],
                        'user_id' => [
                            'type' => 'STRING',
                            'description' => 'Opsional ID pengguna untuk audit spesifik atau penghapusan data.',
                        ],
                        'resume_id' => [
                            'type' => 'STRING',
                            'description' => 'Opsional ULID resume yang diaudit atau dihapus.',
                        ],
                    ],
                ],
            ],

            // =========================================================================
            // 22. MODUL OPEN AGENT SKILLS ECOSYSTEM (VERCEL SKILLS HUB & FIND-SKILLS)
            // =========================================================================
            [
                'name' => 'manage_and_discover_agent_skills',
                'description' => 'Mencari, memeriksa, atau mengelola Open Agent Skills (Vercel Skills Hub, find-skills, dan ECC Universal v2.2.3 di folder .agents) serta menyajikan helper panduan fitur AI lengkap untuk klien.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'action' => [
                            'type' => 'STRING',
                            'description' => 'Tindakan: "list_installed" (daftar skill terpasang), "search" (cari skill), "inspect" (detail SKILL.md), "recommend" (rekomendasi skill stack), "get_install_command" (perintah CLI npx skills add), "ecc_status" (status ECC Universal v2.2.3 pada .agents), "list_subagents" (daftar 68 subagents ECC), atau "get_ai_features_helper" (katalog helper fitur AI untuk klien).',
                        ],
                        'query' => [
                            'type' => 'STRING',
                            'description' => 'Kata kunci pencarian nama atau deskripsi skill (opsional).',
                        ],
                        'skill_name' => [
                            'type' => 'STRING',
                            'description' => 'Nama atau slug skill yang ingin diperiksa (misal: "find-skills").',
                        ],
                        'stack_preset' => [
                            'type' => 'STRING',
                            'description' => 'Stack atau sektor proyek untuk rekomendasi (misal: "STACK_M1_LARAVEL_MONOLITH").',
                        ],
                    ],
                ],
            ],
        ];

        // Format sanitasi resmi Google Gemini Protobuf Map Schema
        // Mencegah konversi empty array [] menjadi JSON list yang memicu HTTP 400
        return array_map(function ($tool) {
            if (isset($tool['parameters'])) {
                if (! isset($tool['parameters']['properties']) || empty($tool['parameters']['properties'])) {
                    $tool['parameters']['properties'] = (object) [];
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

        if (! isset($normalized['type'])) {
            $normalized['type'] = 'object';
        }

        if (isset($normalized['properties']) && empty($normalized['properties'])) {
            $normalized['properties'] = (object) [];
        }

        return $normalized;
    }
}
