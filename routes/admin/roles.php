<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Admin\RoleController;

Route::match(['post', 'get'], '/roles/index', [RoleController::class, 'index'])
    ->name('role.index')
    ->middleware('permission:admin.roles.view');

Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
    ->middleware('role:super-admin')
    ->name('role.destroy')
    ->where('role', '[0-9]+');

Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
    ->middleware('role:super-admin')
    ->name('role.edit')
    ->where('role', '[0-9]+');
Route::put('/roles/{role}', [RoleController::class, 'update'])
    ->middleware('role:super-admin')
    ->name('role.update')
    ->where('role', '[0-9]+');

Route::get('/roles/create', [RoleController::class, 'create'])
    ->middleware('role:super-admin')
    ->name('role.create');
Route::post('/roles', [RoleController::class, 'store'])
    ->middleware('role:super-admin')
    ->name('role.store');
