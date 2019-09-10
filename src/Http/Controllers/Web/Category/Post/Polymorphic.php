<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Category\Post;

use N1ebieski\ICore\Http\Requests\Web\Category\ShowRequest;
use N1ebieski\ICore\Models\Category\Category;
use Illuminate\View\View;

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
     * @return View [description]
     */
    public function show(Category $category, ShowRequest $request) : View;
}
