<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Web\Profile\ProfileController;
use N1ebieski\ICore\Http\Controllers\Web\Profile\SocialiteController;

Route::group(['middleware' => 'auth'], function () {
    Route::get('profile/socialites', [ProfileController::class, 'socialites'])
        ->name('profile.socialites');

    Route::get('symlink/{provider}', [SocialiteController::class, 'redirect'])
        ->name('profile.socialite.redirect')
        ->where('provider', '[A-Za-z]+');
    Route::get('symlink/{provider}/callback', [SocialiteController::class, 'callback'])
        ->name('profile.socialite.callback')
        ->where('provider', '[A-Za-z]+');

    Route::delete('symlink/socialites/{socialite}', [SocialiteController::class, 'destroy'])
        ->name('profile.socialite.destroy')
        ->middleware('can:delete,socialite')
        ->where('socialite', '[0-9]+');

    Route::get('profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::get('profile/edit/password', [ProfileController::class, 'redirectPassword'])
        ->name('profile.redirect_password')
        ->middleware('verified');

    Route::patch('profile/email', [ProfileController::class, 'updateEmail'])
        ->name('profile.update_email');

    Route::match(['post', 'get'], 'profile/tokens', [ProfileController::class, 'tokens'])
        ->name('profile.tokens')
        ->middleware(['permission:api.access', 'permission:web.tokens.create|web.tokens.delete']);
});
