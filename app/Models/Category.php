<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function orderItems(): HasManyThrough
    {
        return $this->hasManyThrough(OrderItem::class, Menu::class);
    }

    /**
     * Emoji used on the menu page, chosen from the category name.
     */
    public function icon(): string
    {
        $name = mb_strtolower($this->name);

        return match (true) {
            str_contains($name, 'non') => '🥤',
            str_contains($name, 'kopi'), str_contains($name, 'coffee') => '☕',
            str_contains($name, 'teh'), str_contains($name, 'tea') => '🍵',
            str_contains($name, 'camilan'), str_contains($name, 'snack') => '🍟',
            str_contains($name, 'makanan'), str_contains($name, 'main') => '🍛',
            str_contains($name, 'dessert'), str_contains($name, 'es ') => '🍨',
            default => '🍽️',
        };
    }

    /**
     * Which preference choices the cart offers: drink (sugar and ice), food (spice) or none.
     */
    public function optionSet(): string
    {
        $name = mb_strtolower($this->name);

        return match (true) {
            str_contains($name, 'kopi'), str_contains($name, 'coffee'),
            str_contains($name, 'teh'), str_contains($name, 'tea') => 'drink',
            str_contains($name, 'camilan'), str_contains($name, 'snack'),
            str_contains($name, 'makanan'), str_contains($name, 'main') => 'food',
            default => 'none',
        };
    }
}
