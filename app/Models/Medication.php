<?php

namespace App\Models;

use App\Enums\MedicationType;
use Database\Factories\MedicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'type', 'dosage', 'is_active'])]
class Medication extends Model
{
    /** @use HasFactory<MedicationFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => MedicationType::class,
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicationLogs(): HasMany
    {
        return $this->hasMany(MedicationLog::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }
}
