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
use N1ebieski\ICore\Http\Controllers\Admin\RoleController;

Route::match(['post', 'get'], '/roles/index', [RoleController::class, 'index'])
    ->name('role.index')
    ->middleware('permission:admin.roles.view');

Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
    ->name('role.destroy')
    ->where('role', '[0-9]+')
    ->middleware('role:super-admin');

Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
    ->name('role.edit')
    ->where('role', '[0-9]+')
    ->middleware('role:super-admin');
Route::put('/roles/{role}', [RoleController::class, 'update'])
    ->name('role.update')
    ->where('role', '[0-9]+')
    ->middleware('role:super-admin');

Route::get('/roles/create', [RoleController::class, 'create'])
    ->name('role.create')
    ->middleware('role:super-admin');
Route::post('/roles', [RoleController::class, 'store'])
    ->name('role.store')
    ->middleware('role:super-admin');
