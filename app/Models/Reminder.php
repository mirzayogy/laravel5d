<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['habit_id', 'remind_at', 'days_of_week', 'is_enabled', 'last_sent_on'])]
class Reminder extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['days_of_week' => 'array', 'is_enabled' => 'boolean', 'last_sent_on' => 'date:Y-m-d'];
    }

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
