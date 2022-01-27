<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Admin\UserController;

Route::match(['post', 'get'], '/users/index', [UserController::class, 'index'])
    ->name('user.index')
    ->middleware('permission:admin.users.view');

Route::patch('/users/{user}', [UserController::class, 'updateStatus'])
    ->middleware(['role:super-admin', 'can:actionSelf,user'])
    ->name('user.update_status')
    ->where('user', '[0-9]+');

Route::delete('/users/{user}', [UserController::class, 'destroy'])
    ->middleware(['role:super-admin', 'can:actionSelf,user'])
    ->name('user.destroy')
    ->where('user', '[0-9]+');
Route::delete('/users', [UserController::class, 'destroyGlobal'])
    ->middleware('role:super-admin')
    ->name('user.destroy_global');

Route::get('/users/{user}/edit', [UserController::class, 'edit'])
    ->middleware(['role:super-admin', 'can:actionSelf,user'])
    ->name('user.edit')
    ->where('user', '[0-9]+');
Route::put('/users/{user}', [UserController::class, 'update'])
    ->middleware(['role:super-admin', 'can:actionSelf,user'])
    ->name('user.update')
    ->where('user', '[0-9]+');

Route::get('/users/create', [UserController::class, 'create'])
    ->middleware('role:super-admin')
    ->name('user.create');
Route::post('/users', [UserController::class, 'store'])
    ->middleware('role:super-admin')
    ->name('user.store');
