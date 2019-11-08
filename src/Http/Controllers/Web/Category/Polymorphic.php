<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Category;

use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Http\Requests\Web\Category\SearchRequest;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Http\Responses\Web\Category\SearchResponse;

/**
 * [interface description]
 */
interface Polymorphic
{
    /**
     * Search Categories for specified name.
     *
     * @param  Category      $category [description]
     * @param  SearchRequest $request  [description]
     * @param  SearchResponse $response [description]
     * @return JsonResponse                [description]
     */
    public function search(Category $category, SearchRequest $request, SearchResponse $response) : JsonResponse;
}
