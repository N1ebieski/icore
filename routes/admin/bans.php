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
use N1ebieski\ICore\Http\Controllers\Admin\BanValueController;
use N1ebieski\ICore\Http\Controllers\Admin\BanModel\BanModelController;
use N1ebieski\ICore\Http\Controllers\Admin\BanModel\User\BanModelController as UserBanModelController;

Route::match(['post', 'get'], 'bans/user/index', [UserBanModelController::class, 'index'])
    ->name('banmodel.user.index')
    ->middleware('permission:admin.bans.view');
Route::match(['post', 'get'], 'bans/{type}/index', [BanValueController::class, 'index'])
    ->name('banvalue.index')
    ->where('type', '[A-Za-z]+')
    ->middleware('permission:admin.bans.view');

Route::delete('bans/model/{banModel}', [BanModelController::class, 'destroy'])
    ->name('banmodel.destroy')
    ->where('banModel', '[0-9]+')
    ->middleware('permission:admin.bans.delete');
Route::delete('bans/model', [BanModelController::class, 'destroyGlobal'])
    ->name('banmodel.destroy_global')
    ->middleware('permission:admin.bans.delete');

Route::delete('bans/value/{banValue}', [BanValueController::class, 'destroy'])
    ->name('banvalue.destroy')
    ->where('banValue', '[0-9]+')
    ->middleware('permission:admin.bans.delete');
Route::delete('bans/value', [BanValueController::class, 'destroyGlobal'])
    ->name('banvalue.destroy_global')
    ->middleware('permission:admin.bans.delete');

Route::get('bans/value/{banValue}/edit', [BanValueController::class, 'edit'])
    ->name('banvalue.edit')
    ->where('banValue', '[0-9]+')
    ->middleware('permission:admin.bans.edit');
Route::put('bans/value/{banValue}', [BanValueController::class, 'update'])
    ->name('banvalue.update')
    ->where('banValue', '[0-9]+')
    ->middleware('permission:admin.bans.edit');

Route::get('bans/value/{type}/create', [BanValueController::class, 'create'])
    ->name('banvalue.create')
    ->where('type', '[A-Za-z]+')
    ->middleware('permission:admin.bans.create');
Route::post('bans/value/{type}', [BanValueController::class, 'store'])
    ->name('banvalue.store')
    ->where('type', '[A-Za-z]+')
    ->middleware('permission:admin.bans.create');

Route::get('bans/user/{user}/create', [UserBanModelController::class, 'create'])
    ->name('banmodel.user.create')
    ->where('user', '[0-9]+')
    ->middleware('permission:admin.bans.create');
Route::post('bans/user/{user}', [UserBanModelController::class, 'store'])
    ->name('banmodel.user.store')
    ->where('user', '[0-9]+')
    ->middleware('permission:admin.bans.create');
