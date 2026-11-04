<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /** Switch language: remembered in the session, and in the profile when signed in. */
    public function update(Request $request, string $locale)
    {
        abort_unless(array_key_exists($locale, config('habitude.locales')), 404);

        $request->session()->put('locale', $locale);
        $request->user()?->profile()->updateOrCreate([], ['locale' => $locale]);

        return back();
    }
}
