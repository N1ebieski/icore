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
use N1ebieski\ICore\Http\Controllers\Admin\UserController;

Route::match(['post', 'get'], '/users/index', [UserController::class, 'index'])
    ->name('user.index')
    ->middleware('permission:admin.users.view');

Route::patch('/users/{user}', [UserController::class, 'updateStatus'])
    ->name('user.update_status')
    ->where('user', '[0-9]+')
    ->middleware(['role:super-admin', 'can:actionSelf,user']);

Route::delete('/users/{user}', [UserController::class, 'destroy'])
    ->name('user.destroy')
    ->where('user', '[0-9]+')
    ->middleware(['role:super-admin', 'can:actionSelf,user']);
Route::delete('/users', [UserController::class, 'destroyGlobal'])
    ->name('user.destroy_global')
    ->middleware('role:super-admin');

Route::get('/users/{user}/edit', [UserController::class, 'edit'])
    ->name('user.edit')
    ->where('user', '[0-9]+')
    ->middleware(['role:super-admin', 'can:actionSelf,user']);
Route::put('/users/{user}', [UserController::class, 'update'])
    ->name('user.update')
    ->where('user', '[0-9]+')
    ->middleware(['role:super-admin', 'can:actionSelf,user']);

Route::get('/users/create', [UserController::class, 'create'])
    ->name('user.create')
    ->middleware('role:super-admin');
Route::post('/users', [UserController::class, 'store'])
    ->name('user.store')
    ->middleware('role:super-admin');
