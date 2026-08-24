<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUlids;

class CmsGlobalSetting extends Model
{
    use HasUlids;

    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    protected static function booted()
    {
        static::saved(function ($setting) {
            if ($setting->key === 'app_timezone') {
                \Illuminate\Support\Facades\Cache::forget('app_timezone');
            }
        });

        static::deleted(function ($setting) {
            if ($setting->key === 'app_timezone') {
                \Illuminate\Support\Facades\Cache::forget('app_timezone');
            }
        });
    }
}
