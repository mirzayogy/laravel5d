<?php

namespace App\Http\Controllers;

use App\Models\Kost;

class KostController extends Controller
{
    public function index()
    {
        $kosts = Kost::with(['owner:id,name,email', 'rooms'])->get();

        return view('kosts.index', compact('kosts'));
    }

    public function show(Kost $kost)
    {
        $kost->load(['owner:id,name,email', 'rooms.facilities']);

        return view('kosts.show', compact('kost'));
    }
}