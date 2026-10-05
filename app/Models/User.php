<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'email', 'password', 'role'])]
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
      public function profile(): HasOne
  {
      return $this->hasOne(Profile::class);
  }

  public function addresses(): HasMany
  {
      return $this->hasMany(Address::class);
  }

  // sebagai seller
  public function products(): HasMany
  {
      return $this->hasMany(Product::class, 'seller_id');
  }

  // sebagai buyer
  public function orders(): HasMany
  {
      return $this->hasMany(Order::class);
  }

  public function reviews(): HasMany
  {
      return $this->hasMany(Review::class);
  }

  // Has-Many-Through: seller -> OrderItem lewat Product
  public function soldItems(): HasManyThrough
  {
      return $this->hasManyThrough(OrderItem::class, Product::class, 'seller_id');
  }
}
