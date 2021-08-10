<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'categories/index', 'Category\CategoryController@index')
    ->name('category.index');

Route::match(['get', 'post'], 'categories/post/index', 'Category\Post\CategoryController@index')
    ->name('category.post.index');
