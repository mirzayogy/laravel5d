<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    protected $table = 'orders';

    // Tabel orders tidak menggunakan updated_at
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'order_date',
        'total_amount',
        'status',
        'created_at',
    ];

    // Pesanan dimiliki oleh satu pengguna
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Satu pesanan memiliki banyak detail produk
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Satu pesanan memiliki satu pembayaran
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    // Relasi Many-to-Many dengan Product
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
                    ->withPivot('quantity', 'price', 'subtotal');
    }
}
