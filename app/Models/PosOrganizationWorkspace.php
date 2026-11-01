<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosOrganizationWorkspace extends Model
{
    use HasUlids;

    protected $table = 'pos_organization_workspaces';

    protected $fillable = [
        'owner_user_id',
        'name',
        'slug',
        'plan_tier',
        'seat_limit',
        'white_label_config_json',
    ];

    protected function casts(): array
    {
        return [
            'seat_limit' => 'integer',
            'white_label_config_json' => 'array',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(PosOrganizationMember::class, 'workspace_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(PosProject::class, 'organization_workspace_id');
    }
}
