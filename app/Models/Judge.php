<?php

namespace App\Models;

use Database\Factories\JudgeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'license_number', 'city', 'years_of_experience'])]
class Judge extends Model
{
    /** @use HasFactory<JudgeFactory> */
    use HasFactory;

    /**
     * Many-to-many: the species this judge is certified to score.
     */
    public function species(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'judge_species');
    }

    /**
     * One-to-many: contests where this judge is the chief judge.
     */
    public function chairedContests(): HasMany
    {
        return $this->hasMany(Contest::class, 'chief_judge_id');
    }
}
