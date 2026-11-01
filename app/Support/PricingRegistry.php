<?php

namespace App\Support;

/**
 * Centralized, Concrete Pricing & Package Registry
 * Strictly avoids magic strings and fuzzy prefix matching.
 */
class PricingRegistry
{
    // Concrete Package IDs
    public const TIER_RETAIL_SPARK = 'retail_spark';
    public const TIER_RETAIL_LITE = 'retail_lite';
    public const TIER_RETAIL_PRO = 'retail_pro';
    public const TIER_RETAIL_ULTIMATE = 'retail_ultimate';

    public const TIER_FULL_MVP = 'full_mvp';
    public const TIER_UMKM_STARTER = 'umkm_starter';
    public const TIER_BLUEPRINT_ADVISORY = 'blueprint_advisory';

    // Inclusive Hybrid Value-Metric Tiers (v8.0 — inklusif.md)
    public const TIER_IMPACT_NONPROFIT_GRANT = 'impact_nonprofit_grant';
    public const TIER_SINGLE_APP_UMKM_PASS = 'single_app_umkm_pass';
    public const TIER_AGENCY_WHITELABEL_PARTNER = 'agency_whitelabel_partner';

    /**
     * Inclusive Hybrid Value-Metric PLG & CSR SKUs (v8.0).
     */
    public const INCLUSIVE_PLG_PACKAGES = [
        self::TIER_IMPACT_NONPROFIT_GRANT => [
            'id' => self::TIER_IMPACT_NONPROFIT_GRANT,
            'name' => 'Impact & Non-Profit Grant (Subsidi CSR 70%–100%)',
            'name_en' => 'Impact & Non-Profit Grant (70%–100% CSR Subsidy)',
            'track_mode' => 'RETAIL_SELF_DEV',
            'category' => 'blueprint_self_service',
            'execution_model' => 'self_service',
            'is_paid' => false,
            'requires_login' => false,
            'requires_sprint_batch' => false,
            'requires_kickoff_slot' => false,
            'default_price' => 0,
            'subsidized_commercial_price' => 75000,
            'setting_key' => 'pricing_impact_grant_price',
            'sector_scope' => 'Khusus Sektor 12 (Yayasan/Masjid/Gereja/Zakat/NGO) & Sektor 07 (Sekolah Non-Komersial)',
            'sector_scope_en' => 'Dedicated to Sector 12 (Non-Profit/Worship/Zakat/NGO) & Sector 07 (Non-Commercial Schools)',
            'stack_scope' => ['STACK_M1_LARAVEL_MONOLITH', 'STACK_M2_INERTIA_REACT', 'STACK_M4_DJANGO_HTMX'],
            'included_v5_engines' => [
                'Subsidi CSR NeriahPro 70%–100% (Rp 0 dgn Verifikasi Lembaga / Voucher CSR)',
                'ISAK 35 / PSAK 109 / FASB ASC 958 Pemisah Dana Terikat vs Operasional',
                'Portal Transparansi Donatur Publik & Kwitansi QR SHA-256',
                '5-Step Zero-Jargon Business Wizard untuk Pengurus Yayasan Awam',
            ],
        ],
        self::TIER_SINGLE_APP_UMKM_PASS => [
            'id' => self::TIER_SINGLE_APP_UMKM_PASS,
            'name' => 'Single-App UMKM Pass (Beli Putus 1 Proyek)',
            'name_en' => 'Single-App SME Pass (Pay-Per-Project Lifetime)',
            'track_mode' => 'RETAIL_SELF_DEV',
            'category' => 'blueprint_self_service',
            'execution_model' => 'self_service',
            'is_paid' => true,
            'requires_login' => true,
            'requires_sprint_batch' => false,
            'requires_kickoff_slot' => false,
            'default_price' => 299000,
            'setting_key' => 'pricing_single_app_pass_price',
            'sector_scope' => '1 Proyek Aktif Pilihan (Bebas Pilih 1 dari 36 Visual UI Sektor)',
            'sector_scope_en' => '1 Active Project Pass (Pick Any 1 of 36 Sector Visual UIs)',
            'stack_scope' => ['M1..M4 Monolith', 'D1..D3 Decoupled Headless'],
            'included_v5_engines' => [
                'Tanpa Langganan Bulanan (Sekali Bayar untuk 1 Aplikasi UMKM/Klinik/Toko)',
                'PRD 26 Parameter + PostgreSQL 16 Strict ULID + 36 Visual UI Blueprint',
                '7-Fase Prompt AI Berurutan + One-Command CLI /init/{ulid}',
                'Active Maintenance Vault & Peringatan Keamanan CVE Otomatis',
            ],
        ],
        self::TIER_AGENCY_WHITELABEL_PARTNER => [
            'id' => self::TIER_AGENCY_WHITELABEL_PARTNER,
            'name' => 'Agency Partner & White-Label Studio (Multi-Seat)',
            'name_en' => 'Agency Partner & White-Label Studio (Multi-Seat)',
            'track_mode' => 'RETAIL_SELF_DEV',
            'category' => 'blueprint_self_service',
            'execution_model' => 'self_service',
            'is_paid' => true,
            'requires_login' => true,
            'requires_sprint_batch' => false,
            'requires_kickoff_slot' => false,
            'default_price' => 990000,
            'setting_key' => 'pricing_agency_whitelabel_price',
            'sector_scope' => 'Unlimited Proyek Klien Agensi across 12 Sektor + Multi-Seat Tim',
            'sector_scope_en' => 'Unlimited Client Projects across 12 Sectors + Multi-Seat Team',
            'stack_scope' => ['All 9 Curated Stacks (M1–S2) + Multi-Jurisdiction Tier-1 (ID/EN/AR-RTL/JP/DE)'],
            'included_v5_engines' => [
                'White-Label PDF Generator (RAB, WBS, SPK, SOP, BAST memakai Logo & Nama Agensi Anda!)',
                'Multi-Seat Kolaborasi Tim Developer + Live MCP Server Bridge (neriahpro-mcp)',
                '7 Bahasa Tier-1 Yurisdiksi Global (PSAK/ISAK35 <-> GAAP/ASC958/HIPAA/ZATCA RTL)',
                'Akses Marketplace Primitif Komunitas & Prioritas Escala Keamanan CVE',
            ],
        ],
    ];

