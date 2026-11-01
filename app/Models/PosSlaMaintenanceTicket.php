<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosSlaMaintenanceTicket extends Model
{
    use HasUlids;

    public const CLASS_BUG_WARRANTY = 'BUG_WARRANTY_COVERED';

    public const CLASS_NEW_SCOPE_DELTA = 'NEW_SCOPE_PAID_DELTA';

    public const CLASS_PENDING = 'PENDING_TRIAGE';

    protected $table = 'pos_sla_maintenance_tickets';

    protected $fillable = [
        'pos_project_id',
        'vision_blueprint_id',
        'reporter_user_id',
        'ticket_number',
        'reporter_name',
        'reporter_email',
        'reporter_phone_country_code',
        'reporter_phone',
        'issue_title',
        'issue_description',
        'classification',
        'confidence_score',
        'locked_scope_hash_v1',
        'ai_triage_rationale',
        'developer_bugfix_prompt',
        'estimated_delta_hours',
        'estimated_delta_idr',
        'linked_change_request_id',
        'status',
        'warranty_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'confidence_score' => 'integer',
            'ai_triage_rationale' => 'array',
            'estimated_delta_hours' => 'integer',
            'estimated_delta_idr' => 'integer',
            'warranty_expires_at' => 'datetime',
        ];
    }

    public function posProject(): BelongsTo
    {
        return $this->belongsTo(PosProject::class, 'pos_project_id');
    }

    public function visionBlueprint(): BelongsTo
    {
        return $this->belongsTo(VisionBlueprint::class, 'vision_blueprint_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    public function linkedChangeRequest(): BelongsTo
    {
        return $this->belongsTo(PosChangeRequest::class, 'linked_change_request_id');
    }
}
