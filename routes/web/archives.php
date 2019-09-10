<?php

use Illuminate\Support\Facades\Route;

Route::get('archives/{month}/{year}/posts', 'Archive\Post\ArchiveController@show')
    ->name('archive.post.show')
    ->where(['month' => '[0-9]+', 'year' => '[0-9]+']);
