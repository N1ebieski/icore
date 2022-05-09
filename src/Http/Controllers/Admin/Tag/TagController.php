<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Tag;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Services\Tag\TagService;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Filters\Admin\Tag\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Tag\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Tag\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Tag\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Tag\DestroyGlobalRequest;

class TagController
{
    /**
     * Undocumented function
     *
     * @param Tag $tag
     * @param IndexRequest $request
     * @param IndexFilter $filter
     * @return HttpResponse
     */
    public function index(Tag $tag, IndexRequest $request, IndexFilter $filter): HttpResponse
    {
        return Response::view('icore::admin.tag.index', [
            'tags' => $tag->makeRepo()->paginateByFilter($filter->all()),
            'filter' => $filter->all(),
            'paginate' => Config::get('database.paginate')
        ]);
    }

    /**
     * Undocumented function
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        return Response::json([
            'view' => View::make('icore::admin.tag.create')->render()
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Tag $tag
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(Tag $tag, StoreRequest $request): JsonResponse
    {
        $tag->makeService()->create($request->validated());

        $request->session()->flash('success', trans('icore::tags.success.store'));

        return Response::json([]);
    }

    /**
     * Undocumented function
     *
     * @param Tag $tag
     * @return JsonResponse
     */
    public function edit(Tag $tag): JsonResponse
    {
        return Response::json([
            'view' => View::make('icore::admin.tag.edit', [
                'tag' => $tag,
            ])->render()
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Tag $tag
     * @param TagService $tagService
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(Tag $tag, UpdateRequest $request): JsonResponse
    {
        $tag->makeService()->update($request->validated());

        return Response::json([
            'view' => View::make('icore::admin.tag.partials.tag', [
                'tag' => $tag
            ])->render()
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Tag $tag
     * @return JsonResponse
     */
    public function destroy(Tag $tag): JsonResponse
    {
        $tag->makeService()->delete();

        return Response::json([]);
    }

    /**
     * Undocumented function
     *
     * @param Tag $tag
     * @param DestroyGlobalRequest $request
     * @return RedirectResponse
     */
    public function destroyGlobal(Tag $tag, DestroyGlobalRequest $request): RedirectResponse
    {
        $deleted = $tag->makeService()->deleteGlobal($request->input('select'));

        return Response::redirectTo(URL::previous())->with(
            'success',
            Lang::get('icore::tags.success.destroy_global', [
                'affected' => $deleted
            ])
        );
    }
}
