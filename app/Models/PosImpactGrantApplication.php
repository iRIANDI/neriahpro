<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Legal\LegalDocumentVaultService;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PosImpactGrantApplication extends Model
{
    use HasUlids;

    protected $table = 'pos_impact_grant_applications';

    protected $fillable = [
        'id',
        'user_id',
        'applicant_name',
        'organization_name',
        'organization_type',
        'official_email',
        'phone_country_code',
        'phone_number',
        'legal_decree_number',
        'private_document_path',
        'requested_project_budget_idr',
        'mission_statement',
        'status',
        'reviewed_by_user_id',
        'reviewed_at',
        'granted_voucher_code',
    ];

    protected function casts(): array
    {
        return [
            'requested_project_budget_idr' => 'integer',
            'mission_statement' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (PosImpactGrantApplication $model): void {
            if (! empty($model->id)) {
                LegalDocumentVaultService::purgeEntityDocuments('impact_grants', $model->id);
            }
            if (! empty($model->private_document_path) && Storage::disk('private')->exists($model->private_document_path)) {
                Storage::disk('private')->delete($model->private_document_path);
            }
        });
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function getPicEmailAttribute(): string
    {
        return (string) ($this->official_email ?? '');
    }

    public function getPicPhoneCountryCodeAttribute(): string
    {
        return (string) ($this->phone_country_code ?? '+62');
    }

    public function getPicPhoneAttribute(): string
    {
        return (string) ($this->phone_number ?? '');
    }
}
