<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Comment\Page;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Lang;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Models\Comment\Page\Comment;
use N1ebieski\ICore\Http\Requests\Web\Comment\Page\StoreRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\Page\CreateRequest;
use N1ebieski\ICore\Events\Web\Comment\StoreEvent as CommentStoreEvent;
use N1ebieski\ICore\Http\Controllers\Web\Comment\Page\Polymorphic as PagePolymorphic;

/**
 * [CommentController description]
 */
class CommentController implements PagePolymorphic
{
    /**
     * Show the form for creating a new Comment for Page.
     *
     * @param  Page          $page    [description]
     * @param  CreateRequest $request [description]
     * @return JsonResponse           [description]
     */
    public function create(Page $page, CreateRequest $request) : JsonResponse
    {
        return Response::json([
            'success' => '',
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
    public function store(Page $page, Comment $comment, StoreRequest $request) : JsonResponse
    {
        $comment = $comment->setRelations(['morph' => $page])
            ->makeService()
            ->create($request->only(['content', 'parent_id']));

        Event::dispatch(App::make(CommentStoreEvent::class, ['comment' => $comment]));

        return Response::json([
            'success' => $comment->status === Comment::ACTIVE ?
                null : Lang::get('icore::comments.success.store.'.Comment::INACTIVE),
            'view' => $comment->status === Comment::ACTIVE ?
                View::make('icore::web.comment.partials.comment', [
                    'comment' => $comment
                ])->render() : null
        ]);
    }
}
