<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\PosChangeRequest;
use App\Models\PosProject;
use App\Models\Transaction;
use App\Models\VisionBlueprint;
use App\Support\PosStackPresetCatalog;
use App\Support\PricingRegistry;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Project OS v5.0 — The Commercial & Delivery Bridge ("Satu Pintu" Retail vs Mitra Proyek).
 *
 * Implements the 5 Commercial-to-Code Bridge engines:
 * 1. Algorithmic Scope-to-Quotation & WBS Estimator (Anti-Underpricing)
 * 2. Cryptographic Scope Freeze & Legal SPK/PKS Appendix (Anti-Scope Creep SHA-256)
 * 3. Paid Change-Order Delta Calculator (Addendum Invoice & Client Approval)
 * 4. Phase-to-Milestone Gantt (Phases 1–7 -> 7 Client Milestones) & Live Staging Preview
 * 5. Automated UAT Sign-Off (Given-When-Then Checklist) & Official Digital BAST
 */
class PosCommercialBridgeService
{
    public const MODE_RETAIL = 'RETAIL_SELF_DEV';
    public const MODE_MITRA = 'MITRA_TURNKEY_PROJECT';

    /**
     * Stack Topology Complexity Multipliers (Anti-Underpricing).
     *
     * @return array<string, float>
     */
    public static function stackComplexityMultipliers(): array
    {
        return [
            'STACK_M1_LARAVEL_MONOLITH' => 1.00,
            'STACK_M1_LARAVEL_FILAMENT_LIVEWIRE' => 1.00,
            'STACK_M2_LARAVEL_INERTIA_REACT' => 1.05,
            'STACK_M2_INERTIA_REACT' => 1.05,
            'STACK_M3_NEXTJS_FULLSTACK_PRISMA' => 1.10,
            'STACK_M3_NEXTJS_TRPC' => 1.10,
            'STACK_M4_GO_HTMX_TEMPL' => 1.05,
            'STACK_M4_DJANGO_HTMX' => 1.05,
            'STACK_D1_LARAVEL_NEXTJS_OPENAPI' => 1.25,
            'STACK_D1_LARAVEL_OPENAPI' => 1.25,
            'STACK_D2_NESTJS_NEXTJS_TRPC' => 1.30,
            'STACK_D2_GOLANG_SQLC' => 1.30,
            'STACK_D3_GO_FIBER_REACT_VITE' => 1.25,
            'STACK_D3_NESTJS_PRISMA' => 1.25,
            'STACK_S1_POLYGLOT_GO_LARAVEL_TEMPORAL' => 1.50,
            'STACK_S1_POLYGLOT_MESH' => 1.50,
            'STACK_S2_RUST_AXUM_LEDGER_CORE' => 1.50,
            'STACK_S2_EVENT_SOURCED_LEDGER' => 1.50,
        ];
    }

