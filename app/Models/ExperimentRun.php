<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'run_number',
    'status',
    'firmware_version',
    'firmware_revision',
    'started_at',
    'completed_at',
    'environment_conditions',
    'notes',
])]
class ExperimentRun extends Model
{
    protected function casts(): array
    {
        return [
            'run_number' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'environment_conditions' => 'array',
        ];
    }

    /**
     * A run belongs to one experiment.
     */
    public function experiment(): BelongsTo
    {
        return $this->belongsTo(Experiment::class);
    }

    /**
     * A run can produce multiple measurement records.
     */
    public function measurementRecords(): HasMany
    {
        return $this->hasMany(MeasurementRecord::class);
    }
}
