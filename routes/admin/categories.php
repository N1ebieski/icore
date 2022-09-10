<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Admin\Category\CategoryController;
use N1ebieski\ICore\Http\Controllers\Admin\Category\Post\CategoryController as PostCategoryController;

Route::match(['post', 'get'], 'categories/post/index', [PostCategoryController::class, 'index'])
    ->name('category.post.index')
    ->middleware('permission:admin.categories.view');

Route::patch('categories/{category}', [CategoryController::class, 'updateStatus'])
    ->name('category.update_status')
    ->where('category', '[0-9]+')
    ->middleware('permission:admin.categories.status');

Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])
    ->name('category.edit')
    ->where('category', '[0-9]+')
    ->middleware('permission:admin.categories.edit');
Route::put('categories/{category}', [CategoryController::class, 'update'])
    ->name('category.update')
    ->where('category', '[0-9]+')
    ->middleware('permission:admin.categories.edit');

Route::get('categories/{category}/edit/position', [CategoryController::class, 'editPosition'])
    ->name('category.edit_position')
    ->where('category', '[0-9]+')
    ->middleware('permission:admin.categories.edit');
Route::patch('categories/{category}/position', [CategoryController::class, 'updatePosition'])
    ->name('category.update_position')
    ->where('category', '[0-9]+')
    ->middleware('permission:admin.categories.edit');

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
    ->name('category.destroy')
    ->where('category', '[0-9]+')
    ->middleware('permission:admin.categories.delete');
Route::delete('categories', [CategoryController::class, 'destroyGlobal'])
    ->name('category.destroy_global')
    ->middleware('permission:admin.categories.delete');
