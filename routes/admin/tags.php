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
use N1ebieski\ICore\Http\Controllers\Admin\Tag\TagController;

Route::match(['post', 'get'], 'tags/index', [TagController::class, 'index'])
    ->name('tag.index')
    ->middleware('permission:admin.tags.view');

Route::get('tags/{tag}/edit', [TagController::class, 'edit'])
    ->name('tag.edit')
    ->where('tag', '[0-9]+')
    ->middleware('permission:admin.tags.edit');
Route::put('tags/{tag}', [TagController::class, 'update'])
    ->name('tag.update')
    ->where('tag', '[0-9]+')
    ->middleware('permission:admin.tags.edit');

Route::get('tags/create', [TagController::class, 'create'])
    ->name('tag.create')
    ->middleware('permission:admin.tags.create');
Route::post('tags', [TagController::class, 'store'])
    ->name('tag.store')
    ->middleware('permission:admin.tags.create');

Route::delete('tags/{tag}', [TagController::class, 'destroy'])
    ->name('tag.destroy')
    ->where('tag', '[0-9]+')
    ->middleware('permission:admin.tags.delete');
Route::delete('tags', [TagController::class, 'destroyGlobal'])
    ->name('tag.destroy_global')
    ->middleware('permission:admin.tags.delete');
