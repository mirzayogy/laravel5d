<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone_number',
        'address',
        'identity_number',
        'avatar',
        'driver_license_type',
    ];

    /**
     * One-to-One: Profile belongs to User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
