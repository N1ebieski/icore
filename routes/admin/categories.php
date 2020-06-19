<?php

use Illuminate\Support\Facades\Route;

Route::get('categories/link/search', 'Category\CategoryController@search')
    ->middleware('permission:admin.categories.view')
    ->name('category.link.search');

Route::get('categories/post/search', 'Category\Post\CategoryController@search')
    ->middleware(['permission:admin.posts.create|admin.posts.edit'])
    ->name('category.post.search');

Route::match(['get', 'post'], 'categories/post/index', 'Category\Post\CategoryController@index')
    ->name('category.post.index')
    ->middleware('permission:admin.categories.view');

Route::patch('categories/{category}', 'Category\CategoryController@updateStatus')
    ->middleware('permission:admin.categories.status')
    ->name('category.update_status')
    ->where('category', '[0-9]+');

Route::get('categories/{category}/edit', 'Category\CategoryController@edit')
    ->middleware('permission:admin.categories.edit')
    ->name('category.edit')
    ->where('category', '[0-9]+');
Route::put('categories/{category}', 'Category\CategoryController@update')
    ->middleware('permission:admin.categories.edit')
    ->name('category.update')
    ->where('category', '[0-9]+');

Route::get('categories/{category}/edit/position', 'Category\CategoryController@editPosition')
    ->middleware('permission:admin.categories.edit')
    ->name('category.edit_position')
    ->where('category', '[0-9]+');
Route::patch('categories/{category}/position', 'Category\CategoryController@updatePosition')
    ->name('category.update_position')
    ->middleware('permission:admin.categories.edit')
    ->where('category', '[0-9]+');

Route::get('categories/post/create', 'Category\Post\CategoryController@create')
    ->name('category.post.create')
    ->middleware('permission:admin.categories.create');
Route::post('categories/post', 'Category\Post\CategoryController@store')
    ->name('category.post.store')
    ->middleware('permission:admin.categories.create');
Route::post('categories/post/json', 'Category\Post\CategoryController@storeGlobal')
    ->name('category.post.store_global')
    ->middleware('permission:admin.categories.create');

Route::delete('categories/{category}', 'Category\CategoryController@destroy')
    ->middleware('permission:admin.categories.delete')
    ->name('category.destroy')
    ->where('category', '[0-9]+');
Route::delete('categories', 'Category\CategoryController@destroyGlobal')
    ->middleware('permission:admin.categories.delete')
    ->name('category.destroy_global');
