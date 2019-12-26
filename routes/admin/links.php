<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'links/{type}/index', 'LinkController@index')
    ->name('link.index')
    ->middleware('permission:index links')
    ->where('type', '[A-Za-z]+');

Route::delete('links/{link}', 'LinkController@destroy')
    ->middleware('permission:destroy links')
    ->name('link.destroy')
    ->where('link', '[0-9]+');

Route::get('links/{link}/edit', 'LinkController@edit')
    ->middleware('permission:edit links')
    ->name('link.edit')
    ->where('link', '[0-9]+');
Route::put('links/{link}', 'LinkController@update')
    ->middleware('permission:edit links')
    ->name('link.update')
    ->where('link', '[0-9]+');

Route::get('links/{link}/edit/position', 'LinkController@editPosition')
    ->middleware('permission:edit links')
    ->name('link.edit_position')
    ->where('link', '[0-9]+');
Route::patch('links/{link}/position', 'LinkController@updatePosition')
    ->name('link.update_position')
    ->middleware('permission:edit links')
    ->where('link', '[0-9]+');

Route::get('links/{type}/create', 'LinkController@create')
    ->middleware('permission:create links')
    ->name('link.create')
    ->where('type', '[A-Za-z]+');
Route::post('links/{type}', 'LinkController@store')
    ->middleware('permission:create links')
    ->name('link.store')
    ->where('type', '[A-Za-z]+');
