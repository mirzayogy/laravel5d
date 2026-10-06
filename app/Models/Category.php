<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
    ];

    /**
     * One-to-Many: Category has many rental items.
     */
    public function rentalItems(): HasMany
    {
        return $this->hasMany(RentalItem::class);
    }

    /**
     * Has-Many-Through: Category has many rental details through rental items.
     */
    public function rentalDetails(): HasManyThrough
    {
        return $this->hasManyThrough(RentalDetail::class, RentalItem::class);
    }

    /**
     * Has-Many-Through: Category has many reviews through rental items.
     */
    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(Review::class, RentalItem::class);
    }
}
