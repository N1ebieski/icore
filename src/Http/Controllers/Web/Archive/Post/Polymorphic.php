<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Archive\Post;

use N1ebieski\ICore\Models\Post;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Http\Requests\Web\Archive\IndexRequest;

/**
 * [interface description]
 */
interface Polymorphic
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
    public function show(int $month, int $year, Post $post, IndexRequest $request) : HttpResponse;
}
