<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientIncidentReport extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'incident_code',
        'vision_blueprint_id',
        'client_email',
        'client_name',
        'client_phone',
        'error_type',
        'error_message',
        'technical_diagnostics',
        'client_notes',
        'status',
        'compensation_type',
        'compensation_voucher_code',
        'compensation_amount_idr',
        'extra_sla_warranty_days',
        'is_compensated',
        'ip_address',
        'idempotency_hash',
    ];

    protected $casts = [
        'technical_diagnostics' => 'array',
        'is_compensated' => 'boolean',
        'compensation_amount_idr' => 'integer',
        'extra_sla_warranty_days' => 'integer',
    ];

    /**
     * Scope for keyset cursor pagination O(1)
     */
    public function scopeRecentFirst(Builder $query): Builder
    {
        return $query->orderBy('id', 'desc');
    }

    public function visionBlueprint(): BelongsTo
    {
        return $this->belongsTo(VisionBlueprint::class, 'vision_blueprint_id');
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(BlueprintVoucher::class, 'compensation_voucher_code', 'code');
    }
}

