<?php

use Illuminate\Support\Facades\Route;

Route::get('search/autocomplete', 'SearchController@autocomplete')
    ->name('search.autocomplete');

Route::get('search', 'SearchController@index')
    ->name('search.index');
