<?php

namespace N1ebieski\ICore\Http\Controllers\Api\Category\Post;

use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Filters\Api\Category\IndexFilter;
use N1ebieski\ICore\Http\Requests\Api\Category\Post\IndexRequest;

interface Polymorphic
{
    /**
     * Display a listing of the Category.
     *
     * @param  Category      $category      [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return JsonResponse                 [description]
     */
    public function index(Category $category, IndexRequest $request, IndexFilter $filter): JsonResponse;
}