    /**
     * Catalog of the 5 Mitra Commercial-to-Code Bridge Features.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function fiveCommercialBridgeFeatures(): array
    {
        return [
            [
                'number' => 1,
                'code' => 'ALGORITHMIC_SCOPE_TO_QUOTATION_WBS',
                'badge' => 'ANTI-UNDERPRICING',
                'artifact' => 'RAB Resmi A4 + WBS 7-Milestone + Jadwal Termin (30/50/20 atau 50/50)',
                'title' => [
                    'id' => 'Algorithmic Scope-to-Quotation & WBS Estimator (Anti-Underpricing)',
                    'en' => 'Algorithmic Scope-to-Quotation & WBS Estimator (Anti-Underpricing)',
                ],
                'summary' => [
                    'id' => 'Setiap Tabel ULID, State Machine Transition, Concurrency Lock, Integrasi Eksternal, dan Stack Multiplier (1.0x–1.5x) otomatis dihitung menjadi Jam Kerja, Hari Kerja, RAB presisi, serta Termin 30% DP / 50% Milestone / 20% BAST.',
                    'en' => 'Every ULID Table, State Machine Transition, Concurrency Lock, External Integration, and Stack Multiplier (1.0x–1.5x) deterministically compiles into Man-Hours, Working Days, exact RAB IDR, and Milestone Termin splits.',
                ],
                'guarantee' => 'Zero Guesswork RAB & Anti-Rugi Jam Engineer',
            ],
            [
                'number' => 2,
                'code' => 'CRYPTOGRAPHIC_SCOPE_FREEZE_SPK_APPENDIX',
                'badge' => 'ANTI-SCOPE CREEP',
                'artifact' => 'SPK / PKS Pasal Kriptografis + Lampiran Teknis SHA-256 Scope Hash',
                'title' => [
                    'id' => 'Cryptographic Scope Freeze & Legal SPK/PKS Appendix (Anti-Scope Creep)',
                    'en' => 'Cryptographic Scope Freeze & Legal SPK/PKS Appendix (Anti-Scope Creep)',
                ],
                'summary' => [
                    'id' => 'Mengunci Blueprint v1.0 menjadi SHA-256 Scope Hash kriptografis yang dicetak langsung di Lampiran Hukum SPK/PKS resmi lengkap daftar Tabel ULID, Kolom, dan State Machine yang disepakati.',
                    'en' => 'Freezes Blueprint v1.0 into a deterministic SHA-256 Scope Hash printed directly inside the official SPK/PKS legal contract appendix alongside agreed ULID tables and state machines.',
                ],
                'guarantee' => 'Batas Hukum Ruang Lingkup Kedap Sengketa',
            ],
            [
                'number' => 3,
                'code' => 'PAID_CHANGE_ORDER_DELTA_CALCULATOR',
                'badge' => 'PAID ADDENDUM',
                'artifact' => 'Dokumen Addendum v1.1 + Invoice Midtrans Delta (NPRO-ADD-...)',
                'title' => [
                    'id' => 'Paid Change-Order Delta Calculator (Addendum Kontrak Otomatis)',
                    'en' => 'Paid Change-Order Delta Calculator (Automated Contract Addendum)',
                ],
                'summary' => [
                    'id' => 'Permintaan fitur baru di tengah jalan (Delta v1.1) otomatis dihitung selisih Jam Kerja, Tambahan Hari, dan Nilai Invoice Addendum sebelum tim mengeksekusi Surgical Delta Prompt.',
                    'en' => 'Mid-sprint feature requests (Delta v1.1) automatically compute incremental Man-Hours, Working Days, and Addendum Invoice IDR before executing the Surgical Delta Prompt.',
                ],
                'guarantee' => 'Ubah Scope Creep Jadi Revenue Addendum Terukur',
            ],
            [
                'number' => 4,
                'code' => 'PHASE_TO_MILESTONE_GANTT_STAGING',
                'badge' => 'LIVE 7-MILESTONE',
                'artifact' => 'Gantt 7-Milestone Portal Klien + Live Staging Preview URL',
                'title' => [
                    'id' => 'Phase-to-Milestone Gantt & Live Staging Preview (7 Milestone Real-Time)',
                    'en' => 'Phase-to-Milestone Gantt & Live Staging Preview (Real-Time 7 Milestones)',
                ],
                'summary' => [
                    'id' => 'Fase 1 s/d Fase 7 di IDE developer terpetakan otomatis ke 7 Milestone Portal Klien lengkap URL Live Staging tanpa perlu membuat laporan progres mingguan manual.',
                    'en' => 'Phases 1–7 inside the developer IDE map automatically onto 7 Client Portal Milestones with Live Staging URL without manual weekly status reports.',
                ],
                'guarantee' => 'Transparansi Real-Time IDE -> Client Portal',
            ],
            [
                'number' => 5,
                'code' => 'AUTOMATED_UAT_SIGNOFF_DIGITAL_BAST',
                'badge' => 'DIGITAL BAST',
                'artifact' => 'Checklist UAT Given-When-Then + BAST Resmi SHA-256 + Invoice Pelunasan',
                'title' => [
                    'id' => 'Automated UAT Sign-Off & Digital BAST (Berita Acara Serah Terima)',
                    'en' => 'Automated UAT Sign-Off & Digital BAST (Official Handover Certificate)',
                ],
                'summary' => [
                    'id' => 'Skenario Given-When-Then dirender menjadi Lembar Checklist UAT Interaktif. Saat 100% tercentang & ditandatangani klien, sistem otomatis menerbitkan BAST Resmi SHA-256 & Invoice Pelunasan Akhir.',
                    'en' => 'Given-When-Then acceptance scenarios render as an interactive UAT checklist; once 100% checked and digitally signed, issues a SHA-256 Digital BAST and Final Settlement Invoice.',
                ],
                'guarantee' => 'Serah Terima Sah & Pencairan Termin Akhir Instan',
            ],
        ];
    }

    /**
     * End-to-End 5-Step Guided Client Wizard ("Satu Pintu" Frontend -> Studio -> Portal).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function guidedClientJourneySteps(): array
    {
        return [
            [
                'step' => 1,
                'code' => 'STEP_1_CHOOSE_TRACK_AND_SECTOR',
                'title' => [
                    'id' => 'Pilih Jalur (Retail vs Mitra) & 12 Sektor',
                    'en' => 'Choose Track (Retail vs Mitra) & 12 Sectors',
                ],
                'route' => '/pricing',
                'desc' => [
                    'id' => 'Pilih mode eksekusi (Mandiri mulai Rp 0–399rb atau Terima Beres Tim Mitra) + pilih 1 dari 12 Sektor Bisnis dengan estimasi RAB langsung.',
                    'en' => 'Pick execution mode (Self-Dev Rp 0–399k or Turnkey Mitra Engineering) + choose 1 of 12 Functional Sectors with live RAB preview.',
                ],
            ],
            [
                'step' => 2,
                'code' => 'STEP_2_COMPOSE_BLUEPRINT_WIZARD',
                'title' => [
                    'id' => 'Rakit Spesifikasi & 3-Pane Studio',
                    'en' => 'Compose Spec & 3-Pane Studio',
                ],
                'route' => '/blueprint/studio',
                'desc' => [
                    'id' => 'Unggah dokumen via MarkItDown (0 token AI) atau pilih Composable Primitives & Stack M1–S2 dengan skor Blueprint Doctor 100%.',
                    'en' => 'Upload docs via local MarkItDown (0 AI tokens) or compose Primitives & Stack M1–S2 with 100% Blueprint Doctor score.',
                ],
            ],
            [
                'step' => 3,
                'code' => 'STEP_3_LOCK_SCOPE_AND_SPK',
                'title' => [
                    'id' => 'Kunci Scope SHA-256 & Kontrak / Lisensi',
                    'en' => 'Freeze SHA-256 Scope & Contract / License',
                ],
                'route' => '/blueprint/{slug}',
                'desc' => [
                    'id' => 'Retail langsung aktifkan lisensi unduh selamanya; Mitra mengunci SPK Lampiran SHA-256 & pembayaran DP Termin 1 via Midtrans Snap.',
                    'en' => 'Retail unlocks lifetime blueprint artifacts immediately; Mitra freezes SHA-256 SPK Appendix & triggers Termin 1 DP via Midtrans Snap.',
                ],
            ],
            [
                'step' => 4,
                'code' => 'STEP_4_LIVE_7PHASE_EXECUTION',
                'title' => [
                    'id' => 'Eksekusi 7-Fase & Live Staging Sync',
                    'en' => '7-Phase Execution & Live Staging Sync',
                ],
                'route' => '/customer/dashboard#projects',
                'desc' => [
                    'id' => 'Developer mengeksekusi Fase 1–7 via MCP IDE Bridge; progres 7 Milestone & Live Staging otomatis ter-update real-time di Portal Klien.',
                    'en' => 'Engineers execute Phases 1–7 via MCP IDE Bridge; 7 Milestones & Live Staging URL sync in real time to Client Portal.',
                ],
            ],
            [
                'step' => 5,
                'code' => 'STEP_5_UAT_BAST_AND_DELTA_V1_1',
                'title' => [
                    'id' => 'Checklist UAT, Digital BAST & Delta v1.1',
                    'en' => 'UAT Checklist, Digital BAST & Delta v1.1',
                ],
                'route' => '/customer/dashboard',
                'desc' => [
                    'id' => 'Klien mencentang UAT Given-When-Then 100%, menandatangani BAST Digital, melunasi termin akhir, & siap iterasi Delta v1.1 kapan saja.',
                    'en' => 'Client checks 100% Given-When-Then UAT scenarios, signs Digital BAST, settles final termin, and unlocks v1.1 Delta iterations.',
                ],
            ],
        ];
    }

    /**
     * Feature #1: Algorithmic Scope-to-Quotation, WBS 7-Milestone Estimator & Deterministic SHA-256 Scope Hash.
     *
     * @param  array<string, mixed>  $schema
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function calculateScopeQuotation(array $schema, array $options = []): array
    {
        $cfg = PricingRegistry::getCommercialEstimatorConfig();

        $ratePerHour = (int) ($options['man_hour_rate_idr'] ?? $cfg['man_hour_rate_idr'] ?? 350000);
        $hoursPerTable = (int) ($options['hours_per_table'] ?? $cfg['hours_per_table'] ?? 4);
        $hoursPerState = (int) ($options['hours_per_state'] ?? $cfg['hours_per_state'] ?? 6);
        $hoursPerLock = (int) ($options['hours_per_lock'] ?? $cfg['hours_per_lock'] ?? 5);
        $hoursPerIntegration = (int) ($options['hours_per_integration'] ?? $cfg['hours_per_integration'] ?? 8);
        $baseSetupHours = (int) ($options['base_setup_hours'] ?? $cfg['base_setup_hours'] ?? 24);
        $terminScheme = (string) ($options['termin_scheme'] ?? $cfg['termin_scheme'] ?? '30_50_20');

        $sectorCode = (string) ($options['sector_code'] ?? $schema['project_metadata']['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL');
        $rawStack = (string) (
            $options['stack_preset']
            ?? $options['stack_preset_code']
            ?? $schema['project_metadata']['stack_preset']
            ?? $schema['stack_topology_adapter']['code']
            ?? 'STACK_M1_LARAVEL_MONOLITH'
        );
        $normalizedStack = PosStackPresetCatalog::normalizePresetCode($rawStack);

        $multipliers = self::stackComplexityMultipliers();
        $stackMultiplier = $multipliers[$rawStack] ?? $multipliers[$normalizedStack] ?? 1.00;
        if (str_starts_with($normalizedStack, 'STACK_S') || str_contains($rawStack, 'STACK_S')) {
            $stackMultiplier = 1.50;
        } elseif (str_starts_with($normalizedStack, 'STACK_D') || str_contains($rawStack, 'STACK_D')) {
            $stackMultiplier = max(1.25, $stackMultiplier);
        }

        $tables = (array) ($schema['data_dictionary'] ?? []);
        $states = (array) ($schema['state_machines'] ?? []);
        $locks = (array) ($schema['domain_invariants']['concurrency_locks'] ?? []);
        $primitives = array_values(array_map('strval', (array) ($schema['project_metadata']['injected_primitives'] ?? $options['primitive_codes'] ?? [])));
        $decisions = (array) ($schema['decision_tables_matrix'] ?? []);
        $sagas = (array) ($schema['saga_and_webhook_contracts'] ?? []);

        $tablesCount = max(1, count($tables));
        $statesCount = max(1, count($states));
        $locksCount = max(1, count($locks));
        $integrationsCount = max(1, count($sagas));

        $rawHours = ($tablesCount * $hoursPerTable)
            + ($statesCount * $hoursPerState)
            + ($locksCount * $hoursPerLock)
            + ($integrationsCount * $hoursPerIntegration)
            + $baseSetupHours;

        $effectiveManHours = (int) ceil($rawHours * $stackMultiplier);
        $timelineDays = (int) max(7, ceil($effectiveManHours / 8));
        $quotationIdr = $effectiveManHours * $ratePerHour;

        // Deterministic Cryptographic Scope Hash (SHA-256)
        $canonicalPayload = [
            'sector' => $sectorCode,
            'stack' => $normalizedStack,
            'primitives' => $primitives,
            'tables' => array_values(array_map(fn ($t) => [
                'table' => $t['table'] ?? '',
                'columns' => array_map(fn ($c) => is_array($c) ? ($c['name'] ?? '') : (string) $c, (array) ($t['columns'] ?? [])),
            ], $tables)),
            'states' => array_values(array_map(fn ($s) => [
                'entity' => $s['entity'] ?? '',
                'states' => $s['states'] ?? [],
            ], $states)),
            'decisions_count' => count($decisions),
        ];
        sort($canonicalPayload['primitives']);
        $scopeHashFull = hash('sha256', (string) json_encode($canonicalPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $scopeHashShort = substr($scopeHashFull, 0, 16);

        // Termin Payment Schedule (Exact Rupiah Penny-Safe Reconciliation)
        if ($terminScheme === '50_50') {
            $t1 = (int) round($quotationIdr * 0.50);
            $t2 = $quotationIdr - $t1;
            $terminSchedule = [
                [
                    'termin_number' => 1,
                    'code' => 'TERMIN_1_DP_50',
                    'label' => 'Termin 1 (DP 50%): Kickoff, Lock Blueprint SHA-256 & Skema Database (Fase 1–3)',
                    'percentage' => 50,
                    'amount_idr' => $t1,
                    'formatted_amount' => 'Rp ' . number_format($t1, 0, ',', '.'),
                    'linked_phases' => [1, 2, 3],
                ],
                [
                    'termin_number' => 2,
                    'code' => 'TERMIN_2_BAST_50',
                    'label' => 'Termin 2 (Pelunasan 50%): Pengujian Pest, UI Sektoral, Red-Team & BAST Sign-Off (Fase 4–7)',
                    'percentage' => 50,
                    'amount_idr' => $t2,
                    'formatted_amount' => 'Rp ' . number_format($t2, 0, ',', '.'),
                    'linked_phases' => [4, 5, 6, 7],
                ],
            ];
        } else {
            $t1 = (int) round($quotationIdr * 0.30);
            $t2 = (int) round($quotationIdr * 0.50);
            $t3 = $quotationIdr - $t1 - $t2;
            $terminSchedule = [
                [
                    'termin_number' => 1,
                    'code' => 'TERMIN_1_DP_30',
                    'label' => 'Termin 1 (DP 30%): Kickoff, Cryptographic Scope Freeze & ULID Database Schema (Fase 1–2)',
                    'percentage' => 30,
                    'amount_idr' => $t1,
                    'formatted_amount' => 'Rp ' . number_format($t1, 0, ',', '.'),
                    'linked_phases' => [1, 2],
                ],
                [
                    'termin_number' => 2,
                    'code' => 'TERMIN_2_MILESTONE_50',
                    'label' => 'Termin 2 (Milestone 50%): Domain Actions Lock, Pest Verification & Sectoral UI/Print (Fase 3–5)',
                    'percentage' => 50,
                    'amount_idr' => $t2,
                    'formatted_amount' => 'Rp ' . number_format($t2, 0, ',', '.'),
                    'linked_phases' => [3, 4, 5],
                ],
                [
                    'termin_number' => 3,
                    'code' => 'TERMIN_3_BAST_20',
                    'label' => 'Termin 3 (Pelunasan BAST 20%): 4-Pillar AI Sync, Security Red-Team, UAT 100% & BAST Resmi (Fase 6–7)',
                    'percentage' => 20,
                    'amount_idr' => $t3,
                    'formatted_amount' => 'Rp ' . number_format($t3, 0, ',', '.'),
                    'linked_phases' => [6, 7],
                ],
            ];
        }

        // 7-Phase WBS Effort Allocation
        $phaseWeights = [
            1 => ['title' => 'Fase 1: Foundation, Strict ULID & Tenancy Harness', 'pct' => 0.12],
            2 => ['title' => 'Fase 2: PostgreSQL 16 Migrations & State Machine Enums', 'pct' => 0.16],
            3 => ['title' => 'Fase 3: Atomic Domain Actions, Pessimistic Locks & match() Rules', 'pct' => 0.22],
            4 => ['title' => 'Fase 4: Automated Pest Verification & Realistic Seeder Gate', 'pct' => 0.12],
            5 => ['title' => 'Fase 5: Sectoral Anti-Slop UI/UX & Thermal/A4 Print Engine', 'pct' => 0.18],
            6 => ['title' => 'Fase 6: 4-Pillar AI Copilot Sync, RBAC, AuditLog & Staging Deploy', 'pct' => 0.10],
            7 => ['title' => 'Fase 7: Security Red-Team Suite, UAT Sign-Off & Digital BAST', 'pct' => 0.10],
        ];

        $wbsBreakdown = [];
        $allocatedHoursSum = 0;
        foreach ($phaseWeights as $pNum => $meta) {
            $pHours = $pNum < 7
                ? (int) max(2, round($effectiveManHours * $meta['pct']))
                : max(2, $effectiveManHours - $allocatedHoursSum);
            $allocatedHoursSum += $pHours;
            $pCost = $pHours * $ratePerHour;

            $wbsBreakdown[] = [
                'phase' => $pNum,
                'title' => $meta['title'],
                'man_hours' => $pHours,
                'working_days' => (int) max(1, ceil($pHours / 8)),
                'cost_idr' => $pCost,
                'formatted_cost' => 'Rp ' . number_format($pCost, 0, ',', '.'),
                'termin_gate' => $pNum <= 2 ? 'Termin 1 (DP)' : ($pNum <= 5 ? 'Termin 2 (Milestone)' : 'Termin 3 (BAST)'),
            ];
        }

        // Recommend ideal Retail vs Mitra package tier ("Satu Pintu" Package Classification)
        $isSimpleUmkm = $tablesCount <= 6 && $stackMultiplier <= 1.05 && count($primitives) <= 3;
        $recommendedRetailTier = count($primitives) <= 2 && $stackMultiplier <= 1.05
            ? PricingRegistry::TIER_RETAIL_LITE
            : (count($primitives) >= 5 || $stackMultiplier >= 1.25 ? PricingRegistry::TIER_RETAIL_ULTIMATE : PricingRegistry::TIER_RETAIL_PRO);
        $recommendedMitraTier = $isSimpleUmkm
            ? PricingRegistry::TIER_UMKM_STARTER
            : PricingRegistry::TIER_FULL_MVP;

        $formattedTotal = 'Rp ' . number_format($quotationIdr, 0, ',', '.');

        return [
            'sector_code' => $sectorCode,
            'stack_preset' => $normalizedStack,
            'stack_multiplier' => $stackMultiplier,
            'man_hour_rate_idr' => $ratePerHour,
            'formatted_man_hour_rate' => 'Rp ' . number_format($ratePerHour, 0, ',', '.') . '/jam',
            'counts' => [
                'tables' => $tablesCount,
                'states' => $statesCount,
                'locks' => $locksCount,
                'integrations' => $integrationsCount,
                'primitives' => count($primitives),
                'decision_rules' => count($decisions),
            ],
            'metrics' => [
                'tables_count' => $tablesCount,
                'transitions_count' => $statesCount,
                'concurrency_locks_count' => $locksCount,
                'integrations_count' => $integrationsCount,
            ],
            'raw_man_hours' => $rawHours,
            'man_hours' => $effectiveManHours,
            'total_man_hours' => $effectiveManHours,
            'timeline_days' => $timelineDays,
            'estimated_working_days' => $timelineDays,
            'quotation_idr' => $quotationIdr,
            'formatted_quotation_idr' => $formattedTotal,
            'formatted_total_quotation_idr' => $formattedTotal,
            'advisory_credit_idr' => 2500000,
            'net_quotation_after_advisory_idr' => max(0, $quotationIdr - 2500000),
            'scope_hash_full' => $scopeHashFull,
            'scope_hash_sha256' => $scopeHashFull,
            'scope_hash_short' => $scopeHashShort,
            'termin_scheme' => $terminScheme,
            'termin_schedule' => $terminSchedule,
            'wbs_7_milestones' => $wbsBreakdown,
            'recommended_packages' => [
                'retail_tier_id' => $recommendedRetailTier,
                'mitra_tier_id' => $recommendedMitraTier,
                'advisory_entry_tier_id' => PricingRegistry::TIER_BLUEPRINT_ADVISORY,
            ],
        ];
    }

    /**
     * Feature #2: Cryptographic Scope Freeze & Legal SPK/PKS Appendix Generator.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public function lockScopeAndGenerateSpk(
        VisionBlueprint $blueprint,
        ?PosProject $posProject = null,
        array $overrides = []
    ): array {
        if (! $posProject) {
            $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
            if (! $posProject) {
                $posProject = app(ProjectOsBlueprintCompilerService::class)->compileAndPersist($blueprint);
            }
        }

        $schema = $posProject && ! empty($posProject->compiled_schema_json)
            ? (is_array($posProject->compiled_schema_json) ? $posProject->compiled_schema_json : (json_decode((string) $posProject->compiled_schema_json, true) ?: []))
            : ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => false]);

        $quotation = $this->calculateScopeQuotation($schema, $overrides);
        $uatChecklist = $this->buildUatChecklist(
            $schema,
            is_array($posProject?->uat_checklist_json) ? $posProject->uat_checklist_json : []
        );

        $contractAmount = isset($overrides['contract_amount'])
            ? (float) $overrides['contract_amount']
            : (float) $quotation['quotation_idr'];
        $dpAmount = isset($overrides['dp_amount'])
            ? (float) $overrides['dp_amount']
            : (float) ($quotation['termin_schedule'][0]['amount_idr'] ?? ($contractAmount * 0.30));

        $tableNames = array_values(array_filter(array_map(
            fn ($t) => (string) ($t['table'] ?? ''),
            (array) ($schema['data_dictionary'] ?? [])
        )));
        $stateEntities = array_values(array_filter(array_map(
            fn ($s) => (string) ($s['entity'] ?? ''),
            (array) ($schema['state_machines'] ?? [])
        )));

        if ($posProject && $posProject->exists) {
            $updates = [];
            if (Schema::hasColumn('pos_projects', 'engagement_mode')) {
                $updates['engagement_mode'] = self::MODE_MITRA;
            }
            if (Schema::hasColumn('pos_projects', 'scope_hash_sha256')) {
                $updates['scope_hash_sha256'] = $quotation['scope_hash_full'];
            }
            if (Schema::hasColumn('pos_projects', 'scope_locked_at')) {
                $updates['scope_locked_at'] = now();
            }
            if (Schema::hasColumn('pos_projects', 'commercial_quotation_json')) {
                $updates['commercial_quotation_json'] = $quotation;
            }
            if (Schema::hasColumn('pos_projects', 'uat_checklist_json')) {
                $updates['uat_checklist_json'] = $uatChecklist;
            }
            if (! empty($updates)) {
                $posProject->forceFill($updates)->saveQuietly();
            }
        }

        $terminLines = array_map(
            fn ($t) => $t['label'] . ' = ' . $t['formatted_amount'],
            $quotation['termin_schedule']
        );

        $clauses = [
            'pasal_1_ruang_lingkup_kriptografis' => [
                'title' => 'Pasal 1: Ruang Lingkup Kriptografis Blueprint v1.0 (Cryptographic Scope Freeze)',
                'description' => 'Pihak Kedua mengerjakan sistem "' . ($blueprint->nama_bisnis ?: $blueprint->client_name) . '" secara presisi berdasarkan Blueprint v' . ($posProject?->current_version ?: '1.0') . ' dengan Kunci Hash Kriptografis SHA-256: ' . $quotation['scope_hash_full'] . '. Sektor Utama: ' . $quotation['sector_code'] . ' | Stack: ' . $quotation['stack_preset'] . '.',
            ],
            'pasal_2_lampiran_tabel_dan_state_machine' => [
                'title' => 'Pasal 2: Lampiran Teknis Tabel ULID & State Machine Resmi',
                'description' => 'Batas hukum ruang lingkup mencakup ' . count($tableNames) . ' Tabel Strict ULID (' . implode(', ', array_slice($tableNames, 0, 12)) . ') serta ' . count($stateEntities) . ' State Machine Terkunci (' . implode(', ', array_slice($stateEntities, 0, 10)) . ') dengan beban terukur ' . $quotation['man_hours'] . ' Jam Kerja (' . $quotation['timeline_days'] . ' Hari Kerja).',
            ],
            'pasal_3_nilai_rab_dan_termin' => [
                'title' => 'Pasal 3: Nilai Kontrak RAB & Jadwal Termin Pembayaran',
                'description' => 'Total nilai kontrak disepakati sebesar Rp ' . number_format($contractAmount, 0, ',', '.') . ' dengan rincian tahapan pembayaran: ' . implode(' | ', $terminLines) . '.',
            ],
            'pasal_4_anti_scope_creep_addendum' => [
                'title' => 'Pasal 4: Perlindungan Anti-Scope Creep & Paid Change-Order Delta (v1.1+)',
                'description' => 'Setiap permintaan penambahan tabel, alur status, atau integrasi di luar SHA-256 Scope Hash Pasal 1 dikategorikan sebagai Change-Order Delta (v1.1) yang dihitung otomatis melalui Paid Change-Order Delta Calculator dan dieksekusi setelah persetujuan Addendum resmi.',
            ],
            'pasal_5_uat_given_when_then_dan_bast' => [
                'title' => 'Pasal 5: Pengujian UAT Terukur (Given-When-Then) & Serah Terima BAST Digital',
                'description' => 'Serah terima sistem mengacu pada ' . count($uatChecklist['scenarios'] ?? []) . ' skenario penerimaan objektif (Given-When-Then) di server Staging. Setelah 100% skenario lolos uji dan ditandatangani secara elektronik, sistem menerbitkan BAST Resmi berstempel SHA-256.',
            ],
        ];

        $existingDoc = Document::query()
            ->where('related_type', VisionBlueprint::class)
            ->where('related_id', $blueprint->id)
            ->where('document_type', 'contract')
            ->first();

        if ($existingDoc) {
            $existingDoc->update([
                'title' => 'SPK & Lampiran Scope SHA-256 - ' . ($blueprint->nama_bisnis ?: $blueprint->client_name),
                'scope_locked' => true,
                'contract_amount' => $contractAmount,
                'dp_amount' => $dpAmount,
                'content_clauses' => $clauses,
                'document_hash' => $quotation['scope_hash_full'],
            ]);
            $doc = $existingDoc;
        } else {
            $doc = Document::create([
                'title' => 'SPK & Lampiran Scope SHA-256 - ' . ($blueprint->nama_bisnis ?: $blueprint->client_name),
                'document_type' => 'contract',
                'related_id' => $blueprint->id,
                'related_type' => VisionBlueprint::class,
                'status' => 'pending_signature',
                'scope_locked' => true,
                'contract_amount' => $contractAmount,
                'dp_amount' => $dpAmount,
                'midtrans_order_id' => 'NPRO-SPK-' . strtoupper(Str::random(8)),
                'content_clauses' => $clauses,
                'document_hash' => $quotation['scope_hash_full'],
            ]);
        }

        $blueprint->forceFill([
            'project_status' => 'Scope Locked',
            'document_sha256' => $quotation['scope_hash_full'],
        ])->saveQuietly();

        AuditLog::record(
            action: 'mitra_scope_frozen_and_spk_generated',
            model: $doc,
            newValues: [
                'blueprint_id' => $blueprint->id,
                'pos_project_id' => $posProject?->id,
                'scope_hash_sha256' => $quotation['scope_hash_full'],
                'contract_amount_idr' => $contractAmount,
                'dp_amount_idr' => $dpAmount,
                'man_hours' => $quotation['man_hours'],
                'timeline_days' => $quotation['timeline_days'],
            ],
            description: "Cryptographic Scope Freeze SHA-256 ({$quotation['scope_hash_short']}) & Kontrak SPK diterbitkan untuk '{$blueprint->nama_bisnis}'.",
            eventCategory: 'commercial_contract'
        );

        return [
            'success' => true,
            'document_id' => $doc->id,
            'document_title' => $doc->title,
            'scope_hash_full' => $quotation['scope_hash_full'],
            'scope_hash_short' => $quotation['scope_hash_short'],
            'quotation' => $quotation,
            'sign_url' => url("/document/{$doc->id}/sign"),
            'pdf_preview_url' => url("/document/{$doc->id}/preview"),
            'spk_appendix_url' => url("/blueprint/{$blueprint->slug}/mitra-document/spk-appendix"),
        ];
    }

    /**
     * Feature #3: Paid Change-Order Delta Calculator (Addendum Cost & Timeline Delta).
     *
     * @param  array<int, mixed>  $deltaTables
     * @param  array<int, string>  $addedPrimitives
     * @param  array<int, mixed>  $addedDecisionRules
     * @return array<string, mixed>
     */
    public function calculatePaidChangeOrderDelta(
        array $deltaTables,
        array $addedPrimitives = [],
        array $addedDecisionRules = [],
        string $stackCode = 'STACK_M1_LARAVEL_MONOLITH'
    ): array {
        $cfg = PricingRegistry::getCommercialEstimatorConfig();
        $ratePerHour = (int) ($cfg['man_hour_rate_idr'] ?? 350000);
        $hoursPerTable = (int) ($cfg['hours_per_table'] ?? 4);

        $multipliers = self::stackComplexityMultipliers();
        $normalizedStack = PosStackPresetCatalog::normalizePresetCode($stackCode);
        $multiplier = $multipliers[$stackCode] ?? $multipliers[$normalizedStack] ?? 1.00;

        $tablesAdded = max(1, count($deltaTables));
        $primsAdded = count($addedPrimitives);
        $rulesAdded = max(1, count($addedDecisionRules));

        // Effort formula for surgical delta: tables + primitive state/locks + decision rule + regression QA (4h)
        $rawDeltaHours = ($tablesAdded * $hoursPerTable)
            + ($primsAdded * 8)
            + ($rulesAdded * 3)
            + 4;

        $effectiveDeltaHours = (int) ceil($rawDeltaHours * $multiplier);
        $deltaWorkingDays = (int) max(1, ceil($effectiveDeltaHours / 8));
        $deltaQuotationIdr = $effectiveDeltaHours * $ratePerHour;

        return [
            'delta_man_hours' => $effectiveDeltaHours,
            'delta_working_days' => $deltaWorkingDays,
            'delta_quotation_idr' => $deltaQuotationIdr,
            'formatted_delta_quotation' => 'Rp ' . number_format($deltaQuotationIdr, 0, ',', '.'),
            'man_hour_rate_idr' => $ratePerHour,
            'stack_multiplier' => $multiplier,
            'addendum_status' => 'PENDING_CLIENT_APPROVAL',
        ];
    }

