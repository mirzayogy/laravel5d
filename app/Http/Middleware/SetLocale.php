<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Pick the interface language: the signed-in user's choice, else what a
     * visitor chose on the sign-in pages, else what their browser asks for.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $available = array_keys(config('habitude.locales'));

        $locale = $request->user()?->profile?->locale
            ?? $request->session()->get('locale')
            ?? $request->getPreferredLanguage($available)
            ?? config('app.locale');

        $locale = in_array($locale, $available, true) ? $locale : config('app.locale');

        App::setLocale($locale);
        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);

        return $next($request);
    }
}
