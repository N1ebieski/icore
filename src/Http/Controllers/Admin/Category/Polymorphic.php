<?php

namespace N1ebieski\ICore\Http\Controllers\Admin\Category;

use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Http\Requests\Admin\Category\UpdateStatusRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\UpdatePositionRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\DestroyGlobalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * [interface description]
 */
interface Polymorphic
{
    /**
     * Show the form for editing the specified Category.
     *
     * @param  Category $category
     * @return JsonResponse
     */
    public function edit(Category $category) : JsonResponse;

    /**
     * Update the specified Category in storage.
     *
     * @param  Category      $category [description]
     * @param  UpdateRequest $request  [description]
     * @return JsonResponse                [description]
     */
    public function update(Category $category, UpdateRequest $request) : JsonResponse;

    /**
     * [editPosition description]
     * @param  Category     $category [description]
     * @return JsonResponse           [description]
     */
    public function editPosition(Category $category) : JsonResponse;

    /**
     * [updatePosition description]
     * @param  Category              $category [description]
     * @param  UpdatePositionRequest $request  [description]
     * @return JsonResponse                    [description]
     */
    public function updatePosition(Category $category, UpdatePositionRequest $request) : JsonResponse;

    /**
     * Update Status attribute the specified Comment in storage.
     *
     * @param  Category            $category [description]
     * @param  UpdateStatusRequest $request  [description]
     * @return JsonResponse                        [description]
     */
    public function updateStatus(Category $category, UpdateStatusRequest $request) : JsonResponse;

    /**
     * Remove the specified Category from storage.
     *
     * @param  Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category) : JsonResponse;

    /**
     * Remove the collection of Categories from storage.
     *
     * @param  Category             $category [description]
     * @param  DestroyGlobalRequest $request  [description]
     * @return RedirectResponse               [description]
     */
    public function destroyGlobal(Category $category, DestroyGlobalRequest $request) : RedirectResponse;
}
