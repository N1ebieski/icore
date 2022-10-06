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
use N1ebieski\ICore\Http\Controllers\Admin\MailingController;

Route::match(['post', 'get'], 'mailings/index', [MailingController::class, 'index'])
    ->name('mailing.index')
    ->middleware('permission:admin.mailings.view');

Route::get('mailings/{mailing}/edit', [MailingController::class, 'edit'])
    ->name('mailing.edit')
    ->where('mailing', '[0-9]+')
    ->middleware('permission:admin.mailings.edit');
Route::put('mailings/{mailing}', [MailingController::class, 'update'])
    ->name('mailing.update')
    ->where('mailing', '[0-9]+')
    ->middleware('permission:admin.mailings.edit');

Route::patch('mailings/{mailing}', [MailingController::class, 'updateStatus'])
    ->name('mailing.update_status')
    ->where('mailing', '[0-9]+')
    ->middleware('permission:admin.mailings.status');

Route::delete('mailings/{mailing}', [MailingController::class, 'destroy'])
    ->name('mailing.destroy')
    ->where('mailing', '[0-9]+')
    ->middleware('permission:admin.mailings.delete');
Route::delete('mailings', [MailingController::class, 'destroyGlobal'])
    ->name('mailing.destroy_global')
    ->middleware('permission:admin.mailings.delete');

Route::delete('mailings/{mailing}/reset', [MailingController::class, 'reset'])
    ->name('mailing.reset')
    ->where('mailing', '[0-9]+')
    ->middleware('permission:admin.mailings.delete');

Route::get('mailings/create', [MailingController::class, 'create'])
    ->name('mailing.create')
    ->middleware('permission:admin.mailings.create');
Route::post('mailings', [MailingController::class, 'store'])
    ->name('mailing.store')
    ->middleware('permission:admin.mailings.create');
