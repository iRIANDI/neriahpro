<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Builder;

class BlueprintVoucher extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'max_uses',
        'used_count',
        'is_active',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'discount_value' => 'decimal:2',
        'max_uses' => 'integer',
        'used_count' => 'integer',
    ];

    /**
     * Scope to find active vouchers that are not expired.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Check if voucher is valid and can be claimed.
     */
    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Increment usage counter safely.
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }
}
