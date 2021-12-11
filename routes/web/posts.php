<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Web\PostController;

Route::match(['get', 'post'], 'posts/index', [PostController::class, 'index'])
    ->name('post.index');

Route::match(['get', 'post'], 'posts/search', [PostController::class, 'search'])
    ->name('post.search');

Route::match(['get', 'post'], 'posts/{post_cache}', [PostController::class, 'show'])
    ->name('post.show')
    ->where('post_cache', '[0-9A-Za-z,_-]+');
