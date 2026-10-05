<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function specificationTypes(): HasMany
    {
        return $this->hasMany(SpecificationType::class);
    }

    // Has-Many-Through: Category -> OrderItem lewat Product
    public function orderItems(): HasManyThrough
    {
        return $this->hasManyThrough(OrderItem::class, Product::class);
    }

    // Has-Many-Through: Category -> Review lewat Product
    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(Review::class, Product::class);
    }
}