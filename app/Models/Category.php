<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon'];

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    // Has-Many-Through: Category -> Review through Recipe
    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(Review::class, Recipe::class);
    }
}
