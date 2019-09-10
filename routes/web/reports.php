<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function() {
    Route::get('reports/comment/{comment_active}/create', 'Report\Comment\ReportController@create')
        ->name('report.comment.create')
        ->where('comment_active', '[0-9]+');
    Route::post('reports/comment/{comment_active}', 'Report\Comment\ReportController@store')
        ->name('report.comment.store')
        ->where('comment_active', '[0-9]+');
});
