<?php

use App\Http\Controllers\Auth\Google2FAController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QuestionnaireController;
use App\Http\Controllers\Admin\ExaminationController;
use App\Http\Controllers\ExamController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

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
                    // Route::post('/exam/submit', [ExamController::class, 'submitExam'])->name('exam.submit');
                    Route::match(['get', 'post'], '/exam/submit', [ExamController::class, 'submitExam'])->name('exam.submit');
                    Route::get('/exam/certification', [ExamController::class, 'showCertification'])->name('exam.certification');
                    Route::get('/exam/results', [ExamController::class, 'showResults'])->name('exam.results');
                    Route::post('/exam/finish', [ExamController::class, 'finishExam'])->name('exam.finish');
                });
            });
        });
    });
});

/*=============================
          Admin Routes
===============================*/

Route::get('/oy4hyd4jmb', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/oy4hyd4jmb', [UserController::class, 'postLogin'])->name('postLogin')->middleware('throttle:3,1'); // 3 attempts per minute

Route::get('/logout', [UserController::class, 'logout'])->name('auth.logout');

// 2FA Routes
Route::prefix('2fa')->group(function () {
    Route::get('/register', [Google2FAController::class, 'register'])->name('google2fa.register');
    Route::post('/register', [Google2FAController::class, 'store'])->name('google2fa.store');
    Route::get('/verify', [Google2FAController::class, 'verify'])->name('google2fa.verify');
    Route::post('/verify', [Google2FAController::class, 'check'])->name('google2fa.check');
});

Route::middleware(['auth', '2fa'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Questionnaire Routes
    Route::get('/questionnaire', [QuestionnaireController::class, 'index'])->name('questionnaire.index');
    Route::post('/questionnaire', [QuestionnaireController::class, 'postQuestionnaireData'])->name('questionnaire.data');
    Route::post('/questionnaire/store', [QuestionnaireController::class, 'store'])->name('questionnaire.store');
    Route::post('/questionnaire/update', [QuestionnaireController::class, 'update'])->name('questionnaire.update');
    Route::post('/questionnaire/delete', [QuestionnaireController::class, 'delete'])->name('questionnaire.delete');
    Route::post('/questionnaire/restore', [QuestionnaireController::class, 'restore'])->name('questionnaire.restore');
    Route::post('/questionnaire/import', [QuestionnaireController::class, 'import'])->name('questionnaire.import');
    
    // Exam Routes
    Route::get('/exam', [ExamController::class, 'index'])->name('exam.index');
});