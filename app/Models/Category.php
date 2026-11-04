<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['name', 'slug', 'icon', 'color'])]
class Category extends Model
{
    use HasFactory;

    /** The seeded categories have Indonesian names; ones an admin adds keep what was typed. */
    public function displayName(): Attribute
    {
        return Attribute::get(fn () => __($this->name));
    }

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }

    public function habitLogs(): HasManyThrough
    {
        return $this->hasManyThrough(HabitLog::class, Habit::class);
    }
}
