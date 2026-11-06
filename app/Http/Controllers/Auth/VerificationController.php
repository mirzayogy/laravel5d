<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->route('home')->with('success', __('Your email is verified. You can place orders now.'));
    }

    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', __('A new verification link has been sent to your email.'));
    }

    /**
     * Local development only: the mail driver is "log", so this button verifies the account without opening the log.
     */
    public function demo(Request $request): RedirectResponse
    {
        abort_unless(app()->environment('local'), 404);

        $request->user()->markEmailAsVerified();

        return redirect()->route('home')->with('success', __('Email marked as verified (demo).'));
    }
}
