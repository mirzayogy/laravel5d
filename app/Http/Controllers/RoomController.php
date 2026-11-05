<?php

namespace App\Http\Controllers;

use App\Models\Room;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['kost', 'facilities'])->get();

        return view('rooms.index', compact('rooms'));
    }

    public function show(Room $room)
    {
        $room->load(['kost', 'facilities', 'bookings.tenant']);

        return view('rooms.show', compact('room'));
    }
}