<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'posts/index', 'PostController@index')
    ->name('post.index')
    ->middleware('permission:index posts');

Route::get('posts/{post}/edit', 'PostController@edit')
    ->middleware('permission:edit posts')
    ->name('post.edit')
    ->where('post', '[0-9]+');
Route::put('posts/{post}', 'PostController@update')
    ->name('post.update')
    ->middleware('permission:edit posts')
    ->where('post', '[0-9]+');

// Full edit
Route::get('posts/{post}/edit/full', 'PostController@editFull')
    ->name('post.edit_full')
    ->middleware('permission:edit posts')
    ->where('post', '[0-9]+');
Route::put('posts/{post}/full', 'PostController@updateFull')
    ->name('post.update_full')
    ->middleware('permission:edit posts')
    ->where('post', '[0-9]+');

Route::patch('posts/{post}', 'PostController@updateStatus')
    ->name('post.update_status')
    ->middleware('permission:status posts')
    ->where('post', '[0-9]+');

Route::delete('posts/{post}', 'PostController@destroy')
    ->middleware('permission:destroy posts')
    ->name('post.destroy')
    ->where('post', '[0-9]+');
Route::delete('posts', 'PostController@destroyGlobal')
    ->name('post.destroy_global')
    ->middleware('permission:destroy posts');

Route::get('posts/create', 'PostController@create')
    ->name('post.create')
    ->middleware('permission:create posts');
Route::post('posts', 'PostController@store')
    ->name('post.store')
    ->middleware('permission:create posts');
