<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Tag\Post;

use N1ebieski\ICore\Http\Requests\Web\Tag\ShowRequest;
use Illuminate\View\View;
use N1ebieski\ICore\Models\Tag;
use N1ebieski\ICore\Models\Post;
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
     * @return View       [description]
     */
    public function show(Tag $tag, Post $post, ShowRequest $request) : View
    {
        return view('icore::web.tag.show', [
            'tag' => $tag,
            'posts' => $post->getCache()->rememberByTag($tag, $request->get('page') ?? 1),
        ]);
    }
}
