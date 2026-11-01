<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasEncryptedPiiWithBlindIndex;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosPrivacyErasureRequest extends Model
{
    use HasEncryptedPiiWithBlindIndex, HasUlids;

    protected $table = 'pos_privacy_erasure_requests';

    protected $fillable = [
        'user_id',
        'requester_email',
        'email_blind_index',
        'requester_phone_e164',
        'phone_blind_index',
        'request_type',
        'status',
        'redaction_token',
        'redacted_fields_summary_json',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'requester_email' => 'encrypted',
            'requester_phone_e164' => 'encrypted',
            'redacted_fields_summary_json' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function blindIndexMap(): array
    {
        return [
            'requester_email' => 'email_blind_index',
            'requester_phone_e164' => 'phone_blind_index',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
