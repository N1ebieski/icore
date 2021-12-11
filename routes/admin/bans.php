<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Admin\BanValueController;
use N1ebieski\ICore\Http\Controllers\Admin\BanModel\BanModelController;
use N1ebieski\ICore\Http\Controllers\Admin\BanModel\User\BanModelController as UserBanModelController;

Route::match(['get', 'post'], 'bans/user/index', [UserBanModelController::class, 'index'])
    ->name('banmodel.user.index')
    ->middleware('permission:admin.bans.view');
Route::match(['get', 'post'], 'bans/{type}/index', [BanValueController::class, 'index'])
    ->name('banvalue.index')
    ->middleware('permission:admin.bans.view')
    ->where('type', '[A-Za-z]+');

Route::delete('bans/model/{banModel}', [BanModelController::class, 'destroy'])
    ->middleware('permission:admin.bans.delete')
    ->name('banmodel.destroy')
    ->where('banModel', '[0-9]+');
Route::delete('bans/model', [BanModelController::class, 'destroyGlobal'])
    ->middleware('permission:admin.bans.delete')
    ->name('banmodel.destroy_global');

Route::delete('bans/value/{banValue}', [BanValueController::class, 'destroy'])
    ->middleware('permission:admin.bans.delete')
    ->name('banvalue.destroy')
    ->where('banValue', '[0-9]+');
Route::delete('bans/value', [BanValueController::class, 'destroyGlobal'])
    ->middleware('permission:admin.bans.delete')
    ->name('banvalue.destroy_global');

Route::get('bans/value/{banValue}/edit', [BanValueController::class, 'edit'])
    ->middleware('permission:admin.bans.edit')
    ->name('banvalue.edit')
    ->where('banValue', '[0-9]+');
Route::put('bans/value/{banValue}', [BanValueController::class, 'update'])
    ->middleware('permission:admin.bans.edit')
    ->name('banvalue.update')
    ->where('banValue', '[0-9]+');

Route::get('bans/value/{type}/create', [BanValueController::class, 'create'])
    ->middleware('permission:admin.bans.create')
    ->name('banvalue.create')
    ->where('type', '[A-Za-z]+');
Route::post('bans/value/{type}', [BanValueController::class, 'store'])
    ->middleware('permission:admin.bans.create')
    ->name('banvalue.store')
    ->where('type', '[A-Za-z]+');

Route::get('bans/user/{user}/create', [UserBanModelController::class, 'create'])
    ->middleware('permission:admin.bans.create')
    ->name('banmodel.user.create')
    ->where('user', '[0-9]+');
Route::post('bans/user/{user}', [UserBanModelController::class, 'store'])
    ->middleware('permission:admin.bans.create')
    ->name('banmodel.user.store')
    ->where('user', '[0-9]+');
