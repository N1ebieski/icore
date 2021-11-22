<?php

namespace N1ebieski\ICore\Http\Controllers\Web;

use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;

class HomeController
{
    /**
     * Undocumented function
     *
     * @return HttpResponse
     */
    public function index(Post $post): HttpResponse
    {
        return Response::view('icore::web.home.index', [
            'posts' => $post->makeCache()->rememberLatestForHome()
        ]);
    }
}
