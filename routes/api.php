<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DiamondApiController;
use App\Http\Controllers\Api\LocationApiController;
use App\Http\Controllers\Api\UserApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

// Public Authentication Routes
Route::post('login', [AuthApiController::class, 'login'])->name('api.login');
Route::post('register', [AuthApiController::class, 'register'])->name('api.register');

// Protected Routes (Passport auth:api)
Route::middleware('auth:api')->group(function () {
    // Auth profile & logout
    Route::get('profile', [AuthApiController::class, 'profile'])->name('api.profile');
    Route::post('logout', [AuthApiController::class, 'logout'])->name('api.logout');

    // Diamond CRUD API
    Route::apiResource('diamonds', DiamondApiController::class);

    // User CRUD API
    Route::apiResource('users', UserApiController::class);

    // Location CRUD API
    Route::apiResource('locations', LocationApiController::class);
});
