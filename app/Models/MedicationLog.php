<?php

namespace App\Models;

use Database\Factories\MedicationLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['medication_id', 'taken_date', 'taken_time'])]
class MedicationLog extends Model
{
    /** @use HasFactory<MedicationLogFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'taken_date' => 'date',
        ];
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }
}
