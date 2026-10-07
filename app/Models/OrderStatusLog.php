<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'laundry_order_id',
        'staff_id',
        'previous_status',
        'new_status',
        'notes',
    ];

    /**
     * Get the laundry order for this log entry (Inverse 1:N relationship).
     */
    public function laundryOrder(): BelongsTo
    {
        return $this->belongsTo(LaundryOrder::class);
    }

    /**
     * Get the staff user who logged the status update (Inverse 1:N relationship).
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
