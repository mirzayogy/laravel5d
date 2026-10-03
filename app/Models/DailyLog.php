<?php

namespace App\Models;

use Database\Factories\DailyLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'log_date', 'basal_temp', 'discharge', 'sex_activity', 'ovulation_test', 'pregnancy_test', 'weight', 'water_ml', 'sleep_hours'])]
class DailyLog extends Model
{
    /** @use HasFactory<DailyLogFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'log_date' => 'date',
            'basal_temp' => 'decimal:2',
            'weight' => 'decimal:2',
            'sleep_hours' => 'decimal:1',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
