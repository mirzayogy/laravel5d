<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $competitions = Competition::where('status', 'running')
            ->withCount([
                'challenges' => fn ($query) => $query->where('is_published', true),
                'teams',
            ])
            ->orderBy('start_at')
            ->get();

        return view('home', compact('competitions'));
    }
}
