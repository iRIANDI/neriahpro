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
        'signer_name',
        'signer_email',
        'signer_ip_address',
        'signed_at',
        'digital_signature_image',
        'document_hash',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
