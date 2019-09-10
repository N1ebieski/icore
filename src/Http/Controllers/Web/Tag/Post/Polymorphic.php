<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Tag\Post;

use N1ebieski\ICore\Http\Requests\Web\Tag\ShowRequest;
use Illuminate\View\View;
use N1ebieski\ICore\Models\Tag;
use N1ebieski\ICore\Models\Post;

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
     * @return View       [description]
     */
    public function show(Tag $tag, Post $post, ShowRequest $request) : View;
}
