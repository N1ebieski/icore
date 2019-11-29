<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Archive\Post;

use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Http\Requests\Web\Archive\IndexRequest;
use Illuminate\View\View;
use Carbon\Carbon;
use N1ebieski\ICore\Http\Controllers\Web\Archive\Post\Polymorphic;

/**
 * [ArchiveController description]
 */
class ArchiveController implements Polymorphic
{
    /**
     * Display a listing of the Archive Posts.
     *
     * @param  int          $month   [description]
     * @param  int          $year    [description]
     * @param  Post         $post    [description]
     * @param  IndexRequest $request [description]
     * @return View                  [description]
     */
    public function show(int $month, int $year, Post $post, IndexRequest $request) : View
    {
        return view('icore::web.archive.show', [
            'posts' => $post->makeCache()->rememeberArchiveByDate($month, $year, $request->get('page') ?? 1),
            'month' => $month,
            'month_localized' => Carbon::createFromFormat('m', $month)->formatLocalized('%B'),
            'year' => $year,
        ]);
    }
}
