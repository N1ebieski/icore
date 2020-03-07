<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {
    Route::get('ratings/comment/{comment}/rate', 'Rating\Comment\RatingController@rate')
        ->name('rating.comment.rate')
        ->where('comment', '[0-9]+');
});
