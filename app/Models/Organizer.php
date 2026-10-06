<?php

namespace App\Models;

use Database\Factories\OrganizerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'city', 'phone', 'email'])]
class Organizer extends Model
{
    /** @use HasFactory<OrganizerFactory> */
    use HasFactory;

    /**
     * One-to-many: the contests held by this organizer.
     */
    public function contests(): HasMany
    {
        return $this->hasMany(Contest::class);
    }
}
