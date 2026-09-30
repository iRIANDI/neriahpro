<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvQuotaTransaction extends Model
{
    use HasUlids;

    protected $table = 'cv_quota_transactions';

    protected $fillable = [
        'user_id',
        'plan_id',
        'type',
        'feature',
        'amount',
        'balance_after',
        'description',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_after' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(CvProPlan::class, 'plan_id');
    }

    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
