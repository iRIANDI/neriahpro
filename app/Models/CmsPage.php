<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsPage extends Model
{
    use HasUlids;

    protected $fillable = [
        'slug',
        'title',
        'meta_description',
        'is_published',
        'plugins',
    ];

    protected $casts = [
        'title' => 'array',
        'meta_description' => 'array',
        'is_published' => 'boolean',
        'plugins' => 'array',
    ];

    protected static function booted(): void
    {
        static::saved(function (CmsPage $page) {
            \Illuminate\Support\Facades\Cache::forget("cms_page_{$page->slug}");
            \Illuminate\Support\Facades\Cache::forget('cms_page_home');
            \Illuminate\Support\Facades\Cache::forget('cms_pages_all');
        });

        static::deleted(function (CmsPage $page) {
            \Illuminate\Support\Facades\Cache::forget("cms_page_{$page->slug}");
            \Illuminate\Support\Facades\Cache::forget('cms_page_home');
            \Illuminate\Support\Facades\Cache::forget('cms_pages_all');
        });
    }
}
