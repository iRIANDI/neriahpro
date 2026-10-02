<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class PaymentWebhookLog extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'gateway',
        'event_type',
        'order_id',
        'status',
        'raw_payload',
        'raw_headers',
        'ip_address',
        'error_message',
        'retry_count',
        'processed_at',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'raw_headers' => 'array',
        'processed_at' => 'datetime',
        'retry_count' => 'integer',
    ];

    /**
     * Mark webhook as successfully processed.
     */
    public function markAsProcessed(?string $eventType = null): void
    {
        $this->update([
            'status' => 'processed',
            'event_type' => $eventType ?? $this->event_type,
            'processed_at' => now(),
            'error_message' => null,
        ]);
    }

    /**
     * Mark webhook as failed with error log for DLQ.
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }
}
