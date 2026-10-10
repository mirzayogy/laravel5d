<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_code',
        'user_id',
        'start_date',
        'end_date',
        'total_amount',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * One-to-Many: Loan belongs to User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * One-to-Many: Loan has many loan details.
     */
    public function details(): HasMany
    {
        return $this->hasMany(LoanDetail::class);
    }

    /**
     * One-to-One: Loan has one return record.
     */
    public function returnRecord(): HasOne
    {
        return $this->hasOne(LoanReturn::class);
    }

    /**
     * Many-to-Many with pivot: Loan has many loan books.
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'loan_details')
            ->withPivot(['quantity', 'fee_per_day', 'subtotal', 'condition_out'])
            ->withTimestamps();
    }
}
