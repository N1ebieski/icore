<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Web\SearchController;

Route::get('search', [SearchController::class, 'index'])
    ->name('search.index');
