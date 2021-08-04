<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::match(['get', 'post'], '/users/index', 'User\UserController@index')
        ->name('user.index')
        ->middleware('permission:admin.users.view');
});
