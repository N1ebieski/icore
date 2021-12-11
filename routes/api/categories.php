<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Api\Category\CategoryController;
use N1ebieski\ICore\Http\Controllers\Api\Category\Post\CategoryController as PostCategoryController;

Route::match(['get', 'post'], 'categories/index', [CategoryController::class, 'index'])
    ->name('category.index');

Route::match(['get', 'post'], 'categories/post/index', [PostCategoryController::class, 'index'])
    ->name('category.post.index');