    /**
     * Complete immutable specification of all application packages.
     */
    public const PACKAGES = [
        self::TIER_RETAIL_SPARK => [
            'id' => self::TIER_RETAIL_SPARK,
            'name' => 'Spark Free Idea Audit',
            'name_en' => 'Spark Free Idea Audit',
            'track_mode' => 'RETAIL_SELF_DEV',
            'category' => 'blueprint_self_service',
            'execution_model' => 'self_service',
            'is_paid' => false,
            'requires_login' => false,
            'requires_sprint_batch' => false,
            'requires_kickoff_slot' => false,
            'default_price' => 0,
            'setting_key' => 'pricing_retail_spark_price',
            'limit_setting_key' => 'pricing_retail_spark_limit',
            'sector_scope' => '1 dari 12 Sektor (Audit & Preview ERD Dasar)',
            'sector_scope_en' => '1 of 12 Sectors (Audit & Core ERD Preview)',
            'stack_scope' => ['STACK_M1_LARAVEL_MONOLITH'],
            'included_v5_engines' => ['Pre-Flight Blueprint Doctor', 'MarkItDown 0-Token Intake', 'Lean PRD & ULID Preview'],
        ],
        self::TIER_RETAIL_LITE => [
            'id' => self::TIER_RETAIL_LITE,
            'name' => 'Starter AI Architect (Lite PRD)',
            'name_en' => 'Starter AI Architect (Lite PRD)',
            'track_mode' => 'RETAIL_SELF_DEV',
            'category' => 'blueprint_self_service',
            'execution_model' => 'self_service',
            'is_paid' => true,
            'requires_login' => true,
            'requires_sprint_batch' => false,
            'requires_kickoff_slot' => false,
            'default_price' => 99000,
            'setting_key' => 'pricing_retail_lite_price',
            'limit_setting_key' => 'pricing_retail_lite_limit',
            'sector_scope' => '1 Sektor Fungsional Kanonik Pilihan',
            'sector_scope_en' => '1 Selected Canonical Functional Sector',
            'stack_scope' => ['STACK_M1_LARAVEL_MONOLITH', 'STACK_M2_INERTIA_REACT', 'STACK_M3_NEXTJS_TRPC', 'STACK_M4_DJANGO_HTMX'],
            'included_v5_engines' => ['PRD 26 Parameter Utuh', 'PostgreSQL 16 Strict ULID SQL', 'AGENTS.md & .cursorrules Anti-Slop', 'SYNTAX_LOCK_2026.md & Revisi 30 Hari'],
        ],
        self::TIER_RETAIL_PRO => [
            'id' => self::TIER_RETAIL_PRO,
            'name' => 'Pro Agentic Blueprint & Scaffold',
            'name_en' => 'Pro Agentic Blueprint & Scaffold',
            'track_mode' => 'RETAIL_SELF_DEV',
            'category' => 'blueprint_self_service',
            'execution_model' => 'self_service',
            'is_paid' => true,
            'requires_login' => true,
            'requires_sprint_batch' => false,
            'requires_kickoff_slot' => false,
            'default_price' => 399000,
            'setting_key' => 'pricing_retail_pro_price',
            'limit_setting_key' => 'pricing_retail_pro_limit',
            'sector_scope' => 'Seluruh 12 Sektor Composable Lintas-Primitif',
            'sector_scope_en' => 'All 12 Composable Cross-Primitive Sectors',
            'stack_scope' => ['M1..M4 Monolith', 'D1..D3 Decoupled Headless', 'S1..S2 Microservices'],
            'included_v5_engines' => ['3-Pane Architect Studio Penuh (15 Pilar Freeze)', 'Kahn DAG Chunker & SYMBOL_MANIFEST.json', 'Hardware/WebSocket & Strict N+1 Guard', 'One-Command CLI /init/{ulid} + SYNTAX_LOCK_2026.md', 'ZIP Scaffold 25+ File + Role-Based Client SOP'],
        ],
        self::TIER_RETAIL_ULTIMATE => [
            'id' => self::TIER_RETAIL_ULTIMATE,
            'name' => 'Ultimate CTO & Lifecycle Blueprint',
            'name_en' => 'Ultimate CTO & Lifecycle Blueprint',
            'track_mode' => 'RETAIL_SELF_DEV',
            'category' => 'blueprint_self_service',
            'execution_model' => 'self_service',
            'is_paid' => true,
            'requires_login' => true,
            'requires_sprint_batch' => false,
            'requires_kickoff_slot' => false,
            'default_price' => 1490000,
            'setting_key' => 'pricing_retail_ultimate_price',
            'limit_setting_key' => 'pricing_retail_ultimate_limit',
            'sector_scope' => 'Multi-Domain Enterprise (12 Sektor + Brownfield Legacy)',
            'sector_scope_en' => 'Multi-Domain Enterprise (12 Sectors + Brownfield Legacy)',
            'stack_scope' => ['All 9 Stacks (M1–S2) + Multi-Tenant Isolation'],
            'included_v5_engines' => ['Semua 15 Pilar Project OS 100% Freeze', 'Live MCP Server Bridge (neriahpro-mcp)', 'Day-30 s/d Day-365 Surgical Delta CR (v1.0 -> v2.0)', 'PG16 Range Partitioning + Dual-Screen QRIS & IoT Bridge', 'SmartGuide.php In-App SOP + 1-on-1 Architecture Call 60m'],
        ],
        self::TIER_BLUEPRINT_ADVISORY => [
            'id' => self::TIER_BLUEPRINT_ADVISORY,
            'name' => 'Mitra Discovery, Scope Lock & RAB Advisory',
            'name_en' => 'Partner Discovery, Scope Lock & BoQ Advisory',
            'track_mode' => 'MITRA_TURNKEY_PROJECT',
            'category' => 'studio_engineering',
            'execution_model' => 'studio_contract',
            'is_paid' => true,
            'requires_login' => false,
            'requires_sprint_batch' => false,
            'requires_kickoff_slot' => false,
            'default_price' => 2500000,
            'setting_key' => 'pricing_advisory_price',
            'sector_scope' => 'Seluruh 12 Sektor (Didampingi Senior Architect)',
            'sector_scope_en' => 'All 12 Sectors (Guided by Senior Architect)',
            'stack_scope' => ['Rekomendasi Terkurasi M1–S2 + Kalkulasi Server'],
            'included_v5_engines' => ['Workshop 1-on-1 Senior Architect (15 Pilar Audit)', 'Algorithmic RAB & WBS 7-Milestone Akurat', 'Cryptographic Scope Freeze SHA-256 & Draft SPK', '100% Biaya Dikreditkan Memotong DP Proyek Turnkey'],
        ],
        self::TIER_UMKM_STARTER => [
            'id' => self::TIER_UMKM_STARTER,
            'name' => 'Mitra UMKM Digital Turnkey Starter',
            'name_en' => 'Partner SME Digital Turnkey Starter',
            'track_mode' => 'MITRA_TURNKEY_PROJECT',
            'category' => 'studio_engineering',
            'execution_model' => 'studio_contract',
            'is_paid' => true,
            'requires_login' => false,
            'requires_sprint_batch' => false,
            'requires_kickoff_slot' => false,
            'default_price' => 7500000,
            'subsidized_default_price' => 3750000,
            'setting_key' => 'pricing_umkm_price',
            'sector_scope' => 'Sektor Esensial UMKM (Ritel POS, F&B, Booking Jasa, Klinik Pratama, Katalog B2B)',
            'sector_scope_en' => 'Essential SME Sectors (Retail POS, F&B, Service Booking, Clinic, B2B Catalog)',
            'stack_scope' => ['STACK_M1_LARAVEL_MONOLITH (Cepat & Hemat VPS)'],
            'included_v5_engines' => ['100% Dikerjakan Tim Neriah Pro (10–14 Hari Kerja)', 'Subsidi 50% (Rp 3,75 Jt dgn Voucher UMKM-SUBSIDI-50)', 'Kontrak SPK SHA-256 + Barcode/Struk Thermal', 'Live Staging + Manual SOP Klien & Digital BAST'],
        ],
        self::TIER_FULL_MVP => [
            'id' => self::TIER_FULL_MVP,
            'name' => 'Mitra Enterprise Turnkey MVP (15-Pillar Freeze)',
            'name_en' => 'Partner Enterprise Turnkey MVP (15-Pillar Freeze)',
            'track_mode' => 'MITRA_TURNKEY_PROJECT',
            'category' => 'studio_engineering',
            'execution_model' => 'studio_contract',
            'is_paid' => false,
            'requires_login' => false,
            'requires_sprint_batch' => true,
            'requires_kickoff_slot' => true,
            'default_price' => 0,
            'setting_key' => 'pricing_mvp_price',
            'sector_scope' => 'Seluruh 12 Sektor Enterprise (Fintech Ledger, EMR RS, Manufaktur MRP, Logistik, HRIS, SaaS)',
            'sector_scope_en' => 'All 12 Enterprise Sectors (Fintech Ledger, Hospital EMR, MRP, Logistics, HRIS, SaaS)',
            'stack_scope' => ['All 9 Curated Stacks (M1–S2) Sesuai Beban Konkurensi'],
            'included_v5_engines' => ['100% Turnkey Software House (15 Pilar Project OS Freeze)', 'Algorithmic Scope-to-Quotation (Termin 30% DP / 50% Milestone / 20% BAST)', 'Hardware/IoT Bridge, Zero-Poll WebSocket, Strict N+1 & PG16 Partitioning', 'Interactive Role SmartGuide.php + Manual Training SOP & BAST Sign-Off'],
        ],
    ];

