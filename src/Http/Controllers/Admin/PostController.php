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

namespace N1ebieski\ICore\Http\Controllers\Admin;

use N1ebieski\ICore\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
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
use N1ebieski\ICore\Http\Requests\Admin\Post\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Post\UpdateFullRequest;
use N1ebieski\ICore\View\ViewModels\Admin\Post\CreateViewModel;
use N1ebieski\ICore\Http\Requests\Admin\Post\UpdateStatusRequest;
use N1ebieski\ICore\View\ViewModels\Admin\Post\EditFullViewModel;
use N1ebieski\ICore\Http\Requests\Admin\Post\DestroyGlobalRequest;

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
    public function index(Post $post, IndexRequest $request, IndexFilter $filter): HttpResponse
    {
        return Response::view('icore::admin.post.index', [
            'posts' => $post->makeRepo()->paginateByFilter($filter->all()),
            'filter' => $filter->all(),
            'paginate' => Config::get('database.paginate')
        ]);
    }

    /**
     * [create description]
     *
     * @return  HttpResponse  [return description]
     */
    public function create(): HttpResponse
    {
        return Response::view(
            'icore::admin.post.create',
            App::make(CreateViewModel::class)
        );
    }

    /**
     * Store a newly created Post in storage.
     *
     * @param Post $post [description]
     * @param  StoreRequest     $request [description]
     * @return RedirectResponse          [description]
     */
    public function store(Post $post, StoreRequest $request): RedirectResponse
    {
        $post->makeService()->create(
            $request->safe()->merge(['user' => $request->user()])->toArray()
        );

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
    public function edit(Post $post): JsonResponse
    {
        return Response::json([
            'view' => View::make('icore::admin.post.edit', [
                'post' => $post
            ])->render(),
        ]);
    }

    /**
     * Show the full-form for editing the specified Post.
     *
     * @param  Post     $post     [description]
     *
     * @return HttpResponse               [description]
     */
    public function editFull(Post $post): HttpResponse
    {
        return Response::view('icore::admin.post.edit_full', App::make(EditFullViewModel::class, [
            'post' => $post
        ]));
    }

    /**
     * Update Status attribute the specified Post in storage.
     *
     * @param  Post                $post    [description]
     * @param  UpdateStatusRequest $request [description]
     * @return JsonResponse                 [description]
     */
    public function updateStatus(Post $post, UpdateStatusRequest $request): JsonResponse
    {
        $post->makeService()->updateStatus($request->input('status'));

        return Response::json([
            'status' => $post->status->getValue(),
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
    public function updateFull(Post $post, UpdateFullRequest $request): RedirectResponse
    {
        $post->makeService()->updateFull($request->validated());

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
    public function update(Post $post, UpdateRequest $request): JsonResponse
    {
        $post->makeService()->update($request->only(['title', 'content_html']));

        return Response::json([
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
    public function destroy(Post $post): JsonResponse
    {
        $post->makeService()->delete();

        return Response::json([]);
    }

    /**
     * Remove the collection of Posts from storage.
     *
     * @param  Post                 $post    [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(Post $post, DestroyGlobalRequest $request): RedirectResponse
    {
        $deleted = $post->makeService()->deleteGlobal($request->get('select'));

        return Response::redirectTo(URL::previous())->with(
            'success',
            Lang::get('icore::posts.success.destroy_global', ['affected' => $deleted])
        );
    }
}
