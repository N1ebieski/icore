<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Admin\PostController;

Route::match(['post', 'get'], 'posts/index', [PostController::class, 'index'])
    ->name('post.index')
    ->middleware('permission:admin.posts.view');

Route::get('posts/{post}/edit', [PostController::class, 'edit'])
    ->middleware('permission:admin.posts.edit')
    ->name('post.edit')
    ->where('post', '[0-9]+');
Route::put('posts/{post}', [PostController::class, 'update'])
    ->name('post.update')
    ->middleware('permission:admin.posts.edit')
    ->where('post', '[0-9]+');

Route::get('posts/{post}/edit/full', [PostController::class, 'editFull'])
    ->name('post.edit_full')
    ->middleware('permission:admin.posts.edit')
    ->where('post', '[0-9]+');
Route::put('posts/{post}/full', [PostController::class, 'updateFull'])
    ->name('post.update_full')
    ->middleware('permission:admin.posts.edit')
    ->where('post', '[0-9]+');

Route::patch('posts/{post}', [PostController::class, 'updateStatus'])
    ->name('post.update_status')
    ->middleware('permission:admin.posts.status')
    ->where('post', '[0-9]+');

Route::delete('posts/{post}', [PostController::class, 'destroy'])
    ->middleware('permission:admin.posts.delete')
    ->name('post.destroy')
    ->where('post', '[0-9]+');
Route::delete('posts', [PostController::class, 'destroyGlobal'])
    ->name('post.destroy_global')
    ->middleware('permission:admin.posts.delete');

Route::get('posts/create', [PostController::class, 'create'])
    ->name('post.create')
    ->middleware('permission:admin.posts.create');
Route::post('posts', [PostController::class, 'store'])
    ->name('post.store')
    ->middleware('permission:admin.posts.create');