    /**
     * Retrieve all canonical + inclusive PLG packages (10 SKUs total).
     *
     * @return array<string, array<string, mixed>>
     */
    public static function allPackages(): array
    {
        return array_merge(self::PACKAGES, self::INCLUSIVE_PLG_PACKAGES);
    }

    /**
     * Retrieve package details by exact ID (searches both canonical 7 & inclusive PLG tiers).
     */
    public static function get(string $id): ?array
    {
        $all = self::allPackages();

        return $all[$id] ?? null;
    }

    /**
     * Resolve live base price in IDR for a package ID from Satu Pintu / Inclusive Catalog (`CmsGlobalSetting` -> default).
     */
    public static function getBaseAmount(string $id): float
    {
        $catalog = self::getSatuPintuCatalog();
        if (isset($catalog[$id])) {
            return (float) ($catalog[$id]['resolved_price_idr'] ?? $catalog[$id]['default_price'] ?? 0);
        }

        $inclusive = self::getInclusiveValueMetricCatalog();
        if (isset($inclusive[$id])) {
            return (float) ($inclusive[$id]['resolved_price_idr'] ?? $inclusive[$id]['default_price'] ?? 0);
        }

        return 0.0;
    }

    /**
     * Check if package is self-service (100% digital, automated generation).
     */
    public static function isSelfService(string $id): bool
    {
        $all = self::allPackages();

        return ($all[$id]['execution_model'] ?? null) === 'self_service';
    }

