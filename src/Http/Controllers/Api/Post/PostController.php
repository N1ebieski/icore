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

class PostController
{
    /**
     * Undocumented function
     *
     * @param Post $post
     * @param IndexRequest $request
     * @param IndexFilter $filter
     * @return JsonResponse
     */
    public function index(Post $post, IndexRequest $request, IndexFilter $filter): JsonResponse
    {
        return App::make(PostResource::class)
            ->collection(
                $post->makeCache()->rememberByFilter(
                    $filter->all(),
                    $request->input('page') ?? 1
                )
            )
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
