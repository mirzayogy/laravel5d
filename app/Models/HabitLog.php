<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['habit_id', 'logged_date', 'value', 'note'])]
class HabitLog extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['logged_date' => 'date'];
    }

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
