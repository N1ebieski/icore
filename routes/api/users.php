<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Api\User\UserController;

Route::group(['middleware' => 'auth:sanctum', 'permission:api.access'], function () {
    Route::match(['post', 'get'], '/users/index', [UserController::class, 'index'])
        ->name('user.index')
        ->middleware(['permission:admin.users.view', 'permission:api.users.view', 'ability:api.users.view']);
});
