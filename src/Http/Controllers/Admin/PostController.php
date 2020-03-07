<?php

namespace N1ebieski\ICore\Http\Controllers\Admin;

use N1ebieski\ICore\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Filters\Admin\Post\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Post\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Post\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Post\CreateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Post\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Post\EditFullRequest;
use N1ebieski\ICore\Http\Requests\Admin\Post\UpdateFullRequest;
use N1ebieski\ICore\Http\Requests\Admin\Post\UpdateStatusRequest;
use N1ebieski\ICore\Http\Requests\Admin\Post\DestroyGlobalRequest;

/**
 * [PostController description]
 */
class PostController
{
    /**
     * Display a listing of the Post.
     *
     * @param Post $post
     * @param  Category        $category        [description]
     * @param  IndexRequest    $request         [description]
     * @param  IndexFilter     $filter          [description]
     * @return HttpResponse                     [description]
     */
    public function index(Post $post, Category $category, IndexRequest $request, IndexFilter $filter) : HttpResponse
    {
        return Response::view('icore::admin.post.index', [
            'posts' => $post->makeRepo()->paginateByFilter($filter->all() + [
                'except' => $request->input('except')
            ]),
            'categories' => $category->makeService()->getAsFlatTree(),
            'filter' => $filter->all(),
            'paginate' => Config::get('database.paginate')
        ]);
    }

    /**
     * Show the form for creating a new Post.
     *
     * @param  CreateRequest  $request  [description]
     * @return HttpResponse             [description]
     */
    public function create(CreateRequest $request) : HttpResponse
    {
        return Response::view('icore::admin.post.create', [
            'max_categories' => Config::get('icore.post.max_categories'),
            'max_tags' => Config::get('icore.post.max_tags'),
        ]);
    }

    /**
     * Store a newly created Post in storage.
     *
     * @param Post $post [description]
     * @param  StoreRequest     $request [description]
     * @return RedirectResponse          [description]
     */
    public function store(Post $post, StoreRequest $request) : RedirectResponse
    {
        $post->makeService()->create($request->all());

        return Response::redirectToRoute('admin.post.index')->with(
            'success',
            Lang::get('icore::posts.success.store')
        );
    }

    /**
     * Show the mini-form for editing the specified Post.
     *
     * @param  Post $post
     * @return JsonResponse
     */
    public function edit(Post $post) : JsonResponse
    {
        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.post.edit', [
                'post' => $post
            ])->render(),
        ]);
    }

    /**
     * Show the full-form for editing the specified Post.
     *
     * @param  Post     $post     [description]
     * @param  EditFullRequest  $request  [description]
     * @return HttpResponse               [description]
     */
    public function editFull(Post $post, EditFullRequest $request) : HttpResponse
    {
        return Response::view('icore::admin.post.edit_full', [
            'post' => $post,
            'max_categories' => Config::get('icore.post.max_categories'),
            'max_tags' => Config::get('icore.post.max_tags'),
        ]);
    }

    /**
     * Update Status attribute the specified Post in storage.
     *
     * @param  Post                $post    [description]
     * @param  UpdateStatusRequest $request [description]
     * @return JsonResponse                 [description]
     */
    public function updateStatus(Post $post, UpdateStatusRequest $request) : JsonResponse
    {
        $post->makeService()->updateStatus($request->only('status'));

        return Response::json([
            'success' => '',
            'status' => $post->status,
            'view' => View::make('icore::admin.post.partials.post', [
                'post' => $post
            ])->render(),
        ]);
    }

    /**
     * Full-Update the specified Post in storage.
     *
     * @param  Post              $post    [description]
     * @param  UpdateFullRequest $request [description]
     * @return RedirectResponse           [description]
     */
    public function updateFull(Post $post, UpdateFullRequest $request) : RedirectResponse
    {
        $post->makeService()->updateFull($request->all());

        return Response::redirectToRoute('admin.post.edit_full', ['post' => $post->id])
            ->with('success', Lang::get('icore::posts.success.update'));
    }

    /**
     * Mini-Update the specified Post in storage.
     *
     * @param  Post          $post    [description]
     * @param  UpdateRequest $request [description]
     * @return JsonResponse           [description]
     */
    public function update(Post $post, UpdateRequest $request) : JsonResponse
    {
        $post->makeService()->update($request->only(['title', 'content_html']));

        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.post.partials.post', [
                'post' => $post
            ])->render()
        ]);
    }

    /**
     * Remove the specified Post from storage.
     *
     * @param  Post         $post [description]
     * @return JsonResponse       [description]
     */
    public function destroy(Post $post) : JsonResponse
    {
        $post->makeService()->delete();

        return Response::json(['success' => '']);
    }

    /**
     * Remove the collection of Posts from storage.
     *
     * @param  Post                 $post    [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(Post $post, DestroyGlobalRequest $request) : RedirectResponse
    {
        $deleted = $post->makeService()->deleteGlobal($request->get('select'));

        return Response::redirectTo(URL::previous())->with(
            'success',
            Lang::get('icore::posts.success.destroy_global', ['affected' => $deleted])
        );
    }
}
