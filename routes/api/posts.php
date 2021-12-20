<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Api\Post\PostController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::match(['get', 'post'], '/posts/index', [PostController::class, 'index'])
        ->name('post.index')
        ->middleware(['permission:api.posts.view']);
});
