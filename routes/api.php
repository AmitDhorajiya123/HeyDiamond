<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DiamondApiController;
use App\Http\Controllers\Api\LocationApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\MstShapeApiController;
use App\Http\Controllers\Api\MstColorApiController;
use App\Http\Controllers\Api\MstClarityApiController;
use App\Http\Controllers\Api\MstFinishGradeApiController;
use App\Http\Controllers\Api\MstFluorescenceApiController;
use App\Http\Controllers\Api\MstLabApiController;
use App\Http\Controllers\Api\MstSizeRangeApiController;
use App\Http\Controllers\Api\MstBgmBlackOptionApiController;
use App\Http\Controllers\Api\MstCategoryApiController;
use App\Http\Controllers\Api\MstSubcategoryApiController;
use App\Http\Controllers\Api\EncryptionTestController;
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

Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'HeyDiamond Laravel API is working!',
    ]);
});
// ============================================
// PUBLIC TEST ENDPOINTS (No Auth Required)
// ============================================
Route::prefix('test')->group(function () {
    // Encrypt a plain ID
    Route::post('encrypt', [EncryptionTestController::class, 'encrypt'])->name('test.encrypt');
    
    // Decrypt an encrypted ID
    Route::post('decrypt', [EncryptionTestController::class, 'decrypt'])->name('test.decrypt');
    
    // Find model by encrypted ID
    Route::post('find-by-encrypted-id', [EncryptionTestController::class, 'findByEncryptedId'])->name('test.find-by-encrypted-id');
});

