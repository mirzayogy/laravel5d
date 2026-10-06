<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServicePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_category_id',
        'name',
        'description',
        'base_price',
        'estimated_duration_minutes',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'estimated_duration_minutes' => 'integer',
        ];
    }

    /**
     * One-to-Many inverse: Service package belongs to a service category.
     */
    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    /**
     * Many-to-Many with Pivot: Service package is included in many service bookings.
     */
    public function serviceBookings(): BelongsToMany
    {
        return $this->belongsToMany(ServiceBooking::class, 'booking_service_package')
            ->withPivot(['package_price', 'technician_notes'])
            ->withTimestamps();
    }
}
