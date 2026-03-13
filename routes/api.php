<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SchoolDayController;
use App\Http\Controllers\Api\StudentController;
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
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/school-days', [SchoolDayController::class, 'index']);

    Route::prefix('dashboard')->group(function () {
        Route::get('/overview', [DashboardController::class, 'overview']);
        Route::get('/enrollment-trend', [DashboardController::class, 'enrollmentTrend']);
        Route::get('/course-distribution', [DashboardController::class, 'courseDistribution']);
        Route::get('/attendance-trend', [DashboardController::class, 'attendanceTrend']);
    });
});
