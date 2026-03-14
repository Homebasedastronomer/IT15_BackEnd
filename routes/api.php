<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\SchoolDayController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('token-test', function () {
    $user = User::query()->first();

    if (! $user) {
        return response()->json([
            'message' => 'No users found. Create a user first.',
        ], 404);
    }

    return response($user->createToken('test')->plainTextToken)
        ->header('Content-Type', 'text/plain');
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/students', [StudentController::class, 'index']);
    Route::post('/students', [StudentController::class, 'store']);
    Route::get('/students/search', [StudentController::class, 'search']);
    Route::get('/students/{studentNumber}/enrollment-history', [StudentController::class, 'enrollmentHistory']);
    Route::get('/programs', [CourseController::class, 'programs']);
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::get('/school-days', [SchoolDayController::class, 'index']);
    Route::get('/enrollment/options', [EnrollmentController::class, 'options']);
    Route::post('/enrollment', [EnrollmentController::class, 'store']);

    Route::prefix('dashboard')->group(function () {
        Route::get('/overview', [DashboardController::class, 'overview']);
        Route::get('/enrollment-trend', [DashboardController::class, 'enrollmentTrend']);
        Route::get('/course-distribution', [DashboardController::class, 'courseDistribution']);
        Route::get('/attendance-trend', [DashboardController::class, 'attendanceTrend']);
    });
});
