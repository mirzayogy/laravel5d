<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'brand',
        'borrow_fee',
        'stock_quantity',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'borrow_fee' => 'decimal:2',
            'stock_quantity' => 'integer',
        ];
    }

    /**
     * One-to-Many: Loan item belongs to Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * One-to-One: Loan item has one specification.
     */
    public function specification(): HasOne
    {
        return $this->hasOne(BookDetail::class);
    }

    /**
     * One-to-Many: Loan item has many reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * One-to-Many: Loan item has many loan details.
     */
    public function loanDetails(): HasMany
    {
        return $this->hasMany(LoanDetail::class);
    }

    /**
     * Many-to-Many: Loan item belongs to many tags.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'book_tag')
            ->withTimestamps();
    }

    /**
     * Many-to-Many with pivot: Loan item belongs to many loans.
     */
    public function loans(): BelongsToMany
    {
        return $this->belongsToMany(Loan::class, 'loan_details')
            ->withPivot(['quantity', 'fee_per_day', 'subtotal', 'condition_out'])
            ->withTimestamps();
    }
}
