<?php

namespace App\Models;

use App\Services\Legal\LegalDocumentVaultService;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadContact extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'name',
        'email',
        'company_name',
        'job_title',
        'phone',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (LeadContact $model): void {
            if (! empty($model->id)) {
                LegalDocumentVaultService::purgeEntityDocuments('leads', $model->id);
                LegalDocumentVaultService::purgeEntityDocuments('impact_grants', $model->id);
            }
        });
    }
}
