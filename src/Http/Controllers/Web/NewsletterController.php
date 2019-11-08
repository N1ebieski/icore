<?php

namespace N1ebieski\ICore\Http\Controllers\Web;

use N1ebieski\ICore\Http\Requests\Web\Newsletter\StoreRequest;
use N1ebieski\ICore\Http\Requests\Web\Newsletter\UpdateStatusRequest;
use N1ebieski\ICore\Models\Newsletter;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Events\NewsletterStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

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
            ['token' => Str::random(30), 'status' => 0]
        );

        event(new NewsletterStore($newsletter));

        return response()->json([
            'success' => trans('icore::newsletter.success.store'),
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
            abort(403, 'The token is invalid.');
        }

        $newsletter->status = (bool)$request->get('status');
        $newsletter->token = Str::random(30);
        $newsletter->save();

        return redirect()->route('web.home.index')->with('success', ($newsletter->status === true) ?
            trans('icore::newsletter.success.update_status_1') : trans('icore::newsletter.success.update_status_0'));
    }
}
