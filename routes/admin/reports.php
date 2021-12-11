<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Admin\Report\Comment\ReportController as CommentReportController;

Route::get('reports/comment/{comment}', [CommentReportController::class, 'show'])
    ->middleware('permission:admin.comments.view')
    ->name('report.comment.show')
    ->where('comment', '[0-9]+');
Route::delete('reports/comment/{comment}/clear', [CommentReportController::class, 'clear'])
    ->middleware('permission:admin.comments.edit')
    ->name('report.comment.clear')
    ->where('comment', '[0-9]+');
