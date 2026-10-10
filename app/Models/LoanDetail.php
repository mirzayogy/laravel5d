<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'book_id',
        'quantity',
        'fee_per_day',
        'subtotal',
        'condition_out',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'fee_per_day' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Detail belongs to Loan.
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Detail belongs to Book.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
