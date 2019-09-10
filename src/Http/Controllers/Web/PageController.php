<?php

namespace N1ebieski\ICore\Http\Controllers\Web;

use N1ebieski\ICore\Filters\Web\Page\ShowFilter;
use N1ebieski\ICore\Http\Requests\Web\Page\ShowRequest;
use N1ebieski\ICore\Models\Page\Page;
use N1ebieski\ICore\Models\Comment\Page\Comment;
use Illuminate\View\View;

/**
 * [PageController description]
 */
class PageController
{
    /**
     * [show description]
     * @param  Page        $page    [description]
     * @param  Comment     $comment [description]
     * @param  ShowFilter  $filter  [description]
     * @param  ShowRequest $request [description]
     * @return View                 [description]
     */
    public function show(Page $page, Comment $comment, ShowFilter $filter, ShowRequest $request) : View
    {
        $comments = ((bool)$page->comment === true) ?
            $comment->setMorph($page)->getCache()->rememberRootsByFilter(
                $filter->all(),
                $request->get('page') ?? 1
            ) : null;

        return view('icore::web.page.show', [
            'page' => $page->getCache()->rememberLoadRecursiveChildrens(),
            'comments' => $comments,
            'filter' => $filter->all()
        ]);
    }
}
