<?php

namespace App\Models;

use Database\Factories\HealthRecordFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['bird_id', 'checked_on', 'type', 'weight_grams', 'diagnosis', 'notes'])]
class HealthRecord extends Model
{
    /** @use HasFactory<HealthRecordFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'checked_on' => 'date',
            'weight_grams' => 'integer',
        ];
    }

    /**
     * Inverse one-to-many: the bird this record belongs to.
     */
    public function bird(): BelongsTo
    {
        return $this->belongsTo(Bird::class);
    }
}
