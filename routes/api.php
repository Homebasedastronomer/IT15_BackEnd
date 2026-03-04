<?php

use App\Http\Controllers\Api\AuthController;
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
