<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUlids;

class LegalPolicy extends Model
{
    use HasUlids;

    protected $fillable = [
        'type',
        'title',
        'content',
        'is_active',
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'is_active' => 'boolean',
    ];
}