    /**
     * Feature #3b: Client Approves Addendum & Generates Official Addendum Invoice (`[ Setujui Addendum & Bayar Invoice Delta ]`).
     *
     * @param  array<string, mixed>  $clientPayload
     * @return array<string, mixed>
     */
    public function approveAddendumAndCreateInvoice(PosChangeRequest $cr, array $clientPayload = []): array
    {
        $blueprint = $cr->visionBlueprint;
        $amount = (int) round((float) ($cr->delta_quotation_idr ?: 0));
        if ($amount <= 0) {
            $calc = $this->calculatePaidChangeOrderDelta(
                (array) ($cr->added_tables ?? []),
                (array) ($cr->added_primitives ?? []),
                (array) ($cr->added_decision_rules ?? [])
            );
            $amount = (int) $calc['delta_quotation_idr'];
            $cr->delta_man_hours = $calc['delta_man_hours'];
            $cr->delta_working_days = $calc['delta_working_days'];
            $cr->delta_quotation_idr = $amount;
        }

        $orderId = $cr->midtrans_addendum_order_id ?: ('NPRO-ADD-' . substr($cr->id, -10) . '-' . time());
        $snapToken = null;
        $redirectUrl = null;

        if (class_exists(MidtransSnapService::class)) {
            try {
                $snapRes = MidtransSnapService::createSnapToken([
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => $amount,
                    ],
                    'customer_details' => [
                        'first_name' => (string) ($clientPayload['signer_name'] ?? $blueprint?->client_name ?? 'Mitra Client'),
                        'email' => (string) ($clientPayload['signer_email'] ?? $blueprint?->email ?? 'client@neriahpro.com'),
                        'phone' => (string) ($blueprint?->phone ?: '08123456789'),
                    ],
                    'item_details' => [
                        [
                            'id' => 'ADDENDUM-' . strtoupper(str_replace('.', '_', (string) $cr->to_version)),
                            'price' => $amount,
                            'quantity' => 1,
                            'name' => substr("Addendum {$cr->to_version}: {$cr->title}", 0, 50),
                        ],
                    ],
                ]);
                $snapToken = $snapRes['token'] ?? null;
                $redirectUrl = $snapRes['redirect_url'] ?? null;
            } catch (\Throwable) {
                // Resilient offline/test fallback when Midtrans credentials are not live
            }
        }

