<?php

declare(strict_types=1);

namespace App\Support;

use App\Services\Security\DocumentThreatSanitizerService;

/**
 * PosAgentShieldAndCoDesignCatalog — Project OS v9.0 Ultimate
 *
 * Unifies:
 * 1. The Co-Design Alignment & Sign-Off Room (Dual-Mirror Client Wireframe <-> Architect ULID + Per-Module Sign-Off + Dual-Key Lock)
 * 2. 12 Sector-Specific Cryptographic Audit Filter Presets (PRIMITIVE_CRYPTO_AUDIT_EXPLORER)
 * 3. Smart Idea-to-Visual-UI Auto-Matcher (36 Visual Frontend Blueprints + 5 Navigation Shells)
 * 4. Complete World-Class 7-Phase / 5-Sprint Engineering Task Matrix driven causally by Sector, Visual UI, Menus & Primitives
 */
class PosAgentShieldAndCoDesignCatalog
{
    public const VERSION = '9.0.0-ULTIMATE-AGENTSHIELD-CODESIGN';

    /**
     * 12 Sector-Specific Industry Audit Filter Presets (security.md Section 4 — Bilingual Tier-1 ID & EN).
     *
     * @return array<string, array<string, mixed>>
     */
    public static function twelveSectorAuditPresets(?string $locale = null): array
    {
        $loc = ($locale ?: app()->getLocale()) === 'en' ? 'en' : 'id';

        $raw = [
            'SECTOR_01_COMMERCE_OMNICHANNEL' => [
                'sector_code' => 'SECTOR_01_COMMERCE_OMNICHANNEL',
                'sector_number' => 1,
                'badge' => 'POS & STOCK AUDIT',
                'title' => [
                    'id' => 'Sektor 01 (Ritel & Distribusi): Void Kasir, Override Harga, Selisih Stock Opname & Retur',
                    'en' => 'Sector 01 (Retail & Distribution): Cashier Void, Manual Price Override, Stock Opname Variance & Returns',
                ],
                'watched_actions' => ['POS_VOID_BILL', 'MANUAL_PRICE_OVERRIDE', 'STOCK_OPNAME_VARIANCE', 'SALES_RETURN_APPROVED', 'FORCE_CANCEL'],
                'compliance_standards' => ['PSAK 202 Persediaan', 'UU Perlindungan Konsumen', 'ISO 27001'],
                'preset_keywords' => ['void', 'price_override', 'opname', 'retur', 'inventory'],
            ],
            'SECTOR_02_HOSPITALITY_SPACE' => [
                'sector_code' => 'SECTOR_02_HOSPITALITY_SPACE',
                'sector_number' => 2,
                'badge' => 'BOOKING & SLOT OVERRIDE',
                'title' => [
                    'id' => 'Sektor 02 (Perhotelan & Sewa Lapangan): Force-Cancel Booking, Override Tarif Peak-Hour & Buka Slot Tanpa Bayar',
                    'en' => 'Sector 02 (Hospitality & Venue): Force-Cancel Booking, Peak-Hour Rate Override & Unpaid Slot Unlock',
                ],
                'watched_actions' => ['FORCE_CANCEL_BOOKING', 'PEAK_RATE_OVERRIDE', 'UNPAID_DOOR_SLOT_UNLOCK', 'REFUND_DEPOSIT'],
                'compliance_standards' => ['PB1 Pajak Daerah', 'PCI-DSS Tokenization', 'SOC2'],
                'preset_keywords' => ['force_cancel', 'booking', 'peak', 'slot', 'deposit'],
            ],
            'SECTOR_03_HEALTHCARE_CLINICAL' => [
                'sector_code' => 'SECTOR_03_HEALTHCARE_CLINICAL',
                'sector_number' => 3,
                'badge' => 'RME KEMENKES & FEFO',
                'title' => [
                    'id' => 'Sektor 03 (Kesehatan & RS): Akses/Perubahan Rekam Medis Pasien (RME Kemenkes), Identifikasi Biometrik & Pemusnahan Obat FEFO',
                    'en' => 'Sector 03 (Healthcare & Hospital): Patient EMR Access/Amendment (MoH RME), Biometric Identification & Expired FEFO Drug Disposal',
                ],
                'watched_actions' => ['EMR_PATIENT_READ', 'EMR_SOAP_ADDENDUM', 'FEFO_EXPIRED_DRUG_DISPOSAL', 'CONTROLLED_NARCOTIC_DISPENSE', 'BIOMETRIC_PATIENT_IDENTIFIED', 'BIOMETRIC_DOCTOR_AUTHORIZED'],
                'compliance_standards' => ['Permenkes RME No. 24/2022', 'SatuSehat HL7 FHIR', 'UU PDP Kesehatan', 'HIPAA'],
                'preset_keywords' => ['emr', 'soap', 'fefo', 'rekam_medis', 'disposal', 'biometrik'],
            ],
            'SECTOR_04_FINTECH_LEDGER' => [
                'sector_code' => 'SECTOR_04_FINTECH_LEDGER',
                'sector_number' => 4,
                'badge' => 'CONTRA-ENTRY & CREDIT LIMIT',
                'title' => [
                    'id' => 'Sektor 04 (Keuangan & Fintech): Jurnal Balik (Contra-Entry), Rekonsiliasi Manual & Perubahan Plafon Kredit',
                    'en' => 'Sector 04 (Finance & Fintech): Contra-Entry Reversal, Manual Bank Reconciliation & Credit Limit Mutation',
                ],
                'watched_actions' => ['LEDGER_CONTRA_ENTRY', 'MANUAL_RECONCILIATION_MATCH', 'CREDIT_PLAFOND_OVERRIDE', 'FISCAL_PERIOD_REOPEN'],
                'compliance_standards' => ['PSAK 109 / IFRS 9', 'OJK / BI SNAP', 'SOC2 Type II', 'ISO 27001'],
                'preset_keywords' => ['contra', 'ledger', 'reconcil', 'plafon', 'credit_limit'],
            ],
            'SECTOR_05_INDUSTRIAL_MRP' => [
                'sector_code' => 'SECTOR_05_INDUSTRIAL_MRP',
                'sector_number' => 5,
                'badge' => 'BOM YIELD & QC REJECT',
                'title' => [
                    'id' => 'Sektor 05 (Manufaktur & Konstruksi): Varian Bahan Baku BoM, Reject QC Pabrik & Persetujuan Termin Proyek',
                    'en' => 'Sector 05 (Manufacturing & Construction): BoM Material Variance, Factory QC Reject & Progress Termin Sign-Off',
                ],
                'watched_actions' => ['BOM_YIELD_VARIANCE', 'QC_BATCH_REJECTED', 'CONSTRUCTION_OPNAME_TERMIN', 'WORK_ORDER_FORCE_CLOSE'],
                'compliance_standards' => ['ISO 9001 Traceability', 'PSAK 72 Konstruksi', 'SMK3'],
                'preset_keywords' => ['bom', 'qc_reject', 'work_order', 'termin'],
            ],
            'SECTOR_06_LOGISTICS_FLEET' => [
                'sector_code' => 'SECTOR_06_LOGISTICS_FLEET',
                'sector_number' => 6,
                'badge' => 'DISPATCH ROUTE & POD',
                'title' => [
                    'id' => 'Sektor 06 (Logistik & Armada): Perubahan Rute, Geofence Radius Hub, Deteksi Anti-Fake GPS & Bukti Serah Terima (PoD)',
                    'en' => 'Sector 06 (Logistics & Fleet): Driver Route, Hub Geofence, Anti-Fake GPS Detection & Proof of Delivery (PoD)',
                ],
                'watched_actions' => ['ROUTE_DEVIATION_APPROVED', 'TRIP_CASH_ALLOWANCE_ADJUST', 'POD_SIGNATURE_SEALED', 'FUEL_ANOMALY_FLAG', 'GEOFENCE_HUB_TRANSIT_ENTERED', 'GPS_TELEPORTATION_FLAGGED'],
                'compliance_standards' => ['e-Meterai Manifest', 'UU Lalu Lintas & Angkutan', 'ISO 28000'],
                'preset_keywords' => ['route', 'pod', 'driver', 'manifest', 'fleet', 'geofence', 'teleportasi'],
            ],
            'SECTOR_07_EDTECH_MEMBERSHIP' => [
                'sector_code' => 'SECTOR_07_EDTECH_MEMBERSHIP',
                'sector_number' => 7,
                'badge' => 'LOCKED GRADE & TUITION WAIVER',
                'title' => [
                    'id' => 'Sektor 07 (Pendidikan & Akademi): Perubahan Nilai Rapor/Ujian setelah Dikunci & Dispensasi SPP/Iuran',
                    'en' => 'Sector 07 (Education & Academy): Post-Lock Exam/Transcript Grade Mutation & Tuition/Fee Waiver',
                ],
                'watched_actions' => ['LOCKED_GRADE_AMENDMENT', 'TUITION_DISPENSATION_WAIVER', 'CERTIFICATE_HASH_ISSUED', 'SCHOLARSHIP_GRANT'],
                'compliance_standards' => ['PDDIKTI / Dapodik', 'UU PDP Data Anak', 'ISO 21001'],
                'preset_keywords' => ['grade', 'nilai', 'spp', 'tuition', 'dispensasi'],
            ],
            'SECTOR_08_PROFESSIONAL_PSA' => [
                'sector_code' => 'SECTOR_08_PROFESSIONAL_PSA',
                'sector_number' => 8,
                'badge' => 'BILLABLE HOURS & MILESTONE',
                'title' => [
                    'id' => 'Sektor 08 (Jasa Profesional & Agensi): Koreksi Jam Kerja (Billable Hours) & Persetujuan Milestone Klien',
                    'en' => 'Sector 08 (Professional Services & Agency): Billable Hours Adjustment & Client Milestone Sign-Off',
                ],
                'watched_actions' => ['TIMESHEET_BILLABLE_ADJUST', 'CLIENT_MILESTONE_SIGN_OFF', 'SCOPE_LOCK', 'RETAINER_WRITE_OFF'],
                'compliance_standards' => ['PSAK 72 Pendapatan Kontrak', 'PPh 23 Jasa', 'SOC2'],
                'preset_keywords' => ['billable', 'milestone', 'scope_lock', 'bast', 'spk'],
            ],
            'SECTOR_09_FOOD_BEVERAGE' => [
                'sector_code' => 'SECTOR_09_FOOD_BEVERAGE',
                'sector_number' => 9,
                'badge' => 'KDS VOID & COMPLIMENTARY',
                'title' => [
                    'id' => 'Sektor 09 (Restoran & F&B): Pembatalan Menu setelah Masuk Dapur (KDS Void) & Komplimen/Diskon Karyawan',
                    'en' => 'Sector 09 (Restaurant & F&B): Post-Kitchen KDS Order Void & Complimentary/Staff Meal Discount',
                ],
                'watched_actions' => ['KDS_POST_FIRE_VOID', 'COMPLIMENTARY_BILL_COMP', 'SPLIT_BILL_MERGE', 'CASH_DRAWER_NO_SALE_OPEN'],
                'compliance_standards' => ['PB1 Pajak Restoran 10%', 'Sertifikasi Halal Recipe Lock', 'ISO 22000'],
                'preset_keywords' => ['kds', 'void', 'compliment', 'split_bill', 'drawer'],
            ],
            'SECTOR_10_MEDIA_TICKETING' => [
                'sector_code' => 'SECTOR_10_MEDIA_TICKETING',
                'sector_number' => 10,
                'badge' => 'VIP COMP & ROYALTY SPLIT',
                'title' => [
                    'id' => 'Sektor 10 (Media, Event & Ticketing): Penerbitan Tiket VIP, Fast-Track Wajah Biometrik & Pencairan Royalti',
                    'en' => 'Sector 10 (Media, Event & Ticketing): VIP Ticket Issuance, Fast-Track Biometric Face Check-in & Royalty Payout',
                ],
                'watched_actions' => ['VIP_COMP_TICKET_ISSUED', 'ROYALTY_PAYOUT_RELEASED', 'GATE_DUPLICATE_QR_BLOCKED', 'SEAT_HOLD_OVERRIDE', 'BIOMETRIC_FAST_TRACK_ENTRY'],
                'compliance_standards' => ['Pajak Hiburan Daerah', 'UU Hak Cipta Royalti', 'OWASP Anti-Scalping'],
                'preset_keywords' => ['vip', 'ticket', 'royalty', 'gate_qr', 'biometrik'],
            ],
            'SECTOR_11_HRIS_INTERNAL_OPS' => [
                'sector_code' => 'SECTOR_11_HRIS_INTERNAL_OPS',
                'sector_number' => 11,
                'badge' => 'PAYROLL & ATTENDANCE EDIT',
                'title' => [
                    'id' => 'Sektor 11 (HRIS & Payroll): Perubahan Gaji Pokok, Absensi Biometrik Wajah, Pelanggaran Geofence & Hak Dilupakan UU PDP',
                    'en' => 'Sector 11 (HRIS & Payroll): Base Salary Mutation, Face Biometrics, Geofence Breach & UU PDP Right to Erasure',
                ],
                'watched_actions' => ['BASE_SALARY_CHANGED', 'MANUAL_ATTENDANCE_OVERRIDE', 'REIMBURSEMENT_DISBURSED', 'PPH21_TER_LOCKED', 'BIOMETRIC_RIGHT_TO_ERASURE_PURGED', 'MOCK_GPS_TELEPORTATION_BLOCKED', 'OUT_OF_GEOFENCE_PRESENCE_APPROVED'],
                'compliance_standards' => ['UU Cipta Kerja', 'PPh 21 TER 2024+', 'BPJS TK & Kesehatan', 'UU PDP Karyawan'],
                'preset_keywords' => ['salary', 'gaji', 'attendance', 'absensi', 'reimburse', 'biometrik', 'geofence', 'mock_gps'],
            ],
            'SECTOR_12_GOVTECH_NONPROFIT' => [
                'sector_code' => 'SECTOR_12_GOVTECH_NONPROFIT',
                'sector_number' => 12,
                'badge' => 'PSAK 109 / ISAK 35 / PP 93',
                'title' => [
                    'id' => 'Sektor 12 (Yayasan, NGO & Filantropi): Verifikasi Donasi Masuk, Pencairan Dana Program & Audit PSAK 109 / ISAK 35 / PP 93/2010',
                    'en' => 'Sector 12 (Non-Profit, NGO & Philanthropy): Incoming Donation Verification, Field Aid Disbursement & PSAK 109 / ISAK 35 / PP 93/2010 Audit',
                ],
                'watched_actions' => ['DONATION_RECEIPT_VERIFIED', 'RESTRICTED_FUND_DISBURSED', 'ISAK35_NET_ASSET_RECLASS', 'BENEFICIARY_AID_HANDOVER'],
                'compliance_standards' => ['PSAK 109', 'ISAK 35', 'PP No. 93 Tahun 2010', 'UU Yayasan', 'SOC2'],
                'preset_keywords' => ['donasi', 'donation', 'isak35', 'psak109', 'disburse'],
            ],
        ];

        foreach ($raw as $code => &$preset) {
            $preset['localized_title'] = $preset['title'][$loc] ?? $preset['title']['id'];
        }
        unset($preset);

        return $raw;
    }

