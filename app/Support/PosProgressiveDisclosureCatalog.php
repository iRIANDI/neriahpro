<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Project OS v12.0 — Progressive Disclosure Flow, Zero-Leak Tier Matrix,
 * Contextual Upsell Scenarios & Anti-Over-Planning (Scope Paralysis) Guard Catalog.
 */
final class PosProgressiveDisclosureCatalog
{
    /**
     * 3-Stage Progressive Disclosure Flow ("Show the Full Ferrari, Lock the Ignition").
     *
     * @return array<int, array<string, mixed>>
     */
    public static function disclosureStages(): array
    {
        return [
            [
                'stage_number' => 1,
                'stage_code' => 'STAGE_A_SMART_INTAKE',
                'badge' => 'TAHAP A // INTAKE TANPA PUSING',
                'name' => [
                    'id' => 'Smart Idea Intake & Auto-Mapping (Deteksi Sektor & Modul Otomatis)',
                    'en' => 'Smart Idea Intake & Auto-Mapping (Automatic Sector & Module Detection)',
                ],
                'target_persona' => [
                    'id' => 'Klien Awam, Pemilik Bisnis UMKM, Founder Startup & Vibe Coder Non-Teknis',
                    'en' => 'Non-Technical Founders, SME Owners, Startup Builders & Vibe Coders',
                ],
                'summary' => [
                    'id' => 'Klien cukup mengetik ide bebas dalam bahasa sehari-hari lalu menekan tombol [✨ Deteksi Sektor & Modul Otomatis dari Ide Saya]. Sistem otomatis memetakan Sektor #01–#12, Primitif Transaksi, Peta Menu L1–L4, dan Zona UI A+B tanpa perlu menghafal istilah teknis.',
                    'en' => 'Clients simply type their idea in everyday language and click [✨ Auto-Detect Sector & Modules from My Idea]. The engine deterministically maps Sector #01–#12, Transaction Primitives, L1–L4 Menu Map, and Zone A+B UI without requiring technical jargon.',
                ],
                'cta_label' => [
                    'id' => '✨ Deteksi Sektor & Modul Otomatis dari Ide Saya',
                    'en' => '✨ Auto-Detect Sector & Modules from My Idea',
                ],
            ],
            [
                'stage_number' => 2,
                'stage_code' => 'STAGE_B_PROPOSAL_WOW',
                'badge' => 'TAHAP B // LAYAR 1: RUANG KAGUM & VALIDASI BISNIS',
                'name' => [
                    'id' => 'Layar 1: Website Architecture Proposal (Executive Summary & Peta Menu Bisnis)',
                    'en' => 'Screen 1: Website Architecture Proposal (Executive Summary & Business Menu Map)',
                ],
                'target_persona' => [
                    'id' => 'Pengambil Keputusan Bisnis, Direktur, Yayasan/CSR & Klien Mitra Proyek',
                    'en' => 'Business Decision Makers, Directors, Non-Profit/CSR Boards & Turnkey Project Clients',
                ],
                'summary' => [
                    'id' => 'Sebelum melihat kode SQL atau JSON teknis, klien disambut dengan Proposal Arsitektur Bisnis yang memukau: Ringkasan Eksekutif ramah manusia, Peta Menu 5 Divisi Operasional (L1–L4) yang langsung terbuka, Pratinjau Bentuk Layar (Zona A + Zona B), serta Kalkulasi Nilai Ekonomis Proyek (jam kerja & biaya rekayasa yang dihemat).',
                    'en' => 'Before seeing any technical SQL or JSON, clients are welcomed by an impressive Business Architecture Proposal: human-friendly Executive Summary, auto-expanded 5-Division Operational Menu Map (L1–L4), Screen Layout Preview (Zone A + Zone B), and Project Economic Value Calculator (engineering hours & Rupiah saved).',
                ],
                'cta_label' => [
                    'id' => '🏛️ Buka Layar 1: Proposal Arsitektur Bisnis (WOW View)',
                    'en' => '🏛️ Open Screen 1: Business Architecture Proposal (WOW View)',
                ],
            ],
            [
                'stage_number' => 3,
                'stage_code' => 'STAGE_C_DUAL_STUDIO',
                'badge' => 'TAHAP C // LAYAR 2: 3-PANE STUDIO MODE GANDA',
                'name' => [
                    'id' => 'Layar 2: 3-Pane Studio Dual-View (Vibe Coder Guided View vs Architect Pro View)',
                    'en' => 'Screen 2: Dual-View 3-Pane Studio (Vibe Coder Guided View vs Architect Pro View)',
                ],
                'target_persona' => [
                    'id' => 'Vibe Coder (Cursor/Windsurf/Claude Code/Antigravity) & Principal Software Architect',
                    'en' => 'Vibe Coders (Cursor/Windsurf/Claude Code/Antigravity) & Principal Software Architects',
                ],
                'summary' => [
                    'id' => 'Mode Terpandu (Vibe Coder Guided View) memandu eksekusi langkah demi langkah dengan instruksi tempel-jalan untuk AI IDE, sedangkan Mode Arsitek Pro membuka penuh Data Dictionary ULID, State Machine Guards, SYMBOL_MANIFEST.json, dan DAG Topological Chunker.',
                    'en' => 'Vibe Coder Guided View walks users step by step with copy-paste AI IDE instructions, while Architect Pro View unlocks full ULID Data Dictionary, State Machine Guards, SYMBOL_MANIFEST.json, and DAG Topological Chunker.',
                ],
                'cta_label' => [
                    'id' => '⚡ Lanjut ke Layar 2: 3-Pane Studio (Eksekusi AI)',
                    'en' => '⚡ Proceed to Screen 2: 3-Pane Studio (AI Execution)',
                ],
            ],
        ];
    }

