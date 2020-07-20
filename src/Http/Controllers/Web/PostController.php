<?php

namespace N1ebieski\ICore\Http\Controllers\Web;

use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Filters\Web\Post\ShowFilter;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use N1ebieski\ICore\Http\Requests\Web\Post\ShowRequest;
use N1ebieski\ICore\Http\Requests\Web\Post\IndexRequest;
use N1ebieski\ICore\Http\Requests\Web\Post\SearchRequest;

/**
 * [PostController description]
 */
class PostController
{
    /**
     * Display a listing of the Posts.
     *
     * @param Post $post
     * @param IndexRequest $request
     * @return HttpResponse
     */
    public function index(Post $post, IndexRequest $request) : HttpResponse
    {
        return Response::view('icore::web.post.index', [
            'posts' => $post->makeCache()->rememberLatest($request->get('page') ?? 1),
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
    public function show(Post $post, Comment $comment, ShowRequest $request, ShowFilter $filter) : HttpResponse
    {
        $postCache = $post->makeCache();

        return Response::view('icore::web.post.show', [
            'post' => $post,
            'previous' => $postCache->rememberPrevious(),
            'next' => $postCache->rememberNext(),
            'related' => $postCache->rememberRelated(),
            'comments' => (bool)$post->comment === true ?
                $comment->setMorph($post)->makeCache()->rememberRootsByFilter(
                    $filter->all() + ['except' => $request->input('except')],
                    $request->input('page') ?? 1
                )
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
    public function search(Post $post, SearchRequest $request) : HttpResponse
    {
        return Response::view('icore::web.post.search', [
            'posts' => $post->makeRepo()->paginateBySearch($request->get('search')),
            'search' => $request->get('search')
        ]);
    }
}
