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
use N1ebieski\ICore\Http\Controllers\Admin\LinkController;

Route::match(['post', 'get'], 'links/{type}/index', [LinkController::class, 'index'])
    ->name('link.index')
    ->where('type', '[A-Za-z]+')
    ->middleware('permission:admin.links.view');

Route::delete('links/{link}', [LinkController::class, 'destroy'])
    ->name('link.destroy')
    ->where('link', '[0-9]+')
    ->middleware('permission:admin.links.delete');

Route::get('links/{link}/edit', [LinkController::class, 'edit'])
    ->name('link.edit')
    ->where('link', '[0-9]+')
    ->middleware('permission:admin.links.edit');
Route::put('links/{link}', [LinkController::class, 'update'])
    ->name('link.update')
    ->where('link', '[0-9]+')
    ->middleware('permission:admin.links.edit');

Route::get('links/{link}/edit/position', [LinkController::class, 'editPosition'])
    ->name('link.edit_position')
    ->where('link', '[0-9]+')
    ->middleware('permission:admin.links.edit');
Route::patch('links/{link}/position', [LinkController::class, 'updatePosition'])
    ->name('link.update_position')
    ->where('link', '[0-9]+')
    ->middleware('permission:admin.links.edit');

Route::get('links/{type}/create', [LinkController::class, 'create'])
    ->name('link.create')
    ->where('type', '[A-Za-z]+')
    ->middleware('permission:admin.links.create');
Route::post('links/{type}', [LinkController::class, 'store'])
    ->name('link.store')
    ->where('type', '[A-Za-z]+')
    ->middleware('permission:admin.links.create');
