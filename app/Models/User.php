<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
     * One-to-one: the contact and club details of a bird owner.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * One-to-many: the birds owned by this user.
     */
    public function birds(): HasMany
    {
        return $this->hasMany(Bird::class);
    }

    /**
     * Has-many-through: health records of every bird owned by this user.
     */
    public function healthRecords(): HasManyThrough
    {
        return $this->hasManyThrough(HealthRecord::class, Bird::class);
    }

    /**
     * Has-many-through: training logs of every bird owned by this user.
     */
    public function trainingLogs(): HasManyThrough
    {
        return $this->hasManyThrough(TrainingLog::class, Bird::class);
    }
}
