<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Token;
use Carbon\Carbon;

class CheckAbandonedExam
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Find tokens that have been "in_use" for too long (e.g., 1 hours)
        $threshold = Carbon::now()->subHours(1);
        // $threshold = Carbon::now()->subMinutes(1); // 1 minute
        
        Token::where('status', Token::STATUS_IN_USE)
            ->where('updated_at', '<', $threshold)
            ->update([
                'status' => Token::STATUS_AVAILABLE,
                'is_used' => false
            ]);
            
        return $next($request);
    }
}
