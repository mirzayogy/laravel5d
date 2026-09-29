<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['habit_id', 'remind_at', 'days_of_week', 'is_enabled'])]
class Reminder extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['days_of_week' => 'array', 'is_enabled' => 'boolean'];
    }

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
