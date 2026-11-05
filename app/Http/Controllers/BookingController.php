<?php

namespace App\Http\Controllers;

use App\Models\Booking;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['room.kost', 'tenant'])->get();

        return view('bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['room.kost', 'tenant', 'payments']);

        return view('bookings.show', compact('booking'));
    }
}