<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $table = 'products';

    // Tabel products tidak menggunakan updated_at
    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'created_at',
    ];

    // Produk dimiliki oleh satu kategori
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Produk memiliki banyak order item
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Produk memiliki banyak review
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Produk dapat masuk ke banyak keranjang
    public function cart(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    // Relasi Many-to-Many dengan Order
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')
                    ->withPivot('quantity', 'price', 'subtotal');
    }
}
