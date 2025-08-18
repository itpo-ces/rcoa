<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StartedExamMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to results page even if exam not started
        if ($request->route()->named('exam.results')) {
            return $next($request);
        }

        // Allow access to finish route even if exam not started
        if ($request->route()->named('exam.finish')) {
            return $next($request);
        }

        if (!session()->has('exam_started')) {
            return redirect()->route('exam.instructions')->with('error', 'Please start the exam first.');
        }
        
        return $next($request);
    }
}
