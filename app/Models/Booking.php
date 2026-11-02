<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'pet_id',
        'service_id',
        'groomer_id',
        'scheduled_at',
        'ends_at',
        'total_price',
        'status',
        'customer_notes',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'ends_at' => 'datetime',
            'total_price' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI CUSTOMER
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI HEWAN PELIHARAAN
    |--------------------------------------------------------------------------
    */

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI LAYANAN GROOMING
    |--------------------------------------------------------------------------
    */

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI GROOMER
    |--------------------------------------------------------------------------
    */

    public function groomer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'groomer_id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'booking_id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI RIWAYAT STATUS PEMESANAN
    |--------------------------------------------------------------------------
    */

    public function statusHistories(): HasMany
    {
        return $this->hasMany(
            BookingStatusHistory::class,
            'booking_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI CATATAN GROOMING
    |--------------------------------------------------------------------------
    */

    public function groomingRecord(): HasOne
    {
        return $this->hasOne(
            GroomingRecord::class,
            'booking_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER STATUS
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
