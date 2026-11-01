<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class PosVerifiedTestimonial extends Model
{
    use HasUlids;

    protected $table = 'pos_verified_testimonials';

    protected $fillable = [
        'pos_project_id',
        'vision_blueprint_id',
        'user_id',
        'sector_code',
        'stack_code',
        'public_ux_preset',
        'operational_ui_variant',
        'verification_trigger',
        'client_name',
        'client_role',
        'client_company',
        'client_email',
        'client_phone_country_code',
        'client_phone',
        'quote',
        'case_study_summary',
        'architecture_receipt_json',
        'anonymized_blueprint_json',
        'rating',
        'is_featured',
        'is_published',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'client_role' => 'array',
            'quote' => 'array',
            'case_study_summary' => 'array',
            'architecture_receipt_json' => 'array',
            'anonymized_blueprint_json' => 'array',
            'rating' => 'integer',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        $flush = static function (): void {
            Cache::forget('project_os:verified_testimonials_aniwall:v10:id');
            Cache::forget('project_os:verified_testimonials_aniwall:v10:en');
            Cache::forget('project_os:explore_seo_catalog:v10:id');
            Cache::forget('project_os:explore_seo_catalog:v10:en');
        };

        static::saved($flush);
        static::deleted($flush);
    }

    public function posProject(): BelongsTo
    {
        return $this->belongsTo(PosProject::class, 'pos_project_id');
    }

    public function visionBlueprint(): BelongsTo
    {
        return $this->belongsTo(VisionBlueprint::class, 'vision_blueprint_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function localizedQuote(?string $locale = null): string
    {
        $loc = $locale ?: (app()->getLocale() === 'en' ? 'en' : 'id');
        $arr = is_array($this->quote) ? $this->quote : [];

        return (string) ($arr[$loc] ?? $arr['id'] ?? $arr['en'] ?? '');
    }

    public function localizedClientRole(?string $locale = null): string
    {
        $loc = $locale ?: (app()->getLocale() === 'en' ? 'en' : 'id');
        $arr = is_array($this->client_role) ? $this->client_role : [];

        return (string) ($arr[$loc] ?? $arr['id'] ?? $arr['en'] ?? '');
    }

    /**
     * Native JSON array translation getter compatible with TranslatableTabs & catalog readers.
     *
     * @return array<string, string>
     */
    public function getTranslations(string $field): array
    {
        $val = $this->getAttribute($field);

        return is_array($val) ? $val : [];
    }

    /**
     * Native JSON array translation setter.
     *
     * @param  array<string, string>  $translations
     */
    public function setTranslations(string $field, array $translations): self
    {
        $this->setAttribute($field, $translations);

        return $this;
    }
}
