<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'laundry_order_id',
        'customer_id',
        'rating',
        'comment',
    ];

    /**
     * Get the order reviewed (Inverse 1:1 relationship).
     */
    public function laundryOrder(): BelongsTo
    {
        return $this->belongsTo(LaundryOrder::class);
    }

    /**
     * Get the customer user who left the review (Inverse 1:N relationship).
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
