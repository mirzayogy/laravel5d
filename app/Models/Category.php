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

    public function fishes(): HasMany
    {
        return $this->hasMany(Fish::class);
    }

    public function salesLogs(): HasManyThrough
    {
        return $this->hasManyThrough(SalesLog::class, Fish::class);
    }
}
