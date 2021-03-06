<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'pages/index', 'PageController@index')
    ->name('page.index')
    ->middleware('permission:admin.pages.view');

Route::get('pages/{page}/edit', 'PageController@edit')
    ->middleware('permission:admin.pages.edit')
    ->name('page.edit')
    ->where('page', '[0-9]+');
Route::put('pages/{page}', 'PageController@update')
    ->name('page.update')
    ->middleware('permission:admin.pages.edit')
    ->where('page', '[0-9]+');

// Full edit
Route::get('pages/{page}/edit/full', 'PageController@editFull')
    ->name('page.edit_full')
    ->middleware('permission:admin.pages.edit')
    ->where('page', '[0-9]+');
Route::put('pages/{page}/full', 'PageController@updateFull')
    ->name('page.update_full')
    ->middleware('permission:admin.pages.edit')
    ->where('page', '[0-9]+');

Route::get('pages/{page}/edit/position', 'PageController@editPosition')
    ->middleware('permission:admin.pages.edit')
    ->name('page.edit_position')
    ->where('page', '[0-9]+');
Route::patch('pages/{page}/position', 'PageController@updatePosition')
    ->name('page.update_position')
    ->middleware('permission:admin.pages.edit')
    ->where('page', '[0-9]+');

Route::patch('pages/{page}', 'PageController@updateStatus')
    ->name('page.update_status')
    ->middleware('permission:admin.pages.status')
    ->where('page', '[0-9]+');

Route::delete('pages/{page}', 'PageController@destroy')
    ->middleware('permission:admin.pages.delete')
    ->name('page.destroy')
    ->where('page', '[0-9]+');
Route::delete('pages', 'PageController@destroyGlobal')
    ->name('page.destroy_global')
    ->middleware('permission:admin.pages.delete');

Route::get('pages/create', 'PageController@create')
    ->name('page.create')
    ->middleware('permission:admin.pages.create');
Route::post('pages', 'PageController@store')
    ->name('page.store')
    ->middleware('permission:admin.pages.create');
