<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'order_number', 'status', 'total_amount', 'ordered_at'];

    protected $casts = [
        'ordered_at' => 'datetime',
    ];

    /** Pesanan dimiliki oleh satu user (pembeli). */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Satu pesanan punya banyak baris item. */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Many-to-many: pesanan berisi banyak produk. */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot(['quantity', 'unit_price', 'subtotal'])
            ->withTimestamps();
    }
}
