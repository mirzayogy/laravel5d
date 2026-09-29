<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['name', 'slug', 'icon', 'color'])]
class Category extends Model
{
    use HasFactory;

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }

    public function habitLogs(): HasManyThrough
    {
        return $this->hasManyThrough(HabitLog::class, Habit::class);
    }
}
