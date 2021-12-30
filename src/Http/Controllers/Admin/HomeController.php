<?php

namespace N1ebieski\ICore\Http\Controllers\Admin;

use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Http\Clients\Intelekt\Client;
use N1ebieski\ICore\Http\Responses\Data\Post\Chart\TimelineData as PostAndPagesTimelineData;

class HomeController
{
    /**
     * Undocumented function
     *
     * @param Post $post
     * @param Page $page
     * @param Client $client
     * @return HttpResponse
     */
    public function index(Post $post, Page $page, Client $client): HttpResponse
    {
        try {
            $posts = Collect::make($client->post('/api/posts/index', [
                'filter' => [
                    'status' => 1,
                    'orderby' => 'created_at|desc',
                    'search' => 'icore'
                ]
            ])->data);
        } catch (\N1ebieski\ICore\Exceptions\Client\TransferException $e) {
            $posts = null;
        }

        return Response::view('icore::admin.home.index', [
            'posts' => $posts,
            'countPostsAndPagesByDate' => App::make(PostAndPagesTimelineData::class, [
                'collection' => $post->makeRepo()->countActiveByDateUnionPages($page->activeByDate())
            ])->toArray()
        ]);
    }
}
