<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Project OS v6.0 Ultimate — The 5 Deterministic Guarantee Engines.
 *
 * Eliminates the 5 Fatal Failure Points of AI-Driven Software Engineering:
 * 1. Shallow Elaboration ("Toy App" Trap) -> L1–L4 Enterprise Menu & Capability Matrix (5 Divisions, 25+ Sub-Menus per Sector)
 * 2. Missing Corporate Glue -> The 5 "Enterprise Glue" Primitives (Atomic Doc Numbering, Fiscal Period Lock, Maker-Checker, Reason+Evidence, Idempotent Outbox)
 * 3. Cross-Part Signature Drift -> Exact Symbol Lock (SYMBOL_MANIFEST.json) synced to .agents/graphify/graph.json
 * 4. LLM Output Truncation & Broken FK Order -> DAG Topological Sub-Chunker (Kahn's Algorithm, max 5–6 entities per Part)
 * 5. Silent Compounding Bugs -> Hard Compiler Exit-Gate (<MANDATORY_VERIFICATION_GATE> with Exit Code 0)
 */
class PosEnterpriseGuaranteeCatalog
{
    public const RELEASE_VERSION = '6.0.0-deterministic-enterprise-guarantees';

    /**
     * Summary of the 5 Deterministic Guarantee Engines (Bilingual ID/EN).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function fiveDeterministicGuarantees(): array
    {
        return self::withDescAlias([
            [
                'number' => 1,
                'code' => 'GUARANTEE_01_L1_L4_ENTERPRISE_MENU_MATRIX',
                'badge' => 'ANTI-TOY-APP (25+ MENU)',
                'failure_eliminated' => 'Jebakan Aplikasi Mainan (Shallow 4-5 Menu CRUD)',
                'artifact' => '.agents/ENTERPRISE_MENU_MATRIX.md + 5 Divisi Operasional (25 Sub-Menu Terstruktur per Sektor)',
                'title' => [
                    'id' => 'L1–L4 Enterprise Menu & Capability Matrix (5 Divisi Operasional)',
                    'en' => 'L1–L4 Enterprise Menu & Capability Matrix (5 Operational Divisions)',
                ],
                'summary' => [
                    'id' => 'Begitu 1 dari 12 Sektor dipilih (bahkan dari prompt 5 kata seperti "Buat aplikasi klub badminton"), sistem otomatis memekarkan 5 Divisi Operasional (Front-Office, Core Ops, Inventory/Procurement, Finance/Accounting, Governance/AI) menjadi 25 sub-menu siap centang untuk rilis v1.0.',
                    'en' => 'Selecting any of the 12 Sectors immediately blooms a 5-Division Enterprise Menu Tree (Front-Office, Core Ops, Inventory/Procurement, Finance/Accounting, Governance/AI) with 25 concrete sub-menus selectable for v1.0.',
                ],
                'guarantee' => '100% Kedalaman Kelas ERP Korporat Sejak Detik Pertama',
            ],
            [
                'number' => 2,
                'code' => 'GUARANTEE_02_FIVE_ENTERPRISE_GLUE_PRIMITIVES',
                'badge' => 'SAP/ODOO GLUE (5 MODUL)',
                'failure_eliminated' => 'Absennya Kontrol Operasional Korporat & Audit Internal',
                'artifact' => 'sys_document_sequences, sys_accounting_periods, sys_approval_requests, sys_reason_evidences, sys_integration_logs',
                'title' => [
                    'id' => 'The 5 "Enterprise Glue" Primitives (Perekat Sistem Kelas Korporat)',
                    'en' => 'The 5 "Enterprise Glue" Primitives (Corporate ERP Glue Layer)',
                ],
                'summary' => [
                    'id' => 'Otomatis menyuntikkan 5 tabel & guard korporat ke semua sektor: (1) Penomoran Dokumen Atomik ber-lock, (2) Kunci Tutup Buku Fiskal anti-backdate, (3) Maker-Checker Dual Control (>15% diskon/void/payout), (4) Reason Code + Bukti Private Disk, dan (5) Idempotent Webhook & Outbox Replay Vault.',
                    'en' => 'Auto-injects 5 corporate ERP glue modules into every sector: Atomic Row-Locked Document Numbering, Fiscal Period Cut-Off Guard, Maker-Checker Dual Control, Reason Code + Private Evidence Attachment, and 1-Click Replayable Integration Outbox.',
                ],
                'guarantee' => 'Standar Kepatuhan Audit Setara SAP / Odoo Enterprise',
            ],
            [
                'number' => 3,
                'code' => 'GUARANTEE_03_EXACT_SYMBOL_MANIFEST_LOCK',
                'badge' => 'ANTI-AMNESIA SYMBOL LOCK',
                'failure_eliminated' => 'Cross-Part Signature Drift (Undefined method / Column not found)',
                'artifact' => '.agents/SYMBOL_MANIFEST.json + Sinkronisasi .agents/graphify/graph.json',
                'title' => [
                    'id' => 'Exact Symbol Lock (SYMBOL_MANIFEST.json) — Anti-Amnesia Lintas Part',
                    'en' => 'Exact Symbol Lock (SYMBOL_MANIFEST.json) — Zero Cross-Part Drift',
                ],
                'summary' => [
                    'id' => 'Membekukan kamus nama FQCN Model, Enum Status, kolom eksak ULID, relasi BelongsTo/HasMany, dan signature __invoke() Action + DTO sebelum Part 1.0 dimulai, lalu menyematkannya di header setiap Part agar Part 5 (UI) memanggil Part 3 (Actions) dengan akurasi 100% tanpa baca ulang file.',
                    'en' => 'Freezes exact Model FQCNs, Status Enums, ULID columns, Eloquent relations, and Action __invoke() + DTO signatures into SYMBOL_MANIFEST.json embedded across every prompt part.',
                ],
                'guarantee' => 'Zero Undefined Method / Column Mismatch Lintas Sesi AI',
            ],
            [
                'number' => 4,
                'code' => 'GUARANTEE_04_DAG_TOPOLOGICAL_SUB_CHUNKER',
                'badge' => 'KAHN DAG + ANTI-TRUNCATION',
                'failure_eliminated' => 'LLM Output Truncation & Broken PostgreSQL Foreign-Key Order',
                'artifact' => 'TopologicalPromptChunker (Part 1.0 -> Part 2.1..2.N -> Part 3.1..3.M -> Part 4.0 -> Part 5.1..5.K -> Part 6.0 -> Part 7.0)',
                'title' => [
                    'id' => 'DAG Topological Sub-Chunker (Kahn\'s Algorithm & Max 5–6 Entitas/Part)',
                    'en' => 'DAG Topological Sub-Chunker (Kahn\'s Algorithm & Token-Safe Parts)',
                ],
                'summary' => [
                    'id' => 'Mengurutkan seluruh tabel berdasarkan ketergantungan foreignUlid (Layer 0 Master -> Layer 1 Katalog -> Layer 2 Transaksi -> Layer 3 Jurnal/Log) dan memecah otomatis setiap fase besar menjadi sub-part berkapasitas maks 5–6 entitas (~1.500 baris kode) bebas kode terpotong.',
                    'en' => 'Topologically sorts all tables via Kahn\'s Algorithm by foreignUlid dependencies (Layer 0 -> Layer 3+) and splits large phases into token-safe sub-parts of max 5–6 entities.',
                ],
                'guarantee' => 'Zero Migration FK Crash & Zero Terpotong Batas Token LLM',
            ],
            [
                'number' => 5,
                'code' => 'GUARANTEE_05_HARD_COMPILER_EXIT_GATE',
                'badge' => 'EXIT CODE 0 GATEKEEPER',
                'failure_eliminated' => 'Silent Compounding Bugs Lintas Tahap Eksekusi',
                'artifact' => '<MANDATORY_VERIFICATION_GATE> (migrate --pretend + PHPStan + Filtered Pest + Graphify Receipt)',
                'title' => [
                    'id' => 'Hard Compiler Exit-Gate (<MANDATORY_VERIFICATION_GATE>) di Akhir Setiap Part',
                    'en' => 'Hard Compiler Exit-Gate (<MANDATORY_VERIFICATION_GATE>) per Sub-Part',
                ],
                'summary' => [
                    'id' => 'Setiap potongan Part ditutup dengan gerbang kompilator wajib (migrate --pretend, PHPStan level tinggi terhadap SYMBOL_MANIFEST.json, Pest test spesifik Part, dan cetak bukti [PART X.Y VERIFIED — ZERO ERRORS]) sebelum developer boleh lanjut ke Part berikutnya.',
                    'en' => 'Every sub-part ends with a non-negotiable terminal gate enforcing migrate --pretend, PHPStan symbol check, targeted Pest test, and [PART X.Y VERIFIED — ZERO ERRORS] receipt.',
                ],
                'guarantee' => 'Zero Akumulasi Bug Diam-Diam dari Part 1 sampai Part 7',
            ],
        ]);
    }

    /**
     * Normalized helper ensuring `desc` alias is present alongside `summary`.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private static function withDescAlias(array $items): array
    {
        return array_values(array_map(static function (array $item): array {
            $item['desc'] = $item['desc'] ?? ($item['summary'] ?? ['id' => '', 'en' => '']);

            return $item;
        }, $items));
    }

    /**
     * JAMINAN 2: The 5 Corporate "Enterprise Glue" Primitives (SAP/Odoo-Grade Operational Controls).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function fiveEnterpriseGluePrimitives(): array
    {
        $items = [
            [
                'number' => 1,
                'code' => 'GLUE_01_ATOMIC_DOC_NUMBERING',
                'table' => 'sys_document_sequences',
                'model_fqcn' => 'App\\Models\\SysDocumentSequence',
                'action_fqcn' => 'App\\Actions\\System\\GenerateAtomicDocumentNumberAction',
                'action_signature' => 'public function __invoke(string $organizationUlid, string $branchCode, string $docPrefix, ?\\Carbon\\CarbonInterface $issuedAt = null): string',
                'title' => [
                    'id' => 'Atomic Document Numbering Engine (sys_document_sequences)',
                    'en' => 'Atomic Document Numbering Engine (sys_document_sequences)',
                ],
                'rule' => 'Penomoran dokumen resmi (INV/{CABANG}/{YYYY}/{MM}/{00001}, PO/..., BKG/..., GRN/...) WAJIB menggunakan row-level lock SELECT ... FOR UPDATE per organisasi/cabang/tahun/bulan. DILARANG KERAS memakai rand() atau Str::random() untuk nomor faktur/dokumen bisnis.',
            ],
            [
                'number' => 2,
                'code' => 'GLUE_02_FISCAL_PERIOD_LOCK',
                'table' => 'sys_accounting_periods',
                'model_fqcn' => 'App\\Models\\SysAccountingPeriod',
                'action_fqcn' => 'App\\Actions\\Finance\\AssertFiscalPeriodOpenAction',
                'action_signature' => 'public function __invoke(string $organizationUlid, string $transactionDateYmd, bool $isReversingAdjustment = false): void',
                'title' => [
                    'id' => 'Fiscal Period Lock & Cut-Off Guard (sys_accounting_periods)',
                    'en' => 'Fiscal Period Lock & Cut-Off Guard (sys_accounting_periods)',
                ],
                'rule' => 'Mencegah staf menyisipkan (backdate), mengubah, atau menghapus transaksi pada periode bulan yang sudah berstatus CLOSED_FINAL, kecuali melalui Jurnal Balik/Penyesuaian (Reversing Entry) di periode berjalan yang terbuka.',
            ],
            [
                'number' => 3,
                'code' => 'GLUE_03_MAKER_CHECKER_CONTROL',
                'table' => 'sys_approval_requests',
                'model_fqcn' => 'App\\Models\\SysApprovalRequest',
                'action_fqcn' => 'App\\Actions\\Governance\\SubmitOrApproveMakerCheckerAction',
                'action_signature' => 'public function __invoke(App\\Data\\MakerCheckerRequestData $data): App\\Models\\SysApprovalRequest',
                'title' => [
                    'id' => 'Maker-Checker Dual Control (sys_approval_requests)',
                    'en' => 'Maker-Checker Dual Control (sys_approval_requests)',
                ],
                'rule' => 'Setiap mutasi sensitif (Diskon manual > 15%, Void/Hapus Transaksi, Pencairan Dana/Refund, Stock Opname Minus) dilarang langsung memutasi saldo; wajib masuk status PENDING_SUPERVISOR_APPROVAL dengan validasi tegas maker_id !== checker_id atau PIN Otorisasi Supervisor.',
            ],
            [
                'number' => 4,
                'code' => 'GLUE_04_REASON_EVIDENCE_VAULT',
                'table' => 'sys_reason_evidences',
                'model_fqcn' => 'App\\Models\\SysReasonEvidence',
                'action_fqcn' => 'App\\Actions\\Governance\\RecordMandatoryReasonEvidenceAction',
                'action_signature' => 'public function __invoke(App\\Data\\ReasonEvidenceData $data): App\\Models\\SysReasonEvidence',
                'title' => [
                    'id' => 'Reason Code & Mandatory Private Evidence Vault (sys_reason_evidences)',
                    'en' => 'Reason Code & Mandatory Private Evidence Vault (sys_reason_evidences)',
                ],
                'rule' => 'Setiap aksi pembatalan (CANCELLED), retur (REFUNDED), void, atau penyesuaian stok wajib memilih reason_code terstandar dan mengunggah lampiran bukti sah ke storage disk = "private" (diakses via temporarySignedRoute 5 menit).',
            ],
            [
                'number' => 5,
                'code' => 'GLUE_05_IDEMPOTENT_OUTBOX_VAULT',
                'table' => 'sys_integration_logs',
                'model_fqcn' => 'App\\Models\\SysIntegrationLog',
                'action_fqcn' => 'App\\Actions\\Integration\\ReplayIdempotentIntegrationAction',
                'action_signature' => 'public function __invoke(string $integrationLogUlid, string $actorUlid): App\\Models\\SysIntegrationLog',
                'title' => [
                    'id' => 'Idempotent Webhook & External Outbox Vault (sys_integration_logs)',
                    'en' => 'Idempotent Webhook & External Outbox Vault (sys_integration_logs)',
                ],
                'rule' => 'Mencatat seluruh trafik keluar/masuk Payment Gateway, WhatsApp, Email, dan API Pajak secara idempoten (idempotency_key unik) lengkap dengan fitur 1-Click Admin Replay saat terjadi gangguan jaringan pihak ketiga.',
            ],
        ];

        return array_values(array_map(static function (array $item): array {
            $item['name'] = $item['name'] ?? ($item['title'] ?? ['id' => $item['code'], 'en' => $item['code']]);
            $item['table_name'] = $item['table_name'] ?? ($item['table'] ?? '');
            $item['invariant'] = $item['invariant'] ?? ($item['rule'] ?? '');

            return $item;
        }, $items));
    }

    /**
     * Strict ULID Database Table Blueprints for the 5 Enterprise Glue Primitives.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function enterpriseGlueTables(): array
    {
        return [
            [
                'table' => 'sys_document_sequences',
                'tenant_scoped' => true,
                'soft_deletes' => false,
                'source_module' => 'ENTERPRISE_GLUE_01_DOC_SEQUENCE',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'primary' => true],
                    ['name' => 'organization_id', 'type' => 'foreignUlid', 'references' => 'organizations.id', 'indexed' => true],
                    ['name' => 'branch_code', 'type' => 'string(24)', 'default' => 'HQ'],
                    ['name' => 'doc_prefix', 'type' => 'string(16)', 'indexed' => true],
                    ['name' => 'period_year', 'type' => 'smallInteger'],
                    ['name' => 'period_month', 'type' => 'tinyInteger'],
                    ['name' => 'current_number', 'type' => 'unsignedBigInteger', 'default' => 0],
                    ['name' => 'padding_length', 'type' => 'tinyInteger', 'default' => 5],
                    ['name' => 'format_pattern', 'type' => 'string(80)', 'default' => '{PREFIX}/{BRANCH}/{YYYY}/{MM}/{SEQ}'],
                    ['name' => 'timestamps', 'type' => 'timestampsTz'],
                ],
                'indexes' => [
                    'UNIQUE(organization_id, branch_code, doc_prefix, period_year, period_month)',
                ],
            ],
            [
                'table' => 'sys_accounting_periods',
                'tenant_scoped' => true,
                'soft_deletes' => false,
                'source_module' => 'ENTERPRISE_GLUE_02_FISCAL_LOCK',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'primary' => true],
                    ['name' => 'organization_id', 'type' => 'foreignUlid', 'references' => 'organizations.id', 'indexed' => true],
                    ['name' => 'period_code', 'type' => 'string(7)', 'comment' => 'YYYY-MM'],
                    ['name' => 'start_date', 'type' => 'date'],
                    ['name' => 'end_date', 'type' => 'date'],
                    ['name' => 'status', 'type' => 'string(24)', 'default' => 'OPEN', 'comment' => 'OPEN|SOFT_LOCKED|CLOSED_FINAL'],
                    ['name' => 'closed_by', 'type' => 'foreignUlid', 'references' => 'users.id', 'nullable' => true],
                    ['name' => 'closed_at', 'type' => 'timestampTz', 'nullable' => true],
                    ['name' => 'closing_checksum_sha256', 'type' => 'string(64)', 'nullable' => true],
                    ['name' => 'timestamps', 'type' => 'timestampsTz'],
                ],
                'indexes' => [
                    'UNIQUE(organization_id, period_code)',
                ],
            ],
            [
                'table' => 'sys_approval_requests',
                'tenant_scoped' => true,
                'soft_deletes' => false,
                'source_module' => 'ENTERPRISE_GLUE_03_MAKER_CHECKER',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'primary' => true],
                    ['name' => 'organization_id', 'type' => 'foreignUlid', 'references' => 'organizations.id', 'indexed' => true],
                    ['name' => 'action_code', 'type' => 'string(64)', 'indexed' => true],
                    ['name' => 'subject_type', 'type' => 'string(120)'],
                    ['name' => 'subject_ulid', 'type' => 'ulid', 'indexed' => true],
                    ['name' => 'threshold_reason', 'type' => 'string(255)'],
                    ['name' => 'payload_snapshot', 'type' => 'jsonb'],
                    ['name' => 'status', 'type' => 'string(36)', 'default' => 'PENDING_SUPERVISOR_APPROVAL', 'indexed' => true],
                    ['name' => 'maker_id', 'type' => 'foreignUlid', 'references' => 'users.id'],
                    ['name' => 'checker_id', 'type' => 'foreignUlid', 'references' => 'users.id', 'nullable' => true],
                    ['name' => 'supervisor_pin_verified', 'type' => 'boolean', 'default' => false],
                    ['name' => 'decided_at', 'type' => 'timestampTz', 'nullable' => true],
                    ['name' => 'decision_note', 'type' => 'text', 'nullable' => true],
                    ['name' => 'timestamps', 'type' => 'timestampsTz'],
                ],
                'indexes' => [
                    'INDEX(organization_id, status, id)',
                ],
            ],
            [
                'table' => 'sys_reason_evidences',
                'tenant_scoped' => true,
                'soft_deletes' => false,
                'source_module' => 'ENTERPRISE_GLUE_04_REASON_EVIDENCE',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'primary' => true],
                    ['name' => 'organization_id', 'type' => 'foreignUlid', 'references' => 'organizations.id', 'indexed' => true],
                    ['name' => 'mutation_type', 'type' => 'string(32)', 'comment' => 'CANCELLED|REFUNDED|VOIDED|STOCK_ADJUSTED'],
                    ['name' => 'subject_type', 'type' => 'string(120)'],
                    ['name' => 'subject_ulid', 'type' => 'ulid', 'indexed' => true],
                    ['name' => 'reason_code', 'type' => 'string(64)', 'indexed' => true],
                    ['name' => 'explanation_note', 'type' => 'text'],
                    ['name' => 'private_evidence_disk', 'type' => 'string(24)', 'default' => 'private'],
                    ['name' => 'private_evidence_path', 'type' => 'string(255)'],
                    ['name' => 'evidence_sha256', 'type' => 'string(64)'],
                    ['name' => 'recorded_by', 'type' => 'foreignUlid', 'references' => 'users.id'],
                    ['name' => 'timestamps', 'type' => 'timestampsTz'],
                ],
                'indexes' => [
                    'INDEX(organization_id, subject_ulid, mutation_type)',
                ],
            ],
            [
                'table' => 'sys_integration_logs',
                'tenant_scoped' => true,
                'soft_deletes' => false,
                'source_module' => 'ENTERPRISE_GLUE_05_OUTBOX_VAULT',
                'columns' => [
                    ['name' => 'id', 'type' => 'ulid', 'primary' => true],
                    ['name' => 'organization_id', 'type' => 'foreignUlid', 'references' => 'organizations.id', 'nullable' => true, 'indexed' => true],
                    ['name' => 'direction', 'type' => 'string(24)', 'comment' => 'INBOUND_WEBHOOK|OUTBOUND_DISPATCH'],
                    ['name' => 'channel', 'type' => 'string(32)', 'comment' => 'PAYMENT_GATEWAY|WHATSAPP|EMAIL|TAX_API'],
                    ['name' => 'idempotency_key', 'type' => 'string(120)', 'unique' => true],
                    ['name' => 'endpoint_or_event', 'type' => 'string(190)'],
                    ['name' => 'request_payload', 'type' => 'jsonb'],
                    ['name' => 'response_payload', 'type' => 'jsonb', 'nullable' => true],
                    ['name' => 'http_status', 'type' => 'smallInteger', 'nullable' => true],
                    ['name' => 'status', 'type' => 'string(28)', 'default' => 'PENDING', 'indexed' => true],
                    ['name' => 'retry_count', 'type' => 'unsignedSmallInteger', 'default' => 0],
                    ['name' => 'last_replayed_by', 'type' => 'foreignUlid', 'references' => 'users.id', 'nullable' => true],
                    ['name' => 'last_replayed_at', 'type' => 'timestampTz', 'nullable' => true],
                    ['name' => 'timestamps', 'type' => 'timestampsTz'],
                ],
                'indexes' => [
                    'UNIQUE(idempotency_key)',
                    'INDEX(organization_id, channel, status, id)',
                ],
            ],
        ];
    }

    /**
     * JAMINAN 1: L1–L4 Enterprise Menu & Capability Matrix across 5 Operational Divisions for all 12 Sectors.
     * Expands every sector automatically into 25 concrete sub-menus (5 Divisions x 5 L3 Operational Screens).
     *
     * @param  array<int, string>|null  $selectedMenuCodes
     * @return array<string, mixed>
     */
    public static function enterpriseMenuMatrixForSector(string $sectorCode, ?array $selectedMenuCodes = null): array
    {
        $normalized = PosSectorUiArchetypeCatalog::normalizeSectorCode($sectorCode);
        $sectorMenus = self::sectorSpecificDivisionsMap()[$normalized]
            ?? self::sectorSpecificDivisionsMap()['SECTOR_01_COMMERCE_OMNICHANNEL'];

        $divisionHeaders = [
            'DIV_01_FRONT_OFFICE' => [
                'number' => 1,
                'code' => 'DIV_01_FRONT_OFFICE',
                'name' => [
                    'id' => 'Divisi 1: Front-Office & Customer Touchpoint (Operasional Garda Depan)',
                    'en' => 'Division 1: Front-Office & Customer Touchpoint',
                ],
                'default_capabilities' => ['CREATE_ATOMIC_LOCK', 'BARCODE_QR_SCAN', 'PRINT_THERMAL_80MM', 'INSTANT_SEARCH_CURSOR'],
            ],
            'DIV_02_CORE_OPERATIONS' => [
                'number' => 2,
                'code' => 'DIV_02_CORE_OPERATIONS',
                'name' => [
                    'id' => 'Divisi 2: Core Operations & Fulfillment (Mesin Produksi / Layanan Inti)',
                    'en' => 'Division 2: Core Operations & Fulfillment',
                ],
                'default_capabilities' => ['STATE_TRANSITION_GUARD', 'BATCH_ACTION_BAR', 'SLA_TIMER_ALERT', 'REALTIME_BOARD_SYNC'],
            ],
            'DIV_03_INVENTORY_SUPPLY_CHAIN' => [
                'number' => 3,
                'code' => 'DIV_03_INVENTORY_SUPPLY_CHAIN',
                'name' => [
                    'id' => 'Divisi 3: Inventory, Procurement & Asset Control (Rantai Pasok & Aset)',
                    'en' => 'Division 3: Inventory, Procurement & Asset Control',
                ],
                'default_capabilities' => ['PR_PO_GRN_CHAIN', 'FEFO_FIFO_ROW_LOCK', 'MAKER_CHECKER_OPNAME', 'ASSET_MAINTENANCE_ALERT'],
            ],
            'DIV_04_FINANCE_ACCOUNTING' => [
                'number' => 4,
                'code' => 'DIV_04_FINANCE_ACCOUNTING',
                'name' => [
                    'id' => 'Divisi 4: Back-Office, Finance & Accounting (Kontrol Keuangan & Pajak)',
                    'en' => 'Division 4: Back-Office, Finance & Accounting',
                ],
                'default_capabilities' => ['IMMUTABLE_DOUBLE_ENTRY', 'FISCAL_PERIOD_CUTOFF', 'AR_AP_AGING_EXPORT', 'OFFICIAL_A4_TAX_PDF'],
            ],
            'DIV_05_GOVERNANCE_INTELLIGENCE' => [
                'number' => 5,
                'code' => 'DIV_05_GOVERNANCE_INTELLIGENCE',
                'name' => [
                    'id' => 'Divisi 5: Master Data, Governance & Executive Intelligence (Fondasi & AI)',
                    'en' => 'Division 5: Master Data, Governance & Executive Intelligence',
                ],
                'default_capabilities' => ['MULTI_BRANCH_TENANCY', 'MAKER_CHECKER_PIN', 'SHA256_AUDIT_EXPLORER', 'PGVECTOR_AI_COPILOT'],
            ],
        ];

        $hasExplicitFilter = is_array($selectedMenuCodes) && count($selectedMenuCodes) > 0;
        $selectedLookup = $hasExplicitFilter ? array_flip(array_map('strval', $selectedMenuCodes)) : [];

        $divisionsOutput = [];
        $allMenusFlat = [];
        $includedV1Count = 0;

        $secPrefix = substr($normalized, 0, 9); // e.g. SECTOR_02

        foreach ($divisionHeaders as $divKey => $divMeta) {
            $rawItems = $sectorMenus[$divKey] ?? [];
            $itemsFormatted = [];

            foreach ($rawItems as $idx => $itemTitle) {
                $menuCode = sprintf('%s_D%d_M%02d', $secPrefix, $divMeta['number'], $idx + 1);
                $includedInV1 = $hasExplicitFilter ? isset($selectedLookup[$menuCode]) : true;
                if ($includedInV1) {
                    $includedV1Count++;
                }

                $entry = [
                    'code' => $menuCode,
                    'division_code' => $divKey,
                    'division_number' => $divMeta['number'],
                    'sort_order' => $idx + 1,
                    'title' => $itemTitle,
                    'route_slug' => '/app/' . strtolower(str_replace('_', '-', $divKey)) . '/' . strtolower($menuCode),
                    'l4_capabilities' => $divMeta['default_capabilities'],
                    'included_in_v1' => $includedInV1,
                    'target_release' => $includedInV1 ? 'v1.0' : 'v1.1-backlog',
                ];
                $itemsFormatted[] = $entry;
                $allMenusFlat[] = $entry;
            }

            $divisionsOutput[] = [
                'division_number' => $divMeta['number'],
                'division_code' => $divKey,
                'division_name' => $divMeta['name'],
                'menus_count' => count($itemsFormatted),
                'menus' => $itemsFormatted,
            ];
        }

        $totalCount = count($allMenusFlat);
        $deferredCount = max(0, $totalCount - $includedV1Count);

        return [
            'sector_code' => $normalized,
            'total_divisions' => 5,
            'total_enterprise_menus' => $totalCount,
            'total_menus' => $totalCount,
            'total_sub_menus' => $totalCount,
            'included_v1_menus_count' => $includedV1Count,
            'included_v1_count' => $includedV1Count,
            'v1_selected_count' => $includedV1Count,
            'deferred_v1_1_menus_count' => $deferredCount,
            'v1_1_backlog_count' => $deferredCount,
            'anti_shallow_guarantee' => 'Zero Toy CRUD — 5 Operational Divisions & 25 Concrete L1–L4 Sub-Menus Automatically Provisioned',
            'divisions' => $divisionsOutput,
            'flat_menus' => $allMenusFlat,
        ];
    }

    /**
     * Domain-Specific 5-Division x 5-Menu Trees for all 12 Functional Sectors (300 Curated Sub-Menus).
     *
     * @return array<string, array<string, array<int, string>>>
     */
    protected static function sectorSpecificDivisionsMap(): array
    {
        $commonInventory = [
            'Purchase Requisition (PR) -> Purchase Order (PO) -> Goods Receipt Note (GRN)',
            'Kartu Stok Multi-Gudang & Reservasi Batch FEFO/FIFO (Row-Level Lock)',
            'Stock Opname Berkala & Koreksi Selisih Berbasis Maker-Checker',
            'Transfer Stok Antar-Cabang (In-Transit Waybill & Penerimaan)',
            'Master Aset Operasional, Jadwal Kalibrasi & Servis Preventif',
        ];

        $commonFinance = [
            'Buku Besar Immutable (Double-Entry Ledger) & Jurnal Balik Periode',
            'Kasir, Petty Cash, Mutasi Bank & Rekonsiliasi QRIS/VA Otomatis',
            'Jadwal Umur Piutang (AR Aging) & Hutang Supplier (AP Aging)',
            'Kalkulasi Komisi Staf, Bagi Hasil Mitra & Periode Tutup Buku Fiskal',
            'Faktur Pajak (PPN/PB1/PPh) & Laporan Laba Rugi / Neraca Real-Time',
        ];

        $commonGovernance = [
            'Master Pricebook Bertingkat, Voucher Promo & Aturan Diskon match()',
            'Manajemen Organisasi Multi-Cabang & Isolasi Tenant (BelongsToOrganization)',
            'Matriks RBAC, Antrean Persetujuan Maker-Checker & PIN Otorisasi Supervisor',
            'Penjelajah Jejak Audit SHA-256, Bukti Private Disk & Outbox Webhook Replay',
            'Executive BI Cockpit & AI Copilot Eksekutif (pgvector RAG + Native Tools)',
        ];

        return [
            'SECTOR_01_COMMERCE_OMNICHANNEL' => [
                'DIV_01_FRONT_OFFICE' => [
                    'Terminal Kasir POS Split-Screen (Barcode Scanner, Hold Bill & Split Tender)',
                    'Katalog E-Commerce B2C / Portal Grosir B2B Tiered Pricing',
                    'Omnichannel Order Inbox (Sinkronisasi Pesanan Toko, Web & Social Commerce)',
                    'Meja Retur, Tukar Barang (Exchange) & Klaim Garansi Pelanggan',
                    'Member Lookup, Katalog Poin Loyalitas & Penukaran Voucher Kasir',
                ],
                'DIV_02_CORE_OPERATIONS' => [
                    'Wave Picking, Packing Station & Cetak Label Pengiriman Massal',
                    'Manajemen Matriks Varian SKU (Ukuran, Warna, Bundling/Hampers)',
                    'Manajemen Shift Kasir (Open Register, Cash Drop & Blind Closing)',
                    'Penanganan Pesanan Pre-Order, Backorder & Alokasi Stok Prioritas',
                    'Rekonsiliasi COD, Penyelesaian Komplain Kurir & Bukti Serah Terima',
                ],
                'DIV_03_INVENTORY_SUPPLY_CHAIN' => $commonInventory,
                'DIV_04_FINANCE_ACCOUNTING' => $commonFinance,
                'DIV_05_GOVERNANCE_INTELLIGENCE' => $commonGovernance,
            ],
            'SECTOR_02_HOSPITALITY_SPACE' => [
                'DIV_01_FRONT_OFFICE' => [
                    'Kalender Gantt Reservasi Unit/Lapangan/Kamar Real-Time (Anti Double-Booking)',
                    'Meja Front-Desk Check-In / Check-Out & QR Pass Gate Scanner',
                    'POS Pro-Shop, F&B Lounge & Persewaan Alat/Perlengkapan Tambahan',
                    'Manajemen Waiting List, Walk-In Queue & Deposit Jaminan Tamu',
                    'Portal Booking Mandiri Member, Paket Jam Main & Reschedule Terukur',
                ],
                'DIV_02_CORE_OPERATIONS' => [
                    'Manajemen Turnamen, Bracket Pertandingan, Skor Live & Jadwal Sparring',
                    'Penjadwalan Pelatih (Coach), Kelas Akademi/Cohort & Presensi Sesi',
                    'Board Housekeeping & Status Unit (Dirty, Inspecting, Clean, Maintenance)',
                    'Mesin Tarif Dinamis (Peak Hour, Weekend, Holiday & Member Tier Pricing)',
                    'Log Kerusakan Fasilitas, Work Order Teknisi & Pemblokiran Slot Darurat',
                ],
                'DIV_03_INVENTORY_SUPPLY_CHAIN' => $commonInventory,
                'DIV_04_FINANCE_ACCOUNTING' => $commonFinance,
                'DIV_05_GOVERNANCE_INTELLIGENCE' => $commonGovernance,
            ],
            'SECTOR_03_HEALTHCARE_CLINICAL' => [
                'DIV_01_FRONT_OFFICE' => [
                    'Admisi Pasien Baru/Lama, Verifikasi KTP/Asuransi & Antrean Poliklinik',
                    'Layar Triage IGD, Tanda Vital Perawat & Prioritas Kegawatan',
                    'Clinical 3-Pane Cockpit Dokter (Rekam Medis SOAP, ICD-10 & Riwayat Alergi)',
                    'Kasir Rawat Jalan/Inap, Split Tagihan Pribadi vs Penjamin Asuransi/BPJS',
                    'Portal Janji Temu Dokter Online, Telemedicine & Pengingat Kontrol WA',
                ],
                'DIV_02_CORE_OPERATIONS' => [
                    'Instalasi Farmasi: E-Resep Dokter, Telaah Interaksi Obat & Dispensing FEFO',
                    'Order & Hasil Laboratorium / Radiologi (Nilai Rujukan & Flag Kritis)',
                    'Penjadwalan Ruang Tindakan / Bedah, Ketersediaan Bed & Visite',
                    'Manajemen Klaim Penjamin / BPJS Bridging, Berkas Resume Medis & Verifikasi',
                    'Informed Consent Digital, Surat Keterangan Medis & Rujukan Eksternal',
                ],
                'DIV_03_INVENTORY_SUPPLY_CHAIN' => [
                    'PR -> PO -> GRN Obat & Alat Kesehatan (Validasi Nomor Batch & NIE)',
                    'Kartu Stok Apotek & Gudang Medis FEFO Ketat + Peringatan Obat Kadaluarsa',
                    'Log Penyimpanan Cold-Chain (Suhu Vaksin/Reagen) & Karantina Obat Rusak',
                    'Stock Opname Narkotika/Psikotropika Berbasis Maker-Checker Ketat',
                    'Jadwal Kalibrasi Alat Medis & Pemeliharaan Elektromedis',
                ],
                'DIV_04_FINANCE_ACCOUNTING' => $commonFinance,
                'DIV_05_GOVERNANCE_INTELLIGENCE' => $commonGovernance,
            ],
            'SECTOR_04_FINTECH_LEDGER' => [
                'DIV_01_FRONT_OFFICE' => [
                    'Onboarding Nasabah/Merchant, e-KYC Dokumen Private Disk & Scoring',
                    'Cockpit Pembukaan Rekening Simpanan / Dompet Multi-Mata-Uang',
                    'Origination Pengajuan Pembiayaan/Pinjaman & Simulasi Jadwal Angsuran',
                    'Loket Setor/Tarik/Transfer & Generator Virtual Account / QRIS Dinamis',
                    'Meja Layanan Sengketa Transaksi (Chargeback/Dispute) & Bukti Investigasi',
                ],
                'DIV_02_CORE_OPERATIONS' => [
                    'Mesin Clearing & Settlement Otomatis (Escrow Hold -> Split Disbursement)',
                    'Manajemen Pencairan Kredit/Pembiayaan, Agunan & Kolektibilitas (DPD)',
                    'Mesin Akrual Bunga/Bagi Hasil Harian & Penagihan Terjadwal',
                    'Antrean Kepatuhan AML / Fraud Velocity Alert & Pembekuan Akun Sementara',
                    'Rekonsiliasi Mutasi Bank H+0 Otomatis vs Buku Besar Internal',
                ],
                'DIV_03_INVENTORY_SUPPLY_CHAIN' => $commonInventory,
                'DIV_04_FINANCE_ACCOUNTING' => $commonFinance,
                'DIV_05_GOVERNANCE_INTELLIGENCE' => $commonGovernance,
            ],
            'SECTOR_05_INDUSTRIAL_MRP' => [
                'DIV_01_FRONT_OFFICE' => [
                    'Quotation Proyek B2B / Kontrak EPC, RAB Teknis & Sales Order',
                    'Portal Klien Pelacakan Progres Produksi / Proyek & Berita Acara Termin',
                    'Penerimaan Spesifikasi Custom Engineering, Gambar Kerja & Revisi Desain',
                    'Jadwal Pengiriman Barang Jadi (Delivery Order) & Surat Jalan Pabrik',
                    'Penanganan Klaim Retur Kualitas (RMA) & Garansi Purna Jual',
                ],
                'DIV_02_CORE_OPERATIONS' => [
                    'Master Bill of Materials (BoM) Multi-Level, Routing Mesin & Work Center',
                    'MRP Material Requirement Planning & Konversi Otomatis ke PR Pembelian',
                    'Eksekusi Work Order (WO) Lantai Pabrik, Pelacakan WIP & Jam Mesin',
                    'Inspeksi Quality Control (QC Pass/Reject/Rework) & Pencatatan Scrap/Yield',
                    'Kalkulasi HPP (COGS) Standar vs Aktual & Termin Progress Billing Proyek',
                ],
                'DIV_03_INVENTORY_SUPPLY_CHAIN' => $commonInventory,
                'DIV_04_FINANCE_ACCOUNTING' => $commonFinance,
                'DIV_05_GOVERNANCE_INTELLIGENCE' => $commonGovernance,
            ],
            'SECTOR_06_LOGISTICS_FLEET' => [
                'DIV_01_FRONT_OFFICE' => [
                    'Loket Booking Resi / AWB, Kalkulator Berat Volumetrik & Cetak Label Barcode',
                    'Portal Pelacakan Pengiriman Real-Time, Estimasi Tarif & Klaim Asuransi Kargo',
                    'Penjadwalan Jemput Paket (First-Mile Pickup) & Penugasan Kurir',
                    'Konfirmasi Bukti Kirim Elektronik (e-PoD Foto + Tanda Tangan + Geo-Tag)',
                    'Rekonsiliasi Setoran Tunai COD Kurir & Penyelesaian Paket Gagal Antar (NDR)',
                ],
                'DIV_02_CORE_OPERATIONS' => [
                    'Dispatch Control Tower: Konsolidasi Manifes Hub, Bagging & Linehaul Antar-Kota',
                    'Optimasi Rute Last-Mile, Geofencing Waypoint & Pemantauan Posisi Armada',
                    'Manajemen Surat Jalan Supir, Uang Jalan (Per Diem) & Klaim Tol/BBM',
                    'Penjadwalan Servis Berkala Kendaraan, Rotasi Ban & Peringatan KIR/STNK',
                    'Manajemen Kontrak Vendor Trucking Pihak Ketiga (3PL) & SLA Keterlambatan',
                ],
                'DIV_03_INVENTORY_SUPPLY_CHAIN' => $commonInventory,
                'DIV_04_FINANCE_ACCOUNTING' => $commonFinance,
                'DIV_05_GOVERNANCE_INTELLIGENCE' => $commonGovernance,
            ],
            'SECTOR_07_EDTECH_MEMBERSHIP' => [
                'DIV_01_FRONT_OFFICE' => [
                    'Pendaftaran Siswa/Peserta Baru (PPDB/Admisi), Seleksi & Penempatan Kelas',
                    'Portal Belajar LMS Interaktif, Jadwal Sesi Live & Pengumpulan Tugas',
                    'Scanner Presensi QR/Biometrik Kelas & Notifikasi Kehadiran Orang Tua/Peserta',
                    'Loket Tagihan SPP / Iuran Keanggotaan Berulang (Recurring) & Beasiswa',
                    'Penerbitan Sertifikat Kelulusan Digital Ber-QR Verifikasi SHA-256',
                ],
                'DIV_02_CORE_OPERATIONS' => [
                    'Arsitektur Kurikulum Berjenjang, Silabus, Prasyarat Modul & Bank Soal',
                    'Penjadwalan Ruangan, Instruktur/Dosen & Pencegahan Bentrok Jadwal',
                    'Buku Nilai (Gradebook), Rubrik Bobot Ujian & Rapor/Transkrip Akademik',
                    'Manajemen Cohort/Angkatan, Kuota Kelas & Perpindahan Semester',
                    'Evaluasi Kinerja Pengajar, Jam Mengajar & Honorarium Instruktur',
                ],
                'DIV_03_INVENTORY_SUPPLY_CHAIN' => $commonInventory,
                'DIV_04_FINANCE_ACCOUNTING' => $commonFinance,
                'DIV_05_GOVERNANCE_INTELLIGENCE' => $commonGovernance,
            ],
            'SECTOR_08_PROFESSIONAL_PSA' => [
                'DIV_01_FRONT_OFFICE' => [
                    'CRM Pipeline Peluang Klien, Kalkulator Proposal RAB & Kontrak SPK/Retainer',
                    'Portal Klien Transparan: Persetujuan Deliverable, UAT & BAST Digital',
                    'Intake Dokumen Perkara/Proyek Terenkripsi & Pemeriksaan Benturan Kepentingan',
                    'Helpdesk Tiket Dukungan SLA, Permintaan Perubahan (Change Request) & Addendum',
                    'Penjadwalan Konsultasi Ahli & Pencatatan Notulensi Resmi',
                ],
                'DIV_02_CORE_OPERATIONS' => [
                    'Work Breakdown Structure (WBS), Board Kanban Milestone & Alokasi Kapasitas',
                    'Timesheet Billable vs Non-Billable Jam Konsultan & Persetujuan Partner',
                    'Pelacakan Biaya Talangan Klien (Reimbursable Expense) & Bukti Kwitansi',
                    'Kontrol Profitabilitas Proyek (Burn-Rate Jam Engineer vs Nilai Kontrak)',
                    'Repositori Template Dokumen Hukum/Teknis & Riwayat Versi Terkunci',
                ],
                'DIV_03_INVENTORY_SUPPLY_CHAIN' => $commonInventory,
                'DIV_04_FINANCE_ACCOUNTING' => $commonFinance,
                'DIV_05_GOVERNANCE_INTELLIGENCE' => $commonGovernance,
            ],
            'SECTOR_09_FOOD_BEVERAGE' => [
                'DIV_01_FRONT_OFFICE' => [
                    'Peta Meja Interaktif (Floor Plan), Split/Merge Bill & Kasir Cepat 80mm',
                    'QR Table Self-Ordering, Katalog Modifier Menu (Level Pedas/Topping) & Upsell',
                    'Integrasi Pesanan Online Delivery, Takeaway Queue & Nomor Panggil',
                    'Reservasi Meja VIP, Minimum Spend Deposit & Pre-Order Paket Acara/Katering',
                    'Program Member Resto, Voucher Ulang Tahun & Penukaran Poin Kasir',
                ],
                'DIV_02_CORE_OPERATIONS' => [
                    'Kitchen Display System (KDS) per Stasiun Masak/Bar, Bump Bar & Timer SLA',
                    'Resep Standar (Recipe Yield), Potong Stok Bahan Baku Otomatis Saat Terjual',
                    'Produksi Central Kitchen (Prep Batch Setengah Jadi) & Distribusi ke Outlet',
                    'Pencatatan Waste/Spoilage Harian, Comps/Void Manajer & Uji Food Cost %',
                    'Rekonsiliasi Kasir per Shift, Bagi Service Charge & Tipping Kru',
                ],
                'DIV_03_INVENTORY_SUPPLY_CHAIN' => $commonInventory,
                'DIV_04_FINANCE_ACCOUNTING' => $commonFinance,
                'DIV_05_GOVERNANCE_INTELLIGENCE' => $commonGovernance,
            ],
            'SECTOR_10_MEDIA_TICKETING' => [
                'DIV_01_FRONT_OFFICE' => [
                    'Peta Kursi Venue Interaktif / Kuota Kategori Tiket (Seat-Hold Countdown Lock)',
                    'Flash-Sale Waiting Room, Kode Presale Sponsor & Checkout Pembayaran Instan',
                    'Aplikasi Gate Access Control: Offline-Ready QR Ticket Scanner Anti-Passback',
                    'Loket Penukaran Wristband/Credential, VIP Hospitality & Top-Up Cashless Event',
                    'Pusat Refund/Pembatalan Acara Terkendali & Transfer Nama Pemegang Tiket',
                ],
                'DIV_02_CORE_OPERATIONS' => [
                    'Manajemen Jadwal Panggung, Lineup Artis/Pembicara & Rider Teknis',
                    'Manajemen Booth Eksibitor, Paket Sponsorship & Deliverable Kontrak Brand',
                    'Kontrol Kapasitas Zona Real-Time, Insiden Keamanan & Evakuasi Lapangan',
                    'Perhitungan Royalti Kreator, Bagi Hasil Promotor & Pajak Hiburan Daerah',
                    'Analitik Konversi Penjualan per Gelombang (Early Bird, Presale, Normal)',
                ],
                'DIV_03_INVENTORY_SUPPLY_CHAIN' => $commonInventory,
                'DIV_04_FINANCE_ACCOUNTING' => $commonFinance,
                'DIV_05_GOVERNANCE_INTELLIGENCE' => $commonGovernance,
            ],
            'SECTOR_11_HRIS_INTERNAL_OPS' => [
                'DIV_01_FRONT_OFFICE' => [
                    'Employee Self-Service (ESS): Presensi Geofence/Wajah, Pengajuan Cuti & Lembur',
                    'Portal Rekrutmen (ATS): Lowongan, Pipeline Kandidat, Skor Tes & Offering Letter',
                    'Meja Klaim Reimbursement Medis/Perjalanan Dinas & Kasbon Karyawan',
                    'Helpdesk Internal IT/GA: Peminjaman Aset Kantor, Ruang Rapat & Tiket Fasilitas',
                    'Akses Slip Gaji Rahasia (Private Disk Signed URL) & Bukti Potong Pajak',
                ],
                'DIV_02_CORE_OPERATIONS' => [
                    'Manajemen Jadwal Shift Kompleks, Tukar Jaga & Kalkulasi Jam Kerja Aktual',
                    'Mesin Payroll Deterministik (Gaji Pokok, Tunjangan, BPJS, PPh 21 TER & Bank File)',
                    'Siklus Penilaian Kinerja (OKR/KPI), Kalibrasi 360-Derajat & Promosi/Mutasi',
                    'Onboarding Karyawan Baru & Checklist Offboarding/Clearance Aset Resign',
                    'Manajemen Pelatihan Internal, Matriks Kompetensi & Masa Berlaku Sertifikasi',
                ],
                'DIV_03_INVENTORY_SUPPLY_CHAIN' => $commonInventory,
                'DIV_04_FINANCE_ACCOUNTING' => $commonFinance,
                'DIV_05_GOVERNANCE_INTELLIGENCE' => $commonGovernance,
            ],
            'SECTOR_12_GOVTECH_NONPROFIT' => [
                'DIV_01_FRONT_OFFICE' => [
                    'Portal Layanan Publik / Donatur: Pengajuan Izin, Donasi Kampanye & Kwitansi QR',
                    'Loket Verifikasi Berkas Pemohon, Nomor Registrasi Atomik & Pelacakan SLA',
                    'Pendaftaran Penerima Manfaat / Mustahik / Hibah & Survei Kelayakan Lapangan',
                    'Kanal Pengaduan Masyarakat / Whistleblowing Terenkripsi & Tindak Lanjut',
                    'Papan Transparansi Publik Real-Time: Serapan Dana Program & Laporan Dampak',
                ],
                'DIV_02_CORE_OPERATIONS' => [
                    'Akuntansi Dana Terikat vs Tidak Terikat (Restricted Fund Ledger per Kampanye)',
                    'Meja Verifikasi Berjenjang (Front Verifier -> Kepala Seksi -> Pejabat Penandatangan)',
                    'Penyaluran Bantuan/Hibah Terjadwal, Berita Acara Distribusi & Geo-Photo Bukti',
                    'Manajemen Relawan, Penugasan Posko Lapangan & Log Jam Kontribusi',
                    'Pelaporan Kepatuhan Regulasi, Audit Eksternal & Penguncian Periode Anggaran',
                ],
                'DIV_03_INVENTORY_SUPPLY_CHAIN' => $commonInventory,
                'DIV_04_FINANCE_ACCOUNTING' => $commonFinance,
                'DIV_05_GOVERNANCE_INTELLIGENCE' => $commonGovernance,
            ],
        ];
    }

    /**
     * Render Markdown document for `.agents/ENTERPRISE_MENU_MATRIX.md`.
     *
     * @param  array<string, mixed>  $schema
     */
    public static function renderEnterpriseMenuMarkdown(array $schema): string
    {
        $appName = (string) ($schema['project_metadata']['name'] ?? 'Enterprise Application');
        $sectorCode = (string) ($schema['project_metadata']['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL');
        $menuMatrix = $schema['enterprise_menu_matrix'] ?? self::enterpriseMenuMatrixForSector($sectorCode);
        $glueList = $schema['five_enterprise_glue_primitives'] ?? self::fiveEnterpriseGluePrimitives();

        $lines = [
            "# L1–L4 ENTERPRISE MENU & CAPABILITY MATRIX + 5 ENTERPRISE GLUE — {$appName}",
            '',
            "> **Sector:** `{$sectorCode}` | **Provisioned Sub-Menus:** `{$menuMatrix['total_enterprise_menus']}` (`{$menuMatrix['included_v1_menus_count']}` Active in `v1.0`)",
            '> **Anti-Toy-App Mandate:** AI Agent DILARANG KERAS mereduksi aplikasi ini menjadi 4–5 menu CRUD dangkal. Bangun struktur navigasi & modul sesuai matriks L1–L4 di bawah ini.',
            '',
            '## BAGIAN A: Pohon Menu Standar Enterprise (5 Divisi Operasional)',
            '',
        ];

        foreach (($menuMatrix['divisions'] ?? []) as $div) {
            $lines[] = '### ' . ($div['division_name']['id'] ?? $div['division_code']);
            $lines[] = '| Kode Menu | Status Rilis | Nama Modul / Layar Operasional (L3) | Kapabilitas Wajib (L4) |';
            $lines[] = '| :--- | :---: | :--- | :--- |';
            foreach (($div['menus'] ?? []) as $m) {
                $statusBadge = ! empty($m['included_in_v1']) ? '`v1.0 ACTIVE`' : '`v1.1 BACKLOG`';
                $caps = implode(', ', (array) ($m['l4_capabilities'] ?? []));
                $lines[] = "| `{$m['code']}` | {$statusBadge} | {$m['title']} | `{$caps}` |";
            }
            $lines[] = '';
        }

        $lines[] = '## BAGIAN B: 5 Perekat Operasional Kelas Korporat (Mandatory Enterprise Glue)';
        $lines[] = '';
        $lines[] = '| # | Kode Glue | Tabel Strict ULID | Action Contract (`__invoke`) | Aturan Penegakan Anti-Slop |';
        $lines[] = '| :---: | :--- | :--- | :--- | :--- |';
        foreach ($glueList as $g) {
            $lines[] = sprintf(
                '| %d | `%s` | `%s` | `%s` | %s |',
                (int) $g['number'],
                $g['code'],
                $g['table'],
                $g['action_fqcn'],
                $g['rule']
            );
        }

        return implode("\n", $lines) . "\n";
    }
}
