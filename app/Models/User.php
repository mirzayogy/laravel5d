<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // One-to-One: User -> Profile
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    // One-to-Many
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function mealPlans(): HasMany
    {
        return $this->hasMany(MealPlan::class);
    }

    // Many-to-Many: recipes saved as favorite (pivot: favorites)
    public function favoriteRecipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'favorites')->withTimestamps();
    }

    // Has-Many-Through: User -> Review through Recipe (reviews received on own recipes)
    public function receivedReviews(): HasManyThrough
    {
        return $this->hasManyThrough(Review::class, Recipe::class);
    }
}