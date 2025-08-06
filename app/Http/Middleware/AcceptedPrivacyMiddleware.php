<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Examinee;

class AcceptedPrivacyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $examinee = Examinee::where('token_id', session('token_id'))->first();
    
        if (!$examinee || !$examinee->accepted_privacy) {
            return redirect()->route('exam.privacy')->with('error', 'You must accept the data privacy statement first.');
        }
        
        return $next($request);
    }
}