    /**
     * Zero-Leak Server-Side Entitlement Tier Matrix.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function tierEntitlementMatrix(): array
    {
        return [
            'FREE_SANDBOX' => [
                'tier_code' => 'FREE_SANDBOX',
                'package_slugs' => ['retail_spark', 'spark', 'free_sandbox'],
                'price_label' => [
                    'id' => 'Rp 0 (Spark Guest Sandbox • Kuota 2x/Bulan)',
                    'en' => 'IDR 0 (Spark Guest Sandbox • 2x/Month Quota)',
                ],
                'unlocked_phases' => [1],
                'max_unlocked_tables' => 2,
                'allow_symbol_manifest_full' => false,
                'allow_canary_ip_seal' => false,
                'allow_cli_export_zip' => false,
                'zero_leak_policy' => [
                    'id' => 'Server-Side Zero-Leak Gate Aktif: Fase 2.2 s/d Fase 7.0 TIDAK PERNAH dikompilasi atau dikirim ke DOM HTML browser pada tier gratis (0% kebocoran Inspect Element F12).',
                    'en' => 'Server-Side Zero-Leak Gate Active: Phases 2.2 through 7.0 are NEVER compiled or sent to the browser HTML DOM on the free tier (0% F12 Inspect Element leak).',
                ],
            ],
            'SINGLE_APP_PASS' => [
                'tier_code' => 'SINGLE_APP_PASS',
                'package_slugs' => ['retail_single_app'],
                'price_label' => [
                    'id' => 'Rp 299.000 (Sekali Bayar • Kunci 1 Aplikasi Permanen)',
                    'en' => 'IDR 299,000 (One-Time Pass • Lock 1 App Permanently)',
                ],
                'unlocked_phases' => [1, 2, 3, 4, 5, 6, 7],
                'max_unlocked_tables' => 999,
                'allow_symbol_manifest_full' => true,
                'allow_canary_ip_seal' => false,
                'allow_cli_export_zip' => true,
                'zero_leak_policy' => [
                    'id' => 'Seluruh 7 Fase Prompt AI, Tabel ULID & SYMBOL_MANIFEST.json terbuka penuh untuk 1 proyek aplikasi terkunci.',
                    'en' => 'All 7 AI Prompt Phases, ULID Tables & SYMBOL_MANIFEST.json are fully unlocked for 1 locked application project.',
                ],
            ],
            'PRO_RETAIL' => [
                'tier_code' => 'PRO_RETAIL',
                'package_slugs' => ['retail_starter', 'retail_pro', 'impact_nonprofit_grant', 'pro'],
                'price_label' => [
                    'id' => 'Rp 149.000 – Rp 399.000 / Bulan (Starter & Pro Agentic / CSR Terverifikasi)',
                    'en' => 'IDR 149,000 – IDR 399,000 / Month (Starter & Pro Agentic / Verified CSR)',
                ],
                'unlocked_phases' => [1, 2, 3, 4, 5, 6, 7],
                'max_unlocked_tables' => 999,
                'allow_symbol_manifest_full' => true,
                'allow_canary_ip_seal' => true,
                'allow_cli_export_zip' => true,
                'zero_leak_policy' => [
                    'id' => 'Akses penuh 7 Fase Prompt AI, Canary IP Watermark Seal, 20 Pilar Paripurna & CLI /init Bundle.',
                    'en' => 'Full access to all 7 AI Prompt Phases, Canary IP Watermark Seal, 20 Complete Pillars & CLI /init Bundle.',
                ],
            ],
            'AGENCY_PARTNER' => [
                'tier_code' => 'AGENCY_PARTNER',
                'package_slugs' => ['retail_agency', 'retail_whitelabel', 'agency'],
                'price_label' => [
                    'id' => 'Rp 990.000 – Rp 1.490.000 / Bulan (Agency Partner & Enterprise White-Label)',
                    'en' => 'IDR 990,000 – IDR 1,490,000 / Month (Agency Partner & Enterprise White-Label)',
                ],
                'unlocked_phases' => [1, 2, 3, 4, 5, 6, 7],
                'max_unlocked_tables' => 999,
                'allow_symbol_manifest_full' => true,
                'allow_canary_ip_seal' => true,
                'allow_cli_export_zip' => true,
                'zero_leak_policy' => [
                    'id' => 'Kuota Tanpa Batas (Unlimited), Multi-Seat RBAC, Ekspor Proposal PDF/ZIP White-Label Tanpa Watermark NeriahPro.',
                    'en' => 'Unlimited Quota, Multi-Seat RBAC, White-Label PDF/ZIP Proposal Export Without NeriahPro Watermark.',
                ],
            ],
            'MITRA_TURNKEY' => [
                'tier_code' => 'MITRA_TURNKEY',
                'package_slugs' => ['advisory', 'monolith', 'enterprise', 'mitra_turnkey'],
                'price_label' => [
                    'id' => 'Mulai Rp 2.500.000 – Rp 120.000.000+ (Mitra Proyek Turnkey • Termin 30%-50%-20%)',
                    'en' => 'From IDR 2,500,000 – IDR 120,000,000+ (Turnkey Project Partner • 30%-50%-20% Milestones)',
                ],
                'unlocked_phases' => [1, 2, 3, 4, 5, 6, 7],
                'max_unlocked_tables' => 999,
                'allow_symbol_manifest_full' => true,
                'allow_canary_ip_seal' => true,
                'allow_cli_export_zip' => true,
                'zero_leak_policy' => [
                    'id' => '100% Dikerjakan Tim Senior Architect NeriahPro + Kontrak SPK SHA-256 + Live Staging + BAST Digital.',
                    'en' => '100% Engineered by NeriahPro Senior Architects + SHA-256 SPK Contract + Live Staging + Digital Handover.',
                ],
            ],
        ];
    }

    /**
     * Contextual Risk & Value Upsell Scenarios per Phase (1..7).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function phaseUpsellCatalog(): array
    {
        return [
            2 => [
                'phase' => 2,
                'title' => [
                    'id' => '🔒 FASE 2.2+ TERKUNCI: Skema Database ULID Inti & Penjaga Integritas Transaksi',
                    'en' => '🔒 PHASE 2.2+ LOCKED: Core ULID Database Schema & Transaction Integrity Guards',
                ],
                'risk_warning' => [
                    'id' => '⚠️ Peringatan Arsitek: Tanpa skema tabel Fase 2.2+ dan Foreign Key ULID presisi, AI koding Anda (Cursor/Claude) akan menebak-nebak nama kolom (halusinasi simbol) dan menyebabkan error SQLSTATE[22P02] / relasi yatim saat produksi.',
                    'en' => '⚠️ Architect Warning: Without Phase 2.2+ table schemas and strict ULID Foreign Keys, your coding AI (Cursor/Claude) will hallucinate column names and cause SQLSTATE[22P02] errors or orphaned records in production.',
                ],
                'value_proposition' => [
                    'id' => 'Buka seluruh definisi tabel PostgreSQL 16 Strict ULID, indeks komposit O(1), dan enum state machine resmi untuk sektor Anda.',
                    'en' => 'Unlock all PostgreSQL 16 Strict ULID table definitions, O(1) composite indexes, and official state machine enums for your sector.',
                ],
            ],
            3 => [
                'phase' => 3,
                'title' => [
                    'id' => '🔒 FASE 3 TERKUNCI: Pelindung Transaksi Anti-Bentrok & Double-Entry Ledger',
                    'en' => '🔒 PHASE 3 LOCKED: Anti-Collision Transaction Guard & Double-Entry Ledger',
                ],
                'risk_warning' => [
                    'id' => '⚠️ Peringatan Arsitek: Tanpa kode pengunci baris atomik (SELECT ... FOR UPDATE) dan State Machine Guard di Fase 3 ini, sistem Anda berisiko mengalami Double-Booking, saldo minus, atau duplikasi webhook pembayaran saat 2 pelanggan membayar di detik yang sama!',
                    'en' => '⚠️ Architect Warning: Without atomic row locking (SELECT ... FOR UPDATE) and State Machine Guards in Phase 3, your system risks Double-Booking, negative balances, or duplicate payment webhooks when 2 users pay at the exact same second!',
                ],
                'value_proposition' => [
                    'id' => 'Dapatkan Service Action atomik, Idempotency Key Middleware, dan validasi kas seimbang (SUM debit = SUM credit) siap tempel.',
                    'en' => 'Get ready-to-paste atomic Service Actions, Idempotency Key Middleware, and balanced ledger validation (SUM debit = SUM credit).',
                ],
            ],
            4 => [
                'phase' => 4,
                'title' => [
                    'id' => '🔒 FASE 4 TERKUNCI: Kokpit Operasional Zona A & Scrollytelling Publik Zona B',
                    'en' => '🔒 PHASE 4 LOCKED: Zone A Operational Cockpit & Zone B Public Scrollytelling',
                ],
                'risk_warning' => [
                    'id' => '⚠️ Peringatan Arsitek: Tanpa kontrak komponen UI Fase 4, AI cenderung menghasilkan antarmuka generik ("AI Slop") yang membingungkan kasir/staf operasional dan gagal lulus standar kontras WCAG AAA.',
                    'en' => '⚠️ Architect Warning: Without Phase 4 UI component contracts, AI tends to generate generic interfaces ("AI Slop") that confuse operational staff and fail WCAG AAA contrast standards.',
                ],
                'value_proposition' => [
                    'id' => 'Buka cetak biru 36 Varian Visual UI Sektoral (Zona A Kerja Cepat + Zona B Halaman Publik Interaktif 60fps).',
                    'en' => 'Unlock the 36 Sectoral Visual UI Variant blueprints (Zone A High-Speed Workspace + Zone B 60fps Interactive Public Web).',
                ],
            ],
            5 => [
                'phase' => 5,
                'title' => [
                    'id' => '🔒 FASE 5 TERKUNCI: Enkripsi UU PDP (AES-256-GCM + Blind Index) & RBAC 5-Divisi',
                    'en' => '🔒 PHASE 5 LOCKED: Privacy Law Encryption (AES-256-GCM + Blind Index) & 5-Division RBAC',
                ],
                'risk_warning' => [
                    'id' => '⚠️ Peringatan Arsitek: Menyimpan NIK, Rekam Medis, atau Nomor Telepon dalam teks biasa melanggar UU PDP No. 27/2022 dan membuka celah kebocoran data fatal.',
                    'en' => '⚠️ Architect Warning: Storing National IDs, Medical Records, or Phone Numbers in plaintext violates Data Protection Laws and exposes fatal data breach liabilities.',
                ],
                'value_proposition' => [
                    'id' => 'Aktifkan trait enkripsi otomatis beserta kolom pencarian HMAC-SHA256 Blind Index O(1) dan matriks hak akses 5 divisi.',
                    'en' => 'Activate automatic encryption traits with O(1) HMAC-SHA256 Blind Index search columns and 5-division RBAC matrices.',
                ],
            ],
            6 => [
                'phase' => 6,
                'title' => [
                    'id' => '🔒 FASE 6 TERKUNCI: SEO/GEO Top-3 AI Citation (llms.txt + JSON-LD) & Docker Hardened',
                    'en' => '🔒 PHASE 6 LOCKED: Top-3 AI Citation SEO/GEO (llms.txt + JSON-LD) & Hardened Docker',
                ],
                'risk_warning' => [
                    'id' => '⚠️ Peringatan Arsitek: Website modern yang tidak memiliki endpoint /llms.txt dan @graph Schema.org sektoral akan tidak terlihat (invisible) di mesin jawaban AI seperti ChatGPT Search, Perplexity, dan Gemini.',
                    'en' => '⚠️ Architect Warning: Modern websites lacking /llms.txt endpoints and sectoral Schema.org @graph markup remain invisible to AI answer engines like ChatGPT Search, Perplexity, and Gemini.',
                ],
                'value_proposition' => [
                    'id' => 'Buka generator otomatis /llms.txt, JSON-LD @graph spesifik sektor, serta konfigurasi Docker Octane/PostgreSQL 16 siap deploy.',
                    'en' => 'Unlock automatic /llms.txt generators, sector-specific JSON-LD @graph, and deploy-ready Octane/PostgreSQL 16 Docker configs.',
                ],
            ],
            7 => [
                'phase' => 7,
                'title' => [
                    'id' => '🔒 FASE 7 TERKUNCI: Pest API Contract Suite, Canary IP Seal & Checklist UAT/BAST',
                    'en' => '🔒 PHASE 7 LOCKED: Pest API Contract Suite, Canary IP Seal & UAT/Handover Checklist',
                ],
                'risk_warning' => [
                    'id' => '⚠️ Peringatan Arsitek: Merilis aplikasi ke klien tanpa automated test suite dan segel kriptografi Canary IP membuat kode Anda rentan regresi dan pembajakan lisensi.',
                    'en' => '⚠️ Architect Warning: Releasing applications without an automated test suite and Canary IP cryptographic seals leaves your code vulnerable to regressions and license piracy.',
                ],
                'value_proposition' => [
                    'id' => 'Buka suite pengujian otomatis 8 dimensi, segel kepemilikan intelektual Canary IP, dan matriks UAT serah-terima proyek.',
                    'en' => 'Unlock the 8-dimension automated test suite, Canary IP ownership seal, and project handover UAT matrix.',
                ],
            ],
        ];
    }

    /**
     * Mandatory Protection Against "Over-Planning / Scope Paralysis"
     * ("Peringatan Penting: Bahaya Over-Planning (Scope Paralysis)").
     *
     * @return array<string, mixed>
     */
    public static function scopeParalysisProtectionBlueprint(string $locale = 'id'): array
    {
        $isEn = $locale === 'en';

        return [
            'guard_code' => 'ANTI_OVER_PLANNING_SCOPE_PARALYSIS_GUARD_V12',
            'maturity_score_percent' => 100,
            'scope_freeze_recommended' => true,
            'scope_freeze_enforced' => true,
            'scope_freeze_active' => true,
            'badge' => $isEn
                ? '🛡️ CRITICAL GUARD // 100% BLUEPRINT MATURITY REACHED — STOP OVER-PLANNING'
                : '🛡️ PROTEKSI ANTI-OVER-PLANNING // BLUEPRINT 100% MATANG — AKTIFKAN SCOPE FREEZE',
            'title' => $isEn
                ? 'Important Warning: The Danger of Over-Planning (Scope Paralysis)'
                : 'Peringatan Penting: Bahaya Over-Planning (Scope Paralysis)',
            'warning_title' => [
                'id' => 'Peringatan Penting: Bahaya Over-Planning (Scope Paralysis)',
                'en' => 'Important Warning: The Danger of Over-Planning (Scope Paralysis)',
            ],
            'warning_body' => [
                'id' => 'Dalam rekayasa produk, ketika sebuah blueprint sudah mencapai 100%, musuh terbesar berikutnya bukanlah "fitur yang kurang", melainkan menunda penulisan kode karena terus mencari tambahan fitur di atas kertas. Semua amunisi sudah ada di tangan Anda. Langkah paling berdampak tinggi sekarang adalah berhenti menambah konsep baru (SCOPE FREEZE) dan mulai mengeksekusi pembangunan fondasi neriahpro.com tahap demi tahap di IDE Anda.',
                'en' => 'In product engineering, once a blueprint reaches 100% maturity, the biggest enemy is no longer "missing features", but delaying code execution by endlessly hunting for extra features on paper. All the ammunition is already in your hands. The highest-impact move right now is to stop adding new concepts (SCOPE FREEZE) and start executing the foundation of neriahpro.com step by step in your IDE.',
            ],
            'action_prompt' => [
                'id' => 'Dari mana Anda ingin kita mulai mengeksekusi kodenya hari ini?',
                'en' => 'Where would you like us to start executing the code today?',
            ],
            'paragraph_1' => $isEn
                ? 'In product engineering, once a blueprint reaches 100% maturity, the biggest enemy is no longer "missing features", but delaying code execution by endlessly hunting for extra features on paper.'
                : 'Dalam rekayasa produk, ketika sebuah blueprint sudah mencapai 100%, musuh terbesar berikutnya bukanlah "fitur yang kurang", melainkan menunda penulisan kode karena terus mencari tambahan fitur di atas kertas.',
            'paragraph_2' => $isEn
                ? 'All the ammunition is already in your hands. The highest-impact move right now is to stop adding new concepts (SCOPE FREEZE) and start executing the foundation of neriahpro.com step by step in your IDE.'
                : 'Semua amunisi sudah ada di tangan Anda. Langkah paling berdampak tinggi sekarang adalah berhenti menambah konsep baru (SCOPE FREEZE) dan mulai mengeksekusi pembangunan fondasi neriahpro.com tahap demi tahap di IDE Anda.',
            'question_prompt' => $isEn
                ? 'Where would you like us to start executing the code today?'
                : 'Dari mana Anda ingin kita mulai mengeksekusi kodenya hari ini?',
            'execution_stages' => self::foundationExecutionStages(),
        ];
    }

