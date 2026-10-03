<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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

    public function cycles(): HasMany
    {
        return $this->hasMany(Cycle::class);
    }

    public function periodLogs(): HasMany
    {
        return $this->hasMany(PeriodLog::class);
    }

    /**
     * Period logs reached through the user's cycles.
     */
    public function periodLogsThroughCycles(): HasManyThrough
    {
        return $this->hasManyThrough(PeriodLog::class, Cycle::class);
    }

    public function symptomLogs(): HasMany
    {
        return $this->hasMany(SymptomLog::class);
    }

    public function symptoms(): BelongsToMany
    {
        return $this->belongsToMany(Symptom::class, 'symptom_logs')
            ->withPivot(['log_date', 'severity'])
            ->withTimestamps();
    }

    public function moodEntries(): HasMany
    {
        return $this->hasMany(MoodEntry::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class);
    }

    public function medications(): HasMany
    {
        return $this->hasMany(Medication::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }
}
