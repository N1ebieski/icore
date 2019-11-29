<?php

namespace N1ebieski\ICore\Http\Controllers\Web\Comment\Post;

use N1ebieski\ICore\Http\Requests\Web\Comment\Post\CreateRequest;
use N1ebieski\ICore\Http\Requests\Web\Comment\Post\StoreRequest;
use N1ebieski\ICore\Models\Post;
use N1ebieski\ICore\Models\Comment\Post\Comment;
use Illuminate\Http\JsonResponse;
use N1ebieski\ICore\Events\CommentStore;
use N1ebieski\ICore\Http\Controllers\Web\Comment\Post\Polymorphic as PostPolymorphic;

/**
 * [CommentController description]
 */
class CommentController implements PostPolymorphic
{
    /**
     * Show the form for creating a new Comment for Post.
     *
     * @param  Post          $post    [description]
     * @param  CreateRequest $request [description]
     * @return JsonResponse           [description]
     */
    public function create(Post $post, CreateRequest $request) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::web.comment.create', [
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
    public function store(Post $post, Comment $comment, StoreRequest $request) : JsonResponse
    {
        $comment = $comment->setMorph($post)->makeService()->create($request->only(['content', 'parent_id']));

        event(new CommentStore($comment));

        return response()->json([
            'success' => $comment->status === 1 ? '' : trans('icore::comments.success.store_0'),
            'view' => $comment->status === 1 ?
                view('icore::web.comment.comment', [
                    'comment' => $comment
                ])->render() : null
        ]);
    }
}
