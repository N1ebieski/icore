<?php

namespace N1ebieski\ICore\Http\Controllers\Web;

use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Event;
use N1ebieski\ICore\Models\Newsletter;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Http\Requests\Web\Newsletter\StoreRequest;
use N1ebieski\ICore\Events\Web\Newsletter\StoreEvent as NewsletterStoreEvent;
use N1ebieski\ICore\Http\Requests\Web\Newsletter\UpdateStatusRequest;

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
            ['email' => $request->input('email')],
            ['status' => Newsletter::INACTIVE]
        );

        $newsletter->token()->updateOrCreate(
            ['email' => $request->input('email')],
            ['token' => Str::random(30)]
        );

        Event::dispatch(App::make(NewsletterStoreEvent::class, ['newsletter' => $newsletter]));

        return Response::json([
            'success' => Lang::get('icore::newsletter.success.store'),
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Newsletter $newsletter
     * @param UpdateStatusRequest $request
     * @return RedirectResponse
     */
    public function updateStatus(Newsletter $newsletter, UpdateStatusRequest $request) : RedirectResponse
    {
        $newsletter->update(['status' => $request->input('status')]);

        $newsletter->token()->update(['token' => Str::random(30)]);

        return Response::redirectToRoute('web.home.index')->with(
            'success',
            $newsletter->status === Newsletter::ACTIVE ?
                Lang::get('icore::newsletter.success.update_status.'.Newsletter::ACTIVE)
                : Lang::get('icore::newsletter.success.update_status.'.Newsletter::INACTIVE)
        );
    }
}
