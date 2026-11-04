<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'category_id', 'name', 'description', 'target_count', 'unit', 'weekly_goal', 'is_active'])]
class Habit extends Model
{
    use HasFactory, MassPrunable, SoftDeletes;

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /** A weekly habit ("3 days a week"); every other habit is daily. */
    public function isWeekly(): bool
    {
        return $this->weekly_goal !== null;
    }

    /**
     * "1 session" / "8 glasses": the unit follows the number.
     */
    protected function targetLabel(): Attribute
    {
        return Attribute::get(fn () => $this->target_count.' '.__($this->target_count === 1 ? Str::singular($this->unit) : $this->unit));
    }

    /**
     * Habits deleted more than 30 days ago are removed for good (`model:prune`, scheduled daily).
     */
    public function prunable(): Builder
    {
        return static::onlyTrashed()->where('deleted_at', '<=', now()->subDays(30));
    }
}
