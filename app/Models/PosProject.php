<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosProject extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'pos_projects';

    protected $fillable = [
        'user_id',
        'vision_blueprint_id',
        'primary_sector_id',
        'stack_preset_id',
        'app_name',
        'architecture_archetype',
        'tenancy_model',
        'tenant_identifier',
        'stack_preset',
        'selected_primitive_ids',
        'selected_rule_pack_ids',
        'custom_entities',
        'compiled_schema_json',
        'compiled_execution_md',
        'compiled_agents_md',
        'compiled_graph_json',
        'phased_prompts_json',
        'doctor_diagnostics_json',
    ];

    protected function casts(): array
    {
        return [
            'selected_primitive_ids' => 'array',
            'selected_rule_pack_ids' => 'array',
            'custom_entities' => 'array',
            'phased_prompts_json' => 'array',
            'doctor_diagnostics_json' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function visionBlueprint(): BelongsTo
    {
        return $this->belongsTo(VisionBlueprint::class, 'vision_blueprint_id');
    }

    public function primarySector(): BelongsTo
    {
        return $this->belongsTo(PosSector::class, 'primary_sector_id');
    }

    public function stackPreset(): BelongsTo
    {
        return $this->belongsTo(PosStackPreset::class, 'stack_preset_id');
    }
}

