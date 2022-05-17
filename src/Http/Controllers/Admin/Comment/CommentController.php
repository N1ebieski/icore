<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Comment;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Models\Comment\Comment;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Filters\Admin\Comment\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Comment\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\UpdateRequest;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\Polymorphic;
use N1ebieski\ICore\Http\Requests\Admin\Comment\UpdateStatusRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\DestroyGlobalRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\UpdateCensoredRequest;

class CommentController implements Polymorphic
{
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
        return Response::view('icore::admin.comment.index', [
            'model' => $comment,
            'comments' => $comment->makeRepo()->paginateByFilter($filter->all()),
            'filter' => $filter->all(),
            'paginate' => Config::get('database.paginate')
        ]);
    }

    /**
     * Display the specified Comment.
     *
     * @param  Comment  $comment [description]
     * @return JsonResponse          [description]
     */
    public function show(Comment $comment): JsonResponse
    {
        return Response::json([
            'view' => View::make('icore::admin.comment.show', [
                'comment' => $comment->loadAncestorsAndChildrens()
            ])->render()
        ]);
    }

    /**
     * Show the form for editing the specified Comment.
     *
     * @param  Comment $comment
     * @return JsonResponse
     */
    public function edit(Comment $comment): JsonResponse
    {
        return Response::json([
            'view' => View::make('icore::admin.comment.edit', [
                'comment' => $comment
            ])->render()
        ]);
    }

    /**
     * Update the specified Comment in storage.
     *
     * @param  Comment       $comment [description]
     * @param  UpdateRequest $request [description]
     * @return JsonResponse                 [description]
     */
    public function update(Comment $comment, UpdateRequest $request): JsonResponse
    {
        $comment->makeService()->update($request->only('content'));

        return Response::json([
            'view' => View::make('icore::admin.comment.partials.comment', [
                'comment' => $comment->loadAllRels()
            ])->render()
        ]);
    }

    /**
     * Update Censored attribute the specified Comment in storage.
     *
     * @param  Comment               $comment [description]
     * @param  UpdateCensoredRequest $request [description]
     * @return JsonResponse                      [description]
     */
    public function updateCensored(Comment $comment, UpdateCensoredRequest $request): JsonResponse
    {
        $comment->update($request->only('censored'));

        return Response::json([
            'censored' => $comment->censored->getValue(),
            'view' => View::make('icore::admin.comment.partials.comment', [
                'comment' => $comment->loadAllRels()
            ])->render()
        ]);
    }

    /**
     * Update Status attribute the specified Comment in storage.
     *
     * @param  Comment             $comment [description]
     * @param  UpdateStatusRequest $request [description]
     * @return JsonResponse                     [description]
     */
    public function updateStatus(Comment $comment, UpdateStatusRequest $request): JsonResponse
    {
        $comment->makeService()->updateStatus($request->input('status'));

        $commentRepo = $comment->makeRepo();

        return Response::json([
            'status' => $comment->status->getValue(),
            // Na potrzebę jQuery pobieramy potomków i przodków, żeby na froncie
            // zaznaczyć odpowiednie rowsy jako aktywowane bądź nieaktywne
            'ancestors' => $commentRepo->getAncestorsAsArray(),
            'descendants' => $commentRepo->getDescendantsAsArray(),
        ]);
    }

    /**
     * Remove the specified Comment from storage.
     *
     * @param  Comment $comment
     * @return JsonResponse
     */
    public function destroy(Comment $comment): JsonResponse
    {
        // Pobieramy potomków aby na froncie jQuery wiedział jakie rowsy usunąć
        $descendants = $comment->makeRepo()->getDescendantsAsArray();

        $comment->makeService()->delete();

        return Response::json([
            'descendants' => $descendants,
        ]);
    }

    /**
     * Remove the collection of Comments from storage.
     *
     * @param  Comment              $comment [description]
     * @param  DestroyGlobalRequest $request [description]
     * @return RedirectResponse              [description]
     */
    public function destroyGlobal(Comment $comment, DestroyGlobalRequest $request): RedirectResponse
    {
        $deleted = $comment->makeService()->deleteGlobal($request->input('select'));

        return Response::redirectTo(URL::previous())->with(
            'success',
            Lang::get('icore::comments.success.destroy_global', ['affected' => $deleted])
        );
    }
}
