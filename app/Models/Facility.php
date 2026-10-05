<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon'];

    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(Field::class)->withTimestamps();
    }
}