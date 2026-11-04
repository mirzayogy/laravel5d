<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'summary',
    'conclusion',
    'limitations',
    'recommendations',
    'generated_at',
])]
class ExperimentReport extends Model
{
    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }

    /**
     * A report belongs to one experiment.
     */
    public function experiment(): BelongsTo
    {
        return $this->belongsTo(Experiment::class);
    }
}
