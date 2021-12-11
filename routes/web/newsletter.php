<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Web\NewsletterController;

Route::post('newsletters', [NewsletterController::class, 'store'])
    ->name('newsletter.store');

Route::get('newsletters/{newsletter}/status', [NewsletterController::class, 'updateStatus'])
    ->name('newsletter.update_status')
    ->where('newsletter', '[0-9]+');
