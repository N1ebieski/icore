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
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Loads\Admin\Page\EditFullLoad;
use N1ebieski\ICore\Filters\Admin\Page\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Page\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Page\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Page\UpdateRequest;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\ICore\Http\Requests\Admin\Page\UpdateFullRequest;
use N1ebieski\ICore\Http\Requests\Admin\Page\UpdateStatusRequest;
use N1ebieski\ICore\View\ViewModels\Admin\Page\EditFullViewModel;
use N1ebieski\ICore\Http\Requests\Admin\Page\DestroyGlobalRequest;
use N1ebieski\ICore\Events\Admin\Page\StoreEvent as PageStoreEvent;
use N1ebieski\ICore\Http\Requests\Admin\Page\UpdatePositionRequest;
use N1ebieski\ICore\Events\Admin\Page\UpdateEvent as PageUpdateEvent;
use N1ebieski\ICore\Events\Admin\Page\UpdateFullEvent as PageUpdateFullEvent;

class PageController
{
    /**
     * Display a listing of the Post.
     *
     * @param  Page            $page            [description]
     * @param  IndexRequest    $request         [description]
     * @param  IndexFilter     $filter          [description]
     * @return HttpResponse                             [description]
     */
    public function index(Page $page, IndexRequest $request, IndexFilter $filter): HttpResponse
    {
        $pageService = $page->makeService();

        return Response::view('icore::admin.page.index', [
            'pages' => $pageService->paginateByFilter($filter->all()),
            'parents' => $pageService->getAsFlatTree(),
            'filter' => $filter->all(),
            'paginate' => Config::get('database.paginate')
        ]);
    }

    /**
     * [create description]
     * @param Page $page
     * @return HttpResponse [description]
     */
    public function create(Page $page): HttpResponse
    {
        return Response::view('icore::admin.page.create', [
            'parents' => $page->makeService()->getAsFlatTree()
        ]);
    }

    /**
     * [store description]
     * @param  Page         $page    [description]
     * @param  StoreRequest $request [description]
     * @return RedirectResponse      [description]
     */
    public function store(Page $page, StoreRequest $request): RedirectResponse
    {
        $page->makeService()->create(
            $request->safe()->merge(['user' => $request->user()])->toArray()
        );

        if (!is_null($request->input('parent_id'))) {
            /** @var Page|null */
            $parent = $page->find($request->input('parent_id'));
        }

        Event::dispatch(App::make(PageStoreEvent::class, ['page' => $page]));

        return Response::redirectToRoute('admin.page.index', [
            'filter' => [
                'search' => 'id:"' . $page->id . '"'
            ]
        ])
        ->with(
            'success',
            // @phpstan-ignore-next-line
            Lang::get('icore::pages.success.store') . (
                !is_null($request->input('parent_id')) ?
                    Lang::get('icore::pages.success.store_parent', [
                        'parent' => $parent?->title
                    ])
                    : null
            )
        );
    }

    /**
     * [editPosition description]
     * @param  Page         $page [description]
     * @return JsonResponse       [description]
     */
    public function editPosition(Page $page): JsonResponse
    {
        $page->siblings_count = $page->countSiblings() + 1;

        return Response::json([
            'view' => View::make('icore::admin.page.edit_position', [
                'page' => $page
            ])->render()
        ]);
    }

    /**
     * [updatePosition description]
     * @param  Page                  $page    [description]
     * @param  UpdatePositionRequest $request [description]
     * @return JsonResponse                   [description]
     */
    public function updatePosition(Page $page, UpdatePositionRequest $request): JsonResponse
    {
        $page->makeService()->updatePosition($request->input('position'));

        return Response::json([
            'siblings' => $page->makeRepo()->getSiblingsAsArray() + [$page->id => $page->position]
        ]);
    }

    /**
     * [edit description]
     * @param  Page   $page [description]
     * @return JsonResponse       [description]
     */
    public function edit(Page $page): JsonResponse
    {
        return Response::json([
            'view' => View::make('icore::admin.page.edit', ['page' => $page])->render()
        ]);
    }

    /**
     *
     * @param Page $page
     * @param EditFullLoad $load
     * @return HttpResponse
     * @throws BindingResolutionException
     */
    public function editFull(Page $page, EditFullLoad $load): HttpResponse
    {
        return Response::view('icore::admin.page.edit_full', App::make(EditFullViewModel::class, [
            'page' => $page
        ]));
    }

    /**
     * [update description]
     * @param  Page          $page    [description]
     * @param  UpdateRequest $request [description]
     * @return JsonResponse                 [description]
     */
    public function update(Page $page, UpdateRequest $request): JsonResponse
    {
        $page->makeService()->update($request->validated());

        Event::dispatch(App::make(PageUpdateEvent::class, ['page' => $page]));

        return Response::json([
            'view' => View::make('icore::admin.page.partials.page', [
                'page' => $page->loadAncestorsExceptSelf()
            ])->render()
        ]);
    }

    /**
     * [updateFull description]
     * @param  Page              $page    [description]
     * @param  UpdateFullRequest $request [description]
     * @return RedirectResponse           [description]
     */
    public function updateFull(Page $page, UpdateFullRequest $request): RedirectResponse
    {
        $page->makeService()->update($request->validated());

        Event::dispatch(App::make(PageUpdateFullEvent::class, ['page' => $page]));

        return Response::redirectToRoute('admin.page.edit_full', [$page->id])
            ->with('success', Lang::get('icore::pages.success.update'));
    }

    /**
     * [updateStatus description]
     * @param  Page                $page    [description]
     * @param  UpdateStatusRequest $request [description]
     * @return JsonResponse                 [description]
     */
    public function updateStatus(Page $page, UpdateStatusRequest $request): JsonResponse
    {
        $page->makeService()->updateStatus($request->input('status'));

        $pageRepo = $page->makeRepo();

        return Response::json([
            'status' => $page->status->getValue(),
            'view' => View::make('icore::admin.page.partials.page', [
                'page' => $page
            ])->render(),
            // Na potrzebę jQuery pobieramy potomków i przodków, żeby na froncie
            // zaznaczyć odpowiednie rowsy jako aktywowane bądź nieaktywne
            'ancestors' => $pageRepo->getAncestorsAsArray(),
            'descendants' => $pageRepo->getDescendantsAsArray()
        ]);
    }

    /**
     * [destroy description]
     * @param  Page         $page [description]
     * @return JsonResponse       [description]
     */
    public function destroy(Page $page): JsonResponse
    {
        // Pobieramy potomków aby na froncie jQuery wiedział jakie rowsy usunąć
        $descendants = $page->makeRepo()->getDescendantsAsArray();

        $page->makeService()->delete();

        return Response::json([
            'descendants' => $descendants
        ]);
    }

    /**
     * [destroyGlobal description]
     * @param  Page                 $page    [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(Page $page, DestroyGlobalRequest $request): RedirectResponse
    {
        $deleted = $page->makeService()->deleteGlobal($request->get('select'));

        return Response::redirectTo(URL::previous())->with(
            'success',
            Lang::get('icore::pages.success.destroy_global', ['affected' => $deleted])
        );
    }
}
