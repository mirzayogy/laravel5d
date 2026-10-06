<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plate_number',
        'brand',
        'model',
        'production_year',
        'color',
        'transmission_type',
    ];

    protected function casts(): array
    {
        return [
            'production_year' => 'integer',
        ];
    }

    /**
     * One-to-Many inverse: Vehicle belongs to a user (owner).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * One-to-Many: Vehicle has many service bookings.
     */
    public function serviceBookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class);
    }
}
