<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Product extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'description',
        'features',
        'price_idr',
        'price_usd',
        'is_active',
        'slug',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'features' => 'array',
        'price_idr' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