    /**
     * Check if package is studio contract (Neriah Pro team custom engineering).
     */
    public static function isStudioContract(string $id): bool
    {
        $all = self::allPackages();

        return ($all[$id]['execution_model'] ?? null) === 'studio_contract';
    }

    /**
     * Get all concrete self-service package IDs.
     */
    public static function getSelfServiceIds(): array
    {
        return array_keys(array_filter(self::allPackages(), fn($p) => $p['execution_model'] === 'self_service'));
    }

    /**
     * Get all concrete studio contract package IDs.
     */
    public static function getStudioContractIds(): array
    {
        return array_keys(array_filter(self::allPackages(), fn($p) => $p['execution_model'] === 'studio_contract'));
    }

    /**
     * Single Source of Truth ("Satu Pintu") Commercial Estimator Config from Backend Admin (`ManageSettings`).
     *
     * @return array<string, mixed>
     */
    public static function getCommercialEstimatorConfig(): array
    {
        $cleanInt = static function (string $key, int $default): int {
            try {
                $raw = \App\Models\CmsGlobalSetting::getVal($key, null);
                if ($raw === null || $raw === '') {
                    return $default;
                }
                $num = (int) preg_replace('/[^\d]/', '', (string) $raw);

                return $num > 0 ? $num : $default;
            } catch (\Throwable) {
                return $default;
            }
        };

        $terminScheme = '30_50_20';
        try {
            $rawTermin = (string) \App\Models\CmsGlobalSetting::getVal('mitra_termin_scheme', '30_50_20');
            if (in_array($rawTermin, ['30_50_20', '50_50'], true)) {
                $terminScheme = $rawTermin;
            }
        } catch (\Throwable) {
            $terminScheme = '30_50_20';
        }

        return [
            'man_hour_rate_idr' => $cleanInt('mitra_man_hour_rate_idr', 350000),
            'hours_per_table' => $cleanInt('mitra_hours_per_table', 4),
            'hours_per_state' => $cleanInt('mitra_hours_per_state', 6),
            'hours_per_lock' => $cleanInt('mitra_hours_per_lock', 5),
            'hours_per_integration' => $cleanInt('mitra_hours_per_integration', 8),
            'base_setup_hours' => $cleanInt('mitra_base_setup_hours', 24),
            'termin_scheme' => $terminScheme,
        ];
    }

