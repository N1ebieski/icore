<?php

use Illuminate\Support\Facades\Route;

Route::get('pages/{page_cache}', 'PageController@show')
    ->name('page.show')
    ->where('page_cache', '[0-9A-Za-z,_-]+');
