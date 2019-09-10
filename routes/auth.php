<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
dd('dsadsa');
/**
 * Login Routes
 */
Auth::routes(['verify' => true]);
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('login/{provider}', 'Auth\SocialiteController@redirect')
    ->middleware('icore.guest')
    ->name('auth.socialite.redirect');
Route::get('login/{provider}/callback', 'Auth\SocialiteController@callback')
    ->middleware('icore.guest')
    ->name('auth.socialite.callback');
