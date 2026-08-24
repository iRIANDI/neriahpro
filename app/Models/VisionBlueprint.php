<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Str;

class VisionBlueprint extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'slug',
        'client_name',
        'email',
        'phone',
        'service_options',
        'project_status',
        'ip_address',
        'user_metadata',
    ];

    protected $casts = [
        'service_options' => 'array',
        'user_metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->client_name) . '-' . strtolower(Str::random(5));
            }
        });
    }
}
