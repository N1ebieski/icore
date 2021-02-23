<?php

use Illuminate\Support\Facades\Route;

Route::get('file-manager', 'FileManagerController@index')
    ->name('filemanager.index');
