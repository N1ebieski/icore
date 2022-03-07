<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Api\Post\PostController;

Route::group(['middleware' => 'auth:sanctum', 'permission:api.access'], function () {
    Route::match(['post', 'get'], '/posts/index', [PostController::class, 'index'])
        ->name('post.index')
        ->middleware(['permission:api.posts.view', 'ability:api.posts.view']);
});
