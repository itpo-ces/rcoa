<?php

use App\Http\Controllers\Auth\Google2FAController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QuestionnaireController;
use App\Http\Controllers\Admin\ExaminationController;
use App\Http\Controllers\Admin\ExamResultController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\AnalysisController;
use App\Http\Controllers\Admin\TokenController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\ExamController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    //  Session::flush();
    return view('welcome');
})->name('welcome');

Route::middleware(['exam.date', 'check.abandoned.exam'])->group(function () {
    Route::get('/exam/start', [ExamController::class, 'start'])->name('exam.start');
    Route::post('/exam/validate-token', [ExamController::class, 'validateToken'])->name('exam.validate-token');
    Route::get('/token/status/{tokenId}', [ExamController::class, 'checkTokenStatus']);
    
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
                Route::get('/exam/resume', [ExamController::class, 'resumeExam'])->name('exam.resume')
                        ->middleware(['valid.token', 'accepted.privacy', 'registered.examinee']);
                
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
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');

    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Questionnaire Routes
    Route::get('/questionnaire', [QuestionnaireController::class, 'index'])->name('questionnaire.index');
    Route::post('/questionnaire/data', [QuestionnaireController::class, 'postQuestionnaireData'])->name('questionnaire.data');
    Route::post('/questionnaire/store', [QuestionnaireController::class, 'store'])->name('questionnaire.store');
    Route::post('/questionnaire/update', [QuestionnaireController::class, 'update'])->name('questionnaire.update');
    Route::post('/questionnaire/delete', [QuestionnaireController::class, 'delete'])->name('questionnaire.delete');
    Route::post('/questionnaire/restore', [QuestionnaireController::class, 'restore'])->name('questionnaire.restore');
    Route::post('/questionnaire/import', [QuestionnaireController::class, 'import'])->name('questionnaire.import');

    // Question Analysis
    Route::get('/analysis/questions', [AnalysisController::class, 'index'])->name('analysis.question.index');
    Route::post('/analysis/questions/data', [AnalysisController::class, 'postQuestionAnalysisData'])->name('analysis.question.data');
    Route::get('/analysis/questions/details', [AnalysisController::class, 'getQuestionDetails'])->name('analysis.question.details');

    // Export All Analysis
    Route::post('/analysis/export-all', [AnalysisController::class, 'exportAllAnalysis'])
        ->name('analysis.export-all');

    // Examination Routes
    Route::get('/examination', [ExaminationController::class, 'index'])->name('examination.index');
    Route::post('/examination/data', [ExaminationController::class, 'postExaminationData'])->name('examination.data');

    // Results Routes
    Route::get('/results', [ExamResultController::class, 'index'])->name('results.index');
    Route::post('/results/data', [ExamResultController::class, 'postResultsData'])->name('results.data');
    Route::get('/results/{id}', [ExamResultController::class, 'show'])->name('results.show');

    Route::get('/results/{id}/export/{type?}', [ExamResultController::class, 'exportResult'])
            ->name('results.export')
            ->where('type', 'excel|pdf');

    Route::post('/results/export-all', [ExamResultController::class, 'exportAllResults'])
        ->name('results.export-all');

    ////////////////////////////////////////////////////
        // Results Routes
    Route::get('/resultss', [ResultController::class, 'index'])->name('resultss.auto.index');
    Route::post('/resultss/data', [ResultController::class, 'postResultsData'])->name('resultss.auto.data');
    Route::get('/resultss/{id}', [ResultController::class, 'show'])->name('resultss.auto.show');

    Route::get('/resultss/{id}/export/{type?}', [ResultController::class, 'exportResult'])
            ->name('resultss.auto.export')
            ->where('type', 'excel|pdf');

    // Token Management Routes
    Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
    Route::post('/tokens/data', [TokenController::class, 'postTokenData'])->name('tokens.data');
    Route::post('/tokens/generate', [TokenController::class, 'generateTokens'])->name('tokens.generate');
    Route::post('/tokens/delete', [TokenController::class, 'deleteTokens'])->name('tokens.delete');
    Route::get('/tokens/qrcode/{tokenId}', [TokenController::class, 'generateQRCode'])->name('tokens.qrcode');
});