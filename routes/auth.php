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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Auth\SocialiteController;

Route::group(['namespace' => 'N1ebieski\ICore\Http\Controllers'], function () {
    Auth::routes(['verify' => true]);
});

Route::get('login/{provider}', [SocialiteController::class, 'redirect'])
    ->name('auth.socialite.redirect')
    ->middleware('icore.guest');
Route::get('login/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('auth.socialite.callback')
    ->middleware('icore.guest');
