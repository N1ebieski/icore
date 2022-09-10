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
use N1ebieski\ICore\Http\Controllers\Admin\PageController;

Route::match(['post', 'get'], 'pages/index', [PageController::class, 'index'])
    ->name('page.index')
    ->middleware('permission:admin.pages.view');

Route::get('pages/{page}/edit', [PageController::class, 'edit'])
    ->name('page.edit')
    ->where('page', '[0-9]+')
    ->middleware('permission:admin.pages.edit');
Route::put('pages/{page}', [PageController::class, 'update'])
    ->name('page.update')
    ->where('page', '[0-9]+')
    ->middleware('permission:admin.pages.edit');

Route::get('pages/{page}/edit/full', [PageController::class, 'editFull'])
    ->name('page.edit_full')
    ->where('page', '[0-9]+')
    ->middleware('permission:admin.pages.edit');
Route::put('pages/{page}/full', [PageController::class, 'updateFull'])
    ->name('page.update_full')
    ->where('page', '[0-9]+')
    ->middleware('permission:admin.pages.edit');

Route::get('pages/{page}/edit/position', [PageController::class, 'editPosition'])
    ->name('page.edit_position')
    ->where('page', '[0-9]+')
    ->middleware('permission:admin.pages.edit');
Route::patch('pages/{page}/position', [PageController::class, 'updatePosition'])
    ->name('page.update_position')
    ->where('page', '[0-9]+')
    ->middleware('permission:admin.pages.edit');

Route::patch('pages/{page}', [PageController::class, 'updateStatus'])
    ->name('page.update_status')
    ->where('page', '[0-9]+')
    ->middleware('permission:admin.pages.status');

Route::delete('pages/{page}', [PageController::class, 'destroy'])
    ->name('page.destroy')
    ->where('page', '[0-9]+')
    ->middleware('permission:admin.pages.delete');
Route::delete('pages', [PageController::class, 'destroyGlobal'])
    ->name('page.destroy_global')
    ->middleware('permission:admin.pages.delete');

Route::get('pages/create', [PageController::class, 'create'])
    ->name('page.create')
    ->middleware('permission:admin.pages.create');
Route::post('pages', [PageController::class, 'store'])
    ->name('page.store')
    ->middleware('permission:admin.pages.create');
