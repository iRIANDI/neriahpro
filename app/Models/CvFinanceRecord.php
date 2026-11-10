<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvFinanceRecord extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'cv_finance_records';

    protected $fillable = [
        'trx_code',
        'transaction_date',
        'client_name',
        'client_email',
        'package_name',
        'amount_idr',
        'payment_method',
        'status',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'amount_idr' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

