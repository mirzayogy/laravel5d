<?php

namespace App\Models;

use Database\Factories\ContestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'organizer_id',
    'chief_judge_id',
    'name',
    'venue',
    'city',
    'held_on',
    'registration_fee',
    'status',
])]
class Contest extends Model
{
    /** @use HasFactory<ContestFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'held_on' => 'date',
            'registration_fee' => 'integer',
        ];
    }

    /**
     * Inverse one-to-many: the organizer who holds this contest.
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Organizer::class);
    }

    /**
     * Inverse one-to-many: the chief judge of this contest.
     */
    public function chiefJudge(): BelongsTo
    {
        return $this->belongsTo(Judge::class, 'chief_judge_id');
    }

    /**
     * Many-to-many with pivot data: the birds entered in this contest.
     * The pivot is exposed as $bird->entry.
     */
    public function birds(): BelongsToMany
    {
        return $this->belongsToMany(Bird::class, 'bird_contest')
            ->using(ContestEntry::class)
            ->as('entry')
            ->withPivot(ContestEntry::PIVOT_COLUMNS);
    }
}
