<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\RateLimiter;

class EmailVerificationPromptController extends Controller
{

    public function index(Request $request)
    {
        $key = 'verification-email:' . $request->user()->id;

        $remaining = RateLimiter::tooManyAttempts($key, 6)
            ? RateLimiter::availableIn($key)
            : 0;

        return view('auth.verify-email', compact('remaining'));
    }
    
    public function __invoke(Request $request): View|RedirectResponse
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(route('dashboard'))
            : view('auth.verify-email');
    }
}