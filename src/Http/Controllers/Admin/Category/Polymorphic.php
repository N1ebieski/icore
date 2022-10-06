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

namespace N1ebieski\ICore\Http\Controllers\Admin\Category;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Loads\Admin\Category\EditLoad;
use N1ebieski\ICore\Filters\Admin\Category\IndexFilter;
use N1ebieski\ICore\Http\Requests\Admin\Category\IndexRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\StoreRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\CreateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\UpdateRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\StoreGlobalRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\UpdateStatusRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\DestroyGlobalRequest;
use N1ebieski\ICore\Http\Requests\Admin\Category\UpdatePositionRequest;

interface Polymorphic
{
    /**
     * Display a listing of the Category.
     *
     * @param  Category      $category      [description]
     * @param  IndexRequest  $request       [description]
     * @param  IndexFilter   $filter        [description]
     * @return HttpResponse                 [description]
     */
    public function index(Category $category, IndexRequest $request, IndexFilter $filter): HttpResponse;

    /**
     * Undocumented function
     *
     * @param Category $category
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function create(Category $category, CreateRequest $request): JsonResponse;

    /**
     * Store a newly created Category in storage.
     *
     * @param  Category      $category      [description]
     * @param  StoreRequest  $request
     * @return JsonResponse
     */
    public function store(Category $category, StoreRequest $request): JsonResponse;

    /**
     * Store collection of Categories with childrens in storage.
     *
     * @param  Category      $category      [description]
     * @param  StoreGlobalRequest  $request
     * @return JsonResponse
     */
    public function storeGlobal(Category $category, StoreGlobalRequest $request): JsonResponse;

    /**
     * Undocumented function
     *
     * @param Category $category
     * @param EditLoad $load
     * @return JsonResponse
     */
    public function edit(Category $category, EditLoad $load): JsonResponse;

    /**
     * Update the specified Category in storage.
     *
     * @param  Category      $category [description]
     * @param  UpdateRequest $request  [description]
     * @return JsonResponse                [description]
     */
    public function update(Category $category, UpdateRequest $request): JsonResponse;

    /**
     * [editPosition description]
     * @param  Category     $category [description]
     * @return JsonResponse           [description]
     */
    public function editPosition(Category $category): JsonResponse;

    /**
     * [updatePosition description]
     * @param  Category              $category [description]
     * @param  UpdatePositionRequest $request  [description]
     * @return JsonResponse                    [description]
     */
    public function updatePosition(Category $category, UpdatePositionRequest $request): JsonResponse;

    /**
     * Update Status attribute the specified Comment in storage.
     *
     * @param  Category            $category [description]
     * @param  UpdateStatusRequest $request  [description]
     * @return JsonResponse                        [description]
     */
    public function updateStatus(Category $category, UpdateStatusRequest $request): JsonResponse;

    /**
     * Remove the specified Category from storage.
     *
     * @param  Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category): JsonResponse;

    /**
     * Remove the collection of Categories from storage.
     *
     * @param  Category             $category [description]
     * @param  DestroyGlobalRequest $request  [description]
     * @return RedirectResponse               [description]
     */
    public function destroyGlobal(Category $category, DestroyGlobalRequest $request): RedirectResponse;
}
