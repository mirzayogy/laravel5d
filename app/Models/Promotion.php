<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'promo_code',
        'title',
        'discount_percentage',
        'max_discount_amount',
        'min_transaction_amount',
        'valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_percentage' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'min_transaction_amount' => 'decimal:2',
            'valid_until' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Many-to-Many with Pivot: Promotion can be used by many users.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'promotion_user')
            ->withPivot(['discount_applied', 'used_at'])
            ->withTimestamps();
    }
}
