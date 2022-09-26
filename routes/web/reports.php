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
use N1ebieski\ICore\Http\Controllers\Web\Report\Comment\ReportController;

Route::get('reports/comment/{comment}/create', [ReportController::class, 'create'])
    ->name('report.comment.create')
    ->where('comment', '[0-9]+');
Route::post('reports/comment/{comment}', [ReportController::class, 'store'])
    ->name('report.comment.store')
    ->where('comment', '[0-9]+');
