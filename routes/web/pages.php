<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Web\PageController;

Route::match(['get', 'post'], 'pages/{page_cache}', [PageController::class, 'show'])
    ->name('page.show')
    ->where('page_cache', '[0-9A-Za-z,_-]+');
