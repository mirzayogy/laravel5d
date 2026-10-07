<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nik',
        'emergency_contact',
        'notes',
        'avatar_url',
    ];

    /**
     * Get the user that owns the profile (Inverse 1:1 relationship).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
