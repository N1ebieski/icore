<?php

use Illuminate\Support\Facades\Route;

Route::get('categories/{category_cache}/posts', 'Category\Post\CategoryController@show')
    ->name('category.post.show')
    ->where('category_cache', '[0-9A-Za-z,_-]+');
