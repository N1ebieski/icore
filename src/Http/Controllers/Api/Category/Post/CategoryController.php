<?php

namespace N1ebieski\ICore\Http\Controllers\Api\Category\Post;

use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Filters\Api\Category\IndexFilter;
use N1ebieski\ICore\Http\Requests\Api\Category\Post\IndexRequest;
use N1ebieski\ICore\Http\Controllers\Api\Category\CategoryController as BaseCategoryController;

/**
 * @group Categories
 *
 * > Routes:
 *
 *     /routes/vendor/icore/api/categories.php
 *
 * > Controller:
 *
 *     N1ebieski\ICore\Http\Controllers\Api\Category\Post\CategoryController
 *
 * > Resource:
 *
 *     N1ebieski\ICore\Http\Resources\Category\CategoryResource
 *
 * Permissions:
 *
 * - api.* - access to all api endpoints
 * - api.categories.* - access to all categories endpoints
 * - api.categories.view - access to endpoints with collection of categories
 */
class CategoryController implements Polymorphic
{
    /**
     * Undocumented variable
     *
     * @var BaseCategoryController
     */
    protected $decorated;

    /**
     * Undocumented function
     *
     * @param BaseCategoryController $decorated
     */
    public function __construct(BaseCategoryController $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * Index of post's categories
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
     * @apiResourceModel N1ebieski\ICore\Models\Category\Post\Category states=active,sentence
     * @apiResourceAdditional meta="Paging, filtering and sorting information"
     *
     * @param  Category      $category      [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return JsonResponse                 [description]
     */
    public function index(Category $category, IndexRequest $request, IndexFilter $filter): JsonResponse
    {
        return $this->decorated->index($category, $request, $filter);
    }
}
