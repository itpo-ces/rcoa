<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
// use Exception;
use App\Http\Middleware\AcceptedPrivacyMiddleware;
use App\Http\Middleware\ExamDateMiddleware;
use App\Http\Middleware\RegisteredExamineeMiddleware;
use App\Http\Middleware\StartedExamMiddleware;
use App\Http\Middleware\ValidTokenMiddleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'exam.date' => ExamDateMiddleware::class,
            'valid.token' => ValidTokenMiddleware::class,
            'accepted.privacy' => AcceptedPrivacyMiddleware::class,
            'registered.examinee' => RegisteredExamineeMiddleware::class,
            'started.exam' => StartedExamMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Request $request, Throwable $e) {
            if ($e instanceof QueryException) {
                Log::error('Database query error: ' . $e->getMessage());
                return response()->view('errors.500', [], 500);
            }

            if ($e instanceof NotFoundHttpException) {
                return response()->view('errors.404', [], 404);
            }

            if ($e instanceof ServiceUnavailableHttpException) {
                return response()->view('errors.503', [], 503);
            }

            if ($e instanceof PostTooLargeException) {
                return response()->view('errors.413', [], 413);
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->view('errors.405', [], 405);
            }

            Log::error('Unexpected exception: ' . $e->getMessage());
            return response()->view('errors.500', [], 500);
        });
    })->create();
