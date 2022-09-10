<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

use Illuminate\Support\Facades\Route;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\CommentController;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\Page\CommentController as PageCommentController;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\Post\CommentController as PostCommentController;

Route::match(['post', 'get'], 'comments/post/index', [PostCommentController::class, 'index'])
    ->name('comment.post.index')
    ->middleware('permission:admin.comments.view');

Route::match(['post', 'get'], 'comments/page/index', [PageCommentController::class, 'index'])
    ->name('comment.page.index')
    ->middleware('permission:admin.comments.view');

Route::get('comments/{comment}', [CommentController::class, 'show'])
    ->name('comment.show')
    ->where('comment', '[0-9]+')
    ->middleware('permission:admin.comments.view');

Route::get('comments/post/{post}/create', [PostCommentController::class, 'create'])
    ->name('comment.post.create')
    ->where('post', '[0-9]+')
    ->middleware('permission:admin.comments.create');
Route::post('comments/post/{post}', [PostCommentController::class, 'store'])
    ->name('comment.post.store')
    ->where('post', '[0-9]+')
    ->middleware('permission:admin.comments.create');

Route::get('comments/page/{page}/create', [PageCommentController::class, 'create'])
    ->name('comment.page.create')
    ->where('page', '[0-9]+')
    ->middleware('permission:admin.comments.create');
Route::post('comments/page/{page}', [PageCommentController::class, 'store'])
    ->name('comment.page.store')
    ->where('page', '[0-9]+')
    ->middleware('permission:admin.comments.create');

Route::get('comments/{comment}/edit', [CommentController::class, 'edit'])
    ->name('comment.edit')
    ->where('comment', '[0-9]+')
    ->middleware('permission:admin.comments.edit');
Route::put('comments/{comment}', [CommentController::class, 'update'])
    ->name('comment.update')
    ->where('comment', '[0-9]+')
    ->middleware('permission:admin.comments.edit');

Route::patch('comments/{comment}/censored', [CommentController::class, 'updateCensored'])
    ->name('comment.update_censored')
    ->where('comment', '[0-9]+')
    ->middleware('permission:admin.comments.status');
Route::patch('comments/{comment}/status', [CommentController::class, 'updateStatus'])
    ->name('comment.update_status')
    ->where('comment', '[0-9]+')
    ->middleware('permission:admin.comments.status');

Route::delete('comments/{comment}', [CommentController::class, 'destroy'])
    ->where('comment', '[0-9]+')
    ->name('comment.destroy')
    ->middleware('permission:admin.comments.delete');
Route::delete('comments', [CommentController::class, 'destroyGlobal'])
    ->name('comment.destroy_global')
    ->middleware('permission:admin.comments.delete');
