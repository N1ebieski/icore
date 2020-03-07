<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Tag\Post;

use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Http\Requests\Web\Tag\ShowRequest;

/**
 * [interface description]
 */
interface Polymorphic
{
    /**
     * Display a listing of the Posts for Tag.
     *
     * @param  Tag  $tag  [description]
     * @param  Post $post [description]
     * @param  ShowRequest $request
     * @return HttpResponse       [description]
     */
    public function show(Tag $tag, Post $post, ShowRequest $request) : HttpResponse;
}
