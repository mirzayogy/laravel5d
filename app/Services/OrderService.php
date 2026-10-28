<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Allowed status changes: from => list of next statuses.
     *
     * @var array<string, list<string>>
     */
    public const TRANSITIONS = [
        'pending' => ['preparing', 'cancelled'],
        'preparing' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    /**
     * Create an order from a list of menu id => quantity.
     * The unit price of every item is copied from the menu at this moment.
     *
     * @param  array<int|string, int|string|null>  $quantities
     */
    public function place(User $user, array $quantities, ?string $note = null): Order
    {
        $quantities = collect($quantities)
            ->map(fn ($quantity) => (int) $quantity)
            ->filter(fn (int $quantity) => $quantity > 0);

        if ($quantities->isEmpty()) {
            throw ValidationException::withMessages(['items' => 'Choose at least one menu.']);
        }

        $menus = Menu::whereIn('id', $quantities->keys())->get()->keyBy('id');

        if ($menus->count() !== $quantities->count()) {
            throw ValidationException::withMessages(['items' => 'One of the selected menus does not exist.']);
        }

        $unavailable = $menus->where('is_available', false)->pluck('name');

        if ($unavailable->isNotEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Not available right now: '.$unavailable->implode(', ').'.',
            ]);
        }

        return DB::transaction(function () use ($user, $quantities, $menus, $note) {
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $this->newOrderNumber(),
                'status' => 'pending',
                'note' => $note,
                'ordered_at' => now(),
            ]);

            foreach ($quantities as $menuId => $quantity) {
                $order->items()->create([
                    'menu_id' => $menuId,
                    'quantity' => $quantity,
                    'unit_price' => $menus[$menuId]->price,
                ]);
            }

            return $order;
        });
    }

    private function newOrderNumber(): string
    {
        do {
            $number = 'ORD-'.Str::upper(Str::random(8));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
