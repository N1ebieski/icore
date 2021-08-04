<?php

namespace N1ebieski\ICore\Http\Controllers\Admin;

use N1ebieski\ICore\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Filters\Admin\Link\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Link\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Link\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Link\CreateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Link\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Link\UpdatePositionRequest;

class LinkController
{
    /**
     * Undocumented function
     *
     * @param string $type
     * @param Link $link
     * @param IndexRequest $request
     * @param IndexFilter $filter
     * @return HttpResponse
     */
    public function index(string $type, Link $link, IndexRequest $request, IndexFilter $filter) : HttpResponse
    {
        return Response::view('icore::admin.link.index', [
            'type' => $type,
            'links' => $link->makeRepo()->paginateByFilter($filter->all()),
            'paginate' => Config::get('database.paginate')
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
        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.link.create', [
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

        $request->session()->flash('success', Lang::get('icore::links.success.store'));

        return Response::json(['success' => '' ]);
    }

    /**
     * Show the form for editing the specified Link.
     *
     * @param  Link             $link [description]
     * @return JsonResponse           [description]
     */
    public function edit(Link $link) : JsonResponse
    {
        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.link.edit', [
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

        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.link.partials.link', [
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
        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.link.edit_position', [
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

        return Response::json([
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

        return Response::json(['success' => '']);
    }
}
