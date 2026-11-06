<?php

namespace App\Http\Middleware;

use App\Http\Controllers\LocaleController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', config('app.locale'));

        if (in_array($locale, LocaleController::LOCALES, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
