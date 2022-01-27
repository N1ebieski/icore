<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Api\Category\Post\CategoryController as PostCategoryController;

Route::match(['post', 'get'], 'categories/post/index', [PostCategoryController::class, 'index'])
    ->name('category.post.index');
