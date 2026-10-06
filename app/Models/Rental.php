<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_code',
        'user_id',
        'start_date',
        'end_date',
        'total_amount',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * One-to-Many: Rental belongs to User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * One-to-Many: Rental has many rental details.
     */
    public function details(): HasMany
    {
        return $this->hasMany(RentalDetail::class);
    }

    /**
     * One-to-One: Rental has one return record.
     */
    public function returnRecord(): HasOne
    {
        return $this->hasOne(RentalReturn::class);
    }

    /**
     * Many-to-Many with pivot: Rental has many rental items.
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(RentalItem::class, 'rental_details')
            ->withPivot(['quantity', 'price_per_day', 'subtotal', 'condition_before'])
            ->withTimestamps();
    }
}
