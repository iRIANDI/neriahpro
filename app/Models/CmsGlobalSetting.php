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

    public static function getAllCached(): \Illuminate\Support\Collection
    {
        // 1. Purge legacy or corrupted __PHP_Incomplete_Class cache
        try {
            $legacy = \Illuminate\Support\Facades\Cache::get('cms_global_settings');
            if ($legacy instanceof \__PHP_Incomplete_Class || ($legacy instanceof \Illuminate\Support\Collection && $legacy->first() instanceof \__PHP_Incomplete_Class)) {
                \Illuminate\Support\Facades\Cache::forget('cms_global_settings');
            }
        } catch (\Throwable) {
            \Illuminate\Support\Facades\Cache::forget('cms_global_settings');
        }

        // 2. Cache raw attribute arrays only (primitives) to prevent __PHP_Incomplete_Class
        $rawAttributes = \Illuminate\Support\Facades\Cache::rememberForever('cms_global_settings_data', function () {
            try {
                return static::all()->mapWithKeys(function ($item) {
                    return [$item->key => $item->getAttributes()];
                })->toArray();
            } catch (\Throwable) {
                return [];
            }
        });

        // 3. Defensive check against corrupt cache value
        if (! is_array($rawAttributes) || $rawAttributes instanceof \__PHP_Incomplete_Class) {
            \Illuminate\Support\Facades\Cache::forget('cms_global_settings_data');
            \Illuminate\Support\Facades\Cache::forget('cms_global_settings');
            try {
                $rawAttributes = static::all()->mapWithKeys(function ($item) {
                    return [$item->key => $item->getAttributes()];
                })->toArray();
                \Illuminate\Support\Facades\Cache::forever('cms_global_settings_data', $rawAttributes);
            } catch (\Throwable) {
                $rawAttributes = [];
            }
        }

        // 4. Reconstitute models in active request lifecycle
        $collection = collect();
        foreach ($rawAttributes as $key => $attrs) {
            if (is_array($attrs)) {
                $collection->put($key, (new static)->newFromBuilder($attrs));
            }
        }

        return $collection;
    }

    /**
     * Retrieve a single setting's decoded value with fallback.
     */
    public static function getVal(string $key, mixed $default = null): mixed
    {
        $all = static::getAllCached();
        return $all->get($key)?->value ?? $default;
    }

    protected static function booted()
    {
        static::saved(function ($setting) {
            \Illuminate\Support\Facades\Cache::forget('app_timezone');
            \Illuminate\Support\Facades\Cache::forget('seo_schema_organization');
            \Illuminate\Support\Facades\Cache::forget('seo_schema_website');
            \Illuminate\Support\Facades\Cache::forget('seo_schema_project_os');
            \Illuminate\Support\Facades\Cache::forget('seo_schema_raw');
            \Illuminate\Support\Facades\Cache::forget('cms_global_settings');
            \Illuminate\Support\Facades\Cache::forget('cms_global_settings_data');
            \Illuminate\Support\Facades\Cache::forget('cms_global_setting_' . $setting->key);
            \App\Http\Controllers\SitemapController::syncRobotsTxtFile();
        });

        static::deleted(function ($setting) {
            \Illuminate\Support\Facades\Cache::forget('app_timezone');
            \Illuminate\Support\Facades\Cache::forget('seo_schema_organization');
            \Illuminate\Support\Facades\Cache::forget('seo_schema_website');
            \Illuminate\Support\Facades\Cache::forget('seo_schema_project_os');
            \Illuminate\Support\Facades\Cache::forget('seo_schema_raw');
            \Illuminate\Support\Facades\Cache::forget('cms_global_settings');
            \Illuminate\Support\Facades\Cache::forget('cms_global_settings_data');
            \Illuminate\Support\Facades\Cache::forget('cms_global_setting_' . $setting->key);
            \App\Http\Controllers\SitemapController::syncRobotsTxtFile();
        });
    }
}
