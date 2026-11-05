<?php

namespace App\Http\Controllers;

use App\Models\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('booking.tenant', 'booking.room')->orderBy('due_date')->get();

        return view('payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load('booking.room.kost', 'booking.tenant', 'verifier');

        return view('payments.show', compact('payment'));
    }
}