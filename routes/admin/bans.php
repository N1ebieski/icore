<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'bans/user/index', 'BanModel\User\BanModelController@index')
    ->name('banmodel.user.index')
    ->middleware('permission:admin.bans.view');
Route::match(['get', 'post'], 'bans/{type}/index', 'BanValueController@index')
    ->name('banvalue.index')
    ->middleware('permission:admin.bans.view')
    ->where('type', '[A-Za-z]+');

Route::delete('bans/model/{banModel}', 'BanModel\BanModelController@destroy')
    ->middleware('permission:admin.bans.delete')
    ->name('banmodel.destroy')
    ->where('banModel', '[0-9]+');
Route::delete('bans/model', 'BanModel\BanModelController@destroyGlobal')
    ->middleware('permission:admin.bans.delete')
    ->name('banmodel.destroy_global');

Route::delete('bans/value/{banValue}', 'BanValueController@destroy')
    ->middleware('permission:admin.bans.delete')
    ->name('banvalue.destroy')
    ->where('banValue', '[0-9]+');
Route::delete('bans/value', 'BanValueController@destroyGlobal')
    ->middleware('permission:admin.bans.delete')
    ->name('banvalue.destroy_global');

Route::get('bans/value/{banValue}/edit', 'BanValueController@edit')
    ->middleware('permission:admin.bans.edit')
    ->name('banvalue.edit')
    ->where('banValue', '[0-9]+');
Route::put('bans/value/{banValue}', 'BanValueController@update')
    ->middleware('permission:admin.bans.edit')
    ->name('banvalue.update')
    ->where('banValue', '[0-9]+');

Route::get('bans/value/{type}/create', 'BanValueController@create')
    ->middleware('permission:admin.bans.create')
    ->name('banvalue.create')
    ->where('type', '[A-Za-z]+');
Route::post('bans/value/{type}', 'BanValueController@store')
    ->middleware('permission:admin.bans.create')
    ->name('banvalue.store')
    ->where('type', '[A-Za-z]+');

Route::get('bans/user/{user}/create', 'BanModel\User\BanModelController@create')
    ->middleware('permission:admin.bans.create')
    ->name('banmodel.user.create')
    ->where('user', '[0-9]+');
Route::post('bans/user/{user}', 'BanModel\User\BanModelController@store')
    ->middleware('permission:admin.bans.create')
    ->name('banmodel.user.store')
    ->where('user', '[0-9]+');
