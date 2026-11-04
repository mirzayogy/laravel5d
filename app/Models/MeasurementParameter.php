<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'source_component_id',
    'name',
    'symbol',
    'unit',
    'description',
])]
class MeasurementParameter extends Model
{
    /**
     * A parameter belongs to one experiment.
     */
    public function experiment(): BelongsTo
    {
        return $this->belongsTo(Experiment::class);
    }

    /**
     * A parameter may come from a specific component.
     */
    public function sourceComponent(): BelongsTo
    {
        return $this->belongsTo(Component::class, 'source_component_id');
    }

    /**
     * A parameter can have multiple measurement records.
     */
    public function measurementRecords(): HasMany
    {
        return $this->hasMany(MeasurementRecord::class);
    }
}