    /**
     * Smart Idea-to-Visual-UI Auto-Matcher:
     * Deterministically selects the best Visual Frontend UI Variant (from the 36 variants) + Navigation Shell
     * based on the resolved sector and keywords in the client's idea/brief.
     *
     * @return array{selected_ui_variant: string, navigation_shell: string, variant_detail: array<string, mixed>}
     */
    public static function autoMatchVisualUiVariantForIdea(string $sectorCode, string $corpusText = '', string $locale = 'id'): array
    {
        $catalog = PosInclusiveCommercialCatalog::allSectorVisualUiCatalog();
        $variants = $catalog[$sectorCode] ?? reset($catalog) ?: [];
        $haystack = mb_strtolower($corpusText);

        $chosen = $variants[0] ?? [
            'code' => 'UI_01A_POS_SPLIT_TERMINAL',
            'default_shell' => 'SIDEBAR_ERP_DENSE',
        ];

        // Keyword heuristics for Variant B vs Variant C within each sector
        if (count($variants) >= 3 && $haystack !== '') {
            $preferC = (bool) preg_match('/\b(?:grosir|b2b|wholesale|matrix|multi-cabang|holding|cfo|treasury|board|eksekutif|executive|lapangan|field|kurir|pwa|mobile|relawan|self-service|ess)\b/iu', $haystack);
            $preferB = ! $preferC && (bool) preg_match('/\b(?:kiosk|touch|sentuh|self-order|qr\s*meja|portal|publik|donatur|wali|orang\s*tua|parent|pasien|antrian|queue|kanban|jadwal|shift)\b/iu', $haystack);

            if ($preferC && isset($variants[2])) {
                $chosen = $variants[2];
            } elseif ($preferB && isset($variants[1])) {
                $chosen = $variants[1];
            }
        }

        $contract = PosInclusiveCommercialCatalog::visualUiContractForSector(
            $sectorCode,
            (string) ($chosen['code'] ?? null),
            (string) ($chosen['default_shell'] ?? null),
            $locale
        );

        return [
            'selected_ui_variant' => (string) $contract['selected_variant_code'],
            'navigation_shell' => (string) $contract['navigation_shell_code'],
            'variant_detail' => $contract,
        ];
    }

