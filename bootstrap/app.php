<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
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
        //
    })->create();
