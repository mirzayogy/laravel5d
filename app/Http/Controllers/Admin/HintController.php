<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\Hint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HintController extends Controller
{
    public function store(Request $request, Challenge $challenge): RedirectResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string'],
            'cost' => ['required', 'integer', 'min:0', 'max:1000'],
        ]);

        $challenge->hints()->create($data);

        return back()->with('success', 'Hint added.');
    }

    public function destroy(Hint $hint): RedirectResponse
    {
        $hint->delete();

        return back()->with('success', 'Hint deleted.');
    }
}
