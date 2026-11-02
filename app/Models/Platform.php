<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Platform extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    // One-to-Many: satu platform punya banyak station
    public function stations(): HasMany
    {
        return $this->hasMany(Station::class);
    }

    // Has-Many-Through: Platform -> Station -> Booking
    public function bookings(): HasManyThrough
    {
        return $this->hasManyThrough(Booking::class, Station::class);
    }
}
