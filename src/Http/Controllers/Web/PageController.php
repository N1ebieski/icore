<?php

namespace N1ebieski\ICore\Http\Controllers\Web;

use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Http\RedirectResponse;
use N1ebieski\ICore\Filters\Web\Page\ShowFilter;
use N1ebieski\ICore\Models\Comment\Page\Comment;
use N1ebieski\ICore\Http\Requests\Web\Page\ShowRequest;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

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
     * @return HttpResponse|RedirectResponse   [description]
     */
    public function show(
        Page $page,
        Comment $comment,
        ShowFilter $filter,
        ShowRequest $request
    ) : BaseResponse {
        if ($page->isRedirect()) {
            return Response::redirectTo(
                html_entity_decode($page->content),
                HttpResponse::HTTP_MOVED_PERMANENTLY
            );
        }

        return Response::view('icore::web.page.show', [
            'page' => $page->makeCache()->rememberLoadSiblingsAndRecursiveChildrens(),
            'comments' => (bool)$page->comment === true ?
                $comment->setMorph($page)->makeCache()->rememberRootsByFilter(
                    $filter->all() + ['except' => $request->input('except')],
                    $request->get('page') ?? 1
                )
                : null,
            'filter' => $filter->all()
        ]);
    }
}
