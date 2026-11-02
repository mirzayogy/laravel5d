<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'description', 'servings',
        'prep_minutes', 'cook_minutes', 'calories_per_serving', 'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RecipeStep::class)->orderBy('step_number');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Many-to-Many with pivot data: Recipe <-> Ingredient
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredient')
            ->withPivot('quantity', 'unit')
            ->withTimestamps();
    }

    // Many-to-Many: users who saved this recipe as favorite
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    // Many-to-Many with pivot data: Recipe <-> MealPlan
    public function mealPlans(): BelongsToMany
    {
        return $this->belongsToMany(MealPlan::class, 'meal_plan_items')
            ->withPivot('planned_date', 'meal_type', 'servings')
            ->withTimestamps();
    }
}
