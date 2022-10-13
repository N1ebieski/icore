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

namespace N1ebieski\ICore\Http\Controllers\Web;

use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Filters\Web\Post\ShowFilter;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use N1ebieski\ICore\Http\Requests\Web\Post\ShowRequest;
use N1ebieski\ICore\Http\Requests\Web\Post\IndexRequest;
use N1ebieski\ICore\Http\Requests\Web\Post\SearchRequest;
use N1ebieski\ICore\Events\Web\Post\ShowEvent as PostShowEvent;
use N1ebieski\ICore\Events\Web\Post\IndexEvent as PostIndexEvent;
use N1ebieski\ICore\Events\Web\Post\SearchEvent as PostSearchEvent;

class PostController
{
    /**
     * Display a listing of the Posts.
     *
     * @param Post $post
     * @param IndexRequest $request
     * @return HttpResponse
     */
    public function index(Post $post, IndexRequest $request): HttpResponse
    {
        $posts = $post->makeCache()->rememberLatest();

        Event::dispatch(App::make(PostIndexEvent::class, ['posts' => $posts]));

        return Response::view('icore::web.post.index', [
            'posts' => $posts,
        ]);
    }

    /**
     * [show description]
     * @param  Post        $post    [description]
     * @param  Comment     $comment [description]
     * @param  ShowRequest $request [description]
     * @param  ShowFilter  $filter  [description]
     * @return HttpResponse                 [description]
     */
    public function show(
        Post $post,
        Comment $comment,
        ShowRequest $request,
        ShowFilter $filter
    ): HttpResponse {
        $postCache = $post->makeCache();

        Event::dispatch(App::make(PostShowEvent::class, ['post' => $post]));

        return Response::view('icore::web.post.show', [
            'post' => $post,
            'previous' => $postCache->rememberPrevious(),
            'next' => $postCache->rememberNext(),
            'related' => $postCache->rememberRelated(),
            'comments' => $post->comment->isActive() ?
                $comment->setRelations(['morph' => $post])
                    ->makeCache()
                    ->rememberRootsByFilter($filter->all())
                : null,
            'filter' => $filter->all(),
            'catsAsArray' => [
                'ancestors' => $post->categories->pluck('ancestors')->flatten()->pluck('id')->toArray(),
                'self' => $post->categories->pluck('id')->toArray()
            ]
        ]);
    }

    /**
     * [search description]
     * @param  Post          $post    [description]
     * @param  SearchRequest $request [description]
     * @return HttpResponse                   [description]
     */
    public function search(Post $post, SearchRequest $request): HttpResponse
    {
        $posts = $post->makeRepo()->paginateBySearch($request->get('search'));

        Event::dispatch(App::make(PostSearchEvent::class, ['posts' => $posts]));

        return Response::view('icore::web.post.search', [
            'posts' => $posts,
            'search' => $request->get('search')
        ]);
    }
}
