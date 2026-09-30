<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CvProPlan extends Model
{
    use HasUlids;

    protected $table = 'cv_pro_plans';

    protected $fillable = [
        'code',
        'type',
        'name',
        'badge',
        'description',
        'price_idr',
        'price_usd',
        'billing_cycle',
        'quotas',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'quotas' => 'array',
        'features' => 'array',
        'price_idr' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function userQuotas(): HasMany
    {
        return $this->hasMany(UserCvQuota::class, 'plan_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CvQuotaTransaction::class, 'plan_id');
    }
}
