<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\PosArchitecturePillar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Project OS v7.0 Ultimate — 100% Architectural Freeze.
 *
 * Implements the Final 2% Real-World & Field Runtime Contracts + Master 15 Architecture Pillars:
 * 1. Hardware, IoT & Dual-Screen Peripheral Bridge Contract (Keyboard Wedge <30ms, BroadcastChannel QRIS Local, ZKTeco/ADMS + Turnstile Relay)
 * 2. Real-Time WebSocket & Event Broadcasting Contract (Zero-Polling <30s Ban + Private Tenant Channel organization.{ulid}.{module})
 * 3. Strict N+1 Query Ban (Model::shouldBeStrict), Mandatory Tenant Composite Indexes & PG16 Range Partitioning
 * 4. Self-Healing Error Dictionary (SYNTAX_LOCK_2026.md & TROUBLESHOOTING_INSTINCTS.md tailored to M1..S2)
 * 5. Auto-Generated Interactive Onboarding Tour (SmartGuide.php) & Role-Based Client SOP Manual for BAST Handover
 */
class PosRealWorldRuntimeCatalog
{
    public const FREEZE_VERSION = '7.0.0-architectural-freeze-15-pillars';

    public const SYNTAX_LOCK_VERSION = '2026.7.0-FREEZE';

