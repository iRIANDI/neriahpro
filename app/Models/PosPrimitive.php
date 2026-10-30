<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class PosPrimitive extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'pos_primitives';

    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'schema_blueprints',
        'state_machines',
        'concurrency_contracts',
        'rbac_permissions',
        'edge_case_playbooks',
        'gemini_tool_declarations',
        'detection_keywords',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'schema_blueprints' => 'array',
            'state_machines' => 'array',
            'concurrency_contracts' => 'array',
            'rbac_permissions' => 'array',
            'edge_case_playbooks' => 'array',
            'gemini_tool_declarations' => 'array',
            'detection_keywords' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(PosSector::CACHE_KEY));
        static::deleted(fn () => Cache::forget(PosSector::CACHE_KEY));
    }

    public function sectors(): BelongsToMany
    {
        return $this->belongsToMany(PosSector::class, 'pos_sector_primitive', 'primitive_id', 'sector_id')
            ->using(PosSectorPrimitive::class)
            ->withPivot(['id', 'is_mandatory', 'sort_order'])
            ->withTimestamps();
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
}

