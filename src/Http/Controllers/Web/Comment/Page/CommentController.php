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

namespace N1ebieski\ICore\Http\Controllers\Web\Comment\Page;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Models\Comment\Page\Comment;
use N1ebieski\ICore\ValueObjects\Comment\Status;
use N1ebieski\ICore\Http\Requests\Web\Comment\Page\StoreRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\Page\CreateRequest;
use N1ebieski\ICore\Events\Web\Comment\StoreEvent as CommentStoreEvent;
use N1ebieski\ICore\Http\Controllers\Web\Comment\Page\Polymorphic as PagePolymorphic;

class CommentController implements PagePolymorphic
{
    /**
     * Show the form for creating a new Comment for Page.
     *
     * @param  Page          $page    [description]
     * @param  CreateRequest $request [description]
     * @return JsonResponse           [description]
     */
    public function create(Page $page, CreateRequest $request): JsonResponse
    {
        return Response::json([
            'view' => View::make('icore::web.comment.create', [
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
        /** @var Comment */
        $comment = $comment->makeService()->create(
            $request->safe()->merge([
                'morph' => $page,
                'user' => $request->user()
            ])->toArray()
        );

        Event::dispatch(App::make(CommentStoreEvent::class, ['comment' => $comment]));

        return Response::json([
            'success' => $comment->status->isActive() ?
                null : Lang::get('icore::comments.success.store.' . Status::INACTIVE),
            'view' => $comment->status->isActive() ?
                View::make('icore::web.comment.partials.comment', [
                    'comment' => $comment
                ])->render() : null
        ]);
    }
}