    /**
     * Canonical Master 15 Pillars of "Project OS Ultimate 100%" (100% LOCK & FREEZE — Bilingual Tier-1 ID & EN).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function fifteenMasterPillars(): array
    {
        return [
            [
                'pillar_number' => 1,
                'code' => 'PILLAR_01_12_SECTORAL_TAXONOMIES',
                'category' => 'DOMAIN_CORE',
                'name' => [
                    'id' => '12 Sectoral Functional Taxonomies',
                    'en' => '12 Sectoral Functional Taxonomies',
                ],
                'function_guarantee' => [
                    'id' => 'Memetakan seluruh bisnis dunia ke 12 arsitektur domain fungsional beserta regulasi resminya (PSAK 109, ISAK 35, SatuSehat/HL7, PB1, PPh 21/23, UU PDP).',
                    'en' => 'Maps every real-world business into 12 functional domain architectures along with statutory regulations (PSAK 109, ISAK 35, SatuSehat/HL7, PB1, PPh, PDP Act).',
                ],
                'technical_contract' => [
                    'id' => 'Zero-assumption domain invariants, terminologi baku industri, dan kepatuhan regulasi otomatis per sektor (SECTOR_01 s/d SECTOR_12).',
                    'en' => 'Zero-assumption domain invariants, standardized industry terminology, and automatic regulatory compliance per sector (SECTOR_01 through SECTOR_12).',
                ],
                'artifacts_list' => ['pos_sectors', 'ProjectOsMatrixCatalog::sectors()'],
                'related_stage_dependencies' => ['PILLAR_02', 'PILLAR_03', 'PILLAR_05', 'PILLAR_09', 'PILLAR_15'],
                'is_locked_freeze' => true,
                'is_active' => true,
            ],
            [
                'pillar_number' => 2,
                'code' => 'PILLAR_02_CORE_PRIMITIVES_AND_GLUE',
                'category' => 'DOMAIN_CORE',
                'name' => [
                    'id' => '8+ Core Primitives + 5 Enterprise Glue',
                    'en' => '8+ Core Primitives + 5 Enterprise Glue',
                ],
                'function_guarantee' => [
                    'id' => 'Modul rakitan lintas sektor (SKU Variant, FEFO Inventory, Time-Slot, Double-Entry Ledger, BoM, Approval, Cohort, RAG) + Penomoran Atomik, Tutup Buku Fiskal, Maker-Checker, Reason Vault, & Outbox.',
                    'en' => 'Cross-sector composable modules (SKU Variant, FEFO Inventory, Time-Slot, Double-Entry Ledger, BoM, Approval, Cohort, RAG) + Atomic Numbering, Fiscal Cut-Off, Maker-Checker, Reason Vault, & Outbox.',
                ],
                'technical_contract' => [
                    'id' => 'Tabel sys_document_sequences, sys_accounting_periods, sys_approval_requests, sys_reason_evidences, dan sys_integration_logs otomatis disuntikkan ke setiap blueprint.',
                    'en' => 'Tables sys_document_sequences, sys_accounting_periods, sys_approval_requests, sys_reason_evidences, and sys_integration_logs are auto-injected into every blueprint.',
                ],
                'artifacts_list' => ['pos_primitives', 'PosEnterpriseGuaranteeCatalog::fiveEnterpriseGluePrimitives()'],
                'related_stage_dependencies' => ['PILLAR_03', 'PILLAR_07', 'PILLAR_08', 'PILLAR_13', 'PILLAR_15'],
                'is_locked_freeze' => true,
                'is_active' => true,
            ],
            [
                'pillar_number' => 3,
                'code' => 'PILLAR_03_L1_L4_ENTERPRISE_MENU_MATRIX',
                'category' => 'DOMAIN_CORE',
                'name' => [
                    'id' => 'L1–L4 Enterprise Menu Matrix',
                    'en' => 'L1–L4 Enterprise Menu Matrix',
                ],
                'function_guarantee' => [
                    'id' => 'Menjamin elaborasi ide menghasilkan 25–35 menu kelas ERP lintas 5 Divisi Operasional (Front-Office, Core Ops, Inventory, Finance, Governance), bukan CRUD dangkal.',
                    'en' => 'Guarantees idea elaboration blooms 25–35 ERP-grade menus across 5 Operational Divisions (Front-Office, Core Ops, Inventory, Finance, Governance), eliminating shallow toy CRUD.',
                ],
                'technical_contract' => [
                    'id' => 'Pemilihan sub-menu aktif v1.0 secara reaktif memperbarui cakupan WebSocket, SOP per Role, jumlah jam kerja RAB, serta checklist UAT/BAST.',
                    'en' => 'Toggling active v1.0 sub-menus reactively updates WebSocket channels, Role SOP playbooks, BoQ engineering hours, and UAT/BAST checklists.',
                ],
                'artifacts_list' => ['.agents/ENTERPRISE_MENU_MATRIX.md', 'PosEnterpriseGuaranteeCatalog::enterpriseMenuMatrixForSector()'],
                'related_stage_dependencies' => ['PILLAR_05', 'PILLAR_09', 'PILLAR_11', 'PILLAR_15'],
                'is_locked_freeze' => true,
                'is_active' => true,
            ],
            [
                'pillar_number' => 4,
                'code' => 'PILLAR_04_NINE_TOPOLOGY_STACK_PRESETS',
                'category' => 'STACK_UI',
                'name' => [
                    'id' => '9 Curated Topology & Stack Presets',
                    'en' => '9 Curated Topology & Stack Presets',
                ],
                'function_guarantee' => [
                    'id' => 'Mendukung Modular Monolith (M1–M4), Decoupled Headless API (D1–D3), dan Distributed Microservices (S1–S2) dengan kontrak tipe data antarlapisan ketat.',
                    'en' => 'Supports Modular Monolith (M1–M4), Decoupled Headless API (D1–D3), and Distributed Microservices (S1–S2) with strict inter-layer type contracts.',
                ],
                'technical_contract' => [
                    'id' => 'Perubahan Stack Preset otomatis menyesuaikan SYNTAX_LOCK_2026.md, driver WebSocket, skrip Docker/Railpack, dan pengali kompleksitas RAB.',
                    'en' => 'Switching Stack Preset automatically adapts SYNTAX_LOCK_2026.md, WebSocket transport broker, Docker/Railpack configs, and BoQ complexity multipliers.',
                ],
                'artifacts_list' => ['pos_stack_presets', 'PosStackPresetCatalog'],
                'related_stage_dependencies' => ['PILLAR_09', 'PILLAR_12', 'PILLAR_13', 'PILLAR_15'],
                'is_locked_freeze' => true,
                'is_active' => true,
            ],
            [
                'pillar_number' => 5,
                'code' => 'PILLAR_05_SECTORAL_UI_PRINT_ARCHETYPES',
                'category' => 'STACK_UI',
                'name' => [
                    'id' => '12 Sectoral UI/UX & Print Archetypes',
                    'en' => '12 Sectoral UI/UX & Print Archetypes',
                ],
                'function_guarantee' => [
                    'id' => 'Memaksa layout ergonomis sesuai industri (POS Split, Gantt, SOAP Cockpit, KDS, Ledger Grid) + spesifikasi cetak Thermal ESC/POS 58/80mm & PDF A4 resmi.',
                    'en' => 'Enforces industry-ergonomic layouts (POS Split, Gantt, SOAP Cockpit, KDS, Ledger Grid) + Thermal ESC/POS 58/80mm & official A4 PDF print contracts.',
                ],
                'technical_contract' => [
                    'id' => 'Menyertakan @media print CSS deterministik, tanda tangan basah/QR verifikasi dokumen, dan pintasan keyboard kasir F1–F12.',
                    'en' => 'Includes deterministic @media print CSS, signature/QR document verification blocks, and F1–F12 cashier keyboard shortcuts.',
                ],
                'artifacts_list' => ['PosSectorUiArchetypeCatalog', 'PosDayTwoProductionCatalog::printEngineForSector()'],
                'related_stage_dependencies' => ['PILLAR_06', 'PILLAR_09', 'PILLAR_15'],
                'is_locked_freeze' => true,
                'is_active' => true,
            ],
            [
                'pillar_number' => 6,
                'code' => 'PILLAR_06_ZERO_SLOP_DESIGN_SYSTEM',
                'category' => 'STACK_UI',
                'name' => [
                    'id' => 'Zero-Slop UI/UX Design System',
                    'en' => 'Zero-Slop UI/UX Design System',
                ],
                'function_guarantee' => [
                    'id' => 'Melarang kapsul rounded-full, gradien warna-warni norak, alert()/confirm(), dan CDN eksternal; mewajibkan gaya tajam Linear/Stripe, WCAG AAA Light/Dark, & tabular-nums.',
                    'en' => 'Bans rounded-full pills, gaudy multi-color gradients, native alert()/confirm(), and external CDNs; enforces sharp Linear/Stripe aesthetics, WCAG AAA Light/Dark, & tabular-nums.',
                ],
                'technical_contract' => [
                    'id' => 'Diverifikasi otomatis oleh BlueprintSlopScannerService dan verify-blueprint.sh (Exit Code 1 jika terdeteksi pola AI slop).',
                    'en' => 'Automatically verified by BlueprintSlopScannerService and verify-blueprint.sh (Exit Code 1 upon any AI slop pattern).',
                ],
                'artifacts_list' => ['BlueprintSlopScannerService', 'scripts/verify-blueprint.sh'],
                'related_stage_dependencies' => ['PILLAR_12', 'PILLAR_13'],
                'is_locked_freeze' => true,
                'is_active' => true,
            ],
            [
                'pillar_number' => 7,
                'code' => 'PILLAR_07_STRICT_PG16_ULID_N1_PARTITION',
                'category' => 'DATA_RUNTIME',
                'name' => [
                    'id' => 'Strict PGSQL 16, ULID, Composite Index, Partitioning & N+1 Guard',
                    'en' => 'Strict PGSQL 16, ULID, Composite Index, Partitioning & N+1 Guard',
                ],
                'function_guarantee' => [
                    'id' => 'Wajib ->ulid("id"), cursorPaginate() O(1), larangan fungsi MySQL di PostgreSQL, Mandatory Tenant Composite Index, Model::shouldBeStrict(), Partisi Kuartalan, & Snapshot Histori.',
                    'en' => 'Mandates ->ulid("id"), O(1) cursorPaginate(), zero MySQL syntax in PG16, Mandatory Tenant Composite Indexes, Model::shouldBeStrict(), Range Partitioning, & Historical Snapshots.',
                ],
                'technical_contract' => [
                    'id' => 'Mencegah 100% kebocoran N+1 Lazy Loading saat testing serta menjaga performa query sub-10ms pada tabel jutaan baris via PARTITION BY RANGE (created_at).',
                    'en' => 'Prevents 100% of N+1 Lazy Loading leaks during testing and sustains sub-10ms queries across multi-million-row tables via PARTITION BY RANGE (created_at).',
                ],
                'artifacts_list' => ['database/sql/pg16_range_partitioning.sql', 'AppServiceProvider::shouldBeStrict()'],
                'related_stage_dependencies' => ['PILLAR_08', 'PILLAR_13'],
                'is_locked_freeze' => true,
                'is_active' => true,
            ],
            [
                'pillar_number' => 8,
                'code' => 'PILLAR_08_CONCURRENCY_FORMULA_SAGA_ENGINE',
                'category' => 'DATA_RUNTIME',
                'name' => [
                    'id' => 'Concurrency, Formula & Saga Engine',
                    'en' => 'Concurrency, Formula & Saga Engine',
                ],
                'function_guarantee' => [
                    'id' => 'Menjamin keakuratan uang DECIMAL(19,4), lockForUpdate() berurutan anti-deadlock, rumus pajak/alokasi sen terkakhir, Transactional Outbox, & Idempotency Guard 120s.',
                    'en' => 'Guarantees DECIMAL(19,4) monetary precision, deadlock-free ordered lockForUpdate(), tax/penny-allocation formulas, Transactional Outbox, & 120s Idempotency Guard.',
                ],
                'technical_contract' => [
                    'id' => 'Decision Tables diekspor sebagai ekspresi PHP 8.5 match() yang teruji deterministik tanpa tebakan pembulatan float.',
                    'en' => 'Decision Tables compile into deterministic PHP 8.5 match() expressions with zero floating-point rounding guesswork.',
                ],
                'artifacts_list' => ['compiled_decision_tables', 'sys_integration_logs'],
                'related_stage_dependencies' => ['PILLAR_07', 'PILLAR_09', 'PILLAR_13'],
                'is_locked_freeze' => true,
                'is_active' => true,
            ],
            [
                'pillar_number' => 9,
                'code' => 'PILLAR_09_HARDWARE_IOT_AND_WEBSOCKET',
                'category' => 'DATA_RUNTIME',
                'name' => [
                    'id' => 'Hardware, IoT & Real-Time WebSocket (Zero-Polling)',
                    'en' => 'Hardware, IoT & Real-Time WebSocket (Zero-Polling)',
                ],
                'function_guarantee' => [
                    'id' => 'Kontrak integrasi Barcode/QR/RFID Wedge (<30ms), Dual-Screen Customer QRIS lokal (BroadcastChannel), Biometrik ZKTeco/Turnstile Relay, serta larangan wire:poll (<30s) diganti WebSocket Reverb/NATS.',
                    'en' => 'Integrates Barcode/QR/RFID Wedge (<30ms), Dual-Screen Local Customer QRIS (BroadcastChannel), ZKTeco Biometric/Turnstile Relay, and bans wire:poll (<30s) in favor of Reverb/NATS WebSockets.',
                ],
                'technical_contract' => [
                    'id' => 'Semua pembaruan layar bersama (KDS, Antrean RS, Booking Lapangan, Dispatch) wajib melalui Private Tenant Channel organization.{ulid}.{module}.',
                    'en' => 'All shared live boards (KDS, Hospital Queue, Court Booking, Dispatch) broadcast strictly over Private Tenant Channel organization.{ulid}.{module}.',
                ],
                'artifacts_list' => ['resources/js/peripherals/barcode-wedge-and-dual-screen.js', 'routes/channels.php'],
                'related_stage_dependencies' => ['PILLAR_05', 'PILLAR_07', 'PILLAR_10', 'PILLAR_15'],
                'is_locked_freeze' => true,
                'is_active' => true,
            ],
            [
                'pillar_number' => 10,
                'code' => 'PILLAR_10_DAY2_OPS_ONBOARDING_SECURITY',
                'category' => 'DAY2_AI_HARNESS',
                'name' => [
                    'id' => 'Day-2 Ops, Black Box Error Monitoring, Interactive Role SOP & Security Isolation',
                    'en' => 'Day-2 Ops, Black Box Error Monitoring, Interactive Role SOP & Security Isolation',
                ],
                'function_guarantee' => [
                    'id' => 'Black Box Client & Server Error Monitoring (Sentry/Local Telemetry Catcher anti-device fragmentation iPhone/Android lama), Auto-Alerting Spike Error, Log Scrubber Anti-Leak PII (Nol bocor kata sandi/token/kartu kredit), Atomic Tenant Provisioning, Import CSV 2-Tahap, 4 Jalur Queue Redis, Isolasi Disk private/public, Matriks WA/Email, serta Auto-Generated SmartGuide.php & Manual SOP Klien.',
                    'en' => 'Black Box Client & Server Error Monitoring (Sentry/Local Telemetry Catcher anti-device fragmentation legacy iPhone/Android), Auto-Alerting Error Spikes, Anti-Leak PII Log Scrubber (Zero password/token/credit card leaks), Atomic Tenant Provisioning, 2-Stage CSV Import, 4-Lane Redis Queues, Private/Public Disk Isolation, WA/Email Matrix, plus Auto-Generated Role SmartGuide.php & Client SOP Manual.',
                ],
                'technical_contract' => [
                    'id' => 'Menghasilkan app/Filament/Pages/SmartGuide.php, resources/js/telemetry/blackbox-error-catcher.js dengan PII-scrubber teruji, dan CLIENT_SOP_AND_TRAINING_MANUAL.md secara otomatis dari L1–L4 Menu Matrix & State Machine untuk serah terima BAST bebas crash perangkat klien.',
                    'en' => 'Auto-generates app/Filament/Pages/SmartGuide.php, resources/js/telemetry/blackbox-error-catcher.js with verified PII-scrubber, and CLIENT_SOP_AND_TRAINING_MANUAL.md directly from L1–L4 Menu Matrix & State Machines for crash-free client device BAST handover.',
                ],
                'artifacts_list' => [
                    'app/Filament/Pages/SmartGuide.php',
                    '.agents/CLIENT_SOP_AND_TRAINING_MANUAL.md',
                    'resources/js/telemetry/blackbox-error-catcher.js',
                ],
                'related_stage_dependencies' => ['PILLAR_03', 'PILLAR_11', 'PILLAR_15'],
                'is_locked_freeze' => true,
                'is_active' => true,
            ],
            [
                'pillar_number' => 11,
                'code' => 'PILLAR_11_FOUR_PILLAR_AI_COPILOT_AUDIT',
                'category' => 'DAY2_AI_HARNESS',
                'name' => [
                    'id' => '4-Pillar AI Copilot, Tier-1 Dual-Locale & Audit Sync',
                    'en' => '4-Pillar AI Copilot, Tier-1 Dual-Locale & Audit Sync',
                ],
                'function_guarantee' => [
                    'id' => 'Sinkronisasi otomatis RAG pgvector 768-dim HNSW, Gemini Native functionCall (30 Tools Closed-Loop), TranslatableTabs Tier-1 (ID & EN), SmartGuide, Shield RBAC, & SHA-256 AuditLog.',
                    'en' => 'Auto-synchronizes 768-dim HNSW pgvector RAG, Gemini Native functionCall (30 Closed-Loop Tools), Tier-1 TranslatableTabs (ID & EN), SmartGuide, Shield RBAC, & SHA-256 AuditLog.',
                ],
                'technical_contract' => [
                    'id' => '0-Token local MarkItDown/OCR ingestion, pemisahan tegas Read RAG vs Write Tool Calling ber-Idempotency Guard 120s, serta larangan LocaleSwitcher sudut halaman.',
                    'en' => '0-Token local MarkItDown/OCR ingestion, strict Read RAG vs Write Tool Calling separation with 120s Idempotency Guard, and zero page-corner LocaleSwitcher.',
                ],
                'artifacts_list' => ['GeminiToolRegistry', 'AgenticActionExecutionEngine', 'TranslatableTabs'],
                'related_stage_dependencies' => ['PILLAR_10', 'PILLAR_12'],
                'is_locked_freeze' => true,
                'is_active' => true,
            ],
            [
                'pillar_number' => 12,
                'code' => 'PILLAR_12_PONYTAIL_ECC_SYNTAX_LOCK_HARNESS',
                'category' => 'DAY2_AI_HARNESS',
                'name' => [
                    'id' => 'Ponytail + ECC Execution Harness & SYNTAX_LOCK_2026',
                    'en' => 'Ponytail + ECC Execution Harness & SYNTAX_LOCK_2026',
                ],
                'function_guarantee' => [
                    'id' => 'Menghemat >50% token lewat Ponytail 4-Rung Ladder, Graphify O(1) Lookup (graph.json), siklus 6-Tahap ECC, serta kamus penyembuhan mandiri SYNTAX_LOCK_2026.md & TROUBLESHOOTING_INSTINCTS.md.',
                    'en' => 'Saves >50% tokens via the Ponytail 4-Rung Ladder, Graphify O(1) Lookup (graph.json), 6-Stage ECC Loop, and self-healing SYNTAX_LOCK_2026.md & TROUBLESHOOTING_INSTINCTS.md.',
                ],
                'technical_contract' => [
                    'id' => 'Mengunci sintaks eksak 2026 (Filament v5 Schema/Width::Full/Get/Set, Livewire 4 FilePond, Alpine x-teleport, FrankenPHP Caddyfile) sehingga memangkas waktu debugging 90%.',
                    'en' => 'Locks exact 2026 framework syntax (Filament v5 Schema/Width::Full/Get/Set, Livewire 4 FilePond, Alpine x-teleport, FrankenPHP Caddyfile), cutting debugging time by 90%.',
                ],
                'artifacts_list' => ['.agents/SYNTAX_LOCK_2026.md', '.agents/TROUBLESHOOTING_INSTINCTS.md', '.agents/PONYTAIL.md'],
                'related_stage_dependencies' => ['PILLAR_04', 'PILLAR_06', 'PILLAR_13'],
                'is_locked_freeze' => true,
                'is_active' => true,
            ],
            [
                'pillar_number' => 13,
                'code' => 'PILLAR_13_DAG_CHUNKER_SYMBOL_LOCK_EXIT_GATE',
                'category' => 'DAY2_AI_HARNESS',
                'name' => [
                    'id' => 'DAG Chunker, Symbol Lock & Compiler Exit-Gate',
                    'en' => 'DAG Chunker, Symbol Lock & Compiler Exit-Gate',
                ],
                'function_guarantee' => [
                    'id' => 'Memecah prompt per 5–6 entitas urut Foreign Key (Kahn Algorithm), mengunci nama class/kolom di SYMBOL_MANIFEST.json, dan mewajibkan Exit Code 0 (PHPStan + Pest) di tiap Part.',
                    'en' => 'Splits prompts into 5–6 entity chunks ordered by Foreign Key (Kahn Algorithm), locks FQCN/column symbols in SYMBOL_MANIFEST.json, and enforces Exit Code 0 per Part.',
                ],
                'technical_contract' => [
                    'id' => 'Setiap perubahan entitas kustom atau primitif otomatis menyusun ulang gelombang migrasi DAG dan memperbarui <MANDATORY_VERIFICATION_GATE>.',
                    'en' => 'Any change to custom entities or primitives automatically re-sorts DAG migration waves and regenerates <MANDATORY_VERIFICATION_GATE>.',
                ],
                'artifacts_list' => ['TopologicalPromptChunker', '.agents/SYMBOL_MANIFEST.json'],
                'related_stage_dependencies' => ['PILLAR_07', 'PILLAR_12', 'PILLAR_14'],
                'is_locked_freeze' => true,
                'is_active' => true,
            ],
            [
                'pillar_number' => 14,
                'code' => 'PILLAR_14_DAY30_DELTA_ENGINE_MCP_BRIDGE',
                'category' => 'COMMERCIAL_LIFECYCLE',
                'name' => [
                    'id' => 'Day-30+ Delta Engine, Hermetic Isolation & MCP Bridge',
                    'en' => 'Day-30+ Delta Engine, Hermetic Isolation & MCP Bridge',
                ],
                'function_guarantee' => [
                    'id' => 'Mengelola Change Request aditif (v1.0 -> v1.1 Surgical Delta Prompt), Hermetic Project Isolation Token (anti-kontaminasi proyek lama), Red-Team Security Test, & koneksi langsung IDE via MCP.',
                    'en' => 'Manages additive Change Requests (v1.0 -> v1.1 Surgical Delta Prompt), Hermetic Project Isolation Tokens, Red-Team Security Tests, & live IDE sync via MCP.',
                ],
                'technical_contract' => [
                    'id' => 'Token MCP disimpan hanya dalam bentuk hash SHA-256 (npro_mcp_*) dengan endpoint JSON-RPC 2.0 dan penanda isolasi pos_iso_* unik per blueprint.',
                    'en' => 'MCP tokens are stored strictly as SHA-256 hashes (npro_mcp_*) backing JSON-RPC 2.0 endpoints and unique pos_iso_* hermetic isolation markers.',
                ],
                'artifacts_list' => ['PosMcpController', 'pos_change_requests', 'isolation_token'],
                'related_stage_dependencies' => ['PILLAR_13', 'PILLAR_15'],
                'is_locked_freeze' => true,
                'is_active' => true,
            ],
            [
                'pillar_number' => 15,
                'code' => 'PILLAR_15_DUAL_BUSINESS_ENGINE_RETAIL_MITRA',
                'category' => 'COMMERCIAL_LIFECYCLE',
                'name' => [
                    'id' => 'Dual Business Engine (Retail Self-Dev & Mitra Agency Turnkey)',
                    'en' => 'Dual Business Engine (Retail Self-Dev & Partner Agency Turnkey)',
                ],
                'function_guarantee' => [
                    'id' => '3-Pane Architect Studio Satu Pintu yang melayani Developer Mandiri (Spark–Ultimate) sekaligus otomatis menghitung RAB, Kontrak SPK ber-hash SHA-256, Sprint Anti-Collision, UAT, & BAST Digital.',
                    'en' => 'Unified 3-Pane Architect Studio serving Self-Service Developers (Spark–Ultimate) while algorithmically computing BoQ, SHA-256 SPK Contracts, Anti-Collision Sprints, UAT, & Digital BAST.',
                ],
                'technical_contract' => [
                    'id' => 'Setiap perubahan spesifikasi teknis (Menu L1–L4, Tabel, Hardware/IoT, WebSocket, Stack) secara real-time mengalkulasi ulang RAB, SHA-256 Scope Hash, UAT, dan Paket BAST.',
                    'en' => 'Every technical specification change (L1–L4 Menus, Tables, Hardware/IoT, WebSocket, Stack) recalculates BoQ IDR, SHA-256 Scope Hash, UAT scenarios, and BAST deliverables in real time.',
                ],
                'artifacts_list' => ['PosCommercialBridgeService', 'PricingRegistry', 'BlueprintArchitectStudio'],
                'related_stage_dependencies' => ['PILLAR_01', 'PILLAR_03', 'PILLAR_09', 'PILLAR_10', 'PILLAR_14'],
                'is_locked_freeze' => true,
                'is_active' => true,
            ],
        ];
    }

    /**
     * Idempotently seed the 15 Master Architecture Pillars into `pos_architecture_pillars`.
     */
    public static function seedFifteenArchitecturePillars(): void
    {
        try {
            if (! Schema::hasTable('pos_architecture_pillars')) {
                return;
            }

            foreach (self::fifteenMasterPillars() as $pillar) {
                PosArchitecturePillar::query()->updateOrCreate(
                    ['pillar_number' => $pillar['pillar_number']],
                    $pillar
                );
            }

            Cache::forget(PosArchitecturePillar::CACHE_KEY);
        } catch (\Throwable) {
            // Safe zero-downtime fallback before migration
        }
    }

