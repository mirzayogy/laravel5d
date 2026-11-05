<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kost;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'kosts' => Kost::count(),
            'rooms' => Room::count(),
            'tenants' => Tenant::count(),
            'activeBookings' => Booking::where('status', 'active')->count(),
            'paid' => Payment::where('status', 'paid')->where('type', '!=', 'refund')->sum('amount'),
            'unpaid' => Payment::whereIn('status', ['unpaid', 'waiting_verification', 'overdue'])->where('type', '!=', 'refund')->sum('amount'),
        ];

        $latestBookings = Booking::with(['room.kost', 'tenant'])->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'latestBookings'));
    }
}