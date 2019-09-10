<?php

use Illuminate\Support\Facades\Route;

Route::get('reports/comment/{comment}', 'Report\Comment\ReportController@show')
    ->middleware('permission:index comments')
    ->name('report.comment.show')
    ->where('comment', '[0-9]+');
Route::delete('reports/comment/{comment}/clear',  'Report\Comment\ReportController@clear')
    ->middleware('permission:edit comments')
    ->name('report.comment.clear')
    ->where('comment', '[0-9]+');
