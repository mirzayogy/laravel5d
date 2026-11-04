<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'sku', 'description', 'price', 'stock'];

    /** Produk dimiliki oleh satu kategori. */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Baris detail pesanan yang memuat produk ini. */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Many-to-many: produk muncul di banyak pesanan. */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot(['quantity', 'unit_price', 'subtotal'])
            ->withTimestamps();
    }
}
