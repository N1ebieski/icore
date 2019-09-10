<?php

use Illuminate\Support\Facades\Route;

Route::get('comments/post', 'Comment\Post\CommentController@index')
    ->name('comment.post.index')
    ->middleware('permission:index comments');

Route::get('comments/page', 'Comment\Page\CommentController@index')
    ->name('comment.page.index')
    ->middleware('permission:index comments');

Route::get('comments/{comment}', 'Comment\CommentController@show')
    ->name('comment.show')
    ->middleware('permission:index comments')
    ->where('comment', '[0-9]+');

Route::get('comments/post/{post}/create', 'Comment\Post\CommentController@create')
    ->name('comment.post.create')
    ->middleware('permission:create comments')
    ->where('post', '[0-9]+');
Route::post('comments/post/{post}', 'Comment\Post\CommentController@store')
    ->name('comment.post.store')
    ->middleware('permission:create comments')
    ->where('post', '[0-9]+');

Route::get('comments/page/{page}/create', 'Comment\Page\CommentController@create')
    ->name('comment.page.create')
    ->middleware('permission:create comments')
    ->where('page', '[0-9]+');
Route::post('comments/page/{page}', 'Comment\Page\CommentController@store')
    ->name('comment.page.store')
    ->middleware('permission:create comments')
    ->where('page', '[0-9]+');

Route::get('comments/{comment}/edit', 'Comment\CommentController@edit')
    ->name('comment.edit')
    ->middleware('permission:edit comments')
    ->where('comment', '[0-9]+');
Route::put('comments/{comment}', 'Comment\CommentController@update')
    ->name('comment.update')
    ->middleware('permission:edit comments')
    ->where('comment', '[0-9]+');

Route::patch('comments/{comment}/censored', 'Comment\CommentController@updateCensored')
    ->middleware('permission:status comments')
    ->name('comment.update_censored')
    ->where('comment', '[0-9]+');
Route::patch('comments/{comment}/status', 'Comment\CommentController@updateStatus')
    ->middleware('permission:status comments')
    ->name('comment.update_status')
    ->where('comment', '[0-9]+');

Route::delete('comments/{comment}', 'Comment\CommentController@destroy')
    ->middleware('permission:destroy comments')
    ->where('comment', '[0-9]+')
    ->name('comment.destroy');
Route::delete('comments', 'Comment\CommentController@destroyGlobal')
    ->middleware('permission:destroy comments')
    ->name('comment.destroy_global');
