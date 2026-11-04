<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'description', 'criteria'])]
class Achievement extends Model
{
    use HasFactory;

    public function displayName(): Attribute
    {
        return Attribute::get(fn () => __($this->name));
    }

    public function displayDescription(): Attribute
    {
        return Attribute::get(fn () => $this->description ? __($this->description) : null);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('earned_at');
    }

    /**
     * Criteria is stored as "type:threshold", for example "streak:7".
     *
     * @return array{0: string, 1: int}
     */
    public function parsedCriteria(): array
    {
        [$type, $threshold] = array_pad(explode(':', $this->criteria, 2), 2, '0');

        return [$type, (int) $threshold];
    }
}
