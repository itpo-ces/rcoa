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

        if (!$exam) {
            return redirect()->route('welcome')->with('error', 'Exam not found.');
        }

        $now = now(); // Automatically uses Asia/Manila if app timezone is set
        $examDate = $exam->exam_date->format('Y-m-d');
        $today = $now->format('Y-m-d');

        if ($today !== $examDate) {
            return redirect()->route('welcome')->with('error', 'This application is only available on ' . $exam->exam_date->format('F j, Y'));
        }

        $startTime = now()->setTimeFromTimeString($exam->start_time);
        $endTime = now()->setTimeFromTimeString($exam->end_time);

        if ($now->lt($startTime) || $now->gt($endTime)) {
            return redirect()->route('welcome')->with('error', 'This application is only available today from ' . $startTime->format('g:i A') . ' to ' . $endTime->format('g:i A') . ' (PHT)');
        }

        return $next($request);
    }

}