    /**
     * The 3 Concrete Foundation Execution Stages to defeat Scope Paralysis immediately.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function foundationExecutionStages(): array
    {
        return [
            [
                'stage_number' => 1,
                'stage_key' => 'stage_1_migrations',
                'stage_code' => 'stage_1_migrations',
                'target_phase' => 2,
                'target_tab' => 'schema',
                'label' => 'Mulai Eksekusi Tahap 1: Buat File Migrasi Lengkap Database Internal neriahpro.com',
                'label_en' => 'Start Stage 1 Execution: Generate Full Internal Database Migrations for neriahpro.com',
                'query' => 'Mari kita mulai eksekusi Tahap 1 sekarang. Buatkan file migrasi Laravel 13 lengkap (100% ULID & PostgreSQL 16 strict) untuk seluruh tabel inti neriahpro.com agar bisa langsung saya jalankan di proyek saya.',
                'query_en' => 'Let us start Stage 1 execution right now. Generate the complete Laravel 13 migration files (100% ULID & strict PostgreSQL 16) for all core neriahpro.com tables so I can run them directly in my project.',
                'deliverables_summary' => [
                    'id' => 'Skema migrasi Laravel 13 (100% ->ulid(\'id\')->primary() & ->foreignUlid()) untuk pos_sector_blueprints, pos_stack_profiles, pos_module_recipes, pos_projects, pos_project_Ledgers, vision_blueprints, dan tabel operasional sektor.',
                    'en' => 'Complete Laravel 13 migration schemas (100% ->ulid(\'id\')->primary() & ->foreignUlid()) for pos_sector_blueprints, pos_stack_profiles, pos_module_recipes, pos_projects, pos_project_ledgers, vision_blueprints, and sectoral operational tables.',
                ],
                'verified_files' => [
                    'database/migrations/2026_10_28_010000_create_project_os_tables.php',
                    'database/migrations/2026_10_30_010000_upgrade_project_os_v8_dual_persona_and_ui_variants.php',
                    'database/migrations/2026_10_31_010000_upgrade_project_os_v9_paripurna_pillars.php',
                ],
            ],
            [
                'stage_number' => 2,
                'stage_key' => 'stage_2_seeders',
                'stage_code' => 'stage_2_seeders',
                'target_phase' => 1,
                'target_tab' => 'menu_map',
                'label' => 'Mulai Eksekusi Tahap 2: Buat Seeder Master 12 Sektor, 9 Stack & L1-L4 Menu Matrix',
                'label_en' => 'Start Stage 2 Execution: Generate Master Seeder for 12 Sectors, 9 Stacks & L1-L4 Menu Matrix',
                'query' => 'Mari kita mulai eksekusi Tahap 2 sekarang. Buatkan kode DatabaseSeeder / PosMasterSeeder lengkap yang mengisi 12 Sektor Fungsional, 36 Varian Visual UI (Zona A + Zona B), 9 Profil Tech Stack, dan Peta Menu L1-L4 ke dalam database.',
                'query_en' => 'Let us start Stage 2 execution right now. Generate the complete DatabaseSeeder / PosMasterSeeder code populating the 12 Functional Sectors, 36 Visual UI Variants (Zone A + Zone B), 9 Tech Stack Profiles, and L1-L4 Menu Matrix into the database.',
                'deliverables_summary' => [
                    'id' => 'PosMasterCatalogSeeder & PosParipurnaCatalog deterministik mencakup 12 Sektor Fungsional (#01–#12), 36 Varian Visual UI (Zona A Kokpit + Zona B Aniwall), 9 Stack Profiles, 8 Regulasi Global, dan 25+ Sub-Menu L1–L4.',
                    'en' => 'Deterministic PosMasterCatalogSeeder & PosParipurnaCatalog covering 12 Functional Sectors (#01–#12), 36 Visual UI Variants (Zone A Cockpit + Zone B Aniwall), 9 Stack Profiles, 8 Global Regulations, and 25+ L1–L4 Sub-Menus.',
                ],
                'verified_files' => [
                    'database/seeders/PosMasterCatalogSeeder.php',
                    'app/Support/PosInclusiveCommercialCatalog.php',
                    'app/Support/PosSeoGeoAndZeroFailureCatalog.php',
                ],
            ],
            [
                'stage_number' => 3,
                'stage_key' => 'stage_3_compiler',
                'stage_code' => 'stage_3_compiler',
                'target_phase' => 3,
                'target_tab' => 'prompts',
                'label' => 'Mulai Eksekusi Tahap 3: Bangun Compiler SYMBOL_MANIFEST.json & Phased Prompt',
                'label_en' => 'Start Stage 3 Execution: Build SYMBOL_MANIFEST.json & Phased Prompt Compiler',
                'query' => 'Mari kita mulai eksekusi Tahap 3 sekarang. Buatkan service compiler PHP (ProjectOsBlueprintCompilerService & BlueprintEntitlementGateService) yang mengompilasi SYMBOL_MANIFEST.json, DAG Topological Chunker 7-Fase, dan Zero-Leak Server-Side Gate.',
                'query_en' => 'Let us start Stage 3 execution right now. Build the PHP compiler services (ProjectOsBlueprintCompilerService & BlueprintEntitlementGateService) that compile SYMBOL_MANIFEST.json, 7-Phase DAG Topological Chunker, and Zero-Leak Server-Side Gate.',
                'deliverables_summary' => [
                    'id' => 'ProjectOsBlueprintCompilerService + BlueprintEntitlementGateService dengan kompilasi SYMBOL_MANIFEST.json anti-halusinasi, pemecah prompt DAG 7-Fase (<2.000 token/fase), dan gerbang server-side Zero-Leak.',
                    'en' => 'ProjectOsBlueprintCompilerService + BlueprintEntitlementGateService with anti-hallucination SYMBOL_MANIFEST.json compilation, 7-Phase DAG prompt chunker (<2,000 tokens/phase), and Zero-Leak server-side gating.',
                ],
                'verified_files' => [
                    'app/Services/ProjectOsBlueprintCompilerService.php',
                    'app/Services/Entitlement/BlueprintEntitlementGateService.php',
                    'app/Livewire/BlueprintArchitectStudio.php',
                ],
            ],
        ];
    }
}
