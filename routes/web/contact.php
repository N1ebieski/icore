<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Web\ContactController;

Route::get('contact', [ContactController::class, 'show'])
    ->name('contact.show');
Route::post('contact', [ContactController::class, 'send'])
    ->name('contact.send');
