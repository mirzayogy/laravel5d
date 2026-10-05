<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Product extends Model
{
       use HasFactory;
    protected $fillable = [
        'seller_id', 'category_id', 'brand_id', 'name', 'slug', 'sku',
        'description', 'price', 'stock', 'condition', 'warranty_months', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Many-to-Many: Product <-> Tag
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    // Many-to-Many + pivot data: Product <-> SpecificationType
    public function specificationTypes(): BelongsToMany
    {
        return $this->belongsToMany(SpecificationType::class, 'product_specifications')
            ->withPivot('value');
    }

    // Many-to-Many + pivot data: Product <-> Order
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('quantity', 'unit_price', 'subtotal');
    }

    // Self-referencing Many-to-Many: Product <-> Product
    public function compatibleProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'compatibilities', 'product_id', 'compatible_product_id')
            ->withPivot('compatibility_type_id', 'status', 'note')
            ->withTimestamps();
    }
}