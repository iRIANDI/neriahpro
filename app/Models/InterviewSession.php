<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewSession extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'resume_id',
        'user_id',
        'target_company',
        'job_title',
        'job_description',
        'questions',
        'answers',
        'evaluation',
        'overall_score',
    ];

    protected $casts = [
        'questions' => 'array',
        'answers' => 'array',
        'evaluation' => 'array',
        'overall_score' => 'integer',
    ];

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
