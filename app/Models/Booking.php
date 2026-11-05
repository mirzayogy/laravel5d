<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
    'room_id', 'tenant_id', 'previous_booking_id', 'code', 'rental_type',
    'start_date', 'end_date', 'duration', 'price_per_unit', 'deposit_amount',
    'total_price', 'expires_at', 'checked_in_at', 'checked_out_at', 'status',
    ];

    protected $casts = [
    'start_date' => 'date',
    'end_date' => 'date',
    'expires_at' => 'datetime',
    'checked_in_at' => 'datetime',
    'checked_out_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
