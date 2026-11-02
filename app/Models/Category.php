<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';

    // Mengikuti ERD yang tidak memiliki timestamps
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
    ];

    // Satu kategori memiliki banyak produk
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
