<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Examinee;

class RegisteredExamineeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $examinee = Examinee::where('token_id', session('token_id'))->first();
        
        if (!$examinee || !$examinee->first_name) { // Check if registration is complete
            return redirect()->route('exam.register')->with('error', 'Please complete your registration first.');
        }
        
        return $next($request);
    }
}
