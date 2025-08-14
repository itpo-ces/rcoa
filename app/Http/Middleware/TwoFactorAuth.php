<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class TwoFactorAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Log user information
            if ($user->google2fa_secret) {
                
                if (!$request->session()->get('2fa_verified')) {
                    // Redirect to the 2FA verification page if not verified
                    return redirect()->route('google2fa.verify');
                }
                
                // Ensure the request proceeds if 2FA is verified
                return $next($request);
            } else {
                // Redirect to 2FA registration page if the secret is missing
                return redirect()->route('google2fa.register');
            }
        } else {
            \Log::info('User is not authenticated.');
            // Redirect to the login page if not authenticated
            return redirect()->route('welcome');
        }
    }
}