<?php

use App\Http\Controllers\API\AssigmentController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\DiscussionController;
use App\Http\Controllers\API\MaterialController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ReplyController;
use App\Http\Controllers\API\SubmissionController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::controller(RegisterController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [RegisterController::class, 'logout']);

    Route::resource('courses', CourseController::class)->middleware('role:dosen');
    Route::post('courses/{id}/enroll', [CourseController::class, 'enroll'])->middleware('role:mahasiswa');

    Route::post('materials', [MaterialController::class, 'store'])->middleware('role:dosen');
    Route::get('materials/{id}/download', [MaterialController::class, 'download'])->middleware('role:mahasiswa');

    Route::post('assignments', [AssigmentController::class, 'store'])->middleware('role:dosen');
    Route::post('submissions', [SubmissionController::class, 'upload'])->middleware('role:mahasiswa');
    Route::post('submissions/{id}/grade', [SubmissionController::class, 'updateScore'])->middleware('role:dosen');

    Route::post('discussions', [DiscussionController::class, 'store'])->middleware('role:dosen,mahasiswa');
    Route::post('discussions/{id}/replies', [ReplyController::class, 'store'])->middleware('role:dosen,mahasiswa');

    Route::get('reports/courses', [CourseController::class, 'courseStatistics'])->middleware('role:dosen');
    Route::get('reports/assignments', [AssigmentController::class, 'assignmentStatistics'])->middleware('role:dosen');
    Route::get('reports/students/{id}', [SubmissionController::class, 'studentStatistics'])->middleware('role:dosen');
});
