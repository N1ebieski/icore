<?php

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Web\Comment\CommentController;
use N1ebieski\ICore\Http\Controllers\Web\Comment\Page\CommentController as PageCommentController;
use N1ebieski\ICore\Http\Controllers\Web\Comment\Post\CommentController as PostCommentController;

Route::post('comments/{comment}/take', [CommentController::class, 'take'])
    ->name('comment.take')
    ->where('comment', '[0-9]+');

Route::group(['middleware' => 'auth'], function () {
    Route::get('comments/post/{post}/create', [PostCommentController::class, 'create'])
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.create|web.comments.suggest'])
        ->name('comment.post.create')
        ->where('post', '[0-9]+');
    Route::post('comments/post/{post}', [PostCommentController::class, 'store'])
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.create|web.comments.suggest'])
        ->name('comment.post.store')
        ->where('post', '[0-9]+');

    Route::get('comments/page/{page}/create', [PageCommentController::class, 'create'])
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.create|web.comments.suggest'])
        ->name('comment.page.create')
        ->where('page', '[0-9]+');
    Route::post('comments/page/{page}', [PageCommentController::class, 'store'])
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.create|web.comments.suggest'])
        ->name('comment.page.store')
        ->where('page', '[0-9]+');

    Route::get('comments/{comment}/edit', [CommentController::class, 'edit'])
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.edit', 'can:update,comment'])
        ->name('comment.edit')
        ->where('comment', '[0-9]+');
    Route::put('comments/{comment}', [CommentController::class, 'update'])
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.edit', 'can:update,comment'])
        ->name('comment.update')
        ->where('comment', '[0-9]+');
});