    /**
     * Summary of the 5 Real-World & Field Runtime Elements (Final 2% -> 100% Architectural Freeze).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function fiveRealWorldElements(?string $locale = null): array
    {
        $locale = in_array($locale, ['id', 'en'], true) ? $locale : 'id';

        $elements = [
            [
                'number' => 1,
                'code' => 'ELEMENT_01_HARDWARE_IOT_DUAL_SCREEN_BRIDGE',
                'badge' => 'BARCODE <30MS • DUAL-SCREEN QRIS • IOT RELAY',
                'pillar_ref' => 9,
                'title' => [
                    'id' => 'Hardware, IoT & Dual-Screen Peripheral Bridge Contract',
                    'en' => 'Hardware, IoT & Dual-Screen Peripheral Bridge Contract',
                ],
                'summary' => [
                    'id' => 'Kontrak integrasi alat fisik lapangan: Global Keyboard Wedge Scanner (<30ms tanpa fokus kursor), Dual-Screen Pelanggan via BroadcastChannel + Dynamic QRIS lokal tanpa CDN, serta Webhook Biometrik ZKTeco/ADMS & Relay Palang Turnstile.',
                    'en' => 'Field hardware integration contract: Global Keyboard Wedge Scanner (<30ms focus-free), Customer Dual-Screen via BroadcastChannel + 100% local Dynamic QRIS (zero CDN), plus ZKTeco/ADMS Biometric Webhook & Smart Turnstile Relay.',
                ],
                'artifacts' => [
                    'resources/js/peripherals/barcode-wedge-and-dual-screen.js',
                    'POST /api/v1/iot/biometric/adms-push',
                    'POST /api/v1/iot/turnstile/open-relay',
                ],
            ],
            [
                'number' => 2,
                'code' => 'ELEMENT_02_REALTIME_WEBSOCKET_ZERO_POLLING',
                'badge' => 'ZERO WIRE:POLL (<30S BAN) • REVERB / NATS',
                'pillar_ref' => 9,
                'title' => [
                    'id' => 'Real-Time WebSocket & Event Broadcasting Contract (Zero-Polling)',
                    'en' => 'Real-Time WebSocket & Event Broadcasting Contract (Zero-Polling)',
                ],
                'summary' => [
                    'id' => 'HARAM wire:poll frekuensi tinggi (<30 detik) pada layar bersama (KDS, Antrean RS, Booking Lapangan, Dispatch). WAJIB Event-Driven WebSocket (Laravel Reverb / Soketi / NATS) di Private Tenant Channel organization.{ulid}.{module}.',
                    'en' => 'STRICT BAN on high-frequency wire:poll (<30s) on shared boards (KDS, Hospital Queue, Court Booking, Dispatch). MANDATORY Event-Driven WebSocket (Laravel Reverb / Soketi / NATS) over Private Tenant Channel organization.{ulid}.{module}.',
                ],
                'artifacts' => [
                    'routes/channels.php',
                    'App\\Events\\OperationalStateBroadcasted',
                ],
            ],
            [
                'number' => 3,
                'code' => 'ELEMENT_03_STRICT_N1_COMPOSITE_INDEX_PARTITIONING',
                'badge' => '0% N+1 STRICT • COMPOSITE INDEX • PG16 PARTITION',
                'pillar_ref' => 7,
                'title' => [
                    'id' => 'Strict N+1 Query Ban, Composite Indexing & PG16 Partitioning',
                    'en' => 'Strict N+1 Query Ban, Composite Indexing & PG16 Partitioning',
                ],
                'summary' => [
                    'id' => 'Wajib Model::shouldBeStrict(! app()->isProduction()) di AppServiceProvider, Mandatory Tenant Composite Index (organization_id, status, created_at) & (organization_id, id), serta skrip PostgreSQL 16 Range Partitioning per kuartal untuk tabel jutaan baris.',
                    'en' => 'Enforces Model::shouldBeStrict(! app()->isProduction()) in AppServiceProvider, Mandatory Tenant Composite Indexes (organization_id, status, created_at) & (organization_id, id), plus quarterly PostgreSQL 16 Range Partitioning DDL for giant append-only tables.',
                ],
                'artifacts' => [
                    'app/Providers/AppServiceProvider.php',
                    'database/sql/pg16_range_partitioning.sql',
                ],
            ],
            [
                'number' => 4,
                'code' => 'ELEMENT_04_SELF_HEALING_SYNTAX_LOCK_2026',
                'badge' => 'SYNTAX_LOCK_2026.MD • -90% DEBUG TIME',
                'pillar_ref' => 12,
                'title' => [
                    'id' => 'Self-Healing Error Dictionary (SYNTAX_LOCK_2026 & TROUBLESHOOTING_INSTINCTS)',
                    'en' => 'Self-Healing Error Dictionary (SYNTAX_LOCK_2026 & TROUBLESHOOTING_INSTINCTS)',
                ],
                'summary' => [
                    'id' => 'Cheatsheet sintaks eksak 2026 sesuai Stack terpilih (Filament v5 Schema/Width::Full/Utilities\\Get&Set, Livewire 4 FilePond upload_progress=0, Alpine x-teleport, FrankenPHP Caddyfile) yang memangkas kebingungan versi LLM hingga 90%.',
                    'en' => 'Exact 2026 syntax cheatsheet tailored to the chosen Stack (Filament v5 Schema/Width::Full/Utilities\\Get&Set, Livewire 4 FilePond upload_progress=0, Alpine x-teleport, FrankenPHP Caddyfile), cutting LLM version drift debugging by 90%.',
                ],
                'artifacts' => [
                    '.agents/SYNTAX_LOCK_2026.md',
                    '.agents/TROUBLESHOOTING_INSTINCTS.md',
                ],
            ],
            [
                'number' => 5,
                'code' => 'ELEMENT_05_INTERACTIVE_ONBOARDING_AND_CLIENT_SOP',
                'badge' => 'SMARTGUIDE.PHP • ROLE SOP & BAST MANUAL',
                'pillar_ref' => 10,
                'title' => [
                    'id' => 'Auto-Generated Interactive Onboarding Tour & Role-Based Client SOP',
                    'en' => 'Auto-Generated Interactive Onboarding Tour & Role-Based Client SOP',
                ],
                'summary' => [
                    'id' => 'Otomatis merakit Panduan Interaktif In-App (SmartGuide.php) per Role/Departemen dan Dokumen Manual Operasional & Materi Training Klien siap BAST langsung dari L1–L4 Menu Matrix + State Machine blueprint.',
                    'en' => 'Automatically synthesizes an In-App Interactive Role Guide (SmartGuide.php) and a BAST-ready Client Operations & Training Manual directly from the blueprint L1–L4 Menu Matrix and State Machines.',
                ],
                'artifacts' => [
                    'app/Filament/Pages/SmartGuide.php',
                    '.agents/CLIENT_SOP_AND_TRAINING_MANUAL.md',
                ],
            ],
        ];

        return array_values(array_map(static function (array $el) use ($locale): array {
            $el['name'] = $el['title'];
            $el['localized_title'] = (string) ($el['title'][$locale] ?? $el['title']['id']);
            $el['localized_summary'] = (string) ($el['summary'][$locale] ?? $el['summary']['id']);

            return $el;
        }, $elements));
    }

    /**
     * Alias for fiveRealWorldElements() used across SmartGuide & Matrix dashboards.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function fiveRealWorldElementsOverview(?string $locale = null): array
    {
        return self::fiveRealWorldElements($locale);
    }

    /**
     * ELEMENT #1: Hardware, IoT & Dual-Screen Peripheral Bridge Contract dynamically resolved per Sector + Primitives + Menus.
     *
     * @param  array<int, string>  $primitiveCodes
     * @param  array<string, mixed>  $menuMatrix
     * @return array<string, mixed>
     */
    public static function hardwarePeripheralContractForSector(
        string $sectorCode,
        array $primitiveCodes = [],
        array $menuMatrix = [],
        ?string $locale = null
    ): array {
        $locale = in_array($locale, ['id', 'en'], true) ? $locale : 'id';
        $prefix = substr(strtoupper(trim($sectorCode)), 0, 9);

        $scannerSectors = ['SECTOR_01', 'SECTOR_02', 'SECTOR_03', 'SECTOR_05', 'SECTOR_06'];
        $dualScreenSectors = ['SECTOR_01', 'SECTOR_03', 'SECTOR_09'];
        $biometricSectors = ['SECTOR_02', 'SECTOR_07', 'SECTOR_11'];

        $hasInventoryOrCatalog = ! empty(array_intersect($primitiveCodes, [
            'PRIMITIVE_CATALOG_VARIANT_MATRIX',
            'PRIMITIVE_MULTI_WAREHOUSE_INVENTORY',
            'PRIMITIVE_BOM_WORK_ORDER',
        ]));
        $hasTimeSlot = in_array('PRIMITIVE_TIME_SLOT_ENGINE', $primitiveCodes, true);

        $scannerActive = in_array($prefix, $scannerSectors, true) || $hasInventoryOrCatalog;
        $dualScreenActive = in_array($prefix, $dualScreenSectors, true);
        $biometricActive = in_array($prefix, $biometricSectors, true) || $hasTimeSlot;

        $v1MenusCount = (int) ($menuMatrix['included_v1_menus_count'] ?? 25);

        $peripherals = [];

        $peripherals[] = [
            'code' => 'PERIPHERAL_BARCODE_QR_RFID_WEDGE',
            'active_by_default' => $scannerActive,
            'mode' => $scannerActive ? 'MANDATORY_ACTIVE' : 'OPTIONAL_READY',
            'applicable_sectors' => ['SECTOR_01', 'SECTOR_02', 'SECTOR_03', 'SECTOR_05', 'SECTOR_06'],
            'title' => [
                'id' => 'Scanner Barcode / QR / RFID (Global Keyboard Wedge < 30ms)',
                'en' => 'Barcode / QR / RFID Scanner (Global Keyboard Wedge < 30ms)',
            ],
            'contract' => [
                'id' => 'Listener global Alpine.js mendeteksi rentetan karakter cepat (< 30ms antar-karakter) yang diakhiri tombol Enter/Tab tanpa mewajibkan kursor fokus di kotak pencarian, lalu memicu event pos:hardware-scan secara atomik.',
                'en' => 'Global Alpine.js listener detects rapid keystroke bursts (< 30ms inter-character cadence) terminated by Enter/Tab without requiring input cursor focus, dispatching atomic pos:hardware-scan events.',
            ],
            'inter_keystroke_threshold_ms' => 30,
            'min_payload_length' => 4,
            'terminator_keys' => ['Enter', 'Tab'],
            'browser_event' => 'pos:hardware-scan',
            'artifact_path' => 'resources/js/peripherals/barcode-wedge-and-dual-screen.js',
        ];

        $peripherals[] = [
            'code' => 'PERIPHERAL_DUAL_SCREEN_LOCAL_QRIS',
            'active_by_default' => $dualScreenActive,
            'mode' => $dualScreenActive ? 'MANDATORY_ACTIVE' : 'OPTIONAL_READY',
            'applicable_sectors' => ['SECTOR_01', 'SECTOR_03', 'SECTOR_09'],
            'title' => [
                'id' => 'Dual-Display / Customer-Facing Screen & Dynamic QRIS Lokal',
                'en' => 'Dual-Display / Customer-Facing Screen & Local Dynamic QRIS',
            ],
            'contract' => [
                'id' => 'Sinkronisasi instan Layar Kasir -> Layar Pelanggan menggunakan BroadcastChannel API lokal browser (neriah_pos_dual_screen_{org_ulid}) yang merender rincian keranjang tabular-nums dan Dynamic QRIS SVG lokal (QRCodeStyling lokal tanpa CDN eksternal).',
                'en' => 'Zero-latency Cashier -> Customer Display sync via browser BroadcastChannel API (neriah_pos_dual_screen_{org_ulid}) rendering tabular-nums line items and 100% local Dynamic QRIS SVG (local QRCodeStyling, zero external CDN).',
            ],
            'channel_pattern' => 'neriah_pos_dual_screen_{organization_ulid}',
            'supported_events' => ['CART_UPDATED', 'SHOW_DYNAMIC_QRIS', 'PAYMENT_SETTLED', 'IDLE_BRAND_SCREEN'],
            'local_qr_vendor_path' => 'public/js/vendor/qr-code-styling.local.min.js',
            'external_cdn_allowed' => false,
        ];

        $peripherals[] = [
            'code' => 'PERIPHERAL_BIOMETRIC_ADMS_AND_TURNSTILE_RELAY',
            'active_by_default' => $biometricActive,
            'mode' => $biometricActive ? 'MANDATORY_ACTIVE' : 'OPTIONAL_READY',
            'applicable_sectors' => ['SECTOR_02', 'SECTOR_07', 'SECTOR_11'],
            'title' => [
                'id' => 'Mesin Absensi Biometrik (ZKTeco/ADMS) & Smart Turnstile Gate Relay',
                'en' => 'Biometric Attendance (ZKTeco/ADMS) & Smart Turnstile Gate Relay',
            ],
            'contract' => [
                'id' => 'Webhook Receiver idempoten standar ZKTeco/ADMS (dicatat di sys_integration_logs) serta Relay API pembuka palang pintu/turnstile otomatis (<150ms) saat token QR member/staf tervalidasi aktif dan tidak kedaluwarsa.',
                'en' => 'Idempotent ZKTeco/ADMS Webhook Receiver (logged in sys_integration_logs) plus low-latency (<150ms) Smart Turnstile/Barrier Gate Relay API triggered upon valid signed member/staff QR verification.',
            ],
            'webhook_endpoint' => 'POST /api/v1/iot/biometric/adms-push',
            'turnstile_relay_endpoint' => 'POST /api/v1/iot/turnstile/open-relay',
            'idempotency_backing_table' => 'sys_integration_logs',
            'qr_token_ttl_seconds' => 15,
        ];

        $activeCount = count(array_filter($peripherals, static fn (array $p): bool => ! empty($p['active_by_default'])));

        return [
            'sector_code' => $sectorCode,
            'active_peripherals_count' => $activeCount,
            'active_bridges_count' => $activeCount,
            'total_peripherals_catalog' => count($peripherals),
            'linked_v1_menus_count' => $v1MenusCount,
            'zero_external_cdn_enforced' => true,
            'barcode_rfid_wedge' => array_merge($peripherals[0], ['enabled' => $scannerActive]),
            'customer_dual_screen' => array_merge($peripherals[1], ['enabled' => $dualScreenActive]),
            'biometric_and_turnstile_iot' => array_merge($peripherals[2], ['enabled' => $biometricActive]),
            'peripherals' => array_values(array_map(static function (array $p) use ($locale): array {
                $p['localized_title'] = (string) ($p['title'][$locale] ?? $p['title']['id']);
                $p['localized_contract'] = (string) ($p['contract'][$locale] ?? $p['contract']['id']);

                return $p;
            }, $peripherals)),
        ];
    }

