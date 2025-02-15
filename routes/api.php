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

    // Route::post('logout', 'logout');

    Route::resource('courses', CourseController::class)->middleware('role:dosen');
    Route::post('courses/{id}/enroll', [CourseController::class, 'enroll'])->middleware('role:mahasiswa');

    Route::resource('material', MaterialController::class)->middleware('role:dosen');
    Route::get('materials/{id}/download', [MaterialController::class, 'download'])->middleware('role:mahasiswa');

    Route::resource('assigment', AssigmentController::class);

    Route::resource('discussion', DiscussionController::class);

    Route::resource('reply', ReplyController::class);
    Route::resource('submisson', SubmissionController::class);
});
