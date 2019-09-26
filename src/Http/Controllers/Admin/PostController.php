<?php

namespace N1ebieski\ICore\Http\Controllers\Admin;

use N1ebieski\ICore\Filters\Admin\Post\IndexFilter;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Http\Requests\Admin\Post\CreateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Post\EditFullRequest;
use N1ebieski\ICore\Http\Requests\Admin\Post\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Post\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Post\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Post\UpdateFullRequest;
use N1ebieski\ICore\Http\Requests\Admin\Post\UpdateStatusRequest;
use N1ebieski\ICore\Http\Requests\Admin\Post\DestroyGlobalRequest;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

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
     * @return View                             [description]
     */
    public function index(Post $post, Category $category, IndexRequest $request, IndexFilter $filter) : View
    {
        $posts = $post->getRepo()->paginateByFilter($filter->all());

        return view('icore::admin.post.index', [
            'posts' => $posts,
            'categories' => $category->getService()->getAsFlatTree(),
            'filter' => $filter->all(),
            'paginate' => config('database.paginate')
        ]);
    }

    /**
     * Show the form for creating a new Post.
     *
     * @param  CreateRequest  $request  [description]
     * @return View               [description]
     */
    public function create(CreateRequest $request) : View
    {
        return view('icore::admin.post.create', [
            'max_categories' => config('icore.post.max_categories'),
            'max_tags' => config('icore.post.max_tags'),
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
        $post->getService()->create($request->all());

        return redirect()->route('admin.post.index')->with('success', trans('icore::posts.success.store') );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the mini-form for editing the specified Post.
     *
     * @param  Post $post
     * @return JsonResponse
     */
    public function edit(Post $post) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.post.edit', ['post' => $post])->render(),
        ]);
    }

    /**
     * Show the full-form for editing the specified Post.
     *
     * @param  Post     $post     [description]
     * @param  EditFullRequest  $request  [description]
     * @return View               [description]
     */
    public function editFull(Post $post, EditFullRequest $request) : View
    {
        return view('icore::admin.post.edit_full', [
            'post' => $post,
            'max_categories' => config('icore.post.max_categories'),
            'max_tags' => config('icore.post.max_tags'),
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
        $post->getService()->updateStatus($request->only('status'));

        return response()->json([
            'success' => '',
            'status' => $post->status,
            'view' => view('icore::admin.post.post', ['post' => $post])->render(),
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
        $post->getService()->updateFull($request->all());

        return redirect()->route('admin.post.edit_full', ['post' => $post->id])
            ->with('success', trans('icore::posts.success.update') );
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
        $post->getService()->update($request->only(['title', 'content_html']));

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.post.post', ['post' => $post])->render()
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
        $post->getService()->delete();

        return response()->json(['success' => '']);
    }

    /**
     * Remove the collection of Posts from storage.
     *
     * @param  Post                 $post    [description]
     * @param  Comment              $comment [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(Post $post, Comment $comment, DestroyGlobalRequest $request) : RedirectResponse
    {
        $deleted = $post->getService()->deleteGlobal($request->get('select'));

        return redirect()->back()->with('success', trans('icore::posts.success.destroy_global', ['affected' => $deleted]));
    }
}
