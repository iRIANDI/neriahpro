<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosOrganizationMember extends Model
{
    use HasUlids;

    protected $table = 'pos_organization_members';

    protected $fillable = [
        'workspace_id',
        'user_id',
        'invited_email',
        'invited_phone_country_code',
        'invited_phone',
        'workspace_role',
        'status',
        'invite_token_hash',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(PosOrganizationWorkspace::class, 'workspace_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
