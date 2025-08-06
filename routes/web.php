<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ExamController;

Route::get('/', function () {
    //  Session::flush();
    return view('welcome');
})->name('welcome');

Route::middleware(['exam.date'])->group(function () {
    Route::get('/exam/start', [ExamController::class, 'start'])->name('exam.start');
    Route::post('/exam/validate-token', [ExamController::class, 'validateToken'])->name('exam.validate-token');
    
    Route::middleware(['valid.token'])->group(function () {
        Route::get('/exam/privacy', [ExamController::class, 'privacy'])->name('exam.privacy');
        Route::post('/exam/accept-privacy', [ExamController::class, 'acceptPrivacy'])->name('exam.accept-privacy');
        
        Route::middleware(['accepted.privacy'])->group(function () {
            Route::get('/exam/register', [ExamController::class, 'register'])->name('exam.register');
            Route::post('/exam/register', [ExamController::class, 'storeRegistration'])->name('exam.store-registration');

            Route::get('/get-subunits', [ExamController::class, 'getSubunits'])->name('getPersonnelSubunits');
            Route::get('/get-stations', [ExamController::class, 'getStations'])->name('getPersonnelStations');
            
            Route::middleware(['registered.examinee'])->group(function () {
                Route::get('/exam/instructions', [ExamController::class, 'instructions'])->name('exam.instructions');
                Route::post('/exam/start-exam', [ExamController::class, 'startExam'])->name('exam.start-exam');
                
                Route::middleware(['started.exam'])->group(function () {
                    Route::get('/exam/take', [ExamController::class, 'takeExam'])->name('exam.take');
                    Route::post('/exam/save-answer', [ExamController::class, 'saveAnswer'])->name('exam.save-answer');
                    Route::get('/exam/question/{question_number}', [ExamController::class, 'showQuestion'])->name('exam.question');
                    Route::post('/exam/submit', [ExamController::class, 'submitExam'])->name('exam.submit');
                    Route::get('/exam/results', [ExamController::class, 'showResults'])->name('exam.results');
                    Route::post('/exam/finish', [ExamController::class, 'finishExam'])->name('exam.finish');
                });
            });
        });
    });
});