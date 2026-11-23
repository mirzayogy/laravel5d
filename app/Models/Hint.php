<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Hint extends Model
{
    use HasFactory;

    protected $fillable = ['challenge_id', 'content', 'cost'];

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'hint_unlocks')->withPivot('unlocked_at');
    }
}
