<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['kost_id', 'room_number', 'floor', 'size_m2', 'price_per_day', 'price_per_month', 'status'];
    
    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class);
    }

    // Many-to-many dengan fasilitas lewat tabel pivot facility_room
    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class)->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
