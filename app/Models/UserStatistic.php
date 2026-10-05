<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'completed_tasks',
        'delayed_tasks',
        'focus_minutes',
        'productivity_score',
        'current_streak',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'productivity_score' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
