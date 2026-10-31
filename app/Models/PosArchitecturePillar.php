<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\PosRealWorldRuntimeCatalog;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class PosArchitecturePillar extends Model
{
    use HasFactory, HasUlids;

    public const CACHE_KEY = 'project_os:15_pillars_freeze:v7_0';

    protected $table = 'pos_architecture_pillars';

    protected $fillable = [
        'pillar_number',
        'code',
        'category',
        'name',
        'function_guarantee',
        'technical_contract',
        'artifacts_list',
        'related_stage_dependencies',
        'is_locked_freeze',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'pillar_number' => 'integer',
            'name' => 'array',
            'function_guarantee' => 'array',
            'technical_contract' => 'array',
            'artifacts_list' => 'array',
            'related_stage_dependencies' => 'array',
            'is_locked_freeze' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::forget(self::CACHE_KEY);
            Cache::forget(PosSector::CACHE_KEY);
        });

        static::deleted(function (): void {
            Cache::forget(self::CACHE_KEY);
            Cache::forget(PosSector::CACHE_KEY);
        });
    }

    public function localizedName(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $arr = is_array($this->name) ? $this->name : [];

        return (string) ($arr[$locale] ?? $arr['id'] ?? $arr['en'] ?? $this->code);
    }

    public function localizedFunctionGuarantee(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $arr = is_array($this->function_guarantee) ? $this->function_guarantee : [];

        return (string) ($arr[$locale] ?? $arr['id'] ?? $arr['en'] ?? '');
    }

    public function localizedTechnicalContract(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $arr = is_array($this->technical_contract) ? $this->technical_contract : [];

        return (string) ($arr[$locale] ?? $arr['id'] ?? $arr['en'] ?? '');
    }

    /**
     * O(1) Cached retrieval of all 15 Canonical Architecture Pillars (DB-first with catalog fallback).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getCachedMatrix(?string $locale = null): array
    {
        $locale = in_array($locale, ['id', 'en'], true) ? $locale : (in_array(app()->getLocale(), ['id', 'en'], true) ? app()->getLocale() : 'id');

        $raw = Cache::rememberForever(self::CACHE_KEY, function (): array {
            try {
                if (Schema::hasTable('pos_architecture_pillars')) {
                    if (self::query()->count() === 0) {
                        PosRealWorldRuntimeCatalog::seedFifteenArchitecturePillars();
                    }
                    $rows = self::query()
                        ->where('is_active', true)
                        ->orderBy('pillar_number', 'asc')
                        ->orderBy('id', 'asc')
                        ->get();

                    if ($rows->isNotEmpty()) {
                        return $rows->map(fn (self $p): array => [
                            'id' => $p->id,
                            'pillar_number' => (int) $p->pillar_number,
                            'code' => (string) $p->code,
                            'category' => (string) $p->category,
                            'name' => is_array($p->name) ? $p->name : ['id' => (string) $p->code, 'en' => (string) $p->code],
                            'function_guarantee' => is_array($p->function_guarantee) ? $p->function_guarantee : ['id' => '', 'en' => ''],
                            'technical_contract' => is_array($p->technical_contract) ? $p->technical_contract : ['id' => '', 'en' => ''],
                            'artifacts_list' => is_array($p->artifacts_list) ? $p->artifacts_list : [],
                            'related_stage_dependencies' => is_array($p->related_stage_dependencies) ? $p->related_stage_dependencies : [],
                            'is_locked_freeze' => (bool) $p->is_locked_freeze,
                        ])->all();
                    }
                }
            } catch (\Throwable) {
                // Fallback gracefully prior to migration
            }

            return PosRealWorldRuntimeCatalog::fifteenMasterPillars();
        });

        return array_values(array_map(static function (array $row) use ($locale): array {
            $row['localized_name'] = (string) ($row['name'][$locale] ?? $row['name']['id'] ?? $row['code']);
            $row['localized_function_guarantee'] = (string) ($row['function_guarantee'][$locale] ?? $row['function_guarantee']['id'] ?? '');
            $row['localized_technical_contract'] = (string) ($row['technical_contract'][$locale] ?? $row['technical_contract']['id'] ?? '');
            $row['guaranteed_outcome'] = $row['function_guarantee'] ?? ['id' => '', 'en' => ''];
            $row['artifact_contract'] = implode(', ', is_array($row['artifacts_list'] ?? null) ? $row['artifacts_list'] : []);
            $row['maturity_percent'] = ! empty($row['is_locked_freeze']) ? 100 : 85;

            return $row;
        }, $raw));
    }
}
