<?php

namespace N1ebieski\ICore\Http\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Http\Requests\Web\Search\IndexRequest;

class SearchController
{
    /**
     * [index description]
     * @param  IndexRequest     $request [description]
     * @return RedirectResponse          [description]
     */
    public function index(IndexRequest $request): RedirectResponse
    {
        return Response::redirectToRoute("web.{$request->input('source')}.search", [
            'search' => $request->input('search')
        ]);
    }
}
