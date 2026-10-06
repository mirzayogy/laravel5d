<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_booking_id',
        'rating',
        'review_text',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    /**
     * One-to-Many inverse: Review belongs to a service booking.
     */
    public function serviceBooking(): BelongsTo
    {
        return $this->belongsTo(ServiceBooking::class);
    }
}
