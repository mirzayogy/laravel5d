<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'invite_code'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function competitions(): BelongsToMany
    {
        return $this->belongsToMany(Competition::class)->withPivot('registered_at');
    }

    public function hints(): BelongsToMany
    {
        return $this->belongsToMany(Hint::class, 'hint_unlocks')->withPivot('unlocked_at');
    }

    public function challenges(): BelongsToMany
    {
        return $this->belongsToMany(Challenge::class, 'submissions')
            ->withPivot('id', 'user_id', 'is_correct', 'submitted_at');
    }
}
