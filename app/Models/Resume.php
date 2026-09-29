<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Resume extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'target_role',
        'template',
        'font_family',
        'primary_color',
        'photo_url',
        'content',
        'section_order',
        'is_public',
        'ats_score',
        'ats_feedback',
    ];

    protected $casts = [
        'content' => 'array',
        'section_order' => 'array',
        'is_public' => 'boolean',
        'ats_score' => 'integer',
        'ats_feedback' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $base = $model->title ?: ($model->target_role ?: 'resume');
                $model->slug = Str::slug($base) . '-' . strtolower(Str::random(6));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function interviewSessions(): HasMany
    {
        return $this->hasMany(InterviewSession::class);
    }

    public function outreachLetters(): HasMany
    {
        return $this->hasMany(OutreachLetter::class);
    }

    public function getPublicUrlAttribute(): string
    {
        return url('/cv/' . $this->slug);
    }

    public function getAtsBadgeColorAttribute(): string
    {
        if ($this->ats_score >= 80) return 'success';
        if ($this->ats_score >= 60) return 'warning';
        return 'danger';
    }

    public function getAtsLabelAttribute(): string
    {
        if ($this->ats_score >= 85) return 'Optimal / ATS-Ready';
        if ($this->ats_score >= 70) return 'Cukup Baik';
        if ($this->ats_score >= 50) return 'Standar';
        return 'Perlu Optimasi';
    }
}