    /**
     * ELEMENT #2: Real-Time WebSocket & Event Broadcasting Contract (Zero-Polling).
     *
     * @param  array<string, mixed>  $menuMatrix
     * @return array<string, mixed>
     */
    public static function websocketBroadcastContract(
        string $sectorCode,
        array $menuMatrix = [],
        string $stackCode = 'STACK_M1_LARAVEL_MONOLITH',
        ?string $locale = null
    ): array {
        $locale = in_array($locale, ['id', 'en'], true) ? $locale : 'id';
        $prefix = substr(strtoupper(trim($sectorCode)), 0, 9);

        $broker = match (true) {
            str_contains($stackCode, 'S1'), str_contains($stackCode, 'S2') => 'NATS JetStream + WebSocket Edge Gateway',
            str_contains($stackCode, 'D2'), str_contains($stackCode, 'D3') => 'Redis Pub/Sub + Dedicated Soketi / Centrifugo WebSocket',
            str_contains($stackCode, 'M3') => 'Soketi / Pusher Protocol + tRPC Subscription Stream',
            str_contains($stackCode, 'M4') => 'Django Channels (ASGI) + Redis Channel Layer',
            default => 'Laravel Reverb (First-Party WebSocket) + Laravel Echo Local Bundle',
        };

        $sectorDefaultBoards = match ($prefix) {
            'SECTOR_01' => ['pos_register_sync', 'kitchen_kds_board', 'warehouse_pick_pack'],
            'SECTOR_02' => ['resource_slot_calendar', 'housekeeping_unit_status', 'turnstile_checkin_feed'],
            'SECTOR_03' => ['outpatient_queue_cockpit', 'pharmacy_dispense_board', 'er_bed_occupancy'],
            'SECTOR_04' => ['payment_settlement_stream', 'maker_checker_approval_inbox', 'reconciliation_ticker'],
            'SECTOR_05' => ['shopfloor_work_order_board', 'wip_station_tracker', 'qc_inspection_feed'],
            'SECTOR_06' => ['fleet_dispatch_control_tower', 'hub_manifest_scan_stream', 'pod_exception_feed'],
            default => ['operational_live_board', 'maker_checker_approval_inbox', 'notification_bell_stream'],
        };

        // Cross-stage link: enrich channels from active v1.0 L1-L4 menus with REALTIME_BOARD_SYNC capability
        $realtimeMenus = [];
        foreach (($menuMatrix['divisions'] ?? []) as $div) {
            foreach (($div['menus'] ?? []) as $menu) {
                if (! empty($menu['included_in_v1']) && in_array('REALTIME_BOARD_SYNC', (array) ($menu['l4_capabilities'] ?? []), true)) {
                    $realtimeMenus[] = [
                        'menu_code' => $menu['code'],
                        'title' => $menu['title_i18n'][$locale] ?? $menu['title'],
                        'private_channel' => 'organization.{organization_ulid}.' . strtolower((string) $menu['code']),
                    ];
                }
            }
        }

        $channels = [];
        foreach ($sectorDefaultBoards as $mod) {
            $channels[] = [
                'module' => $mod,
                'channel_name' => "organization.{organization_ulid}.{$mod}",
                'auth_guard' => 'strict_user_organization_ulid_match',
                'broadcast_interface' => 'Illuminate\\Contracts\\Broadcasting\\ShouldBroadcastNow',
            ];
        }

        return [
            'zero_high_freq_polling_enforced' => true,
            'strict_zero_high_freq_polling' => true,
            'min_allowed_fallback_poll_seconds' => 30,
            'banned_patterns' => [
                'wire:poll (implicit 2.5s)',
                'wire:poll.1s .. wire:poll.29s',
                'wire:poll.500ms / wire:poll.750ms',
                'setInterval(fetch, < 30000)',
            ],
            'selected_broker' => $broker,
            'engine' => $broker,
            'private_channel_pattern' => 'organization.{ulid}.{module}',
            'channels' => array_map(static function (array $ch): array {
                $ch['channel'] = $ch['channel_name'];

                return $ch;
            }, $channels),
            'linked_realtime_v1_menus' => $realtimeMenus,
            'policy_summary' => [
                'id' => "HARAM menggunakan wire:poll (<30s) yang menghancurkan CPU PostgreSQL saat 200+ staf membuka layar bersamaan. Semua update layar bersama wajib Event-Driven via {$broker} pada Private Tenant Channel organization.{ulid}.{module}.",
                'en' => "STRICT BAN on wire:poll (<30s) to protect PostgreSQL CPU under 200+ concurrent operators. All shared operational screens MUST push state deltas via {$broker} on Private Tenant Channel organization.{ulid}.{module}.",
            ],
            'localized_policy_summary' => $locale === 'en'
                ? "STRICT BAN on wire:poll (<30s). Mandatory Event-Driven push via {$broker} on Private Tenant Channel organization.{ulid}.{module}."
                : "HARAM menggunakan wire:poll (<30s). Wajib Event-Driven push via {$broker} pada Private Tenant Channel organization.{ulid}.{module}.",
        ];
    }

