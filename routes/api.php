<?php

use App\Http\Controllers\Api\Admin\ConsultationController as AdminConsultationController;
use App\Http\Controllers\Api\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Api\Admin\ScreeningController as AdminScreeningController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\Parent\ChildController;
use App\Http\Controllers\Api\Student\ActivityController;
use App\Http\Controllers\Api\Student\ConsultationController as StudentConsultationController;
use App\Http\Controllers\Api\Student\JournalController;
use App\Http\Controllers\Api\Student\QuestionController as StudentQuestionController;
use App\Http\Controllers\Api\Student\ScreeningController as StudentScreeningController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ── Public Routes ────────────────────────────────────────────────────────
    Route::post('/login',        [AuthController::class, 'login']);
    Route::post('/login-parent', [AuthController::class, 'loginOrangTua']);

    // ── Protected Routes ─────────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout',          [AuthController::class, 'logout']);
        Route::get('/profile',          [AuthController::class, 'profile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);

        Route::get('/guru-bk',                    [UserController::class, 'index']);
        Route::get('/messages/{receiverId}',      [ChatController::class, 'getMessages']);
        Route::post('/send-message',              [ChatController::class, 'sendMessage']);
        Route::get('/messages/unread/count',      [ChatController::class, 'getUnreadCount']);

        // ── Admin Routes ─────────────────────────────────────────────────────
        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::post('/users/import',        [UserController::class, 'importStudents']);
            Route::apiResource('users',         UserController::class);
            Route::apiResource('questions',     AdminQuestionController::class);

            Route::get('/student-scores',             [AdminScreeningController::class, 'index']);
            Route::get('/student-scores/{student_id}', [AdminScreeningController::class, 'show']);

            Route::get('/consultations',                             [AdminConsultationController::class, 'index']);
            Route::post('/consultations/{consultation}/reply',       [AdminConsultationController::class, 'reply']);
            Route::put('/consultations/{consultation}/close',        [AdminConsultationController::class, 'close']);
        });

        // ── Student Routes ───────────────────────────────────────────────────
        Route::middleware('role:student')->prefix('student')->group(function () {
            Route::get('/questions',              [StudentQuestionController::class, 'index']);
            Route::post('/screening/submit',      [StudentScreeningController::class, 'submit']);
            Route::get('/screening/history',      [StudentScreeningController::class, 'history']);

            Route::apiResource('activities', ActivityController::class);
            Route::apiResource('journals',   JournalController::class);

            Route::get('/journal/check-pin',      [JournalController::class, 'checkPin']);
            Route::post('/journal/setup-pin',     [JournalController::class, 'setupPin']);
            Route::post('/journal/verify-pin',    [JournalController::class, 'verifyPin']);
            Route::post('/journal/forgot-pin',    [JournalController::class, 'forgotPin']);
            Route::post('/journal/verify-otp',    [JournalController::class, 'verifyOtp']);

            Route::get('/teachers',                                    [StudentConsultationController::class, 'index']);
            Route::post('/consultations',                              [StudentConsultationController::class, 'store']);
            Route::get('/consultations/{consultation}',                [StudentConsultationController::class, 'show']);
            Route::post('/consultations/{consultation}/message',       [StudentConsultationController::class, 'sendMessage']);
        });

        // ── Parent Routes ────────────────────────────────────────────────────
        Route::middleware('role:parent')->prefix('parent')->group(function () {
            Route::get('/children',                          [ChildController::class, 'children']);
            Route::get('/children/{student_id}/scores',     [ChildController::class, 'childScores']);
        });
    });
});
