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

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'note',
        'order_type',
        'table_number',
        'payment_method',
        'payment_status',
        'payment_reference',
        'paid_at',
        'ordered_at',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'order_items')
            ->withPivot('id', 'quantity', 'unit_price');
    }

    public function total(): int
    {
        return $this->items->sum(fn (OrderItem $item) => $item->quantity * $item->unit_price);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
