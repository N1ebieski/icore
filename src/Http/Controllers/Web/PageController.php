<?php

namespace N1ebieski\ICore\Http\Controllers\Web;

use Illuminate\Support\Facades\App;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Filters\Web\Page\ShowFilter;
use N1ebieski\ICore\Models\Comment\Page\Comment;
use N1ebieski\ICore\Http\Requests\Web\Page\ShowRequest;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use N1ebieski\ICore\Events\Web\Page\ShowEvent as PageShowEvent;

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
    ): BaseResponse {
        Event::dispatch(App::make(PageShowEvent::class, ['page' => $page]));

        if ($page->isRedirect()) {
            return Response::redirectTo(
                html_entity_decode($page->content),
                HttpResponse::HTTP_MOVED_PERMANENTLY
            );
        }

        return Response::view('icore::web.page.show', [
            'page' => $page->makeCache()->rememberLoadSiblingsAndRecursiveChildrens(),
            'comments' => $page->comment->isActive() ?
                $comment->setRelations(['morph' => $page])
                    ->makeCache()
                    ->rememberRootsByFilter($filter->all())
                : null,
            'filter' => $filter->all()
        ]);
    }
}
