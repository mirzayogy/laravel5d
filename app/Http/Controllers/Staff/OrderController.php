<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');

        $orders = Order::with(['user', 'items'])
            ->when(
                in_array($status, array_keys(OrderService::TRANSITIONS), true),
                fn ($query) => $query->where('status', $status)
            )
            ->latest('ordered_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $statuses = array_keys(OrderService::TRANSITIONS);

        return view('staff.orders.index', compact('orders', 'status', 'statuses'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(OrderService::TRANSITIONS))],
        ]);

        if (! $this->orders->canTransition($order->status, $data['status'])) {
            return back()->with('error', 'An order cannot change from '.$order->status.' to '.$data['status'].'.');
        }

        $order->update(['status' => $data['status']]);

        return back()->with('success', 'Order '.$order->order_number.' is now '.$data['status'].'.');
    }
}
