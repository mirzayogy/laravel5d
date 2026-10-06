<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['fish_id', 'remind_at', 'days_of_week', 'is_enabled'])]
class RestockReminder extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['days_of_week' => 'array', 'is_enabled' => 'boolean'];
    }

    public function fish(): BelongsTo
    {
        return $this->belongsTo(Fish::class);
    }
}
