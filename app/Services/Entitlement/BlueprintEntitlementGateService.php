<?php

declare(strict_types=1);

namespace App\Services\Entitlement;

use App\Models\PosProject;
use App\Models\Transaction;
use App\Models\VisionBlueprint;
use App\Support\PosInclusiveCommercialCatalog;
use App\Support\PosProgressiveDisclosureCatalog;
use App\Support\PosSeoGeoAndZeroFailureCatalog;
use App\Support\ProjectOsMatrixCatalog;

/**
 * Project OS v12.0 — Server-Side Zero-Leak Entitlement Gate, Progressive Disclosure Proposal Builder,
 * Contextual Risk & Value Upsell Coach, Retail-to-Mitra Rescue Credit Engine & Scope Paralysis Guard.
 *
 * CRITICAL SECURITY INVARIANT:
 * - For FREE_SANDBOX ('retail_spark'), Phases 2.2 through 7.0 are NEVER compiled or rendered to the HTML DOM.
 * - Zero reliance on CSS blur-sm / pointer-events-none to hide locked prompt payloads.
 * - Zero hardcoded entity string LIKE queries; all database entitlement lookups use concrete primary/foreign ULID keys.
 */
final class BlueprintEntitlementGateService
{
    /**
     * Resolve the concrete entitlement tier for the current user / project / requested tier slug.
     *
     * Returns one of: 'FREE_SANDBOX' | 'SINGLE_APP_PASS' | 'PRO_RETAIL' | 'AGENCY_PARTNER' | 'MITRA_TURNKEY'
     */
    public function resolveUserTier(?object $user = null, ?string $projectId = null, ?string $requestedTier = null): string
    {
        // 1. Check concrete PosProject record by exact ULID primary key or slug
        if ($projectId !== null && $projectId !== '') {
            $posProject = PosProject::query()->where('id', $projectId)->orWhere('slug', $projectId)->first();
            if ($posProject !== null) {
                if ($posProject->track === 'MITRA') {
                    return 'MITRA_TURNKEY';
                }
                if ($posProject->scope_locked === true) {
                    return 'SINGLE_APP_PASS';
                }
            }

            // 2. Check concrete VisionBlueprint record & settled Transaction by concrete foreign key / metadata
            $blueprint = VisionBlueprint::query()->where('id', $projectId)->orWhere('slug', $projectId)->first();
            if ($blueprint !== null) {
                $metaTier = $blueprint->user_metadata['retail_tier'] ?? $blueprint->user_metadata['package_tier'] ?? null;
                $orderId = $blueprint->user_metadata['order_id'] ?? null;

                $hasSettledTx = Transaction::query()
                    ->where('status', 'settlement')
                    ->where(function ($q) use ($blueprint, $orderId) {
                        $q->where(function ($sq) use ($blueprint) {
                            $sq->where('related_type', VisionBlueprint::class)
                                ->where('related_id', $blueprint->id);
                        });
                        if ($orderId) {
                            $q->orWhere('midtrans_order_id', $orderId);
                        }
                    })
                    ->exists();

                if ($hasSettledTx || $blueprint->project_status === 'Active Sprint') {
                    if ($blueprint->isRetailLicense() || ($blueprint->user_metadata['track'] ?? '') === 'retail') {
                        if ($metaTier) {
                            return $this->normalizeRequestedTierSlug($metaTier);
                        }
                        return 'PRO_RETAIL';
                    }
                    return 'MITRA_TURNKEY';
                }

                // If marked as retail license or has paid metadata
                if (! empty($blueprint->user_metadata['paid']) || $blueprint->project_status === 'Retail License') {
                    if ($metaTier) {
                        return $this->normalizeRequestedTierSlug($metaTier);
                    }
                    return 'PRO_RETAIL';
                }

                // Explicit free spark
                if ($metaTier && in_array(strtolower($metaTier), ['retail_spark', 'spark', 'free_sandbox'], true)) {
                    return 'FREE_SANDBOX';
                }
            }
        }

        // 3. Check authenticated user concrete role / settled transactions
        if ($user !== null && isset($user->id)) {
            if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['super_admin', 'admin', 'principal_architect'])) {
                return 'AGENCY_PARTNER';
            }

            $latestSettledTx = Transaction::query()
                ->where('user_id', (string) $user->id)
                ->where('status', 'settlement')
                ->orderBy('id', 'desc')
                ->first();

