<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kost extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'type', 'address', 'city', 'description'];

    // Kost dimiliki satu user (pemilik)
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Satu kost punya banyak kamar
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
