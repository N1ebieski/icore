<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Web\Token\TokenController;

Route::group(['middleware' => ['auth', 'permission:api.access']], function () {
    Route::get('tokens/create', [TokenController::class, 'create'])
        ->middleware(['permission:web.tokens.create'])
        ->name('token.create');
    Route::post('tokens', [TokenController::class, 'store'])
        ->middleware(['permission:web.tokens.create'])
        ->name('token.store');

    Route::delete('tokens/{token}', [TokenController::class, 'destroy'])
        ->middleware(['permission:web.tokens.delete', 'can:delete,token'])
        ->name('token.destroy')
        ->where('token', '[0-9]+');
});
