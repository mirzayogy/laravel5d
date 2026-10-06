<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'customer_review_id', 'entry_date', 'title', 'content'])]
class SalesReport extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['entry_date' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customerReview(): BelongsTo
    {
        return $this->belongsTo(CustomerReview::class);
    }
}
