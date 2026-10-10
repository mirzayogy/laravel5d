<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'actual_return_date',
        'late_days',
        'penalty_fee',
        'condition_after',
        'return_status',
    ];

    protected function casts(): array
    {
        return [
            'actual_return_date' => 'date',
            'late_days' => 'integer',
            'penalty_fee' => 'decimal:2',
        ];
    }

    /**
     * One-to-One: LoanReturn belongs to Loan.
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
