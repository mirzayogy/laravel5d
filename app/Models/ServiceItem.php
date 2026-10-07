<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_category_id',
        'name',
        'price_per_unit',
        'unit_type',
        'estimated_hours',
    ];

    /**
     * Get the service category of this item (Inverse 1:N relationship).
     */
    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    /**
     * Get the order items for this service (1:N relationship).
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * The laundry orders that include this service (N:M relationship with pivot data).
     */
    public function laundryOrders(): BelongsToMany
    {
        return $this->belongsToMany(LaundryOrder::class, 'order_items')
            ->withPivot('qty', 'price_snapshot', 'subtotal', 'notes')
            ->withTimestamps();
    }
}
