<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Str;

class VisionBlueprint extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'slug',
        'client_name',
        'nama_bisnis',
        'email',
        'phone',
        'service_options',
        'project_status',
        'is_published',
        'masalah_utama',
        'tujuan_utama',
        'target_audiens',
        'aktor_sistem',
        'fitur_wajib',
        'fitur_tambahan',
        'alur_kerja',
        'kebutuhan_integrasi',
        'referensi_desain',
        'kesiapan_aset',
        'target_waktu',
        'prd_content',
        'ip_address',
        'user_metadata',
    ];

    protected $casts = [
        'service_options' => 'array',
        'user_metadata' => 'array',
        'prd_content' => 'array',
        'is_published' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $base = $model->nama_bisnis ?: $model->client_name;
                $model->slug = Str::slug($base) . '-' . strtolower(Str::random(5));
            }
        });
    }

    /**
     * Generate or regenerate the Ultimate PRD for this blueprint.
     */
    public function generateAndSavePrd(): array
    {
        $content = \App\Services\PrdGeneratorService::generate($this);
        $this->update(['prd_content' => $content]);
        return $content;
    }

    /**
     * Check if PRD is published to the client.
     */
    public function isPublic(): bool
    {
        return (bool) $this->is_published;
    }

    /**
     * Get public shareable link.
     */
    public function getPublicUrlAttribute(): string
    {
        return url('/blueprint/' . $this->slug);
    }
}
