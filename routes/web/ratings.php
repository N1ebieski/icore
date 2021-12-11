<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Web\Rating\Comment\RatingController;

Route::group(['middleware' => 'auth'], function () {
    Route::get('ratings/comment/{comment}/rate', [RatingController::class, 'rate'])
        ->name('rating.comment.rate')
        ->where('comment', '[0-9]+');
});
