<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompatibilityType extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function rules(): HasMany
    {
        return $this->hasMany(CompatibilityRule::class);
    }
}