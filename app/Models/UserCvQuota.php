<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCvQuota extends Model
{
    use HasUlids;

    protected $table = 'user_cv_quotas';

    protected $fillable = [
        'user_id',
        'plan_id',
        'tier_code',
        'tailor_cv_quota',
        'tailor_cv_used',
        'mock_interviews_quota',
        'mock_interviews_used',
        'ats_audits_quota',
        'ats_audits_used',
        'linkedin_packs_quota',
        'linkedin_packs_used',
        'outreach_letters_quota',
        'outreach_letters_used',
        'ai_credits_balance',
        'plan_expires_at',
    ];

    protected $casts = [
        'tailor_cv_quota' => 'integer',
        'tailor_cv_used' => 'integer',
        'mock_interviews_quota' => 'integer',
        'mock_interviews_used' => 'integer',
        'ats_audits_quota' => 'integer',
        'ats_audits_used' => 'integer',
        'linkedin_packs_quota' => 'integer',
        'linkedin_packs_used' => 'integer',
        'outreach_letters_quota' => 'integer',
        'outreach_letters_used' => 'integer',
        'ai_credits_balance' => 'integer',
        'plan_expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(CvProPlan::class, 'plan_id');
    }

    /**
     * Check if a specific quota has remaining units.
     */
    public function hasQuota(string $feature): bool
    {
        // -1 represents unlimited
        $quotaField = "{$feature}_quota";
        $usedField = "{$feature}_used";

        if (!isset($this->$quotaField)) {
            return ($this->ai_credits_balance ?? 0) > 0;
        }

        if ($this->$quotaField === -1) {
            return true;
        }

        return ($this->$quotaField - $this->$usedField) > 0;
    }

    /**
     * Get remaining count for a feature.
     */
    public function remaining(string $feature): int
    {
        $quotaField = "{$feature}_quota";
        $usedField = "{$feature}_used";

        if (!isset($this->$quotaField)) {
            return $this->ai_credits_balance ?? 0;
        }

        if ($this->$quotaField === -1) {
            return 999999; // Unlimited
        }

        return max(0, $this->$quotaField - $this->$usedField);
    }
}
