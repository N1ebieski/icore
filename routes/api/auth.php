<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Api\Auth\TokenController;
use N1ebieski\ICore\Http\Controllers\Api\Auth\RegisterController;

Route::post('token', [TokenController::class, 'token'])
    ->name('auth.token.token')
    ->middleware('icore.guest');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('refresh', [TokenController::class, 'refresh'])
        ->name('auth.token.refresh');

    Route::post('revoke', [TokenController::class, 'revoke'])
        ->name('auth.token.revoke');
});

Route::post('register', [RegisterController::class, 'register'])
    ->name('auth.register.register')
    ->middleware('icore.guest');
