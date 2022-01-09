<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Api\Auth\UserController;
use N1ebieski\ICore\Http\Controllers\Api\Auth\TokenController;

Route::post('auth/token', [TokenController::class, 'token'])
    ->name('auth.token.token');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('auth/refresh', [TokenController::class, 'refresh'])
        ->name('auth.token.refresh');

    Route::post('auth/revoke', [TokenController::class, 'revoke'])
        ->name('auth.token.revoke');

    Route::get('auth/user', [UserController::class, 'show'])
        ->name('auth.user.show');
});
