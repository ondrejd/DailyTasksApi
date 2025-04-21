<?php

use App\Http\Controllers\Api\TagController;
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

    Route::controller(TagController::class)->prefix('tag')->group(function() {
        Route::get('', 'list')->name('tag.list');
        Route::post('', 'create')->name('tag.create');
        Route::put('{tag}', 'update')->where('id', '[0-9]+')->name('tag.update');
        Route::delete('{tag}', 'delete')->where('id', '[0-9]+')->name('tag.delete');
    });
});
