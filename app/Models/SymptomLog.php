<?php

namespace App\Models;

use Database\Factories\SymptomLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'symptom_id', 'log_date', 'severity'])]
class SymptomLog extends Model
{
    /** @use HasFactory<SymptomLogFactory> */
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
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function symptom(): BelongsTo
    {
        return $this->belongsTo(Symptom::class);
    }
}
