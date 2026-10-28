<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders) {}

    public function index(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->with('items')
            ->latest('ordered_at')
            ->latest('id')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        $user = $request->user();
        $isStaff = in_array($user->role, ['cashier', 'admin'], true);

        abort_unless($order->user_id === $user->id || $isStaff, 403);

        $order->load(['items.menu', 'user']);

        $nextStatuses = OrderService::TRANSITIONS[$order->status];

        return view('orders.show', compact('order', 'isStaff', 'nextStatuses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0', 'max:20'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $order = $this->orders->place($request->user(), $data['items'], $data['note'] ?? null);

        return redirect()->route('orders.show', $order)->with('success', 'Order placed: '.$order->order_number);
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Only a pending order can be cancelled.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order cancelled.');
    }
}
