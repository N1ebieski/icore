<?php

use Illuminate\Support\Facades\Route;

Route::get('/users', 'UserController@index')
    ->name('user.index')
    ->middleware('permission:index users');

Route::patch('/users/{user}', 'UserController@updateStatus')
    ->middleware(['role:super-admin', 'can:actionSelf,user'])
    ->name('user.update_status')
    ->where('user', '[0-9]+');

Route::delete('/users/{user}', 'UserController@destroy')
    ->middleware(['role:super-admin', 'can:actionSelf,user'])
    ->name('user.destroy')
    ->where('user', '[0-9]+');
Route::delete('/users', 'UserController@destroyGlobal')
    ->middleware('role:super-admin')
    ->name('user.destroy_global');

Route::get('/users/{user}/edit', 'UserController@edit')
    ->middleware(['role:super-admin', 'can:actionSelf,user'])
    ->name('user.edit')
    ->where('user', '[0-9]+');
Route::put('/users/{user}', 'UserController@update')
    ->middleware(['role:super-admin', 'can:actionSelf,user'])
    ->name('user.update')
    ->where('user', '[0-9]+');

Route::get('/users/create', 'UserController@create')
    ->middleware('role:super-admin')
    ->name('user.create');
Route::post('/users', 'UserController@store')
    ->middleware('role:super-admin')
    ->name('user.store');
