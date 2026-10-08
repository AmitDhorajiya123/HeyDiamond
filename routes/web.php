<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiamondController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'prevent-back-history'])->group(function () {
    Route::get('/diamonds', [DiamondController::class, 'index'])->name('diamonds.index');
    Route::get('/diamonds/data', [DiamondController::class, 'data'])->name('diamonds.data');
});

Route::get('/fetch-diamonds', [DiamondController::class, 'fetchAndStoreDiamonds']);
