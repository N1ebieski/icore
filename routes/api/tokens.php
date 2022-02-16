<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Api\Token\TokenController;

Route::group(['middleware' => ['auth:sanctum', 'permission:api.access']], function () {
    Route::post('tokens', [TokenController::class, 'store'])
        ->middleware(['permission:api.tokens.create', 'ability:api.tokens.create'])
        ->name('token.store');

    Route::delete('tokens/{token}', [TokenController::class, 'destroy'])
        ->middleware(['permission:api.tokens.delete', 'ability:api.tokens.delete', 'can:delete,token'])
        ->name('token.destroy')
        ->where('token', '[0-9]+');
});
