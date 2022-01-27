<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Admin\MailingController;

Route::match(['post', 'get'], 'mailings/index', [MailingController::class, 'index'])
    ->name('mailing.index')
    ->middleware('permission:admin.mailings.view');

Route::get('mailings/{mailing}/edit', [MailingController::class, 'edit'])
    ->name('mailing.edit')
    ->middleware('permission:admin.mailings.edit')
    ->where('mailing', '[0-9]+');
Route::put('mailings/{mailing}', [MailingController::class, 'update'])
    ->name('mailing.update')
    ->middleware('permission:admin.mailings.edit')
    ->where('mailing', '[0-9]+');

Route::patch('mailings/{mailing}', [MailingController::class, 'updateStatus'])
    ->name('mailing.update_status')
    ->middleware('permission:admin.mailings.status')
    ->where('mailing', '[0-9]+');

Route::delete('mailings/{mailing}', [MailingController::class, 'destroy'])
    ->middleware('permission:admin.mailings.delete')
    ->name('mailing.destroy')
    ->where('mailing', '[0-9]+');
Route::delete('mailings', [MailingController::class, 'destroyGlobal'])
    ->name('mailing.destroy_global')
    ->middleware('permission:admin.mailings.delete');

Route::delete('mailings/{mailing}/reset', [MailingController::class, 'reset'])
    ->middleware('permission:admin.mailings.delete')
    ->name('mailing.reset')
    ->where('mailing', '[0-9]+');

Route::get('mailings/create', [MailingController::class, 'create'])
    ->name('mailing.create')
    ->middleware('permission:admin.mailings.create');
Route::post('mailings', [MailingController::class, 'store'])
    ->name('mailing.store')
    ->middleware('permission:admin.mailings.create');
