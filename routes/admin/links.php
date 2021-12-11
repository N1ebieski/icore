<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Admin\LinkController;

Route::match(['get', 'post'], 'links/{type}/index', [LinkController::class, 'index'])
    ->name('link.index')
    ->middleware('permission:admin.links.view')
    ->where('type', '[A-Za-z]+');

Route::delete('links/{link}', [LinkController::class, 'destroy'])
    ->middleware('permission:admin.links.delete')
    ->name('link.destroy')
    ->where('link', '[0-9]+');

Route::get('links/{link}/edit', [LinkController::class, 'edit'])
    ->middleware('permission:admin.links.edit')
    ->name('link.edit')
    ->where('link', '[0-9]+');
Route::put('links/{link}', [LinkController::class, 'update'])
    ->middleware('permission:admin.links.edit')
    ->name('link.update')
    ->where('link', '[0-9]+');

Route::get('links/{link}/edit/position', [LinkController::class, 'editPosition'])
    ->middleware('permission:admin.links.edit')
    ->name('link.edit_position')
    ->where('link', '[0-9]+');
Route::patch('links/{link}/position', [LinkController::class, 'updatePosition'])
    ->name('link.update_position')
    ->middleware('permission:admin.links.edit')
    ->where('link', '[0-9]+');

Route::get('links/{type}/create', [LinkController::class, 'create'])
    ->middleware('permission:admin.links.create')
    ->name('link.create')
    ->where('type', '[A-Za-z]+');
Route::post('links/{type}', [LinkController::class, 'store'])
    ->middleware('permission:admin.links.create')
    ->name('link.store')
    ->where('type', '[A-Za-z]+');
