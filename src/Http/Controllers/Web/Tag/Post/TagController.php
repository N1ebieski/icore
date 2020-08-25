<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Tag\Post;

use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Http\Requests\Web\Tag\ShowRequest;
use N1ebieski\ICore\Http\Controllers\Web\Tag\Post\Polymorphic;

/**
 * [TagController description]
 */
class TagController implements Polymorphic
{
    /**
     * Display a listing of the Posts for Tag.
     *
     * @param  Tag  $tag  [description]
     * @param  Post $post [description]
     * @param  ShowRequest $request
     * @return HttpResponse       [description]
     */
    public function show(Tag $tag, Post $post, ShowRequest $request) : HttpResponse
    {
        return Response::view('icore::web.tag.post.show', [
            'tag' => $tag,
            'posts' => $post->makeRepo()->paginateByTag($tag),
        ]);
    }
}
