<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    public function destroy(Order $order): RedirectResponse
    {
        if (! in_array($order->status, ['completed', 'cancelled'], true)) {
            return back()->with('error', 'Only a completed or cancelled order can be deleted.');
        }

        $number = $order->order_number;
        $order->delete();

        return redirect()->route('staff.orders.index')->with('success', 'Order '.$number.' deleted.');
    }
}
