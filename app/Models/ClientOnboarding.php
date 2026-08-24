<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUlids;

class ClientOnboarding extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'email',
        'company_name',
        'project_needs',
        'budget_range',
        'privacy_consent_agreed',
        'status',
    ];

    protected $casts = [
        'project_needs' => 'array',
        'privacy_consent_agreed' => 'boolean',
    ];
}
