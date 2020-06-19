<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'posts/index', 'PostController@index')
    ->name('post.index')
    ->middleware('permission:admin.posts.view');

Route::get('posts/{post}/edit', 'PostController@edit')
    ->middleware('permission:admin.posts.edit')
    ->name('post.edit')
    ->where('post', '[0-9]+');
Route::put('posts/{post}', 'PostController@update')
    ->name('post.update')
    ->middleware('permission:admin.posts.edit')
    ->where('post', '[0-9]+');

// Full edit
Route::get('posts/{post}/edit/full', 'PostController@editFull')
    ->name('post.edit_full')
    ->middleware('permission:admin.posts.edit')
    ->where('post', '[0-9]+');
Route::put('posts/{post}/full', 'PostController@updateFull')
    ->name('post.update_full')
    ->middleware('permission:admin.posts.edit')
    ->where('post', '[0-9]+');

Route::patch('posts/{post}', 'PostController@updateStatus')
    ->name('post.update_status')
    ->middleware('permission:admin.posts.status')
    ->where('post', '[0-9]+');

Route::delete('posts/{post}', 'PostController@destroy')
    ->middleware('permission:admin.posts.delete')
    ->name('post.destroy')
    ->where('post', '[0-9]+');
Route::delete('posts', 'PostController@destroyGlobal')
    ->name('post.destroy_global')
    ->middleware('permission:admin.posts.delete');

Route::get('posts/create', 'PostController@create')
    ->name('post.create')
    ->middleware('permission:admin.posts.create');
Route::post('posts', 'PostController@store')
    ->name('post.store')
    ->middleware('permission:admin.posts.create');
