<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SparePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_code',
        'name',
        'brand',
        'unit_price',
        'stock_quantity',
        'compatibility',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'stock_quantity' => 'integer',
        ];
    }

    /**
     * Many-to-Many with Pivot: Spare part is supplied in many service bookings.
     */
    public function serviceBookings(): BelongsToMany
    {
        return $this->belongsToMany(ServiceBooking::class, 'booking_spare_part')
            ->withPivot(['quantity', 'unit_price', 'subtotal_price'])
            ->withTimestamps();
    }
}
