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

namespace N1ebieski\ICore\Http\Controllers\Web\Tag\Post;

use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Http\Requests\Web\Tag\ShowRequest;
use N1ebieski\ICore\Http\Controllers\Web\Tag\Post\Polymorphic;
use N1ebieski\ICore\Events\Web\Tag\Post\ShowEvent as TagShowEvent;

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
    public function show(Tag $tag, Post $post, ShowRequest $request): HttpResponse
    {
        $posts = $post->makeRepo()->paginateByTag($tag);

        Event::dispatch(App::make(TagShowEvent::class, ['posts' => $posts]));

        return Response::view('icore::web.tag.post.show', [
            'tag' => $tag,
            'posts' => $posts,
        ]);
    }
}
