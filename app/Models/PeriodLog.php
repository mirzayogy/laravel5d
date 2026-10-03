<?php

namespace App\Models;

use App\Enums\FlowLevel;
use Database\Factories\PeriodLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'cycle_id', 'log_date', 'flow_level'])]
class PeriodLog extends Model
{
    /** @use HasFactory<PeriodLogFactory> */
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
            'flow_level' => FlowLevel::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(Cycle::class);
    }
}
