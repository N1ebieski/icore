<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Category;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Http\Requests\Admin\Category\UpdateRequest;
use N1ebieski\ICore\Http\Controllers\Admin\Category\Polymorphic;
use N1ebieski\ICore\Http\Requests\Admin\Category\UpdateStatusRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\DestroyGlobalRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\UpdatePositionRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\SearchRequest;
use N1ebieski\ICore\Http\Responses\Admin\Category\SearchResponse;

/**
 * Base Category Controller
 */
class CategoryController implements Polymorphic
{
    /**
     * Show the form for editing the specified Category.
     *
     * @param  Category $category
     * @return JsonResponse
     */
    public function edit(Category $category) : JsonResponse
    {
        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.category.edit', [
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

        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.category.partials.category', [
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
        $category->siblings_count = $category->countSiblings() + 1;

        return Response::json([
            'success' => '',
            'view' => View::make('icore::admin.category.edit_position', [
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

        return Response::json([
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

        return Response::json([
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

        return Response::json([
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

        return Response::redirectTo(URL::previous())->with(
            'success',
            Lang::get('icore::categories.success.destroy_global', [
                'affected' => $deleted
            ])
        );
    }

    /**
     * Search Categories for specified name.
     *
     * @param  Category      $category [description]
     * @param  SearchRequest $request  [description]
     * @param  SearchResponse $response [description]
     * @return JsonResponse                [description]
     */
    public function search(Category $category, SearchRequest $request, SearchResponse $response) : JsonResponse
    {
        $categories = $category->makeRepo()->getBySearch($request->get('name'));

        return $response->setCategories($categories)->makeResponse();
    }
}
