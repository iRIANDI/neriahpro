<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectComment extends Model
{
    use HasUlids;

    protected $fillable = [
        'vision_blueprint_id',
        'project_issue_id',
        'user_id',
        'content',
        'attachments',
    ];

    protected $casts = [
        'content' => 'array',
        'attachments' => 'array',
    ];

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(VisionBlueprint::class, 'vision_blueprint_id');
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(ProjectIssue::class, 'project_issue_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
