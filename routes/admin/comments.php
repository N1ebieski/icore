<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'comments/post/index', 'Comment\Post\CommentController@index')
    ->name('comment.post.index')
    ->middleware('permission:admin.comments.view');

Route::match(['get', 'post'], 'comments/page/index', 'Comment\Page\CommentController@index')
    ->name('comment.page.index')
    ->middleware('permission:admin.comments.view');

Route::get('comments/{comment}', 'Comment\CommentController@show')
    ->name('comment.show')
    ->middleware('permission:admin.comments.view')
    ->where('comment', '[0-9]+');

Route::get('comments/post/{post}/create', 'Comment\Post\CommentController@create')
    ->name('comment.post.create')
    ->middleware('permission:admin.comments.create')
    ->where('post', '[0-9]+');
Route::post('comments/post/{post}', 'Comment\Post\CommentController@store')
    ->name('comment.post.store')
    ->middleware('permission:admin.comments.create')
    ->where('post', '[0-9]+');

Route::get('comments/page/{page}/create', 'Comment\Page\CommentController@create')
    ->name('comment.page.create')
    ->middleware('permission:admin.comments.create')
    ->where('page', '[0-9]+');
Route::post('comments/page/{page}', 'Comment\Page\CommentController@store')
    ->name('comment.page.store')
    ->middleware('permission:admin.comments.create')
    ->where('page', '[0-9]+');

Route::get('comments/{comment}/edit', 'Comment\CommentController@edit')
    ->name('comment.edit')
    ->middleware('permission:admin.comments.edit')
    ->where('comment', '[0-9]+');
Route::put('comments/{comment}', 'Comment\CommentController@update')
    ->name('comment.update')
    ->middleware('permission:admin.comments.edit')
    ->where('comment', '[0-9]+');

Route::patch('comments/{comment}/censored', 'Comment\CommentController@updateCensored')
    ->middleware('permission:admin.comments.status')
    ->name('comment.update_censored')
    ->where('comment', '[0-9]+');
Route::patch('comments/{comment}/status', 'Comment\CommentController@updateStatus')
    ->middleware('permission:admin.comments.status')
    ->name('comment.update_status')
    ->where('comment', '[0-9]+');

Route::delete('comments/{comment}', 'Comment\CommentController@destroy')
    ->middleware('permission:admin.comments.delete')
    ->where('comment', '[0-9]+')
    ->name('comment.destroy');
Route::delete('comments', 'Comment\CommentController@destroyGlobal')
    ->middleware('permission:admin.comments.delete')
    ->name('comment.destroy_global');
