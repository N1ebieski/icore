<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'tags/index', 'Tag\TagController@index')
    ->name('tag.index')
    ->middleware('permission:admin.tags.view');

Route::get('tags/{tag}/edit', 'Tag\TagController@edit')
    ->middleware('permission:admin.tags.edit')
    ->name('tag.edit')
    ->where('tag', '[0-9]+');
Route::put('tags/{tag}', 'Tag\TagController@update')
    ->middleware('permission:admin.tags.edit')
    ->name('tag.update')
    ->where('tag', '[0-9]+');

Route::get('tags/create', 'Tag\TagController@create')
    ->name('tag.create')
    ->middleware('permission:admin.tags.create');
Route::post('tags', 'Tag\TagController@store')
    ->name('tag.store')
    ->middleware('permission:admin.tags.create');

Route::delete('tags/{tag}', 'Tag\TagController@destroy')
    ->middleware('permission:admin.tags.delete')
    ->name('tag.destroy')
    ->where('tag', '[0-9]+');
Route::delete('tags', 'Tag\TagController@destroyGlobal')
    ->name('tag.destroy_global')
    ->middleware('permission:admin.tags.delete');
