<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Web\Category\Post\CategoryController;

Route::get('categories/{category_post_cache}/posts', [CategoryController::class, 'show'])
    ->name('category.post.show')
    ->where('category_post_cache', '[0-9A-Za-z,_-]+');
