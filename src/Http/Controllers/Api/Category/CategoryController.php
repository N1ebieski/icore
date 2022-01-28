<?php

namespace N1ebieski\ICore\Http\Controllers\Api\Category;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Filters\Api\Category\IndexFilter;
use N1ebieski\ICore\Http\Requests\Api\Category\IndexRequest;
use N1ebieski\ICore\Http\Resources\Category\CategoryResource;
use N1ebieski\ICore\Http\Controllers\Api\Category\Polymorphic;

class CategoryController implements Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Category $category
     * @param IndexRequest $request
     * @param IndexFilter $filter
     * @return JsonResponse
     */
    public function index(Category $category, IndexRequest $request, IndexFilter $filter): JsonResponse
    {
        return App::make(CategoryResource::class)
            ->collection(
                $category->makeCache()->rememberByFilter(
                    $filter->all(),
                    $request->input('page') ?? 1
                )
            )
            ->additional(['meta' => [
                'filter' => Collect::make($filter->all())
                    ->replace([
                        'parent' => $filter->get('parent') instanceof Category ?
                            App::make(CategoryResource::class, ['category' => $filter->get('parent')])
                            : $filter->get('parent')
                    ])
                    ->forget('category')
                    ->toArray()
            ]])
            ->response();
    }
}
