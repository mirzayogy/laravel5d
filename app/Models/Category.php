<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
    ];

    /**
     * One-to-Many: Category has many loan books.
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Has-Many-Through: Category has many loan details through loan books.
     */
    public function loanDetails(): HasManyThrough
    {
        return $this->hasManyThrough(LoanDetail::class, Book::class);
    }

    /**
     * Has-Many-Through: Category has many reviews through loan books.
     */
    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(Review::class, Book::class);
    }
}
