<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Category\Post;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Http\Requests\Web\Category\ShowRequest;
use N1ebieski\ICore\Http\Controllers\Web\Category\Post\Polymorphic;

class CategoryController implements Polymorphic
{
    /**
     * Display a listing of the Posts for Category.
     *
     * @param  Category $category [description]
     * @param ShowRequest $request
     * @return HttpResponse [description]
     */
    public function show(Category $category, ShowRequest $request): HttpResponse
    {
        return Response::view('icore::web.category.post.show', [
            'posts' => $category->makeCache()->rememberPosts($request->get('page') ?? 1),
            'category' => $category,
            'catsAsArray' => [
                'ancestors' => $category->ancestors->pluck('id')->toArray(),
                'self' => [$category->id]
            ]
        ]);
    }
}