            if ($latestSettledTx !== null) {
                $amount = (int) ($latestSettledTx->total_idr ?? $latestSettledTx->original_amount ?? 0);
                if ($amount >= 2500000) {
                    return 'MITRA_TURNKEY';
                }
                if ($amount >= 990000) {
                    return 'AGENCY_PARTNER';
                }
                if ($amount === 299000) {
                    return 'SINGLE_APP_PASS';
                }
                if ($amount >= 149000) {
                    return 'PRO_RETAIL';
                }
            }
        }

        // 4. Resolve explicit tier slug if provided (e.g., from Satu Pintu Wizard or Studio Preview)
        return $this->normalizeRequestedTierSlug($requestedTier);
    }

    /**
     * Normalize a package/tier slug into one of the 5 canonical entitlement tiers.
     */
    public function normalizeRequestedTierSlug(?string $slug): string
    {
        $normalized = strtolower(trim((string) $slug));

        return match ($normalized) {
            'retail_single_app', 'single_app_pass', 'single_app' => 'SINGLE_APP_PASS',
            'retail_starter', 'retail_lite', 'lite', 'retail_pro', 'impact_nonprofit_grant', 'pro_retail', 'pro', 'starter' => 'PRO_RETAIL',
            'retail_ultimate', 'ultimate', 'retail_agency', 'retail_whitelabel', 'agency_partner', 'agency', 'whitelabel' => 'AGENCY_PARTNER',
            'advisory', 'monolith', 'enterprise', 'mitra_turnkey', 'mitra' => 'MITRA_TURNKEY',
            default => 'FREE_SANDBOX',
        };
    }

    /**
     * Check if a specific export / download artifact is entitled for the given tier.
     */
    public function isArtifactEntitled(string $tier, string $artifact): bool
    {
        $normalized = $this->normalizeRequestedTierSlug($tier);

        // Paid tiers have full download entitlements
        if ($normalized !== 'FREE_SANDBOX') {
            return true;
        }

        // In FREE_SANDBOX, only basic documentation and Phase 1 prompt are allowed
        return match ($artifact) {
            'pdf', 'md', 'raw-md', 'phase-1', 'cursorrules', 'claude', 'agents', 'ui-ux-contract', 'engineering-rules' => true,
            'scaffold_zip' => false,
            'symbol_manifest' => false,
            'pg16_partition' => false,
            'mcp_json' => false,
            'phase_prompts_2_to_7' => false,
            default => false,
        };
    }

    /**
     * Filter scaffold files for the given tier.
     * On FREE_SANDBOX, proprietary production code (tests, deploy scripts, seeders) is withheld.
     *
     * @param  array<string, string>  $files
     * @return array<string, string>
     */
    public function filterScaffoldFilesForTier(array $files, string $tier, string $locale = 'id'): array
    {
        $normalized = $this->normalizeRequestedTierSlug($tier);
        if ($normalized !== 'FREE_SANDBOX') {
            return $files;
        }

        $isEn = $locale === 'en';
        $lockedPlaceholder = $isEn
            ? "// [LOCKED IN FREE SANDBOX — ZERO-LEAK SERVER-SIDE GATE]\n// This production file is exclusive to Retail Pro/Ultimate or Mitra Turnkey licenses.\n// Upgrade your license to unlock full implementation code."
            : "// [TERKUNCI PADA SANDBOX GRATIS — ZERO-LEAK SERVER-SIDE GATE]\n// Berkas produksi ini eksklusif untuk lisensi Retail Pro/Ultimate atau Proyek Mitra Turnkey.\n// Upgrade lisensi Anda untuk membuka kode implementasi lengkap.";

        $proprietaryPatterns = [
            'tests/Feature/ApiContractTest.php',
            'tests/e2e/critical-flows.spec.ts',
            'database/seeders/SyntheticDataSeeder.php',
            'app/Http/Controllers/HealthProbeController.php',
            'deploy.sh',
            '.github/workflows/deploy.yml',
            'scripts/backup.sh',
            'scripts/restore.sh',
            'docs/runbooks/disaster-recovery.md',
            'docs/disaster-recovery-runbook.md',
        ];

        foreach ($files as $filePath => $content) {
            if (in_array($filePath, $proprietaryPatterns, true)) {
                $files[$filePath] = $lockedPlaceholder;
            }
        }

        return $files;
    }

    /**
     * Strict Server-Side Prompt Payload Gate (0% F12 Inspect Element Leak).
     *
     * @return array{is_locked: bool, tier: string, phase: int, prompt_content: string, upsell_advice: ?array<string, mixed>}
     */
    public function getAuthorizedPromptPayload(
        ?object $user,
        ?string $projectId,
        int $requestedPhase,
        string $sectorCodeOrName,
        callable $compilerCallback,
        ?string $requestedTier = null,
        string $locale = 'id'
    ): array {
        $tier = $this->resolveUserTier($user, $projectId, $requestedTier);
        $isEn = $locale === 'en';

        // Free Sandbox only receives Phase 1 (Foundation & Folder Structure)
        if ($tier === 'FREE_SANDBOX' && $requestedPhase > 1) {
            $safeTeaser = $isEn
                ? "# [LOCKED IN FREE SANDBOX — PHASE {$requestedPhase}]\n# ZERO-LEAK SERVER-SIDE ENTITLEMENT GATE: Phase {$requestedPhase} production code & prompts are never sent to the browser DOM in Guest Sandbox mode.\n# Upgrade to Single-App Pass (IDR 299,000 One-Time) or Pro Agentic (IDR 399,000/mo) to compile and unlock Phase {$requestedPhase} on the server."
                : "# [TERKUNCI PADA SANDBOX GRATIS — FASE {$requestedPhase}]\n# ZERO-LEAK SERVER-SIDE ENTITLEMENT GATE: Kode produksi & prompt Fase {$requestedPhase} tidak pernah dikirim ke DOM browser pada mode Tamu Sandbox.\n# Upgrade ke Single-App Pass (Rp 299.000 Sekali Bayar) atau Pro Agentic (Rp 399.000/bln) untuk mengompilasi dan membuka Fase {$requestedPhase} di server.";

            return [
                'is_locked' => true,
                'server_side_withheld' => true,
                'tier' => $tier,
                'phase' => $requestedPhase,
                'prompt_content' => $safeTeaser,
                'upsell_advice' => $this->buildContextualUpsellAdvice($requestedPhase, $sectorCodeOrName, $locale),
            ];
        }

        // Paid / Authorized Tier or Phase 1: execute compiler callback on the server
        $compiledContent = (string) $compilerCallback();

        return [
            'is_locked' => false,
            'server_side_withheld' => false,
            'tier' => $tier,
            'phase' => $requestedPhase,
            'prompt_content' => $compiledContent,
            'upsell_advice' => null,
        ];
    }

    /**
     * Filter the 7-phase prompt array on the server before storing in Livewire state or Blade view.
     * Guarantees Phases 2–7 raw prompts are completely withheld when $tier === 'FREE_SANDBOX'.
     *
     * @param  array<int, array<string, mixed>>  $phasedPrompts
     * @return array<int, array<string, mixed>>
     */
    public function filterPhasedPromptsForTier(
        array $phasedPrompts,
        string $tierOrSector,
        string $sectorOrTier = 'SECTOR_01_COMMERCE_OMNICHANNEL',
        string $locale = 'id'
    ): array {
        // Auto-detect parameter order if caller passed ($phases, $sectorCode, $tier)
        if (str_starts_with(strtoupper($tierOrSector), 'SECTOR_')) {
            $sectorCodeOrName = $tierOrSector;
            $tier = $sectorOrTier;
        } else {
            $tier = $tierOrSector;
            $sectorCodeOrName = $sectorOrTier;
        }

        $result = [];

        foreach ($phasedPrompts as $index => $phaseItem) {
            $phaseNumber = (int) ($phaseItem['phase'] ?? ($index + 1));

            $gateResponse = $this->getAuthorizedPromptPayload(
                null,
                null,
                $phaseNumber,
                $sectorCodeOrName,
                static fn (): string => (string) ($phaseItem['prompt'] ?? ''),
                $tier,
                $locale
            );

            $filteredItem = $phaseItem;
            $filteredItem['phase'] = $phaseNumber;
            $filteredItem['is_locked'] = $gateResponse['is_locked'];
            $filteredItem['server_side_withheld'] = $gateResponse['server_side_withheld'];
            $filteredItem['entitlement_tier'] = $gateResponse['tier'];
            $filteredItem['prompt'] = $gateResponse['prompt_content'];
            $filteredItem['upsell_advice'] = $gateResponse['upsell_advice'];

            if ($gateResponse['is_locked']) {
                $filteredItem['estimated_tokens'] = 0;
                $filteredItem['locked_badge'] = $locale === 'en'
                    ? '🔒 LOCKED (SERVER GATE)'
                    : '🔒 TERKUNCI (SERVER GATE)';
            } else {
                $filteredItem['locked_badge'] = $locale === 'en'
                    ? '✅ UNLOCKED'
                    : '✅ TERBUKA';
            }

            $result[] = $filteredItem;
        }

        return $result;
    }

    /**
     * Filter Data Dictionary tables for the resolved tier.
     * In FREE_SANDBOX, only the first 2 tables (Phase 2.1 preview) include full column definitions;
     * tables 3+ have their raw column DDL stripped on the server to prevent F12 DOM scraping.
     *
     * @param  array<int, array<string, mixed>>  $tables
     * @return array<int, array<string, mixed>>
     */
    public function filterDataDictionaryForTier(array $tables, string $tier, string $locale = 'id'): array
    {
        $normalizedTier = $this->normalizeRequestedTierSlug($tier);
        if ($normalizedTier !== 'FREE_SANDBOX') {
            return array_map(static function (array $table): array {
                $table['is_locked_in_sandbox'] = false;
                $table['ddl_locked'] = false;

                return $table;
            }, $tables);
        }

        $isEn = $locale === 'en';
        $filtered = [];

        foreach (array_values($tables) as $idx => $table) {
            if ($idx < 2) {
                $table['is_locked_in_sandbox'] = false;
                $table['ddl_locked'] = false;
                $table['sandbox_phase_tag'] = 'PHASE 2.1 (PREVIEW TERBUKA)';
                $filtered[] = $table;

                continue;
            }

            $columnCount = is_array($table['columns'] ?? null) ? count($table['columns']) : 8;
            $filtered[] = [
                'table' => $table['table'] ?? 'pos_locked_table_'.($idx + 1),
                'description' => $table['description'] ?? '',
                'is_locked_in_sandbox' => true,
                'ddl_locked' => true,
                'raw_ddl' => '-- LOCKED IN FREE SANDBOX (ZERO-LEAK SERVER GATE)',
                'sandbox_phase_tag' => $isEn ? '🔒 PHASE 2.2+ (LOCKED IN SANDBOX)' : '🔒 FASE 2.2+ (TERKUNCI DI SANDBOX)',
                'locked_columns_count' => $columnCount,
                'columns' => [
                    'id' => 'CHAR(26) PRIMARY KEY (ULID)',
                    '__locked_ddl__' => $isEn
                        ? "🔒 [{$columnCount} domain columns & foreign keys withheld on server in Free Sandbox. Upgrade to Single-App Pass IDR 299k to unlock.]"
                        : "🔒 [{$columnCount} kolom domain & foreign key ditahan di server pada Free Sandbox. Upgrade ke Single-App Pass Rp 299rb untuk membuka.]",
                ],
                'indexes' => [
                    $isEn ? '🔒 Composite keyset cursor indexes locked on server' : '🔒 Indeks komposit keyset cursor dikunci di server',
                ],
            ];
        }

        return $filtered;
    }

    /**
     * Filter SYMBOL_MANIFEST.json for the resolved tier.
     *
     * @param  array<string, mixed>  $symbolManifest
     * @return array<string, mixed>
     */
    public function filterSymbolManifestForTier(array $symbolManifest, string $tier, string $locale = 'id'): array
    {
        if ($tier !== 'FREE_SANDBOX') {
            $symbolManifest['is_locked_in_sandbox'] = false;

            return $symbolManifest;
        }

        $isEn = $locale === 'en';
        $tables = is_array($symbolManifest['canonical_tables'] ?? null)
            ? array_slice($symbolManifest['canonical_tables'], 0, 2)
            : [];

        return [
            'manifest_version' => $symbolManifest['manifest_version'] ?? 'v12.0-SANDBOX-PREVIEW',
            'is_locked_in_sandbox' => true,
            'sector_code' => $symbolManifest['sector_code'] ?? 'SECTOR_01_FNB_HOSPITALITY',
            'stack_code' => $symbolManifest['stack_code'] ?? 'M1_LARAVEL_FILAMENT_MODULAR',
            'primary_key_rule' => 'STRICT_26_CHAR_ULID_ONLY',
            'pagination_rule' => 'STRICT_O1_CURSOR_PAGINATE_ONLY',
            'canonical_tables_preview' => $tables,
            'server_gate_notice' => $isEn
                ? '🔒 Full SYMBOL_MANIFEST.json (models, services, routes, state machine enums & Canary IP seal) is withheld on the server in Free Sandbox mode.'
                : '🔒 SYMBOL_MANIFEST.json lengkap (daftar model, service, route, enum state machine & segel Canary IP) ditahan di server pada mode Free Sandbox.',
        ];
    }

    /**
     * Build Contextual Risk & Value Upsell Advice when a user clicks a locked phase (2..7).
     *
     * @return array<string, mixed>
     */
    public function buildContextualUpsellAdvice(int $phase, string $sectorCodeOrName, string $locale = 'id'): array
    {
        $isEn = $locale === 'en';
        $catalog = PosProgressiveDisclosureCatalog::phaseUpsellCatalog();
        $entry = $catalog[$phase] ?? $catalog[3];

        $sectors = ProjectOsMatrixCatalog::sectors();
        $sectorMeta = collect($sectors)->firstWhere('code', $sectorCodeOrName) ?? ($sectors[0] ?? []);
        $sectorName = $sectorMeta['name'][$locale] ?? $sectorMeta['name']['id'] ?? $sectorCodeOrName;

        return [
            'phase' => $phase,
            'sector_name' => $sectorName,
            'headline' => $entry['title'],
            'headline_text' => $entry['title'][$locale] ?? $entry['title']['id'],
            'risk_if_skipped' => $entry['risk_warning'],
            'risk_warning' => $entry['risk_warning'][$locale] ?? $entry['risk_warning']['id'],
            'value_proposition' => $entry['value_proposition'],
            'value_proposition_text' => $entry['value_proposition'][$locale] ?? $entry['value_proposition']['id'],
            'sector_context_note' => $isEn
                ? "Configured for sector: {$sectorName}. All ULID tables, state machines, and Zone A+B UI contracts are ready on the server."
                : "Dikonfigurasi khusus untuk sektor: {$sectorName}. Seluruh tabel ULID, state machine, dan kontrak UI Zona A+B telah siap di server.",
            'cta_retail' => [
                'package_slug' => 'retail_single_app',
                'price_idr' => 299000,
                'label' => [
                    'id' => '🔓 Buka Kunci Semua 7 Fase (Rp 299.000 Single-App Pass)',
                    'en' => '🔓 Unlock All 7 Phases (IDR 299,000 Single-App Pass)',
                ],
                'url' => '/pricing?track=retail&tier=retail_single_app#katalog-paket-Harga',
            ],
            'cta_mitra' => [
                'package_slug' => 'monolith',
                'label' => [
                    'id' => '🛟 Terima Beres Dikodingkan Tim (Mitra DP 30%)',
                    'en' => '🛟 Hand Over to NeriahPro Partner Team (From 30% DP)',
                ],
                'url' => '/pricing?track=mitra&tier=monolith#katalog-paket-Harga',
            ],
            'cta_single_app' => [
                'package_slug' => 'retail_single_app',
                'price_idr' => 299000,
                'label' => $isEn
                    ? '🔓 Unlock All 7 Phases for This App (IDR 299,000 One-Time)'
                    : '🔓 Buka Seluruh 7 Fase untuk Aplikasi Ini (Rp 299.000 Sekali Bayar)',
                'url' => '/pricing?track=retail&tier=retail_single_app#katalog-paket-Harga',
            ],
            'cta_pro_retail' => [
                'package_slug' => 'retail_pro',
                'price_idr' => 399000,
                'label' => $isEn
                    ? '⚡ Upgrade to Pro Agentic (IDR 399,000/mo • 8 Blueprints + Canary IP)'
                    : '⚡ Upgrade ke Pro Agentic (Rp 399.000/bln • 8 Blueprint + Canary IP)',
                'url' => '/pricing?track=retail&tier=retail_pro#katalog-paket-Harga',
            ],
            'cta_mitra_rescue' => [
                'package_slug' => 'monolith',
                'label' => $isEn
                    ? '🤝 Tired of Coding? Hand Over to NeriahPro Partner Team (From 30% DP)'
                    : '🤝 Pusing Coding? Serahkan ke Tim Mitra NeriahPro (Mulai DP 30%)',
                'url' => '/pricing?track=mitra&tier=monolith#katalog-paket-Harga',
            ],
        ];
    }

    /**
     * Build the Enterprise Upscale Advisor Banner when ULID table count > 18.
     *
     * @param  array<string, mixed>|int  $schemaOrTableCount
     * @return ?array<string, mixed>
     */
    public function buildEnterpriseUpscaleAdvisor(array|int $schemaOrTableCount, string $tier = 'SINGLE_APP_PASS', string $locale = 'id'): ?array
    {
        if (is_int($schemaOrTableCount)) {
            $tableCount = $schemaOrTableCount;
        } else {
            $tables = is_array($schemaOrTableCount['data_dictionary'] ?? null) ? $schemaOrTableCount['data_dictionary'] : [];
            $tableCount = count($tables);
        }

        if ($tableCount <= 18) {
            return null;
        }

        $isEn = $locale === 'en';
        $recommendedModules = [
            [
                'code' => 'P13_UU_PDP_BLIND_INDEX',
                'label' => $isEn ? 'AES-256-GCM + HMAC-SHA256 Blind Index Vault' : 'Brankas Enkripsi AES-256-GCM + HMAC-SHA256 Blind Index',
            ],
            [
                'code' => 'P08_IMMUTABLE_AUDIT_TRAIL',
                'label' => $isEn ? 'Immutable Ledger & Forensic Excel Audit Log' : 'Buku Besar Imutabel & Audit Log Forensik Excel',
            ],
            [
                'code' => 'P20_SEO_GEO_TOP3_CITATION',
                'label' => $isEn ? 'Top-3 AI Citation Engine (/llms.txt + @graph JSON-LD)' : 'Mesin Top-3 AI Citation (/llms.txt + @graph JSON-LD)',
            ],
        ];

        return [
            'is_triggered' => true,
            'is_high_complexity' => true,
            'table_count' => $tableCount,
            'threshold' => 18,
            'current_tier' => $tier,
            'badge' => $isEn
                ? "💡 ENTERPRISE COMPLEXITY DETECTED ({$tableCount} ULID TABLES)"
                : "💡 KOMPLEKSITAS SKALA BESAR TERDETEKSI ({$tableCount} TABEL ULID)",
            'title' => $isEn
                ? "Your system has {$tableCount} interconnected ULID database tables (High-Scale Architecture)."
                : "Sistem Anda memiliki {$tableCount} tabel database ULID yang saling berelasi (Skala Menengah-Besar).",
            'recommendation' => $isEn
                ? 'For maximum security and zero-downtime operations, we strongly recommend enabling: (1) Privacy Law Encryption Vault (AES-256-GCM + HMAC Blind Index), (2) Immutable Excel Audit Trail, and (3) Top-3 AI Citation SEO/GEO Engine.'
                : 'Untuk keamanan maksimal dan operasional bebas kendala, kami sangat menyarankan Anda mengaktifkan: (1) Brankas Enkripsi UU PDP (AES-256-GCM + HMAC Blind Index), (2) Audit Trail Ekspor Excel, serta (3) Mesin SEO/GEO Top-3 AI Citation.',
            'recommended_modules' => $recommendedModules,
            'recommended_pillars' => $recommendedModules,
        ];
    }

    /**
     * Calculate the 100% Retail-to-Mitra Rescue Credit ("Tombol Penyelamat Konversi ke Mitra Proyek").
     * Any fee already paid in Retail SaaS is 100% credited/deducted from the Mitra Project 30% DP!
     *
     * @return array<string, mixed>
     */
    public function calculateRetailToMitraRescueCredit(
        object|int|null $userOrRab,
        string|object|null $projectIdOrUser = null,
        int $estimatedMitraRabOrOverride = 32500000,
        int $dpPercentage = 30,
        ?string $currentRetailTier = null,
        string $locale = 'id'
    ): array {
        $paidRetailOverride = 0;
        if (is_int($userOrRab)) {
            $estimatedMitraRabIdr = $userOrRab;
            $user = is_object($projectIdOrUser) ? $projectIdOrUser : null;
            $paidRetailOverride = $estimatedMitraRabOrOverride;
        } else {
            $user = $userOrRab;
            $estimatedMitraRabIdr = $estimatedMitraRabOrOverride;
        }

        $isEn = $locale === 'en';
        $safeRab = max(2500000, $estimatedMitraRabIdr);
        $safeDpPct = max(10, min(100, $dpPercentage));
        $grossDpIdr = (int) round($safeRab * ($safeDpPct / 100));

        $paidRetailCreditIdr = max(0, $paidRetailOverride);

        // 1. Check concrete settled Retail transactions for user if authenticated
        if ($paidRetailCreditIdr === 0 && $user !== null && isset($user->id)) {
            $settledRetailSum = (int) Transaction::query()
                ->where('user_id', (string) $user->id)
                ->where('status', 'settlement')
                ->where('total_idr', '<', 2500000)
                ->sum('total_idr');

            if ($settledRetailSum > 0) {
                $paidRetailCreditIdr = $settledRetailSum;
            }
        }

        // 2. Fallback to tier-based credit simulation if user selects a Retail tier in Studio
        if ($paidRetailCreditIdr === 0 && $currentRetailTier !== null) {
            $paidRetailCreditIdr = match (strtolower(trim($currentRetailTier))) {
                'retail_starter', 'starter' => 149000,
                'retail_single_app', 'single_app_pass' => 299000,
                'retail_pro', 'pro_retail', 'pro' => 399000,
                'retail_agency', 'agency' => 990000,
                'retail_whitelabel', 'agency_partner', 'whitelabel' => 1490000,
                default => 0,
            };
        }

        $netDpAfterCreditIdr = max(0, $grossDpIdr - $paidRetailCreditIdr);

        return [
            'eligible' => true,
            'estimated_mitra_rab_idr' => $safeRab,
            'dp_percentage' => $safeDpPct,
            'gross_dp_idr' => $grossDpIdr,
            'original_dp_30_idr' => $grossDpIdr,
            'retail_paid_credit_idr' => $paidRetailCreditIdr,
            'credited_retail_fee_idr' => $paidRetailCreditIdr,
            'net_dp_payable_idr' => $netDpAfterCreditIdr,
            'credit_applied_100_percent' => true,
            'rescue_button_label' => $isEn
                ? '🛟 Need NeriahPro Engineers? Convert to Turnkey Partner Project (100% Retail Fee Credited to DP)'
                : '🛟 Butuh Bantuan Engineer NeriahPro? Konversi ke Mitra Proyek (100% Biaya Retail Memotong DP)',
            'guarantee_statement' => [
                'id' => '100% biaya lisensi paket Retail SaaS yang sudah Anda bayar otomatis menjadi pemotong uang muka (DP 30%) Proyek Mitra Terima Beres. Tidak ada biaya yang hangus!',
                'en' => '100% of any Retail SaaS license fee you have paid automatically deducts your Turnkey Partner Project 30% Down Payment (DP). Zero wasted cost!',
            ],
            'guarantee_summary' => $isEn
                ? '100% of any Retail SaaS license fee you have paid automatically deducts your Turnkey Partner Project 30% Down Payment (DP). Zero wasted cost!'
                : '100% biaya lisensi paket Retail SaaS yang sudah Anda bayar otomatis menjadi pemotong uang muka (DP 30%) Proyek Mitra Terima Beres. Tidak ada biaya yang hangus!',
        ];
    }

    /**
     * Tahap A: Smart Idea Intake & Auto-Mapping ("Deteksi Sektor & Modul Otomatis dari Ide Saya").
     * Deterministically maps free-form user idea text to the best-matching Sector #01–#12,
     * Transaction Primitives, Visual UI Variant (Zone A + Zone B), and Recommended Stack.
     *
     * @return array<string, mixed>
     */
    public function autoDetectSectorAndModulesFromIdea(string $ideaText, string $locale = 'id'): array
    {
        $isEn = $locale === 'en';
        $normalized = strtolower(trim($ideaText));

        $keywordRules = [
            'SECTOR_01_COMMERCE_OMNICHANNEL' => ['kafe', 'cafe', 'restoran', 'resto', 'kuliner', 'kopi', 'dapur', 'kds', 'pos', 'kasir', 'toko', 'minimarket', 'supermarket', 'grosir', 'eceran', 'barcode', 'stok', 'omnichannel', 'sku', 'marketplace'],
            'SECTOR_02_HEALTHCARE_CLINICAL' => ['klinik', 'rumah sakit', 'dokter', 'pasien', 'rekam medis', 'rme', 'satusehat', 'bpjs', 'apotek', 'farmasi', 'resep obat', 'odontogram', 'gigi', 'lab medis', 'healthcare'],
            'SECTOR_03_LOGISTICS_SUPPLY_CHAIN' => ['logistik', 'ekspedisi', 'kurir', 'armada', 'truk', 'kargo', 'pengiriman', 'resi', 'manifest', 'surat jalan', 'pod', 'gudang sortir', 'fleet'],
            'SECTOR_04_EDUTECH_CERTIFICATION' => ['sekolah', 'kampus', 'kursus', 'bimbel', 'lms', 'ujian', 'cbt', 'sertifikat', 'guru', 'siswa', 'mahasiswa', 'edutech', 'pelatihan', 'bootcamp', 'pesantren'],
            'SECTOR_05_FINTECH_LEDGER' => ['koperasi', 'simpan pinjam', 'fintech', 'bmt', 'shu', 'angsuran', 'pinjaman', 'kredit', 'tabungan anggota', 'pencairan dana', 'mikrofinansial', 'akuntansi', 'ledger'],
            'SECTOR_06_HOSPITALITY_BOOKING' => ['hotel', 'villa', 'resort', 'penginapan', 'kamar', 'booking', 'reservasi', 'salon', 'spa', 'coworking', 'sewa lapangan', 'travel', 'tour'],
            'SECTOR_07_MANUFACTURING_IOT' => ['pabrik', 'manufaktur', 'produksi', 'bom', 'bahan baku', 'perakitan', 'qc', 'mesin', 'shift pabrik', 'work order', 'mrp', 'batch produksi', 'iot'],
            'SECTOR_08_REAL_ESTATE_CONSTRUCTION' => ['properti', 'perumahan', 'developer properti', 'konstruksi', 'kontraktor', 'kos', 'apartemen', 'kavling', 'rab proyek', 'mandor', 'kpr', 'real estate'],
            'SECTOR_09_HRIS_PAYROLL' => ['hris', 'payroll', 'gaji', 'absensi', 'karyawan', 'cuti', 'shift kerja', 'pph 21', 'bpjs ketenagakerjaan', 'agensi', 'konsultan', 'firma hukum', 'timesheet'],
            'SECTOR_10_GOVTECH_PUBLIC' => ['pemda', 'pemerintah', 'dinas', 'desa', 'bumn', 'spbe', 'e-office', 'disposisi', 'surat menyurat', 'perizinan', 'pelayanan publik', 'tte', 'asn'],
            'SECTOR_11_AGRO_FISHERY' => ['pertanian', 'perkebunan', 'perikanan', 'tambak', 'peternakan', 'sawit', 'panen', 'pupuk', 'agro', 'cold storage'],
            'SECTOR_12_NONPROFIT_PHILANTHROPY' => ['yayasan', 'lembaga sosial', 'ngo', 'zakat', 'wakaf', 'donasi', 'amal', 'masjid', 'gereja', 'rumah ibadah', 'panti asuhan', 'csr', 'beasiswa', 'kemanusiaan', 'hibah'],
        ];

        $bestSector = 'SECTOR_01_COMMERCE_OMNICHANNEL';
        $highestScore = 0;
        $matchedKeywords = [];

        foreach ($keywordRules as $sectorCode => $keywords) {
            $score = 0;
            $hits = [];
            foreach ($keywords as $kw) {
                if ($normalized !== '' && str_contains($normalized, $kw)) {
                    $score += strlen($kw) >= 6 ? 3 : 2;
                    $hits[] = $kw;
                }
            }
            if ($score > $highestScore) {
                $highestScore = $score;
                $bestSector = $sectorCode;
                $matchedKeywords = $hits;
            }
        }

        $sectors = ProjectOsMatrixCatalog::sectors();
        $sectorTaxonomy = collect($sectors)->firstWhere('code', $bestSector) ?? ($sectors[0] ?? []);
        $uiCatalog = PosInclusiveCommercialCatalog::allSectorVisualUiCatalog();
        $recommendedUi = $uiCatalog[$bestSector][0]['code'] ?? 'UI_01A_POS_SPLIT_TERMINAL';

        // Pick stack based on scale keywords
        $recommendedStack = 'STACK_M1_LARAVEL_MONOLITH';
        if (str_contains($normalized, 'nextjs') || str_contains($normalized, 'react') || str_contains($normalized, 'mobile')) {
            $recommendedStack = 'STACK_D1_LARAVEL_NEXTJS';
        } elseif (str_contains($normalized, 'ai ') || str_contains($normalized, 'microservice') || str_contains($normalized, 'golang')) {
            $recommendedStack = 'STACK_S1_POLYGLOT_HYBRID';
        }

        $recommendedTrack = str_contains($normalized, 'terima beres') || str_contains($normalized, 'dikodingkan')
            ? 'MITRA'
            : 'RETAIL';

        $recommendedTier = $bestSector === 'SECTOR_12_NONPROFIT_PHILANTHROPY'
            ? 'impact_nonprofit_grant'
            : ($recommendedTrack === 'MITRA' ? 'monolith' : 'retail_pro');

        $confidenceScore = $highestScore > 0 ? min(99, 82 + ($highestScore * 3)) : 85;
        $sectorNameId = $sectorTaxonomy['name']['id'] ?? $bestSector;
        $sectorNameEn = $sectorTaxonomy['name']['en'] ?? $bestSector;

        return [
            'detected_sector_code' => $bestSector,
            'detected_sector_number' => $sectorTaxonomy['number'] ?? '01',
            'detected_sector_name' => $sectorTaxonomy['name'][$locale] ?? $sectorNameId,
            'matched_keywords' => $matchedKeywords !== [] ? $matchedKeywords : ['auto-baseline'],
            'confidence_percent' => $confidenceScore,
            'confidence_score' => $confidenceScore,
            'recommended_stack_code' => $recommendedStack,
            'recommended_ui_variant' => $recommendedUi,
            'recommended_track' => $recommendedTrack,
            'recommended_tier' => $recommendedTier,
            'detected_primitives' => $sectorTaxonomy['default_primitives'] ?? ['PRIMITIVE_POS_CART_INVENTORY', 'PRIMITIVE_DOUBLE_ENTRY_LEDGER'],
            'summary' => [
                'id' => "Dipetakan ke {$sectorNameId} dengan varian UI {$recommendedUi} & stack {$recommendedStack}",
                'en' => "Mapped to {$sectorNameEn} with UI variant {$recommendedUi} & stack {$recommendedStack}",
            ],
            'summary_message' => $isEn
                ? "✨ Auto-Detection Complete: Mapped to {$sectorNameEn} with Zone A Cockpit + Zone B Public Scrollytelling and L1–L4 5-Division Menus."
                : "✨ Deteksi Otomatis Berhasil: Dipetakan ke {$sectorNameId} lengkap dengan Kokpit Zona A + Scrollytelling Publik Zona B serta Peta Menu L1–L4 5 Divisi.",
        ];
    }

    /**
     * Tahap B: Layar 1 — "Website Architecture Proposal" (Ruang Kagum & Validasi Bisnis — First-Landing WOW View).
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    public function buildWebsiteArchitectureProposal(array $schema, string $sectorCode, string $locale = 'id'): array
    {
        $isEn = $locale === 'en';
        $sectors = ProjectOsMatrixCatalog::sectors();
        $sectorTaxonomy = collect($sectors)->firstWhere('code', $sectorCode) ?? ($sectors[0] ?? []);
        $seoGeoProfile = PosSeoGeoAndZeroFailureCatalog::sectorCapsule($sectorCode, $locale);
        $tables = is_array($schema['data_dictionary'] ?? null) ? $schema['data_dictionary'] : [];
        $tableCount = max(12, count($tables));
        $menuMatrix = is_array($schema['enterprise_menu_matrix'] ?? null) ? $schema['enterprise_menu_matrix'] : [];

        // Economic value calculation: each ULID table + state machine + UI screen saves ~14 senior engineering hours
        $hoursSaved = $tableCount * 14;
        $hourlySeniorRateIdr = 350000;
        $engineeringValueSavedIdr = $hoursSaved * $hourlySeniorRateIdr;

        $pricingEstimate = is_array($schema['pricing_estimate'] ?? null) ? $schema['pricing_estimate'] : [];
        $mitraRabIdr = (int) ($pricingEstimate['quotation_idr'] ?? 32500000);
        $sectorNameId = $sectorTaxonomy['name']['id'] ?? $sectorCode;
        $sectorNameEn = $sectorTaxonomy['name']['en'] ?? $sectorCode;

        $divisions = [
            [
                'code' => 'L1_CORE_OPERATIONS',
                'label' => ['id' => 'Operasional Harian & Transaksi Inti', 'en' => 'Core Daily Operations & Transactions'],
                'menu_count' => 5,
                'benefit' => ['id' => 'Layar kerja cepat untuk staf lapangan/kasir tanpa salah input.', 'en' => 'High-speed workspace for frontline staff with zero input errors.'],
            ],
            [
                'code' => 'L2_INVENTORY_FULFILLMENT',
                'label' => ['id' => 'Manajemen Stok, Jadwal & Kapasitas', 'en' => 'Inventory, Scheduling & Capacity Control'],
                'menu_count' => 5,
                'benefit' => ['id' => 'Pencegahan otomatis stok minus dan bentrok jadwal (SELECT ... FOR UPDATE).', 'en' => 'Automatic prevention of negative stock and schedule collisions.'],
            ],
            [
                'code' => 'L3_FINANCE_LEDGER',
                'label' => ['id' => 'Keuangan, Kas Seimbang & Pajak', 'en' => 'Finance, Balanced Ledger & Tax'],
                'menu_count' => 5,
                'benefit' => ['id' => 'Buku besar akuntansi seimbang (Debit = Kredit) & rekonsiliasi otomatis.', 'en' => 'Balanced double-entry ledger (Debit = Credit) & automated reconciliation.'],
            ],
            [
                'code' => 'L4_CRM_RETENTION',
                'label' => ['id' => 'Portal Pelanggan, Loyalitas & SEO/GEO', 'en' => 'Customer Portal, Loyalty & SEO/GEO'],
                'menu_count' => 5,
                'benefit' => ['id' => 'Halaman publik 60fps + kutipan AI Top-3 (/llms.txt + JSON-LD).', 'en' => '60fps public portal + Top-3 AI Search citation (/llms.txt + JSON-LD).'],
            ],
            [
                'code' => 'L5_GOVERNANCE_AUDIT',
                'label' => ['id' => 'Pengawasan Direksi, RBAC & Audit Trail', 'en' => 'Executive Governance, RBAC & Audit Trail'],
                'menu_count' => 5,
                'benefit' => ['id' => 'Jejak audit SHA-256 siap ekspor Excel & brankas enkripsi UU PDP.', 'en' => 'SHA-256 audit trail with Excel export & UU PDP encryption vault.'],
            ],
        ];

        return [
            'screen_code' => 'SCREEN_1_WEBSITE_ARCHITECTURE_PROPOSAL_WOW',
            'sector_number' => $sectorTaxonomy['number'] ?? '01',
            'sector_name' => $sectorTaxonomy['name'][$locale] ?? $sectorNameId,
            'executive_headline' => [
                'id' => 'Proposal Eksekutif Arsitektur Bisnis & Sistem — '.$sectorNameId,
                'en' => 'Executive Business & System Architecture Proposal — '.$sectorNameEn,
            ],
            'executive_subheadline' => [
                'id' => 'Sistem Anda dirancang dengan standar rekayasa enterprise: memadukan kokpit kerja staf (Zona A) dan halaman publik 60fps + SEO/GEO AI (Zona B), ditopang oleh '.$tableCount.' tabel PostgreSQL 16 ber-Primary Key ULID 26 karakter.',
                'en' => 'Your system is architected with enterprise precision: combining a staff cockpit (Zone A) and a 60fps public web + AI SEO/GEO engine (Zone B), backed by '.$tableCount.' 26-char ULID PostgreSQL 16 tables.',
            ],
            'executive_narrative' => $isEn
                ? 'Your system is architected with enterprise-grade precision: combining a high-speed operational cockpit for daily staff (Zone A) and a 60fps interactive public web experience + Top-3 AI Citation SEO/GEO engine (Zone B), backed by '.$tableCount.' strict 26-char ULID PostgreSQL 16 tables.'
                : 'Sistem Anda dirancang dengan standar rekayasa enterprise: memadukan kokpit kerja berkecepatan tinggi untuk staf harian (Zona A) dan halaman publik interaktif 60fps + mesin kutipan AI Top-3 SEO/GEO (Zona B), ditopang oleh '.$tableCount.' tabel PostgreSQL 16 ber-Primary Key ULID 26 karakter.',
            'operational_divisions_count' => count($menuMatrix['divisions'] ?? []) > 0 ? count($menuMatrix['divisions']) : 5,
            'total_menus' => 25,
            'divisions' => $divisions,
            'total_ulid_tables' => $tableCount,
            'engineering_hours_saved' => $hoursSaved,
            'engineering_value_saved_idr' => $engineeringValueSavedIdr,
            'zone_a_public' => [
                'id' => $seoGeoProfile['answer_capsule']['id'] ?? 'Halaman Publik Interaktif 60fps + Schema.org @graph + /llms.txt',
                'en' => $seoGeoProfile['answer_capsule']['en'] ?? '60fps Interactive Public Web + Schema.org @graph + /llms.txt',
            ],
            'zone_b_workspace' => [
                'id' => $sectorTaxonomy['primary_archetype']['id'] ?? 'Kokpit Operasional Bebas Jank & Keyboard-First',
                'en' => $sectorTaxonomy['primary_archetype']['en'] ?? 'Zero-Jank Keyboard-First Operational Cockpit',
            ],
            'cta_self_dev' => [
                'label' => [
                    'id' => '💻 Saya Ingin Koding Sendiri dengan AI (Buka 3-Pane Studio) →',
                    'en' => '💻 I Want to Code Myself with AI (Open 3-Pane Studio) →',
                ],
            ],
            'cta_mitra_turnkey' => [
                'label' => [
                    'id' => '🤝 Terima Beres Dikodingkan Tim NeriahPro (Lihat RAB & Kontrak SPK) →',
                    'en' => '🤝 Hand Over Turnkey to NeriahPro Team (View RAB & SPK Contract) →',
                ],
            ],
            'seo_geo_schemas' => $seoGeoProfile['schema_types'] ?? ['Organization', 'WebSite', 'SoftwareApplication'],
            'economic_valuation' => [
                'engineering_hours_saved' => $hoursSaved,
                'senior_hourly_rate_idr' => $hourlySeniorRateIdr,
                'engineering_value_saved_idr' => $engineeringValueSavedIdr,
                'retail_single_app_idr' => 299000,
                'retail_pro_monthly_idr' => 399000,
                'mitra_turnkey_rab_idr' => $mitraRabIdr,
                'mitra_dp_30_idr' => (int) round($mitraRabIdr * 0.30),
                'mitra_uat_50_idr' => (int) round($mitraRabIdr * 0.50),
                'mitra_bast_20_idr' => (int) round($mitraRabIdr * 0.20),
            ],
        ];
    }

    /**
     * Tahap C: Layar 2 — Build Vibe Coder Guided View step-by-step instructions.
     *
     * @param  array<int, array<string, mixed>>  $phasedPrompts
     * @return array<int, array<string, mixed>>
     */
    public function buildVibeCoderGuidedSteps(array $phasedPrompts, string $tier, string $locale = 'id'): array
    {
        $isEn = $locale === 'en';
        $friendlyGuides = [
            1 => [
                'step_badge' => $isEn ? 'STEP 1 // FOUNDATION' : 'LANGKAH 1 // FONDASI PROYEK',
                'friendly_title' => $isEn ? 'Prepare Project Folders & Strict Architecture Rules' : 'Siapkan Struktur Folder & Aturan Dasar Proyek',
                'human_explanation' => $isEn
                    ? 'Copy the Step 1 prompt on the right and paste it into Cursor, Windsurf, Claude Code, or Antigravity IDE. This sets up the clean folder structure and locks the SYMBOL_MANIFEST rules so your AI never hallucinates file names.'
                    : 'Salin Prompt Langkah 1 di sebelah kanan lalu tempelkan ke Cursor, Windsurf, Claude Code, atau Antigravity IDE. Langkah ini membuat struktur folder bersih dan mengunci aturan dasar agar AI koding Anda tidak mengarang nama file.',
                'ide_instruction' => $isEn
                    ? '1. Open Cursor / Claude Code in an empty folder -> 2. Paste Prompt 1 -> 3. Wait until folders & base configs are created.'
                    : '1. Buka Cursor / Claude Code di folder proyek -> 2. Tempel Prompt Langkah 1 -> 3. Tunggu hingga struktur folder & konfigurasi dasar selesai dibuat.',
            ],
            2 => [
                'step_badge' => $isEn ? 'STEP 2 // DATABASE TABLES' : 'LANGKAH 2 // TABEL DATABASE INTI',
                'friendly_title' => $isEn ? 'Build All 26-Char ULID Database Tables' : 'Bangun Seluruh Tabel Database Berstandar ULID',
                'human_explanation' => $isEn
                    ? 'This step generates all PostgreSQL 16 database migrations using 26-character ULID primary keys so your system scales to millions of rows without slowing down.'
                    : 'Langkah ini membuat seluruh tabel penyimpanan data (migrasi database) berstandar ULID 26-karakter agar aplikasi Anda sanggup menampung jutaan transaksi tanpa melambat.',
                'ide_instruction' => $isEn
                    ? 'Paste Prompt 2 into your AI IDE and run `php artisan migrate` to create all database tables.'
                    : 'Tempel Prompt Langkah 2 ke AI IDE Anda lalu jalankan perintah migrasi untuk membangun seluruh tabel database.',
            ],
            3 => [
                'step_badge' => $isEn ? 'STEP 3 // ANTI-COLLISION ENGINE' : 'LANGKAH 3 // MESIN TRANSAKSI ANTI-BENTROK',
                'friendly_title' => $isEn ? 'Activate Safe Payment, Stock & Double-Entry Ledger Logic' : 'Aktifkan Mesin Kas, Stok & Pembayaran Anti-Bentrok',
                'human_explanation' => $isEn
                    ? 'Protects your business from double-booking, negative inventory, and duplicate webhook payments using atomic database row locks.'
                    : 'Melindungi bisnis Anda dari kesalahan saldo minus, pesanan ganda di detik yang sama, atau duplikasi pembayaran otomatis.',
                'ide_instruction' => $isEn
                    ? 'Paste Prompt 3 into your AI IDE to generate the atomic Service Actions and State Machine Guards.'
                    : 'Tempel Prompt Langkah 3 ke AI IDE Anda untuk membangun mesin pemroses transaksi dan pengunci saldo otomatis.',
            ],
            4 => [
                'step_badge' => $isEn ? 'STEP 4 // ZONE A + B SCREENS' : 'LANGKAH 4 // TAMPILAN LAYAR ZONA A & B',
                'friendly_title' => $isEn ? 'Build Staff Operational Cockpit & Interactive Public Website' : 'Bangun Layar Kerja Staf (Zona A) & Website Publik (Zona B)',
                'human_explanation' => $isEn
                    ? 'Creates both the fast keyboard-friendly dashboard for your daily staff (Zone A) and the sleek 60fps public customer page (Zone B).'
                    : 'Membangun layar kerja cepat untuk staf operasional harian (Zona A) sekaligus halaman depan website publik yang memukau pelanggan (Zona B).',
                'ide_instruction' => $isEn
                    ? 'Paste Prompt 4 into your AI IDE and run `npm run build` to compile the UI.'
                    : 'Tempel Prompt Langkah 4 ke AI IDE Anda lalu jalankan `npm run build` untuk menyalakan tampilan layar.',
            ],
            5 => [
                'step_badge' => $isEn ? 'STEP 5 // PRIVACY & RBAC' : 'LANGKAH 5 // KEAMANAN DATA & HAK AKSES',
                'friendly_title' => $isEn ? 'Lock Role Permissions & Privacy Law Data Encryption' : 'Kunci Hak Akses Staf & Enkripsi Data Pribadi (UU PDP)',
                'human_explanation' => $isEn
                    ? 'Separates staff permissions across 5 operational divisions and encrypts sensitive customer data automatically.'
                    : 'Memisahkan hak akses kasir, gudang, keuangan, dan manajer serta mengenkripsi data sensitif pelanggan sesuai standar UU PDP.',
                'ide_instruction' => $isEn
                    ? 'Paste Prompt 5 into your AI IDE to activate role policies and encryption traits.'
                    : 'Tempel Prompt Langkah 5 ke AI IDE Anda untuk mengaktifkan pembagian hak akses dan enkripsi data.',
            ],
            6 => [
                'step_badge' => $isEn ? 'STEP 6 // SEO/GEO & DOCKER' : 'LANGKAH 6 // MESIN SEO/GEO AI & SERVER',
                'friendly_title' => $isEn ? 'Enable Top-3 AI Search Citation (/llms.txt) & Production Server' : 'Nyalakan Mesin Pencari AI (/llms.txt) & Konfigurasi Server',
                'human_explanation' => $isEn
                    ? 'Makes your business easily recommended by ChatGPT Search, Perplexity, Gemini, and Google with automatic Schema.org and /llms.txt.'
                    : 'Membuat bisnis Anda mudah ditemukan dan direkomendasikan di urutan teratas oleh ChatGPT Search, Perplexity, Gemini, dan Google.',
                'ide_instruction' => $isEn
                    ? 'Paste Prompt 6 into your AI IDE to generate `/llms.txt`, JSON-LD schemas, and deployment scripts.'
                    : 'Tempel Prompt Langkah 6 ke AI IDE Anda untuk memasang `/llms.txt`, Schema.org, dan skrip server.',
            ],
            7 => [
                'step_badge' => $isEn ? 'STEP 7 // AUTO-TEST & HANDOVER' : 'LANGKAH 7 // UJI OTOMATIS & SERAH TERIMA',
                'friendly_title' => $isEn ? 'Run Automated Quality Checks & Canary IP Protection' : 'Jalankan Pengujian Otomatis & Segel Kepemilikan Kode',
                'human_explanation' => $isEn
                    ? 'Verifies every feature with automated tests so you can launch with 100% confidence.'
                    : 'Memastikan seluruh fitur berjalan tanpa bug melalui pengujian otomatis sebelum aplikasi diluncurkan ke pelanggan.',
                'ide_instruction' => $isEn
                    ? 'Paste Prompt 7 into your AI IDE and run `php artisan test` to verify 100% green status.'
                    : 'Tempel Prompt Langkah 7 ke AI IDE Anda lalu jalankan `php artisan test` hingga seluruh pengujian lulus hijau.',
            ],
        ];

        $steps = [];
        foreach ($phasedPrompts as $idx => $phaseItem) {
            $phaseNum = (int) ($phaseItem['phase'] ?? ($idx + 1));
            $guide = $friendlyGuides[$phaseNum] ?? $friendlyGuides[1];

            $steps[] = [
                'phase' => $phaseNum,
                'step_badge' => $guide['step_badge'],
                'friendly_title' => $guide['friendly_title'],
                'human_explanation' => $guide['human_explanation'],
                'ide_instruction' => $guide['ide_instruction'],
                'is_locked' => (bool) ($phaseItem['is_locked'] ?? ($tier === 'FREE_SANDBOX' && $phaseNum > 1)),
                'prompt' => (string) ($phaseItem['prompt'] ?? ''),
                'upsell_advice' => $phaseItem['upsell_advice'] ?? null,
            ];
        }

        return $steps;
    }

    /**
     * Build the Anti-Over-Planning (Scope Paralysis Guard) payload with 100% maturity verification
     * and the 3 Interactive Foundation Stage Execution Launchers.
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    public function buildScopeParalysisGuard(array $schema = [], string $locale = 'id'): array
    {
        $guard = PosProgressiveDisclosureCatalog::scopeParalysisProtectionBlueprint($locale);
        $tables = is_array($schema['data_dictionary'] ?? null) ? count($schema['data_dictionary']) : 15;
        $guard['compiled_table_count'] = $tables;
        $guard['compiled_pillar_count'] = 20;

        return $guard;
    }
}
