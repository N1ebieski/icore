<?php

namespace N1ebieski\ICore\Http\Controllers\Web;

use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use N1ebieski\ICore\Http\Requests\Web\Post\IndexRequest;
use N1ebieski\ICore\Http\Requests\Web\Post\ShowRequest;
use N1ebieski\ICore\Http\Requests\Web\Post\SearchRequest;
use Illuminate\View\View;
use N1ebieski\ICore\Filters\Web\Post\ShowFilter;

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
     * @return View
     */
    public function index(Post $post, IndexRequest $request) : View
    {
        $posts = $post->makeCache()->rememberLatest($request->get('page') ?? 1);

        return view('icore::web.post.index', [
            'posts' => $posts,
        ]);
    }

    /**
     * [show description]
     * @param  Post        $post    [description]
     * @param  Comment     $comment [description]
     * @param  ShowRequest $request [description]
     * @param  ShowFilter  $filter  [description]
     * @return View                 [description]
     */
    public function show(Post $post, Comment $comment, ShowRequest $request, ShowFilter $filter) : View
    {
        $comments = $comment->setMorph($post)->makeCache()->rememberRootsByFilter(
            $filter->all() + ['except' => $request->input('except')],
            $request->input('page') ?? 1
        );

        $postCache = $post->makeCache();

        return view('icore::web.post.show', [
            'post' => $post,
            'previous' => $postCache->rememberPrevious(),
            'next' => $postCache->rememberNext(),
            'related' => $postCache->rememberRelated(),
            'comments' => $comments,
            'filter' => $filter->all(),
            'catsAsArray' => array_merge(
                $post->categories->pluck('ancestors')->flatten()->pluck('id')->toArray(),
                $post->categories->pluck('id')->toArray()
            )
        ]);
    }

    /**
     * [search description]
     * @param  Post          $post    [description]
     * @param  SearchRequest $request [description]
     * @return View                   [description]
     */
    public function search(Post $post, SearchRequest $request) : View
    {
        return view('icore::web.post.search', [
            'posts' => $post->makeRepo()->paginateBySearch($request->get('search')),
            'search' => $request->get('search')
        ]);
    }
}
