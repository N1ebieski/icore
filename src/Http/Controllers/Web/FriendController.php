<?php

namespace N1ebieski\ICore\Http\Controllers\Web;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;

/**
 * [FriendController description]
 */
class FriendController
{
    /**
     * Undocumented function
     *
     * @return HttpResponse
     */
    public function index() : HttpResponse
    {
        return Response::view('icore::web.friend.index');
    }
}