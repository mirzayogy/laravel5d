<?php

namespace App\Models;

use Database\Factories\SymptomFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'category', 'icon'])]
class Symptom extends Model
{
    /** @use HasFactory<SymptomFactory> */
    use HasFactory;

    public function symptomLogs(): HasMany
    {
        return $this->hasMany(SymptomLog::class);
    }
}
