<?php

use Illuminate\Support\Facades\Route;

Route::get('search', 'SearchController@index')
    ->name('search.index');
