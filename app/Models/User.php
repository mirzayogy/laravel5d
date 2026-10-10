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

    /**
     * One-to-One: User has one profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * One-to-Many: User has many loans.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * One-to-Many: User has many reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Many-to-Many with pivot data: User has many promotions.
     */
    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class)
            ->withPivot(['used_at', 'discount_amount'])
            ->withTimestamps();
    }

    /**
     * Has-Many-Through: User has many loan details through loans.
     */
    public function loanDetails(): HasManyThrough
    {
        return $this->hasManyThrough(LoanDetail::class, Loan::class);
    }
}
