<?php

use App\Http\Controllers\API\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::prefix('user')->group(function () {
    Route::post('register', [UserAuthController::class, 'reqister']);
    // Route::post('login', [UserAuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        // Route::get('logout', [UserAuthController::class, 'logout']);
        // Route::get('profile', [UserAuthController::class, 'profile']);
    });
});
