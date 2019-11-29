<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Category\Post;

use N1ebieski\ICore\Http\Requests\Web\Category\ShowRequest;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\View\View;
use N1ebieski\ICore\Http\Controllers\Web\Category\Post\Polymorphic;

/**
 * [CategoryController description]
 */
class CategoryController implements Polymorphic
{
    /**
     * Display a listing of the Posts for Category.
     *
     * @param  Category $category [description]
     * @param ShowRequest $request
     * @return View [description]
     */
    public function show(Category $category, ShowRequest $request) : View
    {
        return view('icore::web.category.show', [
            'posts' => $category->makeCache()->rememberPosts($request->get('page') ?? 1),
            'category' => $category,
            'catsAsArray' => array_merge($category->ancestors->pluck('id')->toArray(), [$category->id])
        ]);
    }
}
