<?php

declare(strict_types=1);

namespace App\Actions\Privacy;

use App\Models\AuditLog;
use App\Models\Concerns\HasEncryptedPiiWithBlindIndex;
use App\Models\PosPrivacyErasureRequest;
use App\Models\PosSlaMaintenanceTicket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VisionBlueprint;
use App\Support\InputHygieneStandard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * ExportAndRedactUserPiiAction — UU PDP (UU No. 27/2022 Pasal 31–44) & GDPR Data Privacy Vault
 *
 * Provides:
 * 1. 1-Click Data Portability Export (JSON archive of user profile, blueprints, tickets & ledger references)
 * 2. Ledger-Safe Right-to-Erasure PII Redaction: replaces personal identifiers with `[REDACTED_UU_PDP_<ULID>]`
 *    across user/blueprint/ticket records while strictly preserving immutable `Transaction` financial rows
 *    and `AuditLog` SHA-256 chains so accounting balances & tax audits remain 100% intact.
 */
class ExportAndRedactUserPiiAction
{
    /**
     * Generate 1-Click UU PDP Data Portability JSON Archive and log blind-indexed audit trail.
     *
     * @return array<string, mixed>
     */
    public function exportPortabilityArchive(User $user, ?string $rawPhone = null): array
    {
        $cleanEmail = InputHygieneStandard::sanitizeStrictEmail((string) $user->email);
        $phoneE164 = $rawPhone
            ? InputHygieneStandard::composeE164Phone('+62', $rawPhone)
            : (string) ($user->phone ?? '');

        $blueprints = VisionBlueprint::query()
            ->where('email', $cleanEmail)
            ->orderBy('id', 'asc')
            ->limit(100)
            ->get(['id', 'slug', 'nama_bisnis', 'project_status', 'created_at']);

        $transactions = Schema::hasTable('transactions')
            ? Transaction::query()
                ->where('user_id', $user->id)
                ->orderBy('id', 'asc')
                ->limit(100)
                ->get(['id', 'midtrans_order_id', 'status', 'total_idr', 'created_at'])
            : collect();

        $receipt = null;
        if (Schema::hasTable('pos_privacy_erasure_requests')) {
            $receipt = PosPrivacyErasureRequest::query()->create([
                'user_id' => $user->id,
                'requester_email' => $cleanEmail,
                'requester_phone_e164' => $phoneE164 !== '' ? $phoneE164 : null,
                'request_type' => 'DATA_PORTABILITY_EXPORT',
                'status' => 'COMPLETED',
                'redaction_token' => 'EXPORT-'.strtoupper(substr((string) Str::ulid(), -10)),
                'redacted_fields_summary_json' => [
                    'blueprints_exported' => $blueprints->count(),
                    'transactions_exported' => $transactions->count(),
                    'statutory_basis' => 'UU PDP No. 27 Tahun 2022 Pasal 32 (Hak Portabilitas Data)',
                ],
                'processed_at' => now(),
            ]);
        }

        if (class_exists(AuditLog::class) && Schema::hasTable('audit_logs')) {
            AuditLog::record(
                action: 'uu_pdp_data_portability_exported',
                model: $receipt ?? $user,
                newValues: [
                    'user_id' => $user->id,
                    'email_blind_index' => HasEncryptedPiiWithBlindIndex::computeBlindIndex($cleanEmail),
                    'blueprints_count' => $blueprints->count(),
                    'transactions_count' => $transactions->count(),
                ],
                description: "UU PDP No. 27/2022 Data Portability JSON archive exported for user {$user->id}.",
                eventCategory: 'uu_pdp_privacy'
            );
        }

        return [
            'schema_version' => 'neriahpro.uu_pdp.portability.v11',
            'statutory_basis' => 'UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)',
            'exported_at' => now()->toIso8601String(),
            'request_receipt_id' => $receipt?->id,
            'email_blind_index' => HasEncryptedPiiWithBlindIndex::computeBlindIndex($cleanEmail),
            'data_subject' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $cleanEmail,
                'created_at' => $user->created_at?->toIso8601String(),
            ],
            'blueprints' => $blueprints->toArray(),
            'immutable_ledger_references' => $transactions->toArray(),
        ];
    }

    /**
     * Execute Right-to-Erasure PII Redaction (`[REDACTED_UU_PDP_<ULID>]`) while preserving immutable financial ledgers.
     *
     * @return array<string, mixed>
     */
    public function redactPersonalDataPreservingLedger(User $user, ?string $rawPhone = null): array
    {
        return DB::transaction(function () use ($user, $rawPhone): array {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $originalEmail = InputHygieneStandard::sanitizeStrictEmail((string) $lockedUser->email);
            $phoneE164 = $rawPhone ? InputHygieneStandard::composeE164Phone('+62', $rawPhone) : null;

            $ulidSuffix = strtoupper(substr((string) $lockedUser->id, -10));
            $redactedTag = "[REDACTED_UU_PDP_{$ulidSuffix}]";
            $redactedEmail = 'redacted.'.strtolower($ulidSuffix).'@privacy-vault.neriahpro.local';

            $redactedBlueprints = VisionBlueprint::query()
                ->where('email', $originalEmail)
                ->update([
                    'client_name' => $redactedTag,
                    'email' => $redactedEmail,
                    'phone' => $redactedTag,
                ]);

            $redactedTickets = 0;
            if (Schema::hasTable('pos_sla_maintenance_tickets')) {
                $redactedTickets = PosSlaMaintenanceTicket::query()
                    ->where('reporter_email', $originalEmail)
                    ->update([
                        'reporter_name' => $redactedTag,
                        'reporter_email' => $redactedEmail,
                        'reporter_phone' => null,
                    ]);
            }

            // Preserve Transaction rows & amounts 100% intact for PSAK/Tax audit, scrubbing only embedded PII strings
            $preservedTransactionsCount = 0;
            if (Schema::hasTable('transactions')) {
                $txRows = Transaction::query()->where('user_id', $lockedUser->id)->get();
                foreach ($txRows as $tx) {
                    $details = is_array($tx->customer_details) ? $tx->customer_details : [];
                    $details['name'] = $redactedTag;
                    $details['email'] = $redactedEmail;
                    $details['phone'] = $redactedTag;
                    $details['uu_pdp_redacted'] = true;
                    $tx->forceFill(['customer_details' => $details])->saveQuietly();
                    $preservedTransactionsCount++;
                }
            }

            $lockedUser->forceFill([
                'name' => $redactedTag,
                'email' => $redactedEmail,
            ])->saveQuietly();

            $receipt = null;
            if (Schema::hasTable('pos_privacy_erasure_requests')) {
                $receipt = PosPrivacyErasureRequest::query()->create([
                    'user_id' => $lockedUser->id,
                    'requester_email' => $originalEmail,
                    'requester_phone_e164' => $phoneE164,
                    'request_type' => 'RIGHT_TO_ERASURE_REDACT',
                    'status' => 'COMPLETED',
                    'redaction_token' => $redactedTag,
                    'redacted_fields_summary_json' => [
                        'user_profile_redacted' => true,
                        'blueprints_redacted' => $redactedBlueprints,
                        'sla_tickets_redacted' => $redactedTickets,
                        'financial_ledger_rows_preserved' => $preservedTransactionsCount,
                    ],
                    'processed_at' => now(),
                ]);
            }

            if (class_exists(AuditLog::class) && Schema::hasTable('audit_logs')) {
                AuditLog::record(
                    action: 'uu_pdp_right_to_erasure_redacted',
                    model: $receipt ?? $lockedUser,
                    newValues: [
                        'user_id' => $lockedUser->id,
                        'redaction_token' => $redactedTag,
                        'email_blind_index' => HasEncryptedPiiWithBlindIndex::computeBlindIndex($originalEmail),
                        'financial_ledger_rows_preserved' => $preservedTransactionsCount,
                    ],
                    description: "UU PDP Right-to-Erasure executed ({$redactedTag}); {$preservedTransactionsCount} financial ledger row(s) preserved intact.",
                    eventCategory: 'uu_pdp_privacy'
                );
            }

            return [
                'success' => true,
                'redaction_token' => $redactedTag,
                'redacted_email' => $redactedEmail,
                'request_receipt_id' => $receipt?->id,
                'email_blind_index' => HasEncryptedPiiWithBlindIndex::computeBlindIndex($originalEmail),
                'blueprints_redacted' => $redactedBlueprints,
                'sla_tickets_redacted' => $redactedTickets,
                'financial_ledger_rows_preserved' => $preservedTransactionsCount,
            ];
        });
    }
}
