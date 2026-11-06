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

    public const ORDER_TYPES = ['dine_in', 'takeaway'];

    public function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    /**
     * Create an order from a list of menu id => quantity.
     * The unit price of every item is copied from the menu at this moment and the stock is reduced.
     *
     * @param  array<int|string, int|string|null>  $quantities
     * @param  array{order_type?: ?string, table_number?: ?string, payment_method?: ?string}  $checkout
     * @param  array<int|string, ?string>  $itemNotes  menu id => preference (for example "Less sugar")
     */
    public function place(User $user, array $quantities, ?string $note = null, array $checkout = [], array $itemNotes = []): Order
    {
        $quantities = collect($quantities)
            ->map(fn ($quantity) => (int) $quantity)
            ->filter(fn (int $quantity) => $quantity > 0);

        if ($quantities->isEmpty()) {
            throw ValidationException::withMessages(['items' => __('Choose at least one menu.')]);
        }

        $orderType = $checkout['order_type'] ?? 'dine_in';
        $method = $checkout['payment_method'] ?? 'cash';

        return DB::transaction(function () use ($user, $quantities, $note, $checkout, $itemNotes, $orderType, $method) {
            $menus = Menu::whereIn('id', $quantities->keys())->lockForUpdate()->get()->keyBy('id');

            if ($menus->count() !== $quantities->count()) {
                throw ValidationException::withMessages(['items' => __('One of the selected menus does not exist.')]);
            }

            $unavailable = $menus->filter(fn (Menu $menu) => ! $menu->is_available)->pluck('name');

            if ($unavailable->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'items' => __('Not available right now: :menus.', ['menus' => $unavailable->implode(', ')]),
                ]);
            }

            $short = $menus->filter(fn (Menu $menu) => $menu->stock !== null && $quantities[$menu->id] > $menu->stock);

            if ($short->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'items' => __('Not enough stock for: :menus.', ['menus' => $short->map(fn (Menu $menu) => $menu->name.' ('.__('left').' '.$menu->stock.')')->implode(', ')]),
                ]);
            }

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $this->newOrderNumber(),
                'status' => 'pending',
                'order_type' => $orderType,
                'table_number' => $orderType === 'dine_in' ? ($checkout['table_number'] ?? null) : null,
                'payment_method' => $method,
                'payment_status' => 'unpaid',
                'note' => $note,
                'ordered_at' => now(),
            ]);

            $order->update(['payment_reference' => app(PaymentService::class)->referenceFor($order, $method)]);

            foreach ($quantities as $menuId => $quantity) {
                $menu = $menus[$menuId];

                $order->items()->create([
                    'menu_id' => $menuId,
                    'quantity' => $quantity,
                    'unit_price' => $menu->price,
                    'note' => filled($itemNotes[$menuId] ?? null) ? Str::limit($itemNotes[$menuId], 100, '') : null,
                ]);

                if ($menu->stock !== null) {
                    $menu->decrement('stock', $quantity);
                }
            }

            return $order;
        });
    }

    /**
     * Change the status. A cancelled order gives its stock back and refunds a payment.
     */
    public function changeStatus(Order $order, string $to): Order
    {
        DB::transaction(function () use ($order, $to) {
            if ($to === 'cancelled') {
                foreach ($order->items()->with('menu')->get() as $item) {
                    if ($item->menu && $item->menu->stock !== null) {
                        $item->menu->increment('stock', $item->quantity);
                    }
                }

                if ($order->isPaid()) {
                    $order->payment_status = 'refunded';
                }
            }

            $order->status = $to;
            $order->save();
        });

        return $order;
    }

    private function newOrderNumber(): string
    {
        do {
            $number = 'ORD-'.Str::upper(Str::random(8));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
