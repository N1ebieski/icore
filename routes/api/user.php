<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Api\Auth\UserController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('user', [UserController::class, 'show'])
        ->name('auth.user.show');
});
