<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * One-to-Many: Service category has many service packages.
     */
    public function servicePackages(): HasMany
    {
        return $this->hasMany(ServicePackage::class);
    }
}
