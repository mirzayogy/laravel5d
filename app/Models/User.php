<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough; // <-- TAMBAHAN 1 (import)
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
     * Satu user memiliki banyak penyewaan (1:N).
     */
    public function penyewaans(): HasMany
    {
        return $this->hasMany(Penyewaan::class);
    }

    /**
     * User -> DetailPenyewaan melalui Penyewaan (has-many-through).
     */
    public function detailPenyewaans(): HasManyThrough // <-- TAMBAHAN 2 (method)
    {
        return $this->hasManyThrough(DetailPenyewaan::class, Penyewaan::class);
    }
}