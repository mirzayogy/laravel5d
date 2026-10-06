<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Pivot model of the bird_contest table. One row is one bird entered in one contest.
 */
class ContestEntry extends Pivot
{
    /**
     * Pivot columns exposed on both sides of the many-to-many relationship.
     *
     * @var list<string>
     */
    public const PIVOT_COLUMNS = [
        'entry_number',
        'division',
        'total_score',
        'final_rank',
        'registered_at',
    ];

    protected $table = 'bird_contest';

    public $incrementing = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entry_number' => 'integer',
            'total_score' => 'decimal:2',
            'final_rank' => 'integer',
            'registered_at' => 'datetime',
        ];
    }
}
