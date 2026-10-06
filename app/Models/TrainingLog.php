<?php

namespace App\Models;

use Database\Factories\TrainingLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['bird_id', 'trained_on', 'focus', 'duration_minutes', 'notes'])]
class TrainingLog extends Model
{
    /** @use HasFactory<TrainingLogFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'trained_on' => 'date',
            'duration_minutes' => 'integer',
        ];
    }

    /**
     * Inverse one-to-many: the bird this session belongs to.
     */
    public function bird(): BelongsTo
    {
        return $this->belongsTo(Bird::class);
    }
}
