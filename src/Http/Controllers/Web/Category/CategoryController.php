<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Category;

use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Http\Requests\Web\Category\SearchRequest;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Http\Controllers\Web\Category\Polymorphic;

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
     * @return JsonResponse                [description]
     */
    public function search(Category $category, SearchRequest $request) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::web.category.partials.search', [
                'categories' => $category->getRepo()->getBySearch($request->get('name')),
                'checked' => false
            ])->render()
        ]);
    }
}
