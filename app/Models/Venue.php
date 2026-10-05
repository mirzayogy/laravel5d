<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'city', 'address', 'phone', 'description'];

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }

    // Has-Many-Through: Venue -> Field -> Booking
    public function bookings(): HasManyThrough
    {
        return $this->hasManyThrough(Booking::class, Field::class);
    }
}