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

namespace N1ebieski\ICore\Http\Controllers\Web\Archive\Post;

use Carbon\Carbon;
use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Http\Requests\Web\Archive\IndexRequest;
use N1ebieski\ICore\Http\Controllers\Web\Archive\Post\Polymorphic;

class ArchiveController implements Polymorphic
{
    /**
     * Display a listing of the Archive Posts.
     *
     * @param  int          $month   [description]
     * @param  int          $year    [description]
     * @param  Post         $post    [description]
     * @param  IndexRequest $request [description]
     * @return HttpResponse          [description]
     */
    public function show(int $month, int $year, Post $post, IndexRequest $request): HttpResponse
    {
        return Response::view('icore::web.archive.post.show', [
            'posts' => $post->makeCache()->rememeberArchiveByDate($month, $year),
            'month' => $month,
            /** @phpstan-ignore-next-line */
            'month_localized' => Carbon::createFromFormat('d/m/Y', "1/{$month}/{$year}")
                ->locale(Config::get('app.locale'))
                ->isoFormat('MMMM'),
            'year' => $year,
        ]);
    }
}
