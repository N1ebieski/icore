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

/**
 * @group Categories
 */
class CategoryController implements Polymorphic
{
    /**
     * Index of categories
     *
     * @bodyParam filter.status int Must be one of 1 or 0 (available only for admin.categories.view). Example: 1
     *
     * @responseField id int
     * @responseField name string
     * @responseField slug string
     * @responseField icon string Class of icon.
     * @responseField status object Contains int value and string label.
     * @responseField real_depth int Level of hierarchy.
     * @responseField created_at string
     * @responseField created_at_diff string
     * @responseField updated_at string
     * @responseField updated_at_diff string
     * @responseField ancestors object[] Contains relationship Category ancestors (parent and higher).
     *
     * @apiResourceCollection N1ebieski\ICore\Http\Resources\Category\CategoryResource
     * @apiResourceModel N1ebieski\ICore\Models\Category\Category states=active,sentence
     * @apiResourceAdditional meta="Paging, filtering and sorting information"
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
