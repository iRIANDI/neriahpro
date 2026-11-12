<?php

namespace App\Services\CvPro;

use App\Models\AuditLog;
use App\Models\CvProPlan;
use App\Models\CvQuotaTransaction;
use App\Models\User;
use App\Models\UserCvQuota;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CvQuotaService
{
    /**
     * Get or initialize quota for a user.
     * Superadmin automatically receives Universal VIP Unlimited Bypass (Rp 0).
     */
    public static function getUserQuota(User $user): UserCvQuota
    {
        $isSuperadminBypass = $user->hasUniversalSuperadminBypass('CV_PRO');
        $freePlan = CvProPlan::where('code', 'starter_free')->first();

        $quota = UserCvQuota::firstOrCreate(
            ['user_id' => $user->id],
            [
                'plan_id' => $freePlan?->id,
                'tier_code' => $isSuperadminBypass ? 'vip_superadmin_bypass' : 'free',
                'tailor_cv_quota' => $isSuperadminBypass ? -1 : ($freePlan?->quotas['tailor_cv_limit'] ?? 1),
                'tailor_cv_used' => 0,
                'mock_interviews_quota' => $isSuperadminBypass ? -1 : ($freePlan?->quotas['mock_interviews_limit'] ?? 1),
                'mock_interviews_used' => 0,
                'ats_audits_quota' => $isSuperadminBypass ? -1 : ($freePlan?->quotas['ats_audits_limit'] ?? 3),
                'ats_audits_used' => 0,
                'linkedin_packs_quota' => $isSuperadminBypass ? -1 : ($freePlan?->quotas['linkedin_packs_limit'] ?? 0),
                'linkedin_packs_used' => 0,
                'outreach_letters_quota' => $isSuperadminBypass ? -1 : ($freePlan?->quotas['outreach_letters_limit'] ?? 1),
                'outreach_letters_used' => 0,
                'ai_credits_balance' => $isSuperadminBypass ? 999999 : ($freePlan?->quotas['ai_credits'] ?? 10),
                'plan_expires_at' => null,
            ]
        );

        if ($isSuperadminBypass && ($quota->tier_code !== 'vip_superadmin_bypass' || $quota->tailor_cv_quota !== -1 || $quota->ai_credits_balance < 999999)) {
            $quota->update([
                'tier_code' => 'vip_superadmin_bypass',
                'tailor_cv_quota' => -1,
                'mock_interviews_quota' => -1,
                'ats_audits_quota' => -1,
                'linkedin_packs_quota' => -1,
                'outreach_letters_quota' => -1,
                'ai_credits_balance' => max(999999, (int) $quota->ai_credits_balance),
                'plan_expires_at' => null,
            ]);
            $quota->refresh();
        }

        return $quota;
    }

    /**
     * Explicitly activate 1-click VIP Superadmin Free Bypass on CV Pro (and record SHA-256 Audit Trail).
     */
    public static function activateSuperadminVipBypass(User $user, ?string $ipAddress = null): UserCvQuota
    {
        $quota = self::getUserQuota($user);

        $quota->update([
            'tier_code' => 'vip_superadmin_bypass',
            'tailor_cv_quota' => -1,
            'mock_interviews_quota' => -1,
            'ats_audits_quota' => -1,
            'linkedin_packs_quota' => -1,
            'outreach_letters_quota' => -1,
            'ai_credits_balance' => 999999,
            'plan_expires_at' => null,
        ]);
        $quota->refresh();

        CvQuotaTransaction::create([
            'user_id' => $user->id,
            'plan_id' => $quota->plan_id,
            'type' => 'vip_superadmin_bypass',
            'feature' => 'universal_superadmin_bypass',
            'amount' => 0,
            'balance_after' => 999999,
            'description' => 'VIP Superadmin Zero-Cost Bypass (Rp 0 Unlimited) diaktifkan untuk CV Pro Ultimate Studio.',
        ]);

        AuditLog::record(
            action: 'CV_PRO_VIP_SUPERADMIN_BYPASS_ACTIVATED',
            model: $quota,
            oldValues: ['tier_code' => 'standard'],
            newValues: [
                'tier_code' => 'vip_superadmin_bypass',
                'billing_amount_idr' => 0,
                'unlimited_all_8_pillars' => true,
                'ip_address' => $ipAddress,
            ],
            description: "Superadmin ({$user->email}) mengaktifkan VIP Owner Bypass Tanpa Bayar (Rp 0 Unlimited) pada ekosistem CV Pro Ultimate Studio.",
            eventCategory: 'superadmin_bypass',
            sectorCode: 'SECTOR_07_HRTECH_EDUTECH',
            primitiveCode: 'PRIMITIVE_UNIVERSAL_SUPERADMIN_BYPASS'
        );

        return $quota;
    }

    /**
     * Check if user is entitled to use a specific feature.
     */
    public static function canUse(User $user, string $feature): bool
    {
        // Universal Superadmin bypasses quota checks across all modules
        if ($user->hasUniversalSuperadminBypass('CV_PRO')) {
            return true;
        }

        $quota = self::getUserQuota($user);

        // Check if plan is expired
        if ($quota->plan_expires_at && $quota->plan_expires_at->isPast()) {
            // Revert to free tier if expired
            $quota->update([
                'tier_code' => 'free',
                'plan_id' => null,
                'plan_expires_at' => null,
            ]);
        }

        return $quota->hasQuota($feature);
    }

    /**
     * Consume a quota unit for a user.
     */
    public static function consume(User $user, string $feature, int $amount = 1, ?string $description = null): bool
    {
        if ($user->hasUniversalSuperadminBypass('CV_PRO')) {
            return true;
        }

        $quota = self::getUserQuota($user);

        return DB::transaction(function () use ($user, $quota, $feature, $amount, $description) {
            $quotaField = "{$feature}_quota";
            $usedField = "{$feature}_used";

            if (isset($quota->$quotaField)) {
                // Unlimited (-1)
                if ($quota->$quotaField === -1) {
                    $quota->increment($usedField, $amount);
                    $remaining = 999999;
                } else {
                    if (($quota->$quotaField - $quota->$usedField) < $amount) {
                        // Fallback: check if user has enough ai_credits_balance
                        if (($quota->ai_credits_balance ?? 0) >= ($amount * 5)) {
                            $quota->decrement('ai_credits_balance', $amount * 5);
                            $remaining = $quota->ai_credits_balance;
                        } else {
                            return false; // Insufficient quota
                        }
                    } else {
                        $quota->increment($usedField, $amount);
                        $remaining = $quota->$quotaField - $quota->$usedField;
                    }
                }
            } else {
                // Feature doesn't have dedicated counter, use ai_credits
                if (($quota->ai_credits_balance ?? 0) < $amount) {
                    return false;
                }
                $quota->decrement('ai_credits_balance', $amount);
                $remaining = $quota->ai_credits_balance;
            }

            // Record transaction audit
            CvQuotaTransaction::create([
                'user_id' => $user->id,
                'plan_id' => $quota->plan_id,
                'type' => 'usage_deduction',
                'feature' => $feature,
                'amount' => -$amount,
                'balance_after' => $remaining,
                'description' => $description ?? "Konsumsi {$amount}x {$feature}",
            ]);

            return true;
        });
    }

    /**
     * Apply a subscription plan to a user.
     */
    public static function applyPlan(User $user, CvProPlan $plan, ?string $paymentTransactionId = null): UserCvQuota
    {
        $quota = self::getUserQuota($user);
        $quotas = $plan->quotas ?? [];

        $durationDays = match ($plan->billing_cycle) {
            'quarterly' => 90,
            'yearly' => 365,
            default => 30,
        };

        $expiresAt = Carbon::now()->addDays($durationDays);

        $quota->update([
            'plan_id' => $plan->id,
            'tier_code' => $plan->code,
            'tailor_cv_quota' => $quotas['tailor_cv_limit'] ?? 15,
            'tailor_cv_used' => 0,
            'mock_interviews_quota' => $quotas['mock_interviews_limit'] ?? 5,
            'mock_interviews_used' => 0,
            'ats_audits_quota' => $quotas['ats_audits_limit'] ?? -1,
            'ats_audits_used' => 0,
            'linkedin_packs_quota' => $quotas['linkedin_packs_limit'] ?? 5,
            'linkedin_packs_used' => 0,
            'outreach_letters_quota' => $quotas['outreach_letters_limit'] ?? 10,
            'outreach_letters_used' => 0,
            'ai_credits_balance' => $quota->ai_credits_balance + ($quotas['ai_credits'] ?? 100),
            'plan_expires_at' => $expiresAt,
        ]);

        CvQuotaTransaction::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'type' => 'plan_grant',
            'feature' => 'subscription',
            'amount' => 1,
            'balance_after' => $quota->ai_credits_balance,
            'description' => "Aktivasi paket langganan {$plan->code} hingga {$expiresAt->format('d M Y')}",
            'transaction_id' => $paymentTransactionId,
        ]);

        return $quota;
    }

    /**
     * Apply an a la carte top-up package to a user.
     */
    public static function applyTopUp(User $user, CvProPlan $topupPlan, ?string $paymentTransactionId = null): UserCvQuota
    {
        $quota = self::getUserQuota($user);
        $quotas = $topupPlan->quotas ?? [];

        if (isset($quotas['tailor_cv_limit'])) {
            $quota->increment('tailor_cv_quota', $quotas['tailor_cv_limit']);
        }

        if (isset($quotas['mock_interviews_limit'])) {
            $quota->increment('mock_interviews_quota', $quotas['mock_interviews_limit']);
        }

        if (isset($quotas['ai_credits'])) {
            $quota->increment('ai_credits_balance', $quotas['ai_credits']);
        }

        $quota->refresh();

        CvQuotaTransaction::create([
            'user_id' => $user->id,
            'plan_id' => $topupPlan->id,
            'type' => 'topup',
            'feature' => $topupPlan->code,
            'amount' => 1,
            'balance_after' => $quota->ai_credits_balance,
            'description' => "Top-up paket a la carte: {$topupPlan->code}",
            'transaction_id' => $paymentTransactionId,
        ]);

        return $quota;
    }
}
