<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'payment_method',
        'amount',
        'payment_status',
        'payment_date',
        'created_at',
    ];

    // Pembayaran dimiliki oleh satu pesanan
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
