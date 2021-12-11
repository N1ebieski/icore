<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Auth\LoginController;
use N1ebieski\ICore\Http\Controllers\Auth\SocialiteController;

Route::group(['namespace' => 'N1ebieski\ICore\Http\Controllers'], function () {
    Auth::routes(['verify' => true]);
});

Route::get('logout', [LoginController::class, 'logout'])
    ->name('logout');

Route::get('login/{provider}', [SocialiteController::class, 'redirect'])
    ->middleware('icore.guest')
    ->name('auth.socialite.redirect');
Route::get('login/{provider}/callback', [SocialiteController::class, 'callback'])
    ->middleware('icore.guest')
    ->name('auth.socialite.callback');
