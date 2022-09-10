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
use N1ebieski\ICore\Http\Controllers\Web\Comment\CommentController;
use N1ebieski\ICore\Http\Controllers\Web\Comment\Page\CommentController as PageCommentController;
use N1ebieski\ICore\Http\Controllers\Web\Comment\Post\CommentController as PostCommentController;

Route::post('comments/{comment}/take', [CommentController::class, 'take'])
    ->name('comment.take')
    ->where('comment', '[0-9]+');

Route::group(['middleware' => 'auth'], function () {
    Route::get('comments/post/{post}/create', [PostCommentController::class, 'create'])
        ->name('comment.post.create')
        ->where('post', '[0-9]+')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.create|web.comments.suggest']);
    Route::post('comments/post/{post}', [PostCommentController::class, 'store'])
        ->name('comment.post.store')
        ->where('post', '[0-9]+')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.create|web.comments.suggest']);

    Route::get('comments/page/{page}/create', [PageCommentController::class, 'create'])
        ->name('comment.page.create')
        ->where('page', '[0-9]+')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.create|web.comments.suggest']);
    Route::post('comments/page/{page}', [PageCommentController::class, 'store'])
        ->name('comment.page.store')
        ->where('page', '[0-9]+')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.create|web.comments.suggest']);

    Route::get('comments/{comment}/edit', [CommentController::class, 'edit'])
        ->name('comment.edit')
        ->where('comment', '[0-9]+')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.edit', 'can:update,comment']);
    Route::put('comments/{comment}', [CommentController::class, 'update'])
        ->name('comment.update')
        ->where('comment', '[0-9]+')
        ->middleware(['icore.ban.user', 'icore.ban.ip', 'permission:web.comments.edit', 'can:update,comment']);
});
