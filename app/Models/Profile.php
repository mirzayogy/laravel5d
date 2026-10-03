<?php

namespace App\Models;

use App\Enums\CycleGoal;
use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'birth_date', 'goal', 'avg_cycle_length', 'avg_period_length', 'avatar', 'timezone'])]
class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'goal' => CycleGoal::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
