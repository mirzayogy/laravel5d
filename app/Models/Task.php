<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'deadline',
        'difficulty',
        'estimated_minutes',
        'priority_score',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'completed_at' => 'datetime',
            'priority_score' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subtasks()
    {
        return $this->hasMany(Subtask::class);
    }

    public function taskDelays()
    {
        return $this->hasMany(TaskDelay::class);
    }

    public function rescuePlans()
    {
        return $this->hasMany(RescuePlan::class);
    }

    public function focusSessions()
    {
        return $this->hasMany(FocusSession::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