    /**
     * Resolve all 7 Retail & Mitra packages with live Backend Admin prices ("Satu Pintu").
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getSatuPintuCatalog(): array
    {
        $resolved = [];
        foreach (self::PACKAGES as $id => $pkg) {
            $price = (int) ($pkg['default_price'] ?? 0);
            $settingKey = $pkg['setting_key'] ?? null;
            $displayPrice = $price === 0 && $id === self::TIER_FULL_MVP ? 'Algorithmic Fair Quotation' : ('Rp ' . number_format($price, 0, ',', '.'));

            if ($settingKey) {
                try {
                    $raw = \App\Models\CmsGlobalSetting::getVal($settingKey, null);
                    if ($raw !== null && $raw !== '') {
                        if ($id === self::TIER_FULL_MVP && ! preg_match('/^\d[\d\.,]*$/', trim((string) $raw))) {
                            $displayPrice = (string) $raw;
                        } else {
                            $cleaned = (int) preg_replace('/[^\d]/', '', (string) $raw);
                            if ($cleaned >= 0 && ($cleaned > 0 || $id === self::TIER_RETAIL_SPARK)) {
                                $price = $cleaned;
                                $displayPrice = 'Rp ' . number_format($price, 0, ',', '.');
                            }
                        }
                    }
                } catch (\Throwable) {
                    // Fallback to defaults gracefully when DB not yet migrated
                }
            }

            $pkg['name_id'] = $pkg['name_id'] ?? $pkg['name'] ?? $id;
            $pkg['name_en'] = $pkg['name_en'] ?? $pkg['name'] ?? $id;
            $pkg['localized_name'] = [
                'id' => $pkg['name_id'],
                'en' => $pkg['name_en'],
            ];
            $pkg['localized_sector_scope'] = [
                'id' => (string) ($pkg['sector_scope'] ?? ''),
                'en' => (string) ($pkg['sector_scope_en'] ?? $pkg['sector_scope'] ?? ''),
            ];
            $pkg['stack_scope_label'] = is_array($pkg['stack_scope'] ?? null)
                ? implode(', ', $pkg['stack_scope'])
                : (string) ($pkg['stack_scope'] ?? 'M1–S2');
            $pkg['resolved_price_idr'] = $price;
            $pkg['formatted_price'] = $displayPrice;
            $resolved[$id] = $pkg;
        }

        return $resolved;
    }

    /**
     * Resolve Inclusive Hybrid Value-Metric PLG & CSR SKUs (v8.0 — Impact Grant, Single-App UMKM Pass, Agency White-Label).
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getInclusiveValueMetricCatalog(): array
    {
        $resolved = [];
        foreach (self::INCLUSIVE_PLG_PACKAGES as $id => $pkg) {
            $price = (int) ($pkg['default_price'] ?? 0);
            $settingKey = $pkg['setting_key'] ?? null;
            $displayPrice = $price === 0
                ? 'Rp 0 (Subsidi CSR 70%–100%)'
                : ('Rp ' . number_format($price, 0, ',', '.'));

            if ($settingKey) {
                try {
                    $raw = \App\Models\CmsGlobalSetting::getVal($settingKey, null);
                    if ($raw !== null && $raw !== '') {
                        $cleaned = (int) preg_replace('/[^\d]/', '', (string) $raw);
                        if ($cleaned >= 0 && ($cleaned > 0 || $id === self::TIER_IMPACT_NONPROFIT_GRANT)) {
                            $price = $cleaned;
                            $displayPrice = $price === 0
                                ? 'Rp 0 (Subsidi CSR 100%)'
                                : ('Rp ' . number_format($price, 0, ',', '.'));
                        }
                    }
                } catch (\Throwable) {
                    // Fallback gracefully
                }
            }

            $pkg['name_id'] = $pkg['name_id'] ?? $pkg['name'] ?? $id;
            $pkg['name_en'] = $pkg['name_en'] ?? $pkg['name'] ?? $id;
            $pkg['localized_name'] = [
                'id' => $pkg['name_id'],
                'en' => $pkg['name_en'],
            ];
            $pkg['localized_sector_scope'] = [
                'id' => (string) ($pkg['sector_scope'] ?? ''),
                'en' => (string) ($pkg['sector_scope_en'] ?? $pkg['sector_scope'] ?? ''),
            ];
            $pkg['stack_scope_label'] = is_array($pkg['stack_scope'] ?? null)
                ? implode(', ', $pkg['stack_scope'])
                : (string) ($pkg['stack_scope'] ?? 'M1–S2');
            $pkg['resolved_price_idr'] = $price;
            $pkg['formatted_price'] = $displayPrice;
            $resolved[$id] = $pkg;
        }

        return $resolved;
    }
}
