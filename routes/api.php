<?php

use App\Http\Controllers\API\AssignmentController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\DiscussionController;
use App\Http\Controllers\API\MaterialController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ReplyController;
use App\Http\Controllers\API\SubmissionController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::controller(RegisterController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum', 'verified')->group(function () {

    Route::post('logout', [RegisterController::class, 'logout']);

    Route::middleware('role:dosen')->group(function () {
        Route::resource('courses', CourseController::class);
        Route::post('materials', [MaterialController::class, 'store']);
        Route::post('assignments', [AssignmentController::class, 'store']);
        Route::post('submissions/{id}/grade', [SubmissionController::class, 'updateScore']);
        Route::get('reports/courses', [CourseController::class, 'courseStatistics']);
        Route::get('reports/assignments', [AssignmentController::class, 'assignmentStatistics']);
        Route::get('reports/students/{id}', [SubmissionController::class, 'studentStatistics']);
    });

    Route::middleware('role:mahasiswa')->group(function () {
        Route::post('courses/{id}/enroll', [CourseController::class, 'enroll']);
        Route::get('materials/{id}/download', [MaterialController::class, 'download']);
        Route::post('submissions', [SubmissionController::class, 'upload']);
    });

    Route::middleware('role:dosen|mahasiswa')->group(function () {
        Route::post('discussions', [DiscussionController::class, 'store']);
        Route::post('discussions/{id}/replies', [ReplyController::class, 'store']);
    });
});

// Email Verification Routes
Route::get('/email/verify', function () {
    return response()->json(['message' => 'Email verification required.'], 403);
})->middleware('auth:sanctum')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return response()->json(['message' => 'Email verified successfully.']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

Route::post('/email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification link sent!']);
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');
