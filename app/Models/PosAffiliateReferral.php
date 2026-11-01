<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosAffiliateReferral extends Model
{
    use HasUlids;

    protected $table = 'pos_affiliate_referrals';

    protected $fillable = [
        'referrer_user_id',
        'referred_user_id',
        'vision_blueprint_id',
        'transaction_id',
        'referral_code',
        'commission_type',
        'gross_transaction_idr',
        'commission_rate_percent',
        'commission_amount_idr',
        'status',
        'settled_at',
        'payout_metadata_json',
    ];

    protected function casts(): array
    {
        return [
            'gross_transaction_idr' => 'integer',
            'commission_rate_percent' => 'decimal:2',
            'commission_amount_idr' => 'integer',
            'settled_at' => 'datetime',
            'payout_metadata_json' => 'array',
        ];
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function visionBlueprint(): BelongsTo
    {
        return $this->belongsTo(VisionBlueprint::class, 'vision_blueprint_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function getConversionTrackAttribute(): string
    {
        return (string) ($this->commission_type ?? 'RETAIL_RECURRING_20');
    }

    public function getPayoutStatusAttribute(): string
    {
        return (string) ($this->status ?? 'REGISTERED');
    }
}
