<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Web\Tag\Post\TagController;

Route::get('tags/{tag_cache}/posts', [TagController::class, 'show'])
    ->name('tag.post.show')
    ->where('tag_cache', '[0-9A-Za-z,_-]+');
