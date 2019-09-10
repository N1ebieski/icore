<?php

use Illuminate\Support\Facades\Route;

Route::post('newsletters', 'NewsletterController@store')
    ->name('newsletter.store');
Route::get('newsletters/{newsletter}', 'NewsletterController@updateStatus')
    ->name('newsletter.update_status')
    ->where('newsletter', '[0-9]+');
