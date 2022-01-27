<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Admin\PageController;

Route::match(['post', 'get'], 'pages/index', [PageController::class, 'index'])
    ->name('page.index')
    ->middleware('permission:admin.pages.view');

Route::get('pages/{page}/edit', [PageController::class, 'edit'])
    ->middleware('permission:admin.pages.edit')
    ->name('page.edit')
    ->where('page', '[0-9]+');
Route::put('pages/{page}', [PageController::class, 'update'])
    ->name('page.update')
    ->middleware('permission:admin.pages.edit')
    ->where('page', '[0-9]+');

Route::get('pages/{page}/edit/full', [PageController::class, 'editFull'])
    ->name('page.edit_full')
    ->middleware('permission:admin.pages.edit')
    ->where('page', '[0-9]+');
Route::put('pages/{page}/full', [PageController::class, 'updateFull'])
    ->name('page.update_full')
    ->middleware('permission:admin.pages.edit')
    ->where('page', '[0-9]+');

Route::get('pages/{page}/edit/position', [PageController::class, 'editPosition'])
    ->middleware('permission:admin.pages.edit')
    ->name('page.edit_position')
    ->where('page', '[0-9]+');
Route::patch('pages/{page}/position', [PageController::class, 'updatePosition'])
    ->name('page.update_position')
    ->middleware('permission:admin.pages.edit')
    ->where('page', '[0-9]+');

Route::patch('pages/{page}', [PageController::class, 'updateStatus'])
    ->name('page.update_status')
    ->middleware('permission:admin.pages.status')
    ->where('page', '[0-9]+');

Route::delete('pages/{page}', [PageController::class, 'destroy'])
    ->middleware('permission:admin.pages.delete')
    ->name('page.destroy')
    ->where('page', '[0-9]+');
Route::delete('pages', [PageController::class, 'destroyGlobal'])
    ->name('page.destroy_global')
    ->middleware('permission:admin.pages.delete');

Route::get('pages/create', [PageController::class, 'create'])
    ->name('page.create')
    ->middleware('permission:admin.pages.create');
Route::post('pages', [PageController::class, 'store'])
    ->name('page.store')
    ->middleware('permission:admin.pages.create');
