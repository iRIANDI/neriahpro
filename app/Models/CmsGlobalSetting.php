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
            \Illuminate\Support\Facades\Cache::forget('app_timezone');
            \Illuminate\Support\Facades\Cache::forget('seo_schema_organization');
            \Illuminate\Support\Facades\Cache::forget('seo_schema_website');
            \Illuminate\Support\Facades\Cache::forget('seo_schema_project_os');
            \Illuminate\Support\Facades\Cache::forget('seo_schema_raw');
            \Illuminate\Support\Facades\Cache::forget('cms_global_settings');
            \Illuminate\Support\Facades\Cache::forget('cms_global_setting_' . $setting->key);
        });

        static::deleted(function ($setting) {
            \Illuminate\Support\Facades\Cache::forget('app_timezone');
            \Illuminate\Support\Facades\Cache::forget('seo_schema_organization');
            \Illuminate\Support\Facades\Cache::forget('seo_schema_website');
            \Illuminate\Support\Facades\Cache::forget('seo_schema_project_os');
            \Illuminate\Support\Facades\Cache::forget('seo_schema_raw');
            \Illuminate\Support\Facades\Cache::forget('cms_global_settings');
            \Illuminate\Support\Facades\Cache::forget('cms_global_setting_' . $setting->key);
        });
    }
}
