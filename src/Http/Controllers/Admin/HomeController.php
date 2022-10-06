<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Http\Controllers\Admin;

use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Http\Clients\Intelekt\Post\PostClient;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\ICore\Http\Responses\Data\Chart\Post\TimelineData as PostsAndPagesTimelineData;

class HomeController
{
    /**
     *
     * @param Post $post
     * @param Page $page
     * @param PostClient $client
     * @return HttpResponse
     * @throws BindingResolutionException
     */
    public function index(Post $post, Page $page, PostClient $client): HttpResponse
    {
        try {
            $posts = $client->index(['filter' => [
                'status' => 1,
                'orderby' => 'created_at|desc',
                'search' => 'icore',
            ]])->data;
        } catch (\N1ebieski\ICore\Exceptions\Client\TransferException $e) {
            $posts = null;
        }

        return Response::view('icore::admin.home.index', [
            'posts' => $posts,
            'countPostsAndPagesByDate' => App::make(PostsAndPagesTimelineData::class)
                ->toArray($post->makeRepo()->countActiveByDateUnionPages($page->activeByDate()))
        ]);
    }
}
