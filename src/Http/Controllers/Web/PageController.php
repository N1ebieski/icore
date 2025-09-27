<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

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
use N1ebieski\ICore\View\ViewModels\Web\Page\ShowViewModel;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use N1ebieski\ICore\Events\Web\Page\ShowEvent as PageShowEvent;

class PageController
{
    /**
     * [show description]
     * @param  Page        $page    [description]
     * @param  ShowRequest $request [description]
     * @return HttpResponse|RedirectResponse   [description]
     */
    public function show(
        Page $page,
        ShowRequest $request
    ): BaseResponse {
        Event::dispatch(App::make(PageShowEvent::class, ['page' => $page]));

        if ($page->isRedirect()) {
            return Response::redirectTo(
                // @phpstan-ignore-next-line
                html_entity_decode($page->content),
                HttpResponse::HTTP_MOVED_PERMANENTLY
            );
        }

        return Response::view('icore::web.page.show', App::make(ShowViewModel::class, [
            'page' => $page
        ]));
    }
}
