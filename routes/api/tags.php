<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Api\Tag\TagController;

Route::match(['post', 'get'], 'tags/index', [TagController::class, 'index'])
    ->name('tag.index');
