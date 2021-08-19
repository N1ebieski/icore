<?php

namespace N1ebieski\ICore\Http\Controllers\Api\Category;

use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Filters\Api\Category\IndexFilter;
use N1ebieski\ICore\Http\Requests\Api\Category\IndexRequest;

interface Polymorphic
{
    /**
     * Undocumented function
     *
     * @param Category $category
     * @param IndexRequest $request
     * @param IndexFilter $filter
     * @return JsonResponse
     */
    public function index(Category $category, IndexRequest $request, IndexFilter $filter): JsonResponse;
}
