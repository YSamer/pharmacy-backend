<?php

use App\Http\Controllers\API\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::prefix('user')->group(function () {
    Route::post('register', [UserAuthController::class, 'register']);
    Route::post('login', [UserAuthController::class, 'login']);
    Route::post('verify-phone', [UserAuthController::class, 'verifyPhone']);

    Route::middleware('auth:user')->group(function () {
        Route::get('logout', [UserAuthController::class, 'logout']);
        Route::get('auto-login', [UserAuthController::class, 'user']);
        Route::get('profile', [UserAuthController::class, 'user']);
        Route::post('update-profile', [UserAuthController::class, 'updateProfile']);
        Route::post('delete-account', [UserAuthController::class, 'deleteAccount']);
    });
});
