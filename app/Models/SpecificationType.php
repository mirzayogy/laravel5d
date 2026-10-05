<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SpecificationType extends Model
{
    protected $fillable = ['category_id', 'name', 'slug', 'unit', 'data_type', 'is_filterable'];

    protected $casts = ['is_filterable' => 'boolean'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_specifications')
            ->withPivot('value');
    }
}