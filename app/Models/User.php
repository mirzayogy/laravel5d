<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }

    public function habitLogs(): HasManyThrough
    {
        return $this->hasManyThrough(HabitLog::class, Habit::class);
    }

    public function moodEntries(): HasMany
    {
        return $this->hasMany(MoodEntry::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function restDays(): HasMany
    {
        return $this->hasMany(RestDay::class);
    }

    /** The rest day taken in the same week (Monday to Sunday) as the date, if any. */
    public function restDayInWeekOf(CarbonImmutable $date): ?RestDay
    {
        return $this->restDays()
            ->whereBetween('rest_date', [$date->startOfWeek()->toDateString(), $date->endOfWeek()->toDateString()])
            ->first();
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class)->withPivot('earned_at');
    }

    public const ROLE_USER = 'user';

    public const ROLE_ADMIN = 'admin';

    /**
     * `role` is deliberately not mass assignable: it is only set by the
     * admin console, the seeder, or the `habitude:make-admin` command.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Today's date in the user's own timezone (falls back to the app timezone).
     */
    public function today(): CarbonImmutable
    {
        return CarbonImmutable::now($this->profile?->timezone ?? config('app.timezone'))->startOfDay();
    }
}
