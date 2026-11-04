<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'title',
    'objective',
    'hypothesis',
    'procedure',
    'status',
    'notes',
])]
class Experiment extends Model
{
    /**
     * An experiment belongs to one project.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * An experiment can use multiple components.
     */
    public function components(): BelongsToMany
    {
        return $this->belongsToMany(
            Component::class,
            'experiment_components'
        )
            ->using(ExperimentComponent::class)
            ->withPivot([
                'id',
                'quantity_used',
                'role',
                'connection_details',
                'notes',
            ])
            ->withTimestamps();
    }

    /**
     * An experiment can have multiple runs.
     */
    public function runs(): HasMany
    {
        return $this->hasMany(ExperimentRun::class);
    }

    /**
     * An experiment can define multiple measurement parameters.
     */
    public function measurementParameters(): HasMany
    {
        return $this->hasMany(MeasurementParameter::class);
    }

    /**
     * An experiment can have one final report.
     */
    public function report(): HasOne
    {
        return $this->hasOne(ExperimentReport::class);
    }
}
