<?php

namespace N1ebieski\ICore\Http\Controllers\Web;

use N1ebieski\ICore\Http\Requests\Web\Search\AutoCompleteRequest;
use N1ebieski\ICore\Http\Requests\Web\Search\IndexRequest;
use N1ebieski\ICore\Models\Tag\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;

/**
 * [SearchController description]
 */
class SearchController
{
    /**
     * [autocomplete description]
     * @param  Tag                 $tag     [description]
     * @param  AutoCompleteRequest $request [description]
     * @return JsonResponse                       [description]
     */
    public function autocomplete(Tag $tag, AutoCompleteRequest $request) : JsonResponse
    {
        return Response::json($tag->makeRepo()->getBySearch($request->input('search')));
    }

    /**
     * [index description]
     * @param  IndexRequest     $request [description]
     * @return RedirectResponse          [description]
     */
    public function index(IndexRequest $request) : RedirectResponse
    {
        return Response::redirectToRoute("web.{$request->input('source')}.search", [
            'search' => $request->input('search')
        ]);
    }
}
