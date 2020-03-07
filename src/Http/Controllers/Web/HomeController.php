<?php

namespace N1ebieski\ICore\Http\Controllers\Web;

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;

/**
 * [HomeController description]
 */
class HomeController
{
    /**
     * Undocumented function
     *
     * @return HttpResponse
     */
    public function index() : HttpResponse
    {
        return Response::view('icore::web.home.index');
    }
}
