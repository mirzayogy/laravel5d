<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $table = 'order_items';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    // Detail pesanan dimiliki oleh satu order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Detail pesanan mengacu pada satu produk
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
