<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Recipe extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'instructions',
        'cooking_time',
        'servings',
        'difficulty',
    ];

    /** Penulis resep (many-to-one). */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Kategori resep (many-to-one). */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Bahan-bahan resep beserta jumlahnya (many-to-many). */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_recipe')
            ->withPivot('quantity');
    }

    /** User yang memfavoritkan resep ini (many-to-many). */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'recipe_user')->withTimestamps();
    }
}
