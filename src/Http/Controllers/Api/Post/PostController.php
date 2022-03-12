<?php

namespace N1ebieski\ICore\Http\Controllers\Api\Post;

use N1ebieski\ICore\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Filters\Api\Post\IndexFilter;
use N1ebieski\ICore\Http\Resources\Post\PostResource;
use N1ebieski\ICore\Http\Requests\Api\Post\IndexRequest;
use N1ebieski\ICore\Http\Resources\Category\CategoryResource;

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
     * <aside class="notice">Available only to users with permission: api.access and api.posts.view.</aside>
     *
     * @authenticated
     *
     * @bodyParam filter.status int Must be one of 1 or (available only for admin.categories.view) 0, 2. Example: 1
     *
     * @responseField id int
     * @responseField title string
     * @responseField slug string
     * @responseField short_content string A shortened version of the post without HTML formatting.
     * @responseField content string Post without HTML formatting.
     * @responseField content_html string Post with HTML formatting.
     * @responseField no_more_content_html string Post with HTML formatting without "show more" button.
     * @responseField less_content_html string Post with HTML formatting with "show more" button.
     * @responseField seo_title string Title for SEO.
     * @responseField meta_title string Title for META.
     * @responseField seo_desc string Description for SEO.
     * @responseField meta_desc string Description for META.
     * @responseField seo_noindex boolean Value for META.
     * @responseField seo_nofollow boolean Value for META.
     * @responseField status object Contains int value and string label.
     * @responseField comment boolean Determines whether comments are allowed.
     * @responseField first_image string Address of the first image in the post for META.
     * @responseField published_at string
     * @responseField created_at string
     * @responseField updated_at string
     * @responseField user object Contains relationship User author.
     * @responseField links object Contains links to resources on the website and in the administration panel.
     * @responseField meta object Paging, filtering and sorting information.
     *
     * @apiResourceCollection N1ebieski\ICore\Http\Resources\Post\PostResource
     * @apiResourceModel N1ebieski\ICore\Models\Post states=active,publish,with_user with=user
     *
     * @param Post $post
     * @param IndexRequest $request
     * @param IndexFilter $filter
     * @return JsonResponse
     */
    public function index(Post $post, IndexRequest $request, IndexFilter $filter): JsonResponse
    {
        return App::make(PostResource::class)
            ->collection($post->makeCache()->rememberByFilter($filter->all()))
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