    /**
     * Build the Interactive Co-Design Alignment & Sign-Off Room Contract ("Satu Data, Dua Kacamata"):
     * Synchronizes Business Modules (L1–L4 Divisions + Custom Client Needs), Visual UI Wireframe Simulation,
     * Architect ULID Tables & State Machines, Per-Module Clause Sign-Offs, Live RAB Impact, and Dual-Key Ceremony.
     *
     * @param  array<string, mixed>  $schema
     * @param  array<int, array<string, mixed>>|null  $existingModules
     * @param  array<string, mixed>|null  $dualKeyState
     * @return array<string, mixed>
     */
    public static function buildCoDesignAlignmentContract(
        array $schema,
        ?array $existingModules = null,
        ?array $dualKeyState = null,
        string $locale = 'id'
    ): array {
        $loc = $locale === 'en' ? 'en' : 'id';
        $divisions = (array) ($schema['enterprise_menu_matrix']['divisions'] ?? []);
        $tables = (array) ($schema['data_dictionary'] ?? []);
        $stateMachines = (array) ($schema['state_machines'] ?? []);
        $locks = (array) ($schema['domain_invariants']['concurrency_locks'] ?? []);
        $visualContract = (array) ($schema['visual_frontend_ui_contract'] ?? []);
        $panes = (array) ($visualContract['wireframe_panes'] ?? []);

        $existingByKey = [];
        $customModules = [];
        foreach (($existingModules ?? []) as $em) {
            if (! is_array($em) || empty($em['module_code'])) {
                continue;
            }
            $existingByKey[(string) $em['module_code']] = $em;
            if (! empty($em['is_custom_client_addition'])) {
                $customModules[] = $em;
            }
        }

        $compiledModules = [];
        $tableChunks = array_chunk($tables, max(1, (int) ceil(count($tables) / max(1, count($divisions)))));

        foreach ($divisions as $idx => $div) {
            $moduleCode = (string) ($div['division_code'] ?? ('MOD_DIV_0' . ($idx + 1)));
            $saved = $existingByKey[$moduleCode] ?? [];
            $menus = array_values(array_filter(
                (array) ($div['menus'] ?? []),
                static fn ($m): bool => ! empty($m['included_in_v1'])
            ));
            $menuLabels = array_values(array_map(
                static fn ($m): string => (string) ($m['label'][$loc] ?? $m['label']['id'] ?? $m['code'] ?? 'Menu'),
                $menus
            ));

            $mappedTables = array_values(array_map(
                static fn ($t): string => (string) ($t['table'] ?? 'ulid_table'),
                $tableChunks[$idx] ?? array_slice($tables, 0, 3)
            ));
            $mappedStates = array_values(array_map(
                static fn ($sm): string => (string) (($sm['entity'] ?? 'Entity') . ': ' . implode(' -> ', array_slice((array) ($sm['states'] ?? []), 0, 4))),
                array_slice($stateMachines, $idx % max(1, count($stateMachines)), 2)
            ));

            $baseHours = max(8, count($menus) * 4);
            $extraHours = (int) ($saved['extra_man_hours'] ?? 0);
            $moduleHours = $baseHours + $extraHours;
            $ratePerHour = (int) (PricingRegistry::getCommercialEstimatorConfig()['hourly_rate_idr'] ?? 350000);
            $moduleCostIdr = $moduleHours * $ratePerHour;
            $moduleDays = (int) max(1, ceil($moduleHours / 8));

            $status = strtoupper((string) ($saved['status'] ?? 'PENDING'));
            if (! in_array($status, ['APPROVED', 'DISCUSSION', 'PENDING'], true)) {
                $status = 'PENDING';
            }

            $panePreview = $panes[$idx % max(1, count($panes))] ?? ($panes[0] ?? [
                'span' => 12,
                'label' => 'OPERATIONAL WORKSPACE',
                'widgets' => array_slice($menuLabels, 0, 3),
            ]);

            $compiledModules[] = [
                'module_number' => $idx + 1,
                'module_code' => $moduleCode,
                'is_custom_client_addition' => false,
                'title' => $div['division_name'] ?? [
                    'id' => 'Divisi Operasional ' . ($idx + 1),
                    'en' => 'Operational Division ' . ($idx + 1),
                ],
                'localized_title' => (string) ($div['division_name'][$loc] ?? $div['division_name']['id'] ?? $moduleCode),
                'status' => $status,
                'client_business_lens' => [
                    'visual_ui_variant' => $visualContract['selected_variant_code'] ?? 'UI_SECTOR_DEFAULT',
                    'navigation_shell' => $visualContract['navigation_shell_code'] ?? 'SIDEBAR_ERP_DENSE',
                    'wireframe_pane_label' => $panePreview['label'] ?? 'PRIMARY SCREEN',
                    'wireframe_widgets' => ! empty($panePreview['widgets']) ? $panePreview['widgets'] : array_slice($menuLabels, 0, 4),
                    'included_menus' => $menuLabels,
                    'estimated_cost_idr' => $moduleCostIdr,
                    'formatted_cost_idr' => 'Rp ' . number_format($moduleCostIdr, 0, ',', '.'),
                    'estimated_days' => $moduleDays,
                ],
                'architect_technical_lens' => [
                    'ulid_tables' => $mappedTables,
                    'state_machines' => $mappedStates,
                    'concurrency_locks' => array_slice(array_values(array_map(
                        static fn ($l): string => is_array($l) ? (string) ($l['target'] ?? $l['mechanism'] ?? 'SELECT ... FOR UPDATE') : (string) $l,
                        $locks
                    )), 0, 2),
                    'websocket_channel' => 'organization.{ulid}.' . strtolower($moduleCode),
                ],
                'annotations' => array_values((array) ($saved['annotations'] ?? [])),
                'extra_man_hours' => $extraHours,
                'approved_by_name' => $saved['approved_by_name'] ?? null,
                'approved_by_ip' => $saved['approved_by_ip'] ?? null,
                'approved_at' => $saved['approved_at'] ?? null,
            ];
        }

        // Append any custom client requirements added live during the Co-Design Alignment Room session
        foreach ($customModules as $cIdx => $cm) {
            $extraHours = max(4, (int) ($cm['extra_man_hours'] ?? 12));
            $ratePerHour = (int) (PricingRegistry::getCommercialEstimatorConfig()['hourly_rate_idr'] ?? 350000);
            $costIdr = $extraHours * $ratePerHour;
            $days = (int) max(1, ceil($extraHours / 8));
            $titleArr = is_array($cm['title'] ?? null)
                ? $cm['title']
                : ['id' => (string) ($cm['localized_title'] ?? 'Kebutuhan Tambahan Klien'), 'en' => (string) ($cm['localized_title'] ?? 'Custom Client Requirement')];

            $compiledModules[] = [
                'module_number' => count($compiledModules) + 1,
                'module_code' => (string) $cm['module_code'],
                'is_custom_client_addition' => true,
                'title' => $titleArr,
                'localized_title' => (string) ($titleArr[$loc] ?? $titleArr['id']),
                'status' => in_array(($cm['status'] ?? 'PENDING'), ['APPROVED', 'DISCUSSION', 'PENDING'], true) ? $cm['status'] : 'PENDING',
                'client_business_lens' => [
                    'visual_ui_variant' => $visualContract['selected_variant_code'] ?? 'UI_SECTOR_DEFAULT',
                    'navigation_shell' => $visualContract['navigation_shell_code'] ?? 'SIDEBAR_ERP_DENSE',
                    'wireframe_pane_label' => 'CUSTOM CLIENT MODULE',
                    'wireframe_widgets' => (array) ($cm['client_business_lens']['wireframe_widgets'] ?? [$titleArr[$loc] ?? 'Custom Feature']),
                    'included_menus' => [$titleArr[$loc] ?? 'Custom Feature'],
                    'estimated_cost_idr' => $costIdr,
                    'formatted_cost_idr' => 'Rp ' . number_format($costIdr, 0, ',', '.'),
                    'estimated_days' => $days,
                ],
                'architect_technical_lens' => [
                    'ulid_tables' => (array) ($cm['architect_technical_lens']['ulid_tables'] ?? ['custom_module_records (ULID)']),
                    'state_machines' => ['CustomFlow: DRAFT -> SUBMITTED -> APPROVED -> COMPLETED'],
                    'concurrency_locks' => ['Atomic DB::transaction + Row-Level Pessimistic Lock'],
                    'websocket_channel' => 'organization.{ulid}.custom_' . ($cIdx + 1),
                ],
                'annotations' => array_values((array) ($cm['annotations'] ?? [])),
                'extra_man_hours' => $extraHours,
                'approved_by_name' => $cm['approved_by_name'] ?? null,
                'approved_by_ip' => $cm['approved_by_ip'] ?? null,
                'approved_at' => $cm['approved_at'] ?? null,
            ];
        }

        $totalModules = count($compiledModules);
        $approvedCount = count(array_filter($compiledModules, static fn (array $m): bool => $m['status'] === 'APPROVED'));
        $discussionCount = count(array_filter($compiledModules, static fn (array $m): bool => $m['status'] === 'DISCUSSION'));
        $totalExtraHours = array_sum(array_map(static fn (array $m): int => (int) ($m['extra_man_hours'] ?? 0), $compiledModules));
        $approvalPercent = $totalModules > 0 ? (int) round(($approvedCount / $totalModules) * 100) : 0;
        $allModulesApproved = $totalModules > 0 && $approvedCount === $totalModules;

        $dualKey = is_array($dualKeyState) ? $dualKeyState : [];
        $isDualKeyLocked = ! empty($dualKey['locked_v1_readonly']) && ! empty($dualKey['master_scope_hash_sha256']);

        return [
            'room_version' => '9.0.0-DUAL-MIRROR-REVERB',
            'realtime_channel' => 'organization.{ulid}.codesign_alignment',
            'total_modules_count' => $totalModules,
            'approved_modules_count' => $approvedCount,
            'discussion_modules_count' => $discussionCount,
            'pending_modules_count' => max(0, $totalModules - $approvedCount - $discussionCount),
            'approval_progress_percent' => $approvalPercent,
            'extra_codesign_man_hours' => $totalExtraHours,
            'can_execute_dual_key_lock' => $allModulesApproved && ! $isDualKeyLocked,
            'dual_key_ceremony' => [
                'is_locked' => $isDualKeyLocked,
                'architect_signed_by' => $dualKey['architect_signed_by'] ?? null,
                'architect_signed_at' => $dualKey['architect_signed_at'] ?? null,
                'client_signed_by' => $dualKey['client_signed_by'] ?? null,
                'client_signed_at' => $dualKey['client_signed_at'] ?? null,
                'master_scope_hash_sha256' => $dualKey['master_scope_hash_sha256'] ?? null,
                'spk_document_id' => $dualKey['spk_document_id'] ?? null,
            ],
            'modules' => $compiledModules,
        ];
    }

