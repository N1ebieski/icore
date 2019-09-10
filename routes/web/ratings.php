<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function() {
    Route::get('ratings/comment/{comment_active}/rate', 'Rating\Comment\RatingController@rate')
        ->name('rating.comment.rate')
        ->where('comment_active', '[0-9]+');
});
