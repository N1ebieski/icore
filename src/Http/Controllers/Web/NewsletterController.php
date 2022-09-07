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

use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use N1ebieski\ICore\Models\Newsletter;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\ValueObjects\Newsletter\Status;
use N1ebieski\ICore\Http\Requests\Web\Newsletter\StoreRequest;
use N1ebieski\ICore\Http\Requests\Web\Newsletter\UpdateStatusRequest;
use N1ebieski\ICore\Events\Web\Newsletter\StoreEvent as NewsletterStoreEvent;

class NewsletterController
{
    /**
     * Store a newly created Subscribe for newsletter in storage.
     *
     * @param  Newsletter   $newsletter [description]
     * @param  StoreRequest $request    [description]
     * @return JsonResponse             [description]
     */
    public function store(Newsletter $newsletter, StoreRequest $request): JsonResponse
    {
        $newsletter = $newsletter->firstOrCreate(
            ['email' => $request->input('email')],
            ['status' => Status::INACTIVE]
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
    public function updateStatus(Newsletter $newsletter, UpdateStatusRequest $request): RedirectResponse
    {
        $newsletter->update(['status' => $request->input('status')]);

        $newsletter->token()->update(['token' => Str::random(30)]);

        return Response::redirectToRoute('web.home.index')->with(
            'success',
            $newsletter->status->isActive() ?
                Lang::get('icore::newsletter.success.update_status.' . Status::ACTIVE)
                : Lang::get('icore::newsletter.success.update_status.' . Status::INACTIVE)
        );
    }
}
