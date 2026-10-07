<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LaundryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'customer_id',
        'rack_id',
        'total_weight_kg',
        'total_amount',
        'discount_amount',
        'final_amount',
        'status',
        'pickup_deadline',
        'completed_at',
    ];

    protected $casts = [
        'pickup_deadline' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the customer user who placed this order (Inverse 1:N relationship).
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the storage rack holding this laundry order (Inverse 1:N relationship).
     */
    public function rack(): BelongsTo
    {
        return $this->belongsTo(StorageRack::class, 'rack_id');
    }

    /**
     * Get the order item entries (1:N relationship).
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * The service items included in this order (N:M relationship with pivot data).
     */
    public function serviceItems(): BelongsToMany
    {
        return $this->belongsToMany(ServiceItem::class, 'order_items')
            ->withPivot('qty', 'price_snapshot', 'subtotal', 'notes')
            ->withTimestamps();
    }

    /**
     * Get the payment records for this order (1:N relationship).
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the latest payment for this order (HasOne / LatestOfMany relationship).
     */
    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /**
     * Get the status history logs for this order (1:N relationship).
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class);
    }

    /**
     * Get the customer review for this order (1:1 relationship).
     */
    public function review(): HasOne
    {
        return $this->hasOne(CustomerReview::class);
    }
}
