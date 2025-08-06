<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Exam;

class ExamDateMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $exam = Exam::first();
        $now = now();

        if (!$exam) {
            return redirect()->route('welcome')->with('error', 'Exam settings not found.');
        }

        // Check if today is the exam date
        if ($now->format('Y-m-d') !== $exam->exam_date->format('Y-m-d')) {
            return redirect()->route('welcome')->with('error', 'This application is only available on ' . $exam->exam_date->format('F j, Y') . ' between ' . date('g:i A', strtotime($exam->start_time)) . ' to ' . date('g:i A', strtotime($exam->end_time)) . ' (PHT)');
        }

        // Check if within allowed time range
        $currentTime = $now->format('H:i:s');
        if ($currentTime < $exam->start_time || $currentTime > $exam->end_time) {
            return redirect()->route('welcome')->with('error', 'This application is only available today from ' . date('g:i A', strtotime($exam->start_time)) . ' to ' . date('g:i A', strtotime($exam->end_time)) . ' (PHT)');
        }

        return $next($request);
    }
}
