<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    use HasUlids;

    protected $fillable = [
        'title',
        'document_type',
        'related_id',
        'related_type',
        'file_path',
        'status',
        'e_meterai_status',
        'e_meterai_sn',
        'e_meterai_stamped_at',
        'scope_locked',
        'contract_amount',
        'dp_amount',
        'midtrans_order_id',
        'midtrans_payment_url',
        'content_clauses',
        'signer_name',
        'signer_email',
        'signer_ip_address',
        'signed_at',
        'digital_signature_image',
        'document_hash',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'e_meterai_stamped_at' => 'datetime',
        'scope_locked' => 'boolean',
        'contract_amount' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'content_clauses' => 'array',
    ];

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function isStamped(): bool
    {
        return $this->e_meterai_status === 'stamped';
    }

    public function isScopeLocked(): bool
    {
        return (bool) $this->scope_locked;
    }
}
