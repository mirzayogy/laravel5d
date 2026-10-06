<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['fish_id', 'logged_date', 'value', 'note'])]
class SalesLog extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['logged_date' => 'date'];
    }

    public function fish(): BelongsTo
    {
        return $this->belongsTo(Fish::class);
    }
}
