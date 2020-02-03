<?php

use Illuminate\Support\Facades\Route;

Route::get('posts/index', 'PostController@index')
    ->name('post.index');
Route::get('posts/search', 'PostController@search')
    ->name('post.search');
Route::match(['get', 'post'], 'posts/{post_cache}', 'PostController@show')
    ->name('post.show')
    ->where('post_cache', '[0-9A-Za-z,_-]+');
