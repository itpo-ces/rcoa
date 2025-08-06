<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Exam;

class ExamDateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $examDate = Exam::first()->exam_date;
    
        if (now()->format('Y-m-d') !== $examDate->format('Y-m-d')) {
            return redirect()->route('welcome')->with('error', 'The exam is only accessible on ' . $examDate->format('F j, Y'));
        }
        
        return $next($request);
    }
}
