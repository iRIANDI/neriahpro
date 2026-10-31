<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class PosStackPreset extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'pos_stack_presets';

    protected $fillable = [
        'code',
        'short_code',
        'topology',
        'sort_order',
        'name',
        'primary_language',
        'best_use_cases',
        'layer_technologies',
        'directory_structure_contract',
        'inter_layer_type_contract',
        'deployment_artifacts',
        'stack_specific_anti_slop_rules',
        'detection_keywords',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'name' => 'array',
            'best_use_cases' => 'array',
            'layer_technologies' => 'array',
            'directory_structure_contract' => 'array',
            'inter_layer_type_contract' => 'array',
            'deployment_artifacts' => 'array',
            'detection_keywords' => 'array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(PosSector::CACHE_KEY));
        static::deleted(fn () => Cache::forget(PosSector::CACHE_KEY));
    }

    public function projects(): HasMany
    {
        return $this->hasMany(PosProject::class, 'stack_preset_id');
    }

    public function localizedName(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $name = is_array($this->name) ? $this->name : [];

        return (string) ($name[$locale] ?? $name['id'] ?? $name['en'] ?? $this->code);
    }

    public function localizedUseCase(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $uc = is_array($this->best_use_cases) ? $this->best_use_cases : [];

        return (string) ($uc[$locale] ?? $uc['id'] ?? $uc['en'] ?? '');
    }
}

