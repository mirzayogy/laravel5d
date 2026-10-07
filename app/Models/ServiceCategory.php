<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Get the service items under this category (1:N relationship).
     */
    public function serviceItems(): HasMany
    {
        return $this->hasMany(ServiceItem::class);
    }

    /**
     * Get all order items using services in this category (Has-Many-Through relationship).
     */
    public function orderItems(): HasManyThrough
    {
        return $this->hasManyThrough(OrderItem::class, ServiceItem::class);
    }
}
