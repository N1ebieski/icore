<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\CommentController;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\Page\CommentController as PageCommentController;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\Post\CommentController as PostCommentController;

Route::match(['get', 'post'], 'comments/post/index', [PostCommentController::class, 'index'])
    ->name('comment.post.index')
    ->middleware('permission:admin.comments.view');

Route::match(['get', 'post'], 'comments/page/index', [PageCommentController::class, 'index'])
    ->name('comment.page.index')
    ->middleware('permission:admin.comments.view');

Route::get('comments/{comment}', [CommentController::class, 'show'])
    ->name('comment.show')
    ->middleware('permission:admin.comments.view')
    ->where('comment', '[0-9]+');

Route::get('comments/post/{post}/create', [PostCommentController::class, 'create'])
    ->name('comment.post.create')
    ->middleware('permission:admin.comments.create')
    ->where('post', '[0-9]+');
Route::post('comments/post/{post}', [PostCommentController::class, 'store'])
    ->name('comment.post.store')
    ->middleware('permission:admin.comments.create')
    ->where('post', '[0-9]+');

Route::get('comments/page/{page}/create', [PageCommentController::class, 'create'])
    ->name('comment.page.create')
    ->middleware('permission:admin.comments.create')
    ->where('page', '[0-9]+');
Route::post('comments/page/{page}', [PageCommentController::class, 'store'])
    ->name('comment.page.store')
    ->middleware('permission:admin.comments.create')
    ->where('page', '[0-9]+');

Route::get('comments/{comment}/edit', [CommentController::class, 'edit'])
    ->name('comment.edit')
    ->middleware('permission:admin.comments.edit')
    ->where('comment', '[0-9]+');
Route::put('comments/{comment}', [CommentController::class, 'update'])
    ->name('comment.update')
    ->middleware('permission:admin.comments.edit')
    ->where('comment', '[0-9]+');

Route::patch('comments/{comment}/censored', [CommentController::class, 'updateCensored'])
    ->middleware('permission:admin.comments.status')
    ->name('comment.update_censored')
    ->where('comment', '[0-9]+');
Route::patch('comments/{comment}/status', [CommentController::class, 'updateStatus'])
    ->middleware('permission:admin.comments.status')
    ->name('comment.update_status')
    ->where('comment', '[0-9]+');

Route::delete('comments/{comment}', [CommentController::class, 'destroy'])
    ->middleware('permission:admin.comments.delete')
    ->where('comment', '[0-9]+')
    ->name('comment.destroy');
Route::delete('comments', [CommentController::class, 'destroyGlobal'])
    ->middleware('permission:admin.comments.delete')
    ->name('comment.destroy_global');
