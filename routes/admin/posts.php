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
use N1ebieski\ICore\Http\Controllers\Admin\PostController;

Route::match(['post', 'get'], 'posts/index', [PostController::class, 'index'])
    ->name('post.index')
    ->middleware('permission:admin.posts.view');

Route::get('posts/{post}/edit', [PostController::class, 'edit'])
    ->name('post.edit')
    ->where('post', '[0-9]+')
    ->middleware('permission:admin.posts.edit');
Route::put('posts/{post}', [PostController::class, 'update'])
    ->name('post.update')
    ->where('post', '[0-9]+')
    ->middleware('permission:admin.posts.edit');

Route::get('posts/{post}/edit/full', [PostController::class, 'editFull'])
    ->name('post.edit_full')
    ->where('post', '[0-9]+')
    ->middleware('permission:admin.posts.edit');
Route::put('posts/{post}/full', [PostController::class, 'updateFull'])
    ->name('post.update_full')
    ->where('post', '[0-9]+')
    ->middleware('permission:admin.posts.edit');

Route::patch('posts/{post}', [PostController::class, 'updateStatus'])
    ->name('post.update_status')
    ->where('post', '[0-9]+')
    ->middleware('permission:admin.posts.status');

Route::delete('posts/{post}', [PostController::class, 'destroy'])
    ->name('post.destroy')
    ->where('post', '[0-9]+')
    ->middleware('permission:admin.posts.delete');
Route::delete('posts', [PostController::class, 'destroyGlobal'])
    ->name('post.destroy_global')
    ->middleware('permission:admin.posts.delete');

Route::get('posts/create', [PostController::class, 'create'])
    ->name('post.create')
    ->middleware('permission:admin.posts.create');
Route::post('posts', [PostController::class, 'store'])
    ->name('post.store')
    ->middleware('permission:admin.posts.create');
