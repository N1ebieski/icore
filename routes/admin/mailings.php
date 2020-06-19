<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'mailings/index', 'MailingController@index')
    ->name('mailing.index')
    ->middleware('permission:admin.mailings.view');

Route::get('mailings/{mailing}/edit', 'MailingController@edit')
    ->name('mailing.edit')
    ->middleware('permission:admin.mailings.edit')
    ->where('mailing', '[0-9]+');
Route::put('mailings/{mailing}', 'MailingController@update')
    ->name('mailing.update')
    ->middleware('permission:admin.mailings.edit')
    ->where('mailing', '[0-9]+');

Route::patch('mailings/{mailing}', 'MailingController@updateStatus')
    ->name('mailing.update_status')
    ->middleware('permission:admin.mailings.status')
    ->where('mailing', '[0-9]+');

Route::delete('mailings/{mailing}', 'MailingController@destroy')
    ->middleware('permission:admin.mailings.delete')
    ->name('mailing.destroy')
    ->where('mailing', '[0-9]+');
Route::delete('mailings', 'MailingController@destroyGlobal')
    ->name('mailing.destroy_global')
    ->middleware('permission:admin.mailings.delete');

Route::delete('mailings/{mailing}/reset', 'MailingController@reset')
    ->middleware('permission:admin.mailings.delete')
    ->name('mailing.reset')
    ->where('mailing', '[0-9]+');

Route::get('mailings/create', 'MailingController@create')
    ->name('mailing.create')
    ->middleware('permission:admin.mailings.create');
Route::post('mailings', 'MailingController@store')
    ->name('mailing.store')
    ->middleware('permission:admin.mailings.create');
