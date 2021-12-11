<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Web\Archive\Post\ArchiveController;

Route::get('archives/{month}/{year}/posts', [ArchiveController::class, 'show'])
    ->name('archive.post.show')
    ->where(['month' => '[0-9]+', 'year' => '[0-9]+']);
