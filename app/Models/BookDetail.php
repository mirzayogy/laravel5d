<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'serial_or_plate_number',
        'year_of_manufacture',
        'color',
        'condition_notes',
        'technical_specs',
    ];

    protected function casts(): array
    {
        return [
            'year_of_manufacture' => 'integer',
            'technical_specs' => 'array',
        ];
    }

    /**
     * One-to-One: Specification belongs to Book.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
