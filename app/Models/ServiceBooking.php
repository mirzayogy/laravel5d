<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'vehicle_id',
        'mechanic_id',
        'booking_date',
        'booking_time',
        'status',
        'customer_notes',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
        ];
    }

    /**
     * One-to-Many inverse: Service booking belongs to a vehicle.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * One-to-Many inverse: Service booking is assigned to a mechanic.
     */
    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(Mechanic::class);
    }

    /**
     * One-to-One: Service booking has one initial vehicle inspection report.
     */
    public function inspectionReport(): HasOne
    {
        return $this->hasOne(InspectionReport::class);
    }

    /**
     * One-to-One: Service booking generates one official invoice.
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * One-to-Many: Service booking has reviews from customer.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ServiceReview::class);
    }

    /**
     * Many-to-Many with Pivot: Service booking includes multiple service packages.
     */
    public function servicePackages(): BelongsToMany
    {
        return $this->belongsToMany(ServicePackage::class, 'booking_service_package')
            ->withPivot(['package_price', 'technician_notes'])
            ->withTimestamps();
    }

    /**
     * Many-to-Many with Pivot: Service booking requires multiple spare parts.
     */
    public function spareParts(): BelongsToMany
    {
        return $this->belongsToMany(SparePart::class, 'booking_spare_part')
            ->withPivot(['quantity', 'unit_price', 'subtotal_price'])
            ->withTimestamps();
    }
}
