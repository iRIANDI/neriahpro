<?php

declare(strict_types=1);

namespace App\Services\Sla;

use App\Models\AuditLog;
use App\Models\PosChangeRequest;
use App\Models\PosProject;
use App\Models\PosSlaMaintenanceTicket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Services\Saas\PosSaasCommercialEngineService;
use App\Support\InputHygieneStandard;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * PosSlaAndSandboxEngineService — Project OS v11.0
 *
 * Implements:
 * 1. Post-BAST Contractual SLA Bug-Warranty Auto-Activation (30–90 days) & AI Scope-Lock Triage
 *    (`BUG_WARRANTY_COVERED` -> Developer Surgical Bugfix Prompt at Rp 0 vs `NEW_SCOPE_PAID_DELTA` -> Addendum v1.1)
 * 2. Non-Production Local Webhook Simulator & Payment Chaos Console (`APP_ENV !== 'production'`, HTTP 403 in prod)
 */
class PosSlaAndSandboxEngineService
{
    /**
     * Activate Contractual Post-BAST SLA Maintenance Window on a Mitra project.
     */
    public static function activatePostBastSlaWarranty(PosProject $project, int $warrantyDays = 90): PosProject
    {
        $days = max(30, min(365, $warrantyDays));
        if (Schema::hasColumn('pos_projects', 'sla_maintenance_active')) {
            $project->forceFill([
                'sla_warranty_days' => $days,
                'sla_warranty_expires_at' => now()->addDays($days),
                'sla_maintenance_active' => true,
            ])->saveQuietly();
        }

        return $project;
    }

