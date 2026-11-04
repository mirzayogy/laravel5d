<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboarded
{
    /**
     * New users go through the onboarding stepper before using the app.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $profile = $request->user()?->profile;

        if ($profile && $profile->onboarded_at === null) {
            return redirect()->route('onboarding.show');
        }

        return $next($request);
    }
}
