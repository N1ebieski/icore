<?php

use Illuminate\Support\Facades\Route;

Route::post('comments/{comment}/take', 'Comment\CommentController@take')
    ->name('comment.take')
    ->where('comment', '[0-9]+');

Route::group(['middleware' => 'auth'], function () {
    Route::get('comments/post/{post}/create', 'Comment\Post\CommentController@create')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.create|web.comments.suggest'])
        ->name('comment.post.create')
        ->where('post', '[0-9]+');
    Route::post('comments/post/{post}', 'Comment\Post\CommentController@store')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.create|web.comments.suggest'])
        ->name('comment.post.store')
        ->where('post', '[0-9]+');

    Route::get('comments/page/{page}/create', 'Comment\Page\CommentController@create')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.create|web.comments.suggest'])
        ->name('comment.page.create')
        ->where('page', '[0-9]+');
    Route::post('comments/page/{page}', 'Comment\Page\CommentController@store')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.create|web.comments.suggest'])
        ->name('comment.page.store')
        ->where('page', '[0-9]+');

    Route::get('comments/{comment}/edit', 'Comment\CommentController@edit')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.edit', 'can:update,comment'])
        ->name('comment.edit')
        ->where('comment', '[0-9]+');
    Route::put('comments/{comment}', 'Comment\CommentController@update')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.edit', 'can:update,comment'])
        ->name('comment.update')
        ->where('comment', '[0-9]+');
});
