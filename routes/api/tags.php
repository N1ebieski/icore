<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'tags/index', 'Tag\TagController@index')
    ->name('tag.index');
