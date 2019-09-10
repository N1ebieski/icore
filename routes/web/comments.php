<?php

use Illuminate\Support\Facades\Route;

Route::post('comments/{comment_active}/take', 'Comment\CommentController@take')
    ->name('comment.take')
    ->where('comment_active', '[0-9]+');

Route::group(['middleware' => 'auth'], function() {
    Route::get('comments/post/{post_active}/create', 'Comment\Post\CommentController@create')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:create comments|suggest comments'])
        ->name('comment.post.create')
        ->where('post_active', '[0-9]+');
    Route::post('comments/post/{post_active}', 'Comment\Post\CommentController@store')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:create comments|suggest comments'])
        ->name('comment.post.store')
        ->where('post_active', '[0-9]+');

    Route::get('comments/page/{page_active}/create', 'Comment\Page\CommentController@create')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:create comments|suggest comments'])
        ->name('comment.page.create')
        ->where('page_active', '[0-9]+');
    Route::post('comments/page/{page_active}', 'Comment\Page\CommentController@store')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:create comments|suggest comments'])
        ->name('comment.page.store')
        ->where('page_active', '[0-9]+');

    Route::get('comments/{comment_active}/edit', 'Comment\CommentController@edit')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'can:update,comment_active'])
        ->name('comment.edit')
        ->where('comment_active', '[0-9]+');
    Route::put('comments/{comment_active}', 'Comment\CommentController@update')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'can:update,comment_active'])
        ->name('comment.update')
        ->where('comment_active', '[0-9]+');
});
