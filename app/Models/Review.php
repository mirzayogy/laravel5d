<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'rating',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    /**
     * One-to-Many: Review belongs to User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * One-to-Many: Review belongs to Book.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
