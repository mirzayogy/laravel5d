<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ExperimentComponent extends Pivot
{
    protected $table = 'experiment_components';

    public $incrementing = true;

    protected function casts(): array
    {
        return [
            'quantity_used' => 'integer',
            'connection_details' => 'array',
        ];
    }
}
