<?php

namespace App\Models;

use Database\Factories\BirdFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'species_id',
    'sire_id',
    'dam_id',
    'name',
    'ring_number',
    'sex',
    'hatched_on',
    'color',
    'is_active',
])]
class Bird extends Model
{
    /** @use HasFactory<BirdFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hatched_on' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Inverse one-to-many: the user who owns this bird.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Inverse one-to-many: the species of this bird.
     */
    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    /**
     * Self-referencing: the male parent of this bird.
     */
    public function sire(): BelongsTo
    {
        return $this->belongsTo(self::class, 'sire_id');
    }

    /**
     * Self-referencing: the female parent of this bird.
     */
    public function dam(): BelongsTo
    {
        return $this->belongsTo(self::class, 'dam_id');
    }

    /**
     * Self-referencing one-to-many: offspring when this bird is the sire.
     */
    public function offspringAsSire(): HasMany
    {
        return $this->hasMany(self::class, 'sire_id');
    }

    /**
     * Self-referencing one-to-many: offspring when this bird is the dam.
     */
    public function offspringAsDam(): HasMany
    {
        return $this->hasMany(self::class, 'dam_id');
    }

    /**
     * One-to-many: health records of this bird.
     */
    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }

    /**
     * One-to-many: training sessions of this bird.
     */
    public function trainingLogs(): HasMany
    {
        return $this->hasMany(TrainingLog::class);
    }

    /**
     * Many-to-many with pivot data: the contests this bird has entered.
     * The pivot is exposed as $contest->entry.
     */
    public function contests(): BelongsToMany
    {
        return $this->belongsToMany(Contest::class, 'bird_contest')
            ->using(ContestEntry::class)
            ->as('entry')
            ->withPivot(ContestEntry::PIVOT_COLUMNS);
    }
}
