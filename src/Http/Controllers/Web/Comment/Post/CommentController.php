<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Comment\Post;

use N1ebieski\ICore\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use N1ebieski\ICore\ValueObjects\Comment\Status;
use N1ebieski\ICore\Http\Requests\Web\Comment\Post\StoreRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\Post\CreateRequest;
use N1ebieski\ICore\Events\Web\Comment\StoreEvent as CommentStoreEvent;
use N1ebieski\ICore\Http\Controllers\Web\Comment\Post\Polymorphic as PostPolymorphic;

class CommentController implements PostPolymorphic
{
    /**
     * Show the form for creating a new Comment for Post.
     *
     * @param  Post          $post    [description]
     * @param  CreateRequest $request [description]
     * @return JsonResponse           [description]
     */
    public function create(Post $post, CreateRequest $request): JsonResponse
    {
        return Response::json([
            'view' => View::make('icore::web.comment.create', [
                'model' => $post,
                'parent_id' => $request->get('parent_id')
            ])->render()
        ]);
    }

    /**
     * [store description]
     * @param  Post         $post    [description]
     * @param  Comment      $comment [description]
     * @param  StoreRequest $request [description]
     * @return JsonResponse          [description]
     */
    public function store(Post $post, Comment $comment, StoreRequest $request): JsonResponse
    {
        /**
         * @var Comment
         */
        $comment = $comment->setRelations(['morph' => $post])
            ->makeService()
            ->create($request->only(['content', 'parent_id']));

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
