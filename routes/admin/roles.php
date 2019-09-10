<?php

use Illuminate\Support\Facades\Route;

Route::get('/roles', 'RoleController@index')
    ->name('role.index')
    ->middleware('permission:index roles');

Route::delete('/roles/{role}', 'RoleController@destroy')
    ->middleware(['role:super-admin', 'can:deleteDefault,role'])
    ->name('role.destroy')
    ->where('role', '[0-9]+');

Route::get('/roles/{role}/edit', 'RoleController@edit')
    ->middleware(['role:super-admin', 'can:editDefault,role'])
    ->name('role.edit')
    ->where('role', '[0-9]+');
Route::put('/roles/{role}', 'RoleController@update')
    ->middleware(['role:super-admin', 'can:editDefault,role'])
    ->name('role.update')
    ->where('role', '[0-9]+');

Route::get('/roles/create', 'RoleController@create')
    ->middleware('role:super-admin')
    ->name('role.create');
Route::post('/roles', 'RoleController@store')
    ->middleware('role:super-admin')
    ->name('role.store');