    /**
     * ELEMENT #3 (Part A): Auto-inject Mandatory Tenant Composite Indexes into every tenant_scoped table in data_dictionary.
     *
     * @param  array<int, array<string, mixed>>  $dataDictionary
     * @return array<int, array<string, mixed>>
     */
    public static function injectMandatoryTenantCompositeIndexes(array $dataDictionary): array
    {
        return array_values(array_map(static function (array $tableSpec): array {
            $isTenantScoped = ! empty($tableSpec['tenant_scoped']);
            $columns = is_array($tableSpec['columns'] ?? null) ? $tableSpec['columns'] : [];
            $colNames = array_map(static fn ($c): string => (string) ($c['name'] ?? ''), $columns);

            $hasOrgCol = in_array('organization_id', $colNames, true);
            if (! $isTenantScoped && ! $hasOrgCol) {
                return $tableSpec;
            }

            $hasStatus = in_array('status', $colNames, true);
            $hasCreatedAt = in_array('created_at', $colNames, true) || in_array('timestamps', $colNames, true) || in_array('posted_at', $colNames, true);

            $compositeIndexes = is_array($tableSpec['composite_indexes'] ?? null) ? $tableSpec['composite_indexes'] : [];
            $tableName = (string) ($tableSpec['table'] ?? 'entity');

            $keysetIdxName = "{$tableName}_org_id_cursor_idx";
            $existingNames = array_column($compositeIndexes, 'name');

            if ($hasOrgCol && ! in_array($keysetIdxName, $existingNames, true)) {
                $compositeIndexes[] = [
                    'name' => $keysetIdxName,
                    'columns' => ['organization_id', 'id'],
                    'laravel_migration' => "\$table->index(['organization_id', 'id'], '{$keysetIdxName}');",
                    'purpose' => 'O(1) Tenant-Scoped Keyset Cursor Pagination Stability',
                ];
            }

            if ($hasOrgCol && $hasStatus && $hasCreatedAt) {
                $statusIdxName = "{$tableName}_org_status_created_idx";
                if (! in_array($statusIdxName, $existingNames, true)) {
                    $timeCol = in_array('posted_at', $colNames, true) && ! in_array('created_at', $colNames, true) && ! in_array('timestamps', $colNames, true)
                        ? 'posted_at'
                        : 'created_at';
                    $compositeIndexes[] = [
                        'name' => $statusIdxName,
                        'columns' => ['organization_id', 'status', $timeCol],
                        'laravel_migration' => "\$table->index(['organization_id', 'status', '{$timeCol}'], '{$statusIdxName}');",
                        'purpose' => 'Sub-10ms Tenant Operational Filtering & Dashboard Rollups',
                    ];
                }
            }

            $tableSpec['composite_indexes'] = $compositeIndexes;

            return $tableSpec;
        }, $dataDictionary));
    }

    /**
     * ELEMENT #3 (Part B): Strict N+1 Query Ban, Composite Indexing & PG16 Partitioning Contract.
     *
     * @param  array<int, array<string, mixed>>  $dataDictionary
     * @return array<string, mixed>
     */
    public static function strictQueryAndPartitionContract(
        array $dataDictionary,
        string $stackCode = 'STACK_M1_LARAVEL_MONOLITH',
        ?string $locale = null
    ): array {
        $locale = in_array($locale, ['id', 'en'], true) ? $locale : 'id';

        $canonicalPartitionCandidates = [
            'audit_logs',
            'inventory_movements',
            'journal_lines',
            'sys_integration_logs',
        ];

        $presentTables = array_map(static fn (array $t): string => (string) ($t['table'] ?? ''), $dataDictionary);
        $partitionedTables = [];

        foreach ($presentTables as $tbl) {
            if (
                in_array($tbl, $canonicalPartitionCandidates, true)
                || str_ends_with($tbl, '_movements')
                || str_ends_with($tbl, '_telemetry')
                || str_ends_with($tbl, '_events')
            ) {
                $partitionedTables[] = $tbl;
            }
        }
        // Always include audit_logs in the partitioning plan as universal governance table
        if (! in_array('audit_logs', $partitionedTables, true)) {
            array_unshift($partitionedTables, 'audit_logs');
        }
        $partitionedTables = array_values(array_unique($partitionedTables));

        $compositeIndexCount = 0;
        foreach ($dataDictionary as $t) {
            $compositeIndexCount += count((array) ($t['composite_indexes'] ?? []));
        }

        return [
            'eloquent_strict_mode_boot' => 'Model::shouldBeStrict(! $this->app->isProduction());',
            'strict_guards_enabled' => [
                'preventLazyLoading (0% N+1 Query Leaks)',
                'preventSilentlyDiscardingAttributes (Strict $fillable Integrity)',
                'preventAccessingMissingAttributes (Zero Undefined Column Drift)',
            ],
            'mandatory_tenant_composite_indexes' => [
                "\$table->index(['organization_id', 'status', 'created_at']);",
                "\$table->index(['organization_id', 'id']);",
            ],
            'total_composite_indexes_compiled' => $compositeIndexCount,
            'partition_strategy' => 'POSTGRESQL_16_DECLARATIVE_RANGE_QUARTERLY',
            'partitioned_tables' => $partitionedTables,
            'sql_artifact_path' => 'database/sql/pg16_range_partitioning.sql',
            'stack_code' => $stackCode,
            'summary' => [
                'id' => sprintf(
                    'Model::shouldBeStrict(! app()->isProduction()) aktif wajib di AppServiceProvider, %d Composite Index tenant terkompilasi otomatis, dan skrip PostgreSQL 16 Quarterly Range Partitioning siap untuk %d tabel raksasa (%s).',
                    $compositeIndexCount,
                    count($partitionedTables),
                    implode(', ', $partitionedTables)
                ),
                'en' => sprintf(
                    'Model::shouldBeStrict(! app()->isProduction()) enforced in AppServiceProvider, %d Tenant Composite Indexes auto-compiled, and PostgreSQL 16 Quarterly Range Partitioning DDL generated for %d high-growth tables (%s).',
                    $compositeIndexCount,
                    count($partitionedTables),
                    implode(', ', $partitionedTables)
                ),
            ],
            'localized_summary' => $locale === 'en'
                ? sprintf('Strict 0%% N+1 Mode + %d Tenant Composite Indexes + PG16 Range Partitioning (%s).', $compositeIndexCount, implode(', ', $partitionedTables))
                : sprintf('Mode Ketat 0%% N+1 + %d Composite Index Tenant + Partisi Kuartal PG16 (%s).', $compositeIndexCount, implode(', ', $partitionedTables)),
        ];
    }

    /**
     * Generate production-ready PostgreSQL 16 Declarative Quarterly Range Partitioning SQL.
     *
     * @param  array<int, array<string, mixed>>  $dataDictionary
     */
    public static function renderPg16RangePartitioningSql(array $dataDictionary, string $appName = 'Enterprise App'): string
    {
        $contract = self::strictQueryAndPartitionContract($dataDictionary);
        $tables = $contract['partitioned_tables'] ?? ['audit_logs', 'inventory_movements', 'journal_lines'];
        $year = (int) date('Y');

        $lines = [
            "-- ============================================================================",
            "-- PROJECT OS v7.0 — POSTGRESQL 16 DECLARATIVE RANGE PARTITIONING & COMPOSITE INDEXES",
            "-- Application : {$appName}",
            "-- Strategy    : Quarterly PARTITION BY RANGE (created_at) + Composite ULID Keys",
            "-- Note        : In PostgreSQL 16, PRIMARY KEY on partitioned tables MUST include",
            "--               the partition key: PRIMARY KEY (id, created_at) with VARCHAR(26) ULID.",
            "-- ============================================================================",
            '',
        ];

        foreach ($tables as $tbl) {
            $safeTbl = preg_replace('/[^a-z0-9_]/', '', strtolower((string) $tbl)) ?: 'audit_logs';
            $lines[] = "-- [Partitioned Table: {$safeTbl}]";
            $lines[] = "CREATE TABLE IF NOT EXISTS {$safeTbl}_partitioned (";
            $lines[] = "    id CHAR(26) NOT NULL,";
            $lines[] = "    organization_id CHAR(26) NOT NULL,";
            $lines[] = "    status VARCHAR(32) NOT NULL DEFAULT 'RECORDED',";
            $lines[] = "    payload JSONB NULL,";
            $lines[] = "    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),";
            $lines[] = "    PRIMARY KEY (id, created_at)";
            $lines[] = ") PARTITION BY RANGE (created_at);";
            $lines[] = '';
            $lines[] = "CREATE INDEX IF NOT EXISTS {$safeTbl}_part_org_status_created_idx ON {$safeTbl}_partitioned (organization_id, status, created_at DESC);";
            $lines[] = "CREATE INDEX IF NOT EXISTS {$safeTbl}_part_org_id_cursor_idx ON {$safeTbl}_partitioned (organization_id, id ASC);";
            $lines[] = '';

            $quarters = [
                ['q' => 'q1', 'from' => "{$year}-01-01", 'to' => "{$year}-04-01"],
                ['q' => 'q2', 'from' => "{$year}-04-01", 'to' => "{$year}-07-01"],
                ['q' => 'q3', 'from' => "{$year}-07-01", 'to' => "{$year}-10-01"],
                ['q' => 'q4', 'from' => "{$year}-10-01", 'to' => ($year + 1) . "-01-01"],
            ];

            foreach ($quarters as $q) {
                $lines[] = sprintf(
                    "CREATE TABLE IF NOT EXISTS %s_%d_%s PARTITION OF %s_partitioned FOR VALUES FROM ('%s 00:00:00+00') TO ('%s 00:00:00+00');",
                    $safeTbl,
                    $year,
                    $q['q'],
                    $safeTbl,
                    $q['from'],
                    $q['to']
                );
            }
            $lines[] = "CREATE TABLE IF NOT EXISTS {$safeTbl}_default PARTITION OF {$safeTbl}_partitioned DEFAULT;";
            $lines[] = '';
        }

        return implode("\n", $lines) . "\n";
    }

