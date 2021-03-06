<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Archive\Post;

use Carbon\Carbon;
use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Http\Requests\Web\Archive\IndexRequest;
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
     * @return HttpResponse          [description]
     */
    public function show(int $month, int $year, Post $post, IndexRequest $request) : HttpResponse
    {
        return Response::view('icore::web.archive.post.show', [
            'posts' => $post->makeCache()->rememeberArchiveByDate($month, $year, $request->get('page') ?? 1),
            'month' => $month,
            'month_localized' => Carbon::createFromFormat('m', $month)->formatLocalized('%B'),
            'year' => $year,
        ]);
    }
}
