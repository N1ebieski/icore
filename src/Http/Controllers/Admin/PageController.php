<?php

namespace N1ebieski\ICore\Http\Controllers\Admin;

use N1ebieski\ICore\Http\Requests\Admin\Page\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Page\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Page\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Page\UpdateFullRequest;
use N1ebieski\ICore\Http\Requests\Admin\Page\UpdateStatusRequest;
use N1ebieski\ICore\Http\Requests\Admin\Page\UpdatePositionRequest;
use N1ebieski\ICore\Http\Requests\Admin\Page\DestroyGlobalRequest;
use N1ebieski\ICore\Filters\Admin\Page\IndexFilter;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * [PageController description]
 */
class PageController
{
    /**
     * Display a listing of the Post.
     *
     * @param  Page            $page            [description]
     * @param  IndexRequest    $request         [description]
     * @param  IndexFilter     $filter          [description]
     * @return View                             [description]
     */
    public function index(Page $page, IndexRequest $request, IndexFilter $filter) : View
    {
        $pageService = $page->makeService();

        return view('icore::admin.page.index', [
            'pages' => $pageService->paginateByFilter($filter->all()),
            'parents' => $pageService->getAsFlatTree(),
            'filter' => $filter->all(),
            'paginate' => config('database.paginate')
        ]);
    }

    /**
     * [create description]
     * @param Page $page
     * @return View [description]
     */
    public function create(Page $page) : View
    {
        return view('icore::admin.page.create', [
            'parents' => $page->makeService()->getAsFlatTree()
        ]);
    }

    /**
     * [store description]
     * @param  Page         $page    [description]
     * @param  StoreRequest $request [description]
     * @return RedirectResponse      [description]
     */
    public function store(Page $page, StoreRequest $request) : RedirectResponse
    {
        $page->makeService()->create($request->all());

        return redirect()->route('admin.page.index')->with('success', trans('icore::pages.success.store') );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * [editPosition description]
     * @param  Page         $page [description]
     * @return JsonResponse       [description]
     */
    public function editPosition(Page $page) : JsonResponse
    {
        $page->siblings_count = $page->countSiblings()+1;

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.page.edit_position', [
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
    public function updatePosition(Page $page, UpdatePositionRequest $request) : JsonResponse
    {
        $page->makeService()->updatePosition($request->only('position'));

        return response()->json([
            'success' => '',
            'siblings' => $page->makeRepo()->getSiblingsAsArray()+[$page->id => $page->position],
        ]);
    }

    /**
     * [edit description]
     * @param  Page   $page [description]
     * @return JsonResponse       [description]
     */
    public function edit(Page $page) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.page.edit', ['page' => $page])->render(),
        ]);
    }

    /**
     * [editFull description]
     * @param  Page $page [description]
     * @return View       [description]
     */
    public function editFull(Page $page) : View
    {
        return view('icore::admin.page.edit_full', [
            'page' => $page,
            'parents' => $page->makeService()->getAsFlatTreeExceptSelf()
        ]);
    }

    /**
     * [update description]
     * @param  Page          $page    [description]
     * @param  UpdateRequest $request [description]
     * @return JsonResponse                 [description]
     */
    public function update(Page $page, UpdateRequest $request) : JsonResponse
    {
        $page->makeService()->update($request->only(['title', 'content_html']));

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.page.page', ['page' => $page])->render()
        ]);
    }

    /**
     * [updateFull description]
     * @param  Page              $page    [description]
     * @param  UpdateFullRequest $request [description]
     * @return RedirectResponse           [description]
     */
    public function updateFull(Page $page, UpdateFullRequest $request) : RedirectResponse
    {
        $page->makeService()->updateFull($request->all());

        return redirect()->route('admin.page.edit_full', [$page->id])
            ->with('success', trans('icore::pages.success.update') );
    }

    /**
     * [updateStatus description]
     * @param  Page                $page    [description]
     * @param  UpdateStatusRequest $request [description]
     * @return JsonResponse                 [description]
     */
    public function updateStatus(Page $page, UpdateStatusRequest $request) : JsonResponse
    {
        $page->makeService()->updateStatus($request->only('status'));

        $pageRepo = $page->makeRepo();

        return response()->json([
            'success' => '',
            'status' => $page->status,
            // Na potrzebę jQuery pobieramy potomków i przodków, żeby na froncie
            // zaznaczyć odpowiednie rowsy jako aktywowane bądź nieaktywne
            'ancestors' => $page->makeRepo()->getAncestorsAsArray(),
            'descendants' => $page->makeRepo()->getDescendantsAsArray(),
        ]);
    }

    /**
     * [destroy description]
     * @param  Page         $page [description]
     * @return JsonResponse       [description]
     */
    public function destroy(Page $page) : JsonResponse
    {
        // Pobieramy potomków aby na froncie jQuery wiedział jakie rowsy usunąć
        $descendants = $page->makeRepo()->getDescendantsAsArray();

        $page->makeService()->delete();

        return response()->json([
            'success' => '',
            // Pobieramy potomków aby na froncie jQuery wiedział jakie rowsy usunąć
            'descendants' => $descendants,
        ]);
    }

    /**
     * [destroyGlobal description]
     * @param  Page                 $page    [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(Page $page, DestroyGlobalRequest $request) : RedirectResponse
    {
        $deleted = $page->makeService()->deleteGlobal($request->get('select'));

        return redirect()->back()->with('success', trans('icore::pages.success.destroy_global', ['affected' => $deleted]));
    }
}
