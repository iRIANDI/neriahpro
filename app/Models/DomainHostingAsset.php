<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DomainHostingAsset extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'domain_hosting_assets';

    protected $fillable = [
        'vision_blueprint_id',
        'asset_type',
        'name',
        'domain_name',
        'provider',
        'server_ip',
        'panel_url',
        'purchase_date',
        'expires_at',
        'billing_cycle',
        'cost_price',
        'client_price',
        'currency',
        'auto_renew',
        'status',
        'reminder_days_before',
        'last_reminder_sent_at',
        'admin_notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expires_at' => 'date',
        'cost_price' => 'decimal:2',
        'client_price' => 'decimal:2',
        'auto_renew' => 'boolean',
        'reminder_days_before' => 'integer',
        'last_reminder_sent_at' => 'datetime',
    ];

    /**
     * Relationship to the associated client project / blueprint.
     */
    public function visionBlueprint(): BelongsTo
    {
        return $this->belongsTo(VisionBlueprint::class, 'vision_blueprint_id');
    }

    /**
     * Calculate remaining days until expiration.
     */
    public function getDaysRemainingAttribute(): int
    {
        if (!$this->expires_at) {
            return 999;
        }

        return (int) Carbon::now()->startOfDay()->diffInDays($this->expires_at->startOfDay(), false);
    }

    /**
     * Determine dynamic expiration status.
     */
    public function getExpirationStatusAttribute(): string
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }

        $days = $this->days_remaining;

        if ($days < 0) {
            return 'expired';
        }

        if ($days <= 7) {
            return 'critical';
        }

        $threshold = $this->reminder_days_before ?: 30;
        if ($days <= $threshold) {
            return 'expiring_soon';
        }

        return 'safe';
    }

    /**
     * Get badge color based on expiration status.
     */
    public function getExpirationColorAttribute(): string
    {
        return match ($this->expiration_status) {
            'expired', 'critical' => 'danger',
            'expiring_soon' => 'warning',
            'safe' => 'success',
            default => 'gray',
        };
    }

    /**
     * Human-friendly expiration label.
     */
    public function getExpirationLabelAttribute(): string
    {
        if ($this->status === 'cancelled') {
            return 'Dibatalkan';
        }

        $days = $this->days_remaining;

        if ($days < 0) {
            return 'Kadaluarsa (' . abs($days) . ' hari lalu)';
        }

        if ($days === 0) {
            return 'Jatuh Tempo HARI INI';
        }

        if ($days <= 7) {
            return 'Kritis (' . $days . ' hari lagi)';
        }

        return 'Sisa ' . $days . ' hari';
    }

    /**
     * Scope for active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    /**
     * Scope for assets expiring within specified days (default 30 days).
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        $today = Carbon::now()->startOfDay()->toDateString();
        $future = Carbon::now()->addDays($days)->endOfDay()->toDateString();

        return $query->where('status', '!=', 'cancelled')
            ->whereBetween('expires_at', [$today, $future]);
    }

    /**
     * Scope for already expired assets.
     */
    public function scopeExpired($query)
    {
        $today = Carbon::now()->startOfDay()->toDateString();

        return $query->where('status', '!=', 'cancelled')
            ->where('expires_at', '<', $today);
    }

    /**
     * Scope for assets needing reminders.
     */
    public function scopeNeedingReminder($query)
    {
        $today = Carbon::now()->startOfDay()->toDateString();
        $future = Carbon::now()->addDays(30)->endOfDay()->toDateString();

        return $query->where('status', '!=', 'cancelled')
            ->whereBetween('expires_at', [$today, $future])
            ->where(function ($q) {
                $q->whereNull('last_reminder_sent_at')
                  ->orWhere('last_reminder_sent_at', '<=', Carbon::now()->subDays(3));
            });
    }

    /**
     * Quick renew helper.
     */
    public function renew(int $months = 12): void
    {
        $currentExpiry = $this->expires_at ?: Carbon::now();
        // If already expired, renew from today
        $baseDate = $currentExpiry->isPast() ? Carbon::now() : $currentExpiry;

        $this->update([
            'expires_at' => $baseDate->copy()->addMonths($months),
            'status' => 'active',
            'last_reminder_sent_at' => null,
        ]);
    }
}
