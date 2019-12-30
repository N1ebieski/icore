<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Category;

use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Http\Requests\Admin\Category\UpdateStatusRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\UpdatePositionRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\DestroyGlobalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use N1ebieski\ICore\Http\Controllers\Admin\Category\Polymorphic;

/**
 * Base Category Controller
 */
class CategoryController implements Polymorphic
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified Category.
     *
     * @param  Category $category
     * @return JsonResponse
     */
    public function edit(Category $category) : JsonResponse
    {
        return response()->json([
            'success' => '',
            'view' => view('icore::admin.category.edit', [
                'category' => $category,
                'categories' => $category->makeService()->getAsFlatTreeExceptSelf()
            ])->render()
        ]);
    }

    /**
     * Update the specified Category in storage.
     *
     * @param  Category      $category [description]
     * @param  UpdateRequest $request  [description]
     * @return JsonResponse                [description]
     */
    public function update(Category $category, UpdateRequest $request) : JsonResponse
    {
        $category->makeService()->update($request->only(['parent_id', 'icon', 'name']));

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.category.partials.category', [
                // Niezbyt ładny hook, ale trzeba na nowo pobrać ancestory
                'category' => $category->resolveRouteBinding($category->id),
                'show_ancestors' => true
            ])->render()
        ]);
    }

    /**
     * [editPosition description]
     * @param  Category     $category [description]
     * @return JsonResponse           [description]
     */
    public function editPosition(Category $category) : JsonResponse
    {
        $category->siblings_count = $category->countSiblings()+1;

        return response()->json([
            'success' => '',
            'view' => view('icore::admin.category.edit_position', [
                'category' => $category
            ])->render()
        ]);
    }

    /**
     * [updatePosition description]
     * @param  Category              $category [description]
     * @param  UpdatePositionRequest $request  [description]
     * @return JsonResponse                    [description]
     */
    public function updatePosition(Category $category, UpdatePositionRequest $request) : JsonResponse
    {
        $category->makeService()->updatePosition($request->only('position'));

        return response()->json([
            'success' => '',
            'siblings' => $category->makeRepo()->getSiblingsAsArray()+[$category->id => $category->position],
        ]);
    }

    /**
     * Update Status attribute the specified Comment in storage.
     *
     * @param  Category            $category [description]
     * @param  UpdateStatusRequest $request  [description]
     * @return JsonResponse                        [description]
     */
    public function updateStatus(Category $category, UpdateStatusRequest $request) : JsonResponse
    {
        $category->makeService()->updateStatus($request->only('status'));

        $categoryRepo = $category->makeRepo();

        return response()->json([
            'success' => '',
            'status' => $category->status,
            // Na potrzebę jQuery pobieramy potomków i przodków, żeby na froncie
            // zaznaczyć odpowiednie rowsy jako aktywowane bądź nieaktywne
            'ancestors' => $categoryRepo->getAncestorsAsArray(),
            'descendants' => $categoryRepo->getDescendantsAsArray(),
        ]);
    }

    /**
     * Remove the specified Category from storage.
     *
     * @param  Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category) : JsonResponse
    {
        // Pobieramy potomków aby na froncie jQuery wiedział jakie rowsy usunąć
        $descendants = $category->makeRepo()->getDescendantsAsArray();

        $category->makeService()->delete();

        return response()->json([
            'success' => '',
            'descendants' => $descendants,
        ]);
    }

    /**
     * Remove the collection of Categories from storage.
     *
     * @param  Category             $category [description]
     * @param  DestroyGlobalRequest $request  [description]
     * @return RedirectResponse               [description]
     */
    public function destroyGlobal(Category $category, DestroyGlobalRequest $request) : RedirectResponse
    {
        $deleted = $category->makeService()->deleteGlobal($request->get('select'));
        //$deleted = $category->whereIn('id', $request->get('select'))->delete();

        return redirect()->back()->with('success', trans('icore::categories.success.destroy_global', ['affected' => $deleted]));
    }
}