// Protected Routes (Passport auth:api)
Route::middleware('auth:api')->group(function () {
    // Auth profile & logout
    Route::get('profile', [AuthApiController::class, 'profile'])->name('api.profile');
    Route::post('logout', [AuthApiController::class, 'logout'])->name('api.logout');

    // Diamond CRUD API (still uses plain ID for backward compatibility)
    Route::apiResource('diamonds', DiamondApiController::class);

    // User CRUD API (NOW USES ENCRYPTED ID)
    Route::get('users', [UserApiController::class, 'index'])->name('users.index');
    Route::post('users', [UserApiController::class, 'store'])->name('users.store');
    Route::get('users/{encrypted_id}', [UserApiController::class, 'show'])->name('users.show');
    Route::put('users/{encrypted_id}', [UserApiController::class, 'update'])->name('users.update');
    Route::patch('users/{encrypted_id}', [UserApiController::class, 'update'])->name('users.update');
    Route::delete('users/{encrypted_id}', [UserApiController::class, 'destroy'])->name('users.destroy');

    // Location CRUD API (still uses plain ID for backward compatibility)
    Route::apiResource('locations', LocationApiController::class);

    // Master Tables CRUD APIs (using encrypted IDs)
    // Shape
    Route::get('shapes', [MstShapeApiController::class, 'index'])->name('shapes.index');
    Route::post('shapes', [MstShapeApiController::class, 'store'])->name('shapes.store');
    Route::get('shapes/{encrypted_id}', [MstShapeApiController::class, 'show'])->name('shapes.show');
    Route::put('shapes/{encrypted_id}', [MstShapeApiController::class, 'update'])->name('shapes.update');
    Route::patch('shapes/{encrypted_id}', [MstShapeApiController::class, 'update'])->name('shapes.update');
    Route::delete('shapes/{encrypted_id}', [MstShapeApiController::class, 'destroy'])->name('shapes.destroy');

    // Color
    Route::get('colors', [MstColorApiController::class, 'index'])->name('colors.index');
    Route::post('colors', [MstColorApiController::class, 'store'])->name('colors.store');
    Route::get('colors/{encrypted_id}', [MstColorApiController::class, 'show'])->name('colors.show');
    Route::put('colors/{encrypted_id}', [MstColorApiController::class, 'update'])->name('colors.update');
    Route::patch('colors/{encrypted_id}', [MstColorApiController::class, 'update'])->name('colors.update');
    Route::delete('colors/{encrypted_id}', [MstColorApiController::class, 'destroy'])->name('colors.destroy');

    // Clarity
    Route::get('clarities', [MstClarityApiController::class, 'index'])->name('clarities.index');
    Route::post('clarities', [MstClarityApiController::class, 'store'])->name('clarities.store');
    Route::get('clarities/{encrypted_id}', [MstClarityApiController::class, 'show'])->name('clarities.show');
    Route::put('clarities/{encrypted_id}', [MstClarityApiController::class, 'update'])->name('clarities.update');
    Route::patch('clarities/{encrypted_id}', [MstClarityApiController::class, 'update'])->name('clarities.update');
    Route::delete('clarities/{encrypted_id}', [MstClarityApiController::class, 'destroy'])->name('clarities.destroy');

    // Finish Grade
    Route::get('finish-grades', [MstFinishGradeApiController::class, 'index'])->name('finish-grades.index');
    Route::post('finish-grades', [MstFinishGradeApiController::class, 'store'])->name('finish-grades.store');
    Route::get('finish-grades/{encrypted_id}', [MstFinishGradeApiController::class, 'show'])->name('finish-grades.show');
    Route::put('finish-grades/{encrypted_id}', [MstFinishGradeApiController::class, 'update'])->name('finish-grades.update');
    Route::patch('finish-grades/{encrypted_id}', [MstFinishGradeApiController::class, 'update'])->name('finish-grades.update');
    Route::delete('finish-grades/{encrypted_id}', [MstFinishGradeApiController::class, 'destroy'])->name('finish-grades.destroy');

    // Fluorescence
    Route::get('fluorescences', [MstFluorescenceApiController::class, 'index'])->name('fluorescences.index');
    Route::post('fluorescences', [MstFluorescenceApiController::class, 'store'])->name('fluorescences.store');
    Route::get('fluorescences/{encrypted_id}', [MstFluorescenceApiController::class, 'show'])->name('fluorescences.show');
    Route::put('fluorescences/{encrypted_id}', [MstFluorescenceApiController::class, 'update'])->name('fluorescences.update');
    Route::patch('fluorescences/{encrypted_id}', [MstFluorescenceApiController::class, 'update'])->name('fluorescences.update');
    Route::delete('fluorescences/{encrypted_id}', [MstFluorescenceApiController::class, 'destroy'])->name('fluorescences.destroy');

    // Lab
    Route::get('labs', [MstLabApiController::class, 'index'])->name('labs.index');
    Route::post('labs', [MstLabApiController::class, 'store'])->name('labs.store');
    Route::get('labs/{encrypted_id}', [MstLabApiController::class, 'show'])->name('labs.show');
    Route::put('labs/{encrypted_id}', [MstLabApiController::class, 'update'])->name('labs.update');
    Route::patch('labs/{encrypted_id}', [MstLabApiController::class, 'update'])->name('labs.update');
    Route::delete('labs/{encrypted_id}', [MstLabApiController::class, 'destroy'])->name('labs.destroy');

    // Size Range
    Route::get('size-ranges', [MstSizeRangeApiController::class, 'index'])->name('size-ranges.index');
    Route::post('size-ranges', [MstSizeRangeApiController::class, 'store'])->name('size-ranges.store');
    Route::get('size-ranges/{encrypted_id}', [MstSizeRangeApiController::class, 'show'])->name('size-ranges.show');
    Route::put('size-ranges/{encrypted_id}', [MstSizeRangeApiController::class, 'update'])->name('size-ranges.update');
    Route::patch('size-ranges/{encrypted_id}', [MstSizeRangeApiController::class, 'update'])->name('size-ranges.update');
    Route::delete('size-ranges/{encrypted_id}', [MstSizeRangeApiController::class, 'destroy'])->name('size-ranges.destroy');

    // BGM Black Option
    Route::get('bgm-black-options', [MstBgmBlackOptionApiController::class, 'index'])->name('bgm-black-options.index');
    Route::post('bgm-black-options', [MstBgmBlackOptionApiController::class, 'store'])->name('bgm-black-options.store');
    Route::get('bgm-black-options/{encrypted_id}', [MstBgmBlackOptionApiController::class, 'show'])->name('bgm-black-options.show');
    Route::put('bgm-black-options/{encrypted_id}', [MstBgmBlackOptionApiController::class, 'update'])->name('bgm-black-options.update');
    Route::patch('bgm-black-options/{encrypted_id}', [MstBgmBlackOptionApiController::class, 'update'])->name('bgm-black-options.update');
    Route::delete('bgm-black-options/{encrypted_id}', [MstBgmBlackOptionApiController::class, 'destroy'])->name('bgm-black-options.destroy');

    // Category
    Route::get('categories', [MstCategoryApiController::class, 'index'])->name('categories.index');
    Route::post('categories', [MstCategoryApiController::class, 'store'])->name('categories.store');
    Route::get('categories/{encrypted_id}', [MstCategoryApiController::class, 'show'])->name('categories.show');
    Route::put('categories/{encrypted_id}', [MstCategoryApiController::class, 'update'])->name('categories.update');
    Route::patch('categories/{encrypted_id}', [MstCategoryApiController::class, 'update'])->name('categories.update');
    Route::delete('categories/{encrypted_id}', [MstCategoryApiController::class, 'destroy'])->name('categories.destroy');

    // Subcategory
    Route::get('subcategories', [MstSubcategoryApiController::class, 'index'])->name('subcategories.index');
    Route::post('subcategories', [MstSubcategoryApiController::class, 'store'])->name('subcategories.store');
    Route::get('subcategories/{encrypted_id}', [MstSubcategoryApiController::class, 'show'])->name('subcategories.show');
    Route::put('subcategories/{encrypted_id}', [MstSubcategoryApiController::class, 'update'])->name('subcategories.update');
    Route::patch('subcategories/{encrypted_id}', [MstSubcategoryApiController::class, 'update'])->name('subcategories.update');
    Route::delete('subcategories/{encrypted_id}', [MstSubcategoryApiController::class, 'destroy'])->name('subcategories.destroy');
});