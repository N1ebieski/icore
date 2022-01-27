<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Admin\Category\CategoryController;
use N1ebieski\ICore\Http\Controllers\Admin\Category\Post\CategoryController as PostCategoryController;

Route::match(['post', 'get'], 'categories/post/index', [PostCategoryController::class, 'index'])
    ->name('category.post.index')
    ->middleware('permission:admin.categories.view');

Route::patch('categories/{category}', [CategoryController::class, 'updateStatus'])
    ->middleware('permission:admin.categories.status')
    ->name('category.update_status')
    ->where('category', '[0-9]+');

Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])
    ->middleware('permission:admin.categories.edit')
    ->name('category.edit')
    ->where('category', '[0-9]+');
Route::put('categories/{category}', [CategoryController::class, 'update'])
    ->middleware('permission:admin.categories.edit')
    ->name('category.update')
    ->where('category', '[0-9]+');

Route::get('categories/{category}/edit/position', [CategoryController::class, 'editPosition'])
    ->middleware('permission:admin.categories.edit')
    ->name('category.edit_position')
    ->where('category', '[0-9]+');
Route::patch('categories/{category}/position', [CategoryController::class, 'updatePosition'])
    ->name('category.update_position')
    ->middleware('permission:admin.categories.edit')
    ->where('category', '[0-9]+');

Route::get('categories/post/create', [PostCategoryController::class, 'create'])
    ->name('category.post.create')
    ->middleware('permission:admin.categories.create');
Route::post('categories/post', [PostCategoryController::class, 'store'])
    ->name('category.post.store')
    ->middleware('permission:admin.categories.create');
Route::post('categories/post/json', [PostCategoryController::class, 'storeGlobal'])
    ->name('category.post.store_global')
    ->middleware('permission:admin.categories.create');

Route::delete('categories/{category}', [CategoryController::class, 'destroy'])
    ->middleware('permission:admin.categories.delete')
    ->name('category.destroy')
    ->where('category', '[0-9]+');
Route::delete('categories', [CategoryController::class, 'destroyGlobal'])
    ->middleware('permission:admin.categories.delete')
    ->name('category.destroy_global');
