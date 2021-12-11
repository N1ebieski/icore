<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Web\Report\Comment\ReportController;

Route::group(['middleware' => 'auth'], function () {
    Route::get('reports/comment/{comment}/create', [ReportController::class, 'create'])
        ->name('report.comment.create')
        ->where('comment', '[0-9]+');
    Route::post('reports/comment/{comment}', [ReportController::class, 'store'])
        ->name('report.comment.store')
        ->where('comment', '[0-9]+');
});
