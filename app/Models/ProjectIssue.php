<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectIssue extends Model
{
    use HasUlids;

    protected $fillable = [
        'vision_blueprint_id',
        'user_id',
        'feature_id',
        'title',
        'description',
        'status',
        'priority',
        'type',
        'is_approved_for_uat',
        'resolution_notes',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'resolution_notes' => 'array',
        'is_approved_for_uat' => 'boolean',
    ];

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(VisionBlueprint::class, 'vision_blueprint_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProjectComment::class);
    }
}
