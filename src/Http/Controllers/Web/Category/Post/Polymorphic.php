<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Category\Post;

use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Category\Post\Category;
use N1ebieski\ICore\Http\Requests\Web\Category\ShowRequest;

/**
 * [interface description]
 */
interface Polymorphic
{
    /**
     * Display a listing of the Posts for Category.
     *
     * @param  Category $category [description]
     * @param ShowRequest $request
     * @return HttpResponse [description]
     */
    public function show(Category $category, ShowRequest $request) : HttpResponse;
}
