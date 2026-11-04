<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'bio', 'avatar', 'timezone', 'locale', 'daily_goal', 'email_reminders', 'onboarded_at'])]
class Profile extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['onboarded_at' => 'datetime', 'email_reminders' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
