<?php

use Illuminate\Support\Facades\Route;

Route::get('tags/{tag_cache}/posts', 'Tag\Post\TagController@show')
    ->name('tag.post.show')
    ->where('tag_cache', '[0-9A-Za-z,_-]+');
