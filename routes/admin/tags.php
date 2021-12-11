<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Admin\Tag\TagController;

Route::match(['get', 'post'], 'tags/index', [TagController::class, 'index'])
    ->name('tag.index')
    ->middleware('permission:admin.tags.view');

Route::get('tags/{tag}/edit', [TagController::class, 'edit'])
    ->middleware('permission:admin.tags.edit')
    ->name('tag.edit')
    ->where('tag', '[0-9]+');
Route::put('tags/{tag}', [TagController::class, 'update'])
    ->middleware('permission:admin.tags.edit')
    ->name('tag.update')
    ->where('tag', '[0-9]+');

Route::get('tags/create', [TagController::class, 'create'])
    ->name('tag.create')
    ->middleware('permission:admin.tags.create');
Route::post('tags', [TagController::class, 'store'])
    ->name('tag.store')
    ->middleware('permission:admin.tags.create');

Route::delete('tags/{tag}', [TagController::class, 'destroy'])
    ->middleware('permission:admin.tags.delete')
    ->name('tag.destroy')
    ->where('tag', '[0-9]+');
Route::delete('tags', [TagController::class, 'destroyGlobal'])
    ->name('tag.destroy_global')
    ->middleware('permission:admin.tags.delete');
