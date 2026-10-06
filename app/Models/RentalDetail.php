<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_id',
        'rental_item_id',
        'quantity',
        'price_per_day',
        'subtotal',
        'condition_before',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price_per_day' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Detail belongs to Rental.
     */
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    /**
     * Detail belongs to RentalItem.
     */
    public function rentalItem(): BelongsTo
    {
        return $this->belongsTo(RentalItem::class);
    }
}
