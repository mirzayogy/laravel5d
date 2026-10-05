<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDelay extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'old_deadline',
        'new_deadline',
        'reason',
        'delayed_at',
    ];

    protected function casts(): array
    {
        return [
            'old_deadline' => 'datetime',
            'new_deadline' => 'datetime',
            'delayed_at' => 'datetime',
        ];
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
