<?php

namespace App\Http\Controllers;

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
        $isStaff = $this->authorizeView($request, $order);

        $order->load(['items.menu', 'user']);

        $nextStatuses = OrderService::TRANSITIONS[$order->status];
        $methods = PaymentService::METHODS;

        return view('orders.show', compact('order', 'isStaff', 'nextStatuses', 'methods'));
    }

    /**
     * Small JSON used by the order page to refresh itself when the status changes.
     */
    public function state(Request $request, Order $order): JsonResponse
    {
        $this->authorizeView($request, $order);

        return response()->json([
            'status' => $order->status,
            'payment_status' => $order->payment_status,
        ]);
    }

    public function receipt(Request $request, Order $order): View
    {
        $this->authorizeView($request, $order);

        $order->load(['items.menu', 'user']);

        return view('orders.receipt', ['order' => $order, 'methods' => PaymentService::METHODS]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0', 'max:20'],
            'options' => ['nullable', 'array'],
            'options.*' => ['nullable', 'string', 'max:60'],
            'note' => ['nullable', 'string', 'max:255'],
            'order_type' => ['nullable', Rule::in(OrderService::ORDER_TYPES)],
            'table_number' => ['nullable', 'string', 'max:10'],
            'payment_method' => ['nullable', Rule::in(array_keys(PaymentService::METHODS))],
        ]);

        $order = $this->orders->place(
            $request->user(),
            $data['items'],
            $data['note'] ?? null,
            [
                'order_type' => $data['order_type'] ?? 'dine_in',
                'table_number' => $data['table_number'] ?? null,
                'payment_method' => $data['payment_method'] ?? 'cash',
            ],
            $data['options'] ?? [],
        );

        return redirect()->route('orders.show', $order)->with('success', __('Order placed: :number', ['number' => $order->order_number]));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->status !== 'pending') {
            return back()->with('error', __('Only a pending order can be cancelled.'));
        }

        $this->orders->changeStatus($order, 'cancelled');

        return back()->with('success', __('Order cancelled.'));
    }

    public function payment(Request $request, Order $order): View|RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->status === 'cancelled') {
            return redirect()->route('orders.show', $order)->with('error', __('A cancelled order cannot be paid.'));
        }

        if ($order->isPaid()) {
            return redirect()->route('orders.show', $order)->with('success', __('This order is already paid.'));
        }

        $order->load('items.menu');
        $methods = PaymentService::METHODS;

        return view('orders.pay', compact('order', 'methods'));
    }

    public function chooseMethod(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'payment_method' => ['required', Rule::in(array_keys(PaymentService::METHODS))],
        ]);

        if ($order->isPaid() || $order->status === 'cancelled') {
            return redirect()->route('orders.show', $order)->with('error', __('The payment method of this order can no longer be changed.'));
        }

        $this->payments->choose($order, $data['payment_method']);

        return redirect()->route('orders.payment', $order);
    }

    public function confirmPayment(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->payment_method === 'cash') {
            return back()->with('error', __('Cash is paid at the cashier. Choose another method to pay online.'));
        }

        $this->payments->markPaid($order);

        return redirect()->route('orders.show', $order)->with('success', __('Payment received. Thank you!'));
    }

    private function authorizeView(Request $request, Order $order): bool
    {
        $user = $request->user();
        $isStaff = in_array($user->role, ['cashier', 'admin'], true);

        abort_unless($order->user_id === $user->id || $isStaff, 403);

        return $isStaff;
    }
}
