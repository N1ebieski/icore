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

namespace N1ebieski\ICore\Http\Controllers\Api\Post;

use N1ebieski\ICore\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Filters\Api\Post\IndexFilter;
use N1ebieski\ICore\Http\Resources\Post\PostResource;
use N1ebieski\ICore\Http\Requests\Api\Post\IndexRequest;
use N1ebieski\ICore\Http\Resources\Category\CategoryResource;
use N1ebieski\ICore\Events\Api\Post\IndexEvent as PostIndexEvent;

/**
 * @group Posts
 *
 * > Routes:
 *
 *     /routes/vendor/icore/api/posts.php
 *
 * > Controller:
 *
 *     N1ebieski\ICore\Http\Controllers\Api\Post\PostController
 *
 * > Resource:
 *
 *     N1ebieski\ICore\Http\Resources\Post\PostResource
 *
 * Permissions:
 *
 * - api.* - access to all api endpoints
 * - api.posts.* - access to all posts endpoints
 * - api.posts.view - access to endpoints with collection of posts
 */
class PostController
{
    /**
     * Index of posts
     *
     * <aside class="notice">Available only to users with permissions: api.access and api.posts.view.</aside>
     *
     * @authenticated
     *
     * @bodyParam filter.status int Must be one of 1 or (available only for admin.categories.view) 0, 2. Example: 1
     *
     * @apiResourceCollection N1ebieski\ICore\Http\Resources\Post\PostResource
     * @apiResourceModel N1ebieski\ICore\Models\Post states=active,publish,withUser with=user
     *
     * @param Post $post
     * @param IndexRequest $request
     * @param IndexFilter $filter
     * @return JsonResponse
     */
    public function index(Post $post, IndexRequest $request, IndexFilter $filter): JsonResponse
    {
        $posts = $post->makeCache()->rememberByFilter($filter->all());

        Event::dispatch(App::make(PostIndexEvent::class, ['posts' => $posts]));

        return App::make(PostResource::class)
            ->collection($posts)
            ->additional(['meta' => [
                'filter' => Collect::make($filter->all())
                    ->replace([
                        'category' => $filter->get('category') instanceof Category ?
                            App::make(CategoryResource::class, ['category' => $filter->get('category')])
                            : $filter->get('category')
                    ])
                    ->toArray()
            ]])
            ->response();
    }
}
