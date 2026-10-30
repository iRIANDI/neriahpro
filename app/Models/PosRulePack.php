<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PosRulePack extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'pos_rule_packs';

    protected $fillable = [
        'slug',
        'name',
        'layer',
        'enforcement_level',
        'bad_pattern_example',
        'good_pattern_example',
        'markdown_directive',
        'is_global_default',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'is_global_default' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(PosSector::CACHE_KEY));
        static::deleted(fn () => Cache::forget(PosSector::CACHE_KEY));
    }

    public function localizedName(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $name = is_array($this->name) ? $this->name : [];

        return (string) ($name[$locale] ?? $name['id'] ?? $name['en'] ?? $this->slug);
    }
}

