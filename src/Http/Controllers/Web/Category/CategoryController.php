<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Category;

use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Http\Requests\Web\Category\SearchRequest;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Http\Controllers\Web\Category\Polymorphic;
use N1ebieski\ICore\Http\Responses\Web\Category\SearchResponse;

/**
 * Base Category Controller
 */
class CategoryController implements Polymorphic
{
    /**
     * Model. Must be protected!
     * @var Category
     */
    protected $category;

    /**
     * [__construct description]
     * @param Category        $category        [description]
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Search Categories for specified name.
     *
     * @param  Category      $category [description]
     * @param  SearchRequest $request  [description]
     * @param  SearchResponse $response [description]
     * @return JsonResponse                [description]
     */
    public function search(Category $category, SearchRequest $request, SearchResponse $response) : JsonResponse
    {
        $categories = $category->makeRepo()->getBySearch($request->get('name'));

        return $response->setCategories($categories)->makeResponse();
    }
}
