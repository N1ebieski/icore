<?php

use Illuminate\Support\Facades\Route;

Route::get('contact', 'ContactController@show')
    ->name('contact.show');
Route::post('contact', 'ContactController@send');