    /**
     * Generate local JavaScript bundle for Element #1 (Keyboard Wedge <30ms + Dual-Screen Local BroadcastChannel QRIS).
     *
     * @param  array<string, mixed>  $schema
     */
    public static function renderHardwareBridgeJs(array $schema): string
    {
        $appName = addslashes((string) ($schema['project_metadata']['name'] ?? 'Enterprise POS'));
        $sector = addslashes((string) ($schema['project_metadata']['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL'));

        return <<<JS
/**
 * Project OS v7.0 — Field Hardware, IoT & Dual-Screen Peripheral Bridge (100% Local, Zero CDN)
 * Application: {$appName} | Sector: {$sector}
 */
export function registerProjectOsHardwareBridge(organizationUlid = 'default_org') {
    // 1. Global Barcode / QR / RFID Keyboard Wedge Listener (< 30ms inter-keystroke cadence, focus-free)
    let wedgeBuffer = '';
    let lastKeyTime = 0;
    const WEDGE_THRESHOLD_MS = 30;
    const MIN_SCAN_LENGTH = 4;

    window.addEventListener('keydown', (event) => {
        const activeTag = (document.activeElement?.tagName || '').toUpperCase();
        const isTypingField = ['INPUT', 'TEXTAREA', 'SELECT'].includes(activeTag) || document.activeElement?.isContentEditable;
        if (isTypingField && !document.activeElement?.dataset?.allowWedgeOverride) {
            return;
        }

        const now = performance.now();
        const delta = now - lastKeyTime;
        lastKeyTime = now;

        if (event.key === 'Enter' || event.key === 'Tab') {
            if (wedgeBuffer.length >= MIN_SCAN_LENGTH && delta <= WEDGE_THRESHOLD_MS * 2.5) {
                event.preventDefault();
                const scannedCode = wedgeBuffer.trim();
                wedgeBuffer = '';
                window.dispatchEvent(new CustomEvent('pos:hardware-scan', {
                    detail: { code: scannedCode, scannedAt: new Date().toISOString() }
                }));
                return;
            }
            wedgeBuffer = '';
            return;
        }

        if (event.key.length === 1 && !event.ctrlKey && !event.metaKey && !event.altKey) {
            wedgeBuffer = delta <= WEDGE_THRESHOLD_MS ? (wedgeBuffer + event.key) : event.key;
        }
    });

    // 2. Dual-Display / Customer-Facing Screen Sync via Local Browser BroadcastChannel (Zero External QR API)
    const channelName = `neriah_pos_dual_screen_\${organizationUlid}`;
    const dualChannel = typeof BroadcastChannel !== 'undefined' ? new BroadcastChannel(channelName) : null;

    return {
        channelName,
        broadcastCartToCustomerScreen(cartItems, totals, dynamicQrisString = null) {
            if (!dualChannel) return;
            dualChannel.postMessage({
                event: dynamicQrisString ? 'SHOW_DYNAMIC_QRIS' : 'CART_UPDATED',
                items: cartItems,
                totals,
                qris_payload: dynamicQrisString,
                updated_at: new Date().toISOString(),
            });
        },
        listenOnCustomerFacingScreen(callback) {
            if (!dualChannel) return () => {};
            const handler = (msg) => callback(msg.data);
            dualChannel.addEventListener('message', handler);
            return () => dualChannel.removeEventListener('message', handler);
        },
    };
}
JS;
    }

    /**
     * ELEMENT #4: Self-Healing Error Dictionary (`SYNTAX_LOCK_2026.md`) tailored to the exact chosen Stack (`M1..S2`).
     *
     * @param  array<string, mixed>  $schema
     */
    public static function renderSyntaxLock2026Markdown(array $schema): string
    {
        $appName = (string) ($schema['project_metadata']['name'] ?? 'Enterprise Application');
        $stackCode = (string) ($schema['project_metadata']['stack_preset'] ?? 'STACK_M1_LARAVEL_MONOLITH');
        $stackName = (string) ($schema['stack_architecture']['name'] ?? $stackCode);
        $wsBroker = (string) ($schema['realtime_websocket_contract']['selected_broker'] ?? 'Laravel Reverb');

        $stackSpecificSection = match (true) {
            str_contains($stackCode, 'M3') => <<<'MD'
## 2. STACK-SPECIFIC LOCK: NEXT.JS 15 APP ROUTER + TRPC v11 + PRISMA PG16 (`M3`)
- **ULID Primary Keys**: Use `@id @default(ulid()) @db.VarChar(26)` in `schema.prisma` — NEVER `@db.Uuid` with 26-char ULIDs (`22P02` crash in PostgreSQL 16).
- **Cursor Pagination**: Always pass `cursor: { id: lastUlid }, take: 25, skip: 1, orderBy: { id: 'asc' }` — NEVER use `skip: page * limit` offset pagination.
- **Next.js 15 Async Request APIs**: `cookies()`, `headers()`, `params`, and `searchParams` are asynchronous Promises in Next.js 15 (`const { slug } = await params;`).
- **Decimal Money**: Map `Decimal(19, 4)` via `Prisma.Decimal` (`decimal.js`), NEVER convert money columns with `Number(row.amount)`.
MD,
            str_contains($stackCode, 'M4') => <<<'MD'
## 2. STACK-SPECIFIC LOCK: DJANGO 5.2 + HTMX + PSYCOPG3 + PG16 (`M4`)
- **ULID Field**: Use `CharField(max_length=26, primary_key=True, default=generate_ulid, editable=False)` — NEVER `UUIDField` for 26-char ULIDs.
- **Strict Zero N+1**: Wrap querysets with `.select_related(...)` and `.prefetch_related(...)`; enable `django-zen-queries` / strict lazy-query assertion in pytest.
- **Concurrency Lock**: Always acquire `.select_for_update(nowait=False)` ordered by `id` inside `with transaction.atomic():`.
- **Zero High-Frequency Polling**: NEVER use `hx-trigger="every 2s"` on shared boards; use Django Channels WebSocket (`hx-ext="ws"`).
MD,
            str_contains($stackCode, 'S1'), str_contains($stackCode, 'S2'), str_contains($stackCode, 'D2') => <<<'MD'
## 2. STACK-SPECIFIC LOCK: GO / NESTJS / DISTRIBUTED MICROSERVICES (`D2 / S1 / S2`)
- **PostgreSQL 16 `pgx/v5` & ULID**: Store ULIDs as `CHAR(26)` / `VARCHAR(26)` (`oklog/ulid/v2`), NEVER `UUID` column type unless explicitly binary-encoded.
- **Money Arithmetic**: Use `shopspring/decimal` (Go) or `decimal.js` (TypeScript) mapped to `NUMERIC(19,4)` — NEVER `float64` / `number`.
- **Transactional Outbox**: Publish NATS JetStream / Kafka events strictly from `sys_integration_logs` outbox relay inside the same DB transaction.
MD,
            default => <<<'MD'
## 2. STACK-SPECIFIC LOCK: LARAVEL 13 + FILAMENT v5 + LIVEWIRE 4 + ALPINE.JS (`M1 / M2 / D1`)
- **Filament v5 `Schema` Signature (FATAL TYPE ERROR IF VIOLATED)**:
  - CORRECT: `public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema` (and `public function form(\Filament\Schemas\Schema $form): \Filament\Schemas\Schema` on Pages).
  - FORBIDDEN: `Filament\Forms\Form` (removed in Filament v5!).
- **Filament v5 Layout, Actions & Utilities Namespaces**:
  - Layout components (`Section`, `Grid`, `Group`, `Tabs`, `Actions`) live under `Filament\Schemas\Components\*`.
  - Reactive state utilities `Get` & `Set` live under `Filament\Schemas\Components\Utilities\Get` and `Filament\Schemas\Components\Utilities\Set` — NEVER `Filament\Forms\Get` or `Filament\Forms\Set`!
  - Table record & bulk actions (`EditAction`, `DeleteAction`, `ViewAction`, `BulkActionGroup`, `DeleteBulkAction`) live under `Filament\Actions\*` — NEVER `Filament\Tables\Actions\*`!
  - Max content width uses enum `Filament\Support\Enums\Width::Full` — NEVER legacy string `'full'`!
- **Filament v5 Strict Property Types on `Page` & `Resource`**:
  - `protected static ?string $title = '...';`
  - `protected static ?string $navigationLabel = '...';`
  - `protected static string | \BackedEnum | null $navigationIcon = '...';`
  - `protected static string | \UnitEnum | null $navigationGroup = '...';`
- **Livewire 4 Hydration & FilePond Upload Safety**:
  - Use `#[Locked]` attribute on ULID/ID public properties (`#[Locked] public string $recordUlid;`) so clients cannot tamper with state payloads.
  - NEVER combine `->live(onBlur: true)` on text inputs inside the same unisolated form as `FileUpload` (causes temporary FilePond state wipe).
  - Enforce `session.upload_progress.enabled = 0` in `php.ini` / FrankenPHP PHP configuration to prevent upload session race crashes.
- **Tier-1 Bilingual Standard (`TranslatableTabs`)**:
  - NEVER use page-corner `LocaleSwitcher::make()` or `LaraZeus\SpatieTranslatable`.
  - Store Tier-1 bilingual fields as JSON `{"id": "...", "en": "..."}` cast as `'array'` and edit via in-place `TranslatableTabs::make(...)` + `HasTranslatableTabs`.
- **Alpine.js Modal & Teleport Discipline**:
  - Always use `<template x-teleport="body">` for custom modals inside overflow-hidden containers to prevent z-index clipping.
MD,
        };

        return <<<MD
# SYNTAX_LOCK_2026.md — SELF-HEALING EXACT SYNTAX CHEATSHEET
> **Application:** `{$appName}` | **Locked Stack:** `{$stackName}` (`{$stackCode}`)
> **Lock Version:** `2026.7.0-FREEZE` | **Realtime Broker:** `{$wsBroker}`
> **Mandatory Agent Rule:** Read this file BEFORE writing or debugging any code in Part 1.0 through Part 7.0. Training-data guesses from 2023–2025 are OVERRIDDEN by the exact contracts below.

## 1. UNIVERSAL DATABASE, CONCURRENCY & RUNTIME LOCKS (ALL STACKS)
1. **PostgreSQL 16 Strict ULID Primary Keys**:
   - CORRECT: `\$table->ulid('id')->primary();` + `use HasUlids;` + `\$table->foreignUlid('organization_id');`
   - FORBIDDEN: `\$table->id()` (AUTO_INCREMENT) and FORBIDDEN `\$table->uuid('id')` with 26-char ULID (`SQLSTATE[22P02]: invalid input syntax for type uuid`).
2. **Strict Zero N+1 & Tenant Composite Indexes**:
   - Activate `Model::shouldBeStrict(! \$this->app->isProduction());` inside `AppServiceProvider::boot()`.
   - Every query rendering relations in tables/cards MUST eager-load via `->with([...])`.
   - Every tenant table MUST declare `\$table->index(['organization_id', 'status', 'created_at']);` and `\$table->index(['organization_id', 'id']);`.
3. **O(1) Keyset Cursor Pagination ONLY**:
   - CORRECT: `->orderBy('id', 'desc')->cursorPaginate(25)`
   - FORBIDDEN: `->paginate(25)` (`OFFSET` degrades linearly at 1M+ rows).
4. **Zero High-Frequency Polling (`wire:poll` < 30s Ban)**:
   - FORBIDDEN: `wire:poll`, `wire:poll.2s`, `wire:poll.5s`, `wire:poll.10s` on shared operational screens.
   - CORRECT: Broadcast `ShouldBroadcastNow` events over Private Tenant Channel `organization.{organization_ulid}.{module}` via **{$wsBroker}**.
5. **Zero-Slop UI & Local Asset Sovereignty**:
   - FORBIDDEN: `rounded-full` on buttons/badges/inputs, multi-color gradients (`bg-gradient-to-r`), `window.alert()`/`confirm()`/`prompt()`, and external CDNs (`cdn.jsdelivr.net`, `unpkg.com`, external QR image APIs).
   - CORRECT: `rounded-none` / `rounded-xs` / `rounded-sm` / `rounded-md`, solid crisp borders, `tabular-nums`, `window.showToast(...)`, and local `public/js/vendor/*` assets.

{$stackSpecificSection}

## 3. FRANKENPHP + RAILPACK + DEPLOYMENT LOCK (`Caddyfile` & `php.ini`)
- Set `session.upload_progress.enabled = 0` in `php.ini` so multipart FilePond / MarkItDown uploads never corrupt PHP worker sessions.
- Reset request-scoped static caches between FrankenPHP worker requests; never store tenant ULIDs in static class properties without clearing them via Laravel Octane/FrankenPHP request termination listeners.
- Use PostgreSQL-native date extraction `EXTRACT(YEAR FROM created_at)` or `TO_CHAR(created_at, 'YYYY-MM')` — NEVER MySQL `YEAR(created_at)` or `DATE_FORMAT(...)`.
MD;
    }

    /**
     * ELEMENT #4 (Companion): `TROUBLESHOOTING_INSTINCTS.md` — Rapid symptom -> root-cause -> deterministic fix matrix.
     *
     * @param  array<string, mixed>  $schema
     */
    public static function renderTroubleshootingInstinctsMarkdown(array $schema): string
    {
        $appName = (string) ($schema['project_metadata']['name'] ?? 'Enterprise Application');

        return <<<MD
# TROUBLESHOOTING_INSTINCTS.md — SELF-HEALING ERROR DICTIONARY ({$appName})

| # | Exact Error / Symptom | Root Cause (2026 Stack Drift) | Immediate Deterministic Fix |
| :---: | :--- | :--- | :--- |
| 1 | `TypeError: Argument #1 (\$form) must be of type Filament\\Forms\\Form, Filament\\Schemas\\Schema given` | Legacy Filament v3/v4 `Form` signature used in Filament v5. | Change method signature & import to `\\Filament\\Schemas\\Schema`. |
| 2 | `Error: Class "Filament\\Forms\\Get" not found` or `Filament\\Tables\\Actions\\EditAction not found` | Moved namespaces in Filament v5. | Use `Filament\\Schemas\\Components\\Utilities\\Get` (`Set`) and `Filament\\Actions\\EditAction`. |
| 3 | `TypeError: Cannot assign string to property ...\$maxContentWidth of type Width` | Passing `'full'` string instead of Filament v5 Enum. | Return/assign `\\Filament\\Support\\Enums\\Width::Full`. |
| 4 | `SQLSTATE[22P02]: Invalid text representation: invalid input syntax for type uuid` | Using `\$table->uuid('id')` for a 26-char `HasUlids` model or passing `""` into JSONB. | Change migration column to `\$table->ulid('id')->primary()` / `foreignUlid()` and pass `null` or valid array to JSONB. |
| 5 | `Illuminate\\Database\\LazyLoadingViolationException: Attempted to lazy load [...] on model [...]` | Missing `->with([...])` caught by `Model::shouldBeStrict()`. | Add explicit `->with(['relation'])` on the Eloquent query or Filament Resource `getEloquentQuery()`. |
| 6 | PostgreSQL CPU spikes > 85% when 50+ staff open KDS / Queue / Calendar | Accidental `wire:poll.2s` or `setInterval` polling loop. | Remove `wire:poll`; dispatch `ShouldBroadcastNow` to `PrivateChannel("organization.{\$orgId}.{\$module}")`. |
| 7 | FileUpload disappears or throws 500 during Livewire 4 form typing | `->live(onBlur: true)` re-hydrating sibling `FileUpload` or `session.upload_progress.enabled=1`. | Remove `->live()` next to `FileUpload` (use Alpine client-side state) and set `session.upload_progress.enabled = 0`. |
| 8 | Barcode scanner misses characters or requires clicking search box | Relying on focused `<input>` instead of Keyboard Wedge cadence listener. | Mount `registerProjectOsHardwareBridge(orgUlid)` listening for `< 30ms` keystrokes + `Enter`. |
MD;
    }

    /**
     * ELEMENT #5: Auto-Generated Interactive Onboarding Tour & Role-Based Client SOP (Reactive to L1–L4 Menus + State Machines).
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    public static function buildRoleBasedClientSopAndOnboarding(array $schema, ?string $locale = null): array
    {
        $locale = in_array($locale, ['id', 'en'], true) ? $locale : 'id';
        $appName = (string) ($schema['project_metadata']['name'] ?? 'Enterprise Application');
        $sectorCode = (string) ($schema['project_metadata']['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL');
        $menuMatrix = is_array($schema['enterprise_menu_matrix'] ?? null)
            ? $schema['enterprise_menu_matrix']
            : PosEnterpriseGuaranteeCatalog::enterpriseMenuMatrixForSector($sectorCode);
        $stateMachines = is_array($schema['state_machines'] ?? null) ? $schema['state_machines'] : [];
        $hwContract = is_array($schema['hardware_iot_peripheral_contract'] ?? null)
            ? $schema['hardware_iot_peripheral_contract']
            : self::hardwarePeripheralContractForSector($sectorCode, [], $menuMatrix, $locale);

        $roleMap = [
            'DIV_01_FRONT_OFFICE' => [
                'role_code' => 'ROLE_FRONT_OFFICE_CASHIER',
                'role_title' => [
                    'id' => 'Staf Garda Depan / Kasir / Resepsionis',
                    'en' => 'Front-Office / Cashier / Receptionist Staff',
                ],
                'daily_mission' => [
                    'id' => 'Membuka shift harian, melayani transaksi cepat dengan Scanner Barcode (<30ms) & Layar Pelanggan QRIS, menangani pembatalan/retur resmi dengan Reason + Bukti, serta menutup kasir harian.',
                    'en' => 'Open daily shift, process fast transactions via Barcode Wedge (<30ms) & Customer QRIS Screen, handle compliant voids/returns with Reason + Evidence, and close daily shift.',
                ],
            ],
            'DIV_02_CORE_OPERATIONS' => [
                'role_code' => 'ROLE_CORE_OPERATIONS_SPECIALIST',
                'role_title' => [
                    'id' => 'Staf Operasional Inti (Dapur / Medis / Lapangan / Produksi)',
                    'en' => 'Core Operations Specialist (Kitchen / Clinical / Field / Shopfloor)',
                ],
                'daily_mission' => [
                    'id' => 'Memantau papan kerja real-time (WebSocket instan tanpa tombol refresh), mengeksekusi transisi status sesuai State Machine resmi, dan menjaga SLA penyelesaian layanan.',
                    'en' => 'Monitor live WebSocket operational boards (zero manual refresh), execute guarded State Machine transitions, and uphold service SLA targets.',
                ],
            ],
            'DIV_03_INVENTORY_SUPPLY_CHAIN' => [
                'role_code' => 'ROLE_INVENTORY_WAREHOUSE_ADMIN',
                'role_title' => [
                    'id' => 'Admin Gudang, Pengadaan & Kontrol Aset',
                    'en' => 'Warehouse, Procurement & Asset Control Admin',
                ],
                'daily_mission' => [
                    'id' => 'Menjalankan alur PR -> PO -> Penerimaan Barang (GRN), memantau stok batch FEFO/FIFO terdekat kedaluwarsa, serta mengajukan Stock Opname via persetujuan Supervisor (Maker-Checker).',
                    'en' => 'Execute PR -> PO -> Goods Receipt (GRN) workflows, monitor FEFO/FIFO batch expiry alerts, and submit Stock Opname adjustments via Supervisor Maker-Checker approval.',
                ],
            ],
            'DIV_04_FINANCE_ACCOUNTING' => [
                'role_code' => 'ROLE_FINANCE_TAX_ACCOUNTANT',
                'role_title' => [
                    'id' => 'Staf Keuangan, Akuntansi & Pajak',
                    'en' => 'Finance, Accounting & Tax Officer',
                ],
                'daily_mission' => [
                    'id' => 'Memverifikasi Buku Besar Double-Entry (Debit === Kredit), melakukan rekonsiliasi pembayaran, mengoreksi jurnal hanya lewat Jurnal Balik (Reversing Entry), dan mengunci Tutup Buku Fiskal bulanan.',
                    'en' => 'Verify Double-Entry General Ledger balance (Debit === Credit), reconcile settlements, post corrections strictly via Reversing Entries, and lock monthly Fiscal Periods.',
                ],
            ],
            'DIV_05_GOVERNANCE_INTELLIGENCE' => [
                'role_code' => 'ROLE_OWNER_SUPERVISOR_AUDITOR',
                'role_title' => [
                    'id' => 'Owner / Kepala Cabang / Supervisor & Auditor Internal',
                    'en' => 'Owner / Branch Manager / Supervisor & Internal Auditor',
                ],
                'daily_mission' => [
                    'id' => 'Mengotorisasi permintaan Maker-Checker (diskon >15%, void, refund), mengaudit jejak berantai SHA-256 AuditLog, me-replay Outbox webhook pihak ketiga bila perlu, dan berkonsultasi dengan AI Copilot RAG.',
                    'en' => 'Authorize Maker-Checker requests (>15% discount, void, refund), inspect SHA-256 chained AuditLogs, trigger 1-click Webhook Outbox replay when needed, and query the RAG AI Copilot.',
                ],
            ],
        ];

        $primarySm = $stateMachines[0] ?? null;
        $smSummaryId = $primarySm
            ? sprintf(
                'Alur Status Utama (%s): %s -> %s.',
                (string) ($primarySm['entity'] ?? 'Transaksi'),
                (string) ($primarySm['initial_state'] ?? 'DRAFT'),
                implode(' -> ', array_slice((array) ($primarySm['states'] ?? []), 1, 5))
            )
            : 'Ikuti tombol transisi status tervalidasi pada layar detail transaksi.';
        $smSummaryEn = $primarySm
            ? sprintf(
                'Primary Lifecycle (%s): %s -> %s.',
                (string) ($primarySm['entity'] ?? 'Transaction'),
                (string) ($primarySm['initial_state'] ?? 'DRAFT'),
                implode(' -> ', array_slice((array) ($primarySm['states'] ?? []), 1, 5))
            )
            : 'Follow guarded state transition buttons inside each transaction view.';

        $rolePlaybooks = [];
        $totalActiveSteps = 0;

        foreach (($menuMatrix['divisions'] ?? []) as $div) {
            $divCode = (string) ($div['division_code'] ?? 'DIV_01_FRONT_OFFICE');
            $meta = $roleMap[$divCode] ?? $roleMap['DIV_01_FRONT_OFFICE'];

            $activeMenus = array_values(array_filter(
                (array) ($div['menus'] ?? []),
                static fn (array $m): bool => ! empty($m['included_in_v1'])
            ));

            $steps = [];
            foreach ($activeMenus as $idx => $m) {
                $titleId = (string) ($m['title_i18n']['id'] ?? $m['title'] ?? $m['code']);
                $titleEn = (string) ($m['title_i18n']['en'] ?? PosEnterpriseGuaranteeCatalog::translateMenuTitleToEn($titleId));
                $routeSlug = (string) ($m['route_slug'] ?? '/admin');

                $steps[] = [
                    'step_number' => $idx + 1,
                    'menu_code' => (string) $m['code'],
                    'route_slug' => $routeSlug,
                    'title' => [
                        'id' => "Langkah " . ($idx + 1) . ": Buka menu \"{$titleId}\"",
                        'en' => 'Step ' . ($idx + 1) . ": Open \"{$titleEn}\"",
                    ],
                    'instruction' => [
                        'id' => "Akses {$routeSlug} untuk menjalankan {$titleId}. Sistem otomatis mencatat nomor dokumen atomik & jejak audit.",
                        'en' => "Navigate to {$routeSlug} to execute {$titleEn}. Atomic document numbering and audit trails run automatically.",
                    ],
                ];
                $totalActiveSteps++;
            }

            $rolePlaybooks[] = [
                'division_code' => $divCode,
                'role_code' => $meta['role_code'],
                'role_title' => $meta['role_title'],
                'localized_role_title' => $meta['role_title'][$locale] ?? $meta['role_title']['id'],
                'daily_mission' => $meta['daily_mission'],
                'localized_daily_mission' => $meta['daily_mission'][$locale] ?? $meta['daily_mission']['id'],
                'state_machine_note' => [
                    'id' => $smSummaryId,
                    'en' => $smSummaryEn,
                ],
                'active_menus_count' => count($activeMenus),
                'steps' => $steps,
            ];
        }

        return [
            'app_name' => $appName,
            'sector_code' => $sectorCode,
            'total_roles_covered' => count($rolePlaybooks),
            'role_count' => count($rolePlaybooks),
            'total_interactive_tour_steps' => $totalActiveSteps,
            'active_v1_menus_covered' => $totalActiveSteps,
            'active_hardware_peripherals' => (int) ($hwContract['active_peripherals_count'] ?? 0),
            'in_app_filament_page' => 'app/Filament/Pages/SmartGuide.php',
            'bast_training_manual_file' => '.agents/CLIENT_SOP_AND_TRAINING_MANUAL.md',
            'role_playbooks' => $rolePlaybooks,
        ];
    }

    /**
     * Render Filament v5 In-App Interactive Onboarding Page (`app/Filament/Pages/SmartGuide.php`) for exported client apps.
     *
     * @param  array<string, mixed>  $schema
     */
    public static function renderClientSmartGuidePhp(array $schema): string
    {
        $sop = self::buildRoleBasedClientSopAndOnboarding($schema, 'id');
        $appName = addslashes((string) ($sop['app_name'] ?? 'Enterprise App'));
        $exportedPlaybooks = var_export($sop['role_playbooks'], true);

        return <<<PHP
<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\Width;

/**
 * Auto-Generated by Neriah Pro Project OS v7.0 — Role-Based In-App Interactive SOP & Onboarding Tour.
 * Application: {$appName}
 */
class SmartGuide extends Page
{
    protected static string | \\BackedEnum | null \$navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string \$navigationLabel = 'Panduan SOP & Tour Staf';

    protected static ?string \$title = 'Panduan Operasional Interaktif per Role (SOP)';

    protected static string | \\UnitEnum | null \$navigationGroup = 'Governance & Bantuan';

    protected string \$view = 'filament.pages.smart-guide';

    public string \$activeRoleCode = 'ROLE_FRONT_OFFICE_CASHIER';

    public string \$localeMode = 'id';

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRolePlaybooks(): array
    {
        return {$exportedPlaybooks};
    }
}
PHP;
    }

    /**
     * Render BAST-Ready Client Operations & Staff Training Manual (`CLIENT_SOP_AND_TRAINING_MANUAL.md`) in ID or EN.
     *
     * @param  array<string, mixed>  $schema
     */
    public static function renderClientSopManualMarkdown(array $schema, ?string $locale = null): string
    {
        $locale = in_array($locale, ['id', 'en'], true) ? $locale : 'id';
        $sop = self::buildRoleBasedClientSopAndOnboarding($schema, $locale);
        $hw = is_array($schema['hardware_iot_peripheral_contract'] ?? null)
            ? $schema['hardware_iot_peripheral_contract']
            : self::hardwarePeripheralContractForSector((string) $sop['sector_code'], [], $schema['enterprise_menu_matrix'] ?? [], $locale);

        $isEn = $locale === 'en';
        $lines = [
            $isEn
                ? "# CLIENT OPERATIONS MANUAL, ROLE SOP & BAST TRAINING HANDOVER — {$sop['app_name']}"
                : "# BUKU MANUAL OPERASIONAL KLIEN, SOP PER DEPARTEMEN & MATERI TRAINING BAST — {$sop['app_name']}",
            '',
            $isEn
                ? "> **Sector:** `{$sop['sector_code']}` | **Covered Roles:** `{$sop['total_roles_covered']}` | **Interactive SOP Steps (`v1.0`):** `{$sop['total_interactive_tour_steps']}`"
                : "> **Sektor:** `{$sop['sector_code']}` | **Cakupan Role Staf:** `{$sop['total_roles_covered']}` | **Total Langkah SOP Aktif (`v1.0`):** `{$sop['total_interactive_tour_steps']}`",
            $isEn
                ? '> **In-App Guide:** Available inside `/admin/smart-guide` (`app/Filament/Pages/SmartGuide.php`).'
                : '> **Panduan In-App:** Tersedia langsung di dalam aplikasi pada menu `/admin/smart-guide` (`app/Filament/Pages/SmartGuide.php`).',
            '',
            $isEn ? '## PART 1: FIELD HARDWARE & REAL-TIME SCREEN PREPARATION' : '## BAGIAN 1: PERSIAPAN PERANGKAT KERAS FISIK & LAYAR REAL-TIME',
            '',
        ];

        foreach (($hw['peripherals'] ?? []) as $p) {
            $status = ! empty($p['active_by_default']) ? ($isEn ? 'ACTIVE IN THIS PROJECT' : 'AKTIF DI PROYEK INI') : ($isEn ? 'OPTIONAL READY' : 'OPSIONAL SIAP PAKAI');
            $lines[] = sprintf(
                '- **[%s] %s**: %s',
                $status,
                (string) ($p['title'][$locale] ?? $p['title']['id']),
                (string) ($p['contract'][$locale] ?? $p['contract']['id'])
            );
        }
        $lines[] = '';
        $lines[] = $isEn ? '## PART 2: STEP-BY-STEP DAILY SOP BY STAFF ROLE / DEPARTMENT' : '## BAGIAN 2: SOP HARIAN LANGKAH DEMI LANGKAH PER ROLE / DEPARTEMEN STAF';
        $lines[] = '';

        foreach ($sop['role_playbooks'] as $role) {
            $lines[] = '### ' . ($role['role_title'][$locale] ?? $role['role_title']['id']) . " (`{$role['role_code']}`)";
            $lines[] = ($isEn ? '- **Daily Mission:** ' : '- **Misi Harian:** ') . ($role['daily_mission'][$locale] ?? $role['daily_mission']['id']);
            $lines[] = ($isEn ? '- **Lifecycle Rule:** ' : '- **Aturan Status Transaksi:** ') . ($role['state_machine_note'][$locale] ?? $role['state_machine_note']['id']);
            foreach (($role['steps'] ?? []) as $step) {
                $lines[] = sprintf(
                    '  %d. **%s** (`%s`) — %s',
                    (int) $step['step_number'],
                    (string) ($step['title'][$locale] ?? $step['title']['id']),
                    (string) $step['menu_code'],
                    (string) ($step['instruction'][$locale] ?? $step['instruction']['id'])
                );
            }
            $lines[] = '';
        }

        $lines[] = $isEn ? '## PART 3: BAST TRAINING & HANDOVER SIGN-OFF CHECKLIST' : '## BAGIAN 3: DAFTAR PERIKSA PELATIHAN STAF & SERAH TERIMA BAST';
        $lines[] = $isEn
            ? '1. [ ] Front-Office / Cashier staff completed live simulation of Shift Open, Barcode Scan, Dual-Screen QRIS, and Shift Close.'
            : '1. [ ] Staf Garda Depan / Kasir telah mempraktikkan simulasi Buka Shift, Scan Barcode, Layar QRIS Pelanggan, dan Tutup Shift.';
        $lines[] = $isEn
            ? '2. [ ] Operations & Warehouse staff verified real-time WebSocket board updates and FEFO Stock Opname approval.'
            : '2. [ ] Staf Operasional & Gudang telah memverifikasi update layar WebSocket tanpa refresh serta alur persetujuan Stock Opname FEFO.';
        $lines[] = $isEn
            ? '3. [ ] Finance & Owner verified Reversing Entry correction, Monthly Fiscal Period Lock, and SHA-256 Audit Log verification.'
            : '3. [ ] Staf Keuangan & Owner telah memverifikasi koreksi Jurnal Balik, Kunci Periode Fiskal Bulanan, dan jejak Audit Log SHA-256.';

        return implode("\n", $lines) . "\n";
    }

    /**
     * Explicit Cross-Stage Causality Matrix proving every stage dynamically updates downstream artifacts.
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    public static function computeCrossStageCausalityGraph(array $schema, ?string $locale = null): array
    {
        $locale = in_array($locale, ['id', 'en'], true) ? $locale : 'id';
        $sector = (string) ($schema['project_metadata']['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL');
        $stack = (string) ($schema['project_metadata']['stack_preset'] ?? 'STACK_M1_LARAVEL_MONOLITH');
        $v1Menus = (int) ($schema['enterprise_menu_matrix']['included_v1_menus_count'] ?? 25);
        $tablesCount = count((array) ($schema['data_dictionary'] ?? []));
        $hwActive = (int) ($schema['hardware_iot_peripheral_contract']['active_peripherals_count'] ?? 1);
        $wsChannels = count((array) ($schema['realtime_websocket_contract']['channels'] ?? []))
            + count((array) ($schema['realtime_websocket_contract']['linked_realtime_v1_menus'] ?? []));
        $compositeIdx = (int) ($schema['strict_query_and_partition_contract']['total_composite_indexes_compiled'] ?? 0);
        $sopSteps = (int) ($schema['client_sop_and_onboarding']['total_interactive_tour_steps'] ?? $v1Menus);
        $visualVariant = (string) ($schema['visual_frontend_ui_contract']['selected_variant_code'] ?? $schema['project_metadata']['selected_ui_variant'] ?? 'DEFAULT_SECTOR_UI');
        $navShell = (string) ($schema['visual_frontend_ui_contract']['navigation_shell_code'] ?? $schema['project_metadata']['navigation_shell'] ?? 'SIDEBAR_ERP_DENSE');
        $publicUxPreset = (string) ($schema['dual_zone_ui_contract']['zone_b_public_frontstage']['selected_public_ux_preset'] ?? $schema['project_metadata']['public_ux_preset'] ?? '');
        $outputLocale = (string) ($schema['jurisdiction_compliance_profile']['locale_code'] ?? $schema['project_metadata']['output_locale'] ?? 'id');
        $jurisdictionCode = (string) ($schema['jurisdiction_compliance_profile']['jurisdiction_code'] ?? 'ID_NKRI_STATUTORY');
        $extraCoDesignHours = (int) ($schema['codesign_alignment_contract']['extra_codesign_man_hours'] ?? 0);
        $approvedCoDesignModules = (int) ($schema['codesign_alignment_contract']['approved_modules_count'] ?? 0);
        $worldClassTasksCount = (int) ($schema['world_class_task_matrix']['total_world_class_tasks'] ?? 15);

        $hashParts = [
            $sector,
            $stack,
            $v1Menus,
            $tablesCount,
            $hwActive,
            $wsChannels,
            $compositeIdx,
            $sopSteps,
            $visualVariant,
            $navShell,
            $outputLocale,
        ];
        if ($publicUxPreset !== '') {
            $hashParts[] = "pubux:{$publicUxPreset}";
        }
        if ($extraCoDesignHours > 0 || $approvedCoDesignModules > 0) {
            $hashParts[] = "codesign:{$approvedCoDesignModules}:{$extraCoDesignHours}";
        }
        $fullSha256 = hash('sha256', implode('|', $hashParts));
        $causalityHash = substr($fullSha256, 0, 16);

        return [
            'freeze_version' => self::FREEZE_VERSION,
            'architectural_freeze_score' => 100,
            'pillars_locked_count' => 15,
            'causality_signature' => 'pos_sync_' . $causalityHash,
            'causality_fingerprint_sha256' => $fullSha256,
            'live_metrics' => [
                'sector_code' => $sector,
                'stack_preset' => $stack,
                'selected_ui_variant' => $visualVariant,
                'navigation_shell' => $navShell,
                'public_ux_preset' => $publicUxPreset ?: 'UX_F2_KINETIC_ANIWALL',
                'output_locale' => $outputLocale,
                'jurisdiction_code' => $jurisdictionCode,
                'v1_enterprise_menus' => $v1Menus,
                'ulid_tables_compiled' => $tablesCount,
                'active_hardware_bridges' => $hwActive,
                'websocket_private_channels' => $wsChannels,
                'tenant_composite_indexes' => $compositeIdx,
                'role_sop_tour_steps' => $sopSteps,
                'world_class_tasks_compiled' => $worldClassTasksCount,
                'codesign_approved_modules' => $approvedCoDesignModules,
                'extra_codesign_man_hours' => $extraCoDesignHours,
            ],
            'stage_chain' => [
                [
                    'stage' => 'STAGE_1_SECTOR_AND_PRIMITIVES',
                    'triggers' => 'STAGE_2_L1_L4_MENUS_AND_HARDWARE',
                    'impact' => $locale === 'en'
                        ? "{$sector} activates {$v1Menus} ERP menus & {$hwActive} field hardware bridges."
                        : "{$sector} mengaktifkan {$v1Menus} menu ERP & {$hwActive} jembatan hardware fisik.",
                ],
                [
                    'stage' => 'STAGE_2_MENUS_AND_STACK',
                    'triggers' => 'STAGE_3_SCHEMA_WEBSOCKET_SYNTAX_LOCK',
                    'impact' => $locale === 'en'
                        ? "{$stack} locks SYNTAX_LOCK_2026.md, {$wsChannels} WebSocket channels, & {$compositeIdx} composite indexes across {$tablesCount} ULID tables."
                        : "{$stack} mengunci SYNTAX_LOCK_2026.md, {$wsChannels} kanal WebSocket, & {$compositeIdx} composite index pada {$tablesCount} tabel ULID.",
                ],
                [
                    'stage' => 'STAGE_3_DAG_AND_RUNTIME',
                    'triggers' => 'STAGE_4_SOP_RAB_SPK_BAST',
                    'impact' => $locale === 'en'
                        ? "Auto-updates Kahn DAG chunks, {$sopSteps}-step Role SmartGuide SOP, Algorithmic BoQ IDR, SHA-256 SPK Hash, and UAT/BAST pack."
                        : "Otomatis memperbarui pecahan DAG Kahn, {$sopSteps} langkah SOP SmartGuide per Role, RAB IDR, Hash SPK SHA-256, dan paket UAT/BAST.",
                ],
            ],
        ];
    }
}
