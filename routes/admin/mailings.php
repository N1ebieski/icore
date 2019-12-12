<?php

use Illuminate\Support\Facades\Route;

Route::get('mailings', 'MailingController@index')
    ->name('mailing.index')
    ->middleware('permission:index mailings');

Route::get('mailings/{mailing}/edit', 'MailingController@edit')
    ->name('mailing.edit')
    ->middleware('permission:edit mailings')
    ->where('mailing', '[0-9]+');
Route::put('mailings/{mailing}', 'MailingController@update')
    ->name('mailing.update')
    ->middleware('permission:edit mailings')
    ->where('mailing', '[0-9]+');

Route::patch('mailings/{mailing}', 'MailingController@updateStatus')
    ->name('mailing.update_status')
    ->middleware('permission:status mailings')
    ->where('mailing', '[0-9]+');

Route::delete('mailings/{mailing}', 'MailingController@destroy')
    ->middleware('permission:destroy mailings')
    ->name('mailing.destroy')
    ->where('mailing', '[0-9]+');
Route::delete('mailings', 'MailingController@destroyGlobal')
    ->name('mailing.destroy_global')
    ->middleware('permission:destroy mailings');

Route::delete('mailings/{mailing}/reset', 'MailingController@reset')
    ->middleware('permission:destroy mailings')
    ->name('mailing.reset')
    ->where('mailing', '[0-9]+');

Route::get('mailings/create', 'MailingController@create')
    ->name('mailing.create')
    ->middleware('permission:create mailings');
Route::post('mailings', 'MailingController@store')
    ->name('mailing.store')
    ->middleware('permission:create mailings');
