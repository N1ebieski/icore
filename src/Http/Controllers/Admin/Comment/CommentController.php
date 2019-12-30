<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Comment;

use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Http\Requests\Admin\Comment\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\UpdateCensoredRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\UpdateStatusRequest;
use N1ebieski\ICore\Http\Requests\Admin\Comment\DestroyGlobalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use N1ebieski\ICore\Http\Controllers\Admin\Comment\Polymorphic;

/**
 * [CommentController description]
 */
class CommentController implements Polymorphic
{
    /**
     * Display the specified Comment.
     *
     * @param  Comment  $comment [description]
     * @return JsonResponse          [description]
     */
    public function show(Comment $comment) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.comment.show', [
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
    public function edit(Comment $comment) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.comment.edit', [
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
    public function update(Comment $comment, UpdateRequest $request) : JsonResponse
    {
        $comment->makeService()->update($request->only('content'));

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.comment.partials.comment', [
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
    public function updateCensored(Comment $comment, UpdateCensoredRequest $request) : JsonResponse
    {
        $comment->update($request->only('censored'));

        return response()->json([
            'success' => '',
            'censored' => $comment->censored,
            'view' => view('icore::admin.comment.partials.comment', [
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
    public function updateStatus(Comment $comment, UpdateStatusRequest $request) : JsonResponse
    {
        $comment->makeService()->updateStatus($request->only('status'));

        $commentRepo = $comment->makeRepo();

        return response()->json([
            'success' => '',
            'status' => $comment->status,
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
    public function destroy(Comment $comment) : JsonResponse
    {
        // Pobieramy potomków aby na froncie jQuery wiedział jakie rowsy usunąć
        $descendants = $comment->makeRepo()->getDescendantsAsArray();

        $comment->makeService()->delete();

        return response()->json([
            'success' => '',
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
    public function destroyGlobal(Comment $comment, DestroyGlobalRequest $request) : RedirectResponse
    {
        $deleted = 0;
        // Antywzorzec, ale nie mialem wyboru, bo ClosureTable nie zmienia pozycji
        // rodzeństwa o 1 podczas usuwania i trzeba to robić ręcznie po każdym usunięciu
        foreach ($ids = $request->get('select') as $id) {
            if ($c = $comment->find($id)) {
                $c->makeService()->delete();

                $deleted += 1;
            }
        }

        return redirect()->back()->with('success', trans('icore::comments.success.destroy_global', ['affected' => $deleted]));
    }
}
