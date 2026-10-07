<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'laundry_order_id',
        'service_item_id',
        'qty',
        'price_snapshot',
        'subtotal',
        'notes',
    ];

    /**
     * Get the order associated with this item (Inverse 1:N relationship).
     */
    public function laundryOrder(): BelongsTo
    {
        return $this->belongsTo(LaundryOrder::class);
    }

    /**
     * Get the service item associated with this order item (Inverse 1:N relationship).
     */
    public function serviceItem(): BelongsTo
    {
        return $this->belongsTo(ServiceItem::class);
    }
}
