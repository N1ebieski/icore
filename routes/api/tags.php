<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Api\Tag\TagController;

Route::match(['get', 'post'], 'tags/index', [TagController::class, 'index'])
    ->name('tag.index');