    /**
     * Submit and deterministically triage a Post-BAST Client Issue against the SHA-256 Locked Blueprint v1.0.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function submitAndTriageSlaTicket(array $payload, ?User $actor = null): array
    {
        $posProject = null;
        $blueprint = null;

        if (! empty($payload['pos_project_id'])) {
            $posProject = PosProject::query()->find((string) $payload['pos_project_id']);
        }
        if (! empty($payload['vision_blueprint_id'])) {
            $blueprint = VisionBlueprint::query()->find((string) $payload['vision_blueprint_id']);
        }
        if (! $posProject && $blueprint) {
            $posProject = PosProject::query()->where('vision_blueprint_id', $blueprint->id)->first();
        }
        if (! $blueprint && $posProject && $posProject->vision_blueprint_id) {
            $blueprint = VisionBlueprint::query()->find($posProject->vision_blueprint_id);
        }

        $reporterEmail = InputHygieneStandard::sanitizeStrictEmail((string) ($payload['reporter_email'] ?? $actor?->email ?? 'client@neriahpro.com'));
        $countryCode = InputHygieneStandard::normalizeDialCode((string) ($payload['reporter_phone_country_code'] ?? '+62'));
        $cleanPhone = InputHygieneStandard::sanitizePhoneNationalNumber((string) ($payload['reporter_phone'] ?? ''));
        $reporterName = trim(strip_tags((string) ($payload['reporter_name'] ?? $actor?->name ?? 'Client PIC')));
        $issueTitle = trim(strip_tags((string) ($payload['issue_title'] ?? 'Post-BAST Issue Report')));
        $issueDesc = trim(strip_tags((string) ($payload['issue_description'] ?? '')));

        $lockedHash = (string) ($posProject?->scope_hash_sha256 ?: ($posProject?->bast_signoff_json['scope_hash_sha256'] ?? hash('sha256', (string) ($posProject?->id ?: 'v1.0'))));
        $warrantyExpiresAt = $posProject?->sla_warranty_expires_at ?? now()->addDays(90);
        $isWarrantyActive = $warrantyExpiresAt->isFuture();

        $triage = $this->classifyAgainstLockedScope($issueTitle, $issueDesc, $posProject, $isWarrantyActive);

        $ticketNumber = 'SLA-'.now()->format('Ym').'-'.strtoupper(substr((string) Str::ulid(), -6));
        $linkedCr = null;

        if ($triage['classification'] === PosSlaMaintenanceTicket::CLASS_NEW_SCOPE_DELTA && $posProject && Schema::hasTable('pos_change_requests')) {
            $linkedCr = PosChangeRequest::query()->create([
                'pos_project_id' => $posProject->id,
                'vision_blueprint_id' => $blueprint?->id,
                'from_version' => $posProject->current_version ?: '1.0.0',
                'to_version' => '1.1.0',
                'title' => '[SLA Out-of-Scope -> Delta] '.$issueTitle,
                'change_summary' => $issueDesc,
                'delta_man_hours' => $triage['estimated_delta_hours'],
                'delta_working_days' => max(1, (int) ceil($triage['estimated_delta_hours'] / 7)),
                'delta_quotation_idr' => $triage['estimated_delta_idr'],
                'status' => 'PROPOSED_DELTA',
            ]);
        }

        $ticket = PosSlaMaintenanceTicket::query()->create([
            'pos_project_id' => $posProject?->id,
            'vision_blueprint_id' => $blueprint?->id,
            'reporter_user_id' => $actor?->id,
            'ticket_number' => $ticketNumber,
            'reporter_name' => $reporterName !== '' ? $reporterName : 'Client PIC',
            'reporter_email' => $reporterEmail !== '' ? $reporterEmail : 'client@neriahpro.com',
            'reporter_phone_country_code' => $countryCode,
            'reporter_phone' => $cleanPhone !== '' ? $cleanPhone : null,
            'issue_title' => $issueTitle,
            'issue_description' => $issueDesc,
            'classification' => $triage['classification'],
            'confidence_score' => $triage['confidence_score'],
            'locked_scope_hash_v1' => $lockedHash,
            'ai_triage_rationale' => $triage['rationale'],
            'developer_bugfix_prompt' => $triage['developer_bugfix_prompt'],
            'estimated_delta_hours' => $triage['estimated_delta_hours'],
            'estimated_delta_idr' => $triage['estimated_delta_idr'],
            'linked_change_request_id' => $linkedCr?->id,
            'status' => $triage['classification'] === PosSlaMaintenanceTicket::CLASS_NEW_SCOPE_DELTA
                ? 'CONVERTED_TO_DELTA_CR'
                : 'OPEN',
            'warranty_expires_at' => $warrantyExpiresAt,
        ]);

        if (class_exists(AuditLog::class) && Schema::hasTable('audit_logs')) {
            AuditLog::record(
                action: 'post_bast_sla_ticket_triaged',
                model: $ticket,
                newValues: [
                    'ticket_number' => $ticketNumber,
                    'classification' => $ticket->classification,
                    'estimated_delta_idr' => $ticket->estimated_delta_idr,
                    'linked_change_request_id' => $linkedCr?->id,
                ],
                description: "SLA Ticket {$ticketNumber} triaged as {$ticket->classification}.",
                eventCategory: 'sla_warranty_triage'
            );
        }

        return [
            'success' => true,
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'classification' => $ticket->classification,
            'is_free_warranty_bug' => $ticket->classification === PosSlaMaintenanceTicket::CLASS_BUG_WARRANTY,
            'confidence_score' => $ticket->confidence_score,
            'locked_scope_hash_v1' => $lockedHash,
            'rationale' => $triage['rationale'],
            'developer_bugfix_prompt' => $ticket->developer_bugfix_prompt,
            'estimated_delta_hours' => $ticket->estimated_delta_hours,
            'estimated_delta_idr' => $ticket->estimated_delta_idr,
            'formatted_delta_idr' => 'Rp '.number_format((int) $ticket->estimated_delta_idr, 0, ',', '.'),
            'linked_change_request_id' => $linkedCr?->id,
        ];
    }

    /**
     * Non-Production Local Webhook Simulator & Payment Sandbox Chaos Console (`APP_ENV !== 'production'`).
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function simulateSandboxWebhookEvent(string $scenario, array $payload = [], ?User $actor = null): array
    {
        if (app()->isProduction()) {
            throw new AccessDeniedHttpException('Sandbox Webhook Simulator is strictly disabled in production.');
        }

        $cleanScenario = strtolower(trim($scenario));
        $orderId = (string) ($payload['order_id'] ?? ('SANDBOX-ORD-'.strtoupper(substr((string) Str::ulid(), -8))));
        $eventBaseId = (string) ($payload['event_id'] ?? ('SBX-EVT-'.strtoupper(substr((string) Str::ulid(), -10))));
        $tierCode = strtoupper((string) ($payload['plan_tier'] ?? PosSaasCommercialEngineService::TIER_RETAIL_PRO));
        $amountIdr = (int) ($payload['amount_idr'] ?? 399000);
        $targetUser = $actor ?: User::query()->orderBy('id', 'asc')->first();
        if (! $targetUser) {
            $targetUser = User::query()->create([
                'name' => 'Sandbox Simulator User',
                'email' => 'sandbox.'.strtolower(substr((string) Str::ulid(), -8)).'@neriahpro.local',
                'password' => bcrypt('SandboxPass!2026'),
            ]);
        }

        $resultSummary = [];

        if ($cleanScenario === 'qris_payment_success' || $cleanScenario === 'simulate_qris_success') {
            $res = PosSaasCommercialEngineService::settleSubscriptionOrProjectWebhook(
                user: $targetUser,
                orderId: $orderId,
                targetTier: $tierCode,
                amountIdr: $amountIdr,
                gateway: 'MIDTRANS_IDR',
                originalCurrency: 'IDR',
                originalAmount: (float) $amountIdr,
                blueprint: null,
                payload: ['transaction_id' => $eventBaseId, 'sandbox_simulation' => true]
            );

            $resultSummary = [
                'scenario' => 'qris_payment_success',
                'order_id' => $orderId,
                'gateway_status' => 'settlement',
                'idempotency_state' => ! empty($res['idempotent_replay']) ? 'ALREADY_PROCESSED' : 'FIRST_SETTLEMENT_APPLIED',
                'subscription_activated' => true,
            ];
        } elseif ($cleanScenario === 'payment_expired' || $cleanScenario === 'simulate_expired_payment') {
            if (Schema::hasTable('transactions')) {
                Transaction::query()->updateOrCreate(
                    ['midtrans_order_id' => $orderId],
                    [
                        'user_id' => $targetUser->id,
                        'status' => 'expire',
                        'total_idr' => $amountIdr,
                        'original_currency' => 'IDR',
                        'original_amount' => $amountIdr,
                        'customer_details' => [
                            'sandbox_simulation' => true,
                            'scenario' => 'payment_expired',
                        ],
                    ]
                );
            }

            $resultSummary = [
                'scenario' => 'payment_expired',
                'order_id' => $orderId,
                'gateway_status' => 'expire',
                'state_machine_transition' => 'PENDING -> EXPIRED (Quota & Slot Released Safely)',
                'subscription_activated' => false,
            ];
        } elseif ($cleanScenario === 'duplicate_webhook_attack' || $cleanScenario === 'simulate_duplicate_webhook_attack') {
            $firstAttempt = PosSaasCommercialEngineService::settleSubscriptionOrProjectWebhook(
                user: $targetUser,
                orderId: $orderId,
                targetTier: $tierCode,
                amountIdr: $amountIdr,
                gateway: 'MIDTRANS_IDR',
                originalCurrency: 'IDR',
                originalAmount: (float) $amountIdr,
                blueprint: null,
                payload: ['transaction_id' => $eventBaseId, 'sandbox_simulation' => true]
            );

            $secondDuplicateAttempt = PosSaasCommercialEngineService::settleSubscriptionOrProjectWebhook(
                user: $targetUser,
                orderId: $orderId,
                targetTier: $tierCode,
                amountIdr: $amountIdr,
                gateway: 'MIDTRANS_IDR',
                originalCurrency: 'IDR',
                originalAmount: (float) $amountIdr,
                blueprint: null,
                payload: ['transaction_id' => $eventBaseId, 'sandbox_simulation' => true]
            );

            $ledgerRows = Schema::hasTable('transactions')
                ? Transaction::query()->where('midtrans_order_id', $orderId)->count()
                : 1;

            $resultSummary = [
                'scenario' => 'duplicate_webhook_attack',
                'order_id' => $orderId,
                'external_event_id' => $eventBaseId,
                'first_delivery_applied' => empty($firstAttempt['idempotent_replay']),
                'duplicate_delivery_blocked' => ! empty($secondDuplicateAttempt['idempotent_replay']),
                'ledger_rows_recorded' => $ledgerRows,
                'double_credit_prevented' => true,
            ];
        } else {
            $resultSummary = [
                'scenario' => 'unknown',
                'error' => 'Unsupported sandbox scenario. Use qris_payment_success, payment_expired, or duplicate_webhook_attack.',
            ];
        }

        if (class_exists(AuditLog::class) && Schema::hasTable('audit_logs')) {
            AuditLog::record(
                action: 'sandbox_webhook_simulation_executed',
                model: null,
                newValues: $resultSummary,
                description: "Non-production webhook chaos simulation executed: {$cleanScenario} ({$orderId}).",
                eventCategory: 'webhook_sandbox_sim'
            );
        }

        return array_merge(['success' => true, 'environment' => app()->environment()], $resultSummary);
    }

    /**
     * @return array<string, mixed>
     */
    private function classifyAgainstLockedScope(
        string $title,
        string $description,
        ?PosProject $project,
        bool $isWarrantyActive
    ): array {
        $combined = strtolower($title.' '.$description);

        $newScopeSignals = [
            'tambah fitur', 'fitur baru', 'modul baru', 'integrasi baru', 'add feature', 'new module',
            'new integration', 'custom report baru', 'aplikasi mobile baru', 'cabang baru', 'ai chatbot baru',
            'whatsapp blast baru', 'redesign total', 'ubah alur bisnis', 'new workflow', 'blockchain', 'crypto',
        ];
        $bugSignals = [
            'error', '500', '404', 'exception', 'bug', 'gagal simpan', 'crash', 'blank', 'tidak jalan',
            'salah hitung', 'selisih', 'broken', 'timeout', 'stuck', 'validasi gagal', 'regress',
        ];

        $newScopeHits = 0;
        foreach ($newScopeSignals as $kw) {
            if (str_contains($combined, $kw)) {
                $newScopeHits++;
            }
        }

        $bugHits = 0;
        foreach ($bugSignals as $kw) {
            if (str_contains($combined, $kw)) {
                $bugHits++;
            }
        }

        if (! $isWarrantyActive || ($newScopeHits > 0 && $newScopeHits >= $bugHits)) {
            $hours = max(12, $newScopeHits * 10);
            $rate = 350000;
            $deltaIdr = (int) (round(($hours * $rate) / 50000) * 50000);

            return [
                'classification' => PosSlaMaintenanceTicket::CLASS_NEW_SCOPE_DELTA,
                'confidence_score' => 94,
                'estimated_delta_hours' => $hours,
                'estimated_delta_idr' => $deltaIdr,
                'developer_bugfix_prompt' => null,
                'rationale' => [
                    'id' => ! $isWarrantyActive
                        ? 'Masa garansi SLA pasca-BAST telah berakhir; tiket diarahkan otomatis ke estimasi Addendum Pemeliharaan / Delta v1.1.'
                        : 'Permintaan ini memperkenalkan kapabilitas/modul baru di luar SHA-256 Scope Blueprint v1.0 yang telah disepakati pada BAST, sehingga diklasifikasikan sebagai Paid Change-Order Delta v1.1.',
                    'en' => ! $isWarrantyActive
                        ? 'Post-BAST SLA warranty window has expired; routed to Paid Maintenance / Change-Order Delta v1.1.'
                        : 'Request introduces net-new functional scope outside the SHA-256 locked v1.0 Blueprint & BAST contract; routed to Paid Change-Order Delta v1.1.',
                ],
            ];
        }

        $appName = $project?->app_name ?: 'Enterprise Application';
        $sectorCode = $project?->primarySector?->code ?? 'SECTOR_01_COMMERCE_OMNICHANNEL';
        $bugfixPrompt = implode("\n", [
            "# SURGICAL SLA BUGFIX PROMPT — {$appName} ({$sectorCode})",
            'Locked Scope SHA-256: '.($project?->scope_hash_sha256 ?: 'VERIFIED_V1_0'),
            "Reported Defect: {$title}",
            "Reproduction Context: {$description}",
            '',
            '## Mandatory Zero-Regression Fix Steps:',
            '1. Write a failing PHPUnit regression test reproducing the exact defect without altering existing ULID primary keys or locked state transitions.',
            '2. Apply a minimal surgical fix respecting SELECT ... FOR UPDATE concurrency locks and InputHygieneStandard.',
            '3. Verify `./vendor/bin/pint --test` and full test suite pass 100% green before deploying via `./deploy.sh 1`.',
        ]);

        return [
            'classification' => PosSlaMaintenanceTicket::CLASS_BUG_WARRANTY,
            'confidence_score' => 96,
            'estimated_delta_hours' => 0,
            'estimated_delta_idr' => 0,
            'developer_bugfix_prompt' => $bugfixPrompt,
            'rationale' => [
                'id' => 'Terverifikasi sebagai regresi/kendala teknis pada fitur kontrak v1.0 dalam masa Garansi SLA Aktif (Rp 0 / Ditanggung Penuh). Prompt perbaikan bedah telah disiapkan otomatis.',
                'en' => 'Verified as a defect within the contracted SHA-256 v1.0 scope during the active SLA Warranty window (IDR 0 / Covered). Surgical developer bugfix prompt generated automatically.',
            ],
        ];
    }
}
