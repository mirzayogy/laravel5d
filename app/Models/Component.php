<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'category',
    'manufacturer',
    'model',
    'interface',
    'specifications',
    'quantity_owned',
    'storage_location',
    'notes',
])]
class Component extends Model
{
    protected function casts(): array
    {
        return [
            'specifications' => 'array',
            'quantity_owned' => 'integer',
        ];
    }

    /**
     * A component belongs to one user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A component can be reused in multiple experiments.
     */
    public function experiments(): BelongsToMany
    {
        return $this->belongsToMany(
            Experiment::class,
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
     * A component can be the source of multiple measurement parameters.
     */
    public function measurementParameters(): HasMany
    {
        return $this->hasMany(
            MeasurementParameter::class,
            'source_component_id'
        );
    }
}
