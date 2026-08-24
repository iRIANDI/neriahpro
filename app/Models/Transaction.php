<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'product_id',
        'midtrans_transaction_id',
        'midtrans_order_id',
        'status',
        'total_idr',
        'original_currency',
        'original_amount',
        'exchange_rate',
        'customer_details',
    ];

    protected $casts = [
        'total_idr' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'customer_details' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
