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

namespace N1ebieski\ICore\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use N1ebieski\ICore\Models\Mailing;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Filters\Admin\Mailing\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Mailing\EditRequest;
use N1ebieski\ICore\Http\Requests\Admin\Mailing\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Mailing\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Mailing\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Mailing\UpdateStatusRequest;
use N1ebieski\ICore\Http\Requests\Admin\Mailing\DestroyGlobalRequest;
use N1ebieski\ICore\Events\Admin\Mailing\StoreEvent as MailingStoreEvent;
use N1ebieski\ICore\Events\Admin\Mailing\UpdateEvent as MailingUpdateEvent;

class MailingController
{
    /**
     * Display a listing of the Mailing.
     *
     * @param  Mailing         $mailing         [description]
     * @param  IndexRequest    $request         [description]
     * @param  IndexFilter     $filter          [description]
     * @return HttpResponse                             [description]
     */
    public function index(Mailing $mailing, IndexRequest $request, IndexFilter $filter): HttpResponse
    {
        return Response::view('icore::admin.mailing.index', [
            'mailings' => $mailing->makeRepo()->paginateByFilter($filter->all()),
            'filter' => $filter->all(),
            'paginate' => Config::get('database.paginate')
        ]);
    }

    /**
     * Show the form for creating a new Mailing.
     *
     * @return HttpResponse               [description]
     */
    public function create(): HttpResponse
    {
        return Response::view('icore::admin.mailing.create');
    }

    /**
     * Store a newly created Mailing in storage.
     *
     * @param  Mailing          $mailing
     * @param  StoreRequest     $request [description]
     * @return RedirectResponse          [description]
     */
    public function store(Mailing $mailing, StoreRequest $request): RedirectResponse
    {
        $mailing = $mailing->makeService()->create($request->all());

        Event::dispatch(App::make(MailingStoreEvent::class, ['mailing' => $mailing]));

        return Response::redirectToRoute('admin.mailing.index', [
                'filter' => [
                    'search' => "id:\"{$mailing->id}\""
                ]
            ])
            ->with(
                'success',
                Lang::get('icore::mailings.success.store', [
                    'recipients' => $mailing->emails()->count()
                ])
            );
    }

    /**
     * Show the full-form for editing the specified Mailing.
     *
     * @param Mailing     $mailing [description]
     * @param EditRequest $request [description]
     * @return HttpResponse                [description]
     */
    public function edit(Mailing $mailing, EditRequest $request): HttpResponse
    {
        return Response::view('icore::admin.mailing.edit', ['mailing' => $mailing]);
    }

    /**
     * Update the specified Mailing in storage.
     *
     * @param  Mailing          $mailing    [description]
     * @param  UpdateRequest $request [description]
     * @return RedirectResponse           [description]
     */
    public function update(Mailing $mailing, UpdateRequest $request): RedirectResponse
    {
        $mailing->makeService()->update($request->all());

        Event::dispatch(App::make(MailingUpdateEvent::class, ['mailing' => $mailing]));

        if ($mailing->status->isActive()) {
            return Response::redirectToRoute('admin.mailing.index')
                ->with('success', Lang::get('icore::mailings.success.update'));
        }

        return Response::redirectToRoute('admin.mailing.edit', [$mailing->id])
            ->with('success', Lang::get('icore::mailings.success.update'));
    }

    /**
     * Update Status attribute the specified Mailing in storage.
     *
     * @param  Mailing             $mailing    [description]
     * @param  UpdateStatusRequest $request [description]
     * @return JsonResponse                 [description]
     */
    public function updateStatus(Mailing $mailing, UpdateStatusRequest $request): JsonResponse
    {
        $mailing->makeService()->updateStatus($request->input('status'));

        return Response::json([
            'status' => $mailing->status->getValue(),
            'view' => View::make('icore::admin.mailing.partials.mailing', [
                'mailing' => $mailing->load('emails')
            ])->render()
        ]);
    }

    /**
     * Reset Recipients the specified Mailing from storage.
     *
     * @param  Mailing         $mailing [description]
     * @return JsonResponse       [description]
     */
    public function reset(Mailing $mailing): JsonResponse
    {
        $mailing->makeService()->reset();

        return Response::json([
            'view' => View::make('icore::admin.mailing.partials.mailing', [
                'mailing' => $mailing
            ])->render()
        ]);
    }

    /**
     * Remove the specified Mailing from storage.
     *
     * @param  Mailing $mailing [description]
     * @return JsonResponse       [description]
     */
    public function destroy(Mailing $mailing): JsonResponse
    {
        $mailing->makeService()->delete();

        return Response::json(['success' => '']);
    }

    /**
     * Remove the collection of Mailings from storage.
     *
     * @param  Mailing           $mailing    [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(Mailing $mailing, DestroyGlobalRequest $request): RedirectResponse
    {
        $deleted = $mailing->makeService()->deleteGlobal($request->get('select'));

        return Response::redirectTo(URL::previous())->with(
            'success',
            Lang::get('icore::mailings.success.destroy_global', ['affected' => $deleted])
        );
    }
}