        if (Schema::hasTable('transactions')) {
            Transaction::query()->updateOrCreate(
                ['midtrans_order_id' => $orderId],
                [
                    'user_id' => auth()->id(),
                    'status' => 'pending',
                    'total_idr' => $amount,
                    'original_currency' => 'IDR',
                    'original_amount' => $amount,
                    'customer_details' => [
                        'type' => 'paid_change_order_addendum',
                        'change_request_id' => $cr->id,
                        'vision_blueprint_id' => $blueprint?->id,
                        'from_version' => $cr->from_version,
                        'to_version' => $cr->to_version,
                        'email' => (string) ($clientPayload['signer_email'] ?? $blueprint?->email ?? ''),
                        'name' => (string) ($clientPayload['signer_name'] ?? $blueprint?->client_name ?? 'Mitra Client'),
                        'snap_token' => $snapToken,
                    ],
                ]
            );
        }

        $updates = ['status' => 'APPROVED_ADDENDUM'];
        if (Schema::hasColumn('pos_change_requests', 'addendum_status')) {
            $updates['addendum_status'] = 'APPROVED_AWAITING_PAYMENT';
            $updates['midtrans_addendum_order_id'] = $orderId;
            $updates['approved_by_client_at'] = now();
            $updates['delta_quotation_idr'] = $amount;
        }
        $cr->forceFill($updates)->save();

