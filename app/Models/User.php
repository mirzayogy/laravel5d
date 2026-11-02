<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi User dengan Orders
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Relasi User dengan Reviews
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Relasi User dengan Cart
    public function cart(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}
