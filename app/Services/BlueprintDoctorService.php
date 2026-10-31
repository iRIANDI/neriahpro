<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\PosPrimitive;
use App\Models\PosSector;
use App\Models\VisionBlueprint;
use App\Support\PosDayTwoProductionCatalog;
use App\Support\PosSectorUiArchetypeCatalog;

/**
 * Layer 8: Interactive "Blueprint Doctor" (Pre-Flight Conflict Resolver & Readiness Validator).
 *
 * Detects cross-module domain gaps, missing primitives, topology vs timeline mismatches,
 * and Day-2 operational readiness BEFORE or AFTER blueprint compilation, providing
 * deterministic 1-Click Auto-Fix payloads.
 */
class BlueprintDoctorService
{
    /**
     * Run Pre-Flight or Live Diagnostics from raw configuration payload.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function diagnose(array $payload, bool $recordAudit = false): array
    {
        $rawSectorCode = (string) ($payload['sector_code'] ?? $payload['primary_sector_code'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL');
        $sectorCode = PosSectorUiArchetypeCatalog::normalizeSectorCode($rawSectorCode);

        $primitiveCodes = array_values(array_unique(array_filter(
            array_map('strval', (array) ($payload['primitive_codes'] ?? []))
        )));

        $stackCode = (string) ($payload['stack_preset_code'] ?? 'STACK_M1_LARAVEL_MONOLITH');
        if ($stackCode === '') {
            $stackCode = 'STACK_M1_LARAVEL_MONOLITH';
        }

        $workflowText = mb_strtolower(trim(implode(' ', array_filter([
            (string) ($payload['workflow_text'] ?? ''),
            (string) ($payload['core_workflow'] ?? ''),
            (string) ($payload['problem_statement'] ?? ''),
            (string) ($payload['business_goal'] ?? ''),
        ]))));

        $timelineDays = isset($payload['timeline_days']) ? (int) $payload['timeline_days'] : 45;
        if ($timelineDays <= 0) {
            $timelineDays = 45;
        }

        // If no primitives explicitly supplied yet, hydrate mandatory primitives from DB sector if available
        $sector = PosSector::query()->with('primitives')->where('code', $sectorCode)->first();
        if (empty($primitiveCodes) && $sector !== null) {
            $primitiveCodes = $sector->primitives
                ->filter(fn ($p) => (bool) ($p->pivot?->is_mandatory ?? false))
                ->pluck('code')
                ->values()
                ->all();
        }

        $findings = [];
        $recommendedPrimitives = [];
        $recommendedStackPreset = null;

        // Rule 1: Clinical Pharmacy / Manufacturing / F&B / Inventory workflow without PRIMITIVE_INVENTORY_LEDGER
        $needsInventorySector = in_array($sectorCode, [
            'SECTOR_03_HEALTHCARE_CLINICAL',
            'SECTOR_05_INDUSTRIAL_MRP',
            'SECTOR_09_FOOD_BEVERAGE',
        ], true);
        $mentionsInventoryKeywords = (bool) preg_match('/\b(apotek|resep|obat|fefo|batch|bahan baku|gudang|stok|warehouse|inventory|sku)\b/iu', $workflowText);

        if (($needsInventorySector || $mentionsInventoryKeywords) && ! in_array('PRIMITIVE_INVENTORY_LEDGER', $primitiveCodes, true)) {
            $recommendedPrimitives[] = 'PRIMITIVE_INVENTORY_LEDGER';
            $findings[] = [
                'code' => 'DOC_GAP_INVENTORY_FEFO_LEDGER',
                'severity' => 'WARNING',
                'category' => 'CROSS_MODULE_DEPENDENCY',
                'title' => [
                    'id' => 'Modul Stok / Batch FEFO Belum Aktif (Potensi Selisih Stok & Resep)',
                    'en' => 'Inventory / Batch FEFO Ledger Missing (Stock & Prescription Drift Risk)',
                ],
                'message' => [
                    'id' => $sectorCode === 'SECTOR_03_HEALTHCARE_CLINICAL'
                        ? 'Anda mengaktifkan alur Klinis/Resep Apotek tetapi belum mengaktifkan PRIMITIVE_INVENTORY_LEDGER (Batch/Expiry FEFO). Tanpa modul ini, penebusan resep tidak memotong stok batch obat secara atomik.'
                        : 'Alur bisnis membutuhkan mutasi stok/bahan baku presisi, namun PRIMITIVE_INVENTORY_LEDGER belum dipilih.',
                    'en' => 'Workflow requires atomic stock/batch FEFO deduction, but PRIMITIVE_INVENTORY_LEDGER is not enabled.',
                ],
                'auto_fix' => [
                    'action' => 'ADD_PRIMITIVES',
                    'primitives' => ['PRIMITIVE_INVENTORY_LEDGER'],
                    'label' => [
                        'id' => '+ Aktifkan Inventory & Batch FEFO Ledger',
                        'en' => '+ Enable Inventory & Batch FEFO Ledger',
                    ],
                ],
            ];
        }

        // Rule 2: Financial settlement / Commerce / Hospitality / Ticketing / GovTech without PRIMITIVE_DOUBLE_ENTRY_LEDGER
        $needsLedgerSector = in_array($sectorCode, [
            'SECTOR_01_COMMERCE_OMNICHANNEL',
            'SECTOR_02_HOSPITALITY_SPACE',
            'SECTOR_04_FINTECH_LEDGER',
            'SECTOR_10_MEDIA_TICKETING',
            'SECTOR_12_GOVTECH_NONPROFIT',
        ], true);
        $mentionsLedgerKeywords = (bool) preg_match('/\b(jurnal|buku besar|rekonsiliasi|escrow|settlement|refund|komisi|akuntansi|ledger|invoic)\b/iu', $workflowText);

        if (($needsLedgerSector || $mentionsLedgerKeywords) && ! in_array('PRIMITIVE_DOUBLE_ENTRY_LEDGER', $primitiveCodes, true)) {
            $recommendedPrimitives[] = 'PRIMITIVE_DOUBLE_ENTRY_LEDGER';
            $findings[] = [
                'code' => 'DOC_GAP_DOUBLE_ENTRY_LEDGER',
                'severity' => $sectorCode === 'SECTOR_04_FINTECH_LEDGER' ? 'WARNING' : 'RECOMMENDATION',
                'category' => 'FINANCIAL_IMMUTABILITY',
                'title' => [
                    'id' => 'Jurnal Akuntansi Berpasangan (Double-Entry Ledger) Belum Terhubung',
                    'en' => 'Immutable Double-Entry Accounting Ledger Not Attached',
                ],
                'message' => [
                    'id' => 'Transaksi pembayaran, refund, atau pencairan dana terdeteksi tanpa PRIMITIVE_DOUBLE_ENTRY_LEDGER. Mengaktifkan modul ini menjamin keseimbangan Debit == Kredit (DECIMAL 19,4) anti-selisih audit.',
                    'en' => 'Monetary settlement/refund flow detected without PRIMITIVE_DOUBLE_ENTRY_LEDGER (Debit == Credit invariant).',
                ],
                'auto_fix' => [
                    'action' => 'ADD_PRIMITIVES',
                    'primitives' => ['PRIMITIVE_DOUBLE_ENTRY_LEDGER'],
                    'label' => [
                        'id' => '+ Aktifkan Double-Entry Ledger',
                        'en' => '+ Enable Double-Entry Ledger',
                    ],
                ],
            ];
        }

        // Rule 3: Multi-tier approval / HRIS / GovTech / B2B Procurement without PRIMITIVE_APPROVAL_WORKFLOW
        $needsApprovalSector = in_array($sectorCode, [
            'SECTOR_08_PROFESSIONAL_PSA',
            'SECTOR_11_HRIS_INTERNAL_OPS',
            'SECTOR_12_GOVTECH_NONPROFIT',
        ], true);
        $mentionsApprovalKeywords = (bool) preg_match('/\b(approval|otorisasi|persetujuan|berjenjang|maker-checker|yayasan|direktur|komite|pencairan|cuti|reimburse)\b/iu', $workflowText);

        if (($needsApprovalSector || $mentionsApprovalKeywords) && ! in_array('PRIMITIVE_APPROVAL_WORKFLOW', $primitiveCodes, true)) {
            $recommendedPrimitives[] = 'PRIMITIVE_APPROVAL_WORKFLOW';
            $findings[] = [
                'code' => 'DOC_GAP_MAKER_CHECKER_APPROVAL',
                'severity' => 'WARNING',
                'category' => 'GOVERNANCE_CONTROL',
                'title' => [
                    'id' => 'Otorisasi Berjenjang (Maker-Checker Approval) Belum Aktif',
                    'en' => 'Multi-Step Maker-Checker Approval Workflow Missing',
                ],
                'message' => [
                    'id' => 'Sektor/alur kerja membutuhkan persetujuan berjenjang (Maker != Approver & SLA eskalasi), tetapi PRIMITIVE_APPROVAL_WORKFLOW belum diaktifkan.',
                    'en' => 'Sector/workflow requires Maker != Approver governance gates, but PRIMITIVE_APPROVAL_WORKFLOW is not active.',
                ],
                'auto_fix' => [
                    'action' => 'ADD_PRIMITIVES',
                    'primitives' => ['PRIMITIVE_APPROVAL_WORKFLOW'],
                    'label' => [
                        'id' => '+ Aktifkan Maker-Checker Approval',
                        'en' => '+ Enable Maker-Checker Approval',
                    ],
                ],
            ];
        }

        // Rule 4: Subscription / SPP / Membership recurring billing without PRIMITIVE_SUBSCRIPTION_COHORT
        $mentionsRecurringKeywords = (bool) preg_match('/\b(langganan|subscription|recurring|spp|iuran|membership|kuota|kredit|retainer)\b/iu', $workflowText);
        if (($sectorCode === 'SECTOR_07_EDTECH_MEMBERSHIP' || $mentionsRecurringKeywords) && ! in_array('PRIMITIVE_SUBSCRIPTION_COHORT', $primitiveCodes, true)) {
            $recommendedPrimitives[] = 'PRIMITIVE_SUBSCRIPTION_COHORT';
            $findings[] = [
                'code' => 'DOC_GAP_SUBSCRIPTION_ENTITLEMENT',
                'severity' => 'RECOMMENDATION',
                'category' => 'REVENUE_RETENTION',
                'title' => [
                    'id' => 'Mesin Tagihan Berulang & Kuota (Subscription/SPP Engine) Disarankan',
                    'en' => 'Recurring Billing & Entitlement Engine Recommended',
                ],
                'message' => [
                    'id' => 'Terdeteksi pola tagihan berkala/SPP/membership. Aktifkan PRIMITIVE_SUBSCRIPTION_COHORT untuk dunning masa tenggang otomatis dan proteksi akses.',
                    'en' => 'Recurring billing/membership pattern detected. Enable PRIMITIVE_SUBSCRIPTION_COHORT for automated grace-period dunning.',
                ],
                'auto_fix' => [
                    'action' => 'ADD_PRIMITIVES',
                    'primitives' => ['PRIMITIVE_SUBSCRIPTION_COHORT'],
                    'label' => [
                        'id' => '+ Aktifkan Subscription & Cohort Engine',
                        'en' => '+ Enable Subscription & Cohort Engine',
                    ],
                ],
            ];
        }

        // Rule 5: Distributed Microservices selected with tight MVP timeline (<= 30 days)
        $isMicroservices = str_starts_with($stackCode, 'STACK_S1_') || str_starts_with($stackCode, 'STACK_S2_');
        if ($isMicroservices && $timelineDays <= 35) {
            $recommendedStackPreset = 'STACK_M1_LARAVEL_MONOLITH';
            $findings[] = [
                'code' => 'DOC_TOPOLOGY_TIMELINE_MISMATCH',
                'severity' => 'WARNING',
                'category' => 'TOPOLOGY_RISK',
                'title' => [
                    'id' => 'Risiko Over-Engineering: Microservices pada Target <= 35 Hari',
                    'en' => 'Over-Engineering Risk: Distributed Microservices on <= 35-Day Timeline',
                ],
                'message' => [
                    'id' => "Anda memilih topologi terdistribusi ({$stackCode}) dengan estimasi waktu {$timelineDays} hari. Untuk kecepatan rilis Day-1 dan transaksi ACID tanpa kompleksitas Saga Broker berlebih, disarankan menggunakan Modular Monolith (STACK_M1_LARAVEL_MONOLITH).",
                    'en' => "Distributed topology ({$stackCode}) selected for a {$timelineDays}-day timeline. Modular Monolith (STACK_M1_LARAVEL_MONOLITH) is strongly recommended for rapid Day-1 velocity.",
                ],
                'auto_fix' => [
                    'action' => 'SWITCH_STACK_PRESET',
                    'stack_preset_code' => 'STACK_M1_LARAVEL_MONOLITH',
                    'label' => [
                        'id' => '⚡ Gunakan Modular Monolith (STACK_M1)',
                        'en' => '⚡ Switch to Modular Monolith (STACK_M1)',
                    ],
                ],
            ];
        }

        // Compute concrete architectural telemetry from selected primitives + Day-2 catalog
        $primitivesModels = PosPrimitive::query()->whereIn('code', $primitiveCodes)->get();
        $ulidTablesCount = 2; // import_staging_batches + import_staging_rows always included in v3.0
        $stateMachinesCount = 0;
        $pessimisticLocksCount = 0;
        $acceptanceTestsCount = 0;

        foreach ($primitivesModels as $primitive) {
            $ulidTablesCount += count((array) ($primitive->schema_blueprints ?? []));
            $stateMachinesCount += count((array) ($primitive->state_machines ?? []));
            $pessimisticLocksCount += count((array) ($primitive->concurrency_contracts ?? []));
            $acceptanceTestsCount += count((array) ($primitive->acceptance_test_scenarios ?? []));
        }

        if (isset($payload['compiled_metrics']) && is_array($payload['compiled_metrics'])) {
            $ulidTablesCount = max($ulidTablesCount, (int) ($payload['compiled_metrics']['ulid_tables_count'] ?? $ulidTablesCount));
            $stateMachinesCount = max($stateMachinesCount, (int) ($payload['compiled_metrics']['state_machines_count'] ?? $stateMachinesCount));
            $pessimisticLocksCount = max($pessimisticLocksCount, (int) ($payload['compiled_metrics']['pessimistic_locks_count'] ?? $pessimisticLocksCount));
            $acceptanceTestsCount = max($acceptanceTestsCount, (int) ($payload['compiled_metrics']['acceptance_tests_count'] ?? $acceptanceTestsCount));
        }

        $dayTwoBundle = PosDayTwoProductionCatalog::bundleForSector($sectorCode);
        $thermalMode = (string) ($dayTwoBundle['printable_and_thermal']['thermal_printer_spec']['mode'] ?? 'NONE');

        $warningCount = count(array_filter($findings, fn (array $f): bool => $f['severity'] === 'WARNING'));
        $recommendationCount = count(array_filter($findings, fn (array $f): bool => $f['severity'] === 'RECOMMENDATION'));

        $readinessScore = max(68, 100 - ($warningCount * 10) - ($recommendationCount * 4));
        $status = $warningCount > 0
            ? 'ACTION_RECOMMENDED'
            : ($recommendationCount > 0 ? 'GOOD_WITH_SUGGESTIONS' : 'OPTIMAL_READY');

        $recommendedPrimitives = array_values(array_unique($recommendedPrimitives));

        $report = [
            'doctor_version' => '3.0.0',
            'evaluated_at' => now()->toIso8601String(),
            'sector_code' => $sectorCode,
            'stack_preset_code' => $stackCode,
            'active_primitives' => $primitiveCodes,
            'status' => $status,
            'readiness_score' => $readinessScore,
            'warning_count' => $warningCount,
            'recommendation_count' => $recommendationCount,
            'findings' => $findings,
            'one_click_auto_fix' => [
                'has_fixes' => ! empty($recommendedPrimitives) || $recommendedStackPreset !== null,
                'add_primitive_codes' => $recommendedPrimitives,
                'merged_primitive_codes' => array_values(array_unique(array_merge($primitiveCodes, $recommendedPrimitives))),
                'recommended_stack_preset_code' => $recommendedStackPreset,
            ],
            'telemetry' => [
                'ulid_tables_count' => max(4, $ulidTablesCount),
                'snapshot_columns_per_tx_table' => 5,
                'state_machines_count' => max(1, $stateMachinesCount),
                'pessimistic_locks_count' => max(1, $pessimisticLocksCount),
                'acceptance_tests_count' => max(4, $acceptanceTestsCount),
                'queue_lanes_count' => 4,
                'phased_prompts_count' => 6,
                'day_two_layers_count' => 8,
                'thermal_print_mode' => $thermalMode,
            ],
            'summary_banner' => [
                'id' => sprintf(
                    '%d Tabel ULID • %d State Machine • %d Pessimistic Lock • 4 Jalur Queue • %d Skenario Test • Skor Kesiapan %d/100',
                    max(4, $ulidTablesCount),
                    max(1, $stateMachinesCount),
                    max(1, $pessimisticLocksCount),
                    max(4, $acceptanceTestsCount),
                    $readinessScore
                ),
                'en' => sprintf(
                    '%d ULID Tables • %d State Machines • %d Pessimistic Locks • 4 Queue Lanes • %d Acceptance Tests • Readiness %d/100',
                    max(4, $ulidTablesCount),
                    max(1, $stateMachinesCount),
                    max(1, $pessimisticLocksCount),
                    max(4, $acceptanceTestsCount),
                    $readinessScore
                ),
            ],
        ];

        if ($recordAudit) {
            $subjectModel = null;
            if (! empty($payload['blueprint_model']) && $payload['blueprint_model'] instanceof VisionBlueprint) {
                $subjectModel = $payload['blueprint_model'];
            } elseif (! empty($payload['blueprint_id'])) {
                $subjectModel = VisionBlueprint::query()->find((string) $payload['blueprint_id']);
            }

            AuditLog::record(
                action: 'blueprint_doctor_diagnosed',
                model: $subjectModel,
                newValues: [
                    'sector_code' => $sectorCode,
                    'stack_preset_code' => $stackCode,
                    'status' => $status,
                    'readiness_score' => $readinessScore,
                    'findings_count' => count($findings),
                ],
                description: "Blueprint Doctor Pre-Flight Audit ({$sectorCode} / {$stackCode}): {$status} ({$readinessScore}/100)",
                eventCategory: 'blueprint_doctor'
            );
        }

        return $report;
    }

    /**
     * Run Blueprint Doctor against an existing persisted VisionBlueprint + PosProject.
     *
     * @return array<string, mixed>
     */
    public function diagnoseBlueprint(VisionBlueprint $blueprint, bool $recordAudit = true): array
    {
        $blueprint->loadMissing(['posProject.primarySector', 'posProject.stackPreset']);
        $posProject = $blueprint->posProject;

        $compiledSchema = [];
        if ($posProject !== null && ! empty($posProject->compiled_schema_json)) {
            $decoded = is_array($posProject->compiled_schema_json)
                ? $posProject->compiled_schema_json
                : json_decode((string) $posProject->compiled_schema_json, true);
            if (is_array($decoded)) {
                $compiledSchema = $decoded;
            }
        }

        $sectorCode = (string) ($posProject?->primarySector?->code
            ?? $compiledSchema['project_metadata']['primary_sector']
            ?? 'SECTOR_01_COMMERCE_OMNICHANNEL');

        $primitiveCodes = array_values(array_filter(array_map(
            'strval',
            (array) ($compiledSchema['project_metadata']['injected_primitives'] ?? [])
        )));
        if (empty($primitiveCodes) && ! empty($posProject?->selected_primitive_ids)) {
            $primitiveCodes = PosPrimitive::query()
                ->whereIn('id', (array) $posProject->selected_primitive_ids)
                ->pluck('code')
                ->values()
                ->all();
        }

        $stackCode = (string) ($posProject?->stackPreset?->code
            ?? $posProject?->stack_preset
            ?? $compiledSchema['project_metadata']['stack_preset']
            ?? 'STACK_M1_LARAVEL_MONOLITH');

        $fiturWajib = is_array($blueprint->fitur_wajib)
            ? implode(' ', array_map('strval', $blueprint->fitur_wajib))
            : (string) ($blueprint->fitur_wajib ?? '');
        $fiturTambahan = is_array($blueprint->fitur_tambahan)
            ? implode(' ', array_map('strval', $blueprint->fitur_tambahan))
            : (string) ($blueprint->fitur_tambahan ?? '');

        $workflowText = implode(' ', array_filter([
            (string) ($blueprint->masalah_utama ?? ''),
            (string) ($blueprint->tujuan_utama ?? ''),
            (string) ($blueprint->alur_kerja ?? ''),
            $fiturWajib,
            $fiturTambahan,
        ]));

        $timelineDays = 45;
        if (! empty($blueprint->target_waktu) && preg_match('/(\d+)/', (string) $blueprint->target_waktu, $m)) {
            $num = (int) $m[1];
            if (stripos((string) $blueprint->target_waktu, 'bulan') !== false || stripos((string) $blueprint->target_waktu, 'month') !== false) {
                $timelineDays = $num * 30;
            } elseif (stripos((string) $blueprint->target_waktu, 'minggu') !== false || stripos((string) $blueprint->target_waktu, 'week') !== false) {
                $timelineDays = $num * 7;
            } else {
                $timelineDays = $num;
            }
        }

        $report = $this->diagnose([
            'blueprint_id' => $blueprint->id,
            'blueprint_model' => $blueprint,
            'sector_code' => $sectorCode,
            'primitive_codes' => $primitiveCodes,
            'stack_preset_code' => $stackCode,
            'workflow_text' => $workflowText,
            'timeline_days' => $timelineDays,
            'compiled_metrics' => [
                'ulid_tables_count' => count((array) ($compiledSchema['data_dictionary'] ?? [])),
                'state_machines_count' => count((array) ($compiledSchema['state_machines'] ?? [])),
                'pessimistic_locks_count' => count((array) ($compiledSchema['domain_invariants']['concurrency_locks'] ?? [])),
                'acceptance_tests_count' => count((array) ($compiledSchema['acceptance_test_matrix'] ?? [])),
            ],
        ], $recordAudit);

        if ($posProject !== null && \Illuminate\Support\Facades\Schema::hasColumn('pos_projects', 'doctor_diagnostics_json')) {
            $posProject->forceFill([
                'doctor_diagnostics_json' => $report,
            ])->saveQuietly();
        }

        return $report;
    }
}
