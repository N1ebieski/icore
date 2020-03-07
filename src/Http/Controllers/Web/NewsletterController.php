<?php

namespace N1ebieski\ICore\Http\Controllers\Web;

use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Event;
use N1ebieski\ICore\Models\Newsletter;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Http\Requests\Web\Newsletter\StoreRequest;
use N1ebieski\ICore\Http\Requests\Web\Newsletter\UpdateStatusRequest;
use N1ebieski\ICore\Events\Web\Newsletter\StoreEvent as NewsletterStoreEvent;

/**
 * [NewsletterController description]
 */
class NewsletterController
{
    /**
     * Store a newly created Subscribe for newsletter in storage.
     *
     * @param  Newsletter   $newsletter [description]
     * @param  StoreRequest $request    [description]
     * @return JsonResponse             [description]
     */
    public function store(Newsletter $newsletter, StoreRequest $request) : JsonResponse
    {
        $newsletter = $newsletter->firstOrCreate(
            ['email' => $request->get('email')],
            ['token' => Str::random(30), 'status' => Newsletter::ACTIVE]
        );

        Event::dispatch(App::make(NewsletterStoreEvent::class, ['newsletter' => $newsletter]));

        return Response::json([
            'success' => Lang::get('icore::newsletter.success.store'),
        ]);
    }

    /**
     * Update Status attribute the specified Subscribe in storage.
     *
     * @param  Newsletter          $newsletter [description]
     * @param  UpdateStatusRequest $request    [description]
     * @return RedirectResponse                [description]
     */
    public function updateStatus(Newsletter $newsletter, UpdateStatusRequest $request) : RedirectResponse
    {
        if ($newsletter->token !== $request->get('token')) {
            abort(HttpResponse::HTTP_FORBIDDEN, 'The token is invalid.');
        }

        $newsletter->status = (bool)$request->get('status');
        $newsletter->token = Str::random(30);
        $newsletter->save();

        return Response::redirectToRoute('web.home.index')->with(
            'success',
            $newsletter->status === true ?
                Lang::get('icore::newsletter.success.update_status_1')
                : Lang::get('icore::newsletter.success.update_status_0')
        );
    }
}
