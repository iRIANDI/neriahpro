<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutreachLetter extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'resume_id',
        'user_id',
        'letter_type',
        'company_name',
        'recipient_name',
        'generated_content',
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