        AuditLog::record(
            action: 'paid_change_order_addendum_approved',
            model: $cr,
            newValues: [
                'change_request_id' => $cr->id,
                'to_version' => $cr->to_version,
                'delta_quotation_idr' => $amount,
                'delta_man_hours' => $cr->delta_man_hours,
                'delta_working_days' => $cr->delta_working_days,
                'midtrans_order_id' => $orderId,
            ],
            description: "Addendum Change-Order {$cr->from_version} -> {$cr->to_version} disetujui klien dengan nilai Rp " . number_format($amount, 0, ',', '.') . ".",
            eventCategory: 'paid_addendum'
        );

        return [
            'success' => true,
            'change_request_id' => $cr->id,
            'to_version' => $cr->to_version,
            'addendum_status' => 'APPROVED_AWAITING_PAYMENT',
            'midtrans_order_id' => $orderId,
            'delta_quotation_idr' => $amount,
            'formatted_delta_quotation' => 'Rp ' . number_format($amount, 0, ',', '.'),
            'delta_man_hours' => (int) $cr->delta_man_hours,
            'delta_working_days' => (int) $cr->delta_working_days,
            'snap_token' => $snapToken,
            'redirect_url' => $redirectUrl,
        ];
    }

    /**
     * Feature #4: Phase-to-Milestone Gantt & Live Staging Preview (Maps Phases 1–7 to 7 Client Milestones).
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    public function buildMilestoneGanttAndStaging(
        ?VisionBlueprint $blueprint,
        ?PosProject $posProject,
        array $schema = []
    ): array {
        $quotation = ! empty($posProject?->commercial_quotation_json) && is_array($posProject->commercial_quotation_json)
            ? $posProject->commercial_quotation_json
            : $this->calculateScopeQuotation($schema);

        $phaseProgress = is_array($posProject?->phase_progress_json) ? $posProject->phase_progress_json : [];
        $devCheckpoints = is_array($blueprint?->prd_content['dev_checkpoints'] ?? null)
            ? $blueprint->prd_content['dev_checkpoints']
            : [];

        $milestones = [];
        $verifiedCount = 0;

        foreach (($quotation['wbs_7_milestones'] ?? []) as $item) {
            $pNum = (int) ($item['phase'] ?? 1);
            $key = 'phase_' . $pNum;
            $isVerified = (($phaseProgress[$key]['status'] ?? '') === 'VERIFIED') || ! empty($devCheckpoints[$key]);
            if ($isVerified) {
                $verifiedCount++;
            }

            $milestones[] = [
                'phase' => $pNum,
                'title' => $item['title'],
                'man_hours' => $item['man_hours'],
                'working_days' => $item['working_days'],
                'formatted_cost' => $item['formatted_cost'],
                'termin_gate' => $item['termin_gate'],
                'status' => $isVerified ? 'VERIFIED' : ($pNum === ($verifiedCount + 1) ? 'IN_PROGRESS' : 'PENDING'),
                'badge' => $isVerified ? 'Verified ✅' : ($pNum === ($verifiedCount + 1) ? 'Active Sprint' : 'Queued'),
                'verified_at' => $phaseProgress[$key]['verified_at'] ?? null,
                'ide_client' => $phaseProgress[$key]['ide_client'] ?? null,
            ];
        }

        $stagingUrl = $blueprint?->staging_url
            ?: ('https://staging-' . Str::slug((string) ($blueprint?->slug ?: $posProject?->app_name ?: 'project')) . '.neriahpro.app');

        return [
            'verified_count' => $verifiedCount,
            'total_milestones' => 7,
            'completion_percent' => (int) round(($verifiedCount / 7) * 100),
            'staging_url' => $stagingUrl,
            'staging_provisioned' => ! empty($blueprint?->staging_url) || $verifiedCount >= 5,
            'milestones' => $milestones,
        ];
    }

    /**
     * Feature #5a: Build Interactive Given-When-Then UAT Checklist from Blueprint Schema.
     *
     * @param  array<string, mixed>  $schema
     * @param  array<string, mixed>  $existingChecklist
     * @return array<string, mixed>
     */
    public function buildUatChecklist(array $schema, array $existingChecklist = []): array
    {
        $existingMap = [];
        foreach (($existingChecklist['scenarios'] ?? []) as $ex) {
            if (! empty($ex['id'])) {
                $existingMap[(string) $ex['id']] = $ex;
            }
        }

        $rawScenarios = (array) ($schema['acceptance_test_matrix'] ?? []);
        $scenarios = [];

        foreach ($rawScenarios as $idx => $sc) {
            $id = (string) ($sc['id'] ?? sprintf('UAT-%02d', $idx + 1));
            $prev = $existingMap[$id] ?? null;

            $scenarios[] = [
                'id' => $id,
                'primitive' => (string) ($sc['primitive'] ?? 'CORE_DOMAIN'),
                'title' => (string) ($sc['title'] ?? $sc['scenario'] ?? ("Skenario UAT #" . ($idx + 1))),
                'given' => (string) ($sc['given'] ?? 'Data organisasi & aktor terautentikasi tersedia di Staging.'),
                'when' => (string) ($sc['when'] ?? 'Pengguna mengeksekusi transaksi utama pada antarmuka aplikasi.'),
                'then' => (string) ($sc['then'] ?? 'State berubah secara atomik sesuai kontrak tanpa race-condition dan tercatat di AuditLog.'),
                'passed' => (bool) ($prev['passed'] ?? false),
                'checked_at' => $prev['checked_at'] ?? null,
                'checked_by' => $prev['checked_by'] ?? null,
            ];
        }

        if (empty($scenarios)) {
            $defaults = [
                [
                    'id' => 'UAT-01',
                    'primitive' => 'PRIMITIVE_RBAC_TENANCY',
                    'title' => 'Isolasi Multi-Tenant & Hak Akses RBAC Ketat',
                    'given' => 'Dua akun organisasi berbeda login pada lingkungan Staging.',
                    'when' => 'Organisasi A mencoba mengakses record ULID milik Organisasi B.',
                    'then' => 'Sistem menolak dengan HTTP 404/403 (Zero Cross-Tenant Leakage).',
                ],
                [
                    'id' => 'UAT-02',
                    'primitive' => 'CORE_CONCURRENCY_LOCK',
                    'title' => 'Transaksi Atomik & Pessimistic Locking Bebas Selisih',
                    'given' => 'Saldo / kuota / stok unit bernilai terbatas di Staging.',
                    'when' => 'Dua transaksi paralel dieksekusi pada milidetik yang sama.',
                    'then' => 'Baris dikunci via lockForUpdate(), tidak ada saldo negatif atau double-booking.',
                ],
                [
                    'id' => 'UAT-03',
                    'primitive' => 'UI_PRINT_AND_AUDIT',
                    'title' => 'Cetak Dokumen Resmi & Integritas Audit Log SHA-256',
                    'given' => 'Transaksi bisnis telah berstatus Approved / Settled.',
                    'when' => 'Klien mencetak bukti A4/Thermal dan memeriksa riwayat Audit Log.',
                    'then' => 'Dokumen tercetak presisi dengan angka snapshot historis & hash SHA-256 valid.',
                ],
            ];
            foreach ($defaults as $d) {
                $prev = $existingMap[$d['id']] ?? null;
                $d['passed'] = (bool) ($prev['passed'] ?? false);
                $d['checked_at'] = $prev['checked_at'] ?? null;
                $d['checked_by'] = $prev['checked_by'] ?? null;
                $scenarios[] = $d;
            }
        }

        $total = count($scenarios);
        $passedCount = count(array_filter($scenarios, fn ($s) => ! empty($s['passed'])));
        $percent = $total > 0 ? (int) round(($passedCount / $total) * 100) : 0;

        $isAllPassed = $total > 0 && $passedCount === $total;

        return [
            'total_scenarios' => $total,
            'passed_scenarios' => $passedCount,
            'progress_percent' => $percent,
            'ready_for_bast' => $isAllPassed,
            'all_passed' => $isAllPassed,
            'scenarios' => $scenarios,
        ];
    }

    /**
     * Feature #5b: Toggle an individual Given-When-Then UAT scenario (or mark all passed).
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    public function toggleUatScenario(
        ?PosProject $posProject,
        array $schema,
        string $scenarioId,
        ?bool $forcePassed = null,
        ?string $actorName = null
    ): array {
        $current = $this->buildUatChecklist(
            $schema,
            is_array($posProject?->uat_checklist_json) ? $posProject->uat_checklist_json : []
        );

        $updatedScenarios = [];
        foreach ($current['scenarios'] as $sc) {
            if ($scenarioId === 'ALL' || $sc['id'] === $scenarioId) {
                $nextState = $forcePassed !== null ? $forcePassed : ! $sc['passed'];
                $sc['passed'] = $nextState;
                $sc['checked_at'] = $nextState ? now()->toIso8601String() : null;
                $sc['checked_by'] = $nextState ? ($actorName ?: (auth()->user()?->name ?: 'Client UAT Reviewer')) : null;
            }
            $updatedScenarios[] = $sc;
        }

        $rebuilt = $this->buildUatChecklist($schema, ['scenarios' => $updatedScenarios]);

        if ($posProject && $posProject->exists && Schema::hasColumn('pos_projects', 'uat_checklist_json')) {
            $posProject->forceFill(['uat_checklist_json' => $rebuilt])->saveQuietly();
        }

        return $rebuilt;
    }

    /**
     * Feature #5c: Automated UAT Sign-Off & Digital BAST Issuance + Final Termin Invoice Unlock.
     *
     * @param  array<string, mixed>  $signerPayload
     * @return array<string, mixed>
     */
    public function signOffUatAndIssueBast(
        VisionBlueprint $blueprint,
        ?PosProject $posProject,
        array $signerPayload = []
    ): array {
        if (! $posProject) {
            $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
            if (! $posProject) {
                $posProject = app(ProjectOsBlueprintCompilerService::class)->compileAndPersist($blueprint);
            }
        }

        $schema = $posProject && ! empty($posProject->compiled_schema_json)
            ? (is_array($posProject->compiled_schema_json) ? $posProject->compiled_schema_json : (json_decode((string) $posProject->compiled_schema_json, true) ?: []))
            : ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint, ['persist_project' => false]);

        $uat = $this->buildUatChecklist(
            $schema,
            is_array($posProject?->uat_checklist_json) ? $posProject->uat_checklist_json : []
        );

        if (! empty($signerPayload['mark_all_passed']) && ! $uat['ready_for_bast']) {
            $uat = $this->toggleUatScenario(
                $posProject,
                $schema,
                'ALL',
                true,
                (string) ($signerPayload['signer_name'] ?? $blueprint->client_name ?: 'Client PIC')
            );
        }

        if (! $uat['ready_for_bast']) {
            return [
                'success' => false,
                'error_code' => 'UAT_INCOMPLETE',
                'message' => "Seluruh skenario UAT wajib tercentang 100% sebelum menerbitkan BAST Resmi (Saat ini: {$uat['passed_scenarios']}/{$uat['total_scenarios']}).",
                'uat' => $uat,
            ];
        }

        $quotation = ! empty($posProject?->commercial_quotation_json) && is_array($posProject->commercial_quotation_json)
            ? $posProject->commercial_quotation_json
            : $this->calculateScopeQuotation($schema);

        $terminList = (array) ($quotation['termin_schedule'] ?? []);
        $finalTermin = end($terminList) ?: [
            'amount_idr' => (int) round(($quotation['quotation_idr'] ?? 0) * 0.20),
            'percentage' => 20,
        ];
        $finalInvoiceAmount = (int) ($finalTermin['amount_idr'] ?? 0);

        $signerName = trim((string) ($signerPayload['signer_name'] ?? $blueprint->client_name ?: 'PIC Klien Resmi'));
        $signerEmail = trim((string) ($signerPayload['signer_email'] ?? $blueprint->email ?: 'client@neriahpro.com'));
        $signerIp = (string) ($signerPayload['signer_ip'] ?? request()?->ip() ?? '127.0.0.1');
        $signedAt = now();

        $bastNumber = sprintf(
            'BAST/NPRO/%s/%s',
            $signedAt->format('Y/m'),
            strtoupper(substr($blueprint->id, -6))
        );

        $bastHash = hash('sha256', implode('|', [
            $bastNumber,
            $blueprint->id,
            $quotation['scope_hash_full'] ?? '',
            $uat['passed_scenarios'],
            $signerName,
            $signerEmail,
            $signedAt->toIso8601String(),
        ]));

        $finalOrderId = 'NPRO-BAST-' . substr($blueprint->id, -10) . '-' . time();

        $bastRecord = [
            'bast_number' => $bastNumber,
            'bast_sha256' => $bastHash,
            'scope_hash_sha256' => $quotation['scope_hash_full'] ?? null,
            'signer_name' => $signerName,
            'signer_email' => $signerEmail,
            'signer_ip' => $signerIp,
            'signed_at' => $signedAt->toIso8601String(),
            'uat_total_scenarios' => $uat['total_scenarios'],
            'uat_passed_scenarios' => $uat['passed_scenarios'],
            'final_termin_percentage' => $finalTermin['percentage'] ?? 20,
            'final_termin_amount_idr' => $finalInvoiceAmount,
            'formatted_final_termin' => 'Rp ' . number_format($finalInvoiceAmount, 0, ',', '.'),
            'final_midtrans_order_id' => $finalOrderId,
            'bast_print_url' => url("/blueprint/{$blueprint->slug}/mitra-document/bast"),
        ];

        if ($posProject && $posProject->exists && Schema::hasColumn('pos_projects', 'bast_signoff_json')) {
            $posProject->forceFill([
                'engagement_mode' => self::MODE_MITRA,
                'uat_checklist_json' => $uat,
                'bast_signoff_json' => $bastRecord,
            ])->saveQuietly();
        }

        // Create Official BAST Document record in `documents` table
        $bastDoc = Document::query()->updateOrCreate(
            [
                'related_type' => VisionBlueprint::class,
                'related_id' => $blueprint->id,
                'document_type' => 'bast',
            ],
            [
                'title' => "Berita Acara Serah Terima (BAST) Resmi - {$bastNumber}",
                'status' => 'signed',
                'scope_locked' => true,
                'contract_amount' => (float) ($quotation['quotation_idr'] ?? 0),
                'dp_amount' => (float) $finalInvoiceAmount,
                'midtrans_order_id' => $finalOrderId,
                'signer_name' => $signerName,
                'signer_email' => $signerEmail,
                'signer_ip_address' => $signerIp,
                'signed_at' => $signedAt,
                'document_hash' => $bastHash,
                'content_clauses' => [
                    'pasal_1_pengujian_uat_100' => [
                        'title' => 'Pernyataan Kelulusan Uji Terima (UAT 100% Given-When-Then)',
                        'description' => "Pihak Pertama ({$signerName}) menyatakan telah menguji secara langsung seluruh {$uat['passed_scenarios']}/{$uat['total_scenarios']} skenario penerimaan fungsional, konkurensi, dan keamanan di lingkungan Staging sesuai Scope Hash SHA-256: " . ($quotation['scope_hash_short'] ?? '-') . '.',
                    ],
                    'pasal_2_serah_terima_dan_garansi' => [
                        'title' => 'Serah Terima Sistem & Aktivasi Masa Garansi Bug 30 Hari',
                        'description' => 'Dengan diterbitkannya BAST Digital Nomor ' . $bastNumber . ', pekerjaan pengembangan dinyatakan diterima dengan baik dan memicu pencairan Termin Pelunasan Akhir sebesar Rp ' . number_format($finalInvoiceAmount, 0, ',', '.') . ' serta aktivasi masa garansi bug 30 hari kalender.',
                    ],
                ],
            ]
        );

        // Record pending final settlement transaction for Client Portal payment
        if ($finalInvoiceAmount > 0 && Schema::hasTable('transactions')) {
            Transaction::query()->updateOrCreate(
                ['midtrans_order_id' => $finalOrderId],
                [
                    'user_id' => auth()->id(),
                    'status' => 'pending',
                    'total_idr' => $finalInvoiceAmount,
                    'original_currency' => 'IDR',
                    'original_amount' => $finalInvoiceAmount,
                    'customer_details' => [
                        'type' => 'bast_final_settlement',
                        'bast_number' => $bastNumber,
                        'vision_blueprint_id' => $blueprint->id,
                        'name' => $signerName,
                        'email' => $signerEmail,
                    ],
                ]
            );
        }

        $blueprint->forceFill([
            'project_status' => 'Completed',
            'signed_agreement' => true,
            'signed_at' => $signedAt,
            'signer_ip' => $signerIp,
        ])->saveQuietly();

        AuditLog::record(
            action: 'uat_signed_off_and_digital_bast_issued',
            model: $bastDoc,
            newValues: $bastRecord,
            description: "UAT 100% disahkan oleh '{$signerName}' & BAST Resmi ({$bastNumber}) diterbitkan untuk '{$blueprint->nama_bisnis}'.",
            eventCategory: 'uat_bast'
        );

        return [
            'success' => true,
            'bast' => $bastRecord,
            'document_id' => $bastDoc->id,
            'uat' => $uat,
        ];
    }
}
