<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUlids;

class TranslationString extends Model
{
    use HasUlids;

    protected $fillable = ['group', 'key', 'text'];

    protected $casts = [
        'text' => 'array',
    ];
}
