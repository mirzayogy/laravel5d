<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable([
    'name',
    'domain',
    'objective',
    'description',
    'status',
    'started_on',
    'completed_on',
])]
class Project extends Model
{
    protected function casts(): array
    {
        return [
            'started_on' => 'date',
            'completed_on' => 'date',
        ];
    }

    /**
     * A project belongs to one user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A project can contain multiple experiments.
     */
    public function experiments(): HasMany
    {
        return $this->hasMany(Experiment::class);
    }

    /**
     * A project can access all runs through its experiments.
     */
    public function experimentRuns(): HasManyThrough
    {
        return $this->hasManyThrough(
            ExperimentRun::class,
            Experiment::class
        );
    }
}
