<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosChangeRequest extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'pos_change_requests';

    protected $fillable = [
        'pos_project_id',
        'vision_blueprint_id',
        'created_by',
        'from_version',
        'to_version',
        'title',
        'change_summary',
        'added_primitives',
        'added_tables',
        'added_decision_rules',
        'delta_migrations_sql',
        'delta_graph_diff',
        'delta_tool_declarations',
        'regression_test_scenarios',
        'surgical_delta_prompt',
        'status',
        'delta_man_hours',
        'delta_working_days',
        'delta_quotation_idr',
        'addendum_status',
        'midtrans_addendum_order_id',
        'approved_by_client_at',
    ];

    protected function casts(): array
    {
        return [
            'added_primitives' => 'array',
            'added_tables' => 'array',
            'added_decision_rules' => 'array',
            'delta_graph_diff' => 'array',
            'delta_tool_declarations' => 'array',
            'regression_test_scenarios' => 'array',
            'delta_man_hours' => 'integer',
            'delta_working_days' => 'integer',
            'delta_quotation_idr' => 'decimal:2',
            'approved_by_client_at' => 'datetime',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

