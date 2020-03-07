<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {
    Route::get('reports/comment/{comment}/create', 'Report\Comment\ReportController@create')
        ->name('report.comment.create')
        ->where('comment', '[0-9]+');
    Route::post('reports/comment/{comment}', 'Report\Comment\ReportController@store')
        ->name('report.comment.store')
        ->where('comment', '[0-9]+');
});
