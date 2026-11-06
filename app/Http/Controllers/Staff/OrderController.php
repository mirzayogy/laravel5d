<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders, private PaymentService $payments) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $payment = $request->query('payment');
        $search = trim((string) $request->query('q', ''));

        $orders = Order::with(['user', 'items'])
            ->when(
                in_array($status, array_keys(OrderService::TRANSITIONS), true),
                fn ($query) => $query->where('status', $status)
            )
            ->when(
                in_array($payment, ['paid', 'unpaid', 'refunded'], true),
                fn ($query) => $query->where('payment_status', $payment)
            )
            ->when($search !== '', fn ($query) => $query->where(
                fn ($inner) => $inner->where('order_number', 'like', "%{$search}%")
                    ->orWhere('table_number', $search)
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%"))
            ))
            ->latest('ordered_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $statuses = array_keys(OrderService::TRANSITIONS);
        $latestId = (int) Order::max('id');

        return view('staff.orders.index', compact('orders', 'status', 'statuses', 'payment', 'search', 'latestId'));
    }

    /**
     * How many orders arrived after the given id (the queue page polls this).
     */
    public function poll(Request $request): JsonResponse
    {
        $after = (int) $request->query('after', 0);

        return response()->json([
            'latest_id' => (int) Order::max('id'),
            'new_orders' => Order::where('id', '>', $after)->count(),
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(OrderService::TRANSITIONS))],
        ]);

        if (! $this->orders->canTransition($order->status, $data['status'])) {
            return back()->with('error', __('An order cannot change from :from to :to.', ['from' => __($order->status), 'to' => __($data['status'])]));
        }

        $this->orders->changeStatus($order, $data['status']);

        return back()->with('success', __('Order :number is now :status.', ['number' => $order->order_number, 'status' => __($data['status'])]));
    }

    /**
     * The cashier confirms a payment (cash, or any other method that was paid at the counter).
     */
    public function pay(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'cash_received' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'payment_method' => ['nullable', Rule::in(array_keys(PaymentService::METHODS))],
        ]);

        if ($order->status === 'cancelled') {
            return back()->with('error', __('A cancelled order cannot be paid.'));
        }

        if ($order->isPaid()) {
            return back()->with('error', __('This order is already paid.'));
        }

        $method = $data['payment_method'] ?? $order->payment_method ?? 'cash';
        $total = $order->load('items')->total();

        if ($method === 'cash' && isset($data['cash_received']) && $data['cash_received'] < $total) {
            return back()->with('error', __('The cash received is less than the total (Rp :total).', ['total' => number_format($total, 0, ',', '.')]));
        }

        $this->payments->markPaid($order, $method);

        $message = __('Payment for :number confirmed.', ['number' => $order->order_number]);

        if ($method === 'cash' && isset($data['cash_received'])) {
            $message .= ' '.__('Change: Rp :amount.', ['amount' => number_format($this->payments->change($total, $data['cash_received']), 0, ',', '.')]);
        }

        return back()->with('success', $message);
    }
}
