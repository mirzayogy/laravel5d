<?php

namespace App\Models;

use Database\Factories\SpeciesFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['name', 'slug', 'scientific_name', 'description'])]
class Species extends Model
{
    /** @use HasFactory<SpeciesFactory> */
    use HasFactory;

    /**
     * One-to-many: the birds of this species.
     */
    public function birds(): HasMany
    {
        return $this->hasMany(Bird::class);
    }

    /**
     * Many-to-many: the judges certified to score this species.
     */
    public function judges(): BelongsToMany
    {
        return $this->belongsToMany(Judge::class, 'judge_species');
    }

    /**
     * Has-many-through: health records of every bird of this species.
     */
    public function healthRecords(): HasManyThrough
    {
        return $this->hasManyThrough(HealthRecord::class, Bird::class);
    }
}
