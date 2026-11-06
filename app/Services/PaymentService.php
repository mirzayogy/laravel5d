<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Simulated payments. No real gateway is called: paying only changes the order record.
 */
class PaymentService
{
    public const METHODS = [
        'cash' => 'Cash at cashier',
        'qris' => 'QRIS',
        'transfer' => 'Bank transfer (virtual account)',
        'ewallet' => 'E-wallet (GoPay, OVO, DANA)',
    ];

    /**
     * Choose (or change) the payment method of an unpaid order and prepare its reference.
     */
    public function choose(Order $order, string $method): Order
    {
        if (! array_key_exists($method, self::METHODS)) {
            throw ValidationException::withMessages(['payment_method' => __('Choose a valid payment method.')]);
        }

        $order->update([
            'payment_method' => $method,
            'payment_reference' => $this->referenceFor($order, $method),
        ]);

        return $order;
    }

    public function referenceFor(Order $order, string $method): ?string
    {
        return match ($method) {
            'qris' => 'QRIS-'.$order->order_number,
            'transfer' => '8808'.str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT),
            'ewallet' => 'EW-'.Str::upper(Str::random(10)),
            default => null,
        };
    }

    /**
     * Mark the order as paid (the "simulate payment" button and the cashier both use this).
     */
    public function markPaid(Order $order, ?string $method = null): Order
    {
        if ($order->status === 'cancelled') {
            throw ValidationException::withMessages(['payment' => __('A cancelled order cannot be paid.')]);
        }

        if ($order->isPaid()) {
            return $order;
        }

        $method ??= $order->payment_method ?? 'cash';

        $order->update([
            'payment_method' => $method,
            'payment_reference' => $order->payment_reference ?? $this->referenceFor($order, $method),
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        return $order;
    }

    public function change(int $total, int $received): int
    {
        return max(0, $received - $total);
    }
}