    /**
     * Compile a complete, deterministic World-Class 7-Phase / 5-Sprint Engineering Task Matrix
     * driven directly by the chosen Functional Sector, Visual UI Variant, Navigation Shell, Stack Preset,
     * Primitives, L1–L4 Menus, AgentShield Security, and 12-Sector Cryptographic Audit Explorer.
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    public static function compileWorldClassSprintTaskMatrix(array $schema, string $locale = 'id'): array
    {
        $loc = $locale === 'en' ? 'en' : 'id';
        $sectorCode = (string) ($schema['project_metadata']['primary_sector'] ?? 'SECTOR_01_COMMERCE_OMNICHANNEL');
        $uiCode = (string) ($schema['visual_frontend_ui_contract']['selected_variant_code'] ?? 'UI_01A_POS_SPLIT_TERMINAL');
        $shellCode = (string) ($schema['visual_frontend_ui_contract']['navigation_shell_code'] ?? 'SIDEBAR_ERP_DENSE');
        $publicUxCode = (string) ($schema['dual_zone_ui_contract']['zone_b_public_frontstage']['selected_public_ux_preset'] ?? 'UX_F2_KINETIC_ANIWALL');
        $publicUxName = (string) ($schema['dual_zone_ui_contract']['zone_b_public_frontstage']['preset_name'] ?? 'Kinetic Aniwall');
        $stackCode = (string) ($schema['project_metadata']['stack_preset'] ?? 'STACK_M1_LARAVEL_MONOLITH');
        $textDir = (string) ($schema['project_metadata']['text_direction'] ?? 'ltr');
        $tablesCount = count((array) ($schema['data_dictionary'] ?? []));
        $statesCount = count((array) ($schema['state_machines'] ?? []));
        $menusCount = (int) ($schema['enterprise_menu_matrix']['included_v1_menus_count'] ?? 25);
        $allAuditPresets = self::twelveSectorAuditPresets($loc);
        $auditPreset = $allAuditPresets[$sectorCode] ?? reset($allAuditPresets);
        $presetTitle = (string) ($auditPreset['localized_title'] ?? $sectorCode);

        $sprints = [
            [
                'sprint' => 1,
                'phase_range' => 'Phase 1–2',
                'code' => 'SPRINT_01_FOUNDATION_AGENTSHIELD_DAG_DDL',
                'title' => [
                    'id' => "Sprint 1: Pondasi {$stackCode}, AgentShield Sandbox & Migrasi ULID DAG ({$tablesCount} Tabel)",
                    'en' => "Sprint 1: {$stackCode} Foundation, AgentShield Sandbox & Kahn DAG ULID Migrations ({$tablesCount} Tables)",
                ][$loc],
                'tasks' => [
                    [
                        'task_code' => 'TSK-101',
                        'role' => 'Lead Architect / DevSecOps',
                        'title' => $loc === 'en'
                            ? 'Lock .claude/settings.json File-Path Jail, APPROVED_DEPENDENCY_MANIFEST & SYNTAX_LOCK_2026.md'
                            : 'Kunci File-Path Jail .claude/settings.json, APPROVED_DEPENDENCY_MANIFEST & SYNTAX_LOCK_2026.md',
                        'deliverable' => '.claude/settings.json + .cursorrules + pos:check-env',
                    ],
                    [
                        'task_code' => 'TSK-102',
                        'role' => 'Database Engineer',
                        'title' => $loc === 'en'
                            ? "Execute Kahn Topological Layer 0..N migrations for {$tablesCount} 26-char ULID tables + PG16 quarterly range partitioning"
                            : "Eksekusi migrasi urut DAG Kahn Layer 0..N untuk {$tablesCount} tabel ULID 26-karakter + partisi kuartal PG16",
                        'deliverable' => 'schema_complete.sql + pg16_range_partitioning.sql',
                    ],
                    [
                        'task_code' => 'TSK-103',
                        'role' => 'Backend Engineer',
                        'title' => $loc === 'en'
                            ? 'Provision Multi-Tenant Organization Isolation, Spatie RBAC Roles & 5 Enterprise Glue Tables'
                            : 'Provisi Isolasi Multi-Tenant Organization, Spatie RBAC per Divisi & 5 Tabel Enterprise Glue',
                        'deliverable' => 'InitializeNewTenantAction.php + RealisticSectorSeeder.php',
                    ],
                ],
            ],
            [
                'sprint' => 2,
                'phase_range' => 'Phase 3–4',
                'code' => 'SPRINT_02_STATE_MACHINES_LOCKS_AND_PEST_QA',
                'title' => [
                    'id' => "Sprint 2: {$statesCount} State Machine Domain, Pessimistic Lock & Deterministic match() Matrix",
                    'en' => "Sprint 2: {$statesCount} Domain State Machines, Pessimistic Locks & Deterministic match() Matrix",
                ][$loc],
                'tasks' => [
                    [
                        'task_code' => 'TSK-201',
                        'role' => 'Senior Backend Engineer',
                        'title' => $loc === 'en'
                            ? "Implement {$statesCount} invokable Action classes locked to SYMBOL_MANIFEST.json with SELECT ... FOR UPDATE"
                            : "Implementasi {$statesCount} class Action __invoke() terkunci SYMBOL_MANIFEST.json dengan SELECT ... FOR UPDATE",
                        'deliverable' => 'app/Actions/* + DeterministicDecisionMatrix.php',
                    ],
                    [
                        'task_code' => 'TSK-202',
                        'role' => 'Security & AI Engineer',
                        'title' => $loc === 'en'
                            ? 'Wire DocumentThreatSanitizerService (<UNTRUSTED_CLIENT_DOCUMENT_DATA>) & RBAC-verified Gemini Tool Calling'
                            : 'Pasang DocumentThreatSanitizerService (<UNTRUSTED_CLIENT_DOCUMENT_DATA>) & Gemini Native Tool Calling terverifikasi RBAC',
                        'deliverable' => 'DocumentThreatSanitizerService.php + AgenticActionExecutionEngine.php',
                    ],
                    [
                        'task_code' => 'TSK-203',
                        'role' => 'QA Automation Engineer',
                        'title' => $loc === 'en'
                            ? 'Pass Given-When-Then Pest concurrency race-condition, DECIMAL(19,4) zero-penny-drift & tenant isolation tests'
                            : 'Luluskan uji Pest Given-When-Then anti-race-condition, akurasi DECIMAL(19,4) 0 selisih sen & isolasi antar-tenant',
                        'deliverable' => 'tests/Feature/TenantIsolationSecurityTest.php',
                    ],
                ],
            ],
            [
                'sprint' => 3,
                'phase_range' => 'Phase 5 (Visual Frontend Lock)',
                'code' => 'SPRINT_03_VISUAL_FRONTEND_UI_AND_L1_L4_MENUS',
                'title' => [
                    'id' => "Sprint 3: Implementasi Visual Frontend [{$uiCode}] + Shell [{$shellCode}] & {$menusCount} Menu L1–L4",
                    'en' => "Sprint 3: Visual Frontend [{$uiCode}] + Navigation Shell [{$shellCode}] & {$menusCount} L1–L4 Menus",
                ][$loc],
                'tasks' => [
                    [
                        'task_code' => 'TSK-301',
                        'role' => 'Lead Frontend / UI Engineer',
                        'title' => $loc === 'en'
                            ? "Build exact locked screen layout {$uiCode} inside {$shellCode} (dir=\"{$textDir}\", Solid Brutalist Dark/Light WCAG AAA, Zero Capsule/CDN)"
                            : "Bangun tata letak layar terkunci {$uiCode} di dalam shell {$shellCode} (dir=\"{$textDir}\", Solid Brutalist Dark/Light WCAG AAA, Tanpa Kapsul/CDN)",
                        'deliverable' => "Locked Visual Layout {$uiCode}",
                    ],
                    [
                        'task_code' => 'TSK-302',
                        'role' => 'Fullstack Engineer',
                        'title' => $loc === 'en'
                            ? "Deliver all {$menusCount} L1–L4 operational sub-menus with Ultimate O(1) Cursor Table, Sticky Header, Freeze Columns & Dual Grid/List View"
                            : "Rakit seluruh {$menusCount} sub-menu L1–L4 dengan Tabel Enterprise O(1) Cursor, Sticky Header, Freeze Kolom & Dual View Grid/List",
                        'deliverable' => "ENTERPRISE_MENU_MATRIX ({$menusCount} Screens)",
                    ],
                    [
                        'task_code' => 'TSK-303',
                        'role' => 'Frontend & Print Engineer',
                        'title' => $loc === 'en'
                            ? 'Implement ESC/POS 58mm/80mm Thermal Receipt + Page-Break-Proof Official A4 PDF with local SHA-256 SVG QR'
                            : 'Implementasi Cetak Struk Thermal 58mm/80mm ESC/POS + Dokumen Resmi A4 anti-terpotong bersegel QR SVG SHA-256 lokal',
                        'deliverable' => 'thermal-and-official-a4.blade.php',
                    ],
                    [
                        'task_code' => 'TSK-304',
                        'role' => 'Public Front-Stage & Motion Engineer',
                        'title' => $loc === 'en'
                            ? "Build Zone B Public Front-Stage [{$publicUxCode}: {$publicUxName}] + PRIMITIVE_VERIFIED_SOCIAL_PROOF Aniwall obeying 5 Iron GPU Animation Laws (0% CDN, CLS=0.00)"
                            : "Bangun Zona B Public Front-Stage [{$publicUxCode}: {$publicUxName}] + PRIMITIVE_VERIFIED_SOCIAL_PROOF Aniwall patuh 5 Hukum Animasi GPU (0% CDN, CLS=0.00)",
                        'deliverable' => "Zone B {$publicUxCode} + VerifiedTestimonialAniwall",
                    ],
                    [
                        'task_code' => 'TSK-305',
                        'role' => 'UX & Form Integrity Engineer',
                        'title' => $loc === 'en'
                            ? 'Enforce Strict Email Chars, Regional Thousand Separators (1.000/1,000) & config/country_zones.php Phone Selector with instant leading-0 strip'
                            : 'Tegakkan Filter Karakter Email Murni, Pemisah Ribuan Regional (1.000/1,000) & Country Zone config/country_zones.php dengan hapus instan angka 0 awal',
                        'deliverable' => 'InputHygieneStandard + country_zones.php',
                    ],
                ],
            ],
            [
                'sprint' => 4,
                'phase_range' => 'Phase 6 (Real-World Runtime)',
                'code' => 'SPRINT_04_HARDWARE_WEBSOCKET_AND_DAY2_QUEUES',
                'title' => [
                    'id' => 'Sprint 4: Jembatan Hardware Fisik (<30ms), WebSocket Zero-Polling & 4-Lane Redis Queue',
                    'en' => 'Sprint 4: Field Hardware Bridge (<30ms), Zero-Polling WebSocket & 4-Lane Redis Queues',
                ][$loc],
                'tasks' => [
                    [
                        'task_code' => 'TSK-401',
                        'role' => 'Realtime & IoT Engineer',
                        'title' => $loc === 'en'
                            ? 'Activate <30ms Barcode/RFID Wedge, Dual-Screen BroadcastChannel local QRIS & Reverb private tenant channels'
                            : 'Aktifkan Scanner Barcode/RFID <30ms, Layar Pelanggan QRIS lokal BroadcastChannel & kanal privat Reverb tanpa wire:poll',
                        'deliverable' => 'barcode-wedge-and-dual-screen.js + Reverb Channels',
                    ],
                    [
                        'task_code' => 'TSK-402',
                        'role' => 'Platform Engineer',
                        'title' => $loc === 'en'
                            ? 'Configure Two-Step Staged CSV Bulk Importer (import_staging_*) & 4-Lane Horizon Queue (critical|default|ai-rag|low-batch)'
                            : 'Konfigurasi Impor Massal CSV 2-Tahap (import_staging_*) & 4 Jalur Antrean Redis (critical|default|ai-rag|low-batch)',
                        'deliverable' => 'project_os_day2_ops.php',
                    ],
                ],
            ],
            [
                'sprint' => 5,
                'phase_range' => 'Phase 7 (Audit, Red-Team & BAST)',
                'code' => 'SPRINT_05_CRYPTO_AUDIT_REDTEAM_AND_BAST_HANDOVER',
                'title' => [
                    'id' => 'Sprint 5: Cryptographic Audit Explorer (3-Sheet .xlsx), Red-Team Security & BAST Handover',
                    'en' => 'Sprint 5: Cryptographic Audit Explorer (3-Sheet .xlsx), Red-Team Security & BAST Handover',
                ][$loc],
                'tasks' => [
                    [
                        'task_code' => 'TSK-501',
                        'role' => 'Compliance & Audit Engineer',
                        'title' => $loc === 'en'
                            ? "Deploy PRIMITIVE_CRYPTO_AUDIT_EXPLORER + 3-Sheet .xlsx/.csv Exporter with preset: {$presetTitle}"
                            : "Pasang PRIMITIVE_CRYPTO_AUDIT_EXPLORER + Ekspor 3-Sheet .xlsx/.csv dengan preset industri: {$presetTitle}",
                        'deliverable' => 'ExportFilteredAuditExcelAction.php + SHA-256 Chain Verifier',
                    ],
                    [
                        'task_code' => 'TSK-502',
                        'role' => 'Security & Handover Lead',
                        'title' => $loc === 'en'
                            ? 'Run ./verify-blueprint.sh (0 Slop), Interactive Role SmartGuide.php, Client SOP Manual & Dual-Key UAT/BAST Sign-Off'
                            : 'Jalankan ./verify-blueprint.sh (0 Slop), SmartGuide.php per Role, Buku SOP Klien A4 & Serah Terima UAT/BAST Dual-Key',
                        'deliverable' => 'SmartGuide.php + CLIENT_SOP_AND_TRAINING_MANUAL.md + Digital BAST',
                    ],
                ],
            ],
        ];

        $totalTasks = array_sum(array_map(static fn (array $sp): int => count($sp['tasks']), $sprints));

        return [
            'version' => self::VERSION,
            'sector_code' => $sectorCode,
            'locked_visual_ui_variant' => $uiCode,
            'locked_navigation_shell' => $shellCode,
            'stack_preset' => $stackCode,
            'total_sprints' => count($sprints),
            'total_world_class_tasks' => $totalTasks,
            'sector_audit_preset' => $auditPreset,
            'sprints' => $sprints,
        ];
    }
}

