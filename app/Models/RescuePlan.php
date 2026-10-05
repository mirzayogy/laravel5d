<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RescuePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'remaining_minutes',
        'days_remaining',
        'daily_target_minutes',
        'recommendation',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
