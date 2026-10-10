<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_percentage',
        'valid_until',
    ];

    protected function casts(): array
    {
        return [
            'discount_percentage' => 'decimal:2',
            'valid_until' => 'date',
        ];
    }

    /**
     * Many-to-Many with pivot data: Promotion has many users.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['used_at', 'discount_amount'])
            ->withTimestamps();
    }
}
