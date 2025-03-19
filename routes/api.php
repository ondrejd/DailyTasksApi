<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::controller(UserController::class)->prefix('user')->group(function() {
    Route::post('register', 'register')->name('user.register');
    Route::post('login', 'login')->name('user.login');
});

Route::middleware('auth:sanctum')->group(function() {
    Route::controller(UserController::class)->prefix('user')->group(function() {
        Route::get('info', 'info')->name('user.info');
        Route::post('unregister', 'unregister')->name('user.unregister');
    });
});
