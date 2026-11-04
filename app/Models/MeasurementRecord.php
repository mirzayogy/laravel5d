<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'measurement_parameter_id',
    'sample_no',
    'measured_value',
    'reference_value',
    'measured_at',
    'notes',
])]
class MeasurementRecord extends Model
{
    protected function casts(): array
    {
        return [
            'sample_no' => 'integer',
            'measured_value' => 'decimal:6',
            'reference_value' => 'decimal:6',
            'measured_at' => 'datetime',
        ];
    }

    /**
     * A measurement record belongs to one experiment run.
     */
    public function experimentRun(): BelongsTo
    {
        return $this->belongsTo(ExperimentRun::class);
    }

    /**
     * A measurement record belongs to one measurement parameter.
     */
    public function measurementParameter(): BelongsTo
    {
        return $this->belongsTo(MeasurementParameter::class);
    }
}
