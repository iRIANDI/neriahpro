<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PosSectorPrimitive extends Pivot
{
    use HasUlids;

    protected $table = 'pos_sector_primitive';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'sector_id',
        'primitive_id',
        'is_mandatory',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_mandatory' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}

