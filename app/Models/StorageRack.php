<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorageRack extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'section',
        'capacity',
    ];

    /**
     * Get the laundry orders currently stored in this rack (1:N relationship).
     */
    public function laundryOrders(): HasMany
    {
        return $this->hasMany(LaundryOrder::class, 'rack_id');
    }
}
