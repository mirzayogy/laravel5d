<?php

namespace App\Http\Controllers;

use App\Models\Facility;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::withCount('rooms')->get();

        return view('facilities.index', compact('facilities'));
    }

    public function show(Facility $facility)
    {
        $facility->load('rooms.kost');

        return view('facilities.show', compact('facility'));
    }
}