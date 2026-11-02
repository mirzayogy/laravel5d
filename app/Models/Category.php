<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    /** Resep dalam kategori ini (one-to-many). */
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }
}
