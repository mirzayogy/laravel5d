<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'category_id',
        'title',
        'description',
        'points',
        'flag_hash',
        'difficulty',
        'is_published',
    ];

    protected $hidden = ['flag_hash'];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function hints(): HasMany
    {
        return $this->hasMany(Hint::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function writeups(): HasMany
    {
        return $this->hasMany(Writeup::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'submissions')
            ->withPivot('id', 'user_id', 'is_correct', 'submitted_at');
    }
}
