<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user profile associated with the user (1:1 relationship).
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get laundry orders placed by this customer user (1:N relationship).
     */
    public function laundryOrders(): HasMany
    {
        return $this->hasMany(LaundryOrder::class, 'customer_id');
    }

    /**
     * Get payments made by the customer through laundry orders (Has-Many-Through relationship).
     */
    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, LaundryOrder::class, 'customer_id', 'laundry_order_id');
    }

    /**
     * Get order status logs updated by this staff user (1:N relationship).
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class, 'staff_id');
    }

    /**
     * Get customer reviews submitted by this customer (1:N relationship).
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(CustomerReview::class, 'customer_id');
    }
}
