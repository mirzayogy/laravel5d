<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RentalItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'brand',
        'daily_rate',
        'stock_quantity',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'daily_rate' => 'decimal:2',
            'stock_quantity' => 'integer',
        ];
    }

    /**
     * One-to-Many: Rental item belongs to Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * One-to-One: Rental item has one specification.
     */
    public function specification(): HasOne
    {
        return $this->hasOne(ItemSpecification::class);
    }

    /**
     * One-to-Many: Rental item has many reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * One-to-Many: Rental item has many rental details.
     */
    public function rentalDetails(): HasMany
    {
        return $this->hasMany(RentalDetail::class);
    }

    /**
     * Many-to-Many: Rental item belongs to many tags.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'item_tag')
            ->withTimestamps();
    }

    /**
     * Many-to-Many with pivot: Rental item belongs to many rentals.
     */
    public function rentals(): BelongsToMany
    {
        return $this->belongsToMany(Rental::class, 'rental_details')
            ->withPivot(['quantity', 'price_per_day', 'subtotal', 'condition_before'])
            ->withTimestamps();
    }
}
