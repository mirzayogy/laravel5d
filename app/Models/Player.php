<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Player extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'name', 'jersey_number', 'position'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}