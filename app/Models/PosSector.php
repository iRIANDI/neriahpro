<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class PosSector extends Model
{
    use HasFactory, HasUlids;

    public const CACHE_KEY = 'project_os:matrix_catalog:v6_0';

    protected $table = 'pos_sectors';

    protected $fillable = [
        'code',
        'sector_number',
        'name',
        'description',
        'domain_terminology',
        'blueprint_invariants',
        'ui_ux_archetypes',
        'realistic_seeder_contract',
        'day_two_ops_contract',
        'default_compliance_standards',
        'detection_keywords',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sector_number' => 'integer',
            'name' => 'array',
            'description' => 'array',
            'domain_terminology' => 'array',
            'blueprint_invariants' => 'array',
            'ui_ux_archetypes' => 'array',
            'realistic_seeder_contract' => 'array',
            'day_two_ops_contract' => 'array',
            'default_compliance_standards' => 'array',
            'detection_keywords' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    public function primitives(): BelongsToMany
    {
        return $this->belongsToMany(PosPrimitive::class, 'pos_sector_primitive', 'sector_id', 'primitive_id')
            ->using(PosSectorPrimitive::class)
            ->withPivot(['id', 'is_mandatory', 'sort_order'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(PosProject::class, 'primary_sector_id');
    }

    public function localizedName(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $name = is_array($this->name) ? $this->name : [];

        return (string) ($name[$locale] ?? $name['id'] ?? $name['en'] ?? $this->code);
    }

    public function localizedDescription(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $desc = is_array($this->description) ? $this->description : [];

        return (string) ($desc[$locale] ?? $desc['id'] ?? $desc['en'] ?? '');
    }

    public function resolvedUiArchetypes(): array
    {
        if (! empty($this->ui_ux_archetypes) && is_array($this->ui_ux_archetypes)) {
            return $this->ui_ux_archetypes;
        }

        return \App\Support\PosSectorUiArchetypeCatalog::forSector((string) $this->code);
    }

    public function resolvedSeederContract(): array
    {
        if (! empty($this->realistic_seeder_contract) && is_array($this->realistic_seeder_contract)) {
            return $this->realistic_seeder_contract;
        }

        return \App\Support\PosSectorUiArchetypeCatalog::seederForSector((string) $this->code);
    }

    public function resolvedDayTwoOpsContract(): array
    {
        if (! empty($this->day_two_ops_contract) && is_array($this->day_two_ops_contract)) {
            return $this->day_two_ops_contract;
        }

        return \App\Support\PosDayTwoProductionCatalog::bundleForSector((string) $this->code);
    }
}

