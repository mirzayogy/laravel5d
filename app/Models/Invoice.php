<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'service_booking_id',
        'total_services_amount',
        'total_parts_amount',
        'discount_amount',
        'grand_total',
        'payment_method',
        'payment_status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'total_services_amount' => 'decimal:2',
            'total_parts_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * One-to-One inverse: Invoice belongs to a service booking.
     */
    public function serviceBooking(): BelongsTo
    {
        return $this->belongsTo(ServiceBooking::class);
    }
}
