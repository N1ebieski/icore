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
use N1ebieski\ICore\Http\Controllers\Api\Token\TokenController;

Route::group(['middleware' => ['auth:sanctum', 'permission:api.access']], function () {
    Route::post('tokens', [TokenController::class, 'store'])
        ->name('token.store')
        ->middleware(['permission:api.tokens.create', 'ability:api.tokens.create']);

    Route::delete('tokens/{token}', [TokenController::class, 'destroy'])
        ->name('token.destroy')
        ->where('token', '[0-9]+')
        ->middleware(['permission:api.tokens.delete', 'ability:api.tokens.delete', 'can:delete,token']);
});
