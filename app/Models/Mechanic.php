<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mechanic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'specialization',
        'experience_years',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'experience_years' => 'integer',
        ];
    }

    /**
     * One-to-Many: Mechanic handles many service bookings.
     */
    public function serviceBookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class);
    }
}
