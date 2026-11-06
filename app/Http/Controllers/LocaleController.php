<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public const LOCALES = ['id', 'en'];

    public function switch(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, self::LOCALES, true), 404);

        $request->session()->put('locale', $locale);

        return back();
    }
}
