<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_booking_id',
        'odometer_km',
        'fuel_level',
        'brake_condition',
        'tire_condition',
        'battery_condition',
        'inspector_notes',
    ];

    protected function casts(): array
    {
        return [
            'odometer_km' => 'integer',
        ];
    }

    /**
     * One-to-One inverse: Inspection report belongs to a service booking.
     */
    public function serviceBooking(): BelongsTo
    {
        return $this->belongsTo(ServiceBooking::class);
    }
}
