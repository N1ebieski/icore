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

namespace N1ebieski\ICore\Http\Controllers\Admin\Comment\Page;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Comment\Page\Comment;
use N1ebieski\ICore\Filters\Admin\Comment\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Comment\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Page\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\Page\CreateRequest;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\Page\Polymorphic;
use N1ebieski\ICore\Events\Admin\Comment\StoreEvent as CommentStoreEvent;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\CommentController as BaseCommentController;

class CommentController implements Polymorphic
{
    /**
     * Undocumented function
     *
     * @param BaseCommentController $decorated
     */
    public function __construct(protected BaseCommentController $decorated)
    {
        //
    }

    /**
     * Display a listing of Comments.
     *
     * @param  Comment       $comment       [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return HttpResponse                         [description]
     */
    public function index(Comment $comment, IndexRequest $request, IndexFilter $filter): HttpResponse
    {
        return $this->decorated->index($comment, $request, $filter);
    }

    /**
     * Show the form for creating a new Comment for Page.
     *
     * @param Page $page
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function create(Page $page, CreateRequest $request): JsonResponse
    {
        return Response::json([
            'view' => View::make('icore::admin.comment.create', [
                'model' => $page,
                'parent_id' => $request->get('parent_id')
            ])->render()
        ]);
    }

    /**
     * [store description]
     * @param  Page         $page    [description]
     * @param  Comment      $comment [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Page $page, Comment $comment, StoreRequest $request): JsonResponse
    {
        $comment = $comment->makeService()->create(
            // @phpstan-ignore-next-line
            $request->safe()->merge([
                'morph' => $page,
                'user' => $request->user()
            ])->toArray()
        );

        Event::dispatch(App::make(CommentStoreEvent::class, ['comment' => $comment]));

        return Response::json([
            'view' => View::make('icore::admin.comment.partials.comment', [
                'comment' => $comment
            ])->render()
        ]);
    }
}
