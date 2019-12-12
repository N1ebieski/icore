<?php

namespace N1ebieski\ICore\Http\Controllers\Admin;

use N1ebieski\ICore\Http\Requests\Admin\Link\UpdatePositionRequest;
use N1ebieski\ICore\Http\Requests\Admin\Link\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Link\CreateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Link\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Link\UpdateRequest;
use N1ebieski\ICore\Models\Link;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

/**
 * [LinkController description]
 */
class LinkController
{
    /**
     * Display a listing of the Link.
     *
     * @param  string       $type     [description]
     * @param  Link     $link [description]
     * @param  IndexRequest $request  [description]
     * @return View                   [description]
     */
    public function index(string $type, Link $link, IndexRequest $request) : View
    {
        $links = $link->makeRepo()->paginateByType($type);

        return view('icore::admin.link.index', [
            'type' => $type,
            'links' => $links,
            'paginate' => config('database.paginate')
        ]);
    }

    /**
     * Show the form for creating a new Link.
     *
     * @param  string       $type [description]
     * @param CreateRequest $request
     * @return JsonResponse       [description]
     */
    public function create(string $type, CreateRequest $request) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.link.create', [
                'type' => $type,
            ])->render()
        ]);
    }

    /**
     * Store a newly created Link in storage.
     *
     * @param  string       $type     [description]
     * @param  Link         $link     [description]
     * @param  StoreRequest $request  [description]
     * @return JsonResponse           [description]
     */
    public function store(string $type, Link $link, StoreRequest $request) : JsonResponse
    {
        $link->makeService()->create($request->validated() + ['type' => $type]);

        $request->session()->flash('success', trans('icore::links.success.store'));

        return response()->json(['success' => '' ]);
    }

    /**
     * Show the form for editing the specified Link.
     *
     * @param  Link             $link [description]
     * @return JsonResponse           [description]
     */
    public function edit(Link $link) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.link.edit', [
                'link' => $link->loadAncestorsWithoutSelf(),
            ])->render()
        ]);
    }

    /**
     * Update the specified Link in storage.
     *
     * @param  Link          $link     [description]
     * @param  UpdateRequest $request  [description]
     * @return JsonResponse            [description]
     */
    public function update(Link $link, UpdateRequest $request) : JsonResponse
    {
        $link->makeService()->update($request->validated());

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.link.partials.link', [
                'link' => $link,
            ])->render()
        ]);
    }

    /**
     * [editPosition description]
     * @param  Link     $link [description]
     * @return JsonResponse           [description]
     */
    public function editPosition(Link $link) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.link.edit_position', [
                'link' => $link,
                'siblings_count' => $link->countSiblings()
            ])->render()
        ]);
    }

    /**
     * [updatePosition description]
     * @param  Link              $link [description]
     * @param  UpdatePositionRequest $request  [description]
     * @return JsonResponse                    [description]
     */
    public function updatePosition(Link $link, UpdatePositionRequest $request) : JsonResponse
    {
        $link->makeService()->updatePosition($request->only('position'));

        return response()->json([
            'success' => '',
            'siblings' => $link->makeRepo()->getSiblingsAsArray()
        ]);
    }

    /**
     * Remove the specified Link from storage.
     *
     * @param  Link         $link [description]
     * @return JsonResponse       [description]
     */
    public function destroy(Link $link) : JsonResponse
    {
        $link->makeService()->delete();

        return response()->json(['success' => '']);
    }
}
