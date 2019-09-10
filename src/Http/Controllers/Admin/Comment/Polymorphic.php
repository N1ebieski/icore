<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Comment;

use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Http\Requests\Admin\Comment\IndexRequest;
use N1ebieski\ICore\Filters\Admin\Comment\IndexFilter;
use Illuminate\View\View;

/**
 * [interface description]
 * @var [type]
 */
interface Polymorphic
{
    /**
     * [index description]
     * @param  Comment      $comment [description]
     * @param  IndexFilter  $filter  [description]
     * @param  IndexRequest $request [description]
     * @return View                  [description]
     */
    public function index(Comment $comment, IndexRequest $request, IndexFilter $filter) : View;
}
