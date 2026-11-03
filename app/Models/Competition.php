<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'start_at',
        'end_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    /**
     * Where the competition is in its life: draft, upcoming, running, or finished.
     *
     * `status` is the admin's choice: `draft` hides the competition, `finished` ends it
     * early, and `running` publishes it so that the start and end times decide the phase.
     */
    protected function phase(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->status === 'draft') {
                return 'draft';
            }

            if ($this->status === 'finished' || now()->gte($this->end_at)) {
                return 'finished';
            }

            return now()->lt($this->start_at) ? 'upcoming' : 'running';
        });
    }

    public function isDraft(): bool
    {
        return $this->phase === 'draft';
    }

    public function isUpcoming(): bool
    {
        return $this->phase === 'upcoming';
    }

    public function isRunning(): bool
    {
        return $this->phase === 'running';
    }

    public function isFinished(): bool
    {
        return $this->phase === 'finished';
    }

    public function challenges(): HasMany
    {
        return $this->hasMany(Challenge::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withPivot('registered_at');
    }

    public function submissions(): HasManyThrough
    {
        return $this->hasManyThrough(Submission::class, Challenge::class);
    }
}
